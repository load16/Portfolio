#!/bin/bash
# Конечная версия скрипта.
# Скрипт для управления кластером из DRBD, MariaDB, Appache, Postfix, Dovecot и др.
# Предполагается что управляемые итерфейсы и сервисы по умолчанию - остановленны, за исключением второго провайдера с DHCP.

# Удаленный хост управляющей сети.
HOST2=XXX.XXX.XXX.XXX
# Имя локального юнита кластера.
UNIT_N='ha1'
# Имя управляемого интерфейса
INT_U='eth0'
# Имя интерфейса второго провайдера (DHCP)
INT_2U='eth1'
# IP-адресс управляемого сетевого интерфейса.
IP_U=XXX.XXX.XXX.XXX
# Маска сети управляемого сетевого интерфейса.
MASK_U=XXX.XXX.XXX.XXX



# Проверка всех функций и установка флагов.

# Проверка роли DRBD.
if drbdadm status data | grep "data role:Primary"
then
 # Проверка корректности работы DRBD.
 if drbdadm status data | grep "role:Secondary"
 then
   dd=1
 else
   dd=0
 fi
 if drbdadm status data | grep "SyncSource"
 then
   ddc=1
 else
   ddc=0
 fi
 d=1
fi

if drbdadm status data | grep "data role:Secondary"
then
 # Проверка корректности работы DRBD.
 if drbdadm status data | grep "role:Primary"
 then
   ds=1
 else
   ds=0
 fi

 if drbdadm status data | grep "SyncSource"
 then
   dsc=1
 else
   dsc=0
 fi

 d=0
fi

# Провека статуса юнита.
if pcs resource | grep "Started ${UNIT_N}"
then
 u=1
else
 u=0
fi

#  Проверка статуса сервисов.
if systemctl status mariadb.service | grep "Active: active (running)"
then
 s=1
else
 s=0
fi

# Проверка примонтированных коталогов.
if mount | grep "/dev/drbd0 on /var/www type ext4"
then
 m=1
else
 m=0
fi

# Проверка наличия интерфейса.
if ifconfig | grep "${INT_2U}"
then
 i2=1
else
 i2=0
fi

# Проверка наличия интерфейса.
if ifconfig | grep "${INT_U}"
then
 i1=1
else
 i1=0
fi

# Проверка доступности хоста.
ping -q -c 1 ${HOST2} > /dev/null 2>&1
if [ $? -eq 0 ];
then
 h=1
else
 h=0
fi

# Проверка статуса юнита кластера.
if [ "$u" = "1" ];
then
   echo 'Локальный юнит кластера в режиме PRIMARY.'
   # Проводим проверку статуса юнита.
   if [ "$h" = "1" -a "$dd" = "1" -o "$h" = "0" -a "$d" = "1" -o "$ddc" = "1" -a "$d" = "1" ];
   then
     echo 'DRBD в актуальном состоянии!'
   else
     echo 'DRBD не работает репликация!'
     echo 'Исправляем работу DRBD!'
     systemctl stop httpd.service
     systemctl stop postfix
     systemctl stop dovecot
     systemctl stop amavisd
     systemctl stop spamassassin
     systemctl stop mariadb.service
     umount /dev/drbd0 /var/www/
     umount /dev/drbd0 /etc/httpd/
     umount /dev/drbd0 /mnt/mail/
     drbdadm disconnect data
     drbdadm down data
     drbdadm up data
     drbdadm primary --force data
     mount /dev/drbd0 /var/www/
     mount /dev/drbd0 /etc/httpd/
     mount /dev/drbd0 /mnt/mail/
   fi


else
   echo 'Локальный юнит кластера в режиме SECONDERY.'
   # Проводим проверку DRBD.
   if [ "$h" = "1" -a "$ds" = "1" -o "$d" = "0" -a "$dsc" = "1" ];
   then
     echo 'DRBD в актуальном состоянии в режиме SECONDERY!'
   else
     echo 'DRBD не работает репликация в режиме SECONDERY!'
     echo 'Исправляем работу DRBD в режиме SECONDERY!'
     systemctl stop httpd.service
     systemctl stop postfix
     systemctl stop dovecot
     systemctl stop amavisd
     systemctl stop spamassassin
     systemctl stop mariadb.service
     umount /dev/drbd0 /var/www/
     umount /dev/drbd0 /etc/httpd/
     umount /dev/drbd0 /mnt/mail/
     drbdadm disconnect data
     drbdadm secondary data
     drbdadm -- --discard-my-data connect data
   fi
fi





# Проверка работы сервисов.
if mount | grep "/dev/drbd0 on /var/www type ext4"
then
   if systemctl status mariadb.service | grep "Active: active (running)"
   then
      echo 'Все сервисы работают нормально!'
      sh /etc/postfix.sh
   else
      echo 'Сервисы - стартуем!'
      systemctl start mariadb
      systemctl start spamassassin
      systemctl start amavisd
      systemctl start postfix
      systemctl start dovecot
      systemctl start httpd.service
      # Подымаем интерфейсы.
      ifconfig ${INT_U} up
      ifconfig ${INT_2U} up
      nmcli con up ${INT_2U}
      nmcli con up ${INT_U}
      sh /etc/postfix.sh
   fi
else
   if systemctl status mariadb.service | grep "Active: inactive"
   then
      echo 'Сервисы штатно отключено!'
   else
      # Стопорим сервисы и отключаем интерфейсы.
      nmcli con down ${INT_U}
      nmcli con down ${INT_2U}
      ifconfig ${INT_U} down
      ifconfig ${INT_2U} down
      systemctl restart iptables.service
      echo 'Сервисы необходимо отключить!'
      systemctl stop httpd.service
      systemctl stop postfix
      systemctl stop dovecot
      systemctl stop amavisd
      systemctl stop spamassassin
      systemctl stop mariadb.service
   fi
   
   # Проверка активности интерфейсов.
   if ifconfig | grep "${INT_2U}"
   then
      # Отключаем интерфейсы.
      echo 'Отключаем интерфейс второго провайдера!'
      nmcli con down ${INT_U}
      nmcli con down ${INT_2U}
      ifconfig ${INT_U} down
      ifconfig ${INT_2U} down

   else
      echo 'Интерфейс второго провайдера - отключен!'
   fi
fi

