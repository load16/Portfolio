<?php
  //Класс ответственный за работу с файлами.
  //include_once 'classes/register.php'; 
  
  
  
  class files{
      //var $register;                                        //Объект для работы с реестром пациентов.
      
      //Конструктор класса.
      function __construct(){
           //$this->register = new register();                 //Создание объекта реестра пациентов.
      }
       
       //Деструктор класса.
      function __destruct(){
          
      }
      
      
      
      //Метод копирования файлов по шаблону.
      
      
      
      //Метод проверки корректности имени файла, для предотвращения неправильной работы на линухе при обработке списка файлов.
      public function chekNameFile($nameFile = null){
          if($nameFile != null){
              $arr = str_split($nameFile);                                      //Преобразовываем сроку в массив.
              $n = 0;                                                           //Установка счетчика.
              foreach($arr as $k => $v){                                        //Обходим массив.
                  if($v == '.' && $n == 0){                                     //Если сначала - точка, то это не правильное имя.
                      unset($nameFile, $n, $arr, $k, $v);                       //Штатный сброс.
                      return false;                                             //Возвращаем фальш
                  }
                  $n++;                                                         //Инкремент счетчика.
              }
              unset($k, $v);
          }
          return true;                                                          //Возвращаем истину.
      }
      
      
      //Метод полученя массива файлов и каталогов для формирования поисковой базы.
      public function getArryBaseFile($dir = null){
          if($dir != null){
              $os = PHP_OS;                                                     //Определяем ОС.
              $os = strtolower($os);
              $arr = $this->getArrayFilePach($dir);                             //Получаем массив файлов и каталоов.
              if(count($arr) >= 1){                                             //Проверка наличия данных каталога.
                  foreach($arr as $key => $value){                              //Обходим массив.
                       if($this->chekNameFile($value)){                         //Проверка корректности имени файла.
                           if (strpos($os, 'win') !== false){                   //Проверка наличичя ОС Windows.
                                $ddd = $dir.$value.'\\';                        //Формируе имя каталога.
                           }
                           else{                                                    //Иначе оставляем как есть.
                                $ddd = $dir.$value.'/';                             //Формируе имя каталога.
                           }
                          $dd = $this->getArrayFilePach($ddd);                      //Получаем массив файлов.
                          if(count($dd) >= 1){                                      //Если файлы есть, то это каталог.   
                              $rr = $this->getArryBaseFile($ddd);                   //Выполняем рекурсивное получение данных.
                              if(count($rr) >= 1){                                  //Проверяем наличие данных.
                                  foreach($rr as $k => $v){                         //Обходим полученный массив.
                                      if($this->chekNameFile($v)){                  //Проверка коректности имени файла.
                                          $ret[] = $v;                              //Формируем массив для возврата. 
                                      }
                                  }
                                  unset($k, $v, $rw, $rr);                          //Штатный сброс переменных.
                              }
                              else{                                                 //Иначе не предусматривается.
                                  
                              }
                          }
                          else{                                                     //Если не каталог, то формаруем данные как для файла.
                              if (strpos($os, 'win') !== false){                     //Проверка наличичя ОС Windows.
                                  $rw['dir'] = mb_convert_encoding($dir, "UTF-8", "WINDOWS-1251");
                                  $rw['name'] = mb_convert_encoding($value, "UTF-8", "WINDOWS-1251"); //Декодируем под ОС. 
                              }
                              else{                                                  //Иначе оставляем как есть.
                                  $rw['dir'] = $dir;
                                  $rw['name'] = $value;
                              }  
                              $ret[] = $rw;                                           //Формируем массив для возврата.; 
                          }
                       }        
                  }
                  unset($key, $value, $dd,  $ddd, $rr, $arr, $rw);                    //Штатный сброс переменных.
              }
              else{                                                                 //Если файл, то готовим данные для файла.
                  
                  $di = $this->getArrDirFile($dir);                                 //Получаем массив имени и пути файла.
                  if (strpos($os, 'win') !== false){                                //Проверка наличичя ОС Windows.
                              $rw['dir'] = mb_convert_encoding($di['dir'], "UTF-8", "WINDOWS-1251");
                              $rw['name'] = mb_convert_encoding($di['name'], "UTF-8", "WINDOWS-1251"); //Декодируем под ОС. 
                  }
                  else{                                                             //Иначе оставляем как есть.
                              $rw['dir'] = $di['dir'];
                              $rw['name'] = $di['name'];
                  }  
                  $ret[] = $rw;                                                     //Готовим данные для возврата

                  unset($rw, $di);
              }
              return $ret;                                                          //Возврат результата.
          }
      }
      
      
      //Метод получения краткого имени с полного пути файла.
      public function getArrDirFile($fullNaneFile = null){
          if($fullNaneFile != null){
              $os = PHP_OS;                                                     //Определяем ОС.
              $os = strtolower($os);
               if (strpos($os, 'win') !== false){                               //Если винда, то.
                   $ex = '\\';                                                  //Определяем такой разделитель.
               }
               else{
                   $ex = '/';                                                   //Если не винда, то такой разделитель. 
               }
               $arr = explode($ex, $fullNaneFile);                              //Разделяем полное имя файла.
               if(count($arr) >= 1){                                            //Проверка наличия данных
                   foreach($arr as $k => $v){
                        $name = $v;                                              //Находим паследний элемент.
                   }
                   unset($k, $v);                                               //Штатный сброс.
                   $NaneFile = $fullNaneFile.'add';                             //Добавляем несколько символов для корректного разделения.
                   $rr = explode($name, $NaneFile);                             //Разделяем на краткое имя файла.
                   $rrr['dir'] = $rr['0'];                                      //Получаем массив для возврата.
                   $rrr['name'] = $name;
                   unset($rr, $name, $fullNaneFile, $os);                       //Штатный сброс.
                   return $rrr;                                                 //Возвращаем краткое имя файла.
               }
          }
      }
      
      
      
      //Метод получения массива каталогов.
      public function getArrayDir($dir = null){
          if($dir != null){                                                         //Проверка наличия каталога.
              $os = PHP_OS;                                                         //Определяем ОС.
              $os = strtolower($os);
              $arr = $this->getArrayFilePach($dir);                                 //Полкчаем массив файлов.
              if(count($arr) >= 1){                                                 //Проверка наличия массива файлов.
                  foreach($arr as $k => $v){                                        //Обходим массив.
                      if (strpos($os, 'win') !== false){                            //Проверка наличичя ОС Windows.
                              $a = $this->getArrayFilePach($dir.'\\'.$v);           //Получаем массив файлов в каталоге.  
                       }
                       else{                                                        
                              $a = $this->getArrayFilePach($dir.'/'.$v);            //Получаем массив файлов в каталоге.  
                       }
                      if(count($a) >= 1){                                           //Проверка наличия файлов в каталоге.
                            $ret[] = $v;                                            //Формируем массив для возврата.
                      }
                  }
                  unset($k, $v, $arr, $os, $dir);                                   //Штатный сброс.
                  return $ret;                                                      //Возвращаем массив каталогов.
              }
          }
      }
      
   
      
      //Метод получения массива файтов и каталогов в каталоге.
      public function getArrayFilePach($dir = null){
          if($dir != null){
              if(is_dir($dir)){
                  $files = scandir($dir);                                          //сканируем (получаем массив файлов)
                  //$files = scandir(iconv("UTF-8", "cp1251", $dir));
                  array_shift($files);                                             // удаляем из массива '.'
                  array_shift($files);                                             // удаляем из массива '..' 
                  return $files;      
              }
          }
      }
      
      
      //Метод удаления файла.
      public function delFile($filename = null){
          if($filename != null){
              if(file_exists($filename)){
                  unlink($filename);
              }
          }
      }
      
      //Метод создания пустого файла
      public function createFile($filename){
           if($filename != null){
                if(!file_exists($filename)){
                    fclose(fopen($filename,'x'));
                }
           }
      }
      
      
      
      //Метод получения текущего пути для файлов.
      public function getPatchFile(){
          $p = $_SERVER['SCRIPT_FILENAME'];            
          $a = explode('index.php', $p);
          $p = $a['0'];
          $os = PHP_OS;                                                         //Определяем ОС.
          $os = strtolower($os);
          if (strpos($os, 'win') !== false){                                    //Если винда, то
              $p = str_replace('/', '\\', $p);                                  //Заменяем символы.
          }    
          unset($a, $os);
          return $p;
      }
      
      
      
      //Метод получения списка файлов в каталоге.
      public function getListFile($patch = null){
          if($patch != null){                                                    //Проверка наличия пути.
              $arr = scandir($patch);
              unset($arr['0'], $arr['1']);
              return $arr;
          }
      }
      
      //Метод получения данных с файла.
      public function getDataFile($name = null){
          if($name != null){
              $ret = '';
              if(file_exists($name)){                                            //Проверка наличия файла.
                  $fp = fopen($name, "rt");                                     //Открываем файл в режиме чтения
                  if($fp){
                        while (!feof($fp)){
                              $mytext = fgets($fp, 999);
                              $ret .= $mytext;
                        }
                  }
                  fclose($fp);                                                    //Закрытие файла.
                  unset($mytext, $fp, $name);
                  return $ret;                                                    //Возвращае результат.
              }
          }
      }
      
      //Метод записи данных в файл.
      public function setDataFile($text = null, $arr = null){
          if($text != null){                                                                                    //Проврка наличия текста.
              $d = '';
              if(count($arr) >= 1){                                                                                //Проврка наличия массива аналитических данных.
                  foreach($arr as $k => $v){
                      $d .= $k.' => '.$v.', ';
                  }
                  unset($k, $v);
              }
              
              $url = router::getCurrentUrl();
              $ip = $_SERVER["REMOTE_ADDR"];                                                                    //IP пользователя.
              $idUser = $_SESSION['login']['id'];                                                                //Получаем ИД пользователя.
              $nameModule = router::getNameModule($url);
              $nameFile = log::getNameFile();                                                                    //Получаем имя файла.
              $obj = new data('Europe/Kiev');                                                                    //Получаем объект дата.
              $time = $obj->time_i;                                                                                //Получить текущее время.
              $send = $time.'|'.$ip.'|'.$idUser.'|'.$text.'|'.$d;                                                //Получаем сообщение.
              unset($obj);
              for($a = 0; $a <= 5; $a++){                                                                        //Делаем 5 попыток.
                  if(files::writeFile($nameFile, $send)){                                                            //Если запись прошла то возвращаемся.
                      unset($a, $text, $arr, $d, $url, $ip, $idUser, $nameModule, $time, $send);
                      return;
                  }
                  else{                                                                                            //Иначе ждем секунду.
                      sleep(1);
                  }
              }
               print '<script> alert(\'Внимание! Файл лога '.$nameFile.' открыт для записи.\') </script>';        //Если записи не удалась, выдаем сообщение.
          }
      }
      
      
      //Метод дописывания в файл с новой строки.
      public function writeFile($filename = null, $somecontent = null){
          if($filename != null && $somecontent != null){                                                        //Проверка необходимого.
              if(file_exists($filename)){                                                                        //Проверка наличия файла.
                  
              }
              else{
                  $fp = fopen($filename, 'w');                                                                    //Создаем файл.
                  fclose($fp);                                                                                     //Закрытие файла
              }
              
              //Вначале убедимся, что файл существует и доступен для записи.
              if(is_writable($filename)){                                                                          //Проверка наличия файла.
                    $handle = fopen($filename, 'a');
                  if($handle){                                                                                      //Проверка открит ли файл.
                        // Записываем $somecontent в наш открытый файл.
                      if(!fwrite($handle, $somecontent)){                                                  //Если запись не произведена, то
                            fclose($handle);                                                                        //Закрытие файла.
                          return false;                                                                            //Возвращаем лож.
                      }
                      else{
                            fclose($handle);                                                                        //Закрытие файла
                          return true;                                                                            //Возвращаем истина, запись сделана.
                      }
                  }
                  else{
                        return false;                                                                                 //Если нет, то возвращаем ложь.
                  }
              }
              else{
                    return false;                                                                                    //Если фал не доступен, возвращаем ложь.
              }
          }
          return false;  
      }
      
  }
?>
