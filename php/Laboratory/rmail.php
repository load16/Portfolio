<?php
  // Скрипт ответственный за чтение писем и формирование базы пациентов.
  
  include_once 'classes/mail.php';
  include_once 'classes/config.php'; 
  include_once 'classes/register.php';
  
  $config  = config::getInstance();                                                                         //Получаем объект конфигурации.
  $mail = new mail($config->conf['imaphost'], $config->conf['smtpuser'], $config->conf['smtppass']);        //Получаем объект для работы с письмами.
  $ArrMail = $mail->getArrayMail();                                                                         //Получаем массив писем.
  if(count($ArrMail) >= 1){                                                                                 //Проверка наличия писем.
      $reg = new register();                                                                                //Получаем объект для работы с реестром пациентов.
      $arr_reg = $reg->getArray($ArrMail);                                                                  //Получаем массив данных для заполения реестра.  
      $reg->writeDataBase($arr_reg);                                                                        //Вносим данные в реестр пациентов.  
      echo '<pre>'.print_r($arr_reg).'</pre>';
      //var_dump($arrr);
  }
  unset($config, $mail, $ArrMail, $arr_reg);
      
  
?>
