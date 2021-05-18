<?php
  //Класс ответсвенный за организацию выполнения кода в много поточном режиме.
  //include_once 'classes/log.php';
  include_once 'classes/SqlAdapter.php';
  include_once 'classes/config.php';
  
  
  class much extends SqlAdapter{
  	  
  	  var $_os;																			//Версия ОС.
  	  var $config;																		//Объект конфигурации.
  	  //var $log;																			//Объект логирования.
	  
	  
	  //Конструктор класса.
	  function __construct(){
	  	parent::__construct();															//Выполнение конструктора предшественника.
	  	$this->config = config::getInstance();											//Поучаем объект конфигурации.
        //$this->log = new log('true');													//Создаем объект логирования.
        setlocale(LC_ALL, "en_US");
        $sysname = php_uname();
        if(substr($sysname, 0, 5) === "Linux"){
            $this->_os = "linux";
        } 
        if(substr($sysname, 0, 6) === "Darwin"){
            $this->_os = "osx";
        }
        if(substr($sysname, 0, 7) === "Windows"){
            $this->_os = "windows";
        }
        if(substr($sysname, 0, 7) === "FreeBSD"){
            $this->_os = "freebsd";
        }
	  }
	  
	  //Деструктор класса.
	  function __destruct(){
	  	  parent::__destruct();
		  unset($this->_os, $this->config, $this->log);
	  }
	  
	  
	  
	  
	  //Диспетчер процессов.
	  //Запуск через объект.
	  public function ProcessManager($start = false){
		  if($start){																	//Проверка статуса старта всех процессов.
			  $s = 1;																	//Установка счетчика проходов.
			  while($s <= 20){															//Выполнять не более 20 раз.
				  if(!$this->searchRunProcess()){										//Проверка на предмет незавершенных процессов.
					  $arrProcess = $this->getListProcess();							//Получаем список всех процессов.
					  if(count($arrProcess) >= 1){										//Проверка наличия процессов.
						  foreach($arrProcess as $k => $v){								//Обходим процессы.
							  if($this->initProcess($v['id'])){							//Если инициализация прошла, то...
								  $this->runProcess();									//Запускаем процесс.
							  }
							  else{														//Если инициализация не прошла.
								  $this->delProcess($v['id']);							//Удаляем процесс.
							  }
						  }
						  unset($k, $v, $s, $arrProcess);
						  return true;													//Возвращае истину. Все процессы стартовали.  
					  }
				  }
				  else{																	//Если есть незавершенные процессы, то..
					  sleep(1);															//Ждем секнду.
					  $s++;																//Инкремент счетчика.
				  }
			  }
			  unset($s);	  
		  }
		  else{																			//Иначе проверяем статус завешения всех процессов.
			  return $this->searchRunProcess();											//Если есть незавершенные процессы, то возвращаем истину.
		  }
	  }
	  
	  
	  //Метод запуска процесса.
	  //Запуск через объект.
	  private function runProcess(){
	  	  $pach = $this->config->conf['patchmach'];
		  if($this->_os == 'windows'){		  											//Формиркем комманду для разных ОС.
		  	  $cmd = 'cd '.$pach.' &';
			  $cmd .= ' php processes.php';
		  }
		  if($this->_os == 'linux'){
		  	  $cmd = 'cd '.$pach.';';
			  $cmd .= ' php processes.php';
			  $cmd .= ' > /dev/console &';
		  }
		  if($this->_os == 'freebsd'){
		  	  $cmd = 'cd '.$pach.';';
			  $cmd .= ' php processes.php';
			  $cmd .= ' > /dev/console &';
		  }
		  if($cmd != ''){		  														//Если есть комманда, то выполняем ее.
		  	  exec($cmd);																//Выполняем с выводом результата.
		  }
	  }
	  
	  
	  
	  //Метод инициализации процесса для старта.
	  private function initProcess($id = null){
		  if($id != null){																//Проверка необходимого.
		  	   $query = 'SELECT
		  				*
						FROM
						`processes`
						WHERE 
			  			`id` = \''.$id.'\'';
		  	  $arrp = SqlAdapter::select_sql($query);									//Получаем данные процесса.
		  	  if(count($arrp) >= 1){													//Проверка наличия данных процесса.
				  unset($arrp);															//Сброс данных.
				  $s = 1;																//Начальная установка.
		  		  while($s <= 10){														//Повторять 10 раз.
					  $query = "SELECT
		  						*
								FROM
								`start`";
		  			  $arr =  SqlAdapter::select_sql($query);							//Получаем данные о старте процесса.
		  			  if(count($arr) == 0){												//Если данных нет, то...
						  $query = 'INSERT INTO
			  						`start`
				  					SET
				  					`id` = \''.$id.'\'';
									unset($id, $s, $arr);	
						  SqlAdapter::select_sql($query);								//Добавляем данные.
						  return true;													//Возвращаем истину.
		  			  }
		  			  else{
		  		  		  sleep(1);
						  $s++;
		  			  }	
		  		  }
		  	  }		  		  
		  }
		  return false;																	//Возвращае ложь.
	  }
	  
	  
	  //Метод поиска незавершенного процесаа.
	  public function searchRunProcess(){
		   $arrProcess = much::getListProcess();										//Получаем список всех процессов.
		   if(count($arrProcess) >= 1){
			   foreach($arrProcess as $r => $v){			   							//Обходим процессы.			   	   
			   	   if($v['run'] == '1'){			   	   								//Находим незавершенный процесс.
			   	   	   unset($r, $v, $arrProcess);										//Сброс отработанных переменных.
			   	   	   return true;														//Возвращаем истину.
				   }
			   }
			   unset($r, $v, $arrProcess);
		   }
		   else{																		//Если нет процессов, то..
			   return false;															//Возвращаем ложь.
		   }
		   return false;
	  }
	  
	  
	  //Метод принудительного удения процесса.
	  //Запуск через объект.
	  private function delProcess($id = null){
		  if($id != null){																//Проверка необходимого.
			  $query = 'SELECT
		  				*
						FROM
						`processes`
						WHERE 
			  			`id` = \''.$id.'\'';
		  	  $arr = SqlAdapter::select_sql($query);									//Получаем данные процесса.
		  	  if(count($arr) >= 1){														//Проверка наличия данных.
				  //$this->log->sendLog('Удален процесс не прошедший инициализацию! Данные - '.$arr);//Фиксируем удаленный процес в логе.
		  	  }
			  $query = 'DELETE FROM 
			  			`processes`
			  			WHERE 
			  			`id` = \''.$id.'\'';
		  	  SqlAdapter::select_sql($query);											//Удаляем процесс.
		  	  $query = 'DELETE FROM 
			  			`start`
			  			WHERE 
			  			`id` = \''.$id.'\'';
		  	  SqlAdapter::select_sql($query);											//Удаляем метку о старте.
		  }
	  }
	  
	  
	  
	  //Метод принудительного удаления незвершенных процессов.
	  public function DelNotEndProcess(){
		  $query = 'TRUNCATE TABLE `processes`';
		  SqlAdapter::select_sql($query);
		  $query = 'TRUNCATE TABLE `start`';
		  SqlAdapter::select_sql($query);
	  }
	  
	  
	  //Метод регистрации кода для выполнения и получения его ИД.
	  public function RegCode($code = null){
		  if($code != null){
			  $query = 'INSERT INTO
			  			`processes`
				  		SET
				  		`code` = \''.$code.'\',
				  		`datetime` = NOW();';
						unset($code);	
			  return SqlAdapter::select_sql($query);
		  }
	  }
	  
	  
	  
	  //Метод получения списка всех инициализированных процессов.
	  private function getListProcess(){
		  $query = "SELECT
		  			*
					FROM
					`processes`";
		  return SqlAdapter::select_sql($query);
	  }
	  
	  
  }
?>
