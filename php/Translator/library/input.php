<?php
  //Класс ответственный за организаци элемента TEXT
  class input {

       
      
      function __construct(){
          
      }
               
      //Получение элемента TEXT
      function __invoke($text = null, $name = null, $type = 'text', $size = null){
          return input::get_main_part_input($text, $name, $type, $size);
      }
      
      
      
      //Метод возвращает основную часть кода.
      protected function get_main_part_input($text, $name, $type, $size){
          if($name != null){                                                //Проверка наличия необходимого.
              $ret .= '<input type="'.$type.'" name="'.$name.'"'; if($size != null){$ret .= ' size="'.$size.'"';} $ret .=' value="'.$text."\">\n";
          }
          return $ret;
      }
      
  }

?>
