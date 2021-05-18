# Скрипт подготовки к работе на два провайдера.

ip rule add from XXX.XXX.XXX.XXX table table1
ip route add default via GW1.XXX.XXX.XXX dev eth0 table table1
ip rule add from XXX.XXX.XXX.XXX table table2
ip route add default via GW2.XXX.XXX.XXX dev eth1 table table2

if [ -e /etc/postfix/GW2 ];
then
	# Меняем переменную.
	sudo mv -f /etc/postfix/GW2 /etc/postfix/GW1
	sudo cp -f /etc/postfix/main.cf.ws1 /etc/postfix/main.cf
        systemctl restart postfix	
fi
