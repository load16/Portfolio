<?php
  //Класс ответственный за организацию меню с использованием элемента SELECT
  //include_once 'library\ajax_int.php';          //Подключаем модуль отвественный за формирования элементов управления для компонентов интерфейса.
  class select {

       
      
      function __construct(){
          
      }
               
      //Получение элемента SELECT
      function __invoke($select, $ind, $arr, $size){
          return select::get_main_part_tselect($select, $ind, $arr, $size);
      }
      
      
      
      //Метод возвращает основную часть кода.
      protected function get_main_part_tselect($select, $ind, $arr, $size){
          $ret .= " <select ".$style." $method;  name=$select size=$size  onClick=\"this.form.submit();\">\n";
          foreach ($arr as $key => $value){                     //Главный цикл конструктора элемента интерфейса.  
              $ret .= "<option value='$value[$ind]'>$value[$ind]</option>\n";
          }
          $ret .= " </select> \n";
          return $ret;
      }
      
  }

?>
