<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
/**
 * Model Appointment_doctor
 * @author Олег Борисович Дубик
 */
 /**
  * Модель задачи записи на прием.
  */       
// Подключаем библиотеку modelitem Joomla.
jimport('joomla.application.component.modelitem');
//jimport( 'joomla.database.table' ); 

//require_once (JPATH_COMPONENT.DS.'classes'.DS.'rights.php');
require_once (JPATH_COMPONENT.DS.'libraries'.DS.'data.php');
require_once (JPATH_COMPONENT.DS.'models'.DS.'schedule.php');
require_once (JPATH_COMPONENT.DS.'models'.DS.'registry_management.php'); 
require_once (JPATH_COMPONENT.DS.'libraries'.DS.'interface.php');
require_once (JPATH_COMPONENT.DS.'libraries'.DS.'snoopy.class.php');       
 

class medical_registryModelAppointment_doctor extends JModelItem{
    
   
    //Метод получения списка специальностей у которых есть расписание.
    public function getListSpecialtySchedule(){
        $db = & JFactory::getDbo(); 
        $nameTable1 = $db->nameQuote('#__registry_specialty');
        $nameTable2 = $db->nameQuote('#__registry_login');
        $nameTable3 = $db->nameQuote('#__registry_schedule');   
        $query = 'SELECT DISTINCT
                    '.$nameTable1.'.ru_specialty,
                    '.$nameTable2.'.id_specialty_login,
                    '.$nameTable1.'.id_specialty
                    FROM
                    '.$nameTable2.'
                    Inner Join '.$nameTable3.' ON '.$nameTable3.'.id_login_schedule = '.$nameTable2.'.id_login
                    Inner Join '.$nameTable1.' ON '.$nameTable1.'.id_specialty = '.$nameTable2.'.id_specialty_login
                    WHERE
                    '.$nameTable2.'.id_activation_login = \'1\'';          
        $db->setQuery($query);
        return $db->loadAssocList();   
    }
    
    
    //Метод получения списка докторов у которых есть расписание на специальность если есть.
    public function getListUsersSchedule($id_specialty = null){
    	$db = & JFactory::getDbo();
        $nameTable1 = $db->nameQuote('#__registry_login');
        $nameTable2 = $db->nameQuote('#__registry_specialty');
        $nameTable3 = $db->nameQuote('#__registry_schedule');
        $nameTable4 = $db->nameQuote('#__registry_sex'); 
        $nameTable5 = $db->nameQuote('#__registry_role');
        $nameTable6 = $db->nameQuote('#__registry_activation');   
        if($id_specialty != null){ 
            $query = 'SELECT DISTINCT
                        '.$nameTable1.'.name_login,
                        '.$nameTable1.'.patronymic_login,
                        '.$nameTable1.'.surname_login,
                        '.$nameTable1.'.skype_login, 
                        '.$nameTable2.'.ru_specialty,
                        '.$nameTable1.'.id_activation_login,
                        '.$nameTable1.'.id_role_login,
                        '.$nameTable1.'.id_specialty_login,
                        '.$nameTable1.'.id_sex_login,
                        '.$nameTable4.'.sex_ru,
                        '.$nameTable5.'.name_role,
                        '.$nameTable1.'.id_login,
                        '.$nameTable1.'.login_login,
                        '.$nameTable1.'.post_login,
                        '.$nameTable1.'.id_create_login,
                        '.$nameTable1.'.data_create_login,
                        '.$nameTable1.'.time_create_login,
                        '.$nameTable1.'.ip_create_login,
                        '.$nameTable1.'.id_modification_login,
                        '.$nameTable1.'.date_modification_login,
                        '.$nameTable1.'.time_modification_login,
                        '.$nameTable1.'.ip_modification_login,
                        '.$nameTable1.'.phone_login,
                        '.$nameTable1.'.cabinet_login,
                        '.$nameTable1.'.time_login,
                        '.$nameTable6.'.name_activation
                        FROM
                        '.$nameTable6.'
                        Inner Join '.$nameTable1.' ON '.$nameTable1.'.id_activation_login = '.$nameTable6.'.id_activation
                        Inner Join '.$nameTable5.' ON '.$nameTable5.'.id_role = '.$nameTable1.'.id_role_login
                        Inner Join '.$nameTable2.' ON '.$nameTable2.'.id_specialty = '.$nameTable1.'.id_specialty_login
                        Inner Join '.$nameTable4.' ON '.$nameTable4.'.id_sex = '.$nameTable1.'.id_sex_login
                        Inner Join '.$nameTable3.' ON '.$nameTable3.'.id_login_schedule = '.$nameTable1.'.id_login
                        WHERE
                        '.$nameTable1.'.id_specialty_login = \''.$id_specialty.'\' AND
                        '.$nameTable6.'.id_activation = \'1\'
                        ORDER BY '.$nameTable1.'.id_specialty_login ASC, '.$nameTable1.'.surname_login ASC';
        }
        else{
			$query = 'SELECT DISTINCT
                        '.$nameTable1.'.name_login,
                        '.$nameTable1.'.patronymic_login,
                        '.$nameTable1.'.surname_login,
                        '.$nameTable1.'.skype_login, 
                        '.$nameTable2.'.ru_specialty,
                        '.$nameTable1.'.id_activation_login,
                        '.$nameTable1.'.id_role_login,
                        '.$nameTable1.'.id_specialty_login,
                        '.$nameTable1.'.id_sex_login,
                        '.$nameTable4.'.sex_ru,
                        '.$nameTable5.'.name_role,
                        '.$nameTable1.'.id_login,
                        '.$nameTable1.'.login_login,
                        '.$nameTable1.'.post_login,
                        '.$nameTable1.'.id_create_login,
                        '.$nameTable1.'.data_create_login,
                        '.$nameTable1.'.time_create_login,
                        '.$nameTable1.'.ip_create_login,
                        '.$nameTable1.'.id_modification_login,
                        '.$nameTable1.'.date_modification_login,
                        '.$nameTable1.'.time_modification_login,
                        '.$nameTable1.'.ip_modification_login,
                        '.$nameTable1.'.phone_login,
                        '.$nameTable1.'.cabinet_login,
                        '.$nameTable1.'.time_login,
                        '.$nameTable6.'.name_activation
                        FROM
                        '.$nameTable6.'
                        Inner Join '.$nameTable1.' ON '.$nameTable1.'.id_activation_login = '.$nameTable6.'.id_activation
                        Inner Join '.$nameTable5.' ON '.$nameTable5.'.id_role = '.$nameTable1.'.id_role_login
                        Inner Join '.$nameTable2.' ON '.$nameTable2.'.id_specialty = '.$nameTable1.'.id_specialty_login
                        Inner Join '.$nameTable4.' ON '.$nameTable4.'.id_sex = '.$nameTable1.'.id_sex_login
                        Inner Join '.$nameTable3.' ON '.$nameTable3.'.id_login_schedule = '.$nameTable1.'.id_login
                        WHERE
                        '.$nameTable6.'.id_activation = \'1\'
                        ORDER BY '.$nameTable1.'.id_specialty_login ASC, '.$nameTable1.'.surname_login ASC';
        }
        $db->setQuery($query);
        return $db->loadAssocList();
    }
    
    
    
    
    //Метод получения списка записанных пациентов на дату и по возможности на ИД пользователя.
    public function getListPatientsRecorded($data = null, $id_user = null){
        if($data != null){
            $db = & JFactory::getDbo();
            $nameTable1 = $db->nameQuote('#__registry_record'); 
            $nameTable2 = $db->nameQuote('#__registry_status'); 
            $nameTable3 = $db->nameQuote('#__registry_specialty');   
            $nameTable4 = $db->nameQuote('#__registry_sex');   
            $nameTable5 = $db->nameQuote('#__registry_activation');   
            $nameTable6 = $db->nameQuote('#__registry_role');   
            $nameTable7 = $db->nameQuote('#__registry_login');
            $nameTable8 = $db->nameQuote('#__registry_type');    
            if($id_user != null){
                $query = 'SELECT
                        '.$nameTable2.'.`status`,
                        '.$nameTable1.'.id_record,
                        '.$nameTable1.'.id_type,
                        '.$nameTable7.'.skype_login,
                        '.$nameTable1.'.data_record,
                        '.$nameTable1.'.time_record,
                        '.$nameTable1.'.id_user,
                        '.$nameTable1.'.id_status,
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
                        '.$nameTable7.'.surname_login,
                        '.$nameTable7.'.name_login,
                        '.$nameTable7.'.patronymic_login,
                        '.$nameTable7.'.post_login,
                        '.$nameTable7.'.time_login,
                        '.$nameTable7.'.phone_login,
                        '.$nameTable7.'.cabinet_login,
                        '.$nameTable7.'.id_specialty_login,
                        '.$nameTable3.'.ru_specialty,
                        '.$nameTable7.'.id_sex_login,
                        '.$nameTable4.'.sex_ru,
                        '.$nameTable7.'.id_activation_login,
                        '.$nameTable5.'.name_activation,
                        '.$nameTable7.'.id_role_login,
                        '.$nameTable6.'.name_role,
                        '.$nameTable7.'.id_login,
                        '.$nameTable8.'.type
                        FROM
                        '.$nameTable1.'
                        Inner Join '.$nameTable2.' ON '.$nameTable2.'.id_status = '.$nameTable1.'.id_status
                        Inner Join '.$nameTable7.' ON '.$nameTable7.'.id_login = '.$nameTable1.'.id_user
                        Inner Join '.$nameTable3.' ON '.$nameTable3.'.id_specialty = '.$nameTable7.'.id_specialty_login
                        Inner Join '.$nameTable4.' ON '.$nameTable4.'.id_sex = '.$nameTable7.'.id_sex_login
                        Inner Join '.$nameTable5.' ON '.$nameTable5.'.id_activation = '.$nameTable7.'.id_activation_login
                        Inner Join '.$nameTable6.' ON '.$nameTable6.'.id_role = '.$nameTable7.'.id_role_login
                        Inner Join '.$nameTable8.' ON '.$nameTable1.'.id_type = '.$nameTable8.'.id_type
                        WHERE
                        '.$nameTable1.'.data_record = \''.$data.'\' AND
                        '.$nameTable1.'.id_user = \''.$id_user.'\'
                        ORDER BY TIME(`time_record`) ASC';          
            }
            else{
                $query = 'SELECT
                        '.$nameTable2.'.`status`,
                        '.$nameTable1.'.id_record,
                        '.$nameTable1.'.id_type,
                        '.$nameTable7.'.skype_login, 
                        '.$nameTable1.'.data_record,
                        '.$nameTable1.'.time_record,
                        '.$nameTable1.'.id_user,
                        '.$nameTable1.'.id_status,
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
                        '.$nameTable7.'.surname_login,
                        '.$nameTable7.'.name_login,
                        '.$nameTable7.'.patronymic_login,
                        '.$nameTable7.'.post_login,
                        '.$nameTable7.'.time_login,
                        '.$nameTable7.'.phone_login,
                        '.$nameTable7.'.cabinet_login,
                        '.$nameTable7.'.id_specialty_login,
                        '.$nameTable3.'.ru_specialty,
                        '.$nameTable7.'.id_sex_login,
                        '.$nameTable4.'.sex_ru,
                        '.$nameTable7.'.id_activation_login,
                        '.$nameTable5.'.name_activation,
                        '.$nameTable7.'.id_role_login,
                        '.$nameTable6.'.name_role,
                        '.$nameTable7.'.id_login,
                        '.$nameTable8.'.type
                        FROM
                        '.$nameTable1.'
                        Inner Join '.$nameTable2.' ON '.$nameTable2.'.id_status = '.$nameTable1.'.id_status
                        Inner Join '.$nameTable7.' ON '.$nameTable7.'.id_login = '.$nameTable1.'.id_user
                        Inner Join '.$nameTable3.' ON '.$nameTable3.'.id_specialty = '.$nameTable7.'.id_specialty_login
                        Inner Join '.$nameTable4.' ON '.$nameTable4.'.id_sex = '.$nameTable7.'.id_sex_login
                        Inner Join '.$nameTable5.' ON '.$nameTable5.'.id_activation = '.$nameTable7.'.id_activation_login
                        Inner Join '.$nameTable6.' ON '.$nameTable6.'.id_role = '.$nameTable7.'.id_role_login
                        Inner Join '.$nameTable8.' ON '.$nameTable1.'.id_type = '.$nameTable8.'.id_type 
                        WHERE
                        '.$nameTable1.'.data_record = \''.$data.'\'
                        ORDER BY TIME(`time_record`) ASC';          
            } 
            $db->setQuery($query);    
            return $db->loadAssocList();
        }
    }
    
    
    
