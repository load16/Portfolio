<?php
  //Библиотека.
  //Абстактный класс отвественный за организацию календаря меню URL для СМС Joomla 2.5.
  defined('_JEXEC') or die;
  require_once (JPATH_COMPONENT.DS.'libraries'.DS.'data.php');
  jimport('joomla.html.html');
  
  
  abstract class calendarURL{
  	  
  	  //Метод получения календаря в виде плагина.
  	  public function getPlaginCalendarUrlTabs($url = null, $date = null){
		  if($url != null){
			  //Подключаем библиотеку.
			  JHTML::script('jquery-3.1.1.min.js', 'components/com_medical_registry/assets/scripts/');
			  $config = & JFactory::getConfig();
			  $zone = $config->getValue('offset');
			  $d = new data($zone);
			  
			  $id = 'calendar'.rand(1, 15).rand(1, 15);																		//Формируем ИД.
			  $r[] = '<div>'.JTEXT::_('REG_SELECT_DATE').'</div>';
			  $r[] = '<div style="border: 1px solid; height: 20px; border-radius: 5px; padding:3px;">'.$date.'</div>';
			  $head = '<div class="inputbox" style="width:auto; padding:1px; border: 1px solid; text-align: center;">'.JTEXT::_('REG_TODAY').' - '.calendarURL::getUrlforMenu($url, "date", $d->data_i, $d->data_i, JTEXT::_('REG_CURRENT_DATE')." - ".$d->data_i).'</div>';
			  $ret = calendarURL::getCalendarUrlTabs($url, $date);															//Получаем календарь ссылок.
			  //Помещае календарь в невидимый блок.
			  $r[] = '<div id="'.$id.'" style="display: none; z-index: 1; position: absolute; background: #F0F0F0; box-shadow: 0 0 30px rgba(0,0,0,0.9); ">'.$head.$ret.'</div>';
			  $ret = '<div title="'.JTEXT::_('REG_SELECT_DATE').'" id="'.$id.'view" onclick="$(\'#'.$id.'\').css(\'display\', \'block\');">'.calendarURL::getElementsColumn($r).'</div>';
			  //Формируем скрипт закрытия при нажатии внеблока.
			  $ret .= '<script type="text/javascript">
						$(document).mouseup(function (e){
						    var container = $("#'.$id.'");
						    if (container.has(e.target).length === 0){
						        container.hide();
						    }
						});
			  </script>';
			  return $ret;
		  }
  	  }
	  
	  
	  //Метод вывод календаря со вкладками.
	  public function getCalendarUrlTabs($url = null, $date = null){
		  if($url != null){
			  if($date == null){																							//Если даты нет, то формируем из текущей.
				  $config = & JFactory::getConfig();
				  $zone = $config->getValue('offset');
				  $d = new data($zone);
				  $date = $d->data_i;
			  }
			  $arrDate = explode('-', $date);																				//Разделяем дату.
			  $arrYear =  calendarURL::getArrYear($arrDate['0']);															//Получаем массив лет.
			  foreach($arrYear as $k_y => $v_y){																			//Обходим года.
				  $arrMonth = calendarURL::getArrMonth($v_y['value']);
				  foreach($arrMonth as $k_m => $v_m){																		//Обходим месеца.
					  $arrDay = calendarURL::getArrDay($v_y['value'], $v_m['value']);										//Получаем массив дней.
					  $tabsDay[$v_m['out']] = calendarURL::getMenuTabsUrl($url, $arrDay, $v_y['value'], $v_m['value']);		//Получаем данные для вкладок месяца.
				  }
				  unset($k_m, $v_m);
				  $tabsYear[$v_y['out']] = calendarURL::getTabsVerical($tabsDay);											//Получаем данны для вкладки года.
			  }
			  unset($k_y, $v_y);
			  $ret =  calendarURL::getTabsVerical($tabsYear);
			  return $ret;
		  }
	  }
	  
	  
	  //Метод вывода на экран календаря.
	  public function getCalendarURL($url = null, $Year = null, $Month = null, $Day = null){
		  $arrYear = calendarURL::getArrYear($Year);																		//Получаем все массивы данных.
		  $arrMonth = calendarURL::getArrMonth($Year, $Month);
		  $arrDay = calendarURL::getArrDay($Year, $Month, $Day);
		  
		  $config = & JFactory::getConfig();
		  $zone = $config->getValue('offset');
		  $d = new data($zone);
		  
		  if($Year != null){
		  	  if($Month == ''){
				  $Month = '01'.'';
		  	  }
		  	  if($Day == ''){
				  $Day = '01'.'';
		  	  }
			  $date = $d->correction_data($Year.'-'.$Month.'-'.$Day);
		  }
		  else{
			  $date = $d->data_i;
		  }
		  //Готовим данные для URL текущей даты.
		  $dt = $d->data;
		  $arr_data = explode('-', $dt);
		  $arrtD['y'] = $arr_data['0'];
		  $arrtD['m'] = $arr_data['1'] + 1 - 1;
		  
		  //Готовим параметры для формирования ссылок.
		  $arr_data = explode('-', $date);
		  $arrM['y'] = $arr_data['0'];
		  $arrD['y'] = $arr_data['0'] + 1 - 1;
		  $arrD['m'] = $arr_data['1'] + 1 - 1;
		  
		  $head = '<div class="cat-list-row1" style="width:auto; padding:1px; border: 1px solid; text-align: center;">'.JText::_('REG_SELECTED_DATE').' - '.$d->correction_data($date).JTEXT::_('REG_TODAY').' - '.calendarURL::getUrlforMenu($url, "d", $arr_data["2"] + 1 - 1, $d->correction_data($dt), JTEXT::_('REG_SELECT_THE_CURRENT_DATE')." ".$d->correction_data($dt), $arrtD).'</div>';
		  $yy = '<div class="cat-list-row1" style="vertical-align: top; height:80px; width:40px; padding:1px; border: 1px solid; display: inline-block;" >'.calendarURL::getMenuUrl($url, $arrYear).'</div>';
		  $mm = '<div class="cat-list-row1" style="vertical-align: top; height:80px; width:90px; padding:1px; border: 1px solid; display: inline-block; overflow-x: hidden; overflow-y: auto;">'.calendarURL::getMenuUrl($url, $arrMonth, $arrM).'</div>';
		  $dd = '<div class="cat-list-row1" style="vertical-align: top; height:80px; width:270px; padding:1px; border: 1px solid; display: inline-block;">'.calendarURL::getMenuUrl($url, $arrDay, $arrD).'</div>';
		  return '<div style="width:auto; font-size: 95%; height:auto; vertical-align: top;">'.$head.$yy.$mm.$dd.'</div>';
	  }
	  
	  
	  //Метод получения ссылки для меню.
	  public function getUrlforMenu($url = null, $name = null, $value = null, $out = null, $title = null, $arr_param = null){
		  if($url != null && $name != null && $value != null && $out != null){
		  	  $url = calendarURL::getBaseUrl($url, $name, $value, $arr_param);
		  	  if($name == 'd' && $arr_param['m'] != '' && $arr_param['y'] != ''){					//Проверка на предмет дня.
				  $date = $arr_param['y'].'-'.$arr_param['m'].'-'.$value;
				  $config = & JFactory::getConfig();
				  $zone = $config->getValue('offset');
				  $d = new data($zone);
				  $date = $d->correction_data($date);
				  $w = date("w", strtotime($date));													//Определяем выхлдные дни.
				  if($w == 0 || $w == 6){															//Если выходной, то подсвечиваем.
					  $ret = '<div class="cat-list-row0" style="display: inline-block; padding:3px;">'.JHTML::_('link', $url, $out, array('title'=>$title,'class'=>'list-title')).'</div>';
				  }
				  else{																				//Иначе обычный вывод.
					  $ret = '<div class="" style="display: inline-block; padding:3px;">'.JHTML::_('link', $url, $out, array('title'=>$title,'class'=>'list-title')).'</div>';
				  }  
		  	  }
		  	  else{
				  $ret = '<div class="" style="display: inline-block; padding:3px;">'.JHTML::_('link', $url, $out, array('title'=>$title,'class'=>'list-title')).'</div>';
		  	  }
			  
			  return $ret;
		  }
	  }
	  
	  
	  //Метод получения меню ссылок для вкладок.
	  private function getMenuTabsUrl($url = null, $arr = null, $Year = null, $Month = null){
		  if($url != null && count($arr) >= 1 && $Year != null && $Month != null){
			  $config = & JFactory::getConfig();
			  $zone = $config->getValue('offset');
			  $d = new data($zone);
			  
			  $hn = 1;																				//Начальная установка.
			  $ht = 1;
			  //Формируем верхушку над таблицей.
			  $hh = '<div style="font-weight:bold; text-align:center; border: 1px solid;" class="inputbox">'.calendarURL::getMonth($Month).' '.$Year.'</div>';				
			  
			  $table = '<table>';
			  foreach($arr as $key => $value){
			  	  $date = $Year.'-'.$Month.'-'.$value['value'];										//Формируем дату.
				  $date = $d->correction_data($date);												//Исправляем дату.
				  $w = date("w", strtotime($date));													//Получаем день недели.
			  	  if($hn <= 14){																	//Первой строке формируем заглавие.
					  $hn++;																		//Инкремент счетчика.
			  	  	  
			  	  	  if($w == 0 || $w == 6){														//Выходные дны подкрашиваем.
						  $head .= '<th class="cat-list-row0" bgcolor="#FFDDDD">';
			  	  	  }
			  	  	  else{
						  $head .= '<th>';
			  	  	  }
			  	  	  $head .= calendarURL::getDayWeek($w);											//Формируем день дедели.
					  $head .= '</th>';
			  	  }
			  	  if($ht <= 13){																	//Проверка не кончиллась ли строка.
			  	  	  if($ht == 1){																	//Проверка начало строки.
						  $tt .= '<tr class="cat-list-row1">';
			  	  	  }
			  	  	  if($w == 0 || $w == 6){														//Выходные дни подкрашиваем.
						  $tt .= '<td class="cat-list-row0" bgcolor="#FFDDDD">'.calendarURL::getUrlforMenu($url, 'date', $date, $value['out'], $value['title']).'</td>';
			  	  	  }
			  	  	  else{																			//Обычные дны.
						  $tt .= '<td class="cat-list-row1">'.calendarURL::getUrlforMenu($url, 'date', $date, $value['out'], $value['title']).'</td>';
			  	  	  }
					  $ht++;
			  	  }	  																				//Если кончилась, то переводим счетчик.
			  	  else{
			  	  	  if($w == 0 || $w == 6){														//Выходные дни подкрашиваем.
						  $tt .= '<td class="cat-list-row0" bgcolor="#FFDDDD">'.calendarURL::getUrlforMenu($url, 'date', $date, $value['out'], $value['title']).'</td>';
			  	  	  }
			  	  	  else{																			//Обычные дни.
						  $tt .= '<td class="cat-list-row1">'.calendarURL::getUrlforMenu($url, 'date', $date, $value['out'], $value['title']).'</td>';
			  	  	  }
			  	  	  $tt .= '</tr>';
					  $ht = 1;																		//Установка счетчика.
			  	  }
			  	  
				  //Формируем URL, и ставим в ячейку таблицы.		  	  
				  $ret .= calendarURL::getUrlforMenu($url, 'date', $date, $value['out'], $value['title']);
			  }
			  unset($key ,$value);
			  if($ht != 1){																			//Проверка наличия конца строки.
				  $tt .= '</tr>';
			  }
			  $table .= '<tr class="inputbox" style="border: 1px solid;">'.$head.'</tr>';			//Вставляем шапку таблици.
			  $table .= $tt;																		//Вставляем основу таблици.
			  $table .= '</table>';
			  return '<div style="border: 1px solid;">'.$hh.$table.'</div>';						//Возвращаем таблицу.
		  }
	  }
	  
	  
	  
	  //Метод получения меню ссылок
	  private function getMenuUrl($url = null, $arr = null, $arr_param = null){
		  if(count($arr) >= 1 && $url != null){
			  foreach($arr as $key => $value){
				  $ret .= calendarURL::getUrlforMenu($url, $value['name'], $value['value'], $value['out'], $value['title'], $arr_param);
			  }
			  unset($key, $value);
			  return $ret;
		  }
	  }
	  
	  
	  //Метод получения URL.
	  public function getBaseUrl($urlBase = null, $name = null, $value = null, $arr_param = null){
		  if($urlBase != null && $name != null && $value != null){
		  	  $urlBase = calendarURL::getUrlIntermediate($urlBase, $arr_param);						//Обрабатываем параметры.
		  	  $farr = explode('?', $urlBase);														//Разделяем URL.
		  	  if($farr['1'] != ''){																	//Если есть символ, то ведем обработку.
		  	  	  $ret = JRoute::_($urlBase.'&'.$name.'='.$value.'');
		  	  }
		  	  else{																					//Иначе, ставим его.
		  	  	  $ret = JRoute::_($urlBase.'?'.$name.'='.$value.'');
		  	  }
			  return $ret;																			//Возвращаем результат.
		  }
	  }
	  
	  
	  //Метод получения промежуточного URL
	  public function getUrlIntermediate($urlBase = null, $arrParam = null){
		  if($urlBase != '' && count($arrParam) >= 1){												//Проверяем необходимое.
			  foreach($arrParam as $k => $v){														//Обходим параметры.
			  	  $urlBase = calendarURL::getBaseUrl($urlBase, $k, $v);								//Формируем URL.
			  }
			  unset($k, $v);
		  }
		  return $urlBase;																			//Возвращаем результат.
	  }
	  
	  
	  //Метод получения массива лет.
	  public function getArrYear($year = null){
	  	  $config = & JFactory::getConfig();
		  $zone = $config->getValue('offset');
		  $d = new data($zone);
		  if($year == null){
		  	  $year = $d->year;
		  }
		  $arr['name'] = 'y';
		  
		  $arr['value'] = $year - 1;
		  $arr['out'] = $arr['value'];
		  $arr['title'] = JTEXT::_('REG_YEAR').' - '.$arr['value'];
		  $arrYear[] = $arr;
		  
		  $arr['value'] = $year;
		  $arr['out'] = $arr['value'];
		  $arr['title'] = JTEXT::_('REG_YEAR').' - '.$arr['value'];
		  $arrYear[] = $arr;
		  
		  $arr['value'] = $year + 1;
		  $arr['out'] = $arr['value'];
		  $arr['title'] = JTEXT::_('REG_YEAR').' - '.$arr['value'];
		  $arrYear[] = $arr;
		  
		  unset($d);
		  return $arrYear;
	  }
	  
	  
	  //Метод получения массива месяцев.
	  public function getArrMonth($Year = null, $month = null){
		  $config = & JFactory::getConfig();
		  $zone = $config->getValue('offset');
		  $d = new data($zone);
		   if($Year == null){																		//Если нет года, то формируем из текущего.
			  $date = $d->data_i;
			  $r_d = explode('-', $date);
			  $Year = $r_d['0'];
		  }
		  /*
		  $dd['value'] = $month;
		  $dd['name'] = 'm';
		  $dd['out'] =  calendarURL::getMonth($month);
		  $dd['title'] = 'Год -'.$Year.', месяц '.mb_strtolower(calendarURL::getMonth($month));
		  if($month == null){
		  	  $month = $d->mon;
		  	  $arrMonth[] = $dd;
		  }
		 
		  $arrMonth[] = $month;
		  */
		  for($i = 1; $i <= 12; $i++){
		  	  $dd['value'] = $i;
			  $dd['name'] = 'm';
			  $dd['out'] =  calendarURL::getMonth($i);
			  $dd['title'] = JTEXT::_('REG_YEAR').' - '.$Year.'  '.mb_strtolower(JTEXT::_('REG_MONTH')).' - '.mb_strtolower(calendarURL::getMonth($i));
			  /*
			  if($month != null){
				  if($month != $i){
					  $arrMonth[] = $dd;
				  }
			  }
			  else{
				  $arrMonth[] = $dd;
			  }
			  */
			  $arrMonth[] = $dd;
		  }
		  unset($d);
		  return $arrMonth;
	  
	  
	  }
	  
	   
	  //Метод получения массива дней.
	  public function getArrDay($year = null, $month = null, $day = null){
		  $config = & JFactory::getConfig();
		  $zone = $config->getValue('offset');
		  $d = new data($zone);																	//Получаем объект дата.
		  if($year == null || $month == null){													//Если нет данных, то формируем дату с текущей.
			  $date = $d->data;
		  }
		  else{																					//Если неть , то формируем.
			  $date = $year.'-'.$month.'-'.$day;
		  }
		  $dd = explode('-', $date);															//Разделяем дату.
		  $year = $dd['0'];
		  $month = $dd['1'];
		  $day = $dd['2'];   
		  $date = $d->correction_data($year.'-'.$month.'-'.$day);								//Коректируем дату.
		  return calendarURL::getNumberDeys($date);												//Возвращаем массив дней.
	  }
	  
	  //Метод определения количества дней в дате месяца.
	  public function getNumberDeys($date = null){
		  if($date != null){
			  $config = & JFactory::getConfig();
			  $zone = $config->getValue('offset');												//Получаем зону для даты.
			  $d = new data($zone);																//Получаем объект дата.
			  $dd = explode('-', $date);														//Получаем текущею дату.
			  $dn = $dd['0'].'-'.$dd['1'].'-'.'01';												//Формируем первое число месяца.
			  $vv = $dd['0'].'-'.$dd['1'];														//Формируем год и месяц для сравнения.
			  $ddd['name'] = 'd';
			  for($i = 1; $i <= 40; $i++){														//Обходим все дни месяца.
				  $ddd['value'] = $i;															//Готовим данные.
				  $ddd['out'] = calendarURL::correctionDay($i);
				  $ddd['title'] = JText::_('REG_SELECTED_DATE').' - '.$d->correction_data($vv.'-'.$i);
				  $ret[] = $ddd;																//Формируем массив дней.
				  $dn = $d->getСalculationDate($dn, '+', 1);									//Увеличиваем дату на день
				  $dd = explode('-', $dn);														//Разделяем даду.
				  $vvv = $dd['0'].'-'.$dd['1'];													//Формируем год и месяц для сравнение.
				  $ii = $dd['2'];
				  if($vvv != $vv){																//Если месяц изменился, то выходи из цикла.
					  break;
				  }  
			  }
			  return $ret;
		  }
	  }
	  
	  
	  //Метод получения дня по номеру
	  public function getDayWeek($n = null){
	  	  if($n == 1){
			  return JTEXT::_('REG_MO');
	  	  }
	  	  if($n == 2){
			  return JTEXT::_('REG_TU');
	  	  }
	  	  if($n == 3){
			  return JTEXT::_('REG_WE');
	  	  }
	  	  if($n == 4){
			  return JTEXT::_('REG_TH');
	  	  }
	  	  if($n == 5){
			  return JTEXT::_('REG_FR');
	  	  }
	  	  if($n == 6){
			  return JTEXT::_('REG_SA');
	  	  }
	  	  if($n == 0){
			  return JTEXT::_('REG_SU');
	  	  }
	  }
	  
	  
	  //Метод получения месяца по номеру.
	  public function getMonth($n = null){
		  if($n != ''){
			  if($n == '1' || $n == '01'){
				  return JTEXT::_('REG_JANUARY');
			  }
			  if($n == '2' || $n == '02'){
				  return JTEXT::_('REG_FEBRUARY');
			  }
			  if($n == '3' || $n == '03'){
				  return JTEXT::_('REG_MARCH');
			  }
			  if($n == '4' || $n == '04'){
				  return JTEXT::_('REG_APRIL');
			  }
			  if($n == '5' || $n == '05'){
				  return JTEXT::_('REG_MAY');
			  }
			  if($n == '6' || $n == '06'){
				  return JTEXT::_('REG_JUNE');
			  }
			  if($n == '7' || $n == '07'){
				  return JTEXT::_('REG_JULY');
			  }
			  if($n == '8' || $n == '08'){
				  return JTEXT::_('REG_AUGUST');
			  }
			  if($n == '9' || $n == '09'){
				  return JTEXT::_('REG_SEPTEMBER');
			  }
			  if($n == '10'){
				  return JTEXT::_('REG_OCTOBER');
			  }
			  if($n == '11'){
				  return JTEXT::_('REG_NOVEMBER');
			  }
			  if($n == '12'){
				  return JTEXT::_('REG_DECEMBER');
			  }
		  }
	  }
	  
	  //Метод исправления дня.
	  private function correctionDay($day = null){
		  if($day != null){
			  if($day <= 9){
				  return '0'.$day;
			  }
			  else{
				  return $day;
			  }
		  }
	  }
	  
	  
    //Метод постройки элементов в рад.
    private function getElementsLine($arr = null){
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
            return $ret;
        }
    }
    
    //Метод постройки элементов в столбик.
    private function getElementsColumn($arr = null){
        if(count($arr) >= 1){
            foreach($arr as $key => $value){
                if($value != ''){
                    $ret .= "\t".'<div class="" style="display: block; vertical-align: top;">'."\n";
                    $ret .= "\t"."\t".'<div class="cat-list-row1"  style="display: block;">'."\n";
                    $ret .= "\t"."\t"."\t".$value;
                    $ret .= "\t"."\t".'</div>'."\n";
                    $ret .= "\t".'</div>'."\n";
                }
            }
            return $ret;
        }
    }
    
     
	  
	  //Метод получения ID со строки.
     private function getIdFromLine($line = null){
		 if($line != null){
			 $ret = Tinterface::getTranslit($line);										//Транслицируем сивволы.
			 return str_replace(' ', '_', $ret);										//Заменяем пробел на нижнее подчеркивание.
		 }
     }
	 
	  
	  
    //Метод для получения вертикальних вкладок.
    private function getTabsVerical($arr = null, $id = null){
		if(count($arr) >= 1){															//Проверка ниличия данных.
			//Подключаем библиотеку.
			JHTML::script('jquery-3.1.1.min.js', 'components/com_medical_registry/assets/scripts/');
			$class = 'tabscalendar'.rand(1, 15).rand(1, 15);							//Получаем класс элементов.
			foreach($arr as $key => $value){											//Формируем ссылки.
				$lia[] = '<a style="padding: 5px;" title="'.JText::_('REG_CHOOSE').' - '.mb_strtolower($key).'" href="javascript:$(\'.'.$class.'\').css(\'display\', \'none\'); $(\'#'.calendarURL::getIdFromLine($key).$class.'\').css(\'display\', \'block\');  ">'.$key.'</a>';
			}
			unset($key, $value);
			
			foreach($arr as $key => $value){											//Формируем вкладки.
				$iddiv = calendarURL::getIdFromLine($key).$class;						//Получаем ИД Элемента.
				$idd = calendarURL::getIdFromLine($id).$class;							//Получаем ИД выбранного элемента.
				if($id != null && $idd == $iddiv){										//Если совпадают ИД, то делаем видимым.
					$tabs .= '<div class="'.$class.'" style="display: block;" id="'.$iddiv.'">'.$value.'</div>';
				}
				else{																	//Иначе далем невидимым.
					$tabs .= '<div class="'.$class.'" style="display: none;" id="'.$iddiv.'">'.$value.'</div>';
				}	
			}
			unset($key, $value);
			$l[] = '<div style="vertical-align: top; height:150px; overflow-x: hidden; overflow-y: auto; border: 1px solid;">'.calendarURL::getElementsColumn($lia).'</div>';
			$l[] = '<div>'.$tabs.'</div>';
			$ret = calendarURL::getElementsLine($l);
			return '<div class="inputbox">'.$ret.'</div>';
		}
    }
	  
	  
	  
	  
  }
?>
