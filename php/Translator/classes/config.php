<?php
//Класс отвественный за конфигурацию.
  class config
  {
      static protected $_instance;
      public $conf;											//Массив конфигурации.
      
      protected function __construct(){
          require_once 'config.php';
          $this->conf['patchdir'] = $patchdir;
      }
      
      protected function __clone(){}
      
      
      static public function getInstance(){
          if(!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
      }
      
      
  }

  
	  
?>