    //Метод удаления устаревших блокировок.
    public function delObsoleteData($id_user = null, $date = null, $time = null){
		$db = & JFactory::getDbo();
	    $nameTable1 = $db->nameQuote('#__registry_blocking');
    	if($id_user != null && $date != null && $time != null){
	        $query = 'DELETE
	                    FROM
	                    '.$nameTable1.'
	                    WHERE
	                    '.$nameTable1.'.date = \''.$date.'\' AND
	                    '.$nameTable1.'.id_user = \''.$id_user.'\'AND
	                    '.$nameTable1.'.time = \''.$time.'\'';
    	}
    	else{
    		$query = 'DELETE
	                    FROM
	                    '.$nameTable1.'
	                    WHERE
	                    '.$nameTable1.'.datetime_create < (NOW() - INTERVAL 10 MINUTE)';
			
    	}	
        $db->setQuery($query);
        $db->execute();
    }
    
    
    //Метод получения списка блокировок записи на прием на пользователя и на дату.
    public function getBlockList($id_user = null, $date = null){
		if($id_user != null && $date != null){
			$db = & JFactory::getDbo();
            $nameTable1 = $db->nameQuote('#__registry_blocking');
            $query = 'SELECT DISTINCT
                        *
                        FROM
                        '.$nameTable1.'
                        WHERE
                        '.$nameTable1.'.date = \''.$date.'\' AND
                        '.$nameTable1.'.id_user = \''.$id_user.'\'
                        ORDER BY TIME(`time`) ASC';
            $db->setQuery($query);    
            return $db->loadAssocList(); 
		}
    }
    
    
    
    //Метод блокировки записи на прием.
    public function CreateWriteLock($id_user = null, $date = null, $time = null){
		if($id_user != null && $date != null && $time != null){
			$db = & JFactory::getDbo();
            $nameTable1 = $db->nameQuote('#__registry_blocking');
            $query = 'INSERT INTO
            			'.$nameTable1.'
            			SET
            			'.$nameTable1.'.date = \''.$date.'\',
            			'.$nameTable1.'.id_user = \''.$id_user.'\',
            			'.$nameTable1.'.time = \''.$time.'\',
            			'.$nameTable1.'.datetime_create = NOW()';
            $db->setQuery($query);
        	$db->execute();
		}
    }
    
    
    //Метод валидации на предмет временно блокированной записи.
    public function ValidationLock($id_user = null, $date = null, $time = null, $message = false){
		if($id_user != null && $date != null && $time != null){				//Проверка необходимых данных.
			$arr = $this->getBlockList($id_user, $date);					//Получаем массив блокировок.
			if(count($arr) >= 1){											//Если есть массив.
				foreach($arr as $k => $v){									//Обходим массив.
					if($time == $v['time']){								//Надодим нужную блокировку.
						if($message == true){								//Если есть флаг оповещения, то выполняем его.
							JError::raiseWarning( 100, JText::_('REG_THE_RECORDING_IS_TEMPORARILY_BLOCKED_PLEASE_CHOOSE_ANOTHER_TIME'));
							$LogErrors = &JLog::getInstance('Error_Medical_Registry.'.date('Y_m_d').'.log.php'); 
							Tinterface::LogEvents($LogErrors, 'VALIDATION LOCK', 'The validation of the reception time has not been passed. This reception time is temporarily blocked.', $v);
						}
						return false;										//Возвращаем результат.
					}
				}
				unset($k, $v);
			}
		}
		return true;														//Возврат, прохождения валидации.
    }
    
    
    //Метод получения массива расписания на дату и на ИД пользователя.
    public function getScheduleArray($date = null, $time = null, $id_user = null){
        if($date != null && $time != null && $id_user != null){
            $Schedule = &$this->getInstance('medical_registryModelSchedule');           //Получаем экземпляр модели с просмотра расписаний. 
            $arr_Schedule = $Schedule->getDataSchedule($date, $id_user);
            $d = new data();
            $time_p = $d->conversion_time_data($time); 
            if(count($arr_Schedule) >= 1){
                foreach($arr_Schedule as $key => $value){
                    $time_with = $d->conversion_time_data($value['with_schedule']);     //Превращаем время.
                    $timme_to = $d->conversion_time_data($value['to_schedule']);  
                    if($time_with <= $time_p && $timme_to >= $time_p){                  //Находим вхождение в диапазан.
                        return $value;                                                  //Возвращаем результат.
                    }
                }
                unset($key, $value);
            }
        } 
    }
    
    //Метод получения массива дат на неделю по дате.
    public function getArrayDate($date = null){
        $d = new data();
        return $d->getArray_Week($date); 
    }
    
    
    
    
    //Метод получения данных для формы выбора специальности.
    public function getFormSpecialty($id_component = null, $id_specialty){
        $specialty = $this->getListSpecialtySchedule();
        if($specialty != '' && $id_component != null){
            if($id_specialty == ''){
                $arr_sp['n'] = JText::_('REG_NOT_CHOSEN');
            }
            foreach($specialty as $key => $value){
                $arr_sp[$value['id_specialty_login']] = JText::_($value['ru_specialty']);
            }
            unset($key, $value);
            $ret .= "\t".'<div>'."\n";
            $ret .= "\t"."\t".'<div>'."\n"; 
            $ret .= "\t"."\t"."\t".JText::_('REG_SELECT_DOCTOR').'<br/>'."\n";
            //$ret .= "\t"."\t"."\t".JText::_('REG_SELECT_SPECIALTY')'специальности доктора'."\n"; 
            $ret .= "\t"."\t".'</div>'."\n"; 
            $ret .= "\t"."\t".'<div>'."\n";
            $onChange = "this.form.submit()";
            $ret .= JHTML::_('select.genericlist', $arr_sp, 'id_specialty'.$id_component, 'size="1" onChange="'.$onChange.'"', 'id', 'title', $id_specialty)."\n";
            $ret .= "\t"."\t".'</div>'."\n";
            $ret .= "\t".'</div>'."\n"; 
        }
        return $ret;
    }
    
    
    //Метод для получения данных для формы для выбора пользователя.
    public function getFormSelectUser($id_component = null, $id_specialty = null, $id_user){
        $specialty = $this->getListSpecialtySchedule();
        if($id_specialty != ''){
            $users = $this->getListUsersSchedule($id_specialty);
            if($users != ''){
                foreach($users as $key => $value){
                    $arr_ur[$value['id_login']] = $value['surname_login'].' '.$value['name_login'].' '.$value['patronymic_login']; 
                }
                unset($key, $value);
            }
        }
        if($specialty != '' && $id_component != null){
            $onChange = "this.form.submit()";
            $ret .= "\t".'<div>'."\n";
            $ret .= "\t"."\t".'<div>'."\n"; 
            $ret .= "\t"."\t"."\t".JText::_('REG_DOCTOR_NAME');
            $ret .= "\t"."\t".'</div>'."\n";  
            if($arr_ur != ''){
                $ret .= "\t"."\t".'<div>'."\n"; 
                $ret .= JHTML::_('select.genericlist', $arr_ur, 'id_login'.$id_component, 'size="6" onChange="'.$onChange.'"', 'id', 'title', $id_user)."\n";
                $ret .= "\t"."\t".'</div>'."\n";
            }
            $ret .= "\t".'</div>'."\n"; 
        }
        return $ret;
    }
    
    
    
