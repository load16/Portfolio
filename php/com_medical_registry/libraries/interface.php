<?php
//Библиотека.
//Абстрактный клас Tinterface для организации элементов интерфейса.
defined('_JEXEC') or die;
jimport('joomla.html.html');
require_once (JPATH_COMPONENT.DS.'classes'.DS.'rights.php');
require_once (JPATH_COMPONENT.DS.'libraries'.DS.'data.php'); 

 
abstract class Tinterface {
	
	
	//Метод получения базового URL
	public function getUrlBases(){
		$u = &JURI::getInstance();
		$url = $u->toString();
		$arrErros = $u->getErrors();
		if(count($arrErros) > 0){
		}
		else{
			$base =  $u->getPath();
			return $base;
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
            $ret .= '<div id="'.$id.'">'."\n";
            $ret .= "\t".$content."\n";
            $ret .= '</div>'."\n";
            return $ret;
        }
    }
    
    
    //Метод для получения формы.
    public function getForm($form = null, $id = null, $timer = null, $tools = null){
        if($form != null){
            $url = & JFactory::getURI();
            $d = new data();
            $arr_t = explode(':', $timer);
            if(count($arr_t) != 3){									//Валидация времени.
				unset($timer);
            }
            $ret .= '<div class="module-body">'."\n";
            if($timer != null){										//Если есть время, то выводим таймер.
            	$ret .= JText::_('REG_THE_TIME_UNTIL_THE_END_OF_THE_INPUT');
				$ret .= '<div style="font-size: 8pt;">'.Tinterface::getTimer('TimeCounter', $timer).'</div>'; 
            }
            $ret .= '<form  action="'.$url.'" style="display: none;" method="post" name="cancel">'."\n";
            $ret .= '<input type="text" name="Cancel" value="'.JText::_('REG_CANCEL').'" style="display: none;" >'."\n"; 
            $ret .= '</form>'."\n"; 
            $ret .= JHtml::_('behavior.tooltip')."\n";          // Загружаем тултипы.
            $ret .= JHtml::_('behavior.formvalidation')."\n";   // Загружаем проверку формы. (Валидация на стороне клиента.)
            $ret .= JHTML::stylesheet('Form.css', 'components/com_medical_registry/assets/styles/')."\n";  
            $ret .= '<form action="'.$url.'" method="post" name="form1" class="form-validate">'."\n";
            $ret .= '<div class="inputbox">'."\n";  
            if($tools != null){									//Если есть элементы управления в панель инструментов, то показываем их.
				$ret .= $tools."\n";
            }
            $ret .= '<input id="form_calcel" class="button" type="button" title="'.JText::_('REG_CANCEL_ENTRY_AND_RETURN_TT').'" name="Cancel" value="'.JText::_('REG_CANCEL').'"  onclick="cancel.submit()">'."\n"; 
            $ret .= '<input type="submit" class="button" title="'.JText::_('REG_SAVE_YOUR_ENTRIES_TT').'" name="Save" value="'.JText::_('REG_SAVE').'" >'."\n";
            $ret .= '</div>'."\n";
            $ret .= Tinterface::setFieldsForm($form, $id)."\n";  //Получаем поля формы.
            $ret .= JHTML::_('form.token')."\n";                 // Вставляем ТОКЕН скрытое поле в форму.
            $ret .= '</form>'."\n";
            $ret .= '</div>'."\n";
            if($timer != null){
				//Скрип с задержкой во времени отправки формы отмены. Время задержки 10 минут.
				$Msec = $d->conversion_time_data($timer) * 1000;	//Готовим время в милисекундах. 
	            $ret .= '<script> setTimeout("cancel.submit()", '.$Msec.'); </script>'."\n"; 
            }	            
            return $ret; 
        }
    }
    
    //Метод для получения формы без кнопок.
    public function getUniFormed($form = null){
        if($form != null){
            $url = & JFactory::getURI();
            $ret .= '<form action="'.$url.'" method="post">'."\n";
            $ret .= "\t".$form."\n";
            $ret .= "\t".JHTML::_('form.token')."\n";                // Вставляем ТОКЕН скрытое поле в форму.
            $ret .= '</form>'."\n";
            return $ret; 
        }
    }
    
    
    //Метод получения формы без кнопок для модального окна.
    public function getUniFormModal($form = null){
		if($form != null){
            $url = &JFactory::getURI();
            $ret .= '<div class="module-body">'."\n";
            $ret .= "\t".'<form  action="'.$url.'" style="display: none;" method="post">'."\n";
            $ret .= "\t"."\t".'<input type="text" name="Cancel" value="'.JText::_('REG_CANCEL').'" style="display: none;" >'."\n"; 
            $ret .= "\t".'</form>'."\n"; 
            $ret .= "\t".'<form action="'.$url.'" method="post">'."\n";
            $ret .= "\t"."\t".'<div class="moduletable">'."\n";
            $ret .= "\t"."\t"."\t".$form."\n";
            $ret .= "\t"."\t".'</div>'."\n";
            $ret .= "\t"."\t".JHTML::_('form.token')."\n";                // Вставляем ТОКЕН скрытое поле в форму.
            $ret .= "\t".'</form>'."\n";
            $ret .= '</div>'."\n";
            return $ret; 
        }
    }
    
    
    
