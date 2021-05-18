#!/bin/bash

# Скрипт управляющий POSTFIX на два ровайдера.

# Опрашиваемые узелы.
# Предполагается что первый хост прописан в маршрут первого провайдера, второй - второго провайдера.
HOST1=8.8.4.4
HOST2=8.8.8.8

# Шлюз к основному провайдеру
GW1=XXX.XXX.XXX.XXX
# Давайс главного маршрута.
DEV='eth0'
# Метрика главного маршрута.
METRIC=100

# Шлюз к второму провайдеру
GW2=XXX.XXX.XXX.XXX
# Давайс второго маршрута.
DEV2='eth1'


# Маршрут по умолчанию первого провайдера
ROU1="default via ${GW1} dev eth0 proto static metric ${METRIC}"
# Маршрут по умолчанию второго провайдера
ROU2="default via ${GW2} dev eth1 proto dhcp metric"
ROU="default via ${GW2} dev eth1 metric 0"
# Маршрут для пинга первого провайдера.
PIN1="${HOST1} via ${GW1} dev eth0 proto static metric ${METRIC}"
# Маршрут для пинга второго провайдера.
PIN2="${HOST2} via ${GW2} dev eth1 proto static metric 101"


# Проверяаем таблицу маршрутизации и меняем ее при отсуствии записей.
if ip route show | grep "${PIN1}"
then
        echo 'Есть первый маршрут пинга!'
else
        ip route add ${PIN1}
fi

#if ip route show | grep "${ROU}"
#then
#	ip route delete ${ROU}
#fi

#if ip route show | grep "${ROU2}"
#then
        if ip route show | grep "${PIN2}"
        then
                echo 'Есть второй маршрут пинга!'
        else
                ip route add ${PIN2}
        fi
#else
#        echo 'Нет второго маршрута!'
#fi






# Проверка установленной переменной.   
if [ -e /etc/postfix/GW1 ];
then
        # Если есть флаг первого маршрута.
        ping -q -c 1 ${HOST1} > /dev/null 2>&1
        # Проверка прохождения пинга.
        if [ $? -eq 0 ];
        then
		echo 'Активный первый провайдер'
        else
        	# Прингуем второй хост.
                ping -q -c 1 ${HOST2} > /dev/null 2>&1
                if [ $? -eq 0 ];
                then
                	# Стопорим постфикс.
                        systemctl stop postfix
                        # Меняем конфигурацию постфикса.
                        sudo cp -f /etc/postfix/main.cf.ws2 /etc/postfix/main.cf
                        # Переключаем на рабочий маррут.


			# Удаляем первый маршрут.
			ip route delete default via ${GW1} dev ${DEV} proto static metric ${METRIC}
			ip route delete default via ${GW1} dev ${DEV}


			# Создаем второй маршрут.
			ip route replace default via ${GW2} dev ${DEV2} proto dhcp metric ${METRIC}
                        
			# ip route delete default via 176.241.109.193 dev eth0 proto static metric 100
                        # Меняем переменную.
			sudo mv -f /etc/postfix/GW1 /etc/postfix/GW2
                        # Стартуем пост фикс.
                        systemctl start postfix
			echo 'Активный второй провайдер'
		else
			echo 'Оба провайдера не работают'
                fi

        fi
else
	ping -q -c 1 ${HOST2} > /dev/null 2>&1
        #if [ $? -eq 0 ] && !ip route show | grep "${ROU1}";
        if [ $? -eq 0 ];
	then
		
                echo 'Активный второй провайдер'
		# Проверяем доступность первого провайдера для востановления его по умолчанию.
                ping -q -c 1 ${HOST1} > /dev/null 2>&1
                if [ $? -eq 0 ];
                then
                        systemctl stop postfix
                        sudo cp -f /etc/postfix/main.cf.ws1 /etc/postfix/main.cf
                        # Если не прошел, то переключаем на рабочий маррут.

                        # Удаляем второй маршрут.
                        ip route delete default via ${GW2} dev ${DEV2} proto dhcp metric ${METRIC}
                        ip route delete default via ${GW2} dev ${DEV2}

                        # Создаем первый маршрут.
                        ip route replace default via ${GW1} dev ${DEV} proto static metric ${METRIC}
                        # ip route replace default via 176.241.109.193 dev eth0 proto static metric 100
                        # Меняем переменную.
                        sudo mv -f /etc/postfix/GW2 /etc/postfix/GW1
                        systemctl start postfix
                        echo 'Возврат первого провайдера'
                fi









        else
        	ping -q -c 1 ${HOST1} > /dev/null 2>&1
                if [ $? -eq 0 ];
                then
                	systemctl stop postfix
                        sudo cp -f /etc/postfix/main.cf.ws1 /etc/postfix/main.cf
                        # Если не прошел, то переключаем на рабочий маррут.
			
			# Удаляем второй маршрут.
			ip route delete default via ${GW2} dev ${DEV2} proto dhcp metric ${METRIC}
			ip route delete default via ${GW2} dev ${DEV2}
			
			# Создаем первый маршрут.
			ip route replace default via ${GW1} dev ${DEV} proto static metric ${METRIC}
			# ip route replace default via 176.241.109.193 dev eth0 proto static metric 100
                        # Меняем переменную.
			sudo mv -f /etc/postfix/GW2 /etc/postfix/GW1
                        systemctl start postfix
			echo 'Активный первый провайдер'
		else
			echo 'Оба провайдера не работают'
                fi
        fi
fi

echo '
'
