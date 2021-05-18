<?php
  //Класс ответственный за организацию личного кабинета управления сервером.
  include_once 'classes/login.php';
  include_once 'library/much.php';
  include_once 'library/php_serial.class.php';
  include_once 'library/data.php';
  include_once 'classes/register.php';
  //include_once 'classes/log.php';  
  include_once 'library/jan_menu.php'; 
  
  class room extends login{
  	  
  	  var $menu;											//Меню кабинета.
  	  var $heat;											//Заглавие задачи.
  	  var $footer;											//Фитер кабинета.
  	  var $content;											//Выводимый контент кабинента.
  	  var $serial;											//Объект для взаимодействя с портами.	
  	  var $much;											//Объект для многопотокового выполнения.
  	  var $config;											//Объект конфигурации.
      //var $log;                                             //Объект логирования. 
  	  var $date;											//Объект дата.
      var $register;                                        //Объект для работы с реестром пациентов.
      var $jan_menu;                                        //Объект анимационного меню.
  	  
  	  //Конструктор класса.
	  function __construct($permission = false){
	  	  $this->serial = new phpSerial();					//Создание объекта для взаимодействия с портами.
		  $this->much = new much();							//Создание объекта многопотокового выполнения.
		  $this->config = config::getInstance();			//Создаем объект конфигурации.
          $this->register = new register();                 //Создание объекта реестра пациентов.
		  $this->date = new data('Europe/Kiev');
          $this->jan_menu = new jan_menu();                 //Создаем объект анимационного меню.
          //$this->log = new log('true');
          
		  if(!$permission){									//Проверка разрешения.
			  parent::__construct();						//Выполнение конструктора предшесвенника.
			  room::action_room();							//Обрабатываем действия пользователей.
			  //$this->menu = room::getMenu(room::getDataMenu());
              $this->menu = room::getMenuAnime(room::getDataMenu());
			  $url = router::getCurrentUrl();
			  if(router::getNameModule($url) == 'room'){	//Если запущен модуль, то показываем кабинет.
				  print room::getView();					//Показываем кабинет.
			  }
		  }
		  unset($url);
	  }
	  
	  
	  //Деструктор класса.
	  function __destruct(){
	  	  unset($this->serial, $this->much, $this->config, $this->date);
	  	  parent::__destruct();
		  gc_collect_cycles();
	  }
	  
	  //Метод обработки действия пользователя.
	  private function action_room(){
		  
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
	  
	  //Метод возвращает вид кабинета.
	  function getView(){
	  	  //$this->heat = '<div>МЕНЮ </div>Автоматизированная система для лаборатории!';
          $line['0'] = '<input type="button" value="Меню" style="position: fixed;  left: 5; top: 5;" class="menu-start">';
          $line['1'] = 'Автоматизированная система для лаборатории!';
          $this->heat = room::getElementsLine($line).$this->menu;
	  	  $this->footer = '@load16 <div style="font-size: 70%;display: inline-block;">Дата -'.$this->date->data_i.' '.$this->date->time_i.'</div>';
	  	  //$line['0'] = $this->menu;
	  	  //$line['1'] = $this->content;
	  	  $url = router::getCurrentUrl();
	  	  $arr = explode('index.php', $url);
	  	  $url = $arr['0'].'js';
	  	  $urlcss = $arr['0'].'css';
		  $ret = '
		  <head>
            '.$this->jan_menu->load().'
            '.room::LoadLibrary('print.js').'
      		<SCRIPT src="'.$url.'/close.js" type="text/javascript">
      		</SCRIPT>
      		<link href="'.$urlcss.'/style.css" rel="stylesheet">
      		'.room::LoadLibrary().'
		  </head>
		  <body>
			  <div>
		  		<div class="hat">
		  			'.room::getElementsСenter($this->heat).'
		  		</div>';
                if($this->content != ''){
                    $ret .= '                
		  		<div class="content">
		  			'.$this->content.
		  		'</div><br/><br/><br/><br/><br/><br/>';
                }
		  		$ret .= '
                <div class="bottom">
		  			'.room::getElementsСenter($this->footer).'
		  		</div>
			  </div>
		  </body>';
		  unset($urlcss, $url, $arr, $line);
		  return $ret;
	  }
	  
	  
	  //Метод вывода контента
	  public function outContent($content){
		  $this->content = $content;
	  }
      
	  
	  
	  //Метод загрузки Javascrip и CSS.
	  public function LoadLibrary($name = null){
		  if($name != null){																			//Проверка наличия имени.
			  $_SESSION['loadlibrary'][$name] = $name;													//Регистрируем библиотеку.  
		  }
		  else{																							//Иначе читаем зарегистрированные быблиотеки.
			  $url = router::getCurrentUrl();
	  		  $arr = explode('index.php', $url);
	  		  $url = $arr['0'].'js';
	  		  $urlcss = $arr['0'].'css';
	  		  
	  		  
	  		  $arr = $_SESSION['loadlibrary'];															//Формируем массив.
	  		  if(count($arr) >= 1){																		//Проверка наличия элементов.
				  foreach($arr as $kk => $vv){															//Обходим массив.
					  $arrname = explode('.', $vv);														//Зазделяем имя.
					  if(count($arrname) >= 2){															//Проверка наличия расширения.
						  foreach($arrname as $k => $v){
							  $key = $v;																//Определяем ключ последненго элемента.
						  }
						  if($key == 'js'){																//Если JS то ведем обработку скрипта.
							  $ret .= '<SCRIPT src="'.$url.'/'.$vv.'" type="text/javascript"></SCRIPT>'."\n";
						  }
						  else{																			//Иначе CSS.
							  $ret .= '<link href="'.$urlcss.'/'.$vv.'" rel="stylesheet">'."\n";
						  }
						  unset($k, $v);
					  }
				  }
				  unset($kk, $vv);
	  		  }
	  		  unset($_SESSION['loadlibrary']);															//Штатный сброс настроек.
	  		  unset($arr, $url, $key, $arrname, $name);
	  		  return $ret;																				//Возвращаем результат для хедера.  
		  }
	  }
	  
	  
	  //Метод получения формы.
	  public function getForm($form = null){
		  if($form != null){
			  $url = router::getCurrentUrl();
			  $ret .= '<form  action="'.$url.'" method="post">'."\n";
			  $ret .= $form."\n";
			  $ret .= '</form>'."\n";
			  unset($form, $url);
			  return $ret;
		  }
	  }
	  
	  
		  //Метод постройки элементов в рад.
	    public function getElementsLine($arr = null){
	        if(count($arr) >= 1){
	            foreach($arr as $key => $value){
	                if($value != ''){
	                    $ret .= "\t".'<div class="" style="display: inline-block; vertical-align: top;">'."\n";
	                    $ret .= "\t"."\t".'<div style="display: block;">'."\n";
	                    $ret .= "\t"."\t"."\t".$value;
	                    $ret .= "\t"."\t".'</div>'."\n";
	                    $ret .= "\t".'</div>'."\n";
	                }
	            }
	            unset($arr);
	            return $ret;
	        }
	    }
    
	    //Метод постройки элементов в столбик.
	    public function getElementsColumn($arr = null){
	        if(count($arr) >= 1){
	            foreach($arr as $key => $value){
	                if($value != ''){
	                    $ret .= "\t".'<div class="" style="display: block; vertical-align: top;">'."\n";
	                    $ret .= "\t"."\t".'<div style="display: block;">'."\n";
	                    $ret .= "\t"."\t"."\t".$value;
	                    $ret .= "\t"."\t".'</div>'."\n";
	                    $ret .= "\t".'</div>'."\n";
	                }
	            }
	            unset($key, $value, $arr);
	            return $ret;
	        }
	    }
	    
	    //Метод размещения элемента по центру.
	    public function getElementsСenter($content = null){
	        if($content != ''){
	            $ret .= "\t".'<div style="margin:0 auto;">'."\n";
	            $ret .= "\t"."\t".$content; 
	            $ret .= "\t".'</div>'."\n";
	            return $ret;
	        }
	    }
	    
	    //Метод размещения элемента в верху.
	    public function getElementsUP($content = null){
	        if($content != ''){
	            $ret .= "\t".'<div style="display: block; vertical-align: top;">'."\n";
	            $ret .= "\t"."\t"."\t".$content;
	            $ret .= "\t".'</div>'."\n";
	            return $ret;
	        }
	    }
	    
	    //Метод размещения элемента в внизу.
	    public function getElementsDOWN($content = null){
	        if($content != ''){
	            $ret .= "\t".'<div style="display: block; vertical-align: bottom;">'."\n";
	            $ret .= "\t"."\t"."\t".$content;
	            $ret .= "\t".'</div>'."\n";
	            return $ret;
	        }
	    }
	    
	    
	    //Метод приготовки блока к печати.
	    public function getDivPrint($content = null, $id = null){
	        if($id != null && $content != null){
	            //$ret .= '<div style="color: #000000;" id="'.$id.'">'."\n";
                $ret .= '<div id="'.$id.'">'."\n"; 
	            $ret .= "\t".$content."\n";
	            $ret .= '</div>'."\n";
	            return $ret;
	        }
	    }
	    //Метод подготовки кнопки для печати.
	    public function getButtonPrint($id = null){
	        if($id != null){
	        	room::LoadLibrary('print.js');
	            return '<input type="button" title="Печать отчета!" class="button" onclick="printDiv(\''.$id.'\');" value="Печать" />';   
	        }
	    }
	  
	  
	  //Метод получения данных для главного меню.
	  private function getDataMenu(){
		  $menu['Регистрация'] = 'loglab';
          $menu['Загрузка'] = 'loading'; 
          $menu['Просмотр'] = 'wiev'; 
          $menu['Отправить'] = 'send'; 
		  $menu['Писковик'] = 'search'; 
		  $menu['Письма'] = 'read_mail';
		  
          $menu['Пользователи'] = 'users'; 
		  $menu['Управление'] = 'control';
		  
          $menu['Отчеты'] = 'report'; 
          $menu['Логи'] = 'log';
		  $menu['Выход'] = 'logout';
		  return $menu;
	  }
	  
	  
	  //Метод получения ссылки.
	  public function getUrlMenu($url = null, $value = null, $title = null){
		  if($url != null && $value != null){ 
			  $ret .= '
			  <li>
			  	<a href="'.$url.'">'.$value.'</a>
			  </li>
			  ';
			  unset($url, $value, $title);
			  return $ret;
		  }
	  }
	  
	  //Метод получения главоно меню.
	  private function getMenu($arrMenu = null){
		  if($arrMenu != null){                
			  $ret .= 'Главное меню:</br>'."\n";
			  foreach($arrMenu as $key => $value){
			  	  $ret .= room::getUrlMenu($value, $key);
			  }
			  unset($key, $value, $arrMenu);
			  $ret = '<lu>'.$ret.'</lu>';
              $ret = '<div class="main_menu">'.$ret.'</div>';
              return $ret;
		  }
	  }
      
      /*
      //Метод получения скрипта для анимации меню.
      private function getScriptAnime(){
          $ControlObject = '.menu-start';
          $ManagedObject = '.menu-main';
          $AnimationSpeedUp = 500;
          $AnimationSpeedDown = 300;
          $TypeAnimation = '';
          return $this->jan_menu->StartMenu($ControlObject, $ManagedObject, $AnimationSpeedUp, $AnimationSpeedDown, $TypeAnimation);
      }
      */
      
      //Метод получения анимацинооного главоно меню.
      private function getMenuAnime($arrMenu = null){
          if($arrMenu != null){              
              
              $ControlObject = '.menu-start';
              $ManagedObject = '.main_menu';
              $AnimationSpeedUp = 500;
              $AnimationSpeedDown = 300;
              $TypeAnimation = ''; 
              $ret .= '<div class="module_heat">Главное меню:</div>'."\n";
              foreach($arrMenu as $key => $value){
                    $ret .= room::getUrlMenu($value, $key);
              }
              unset($key, $value, $arrMenu);
              $ret = '<lu>'.$ret.'</lu>';
              $ret = '<div style="" class="main_menu">'.$ret.'</div>';
              $ret .= $this->jan_menu->StartMenu($ControlObject, $ManagedObject, $AnimationSpeedUp, $AnimationSpeedDown, $TypeAnimation);
              return $ret;
          }
      }
	  
	  
	  //Метод валидации вводимых данных.
  	  public function ValidationCodeData($var = null, $quiet = false){  
		  if($var != null){
			  if(is_array($var)){												//Если массив, то ведеи обработку.
				  foreach($var as $key => $value){								//Обходим массив.
					  if(router::validationCode($value)){						//Проводим валидацию.
						  
					  }
					  else{														//Если не пройдена, то возвращаем результат.
						  if($quiet == false){									//Если режим не тихий, то выводим сообщение.
							  print '<script> alert(\'Валидация на враждебный код не пройдена!\') </script>';
						  }
						  return false;
					  }
				  }
				  unset($key, $value);
				  return true;													//Если не найдено, то возвращаем true.
			  }
			  return router::validationCode($var);
		  }
		  return true;
  	  }
	  
	  
	  //Удаление данных по ИД.
	  public function DeleteRcord($id = null, $nameTable = null){
		  if($id != null && $nameTable != null){
			  $query = 'DELETE FROM 
			  			`'.$nameTable.'`
			  			WHERE 
			  			`id` = \''.$id.'\'';
			  return SqlAdapter::select_sql($query);
		  }
	  }
	  
	  
	   //Метод получения всех записей таблици.
	   public function getListTable($nameTable = null){
		   if($nameTable != null){
			    $query = "SELECT
		  			*
					FROM
					`".$nameTable."`";
		  		return SqlAdapter::select_sql($query);
		   }
	   }
	   
	   //Метод получения ссыдки на объек
	   public function getLInkObj($obj = null){
		   if($obj != null){
		   	   $ret = &$this->$obj;
			   return $ret;
		   }
	   }
	   
	   
	   
	   //Метод настройки порта для отправки.
	   public function tuningPort($port = null){
		   if($port != null){
			   if($this->serial->deviceSet("COM".$port)){											//Установка порта.
				   if($this->serial->deviceOpen()){													//Открытие порта.
					   return true;																	//Если настройка прошла, то возвращаем истину.
				   }
			   }
		   }
		   $this->serial->deviceClose();															//Закрытие порта.
		   unset($port);
		   return false;																			//Если натройка не прошла созвращаем ложь.
	   }
	   
	   
	   
	   
	   
	   //Метод отправки команды в порт.
	   public function sendCommand($commmand = null, $wait = null, $read = false){
		   if($command != null){
			   if($wait == null){			   														//Проверка наличия параметра ожидания.
			   	   $this->serial->sendMessage($command, $this->config->conf['sleepcommand']);		//Отправляем комманду и ждем.
			   }
			   else{			   																	//Есл параметра нет, то береи из конфига.
			   	   $this->serial->sendMessage($command, $wait);										//Отправляем комманду и ждем.
			   }
			   if($read){																			//Проверка наличия флага чтения.
				   return $this->serial->readPort();
			   }
		   }
	   }
	   
	   
	   
	   //Метод выполнения комманды.
	   public function rumCommand($command = null, $serialPort = null, $wait = null, $read = false){
		   if($command != null && $serialPort != null){												//Проверка наличия параметров.
			   if(room::tuningPort($serialPort)){													//Настройка порта.
				   $result = room::sendCommand($command, $wait, $read);
				   if($read){
				   		if(stristr($result, 'OK') === false){
				   			$this->serial->deviceClose();												//Закритие порта.
						   	return false;
					   	}
					   	else{
							return true;
					   	}
				   }
			   }
			   else{																				//Если настройка не прошла, то возвращаем ложь.
				   return false;
			   } 
		   }
		   return false;
	   }
	  
	  
	  //Метод выполнения массива комманд.
	  public function runArrCommand($arr = null, $wait = null, $read = false){
		  if(count($arr) >= 1){																		//Проверка наличия массива комманд и порта.
			  foreach($arr as $k => $v){															//Обходим массив комманд.
				  $result = room::sendCommand($v, $wait, $read);									//Выполняем комманду, получаем результат.
				  if($read){
					  if(stristr($result, 'OK') == false){												//Усли комманда не пройшла, то возврвщаем фальш.
						  unset($k, $v, $result, $arr, $wait, $read);
						  return false;
					  }
				  }
			  }
			  unset($k, $v, $result, $arr, $wait, $read);
			  return true;																			//Если все комманды прошли, то возвращаем истину.
		  }
		  return false;																				//Если нет параметров, то возвращаем фальш.
	  }
	  
	  //Метод обнаружения AJAX запроса.
	  public function detectionAJAX(){
		  if($_POST['ajax'] != ''){
			  return true;
		  }
		  else{
			  return false;
		  }
	  }
	  
	  //Метод организации фонового AJAX запроса.
	  public function setFoneAJAX($id_reception = null, $id_data = null, $namemodule = null, $time = null){
	  	  if($id_reception != null && $id_data != null && $namemodule != null && $time != null){
			  room::LoadLibrary('jquery-2.1.3.min.js');															//Подключаем билиотеку для Jquery запроса.
	  		  room::LoadLibrary('ajax.js');																		//Подключаем билиотеку для AJAX запроса.
	  		  room::LoadLibrary('add.js');
	  		  //Получаем набор параметров для AJAX запроса.
	  		  $url = router::getCurrentUrl();
	  		  
	  		  $time = '
<script>
    var timerId = setTimeout(function tick() {
   		AjaxQuery.AjaxPostAsyncShort(\'get\',\'data\',\''.$id_data.'\',\''.$namemodule.'\',\''.$url.'\');
   		addQuery.add(\'reception_data\',\''.$id_reception.'\');
    var div = $(\'#'.$id_reception.'\');
   		div.scrollTop(div.prop(\'scrollHeight\'));
		timerId = setTimeout(tick, '.$time.');
	}, '.$time.');
</script>';
			  $ret .= '<div style="display: none;" id="'.$id_data.'"></div>';
			  return $ret.$time;
		  }
	}
	  		  
	  
	  
	  
  }
?>
