<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
/**
 * Model Report
 * @author Олег Борисович Дубик
 * load16@rambler.ru
 */
 
 /**
  * Модель задачи "Report" Поисковой состемы.
  */ 
 
     
// Подключаем библиотеку modelitem Joomla.
//jimport('joomla.application.component.modegetInstance');
jimport('joomla.application.component.modelitem'); 
jimport('joomla.database.table'); 
//jimport('joomla.form.form'); 
require_once (JPATH_COMPONENT.DS.'libraries'.DS.'interface.php');
require_once (JPATH_COMPONENT.DS.'libraries'.DS.'data.php'); 
require_once (JPATH_COMPONENT.DS.'models'.DS.'schedule.php');
require_once (JPATH_COMPONENT.DS.'models'.DS.'appointment_doctor.php');       
//jimport( 'joomla.application.component.modelform' );
//jimport('joomla.form.form'); 
 
/**
 * Модель Управление регистратурой.
 */
class medical_registryModelReports extends JModelItem{
    
   
    
    
    //Метод получения всех записей базы.
    public function getAllRecord($nameTable = null){
        if($nameTable != null){
            $db = & JFactory::getDbo();
            $name = $db->nameQuote('#__'.$nameTable);
            $query = 'SELECT
                        *
                        FROM
                        '.$name;
            $db->setQuery($query);
            return $db->loadAssocList();  
        }       
    }
    
    
    //Метод получения типа поля.
    public function getTypePole($namePole = null){
		if($namePole != null){
			$db = & JFactory::getDbo();
			$nameTable = 'registry_record';
			$name = $db->nameQuote('#__'.$nameTable);
			$query = 'DESCRIBE '.$name.';';
			$db->setQuery($query);
			$arr = $db->loadAssocList();
			if(count($arr) >= 1){
				foreach($arr as $k => $v){
					if($v['Field'] == $namePole){
						$ret = $v;
					}
				}
				unset($k, $v);
				return $ret['Type']; 
			}
		}
    }
    
    
    
