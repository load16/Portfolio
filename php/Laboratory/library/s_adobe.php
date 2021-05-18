<?php
  //Класс отвественный за организацию меню выбара файла с использованием плагина для просмотра adobe
  //Просмотр файлов организован по принципу работы элемента SELECT.
  //Подразумевается что плагин adobe reader установлен в браузере.
  
  class s_adobe{
      
      
      function __construct(){
          
      }
               
      //Получение элемента SELECT
      
      /*
      $select - 
      $arr - массив значений.
      $ind - индекс элемента для вывода.
      $name - индекс элементы путь к конкретному файлу.
      $dir - путь к файлам.
      $size - Количество элементов в списке.
      
      
      
      
      
      */
      
      //function __invoke($select, $ind, $name, $arr, $dir, $size){
      function __invoke($arr = null, $ind = null, $name = null, $dir = null,  $size = null){  
          //return select::get_main_part_($arr, $ind, $name, $size);
          //return s_adobe::getLineSelect('1703_Шинакова.pdf', 'C:\Ampps\www\Laboratory\search\smgcanaliz\ХСМГЦ аминокислоты\АК_2020\Молодан_ЛВ\1703_Шинакова.pdf', 'C:\Ampps\www\Laboratory\search\\');
          //return s_adobe::getUrlFile('1703_Шинакова.pdf', 'http://localhost/Laboratory/search/smgcanaliz/ХСМГЦ аминокислоты/АК_2020\Молодан_ЛВ/1703_Шинакова.pdf');
          if(count($arr) >> 0 && $ind != null && $name != null && $dir != null && $size != null){
                        return s_adobe::getCoge($arr, $ind, $name, $dir, $size);
          }                                                                   
      }
      
      
      //Метод получения кода элемента интерфейса.
      private function getCoge($arr = null, $ind = null, $name = null, $dir = null,  $size = null){
          if(count($arr) >> 0 && $ind != null && $name != null && $dir != null && $size != null){
              $ret = '<div style=" display: block; vertical-align: top; height: '.$size.'px; overflow:scroll">'."\n";
              foreach($arr as $k => $v){                                            //Обходим массив.
                  $title = '';                                                      //Первичная ин циализация.
                  if($v['title'] != ''){                                            //Проверка наличия всплывающей подсказки.
                      $title = $v['title'];
                  }
                  if($v['style'] != ''){                                            //Проверка наличия дополнительных данных.
                      $ret .= '<div style="';                                       //Формируем стиль из дополнительных данных.
                      $ret .= $v['style'];
                      $ret .= '"';
                      $ret .='>'.s_adobe::getLineSelect($v[$ind], $v[$name], $dir, '', $title).'</div>'."\n"; 
                  }
                  else{                                                             //Если нет дополнительных данных выводим обычний блок.
                      $ret .= '<div>'.s_adobe::getLineSelect($v[$ind], $v[$name], $dir, '', $title).'</div>'."\n";   
                  }
                  unset($title);                                                    //Штатный сброс. 
              }
              unset($k, $v);
              $ret .= '</div>'."\n";
              return $ret; 
          }
      }
      
      
      
      
      //Метод получения строки выбора с чекбоксом.
      private function getLineSelect($name = null, $fullName = null, $dir = null, $chekBox = null, $title = null){
          if($name != null && $fullName != null && $dir != null){
              $ret = s_adobe::getUrlFile($name, $fullName, $dir, $title);
              $fullName = str_replace('.', '&', $fullName);                             //Готовим именя чекбоксов.
              $fullName = str_replace(' ', '$', $fullName);
              $tt = '';                                                                 //Начальная инициализация всплывающей подсказки.
              if($title != null){                                                       //Проверка наличия тайтла.
                  $tt = 'title="'.$title.'"';                                           //Если есть то формируем.
              }
              if($chekBox != null){
                  $ret = '<input  '.$tt.' type="checkbox" name="'.$fullName.'" checked >'.$ret;
              }
              else{
                  $ret = '<input '.$tt.' type="checkbox" name="'.$fullName.'" >'.$ret;
              }
              return $ret;
          }
      }
      
      
      //Метод формирования ссылки на файл для открытия в новой вкладке.
      public function getUrlFile($name = null, $fullName = null, $dir = null, $title = null){
          if($name != null && $fullName != null  && $dir != null){
              $fullName = s_adobe::getUrl($fullName, $dir);
              $os = PHP_OS;                                                             //Определяем ОС.
              $os = strtolower($os);
              if (strpos($os, 'win') !== false){                                        //Если ОС Винда, то проводим адаптацию под ОС.
                  $name = mb_convert_encoding($name, "UTF-8", "auto"); 
                  $fullName = mb_convert_encoding($fullName, "UTF-8", "auto");
              }
              if($title != null){
                  $ret = '<a href="'.$fullName.'" title="'.$title.'" class="btn" target="_blank">'.$name.'</a>';   
              }
              else{
                  $ret = '<a href="'.$fullName.'" title="'.$fullName.'" class="btn" target="_blank">'.$name.'</a>';   
              }
              
              return $ret;
          }
      }
      
      
      //Метод получения URL по локальной ссылке.
      private function getUrl($pach = null, $dir = null){
          if($pach != null && $dir != null){
              $html = $_SERVER['HTTP_REFERER'];
              $arr = explode('/index.php', $html);
              $url = $arr['0'];                                     //Формируем URL
              unset($arr);
              $os = PHP_OS;                                          //Определяем ОС.
              $os = strtolower($os);
              if (strpos($os, 'win') !== false){
                  $pach = str_replace('\\', '/', $pach);            //Адаптируем путь к файлу с учетом ОС.
                  $dir = str_replace('\\', '/', $dir);
              }
              $a = explode('/', $dir);
              $n = count($a);
              $nameDir = $a[$n - 3];
              $arr = explode($nameDir, $pach);                      //Разделяем путь на директорию.
              return $url.'/'.$nameDir.$arr['1'];               //Возвращаем юрл. 
          }
      }
      
      
      
  }
?>
