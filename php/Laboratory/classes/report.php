<?php
  //Класс ответственный за ведени и чтения отчетов.
  include_once 'classes/room.php';
  include_once 'library/data.php';
  include_once 'library/datetimelib.php';
  include_once 'library/select.php';
  //include_once 'library/cmd.php';
    
  class report extends room{
  	  
  	  var $tools;															//Панель инструментов.
  	  var $heat;															//Заглавие задачи.
  	  var $footer;															//Фитер.
  	  var $content;
  	  var $date;															//Объект дата.
  	  var $datetime1ib;														//Объект DateTime.
  	  //var $cmd;																//Библиотека комманд.
  	  
	  
	  
	  //Конструктор класса.
	  function __construct($permission = false){
		  $this->date = new data('Europe/Kiev');														//Создаем объект дата.
		  //$this->cmd = new cmd();																		//Создаем объект команд модема.
		  $this->datetime1ib = new datetimelib();														//Создание объекта DateTime.
		  if($permission){																				//Проверка разрешения не выводить кабинет и не обрабатывать данные.
			  
		  }
		  else{
			  parent::__construct();																			//Выполнение конструктора предшественника.
			  report::action_report();			  																//Обрабатываем действия пользователя.	
			  print room::getView();																			//Отображаем кабинет.
		  }
		  
	  }
	  
	  
	  //Метод обработки действия пользователя.
	  private function action_report(){
	  	  $this->heat = 'Управление отчётами';
	  	  if(isset($_POST['activity_report'])){																		//Обработка выбора графика активности.
			  unset($_SESSION['report']['search_report']);
			  $_SESSION['report']['activity_report'] = true;
	  	  }
	  	  if(isset($_POST['search_report'])){
			  unset($_SESSION['report']['activity_report']);
			  $_SESSION['report']['search_report'] = true;
	  	  }
	  	  //Установка выбора по умолчанию.
	  	  if(!$_SESSION['report']['search_report'] && !$_SESSION['report']['activity_report']){
			  $_SESSION['report']['activity_report'] = true;
	  	  }
	  	  
	  	  if($_SESSION['report']['activity_report'] == true){
	  	  	  if(																								//Обработка выбора диапазон.
	  	  	  isset($_POST['date_from_report'])
	  	  	  && isset($_POST['date_to_report'])
	  	  	  && isset($_POST['time_from_report'])
	  	  	  && isset($_POST['time_to_report'])
	  	  	  ){
				  if($this->datetime1ib->ComparisonDatetime($_POST['date_from_report'].' '.$_POST['time_from_report'].':00', $_POST['date_to_report'].' '.$_POST['time_to_report'].':00')){
					  $_SESSION['report']['timetable']['datetimefrom'] = $_POST['date_from_report'].' '.$_POST['time_from_report'].':00';
					  $_SESSION['report']['timetable']['datetimeto'] = $_POST['date_to_report'].' '.$_POST['time_to_report'].':00';
				  }
				  else{
					  
				  }	  
	  	  	  }
	  	  	  $this->content .= '<b>График активности:</b>'.'<br/>';
	  	  	  $this->content .= report::getToolsTimetableActivity($_SESSION['report']['timetable']['datetimefrom'], $_SESSION['report']['timetable']['datetimeto']);
	  	  	  
	  	  	  if(																								//Проверка выбранного диапазона.
	  	  	  $_SESSION['report']['timetable']['datetimefrom'] != '' 
	  	  	  && $_SESSION['report']['timetable']['datetimeto'] != ''
	  	  	  ){
				  $this->content .= '<br/>'.report::getTimetableActivityC3JS($_SESSION['report']['timetable']['datetimefrom'], $_SESSION['report']['timetable']['datetimeto']);
	  	  	  }
	  	  }
	  	  if($_SESSION['report']['search_report']){																//Если выбрано поиск.
			  if(isset($_POST['reset_search'])){																//Обработка зброса параметров.
				  unset($_SESSION['report']['search']);
			  }
			  if($_POST['selected_sms'] != ''){																	//Обработка выбора СМС.
				  $selected_sms = $_POST['selected_sms'];
			  }
			  if(																								//Обработка выбора диапазон.
	  	  	  isset($_POST['date_from_report'])
	  	  	  && isset($_POST['date_to_report'])
	  	  	  && isset($_POST['time_from_report'])
	  	  	  && isset($_POST['time_to_report'])
	  	  	  ){
				  if($this->datetime1ib->ComparisonDatetime($_POST['date_from_report'].' '.$_POST['time_from_report'].':00', $_POST['date_to_report'].' '.$_POST['time_to_report'].':00')){
					  $_SESSION['report']['search']['datetimefrom'] = $_POST['date_from_report'].' '.$_POST['time_from_report'].':00';
					  $_SESSION['report']['search']['datetimeto'] = $_POST['date_to_report'].' '.$_POST['time_to_report'].':00';
				  }
				  else{
					  
				  }
	  	  	  }
	  	  	  if(isset($_POST['numer_report'])){																	//Обработка ввода номера для поиска.
				  if($_POST['numer_report'] == ''){
					  unset($_SESSION['report']['search']['numer_report']);
				  }
				  else{
					  $_SESSION['report']['search']['numer_report'] = $_POST['numer_report'];
				  }
	  	  	  }
	  	  	  if(isset($_POST['sms_report'])){																	//Обработка выбора СМС.
				  if($_POST['sms_report'] == ''){
					  unset($_SESSION['report']['search']['sms_report']);
				  }
				  else{
					  $_SESSION['report']['search']['sms_report'] = $_POST['sms_report'];
				  }
	  	  	  }
	  	  	  $reset = '<input type="submit" name="reset_search" value="Зброс параметров">';
	  	  	  $h[] = '<b>Поиск СМС:</b>';
	  	  	  $h[] = room::getForm($reset);
	  	  	  $this->content .= room::getElementsLine($h).'<br/>';
	  	  	  $tools[] = report::getToolsTimetableActivity($_SESSION['report']['search']['datetimefrom'], $_SESSION['report']['search']['datetimeto'], true);
	  	  	  $tools[] = report::getToolsSearchSMS($_SESSION['report']['search']['numer_report'], $_SESSION['report']['search']['sms_report']);
	  	  	  $arr_sms = report::getDateMenuSMS();
	  	  	  $tools[] = report::getMenuSMS($arr_sms);
	  	  	  $line[] = room::getElementsColumn($tools);														//Располагаем элементы в столбик.
	  	  	  $line[] = report::viewSMS($arr_sms, $selected_sms);
	  	  	  $this->content .= room::getElementsLine($line);													//Располагаем элементы в ряд.
	  	  }
	  	  
	  	  
	  	  
	  	  
	  	  
	  	  $this->tools = room::getForm(report::getTools());
		  room::outContent(report::getView());
	  }
	  
	  
	  //Метод отображения модуля.
	  public function getView(){
		  return room::getViewModule();
	  }
	  
	  
	  //Метод получения панели инструментов.
	  private function getTools(){
	  	  $ret .= '<input type="submit" name="search_report" value="Поиск">';
		  $ret .= '<input type="submit" name="activity_report" value="График активности">';
		  return $ret;
	  }
	  
	  
	  
	  //Метод получения графика отправки СМС по дате через плагин C3JS.
	  private function getTimetableActivityC3JS($datetimea = null, $datetimeb = null){
	  	  //if($datetimea != null && $datetimeb != null){																//Проверка наличия данных
			  $dd = report::PreparationDataPluginC3JS($datetimea, $datetimeb);											//Готовим данные.
			  if($dd != ''){
				  room::LoadLibrary('c3.css');																			//Подключаем библиотеки.
	  			  room::LoadLibrary('d3.v3.min.js');
	  			  room::LoadLibrary('c3.min.js');
	  			  $ret .= '<div id="chart" style="padding: 10px; background: #FFFFFF; width: auto;"></div>';
	  			  $ret .= '<script>
	  	  					var chart = c3.generate({
							    bindto: \'#chart\',
							    data: {
							      columns: [
							        '.$dd.'
							      ]
							    }
							});
								
		          </script>';
			  }
			  return $ret;
	  	  //}  
	  }
	  
	  
	  //Метод подготовки данных для плагина C3JS.
	  private function  PreparationDataPluginC3JS($datetimea = null, $datetimeb = null){
		  if($datetimea != null && $datetimeb != null){												//Проверка наличия данных.
			  $arr = report::getListCMCband($datetimea, $datetimeb);								//Получаем массив данных.
			  $sk = report::getDutyRatioC3JS($datetimea, $datetimeb);								//Получаем скажность.
			  $s = report::calculatingSecDatetime($datetimea, $datetimeb);							//Получаем общее количество секунд.
			  $d1 = $datetimea;																		//Начальная установка.
			  if(count($arr) >= 1){
				  foreach($arr as $key => $value){			  											//Обходим данные.
			  		  $arrkey[$value['name']] = true;			  										//Формируем массив ключей.
			  		  $rr1[$value['name']] = 0;
				  }
				  unset($key, $value); 
				  
				  for($i = 0; $i <= $s; $i += $sk){
			  		  $d2 = $d1;
			  		  $d2 = $this->datetime1ib->getAddDatatime($d2, $sk);								//Формируем вторую отмктку.
			  		  $rr = $rr1;
			  		  					
			  		  $ar = $this->datetime1ib->getArrFilter($arr, 'datetime', $d1, $d2);				//Получаем данные.
					  $n = count($ar);																	//Получаем количество значений.
					  if($n >= 1){																		//Проверка наличия данных.
						  foreach($ar as $key => $value){												//Обходим данные.
							  $rr[$value['name']] += 1;													//Формируем массив данных в разрезе соединений.
						  }
						  unset($key, $value);															//Сброс отработанных переменных.  
					  }
					  $ret[] = $rr;																	//Фиксация результата.
					  //unset($rr);																		//Сброс использованны данных.
					  $d1 = $d2;
				  }
				  unset($n, $arr, $sk, $d, $d1, $d2, $rr, $datetimea, $datetimeb);						//Сброс использованных переменных.
				  
				  $n = count($ret);																		//Определяем количество элементов.
				  if($n >= 1){																			//Проверка наличия данных.
					  //Формиркем массив выходных данных.
					  foreach($ret as $key => $value){													//Обходим данные.
						  if(is_array($value)){															//Проверка наличия данных.
						  $ee = $value;
							  foreach($ee as $k => $v){												//Обходим данные соединений.
								  if($rr[$k] == ''){													//Проверка на предмет первого прохода.
									  $rr[$k] = ''.$v;														//Присаиваем данные.
								  }
								  else{																	//Иначе повторяем добавление.
									  $rr[$k] = $rr[$k].', '.$v;
								  }
							  }
							  unset($k, $v);
						  }
					  }
					  unset($key, $value);
				  }
				  unset($n, $ret);																		//Штатный зброс переменных.
				  
				  $n = count($rr);																		//Определяем количество элементов.
				  if($n >= 1){																			//Проверка наличие данных.
					  //Формируем готовые данные.
					  foreach($rr as $k => $v){															//Обходим данные.
						  if($ret == ''){																//Определение первого прохода.
							  $ret .= '[\''.$k.'\', '.$v.']';
						  }
						  else{																			//При втором проходе.
							  $ret .= ',
	[\''.$k.'\', '.$v.']';
						  }
					  }
					  unset($k, $v);
				  }
				  return $ret;
			  } 
		  }
	  }
	  
	  
	  //Метод вычисления максимального и минимального значения времни в секндах.
	  private function calculatingMaxMin($arr = null){
		  if(count($arr) >= 1){
			  foreach($arr as $key => $value){														//Обходим массив.
				  $data[$this->datetime1ib->separation($value['datetime'], 'data')] = true;						//Получаем сассив дат.
				  $time[$this->datetime1ib->separation($value['datetime'])] = true;								//Получаем массив времен.
			  }
			  unset($key, $value);
			  
			  foreach($time as $t => $v){															//Обходим массив времени.
				  if($t >> $tma){																	//Находим максимальное значение времени.
					  $tma = $t;
				  }
				  if($t << $tmi){																	//Находим минимальное значение времени.
					  $tmi = $t;
				  }
			  }
			  unset($t, $v);
			  
			  $smax = $this->date->conversion_time_data($tma);												//Превращаем время в секунды.
			  $smin = $this->date->conversion_time_data($tmi);
			  $ret['smax'] = $smax;
			  $ret['smin'] = $smin;
			  return $ret;
		  }
	  }
	  
	  
	  //Метод получения разници дат в днях.
      function DifferenceDays($data1 = null, $data2 = null){
      	  if($this->date->comparison_date($data1, $data2)){
			  $data_arr1 = explode('-',$data1);
	          $y1 = $data_arr1[0];
	          $m1 = $data_arr1[1];
	          $d1 = $data_arr1[2];
	          
	          $data_arr2 = explode('-',$data2);
	          $y2 = $data_arr2[0];
	          $m2 = $data_arr2[1];
	          $d2 = $data_arr2[2];
	          
	          if($y1 <= $y2){
				  $ret = ($y2 - $y1) * 365;
	          }
	          if($m1 <= $m2){
          		  $ret += ($m2 - $m1) * 31;
	          }
	          if($d1 < $d2){
				  $ret += ($d2 - $d1);
	          }
	          
	          return $ret;
      	  }  
      }
	  
	  
	  
	  //Метод вычисления количества секунд в в разнице дат
	  private function calculatingSecDatetime($datetimea = null, $datetimeb = null){
		  if($datetimea != null && $datetimeb != null){													//Проверка необходимого.
			  if($this->datetime1ib->ComparisonDatetime($datetimea, $datetimeb)){									//Проверка коректности дат.
				  $datea = $this->datetime1ib->separation($datetimea, 'date');										//Получаем двту и время.
			   	  $timea = $this->datetime1ib->separation($datetimea);
			   	  $timesa = $this->date->conversion_time_data($timea);	
			   	  $dateb = $this->datetime1ib->separation($datetimeb, 'date');										//Получаем двту и время.
			   	  $timeb = $this->datetime1ib->separation($datetimeb);
			   	  $timesb = $this->date->conversion_time_data($timeb);	
			   	  if($this->date->comparison_date($datea, $dateb)){										//Проверка дат.
					  $d = report::DifferenceDays($datea, $dateb);
					  $s1 = 86400 - $timesa;															//Вычисляем секунды после отметки времени.
					  $ret = $s1 + (86400 * $d);														//Умножаем на количество дней.
					  $ret = $ret - $timesb;															//Вычитаем остаток.
			   	  }
			   	  else{
					  $ret = $timesb - $timesa;
			   	  }
			   	  return $ret;																			//Возвращаем результат.
			  }
		  }
	  }
	  
	  
	  //Метод получения скважности для плагина C3JS
	  private function getDutyRatioC3JS($datetimea = null, $datetimeb = null, $reverse = false){
		  if($this->datetime1ib->ComparisonDatetime($datetimea, $datetimeb)){
			  $sd = report::calculatingSecDatetime($datetimea, $datetimeb);							//Вычисляем диапазон в секундах.
			  if($sd > 2592000){			//больше 10 суток.
			  	  $s = 86400;
			  	  $d = '1 сутки.';
			  }
			  if($sd <= 2592000){		//10 сутки.
			  	  $s = 86400;
			  	  $d = '1 сутки.';
			  }
			  if($sd <= 864000){		//10 сутки.
			  	  $s = 14400;
			  	  $d = '4 часа.';
			  }
			  if($sd <= 432000){		//5 сутки.
			  	  $s = 7200;
			  	  $d = '2 часа.';
			  }
			  if($sd <= 172800){		//3 сутки.
			  	  $s = 3600;
			  	  $d = '1 час.';
			  }
			  if($sd <= 86400){			//1 сутки.
			  	  $s = 3600;
			  	  $d = '1 час.';
			  }
			  if($sd <= 43200){			//12 час.
			  	  $s = 1800;
			  	  $d = '30 минут.';
			  }
			  if($sd <= 36000){			//10 час.
			  	  $s = 1800;
			  	  $d = '30 минут.';
			  }
			  if($sd <= 28800){			//8 час.
			  	  $s = 1800;
			  	  $d = '30 минут.';
			  }
			  if($sd <= 21600){			//6 час.
			  	  $s = 1800;
			  	  $d = '30 минут.';
			  }
			  if($sd <= 14400){			//4 час.
			  	  $s = 1800;
			  	  $d = '30 минут.';
			  }
			  if($sd <= 7200){			//2 час.
			  	  $s = 1200;
			  	  $d = '20 минут.';
			  }
			  if($sd <= 3600){			//1 час.
			  	  $s = 300;
			  	  $d = '5 минут.';
			  }
			  if($sd <= 1800){			//30 минут.
			  	  $s = 60;
			  	  $d = '1 минута.';
			  }
			  if($sd <= 600){			//10 минут.
			  	  $s = 5;
			  	  $d = '5 секунд.';
			  }
			  if($sd <= 60){			//Минута.
			  	  $s = 2;				//5 секунд.
			  	  $d = '2 секунды.';
			  }
			  if($reverse){
				  return $d;
			  }
			  else{
				  return $s;
			  }																	//Возвращаем скважность.
		  }
	  }
	  

	  
	  
	  //Метод преобразованиея строки в ключ для массива и обратно.
	  public function TransformationArrayVar($var = null, $type = true){
		  if($type == true){
			  $ret = strtr($var, array(" "=>"_"));
		  }
		  else{
			  $ret = strtr($var, array("_"=>" "));
		  }
		  return $ret;
	  }
	  
	  
      //Метод получения времени для плагина
      public function getTimeForPlugin($time = null){
    	if($time != null){
			$arr = explode(':', $time);
			$valuePole = $arr['0'].':'.$arr['1'];
			return $valuePole;
    	}
      }
	  
	  
	  //Метод получения пенели управления графиком отправки по дате.
	  private function getToolsTimetableActivity($datetimefrom = null, $datetimeto = null, $step = false){
		  if($datetimefrom == null && $datetimeto == null){														//Установка параметров по умолчанию.
			  $datato = $this->date->data_i;																			//Текущая дата.
			  $datafrom = date('Y-m-d', strtotime($datato.' -1days'));											//Вчерашняя дата.
			  
			  $timeto = '00:00';																				//Начальное время.
			  $timefrom = $timeto;
		  }
		  else{																									//Иначе разделяем данные.
			  $datafrom = $this->datetime1ib->separation($datetimefrom, 'date');
			  $datato = $this->datetime1ib->separation($datetimeto, 'date');
			  $timefrom = report::getTimeForPlugin($this->datetime1ib->separation($datetimefrom));
			  $timeto = report::getTimeForPlugin($this->datetime1ib->separation($datetimeto));
		  }
		  $from .= 'С - '.report::getPluginDate('id_date_from_repor', 'date_from_report', $datafrom).'  ';
		  $poletimefrom = '<input type="text" name="time_from_report" value="'.$timefrom.'">';
		  $from .= report::getPluginTime('id_time_from', $poletimefrom);
		  if(!$step){																							//При наличии флага...
			  $from .= '   Шаг графика - '.report::getDutyRatioC3JS($datetimefrom, $datetimeto, true);			//Поуазываем флаг.
		  }
		  
		  $to .= 'По - '.report::getPluginDate('id_date_to_repor', 'date_to_report', $datato).'  ';
		  $poletimeto = '<input type="text" name="time_to_report" value="'.$timeto.'">';
		  $to .= report::getPluginTime('id_time_to', $poletimeto);
		  if(!$step){
			  $to .= '   Количество СМС за период - '.count(report::getListCMCband($datetimefrom, $datetimeto));
		  }
		  $arr['0'] = $from;
		  $arr['1'] = $to;
		  $arr['2'] = '<input type="submit" value="Выбор">';
		  $ret .= room::getElementsColumn($arr);
		  return room::getForm($ret);
	  }
	  
	  
	  //Метод получения панели поиска СМС.
	  private function getToolsSearchSMS($numer = null, $sms = null){
		  $from .= 'Номер телефона - '.'<input type="text" name="numer_report" size="20" value="'.$numer.'"><br/>';
		  $from .= 'Текст СМС - '.'<input type="text" name="sms_report" size="25" value="'.$sms.'"><br/>';
		  $from .= '<input type="submit" value="Поиск">';
		  return room::getForm($from);
	  }
	  
	  
	  //Метод сравнения строк.
	  public function comparisonStringLike($s1 = null, $s2 = null){
		  if($s1 != null && $s2 != null){
			  $arr = explode($s2, $s1);
			  if(count($arr) >= 2){
				  return true;
			  }
			  else{
				  return false;
			  }
		  }
	  }
	  
	  
	  //Метод получения данных для меню СМС.
	  private function getDateMenuSMS(){
		  if(																									//Проверка наличия данных выбранного времени.
		  $_SESSION['report']['search']['datetimefrom'] != ''
		  && $_SESSION['report']['search']['datetimeto'] != ''
		  ){
			  $_SESSION['report']['search']['print'] = 'С - '.$_SESSION['report']['search']['datetimefrom'].' по - '.$_SESSION['report']['search']['datetimeto'];
			  //Получаем данные за прериод.
			  $arr = report::getListCMCband($_SESSION['report']['search']['datetimefrom'], $_SESSION['report']['search']['datetimeto']);
			  if(																								//Если есть введенныые данные СМС.
			  $_SESSION['report']['search']['numer_report'] != ''
			  || $_SESSION['report']['search']['sms_report'] != ''
			  ){
				  if(count($arr) >= 1){
				  	  $_SESSION['report']['search']['print'] .= '<br/>'.'Номер телефона - "'.$_SESSION['report']['search']['numer_report'].'"<br/>';
				  	  $_SESSION['report']['search']['print'] .= 'Слова из СМС - "'.$_SESSION['report']['search']['sms_report'].'"<br/>';
					  foreach($arr as $k => $v){																//Обходим данные.
						  $n = cmd::getFulMobFone($v['to']);													//Получаем полный телефонный номер.
						  if($_SESSION['report']['search']['numer_report'] == '' && $_SESSION['report']['search']['sms_report'] != ''){
							  if(report::comparisonStringLike('@'.$v['smc'].'@', $_SESSION['report']['search']['sms_report'])){
								  $ret[] = $v;
							  }
						  }
						  if($_SESSION['report']['search']['numer_report'] != '' && $_SESSION['report']['search']['sms_report'] == ''){
							  if(report::comparisonStringLike('@'.$n.'@', $_SESSION['report']['search']['numer_report'])){
								  $ret[] = $v;
							  }
						  }
						  if($_SESSION['report']['search']['numer_report'] != '' && $_SESSION['report']['search']['sms_report'] != ''){
							  if(
							  report::comparisonStringLike('@'.$n.'@', $_SESSION['report']['search']['numer_report'])
							  && report::comparisonStringLike('@'.$v['smc'].'@', $_SESSION['report']['search']['sms_report'])
							  ){
								  $_SESSION['report']['search']['print'] .= '<br/>'.'Номер телефона - "'.$_SESSION['report']['search']['numer_report'].'"<br/>';
				  	  			  $_SESSION['report']['search']['print'] .= 'Слова из СМС - "'.$_SESSION['report']['search']['sms_report'].'"';
								  $ret[] = $v;
							  }
						  }
					  }
					  unset($k, $v, $n);
					  return $ret;
				  }
			  }
			  else{
				  return $arr;
			  }
		  }
		  else{
		  	  $_SESSION['report']['search']['print'] = '<br/>'.'Номер телефона - "'.$_SESSION['report']['search']['numer_report'].'"<br/>';
			  $_SESSION['report']['search']['print'] .= 'Слова из СМС - "'.$_SESSION['report']['search']['sms_report'].'"';
			  if(
			  $_SESSION['report']['search']['numer_report'] != ''
			  || $_SESSION['report']['search']['sms_report'] != ''
			  ){
				  if($_SESSION['report']['search']['numer_report'] != ''){
					  $param['to'] = $_SESSION['report']['search']['numer_report'];
				  }
				  if($_SESSION['report']['search']['sms_report'] != ''){
					  $param['smc'] = $_SESSION['report']['search']['sms_report'];
				  }
				  return report::getListSMS($param);
			  }
			  else{
				  
			  }
		  }
	  }
	  
	  
	  //Метод получения меню СМС.
	  private function getMenuSMS($arr = null){
		  if(count($arr) >= 1){
		  	  $select = new select();
			  $ret = 'Выбор СМС: '.$select('selected_sms', 'datetime', $arr, 4);
			  unset($arr, $select);
			  return room::getForm($ret);
		  }
	  }
	  
	  //Метод просмотра СМС.
	  private function viewSMS($arr_sms = null, $datetime = null){
		  if($arr_sms != null || $datetime != null){											//Проверка необходимого.
		  	  if($datetime != null){															//Проверка наличия данных СМС.
				  $arr_sms = report::getListCMC('datetime', $datetime);							//Формируем новые.
		  	  }
		  	  if(count($arr_sms) >= 1){															//Проверка наличия данных													
		  	  	  $print .= '<div style="font-size: 130%"><b>Отчет по отправленным СМС</b></div><br/>';
		  	  	  $print .= '<b>'.$_SESSION['report']['search']['print'].'</b><br/><br/>';
		  	  	  foreach($arr_sms as $k => $v){												//Обходим данные.
				  	  $value .= $v['datetime']."\r\n";											//Формируем данные для просмотра.
					  $value .= $v['to']."\r\n";
					  $value .= $v['smc']."\r\n";
					  $value .= '______________________________'."\r\n";
					  
					  $print .= $v['datetime'].'<br/>';											//Формируем данные для печати.
					  $print .= $v['to'].'<br/>';
					  $print .= $v['smc'].'<br/>';
					  $print .= '<br/>';
				  }
				  $k++;
				  $print .= '<b>Количество СМС: '.$k.' шт.</b><br/>';
				  unset($k, $v);				  
		  	  }
		  	  $ret .= '<div style="display: none;">'.room::getDivPrint($print, 'id_print_SMS').'</div>';				//Готовим блок для печати.
			  $ret .= room::getButtonPrint('id_print_SMS').'<br/>';														//Показываем кнопку для печати.
			  $ret .= '<textarea style="font-size: 70%" rows="19" cols="30" readonly>'.$value.'</textarea>';
			  unset($sms, $value, $print);
			  return $ret;
		  }
	  }
	  
	  
    //Метод получения плагина Date к полю по ID
    public function getPluginDate($idPole = null, $pole = null, $value = null){
		if($idPole != null && $pole != null){													//Проверка необходимого.
			//Плагины для работы с базой.
			room::LoadLibrary('jquery-2.1.3.min.js');
			room::LoadLibrary('jquery.datetimepicker.full.min.js');
			room::LoadLibrary('jquery.datetimepicker.min.css');		
			$ret = '<input type="text" value="'.$value.'" name="'.$pole.'" id="'.$idPole.'" style="cursor: inherit; text-align: center; width: 100px;" />'."\n";			
			$ll = 'ru';
						
			$ret .= "<script>
						\$.datetimepicker.setLocale('".$ll."');
		                \$('#".$idPole."').datetimepicker({
		                    format:'Y-m-d',
		                    timepicker:false
		                });
        			</script>";
			
			
			return $ret;
		}
	}	  
	  
	  
    //Метод получения плагина Time к полю по ID
    public function getPluginTime($idPole = null, $pole = null){
		if($idPole != null && $pole != null){
			//Загружаем библиотеки и плагины.
			room::LoadLibrary('jquery-2.1.3.min.js');
			room::LoadLibrary('jquery-clockpicker.min.js');
			room::LoadLibrary('clockpicker.css');
			room::LoadLibrary('jquery-clockpicker.min.css');
			
			return '<div class="input-group clockpicker" style=" display: inline-block;" id="'.$idPole.'" data-placement="left" data-align="top" data-autoclose="true">
					    '.$pole.'
					    <span class="input-group-addon">
					        <span class="glyphicon glyphicon-time"></span>
					    </span>
					</div>
					<script type="text/javascript">
						$(\'#'.$idPole.'\').clockpicker();
					</script>';
		}
    }
	  
	  
	  
	  //Метод получения списка отправленных СМС по диапазону.
	  private function getListCMCband($datetimeform = null, $datetimeto = null){
		  if($datetimeform != null && $datetimeto != null){
			   $query = 'SELECT
						journal.`to`,
						journal.smc,
						journal.`datetime`,
						conect.`name`
						FROM
						journal
						Inner Join conect ON conect.id = journal.id_connection
						WHERE datetime BETWEEN STR_TO_DATE(\''.$datetimeform.'\', \'%Y-%m-%d %H:%i:%s\') AND STR_TO_DATE(\''.$datetimeto.'\', \'%Y-%m-%d %H:%i:%s\')';
		  	   $ret = SqlAdapter::select_sql($query);
			   return $ret;
		  }
	  }
	  
	  
	  
	  //Метод получения списка СМС по параметрам.
	  private function getListCMC($param = null, $value = null, $flag = false){
		  if($param != null && $value != null){
			  if($flag){
				  $query = 'SELECT
		  					*
							FROM
							journal
							WHERE
							`'.$param.'` LIKE \'%'.$value.'%\'';
			  }
			  else{
				  $query = 'SELECT
		  					*
							FROM
							journal
							WHERE
							`'.$param.'` = \''.$value.'\'';
			  }
			  $ret = SqlAdapter::select_sql($query);
			  return $ret;
		  }
	  }
	  
	  //Метод получения списка СМС по массиву параметров.
	  private function getListSMS($param = null){
		  $n = count($param);																							//Определяем количество параметров.
		  if($n >= 1){																									//Проверка наличия рараметров.
			   $s = 1;																									//Установка счетчика.
			   foreach($param as $k => $v){																				//Обходим рараметры.
				   if($s >= $n){																						//Проверка конца параметров.
					   $q .=  '`'.$k.'` LIKE \'%'.$v.'%\'';
				   }
				   else{
					   $q .=  '`'.$k.'` LIKE \'%'.$v.'%\' AND ';
				   }
				   $s++;																								//Инкремент счетчика.
				   
			   }
			   unset($k, $v, $s, $n);																					//Зброс переменных.
			   $query = 'SELECT
		  					*
							FROM
							journal
							WHERE
							'.$q;
			   unset($q);
			   $ret = SqlAdapter::select_sql($query);
			  return $ret;
		  }
	  }
	  
	  
	  
	  
	  
  }
  
?>