    //Метод получения данных для всплывающено модального окна с формой отзыва или предложения пользователя.
    function getReviews(){
		$form = $this->getForm('Reviews', 'ReviewsData');      														//Получаем объект формы.
		$viewForm = Tinterface::getForm($form, 'id_form_Rev');														//Получаем вид формы.
		//$viewForm = Tinterface::getUniFormModal($form);
		$modalWindow = Tinterface::getTimeModalWindow('id_modal_Reviews', $viewForm, 1000);							//Получаем модальное окно.
		return $modalWindow;
    }
    
    
    //Метод формирования меню выбора времени записи на прием на неделю.
    public function getSelectTimeWeek($data = null, $id_user = null, $admin = false){
        if($data != null && $id_user != null){
            $objRegistry_Management = $this->getInstance('medical_registryModelRegistry_Management');               //Получаеи объект модели Registry_Management.
            $arr_Week = $objRegistry_Management->getAllRecord('registry_week');                                     //Получаем массив дней недели.
            $arr_date = $this->getArrayDate($data);                                                                 //Получаем массив дат.
            $ret .= "\t".'<div style="display: block; vertical-align: top;">'."\n";
            foreach($arr_date as $key => $value){
                $table = $this->getSelectTime($value, $id_user, $admin); 
                if($table != ''){
                    $ret .= "\t"."\t".'<div style="display: inline-block; vertical-align: top; padding: 2px;">'."\n";
                    $ret .= "\t".'<div>'."\n";
                    $ret .= '<b>'.JText::_($arr_Week[$key-1]['week']).'</b>';
                    $ret .= "\t".'</div>'."\n";
                    $ret .= "\t"."\t"."\t".$table; 
                    $ret .= "\t".'</div>'."\n";
                }
                unset($table);    
            }
            unset($key, $value);
            $ret .= "\t".'</div>'."\n";
            return $ret; 
        }
    }
    
    
    //Метод формирования меню выбора времени записи на прием.
    public function getSelectTime($data = null, $id_user = null, $admin = false){
        $arr = $this->getArraySchedule($data, $id_user, $admin);
        if(count($arr) >= 1){   
            $ret .= "\t".'<div style="display: block;">'."\n";
            $ret .= "\t"."\t".'<div style="display: block;">'."\n";  
            $ret .= "\t"."\t"."\t".$data."\n";
            $ret .= "\t"."\t".'</div>'."\n";
            $ret .= "\t"."\t".'<div style="display: block;">'."\n";  
            $ret .= "\t"."\t"."\t".JText::_('REG_SELECTED_TIME')."\n"; 
            $ret .= "\t"."\t".'</div>'."\n";
            $ret .= "\t"."\t".'<div style="display: block;">'."\n"; 
            $ret .= '<select name="SelectDateTime" size="10" onChange="this.form.submit()">'."\n";
            foreach($arr as $key => $value){
                $dd = $this->PreparationTime($value['start']);
                if($value['id_record'] != ''){
                	$tooltip = $value['date']['surname']."\n";
		            $tooltip .= $value['date']['name']."\n";
		            if($value['date']['patronymic'] != ''){
						$tooltip .= $value['date']['patronymic']."\n";
		            }
		            $tooltip .= "\n";
		            $tooltip .= JText::_('REG_DATE_OF_BIRTH').' - '.$value['date']['data_of_birth']."\n";
		            $tooltip .= JText::_('REG_COUNTRY').' - '.$value['date']['country']."\n";
		            $tooltip .= JText::_('REG_REGION').' - '.$value['date']['region']."\n";
		            $tooltip .= JText::_('REG_DISTRICT').' - '.$value['date']['district']."\n";
		            if($value['date']['city'] != ''){
						$tooltip .= JText::_('REG_CITY').' - '.$value['date']['city']."\n";
		            }
		            if($value['date']['village'] != ''){
						$tooltip .= JText::_('REG_VILLAGE').' - '.$value['date']['village']."\n";
		            }
		            if($value['date']['street'] != ''){
						$tooltip .= JText::_('REG_STREET_HOUSE').' - '.$value['date']['street']."\n";
		            }
		            $tooltip .= "\n";
		            if($value['date']['phone'] != ''){
						$tooltip .= JText::_('REG_PHONE').' - '.$value['date']['phone']."\n";
		            }
                    if($value['date']['referral'] != ''){
                        $tooltip .= JText::_('REG_ELECTRONIC_REFERRAL').' - '.$value['date']['referral']."\n";
                    }
		            if($value['date']['mail'] != ''){
						$tooltip .= JText::_('REG_EMAIL').' - '.$value['date']['mail']."\n";
		            }
		            $tooltip .= "\n";
		            $tooltip .= JText::_('REG_TYPE_OF_CONSULTATION').' - '.mb_strtolower(JText::_($value['date']['type']))."\n";
		            $tooltip .= JText::_($value['date']['status'])."\n";
		            $tooltip .= JText::_('REG_DESCRIPTION_OF_TARGET_APPOINTMENTS')."\n";
		            $tooltip .= $value['date']['description_recording']."\n";
		            
                	
                    if($value['date']['id_status'] <= 2){
                    	if(Rights::getRights('15')){
							$ret .= "\t"."\t".'<option title="'.$tooltip.'" class="MEDICAL_REGISTRY_background_red" value="'.$data.'$$'.$value['start'].'$$'.$id_user.'">'.$dd.'</option>'."\n"; 
                    	}
                    	else{
							$ret .= "\t"."\t".'<option title="'.JText::_('REG_TIME_NOT_FREELY_TT').'" class="MEDICAL_REGISTRY_background_red" value="'.$data.'$$'.$value['start'].'$$'.$id_user.'">'.$dd.'</option>'."\n"; 
                    	}
						
                    }
                    else{
                    	if(Rights::getRights('15')){
							$ret .= "\t"."\t".'<option title="'.$tooltip.'" class="MEDICAL_REGISTRY_background_yellow" value="'.$data.'$$'.$value['start'].'$$'.$id_user.'">'.$dd.'</option>'."\n";
                    	}
                    	else{
							$ret .= "\t"."\t".'<option title="'.JText::_('REG_TIME_NOT_FREELY_TT').'" class="MEDICAL_REGISTRY_background_yellow" value="'.$data.'$$'.$value['start'].'$$'.$id_user.'">'.$dd.'</option>'."\n"; 
                    	}
                        
                    }  
                }
                else{
                	if($value['blocked']){
						$ret .= "\t"."\t".'<option title="'.JText::_('REG_THE_RECORDING_IS_TEMPORARILY_BLOCKED_PLEASE_CHOOSE_ANOTHER_TIME').'" class="MEDICAL_REGISTRY_background_yellow" value="'.$data.'$$'.$value['start'].'$$'.$id_user.'">'.$dd.'</option>'."\n";
                	}
                	else{
						$ret .= "\t"."\t".'<option title="'.JText::_('REG_TIME_FREELY_TT')."\n".JText::_('REG_THE_RECEIVE_LOCATION').' - '.$value['cabinet_schedule'].'" class="MEDICAL_REGISTRY_background_green" value="'.$data.'$$'.$value['start'].'$$'.$id_user.'">'.$dd.'</option>'."\n";
                	}       
                }
            }
            $ret .= '</select>'."\n";
            $ret .= "\t"."\t".'</div>'."\n"; 
            $ret .= '</div>'."\n";    
            return $ret;
        }   
    }
    
    
    
    
    //Метод получения данных для меню времени записи на прием в табличном виде.
    function getMenuTimeTable($data = null, $id_specialty = null, $admin = false){
		if($data != null){
			$list_doctors = $this->getListUsersSchedule($id_specialty);											//Получаем список докторов с расписанием.
			$list_week = medical_registryModelRegistry_Management::getAllRecord('registry_week');				//Получаем список дней недели.
			$list_date = medical_registryModelSchedule::getArrayDate($data);									//Метод получения списка дат на неделю.
			$MainLine[JText::_('REG_DOCTOR_NAME')] = JText::_('REG_DOCTOR_NAME');
			foreach($list_week as $k => $v){																	//формируем заглавный столбец.
				$MainLine[$k] = '<b>'.JText::_($v['week']).'</b>';												//Пишем день недели.
				$MainLine[$k] .= '<br/>';																		//Снизу.
				$MainLine[$k] .= $list_date[$k + 1];															//Пишем даду.
			}
			unset($k, $v);
			$arr[] = $MainLine;																					//Пишем первую строчку массива.
			$n_line = 1;																						//Устанавливаем счетчик строк.
			foreach($list_doctors as $k => $v){																	//Обходим всех докторов.
				$arr[$n_line][JText::_('REG_DOCTOR_NAME')] = $v['surname_login'].'<br/>';
				$arr[$n_line][JText::_('REG_DOCTOR_NAME')] .= $v['name_login'].' ';
				$arr[$n_line][JText::_('REG_DOCTOR_NAME')] .= $v['patronymic_login'].'<br/>';
				$arr[$n_line][JText::_('REG_DOCTOR_NAME')] .= JText::_($v['ru_specialty']);
				foreach($list_date as $k_d => $v_d){															//Обходим все даты недели.
					//Получаем расписание на дату и на пользователя.
					$list_data_schedules = medical_registryModelSchedule::getDataSchedule($v_d, $v['id_login'], $admin);
					$list_records = $this->getListPatientsRecorded($v_d, $v['id_login']);						//Получаем список записанных.
					if(
					count($list_data_schedules) >= 1									//Если есть расписание на дату и на ИД пользователя, то показываем.
					|| (count($list_records) >= 1 && $admin)							//Или есть записанные в админской консоли.
					){
						$arr_wiev = $this->getSumTimeSegments($list_data_schedules);	//Получаем сумарные отрезки времени.
						$arr[$n_line][$v_d]['id_user'] = $v['id_login'];
						$arr[$n_line][$v_d]['data'] = $v_d;
						$arr[$n_line][$v_d]['admin'] = $admin;
						if($arr_wiev == ''){																//Если нет отрезков, то формируем вручную.
							//$arr[$n_line][$v_d]['view'] = JText::_('00-00').'<br/>';						//Формируем вид ячейки.
							//$arr[$n_line][$v_d]['view'] .= JText::_('00-00');
							$arr[$n_line][$v_d]['tooltip'] = JText::_('REG_NO_TIMETABLE_BUT_THERE_ARE_RECORDED');		//Всплывающая подсказка.	
						}
						else{
							//$arr[$n_line][$v_d]['view'] = $this->PreparationTime($arr_wiev['with_schedule']).'<br/>';	//Формируем вид ячейки.
							//$arr[$n_line][$v_d]['view'] .= $this->PreparationTime($arr_wiev['to_schedule']);
							$arr[$n_line][$v_d]['tooltip'] = $this->getTooltipForPatient($list_data_schedules);	
						}																								//Формируем всплывающую подсказку.
						$ArraySchedule = $this->getArraySchedule($v_d, $v['id_login'], $admin);							//Полуаем список временных позиций.
						//Формируем модальное окно.
						$arr[$n_line][$v_d]['modalwindow'] = $this->getDataForModalWindodw($v_d, $list_data_schedules, $ArraySchedule, $admin);
						//Определяем цвет ячейки.
						if(Tinterface::PresenceLineCode($arr[$n_line][$v_d]['modalwindow'], 'MEDICAL_REGISTRY_background_green')){
							$arr[$n_line][$v_d]['class'] = 'MEDICAL_REGISTRY_background_green';							//Есть всободные ячейки.
							$arr[$n_line][$v_d]['view'] = JText::_('REG_FREE');											//Формируем вид ячейки.
						}
						else{
							$arr[$n_line][$v_d]['class'] = 'MEDICAL_REGISTRY_background_red';							//Нет свободных ячеек.
							$arr[$n_line][$v_d]['view'] = JText::_('REG_BUSY');											//Формируем вид ячейки.
						}
					}
					else{																						//Если нет, то формируем соответсвующее данные.
						$arr[$n_line][$v_d]['view'] = '-';
						$arr[$n_line][$v_d]['tooltip'] = JText::_('REG_NO_SCHEDULE');
					}	
				}
				unset($k_d, $v_d);
				$n_line++;
			}
			unset($k, $v);			
			return $arr;	
		}
    }
    
    
    //Метод формирования данных для модального окна.
    function getDataForModalWindodw($data = null, $arr_schedule = null, $ArraySchedule = null, $admin = false){
    	if($data != null && count($ArraySchedule) >= 1){
    		//Фромируем классы ячеек таблицы.
            $class_row0 = 'cat-list-row0';
            $class_row1 = 'cat-list-row1';
    		$arr = $this->DistributionTimeSlices($arr_schedule, $ArraySchedule, $admin);//Получаем рапределенный массив расписаний.
			$ret .= '<div class="module-body">';
			$ret .= '<div class="moduletable">';
			$ret .= '<table class="category" style="width: 100%;">';															//Формируем данные в виде таблицы.
			$ret .= '<thead class="inputbox">';
			$ret .= '<tr><th class="list-title" colspan="2" style="font-size: 120%;">';
			if(count($arr_schedule) >= 1){												//Если нет расписания, то показываем данные с записанных.
				$id = $data.$arr_schedule['0']['id_login_schedule'];					//ИД элемента для вкладок.
				$ret .= $arr_schedule['0']['surname_login'].'<br/>';					//Показываем данные доктора.
				$ret .= $arr_schedule['0']['name_login'].' ';
				$ret .= $arr_schedule['0']['patronymic_login'].'<br/>';
				$ret .= JText::_($arr_schedule['0']['ru_specialty']);
			}
			else{
				$id = $data.$ArraySchedule['0']['id_login'];							//ИД элемента для вкладок.
				$ret .= $ArraySchedule['0']['date']['surname_login'].'<br/>';			//Показываем данные доктора.
				$ret .= $ArraySchedule['0']['date']['name_login'].' ';
				$ret .= $ArraySchedule['0']['date']['patronymic_login'].'<br/>';
				$ret .= JText::_($ArraySchedule['0']['date']['ru_specialty']);
			}
			$ret .= '</th></tr>';
			$ret .= '<tr><th class="list-title" colspan="2" style="font-size: 120%;">';
			$ret .= JText::_('REG_RECORDING_DATE').' - ';
			$ret .= $data;
			$ret .= '</th></tr>';
			$ret .= '<tr><th class="list-title" style="font-size: 120%; width: 50%">';								//Формируем шапку таблици.
			$ret .= JText::_('REG_SCHEDULE');
			$ret .= '</th><th class="list-title" style="font-size: 120%;">';
			$ret .= JText::_('REG_SELECTED_TIME');
			$ret .= '</th></tr>';
			$ret .= '</thead>';
			$ret .= '<tbody class="inputbox">';
			$n = 0;																		//Устанавливаем счетчик ключа массива.
			foreach($arr as $key => $value){											//Обходим массив расписаний.
				if($value['hidden_flag_schedule']){										//Если есть скрытое расписание, то акцентируем его.
					$ret .= '<tr class="'.$class_row0.'"><th class="item-title">';
				}
				else{
					$ret .= '<tr style="border: 3px" class="'.$class_row1.'"><th class="item-title">';
				}
				if($value['with_schedule'] != ''){										//Если нет расписания, то выводим сообщение.
					$ret .= JText::_('REG_BEGINNING').' - ';
					$ret .= '('.$this->PreparationTime($value['with_schedule']).')';	//Формируем строки расписания для просмотра.
					$ret .= '<br/>';
					$ret .= JText::_('REG_THE_END').' - ';
					$ret .= '('.$this->PreparationTime($value['to_schedule']).')';
					$ret .= '<br/>';
					$ret .= JText::_('REG_THE_RECEIVE_LOCATION').':';
					$ret .= '<br/>';
					$ret .= $value['cabinet_schedule'];
				}
				else{
					$ret .= JText::_('REG_NO_SCHEDULE');
				}	
				$ret .= '</th><td>';
				$ret .= $this->getButtonMenu($value['ArraySchedule'], $data, $value['id_login'], $admin);
				$ret .= '</td></tr>';
				$n++;																	//ИНкрементируем счетчик.
			}
			unset( $key, $value);
			$ret .= '</tbody>';
			$ret .= '</table>';
			$ret .= '</div>';
			$ret .= '</div>';
			return $ret;																	//Возвращаем результат.
    	}
    }
    
    
    
