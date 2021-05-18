#!/bin/bash
# Скрипт проверки места на диске. При уменшении ниже критического - оповещение на почту.
# Задаем переменную, где вычисляем свободное место на диске /dev/vda2 (в мегабайтах)
#freespace=`df -m | grep "/dev/vda2" | awk '{print $4}'`
#freespace=`df -m | grep "/dev/mapper/centos-home" | awk '{print $4}'`
freespace=`df -m | grep "/dev/drbd0" | awk '{print $4}'`
#echo $freespace
# Если сободного места меньше 20000 Mb, то отправляем письмо на e-mail.
if [ $freespace -lt 20000 ];
then
   echo 'Свободного места на диске почтовых ящиков меньше 20Gb.'
  /bin/php -r 'var_dump(mail("support@mail","Почтовый сервер","Attention!!! \r\n The mail server disk is full. \r\n Free disk space for mail less than 20 GB  \r\n The mail server may fail. \r\n Urgently take action."));'
fi
