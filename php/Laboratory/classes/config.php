<?php
//Класс отвественный за конфигурацию.
  class config
  {
      static protected $_instance;
      public $conf;											//Массив конфигурации.
      
      protected function __construct(){
          require_once 'config.php';
          $this->conf['dbhost'] = $dbhost;
		  $this->conf['dbport'] = $dbport;
		  $this->conf['dbname'] = $dbname;
		  $this->conf['dbuser'] = $dbuser;
		  $this->conf['dbpasswd'] = $dbpasswd;
		  
		  $this->conf['mailmessage'] = $mailmessage;
		  $this->conf['mailer'] = $mailer;
		  $this->conf['mailfrom'] = $mailfrom;
		  $this->conf['fromname'] = $fromname;
		  $this->conf['smtpuser'] = $smtpuser;
		  $this->conf['smtppass'] = $smtppass;
		  $this->conf['smtphost'] = $smtphost;
          $this->conf['imaphost'] = $imaphost;
		  $this->conf['smtpsecure'] = $smtpsecure;
          $this->conf['imapsecure'] = $imapsecure; 
		  $this->conf['smtpport'] = $smtpport;
          $this->conf['simaport'] = $simaport;
          $this->conf['message'] = $message;
          $this->conf['subject'] = $subject;  
		  
		  $this->conf['cycles'] = $cycles;
		  $this->conf['timelimit'] = $timelimit;
		  $this->conf['patchlog'] = $patchlog;
		  
		  $this->conf['sleepcommand'] = $sleepcommand;
		  $this->conf['sleepcycles'] = $sleepcycles;
		  
		  $this->conf['nameserverr'] = $nameserver;
		  $this->conf['connection'] = $connection;
		  $this->conf['testphone'] = $testphone;
		  $this->conf['patchmach'] = $patchmach;
          $this->conf['patchseach'] = $patchseach;
          $this->conf['patchfullseach'] = $patchfullseach; 
          $this->conf['patchtemp'] = $patchtemp;  
		  $this->conf['much'] = $much;
          $this->conf['timestart'] = $timestart;
          $this->conf['timestop'] = $timestop;
          $this->conf['permit'] = $permit;
          $this->conf['age_p'] = $age_p;
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