    //Метод распределение временных отрезков в соответсвиии с расписанием.
    function DistributionTimeSlices($arr_schedule = null, $ArraySchedule = null, $admin = false){
		if(count($arr_schedule) >= 1 && count($ArraySchedule) >= 1){
			$d = new data();
			foreach($arr_schedule as $k_s => $v_s){											//Обходим расписание.
				$start = $d->conversion_time_data($v_s['with_schedule']);					//Формируем промежутки для сравнения.
				$stop = $d->conversion_time_data($v_s['to_schedule']);
				$ret[$k_s] = $v_s;															//Сохраняем расписание.
				foreach($ArraySchedule as $k_a => $v_a){									//Оюходим временные промежутки.
					$ss = $d->conversion_time_data($v_a['start']);
					if($start <= $ss && $stop >= $ss){										//Если промежуток входит в промежуток, то
						$ret[$k_s]['ArraySchedule'][] = $v_a;								//Формируем массив.
						unset($ArraySchedule[$k_a]);										//Сбрасываем использованный массив.
					}
				}
				unset($k_a, $v_a);															//Сброс переменных
			}
			$n = $k_s;																		//Сохраняем ключ масива.
			unset($k_s, $v_s, $d, $start, $stop, $ss);										//Сброс переменных	
		}
		
		if(count($ArraySchedule) >= 1 && $admin){											//Если остались записанные, и есть режим админа, то дописываем их в массив.
			$n++;
			foreach($ArraySchedule as $k_a => $v_a){										//Обходим записанных.
				$ret[$n]['ArraySchedule'][] = $v_a;											//Дописываем их в массив.
				$ret[$n]['id_login'] = $v_a['date']['id_login'];
			}
			unset($k_a, $v_a);																//Сброс переменных.
		}
		
		return $ret;
    }
    
    
    //Метод кнопочного меню.
    function getButtonMenu($arr = null, $data = null, $id_user = null, $admin = false){
    	if(count($arr) >= 1 && $data != null && $id_user != null){
			foreach($arr as $key => $value){
				$cabinet = $value['cabinet_schedule'];
				if($value['id_record'] != ''){
					if(Rights::getRights('15')){											//Если есть права, то показываем записанного.
						$tooltip = $value['date']['surname']."\n";
			            $tooltip .= $value['date']['name']."\n";
			            if($value['date']['patronymic'] != ''){
							$tooltip .= $value['date']['patronymic']."\n";
			            }
			            $tooltip .= "\n";
			            $tooltip .= JText::_('REG_DATE_OF_BIRTH').' - '.$value['date']['data_of_birth']."\n";
			            $tooltip .= JText::_('REG_COUNTRY').' - '.$value['date']['country']."\n";
			            $tooltip .= JText::_('REG_REGION').' - '.$value['date']['region']."\n";
			            $tooltip .= JText::_('REG_DISTRICT').' - '.$value['date']['district']."\n";
			            if($value['date']['city'] != ''){
							$tooltip .= JText::_('REG_CITY').' - '.$value['date']['city']."\n";
			            }
			            if($value['date']['village'] != ''){
							$tooltip .= JText::_('REG_VILLAGE').' - '.$value['date']['village']."\n";
			            }
			            if($value['date']['street'] != ''){
							$tooltip .= JText::_('REG_STREET_HOUSE').' - '.$value['date']['street']."\n";
			            }
			            $tooltip .= "\n";
			            if($value['date']['phone'] != ''){
							$tooltip .= JText::_('REG_PHONE').' - '.$value['date']['phone']."\n";
			            }
                        if($value['date']['referral'] != ''){
                            $tooltip .= JText::_('REG_ELECTRONIC_REFERRAL').' - '.$value['date']['referral']."\n";
                        }
			            if($value['date']['mail'] != ''){
							$tooltip .= JText::_('REG_EMAIL').' - '.$value['date']['mail']."\n";
			            }
			            $tooltip .= "\n";
			            $tooltip .= JText::_('REG_TYPE_OF_CONSULTATION').' - '.mb_strtolower(JText::_($value['date']['type']))."\n";
			            $tooltip .= JText::_($value['date']['status'])."\n";
			            $tooltip .= JText::_('REG_DESCRIPTION_OF_TARGET_APPOINTMENTS')."\n";
			            $tooltip .= $value['date']['description_recording']."\n";
					}
					else{																	//Если нет, то выводим сообщение о занятоц позиции.
						$tooltip = JText::_('REG_TIME_NOT_FREELY_TT');
					}
					
					if($value['date']['id_status'] >= 3){
						$button = '<input type="text" name="SelectDateTime" value="'.$data.'$$'.$value['start'].'$$'.$id_user.'" style="display: none;">'."\n";
						if($admin == true){
							$button .= '<input onclick="this.form.submit()" type="button" value="'.$this->PreparationTime($value['start']).'" title="'.$tooltip.'" class="MEDICAL_REGISTRY_background_yellow">'."\n";
						}
						else{
							$button .= '<input onclick="alert(\''.JText::_('REG_RECORDING_IS_NOT_POSSIBLE_AS_IT_IS_A_BUSY_TIME_SELECT_ANOTHER_TIME').'\')" type="button" value="'.$this->PreparationTime($value['start']).'"  title="'.$tooltip.'" class="MEDICAL_REGISTRY_background_yellow">'."\n";
						}
					}
					else{
						$button = '<input type="text" name="SelectDateTime" value="'.$data.'$$'.$value['start'].'$$'.$id_user.'" style="display: none;">'."\n";
						if($admin == true){
							$button .= '<input onclick="this.form.submit()" type="button" value="'.$this->PreparationTime($value['start']).'" title="'.$tooltip.'" class="MEDICAL_REGISTRY_background_red">'."\n";
						}
						else{
							$button .= '<input onclick="alert(\''.JText::_('REG_RECORDING_IS_NOT_POSSIBLE_AS_IT_IS_A_BUSY_TIME_SELECT_ANOTHER_TIME').'\')" type="button" value="'.$this->PreparationTime($value['start']).'" title="'.$tooltip.'" class="MEDICAL_REGISTRY_background_red">'."\n";
						}
					}		
				}
				else{
					$button = '<input type="text" name="SelectDateTime" value="'.$data.'$$'.$value['start'].'$$'.$id_user.'" style="display: none;">'."\n";
					if($value['blocked']){
						$button .= '<input onclick="alert(\''.JText::_('REG_THE_RECORDING_IS_TEMPORARILY_BLOCKED_PLEASE_CHOOSE_ANOTHER_TIME').'\')" type="button" value="'.$this->PreparationTime($value['start']).'"  title="'.JText::_('REG_THE_RECORDING_IS_TEMPORARILY_BLOCKED_PLEASE_CHOOSE_ANOTHER_TIME').'" class="MEDICAL_REGISTRY_background_yellow">'."\n";
					}
					else{
						$button .= '<input onclick="this.form.submit()" type="button" value="'.$this->PreparationTime($value['start']).'" title="'.JText::_('REG_TIME_FREELY_TT').''."\n".JText::_('REG_THE_RECEIVE_LOCATION').' - '.$cabinet.'" class="MEDICAL_REGISTRY_background_green">'."\n";	
					}	
				}
				$ret .= '<div style="display: inline-block; vertical-align: top;">'.Tinterface::getUniFormed($button).'</div>';
			}
			unset($key, $value);
			return $ret;
    	}
    }
    
    
    
    //Метод для получения всплывающей подсказки для таблици записи на прием для пациента.
    function getTooltipForPatient($arr = null){
		if(count($arr) >= 1){
			$ret .= "\t".$arr['0']['surname_login']."\n".$arr['0']['name_login'].' '.$arr['0']['patronymic_login']."\n".JText::_($arr['0']['ru_specialty']);
			$ret .= "\n";
			$ret .= "\n";
			$ret .= JText::_('REG_DATE').':'."\n";
			$ret .= $arr['0']['date_schedule'];
			$ret .= "\n";
			$ret .= "\n";
			$ret .= JText::_('REG_BEGINNING')."\t".JText::_('REG_THE_END')."\t".JText::_('REG_THE_RECEIVE_LOCATION');
			$ret .= "\n";
			foreach($arr as $key => $value){
				$ret .= $this->PreparationTime($value['with_schedule']);
				$ret .= "\t";
				$ret .= $this->PreparationTime($value['to_schedule']);
				$ret .= "\t";
				$ret .= $value['cabinet_schedule'];
				$ret .= "\n";
			}
			unset($key, $value);
			return $ret;
		}
    }
    
    
    
    //Метод получения данных для отображения сумарного отрезка времени.
    function getSumTimeSegments($arr = null){
		if(count($arr) >= 1){
			$n = 1;
			foreach($arr as $key => $value){
				if(count($arr) == 1){
					$ret['with_schedule'] = $value['with_schedule'];
					$ret['to_schedule'] = $value['to_schedule'];
					return $ret;
				}
				if($n == 1){
					$ret['with_schedule'] = $value['with_schedule'];
				}
				else{
					$ret['to_schedule'] = $value['to_schedule'];
				}
				$n++;
			}
			unset($key, $value, $n);
			return $ret;
		}
    }
    
    
    
