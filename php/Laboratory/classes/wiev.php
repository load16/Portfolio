<?php
  //Класс отвественный за ведение журнала посещений лаборатории.
  include_once 'classes/room.php';
  include_once 'library/select.php';
  include_once 'library/s_adobe.php';
  include_once 'classes/log.php';
  include_once 'classes/mail.php';
  include_once 'classes/pat.php';
  class wiev extends room{
      
      
       var $heat;                                            //Заглавие задачи.
       var $footer;                                          //Фитер модуля.
       var $tools;                                           //Панель инструментов.
       var $content;                                         //Выводимый контент модуля.
       var $log;                                             //Объект логирования.
       var $mail;                                            //Объект отправки писем.
       var $pat;                                             //Объект для огганизации интерфейса отображения данных пациента
      
      
      //Конструктор класса.
      function __construct($permission = false){
           parent::__construct();                                        //Выполнение конструктора предшественника.     
           $config = $this->config;
           $this->mail = new mail($config->conf['imaphost'], $config->conf['smtpuser'], $config->conf['smtppass']);
           $this->log = new log('true');                                 //Создание объекта логирования.
           $this->pat = new pat(); 
           unset($config);
           wiev::action_wiev();                                          //Обрабатываем действие пользоватей.
           print room::getView();                                        //Отображаем кабинет.
      }
      
      
      
      //Деструктор класса.
      function __destruct(){
          
      }
      
      //Метод обработки действия пользователя.
      private function action_wiev(){
          $this->pat->action_pat('wiev');                               //Обработка действий по выбору пациета.
          $this->heat = 'Просмотр готовых анализов!';
          
          
          $a = $this->pat->getContent();                                //Получение контента для отображения интерфейса.  
          $this->content = $this->pat->content_pat;
          
          if(isset($_SESSION['global']['search']['select_p'])){         //Условие показа панелив в контенте с данными на пациента.
              $fio = $this->register->prepareFIO($_SESSION['global']['search']['fio_send']); //Адаптируем ФИО для поиска. 
              $a[] = $this->pat->getDataTable($fio, '14', true, true, false);
              unset($fio);
          }
          else{
              if(isset($_SESSION['global']['search']['search'])             //Условие показа фоновых данных в футере. 
              || isset($_SESSION['global']['search']['fio_send'])
              || isset($_SESSION['global']['search']['id'])){
                  
                  $fio = $this->register->prepareFIO($_SESSION['global']['search']['fio_send']); //Адаптируем ФИО для поиска.  
                  if(isset($_SESSION['global']['search']['fio_send'])){
                      //$this->footer = $this->pat->getDataTable($_SESSION['global']['search']['fio_send'], $_SESSION['global']['search']['id'], false, true, false);
                      $this->footer = $this->pat->getDataTable($fio, '14', false, true, false); 
                  }
                  else{
                      $this->footer = $this->pat->getDataTable($fio, '14', false, false, false);  
                      //$this->footer = $this->pat->getDataTable($_SESSION['global']['search']['search'], $_SESSION['global']['search']['id'], false, true, false);
                  }
                  unset($fio);
              }
          }
          
          if(count($a) >= 1){                                           //Проверка наличия разделенных данных.
              $this->content = $this->getElementsLine($a);              //Если есть, то показываем их.
          }
          
              
          
          unset($a);                                                    //Штатный сброс.
          room::outContent(wiev::getView());                            //Вывод всего модуля.
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
      
      
      
     
     
      
      //Метод получения кнопки создать пациента.
      private function getButtonCreatePatient(){
          $ret .= '<input type="submit" name="new_p" value="Создать пациента">';  
          return $ret;
      }
      
      
      //Метод получения кнопки модефикайции пациента.
      private function getButtonModPatient(){
          $ret .= '<input type="submit" name="mod_p" value="Изменить пациента">';  
          return $ret;
      }
      
      //Метод получения кнопки добавления данных к пациенту.
      private function getButtonAddPatient(){
          $ret .= '<input type="submit" name="new_data_p" value="Новые данные">';  
          return $ret;
      }
      
      
  }
?>
