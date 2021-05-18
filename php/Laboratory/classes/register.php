<?php
  //Класс отвественный за ведение реестра пациентов.
  
  
  
  include_once 'classes/SqlAdapter.php';
  include_once 'library/data.php';
  
  class register{
      
      var $data;
      
      
      
      //Конструктор класса.
      function __construct(){
          $this->data = new data('Europe/Kiev');
      }
      
      
      //Деструктор класса.
      function __destruct(){
          
      }
      
      
      public function prepareFIO($fio = null){
          if($fio != null){
              $q = explode(' ', $fio);
              //$fio = $q['0'].' '. mb_substr($q['1'], 0, 1).'. '.mb_substr($q['2'], 0, 1).'.'; 
              $fio = $q['0'];
              unset($q);
              return $fio;
          }
      }
      
      
      //Метод авторизации пользователя
      public function authorizationUsers($idUsers = null, $idCodeRights = null){
          if($idUsers != null && $idCodeRights != null){
              $query = 'SELECT
                        *
                        FROM
                        `rights_user`
                        WHERE
                        `id_u` = \''.$idUsers.'\'
                        AND
                        `id_r` = \''.$idCodeRights.'\'';
              $arr = SqlAdapter::select_sql($query);
              if(count($arr) == 1){
                  return true;
              }
              else{
                  return false;
              }
          }
      }
      
      
      //Метод получения списка файлов для рассылки для пациента по его ИД.
      public function getListFileSendFio($id = null, $send_all = false){
          if($id != null){                                                          //Проверка наличия ИД.
              $ar_p = $this->geteRecordId($id, 'patients');                         //Получаем запись с базы по ИД.
              if($ar_p != null){
                  $fio = $ar_p['0']['fio'];                                         //Получаем ФИО с базы.
              }
              $q = explode(' ', $fio);
              $fio = $q['0'].' '. mb_substr($q['1'], 0, 1).'. '.mb_substr($q['2'], 0, 1).'.';
          }              
          if($fio != null){
              $f[] = $fio;
              $f[] = $q['0'].' '. mb_substr($q['1'], 0, 1);
              //$f[] = $q['0'];
              //echo '<pre>'.print_r($f).'</pre>';
                                                                        //Фомируем массив возможных вариантов поисковых фамилий.
              /*
              $a = explode('.', $fio);
              $f[] = $a[0].'.';
              $a = explode(' ', $fio);
              $f[] = $a['0'];
              */
              unset($a, $fio);                                                      //Штатный сброс переменных.
              foreach($f as $k => $v){                                              //Обходим массив возможных вариантов фамилий.
                  if($send_all){                                                    //Проверка наличия флага полного поиска.
                      $arr = $this->getListFileFio($v, 'files_full');               //Поиск в базе полной информации.
                  }
                  else{                                                             //Если нет флага полного поиска, то
                      $arr = $this->getListFileFio($v, 'files');                    //Ищем в не полной безе.
                  }
                  if($arr != null){                                                 //Проверяем наличие данных.
                      $ret = $arr;                                                  //Фиксируем полученный список.
                      //echo '<pre>'.print_r($ret).'</pre>'; 
                      foreach($arr as $kk => $vv){                                  //Обходим массив отправляемых файлов.
                          if($this->checkFileSend($vv['fullname'], $id)){           //Если файл уже отправлен, то
                              unset($ret[$kk]);                                   //Удаляем его из списка.
                          }
                      }
                      unset($kk, $vv);                      
                      return $ret;                                                  //Возвращаем данные.   
                  }
                   
              }
              unset($k, $v);
          }
      }
      
      
      //Метод проверки файла на предмет того что он уже отправлен пациенту.
      public function checkFileSend($file = null, $id_p = null){
          if($file != null && $id_p != null){
              $query = 'SELECT
                        *
                        FROM
                        `send_files`
                        WHERE
                        `id_p` = \''.$id_p.'\'';
              $arr = SqlAdapter::select_sql($query);  
              if(count($arr) >> 0){                                                     //Проверка налдичия данных.
                  foreach($arr as $k => $v){                                            //Обходим полученные данные.
                      if($v['fullname'] == $file){                                      //Проверка неличия отправленного файла.
                          return true;                                                  //Если находим, то возвращаем истину.
                      }
                  }
                  unset($k, $v);
              }
          }
          return false;                                                                 //Когда не находим возвращаем ложь.
      }
       
      
      
      
      //Метод плучения данных отправленного файла пациенту.
      public function getDataFileSend($file = null, $id_p = null, $arrPach = null){
          if($file != null && $id_p != null){                                           //Проверка необходимого
              if(count($arrPach) >= 1){                                                 //Проверка наличия массива путей для адаптации.
                  $file = $this->AdaptingFileName($file, $arrPach);                     //Адаптируем имя файла.  
              }
              $query = 'SELECT
                        *
                        FROM
                        `send_files`
                        WHERE
                        `id_p` = \''.$id_p.'\'
                        AND
                        `fullname`  LIKE \'%'.$file.'\'';
              $arr = SqlAdapter::select_sql($query);                                    //Выполняем запрос, получаем данные.
              $arr_id = $this->geteRecordId($id_p, 'patients');
              if(count($arr) >= 1){                                                     //Проверка наличия данных
                  $ret = '';                                                            //Первичная инициализация переменоой.
                  foreach($arr as $k => $v){                                            //Обходим данные.
                      if(count($arr_id) == 1){
                          $ret .= ' Отправленно пациенту - '.$arr_id['0']['fio'].'
';
                      }
                      $ret .= 'Дата отправки - '.$v['data'].'
';                    //Готовим данные.
                      $ret .= 'E-mail - '.$v['e-mail'].'
'; 
                      $ret .= 'Краткое имя файла - '.$v['file'].'
';
                      $ret .= 'Полное имя файла - '.$v['fullname'].'
'; 
                      $ret .= '
';
                  }
                  unset($k, $v, $arr, $file, $id_p, $query);                            //Штатный сброс.
              }
              return $ret;                                                              //Возвращаем подготовленные данные. 
          }
      }
      
      
      //Метод получения адаптированного имени файла для последующего сравнения в условиях.
      public function AdaptingFileName($nemefile = null, $arrPach = null){
          if($nemefile != null){                                                        //Проверка наличия двннфх.
              if(count($arrPach) >= 1){                                                 //Проверка наличия массива путей.
                  $modname = 'temp'.$nemefile;                                          //Начальная модификация имени файла. 
                  foreach($arrPach as $k => $v){                                        //Обходим массив путей.
                      $arr = explode($v, $modname);                                     //Разделяем имя файла на путь.
                      if($arr['1'] != ''){                                              //Проверка наличия адаптированного имени.
                          unset($nemefile, $modname);                                   //Штатный сброс.
                          return $arr['1'];                                             //Возвращаем адаптированое имя файла.
                      }
                      unset($arr);
                  }
                  unset($k, $v, $modname);                                              //Штатный сброс.
              }
          }
      }
      
      
      
      //Метод получения списка файлов по фамилии.
      public function getListFileFio($fio = null, $base = null){
          if($fio != null && $base != null){
              $query = 'SELECT
                        *
                        FROM
                        `'.$base.'`
                        WHERE
                        `name` LIKE \'%'.$fio.'%\'';
              return SqlAdapter::select_sql($query);
          }
      }
      
      
      //Метод записи списка рассылки в базу.
      public function putListSend($arr = null){
          if($arr != null){
              $this->ClearBase('sent_list');
              foreach($arr as $k => $v){
                  $aa = explode(' ', $v['fio']);
                  $fio = $aa['0'].' '.mb_substr($aa['1'], 0, 1).'.'.mb_substr($aa['2'], 0, 1).'.';
                  if($v['send_all']){
                      $send_all = 'TRUE';
                  }
                  else{
                      $send_all = 'FALSE';
                  }
                  $query = 'INSERT INTO
                              `sent_list`
                              SET
                              `id` = \''.$v['id'].'\', 
                              `fio` = \''.$fio.'\',
                              `e-mail` = \''.$v['e-mail'].'\',
                              `send_all` = '.$send_all;
                  SqlAdapter::select_sql($query);
              }
              unset($k, $v, $fio, $send_all, $arr, $query, $aa);
          }
      }
      
      
      //Метод получения спика рассылки по активным записям.
      public function getListSend(){
          $query = 'SELECT
                    *
                    FROM
                    `patients`
                    WHERE
                    `active` = \'1\'
                    OR
                    `send_all` = \'1\''; 
          return SqlAdapter::select_sql($query);
      }
      
      
      //Метод фиксации файла в базе отправленных файлов.
      public function putSendFile($id_p = null, $dir = null, $file = null){
          if($id_p != null && $dir != null && $file != null){                                   //Проверяем наличее необходимого.
              $arr_p = $this->geteRecordId($id_p, 'patients');                                  //Получаем данные про пациента.
              $email = $arr_p['0']['e-mail'];                                                   //Подготовка мейла.
              //$dir = str_replace('\\', '\\\\', $dir);                                           //Подготовка имени файла к записи в базу.
              //$file = str_replace('\\', '\\\\', $file);
              $dir = $this->correctionString($dir);                                             //Подготовка имени файла к записи в базу.
              $file = $this->correctionString($file);
              $fullname = $dir.$file;                                                           //Получаем полное имя файла.
              if($arr_p != null){
                  $query = 'INSERT INTO
                                  `send_files`
                                  SET
                                  `id_p` = \''.$id_p.'\',
                                  `dir` = \''.$dir.'\',
                                  `file` = \''.$file.'\',
                                  `fullname` = \''.$fullname.'\',
                                  `e-mail` = \''.$email.'\',
                                  `data` = CURRENT_TIMESTAMP()'; 
                  SqlAdapter::select_sql($query);
              }   
          }
      }
      
      
      //Метод фиксации отправленного письма в базе.
      public function putSendMail($id_p = null, $body = null){
          if($id_p != null && $body != null){
              $arr_p = $this->geteRecordId($id_p, 'patients');                                  //Получаем данные про пациента.
              $email = $arr_p['0']['e-mail'];                                                   //Получем мейл.
              if($arr_p != null){                                                               //Проверка наличия данных.
                  $query = 'INSERT INTO
                                  `send_mail`
                                  SET
                                  `id_p` = \''.$id_p.'\',
                                  `e-mail` = \''.$email.'\',
                                  `body` = \''.$body.'\',
                                  `data` = CURRENT_TIMESTAMP()'; 
                  SqlAdapter::select_sql($query);
              }
          }
      }
      
                                                                                     
      //Метод обновления базы файлов анализов.
      public function updateBaseAnalizFile($arr = null, $full = false, $noClear = false){
          if($arr != null){
              if($full){                                                                    //Проверка налиия флага полной базы.
                  $base = 'full_files';                                                     //Если полная база, то соотвественно и обрабатываем ее.
              }
              else{                                                                         //Иначе краткая база.
                  $base = 'files'; 
              }
              if(!$noClear){                                                                //Проверка флага стирания перед записью.
                  $this->ClearBase($base);                                                        //Удаляем все данные. 
              }
              foreach($arr as $k => $v){                                                        //Обходим массив данных.
                  //$dir = str_replace('\\', '\\\\', $v['dir']);                                  //Исправляем имя директории.
                  //$name = str_replace('\\', '\\\\', $v['name']);
                  //$dir = str_replace('\'', '"', $dir);                                     //Исправляем имя директории.
                  //$name = str_replace('\'', '"', $dir);
print '<pre>';
print_r($v);
print '</pre>';
print '<br/>';
                  $dir = $this->correctionString($v['dir']);                                  //Исправляем имя директории.
                  $name = $this->correctionString($v['name']);
                  $full = $dir.$name;
                  $query = 'INSERT INTO
                              `'.$base.'`
                              SET
                              `dir` = \''.$dir.'\',
                              `name` = \''.$name.'\',
                              `fullname` = \''.$full.'\'';
                  
                  SqlAdapter::select_sql($query);                                               //Добавляем данные. 
              }
              unset($k, $v, $arr, $dir, $name, $base);                                          //Штатный сброс данных.
          }
      }
      
      
      //Метод внеения данных в базу.
      public function writeDataBase($arr = null){
           if(count($arr) >= 1){                                                                //Проверка наличия данных.
               foreach($arr as $key => $value){                                                 //Обходим массив записей.
                   if($this->checRecord($value['fio'], $value['e-mail'])){                      //Проверяем наличие такойже записи.
                        $this->writeData($value, true);                                         //Если есть, то обновляем.
                   }
                   else{
                       $val_fio = $this->searchData('patients', 'fio', $value['fio']);              //Находим записи с таким же именем.
                       $val_mail = $this->searchData('patients', 'e-mail', $value['e-mail']);
                       if(                                                                          //Валидация вносимых данных.
                       count($val_fio) >= 0                                                         //Не должно быть записей с одинаковым ФИО. 
                       && count($val_mail) >= 3                                                     //Не должно больше 3 записей с одинаковыми мейлами.
                       ){
                           
                       }
                       else{
                           $this->writeData($value);                                                //Если нет такой записи, то добавляем.
                       }
                       unset($val_fio, $val_mail);                                                  //Штатный сброс.
                       
                   }
               }
               unset($key, $value);                                                             //Штатный зброс переменных.
           }
      }
      
      
      //Метод дизактивации пациентов старше установленного дня.
      public function deyUpdateBase($dey = null){
          if($dey != null){
              $query = 'UPDATE
                        `patients`
                        SET
                        `send_all` = FALSE,
                        `active` = FALSE
                        WHERE
                        `data_updates` <= NOW() - INTERVAL '.$dey.' DAY';        
               SqlAdapter::select_sql($query);        
          }
      }
      
      
      
      //Метод дизактивации записи по ИД.
      public function deactivationRecord($id = null){
          if($id != null){
               $query = 'UPDATE
                        `patients`
                        SET
                        `send_all` = \'0\',
                        `active` = \'0\'
                        WHERE
                        `id` = \''.$id.'\'';
               SqlAdapter::select_sql($query);
          }
      }
      
      /*
      //Метод получения журнальных данных по ИД пациента
      public function getAllDataID($id = null){
          if($id != null){
              $query = 'SELECT
                        analyzes_patients.`id_p`,
                        analyzes_patients.`id_a`,
                        analyzes_patients.`datatime`,
                        analyzes_patients.`data`,
                        analyzes.`name_a`,
                        issued.`amount`,
                        funds.`name_f`,
                        funds.`measure`
                        FROM
                        `analyzes_patients`
                        Inner Join analyzes ON analyzes.`id` = analyzes_patients.`id_a`
                        Inner Join issued ON issued.`id_p` = analyzes_patients.`id_p` AND issued.`id_a` = analyzes_patients.`id_a`
                        Inner Join funds ON funds.`id` = issued.`id_f`
                        WHERE
                        analyzes_patients.`id_p` = \''.$id.'\'
                        ORDER BY
                        analyzes_patients.`data` ASC';
              $arr = SqlAdapter::select_sql($query);
              return $arr;
          }
      }
      */
      
      
      //Метод получения записей полной отправки.
      public function getAllSend(){
         $query = 'SELECT
                    *
                    FROM
                    `patients`
                    WHERE
                    `send_all` = \'1\'';
          $arr = SqlAdapter::select_sql($query);
          return $arr;
      }
      
      
      //Метод очистки базы.
      public function ClearBase($base = null){
          if($base != null){
              $query = 'TRUNCATE TABLE `'.$base.'`';                                                //Удаляем все данные.
              SqlAdapter::select_sql($query);
          }
      }
      
      
      
      //Метод удаления записи из базы по ИД
      public function delRocordId($id = null, $base = null){
          if($id != null && $base != null){
              $query = 'DELETE
                        *
                        FROM
                        `'.$base.'`
                        WHERE
                        `id` = \''.$id.'\'';
              SqlAdapter::select_sql($query);
          }
      }
      
      
      //Метод получения данных с базы по ИД
      public function geteRecordId($id = null, $base = null){
          if($id != null && $base != null){
              $query = 'SELECT
                        *
                        FROM
                        `'.$base.'`
                        WHERE
                        `id` = \''.$id.'\'';
              $arr = SqlAdapter::select_sql($query);
              return $arr;
          }  
      }
      
      
      //Метод проверки наличия записи в базе.
      private function checRecord($fio = null, $mail = null){
          if($fio != null && $mail != null){
              $query = 'SELECT
                        *
                        FROM
                        `patients`
                        WHERE
                        `fio` = \''.$fio.'\'
                        AND
                        `e-mail` = \''.$mail.'\'';
              $arr = SqlAdapter::select_sql($query);
              if(count($arr) >> 0){
                  return true;
              }
              else{
                  return false;
              }
          }
      }
      
      //Метод исправления строки для записи в базу.
      public function correctionString($string = null){
          if($string != null){
              $string = str_replace('\\', '\\\\', $string);                                  //Исправляем строку от обратного слеша.
              $string = str_replace('\'', '"', $string);                                     //Исправляем строку от одинарных кавычек.
              return $string;                                                                //Возвращаем результат.
          }
      }
      
      
      //Метод валидации на враждебный код.
      public function validationSring($text = null){
          if($text != null){
            $text = strtolower($text);                      //Приравниваем текст параметра к нижнему регистру.
            $check[] = 'select';                            //Создаем массив враждебного кода.            
            $check[] = 'union';
            $check[] = 'order';
            $check[] = 'where';
            $check[] = 'char';
            $check[] = 'from';
            $check[] = 'insert';
            $check[] = 'delete';
            $check[] = 'create';
            $check[] = 'eval';
            foreach($check as $key => $value){              //Обходим массив.
                $var = explode($value, $text);              //Разчепляем водимые данные.
                if(count($var) >= 2){                       //Если находми слова из массива, то сообщаем.
                    return false;
                }
            }
            unset($key, $value);
            return true;
          }
      }
      
      
      //Метод записи пациента в базу.
      private function writeData($arr = null, $update = false){
          if(count($arr) >= 1){
              foreach($arr as $key => $value){                      //Обходим массив вводимых данных.
                  if($this->validationSring($value)){               //Если валидация на враждебный код прошла, то
                      $rr[$key] = $this->correctionString($value);  //Сохраняем данные.
                  } 
              }
              $arr = $rr;                                           //Фиксируем данные.
              unset($key, $value, $rr);                             //Штатный сброс.
              $town = $arr['town'];
              $address = $arr['address'];
              $birth = $arr['birth'];
              $phone = $arr['phone'];
              $electronic = $arr['electronic'];
              $about = $arr['about'];
              $consultant = $arr['consultant'];
              $reception = $arr['reception'];
              $description = $arr['description'];
              $fio = $arr['fio'];
              $mail = $arr['e-mail'];
              if($update){
                  $query = 'UPDATE
                              `patients`
                              SET
                              `town` = \''.$town.'\',
                              `address` = \''.$address.'\',
                              `birth` = \''.$birth.'\',
                              `phone` = \''.$phone.'\',
                              `electronic` = \''.$electronic.'\',
                              `about` = \''.$about.'\',
                              `consultant` = \''.$consultant.'\',
                              `reception` = \''.$reception.'\',
                              `description` = \''.$description.'\',
                              `data_updates` = \''.$this->data->data_i.' '.$this->data->time_i.'\',
                              `active` = \'1\'
                              WHERE
                              `fio` = \''.$fio.'\'';
              }
              else{
                   $query = 'INSERT INTO
                              `patients`
                              SET
                              `fio` = \''.$fio.'\',
                              `e-mail` = \''.$mail.'\',
                              `town` = \''.$town.'\',
                              `address` = \''.$address.'\',
                              `birth` = \''.$birth.'\',
                              `phone` = \''.$phone.'\',
                              `electronic` = \''.$electronic.'\',
                              `about` = \''.$about.'\',
                              `consultant` = \''.$consultant.'\',
                              `reception` = \''.$reception.'\',
                              `description` = \''.$description.'\',
                              `data_create` = \''.$this->data->data_i.' '.$this->data->time_i.'\',
                              `data_updates` = \''.$this->data->data_i.' '.$this->data->time_i.'\',
                              `active` = \'1\'';
                     
              }
              SqlAdapter::select_sql($query);  
              
          }
      }
      
      //Метод обновления данны пациента по ИД
      public function updateDataId($arr = null, $id = null){
          if(count($arr) >= 1 && $id != null){
              foreach($arr as $key => $value){                      //Обходим массив вводимых данных.
                  //if($this->validationSring($value)){               //Если валидация на враждебный код прошла, то
                      $rr[$key] = $this->correctionString($value);  //Сохраняем коректированные данные.
                  //}
                  //else{
                  //    return false;
                  //} 
              }
              unset($key, $value);
              $arr = $rr;
              $town = $arr['town'];
              $address = $arr['address'];
              $birth = $arr['birth'];
              $phone = $arr['phone'];
              $electronic = $arr['electronic'];
              $about = $arr['about'];
              $consultant = $arr['consultant'];
              $reception = $arr['reception'];
              $description = $arr['description'];
              $fio = $arr['fio'];
              $mail = $arr['e-mail'];
              $query = 'UPDATE
                              `patients`
                              SET
                              `fio` = \''.$fio.'\',
                              `town` = \''.$town.'\',
                              `address` = \''.$address.'\',
                              `birth` = \''.$birth.'\',
                              `phone` = \''.$phone.'\',
                              `electronic` = \''.$electronic.'\',
                              `about` = \''.$about.'\',
                              `consultant` = \''.$consultant.'\',
                              `reception` = \''.$reception.'\',
                              `description` = \''.$description.'\',
                              `data_updates` = \''.$this->data->data_i.' '.$this->data->time_i.'\'
                              WHERE
                              `id` = \''.$id.'\'';
              SqlAdapter::select_sql($query);
              return true; 
          }
      }
      
      
      //Метод динамического поиска в базе.
      public function searchDinData($base = null, $name = null, $value = null){
          if($base != null && $name != null && $value != null){
              $query = 'SELECT
                        *
                        FROM
                        `'.$base.'`
                        WHERE
                        `'.$name.'` LIKE \'%'.$value.'%\'';
              return SqlAdapter::select_sql($query);
          }
      }
      
      
      //Метод поиска в базе.
      public function searchData($base = null, $name = null, $value = null){
          if($base != null && $name != null && $value != null){
              $query = 'SELECT
                        *
                        FROM
                        `'.$base.'`
                        WHERE
                        `'.$name.'` = \''.$value.'\'';
              return SqlAdapter::select_sql($query);
          }
      }
      
      
      //Метод получения уникальных зачений с базы с выборкой.
      public function searchUniqueSelect($base = null, $name = null, $value = null, $arrPoleSelect = null){
          if($base != null && $name != null && $value != null){
              if(count($arrPoleSelect) >= 1){                                       //Проверка ниличия массива полей для выборки.
                  $query = 'SELECT DISTINCT';
                  foreach($arrPoleSelect as $k => $v){                              //Обходим поля формируем запрос.
                      $query .= '
                            `'.$v.'`';
                  }
                  unset($k, $v);                                                    //Штатный сброс.
                  $query .='
                            FROM
                            `'.$base.'`
                            WHERE
                            `'.$name.'` = \''.$value.'\'';
              }
              else{                                                                 //Если нет полей для выборки, то показываем все поля.
                  $query = 'SELECT DISTINCT
                            *
                            FROM
                            `'.$base.'`
                            WHERE
                            `'.$name.'` = \''.$value.'\'';
              }
                  
              return SqlAdapter::select_sql($query);
          }
      }
      
      
      //Метод получения всех уникальных зачений с базы.
      public function searchUniqueData($base = null, $arrPoleSelect = null){
          if($base != null){
              if(count($arrPoleSelect) >= 1){                                       //Проверка ниличия массива полей для выборки.
                  $query = 'SELECT DISTINCT';
                  foreach($arrPoleSelect as $k => $v){                              //Обходим поля формируем запрос.
                      $query .= '
                            `'.$v.'`';
                  }
                  unset($k, $v);                                                    //Штатный сброс.
                  $query .='
                            FROM
                            `'.$base.'`';
              }
              else{                                                                 //Если нет полей для выборки, то показываем все поля.
                  $query = 'SELECT DISTINCT
                            *
                            FROM
                            `'.$base.'`';
              }
                  
              return SqlAdapter::select_sql($query);
          }
      }
      
      
      //метод получения данных с базы по временным характеристикам.
      public function searchDataTimeBase($base = null, $pole = null, $year = null, $month = null, $date = null, $time = null){
          if($base != null && $pole != null){
              $arr = $this->getAllRecord($base);                                        //Получаем все записи с базы.
              if(count($arr) >= 1){                                                     //Проверка наличия данных.
                  foreach($arr as $k => $v){                                            //Обходим данные.
                      
                  }
                  unset($k, $v);
              }
          }
      }
      
      //Метод получения всех записей из базы
      public function getAllRecord($base = null){
          if($base != null){
              $query = 'SELECT
                        *
                        FROM
                        `'.$base.'`';
              return SqlAdapter::select_sql($query);
          }
      }
      
      
      //Метод добавления записи в базу.
      public function insertRecord($base = null, $arr = null){
          if($base != null && count($arr) >= 1){                                            //Проверка необходимого.
              $query = 'INSERT INTO
                        `'.$base.'`
                        SET
                        ';
              $n = 0;                                                                       //Начальная установка счетчика.
              foreach($arr as $k => $v){                                                    //Обходим массив параметров.
                  if($n >> 0){                                                              //Если не первый проход, то доб. запятую.
                      $query .= ',
                        ';
                  }
                  $query .= '`'.$k.'` = \''.$v.'\'';                                        //Дописываем запрос.
                  $n++;
              }
              unset($k, $v);                                                                //Штатный сброс.
              //$_SESSION['query'] = $query;
              SqlAdapter::select_sql($query);
          }
      }
      
      
      //Метод обновления всех записей базы
      public function updateAllRecord($base = null,  $arr = null){
           if($base != null && count($arr) >= 1){                                                           //Проверка необходимого.
               foreach($arr as $k => $v){                                                                   //Обходим массив.
                   $rv = $v;                                                                                //Создаем вторичный массив.
                   foreach($rv as $kk => $vv){                                                              //Обходим вторичный массив, получаем ключи полей.                                                              
                       if($v['id'] == ''){                                                                  //Условие создания и обновления.
                           if($vv != ''){                                                                   //Проверка наличия даннных.
                               if(isset($insert)){                                                          //Проверка наличия ранее сохраненных данных.
                                   $insert .= ',
                           ';
                               }
                               $insert .= '`'.$kk.'` = \''.$vv.'\'';                                        //Фиксируем данные.
                           }   
                       }
                       else{                                                                                //При обновлениии.
                           if($kk != 'id'){                                                                 //Если есть ИД
                               if(isset($update)){                                                          //Аналогично выше.
                                   $update .= ',
                           ';
                               }
                               $update .= '`'.$kk.'` = \''.$vv.'\'';                                        //Фиксируем данные. 
                           }    
                       }
                   }
                   unset($kk, $vv, $rv);                                                                    //Штатный сброс.
                   
                   if(isset($update)){                                                                      //При наличии данных, формируем окончательный запрос.       
                       $update = 'UPDATE
                       `'.$base.'`
                        SET
                       '.$update.' 
                        WHERE
                       `id` = \''.$v['id'].'\'';
                       SqlAdapter::select_sql($update); 
                   }
                   if(isset($insert)){                                                                      //Аналогично выше.
                       $insert = 'INSERT INTO
                       `'.$base.'`
                       SET
                       '.$insert;
                       SqlAdapter::select_sql($insert);
                   }
                   unset($update, $insert);
               }
               unset($k, $v);   
           }
      }
      
      //Метод получения структуры таблицы.
      public function getBaseStructure($base = null){
          if($base != null){
              $query = 'DESCRIBE
                        `'.$base.'`';
              return SqlAdapter::select_sql($query);
          }
      }
      
      
      //Метод получения общего масива данных.
      public function getArray($arr = null){
          if(count($arr) >= 1){                                                             //Проверка наличия элементов в массиве.
              foreach($arr as $key => $value){                                              //Обходим масив.
                  $a[$key] = $this->getArrayData($value['body']);                           //Получаем данные.
              }
              unset($key, $value, $arr);                                                    //Штатный сброс.
              return $a;                                                                    //Возврвт результата.
          }
      }
      
      
      //Метод получения массива данных для внесения в реестр.
      public function getArrayData($text = null){
          if($text != null){
              $arr = $this->getArrayTuning();                                               //Получаем массив с настройками.
              if(count($arr) >= 1){                                                         //Проверка наличия массива.
                  foreach($arr as $key => $value){                                          //Обходим массив с настройками.
                      $a = explode($arr[$key]['start'], $text);                             //Выделяем первый фрагмент.
                      if(count($a) >> 1){                                                   //Провека наличия данных.
                          $b = explode($arr[$key]['stop'], $a['1']);                        //Выделяем второй фрагиент.
                          $ar[$key] = $this->delSpace($b['0']);                             //Удаляем пробелы в начале и конце.. 
                          if($key == 'fio'){                                                //Находим ФИО и нормализируем его.
                              $ar[$key] = $this->normalizationFIO($ar[$key]);
                          }
                          if($key == 'e-mail'){                                             //Находим МЕЙЛ.
                              $ar[$key]  = $this->correctionMail($ar[$key]);                    //Коректируем мейл. 
                          }
                      }
                  }
                  unset($key, $arr, $b, $a, $value);                                        //Штанный сброс.
                  if(                                                                       //Валидация найденных данных.
                  count($ar) >= 3
                  && $ar['e-mail'] !== ''
                  && $ar['fio'] !== ''
                  ){
                      return $ar;                                                               //Возвращаем результат.
                  }
                  unset($ar);
              }
              unset($arr, $text);
          }
      }
      
      
      //Метод исправления имейла.
      private function correctionMail($mail = null){
          if (strpos($mail, '@') !== false){
              $mail = $this->delSpace($mail);                                                       //Удаляем все пробелы.
              return mb_strtolower($mail);                                                          //Переводим строку в нижний регистр и возвращаем. 
          }
      }
      
      
      //Метод удаления пробелов вначале строки и в конце.
      public function delSpace($text = null){
          if($text != null){
              $arr = explode('  ', $text);                                                           //Разделяем на 2 пробелы.
              if(count($arr) >= 1){
                  foreach($arr as $k => $v){
                      $text = str_replace('  ', ' ', $text);                                        //Заменям двойнык пробел на одинарный.
                  }
                  unset($k, $v, $arr);                                                              //Штатный сброс.
              }
              $text = str_replace('  ', ' ', $text);
              $text = trim($text);                                                                  //Удаляем пробелы в начале и в конце строки.
              return $text; 
          }
      }
      
      
      
      //Метод нормализации ФИО
      public function normalizationFIO($fio = null){
          if($fio != null){
              $ret = mb_convert_case($fio, MB_CASE_TITLE, "UTF-8"); 
              return $ret;
          }
      }
      
      
      //Метод получения массива с настройками для выделения данных из строки.
      private function getArrayTuning(){
          $arr['fio']['start'] = '[Прізвище ім\'я по батькові пацієнта:] ';
          $arr['fio']['stop'] = '[Ваш e-mail]';
          $arr['e-mail']['start'] = '[Ваш e-mail] ';
          $arr['e-mail']['stop'] = '[Місто:]';
          $arr['town']['start'] = '[Місто:] ';
          $arr['town']['stop'] = '[Адреса:]';
          $arr['address']['start'] = '[Адреса:] ';
          $arr['address']['stop'] = '[Дата народження пацієнта:]';
          $arr['birth']['start'] = '[Дата народження пацієнта:] ';
          $arr['birth']['stop'] = '[Номер телефону:]';
          $arr['phone']['start'] = '[Номер телефону:] ';
          $arr['phone']['stop'] = '[Номер електронного направлення]';
          $arr['electronic']['start'] = '[Номер електронного направлення] ';
          $arr['electronic']['stop'] = '[Як ви дізналися про нас?]';
          $arr['about']['start'] = '[Як ви дізналися про нас?] ';
          $arr['about']['stop'] = '[Консультант:]';
          $arr['consultant']['start'] = '[Консультант:] ';
          $arr['consultant']['stop'] = '[Тип прийому:]';
          $arr['reception']['start'] = '[Тип прийому:] ';
          $arr['reception']['stop'] = '[Короткий опис мети запису на прийом:]';
          $arr['description']['start'] = '[Короткий опис мети запису на прийом:] ';
          $arr['description']['stop'] = '[Я згоден з вашими правилами і умовами]';
          return $arr;
      }
  }
?>