    //Метод получения записей базы по нескольким полям и параметрам.
    public function getRecordParameters($arr_query = null){
        if(count($arr_query) >= 1){
            $db = & JFactory::getDbo();
			$nameTable1 = $db->nameQuote('#__registry_record');
			$nameTable2 = $db->nameQuote('#__registry_status');
			$nameTable3 = $db->nameQuote('#__registry_type');
			$nameTable4 = $db->nameQuote('#__registry_login');
			$nameTable5 = $db->nameQuote('#__registry_activation');
			$nameTable6 = $db->nameQuote('#__registry_role');
			$nameTable7 = $db->nameQuote('#__registry_specialty');
			//Формируем общую часть запроса.
            $query = 'SELECT
					'.$nameTable1.'.id_record,
					'.$nameTable1.'.data_record,
					'.$nameTable1.'.time_record,
					'.$nameTable1.'.id_user,
					'.$nameTable1.'.id_status,
					'.$nameTable1.'.id_type,
					'.$nameTable1.'.surname,
					'.$nameTable1.'.`name`,
					'.$nameTable1.'.patronymic,
					'.$nameTable1.'.data_of_birth,
					'.$nameTable1.'.country,
					'.$nameTable1.'.region,
					'.$nameTable1.'.district,
					'.$nameTable1.'.city,
					'.$nameTable1.'.village,
					'.$nameTable1.'.description_recording,
					'.$nameTable1.'.phone,
                    '.$nameTable1.'.referral,
					'.$nameTable1.'.mail,
					'.$nameTable1.'.id_create,
					'.$nameTable1.'.data_create,
					'.$nameTable1.'.time_create,
					'.$nameTable1.'.ip_create,
					'.$nameTable1.'.id_modification,
					'.$nameTable1.'.data_modification,
					'.$nameTable1.'.time_modification,
					'.$nameTable1.'.ip_modification,
					'.$nameTable1.'.street,
					'.$nameTable2.'.`status`,
					'.$nameTable3.'.type,
					'.$nameTable7.'.ru_specialty,
					'.$nameTable4.'.surname_login,
					'.$nameTable4.'.name_login,
					'.$nameTable4.'.patronymic_login,
					'.$nameTable6.'.name_role,
					'.$nameTable5.'.name_activation,
					'.$nameTable7.'.id_specialty,
					'.$nameTable6.'.id_role,
					'.$nameTable5.'.id_activation
					FROM
					Joomla_registry_record
					Inner Join '.$nameTable2.' ON '.$nameTable2.'.id_status = '.$nameTable1.'.id_status
					Inner Join '.$nameTable3.' ON '.$nameTable3.'.id_type = '.$nameTable1.'.id_type
					Inner Join '.$nameTable4.' ON '.$nameTable4.'.id_login = '.$nameTable1.'.id_user
					Inner Join '.$nameTable7.' ON '.$nameTable7.'.id_specialty = '.$nameTable4.'.id_specialty_login
					Inner Join '.$nameTable6.' ON '.$nameTable6.'.id_role = '.$nameTable4.'.id_role_login
					Inner Join '.$nameTable5.' ON '.$nameTable5.'.id_activation = '.$nameTable4.'.id_activation_login
					WHERE
					';
					//Конструктор запроса.
					$flag = false;																					//Начальная установка флага.
					foreach($arr_query as $k => $v){																//Обходим массиве переменных.
						$tabs = $v;
						foreach($tabs as $k_t => $v_t){
							if($v_t['value'] != ''){																//Если есть настройки то формируем запрос.
								$typePole = $this->getTypePole($v_t['pole']);										//Обпределяем тип поля.
								if(																					//Ведем обработку при условии.
								$typePole == 'date'																	//Если поле дата или время
								|| $typePole == 'time'
								){
									
									$dd = explode('$$', $v_t['value']);												//Расщепляем дату.
									
									if($flag){
										//Формируе элемент логики в запросе.
										$query .= ' AND 
					';
									}
									$flag = true;																	//Изменение состяния флага.
									
									if($dd['1'] != ''){
										if($typePole == 'time'){													//Если тип поля TIME, то формируем соотв. запрос.
											$query .= 'TIME('.$v_t['pole'].') >= \''.$dd['0'].'\'';					//Формируем запрос.
											$query .= ' AND 
					TIME('.$v_t['pole'].') <= \''.$dd['1'].'\'
					';
										}
										else{																		//Иначе формируем запрос для поля DATA.
											$query .= 'DATE('.$v_t['pole'].') >= \''.$dd['0'].'\'';					//Формируем запрос.
											$query .= ' AND 
					DATE('.$v_t['pole'].') <= \''.$dd['1'].'\'
					';
										}
									}
									else{
										if($typePole == 'time'){													//Если тип поля TIME, то формируем соотв. запрос.
											$query .= 'TIME('.$v_t['pole'].') = \''.$dd['0'].'\'';					//Формируем запрос.
										}
										else{																		//Иначе формируем запрос для поля DATA.
											$query .= 'DATE('.$v_t['pole'].') = \''.$dd['0'].'\'';					//Формируем запрос.
										}	
									}	
								}
								else{																				//Иначе ведем обычную обработку.
									if($flag){
										//Формируме элемент логики в запросе.
										$query .= ' AND 
					';
									}
									$flag = true;																	//Изменение состяния флага.	
									$query .= '`'.$v_t['pole'].'` LIKE \''.$v_t['value'].'%\'';						//Формируем запрос.
								}	
							}
						}																		
						unset($k_t, $v_t);
					}
					unset($k, $v);						
            $db->setQuery($query);
            return $db->loadAssocList();  
        }       
    }
    
    
    
