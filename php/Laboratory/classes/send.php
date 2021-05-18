<?php
  //Класс отвественный за ручную отправку анализов пациентам.
  include_once 'classes/room.php';
  include_once 'library/select.php';
  include_once 'library/s_adobe.php';
  include_once 'classes/log.php';
  include_once 'classes/pat.php';
  include_once 'classes/mail.php';
  class send extends room{
      
      
       var $heat;                                            //Заглавие задачи.
       var $footer;                                          //Фитер модуля.
       var $tools;                                           //Панель инструментов.
       var $content;                                         //Выводимый контент модуля.
       var $view;                                            //Область просмотра модуля.
       var $log;                                             //Объект логирования.
       var $mail;                                            //Объект отправки писем.
       var $pat;
      
      
      //Конструктор класса.
      function __construct($permission = false){ 
          parent::__construct($permission);                  //Выполнение конструктора предшественника. 
           $config = $this->config;
           $this->mail = new mail($config->conf['imaphost'], $config->conf['smtpuser'], $config->conf['smtppass']);
           $this->log = new log('true');                                 //Создание объекта логирования.
           $this->pat = new pat($permission);
           unset($config);
           if($permission){
                
           }
           else{
               send::action_send();                                       //Обрабатываем действие пользоватей.
               print room::getView();                                     //Отображаем кабинет.
           }
      }
      
      
      
      //Деструктор класса.
      function __destruct(){
          
      }
      
      //Метод обработки действия пользователя.
      private function action_send(){                                            
          /*
          if(isset($_POST['select_change'])){                               //Обработка изменение данных пациента.
              $arr = $_POST;                                                //Фиксируем данные.
              unset($arr['select_change']);                                 //Убираем лишнее.
              $message = $this->validationPat($arr);                        //Получаем сообщение валидационное.
               if($message == ''){                                          //Проверяем валидацию
                   $this->register->updateDataId($arr, $_SESSION['global']['search']['id']);
                   $this->log->sendLog('Пациент! '.$arr['fio'].' - обновлен!', $arr);
                   $_SESSION['send']['search']['arr']['0'] = $arr;
                   print '<script> alert(\'Пациент! '.$arr['fio'].' - обновлен!\') </script>';//Показываем сообщение о новом пациете.
               }
               else{
                   print '<script> alert(\'Валидация не пройдена! '.$message.'\') </script>';//ВПОказываем сообщение.
               }
              unset($arr, $message);
              unset($_SESSION['send']['search']['mod_p']); 
          }
          
          
          if(isset($_POST['mod_p'])){                                       //Обработка модификации пациента.
              $_SESSION['send']['search']['mod_p'] = true;
          }
          
          
          if(isset($_POST['select_p_new'])){                                //ОБработка ввода нового пациента.            
              $arr = $_POST;                                                //Фиксируем данные.
              unset($arr['select_p_new']);                                  //Убираем лишнее.
              if($this->createNewPat($arr)){                                //Создаем нового пациента.
                  $this->log->sendLog('Пациент! '.$_POST['fio'].' - создан!', $arr); 
                  $_SESSION['send']['search']['arr'] = $this->register->searchDinData('patients', 'fio', $_POST['fio']);
                  if(count($_SESSION['send']['search']['arr']) == 1){       //Проверка количесва пациентов на введенное ФИО.
                      $_SESSION['global']['search']['id'] = $_SESSION['send']['search']['arr']['0']['id'];
                      $_SESSION['global']['search']['select_p'] = true;
                  }
                  
              }
              else{                                                         //При не удачном создании пациента.
                  
              }
              unset($arr);                                                  //Сброс отработанных данных.
              
          }
          
          
          if(isset($_POST['new_p'])){                                       //Обработка выбора нового пациента.
              unset($_SESSION['send']['search']['arr']);                           //Збрасываем все настройки.
              $_SESSION['send']['search']['new_p'] = true;                  //Устанавливаем флаг нового пациента.
          }
          */
          
          if(isset($_POST['send'])){                                        //Обработка отправки файлов.
              if(filter_var($_SESSION['send']['search']['arr']['0']['e-mail'], FILTER_VALIDATE_EMAIL) == false){//Валидация мейла перед отправкий.
                  print '<script> alert(\'Отправка не возможна! Мейл: '.$_SESSION['send']['search']['arr']['0']['e-mail'].' - не правильный!\') </script>';
                  //Фиксируем событие.
                  $this->log->sendLog('Письмо пациенту '.$_SESSION['send']['search']['fio_send'].' - не отправленно! Мейл: '.$_SESSION['send']['search']['arr']['0']['e-mail'].' - не правильный!', $arr_log);//Фиксацмя действия в логе.
              }
              else{                                                             //Валидация пройдена, продолжаем обработку.
                  $arr_send = $_POST;                                           //Снимаем данные.            
                  if(count($arr_send) >= 2){                                    //Проверяем наличие данных.
                      unset($arr_send['send']);                                 //Убираем лишнее.                 
                      foreach($arr_send as $k => $v){                                 //Обходим полученные данные и готовим к отправки.
                          $a = str_replace('&', '.', $k);
                          $a = str_replace('$', ' ', $a);
                          $b = $this->getShortName($k);
                          $b = str_replace('&', '.', $b);
                          $b = str_replace('$', ' ', $b);
                          $aa[] = $a;                                                   //Исправляем данные.
                          $bb[] = $b;                                                  //Получаем краткое имя файла.
                          $arr_log = $bb;                                               //Фиксирум данные для лога.
                      }
                      unset($k, $v, $a, $b);
                      $this->sendAnaliz($_SESSION['send']['search']['arr']['0']['e-mail'], $this->config->conf['subject'], $this->config->conf['message'], $aa, $bb);
                      $_SESSION['send']['arr_send'] = $arr_send;               //Фиксируем данные.
                      $this->log->sendLog('Письмо пациенту '.$_SESSION['send']['search']['fio_send'].' - отправленно!', $arr_log);//Фиксацмя действия в логе.
                      $this->FixSendFile($_SESSION['global']['search']['id'], $this->config->conf['message'], $aa);
                      unset($arr_log);
                      print '<script> alert(\'Письмо пациенту '.$_SESSION['send']['search']['fio_send'].' - отправленно!\') </script>';  
                  }
                  else{
                      unset($_SESSION['send']['arr_send']);
                      print '<script> alert(\'Нет выбранных файлов!\') </script>';
                  }
                  unset($aa, $bb);
              }
          }
          
          $this->pat->action_pat('send');                                   //Обрабатываем действия пользователя по выбору пациента. 
          
          
          //$a = $this->pat->getContent();       
          /*
          
          if(isset($_POST['fio_sel_double'])){                              //Обработка выбора пациента двойника по ФИО.
              $_SESSION['send']['search']['arr'] = $this->register->geteRecordId($_POST['fio_sel_double'], 'patients');
              $_SESSION['global']['search']['id'] = $_POST['fio_sel_double'];
              $_SESSION['global']['search']['select_p'] = true;
              unset($_SESSION['send']['search']['fio_sel_double']); 
              
          }
          if(isset($_POST['select_p'])){                                    //Обработка выбора пациента для просмотра файлов.
              $_SESSION['global']['search']['select_p'] = true;
          }
          if(isset($_POST['select_p_reset'])){                              //Обработка сброса поиска.
              unset($_SESSION['global']['search']['select_p'], $_SESSION['send']['arr_send'], $_SESSION['send']['search']['new_p']);
              unset($_SESSION['send']['search']['mod_p']);
          }
          if(isset($_POST['fio_send'])){                                    //Обработка выбора пациента в списке.
              $_SESSION['send']['search']['fio_send'] = $_POST['fio_send'];
              $arr_p = $this->register->searchDinData('patients', 'fio', $_SESSION['send']['search']['fio_send']);
              if(count($arr_p) >> 1){
                   $_SESSION['send']['search']['fio_many'] = $arr_p;
              }
              if(count($arr_p) == 1){                                       //Если нашли одного то фиксируем.
                  $_SESSION['global']['search']['id'] = $arr_p['0']['id'];
                  $_SESSION['send']['search']['arr'] =  $arr_p;
              }
              if(count($arr_p) >=2){                                        //Если нашли много, то фиксируем двойников.
                  $_SESSION['send']['search']['arr'] =  $arr_p;
                  $_SESSION['send']['search']['fio_sel_double'] = true;
              }
          }
          if(isset($_POST['search_fio'])){                                //Обработка поиска пациента по ФИО.
              $_SESSION['global']['search']['search'] = $_POST['search_fio'];
              $_SESSION['send']['search']['name'] = 'fio';
          }
          
          */
          
          $this->heat = 'Отправка анализов пациенту';
          $this->tools = room::getForm(send::getTools($_POST['search_fio']).$this->getButtonModPat());
          
          
           $a = $this->pat->getContent();
          /*
          if(isset($_SESSION['global']['search']['arr'])){                    //Проверка наличия данных выбранного пациента.
              if(isset($_SESSION['global']['search']['fio_sel_double'])){     //Проверка наличия двойников.
                  //$this->content = $this->getSelectDouble($_SESSION['global']['search']['arr'], $_SESSION['global']['search']['fio_send']);
                  $this->content = $this->pat->getSelectDouble($_SESSION['global']['search']['arr'], $_SESSION['global']['search']['fio_send']); 
              }
              else{
                  if(isset($_SESSION['global']['search']['mod_p'])){              //Условия для показа панели изменения данных пациента.
                      $this->content = $this->pat->getDataPat($_SESSION['global']['search']['id'], $_SESSION['global']['search']['arr'], false, false, true);
                  }
                  if(!isset($_SESSION['global']['search']['select_p'])){          //Условие для показа панели поиска.
                      $a[]  = $this->pat->getSearchPat($_SESSION['global']['search']['name'], $_SESSION['global']['search']['search']); 
                  }
                  if(isset($_SESSION['global']['search']['arr'])
                  && !isset($_SESSION['global']['search']['mod_p'])){                //Условие для показа панели тображение данных.
                      $a[] = $this->pat->getDataPat($_SESSION['global']['search']['id'], $_SESSION['global']['search']['arr'], $_SESSION['global']['search']['select_p']);

                  }
                  if($_SESSION['global']['search']['select_p']
                  && !isset($_SESSION['global']['search']['mod_p'])){                  //Условие для отображения файлов для отправки.
                      $fio = $this->register->prepareFIO($_SESSION['global']['search']['fio_send']);  //Адаптируем ФИО для поиска.
                      //$fio = 'Буряк';
                      //$arr = $this->register->getListFileFio($fio, 'files');                        //ПОлучаем массив данных
                      $arr = $this->register->getListFileFio($fio, 'full_files');                   //ПОлучаем массив данных 
                      if(count($arr) >> 0){                                                         //Проверка наличия данных
                           $s_adbobe = new s_adobe();                                               //Готовим интерфейс и показываем данные.
                           $a[] = $this->getForm( $s_adbobe($arr, 'name', 'fullname', $this->config->conf['patchseach'], 380).'<br/><input type="submit" name="send" value="Выбранное отправить">');
                      }
                      else{                                                                         //Если нет ,данных, то оповещаем.
                          $a[] = 'Нет данных!';
                      }
                  }
              }                  
          }
          else{                                                                 //Есди нет выбранных данных то показываем панель для поиска.
              $this->content = $this->pat->getSearchPat($_SESSION['global']['search']['name'], $_SESSION['global']['search']['search']);
              if(isset($_SESSION['global']['search']['new_p'])){                  //Условие показа формы создания нового пациента.
                  if(isset($_POST['select_p_new'])){
                      $arr[] = $_POST;
                      $this->content = $this->pat->getDataPat('', $arr, '', true);
                  }
                  else{
                      $this->content = $this->pat->getDataPat();
                  }                  
              }
          }
          
          */
          
          
          if($_SESSION['global']['search']['select_p']                                              //Условие для отображения файлов для отправки.
          && isset($_SESSION['global']['search']['arr'])
          && !isset($_SESSION['global']['search']['mod_p'])){
                      $fio = $this->register->prepareFIO($_SESSION['global']['search']['fio_send']); //Адаптируем ФИО для поиска.
                      //$fio = 'Буряк';
                      //$arr = $this->register->getListFileFio($fio, 'files');                        //ПОлучаем массив данных
                      $arr = $this->register->getListFileFio($fio, 'full_files');                   //ПОлучаем массив данных 
                      $arr = $this->processingArrayFiles($arr, $_SESSION['global']['search']['id']);
                      if(count($arr) >> 0){                                                         //Проверка наличия данных
                           $s_adbobe = new s_adobe();                                               //Готовим интерфейс и показываем данные.
                           $a[] = $this->getForm( $s_adbobe($arr, 'name', 'fullname', $this->config->conf['patchfullseach'], '220').'<br/><input type="submit" name="send" value="Выбранное отправить">');
                      }
                      else{                                                                         //Если нет ,данных, то оповещаем.
                          $a[] = 'Нет данных!';
                      }
          }
          
          $this->content = $this->pat->content_pat;
          
          if(count($a) >= 2){                                           //Проверка наличия разделенных данных.
              $this->content = $this->getElementsLine($a);              //Если есть, то показываем их.
          }
          
          
          unset($a, $arr, $fio, $arr_p);                                //Штатный сброс.
          room::outContent(send::getView());                            //Вывод всего модуля.
      }
      
      
      //Метод отображения модуля.
      public function getViewModule(){
          $ret = '
          <div class="module">
              <div class="module_heat">
                  '.$this->heat.'
              </div>
              <div class="module_tools">
                  '.$this->tools.'
              </div>
              <div class="module_content">
                  '.$this->content.
              '</div>
              <div class="module_footer">
                  '.$this->footer.'
              </div>
          </div>';
          return $ret;
      }
      
      
      //Метод подготовки контента
      private function prepContent(){
          if($this->view = ''){
              $a[] = $this->content;
              $a[] = $this->view;
              return $this->getElementsLine($a);
          }
          else{
              return $this->content;
          }
      }
      
      
      //Метод отображения модуля.
      public function getView(){
          return room::getViewModule();
      }
      
      /*
      //Метод создания нового пациента.
      private function createNewPat($arr = null){
          if(count($arr) >> 1){                                                             //Проверка наличия данных.
              $arr['phone'] = str_replace(' ', '', $arr['phone']);                          //Исправление данных.
              $arr['e-mail'] = str_replace(' ', '', $arr['e-mail']);                        //Удаляем пробел из строки.
              $message = $this->validationPat($arr);                                        //ПОлучаем сообщение валидатора.
              if($message == ''){                                                           //Проводим валидацию.
                  $aa[] = $arr;                                                             //Готовим массив. 
                   $this->register->writeDataBase($aa);                                         //Заносим данные в базу.
                   print '<script> alert(\'Пациент! '.$arr['fio'].' - создан!\') </script>';//Показываем сообщение о новом пациете.
                   unset($_POST['select_p_new'], $_SESSION['send']['search']['new_p']);
                   unset($aa, $arr);                                                      //Штатный сброс.
                   return true;                                                             //Возвращаем истину если создан пациент.
              }
              else{
                  print '<script> alert(\'Валидация не пройдена! '.$message.'\') </script>';//ВПОказываем сообщение. 
                  return false;                                                             //При не удачном создании, возвращаем ложь.
              }
          }    
      }
      */
      
      /*
      //Метод валидации вводимых данных пациента.
      private function validationPat($arr = null){
          if(count($arr) >> 1){                                                         //ПРоверка наличия данных.
              foreach($arr as $k => $v){                                                //Обходим массив данных.
                  if($this->register->validationSring($v) == false && $v != ''){        //Проводим валидацию на враждебный код.
                      $message = 'Не пройдена валидация на враждебный код!';            //Если валидация не прошла, то формируем сообщение.
                  }
              }
              unset($k, $v);                                                            //Штатный сброс переменных.
               if($arr['fio'] == ''){
                   $message = 'ФИО пустое!';
               }
               
               //if($arr['town'] == ''){
               //    $message = 'Нет города!';
               //}
               
               if($arr['phone'] == ''){
                   $message = 'Нет телефона!';
               }
               else{
                   if($this->validationPhone($arr['phone']) == false){
                       $message = 'Телефон не прошел валидацию!';
                   }
               }
               if($arr['e-mail'] == ''){
                   $message = 'Нет мейла!';
               }
               else{
                   if(filter_var($arr['e-mail'], FILTER_VALIDATE_EMAIL) == false){
                       $message = 'Мейл указан не верно!';
                   }
               }
          }
          else{
              $message = 'Нет данных!';
          }
          return $message;
      }
      
      
      */
      /*
      //Метод валидации тедефона.
      private function validationPhone($phone = null){
          if($phone != null){
              //Формипуе массив символов для валидации.
              $s[] = '0';
              $s[] = '1';
              $s[] = '2';
              $s[] = '3';
              $s[] = '4';
              $s[] = '5';
              $s[] = '6';
              $s[] = '7';
              $s[] = '8';
              $s[] = '9';
              $s[] = '-';
              $s[] = '(';
              $s[] = ')';
              $s[] = '+';
              $arr = str_split($phone);                                 //Превращаем строку в массив.
              foreach($arr as $k => $v){                                //Обходим массив строки.
                  $rr = false;                                          //Установка флага.
                  foreach($s as $kk => $vv){                            //Обходим массив валидационных символов.
                      if($vv == $v){                                    //Если символ соответствует валидационному,  то меняем флаг.
                          $rr = true;
                      }
                  }
                  unset($kk, $vv);
                  if(!$rr){
                      return false;                                     //При не верной валидации возвращаем ложь.
                  }
              }
              unset($k, $v);
              return true;                                              //При успешной валидации, возвращаем истину.
          }
          return false;
      }
      */
      /*
      //Метод получения панели поиска пациента.
      private function getSearchPat($name = null, $value = null){
          if($name != null && $value != null){
              $arr = $this->register->searchDinData('patients', $name, $value);         //Получаем данные с базы
              $ret .= '<div class="module_heat" style="font-size: 85%;">'."\n";
              if(count($arr) >> 0){                                                     //Проверка наличия данных.
                  $select = new select();                                               //Если есть данные, то показываем список.
                  $ret .= 'Выбор пациента:</br>'."\n";
                  $ret .= $select('fio_send', 'fio', $arr, 15)."\n";
                  $ret = $this->getForm($ret);
                  unset($select);
              }
          }
          //Готовим кнопку поиска.
          if($name == 'fio'){
              $b_search = '<div>ФИО <input type="text" name="search_fio" value="'.$value.'"" size="15"> <input type="submit" name="newuser" value="Поиск"></div>';
          }
          else{
              $b_search = '<div><input type="text" name="search_fio" size="15"> <input type="submit" name="newuser" value="Поиск"></div>';
          }
          
          $b_form = $this->getForm($b_search);
          
          $ret .= $b_form;
          $ret .= '</div>'."\n";
          return $ret;
      }
      
      
      //Метод получения данных отобранного пацыента.
      private function getDataPat($id = null, $arr = null, $selected = false, $new = false, $update = false){
          
          $read = '<div style="font-size: 80%;">'."\n"; 
          if($id != null && $arr == null){                                                  //Проверка наличия ИД пациента.
              $arr = $this->register->geteRecordId($id, 'patients');                        //Получаем данные с базы.
          }
          if($id == null && $arr == null){
              $ret .= '<div class="module_heat">Создание нового пациента!</div></br>'."\n";
          }
          if($update){
              $ret .= '<div class="module_heat">Изменение пациента!</div></br>'."\n";
          }
          
          if($new || $update || $id == null && $arr == null){
              $ret .= '<div style="font-size: 80%;">'."\n";  
              $ret .= 'ФИО <input type="text" name="fio" value="'.$arr['0']['fio'].'" size="37"></br>'."\n";
              $ret .= 'e-mail <input type="text" name="e-mail" value="'.$arr['0']['e-mail'].'" size="36"></br>'."\n"; 
              $ret .= 'Город <input type="text" name="town" value="'.$arr['0']['town'].'" size="36"></br>'."\n"; 
              $ret .= 'Адрес <textarea name="address"  rows="1" cols="28">'.$arr['0']['address'].'</textarea></br>'."\n";
              $ret .= 'Дата рождения <input type="text" name="birth" value="'.$arr['0']['birth'].'" size="10"></br>'."\n"; 
              $ret .= 'Телефон <input type="text" name="phone" value="'.$arr['0']['phone'].'" size="20"></br>'."\n"; 
              $ret .= 'Эл. направление <input type="text" name="electronic" value="'.$arr['0']['electronic'].'" size="25"></br>'."\n"; 
              $ret .= 'Как узнали про нас <input type="text" name="about" value="'.$arr['0']['about'].'" size="25"></br>'."\n";
              $ret .= 'Консультант <input type="text" name="consultant" value="'.$arr['0']['consultant'].'" size="30"></br>'."\n"; 
              $ret .= 'Тип приема <input type="text" name="reception" value="'.$arr['0']['reception'].'" size="25"></br>'."\n"; 
              $ret .= 'Описание проблемы <textarea name="description"  rows="1" cols="16">'.$arr['0']['description'].'</textarea></br>'."\n"; 
              $ret .= 'Дата создания <input type="text" name="data_create" value="'.$arr['0']['data_create'].'" size="20"></br>'."\n";
          }
          else{
              $ret .= '<div class="module" style="font-size: 100%;">'."\n"; 
              $ret .= 'ФИО - '.$arr['0']['fio'].'</br>'."\n";
              $ret .= 'e-mail - '.$arr['0']['e-mail'].'</br>'."\n"; 
              $ret .= 'Город - '.$arr['0']['town'].'</br>'."\n"; 
              $ret .= 'Адрес - <textarea name="address" disabled readonly  rows="1" cols="28">'.$arr['0']['address'].'</textarea></br>'."\n";
              $ret .= 'Дата рождения - '.$arr['0']['birth'].'</br>'."\n"; 
              $ret .= 'Телефон - '.$arr['0']['phone'].'</br>'."\n"; 
              $ret .= 'Эл. направление - '.$arr['0']['electronic'].'</br>'."\n"; 
              $ret .= 'Как узнали про нас - '.$arr['0']['about'].'</br>'."\n";
              $ret .= 'Консультант - '.$arr['0']['consultant'].'</br>'."\n"; 
              $ret .= 'Тип приема - '.$arr['0']['reception'].'</br>'."\n"; 
              $ret .= 'Описание проблемы <textarea name="description" disabled readonly  rows="1" cols="16">'.$arr['0']['description'].'</textarea></br>'."\n"; 
              $ret .= 'Дата создания -'.$arr['0']['data_create'].'</br>'."\n";
          }
          
              
          
          
          if($selected){
              if($arr['0']['fio'] != null){
                  $ret .= '<input type="submit" name="select_p_reset" value="Сброс">'; 
              }
          }
          else{
              
              if($id == null && $arr == null || $new){
                  $button = '<input type="submit" name="select_p_new" value="Создать">';
                  $button .= '<input type="submit" name="select_p_reset" value="Отмена">';  
              }
              else{
                  $button = '<input type="submit" name="select_p" value="Выбрать">'; 
              }
              if($update){
                  $button = '<input type="submit" name="select_change" value="Изменить">';
                  $button .= '<input type="submit" name="select_p_reset" value="Отмена">';
              }
              
          }
          $ret .= $button."\n"; 
          $ret .= '</div>'."\n";  
          return $this->getForm($ret);
      }
      */
      
      
      /*
      //Метод получения панели выбора пациента в случае наличия в базе двойника.
      private function getSelectDouble($arr = null, $fio = null){
          if(count($arr) >= 2 && $fio != null){
               $select = new select();                                               //Если есть данные, то показываем список.
               $ret = '<div class="module_heat">';
               $ret .= 'Выбор пациента</br>двойника</br>"'.$fio.'"</br>по ID:</br>'."\n";
               $ret .= $select('fio_sel_double', 'id', $arr, 5)."\n";
               $ret = $this->getForm($ret);
               $ret .= '</div>';
               unset($select);
          }
          return $ret;          
      }
      
      */
      
      
      
      
      //Метод обработки массива файлов для отправки с целью нахождения отправленных файлов с последующим выделением их.
      private function processingArrayFiles($array = null, $id_p = null){
          if(count($array) >= 1 && $id_p != null){                                              //Проверка необходимого.
              $arrPach[] = $this->config->conf['patchfullseach'];                               //Пролучаем массивы для адаптации имени файла.
              $arrPach[] = $this->config->conf['patchseach'];
              foreach($array as $k => $v){
                  $title = $this->register->getDataFileSend($v['fullname'], $id_p, $arrPach);   //Получаем данные отправленного файла.
                  if($title != ''){                                                             //Если есть данные, то формируем сотв. массив.
                      $arr = $v;                                                                //Формируем первичние данные.
                      $arr['title'] = $title;                                                   //К ним добавляем тайтл и стиль.
                      unset($title);
                      $arr['style'] = 'background: #FF0000;';
                      $ret[$k] = $arr;
                      unset($arr);
                  }
                  else{                                                                         //Если нет данных, то осталяем без изменения.
                      $arr = $v;
                      $ret[$k] = $arr;
                      unset($arr);
                  }
              }    
              unset($k, $v);
              return $ret;
          }
      }
      
      
      //Получение кнопки модификации пациента.
      private function getButtonModPat(){
          if(isset($_SESSION['global']['search']['id']) && isset($_SESSION['global']['search']['select_p'])){
              return '<input type="submit" name="mod_p" value="Изменть пациента">';
          }
      }
      
      
      
      //Метод получения панели инстркментов.
      private function getTools($create = false){
          if($create){
              $ret .= '<input type="submit" name="new_p" value="Создать пациента для отправки анализов">';
          }  
          return $ret;
      }
      
      
      //Метод получения краткого имени файла с полного.
      public function getShortName($name = null){
          if($name != null){
              $os = PHP_OS;                                          //Определяем ОС.
              $os = strtolower($os);
              if (strpos($os, 'win') !== false){                     //Проверка наличичя ОС Windows.
                              $arr = explode('\\', $name);           //Разделяем строку.
              }
              else{                                                  //Иначе оставляем как есть.
                              $arr = explode('/', $name);
              }
              if(count($arr) >= 2){                                  //Проверка наличия данных
                  foreach($arr as $k => $v){                         //Обхлдим данные.
                      $ret = $v;                                     //Фиксируем последний элемент.
                  }
                  unset($k, $v);                                     //Фтатный сброс.
                  return $ret;                                       //Возвращаем данные.
              }
          }
      }
      
      
      
      
      //Метод отправки анализов пациенту.
      public function sendAnaliz($to = null, $subject = null, $text = null, $filefullname = null, $filename = null){
          if($to != null && $subject != null && $text != null && $filefullname != null && $filename != null){       //Проверка необходимого.
              if(count($filefullname) == 1){                                                                        //Проверка наличия массива.
                  $filefullname = $filefullname['0'];
                  $filename = $filename['0'];
              }              
              $this->mail->sendMailAttachment($to, $subject, $text, $filefullname, $filename);
              unset($this->mail);                                                                                      //Закритие подключений после отправки письма.
              $this->mail = new mail($config->conf['imaphost'], $config->conf['smtpuser'], $config->conf['smtppass']); //СОздание нового подключения.
          }
      }
      
      
      //Метод фиксации в базе отправленных анализов.
      public function FixSendFile($id = null, $text = null, $arr = null){
          if($id != null && $text != null && $arr != null){                         //Проверка необходимого.
              $arr_p = $this->register->geteRecordId($id, 'patients');              //Получаем данные пациента по ИД.          
              if(count($arr) >= 1){
                  foreach($arr as $k => $v){                                        //Обходим массив файлов.
                      $shortName = $this->getShortName($v);                         //Получаем краткое имя файла.
                      $dir = str_replace($shortName, '', $v);                       //Получаем директорию.
                      $this->register->putSendFile($id, $dir, $shortName);          //Фикируем в базе отправленных файлов.
                  }
                  unset($k, $v, $dir, $shortNam);                                   //Штатный сброс.
              }
              $this->register->putSendMail($id, $text);                             //Фиксируем отправленное письмо.
          }
      }
  }
?>
