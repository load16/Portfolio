<?php
  //Класс отвественный за обработку данных типа DateTime
  class datetimelib{
	  
	  function __construct(){
		  
	  }
	  
	  
	  
	  //Метод фильтрации многомерного массива по полю типа datetime
	  public function getArrFilter($arr = null, $var = null, $a = null, $b = null){
		  if(count($arr) >= 2 && $var != null && $a != null && $b != null){
			  if(datetimelib::comparison_date($a, $b)){
				  foreach($arr as $key => $value){
				  	  $c = $value[$var];
					  if(datetimelib::ComparisonDatetime($a, $c)){
						  if(datetimelib::ComparisonDatetime($c, $b)){
							  $ret[] = $value;
						  }
					  }
				  }
				  unset($key, $value);
				  return $ret;
			  }
		  }
	  }
	  
	  
	  
	  //Метод разделения даты и времени.
	  public function separation($datatime = null, $get = 'time'){
		  if($datatime != null){
		  	  $e = explode(' ', $datatime);
			  if($get == 'time'){
				  return $e['1'];
			  }
			  else{
				  return $e['0'];
			  }
		  }
	  }
	  
	  //Метод превращения времени в секунды.
      public function conversion_time_data($time = null){
          if($time != null){
			  $t = explode(':',$time);
	          $c = $t[2] + ($t[1] * 60) + ($t[0] * 3600);
	          return $c;
          }
      }
      
      //метод превращения секунд во время.
      public function conversion_seconds_data($seconds = null){
          if($seconds != null){
			  $r[0] = intval($seconds/3600);
	          $temp = $seconds%3600;
	          $r[1] = intval($temp/60);
	          $temp = $temp%60;
	          $r[2] = $temp;
	          $res = $r[0].':'.$r[1].':'.$r[2];
	          $res = datetimelib::correction_time($res);
	          return $res; 
          }        
      }
      
      
      //Метод исправления времены.
      public function correction_time($time = null){
          if($time != null){
			  $time_arr = explode(':',$time);
	          $c = $time_arr[0];
	          $v = $time_arr[1];
	          $s = $time_arr[2];
	          if($c == '0' || $c == '1' || $c == '2' || $c == '3' || $c == '4' || $c == '5' || $c == '6' || $c == '7' || $c == '8' || $c == '9'){
	              
	              $c_i = '0'.$c;
	          }
	          else{
	              $c_i = $c;
	          }
	          if($v == '0' || $v == '1' || $v == '2' || $v == '3' || $v == '4' || $v == '5' || $v == '6' || $v == '7' || $v == '8' || $v == '9'){
	              
	              $v_i = '0'.$v;
	          }
	          else{
	              $v_i = $v;
	          }
	          if($s == '0' || $s == '1' || $s == '2' || $s == '3' || $s == '4' || $s == '5' || $s == '6' || $s == '7' || $s == '8' || $s == '9'){
	              
	              $s_i = '0'.$s;
	          }
	          else{
	              $s_i = $s;
	          }
	          $time_i = $c_i.':'.$v_i.':'.$s_i;
	          return  $time_i;
          }   
      }
      
      
      //Метод сравнения дат.
      public function comparison_date($data1 = null, $data2 = null){
          if($data1 != null && $data2 != null){
			  $data_arr1 = explode('-',$data1);
	          $y1 = $data_arr1[0];
	          $m1 = $data_arr1[1];
	          $d1 = $data_arr1[2];
	          
	          $data_arr2 = explode('-',$data2);
	          $y2 = $data_arr2[0];
	          $m2 = $data_arr2[1];
	          $d2 = $data_arr2[2];
	          
	          if(
	          (($y1 <= $y2) && ($m1 <= $m2) && ($d1 < $d2))
	          || (($y1 <= $y2) && ($m1 < $m2))
	          || ($y1 < $y2)
	          ){
	              return true;
	          }
	          else{
	              return false;
	          }
          }
	      return false;    
      }
	  
	  //Метод сравнения datetime (a < b) = true.
	  public function ComparisonDatetime($datetimea = null, $datetimeb = null){
		  if($datetimea != null && $datetimeb != null){												//Проверяем необходимое.
			  $da = datetimelib::separation($datetimea, 'date');											//Получаем даты.
			  $db = datetimelib::separation($datetimeb, 'date');
			  if(datetimelib::comparison_date($da, $db)){											//Сравниваем даты.
				  return true;
			  }
			  if($da == $db){
				  $ta = datetimelib::separation($datetimea);													//Получаем время.
				  $tb = datetimelib::separation($datetimeb);
				  
				  $ta1 = datetimelib::conversion_time_data($ta);												//Превращаем время в секунды.
				  $tb1 = datetimelib::conversion_time_data($tb);
				  if($ta1 < $tb1){																		//Сравниваем время.
					  return true;
				  }
			  }
		  }
		  return false;
	  }
	  
	  //Метод увеличение datetime на заданное количество секунд.
	  public function getAddDatatime($datetime = null, $s = null){
		  if($datetime != null && $s != null){
			   $date = datetimelib::separation($datetime, 'date');										//Получаем двту и время.
			   $time = datetimelib::separation($datetime);
			   $times = datetimelib::conversion_time_data($time);										//Превращаем время в секунды.
			   $times += $s;																		//Суммируем секунды.
			   if($times > 86400){																	//Если время превысило сутки, то
				   $date = date('Y-m-d', strtotime($date.' +1days'));								//Увеличиваем дату на день.
				   $times = $times - 86400;															//Пересчитываем время.
			   }
			   $time = datetimelib::conversion_seconds_data($times);
			   return $date.' '.$time;																//Возвращаем результат.
		  }
	  }
	  
  }
?>
