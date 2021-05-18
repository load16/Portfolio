#!/bin/bash

# Скрипт для переполучения айпи-адреса после перебоев связи провайдера с DHCP.
# Предполагается маршрут прописан через провайдер с DHCP.
HOST2=8.8.8.8
# Локальный хост смотрящий через DHCP
HOST1=77.120.162.137


# Проверяем наличия IP второго провайдера.
if ifconfig | grep "${HOST1}"
then
  ping -q -c 1 ${HOST1} > /dev/null 2>&1
  if [ $? -eq 0 ];
   then
     echo 'Интерфейс провайдера с DHCP - активный!'
   else
#     dhclient -r eth1
#     dhclient -v eth1  
     nmcli con down eth1
     nmcli con up eth1
     ip rule add from 176.241.109.199 table table1
     ip route add default via 176.241.109.193 dev eth0 table table1
     ip rule add from 77.120.162.137 table table2
     ip route add default via 77.120.162.1 dev eth1 table table2
  fi
  ping -q -c 1 ${HOST2} > /dev/null 2>&1
  # Проверка прохождения пинга.
  if [ $? -eq 0 ];
   then
     echo 'Провайдер с DHCP - активный!'
   else
#     dhclient -r eth1
#     dhclient -v eth1
     nmcli con down eth1
     nmcli con up eth1
 
     ip rule add from 176.241.109.199 table table1
     ip route add default via 176.241.109.193 dev eth0 table table1
     ip rule add from 77.120.162.137 table table2
     ip route add default via 77.120.162.1 dev eth1 table table2
  fi

  # Проверка правил
  if ip rule ls | grep "from 77.120.162.137 lookup table2"
  then
     echo 'Правила есть!'
  else
     echo 'Восстанавливаем правила!'
     ip rule add from 176.241.109.199 table table1
     ip route add default via 176.241.109.193 dev eth0 table table1
     ip rule add from 77.120.162.137 table table2
     ip route add default via 77.120.162.1 dev eth1 table table2
  fi
fi
