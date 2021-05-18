<?php
  //Класс отвественный за поиск и полечение файлов на гуглдиске.
  require_once '/library/vendor/autoload.php';
  
  class google{
      
      
      var $client;
      
      //Конструктор класса.
      function __construct(){
          $this->client = new Google_Client();
      }
      
      //Деструктор класса.
      function __destruct(){
          unset($this->client);
      }
      
  }
?>