    //Метод получения данных для панели управления таблицой записи на прием.
    //с применением технологии AJAX.
    public function getToolsTable($date = null, $id_specialty = null){
		if($date != null){
			//Готовим данные для AJAX запроса.
    		$url = &JFactory::getURI();
    		$ControlId = 'idTableFormat';
    		$NameModule = true;
    													//Посылаемые данные.
    		$LoadImg = JUri::root(true).'/components/com_medical_registry/assets/images/plex/huge_load_style.GIF';
    		$FailureImg = JUri::root(true).'/components/com_medical_registry/assets/images/plex/huge_failure.GIF';
    		$NullImg = JUri::root(true).'/components/com_medical_registry/assets/images/plex/huge_no_data.GIF';
			
			$id_component = 'id_select_date_table';
			$ret .= '<table class="inputbox" style="width: 100%;">'."\n";
			$ret .= "\t".'<tr>'."\n";
			
			$NamePost = 'cancel_id_specialty';								//Готовим данные для AJAX запроса.
    		$ValuePost = 'C';
			$ret .= "\t"."\t".'<th class="list-title">'."\n";									//Выбор специальности доктора.
			$button = ' <input class="button" onclick="AjaxQuery.AjaxPostAsync(\''.$NamePost.'\',\''.$ValuePost.'\',\''.$ControlId.'\',\''.$NameModule.'\',\''.$url.'\',\''.$LoadImg.'\',\''.$FailureImg.'\',\''.$NullImg.'\');" type="button" name="cancel_id_specialty" style="font-size: 150%;" value="C" title="'.JText::_('REG_RESET_FILTER').'">';
			$m['0'] .= "\t"."\t"."\t".$button."\n";
			$m['1'] .= "\t"."\t"."\t".Tinterface::getUniFormed($this->getFormSpecialty('Appointment', $id_specialty))."\n";
			$ret .= '<div style="display: inline-block;">'.'<div title="'.JText::_('REG_FILTER_SETTING').'" style="display: inline-block;">'.$m['1'].' '.'</div>'.'<div style="display: inline-block;">'.$m['0'].'</div>'."\n";
			$ret .= "\t"."\t".'</th>'."\n";
			
			$NamePost = 'weekdown';											//Готовим данные для AJAX запроса.
    		$ValuePost = '-';
			$ret .= "\t"."\t".'<th class="list-title">'."\n";									//Кнопка переключения недель.
			$ret .= "\t"."\t"."\t".'<input class="button" onclick="AjaxQuery.AjaxPostAsync(\''.$NamePost.'\',\''.$ValuePost.'\',\''.$ControlId.'\',\''.$NameModule.'\',\''.$url.'\',\''.$LoadImg.'\',\''.$FailureImg.'\',\''.$NullImg.'\');" type="button" title="'.JText::_('REG_GO_TO_A_WEEK_AGO').'" name="weekdown" style="font-size: 150%;" value="-">'."\n";
			$ret .= "\t"."\t".'</th>'."\n";
			
			
			
			$NamePost = 'RebootTableFormat';
    		$ValuePost = 'RebootTableFormat';
			$ret .= "\t"."\t".'<th class="list-title" title="'.JText::_('REG_SETTING_THE_DATE').'">'."\n";									//Выбор даты.
			$button = ' <input class="button" onclick="AjaxQuery.AjaxPostAsync(\''.$NamePost.'\',\''.$ValuePost.'\',\''.$ControlId.'\',\''.$NameModule.'\',\''.$url.'\',\''.$LoadImg.'\',\''.$FailureImg.'\',\''.$NullImg.'\');" type="button" title="'.JText::_('REG_REBOOT_TABLE').'" name="reboot" style="font-size: 120%;" value="'.JText::_('REG_REBOOT_TABLE').'">'."\n";
			$ret_b['0'] = "\t"."\t"."\t".$button."\n";
			
			
			
			/*
			
			//При AJAX запросе формируе скрипт собития отображения календаря.
			if(JRequest::getVar('ajax') != ''){
				$ret .= "        <script type=\"text/javascript\" language=\"javascript\">\n";
				$ret .= 'window.addEvent(\'domready\', function() {Calendar.setup({
	                inputField: "'.$id_component.'",          // id of the input field
	                ifFormat: "%Y-%m-%d",        // format of the input field
	                button: "'.$id_component.'_img",          // trigger for the calendar (button ID)
	                align: "Tl",                            // alignment (defaults to "Bl")
	                singleClick: true
	        });});';
				$ret .= "       </script>\n";
			}
			
			*/
			
			
			
			
			//$arr = array('size'=>10, 'onchange'=>'this.form.submit()', 'style'=>"class='inputbox'");
			//$ret_b['1'] .= "\t"."\t"."\t".Tinterface::getUniFormed(JText::_('REG_SELECTED_DATE').'<br/>'.JHTML::_('calendar', $date, 'SelectDate'.'Appointment1', $id_component, '%Y-%m-%d', $arr))."\n";
			
			
			//$ret_b['1'] .= "\t"."\t"."\t".Tinterface::getUniFormed(JText::_('REG_SELECT_DATE').'</br>'.'<input type="text" value="'.$date.'" name="SelectDate'.'Appointment1'.'" id="select-timing-date" onchange=\'this.form.submit()\'  readonly  style="font-size: 150%; cursor: inherit; text-align: center; width: 120px;" title="'.JText::_('REG_SETTING_THE_DATE').'" />')."\n";
			
			
			//$ret_b['1'] .= "\t"."\t"."\t".Tinterface::getUniFormed(JText::_('REG_SELECT_DATE').'</br>'.'<input type="text" value="'.$date.'" name="SelectDate'.'Appointment1'.'" id="select-timing-date" \'  readonly  style="font-size: 150%; cursor: inherit; text-align: center; width: 120px;" title="'.JText::_('REG_SETTING_THE_DATE').'" />')."\n";
			
			
			$ret_b['1'] .= "\t"."\t"."\t".Tinterface::getUniFormed(JText::_('REG_SELECT_DATE').'</br>'.Tinterface::getPluginDate('select-timing-date', 'SelectDateAppointment1', $date));
			//Вводим обработку собитий.
			//$ret_b['1'] .= Tinterface::getEventHandlingMake('select-timing-date', 'focus', 'this.select();lcs(this)');
			//$ret_b['1'] .= Tinterface::getEventHandlingMakeClass('plugin_calendar', 'click', 'this.form.submit()');
			$ret_b['1'] .= Tinterface::getEventHandlingMake('select-timing-date', 'change', 'this.form.submit()');
			
			$ret .= '<div style="display: inline-block;">'.'<div style="display: inline-block;">'.$ret_b['0'].' '.'</div>'.'<div style="display: inline-block;">'.$ret_b['1'].'</div>';
			$ret .= "\t"."\t".'</th>'."\n";
			
			$NamePost = 'weekup';
    		$ValuePost = '+';
			$ret .= "\t"."\t".'<th class="list-title">'."\n";									//Кнопка переключения недель.
			$ret .= "\t"."\t"."\t".' <input class="button" onclick="AjaxQuery.AjaxPostAsync(\''.$NamePost.'\',\''.$ValuePost.'\',\''.$ControlId.'\',\''.$NameModule.'\',\''.$url.'\',\''.$LoadImg.'\',\''.$FailureImg.'\',\''.$NullImg.'\');" type="button" title="'.JText::_('REG_GO_FOR_A_WEEK_AHEAD').'" name="weekup" style="font-size: 150%;" value="+">';
			$ret .= "\t"."\t".'</th>'."\n";
			
			$ret .= "\t".'</tr>'."\n";
			$ret .= '</table>'."\n";
			
			/*
			$language = JFactory::getLanguage();												//Определяем язык сайта.
			$lang = $language->getTag();														//Получаем тег языка.
			$arr_lang = explode('-', $lang);													//Выделяем настройки.
			if($arr_lang['0'] != ''){															//Если язык получен, то	формируем переменную.
				$ll = $arr_lang['0'];
			}
			else{
				$ll = 'ru';																		//Иначе ставим по умолчанию русский.
			}
			
			//Скрипт для плагина выбора даты.
			$ret .= "<script>
				\$.datetimepicker.setLocale('".$ll."');
                \$('#select-timing-date').datetimepicker({
                    format:'Y-m-d',
                    timepicker:false
                });
        			</script>";
			
			*/
			return $ret;
		}
    }
    
    
 
    
    //Метод подготовки значения времени для отображения.
    public function PreparationTime($var = null){
        if($var != null){
            $arr = explode(':', $var);
            $ret = $arr['0'].'-'.$arr['1'];
            return $ret;
        }
    }
    
    
    //Метод получения списка временных позиций для записи на прием на дату и на ИД пользователя..
    public function getArraySchedule($data = null, $id_user = null, $admin = false){
        if($data != null && $id_user != null){                                          //Если полученны все данные, то ведем обработку.
            $d = new data();                                                            //Получаеи объект ДАТА. 
            $Schedule = &$this->getInstance('medical_registryModelSchedule');           //Получаем экземпляр модели с просмотра расписаний. 
            $arrPatientsRecorded = $this->getListPatientsRecorded($data, $id_user);     //Получаем список записанных пациентов.
            $arr_Schedule = $Schedule->getDataSchedule($data, $id_user, $admin);        //Получем список расписаний.
            $arr_BlockList = $this->getBlockList($id_user, $data);						//Получаем список блокировок.
            if(count($arr_Schedule) >= 1){                                              //Если есть элементы массива, то ведем обработку.
                //Формируе массив временных отрезков из расписания.
                foreach($arr_Schedule as $key => $value){                               //Обходим расписание.
                    $time_login = $d->conversion_time_data($value['time_login']);
                    $arr1[$d->conversion_time_data($value['with_schedule'])]['start'] = $d->conversion_time_data($value['with_schedule']);
                    $arr1[$d->conversion_time_data($value['with_schedule'])]['stop'] = $d->conversion_time_data($value['to_schedule']);
                    $arr1[$d->conversion_time_data($value['with_schedule'])]['cabinet_schedule'] = $value['cabinet_schedule'];                   
                }
                unset($key, $value);
                $r = $arr1; 
                $n = 0;                                                             	//Установка счетчика.
                //Создаем массив времени.
                foreach($r as $key => $value){                                      	//Обходим сортированный массив.
                    $start = $value['start'];
                    $stop = $value['stop'];
                    $cabinet = $value['cabinet_schedule']; 
                    while($stop > $start){                                          	//Формируем временную цепочку.
                        $ret[$n]['start'] = $d->conversion_seconds_data($start);
                        $ret[$n]['cabinet_schedule'] = $cabinet;						//Пишем место приема для отображения во всплывающей подсказке.
                        $rr = $n;                                                   	//Запоминаем ключ.
                         if(count($arr_Schedule) >= 1){                             	//Если есть записанные пациенты то ведем обработку. 
                             $start_s = $start + $time_login;
                             $ret[$n]['id_record'] = '';
                             foreach($arrPatientsRecorded as $k => $v){             	//Обходим записанных пацыентов.
                                 $time_record = $d->conversion_time_data($v['time_record']);
                                 if($start == $time_record){                                    //Находим когда равняется, и заменяем.
                                     $ret[$rr]['start'] = $d->conversion_seconds_data($time_record);//Заполняем новую позицию значением времени.
                                     $ret[$rr]['id_record'] = $v['id_record'];                  //Фиксируем идентификатор записи.
                                     $ret[$rr]['date'] = $v;
                                     unset($arrPatientsRecorded[$k]);                           //Удаляем использованую запись.
                                 }
                                 else{                                                          //Иначе добавляем позиции
                                     if(														//Находим записанного входящего в диапазин приема.
                                     $start < $time_record 
                                     && $start_s > $time_record
                                     && $stop > $time_record
                                     ){
                                        $n++;                                                   //Добавлем позицию.  
                                        $ret[$n]['start'] = $d->conversion_seconds_data($time_record);//Заполняем новую позицию значением времени.
                                        $ret[$n]['id_record'] = $v['id_record'];                //Фиксируем идентификатор записи.
                                        $ret[$n]['date'] = $v;
                                        unset($arrPatientsRecorded[$k]);                        //Удаляем использованую запись.
                                     }
                                 }      
                             }
                             unset($k, $v);    
                         }
                         $start = $start + $time_login;                             //Со скважность времены приема.
                         $n++;                                                      //Инкрементируем счетчик.   
                    }
                }
                unset($key, $value, $stop, $start, $r);                             //Освобождаем пямять.
            }
            
            if(count($arrPatientsRecorded) >= 1){                                   //Если остались записанные пациенты 
                if($n == ''){                                                       //Если счетчик пустой, то заполняем его.
                    $n = 0;
                }
                foreach($arrPatientsRecorded as $k_r => $v_r){                      //не входящие в расписание, 
                    $ret[$n]['start'] = $v_r['time_record'];                        //то формируем на позиции.
                    $ret[$n]['id_record'] = $v_r['id_user'];
                    $ret[$n]['date'] = $v_r;
                    $n++;
                }   
                unset($k_r, $v_r);
            }
            //Обработка блокировок.
            if(count($arr_BlockList) >= 1){											//Если есть блокированные записи, то включаем их.
				foreach($ret as $k_r => $v_r){										//Обходим массив и находим какая запись блокированна.
					$start_r = $v_r['start'];
					$start_r = $d->conversion_time_data($start_r);					//Превращаем в секунды для сравнения.
					foreach($arr_BlockList as $k => $v){
						$start_b = $v['time'];
						$start_b = $d->conversion_time_data($start_b);				//Превращаем в секунды для сравнения.
						if($start_r == $start_b){									//Находим нужную позыцыю, и метим ее.
							$ret[$k_r]['blocked'] = true;							//Метка блокированной записи.	
						}
					}
					unset($k, $v);
				}
				unset($k_r, $v_r);
            }
            unset($start_r, $start_b);
            return $ret;
            return Tinterface::SortArray($ret, 'start', 'time');					//Возвращаем сортированный массив. 
        }
    }
    
    
    //Метод получения списка записанных пациентов на неделю и по возможности на ИД пользователя.
    public function getTableWeek($data = null, $id_user = null){
        if($data != null){
            $arr_data = $this->getArrayDate($data);
            $ret .= '<div>';
            foreach($arr_data as $key => $value){
                $ret .= $this->getTableDate($value, $id_user); 
            }
            unset($key, $value);
            $ret .= '</div>';
            return $ret;
        }
    }
    
    
    
    //Метод получения таблици записанных пацинтов на дату и по возможности на ИД пользователя.
    public function getTableDate($data = null, $id_user = null){
        if($data != null){
            $arr = $this->getListPatientsRecorded($data, $id_user);
            if(count($arr) >= 1){
                foreach($arr as $k => $v){                                                  //Удаляем не активированных пациентов.
                    if($v['id_status'] == '3' || $v['id_status'] == '4'){
                        //unset($arr[$k][$v]);
                    }
                    else{
                        $arr1[] = $v;
                    }
                }
                unset($k, $v);
            }
            $arr = $arr1;
            unset($arr1);
            if(count($arr) >= 1){
                if($id_user != null){                                                           //Если установленно ИД пользователя, то пок. соотв. шапку таблици.
                    //Формируем шапку таблии.
                    $table = '<table><tr><th colspan="7">'.JText::_('REG_RECORDED_ON_PATIENTS').' ' .$data.' '.JText::_('REG_FOR_THE_DOCTOR').'<br/> '.mb_strtolower($this->getSpecialty($arr, $id_user)).' '.$this->getUser($arr, $id_user).'</th></tr>';
                    //Формируем названия столбцов таблици.
                    $table .= '<tr><th>'.JText::_('REG_TIME').'</th><th>'.JText::_('REG_A_PATIENT').'</th><th>'.JText::_('REG_DATE_OF_BIRTH').'</th><th>'.JText::_('REG_ADDRESS_OF_RESIDENCE').'</th><th>'.JText::_('REG_CONTACTS').'</th><th>'.JText::_('REG_TYPE_OF_CONSULTATION').'</th><th td style="font-size: 80%">'.JText::_('REG_SHORT_DESCRIPTION_OF_THE_PURPOSE_MAKE_AN_APPOINTMENT').'</th></tr>';
                }
                else{
                    //Формируем шапку таблии.
                    $table = '<table><tr><th colspan="9">'.JText::_('REG_RECORDED_ON_PATIENTS').' ' .$data.'</th></tr>';
                    //Формируем названия столбцов таблици.
                    $table .= '<tr><th>'.JText::_('REG_DOCTOR').'</th><th>'.JText::_('REG_DOCTOR_NAME').'</th><th>'.JText::_('REG_TIME').'</th><th>'.JText::_('REG_A_PATIENT').'</th><th>'.JText::_('REG_DATE_OF_BIRTH').'</th><th>'.JText::_('REG_ADDRESS_OF_RESIDENCE').'</th><th>'.JText::_('REG_CONTACTS').'</th><th>'.JText::_('REG_TYPE_OF_CONSULTATION').'</th><th td style="font-size: 80%">'.JText::_('REG_SHORT_DESCRIPTION_OF_THE_PURPOSE_MAKE_AN_APPOINTMENT').'</th></tr>';    
                }
                $list_Specialty = $this->getListSpecialty($arr);                            //Получаем список специальностей.
                foreach($list_Specialty as $k_s => $v_s){                                   //Обходим специальности.
                    $list_users = $this->getListUsers($arr, $k_s);                          //Получаем список пользователей. 
                    $rowspan_Sp = count($this->getListPatients($arr, $k_s));                //Формируем переменную для таблици специальности.
                    $table .= '<tr>';
                    if($id_user == null){
                         if($rowspan_Sp >= 2){
                             $table .= '<td rowspan="'.$rowspan_Sp.'">';
                             $table .= $v_s;
                             $table .= '</td>';
                         }
                         else{
                             $table .= '<td>';
                             $table .= $v_s;
                             $table .= '</td>';
                         }
                    }
                    foreach($list_users as $k_u => $v_u){                                   //Обходим пользователей.
                        $list_Records = $arr;
                        $rowspan_Ur = count($list_Records);                                 //Формируем переменную для таблицы пользователей.
                        if($id_user == null){
                            if($rowspan_Ur >= 2){
                                $table .= '<td rowspan="'.$rowspan_Ur.'">';
                                $table .= $v_u;
                                $table .= '</td>';
                            }
                            else{
                                $table .= '<td>';
                                $table .= $v_u;
                                $table .= '</td>';
                            }
                        }    
                        foreach($list_Records as $key => $value){                           //Обходим все записанные пациенты.
                            if($id_user != ''){                                             //Если установлен пользователь, то формируем соотв. данные.
                                $table .= '<td>';
                                $table .= $this->PreparationTime($value['time_record']);
                                $table .= '</td>';
                                $table .= '<td>';
                                $table .= $value['surname'].' '.$value['name'].' '.$value['patronymic'];
                                $table .= '</td>';
                                $table .= '<td>';
                                $table .= $value['data_of_birth'];
                                $table .= '</td>';
                                $table .= '<td>';
                                $table .= JText::_('REG_CO').' '.$value['country'];
                                $table .= '<br/>'.JText::_('REG_R').' '.$value['region'];
                                $table .= '<br/>'.JText::_('REG_C').$value['district'];
                                
                                
                                if($value['city'] != ''){
									$table .= '<br/>'.JText::_('REG_D').' '.$value['city'];
                                }
                                
                                if($value['village'] != ''){
									$table .= '<br/>'.JText::_('REG_V').' '.$value['village'];
                                }
                                if($value['street'] != ''){
									$table .= '<br/>'.JText::_('REG_S').' '.$value['street'];
                                }
                                
                                $table .= '</td>';
                                $table .= '<td>';
                                if($value['phone'] != ''){
                                    $table .= $value['phone'];
                                    $table .= '<br/>';  
                                }
                                if($value['referral'] != ''){
                                    $table .= $value['referral'];
                                    $table .= '<br/>';  
                                }
                                if($value['mail'] != ''){
                                    $table .= $value['mail'];
                                }
                                $table .= '</td>';
                                $table .= '<td>';
                                $table .= JText::_($value['type']);
                                $table .= '</td>';
                                $table .= '<td style="font-size: 80%">'; 
                                $table .= JText::_($value['description_recording']);
                                $table .= '</td>';
                                $table .= '</tr>';
                            }
                            else{
                                $table .= '<td>';
                                $table .= $this->PreparationTime($value['time_record']);
                                $table .= '</td>';
                                $table .= '<td>';
                                $table .= $value['surname'].'<br/>'.$value['name'].'<br/>'.$value['patronymic'];
                                $table .= '</td>';
                                $table .= '<td>';
                                $table .= $value['data_of_birth'];
                                $table .= '</td>';
                                $table .= '<td>';
                                
								$table .= JText::_('REG_CO').' '.$value['country'];
                               	$table .= '<br/>'.JText::_('REG_R').' '.$value['region'];
                                $table .= '<br/>'.JText::_('REG_C').$value['district'];
                                
                                if($value['city'] != ''){
									$table .= '<br/>'.JText::_('REG_D').' '.$value['city'];
                                }
                                
                                if($value['village'] != ''){
									$table .= '<br/>'.JText::_('REG_V').' '.$value['village'];
                                }
                                if($value['street'] != ''){
									$table .= '<br/>'.JText::_('REG_S').' '.$value['street'];
                                }
                                $table .= '</td>';
                                $table .= '<td>';
                                if($value['phone'] != ''){
                                    $table .= $value['phone'];
                                    $table .= '<br/>';  
                                }
                                if($value['mail'] != ''){
                                    $table .= $value['mail'];
                                }
                                $table .= '</td>';
                                $table .= '<td>';
                                $table .= JText::_($value['type']);
                                $table .= '</td>';
                                $table .= '<td style="font-size: 80%">';
                                $table .= JText::_($value['description_recording']); 
                                $table .= '</td>';
                                $table .= '</tr>';
                            }
                        }
                        unset($key, $value);
                    }
                    unset($k_u, $v_u); 
                }
                unset($k_s, $v_s);
                $table .= '</table>'; 
                return $table;
            }
        }
    }
    
