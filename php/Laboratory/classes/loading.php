<?php
  //Класс отвественный за настройку и управление .
  include_once 'classes/room.php';
  include_once 'library/select.php';
  include_once 'library/s_adobe.php';
  include_once 'classes/log.php';
  include_once 'classes/mail.php';
  class loading extends room{
      
      
       var $heat;                                            //Заглавие задачи.
       var $footer;                                          //Фитер модуля.
       var $tools;                                           //Панель инструментов.
       var $content;                                         //Выводимый контент модуля.
       var $log;                                             //Объект логирования.
       var $mail;                                            //Объект отправки писем.
      
      
      //Конструктор класса.
      function __construct($permission = false){
           parent::__construct();                                        //Выполнение конструктора предшественника.     
           $config = $this->config;
           $this->mail = new mail($config->conf['imaphost'], $config->conf['smtpuser'], $config->conf['smtppass']);
           $this->log = new log('true');                                 //Создание объекта логирования.
           unset($config);
           loading::action_loading();                                          //Обрабатываем действие пользоватей.
           print room::getView();                                        //Отображаем кабинет.
      }
      
      
      
      //Деструктор класса.
      function __destruct(){
          
      }
      
      //Метод обработки действия пользователя.
      private function action_loading(){
          
          
          $this->heat = 'Загрузка готовых анализов!';
          
          
          
          
          
          room::outContent(loading::getView());                            //Вывод всего модуля.
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
      
      
     
     
      
      //Метод получения панели инстркментов.
      private function getTools($create = false){
          if($create){
              $ret .= '<input type="submit" name="new_p" value="Создать пациента для отправки анализов">';
          }  
          return $ret;
      }
      
      
  }
?>
