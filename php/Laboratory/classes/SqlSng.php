<?php
//Класс отвественный за взаимодействие с базой.

include_once 'classes/config.php';
  class sqlsng
  {
      static protected $_instance;
      protected $connection;
      protected $mysqli;
      
      protected function __construct(){
          //require_once 'config.php';
          $config  = config::getInstance();
          $this->connection = mysql_connect($config->conf['dbhost'], $config->conf['dbuser'], $config->conf['dbpasswd']) or die('Не удалось соединиться: ' . mysql_error());
          mysql_select_db($config->conf['dbname']) or die('Не удалось выбрать базу данных');
          
          /* 
          $this->connection = mysql_connect($dbhost, $dbuser, $dbpasswd) or die('Не удалось соединиться: ' . mysql_error());
          mysql_select_db($dbname) or die('Не удалось выбрать базу данных');
          */
      }
      
      protected function __clone(){}
      
      
      static public function getInstance(){
          if(!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
      }
      
      
      public function select($query){
          if(isset($_SESSION['view']['SqlAdapter']['query'])){
              $_SESSION['view']['SqlAdapter']['query']++;
          }
          else{
              $_SESSION['view']['SqlAdapter']['query'] = 1;
          }
          $arr = explode(' ', $query);
          
          if($arr[0] == 'PREPARE' || $arr[0] == 'prepare' || $arr[0] == 'SET' || $arr[0] == 'set' ){
              mysql_unbuffered_query($query) or die('Запрос не удался: ' . mysql_error());
              //mysql_query($query) or die('Запрос не удался: ' . mysql_error());                                                // Освобождаем память от результата  
              return;
          }
          
          if($arr[0] == 'INSERT' || $arr[0] == 'insert'){                               //Если insert, то возвращаем ID.
              $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
              $id = mysql_insert_id();                                               // Освобождаем память от результата  
              return $id;
          }
          else{
              $result = mysql_unbuffered_query($query) or die('Запрос не удался: ' . mysql_error());
              
              if($arr[0] == 'UPDATE' || $arr[0] == 'update' || $arr[0] == 'DELETE' || $arr[0] == 'delete'){
                  
              }
              else{
                  if($result != ''){
                      while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {                 //Формируем массив областей.
                           $array[] = $line;
                      }
                  }
                  
                  if($array != ''){
                      mysql_free_result($result);                                               // Освобождаем память от результата  
                  }
                  return $array;
              }
                  
                          
          }
      }
      
  }

  
	  
?>