    /*
    //Метод получения отсартированого списка записанных пациентов на дату и на ИД пользователя.
    private function getSortPatients($arr = null, $data = null, $id_user = null){
        if($arr != null && $data != null && $id_user != null){
            if(count($arr) == 1){
                return $arr;
            }
            $d = new data();
            foreach($arr as $key => $value){                                    //Обходим все денные.
                if($value['id_login'] == $id_user){                             //Если ИД соответствует, то формируем массив.                         
                    $key_a = $d->conversion_time_data($value['time_record']);   //Формируем ключ для далнейшей сортировки по ему.
                    $arr1[$key_a] = $value;                                     //Формируем массив.
                }
            }
            unset($key, $value, $key_a);                                        //Обнуление переменных.        
            $arr2 = $arr1;                                                      //Клонируем массив.
            
            return $arr2;
            foreach($arr1 as $k_1 => $v_1){                                     //Обходим два массива с цикле и формируем третий отсортированный.
                $var = $k_1;
                foreach($arr2 as $k_2 => $v_2){
                    if($var >= $k_2){                                           //Находим найменшее значение ключа.
                        $var = $k_2;                                            //Сохраняем это значение ключа.
                    }
                    else{
                        if(count($arr2) == 1){
                            $var = $k_2;
                        }
                    }
                }
                $ret[] = $arr1[$var];                                           //Формируем новый масив. 
                unset($arr2[$var]);                                             //Удаление найменьшего элемента массива.
                unset($k_2, $v_2);                                              //Обнуление переменных.
            }
            unset($k_1, $v_1);                                                  //Обнуление переменных.
            unset($arr2, $arr1);                                                //Обнуление переменных.
            return $ret;                                                        //Возвращаем сортированный массив.
        }
    }
    */
    
    //Метод получения списка записанных пациентов на специальность.
    private function getListPatients($arr = null, $id_specialty = null){
        if($arr != '' && $id_specialty != null){
            foreach($arr as $key => $value){
                if($value['id_specialty_login'] == $id_specialty){
                    $ret[] = $value;
                }
            }
            unset($key, $value);
            return $ret;
        }
    }
    
    
    //Метод получения списка специальностей врачей в которых есть записенные пациенты.
    private function getListSpecialty($arr = null){
        if($arr != ''){
            foreach($arr as $key => $value){
                $ret[$value['id_specialty_login']] = JText::_($value['ru_specialty']);
            }
            unset($key, $value);
            return $ret;
        }
    }
    
    
    //Метод получения списка пользователей на специальность, на которых есть записанные пациенты.
    private function getListUsers($arr = null, $id_specialty = null){
        if($arr != null && $id_specialty != null){
            foreach($arr as $key => $value){
                if($id_specialty == $value['id_specialty_login']){
                    $ret[$value['id_login']] = $value['surname_login'].'<br/>'.$value['name_login'].'<br/>'.$value['patronymic_login'];   
                }
            }
            unset($key, $value);
            return $ret;
        }
    }
    
    
    //Метод получения пользователя по его ИД.
    private function getUser($arr = null, $id_user = null){
        if($arr != null && $id_user != null){
            foreach($arr as $key => $value){
                if($id_user == $value['id_login']){
                    $ret = $value['surname_login'].' '.$value['name_login'].' '.$value['patronymic_login'];
                    return $ret;
                }
            }
            unset($key, $value);
        }
    }
    
    
    //Метод получения типа приема по ИД
    public function getTypeReception($id = null){
        if($id != null){
            $objRegistry_Management = $this->getInstance('medical_registryModelRegistry_Management');                         //Получаем объкет модели расписаний.
            $arr =  $objRegistry_Management->getAllRecord('registry_type');
            foreach($arr as $k => $v){
                if($v['id_type'] == $id){
                    return mb_strtolower(JText::_($v['type']));
                }
            }
            unset($k, $v);  
        }
    }
    
    
    
    //Метод получения специальности по ИД пользователя.
    private function getSpecialty($arr = null, $id_user = null){
        if($arr != null && $id_user != null){
            foreach($arr as $key => $value){
                if($id_user == $value['id_login']){
                    $ret = JText::_($value['ru_specialty']);
                    return $ret;
                }
            }
            unset($key, $value);
        }
    }
    
    //Метод получения меню в панель инструментов.
    public function getMenuTools(){
        $ret .= '<input type="submit" class="button" title="'.JText::_('REG_THE_MAIN_SERVICE_MENU_WHICH_YOU_SAW_AT_THE_SERVICE_ENTRANCE_TT').'" value="'.JText::_('REG_MAIN_MENU').'" name="ManeMenu">'."\n";
       $ret .= '<input type="submit" class="button" title="'.JText::_('REG_SUGGESTIONS_AND_COMMENTS_TO_THE_DEVELOPERS').'" value="'.JText::_('REG_REVIEWS').'" name="reviews">'."\n";
        return $ret;
    }
    
    //Метод получения данных для панели управления зарегистрированным пациентом.
    public function getButtonPatient(){
        return '<input type="submit" class="button" value="'.JText::_('REG_CHANGE_DATA').'" name="Registration">'."\n";  
    }
    
    
    //Метод подготовки меню для выбора расписаний на дату
    public function getMenuSchedule($SelectDate = null, $ScheduleWeek = null, $prefix){
        if($SelectDate != null){
            $ret .= "\t".'<div>'."\n";  
            $ret .= "\t"."\t".'<div>'."\n"; 
            $ret .= "\t"."\t"."\t".JText::_('REG_SELECTED_DATE')."\n";
            $ret .= "\t"."\t".'</div>'."\n";
            $ret .= "\t"."\t".'<div>'."\n"; ;
            $ret .= "\t"."\t"."\t".'<b>'.$SelectDate.'</b>'."\n";
            $ret .= "\t"."\t".'</div>'."\n";
            $ret .= "\t"."\t".'<div>'."\n";
            if($ScheduleWeek != null){
                $ret .= "\t"."\t"."\t".'<input type="submit" class="button" title="'.JText::_('REG_GET_THE_SCHEDULE_FOR_THE_SELECTED_DATE_TT').'" value="'.JText::_('REG_THE_SCHEDULE_FOR_THE_DATES').'" name="ScheduleDate'.$prefix.'">'."\n"; 
            }
            else{
                $ret .= "\t"."\t"."\t".'<input type="submit" class="button" title="'.JText::_('REG_GET_THE_SCHEDULE_FOR_THE_WEEK_ACCORDING_TO_THE_SELECTED_DATE_TT').'" value="'.JText::_('REG_THE_SCHEDULE_FOR_THE_WEEK').'" name="ScheduleWeek'.$prefix.'">'."\n"; 
            }  
            $ret .= "\t"."\t".'</div>'."\n";
            $ret .= "\t".'</div>'."\n";  
        }  
        return $ret;
    }
    
    
    //Метод подготовки кнопки для печати.
    public function getButtonPrint($id = null){
        if($id != null){
            return '<input type="button" class="button" onclick="printDiv(\''.$id.'\');" value="'.JText::_( 'REG_PRINT').'" />';   
        }
    }
    
    
    