    //Метод подготовки массива для запросов.
    public function getArrQurey(){
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_SURNAME')]['value'] = '';	
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_NAME')]['value'] = '';
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_PATRONYMIC')]['value'] = '';
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_DATE_OF_BIRTH')]['value'] = '';
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_PHONE')]['value'] = '';
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_ELECTRONIC_REFERRAL')]['value'] = '';
        $ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_EMAIL')]['value'] = '';
		
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_SURNAME')]['pole'] = 'surname';
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_NAME')]['pole'] = 'name';
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_PATRONYMIC')]['pole'] = 'patronymic';
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_DATE_OF_BIRTH')]['pole'] = 'data_of_birth';
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_PHONE')]['pole'] = 'phone';
        $ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_ELECTRONIC_REFERRAL')]['pole'] = 'referral';
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_EMAIL')]['pole'] = 'mail';
		
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_SURNAME')]['selected'] = false;
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_NAME')]['selected'] = false;
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_PATRONYMIC')]['selected'] = false;
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_DATE_OF_BIRTH')]['selected'] = false;
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_PHONE')]['selected'] = false;
        $ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_ELECTRONIC_REFERRAL')]['selected'] = false; 
		$ret[JText::_('REG_PERSONAL_INFORMATION')][JText::_('REG_EMAIL')]['selected'] = false;
		
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_COUNTRY')]['value'] = '';
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_REGION')]['value'] = '';
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_CITY')]['value'] = '';
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_DISTRICT')]['value'] = '';
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_VILLAGE')]['value'] = '';
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_STREET_HOUSE')]['value'] = '';
		
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_COUNTRY')]['pole'] = 'country';
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_REGION')]['pole'] = 'region';
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_CITY')]['pole'] = 'city';
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_DISTRICT')]['pole'] = 'district';
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_VILLAGE')]['pole'] = 'village';
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_STREET_HOUSE')]['pole'] = 'street';
		
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_COUNTRY')]['selected'] = false;
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_REGION')]['selected'] = false;
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_CITY')]['selected'] = false;
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_DISTRICT')]['selected'] = false;
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_VILLAGE')]['selected'] = false;
		$ret[JText::_('REG_ADDRESS_OF_RESIDENCE')][JText::_('REG_STREET_HOUSE')]['pole'] = false;
		
		
		
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_DOCTOR_SS')]['value'] = '';
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_SELECTING_SPECIALIZATION')]['value'] = '';
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_STATUS')]['value'] = '';
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_RECORDING_DATE')]['value'] = '';	
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_RECORDING_TIME')]['value'] = '';
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_TYPE_OF_CONSULTATION')]['value'] = '';
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_SHORT_DESCRIPTION_OF_THE_PURPOSE_MAKE_AN_APPOINTMENT')]['value'] = '';
		
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_DOCTOR_SS')]['pole'] = 'surname_login';
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_SELECTING_SPECIALIZATION')]['pole'] = 'ru_specialty';
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_STATUS')]['pole'] = 'status';
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_RECORDING_DATE')]['pole'] = 'data_record';
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_RECORDING_TIME')]['pole'] = 'time_record';
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_TYPE_OF_CONSULTATION')]['pole'] = 'type';
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_SHORT_DESCRIPTION_OF_THE_PURPOSE_MAKE_AN_APPOINTMENT')]['pole'] = 'description_recording';
		
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_DOCTOR_SS')]['selected'] = false;
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_SELECTING_SPECIALIZATION')]['selected'] = false;
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_STATUS')]['selected'] = false;
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_RECORDING_DATE')]['selected'] = false;
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_RECORDING_TIME')]['selected'] = false;
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_TYPE_OF_CONSULTATION')]['selected'] = false;
		$ret[JText::_('REG_MEDICAL_DATA')][JText::_('REG_SHORT_DESCRIPTION_OF_THE_PURPOSE_MAKE_AN_APPOINTMENT')]['selected'] = false;
		
		
		if(Rights::getRights(24)){
			$ret[JText::_('REG_SERVICE_INFORMATION')][JText::_('REG_CREATOR')]['pole'] = 'id_create';
			$ret[JText::_('REG_SERVICE_INFORMATION')][JText::_('REG_CREATED_DATE')]['pole'] = 'data_create';
			$ret[JText::_('REG_SERVICE_INFORMATION')][JText::_('REG_CREATION_TIME')]['pole'] = 'time_create';
			$ret[JText::_('REG_SERVICE_INFORMATION')][JText::_('REG_THE_IP_CREATOR')]['pole'] = 'ip_create';
			$ret[JText::_('REG_SERVICE_INFORMATION')][JText::_('REG_MODIFICATOR')]['pole'] = 'id_modification';
			$ret[JText::_('REG_SERVICE_INFORMATION')][JText::_('REG_DATE_OF_MODIFICATION')]['pole'] = 'data_modification';
			$ret[JText::_('REG_SERVICE_INFORMATION')][JText::_('REG_THE_TIME_OF_MODIFICATION')]['pole'] = 'time_modification';
			$ret[JText::_('REG_SERVICE_INFORMATION')][JText::_('REG_IP_MODIFICATOR')]['pole'] = 'ip_modification';
			
			$ret[JText::_('REG_SERVICE_INFORMATION')][JText::_('REG_CREATOR')]['selected'] = false;
			$ret[JText::_('REG_SERVICE_INFORMATION')][JText::_('REG_CREATED_DATE')]['selected'] = false;
			$ret[JText::_('REG_SERVICE_INFORMATION')][JText::_('REG_CREATION_TIME')]['selected'] = false;
			$ret[JText::_('REG_SERVICE_INFORMATION')][JText::_('REG_THE_IP_CREATOR')]['selected'] = false;
			$ret[JText::_('REG_SERVICE_INFORMATION')][JText::_('REG_MODIFICATOR')]['selected'] = false;
			$ret[JText::_('REG_SERVICE_INFORMATION')][JText::_('REG_DATE_OF_MODIFICATION')]['selected'] = false;
			$ret[JText::_('REG_SERVICE_INFORMATION')][JText::_('REG_THE_TIME_OF_MODIFICATION')]['selected'] = false;
			$ret[JText::_('REG_SERVICE_INFORMATION')][JText::_('REG_IP_MODIFICATOR')]['selected'] = false;
		}
		return $ret;
    }
    
    
    //Метод полученя формы для выбора полей.
    public function getFormSelectPole($arr = null, $id = null){
		if(count($arr) >= 3 && $id != null){
			$form .= '<div class="inputbox">'."\n";
			$form .= '<input type="submit" class="button" name="Select_poles"  value="'.JText::_('REG_FIELD_SELECTION').'" title="'.JText::_('REG_SELECTING_FIELDS_FOR_A_REPORT').'">'."\n";
			$form .= '</div>'."\n";
			foreach($arr as $k => $v){
				$tabs = $v;
				foreach($tabs as $k_t => $v_t){
					if(isset($v_t['selected']) && $v_t['pole'] != ''){
						if($v_t['selected'] == true){
							$form .= $k_t.' - '.'<input type="checkbox" name="'.$v_t['pole'].'" checked>'.'<br/>'."\n";
						}
						else{
							$form .= $k_t.' - '.'<input type="checkbox" name="'.$v_t['pole'].'">'.'<br/>'."\n";
						}
						
					} 
				}
				unset($k_t, $v_t);
			}
			unset($k, $v);
			
			$UniForm = Tinterface::getUniFormModal($form);
			return Tinterface::getModalWindow($id, $UniForm);
		}
    }
    
    
    //Метод получения данных для панели управления поисковыми запросами.
    public function getToolsQuery($flag = false, $arr = null){
    	if(count($arr) >= 2){
			$id = 'Report_Select_Pole';
    		
			$ret .= '<input type="button" class="button" onclick=" $(\'#'.$id.'modal\').arcticmodal();"  value="'.JText::_('REG_FIELD_SELECTION').'" title="'.JText::_('REG_SELECTING_FIELDS_FOR_A_REPORT').'">';
			$ret .= '<input type="submit" class="button" name="Reset_search"  value="'.JText::_('REG_RESET').'" title="'.JText::_('REG_RESET_ALL_SETTINGS_OF_THE_REQUEST').'">';
			
			if($flag != true){									//Если выбранно AJAX запросы то убираем кнопку "ПОИСК"
				$ret .= '<input type="submit" class="button" name="Type_search"  value="'.JText::_('REG_ACTIVE_SEARCH').'" title="'.JText::_('REG_A_SEARCH_MODE_IN_WHICH_THE_OUTPUT_FROM').'">';
			}
			else{
				$ret .= '<input type="submit" class="button" name="Type_search"  value="'.JText::_('REG_PASSIVE_SEARCH').'" title="'.JText::_('REG_A_SEARCH_MODE_IN_WHICH_THE_RESULT_IS_GIVEN_AFTER_PRESSING_THE_SEARCH_BUTTON').'">';
			}
			$ret .= $this->getFormSelectPole($arr, $id);		//Получаем модальное окно.
			return $ret;
    	}
    }
    
    
    //Метод определения наличия выбранных полей.
    public function determineOption($arr = null){
		if(count($arr) >= 2){														//Создаем массив для цикла.
	        foreach($arr as $k => $v){												//Цикл обхода массива перменных.
				$vv = $v;
				foreach($vv as $k_v => $v_v){
					if($v_v['selected']){
						return true;
					}
				}
				unset($k_v, $v_v);
	        }
	        unset($k, $v, $arr);
		}
		return false;
    }
    


    
    
    
    //Метод удаления лишних данных с таблици запроса.
    public function delPoleTable($arr_serach = null, $arr_query = null){
		if(count($arr_serach) >= 1 && count($arr_query) >= 1){
			foreach($arr_query as $k => $v){												//Цикл обхода массива перменных.
				$vv = $v;
				foreach($vv as $k_v => $v_v){
					foreach($arr_serach as $k_s => $v_s){
						$tt = $v_s;
						foreach($tt as $k_l => $v_l){
							if($k_l == $v_v['pole'] && $v_v['selected'] == true){
								$ret[$k_s][$k_v] = $v_l;
							}
						}
						unset($k_l, $v_l);
					}
					unset($k_s, $v_s);
				}
				unset($k_v, $v_v);
	        }
	        unset($k, $v, $arr);
		}
		return $ret;
    }
    
    
    //Метод для получения данных для фомирования информации о выбранных параметрах.
    public function getInfo($arr = null){
		if(count($arr) >= 3){
			$falag = false;															//Установка флага.
			foreach($arr as $k => $v){												//Цикл обхода массива переменных.
				$vv = $v;
				foreach($vv as $k_v => $v_v){
					if($v_v['value']){												//Если есть значение, то ведем обработку.
						if($falag){
							$ret .= ',';
							$ret .= '<br/>';
						}
						$arr_exp = explode('$$',$v_v['value']);
						if($arr_exp['1'] != ''){									//Если есть комбинированое поле, то показываем его.
							$ret .= JText::_($k_v).' - '.JText::_('REG_WITH').' '.$arr_exp['0'].'  '.JText::_('REG_ATN').' '.$arr_exp['1'];
						}
						else{														//Иначе показываем обычное поле.
							$ret .= JText::_($k_v).' - '.JText::_($v_v['value']);
						}
						$falag = true;												//Меняем состояние флага.
					}
				}
				unset($k_v, $v_v);
	        }
	        unset($k, $v, $arr);
	        $ret .= '.';
	        $ret .= '<br/>';
	        if($ret != ''){
				$ret = JText::_('REG_REPORT_ON_REQUEST:').'<br/>'.$ret;
	        }
	        return $ret;
		}
    }
    
    
    
    
    //Метод получения таблици для просмотра и печати.
    public function getTableSerach($arr = null){
		if(
		count($arr) >= 1 
		){
			$n = 0;																	//Установка счетчика.
			$dd = new data();
			$session = &JFactory::getSession();
			$id = $session->get('Medical_Registry_id');								//Данные текущего пользователя.
			$table = '<table>';
			foreach($arr as $k => $v){												//Цикл обхода массива перменных.
				$vv = $v;
				$table .= '<tr>';
				if($k == '0'){
					foreach($vv as $k_v => $v_v){
						if($k == '0'){															//В первой строке выводим шапку таблицы.
							$table .= '<th>';
							$table .= JText::_($k_v);
							$table .= '</th>';
						}
					}
					unset($k_v, $v_v);
					$table .= '</tr>';
					$table .= '<tr>';
				}
				foreach($vv as $k_v => $v_v){
					$table .= '<td>';
					$table .= JText::_($v_v);
					$table .= '</td>';
				}
				$n++;																//Инкремент счетчика.
				unset($k_v, $v_v);	
				$table .= '</tr>';
	        }
	        unset($k, $v, $arr);
	        $table .= '</table>';
	        //Формируем концовку таблицы.
	        $table .= '<br/>';
	        $table .= JText::_('REG_THE_NUMBER_OF_ROWS_IN_THE_TABLE').' - '.$n.'.';
	        $table .= '<br/>';
	        $table .= JText::_('REG_THE_CURRENT_DATE_AND_TIME').' - '.$dd->data_i.', '.$dd->time_i.'.';
	        $table .= '<br/>';
	        $table .= JText::_('REG_CREATOR').' - '.$id['0']['surname_login'].' '.$id['0']['name_login'].' '.$id['0']['patronymic_login'].'.';
	        return $table;
		}
    }
    
    
    