    //Метод формирования полей формы.
    public function setFieldsForm($form = null, $id = null){
        $arr = Tinterface::object_to_array($form);              //Формируем массив с объекта.
        foreach($arr as $key => $value){                        //Ставим на последний ключ.    
        }
        if($value['fields']['@attributes'] != ''){				//Если одна вкладка, то
			$arr[] = $value['fields'];                          //Формируем массив для одной вкладки.
        }
        else{
			$arr = $value['fields'];                            //Формируем новый массив.
        }
        
        unset($key, $value);                                    //Обнуляем переменные. 
        foreach($arr as $key => $value){                        //Обходим массив.
            $fields = $value['@attributes']['name'];            //Формируем переменную для вкладок.
            if(           										//Проверка прав на просмотр служебной информации.
            ($fields == 'REG_SERVICE_INFORMATION' && Rights::getRights(24))
            || $fields != 'REG_SERVICE_INFORMATION'
            ){
                foreach($value['fieldset']['field'] as $k => $v){   //Обходим элементы в группе.
                    $name = $v['@attributes']['name'];              //Формируем переменные.
                    $type = $v['@attributes']['type'];				//Получаем тип поля.
                    $required = $v['@attributes']['required'];
                    $line['1'] = JText::_($form->getLabel($name, $fields));
                    if($type == 'checkbox'){						//Если поле с пипом checkbox, то устанавливаем свойство checked.
						$form->getInput($name, $fields)->checked = true;
                    }
                    $line['2'] = JText::_($form->getInput($name, $fields)); //штатная обработка.
                    if($type == 'time'){									//Если есть поле типа время, то подключаем плагин.
						$time = Tinterface::getTimeForPlugin($form->getValue($name, $fields));
						$form->setValue($name, $fields, $time);
						$line['2'] = JText::_($form->getInput($name, $fields));
						$line['2'] = Tinterface::getPluginTime($v['@attributes']['id'], $line['2']);
                    }
                    
                    if($type == 'data'){									//Если есть поле типа data, то подключаем плагин.
						$data = $form->getValue($name, $fields);
						$name = $fields.'['.$name.']';						//Исправляем имя поля.
						$line['2'] = Tinterface::getPluginDate($v['@attributes']['id'], $name, $data, $required);
                    }
                    
                    $line['2'] = Tinterface::CodeTranslation($line['2']);			//Преводим код.
                    
                    //$tabs[$fields] .= Tinterface::getElementsColumn($line)."\n"; 	//Выстраиваем элементы в строку. 
                    $tabs[$fields] .= Tinterface::getElementsLine($line).'<br/>'."\n"; 	//Выстраиваем элементы в строку. 
                }
                unset($k, $v);  
            }                                                   //Обнуляем переменные.  
        }
        unset($key, $value, $form);                             //Обнуляем переменные. 
        return Tinterface::getTabs($tabs, $id);                 //Возвращаем результат орамляя во вкладки.
    }
    
    
    
    
    //Метод преобразования объекта в массив.
    public function object_to_array($data){
            if (is_array($data) || is_object($data)){           //Если объект или массив то выполняем код.
                if(is_object($data)){                           //Если объект то выполняем код.
                    $data = (array)$data;                       //Преобразуем объект в массив.
                }
                foreach ($data as $key => $value){              //Обход массива.
                    $kk = str_replace('*','', $key);            //Удаляем лишние символи в ключе.
                    $result[$kk] = Tinterface::object_to_array($value);     //Рекурсивный вызов.
                }
                unset($data, $key, $value);                     //Обнуление переменных.
                return $result;                                 //Возврат результата.
            }
            unset($key, $value);                                //Обнуление переменных. 
            return $data;                                       //Возврат результата.
        }
    
    
    
    //Метод для получения меню SELECT.
    public function getSelect($arr = null, $paramets = null){
        if($arr != '' && $paramets == null){
            return JHTML::_('select.genericlist', $arr, 'genlist', 'size="8"', 'id', 'title', $selected=NULL, $idtag=false, $translate=true ); 
        }
        if($arr != '' && $paramets != null){
            if($paramets['default'] != ''){
                return JHTML::_('select.genericlist', $arr, $paramets['id'], 'size="'.$paramets['size'].'" class ="'.$paramets['class'].'"', 'id', 'title',  $paramets['default'], $idtag=false, $translate=true); 
            }
            else{
                return JHTML::_('select.genericlist', $arr, $paramets['id'], 'size="'.$paramets['size'].'" class ="'.$paramets['class'].'"', 'id', 'title',  $selected=NULL, $idtag=false, $translate=true); 
            }
        }
    }
    

    
    
    //Метод для получения вкладок.
    public function getTabs($arr = null, $id = null, $active = null){
        if($arr != null){
            $n = 0;
            foreach($arr as $k => $v){                          //Определяем номер активного таба.
                if($k == $active){
                    $nn = $n;
                }
                $n++;
            }
            unset($k, $v);
            
            $options = array(
                'onActive' => 'function(title, description){
                    description.setStyle("display", "block"); 
                    title.addClass("open").removeClass("closed");
                }',
                'onBackground' => 'function(title, description){
                    description.setStyle("display", "none");
                    title.addClass("closed").removeClass("open");
                    
                }',
                'startOffset' => $nn,   // 0 starts on the first tab, 1 starts the second, etc...
                'useCookie' => true,    // this must not be a string. Don't use quotes.
            );
            
