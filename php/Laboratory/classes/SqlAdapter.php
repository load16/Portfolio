<?php
  //Класс отетственный за организацию запросов в базу
  include_once 'classes/SqlSng.php';
  class SqlAdapter{
  	  
 	  var $obj;
  	  
  	  //Деструктор класса.
  	  function __destruct(){
  	  	  unset($this->obj);
  	  	  gc_collect_cycles();
  	  }
  	  
  	  //Конструктор класса.
	  function __construct(){
		  
	  }
      
      // Метод получения данных из базы.
      public function select_sql($query){
      	  $this->obj = sqlsng::getInstance();
      	  $ret = $this->obj->select($query);
          return $ret;
      }
      
  }
  
	  
?>
