<?php
  //Класс отвественный за ведение журнала посещений лаборатории.
  include_once 'classes/room.php';
  include_once 'library/select.php';
  include_once 'library/s_adobe.php';
  include_once 'classes/log.php';
  include_once 'classes/mail.php';
  include_once 'classes/pat.php';
  class loglab extends room{
      
      
       var $heat;                                            //Заглавие задачи.
       var $footer;                                          //Фитер модуля.
       var $tools;                                           //Панель инструментов.
       var $content;                                         //Выводимый контент модуля.
       var $log;                                             //Объект логирования.
       var $mail;                                            //Объект отправки писем.
       var $pat;                                             //Объект для формирования элементов интерфейса пациента.
      
      
      //Конструктор класса.
      function __construct($permission = false){
           parent::__construct();                                        //Выполнение конструктора предшественника.     
           $config = $this->config;
           $this->mail = new mail($config->conf['imaphost'], $config->conf['smtpuser'], $config->conf['smtppass']);
           $this->log = new log('true');                                 //Создание объекта логирования.
           $this->pat = new pat(); 
           unset($config);
           loglab::action_loglab();                                          //Обрабатываем действие пользоватей.
           print room::getView();                                        //Отображаем кабинет.
      }
      
      
      
      //Деструктор класса.
      function __destruct(){
          
      }
      
      //Метод обработки действия пользователя.
      private function action_loglab(){
          $this->tools = '';
          if(isset($_POST['reset_lab'])){                               //Обработка сброса ввода.
              unset($_POST);
          }
          if(isset($_POST['select_an'])){                               //Обработка выбора режима.
              $_SESSION['loglab']['mode'] = 'analiz';
          }
          if(isset($_POST['select_mat'])){
              $_SESSION['loglab']['mode'] = 'material';
          }
          $this->pat->action_pat('loglab');                             //Обработка действий пользователя по выбору ациента.
          $this->heat = 'Журнал посещений лаборатории пациентами';
          
          unset($_SESSION['loglab']['view']); 
          
          $a = $this->pat->getContent();                                //Получение контента для отображения интерфейса.  
          $this->content = $this->pat->content_pat;                     //Получение готового контента.
          if(isset($_POST['search_fio'])){                              //Условие показа кнопки создания пациента.
              $this->tools .= $this->getButtonCreatePatient();
          }
          if($_SESSION['global']['search']['select_p'] 
          && !isset($_POST['new_p'])
          && !isset($_POST['mod_p'])
          ){                                                             //Условие ввода данных пациента.
              $this->tools .= $this->getButtonModPatient();
              $this->tools .= $this->getButtonAddPatient();              //Формируем меню режимов ввода.
              if($_SESSION['loglab']['mode'] == 'analiz'){               //Условие работы в режиме ввода анализов.
                  
                  if(isset($_POST['save_analyzes'])){                   //Условие сохранения анализов.
                      if($this->validation($_SESSION['global']['search']['id'], 'analyzes_patients', 'id_a', $this->defIdCataloge('analyzes', 'name_a', $_POST['save_analyzes']), $this->date->data_i)){
                          //print '<script> alert(\'Валидация пройдена!\') </script>';//Показываем сообщение о новом пациете.
                          $this->writeBase($_SESSION['global']['search']['id'], 'analyzes_patients', 'id_a', $this->defIdCataloge('analyzes', 'name_a', $_POST['save_analyzes']));  
                      }
                      else{
                          print '<script> alert(\'Валидация не пройдена! Введен анализ повторно!\') </script>';//Показываем сообщение о новом пациете.
                      }
                  }
                  if(isset($_POST['selected_analyzes'])){               //Обработка выбора имени анализа.
                      $a[] = $this->getInput('Ввод нанализа!', 'save_analyzes', $_POST['selected_analyzes']);
                  }
                  else{
                      $a[] = $this->getSelect('Выбор<br/>анализа:', 'analyzes', 'selected_analyzes', 'name_a');
                  }
              }
              if($_SESSION['loglab']['mode'] == 'material'){              //Условие работы в режиме ввода материалов.
                  if(isset($_POST['save_funds'])){                   //Условие сохранения анализов.
                      if($this->validation($_SESSION['global']['search']['id'], 'issued', 'id_f', $this->defIdCataloge('funds', 'name_f', $_POST['save_funds']), $this->date->data_i)){
                          //print '<script> alert(\'Валидация пройдена!\') </script>';//Показываем сообщение о новом пациете.
                          //$_SESSION['loglab']['view'] = $_POST;
                          if(is_numeric($_POST['measure'])){                    //Валидация количества.
                               unset($arr);
                              $arr['amount'] = $_POST['measure']; 
                              $this->writeBase($_SESSION['global']['search']['id'], 'issued', 'id_f', $this->defIdCataloge('funds', 'name_f', $_POST['save_funds']), $arr);
                              unset($arr);
                          }
                          else{
                              print '<script> alert(\'Валидация не пройдена! Количество должно быть числом!\') </script>';//Показываем сообщение о новом пациете.
                          }
                      }
                      else{
                          print '<script> alert(\'Валидация не пройдена! Введен анализ повторно!\') </script>';//Показываем сообщение о новом пациете.
                      }
                  }
                  if(isset($_POST['selected_funds'])){                  //Обработка выбора имени мат. средств.
                      $arr = $this->register->searchData('funds', 'name_f', $_POST['selected_funds']);
                      //unset($_SESSION['loglab']['view']);
                      
                      $a[] = $this->getInput('Ввод мат. ср.:', 'save_funds', $_POST['selected_funds'], 'measure', $arr);
                      //$_SESSION['loglab']['view'] = $arr;
                      unset($arr);
                  }
                  else{
                      $a[] = $this->getSelect('Выбор<br/>мат. средст.:', 'funds', 'selected_funds', 'name_f'); 
                  }
                  
              }
          }
          
          if(isset($_POST['view_data'])){
              //$a[] = $this->pat->getDataTable('Буряк', $_SESSION['global']['search']['id'], false, false, false, $this->date->data_i);
              $a[] = $this->pat->getDataTable('Буряк', $_SESSION['global']['search']['id'], false, false, false, $this->date->data_i);
          }
          
          if(count($a) >= 1){                                           //Проверка наличия разделенных данных.
              $this->content = $this->getElementsLine($a);              //Если есть, то показываем их.
          }
          
          unset($a);                                                    //Штатный сброс.
          if($this->tools != ''){                                       //Условие подготовки зони инструментов.
              $this->tools = $this->getForm($this->tools); 
          }
          $this->footer = $this->pat->getDataTable('Буряк', $_SESSION['global']['search']['id'], false, false, false);
          room::outContent(loglab::getView());                          //Вывод всего модуля.
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
      
      //Метод отображения модуля.
      public function getView(){
          return room::getViewModule();
      }
      
      
      //Метод валидации ввода.
      private function validation($idPat = null, $nameBase = null, $namePole = null, $value = null, $date = null){
          $f = true;                                                                           //Начальная установка флага.
          if($idPat != null && $nameBase != null && $namePole != null && $value != null && $date != null){
              $arr = $this->register->searchData($nameBase, $namePole, $value);                 //Получаем данные с базы.
              if(count($arr) >= 1){                                                             //Проверка данных.
                  foreach($arr as $k => $v){                                                    //Обходим данные.
                      if(
                      $v['data'] == $date 
                      && $v['id_p'] == $idPat
                      && $v[$namePole] == $value
                      ){                                                                        //Если находим данные, то
                          $f = false;                                                           //возвращаем ложь
                      }
                  }
                  unset($k, $v);
                  return $f;                                                                    //Если нет данных.
              }                                                                                 //возвращаем истину.
              else{
                  return $f;
              }
          }
          return false;
      }
      
      //Метод ввода данных в базу.
      private function writeBase($idPat = null, $nameBase = null, $namePole = null, $value = null, $arr = null){
          if($idPat != null && $nameBase != null && $namePole != null && $value != null){
              if(count($arr) >= 1){
                  foreach($arr as $k => $v){
                      $arr[$k] = $v;
                  }
                  unset($k, $v);
              }
              $arr[$namePole] = $value;
              $arr['id_p'] = $idPat;
              $arr['data'] = $this->date->data_i;
              $arr['datatime'] = $this->date->data_i.' '.$this->date->time_i;
              $this->register->insertRecord($nameBase, $arr);
          }
      }
      
      
      //Метод получения элемента выбора.
      private function getSelect($name = null, $nameBase = null, $nameSelect = null, $namePost = null){
          if($name != null && $nameBase != null && $nameSelect != null && $namePost != null){
               $arr = $this->register->getAllRecord($nameBase);
               if(count($arr) >= 1){                                                           //Если есть данные, то показываем список.  
                   $select = new select();
                   $ret = '<div class="module_content" style="font-size:90%">'; 
                   $ret .= '<div class="module_heat">';
                   $ret .= '<b>'.$name.'</b></br>'."\n";
                   $ret .= '</div>';
                   $s = $select($nameSelect, $namePost, $arr, 8)."\n";
                   $ret .= $this->getForm($s);
                   $ret .= '</div>';
                   unset($select);
                   return $ret;
               } 
          }
      }
      
      
      //Метод получения элемента ввода
      function getInput($name = null, $namePost = null, $value = null, $nameMeasure = null, $arr = null){
          if($name != null && $value != null && $namePost != null){
              $ret = '<div class="module_content" style="font-size:80%">';  
              $ret .= '<div class="module_heat">';
              $ret .= '<b>'.$name.'</b></br>'."\n";
              $ret .= '</div>';
              if($nameMeasure != null && count($arr) == 1){
                   $r = '<input type="text" readonly name="'.$namePost.'" value="'.$value.'"><br/>'.' кол. <input size="4 type="text" name="'.$nameMeasure.'">'.$arr['0'][$nameMeasure].'<br/>'."\n"; 
              }
              else{
                  $r = '<input type="text" readonly name="'.$namePost.'" value="'.$value.'"><br/>'."\n"; 
              }
              $r .= '<div class="menu-start"><input type="submit" name="save_lab" value="Ввод"><input type="submit" name="reset_lab" value="Отмена"></div>'.'</br>'."\n";
              $ret .= $this->getForm($r);
              $ret .= '</div>';
              return $ret;
          }
      }
      
      
      //Метод определения ИД выбранного элемента справочника.
      private function defIdCataloge($nameBase = null, $namePole = null, $value = null){
          if($nameBase != null && $namePole != null && $value != null){
              $arr = $this->register->searchData($nameBase, $namePole, $value);
              if(count($arr) == 1){
                  return $arr['0']['id'];
              }
          }
      }
      
      
      
      //Метод сохранения данных в базу.
      private function saveData($nameBase = null, $nameValue = null, $namePost = null){
          if($nameBase != null && $nameValue != null && $namePost != null){
              
          }
      }
      
      
     
     
      
      //Метод получения кнопки создать пациента.
      private function getButtonCreatePatient(){
          $ret .= '<input type="submit" name="new_p" value="Создать пациента">';  
          return $ret;
      }
      
      
      //Метод получения кнопки модефикайции пациента.
      private function getButtonModPatient(){
          if(isset($_SESSION['global']['search']['id']) && isset($_SESSION['global']['search']['select_p'])){
              return '<input type="submit" name="mod_p" value="Изменть пациента">';
          }
      }
      
      //Метод получения кнопки добавления данных к пациенту.
      private function getButtonAddPatient(){
          $ret .= '<input type="submit" name="select_an" value="Анализы">'; 
          $ret .= '<input type="submit" name="select_mat" value="Метериальные средства">';
          $ret .= '<input type="submit" name="view_data" value="Введено сегодня">'; 
          return $ret;
      }
      
      
  }
?>