    //Метод получения объекта формы.
    public function getForm($nameTable = null, $nameXML = null, $table = null){
        if($nameTable != null && $nameXML != null){
            $pathToMyXMLFile = JPATH_COMPONENT.DS.'models'.DS.'forms'.DS.$nameXML.'.xml'; 
            $form = &JForm::getInstance($nameTable, $pathToMyXMLFile);
            $form = medical_registryModelRegistry_Management::setFieldsForm($form, $table);                //Заполяем форму.
            return $form;
        }
            
    }
    
    
    //Метод получения панели данных для панели записи на прием.
    public function getToolbarAppointmentRecording($SelectDateTime = null){
        $arr = explode('$$', $SelectDateTime);
        if($arr[0] != '' && $arr['1']){
            $ret .= JText::_( 'REG_SELECTED_DATE').' - '.$arr[0].'<br/>';
            $ret .= JText::_( 'REG_THE_SELECTED_TIME').' - '.$this->PreparationTime($arr[1]).'<br/>';
            $ret .= '<input type="submit" class="button" value="'.JText::_( 'REG_MAKE_AN_APPOINTMENT').'" name="MakeAppointment">'."\n";
            return $ret; 
        }
    }
    
    
    //Метод удаления устаревших не активных записей.
    public function DeletingObsoleteRecords(){
        $d = new data();
        $db = & JFactory::getDbo();
        $nameTable1 = $db->nameQuote('#__registry_record');
        
        $query = 'SELECT
                    *
                    FROM 
                    '.$nameTable1.'
                     WHERE
                     id_status = \'3\' AND
                     DATE(data_create) < \''.$d->data_i.'\'';
        $db->setQuery($query);
        $arr = $db->loadAssocList();
        if(count($arr) >= 1){                                                       //Если есть не активние записи, от записывам их в лог.
            $log =  &JLog::getInstance('Info_Medical_Registry.'.date('Y_m_d').'.log.php'); 
            foreach($arr as $key => $value){
                Tinterface::LogEvents($log, 'DELETE RECORDS:', 'Deleted an inactive record.', $value);             //Записываем событие.
            }
            unset($key, $value, $arr);
        }
               
        $query = 'DELETE
                    FROM 
                    '.$nameTable1.'
                     WHERE
                     id_status = \'3\' AND
                     DATE(data_create) < \''.$d->data_i.'\'';
        $db->setQuery($query);
        $db->execute();
    }
    
    
    //Метод активации записи на прием.
    public function ActivationRecord($idu = null, $date = null, $time = null, $mail = null){
        if(
        $idu != null && $date != null && $time != null && $mail != null &&
        Tinterface::Validation($idu, true) && Tinterface::Validation($date, true) && Tinterface::Validation($time, true)
        ){
            $d = new data();
            $LogInfo = &JLog::getInstance('Info_Medical_Registry.'.date('Y_m_d').'.log.php');
            $LogErrors = &JLog::getInstance('Error_Medical_Registry.'.date('Y_m_d').'.log.php');
            
            $time = $d->conversion_seconds_data($time);
            $db = & JFactory::getDbo();
            $nameTable1 = $db->nameQuote('#__registry_record');
            $query = 'SELECT
                        *
                        FROM 
                        '.$nameTable1.'
                         WHERE
                         id_user = \''.$idu.'\' AND
                         data_record = \''.$date.'\' AND
                         time_record = \''.$time.'\'';
            $db->setQuery($query);
            $arr = $db->loadAssocList();                                                            //Формируем массив записей записанных пацентов.
            if(count($arr) == 0){                                                                   //Проверка на наличие записи.
                JError::raiseWarning( 100, JText::_( 'REG_ATTENTION_YOU_ACTIVATE_A_NON-EXISTENT_RECORD'));
                Tinterface::LogEvents($LogErrors, 'ACTIVATION:', 'Attention! You activate a non-existent record!'); 
                return false;
            }
            else{
                if(md5($arr['0']['mail']) != $mail){
                    JError::raiseWarning( 100, JText::_( 'REG_ATTENTION_ACTIVATION_ERROR_CHECK_THE_LINK'));
                     Tinterface::LogEvents($LogErrors, 'ACTIVATION:', 'Attention! Activation error! Check the links'); 
                    return false;
                }
                $query = 'UPDATE 
                        '.$nameTable1.'
                         SET
                         id_status = \'2\'
                         WHERE
                         id_status = \'3\' AND
                         id_user = \''.$idu.'\' AND
                         data_record = \''.$date.'\' AND
                         time_record = \''.$time.'\'';         
                $db->setQuery($query);
                $db->execute();                                                                     //Выполняем смену статуса записи.
                $App = &JFactory::getApplication();
                $App->enqueueMessage(JText::_( 'REG_ACTIVATED_RECORDING_DONE'));                    //Выводим сообщение.   
                Tinterface::LogEvents($LogInfo, 'ACTIVATION:', 'Activation of an appointment record is made');  
                $patient = $this->DataRecordedPatient($idu, $date , $time);                         //Получаем данные записанного пациента.
                $this->PatientMessage($patient, JText::_( 'REG_ACTIVATED_RECORDING_DONE'), true);   //Отправляем сообщение о записи на прием.
                return true;
            }
        }
        return false;
    }
    
    
    //Метод получения данных записанного пациента.
    public function DataRecordedPatient($idu = null, $date = null, $time = null){
        if($idu != null && $date != null && $time != null){
            $arr_schedule = $this->getScheduleArray($date, $time, $idu);                //Массив расписания для пациента.
            $arr = $this->getListPatientsRecorded($date, $idu);                         //Массив записанных пациентов.
            if(count($arr) != 0){
                $d = new data();
                foreach($arr as $key => $value){                                        //Обходим массив записанных пациентов.
                    if($value['time_record'] == $time){                                 //Находим записанного пациента.
                        $ret = $value;                                                  //Сохраняем данные.
                        if(count($arr_schedule) >= 0){
                            foreach($arr_schedule as $k => $v){                         //Обходим массив расписания.
                                $ret[$k] = $v;                                          //Дописываем данные.
                            }
                            unset($k, $v);
                        }
                        return $ret;                                                    //Возвращаем результат.
                    }
                }
                unset($key, $value);
                return false;
            }
        }
    }
    
    
    //Метод отправки оповещения пациента.
    public function PatientMessage($value = null, $message = null, $record = false){
        if($value != null && $message != null){         								//Проверка на предмет наличия необходимого.
            $app = &JFactory::getApplication();
$body .= $value['surname'].'
'.$value['name'].'
'.$value['patronymic'].'

';
//$sms .= $value['surname'].' '.$value['name'].' '.$value['patronymic'].' '; 
$body .= JText::_('REG_DATE_OF_BIRTH').' - '.$value['data_of_birth'].'

';
$body .= JText::_('REG_PLACE_OF_RESIDENCE').'
';
$body .= JText::_('REG_COUNTRY').' - '.$value['country'].'
';  
$body .= JText::_('REG_REGION').' - '.$value['region'].'
'; 
$body .= JText::_('REG_DISTRICT').' - '.$value['district'].'
';
if($value['city'] != ''){
$body .= JText::_('REG_CITY').' - '.$value['city'].'
'; 
}
if($value['village'] != ''){
$body .= JText::_('REG_VILLAGE').' - '.$value['village'].'
'; 
}
if($value['street'] != ''){
$body .= JText::_('REG_STREET_HOUSE').' - '.$value['street'].'
'; 
}
$body .= '
';
$body .= $message.'
';
//$sms .= ' '.$message;
$sms .= $message;
if($record == true){                                //Если есть запись, то соощаем.
$body .= JText::_('REG_YOU_MAKE_AN_APPOINTMENT_WITH_THE_DOCTOR').'
';
//$sms .= ' '.JText::_('REG_YOU_MAKE_AN_APPOINTMENT_WITH_THE_DOCTOR');
$body .= mb_strtolower(JText::_($value['ru_specialty'])).'
';
$body .= $value['surname_login'].'
'.$value['name_login'].'
'.$value['patronymic_login'].'

';
$sms .= ' '.$value['surname_login'].'.';
if($value['id_type'] == '3'){                       //Если есть запись на скайп, то формируем соответвстующее сообщени.
$body .= JText::_('REG_YOU_SHOULD_BE_AT_THE_COMPUTER').'
';
$sms .= ' '.JText::_('REG_YOU_SHOULD_BE_AT_THE_COMPUTER');
$body .= JText::_('REG_NUMBER').' - '.$value['data_record'].',
';
$sms .= ' (d)'.$value['data_record'];
$body .= JText::_('REG_AT').' - '. medical_registryModelAppointment_doctor::PreparationTime($value['time_record']).',
';
$sms .= ' (t)'. medical_registryModelAppointment_doctor::PreparationTime($value['time_record']);
$body .= JText::_('REG_SKYPE_FOR_COMMUNICATION').' - '.$value['skype_login'].'

';
$sms .= ' '.JText::_('REG_SKYPE_FOR_COMMUNICATION').' - '.$value['skype_login'];   
}
else{
$body .= JText::_('REG_YOU_WILL_BE_REQUIRED_TO_PROCEED').'
';
//$sms .= ' '.JText::_('REG_YOU_WILL_BE_REQUIRED_TO_PROCEED'); 
$body .= $app->getCfg('sitename').',
';
//$sms .= ' '.JText::_('REG_YOU_WILL_BE_REQUIRED_TO_PROCEED');
$body .= JText::_('REG_NUMBER').' - '.$value['data_record'].',
';
$sms .= ' (d)'.$value['data_record'];
//$sms .= ' '.JText::_('REG_DATES_AND_TIMES').' - '.$value['data_record'];
$body .= JText::_('REG_AT').' - '.medical_registryModelAppointment_doctor::PreparationTime($value['time_record']).',
';
$sms .= ' (t)'.medical_registryModelAppointment_doctor::PreparationTime($value['time_record']);
//$sms .= ', ('.medical_registryModelAppointment_doctor::PreparationTime($value['time_record']).').';
$body .= JText::_('REG_THE_RECEIVE_LOCATION').' - '.$value['cabinet_schedule'].'.

';    
}                       

}                         
$body .= JText::_('REG_TYPE_OF_CONSULTATION').' - '.mb_strtolower(JText::_($value['type'])).'
';
$body .= JText::_('REG_DESCRIPTION_OF_TARGET_APPOINTMENTS').'
';
//$sms .= ' '.JText::_('REG_DESCRIPTION_OF_TARGET_APPOINTMENTS');
$body .= $value['description_recording'];
            $this->sendPersonalMessage($body, $value, $message, $sms);				//Отправка письма.
        }                 
    }
    
    
    // Метод кодирует строку для URL 
	public function UrlEncode($s){
        $arr =  array (' '=> '%20', 'а'=>'%D0%B0', 'А'=>'%D0%90','б'=>'%D0%B1', 'Б'=>'%D0%91',
        'в'=>'%D0%B2', 'В'=>'%D0%92', 'г'=>'%D0%B3', 'Г'=>'%D0%93', 'д'=>'%D0%B4', 'Д'=>'%D0%94',
        'е'=>'%D0%B5', 'Е'=>'%D0%95', 'ё'=>'%D1%91', 'Ё'=>'%D0%81', 'ж'=>'%D0%B6', 'Ж'=>'%D0%96',
        'з'=>'%D0%B7', 'З'=>'%D0%97', 'и'=>'%D0%B8', 'И'=>'%D0%98', 'й'=>'%D0%B9', 'Й'=>'%D0%99',
        'к'=>'%D0%BA', 'К'=>'%D0%9A', 'л'=>'%D0%BB', 'Л'=>'%D0%9B', 'м'=>'%D0%BC', 'М'=>'%D0%9C',
        'н'=>'%D0%BD', 'Н'=>'%D0%9D', 'о'=>'%D0%BE', 'О'=>'%D0%9E', 'п'=>'%D0%BF', 'П'=>'%D0%9F',
        'р'=>'%D1%80', 'Р'=>'%D0%A0', 'с'=>'%D1%81', 'С'=>'%D0%A1', 'т'=>'%D1%82', 'Т'=>'%D0%A2',
        'у'=>'%D1%83', 'У'=>'%D0%A3', 'ф'=>'%D1%84', 'Ф'=>'%D0%A4', 'х'=>'%D1%85', 'Х'=>'%D0%A5',
        'ц'=>'%D1%86', 'Ц'=>'%D0%A6', 'ч'=>'%D1%87', 'Ч'=>'%D0%A7', 'ш'=>'%D1%88', 'Ш'=>'%D0%A8',
        'щ'=>'%D1%89', 'Щ'=>'%D0%A9', 'ъ'=>'%D1%8A', 'Ъ'=>'%D0%AA', 'ы'=>'%D1%8B', 'Ы'=>'%D0%AB',
        'ь'=>'%D1%8C', 'Ь'=>'%D0%AC', 'э'=>'%D1%8D', 'Э'=>'%D0%AD', 'ю'=>'%D1%8E', 'Ю'=>'%D0%AE',
        'я'=>'%D1%8F', 'Я'=>'%D0%AF');
        $s = strtr($s, $arr);
        //$s= strtr ($s, array (" "=> "%20", "а"=>"%D0%B0", "А"=>"%D0%90","б"=>"%D0%B1", "Б"=>"%D0%91", "в"=>"%D0%B2", "В"=>"%D0%92", "г"=>"%D0%B3", "Г"=>"%D0%93", "д"=>"%D0%B4", "Д"=>"%D0%94", "е"=>"%D0%B5", "Е"=>"%D0%95", "ё"=>"%D1%91", "Ё"=>"%D0%81", "ж"=>"%D0%B6", "Ж"=>"%D0%96", "з"=>"%D0%B7", "З"=>"%D0%97", "и"=>"%D0%B8", "И"=>"%D0%98", "й"=>"%D0%B9", "Й"=>"%D0%99", "к"=>"%D0%BA", "К"=>"%D0%9A", "л"=>"%D0%BB", "Л"=>"%D0%9B", "м"=>"%D0%BC", "М"=>"%D0%9C", "н"=>"%D0%BD", "Н"=>"%D0%9D", "о"=>"%D0%BE", "О"=>"%D0%9E", "п"=>"%D0%BF", "П"=>"%D0%9F", "р"=>"%D1%80", "Р"=>"%D0%A0", "с"=>"%D1%81", "С"=>"%D0%A1", "т"=>"%D1%82", "Т"=>"%D0%A2", "у"=>"%D1%83", "У"=>"%D0%A3", "ф"=>"%D1%84", "Ф"=>"%D0%A4", "х"=>"%D1%85", "Х"=>"%D0%A5", "ц"=>"%D1%86", "Ц"=>"%D0%A6", "ч"=>"%D1%87", "Ч"=>"%D0%A7", "ш"=>"%D1%88", "Ш"=>"%D0%A8", "щ"=>"%D1%89", "Щ"=>"%D0%A9", "ъ"=>"%D1%8A", "Ъ"=>"%D0%AA", "ы"=>"%D1%8B", "Ы"=>"%D0%AB", "ь"=>"%D1%8C", "Ь"=>"%D0%AC", "э"=>"%D1%8D", "Э"=>"%D0%AD", "ю"=>"%D1%8E", "Ю"=>"%D0%AE", "я"=>"%D1%8F", "Я"=>"%D0%AF"));
		return $s; 
	}
    
    
    
