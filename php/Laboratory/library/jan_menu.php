<?php
  //Класс отвественный за организацию анимационного меню.
  class jan_menu{
      
      
      function __construct(){
          
      }
      
      
      //Метод получения текущего пути.
      public function getPatch(){
          $p = $_SERVER['HTTP_REFERER'];            
          $a = explode('index.php', $p);
          return $a['0'];
      }
      
      
      //Метод загрузки библиотек в ХЕД.
      public function load(){
          $ret = '<script src="'.jan_menu::getPatch().'js/jquery-2.1.3.min.js" type="text/javascript" language="javascript"></script>'."\n"; 
          $ret .= '<script src="'.jan_menu::getPatch().'js/menu.js" type="text/javascript" language="javascript"></script>'."\n";
          return $ret;
      }
      
      
      
      //Метод запуска меню
      function StartMenu($ControlObject, $ManagedObject, $AnimationSpeedUp, $AnimationSpeedDown, $TypeAnimation){
          $random_number = mt_rand(1, 100);
          //$ret = "<html>\n";
          //$ret .= "<head>\n";
          //$ret .= "<title>Выбор</title>\n";
          //$ret .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=WINDOWS-1251\">\n"; 
          //$ret .= "<script src=\"js/jquery-2.1.3.min.js\" type=\"text/javascript\" language=\"javascript\"></script>\n"; 
          //$ret .= "<script src=\"js/menu.js\" type=\"text/javascript\" language=\"javascript\"></script>\n";
          //$ret .= "\n";
          //$ret .= "";
          $ret .= "        <script type=\"text/javascript\" language=\"javascript\">\n";
          $ret .= "            Popup".$random_number." = function() {\n";
          $ret .= "               MenuPopup.InitAnimation('".$ControlObject."','".$ManagedObject."','".$AnimationSpeedUp."','".$AnimationSpeedDown."','".$TypeAnimation."')\n";
          $ret .= "            }\n";
          $ret .= "         </script>\n";
          $ret .= "\n";
          $ret .= "\n";
          $ret .= "\n";
          //$ret .= "           </head>\n";
          //$ret .= "<body>\n";
          $ret .= "           <script>";
          $ret .= "               Popup".$random_number."()\n";
          $ret .= "           </script>\n";
          //$ret .= 'test';
          //$ret .= "</body>\n";
          //$ret .= "</html>\n";
          return $ret;
      }   
  }
?>
