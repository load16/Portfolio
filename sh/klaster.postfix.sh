#!/bin/bash
# Промежуточная версия скрипта.
# Скрипт для управления кластером из DRBD, MariaDB, Appache, Postfix, Dovecot и др.
# Предполагается что управляемые итерфейсы и сервисы по умолчанию - остановленны.

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



# Проверка доступности хоста.
ping -q -c 1 ${HOST2} > /dev/null 2>&1
if [ $? -eq 0 ];
then
   echo 'Удаленный юнит кластера в рабочем состоянии.'
   # Проводим проверку статуса DRBD.
   if drbdadm status data | grep "data role:Secondary"
   then
      echo 'Активна роль DRBD SECONDARY!'
      if drbdadm status data | grep "role:Primary"
      then
        echo 'DRBD в актуальном состоянии!'
      else
        if drbdadm status data | grep "data role:Secondary"
        then
          echo 'Активна роль DRBD SECONDARY!'
          # Проводим проверку юнита кластера на главную роль.
          if pcs resource | grep "Started ${UNIT_N}"
          then
             # Возникла проблема когда два юнита имеют DRBD SECONDARY.
             echo 'На главном юните переводим DRBD в режим PRIMARY!'
             drbdadm primary --force data
             mount /dev/drbd0 /var/www/
             mount /dev/drbd0 /etc/httpd/
             mount /dev/drbd0 /mnt/mail/
          else
             echo 'Устраняем ошибку работы DRBD!'
             drbdadm disconnect data
             drbdadm secondary data
             drbdadm -- --discard-my-data connect data
          fi
        fi
      fi
   else
      echo 'DRBD в режиме PRIMARY!'
   fi

   if drbdadm status data | grep "data role:Primary"
   then
      echo 'Активна роль DRBD PRIMARY!'
      if drbdadm status data | grep "role:Secondary"
      then
        echo 'DRBD в рабочем состоянии!'
      else
        echo 'DRBD не работает репликация!'
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
      echo 'DRBD в режиме SECONDARY!'
   fi



else
   # Проверка работы DRBD
   if systemctl status drbd | grep "Active: activating (start)"
   then
      # Если не работает, то перезапускаем и делаем PRIMARY.
      echo 'збой DRBD на старте!'
      drbdadm disconnect data
      drbdadm down data
      drbdadm up data
      drbdadm primary --force data
      mount /dev/drbd0 /var/www/
      mount /dev/drbd0 /etc/httpd/
      mount /dev/drbd0 /mnt/mail/
   else
      echo 'DRBD работает правильно!'
      # Проверка примонтированной файлово системы
      if mount | grep "/dev/drbd0 on /var/www"
      then
        echo "Фаловая система промонтирована!"
      else
        drbdadm primary --force data
        mount /dev/drbd0 /var/www/
        mount /dev/drbd0 /etc/httpd/
        mount /dev/drbd0 /mnt/mail/
      fi      
   fi
   echo 'Удаленный хост кластера отключен!'
fi


# Проверка работы сервисов.
if mount | grep "/dev/drbd0 on /var/www type ext4"
then
   if systemctl status mariadb.service | grep "Active: active (running)"
   then
      echo 'Все сервисы работают нормально!'
#      sh /etc/dhcp.sh
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
#      ifconfig ${INT_U} up
#      ifconfig ${INT_U} ${IP_U} netmask ${MASK_U}
#      ifconfig ${INT_2U} up
#      dhclient -r ${INT_2U}
#      dhclient -v ${INT_2U}
      nmcli con up ${INT_2U}
      nmcli con up ${INT_U}
#      systemctl restart iptables.service
#      sh /etc/dhcp.sh
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