    //Метод отправки СМС сообщения.
    public function sendSMS($phone = null, $message = null){
		if($phone != null && $message != null){									//Проверка наличия необходимого.
			require_once (JPATH_COMPONENT.DS.'ini'.DS.'sms.php');				//Подключаем конфиг.
			if($smssend){														//Проверка флага отправки.
				//Формируем СМС.
				$site = $url.'/index.php/conect/sendsms$$$$'.md5($key).'$$$$'.$phone.'$$$$'.$message;
				$site = $this->UrlEncode($site);								//Кодируем ссылку.
				$snoopy = new Snoopy;											//Используем библиотеку.
				$snoopy->fetchlinks($site);										//Отправляем СМС.
				if($snoopy->results){											//Если СМС принята, то пишем в лог.
					$LogInfo = &JLog::getInstance('Info_Medical_Registry.'.date('Y_m_d').'.log.php');
					Tinterface::LogEvents($LogInfo, 'SMS:', 'SMS sent! Phone: '.$phone.'. Body message - "'.$message.'"');
				}
				else{															//Если СМС не принята, пишем с лог.
					$LogErrors = &JLog::getInstance('Error_Medical_Registry.'.date('Y_m_d').'.log.php');
					Tinterface::LogEvents($LogErrors, 'SMS:', 'Error sending SMS alerts! Phone: '.$phone.'. Body message - "'.$message.'"');
				}
			}
		}
    }
    
    
    
        
    //Метод отправки личного сообщения пациенту.
    public function sendPersonalMessage($message = null, $arr_patient = null, $subject = null, $sms = null){
    	$session = &JFactory::getSession();
        $arr = $session->get('Medical_Registry_id');
        $user = $arr['0'];														//Получаем данные текущего пользователя.
		
		if($arr_patient['phone'] != ''){										//Проверка наличия телефона.
			if($sms == null){													//Проверка наличия СМС.
				$sms = $message;												//Используем сообщение.
			}
			$this->sendSMS($arr_patient['phone'], $sms);						//Отправляем СМС.
		}
		
		
		if(																		//Проверка наличия необходимого.
			$message != null 
			&& count($arr_patient) >= 2
			&& $arr_patient['mail'] != ''
		){
			$LogInfo = &JLog::getInstance('Info_Medical_Registry.'.date('Y_m_d').'.log.php');
            $LogErrors = &JLog::getInstance('Error_Medical_Registry.'.date('Y_m_d').'.log.php');
            $app = &JFactory::getApplication();
            $mailer = &JFactory::getMailer();                                   //Создаем ссылку на объект отправки почты.
            $recipient = $arr_patient['mail'];
            $mailer->addRecipient($recipient);
            if($subject == null){												//Если нет темы, то подставляем название сайта.
				$subject = $app->getCfg('sitename');
            }
            $mailer->setSubject($subject);
	            
$body .= '
';
$body .= $message.'

';
if(count($user) >= 3){										//Если зарегистрирован пользователь, то формируем сообщение от его лица.
$body .= JText::_('REG_SINCERELY').'
		';
		$body .= $user['surname_login'].'
		'.$user['name_login'].' '.$user['patronymic_login'].'

';
$body .= 'mailto:'.$user['post_login'];
}				
			
			$mailer->setBody($body);                        //Отправка письма.
            $send = &$mailer->Send();
            
            if ($send !== true){                            //При удачной отправке выводим сообщение.
                JError::raiseWarning( 100, JText::_('REG_ERROR_SENDING_MAIL_ALERTS').$send->message);
                Tinterface::LogEvents($LogErrors, 'MESSAGE:', 'Error sending mail alerts! Mail - '.$recipient.'. Body message - "'.$body.'"', $user);
            }
            else{
                $app->enqueueMessage(JText::_('REG_THE_LETTER_SENT_TO_THE_ALERT'));
                Tinterface::LogEvents($LogInfo, 'MESSAGE:', 'The letter with the notification of departure! Mail - '.$recipient.'. Body message - '.$body.'"', $user); 
            }
		}
    }
    
    
    
    //Метод валидации kcaptcha
    public function ValidationKcaptcha($Kcaptcha = null, $message = false){
        return Tinterface::ValidationKcaptcha($Kcaptcha, $message);
    }
    
    
    
    //Метод валидации данных вводимых пользователем
    public function ValidationDataUser($arr = null, $Massage = false, $admin = false){
		if($admin == false){																								//Для администраторов, не проводим валидацию.
			return true;
		}
		if(count($arr) >= 2){
			$d = new data();
			if($d->comparison_date($d->data_i, $arr['data_of_birth'])){
				if($Massage){
                    JError::raiseWarning( 100, JText::_('REG_THE_START_DATE_OR_TIME_CANNOT_BE_OLDER_THAN_END_DATE_OR_TIME'));                        //Формируем сообщение
                    Tinterface::LogEvents($LogErrors, 'VALIDATION APPOINTMENTS:', 'REG_THE_START_DATE_OR_TIME_CANNOT_BE_OLDER_THAN_END_DATE_OR_TIME', $arr); 
                }
                return false;
			}
			$arr_d = explode('-', $d->data_i);
			$arr_d[0] = $arr_d[0] - 120;																					//Получаем дату на 120 позже.
			$data_v = implode('-', $arr_d);
			if($d->comparison_date($arr['data_of_birth'], $data_v)){
				if($Massage){
                    JError::raiseWarning( 100, JText::_('Пациенту не может быть больше 120 лет!'));                        //Формируем сообщение
                    Tinterface::LogEvents($LogErrors, 'VALIDATION APPOINTMENTS:', 'Пациенту не может быть больше 120 лет!', $arr); 
                }
                return false;
			}
			return true;
		}
    }
     
    
    
    
    //Метод валидации записи на прием.
    /*
    $id_user - ИДпользователя для поиска записанных.
    $arr - массив аналитических данных для оповещения и логирования.
    $SelectDateTime - массив выбраной даты и времени.
    $Massage - флаг оповещения.
    $Transfer - флаг переноса.
    $admin - флаг панели администрирования.
    */
    public function ValidationReceptionRecord($id_user = null, $SelectDateTime = null, $arr = null, $Massage = false, $Transfer = false, $admin = false){
        if(
        //$id_user != null 
        //&& 
        $SelectDateTime != null
        ){
            $temp = explode('$$', $SelectDateTime);
            $date = $temp['0'];                                                                             //Получаем дату.
            $Time = $temp['1'];                                                                             //Получаем время. 
            $d = new data();
            
            $db = & JFactory::getDbo();
			$query = 'SELECT CURTIME();';
			$db->setQuery($query);
			$arr_time = $db->loadAssocList();
	        $Time_new = $arr_time['0']['CURTIME()'];														//Получение времени с MySql.
	        unset($db, $arr_time, $query);																	//Сброс переменных.
            $LogInfo = &JLog::getInstance('Info_Medical_Registry.'.date('Y_m_d').'.log.php');
            $LogErrors = &JLog::getInstance('Error_Medical_Registry.'.date('Y_m_d').'.log.php');
            
            if(																							//Валидациа даты рождения.
            $arr['data_of_birth'] != ''																	//Если есть дата рождения
            && $this->ValidationDataUser($arr, $Massage, $admin) == false								//Проверка даты рождения.
            ){
				return false;
            }
            
            if($d->comparison_date($date, $d->data_i)){                                                    //Проверка на дату записи.
                if($Massage == true){
                    JError::raiseWarning( 100, JText::_('REG_THE_DATE_MAY_NOT_BE_EARLIER_THAN_THE_CURRENT'));                        //Формируем сообщение
                    Tinterface::LogEvents($LogErrors, 'VALIDATION APPOINTMENTS:', 'The date may not be earlier than the current', $arr); 
                }
                return false;
            }
            if(                                                                                             //Проверка на время записи.
            ($date == $d->data_i) && 
            ($d->conversion_time_data($Time_new) > $d->conversion_time_data($Time))
            ){
                if($Massage == true){
                    Tinterface::LogEvents($LogErrors, 'VALIDATION APPOINTMENTS:', 'The time can not be earlier than the current', $arr); 
                    JError::raiseWarning( 100, JText::_('REG_THE_TIME_CAN_NOT_BE_EARLIER_THAN_THE_CURRENT'));//Формируем сообщение
                }
                return false;
            }
            $list_patient = $this->getListPatientsRecorded($date);                                          //Получаем список записанных пациентов.  
            if(count($list_patient) >= 1){
                foreach($list_patient as $key => $value){                                                   //Обходим список записанных.
                    if(is_array($arr) && $Transfer == false){                                               //Если есть записанные и на включен перенос записи.
                        if($arr['mail'] == $value['mail'] && $admin == false){                              //Проверка на наличие двойника.
                            if($Massage == true){
                                Tinterface::LogEvents($LogErrors, 'VALIDATION APPOINTMENTS:', 'Recording is not possible because you have already been recorded on this day', $arr); 
                                JError::raiseWarning( 100, JText::_('REG_RECORDING_IS_NOT_POSSIBLE_BECAUSE_YOU_HAVE_ALREADY_BEEN_RECORDED_ON_THIS_DAY'));//Формируем сообщение
                            }
                            return false;
                        }
                    }                                                                                       
                    if($value['time_record'] == $Time && $value['id_user'] == $id_user){                    //Проверка на наличие записанного пациент.                                                                         
                        if($Massage == true){
                            Tinterface::LogEvents($LogErrors, 'VALIDATION APPOINTMENTS:', 'Recording is not possible as it is a busy time. Select another time', $arr);  
                            JError::raiseWarning( 100, JText::_('REG_RECORDING_IS_NOT_POSSIBLE_AS_IT_IS_A_BUSY_TIME_SELECT_ANOTHER_TIME'));//Формируем сообщение
                        }
                        return false;
                    }
                }
                unset($key, $value);
            }
        }
        return true;
    }
    
    
    //Метод записи на прием.
    public function MakingAppointment($id_user = null, $SelectDateTime = null, $arr = null, $flag = false){
        if(
        $id_user != null && $SelectDateTime != null &&
        $this->ValidationReceptionRecord($id_user, $SelectDateTime, $arr, true) &&                          //Проводим валидацию.
        is_array($arr)
        ){
            $temp = explode('$$', $SelectDateTime);
            $date = $temp['0'];                                                                             //Получаем дату.
            $time = $temp['1'];
            $d = new data();
            $arr['id_user'] = $id_user;
            
            if($flag == true){                                                                              //Если записан докторм, то устанавливаем соответствующий статус.
                $arr['id_status'] = '1';
            }
            else{
                $arr['id_status'] = '3';
            }
            $arr['data_record'] = $date;                                                                    //Получаем другие параметры.
            $arr['time_record'] = $time;
            medical_registryModelRegistry_Management::setData($arr, '', 'Record');                          //Записываем данные.
            if($arr['mail']){																				//Если есть почта, то формируем данные.
                $url =  JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Appointment_doctorTask&u='.$id_user, true, '2');
                $url = JRoute::_($url.'&d='.$date);
                $url = JRoute::_($url.'&t='.$d->conversion_time_data($time));
                $url = JRoute::_($url.'&m='.md5($arr['mail']));
                $subject = JText::_('REG_ACTIVATING_AN_APPOINTMENT_RECORD');
                //$sms = $subject.' '.$url;	
                $sms = JText::_('REG_ACTIVATING_AN_APPOINTMENT_RECORD').'. '.JText::_('REG_THE_LETTER_SENT_TO_THE_ALERT');																		//Формируем СМС сообщение.
                //$sms = preg_replace('http://','', $sms);											
                //$sms = preg_replace('https://','', $sms);
$body .= $arr['surname'].'
'.$arr['name'].'
'.$arr['patronymic'].'
                
';
$body .= JText::_('REG_TO_ACTIVATE_AN_APPOINTMENT').'
';
$body .= JText::_('REG_YOU_NEED_TO_CLICK_ON_THIS_LINK').'
';
$body .= $url;
				$this->sendPersonalMessage($body, $arr, $subject, $sms);											//Отправка сообщения.
            }    
        }
    } 
    
    
    
    
}