    //Метод получения данных для элемента выбора.
    public function getDataSelect($nameTable = null, $namePole = null, $viewPole = null, $Selected = null){
		$arr = $this->getAllRecord($nameTable);										//Получаем данные с таблици.
		if(																			//Проверяем наличие всех параметров.
		count($arr) >= 3
		&& $namePole != null
		&& $viewPole != null
		){
			$ret_arr[$v['not']] = JText::_('REG_NOT_CHOSEN');
			foreach($arr as $k => $v){												//Обходим данные.
				$ret_arr[$v[$namePole]] = JText::_($v[$viewPole]);					//Формируем массив для элемента интерфейса.
			}
			unset($k, $v);
			//Формируем набор параметров.
			$parametrs['size'] = '1';
			$parametrs['id'] = $namePole;
			$parametrs['default'] = $Selected;
			return Tinterface::getSelect($ret_arr, $parametrs);						//Формируем поле со списком 
		}
    }
    
    
    //Метод получения данных для элемента выбора даты.
    public function getDateSelect($namePole = null, $Data = null){
		if($namePole != ''){
			$arr_data = explode('$$', $Data);										//Разделяем дату.
			$arr = array('size'=>10, 'style'=>"class='inputbox'");
			$ret .= JText::_('REG_WITH').' : '.JHTML::_('calendar', $arr_data['0'], 'from'.$namePole, 'id'.$namePole.'from', '%Y-%m-%d', $arr)."\n";
			$ret .= '   ';
			$ret .= JText::_('REG_ATN').' : '.JHTML::_('calendar', $arr_data['1'], 'to'.$namePole, 'id'.$namePole.'to', '%Y-%m-%d', $arr)."\n";
			return $ret;
		}
    }
    
    
    //Метод получения данных для элемента выбора времени.
    public function getTimeSelect($namePole = null, $Time = null){
		if($namePole != ''){
			$arr_time = explode('$$', $Time);
			$ret .= '<div style="display: inline-block; vertical-align: top;">'."\n";
			$ret .= '<div style="display: inline-block;">'.JText::_('REG_WITH').' : '.'</div>'.'<div style="display: inline-block;">'.Tinterface::getTime('from'.$namePole, 'idform'.$namePole, $arr_time['0']).'</div>'."\n";
			$ret .= '   ';
			$ret .= '<div style="display: inline-block;">'.JText::_('REG_ATN').' : '.'</div>'.'<div style="display: inline-block;">'.Tinterface::getTime('to'.$namePole, 'idto'.$namePole, $arr_time['1']).'</div>'."\n";
			$ret .= '</div>'."\n";
			return $ret;
		}
    }
    
    
    
     
     //Метод подготовки кнопки для печати.
    public function getButtonPrint($id = null){
        return medical_registryModelAppointment_doctor::getButtonPrint($id);
    }
    
     
     
    
    //Метод логирования действий пользователя.
    public function LogEvents($TypeLog = null, $category = null, $message = null, $array = null){
        Tinterface::LogEvents($TypeLog, $category, $message, $array);
    }
     
     
}