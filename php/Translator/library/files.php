<?php
  //Класс ответственный за работу с файлами.
  class files{
      
      
      //Конструктор класса.
      function __construct(){
           
      }
       
       //Деструктор класса.
      function __destruct(){
          
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
      public function getPatchFile($patch = null){
            $p = $_SERVER['SCRIPT_FILENAME'];            
            $a = explode('index.php', $p);
            $p = $a['0'];
          if($patch != null){
              $p .= $patch.'/';
          }
          $p = explode('/', $p);
          $p = implode("\\", $p);
          unset($a);
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
