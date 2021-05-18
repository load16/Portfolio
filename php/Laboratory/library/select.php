<?php
  //Класс ответственный за организацию меню с использованием элемента SELECT
  //include_once 'library\ajax_int.php';          //Подключаем модуль отвественный за формирования элементов управления для компонентов интерфейса.
  class select {

       
      
      function __construct(){
          
      }
               
      //Получение элемента SELECT
      function __invoke($select = null, $ind = null, $arr = null, $size = null, $selected = null){
          return select::get_main_part_tselect($select, $ind, $arr, $size, $selected);
      }
      
      
      
      //Метод возвращает основную часть кода.
      protected function get_main_part_tselect($select = null, $ind = null, $arr = null, $size = null, $selected = null){
          if($select != null && $ind != null && $arr != null){              //Проверка необходимого.
              if($size != null){                                            //Проверка количества строк в списке.
                  $ret .= '<select name="'.$select.'" size="'.$size.'" onClick="this.form.submit();">'."\n";
              }
              else{
                  $ret .= '<select name="'.$select.'" onchange="this.form.submit();">'."\n"; 
              }
              if(count($arr) >= 1){                                         //Проверка наличия данных для цикла.
                  foreach ($arr as $key => $value){                         //Главный цикл конструктора элемента интерфейса.  
                      if(count($arr) == 1){                                 //Если один элемент, то добавляем пустое значение, для выбоа.
                          $ret .= '<option onClick="this.form.submit();" value="'.$value[$ind].'"></option>'."\n"; 
                      }
                      if($selected == $value[$ind] && $selected != null){   //Если находим выбранный элементо, то показываем его.
                          $ret .= '<option onClick="this.form.submit();" selected value="'.$value[$ind].'">'.$value[$ind].'</option>'."\n";  
                      }
                      else{                                                 //Иначе, не показываем.
                         $ret .= '<option onClick="this.form.submit();" value="'.$value[$ind].'">'.$value[$ind].'</option>'."\n";   
                      }
                  }
                  unset($key, $value);                                      //Штатный сброс.
              } 
              $ret .= " </select>\n";
              return $ret;                                                  //Возвращаем результат.
          }  
      }
      
  }

?>
