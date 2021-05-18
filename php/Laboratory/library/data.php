<?php
  //Клас ответственный за текущею дату и время.
  class data{
      
      var $time;                    //Текущее время.
      var $data;                    //Текущая дата.
      
      var $data_i;                  //Текущая дата исправленная
      var $time_i;                  //Текущее время исправленное.
      var $day;
      var $mon;
      var $year;
      
      var $hours;
      var $minutes;
      var $seconds;
      
      
      function __construct ($timezone = null){
          if($timezone != null){
			  date_default_timezone_set($timezone);
          }
          $todayh = getdate();      //Текущаяя дата
          $d = $todayh['mday'];
          $m = $todayh['mon'];
          $y = $todayh['year'];
          
          if($m == 1 || $m == 2 || $m == 3 || $m == 4 || $m == 5 || $m == 6 || $m == 7 || $m == 8 || $m == 9){
              $m_i = '0'.$m;
          }
          else{
              $m_i = $m;
          }
          
          
          
          if($d == 1 || $d == 2 || $d == 3 || $d == 4 || $d == 5 || $d == 6 || $d == 7 || $d == 8 || $d == 9){
              $d_i = '0'.$d;
          }
          else{
              $d_i = $d;
          }
           
          
          $this->day = $d;
          $this->mon = $m;
          $this->year = $y;
          
          
          $h = $todayh['hours'];
          $t = $todayh['minutes'];
          $s = $todayh['seconds'];
          
          $this->hours = $h;
          $this->minutes = $t;
          $this->seconds = $s;
          
          $this->data_i ="$y-$m_i-$d_i"; 
          $this->data = "$y-$m-$d"; 
          $this->time = "$h:$t:$s";
          $this->time_i = data::correction_time($this->time);
      }
      
      //метод превращения секунд во время.
      function conversion_seconds_data($seconds){
          unset($r);
          /*
          $r['0'] =  intval($seconds/3600);
          $r['1'] = $seconds%3600;
          $temp = $r['1']/60;
          $r['1'] = $r['1']/60;
          $r['2'] = $temp%60;
          */
          
          $r['0'] = intval($seconds/3600);
          $temp = $seconds%3600;
          $r['1'] = intval($temp/60);
          $temp = $temp%60;
          $r['2'] = $temp;
          
          
          $res = $r['0'].':'.$r['1'].':'.$r['2'];
          $res = data::correction_time($res);
          return $res;          
      }
      
      //Метод превращения времени в секунды.
      function conversion_time_data($time){
        //  $time = $this->conv_time;
          $t = explode(':',$time);
          $c = $t['2'] + ($t['1'] * 60) + ($t['0'] * 3600);
          return $c;
          //$this->conv_seconds = $c;
      }
      
      //Метод добавления времени.
      function add_time_data($time1, $time2){
          $t1 = explode(':',$time1);
          $t2 = explode(':',$time2);
          
          //Превращаем время в секунды.
          $c1 = $t1['2'] + ($t1['1'] * 60) + ($t1['0'] * 3600);
          $c2 = $t2['2'] + ($t2['1'] * 60) + ($t2['0'] * 3600);
          $c = $c1 + $c2;
          $r['0'] = $c/3600;
          $r['1'] = $c%3600;
          $r['1'] = $r/60;
          $r['2'] = $r['0']%60;
          $res = $r['0'].':'.$r['1'].':'.$r['2'];
          return $res;
      }
      
      
      //Метод сравнения дат.
      function comparison_date($data1, $data2){
          $data_arr1 = explode('-',$data1);
          $y1 = $data_arr1['0'];
          $m1 = $data_arr1['1'];
          $d1 = $data_arr1['2'];
          
          $data_arr2 = explode('-',$data2);
          $y2 = $data_arr2['0'];
          $m2 = $data_arr2['1'];
          $d2 = $data_arr2['2'];
          
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
      
      
      //Метод исправления времены.
      function correction_time($time){
          $time_arr = explode(':',$time);
          $c = $time_arr['0'];
          $v = $time_arr['1'];
          $s = $time_arr['2'];
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
      
      
      
      //Метод исправления даты.
      function correction_data($data){
          $data_arr = explode('-',$data);
          $y = $data_arr['0'];
          $m = $data_arr['1'];
          $d = $data_arr['2'];
          if($m == '1' || $m == '2' || $m == '3' || $m == '4' || $m == '5' || $m == '6' || $m == '7' || $m == '8' || $m == '9'){
              
              $m_i = '0'.$m;
          }
          else{
              $m_i = $m;
          }          
          if($d == '1' || $d == '2' || $d == '3' || $d == '4' || $d == '5' || $d == '6' || $d == '7' || $d == '8' || $d == '9'){
              $d_i = '0'.$d;
          }
          else{
              $d_i = $d;
          }
          $data_i = $y.'-'.$m_i.'-'.$d_i;
          return $data_i;
      }
      
      
      //Метод получения массива дат на неделю по дате
      function getArray_Week($date = null){
          if($date != null){
              
          }
          else{
              $date = $this->data_i;
          }
          $d = explode("-", $date);
          $n = date("w", mktime(0, 0, 0, $d['1'], $d['2'], $d['0']));
          
          $sub = $n - 1;
          $dd1 = strtotime($date.' -'.$sub.'days');
          $n = 1;
           
          while($n <= 7){
              $arr['$n'] = date('Y-m-d', $dd1);
              $dd = date('Y-m-d', $dd1);
              $dd1 = strtotime($dd.' +1days');
              $n++;
          }
          return $arr;
      }
      
      //Метод получения даты на неделю вперед.
      function getDateWeekUp($date = null){
		  if($date != null){
              
          }
          else{
              $date = $this->data_i;
          }
          /*
          $d = explode("-", $date);
          $n = date("w", mktime(0, 0, 0, $d['1'], $d['2'], $d['0']));
          
          $sub = $n - 1;
          */
          $dd1 = strtotime($date);
          $n = 1;
           
          while($n <= 7){
              $dd = date('Y-m-d', $dd1);
              $dd1 = strtotime($dd.' +1days');
              $n++;
          }
          return date('Y-m-d', $dd1);
      }
      
      
      //Метод получения вычисленной даты.
      function getСalculationDate($date = null, $action = null, $days = null){
		  if($date != null && $days != null && ($action == '+' || $action == '-')){
			  $n = 1;
			  $dd1 = strtotime($date);
	          while($n <= $days){
	              $dd = date('Y-m-d', $dd1);
	              if($action == '+'){						//Если сумма, то сумируем.
					  $dd1 = strtotime($dd.' +1days');
	              }
	              else{										//Иначе отнимаем.
					  $dd1 = strtotime($dd.' -1days');
	              }  
				  $n++;
	          }
	          return date('Y-m-d', $dd1);					//Возвращаем результат.
		  }
      }
      
      
      //Метод получения даты на неделю назад.
      function getDateWeekDown($date = null){
		  if($date != null){
              
          }
          else{
              $date = $this->data_i;
          }
          $dd1 = strtotime($date);
          $n = 1;
          while($n <= 7){
              $dd = date('Y-m-d', $dd1);
              $dd1 = strtotime($dd.' -1days');
              $n++;
          }
          return date('Y-m-d', $dd1);
      }
      
      //Метод извлечения значений с отпечатка времени в ввиде массива.
      public function extraction($datatime = null){
          if($datatime != null){
              $arr_d = explode(' ', $datatime);
              //$data = data::correction_data($arr_d['0']);
              //$time = data::conversion_time_data($arr_d['1']);
              $data = $arr_d['0'];
              $time = $arr_d['1'];
              unset($arr_d);
              $arr_a = explode('-', $data);
              $year = $arr_a['0'];
              $month = $arr_a['1'];
              $day = $arr_a['2'];
              unset($arr_a);
              $arr_b = explode(':', $time);
              $hour = $arr_b['0'];
              $minute = $arr_b['1'];
              $second = $arr_b['2'];
              unset($arr_b);
              $arr['data'] = $data;
              $arr['time'] = $time;
              $arr['year'] = $year;
              $arr['month'] = $month;
              $arr['day'] = $day;
              $arr['hour'] = $hour;
              $arr['minute'] = $minute;
              $arr['second'] = $second;
              return $arr;
          }
      }
   
  }  
?>
