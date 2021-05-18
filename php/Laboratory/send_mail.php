<?php
  //Скрипт отвественный за поиск и отправку готовых нанализов пациентов.
  gc_collect_cycles();
  
  include_once 'classes/mail.php';
  include_once 'classes/config.php'; 
  include_once 'classes/register.php';
  include_once 'classes/files.php'; 
  include_once 'classes/send.php';
  
  $os = PHP_OS;                                                                                             //Определяем ОС.
  $os = strtolower($os);                                                                                    //Переводим в нижний регистр.
  $send = new send(true);                                                                                   //Полуяаем объект отправки нанализов.
  $config = config::getInstance(); 
  $files = new files();                                                                                     //Получаем объект работы с файлами.
  $reg = new register();                                                                                    //Получаем объект с методологической базой.
  //$mail = new mail($config->conf['imaphost'], $config->conf['smtpuser'], $config->conf['smtppass']);        //Получаем объект для работы с письмами. 
  
  $start = strtotime($config->conf['timestart']);                                                           //Начальное время.
  $end = strtotime($config->conf['timestop']);                                                              //Конечное время.
  $current_time = strtotime($send->date->time_i);                                                           //Текущее время.
  if (
  $current_time >= $start 
  && $current_time <= $end
  && $config->conf['permit']
    ){                                                                              //Проверка разрешенного времени работы.
      //Обновляем базу анализов.
      $reg->updateBaseAnalizFile($files->getArryBaseFile($config->conf['patchseach']));
      
      //Обновляем базу пациентов на предмет старше лопределенного строка и дизактивации.
      $reg->deyUpdateBase($config->conf['age_p']);
      
      
      //Формируем списки активных для рассылки.
      $list_send = $reg->getListSend();
      $reg->putListSend($list_send);  
      
      if(count($list_send) >> 0){                                                       //Проверка наличия данных.
          $n = 0;                                                                       //Начальная установка счетчика цикла.
          foreach($list_send as $key => $value){                                        //Обходим список активных для отправки.
              if($value['send_all']){                                                   //Проверка признака полной отправки.
                  $arr_file = $reg->getListFileSendFio($value['id'], true);             //Формируем список файлов с полной отправкой.
              }
              else{
                  $arr_file = $reg->getListFileSendFio($value['id']);                   //Формируем список файлов без полной отправки.
              }
              if(count($arr_file) >> 0){                                                //Проверка наличия данных для отправки.
                  foreach($arr_file as $k => $v){                                       //Обходим полученные данные и готовим к отправки. 
                      $aa[] = $v['fullname'];
                      $bb[] = $v['name'];                                               //Фиксирум данные для лога.
                                                 
                  }
                  unset($k, $v);
                  $send->sendAnaliz($value['e-mail'], $config->conf['subject'], $config->conf['message'], $aa, $bb);
                  //$send->sendAnaliz('dob@clingenetic.com.ua', $config->conf['subject'], $config->conf['message'], $aa, $bb);
                  $send->FixSendFile($value['id'], $config->conf['message'], $aa);        //Фиксируем отаправленные файлы.
                  $send->log->sendLog('Письмо пациенту '.$value['fio'].', на мейл '.$value['e-mail'].' - отправленно!', $bb);//Фиксацмя действия в логе.
                  $n++;                                                                     //Инкремент счетчика отправленных писем. 
                  unset($aa, $bb, $arr_file);                                               //Штатный сброс для следующенго прохода цикла. 
              }
              if($n >> $config->conf['cycles']){                                            //Проверка количества проходов. 
                  break;                                                                    //При пивышении, выход из цикла.
              }
              unset($arr_file);                                                             //Зброс списка файлов для отправки в конце прохода цикла.
          }
          unset($key, $value);                                                              //Штатный сброс.
      }
  }
  else{                                                                         //В неразрешенное время отправки прекращена.
      echo 'Запуск автоотпраки прекращен!';
  }
  gc_collect_cycles();
  //echo '<pre>'.print_r($list_send).'</pre>'; 
  //echo mb_detect_encoding($str, "auto");
  
?>