            if($id == null){
                $ret .= JHtml::_('tabs.start', 'tabs', $options);    
            }
            else{
                $ret .= JHtml::_('tabs.start', $id, $options);    
            }
            foreach($arr as $key => $value){
                $ret .= JHtml::_('tabs.panel', JText::_($key), 'active'); 
                $iddiv = Tinterface::getIdFromLine($key);
                $ret .= '<div id="'.$iddiv.'">'.$value.'</div>';
            }
            $ret .= JHtml::_('tabs.end');
            return $ret;
        }
    }
    
    
    //Метод валидации kcaptcha.
    public function ValidationKcaptcha($Kcaptcha = null, $message = false){
        if($Kcaptcha != null){
            $SessionItem = &JFactory::getSession(); 
            if($SessionItem->get('captcha_keystring') == $Kcaptcha){
                return true;
            }
        }
        if($message == true){
            JError::raiseWarning( 100, JText::_('REG_NOT_TRUE_TYPED_ELEMENT_PROTECTION_KCAPTCHA'));
            $LogErrors = &JLog::getInstance('Error_Medical_Registry.'.date('Y_m_d').'.log.php'); 
            Tinterface::LogEvents($LogErrors, 'KCAPTCHA:', 'Not the right input KCAPTCHA! Data - '.$Kcaptcha);
            return false;
        }
        else{
            return false;
        } 
    }
    
    //Метод валидации вводимых данных на предмет враждебных запросов.
    public function Validation($text, $message = false){
        $text = strtolower($text);                      //Приравниваем текст параметра к нижнему регистру.
        $check[] = 'select';                            //Создаем массив враждебного кода.            
        $check[] = 'union';
        $check[] = 'order';
        $check[] = 'where';
        $check[] = 'char';
        $check[] = 'from';
        $check[] = 'insert';
        $check[] = 'delete';
        $check[] = 'create';
        $check[] = 'eval';
        foreach($check as $key => $value){              //Обходим массив.
            $var = explode($value, $text);              //Разчепляем водимые данные.
            if(count($var) >= 2){                       //Если находми слова из массива, то сообщаем.
                if($message == true){
                    JError::raiseWarning( 100, JText::_('REG_THE_ENTERED_DATA_IS_NOT_ACCEPTED'));
                    $LogErrors = &JLog::getInstance('Error_Medical_Registry.'.date('Y_m_d').'.log.php');
                    Tinterface::LogEvents($LogErrors, 'VALIDATION:', 'The entered data is not accepted! Data - '.$text); 
                }
                return false;
            }
        }
        unset($key, $value);
        return true;
    }
    
    
    //Метод подмены цифр буквами.
    public function getSubstitution($var = null){
		if($var != null){
			$arr = str_split($var);
			if(count($arr) >= 1){
				foreach($arr as $k => $v){
					if($v == 0){
						$ret .= 'a';
					}
					if($v == 1){
						$ret .= 's';
					}
					if($v == 2){
						$ret .= 'd';
					}
					if($v == 3){
						$ret .= 'f';
					}
					if($v == 4){
						$ret .= 'g';
					}
					if($v == 5){
						$ret .= 'h';
					}
					if($v == 6){
						$ret .= 'j';
					}
					if($v == 7){
						$ret .= 'k';
					}
					if($v == 8){
						$ret .= 'l';
					}
					if($v == 9){
						$ret .= 'q';
					}
				}
				unset($k, $v);
				return $ret;
			}
		}
    }
    
    
    
    //Метод логирования собитий.
    public function LogEvents($TypeLog = null, $category = null, $message = null, $array = null){
        if($category != null && $message != null && $TypeLog != null){
            $SessionItem = &JFactory::getSession(); 
            $id_s = $SessionItem->get('Medical_Registry_id'); 
            $login = $id_s[0]['login_login'];                       //Логин пользователя. 
            if(is_array($array)){                                   //Если есть массив, то преобразовываем его.
                foreach($array as $key => $value){
                    $a = $value;
                    if(is_array($a)){
                        foreach($a as $k => $v){
                            $vv .= $k.'='.$v.', ';
                        }
                        unset($k, $v);
                    }
                    else{
                        $vv .= $key.'='.$value.', ';
                    }
                }
                unset($key, $value);
            }
            if($vv != '' && $login != ''){
                $TypeLog->addEntry(array('category' => $category, 'message' => $message.'. User actions - '.$login.'. Analytical data - '.$vv.'.'));
            }
            if($vv == '' && $login != ''){
                $TypeLog->addEntry(array('category' => $category, 'message' => $message.'. User actions - '.$login.'.'));
            }
            if($vv != '' && $login == ''){
                $TypeLog->addEntry(array('category' => $category, 'message' => $message.'. Analytical data - '.$vv.'.'));
            }
            if($vv == '' && $login == ''){
                $TypeLog->addEntry(array('category' => $category, 'message' => $message.'.'));
            }                                       
        }    
    }
    
    
    //Метод определения нахождения кода в строке.
    public function getCodeSymbol($code = null, $arr_Symbol = null){
		if($code != null && count($arr_Symbol) >= 1){
			foreach($arr_Symbol as $k => $v){
				$arr = explode($v, $code);
				if($arr['1'] != ''){
					return true;
				}
			}
			unset($k, $v);
			return false;
		}
    }
    
    
    //Метод перевода HTML кода для отображения.
    public function CodeTranslation($code = null){
        if($code != null){
            $arr = explode('"', $code);                                     //Разделение
            if(count($arr) >= 1){
                foreach($arr as $key => $value){
                    $arr_v[$key] = JText::_($value);                        //Перевод
                }
                unset($key, $value, $arr);
                $arr_v = implode('"', $arr_v);                              //Слияние
                $arr_v = explode('>', $code);                               //Разделение
                if(count($arr_v) >= 1){
                    foreach($arr_v as $key => $value){
                        $rr = explode('<', $value);                         //Разделение
                        if(count($rr) >= 1){
                            $rr['0'] = JText::_($rr['0']);                  //Преводим.
                            $ret[$key] = implode('<', $rr);
                        }
                        else{
                            $ret[$key] = implode('<', $rr);                 //Слияние
                        }   
                    }
                    unset($key, $value, $arr_v, $rr);
                    return implode('>', $ret);                              //Слияние и возврат. 
                } 
            }
        }
        return $code;                                                       //Возврат исходного кода.
    }
    
    
    
    //Метод определения наличия строки в коде.
    public function PresenceLineCode($text = null, $code = null){
        if($text != null && $code != null){
            $text = strtolower($text);
            $code = strtolower($code);
            $var = explode($code, $text);
            if(count($var) >= 2){
                return true;
            }
            else{
                return false;
            }
        }
    }
    
    
    
    //Метод получения таблици форматированной.
    public function getTableFormat($arr, $id_table = 'id_TableFormat'){
		if(count($arr) >= 1){
			$n_l = 1;													//Установка счетчика строки.
			$ret .= '<table  class="display responsive nowrap" style="width:100%" id="'.$id_table.'">'."\n";
			$id = 1;													//Устанока счетчика ИД элемета.
            $class_row0 = 'cat-list-row0';								//Фромируем классы ячеек таблицы.
            $class_row1 = 'cat-list-row1';
            $n = true;													//Установка флага.
			foreach($arr as $k_l => $v_l){
				$n_s = 1;												//Установка счетчика ячейки в строке.
				$line = $v_l;
				if($n){													//Формируем чередующися класс.
					$cl = $class_row0;
					$n = false;
                }
                else{
					$cl = $class_row1;
					$n = true;
                }
                if($n_l == 1){											//Первую строчку выдиляем.
					$cl = 'inputbox';
					$ret .= '<thead>';
                }
                
                
                
				$ret .= "\t".'<tr class="'.$cl.'">'."\n";
				foreach($line as $k_s => $v){
					
					$param['id'] = $id.'cell';
					$id_ButtonTooltip = $param['id'].'ButtonTooltip';
					if($v['class'] != ''){
						$param['class'] = $v['class'];
					}
					else{
						unset($param['class']);
					}
					
					if($n_l == 1 || $n_s == 1){							//Находим первую строку или столбик.
						
						
						
						if($n_l == 1 && $n_s == 1){
							$ret .= "\t"."\t".'<th class="list-title" style="font-size: 150%;" class="item-suburb">'."\n";
						}
						else{
							$ret .= "\t"."\t".'<th class="list-title" style="font-size: 80%;" class="item-suburb">'."\n";
						}
						$ret .= "\t"."\t"."\t".$v."\n";
						$ret .= "\t"."\t".'</th>'."\n";
						
					}
					else{
						$param['style'] = '
						font-size: 120%;
						';
						$ret .= "\t"."\t".'<td>'."\n";
						$ret .= "\t"."\t"."\t".'<div id="'.$id_ButtonTooltip.'">'.Tinterface::getButtonTooltip($v['id_user'], $v['data'],  $v['admin'], $param, $v['view'], $v['tooltip'], $v['modalwindow']).'</div>'."\n";
						$ret .= "\t"."\t".'</td>'."\n";
					}
					$id++;												//Инкремент ИД.
					$n_s++;												//Инкремент счетчика.
				}
				unset($k_s, $v);
				$ret .= "\t".'</tr>'."\n";
				if($n_l == 1){
					$ret .= '</thead><tbody>';
				}
				$n_l++;													//Инкремент счетчика.
			}
			unset($k_l, $v_l);
			
			//Подключаем плагины и библиотеки.
			JHTML::script('jquery.dataTables.min.js', 'components/com_medical_registry/assets/scripts/');
			JHTML::script('dataTables.responsive.js', 'components/com_medical_registry/assets/scripts/');
			JHTML::stylesheet('responsive.css', 'components/com_medical_registry/assets/styles/');
			
			
			$ret .= '</tbody></table>
			<script>
			  $(function(){
			    $("#'.$id_table.'").dataTable({
			    	"scrollX": true,

			    	language: {
				      "processing": "Подождите...",
				      "search": "Поиск:",
				      "lengthMenu": "Показать _MENU_ записей",
				      "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
				      "infoEmpty": "Записи с 0 до 0 из 0 записей",
				      "infoFiltered": "(отфильтровано из _MAX_ записей)",
				      "infoPostFix": "",
				      "loadingRecords": "Загрузка записей...",
				      "zeroRecords": "Записи отсутствуют.",
				      "emptyTable": "В таблице отсутствуют данные",
				      "paginate": {
				        "first": "Первая",
				        "previous": "Предыдущая",
				        "next": "Следующая",
				        "last": "Последняя"
				      },
				      "aria": {
				        "sortAscending": ": активировать для сортировки столбца по возрастанию",
				        "sortDescending": ": активировать для сортировки столбца по убыванию"
				      }
				  }
			    });
			  })
			</script>	
			'."\n";
			
			
			
			
			
			
			
			
			/*
			$ret .= '</tbody></table>
			<script>
			  $(document).ready(function() { 
				  var table = $(\'#'.$id_table.'\').DataTable( { 
			  		 "scrollX": true,
			  		 language: {
					 	"processing": "Подождите...",
					    "search": "Поиск:",
					    "lengthMenu": "Показать _MENU_ записей",
					    "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
					    "infoEmpty": "Записи с 0 до 0 из 0 записей",
					    "infoFiltered": "(отфильтровано из _MAX_ записей)",
					    "infoPostFix": "",
					    "loadingRecords": "Загрузка записей...",
					    "zeroRecords": "Записи отсутствуют.",
					    "emptyTable": "В таблице отсутствуют данные",
					    "paginate": {
					        "first": "Первая",
					        "previous": "Предыдущая",
					        "next": "Следующая",
					        "last": "Последняя"
					    },
					    "aria": {
					        "sortAscending": ": активировать для сортировки столбца по возрастанию",
					        "sortDescending": ": активировать для сортировки столбца по убыванию"
					    }
				  	 },
			  		 
			  		 
			  		 fixedColumns: { 
			  		 	leftColumns: 1
			  		 }
				  }); 
			  });
			</script>	
			'."\n";
			
			*/
			return $ret;
		}
    }
    
    
    
    //Метод формирования строки заданной длины для таблици.
    public function getStringSize($text = null, $size = null){
		if($text != null && is_int($size)){
			$arr = explode(' ', $text);
			if(count($arr) >= 2){								//Если есть несколько слов, то ведем обработку.
				$n = 0;
				foreach($arr as $key => $value){
					if($n >= $size){
						$n = 0;
						$ret .= $value.'<br/>';
					}
					else{
						$n++;
						$ret .= $value.' ';
					}
				}
				return '<pre>'.$ret.'</pre>';
			}
			//Иначе возвращаем тотже результат.
			return '<pre>'.$text.'</pre>';
		}
    }
    
    
    
    //Метод получения столбца таблицы.
    public function getColumn($arr, $n = null){
		if(count($arr) >= 2 && $n != null){
			foreach($arr as $key => $value){							//Обходим массив.
				$line = $value;											//Сохраняем строку.
				if(count($line) >= $n){
					$nn = 1;											//Наальная установка счетчика.
					foreach($line as $k => $v){							//Обходим строку.
						if($nn == $n){									//Находим нужную строку.
							$ret_v[$k] = $v;							//Сохраняем ее.
						}
						$nn++;											//Инкрементируем счетчик.
					}
					unset($k, $v);
					$ret[$key] = $ret_v;								//Формируем новый массив.
				}
			}
			unset($key, $value);
			return $ret;												//Возвращаем массив.
		}
    }
    
    
    //Метод удаления столбца таблицы.
    public function delColumn($arr, $n = null){
		if(count($arr) >= 2 && $n != null){
			foreach($arr as $key => $value){							//Обходим массив.
				$line = $value;											//Сохраняем строку.
				$ll = $line;
				if(count($line) >= $n){
					$nn = 1;											//Наальная установка счетчика.
					foreach($line as $k => $v){							//Обходим строку.
						if($nn == $n){									//Находим нужную строку.
							unset($ll[$k]);								//Удаляем ееж
						}
						$nn++;											//Инкрементируем счетчик.
					}
					unset($k, $v, $line);
					$ret[$key] = $ll;									//Формируем новый массив.
				}
			}
			unset($key, $value);
			return $ret;												//Возвращаем массив.
		}
    }
    
    
    //Метод получения строки таблицы.
    public function getLine($arr, $n = null){
		if(count($arr) >= 2 && $n != null){
			$nn = 1;													//Нальная установка счетчика.
			foreach($arr as $key => $value){							//Обходим массив.
				$line = $value;											//Сохраняем строку.
				if($nn == $n){											//Находим нужную строку.
					return $line;										//Возвращаем.
				}
				$nn++;
			}
			unset($key, $value);
		}
    }
    
    
    //Метод удаления строки таблицы.
    public function delLine($arr, $n = null){
		if(count($arr) >= 2 && $n != null){
			$nn = 1;													//Нальная установка счетчика.
			foreach($arr as $key => $value){							//Обходим массив.
				$line = $value;											//Сохраняем строку.
				if($nn != $n){											//Находим нужную строку.
					$ret[$key] = $line;									//Сохраняем массив.
				}
				$nn++;
			}
			unset($key, $value);
			return $ret;												//Вохвращаем результат.
		}
    }
    
    //Метод получения блока фиксированного размера.
    public function getDivSize($data, $y = null, $x = null){
		if($data != ''){
			if($y == null && $x != null){
				$ret .= '<div style="width: '.$x.'px; display: block;">'."\n";
				$ret .= "\t".$data."\n";
				$ret .= '</div>'."\n";
				return $ret;
			}
			if($y != null && $x == null){
				$ret .= '<div style="height: '.$y.'px; display: block;">'."\n";
				$ret .= "\t".$data."\n";
				$ret .= '</div>'."\n";
				return $ret;
			}
			if($y != null && $x != null){
				$ret .= '<div style="height: '.$y.'px; width: '.$x.'px; display: block;">'."\n";
				$ret .= "\t".$data."\n";
				$ret .= '</div>'."\n";
				return $ret;
			}
			return $data;
		}
    }
    
    
    
    //Метод получения блока с горизонтальным скролингом.
    public function getDivHorizontalScrolling($data){
		if($data != ''){
			$ret .= '<div style="overflow-x: auto; overflow-y: hidden;">'."\n";
			$ret .= "\t".$data."\n";
			$ret .= '</div>'."\n";
			return $ret;
		}
    }
    
    
    //Метод получения блока с вертикальным скролингом.
    public function getDivVerticalScrolling($data){
		if($data != ''){
			$ret .= '<div style="overflow-x: scroll; overflow-y: hidden;">'."\n";
			$ret .= "\t".$data."\n";
			$ret .= '</div>'."\n";
			return $ret;
		}
    }
    
    
    
    //Метод получения кнопки с текстом, подскаской и выводом модального окна.
    /*
    $param - массив с паремеерами для <div>
    $data - выводимый текст с кнопке.
    $tooltip - текст подсказки.
    $ModalWindow - код модального окна.
    $id_user - идентиффикатор ползователя
    $d - дата.
    */
    public function getButtonTooltip($id_user = null, $d = null, $admin = false, $param = null, $data = null, $tooltip = null, $ModalWindow = null){
    	if($param != null && $data != null){
    		if($ModalWindow != null && $tooltip != null){
    			
    			$id_ButtonTooltip = $param['id'].'ButtonTooltip';
    			
				//Готовим данные для AJAX запроса.
    			//$url = JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Ajax', true, '2');
    			$url = &JFactory::getURI();
    			//$ControlId = $param['id'].'modal';
    			$ControlId = $param['id'].'modal';
    			$NameModule = $admin;
    			$NamePost = 'ModalWindow';
    			$ValuePost = $id_user.'$$'.$d;												//Посылаемые данные.
    			$LoadImg = JUri::root(true).'/components/com_medical_registry/assets/images/plex/huge_load_style.GIF';
    			$FailureImg = JUri::root(true).'/components/com_medical_registry/assets/images/plex/huge_failure.GIF';
    			$NullImg = JUri::root(true).'/components/com_medical_registry/assets/images/plex/huge_no_data.GIF';
    			
    		}
    		$ret .= '<a style="text-decoration: none; outline: none;"';
    		if($tooltip != null){													//Если есть подсказка, то готовим ее.
    			if($ModalWindow != null){											//Если есть модальное окно, то готовим его.
					//$ret .= ' class="modal" onclick="AjaxQuery.AjaxPostSync(\''.$NamePost.'\',\''.$ValuePost.'\',\''.$ControlId.'\',\''.$NameModule.'\',\''.$url.'\',\''.$LoadImg.'\',\''.$FailureImg.'\',\''.$NullImg.'\');" href="#'.$param['id'].'modal" title="';
					$ret .= 'onclick=" $(\'#'.$param['id'].'modal\').arcticmodal(); AjaxQuery.AjaxPostAsync(\''.$NamePost.'\',\''.$ValuePost.'\',\''.$ControlId.'\',\''.$NameModule.'\',\''.$url.'\',\''.$LoadImg.'\',\''.$FailureImg.'\',\''.$NullImg.'\');" title="';
    			}
    			else{
					$ret .= ' title="';
    			}
    			$ret .= $tooltip;
    		}
    		
    		$ret .= '">';
    		$ret .= '<div style="font-size: 90%; padding: 9px 0px 9px 0px;"';
    		if(count($param) >= 1){													//Если есть параметры, то формируем их.
				foreach($param as $key => $value){
					$ret .= ' '.$key.'="'.$value.'"';
    			}
    			unset($key, $value);
    		}
    		$ret .= '>'."\n";
			$ret .= $data;
			$ret .= '</div>'."\n";
			if($ModalWindow != null && $param['id'] != null){						//Если есть всплывающее модальное окно, то готовим его.
				$ret .= Tinterface::getModalWindow($param['id'], $ModalWindow);		//Получаем модальное окно.
			}
			$ret .= '</a>'."\n";													//Готовим концовку.
			return $ret;															//Возвращаем результат.
    	}
    }
    
    
    
    //Метод получения модального окна.
    public function getModalWindow($id = null, $ModalWindow = null, $Closebutton = false){
		if($id != null && $ModalWindow != null){
			//Подключаем плагины и библиотеки.
			JHTML::script('jquery.arcticmodal-0.3.min.js', 'components/com_medical_registry/assets/scripts/');
			JHTML::stylesheet('jquery.arcticmodal-0.3.css', 'components/com_medical_registry/assets/styles/');
			JHTML::stylesheet('simple.css', 'components/com_medical_registry/assets/styles/themes/');
			$ret .= '<div style="display: none;">'."\n";
			$ret .= "\t".'<div class="box-modal" id="'.$id.'modal">'."\n";
			if($Closebutton == false){
				$ret .= "\t"."\t".'<div class="box-modal_close arcticmodal-close">X</div>';
			}
			$ret .= "\t"."\t".'<div class="cat-list-row0">'."\n";
			$ret .= "\t"."\t"."\t".'<div class="module-body">';
			$ret .= "\t"."\t"."\t"."\t".$ModalWindow."\n";
			$ret .= "\t"."\t"."\t".'</div>';
			$ret .= "\t"."\t".'</div>'."\n";
			$ret .= "\t".'</div>'."\n";
			$ret .= '</div>'."\n";
			return $ret;
		}
    }
    
    
    
    
    //Метод вывода модального окна с задержкой на время.
    public function getTimeModalWindow($id = null, $ModalWindow = null, $MSeconds = null, $Closebutton = false){
		if($id != null && $ModalWindow != null && $MSeconds != null){
			$timer = '<script> setTimeout("$(\'#'.$id.'modal\').arcticmodal()", '.$MSeconds.'); </script>';
			$modalWindow = Tinterface::getModalWindow($id, $ModalWindow."\n".$timer, $Closebutton);
			return $modalWindow; 
		}
    }
    
  
    
    
    
    //Метод получения таймера обратного отсчета.
    public function getTimer($id = null, $time = null){
    	if($id != null && time != null){
    		//Подкулючаем плагины и библиотеки.
			JHTML::stylesheet('dscountdown.css', 'components/com_medical_registry/assets/styles/');
			JHTML::script('dscountdown.js', 'components/com_medical_registry/assets/scripts/');
			$d = new data();
			if($time == null){
				$time = $d->time_i;
			}
			$db = & JFactory::getDbo();
			$query = 'SELECT CURTIME();';
			$db->setQuery($query);
			$arr_time = $db->loadAssocList();
			
			$time_new = $arr_time['0']['CURTIME()'];
			$time_new = $d->conversion_time_data($time_new);
			$time = $d->conversion_time_data($time);
			$time = $time + $time_new;
			$time = $d->conversion_seconds_data($time);
			$date = date('F d, Y');
			
			$ret = '<script>
				$(document).ready(function($){
					$(\'#'.$id.'\').dsCountDown({
						endDate: new Date("'.$date.' '.$time.'")
					});
				});
			</script>';
			$ret .= '<div id="'.$id.'"></div>';
			return $ret;
    	}
    }
    
    
    
    
    //Метод получения времени для плагина
    public function getTimeForPlugin($time = null){
    	if($time != null){
			$arr = explode(':', $time);
			$valuePole = $arr['0'].':'.$arr['1'];
			return $valuePole;
    	}
    }
    
    
    
    //Метод получения поля с плагином выбора времени
    public function getTime($namePole = null, $idPole = null, $valuePole = null){
		if($namePole != null && $idPole != null){								//Если есть необходимые денные, то ведем обработку.
			if($valuePole != null){												//Если есть значение то готовим его для плагина.
				$valuePole = Tinterface::getTimeForPlugin($valuePole);
			}
			$pole = '<input type="text" name="'.$namePole.'" class="form-control" size="5" value="'.$valuePole.'">';
			return Tinterface::getPluginTime($idPole, $pole);
		}
    }
    
    
    //Метод обработки собитий объекта по ИД
    public function getEventHandlingMake($id = null, $event = null, $make = null){
		if($id != null && $event != null && $make != null){							//Проверка необходимого.
			$ret = "<script>
		                \$('#".$id."').".$event."(function(){
							  ".$make.";
						});
        			</script>";
			return $ret;
		}
    }
    
    //Метод обработки собитий объекта по КЛАССУ
    public function getEventHandlingMakeClass($class = null, $event = null, $make = null){
		if($class != null && $event != null && $make != null){							//Проверка необходимого.
			$ret = "<script>
		                \$('.".$class."').".$event."(function(){
							  ".$make.";
						});
        			</script>";
			return $ret;
		}
    }
    
    
    //Метод получения плагина Date к полю по ID
    public function getPluginDate($idPole = null, $pole = null, $value = null, $required = null){
		if($idPole != null && $pole != null){													//Проверка необходимого.
			//Плагины для работы с базой.
			//JHTML::script('jquery.datetimepicker.full.min.js', 'components/com_medical_registry/assets/scripts/');
			//JHTML::stylesheet('jquery.datetimepicker.min.css', 'components/com_medical_registry/assets/styles/');
			JHTML::script('calendar_ru.js', 'components/com_medical_registry/assets/scripts/');
			if($required == null){
				$ret = '<input type="text" value="'.$value.'" name="'.$pole.'" id="'.$idPole.'" style="cursor: inherit; text-align: center; width: 80px;" aria-required="false" readonly  onfocus="lcs(this)" onclick="event.cancelBubble=true;lcs(this);">'."\n";//Проверка на предмет обязательного поля.
				//$ret .= '<input type="text" value="'.$value.'" name="'.$pole.'" id="'.$idPole.'text"  />'."\n";
				
				
				//$ret = '<input type="text" value="'.$value.'" name="'.$pole.'" id="'.$idPole.'" style="cursor: inherit; text-align: center; width: 100px;" aria-required="false" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this);" />'."\n";	
				//$ret = '<input type="text" value="'.$value.'" name="'.$pole.'" id="'.$idPole.'" style="cursor: inherit; text-align: center; width: 80px;" aria-required="false" readonly  />'."\n";
			}
			else{
				$ret = '<input type="text" value="'.$value.'" name="'.$pole.'" id="'.$idPole.'" style="cursor: inherit; text-align: center; width: 100px;" aria-required="false" class="date inputbox required" required="required" onfocus="lcs(this)" onclick="event.cancelBubble=true;lcs(this)" />'."\n";	
				//$ret = '<input type="text" value="'.$value.'" name="'.$pole.'" id="'.$idPole.'" style="cursor: inherit; text-align: center; width: 80px;" aria-required="false" class="date inputbox required" required="required"/>'."\n";
			}			
			/*
			//Скрипт для плагина выбора даты.
			$language = JFactory::getLanguage();												//Определяем язык сайта.
			$lang = $language->getTag();														//Получаем тег языка.
			$arr_lang = explode('-', $lang);													//Выделяем настройки.
			if($arr_lang['0'] != ''){															//Если язык получен, то	формируем переменную.
				$ll = $arr_lang['0'];
			}
			else{
				$ll = 'ru';																		//Иначе ставим по умолчанию русский.
			}
			
			
			
			
			
			$ret .= "<script>
						\$.datetimepicker.setLocale('".$ll."');
		                \$('#".$idPole."').datetimepicker({
		                    format:'Y-m-d',
		                    timepicker:false
		                });
        			</script>";
			
			
			*/
			return $ret;
		}
	}
    
    
    
    //Метод получения плагина Time к полю по ID
    public function getPluginTime($idPole = null, $pole = null){
		if($idPole != null && $pole != null){
			//Загружаем библиотеки и плагины.
			JHTML::stylesheet('jquery-clockpicker.min.css', 'components/com_medical_registry/assets/styles/');
			JHTML::stylesheet('clockpicker.css', 'components/com_medical_registry/assets/styles/');
			JHTML::script('jquery-clockpicker.min.js', 'components/com_medical_registry/assets/scripts/');
			
			return '<div class="input-group clockpicker" style=" display: inline-block;" id="'.$idPole.'" data-placement="left" data-align="top" data-autoclose="true">
					    '.$pole.'
					    <span class="input-group-addon">
					        <span class="glyphicon glyphicon-time"></span>
					    </span>
					</div>
					<script type="text/javascript">
						$(\'#'.$idPole.'\').clockpicker();
					</script>';
		}
    }
    
    
    //Метод вывода системного сообщения в модальном окне.
    public function getSystemMessage(){
		//Подключаем необходимые библиотеки.
		JHTML::script('jquery.arcticmodal-0.3.min.js', 'components/com_medical_registry/assets/scripts/');
		JHTML::stylesheet('jquery.arcticmodal-0.3.css', 'components/com_medical_registry/assets/styles/');
		JHTML::stylesheet('simple.css', 'components/com_medical_registry/assets/styles/themes/');
		return '
		<script>
			function blink(selector){
				$(selector).fadeOut(100, function(){
					$(this).fadeIn(100, function(){
						blink(this);
					});
				});
			}

			$(document).ready(
			
			 function(){                                                                  
			    var messLgth = $(\'#system-message-container\').children().length;
			    
			    if(messLgth !== 0){
    				var mess = \'<div id="idsysmessage" style="position: absolute; top: 40%; left: 40%; width: 300px; font-size: 14pt;"><div class="box-modal_close arcticmodal-close">X</div>\'+$(\'#system-message-container\').html()+\'</div>\';
    				$.arcticmodal({
						content: mess
					});

					$(\'#system-message-container\').remove();
					setTimeout("blink(\'#idsysmessage\')", 9500);
					setTimeout("$.arcticmodal(\'close\')", 10000);
							
			    };
			 });
			  
		</script>';
    }
    
    
    
    //Метод сортировки многомерного массива.
    /*
    $arr - массив.
    $pole - имя поля сортировки.
    $type - тип поля сортировки.
    */
    public function SortArray($arr = null, $pole = null, $type = null){
		if(count($arr) >= 1 && $pole != null){
			
			$tmp = Array(); 
			foreach($arr as &$ma) 
			    $tmp[] = &$ma[$pole];
			    sort($tmp);
			    foreach($tmp as $k => $v){
			    	foreach($arr as $k_a => $v_a){
						if($v_a[$pole] == $v){
							$ret[] = $v_a;
						}
			    	}
			    	unset($k_a, $v_a);
			    }
			unset($k, $v);
			return $ret;													//Возвращаем сортированный массив.
		}
		return $arr;														//Возвращаем несортированный масссив.
    }
    
    
    
    
    
    //Метод получения массива POST с формы.
     public function getPostForm(){
         $arr = $_POST;                                              	//Получение массива POST с формы.
         foreach($arr as $key => $value){                            	//Обработка массива.
            foreach($value as $k => $v){
            	if(														//Обрабатываем только поля не принад. условным соглашениям.
            	$k != 'conditions_one'
            	&& $k != 'conditions_two'
            	){
					$post[$k] = $v;                                     //Получение массива.
            	}
            }
            unset($k, $v);
         }
         unset($key, $value, $arr);
         return $post;
     }
     
     
     
     //Метод получения ID со строки.
     public function getIdFromLine($line = null){
		 if($line != null){
			 $ret = Tinterface::getTranslit($line);					//Транслицируем сивволы.
			 return str_replace(' ', '_', $ret);					//Заменяем пробел на нижнее подчеркивание.
		 }
     }
     
     
     
     //Метод транслитерации русского алфавита на латинский.
     public function getTranslit($str = null){
     	 if($str != null){
			 $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
		    $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
		    return str_replace($rus, $lat, $str);					//Заменяем руские символы латинскими.
     	 }
     }

    
}

?>
