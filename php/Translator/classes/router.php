<?php
  //Класс ответственный за маршрутизацию.
  //include_once 'classes/SqlAdapter.php';
  include_once 'classes/room.php'; 
  
  //include_once 'classes/translator.php';
  //include_once 'classes/translate.class.php';
  include_once 'classes/processing.php';
  //include_once 'config.php';  
  class router{
  	  
  	  var $url;											//Текущий адрес системы.
	  
	  
	  //Конструктор класса.
	  function __construct($permission = false){
		  $room  = new room(); 
	  }
	  
	  //Деструктор класса.
	  function __destruct(){
	  	  unset($this->url);
		  gc_collect_cycles();
	  }
	  
	  
	  //Метод обработки действия пользователя.
	  private function action_router(){
	  	  $url = router::getCurrentUrl();					//Получаем текущий URL.
	  	  $a = explode('index.php', $url);					//Разделяем.
	  	  if(												//Проверка наличия индекса.
	  	  $a['0'] != '' && $a['1'] == ''
	  	  //&& !stristr($url, 'cron.php')
	  	  ){
			  $url  = router::getDefaultUrl();				//Если нет, то получаем URL поумолчанию.
			  header('Location:'.$url);						//Перенаправление.
		  }
		  unset($url, $a);
	  }
	  
	  
	  //Метод выполнения маршрутизации.
	  public function getRoute($url = null){
		  if($url == null){									//Если есть маршрут, то выполняем готовим его.
			  $u = $this->url;
		  }
		  else{												//Иначе берем готовый.
			  $u = $url;
		  }
		   $nameObj = router::getNameModule($u);			//Получаем имя модуля.
		   $obj = router::createObj($nameObj);
		   unset($nameObj, $u);
		   return $obj;
	  }
	  
	  
	  //Метод получения URL по умолчанию.
	  public function getDefaultUrl(){
		  $url = router::getCurrentUrl();
		  $a = explode('index.php', $url);
		  if($a['0'] != ''){
			  $url  = $a['0'].'index.php/room';
			  return $url;
		  }
		  
	  }
	  
	  
	  //Метод получение текущего URL.
	  public function getCurrentUrl() {
		  $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
		  $url .= ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
		  $url .= $_SERVER["REQUEST_URI"];
		  return $url;
	  }
	  
	  
	  //Метод перекодировки текста.
	  public function transcoding($text = null, $code = 'Windows-1251'){
		  if($text != null){
			  $c = mb_detect_encoding($text);					//Определяем кодировку текста.
			  if($c == $code){									//Если кодировка не совпадает с требуемой, то ведем перекодировку.
				  
			  }
			  else{
				  $text = mb_convert_encoding($text, $c, $code);
			  }
			  return $text;
		  }
	  }
	  
	  
	  // Метод раскодирует строку из URL 
	  public function UrlDecode($s){
          $arr =  array ('%20'=>' ', '%D0%B0'=>'а', '%D0%90'=>'А', '%D0%B1'=>'б', '%D0%91'=>'Б',
          '%D0%B2'=>'в', '%D0%92'=>'В', '%D0%B3'=>'г', '%D0%93'=>'Г', '%D0%B4'=>'д', '%D0%94'=>'Д',
          '%D0%B5'=>'е', '%D0%95'=>'Е', '%D1%91'=>'ё', '%D0%81'=>'Ё', '%D0%B6'=>'ж', '%D0%96'=>'Ж',
          '%D0%B7'=>'з', '%D0%97'=>'З', '%D0%B8'=>'и', '%D0%98'=>'И', '%D0%B9'=>'й', '%D0%99'=>'Й',
          '%D0%BA'=>'к', '%D0%9A'=>'К', '%D0%BB'=>'л', '%D0%9B'=>'Л', '%D0%BC'=>'м', '%D0%9C'=>'М',
          '%D0%BD'=>'н', '%D0%9D'=>'Н', '%D0%BE'=>'о', '%D0%9E'=>'О', '%D0%BF'=>'п', '%D0%9F'=>'П',
          '%D1%80'=>'р', '%D0%A0'=>'Р', '%D1%81'=>'с', '%D0%A1'=>'С', '%D1%82'=>'т', '%D0%A2'=>'Т',
          '%D1%83'=>'у', '%D0%A3'=>'У', '%D1%84'=>'ф', '%D0%A4'=>'Ф', '%D1%85'=>'х', '%D0%A5'=>'Х',
          '%D1%86'=>'ц', '%D0%A6'=>'Ц', '%D1%87'=>'ч', '%D0%A7'=>'Ч', '%D1%88'=>'ш', '%D0%A8'=>'Ш',
          '%D1%89'=>'щ', '%D0%A9'=>'Щ', '%D1%8A'=>'ъ', '%D0%AA'=>'Ъ', '%D1%8B'=>'ы', '%D0%AB'=>'Ы',
          '%D1%8C'=>'ь', '%D0%AC'=>'Ь', '%D1%8D'=>'э', '%D0%AD'=>'Э', '%D1%8E'=>'ю', '%D0%AE'=>'Ю',
          '%D1%8F'=>'я', '%D0%AF'=>'Я');
          $s = strtr($s, $arr);
	  		//$s = strtr ($s, array ("%20"=>" ", "%D0%B0"=>"а", "%D0%90"=>"А", "%D0%B1"=>"б", "%D0%91"=>"Б", "%D0%B2"=>"в", "%D0%92"=>"В", "%D0%B3"=>"г", "%D0%93"=>"Г", "%D0%B4"=>"д", "%D0%94"=>"Д", "%D0%B5"=>"е", "%D0%95"=>"Е", "%D1%91"=>"ё", "%D0%81"=>"Ё", "%D0%B6"=>"ж", "%D0%96"=>"Ж", "%D0%B7"=>"з", "%D0%97"=>"З", "%D0%B8"=>"и", "%D0%98"=>"И", "%D0%B9"=>"й", "%D0%99"=>"Й", "%D0%BA"=>"к", "%D0%9A"=>"К", "%D0%BB"=>"л", "%D0%9B"=>"Л", "%D0%BC"=>"м", "%D0%9C"=>"М", "%D0%BD"=>"н", "%D0%9D"=>"Н", "%D0%BE"=>"о", "%D0%9E"=>"О", "%D0%BF"=>"п", "%D0%9F"=>"П", "%D1%80"=>"р", "%D0%A0"=>"Р", "%D1%81"=>"с", "%D0%A1"=>"С", "%D1%82"=>"т", "%D0%A2"=>"Т", "%D1%83"=>"у", "%D0%A3"=>"У", "%D1%84"=>"ф", "%D0%A4"=>"Ф", "%D1%85"=>"х", "%D0%A5"=>"Х", "%D1%86"=>"ц", "%D0%A6"=>"Ц", "%D1%87"=>"ч", "%D0%A7"=>"Ч", "%D1%88"=>"ш", "%D0%A8"=>"Ш", "%D1%89"=>"щ", "%D0%A9"=>"Щ", "%D1%8A"=>"ъ", "%D0%AA"=>"Ъ", "%D1%8B"=>"ы", "%D0%AB"=>"Ы", "%D1%8C"=>"ь", "%D0%AC"=>"Ь", "%D1%8D"=>"э", "%D0%AD"=>"Э", "%D1%8E"=>"ю", "%D0%AE"=>"Ю", "%D1%8F"=>"я", "%D0%AF"=>"Я")); 
			return $s; 
	  }
	  
	   // Метод кодирует строку для URL 
	   public function UrlEncode($s){
           $arr =  array (' '=> '%20', 'а'=>'%D0%B0', 'А'=>'%D0%90','б'=>'%D0%B1', 'Б'=>'%D0%91',
           'в'=>'%D0%B2', 'В'=>'%D0%92', 'г'=>'%D0%B3', 'Г'=>'%D0%93', 'д'=>'%D0%B4', 'Д'=>'%D0%94',
           'е'=>'%D0%B5', 'Е'=>'%D0%95', 'ё'=>'%D1%91', 'Ё'=>'%D0%81', 'ж'=>'%D0%B6', 'Ж'=>'%D0%96',
           'з'=>'%D0%B7', 'З'=>'%D0%97', 'и'=>'%D0%B8', 'И'=>'%D0%98', 'й'=>'%D0%B9', 'Й'=>'%D0%99',
           'к'=>'%D0%BA', 'К'=>'%D0%9A', 'л'=>'%D0%BB', 'Л'=>'%D0%9B', 'м'=>'%D0%BC', 'М'=>'%D0%9C',
           'н'=>'%D0%BD', 'Н'=>'%D0%9D', 'о'=>'%D0%BE', 'О'=>'%D0%9E', 'п'=>'%D0%BF', 'П'=>'%D0%9F',
           'р'=>'%D1%80', 'Р'=>'%D0%A0', 'с'=>'%D1%81', 'С'=>'%D0%A1', 'т'=>'%D1%82', 'Т'=>'%D0%A2',
           'у'=>'%D1%83', 'У'=>'%D0%A3', 'ф'=>'%D1%84', 'Ф'=>'%D0%A4', 'х'=>'%D1%85', 'Х'=>'%D0%A5',
           'ц'=>'%D1%86', 'Ц'=>'%D0%A6', 'ч'=>'%D1%87', 'Ч'=>'%D0%A7', 'ш'=>'%D1%88', 'Ш'=>'%D0%A8',
           'щ'=>'%D1%89', 'Щ'=>'%D0%A9', 'ъ'=>'%D1%8A', 'Ъ'=>'%D0%AA', 'ы'=>'%D1%8B', 'Ы'=>'%D0%AB',
           'ь'=>'%D1%8C', 'Ь'=>'%D0%AC', 'э'=>'%D1%8D', 'Э'=>'%D0%AD', 'ю'=>'%D1%8E', 'Ю'=>'%D0%AE',
           'я'=>'%D1%8F', 'Я'=>'%D0%AF');
           $s = strtr($s, $arr);
		   //$s= strtr ($s, array (" "=> "%20", "а"=>"%D0%B0", "А"=>"%D0%90","б"=>"%D0%B1", "Б"=>"%D0%91", "в"=>"%D0%B2", "В"=>"%D0%92", "г"=>"%D0%B3", "Г"=>"%D0%93", "д"=>"%D0%B4", "Д"=>"%D0%94", "е"=>"%D0%B5", "Е"=>"%D0%95", "ё"=>"%D1%91", "Ё"=>"%D0%81", "ж"=>"%D0%B6", "Ж"=>"%D0%96", "з"=>"%D0%B7", "З"=>"%D0%97", "и"=>"%D0%B8", "И"=>"%D0%98", "й"=>"%D0%B9", "Й"=>"%D0%99", "к"=>"%D0%BA", "К"=>"%D0%9A", "л"=>"%D0%BB", "Л"=>"%D0%9B", "м"=>"%D0%BC", "М"=>"%D0%9C", "н"=>"%D0%BD", "Н"=>"%D0%9D", "о"=>"%D0%BE", "О"=>"%D0%9E", "п"=>"%D0%BF", "П"=>"%D0%9F", "р"=>"%D1%80", "Р"=>"%D0%A0", "с"=>"%D1%81", "С"=>"%D0%A1", "т"=>"%D1%82", "Т"=>"%D0%A2", "у"=>"%D1%83", "У"=>"%D0%A3", "ф"=>"%D1%84", "Ф"=>"%D0%A4", "х"=>"%D1%85", "Х"=>"%D0%A5", "ц"=>"%D1%86", "Ц"=>"%D0%A6", "ч"=>"%D1%87", "Ч"=>"%D0%A7", "ш"=>"%D1%88", "Ш"=>"%D0%A8", "щ"=>"%D1%89", "Щ"=>"%D0%A9", "ъ"=>"%D1%8A", "Ъ"=>"%D0%AA", "ы"=>"%D1%8B", "Ы"=>"%D0%AB", "ь"=>"%D1%8C", "Ь"=>"%D0%AC", "э"=>"%D1%8D", "Э"=>"%D0%AD", "ю"=>"%D1%8E", "Ю"=>"%D0%AE", "я"=>"%D1%8F", "Я"=>"%D0%AF")); 
		   return $s; 
		}
		
		
		
		
		public function rustranslit($string) {
		    $converter = array(
		        'а' => 'a',   'б' => 'b',   'в' => 'v',
		        'г' => 'g',   'д' => 'd',   'е' => 'e',
		        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
		        'и' => 'i',   'й' => 'y',   'к' => 'k',
		        'л' => 'l',   'м' => 'm',   'н' => 'n',
		        'о' => 'o',   'п' => 'p',   'р' => 'r',
		        'с' => 's',   'т' => 't',   'у' => 'u',
		        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
		        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
		        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
		        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
		        
		        'А' => 'A',   'Б' => 'B',   'В' => 'V',
		        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
		        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
		        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
		        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
		        'О' => 'O',   'П' => 'P',   'Р' => 'R',
		        'С' => 'S',   'Т' => 'T',   'У' => 'U',
		        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
		        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
		        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
		        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
		    );
		    return strtr($string, $converter);
		}
		
		//Транслитерация на латинский шрифт.
		public function translit($str) {
    $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
    $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
    		return str_replace($rus, $lat, $str);
  		} 
	  
	  
	  //Метод определения раскладки клавиатуры текста.
	  public function getKeyboardLayoutLat($str = null){
		  if($str != null){
			  if (preg_match("/^[a-z]+$/i", $str)) {
			  	  return 'Lat';
				
			  }
			  else{
				  return 'Not Lat';
			  }
		  }
	  }
	  
	  
	  
	  //Метод отределения наличия латинских букв в тексте.
	  public function determinationLatLetter($text = null){
		  if($text != null){
			  $arr = str_split($text);
    		  $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
			  if(count($arr) >= 1){
				  foreach($arr as $k => $v){
					  foreach($lat as $kk => $vv){
						  if($v == $vv){
						  	  unset($k, $v);
						  	  unset($kk, $vv);
							  return true;
						  }
					  }
					  unset($kk, $vv);
				  }
				  unset($k, $v);
			  }
		  }
	  }
	  
	  
	  
	  //Метод получения URL для модуля.
	  public function getUrl($nameModule = null){
		  if($nameModule != null){
			  $url  = $this->url;													//Получаем текущий URL.
			  $a = explode('index.php',$url);										//Получаем маршрут перед индексом.
			  $a = $a['0'].'index.php/'.$nameModule;
			  unset($url, $nameModule);
		  	  return $a;
		  }
	  } 
	  
	  
	  //Метод получения имени запускаемого модуля.
	  function getNameModule($url = null){
		  if($url != null){
			  $a = explode('index.php',$url);										//Получаем маршрут за индексом.
			  $a = $a['1'];
			  if($a != ''){															//Проверка на наличия команд.
				  $com = explode('/', $a);											//Получаем массив команд.
				  if($com['1'] != ''){												//Проверка на наличие команды.
					  if(router::validationCode($com['1'])){						//Проверка на враждебный код.
						  return $com['1'];											//Возвращаем имя модуля.
					  }
				  }
			  }
		  }
	  }
	  
	  
	  //Метод валидации на враждебный код.
	  public function validationCode($text = null){
		  if($text != null){
			  $text = strtolower($text);                      //Приравниваем текст параметра к нижнему регистру.
		      $check[] = 'select';                            //Создаем массив враждебного кода.            
		      $check[] = 'union';
		      $check[] = 'order';
		      $check[] = 'where';
		      $check[] = 'char';
		      $check[] = 'from';
		      $check[] = 'insert';
		      $check[] = 'delete';
		      $check[] = 'create';
		      $check[] = 'eval';
		      $check[] = 'router';
		      foreach($check as $key => $value){              //Обходим массив.
		            $var = explode($value, $text);            //Разчепляем водимые данные.
		            if(count($var) >= 2){                     //Если находми слова из массива, то сообщаем.
		            	print '<script> alert(\'Не пройдена валидация на враждебный код!\') </script>';
		                return false;
		            }
		      }
		      unset($key, $value, $text, $var);
		      return true;
		  }
		  unset($text);
		  return true;
	  }
	  
	  
	  
	  //Метод создания объекта.
	  public function createObj($nameObj = null){
		  if($nameObj != null){
			  if(router::validationFile('classes/', $nameObj.'.php')){				//Проверка на наличия файла.
			  	  include_once 'classes/'.$nameObj.'.php';							//Подключение файла.
			  	  $code = '$obj = new '.$nameObj.'();';								//Получаем исполняемый код.
				  eval($code);														//Выполняем код.
				  return $obj;														//Возвращаем объект.
			  }
		  }
	  }
	  
	  //Метод валидации файлового маршрута.
	  private function validationFile($url = null, $name = null){
		  if($url != null && $name != null){										//Если файл существует, то возвращаем true.
			  return file_exists($url.$name);
		  }
		  return false;
	  }
	  
	  
	  //Метод получения текущего пути для файлов.
	  public function getPatchFile($patch = null){
	  	  $p = $_SERVER['SCRIPT_FILENAME'];	  	  
	  	  $a = explode('index.php', $p);
	  	  $p = $a['0'];
		  if($patch != null){
			  $p .= $patch.'/';
		  }
		  $p = explode('/', $p);
		  $p = implode("\\", $p);
		  unset($a);
		  return $p;
	  }
	  
	  
	  //Метод получения списка файлов в каталоге.
	  public function getListFile($patch = null){
		  if($patch != null){													//Проверка наличия пути.
			  $arr = scandir($patch);
			  unset($arr['0'], $arr['1']);
			  return $arr;
		  }
	  }
	  
	  //Метод получения данных с файла.
	  public function getDataFile($name = null){
		  if($name != null){
			  $ret = '';
			  if(file_exists($name)){											//Проверка наличия файла.
				  $fp = fopen($name, "rt"); 									//Открываем файл в режиме чтения
				  if($fp){
				  	  while (!feof($fp)){
				  	  	  $mytext = fgets($fp, 999);
				  	  	  $ret .= $mytext."\n";
				  	  }
				  }
				  fclose($fp);													//Закрытие файла.
				  unset($mytext, $fp, $name);
				  return $ret;													//Возвращае результат.
			  }
		  }
	  }
	  
	  
	  
  }
  
?>
