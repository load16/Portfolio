<?php
  //Класс ответственный за организацию личного кабинета управления программой перевода.
  include_once 'library/files.php'; 
  include_once 'library/data.php';
  include_once 'library/select.php';
  include_once 'library/textarea.php';
  include_once 'library/input.php';  
  
  class room {
  	  
  	  var $menu;											//Меню кабинета.
  	  var $heat;											//Заглавие задачи.
  	  var $footer;											//Фитер кабинета.
  	  var $content;											//Выводимый контент кабинента.
  	  var $serial;											//Объект для взаимодействя с портами.	
  	  var $much;											//Объект для многопотокового выполнения.
  	  var $date;											//Объект дата.
      var $files;                                           //Объект взаемодействия с файлами.
      var $select;                                          //Объект меню выбора.
      var $textarea;                                        //Объект текста.
      var $input;                                           //Объект input.
      var $session;                                         //Массив переменных сессии.  
  	  
  	  //Конструктор класса.
	  function __construct($permission = false){ 
          session_start();                                  //Стартуем сессию.
          $this->session = $_SESSION;                       //СОздаем объекты.
		  $this->date = new data('Europe/Kiev');
          $this->select = new select();
          $this->input = new input();
          $this->textarea = new textarea();
          $this->files = new files();                       //Создаем оъект взаемодействия с файлами.
		  
          room::action_room();							    //Обрабатываем действия пользователей.
          $this->content = $this->prepContent($this->session['s_file']);                        //Формируем контент.
              
          print room::getView();  
          $_SESSION = $this->session;
	  }
	  
	  
	  //Деструктор класса.
	  function __destruct(){
	  	  unset($this->serial, $this->much, $this->config, $this->date);
		  gc_collect_cycles();
	  }
	  
	  //Метод обработки действия пользователя.
	  private function action_room(){
		  
           if(isset($_POST['language'])){
               $this->session['language'] = $_POST['language'];
           }
            if(isset($_POST['s_file']) && $this->session['language'] != ''){
               $this->session['s_file'] = $_POST['s_file'];
           }
           if(isset($_POST['translate']) 
           && $this->session['language'] != ''
           && $this->session['s_file'] != ''
           && isset($_POST['text_translation'])){                                                           //Обрабатываем действие перевода.
               $language = $this->getVarLan($this->session['s_file']);                                      //Получаем язык файла.
               $namefile = str_replace($language, $this->session['language'], $this->session['s_file']);    //Меняем язык файла.
               $namefile = $this->files->getPatchFile().'Translated\\'.$namefile;                           //Формируем имя нового файла.
               
               $save = $this->getDataSave($this->files->getPatchFile().'Files\\'.$this->session['s_file'], $_POST['text_translation']);
               
               if($save != ''){                                                                             //Проверка наличия данных..
                   $this->files->createFile($namefile);                                                     //Создаеи новый файл. 
                   $this->files->writeFile($namefile, $save);                                               //Записываем данные в файл.
                   $this->files->delFile($this->files->getPatchFile().'Files\\'.$this->session['s_file'], $_POST['text_translation']);
                   unset($_POST['translate'], $this->session['s_file'], $_POST['text_translation']); 
               }
           }
	  }
      
      //Метод исправления переменной
      private function correctionVar($var = null){
          if($var != null){
              return str_replace('% ', ' %', $var);
          }
      }
      
      
      //Метод получения названия языка в названия файла.
      private function getVarLan($namefile = null){
          if($namefile != null){
              $s = explode('.', $namefile);
              if(count($s) >= 3){
                  return $s[0];
              }
          }
      }
      
      
      //Метод получения переведенных данных для сохранения.
      private function getDataSave($filename = null, $data_save = null){
          if($filename != null && $data_save != null){
              $arr = $this->getArrayData($filename, $data_save);                                            //ПОлучаем массив для перевода.
              if(count($arr) >> 0){
                  $data = $this->files->getDataFile($filename);                                             //Получаем данные с файла.
                  foreach($arr as $k => $v){                                                                //Обхлдим данные массива перевода.
                      $v1 = $v['var'];
                      $v2 = substr($v['translation'], 0, -1);                                               //Удаляем последний символ - это перенос строки.
                      $v2 = str_replace('% ', ' %', $v2);                                                   //Исправляем переменные в строке.
                      $v2 = str_replace('/ ', '/', $v2);
                      $v2 = str_replace('" _QQ_ "', '"_QQ_"', $v2);
                      $v2 = str_replace('% S', '%s', $v2);
                      $v2 = str_replace(' $ ', '$', $v2);
                      $v2 = str_replace(' %S', '%s', $v2); 
                      $v2 = str_replace('%S', '%s', $v2);
                      $v2 = str_replace('% M-% d-% Y', '%m-%d-%Y', $v2); 
                      $v2 = str_replace('# ', '#', $v2);
                      $v2 = str_replace('n /', 'n/', $v2);
                      $v2 = str_replace('\ N', '\n', $v2);
                      $v2 = str_replace('br /', 'br/', $v2); 
                      $v2 = str_replace('h /', 'h/', $v2);
                      $v2 = str_replace('i /', 'i/', $v2);
                      $v2 = str_replace('i /', 'i/', $v2);
                      $v2 = str_replace('<B>', '<b>', $v2);
                      $v2 = str_replace('% D', '%d', $v2);
                      $v2 = str_replace(' %D', '%d', $v2);
                      $v2 = str_replace('\ n', '\n', $v2);  
                      //$v2 = str_replace('</ ', '</', $v2);
                      $v2 = str_replace('/> ', '/>', $v2);

                      $data = str_replace($v1, $v2, $data);                                                 //Заменяем данные.
                  }
                  unset($k, $v);
              }
              return $data;                                                                                 //Возвращаем переведенные данные. 
          }
      }
      
      
      //Метод получения массива с данными строк и реревода.
      function getArrayData($filename = null, $data_save = null){
          if($filename != null && $data_save != null){                                                  //Проверка необходимого.
              $data = $this->files->getDataFile($filename);                                             //Получаем данные с файла.
              $arr_save = explode("\n", $data_save);                                                    //Фомируем массив переведенных данных.
              $arr_data = explode("\n", $data);                                                         //формируем массив данных с файла. 
              if(count($arr_save) >> 0){                                                                //Проверка наличия данных
                  foreach($arr_save as $k => $v){                                                       //Исправления ошибок считывания.
                      if($v != null){
                          $ar[] = $v;                                                                   //Формируем новый массив.
                      }
                  }
                  unset($k, $v, $arr_save);                                                             //Штатный сброс переменных.
                  $arr_save = $ar;                                                                      //Фиксируем значения.
                  unset($ar);
              }
              if(count($arr_data) >> 0 && count($arr_save) >> 0){                                       //Проверяем наличие данных.;
                  foreach($arr_data as $k => $v){                                                       //Обходим данные с файла.
                      $var = $this->getValue($v);
                      if($var != null){                                                                 //Формируем переменную для перевода.
                          $arr[] = $var;                                                                //Сохраняем переменную перевода.
                      }
                  }
                  unset($k, $v, $arr_data);
                  $a = count($arr) + 1;                                                                 //Устранение ошибки считывания.
                  if($a == count($arr_save)){                                                           //Проверяем релевантность массивовов.
                      foreach($arr as $k => $v){
                          $ret[$k]['translation'] = $arr_save[$k];                                      //Формируем массив.
                          $ret[$k]['var'] = $v;
                      }
                      unset($k, $v, $arr, $arr_save, $data);                                            //Штатный сброс переменных.
                      return $ret;                                                                      //ВОзвращаем массив.
                  }
              }
           }
      } 
      
      
      //Метод нахождения переменной в переводимой строке
      private function findVar($value = null){
          if($value != null){
              $s = explode('%', $value);                                                //Разделяем по символу %
              if(count($s) >= 2){                                                       //Проверяем наличе символов.
                  foreach($s as $k => $v){                                              //Обходим массив.
                      $a = str_split($v);
                  }
                  unset($k, $v);
              }
          }
      }
      
      //Метод получения значения переводимой перемменой
      private function getValue($value = null){
          if($value != null){                                                           //Проверка наличия значения.
              $arr = explode('"' ,$value);                                              //Разделяем по "
              $a = explode('=' ,$value);                                                //Разделяем по =
              if((count($arr) >= 3)&&(count($a) >= 2)){               //Валидация строки. 
                  if((count($arr) == 3)){
                      $st = $arr['1'];
                  }
                  else{
                      foreach($arr as $k => $v){                                        //Обходим массив.                    
                          if($k == '1'){                                                //Находим второй элемент
                              $st = $v;                              
                          }
                          else{
                              if(($k >> '1')&&(count($arr)) != ($k + 1)){               //Если не конец не начало, то вычисляем.
                                   $st .='"'.$v;
                              }
                          }
                      }
                      unset($k, $v, $arr, $a, $b);                                       //Принудительный сброс переменных.
                  }
                  return $st;                                                            //Возвращаем результат.
              } 
          }
      }
      
      //Метод получения меню файлов
      private function getMenuFile($pach = null){
          if($pach != null){
              $arr = $this->files->getListFile($pach);
              foreach($arr as $key => $value){
                  $a[]['file'] = $value;
              }
              unset($key, $value, $arr);
              $select = $this->select;
              return $select('s_file', 'file', $a, '20');
          }
      }
	  
	  
	  //Метод отображения модуля.
	  public function getViewModule(){
		  $ret = '
		  <div class="module">
		  	<div class="module_heat">
		  		'.$this->heat.'
		  	</div>
		  	<div class="module_tools">
		  		'.$this->tools.'
		  	</div>
		  	<div class="module_content">
		  		'.$this->content.
		  	'</div>
		  	<div class="module_footer">
		  		'.$this->footer.'
		  	</div>
		  </div>';
		  return $ret;
	  }
	  
	  //Метод возвращает вид кабинета.
	  function getView(){
	  	  $this->heat = 'Программа перевода языковых файлов Joomla';
	  	  $this->footer = '@load16 <div style="font-size: 70%;display: inline-block;">Дата -'.$this->date->data_i.' '.$this->date->time_i.'</div>';
	  	  $line['0'] = $this->menu;
	  	  $line['1'] = $this->content;
	  	  $url = router::getCurrentUrl();
	  	  $arr = explode('index.php', $url);
	  	  $url = $arr['0'].'js';
	  	  $urlcss = $arr['0'].'css';
		  $ret = '
		  <head>
      		<SCRIPT src="'.$url.'/close.js" type="text/javascript">
      		</SCRIPT>
      		<link href="'.$urlcss.'/style.css" rel="stylesheet">
      		'.room::LoadLibrary().'
		  </head>
		  <body>
			  <div>
		  		<div class="hat">
		  			'.room::getElementsСenter($this->heat).'
		  		</div>
		  		<div class="content">
		  			'.room::getElementsLine($line).
		  		'</div><br/><br/><br/><br/><br/><br/>
		  		<div class="bottom">
		  			'.room::getElementsСenter($this->footer).'
		  		</div>
			  </div>
		  </body>';
		  unset($urlcss, $url, $arr, $line);
		  return $ret;
	  }
      
      
      //МЕтод подготовки контента.
      private function prepContent($file = null){
          $comtent['1'] = $this->prepMenu($file);
          if($file != null){
               $comtent['2'] = $this->prepDataTranslation($this->files->getPatchFile().'Files\\'.$file);
               $textarea = $this->textarea;
               $input = $this->input;
               $comtent['2'] = 'Текст для перевода!<br/>'.$textarea($comtent['2'], 'text_translation', '40', '200');
               $comtent['2'] .= '<br/>'.$input('Перевести', 'translate', 'submit');
               $comtent['2'] = $this->getForm($comtent['2']);
          }
          else{
               $comtent['2'] = 'Файл для перевода не выбран!';
          }
          return $this->getElementsLine($comtent);
      }
      
      
      //Метод подготовки данных для перевода.
      private function prepDataTranslation($file = null){
          if($file != null){                                                        //Проверка наличия имени файла.
              $data = $this->files->getDataFile($file);                             //Получение данных с файла.
              $arr = explode("\n", $data);                                          //Разделяем файл по концу строки.
              if(count($arr) >= 1){                                                 //Проверка наличия данных.
                  unset($data);                                                     //Штатный сброс данных.
                  foreach($arr as $key => $value){                                  //Обходим массив данных.
                      $get = $this->getValue($value);                               //Формируем строки для перевода.
                      if($get != null){                                             //Проверка наличия данных.
                           $data .=  $get."\n";                                     //Фомируем текст для перевода. 
                      }
                  }
                  unset($key, $value);                                               //Штаный зброс переменных.
              }
              return $data;
          }
      }
      
      
  
      
      
      //Метод подготовки меню файлов.
      private function prepMenu($selected = null){
          $value = $this->session['language'];
          $input = $this->input;
          $ret = $this->getVarLan($selected).' -> '.$input($this->session['language'], 'language').'<br/>';
          $ret .= 'Выбор файла! <br/>'; 
          $ret .= $this->getMenuFile('Files\\');
          $ret = $this->getForm($ret);
          return $ret;
      }
	  
	  
	  //Метод вывода контента
	  public function outContent($content){
		  $this->content = $content;
	  }
	  
	  
	  //Метод загрузки Javascrip и CSS.
	  public function LoadLibrary($name = null){
		  if($name != null){																			//Проверка наличия имени.
			  $_SESSION['loadlibrary'][$name] = $name;													//Регистрируем библиотеку.  
		  }
		  else{																							//Иначе читаем зарегистрированные быблиотеки.
			  $url = router::getCurrentUrl();
	  		  $arr = explode('index.php', $url);
	  		  $url = $arr['0'].'js';
	  		  $urlcss = $arr['0'].'css';
	  		  
	  		  
	  		  $arr = $_SESSION['loadlibrary'];															//Формируем массив.
	  		  if(count($arr) >= 1){																		//Проверка наличия элементов.
				  foreach($arr as $kk => $vv){															//Обходим массив.
					  $arrname = explode('.', $vv);														//Зазделяем имя.
					  if(count($arrname) >= 2){															//Проверка наличия расширения.
						  foreach($arrname as $k => $v){
							  $key = $v;																//Определяем ключ последненго элемента.
						  }
						  if($key == 'js'){																//Если JS то ведем обработку скрипта.
							  $ret .= '<SCRIPT src="'.$url.'/'.$vv.'" type="text/javascript"></SCRIPT>'."\n";
						  }
						  else{																			//Иначе CSS.
							  $ret .= '<link href="'.$urlcss.'/'.$vv.'" rel="stylesheet">'."\n";
						  }
						  unset($k, $v);
					  }
				  }
				  unset($kk, $vv);
	  		  }
	  		  unset($_SESSION['loadlibrary']);															//Штатный сброс настроек.
	  		  unset($arr, $url, $key, $arrname, $name);
	  		  return $ret;																				//Возвращаем результат для хедера.  
		  }
	  }
	  
	  
	  //Метод получения формы.
	  public function getForm($form = null){
		  if($form != null){
			  $url = router::getCurrentUrl();
			  $ret .= '<form  action="'.$url.'" method="post">'."\n";
			  $ret .= $form."\n";
			  $ret .= '</form>'."\n";
			  unset($form, $url);
			  return $ret;
		  }
	  }
	  
	  
		  //Метод постройки элементов в рад.
	    public function getElementsLine($arr = null){
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
	            unset($arr);
	            return $ret;
	        }
	    }
    
	    //Метод постройки элементов в столбик.
	    public function getElementsColumn($arr = null){
	        if(count($arr) >= 1){
	            foreach($arr as $key => $value){
	                if($value != ''){
	                    $ret .= "\t".'<div class="" style="display: block; vertical-align: top;">'."\n";
	                    $ret .= "\t"."\t".'<div style="display: block;">'."\n";
	                    $ret .= "\t"."\t"."\t".$value;
	                    $ret .= "\t"."\t".'</div>'."\n";
	                    $ret .= "\t".'</div>'."\n";
	                }
	            }
	            unset($key, $value, $arr);
	            return $ret;
	        }
	    }
	    
	    //Метод размещения элемента по центру.
	    public function getElementsСenter($content = null){
	        if($content != ''){
	            $ret .= "\t".'<div style="margin:0 auto;">'."\n";
	            $ret .= "\t"."\t".$content; 
	            $ret .= "\t".'</div>'."\n";
	            return $ret;
	        }
	    }
	    
	    //Метод размещения элемента в верху.
	    public function getElementsUP($content = null){
	        if($content != ''){
	            $ret .= "\t".'<div style="display: block; vertical-align: top;">'."\n";
	            $ret .= "\t"."\t"."\t".$content;
	            $ret .= "\t".'</div>'."\n";
	            return $ret;
	        }
	    }
	    
	    //Метод размещения элемента в внизу.
	    public function getElementsDOWN($content = null){
	        if($content != ''){
	            $ret .= "\t".'<div style="display: block; vertical-align: bottom;">'."\n";
	            $ret .= "\t"."\t"."\t".$content;
	            $ret .= "\t".'</div>'."\n";
	            return $ret;
	        }
	    }
	    
	    
	    //Метод приготовки блока к печати.
	    public function getDivPrint($content = null, $id = null){
	        if($id != null && $content != null){
	            $ret .= '<div style="color: #000000;" id="'.$id.'">'."\n";
	            $ret .= "\t".$content."\n";
	            $ret .= '</div>'."\n";
	            return $ret;
	        }
	    }
	    //Метод подготовки кнопки для печати.
	    public function getButtonPrint($id = null){
	        if($id != null){
	        	room::LoadLibrary('print.js');
	            return '<input type="button" title="Печать отчета!" class="button" onclick="printDiv(\''.$id.'\');" value="Печать" />';   
	        }
	    }
	  
	  
	  //Метод получения данных для главного меню.
	  private function getDataMenu(){
		  $menu['Пользователи'] = 'users';
		  $menu['Подключения'] = 'conect';
		  $menu['Письма'] = 'mail';
		  $menu['Устройства'] = 'devices';
		  $menu['Мониторинг'] = 'monitoring';
		  $menu['Отчеты'] = 'report';
		  $menu['Журнал'] = 'log';
		  $menu['Выход'] = 'logout';
		  return $menu;
	  }
	  
	  
	  //Метод получения ссылки.
	  public function getUrlMenu($url = null, $value = null, $title = null){
		  if($url != null && $value != null){ 
			  $ret .= '
			  <li>
			  	<a href="'.$url.'">'.$value.'</a>
			  </li>
			  ';
			  unset($url, $value, $title);
			  return $ret;
		  }
	  }
	  
	  //Метод получения главоно меню.
	  private function getMenu($arrMenu = null){
		  if($arrMenu != null){
		  	  $ret .= '<div class="main_menu">';
			  $ret .= 'Главное меню:</br>'."\n";
			  foreach($arrMenu as $key => $value){
			  	  $ret .= room::getUrlMenu($value, $key);
			  }
			  unset($key, $value, $arrMenu);
			  return '<lu>'.$ret.'</lu>'.'</div>';
		  }
	  }
	  
	  
	  //Метод валидации вводимых данных.
  	  public function ValidationCodeData($var = null, $quiet = false){  
		  if($var != null){
			  if(is_array($var)){												//Если массив, то ведеи обработку.
				  foreach($var as $key => $value){								//Обходим массив.
					  if(router::validationCode($value)){						//Проводим валидацию.
						  
					  }
					  else{														//Если не пройдена, то возвращаем результат.
						  if($quiet == false){									//Если режим не тихий, то выводим сообщение.
							  print '<script> alert(\'Валидация на враждебный код не пройдена!\') </script>';
						  }
						  return false;
					  }
				  }
				  unset($key, $value);
				  return true;													//Если не найдено, то возвращаем true.
			  }
			  return router::validationCode($var);
		  }
		  return true;
  	  }
	  
	  
	  //Удаление данных по ИД.
	  public function DeleteRcord($id = null, $nameTable = null){
		  if($id != null && $nameTable != null){
			  $query = 'DELETE FROM 
			  			`'.$nameTable.'`
			  			WHERE 
			  			`id` = \''.$id.'\'';
			  return SqlAdapter::select_sql($query);
		  }
	  }
	  
	  
	   //Метод получения всех записей таблици.
	   public function getListTable($nameTable = null){
		   if($nameTable != null){
			    $query = "SELECT
		  			*
					FROM
					`".$nameTable."`";
		  		return SqlAdapter::select_sql($query);
		   }
	   }
	   
	   //Метод получения ссыдки на объек
	   public function getLInkObj($obj = null){
		   if($obj != null){
		   	   $ret = &$this->$obj;
			   return $ret;
		   }
	   }
	   
	   
	   
	   //Метод настройки порта для отправки.
	   public function tuningPort($port = null){
		   if($port != null){
			   if($this->serial->deviceSet("COM".$port)){											//Установка порта.
				   if($this->serial->deviceOpen()){													//Открытие порта.
					   return true;																	//Если настройка прошла, то возвращаем истину.
				   }
			   }
		   }
		   $this->serial->deviceClose();															//Закрытие порта.
		   unset($port);
		   return false;																			//Если натройка не прошла созвращаем ложь.
	   }
	   
	   
	   
	   
	   
	   //Метод отправки команды в порт.
	   public function sendCommand($commmand = null, $wait = null, $read = false){
		   if($command != null){
			   if($wait == null){			   														//Проверка наличия параметра ожидания.
			   	   $this->serial->sendMessage($command, $this->config->conf['sleepcommand']);		//Отправляем комманду и ждем.
			   }
			   else{			   																	//Есл параметра нет, то береи из конфига.
			   	   $this->serial->sendMessage($command, $wait);										//Отправляем комманду и ждем.
			   }
			   if($read){																			//Проверка наличия флага чтения.
				   return $this->serial->readPort();
			   }
		   }
	   }
	   
	   
	   
	   //Метод выполнения комманды.
	   public function rumCommand($command = null, $serialPort = null, $wait = null, $read = false){
		   if($command != null && $serialPort != null){												//Проверка наличия параметров.
			   if(room::tuningPort($serialPort)){													//Настройка порта.
				   $result = room::sendCommand($command, $wait, $read);
				   if($read){
				   		if(stristr($result, 'OK') === false){
				   			$this->serial->deviceClose();												//Закритие порта.
						   	return false;
					   	}
					   	else{
							return true;
					   	}
				   }
			   }
			   else{																				//Если настройка не прошла, то возвращаем ложь.
				   return false;
			   } 
		   }
		   return false;
	   }
	  
	  
	  //Метод выполнения массива комманд.
	  public function runArrCommand($arr = null, $wait = null, $read = false){
		  if(count($arr) >= 1){																		//Проверка наличия массива комманд и порта.
			  foreach($arr as $k => $v){															//Обходим массив комманд.
				  $result = room::sendCommand($v, $wait, $read);									//Выполняем комманду, получаем результат.
				  if($read){
					  if(stristr($result, 'OK') == false){												//Усли комманда не пройшла, то возврвщаем фальш.
						  unset($k, $v, $result, $arr, $wait, $read);
						  return false;
					  }
				  }
			  }
			  unset($k, $v, $result, $arr, $wait, $read);
			  return true;																			//Если все комманды прошли, то возвращаем истину.
		  }
		  return false;																				//Если нет параметров, то возвращаем фальш.
	  }
	  
	  //Метод обнаружения AJAX запроса.
	  public function detectionAJAX(){
		  if($_POST['ajax'] != ''){
			  return true;
		  }
		  else{
			  return false;
		  }
	  }
	  
	  //Метод организации фонового AJAX запроса.
	  public function setFoneAJAX($id_reception = null, $id_data = null, $namemodule = null, $time = null){
	  	  if($id_reception != null && $id_data != null && $namemodule != null && $time != null){
			  room::LoadLibrary('jquery-2.1.3.min.js');															//Подключаем билиотеку для Jquery запроса.
	  		  room::LoadLibrary('ajax.js');																		//Подключаем билиотеку для AJAX запроса.
	  		  room::LoadLibrary('add.js');
	  		  //Получаем набор параметров для AJAX запроса.
	  		  $url = router::getCurrentUrl();
	  		  
	  		  $time = '
<script>
    var timerId = setTimeout(function tick() {
   		AjaxQuery.AjaxPostAsyncShort(\'get\',\'data\',\''.$id_data.'\',\''.$namemodule.'\',\''.$url.'\');
   		addQuery.add(\'reception_data\',\''.$id_reception.'\');
    var div = $(\'#'.$id_reception.'\');
   		div.scrollTop(div.prop(\'scrollHeight\'));
		timerId = setTimeout(tick, '.$time.');
	}, '.$time.');
</script>';
			  $ret .= '<div style="display: none;" id="'.$id_data.'"></div>';
			  return $ret.$time;
		  }
	}
	  		  
	  
	  
	  
  }
?>
