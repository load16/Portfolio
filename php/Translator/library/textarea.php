<?php
  //Класс ответственный за организаци элемента TEXT
  class textarea {

       
      
      function __construct(){
          
      }
               
      //Получение элемента textarea
      function __invoke($text = null, $name = null, $rows = null, $cols = null){
          return textarea::get_main_part_textarea($text, $name, $rows, $cols);
      }
      
      
      
      //Метод возвращает основную часть кода.
      protected function get_main_part_textarea($text, $name, $rows, $cols){
          if($name != null){                                                //Проверка наличия нелбходимого.
              $ret .= "<textarea name=".$name." "; if($rows != null){$ret .= " rows=".$rows." ";} if($cols != null){$ret .="cols=".$cols."";} $ret .=">\n";
              $ret .= $text."\n";
              $ret .= "</textarea>\n";
          }
          return $ret;
      }
      
  }

?>
