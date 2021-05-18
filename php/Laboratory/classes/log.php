<?php
  //Класс ответственный за ведени и чтения лога.
  include_once 'classes/room.php'; 
  include_once 'library/data.php';
  include_once 'library/select.php';
  
  class log extends room{
  	  
  	  
  	  var $tools;															//Панель инструментов.
  	  var $heat;															//Заглавие задачи.
  	  var $footer;															//Фитер.
  	  var $content;
  	  
  	  
	  
	  
	  //Конструктор класса.
	  function __construct($permission = false){
		  if($permission){													//Проверка разрешения не выводить кабинет и не обрабатывать данные.
			  
		  }
		  else{
			  parent::__construct();																			//Выполнение конструктора предшественника.
			  log::action_log();			  																	//Обрабатываем действия пользователя.	
			  print room::getView();																			//Отображаем кабинет.
		  }
	  }
	  
	  
	  //Деструктор класса.
	  function __destruct(){
	  	  parent::__destruct();
		  gc_collect_cycles();
	  }
	  
	  
	  
	  //Метод обработки действия пользователя.
	  private function action_log(){
	  	  $this->heat = 'Управление журналами';
	  	  $menuDate = log::getMenuLog();																		//Получаем меню дат.
		  $arrLine['0'] = $menuDate;
		  $this->tools = room::getForm(log::getTools());
		  	  
		  
		  if(isset($_POST['date_log'])){																		//Обработка выбора даты.
			  $_SESSION['log']['date_log'] = $_POST['date_log'];
			  unset($_SESSION['log']['record_log']);
		  }
		  if($_SESSION['log']['date_log'] != ''){
			  if(isset($_POST['del_log'])){			  															//Обработка удаления лога.
			  	  $config  = config::getInstance();																//Читаем конфиг.
			  	  $patch = $config->conf['patchlog'].'/';														//Получаем путь.
				  $nameFile = $patch.$_SESSION['log']['date_log'].'.log';
				  unlink($nameFile);																			//Удаляем файл.
				  print '<script> alert(\'Журнал '.$_SESSION['log']['date_log'].'.log'.' удален!\') </script>';	//Выводим оповещение.
				  log::sendLog('Журнал '.$_SESSION['log']['date_log'].'.log'.' удален!');						//Логируем собитие.
				  unset($_SESSION['log']);
				  $menuDate = log::getMenuLog();																//Получаем меню дат.
		  		  $arrLine['0'] = $menuDate;																	//Обнуляем данные.
		  		  unset($config, $patch, $nameFile);
			  }
			  $menuTime = log::getMenuRecord($_SESSION['log']['date_log']);										//Полчаем меню времен.
			  $arrLine['1'] = $menuTime;
			  $arrLine['2'] = log::getViewLog($_SESSION['log']['date_log']);
			  
			  if(isset($_POST['Record_log'])){																	//Обработка выбора вреиени.
				  $_SESSION['log']['record_log'] = $_POST['Record_log'];
			  }
			  if($_SESSION['log']['record_log'] != ''){
				  $arrLine['2'] = log::getViewRecord($_SESSION['log']['date_log'], $_SESSION['log']['record_log']);
			  }
			  unset($menuDate);
		  }

		  $this->content = room::getElementsLine($arrLine);
		  $this->tools = room::getForm(log::getTools());
		  room::outContent(log::getView());																		//Вывод всего модуля.
		  unset($arrLine);
	  }
	  
	  
	  
	  
	  //Метод отображения модуля.
	  public function getView(){
		  return room::getViewModule();
	  }
	  
	  
	  //Метод получения панели инстркментов.
	  private function getTools(){
	  	  if($_SESSION['log']['date_log'] != ''){
			  $ret = '<input type="submit" name="del_log" value="Удалить выбранный лог - ('.$_SESSION['log']['date_log'].'.log)">';
	  	  }
		  return $ret;
	  }
	  
	  
	  //Метод получения меню готовых логов по дате.
	  private function getMenuLog(){
		  $config  = config::getInstance();
		  $patch = $config->conf['patchlog'].'/';																//Получаем путь.
		  $arr_log = router::getListFile($patch);																//Получаем список логов по дате.
		  if(count($arr_log) >= 1){																				//Проверка наличия данных.
			  foreach($arr_log as $k => $v){																	//Готовим данные для меню.
				  $name = explode('.log', $v);																	//Получаем дату с имени файла.
				  $arr_m[$k]['name'] = $name['0'];																//Формируем массив.
			  }
			  unset($k, $v);
			  $select = new select();
			  $ret .= 'Выбор</br>даты:</br>'."\n";;
			  $ret .= $select('date_log', 'name', $arr_m, 10)."\n";
			  $ret = room::getForm($ret);
			  unset($select, $config, $patch, $arr_log);
			  return $ret;
		  }
	  }
	  
	  
	  //Метод получения меню записей в логе по дате.
	  private function getMenuRecord($date = null){
		  if($date != null){																					//Проверка наличия даты.
			  $arrFile = log::readLog($date);																	//Читаем лог.
			  if(count($arrFile) >= 1){																			//Проверка данных.
				  $select = new select();
				  $ret .= 'Выбор</br>времени:</br>'."\n";
				  $ret .= $select('Record_log', '0', $arrFile, 10)."\n";
				  $ret = room::getForm($ret);
				  unset($date, $select, $arrFile);
				  return $ret;
			  }
		  }
	  }
	  
	  
	  //Метод получения данных с файла лока для просмотра
	  private function getViewLog($date = null){
		  if($date != null){
			  $config  = config::getInstance();																	//Читаем конфиг.
		  	  $patch = $config->conf['patchlog'].'/';															//Получаем путь.
			  $file = router::getDataFile($patch.'/'.$date.'.log');											//Читаем содержимое файла.
			  unset($config);
			  return 'Данные с файла - "'.$patch.$date.'.log"</br><textarea rows="12" cols="70"  readonly>'.$file.'</textarea>';
		  }
	  } 
	  
	  
	  
	  
	  //Метод получения расшифрованой строки лога для просмотра.
	  private function getViewRecord($date = null, $time = null){
		  if($date != null && $time != null){																	//Проверка наличия данных.
			  $file = log::readLog($date);																		//Получения данных с файла.
			  if(count($file) >= 1){																			//Проверка наличия данных
				  foreach($file as $k => $v){																	//Обходим данные.
					  if($v['0'] == $time){																		//Нахдим нужное время.
						  $arr = $v;																			//Формируем данные.
						  break;																				//Выход из цикла.
					  }
				  }
				  unset($k, $v);
				  $arrUser = room::getListTable('login');														//Получаем массив пользователей.
				  foreach($arrUser as $k_u => $v_u){
					  if($v_u['id'] == $arr['2']){																//Определяем имя пользователя.
						  $user = $v_u['name'];
						  break;
					  }
				  }
				  unset($k_u, $v_u);																			//Сброс переменных.
				  if($arr['4'] != ''){																			//Проверка наличия аналитики.
					  $arrAn = str_replace(',', "\n", $arr['4']);												//Готовим данные.
					  $arrAn = str_replace('=>', '=', $arrAn);													//Готовим данные.
				  }
				  //Готовим данные для просмотра.
				  $ret .= 'Расшифровка записи на время - ('.$time.')</br>';
				  $ret .= 'Время - <input type="text" size="8" value="'.$arr['0'].'" readonly></br>'."\n";
				  $ret .= 'IP пользователя - <input type="text" size="32" value="'.$arr['1'].'" readonly></br>'."\n";
				  $ret .= 'Пользователь - <input type="text" size="35" value="'.$user.'" readonly></br>'."\n";
				  $ret .= 'Сообщение - <textarea rows="2" cols="40" readonly>'.$arr['3'].'</textarea></br>'."\n";
				  $ret .= 'Аналитика - <textarea rows="5" cols="40" readonly>'.$arrAn.'</textarea></br>'."\n";
				  unset($date, $time, $file, $arrUser, $arr, $arrAn);
				  return $ret;																					//Возвращаем данные.
			  }
		  }
	  }
	  
	  
	  
	  
	  
	  
	  //Метод получения имени лога на каждый день.
	  private function getNameFile($data = null){
		  if($data == null){																					//Если нет имени, то получаем его.
		  	  $obj = new data('Europe/Kiev');																	//Получаем объект дата.
			  $data = $obj->data_i;																				//Получем текущею дату.
		  }
		  unset($obj);																							//Сбрасфваем объект.									
		  $config  = config::getInstance();																		//Читаем конфиг.
		  $patch = $config->conf['patchlog'].'/';																//Получаем путь к файлу.
		  return $patch.'/'.$data.'.log';
	  }
	  
	  
	  //Метод записи данных в лог.
	  public function sendLog($text = null, $arr = null){
		  if($text != null){																					//Проврка наличия текста.
			  $d = '';
			  if(count($arr) >= 1){																				//Проврка наличия массива аналитических данных.
				  foreach($arr as $k => $v){
					  $d .= $k.' => '.$v.', ';
				  }
				  unset($k, $v);
			  }
			  
			  $url = router::getCurrentUrl();
			  $ip = $_SERVER["REMOTE_ADDR"];																	//IP пользователя.
			  $idUser = $_SESSION['login']['id'];																//Получаем ИД пользователя.
			  $nameModule = router::getNameModule($url);
			  $nameFile = log::getNameFile();																	//Получаем имя файла.
			  $obj = new data('Europe/Kiev');																	//Получаем объект дата.
			  $time = $obj->time_i;																				//Получить текущее время.
			  $send = $time.'|'.$ip.'|'.$idUser.'|'.$text.'|'.$d;												//Получаем сообщение.
			  unset($obj);
			  for($a = 0; $a <= 5; $a++){																		//Делаем 5 попыток.
				  if(log::writeFile($nameFile, $send)){															//Если запись прошла то возвращаемся.
					  unset($a, $text, $arr, $d, $url, $ip, $idUser, $nameModule, $time, $send);
					  return;
				  }
				  else{																							//Иначе ждем секунду.
					  sleep(1);
				  }
			  }
			   print '<script> alert(\'Внимание! Файл лога '.$nameFile.' открыт для записи.\') </script>';		//Если записи не удалась, выдаем сообщение.
		  }
	  }
	  
	  
	   
	  
	  
	  //Метод дописывания в файл с новой строки.
	  public function writeFile($filename = null, $somecontent = null){
		  if($filename != null && $somecontent != null){														//Проверка необходимого.
			  if(file_exists($filename)){																		//Проверка наличия файла.
				  
			  }
			  else{
				  $fp = fopen($filename, 'w');																	//Создаем файл.
				  fclose($fp); 																					//Закрытие файла
			  }
			  
			  //Вначале убедимся, что файл существует и доступен для записи.
			  if(is_writable($filename)){			  															//Проверка наличия файла.
			  	  $handle = fopen($filename, 'a');
				  if($handle){				  			    													//Проверка открит ли файл.
				  	  // Записываем $somecontent в наш открытый файл.
					  if(!fwrite($handle, $somecontent."\r\n")){					  							//Если запись не произведена, то
					  	  fclose($handle);																		//Закрытие файла.
					      return false;																			//Возвращаем лож.
					  }
					  else{
					  	  fclose($handle);																		//Закрытие файла
						  return true;				    														//Возвращаем истина, запись сделана.
					  }
				  }
				  else{
				  	  return false;			         															//Если нет, то возвращаем ложь.
				  }
			  }
			  else{
			  	  return false;																					//Если фал не доступен, возвращаем ложь.
			  }
		  }
		  return false;  
	  }
	  
	  
	  //Метод чтения лога из файла.
	  private function readLog($date = null){
		  if($date != null){
			  $config  = config::getInstance();																	//Получаем конфиг.
			  $patch = $config->conf['patchlog'].'/';															//Получаем путь.	
			  $file = router::getDataFile($patch.$date.'.log');													//Читаем содержимое файла.
			  $arr = explode("\n", $file);																		//Формируем массив строк.
			  if(count($arr) >= 1){																				//Проверка наличия строк.
				  foreach($arr as $k => $v){																	//Обходим строки.
					  if($v != ''){																				//Проверка наличия данных в строке.
						  $arrRecord = explode('|', $v);														//Формируем массив записей в строке.
						  if(count($arrRecord) >= 1){															//Проверка наличия записей в строке.
							  $arrFile[$k] = $arrRecord;														//Формируем массив данных.
						  }
					  }
				  }
				  unset($k, $v, $arr, $arrRecord, $patch, $file);												//Сброс переменных.
				  return $arrFile;																				//Возвращаем массив.
			  }
		  }
	  }
	    
  }
  
?>
