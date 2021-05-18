<?php
  //Класс отвественный за настройку и управление .
  include_once 'classes/room.php';
  include_once 'library/select.php';
  //include_once 'library/s_adobe.php';
  include_once 'classes/log.php';
  //include_once 'classes/mail.php';
  include_once 'classes/files.php';
  class control extends room{
      
      
       var $heat;                                            //Заглавие задачи.
       var $footer;                                          //Фитер модуля.
       var $tools;                                           //Панель инструментов.
       var $content;                                         //Выводимый контент модуля.
       var $log;                                             //Объект логирования.
       //var $mail;                                            //Объект отправки писем.
       var $files;
      
      
      //Конструктор класса.
      function __construct($permission = false){
           parent::__construct();                                        //Выполнение конструктора предшественника.     
           $config = $this->config;
           //$this->mail = new mail($config->conf['imaphost'], $config->conf['smtpuser'], $config->conf['smtppass']);
           $this->log = new log('true');                                 //Создание объекта логирования.
           $this->files = new files();                                   //Создание объекта для работы с файлами.
           unset($config);
           control::action_control();                                    //Обрабатываем действие пользоватей.
           print room::getView();                                        //Отображаем кабинет.
      }
      
      
      
      //Деструктор класса.
      function __destruct(){
          
      }
      
      //Метод обработки действия пользователя.
      private function action_control(){
          
          
          $this->heat = 'Управление и настройка';
          $this->tools = $this->getTools();
          
          if(isset($_POST['cancel_rights']) || isset($_POST['cancel_file'])){                           //Обработка сброса.
              unset($_SESSION['control']);
          }
          
          if(isset($_POST['select_mode_rights'])){                                                      //Обработка переключения режимов.
               $_SESSION['control']['mode'] = 'rights';
          }
          if(isset($_POST['select_mode_settings'])){
               $_SESSION['control']['mode'] = 'settings';
          }
          if(isset($_POST['select_mode_catalog'])){
               $_SESSION['control']['mode'] = 'catalog';
          }
          
          
          
          if($_SESSION['control']['mode'] == 'rights'){                                                 //Работа в режиме управления правами.
              
              if(isset($_POST['login'])){                                                               //Обработка выбора пользователя.
                  $_SESSION['control']['id'] = $this->getIDUser($_POST['login']);                       //Определяем ИД пользователя.
                  $_SESSION['control']['user'] = $_POST['login'];
              }
              if($_SESSION['control']['id'] != ''){
                  unset($_SESSION['control']['wiev']);
                  if(isset($_POST['save_rights'])){                                                     //Обработка сохранения прав пользователя.
                      unset($_POST['save_rights']);
                      $_SESSION['control']['wiev'] = $_POST;
                      $this->saveRights($_SESSION['control']['id'], $_POST);                            //Сохраняем права пользователя.
                  }
                  $line[] = $this->getMenuUser();
                  $line[] = $this->getFormRightsUsers($_SESSION['control']['id'], $_SESSION['control']['user']);
                  $this->content = $this->getElementsLine($line);
                  unset($line);
              }
              else{
                  $this->content = $this->getMenuUser();
              }
          }
          if($_SESSION['control']['mode'] == 'settings'){                                               //Работа в режиме настроек.
              $this->content = $this->getFormEditFile('config.php'); 
          }
          if($_SESSION['control']['mode'] == 'catalog'){                                                //Работа в режиме справчников.
               if(isset($_POST['select_catalog'])){                                                     //Обработка выбора каталога.
                   $_SESSION['control']['select_catalog'] = $_POST['select_catalog'];
               }
                if(isset($_SESSION['control']['select_catalog'])){
                    if(isset($_POST['save_catalog'])){                                                  //Обработка сохранения справочника.
                        unset($_POST['save_catalog']);
                        if(!$this->saveCatalog($_SESSION['control']['select_catalog'], $_POST, '#')){
                            print '<script> alert(\'Валидация не пройдена! Справочник не сохранён!\') </script>';//Показываем сообщение о новом пациете.
                        }

                        
                    }
                    if(isset($_POST['cancel_catalog'])){                                                //Обработка сброса сохранения каталога.
                        unset($_SESSION['control']['select_catalog']);
                    }
                    $line[] = $this->getMenuCatalog($this->getArrCatalog()); 
                    $line[] = $this->getFormEditCatalog($_SESSION['control']['select_catalog'], '#');
                    $this->content = $this->getElementsLine($line);
                    unset($line);
                }
                else{
                    $this->content = $this->getMenuCatalog($this->getArrCatalog());
                }
               
          }
          
          
          room::outContent(control::getView());                            //Вывод всего модуля.
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
      
      
      //Метод получения ID пользователя по логину
      private function getIDUser($login = null){
          if($login != null){
              $arr = $this->register->searchDinData('login', 'login', $login);
              return $arr['0']['id'];
          }
      }
      
      
      
      //Метод получения меню выбора пальзователя.
      private function getMenuUser(){
          $arr = $this->register->getAllRecord('login');
          if(count($arr) >= 1){
              $select = new select();
              $ret = $select('login', 'login', $arr, 6)."\n";
              $ret = room::getForm($ret);
              return 'Выбор<br/>пользователя<br/>'.$ret;
          }
      }
      
      
      
      //Метод получения меню выбра справочника.
      private function getMenuCatalog($arr = null){
          if(count($arr) >= 1){
              $select = new select();
              $ret = $select('select_catalog', 'catalog', $arr, 6)."\n";
              $ret = room::getForm($ret);
              return 'Выбор<br/>справочника<br/>'.$ret;
          }
      }
      
      
      
      
      
      //Метод получения массива справчников.
      private function getArrCatalog(){
          $arr[]['catalog'] = 'analyzes';
          $arr[]['catalog'] = 'funds';
          $arr[]['catalog']= 'rights';
          //$arr[]['catalog']= 'login';
          return $arr;
      }
      
      
      
      //Метод получения формы для редактирования файла настроек.
      private function getFormEditFile($file = null){
          if($file != null){
              $pach = $this->files->getPatchFile();
              $fullfile = $pach.$file;
              $data = $this->files->getDataFile($fullfile);
              $ret = '<div class="module_heat">'."\n";
              $ret .= 'Редактируется файл - '.$file."\n";
              $ret .= '</div>'."\n";
              $ret .= '<textarea name="'.$file.'" rows="20" cols="100">'.$data.'</textarea><br/>'."\n";
              $button = '<div class="menu-start"><input type="submit" name="save_file" value="Сохранить"> <input type="submit" name="cancel_file" value="Отмена"></div>'."\n";
              $ret .= $button;
              return $this->getForm($ret);
          }
      }
      
      
      
      //Метод сохранения прав пользователя.
      private function saveRights($id = null, $arr = null){
          if($id != null){
              $query = 'DELETE FROM
                        `rights_user`
                        WHERE
                        `id_u` = \''.$id.'\'';
              SqlAdapter::select_sql($query);                                               //Удаляем все записи на ИД пользователя.
              if(count($arr) >= 1){
                  foreach($arr as $k => $v){                                                //Обходим массив с данными.
                      $query = 'INSERT INTO
                                `rights_user`
                                SET
                                `id_u` = \''.$id.'\',
                                `id_r` = \''.$k.'\'';
                      SqlAdapter::select_sql($query);                                       //Добавляем права.
                  }
              }    
              unset($k, $v, $arr, $id, $query);                                             //Штатный сброс.
          }
      }
      
      
      
      
      //Метод получения формы для редактирования прав пользователя.
      private function getFormRightsUsers($id = null, $user = null){
          if($id != null){                                                                  //Проверка небходимого.
              $arr_r = $this->register->getAllRecord('rights');                             //Получаем данные с прав.
              $arr_ru = $this->register->getAllRecord('rights_user');                       //Получаем данные про права пользователей.
              if(count($arr_r) >= 1){                                                       //Проверка наличия данных.
                  $ret = '<div class="module_content">';
                  if($user != null){
                      $ret .= '<div class="module_heat">';
                      $ret .= 'Права пользователя - '.$user.'<br/>'."\n";
                      $ret .= '</div>';
                  }
                  foreach($arr_r as $k => $v){                                              //Обходим массив справочника.
                      if(count($arr_ru) >= 1){                                              //Проверка наличия данных прав ползователя.
                          $f = false;                                                       //Начальная установка флага.
                          foreach($arr_ru as $kk => $vv){                                   //Обходим массив прав пользователя.
                              if($vv['id_r'] == $v['id'] && $vv['id_u'] == $id){            //Если находим права, то показываем их.
                                  $f = true;                                                //Устанавливаем флаг.  
                              }
                          }
                          unset($kk, $vv);
                          if($f){                                                            //Проверка флага наличия данных.
                              $ret .= '<input type="checkbox" name="'.$v['id'].'" checked>'.' '.$v['name'].'<br/>'."\n";  
                          }
                          else{
                              $ret .= '<input type="checkbox" name="'.$v['id'].'">'.' '.$v['name'].'<br/>'."\n"; 
                          }
                          unset($f);
                      }
                      else{
                          $ret .= '<input type="checkbox" name="'.$v['id'].$separator.'">'.' '.$v['name'].'<br/>'."\n"; 
                      }
                      
                  }
                  unset($k, $v);
                  $button = '<div class="menu-start"><input type="submit" name="save_rights" value="Сохранить"> <input type="submit" name="cancel_rights" value="Отмена"></div>';
                  $ret .= $button;
                  $ret .= '</div>';
                  return $this->getForm($ret);
              }
          }
      }
      
      
      
      //Метод сохранения изменений каталога.
      private function saveCatalog($nameCatalog = null, $arr = null, $separator = null){
          if($nameCatalog != null && $arr != null && $separator != null){                   //Проверка необходимого.
              foreach($arr as $k => $v){                                                    //Обходим массив.
                  $aa = explode($separator, $k);                                            //Разделяем данные.
                  $ret[$aa['1']][$aa['0']] = $v;                                            //Формируем новый массив для сохранения.
              }
              unset($k, $v, $aa, $arr);                                                     //Штатный сброс.
              foreach($ret as $k => $v){                                                    //Обходим созданный массив, проводим валидацию.
                  if($v['id'] != ''){                                                       //Находим где есть ИД.
                      $aa = $v;                                                             //Создаем масиив строки.
                      foreach($aa as $kk => $vv){                                           //Обходим его.
                          if($vv == ''){                                                    //Если находим пустое значение, то.
                              return false;                                                 //валидация не пройдяна
                          }
                      }
                      unset($kk, $vv);
                  }
              }
              unset($k, $v);
              $this->register->updateAllRecord($nameCatalog, $ret);
              return true;                                                                  //Возвращаем истину в случае прохождения валидации и сохранения результата.
          }
      }
      
      
      
      //Метод получения формы редактирования справочника.
      private function getFormEditCatalog($nameCatalog = null, $separator = null){
          if($nameCatalog != '' && $separator != null){                                     //Проверка наличия имени.             
              $str = $this->register->getBaseStructure($nameCatalog);                       //Получаем структуру базы. 
              if(count($str) >= 1){                                                         //Проверка наличия таблицы.
                  $ret .= '<div class="module_content">'."\n";
                  $ret .= '<div class="module_heat">';
                  $ret .= 'Редактируется справочник - '.$nameCatalog;
                  $ret .= '</div>'."\n";
                  $ret .= '<table class="menu-start">';                                                         //Рисуем таблицу.  
                  $arr = $this->register->getAllRecord($nameCatalog);                       //Получаем набор данных.
                  $ret .= '<tr>';                                                           //Начало строки 
                  foreach($str as $k => $v){
                      $ret .= '<td class="module_heat">'.$v['Field'].'</td>'."\n";                              //Шапка таблици. 
                  }
                  unset($k, $v);
                  $ret .= '</tr>';                                                          //Конец строки.
                  if(count($arr) >= 1){                                                     //Проверка наличия данных.
                      foreach($arr as $k => $v){                                            //Обходим данные.;
                          if(count($v) >= 1){                                               //Проверка наличия вложенного массива.
                              $ret .= '<tr>';                                               //Начало строки. 
                              $rv = $v;                                                     //Фиксируем данные.
                              foreach($rv as $kk => $vv){                                   //Обходим вложенные данные.
                                  if($kk == 'id'){
                                      $ret .= '<td>'.'<input type="text" name="'.$kk.$separator.$v['id'].'" value="'.$vv.'" readonly>'.'</td>'."\n";  
                                  }
                                  else{
                                      $ret .= '<td>'.'<input type="text" name="'.$kk.$separator.$v['id'].'" value="'.$vv.'">'.'</td>'."\n";  
                                  }
                              }
                              unset($kk, $vv, $rv);
                              $ret .= '</tr>';                                              //Конец строки.
                          }
                      }    
                  }
                  
                  $ret .= '<tr>';
                  foreach($str as $k => $v){                                                //Концовка таблыци с пустыми полями.
                          if($v['Field'] == 'id'){
                              $ret .= '<td>'.'<input type="text" name="'.$v['Field'].'" value="" readonly>'.'</td>'."\n";//Конец таблици.  
                          }
                          else{
                              $ret .= '<td>'.'<input type="text" name="'.$v['Field'].'" value="">'.'</td>'."\n";         //Конец таблици.  
                          }
                  }
                  unset($k, $v,$str);
                  $button = '<div class="menu-start"><input type="submit" name="save_catalog" value="Сохранить"> <input type="submit" name="cancel_catalog" value="Отмена"></div>'; 
                  $ret .= '</table><br/>'.$button;
                  $ret .= '</div>'."\n";
                  return $this->getForm($ret);
              }
          }           
      }
     
      
      //Метод получения панели инстркментов.
      private function getTools(){
          $ret = '<input type="submit" name="select_mode_rights" value="Права пользователей">';
          $ret .= '<input type="submit" name="select_mode_settings" value="Настройки">';
          $ret .= '<input type="submit" name="select_mode_catalog" value="Справочники">';  
          return $this->getForm($ret);
      }
      
      
  }
?>
