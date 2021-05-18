<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
/**
 * Model Registry_Management
 * @author Олег Борисович Дубик
 * load16@rambler.ru
 */
 
 /**
  * Модель задачи управления регистратурой.
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
class medical_registryModelRegistry_Management extends JModelItem{
    
   
    //Метод получения пользователей на специальность.
    public function getListUser($id_specialty = null){
        if($id_specialty != null){
            $db = & JFactory::getDbo();
            $nameTable1 = $db->nameQuote('#__registry_login');
            $nameTable2 = $db->nameQuote('#__registry_specialty');    
            $query = 'SELECT
                    '.$nameTable1.'.id_login,
                    '.$nameTable1.'.surname_login,
                    '.$nameTable1.'.name_login,
                    '.$nameTable1.'.patronymic_login,
                    '.$nameTable1.'.time_login, 
                    '.$nameTable1.'.phone_login,
                    '.$nameTable1.'.post_login,  
                    '.$nameTable1.'.id_specialty_login,
                    '.$nameTable2.'.ru_specialty
                    FROM
                    '.$nameTable1.'
                    Inner Join '.$nameTable2.' ON '.$nameTable2.'.id_specialty = '.$nameTable1.'.id_specialty_login
                    WHERE
                    '.$nameTable1.'.id_specialty_login = '.$id_specialty;
            $db->setQuery($query);
            return $db->loadAssocList();
        }     
    }
    
    
    
    //Метод получения списка груп пользователей.
    public function getListGroups(){
        $db = & JFactory::getDbo();
        $nameTable1 = $db->nameQuote('#__registry_role');   
            $query = 'SELECT
                        *
                        FROM
                        '.$nameTable1;
            $db->setQuery($query);
            return $db->loadAssocList();
    }
    
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
    
    
    //Метод получения расписания шаблона на день недели и на пользователя.
    public function getListTemplate($id_Week = null, $id_login = null){
        $db = & JFactory::getDbo();
        $nameTable1 = $db->nameQuote('#__registry_template'); 
        $nameTable2 = $db->nameQuote('#__registry_week');
        $nameTable3 = $db->nameQuote('#__registry_login'); 
        $nameTable4 = $db->nameQuote('#__registry_specialty'); 
        $nameTable5 = $db->nameQuote('#__registry_sex'); 
        $nameTable6 = $db->nameQuote('#__registry_activation'); 
        
        if($id_Week != null){
            if($id_login != null){
                $query = 'SELECT
                                '.$nameTable2.'.week,
                                '.$nameTable1.'.id_record_schedule,
                                '.$nameTable1.'.id_login_schedule,
                                '.$nameTable1.'.id_week,
                                '.$nameTable1.'.with_schedule,
                                '.$nameTable1.'.to_schedule,
                                '.$nameTable1.'.cabinet_schedule,
                                '.$nameTable1.'.id_create_schedule,
                                '.$nameTable1.'.data_create_schedule,
                                '.$nameTable1.'.time_create_schedule,
                                '.$nameTable1.'.ip_create_schedule,
                                '.$nameTable1.'.id_modifications_schedule,
                                '.$nameTable1.'.data_modifications_schedule,
                                '.$nameTable1.'.time_modifications_schedule,
                                '.$nameTable1.'.ip_modifications_schedule,
                                '.$nameTable3.'.surname_login,
                                '.$nameTable3.'.name_login,
                                '.$nameTable3.'.patronymic_login,
                                '.$nameTable3.'.id_specialty_login,
                                '.$nameTable4.'.ru_specialty,
                                '.$nameTable3.'.id_login,
                                '.$nameTable3.'.login_login,
                                '.$nameTable3.'.pass_login,
                                '.$nameTable3.'.id_activation_login,
                                '.$nameTable3.'.id_role_login,
                                '.$nameTable3.'.id_sex_login,
                                '.$nameTable3.'.post_login,
                                '.$nameTable3.'.id_create_login,
                                '.$nameTable3.'.data_create_login,
                                '.$nameTable3.'.time_create_login,
                                '.$nameTable3.'.ip_create_login,
                                '.$nameTable3.'.id_modification_login,
                                '.$nameTable3.'.date_modification_login,
                                '.$nameTable3.'.time_modification_login,
                                '.$nameTable3.'.ip_modification_login,
                                '.$nameTable3.'.phone_login,
                                '.$nameTable3.'.cabinet_login,
                                '.$nameTable3.'.time_login,
                                '.$nameTable5.'.sex_ru,
                                '.$nameTable6.'.name_activation,
                                '.$nameTable1.'.hidden_flag_schedule
                                FROM
                                '.$nameTable1.'
                                Inner Join '.$nameTable2.' ON '.$nameTable2.'.id_week = '.$nameTable1.'.id_week
                                Inner Join '.$nameTable3.' ON '.$nameTable3.'.id_login = '.$nameTable1.'.id_login_schedule
                                Inner Join '.$nameTable4.' ON '.$nameTable4.'.id_specialty = '.$nameTable3.'.id_specialty_login
                                Inner Join '.$nameTable5.' ON '.$nameTable5.'.id_sex = '.$nameTable3.'.id_sex_login
                                Inner Join '.$nameTable6.' ON '.$nameTable6.'.id_activation = '.$nameTable3.'.id_activation_login
                                WHERE
                                '.$nameTable1.'.id_login_schedule = \''.$id_login.'\' AND
                                '.$nameTable1.'.id_week = \''.$id_Week.'\'';
            }
            else{
                $query = 'SELECT
                                '.$nameTable2.'.week,
                                '.$nameTable1.'.id_record_schedule,
                                '.$nameTable1.'.id_login_schedule,
                                '.$nameTable1.'.id_week,
                                '.$nameTable1.'.with_schedule,
                                '.$nameTable1.'.to_schedule,
                                '.$nameTable1.'.cabinet_schedule,
                                '.$nameTable1.'.id_create_schedule,
                                '.$nameTable1.'.data_create_schedule,
                                '.$nameTable1.'.time_create_schedule,
                                '.$nameTable1.'.ip_create_schedule,
                                '.$nameTable1.'.id_modifications_schedule,
                                '.$nameTable1.'.data_modifications_schedule,
                                '.$nameTable1.'.time_modifications_schedule,
                                '.$nameTable1.'.ip_modifications_schedule,
                                '.$nameTable3.'.surname_login,
                                '.$nameTable3.'.name_login,
                                '.$nameTable3.'.patronymic_login,
                                '.$nameTable3.'.id_specialty_login,
                                '.$nameTable4.'.ru_specialty,
                                '.$nameTable3.'.id_login,
                                '.$nameTable3.'.login_login,
                                '.$nameTable3.'.pass_login,
                                '.$nameTable3.'.id_activation_login,
                                '.$nameTable3.'.id_role_login,
                                '.$nameTable3.'.id_sex_login,
                                '.$nameTable3.'.post_login,
                                '.$nameTable3.'.id_create_login,
                                '.$nameTable3.'.data_create_login,
                                '.$nameTable3.'.time_create_login,
                                '.$nameTable3.'.ip_create_login,
                                '.$nameTable3.'.id_modification_login,
                                '.$nameTable3.'.date_modification_login,
                                '.$nameTable3.'.time_modification_login,
                                '.$nameTable3.'.ip_modification_login,
                                '.$nameTable3.'.phone_login,
                                '.$nameTable3.'.cabinet_login,
                                '.$nameTable3.'.time_login,
                                '.$nameTable5.'.sex_ru,
                                '.$nameTable6.'.name_activation,
                                '.$nameTable1.'.hidden_flag_schedule
                                FROM
                                '.$nameTable1.'
                                Inner Join '.$nameTable2.' ON '.$nameTable2.'.id_week = '.$nameTable1.'.id_week
                                Inner Join '.$nameTable3.' ON '.$nameTable3.'.id_login = '.$nameTable1.'.id_login_schedule
                                Inner Join '.$nameTable4.' ON '.$nameTable4.'.id_specialty = '.$nameTable3.'.id_specialty_login
                                Inner Join '.$nameTable5.' ON '.$nameTable5.'.id_sex = '.$nameTable3.'.id_sex_login
                                Inner Join '.$nameTable6.' ON '.$nameTable6.'.id_activation = '.$nameTable3.'.id_activation_login
                                WHERE
                                '.$nameTable1.'.id_week = \''.$id_Week.'\'';
            }
        }
        else{
            $query = 'SELECT
                                '.$nameTable2.'.week,
                                '.$nameTable1.'.id_record_schedule,
                                '.$nameTable1.'.id_login_schedule,
                                '.$nameTable1.'.id_week,
                                '.$nameTable1.'.with_schedule,
                                '.$nameTable1.'.to_schedule,
                                '.$nameTable1.'.cabinet_schedule,
                                '.$nameTable1.'.id_create_schedule,
                                '.$nameTable1.'.data_create_schedule,
                                '.$nameTable1.'.time_create_schedule,
                                '.$nameTable1.'.ip_create_schedule,
                                '.$nameTable1.'.id_modifications_schedule,
                                '.$nameTable1.'.data_modifications_schedule,
                                '.$nameTable1.'.time_modifications_schedule,
                                '.$nameTable1.'.ip_modifications_schedule,
                                '.$nameTable3.'.surname_login,
                                '.$nameTable3.'.name_login,
                                '.$nameTable3.'.patronymic_login,
                                '.$nameTable3.'.id_specialty_login,
                                '.$nameTable4.'.ru_specialty,
                                '.$nameTable3.'.id_login,
                                '.$nameTable3.'.login_login,
                                '.$nameTable3.'.pass_login,
                                '.$nameTable3.'.id_activation_login,
                                '.$nameTable3.'.id_role_login,
                                '.$nameTable3.'.id_sex_login,
                                '.$nameTable3.'.post_login,
                                '.$nameTable3.'.id_create_login,
                                '.$nameTable3.'.data_create_login,
                                '.$nameTable3.'.time_create_login,
                                '.$nameTable3.'.ip_create_login,
                                '.$nameTable3.'.id_modification_login,
                                '.$nameTable3.'.date_modification_login,
                                '.$nameTable3.'.time_modification_login,
                                '.$nameTable3.'.ip_modification_login,
                                '.$nameTable3.'.phone_login,
                                '.$nameTable3.'.cabinet_login,
                                '.$nameTable3.'.time_login,
                                '.$nameTable5.'.sex_ru,
                                '.$nameTable6.'.name_activation,
                                '.$nameTable1.'.hidden_flag_schedule
                                FROM
                                '.$nameTable1.'
                                Inner Join '.$nameTable2.' ON '.$nameTable2.'.id_week = '.$nameTable1.'.id_week
                                Inner Join '.$nameTable3.' ON '.$nameTable3.'.id_login = '.$nameTable1.'.id_login_schedule
                                Inner Join '.$nameTable4.' ON '.$nameTable4.'.id_specialty = '.$nameTable3.'.id_specialty_login
                                Inner Join '.$nameTable5.' ON '.$nameTable5.'.id_sex = '.$nameTable3.'.id_sex_login
                                Inner Join '.$nameTable6.' ON '.$nameTable6.'.id_activation = '.$nameTable3.'.id_activation_login';
        }   
        $db->setQuery($query);
        return $db->loadAssocList();
    }
    
    
    public function getListSpecialtyJTable(){
        JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
        $table = JTable::getInstance('Specialty');
        return $table;
    }
    
    //Метод валидации данных на предмет предыдущего изменения, предотвращает эффект перезаписи данных.
    public function ValidationRedactedData($nameTable = null, $id_record = null, $message = false){
        $SessionItem = &JFactory::getSession(); 
        if($nameTable != null && $id_record != null){
            JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
            $table = JTable::getInstance($nameTable);
            $table->load($id_record);                                                       //Установка на нужный идентиффикатор.
            $arr = $table->getProperties();
            $arr_s = $SessionItem->get('EditableData');            
            if(count($arr) >= 2 && count($arr_s) >= 2){
                foreach($arr as $key => $value){                                            //Обходим масивы.
                    if($arr[$key] != $arr_s[$key] && $key != 'pass_login'){                 //Если есть отличия, то сообщаем.
                        if($message == true){
                            JError::raiseWarning( 100, JText::_('REG_ATTENTION_REDACTED_DATA_YOU_ALREADY_HAVE_CHANGED'));
                            $LogErrors = &JLog::getInstance('Error_Medical_Registry.'.date('Y_m_d').'.log.php');
                            Tinterface::LogEvents($LogErrors, 'VALIDATION:', 'Attention! redacted data you already have changed. Click (Cancel), and then open again for editing.', $arr_s);
                        }
                        $SessionItem->set('EditableData', '');                               //Удаление использаванных данных.  
                        return false;
                    }
                }
                $SessionItem->set('EditableData', '');                                       //Удаление использаванных данных.
                unset($key, $value, $arr, $arr_s);
                return true;
            }
            $SessionItem->set('EditableData', '');                                           //Удаление использаванных данных.  
        }
        $SessionItem->set('EditableData', '');                                               //Удаление использаванных данных.
        return true;
    }
    
    
    
    //Метод валидации вводимых данных ползователя.
    public function ValidationRedactedUserData($arr = null, $id_login = null, $message = false){
		if(count($arr) >= 1){
			$login = $arr['login_login'];					//Инициализация переменных.
			$pass = $arr['pass_login'];
			$LogErrors = &JLog::getInstance('Error_Medical_Registry.'.date('Y_m_d').'.log.php');
			$db = & JFactory::getDbo();
            $nameTable1 = $db->nameQuote('#__registry_login');   
            $query = 'SELECT
            			*
            			FROM
	                    '.$nameTable1.'
	                    WHERE
                    	'.$nameTable1.'.login_login = \''.$login.'\'';
            $db->setQuery($query);
            $a = $db->loadAssocList();
            if($id_login == null){							//Есди есть ИД записи, то проверяем на предмет дублей.
				if(count($a) >= 1){
					$ret = false;				
	            }
	            else{
	            	if($pass != ''){						//При создании пользователя проверяем пароль.
						$ret = true;
	            	}
	            	else{									//Если нет пароля, то выводим сообщение.
						$ret = false;
						if($message == true){
							JError::raiseWarning( 100, JText::_('REG_WHEN_CREATING_A_USER_YOU_DID_NOT_ENTER_A_PASSWORD'));
							Tinterface::LogEvents($LogErrors, 'VALIDATION:', 'When creating a user, you did not enter a password!', $arr);
						}
						return $ret;
	            	}
	            }
            }
            else{
				if(count($a) >= 1){							//Есди не соответсвует существующему ИД, то значит есть дублирование.
					if(
					$a['0']['id_login'] == $id_login
					&& count($a) == 1
					){
						$ret = true;
					}
					else{
						$ret = false;
					}
	            }
	            else{
					$ret = true;
	            }
            }   
		}
		if($ret == false && $message == true){				//Если не пройдена валидация, и есть флаг соощения, то выполняем его.
			JError::raiseWarning( 100, JText::_('REG_VALIDATION_OF_THE_USER_IS_LOGIN_FAILED_PLEASE_CHOOSE_A_DIFFERENT_LOGIN'));
			Tinterface::LogEvents($LogErrors, 'VALIDATION:', 'Validation of the user\'s login failed. Please choose a different login.', $arr);
		}
		return $ret;
    }
    
    
    
    
    //Метод формирования области данных для фиксации значений редактированный полей базы.
    public function getDataAreaRedactedData($nameTable = null, $id_record = null){
        if($nameTable != null && $id_record != null){
            JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
            $table = JTable::getInstance($nameTable);
            $table->load($id_record);                                                       //Установка на нужный идентиффикатор.
            $SessionItem = &JFactory::getSession();
            $SessionItem->set('EditableData', $table->getProperties());                     //Фиксация значения 
        }
    }
    
    
    
    //Метод получения списка специальностей.
    public function getListSpecialty(){
        $db = & JFactory::getDbo();
        $nameTable1 = $db->nameQuote('#__registry_specialty');    
        $query = 'SELECT
                '.$nameTable1.'.id_specialty,
                '.$nameTable1.'.ru_specialty
                FROM
                '.$nameTable1;
        $db->setQuery($query);
        return $db->loadAssocList();    
    }
    
    //Метод для получения специальностей для выбора пользователей.
    public function getListSpecialtySelect(){
        $db = & JFactory::getDbo(); 
        $nameTable1 = $db->nameQuote('#__registry_specialty');
        $nameTable2 = $db->nameQuote('#__registry_login');  
        $query = 'SELECT DISTINCT
                    '.$nameTable1.'.ru_specialty,
                    '.$nameTable1.'.id_specialty
                    FROM
                    '.$nameTable2.'
                    Inner Join '.$nameTable1.' ON '.$nameTable2.'.id_specialty_login = '.$nameTable1.'.id_specialty';          
        $db->setQuery($query);
        return $db->loadAssocList();
    }
    
    
    //Метод получения массива для меню.
    public function getMenuButton(){
        $session = JFactory::getSession(); 
        $id_s = $session->get('Medical_Registry_id'); 
        $id = $id_s[0]['id_login']; 
        
        $url = & JFactory::getURI();
        $MenuItem .= '<div style="font-size: 14pt;"><ul class="menu">';
        if(Rights::getRights('1')){
            //Личные данные
            $MenuItem .= '<li>'.JHTML::_('link', JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Registry_ManagementTask&TaskManagement=PersonalData'),JText::_('REG_PERSONAL_DATA'), array('title'=>JText::_('REG_MANAGEMENT_OF_PRIVATE_USER_DATA'),'class'=>'item-title')).'</li>'.'<br/>';      
        }   
        if(Rights::getRights('3')){
            $MenuItem .= '<li>'.JHTML::_('link', JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Registry_ManagementTask&TaskManagement=PersonalSchedule'),JText::_('REG_PERSONAL_SCHEDULE'), array('title'=>JText::_('REG_MANAGING_SCHEDULE_OF_USER'),'class'=>'item-title')).'</li>'.'<br/>';
        }
        $MenuItem .= '<li>'.JHTML::_('link', JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Registry_ManagementTask&TaskManagement=Schedules'),JText::_('REG_ALL_SCHEDULES'), array('title'=>JText::_('REG_MANAGE_ALL_SCHEDULES_FOR_PHYSICIANS'),'class'=>'item-title')).'</li>'.'<br/>';
        if(Rights::getRights('11')){
            $MenuItem .= '<li>'.JHTML::_('link', JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Registry_ManagementTask&TaskManagement=TemplateSchedules'),JText::_('REG_MANAGING_TEMPLATE_SCHEDULES'), array('title'=>JText::_('REG_MANAGING_TEMPLATE_SCHEDULES'),'class'=>'item-title')).'</li>'.'<br/>';        
        }   
        if(Rights::getRights('19')){
            $MenuItem .= '<li>'.JHTML::_('link', JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Registry_ManagementTask&TaskManagement=Users'), JText::_('REG_USER_MANAGEMENT'), array('title'=>JText::_('REG_MANAGEMENT_OF_ALL_USERS'),'class'=>'item-title')).'</li>'.'<br/>';
        }
        $MenuItem .= '<li>'.JHTML::_('link', JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Registry_ManagementTask&TaskManagement=Records'),JText::_('REG_MANAGING_APPOINTMENTS'), array('title'=>JText::_('REG_CONTROLS_ALL_APPOINTMENTS_TO_THE_DOCTOR'),'class'=>'item-title')).'</li>'.'<br/>'; 
        if(Rights::getRights('27')){
            $MenuItem .= '<li>'.JHTML::_('link', JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=RecallTask'), JText::_('REG_VIEW_REVIEWS'), array('title'=>JText::_('REG_FEEDBACK_AND_SUGGESTIONS_RECEIVED_FROM_USERS_BY_THE_REGISTRY'),'class'=>'item-title')).'</li>'.'<br/>';
        }
        if(Rights::getRights('25')){
            $MenuItem .= '<li>'.JHTML::_('link', JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=ReportsTask'), JText::_('REG_SEARCH_SYSTEM'), array('title'=>JText::_('REG_RECEIVE_REPORTS_ON_THE_RECEPTION'),'class'=>'item-title')).'</li>'.'<br/>';
        }
        $MenuItem .= '</ul></div>';      
        return $MenuItem;
    }
    
    
    //Метод получения меню в панель инструментов.
    public function getMenuTools(){
        $ret .= '<input class="button" type="submit" title="'.JText::_('REG_THE_MAIN_SERVICE_MENU_WHICH_YOU_SAW_AT_THE_SERVICE_ENTRANCE_TT').'" value="'.JText::_('REG_MAIN_MENU').'" name="ManeMenu">'."\n";
        $ret .= '<input class="button" type="submit" title="'.JText::_('REG_CONTROL_MENU_IN_THIS_MENU_ALL_REGISTRY_MANAGEMENT_TASKS_TT').'" value="'.JText::_('REG_CONTROL_MENU').'" name="MenuManagement">'."\n";   
        $ret .= '<input class="button" type="submit" title="'.JText::_('REG_EXIT_ACCOUNT_AND_GO_TO_THE_MAIN_SERVICE_MENU_IN_PROGRESS_AT_THE_CONCLUSION_OF_THE_SYSTEM_TT').'" value="Logout" name="Logout">'."\n";
        return $ret;
    }
    
    
    
    //Метод получения данных редактирования личных данных.
    public function getPersonalData(){
        $session = JFactory::getSession();
        $id_s = $session->get('Medical_Registry_id');
        if(count($id_s) == 1){
            $id = $id_s[0]['id_login'];
            JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
            $table = JTable::getInstance('Login');
            $table->load($id);
            return $table;
        }   
    }
    
    
    
    //Метод удаления записи из базы
    public function delData($id = null, $nameTable = null){
        if($id != null && $nameTable != null){
            JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
            $table = JTable::getInstance($nameTable);
            $table->delete($id);
        }
    }
    
    
    
    //Метод сохранения данных в базу из формы.
    public function setData($arr = null, $id = null, $nameTable = null){
        if(is_array($arr) && $nameTable != null && medical_registryModelRegistry_Management::ValidationRedactedData($nameTable, $id, true)){
            $Session = &JFactory::getSession();
            $id_s = $Session->get('Medical_Registry_id'); 
            $id_login = $id_s[0]['id_login'];                               //Получаем ИД пользователя.
            JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
            $table = JTable::getInstance($nameTable);
            if($id != null){
                $table->load($id);
                $table->setServiceInfMod($id_login);                        //Вносим сервисную информацию  
            }
            else{
                 $table->reset();
                 $table->setServiceInfAdd($id_login);                       //Вносим сервисную информацию  
            }    
            foreach($arr as $key => $value){
                if(Tinterface::Validation($value) == true){                 //Валиация на предмет враждебного кода.
                    if($key != 'pass_login'){
                        $table->$key = $value;
                    }
                    if($key == 'pass_login' && $value != ''){
                        $table->$key = md5($value);
                    }
                }    
            }                                  
            $table->store();
            unset($key, $value, $table);
        } 
    }

    
    
    //Метод получения объекта формы.
    public function getForm($id = null, $nameTable = null, $nameXML = null, $fields = null){
        if($nameTable != null && $nameXML != null){
            JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');   
            $table = JTable::getInstance($nameTable);
            if($id != ''){                                              //Если есть ИД, то загружаем запись.
                $table->load($id);
                $this->getDataAreaRedactedData($nameTable, $id);        //Фиксируем данные редактированного поля.    
            }
            else{                                                       //Если нет, то сбразываем.
                $table->reset();
                if($fields != null){                                    //Если есть массив наполнения, то записываем его в буфер объекта таблицы.
                    foreach($fields as $key => $value){                 //Обходим массив параметров.
                        $table->$key = $value;                          //Записываем значение в буфер объекта для последующего наполнения формы.
                    }
                    unset($key, $value);
                }
            }
            $pathToMyXMLFile = JPATH_COMPONENT.DS.'models'.DS.'forms'.DS.$nameXML.'.xml'; 
            $form = &JForm::getInstance($nameTable, $pathToMyXMLFile);
            $form = $this->setFieldsForm($form, $table);                //Заполяем форму.
            $table->reset();
            return $form;
        }
            
    }
    
    
    //Метод валидации логики вводимого расписания.
    //public function ValidationLogicSchedule($arr = null, $date = null, $id = null, $typeValidation = null){
    public function ValidationLogicSchedule($arr = null, $date = null, $id = null, $Template = null){  
    $data = new data();                                         //Получаем объект дата.   
    //Валидация даты.
    if($date != null){
    	$LogInfo = &JLog::getInstance('Info_Medical_Registry.'.date('Y_m_d').'.log.php');
        $LogErrors = &JLog::getInstance('Error_Medical_Registry.'.date('Y_m_d').'.log.php');
        $data_n = $data->data; 
        $data_p = explode('-',$data);
        $data_n_p = explode('-',$data_n);
        //Дата не может быть ранее текущей.
        if($data->comparison_date($data->data_i, $date) == false){
        	JError::raiseWarning( 100, JText::_('REG_DATE_CAN_NOT_BE_EARLIER_THAN_CURRENT_OR_TODAY'));
            //Логирование действия. 
            $this->LogEvents($LogErrors, 'VALIDATION:', 'Date can not be earlier than current, or today!', $arr);
            return  false;
        }
                /*
                //Дата не может быть больше чем на месяц (в конце мясяца).
                if($data_p[0] == $data_n_p[0] && $data_p[1] > $data_n_p[1] && $data_p[2] > 30){
                    JError::raiseWarning( 100, 'Дата не может быть больше чем на месяц (в конце мясяца).' ); 
                    return  false;
                }
                //Дата не может быть больше чем на месяц (в конце года).
                if($data_p[0] > $data_n_p[0] && $data_p[1] > 2){
                    JError::raiseWarning( 100, 'Дата не может быть больше чем на месяц (в конце года).' );
                    return  false;
                }
              
                //Дата не может быть больше чем на год.
                if($data_p[0] > ($data_n_p[0] + 2)){
                    JError::raiseWarning( 100, 'Дата не может быть больше чем на год.' ); 
                    return  false;
                }
                
                */
    	}
    	//Валидация данных.
       	if($arr != null){    
            if($Template != null){
                $table = $this->getListTemplate($Template['id_Week'], $Template['id_login']); //Получаем данные о введенном шаблоне расписании.  
            }
            else{
                $table = $this->getScheduleDataId($date, $id);              //Получаем данные о введенном расписании.  
            }
            $app = &JFactory::getApplication(); 
            
            $with = explode(':', $arr['with_schedule']);
            $to = explode(':', $arr['to_schedule']);
            
            foreach($with as $key => $value){                           //Проверка на предмет цифры.
                $arr_with = str_split($value);
                foreach($arr_with as $k => $v){
                    if(!is_numeric($v)){
                        //Логирование действия. 
                        $this->LogEvents($LogErrors, 'VALIDATION:', 'Type the characters in a location figure in time starts a', $arr);
                        JError::raiseWarning( 100, JText::_('REG_CHARACTER_ENTERED_IN_PLACE_OF_NUMBERS_IN_TIME_START_RECEIVING')); 
                        return false;
                    }
                }
                unset($k, $v);
                foreach($to as $k => $v){
                    if(!is_numeric($v)){
                        //Логирование действия. 
                        $this->LogEvents($LogErrors, 'VALIDATION:', 'Type the characters in a location numbers in the receiving end of time', $arr);
                        JError::raiseWarning( 100, JText::_('REG_THE_SYMBOL_YOU_TYPED_IN_PLACE_OF_NUMERALS_AT_THE_END_TIME_OF_THE_RECEPTION')); 
                        return false;
                    }
                }
                unset($k, $v);
                $arr_to = str_split($to[$key]);
                
                $with[$key] = (int)$value;                              //Преобразование типа в целое число.
                $to[$key] = (int)$to[$key];
            }
            unset($key, $value);
            if                                                          //Проверка на коректность времени.
            (
            $with[0] <= 24 && $with[0] >= 0 && $with[1] <= 59 && $with[1] >= 0 && $with[2] <= 59 && $with[2] >= 0 && 
            $to[0] <= 24 && $to[0] >= 0 && $to[1] <= 59 && $to[1] >= 0 && $to[2] <= 59 && $to[2] >= 0
            ){
                
            }
            else{
                //Логирование действия. 
                $this->LogEvents($LogErrors, 'VALIDATION:', 'Entered is not the right time', $arr);
                JError::raiseWarning( 100, JText::_('REG_NOT_ENTERED_THE_CORRECT_TIME')); 
                return false;
            }
            unset($with, $to);
            if($id != null){
                
            }
            $w_s = $data->conversion_time_data($arr['with_schedule']);            //Превращаем время в секунды.
            $t_s = $data->conversion_time_data($arr['to_schedule']);
            
            if($w_s >= $t_s){                                                       //Проверка на соответствие начало концу.
                //Логирование действия. 
                $this->LogEvents($LogErrors, 'VALIDATION:', 'Start reception may not be later than the deadline', $arr);
                JError::raiseWarning( 100, JText::_('REG_THE_APPOINTMENT_MAY_NOT_BE_LATER_THAN_THE_END_OF_THE_RECEPTION')); 
                return false;
            }
            if($table != ''){
                foreach($table as $key => $value){
                    $w_s_f = $data->conversion_time_data($value['with_schedule']);            //Превращаем время в секунды.
                    $t_s_f = $data->conversion_time_data($value['to_schedule']);
                    
                    if($arr['id_login_schedule'] != ''){                                     //В случае редактирования.
                        if(($w_s > $t_s_f) || (($w_s == $w_s_f) && ($t_s == $t_s_f))){
                               
                        }
                        else{
                            //Логирование действия. 
                            $this->LogEvents($LogErrors, 'VALIDATION:', 'Reception hours should not overlap', $arr);
                            JError::raiseWarning( 100, JText::_('REG_TIMES_TECHNIQUES_SHOULD_NOT_BE_CROSSED')); 
                            return false;
                        }
                    }
                    else{                                                                     //В случае добавления.         
                        if($w_s > $t_s_f){
                               
                        }
                        else{
                            //Логирование действия. 
                            $this->LogEvents($LogErrors, 'VALIDATION:', 'Reception hours should not overlap', $arr);
                            JError::raiseWarning( 100, JText::_('REG_TIMES_TECHNIQUES_SHOULD_NOT_BE_CROSSED')); 
                            return false;
                        }
                    }     
                }
            }
        }
        return true; 
    }
    
    
    //Метод получения расписаний на дату и пользователя для меню.
    public function getScheduleDataId($date = null, $id = null){
        if($date != null && $id != null){
            $db = & JFactory::getDbo();
            $nameTable1 = $db->nameQuote('#__registry_login');
            $nameTable2 = $db->nameQuote('#__registry_schedule');    
            $query = 'SELECT
                    '.$nameTable2.'.date_schedule,
                    '.$nameTable2.'.with_schedule,
                    '.$nameTable2.'.to_schedule,
                    '.$nameTable2.'.cabinet_schedule,
                    '.$nameTable2.'.id_record_schedule,
                    '.$nameTable2.'.id_login_schedule,
                    '.$nameTable2.'.id_create_schedule,
                    '.$nameTable2.'.data_create_schedule,
                    '.$nameTable2.'.time_create_schedule,
                    '.$nameTable2.'.ip_create_schedule,
                    '.$nameTable2.'.id_modifications_schedule,
                    '.$nameTable2.'.data_modifications_schedule,
                    '.$nameTable2.'.time_modifications_schedule,
                    '.$nameTable2.'.ip_modifications_schedule
                    FROM
                    '.$nameTable1.'
                    Inner Join '.$nameTable2.' ON '.$nameTable2.'.id_login_schedule = '.$nameTable1.'.id_login
                    WHERE
                    '.$nameTable1.'.id_login = '.$id.' AND
                    '.$nameTable2.'.date_schedule = "'.$date.'" AND
                    '.$nameTable1.'.id_activation_login = 1
                     ORDER BY with_schedule ASC';
            $db->setQuery($query);
            return $db->loadAssocList();
        }
    }
    
    
    //Метод определения идентификатора расписания по данным с POST запроса, дате и ИД пользователя.
    public function getIdSchedule($var = null, $date = null, $id = null){
        if($var != null && $id != null && $date != null){
            $vv = explode('$$',$var);                                      //Формируем массив.
            $with_schedule = $vv[0];
            $to_schedule = $vv[1];
            $arr = $this->getScheduleDataId($date, $id);
            if(is_array($arr)){
                foreach($arr as $key => $value){
                    if($value['with_schedule'] == $with_schedule && $value['to_schedule'] == $to_schedule){
                        return $value ['id_record_schedule'];
                    }
                }
                unset($key, $value);
                return $arr;
            }
        }
    }
    
    
    
    
    //Метод для подготовки данных для формы выбора даты.
    public function getFormDate($id_component = null, $SelectDate = null){
        //$arr = array('size'=>10, 'onchange'=>'this.form.submit()', 'style'=>"class='inputbox'");
        $ret .= '<div>'."\n";  
        $ret .= "\t".'<div>'."\n"; 
        $ret .= "\t"."\t".JText::_('REG_SELECT_DATE');
        $ret .= "\t".'</div>'."\n";
        $ret .= "\t".'<div>'."\n"; 
        $ret .= "\t"."\t".Tinterface::getUniFormed(Tinterface::getPluginDate($id_component, 'SelectDate'.$id_component, $SelectDate))."\n";
        $ret .= Tinterface::getEventHandlingMake($id_component, 'change', 'this.form.submit()');
        
        $ret .= "\t".'</div>'."\n";
        $ret .= "\t".'<div>'."\n";
        $ret .= "\t".'</div>'."\n";
        $ret .= '</div>'."\n"; 
        return $ret;
    }
    
    
    //Метод получения данных формы для манипулирования расписанием.
    public function getFormControlSchedule($id_select_schedule = null, $date_editing = null, $Complete = false){
        $ret .= '<div>'."\n";
        if(Rights::getRights('8') && $date_editing != null){
            $ret .= '<input type="submit" class="button" title="'.JText::_('REG_FILLING_SCHEDULES_FOR_THE_USER_FOR_A_WEEK_ACCORDING_TO_THE_SELECTED_DATE_IN_THE_WEEK_TT').'" value="'.JText::_('REG_THE_INPUT_FROM_THE_TEMPLATE').'" name="AddForTemplate"><br/>'."\n";
            
            $ret .= '<input type="submit" class="button" title="'.JText::_('REG_ATTENTION_INPUT_FROM_THE_TEMPLATE_FOR_FIVE_WEEKS').'" value="'.JText::_('REG_INPUT_FROM_THE_TEMPLATE_FOR_FIVE_WEEKS').'" name="AddForTemplateMonth"><br/>'."\n";
            $ret .= '<input type="submit" class="button" title="'.JText::_('REG_CAUTION_INPUT_FROM_THE_TEMPLATE_FOR_TEN_WEEKS').'" value="'.JText::_('REG_INPUT_FROM_THE_TEMPLATE_FOR_TEN_WEEKS').'" name="AddForTemplateDecade"><br/>'."\n";
            $ret .= '<input type="submit" class="button" title="'.JText::_('REG_ATTENTION_THINK_CAREFULLY_BEFORE_CLICKING_HERE_THIS_IS_THE_INPUT_FROM_THE_TEMPLATE_FOR_FIFTY_WEEKS').'" value="'.JText::_('REG_INPUT_FROM_TEMPLATE_FOR_FIFTY_WEEKS').'" name="AddForTemplateFifty"><br/>'."\n";   
        }
        if($id_select_schedule != ''){
            if(Rights::getRights('10')){
                $ret_ca = '<input type="submit" class="button" title="'.JText::_('REG_DELETE_A_SET_AMOUNT_OF_WORKING_TIME_TT').'" value="'.JText::_('REG_REMOVE').'" name="Remove">'."\n";  
            } 
        }
        else{
             if(Rights::getRights('7')){
                 $ret_cb = '<input type="submit" class="button" title="'.JText::_('REG_ADD_NEW_PERIOD_OF_WORKING_TIME_TT').'" value="'.JText::_('REG_ADD').'" name="Add"><br/>'."\n";    
             }
        }
        if($Complete == false){										//Если короткое меню, то выводим укороченный вариант.								
			return $ret_ca;
        }
        if($ret_ca != ''){
			$ret .= $ret_ca.'<br/>';
        }
        if($ret_cb != ''){
			$ret .= $ret_cb;
        }
        $ret .= '</div>'."\n";
        return $ret;
    }
    
    
    
    //Метод получения данных для формы управления просмотром расписаний.
    public function getScheduleTools($date = null){
        if($date != null){
            $ret .= '<input type="submit" class="button" title="'.JText::_('REG_GET_THE_SCHEDULE_FOR_THE_SELECTED_DATE_TT').'" value="'.JText::_('REG_THE_SCHEDULE_FOR_THE_DATES').'" name="DateSchedule">'."\n";    
        }
        $ret .= '<input type="submit" class="button" title="'.JText::_('REG_GET_THE_SCHEDULE_FOR_THE_WEEK_ACCORDING_TO_THE_SELECTED_DATE_TT').'" value="'.JText::_('REG_THE_SCHEDULE_FOR_THE_WEEK').'" name="WeekSchedule">'."\n";
        return $ret; 
    }
    
    
    
    //Метод получения данных для формы выбора специальности.
    public function getFormSpecialty($id_component = null, $id_specialty){
        $specialty = $this->getListSpecialtySelect();
        if($id_specialty == null){
            $arr_sp['n'] = JText::_('REG_NOT_CHOSEN');
        }
        if($specialty != '' && $id_component != null){
            foreach($specialty as $key => $value){
                $arr_sp[$value['id_specialty']] = JText::_($value['ru_specialty']);
            }
            unset($key, $value);
            $ret .= "\t".'<div>'."\n";
            $ret .= "\t"."\t".'<div>'."\n"; 
            $ret .= "\t"."\t"."\t".JText::_('REG_SELECT_SPECIALTY')."\n";
            $ret .= "\t"."\t".'</div>'."\n"; 
            $ret .= "\t"."\t".'<div>'."\n";
            $onChange = "this.form.submit()";
            $ret .= JHTML::_('select.genericlist', $arr_sp, 'id_specialty'.$id_component, 'size="1" onChange="'.$onChange.'"', 'id', 'title', $id_specialty)."\n";
            $ret .= "\t"."\t".'</div>'."\n";
            $ret .= "\t".'</div>'."\n"; 
        }
        return $ret;
    }
    
    
    
    //Метод получения данных для формы выбра расписания шаблона.
    public function getSelectTemplate($id_component = null, $id_Week = null, $id_login = null, $id_record_schedule = null){
        if($id_component != null && $id_Week != null && $id_login != null){
            $arr_Template = $this->getListTemplate($id_Week, $id_login);
            if(count($arr_Template) >= 1){
                foreach($arr_Template as $key => $value){
                    $arr[$value['id_record_schedule']] = JText::_('REG_WITH').' - '.medical_registryModelAppointment_doctor::PreparationTime($value['with_schedule']).'   '.JText::_('REG_ATN').' - '.medical_registryModelAppointment_doctor::PreparationTime($value['to_schedule']).'   '.JText::_('REG_THE_RECEIVE_LOCATION').' - '.$value['cabinet_schedule'];
                }
                unset($key, $value);
                $ret .= "\t".'<div>'."\n";
                $ret .= "\t"."\t".'<div>'."\n"; 
                $ret .= "\t"."\t"."\t".JText::_('REG_SELECTED_TIME')."\n";
                $ret .= "\t"."\t".'</div>'."\n"; 
                $ret .= "\t"."\t".'<div>'."\n";
                $onChange = "this.form.submit()";
                $ret .= JHTML::_('select.genericlist', $arr, 'id_record_schedule'.$id_component, 'size="4" onChange="'.$onChange.'"', 'id', 'title', $id_record_schedule)."\n";
                $ret .= "\t"."\t".'</div>'."\n";
                $ret .= "\t".'</div>'."\n";
                return $ret; 
            }
        }
    }
    
    
    
    //Метод получения данных для вкладок панели управления записями на прием.
    public function getDataControlPanel($data = null, $admin = false){
    	$objdata = new data();
    	$objModelAppointment_doctor = new medical_registryModelAppointment_doctor();
        
        if($data[JText::_('REG_ALL_DOCTORS')]['d'] == ''){											//Если нет даты во вкладке все доктора, то заполняеме ее текущей.
			$data[JText::_('REG_ALL_DOCTORS')]['d'] = $objdata->data_i;
			//$data['Все'] = $objModelAppointment_doctor->getMenuTimeTable($data['Все']['d']);
        }
        
        if(count($data) >= 1){
        	
        	
            foreach($data as $key => $value){                                           //Обходим все данные.
                if($data[$key]['id_Specialty'] != ''){                                  //Если выбрано специальность, то получаем пользователей.
                    //Получаем пользователей на специальность.
                    $arr_Users = medical_registryModelAppointment_doctor::getListUsersSchedule($data[$key]['id_Specialty']);
                    foreach($arr_Users as $k => $v){                                    //Обходим пользователей, формируем массив.
                        $nameUser = $v['name_login'];
                        $namePatronymic = $v['patronymic_login'];
                        $Users[$v['id_login']] = $v['surname_login'].' '.$nameUser.' '.$namePatronymic;
                        $UsersData[$v['id_login']]['time_login'] = $v['time_login'];
                        $UsersData[$v['id_login']]['phone_login'] = $v['phone_login'];
                        $UsersData[$v['id_login']]['post_login'] = $v['post_login'];
                        $UsersData[$v['id_login']]['skype_login'] = $v['skype_login'];    
                    }
                    $data[$key]['Users'] = $Users;                                   //Сохраняем результат.
                    unset($k, $v, $Users, $arr_Users);
                }
                if(                                                                  //Если выбрано дату и время приема, то готовим данные для панели.     
                $data['REG_SELECTED_PATIENT'] != '' && $key != 'REG_SELECTED_PATIENT'
                ){
                    //Готовим панель управления пациентом.
                    $data[$key]['ControlPanelPatient'] = $this->getToolbarAppointmentRecording($value, $data['REG_SELECTED_PATIENT']); 
                }
                else{
                    unset($data[$key]['ControlPanelPatient']);
                }
                
                
                if($key == JText::_('REG_ALL_DOCTORS')){														//Заполняем данными вкладку ВСЕ.
					$data[$key]['data_all'] = $objModelAppointment_doctor->getMenuTimeTable($value['d'], $value['id_Specialty'], true);
					if($data[$key]['ControlPanelPatient'] != ''){						//Если есть панел управления пациентом, то формируем ее.
						$tools_all['0'] = '<div class="inputbox">'.Tinterface::getUniFormed($data[$key]['ControlPanelPatient']).'</div>';
					}
					$tools_all['1'] = $objModelAppointment_doctor->getToolsTable($value['d'], $value['id_Specialty']);
					$data[$key]['tools_all'] = Tinterface::getElementsColumn($tools_all);
                }
                
                
                
                if($data[$key]['id_Users'] != ''){                                      //Если выбрано пользователя, то получаем его данные.
                    $data[$key]['time_login']  = $UsersData[$data[$key]['id_Users']]['time_login'];
                    $data[$key]['phone_login']  = $UsersData[$data[$key]['id_Users']]['phone_login'];  
                    $data[$key]['post_login']  = $UsersData[$data[$key]['id_Users']]['post_login'];
                    $data[$key]['skype_login']  = $UsersData[$data[$key]['id_Users']]['skype_login'];  
                    $data[$key]['data'] = $this->getDataControlPanelSecondLevel($data[$key], $key, $admin);
                }
            }
            unset($key, $value);
            if($data['REG_SELECTED_PATIENT'] != ''){                       //Если пациент отобран, то обрабатываем его.
                 
            }
            if($data['blank']['Specialty'] == ''){                                   //Если бланк пустой, заполняем его специальностями.
               //Формируем массив у кого есть расписание.
               $arr_Specialty = medical_registryModelAppointment_doctor::getListSpecialtySchedule();
                foreach($arr_Specialty as $kk => $vv){                              //Обходим его.
                    $Specialty[$vv['id_specialty']] = JText::_($vv['ru_specialty']);//Формируем новый массив.
                }
                $data['blank']['Specialty'] = $Specialty;                           //Сохранеем массив специальностей.
                unset($kk, $vv, $arr_Specialty, $Specialty); 
           }   
        }
        else{                                                                   //Если переменная пустая то наполняем ее бланком.
            //Формируем массив у кого есть расписание. 
            $arr_Specialty = medical_registryModelAppointment_doctor::getListSpecialtySchedule(); 
            foreach($arr_Specialty as $kk => $vv){                              //Обходим его.
                $Specialty[$vv['id_specialty']] = JText::_($vv['ru_specialty']);//Формируем новый массив.
            }
            $data['blank']['Specialty'] = $Specialty;                           //Сохранеем массив специальностей.
            unset($kk, $vv, $arr_Specialty, $Specialty); 
        }
        unset($objdata, $objModelAppointment_doctor);							//Освобождение памяти.  
        return $data;
    }
    
    
    
    //Метод получения данных для вкладок второго уровна панели управления.
    public function getDataControlPanelSecondLevel($data, $key, $admin = false){
        $prefix = str_replace(' ', '_', $key);                                              //Получаем префикс.
        $ModelItemSchedule = &$this->getInstance('medical_registryModelSchedule');          //Получаем экземпляр модели с просмотра расписаний.
        $ModelItemAppointment = &$this->getInstance('medical_registryModelAppointment_doctor');    //Получаем экземпляр модели с просмотра записи на прием.  
        $date = new data();                                                                 //Получаем объект дата.  
        $SelectedDate = $data['SelectedDate'];
        $SelectedWeek = $data['SelectedWeek'];
        $id_Users = $data['id_Users'];
        if($data['SelectedDate'] != ''){
             $SelectedDate = $data['SelectedDate'];
        }
        $ScheduleElemetsLine['0'] = Tinterface::getUniFormed($this->getFormDate($key, $SelectedDate));
        $ScheduleElemetsLine['1'] = Tinterface::getUniFormed(medical_registryModelAppointment_doctor::getMenuSchedule($SelectedDate, $SelectedWeek, $prefix));
        if($data['ControlPanelPatient'] != ''){
            $arr_SelectedDateTime = explode('$$', $data['SelectedDateTime']);
            $ScheduleElemetsLine['2'] = Tinterface::getUniFormed($data['ControlPanelPatient']);
        }
        $ScheduleElemetsColumn['0'] = '<div class="inputbox">'.Tinterface::getElementsLine($ScheduleElemetsLine).'</div>';
        if($SelectedWeek != ''){
            $ScheduleElemetsColumn['1'] = Tinterface::getUniFormed($ModelItemAppointment->getSelectTimeWeek($SelectedDate, $id_Users, $admin));
        }
        else{
            $ScheduleElemetsColumn['1'] = Tinterface::getUniFormed($ModelItemAppointment->getSelectTime($SelectedDate, $id_Users, $admin));
        }
        
        //$retSchedule = $ModelItemSchedule->getTableWeek($ModelItemSchedule->getArrayDate($SelectedDate), $id_Users);
        $retSchedule = $ModelItemSchedule->getTableWeekSchedule($SelectedDate, $data['id_Specialty'], $id_Users, $admin); 
        $retSchedule = Tinterface::getDivPrint($retSchedule, $key.'Schedule');
        $ret[JText::_('REG_SCHEDULE')] =  $this->getDivTools($this->getButtonPrint($key.'Schedule')).$retSchedule; 
        
        
        $ret[JText::_('REG_DATES_AND_TIMES')] = Tinterface::getElementsColumn($ScheduleElemetsColumn);
        if($SelectedWeek != ''){
            $retRecords = $ModelItemAppointment->getTableWeek($SelectedDate, $id_Users);
        }
        else{
            $retRecords = $ModelItemAppointment->getTableDate($SelectedDate, $id_Users);
        }
        
        $retRecordsView = Tinterface::getDivPrint($retRecords, $key.'Records');
        if(Rights::getRights('15') && $retRecords != ''){
            $ret[JText::_('REG_RECORDED_PATIENTS')] = $this->getDivTools($this->getButtonPrint($key.'Records')).$retRecordsView; 
        }
        return $ret;
    }
    
    
    
    
    //Метод получения данных для панели записи на прием.
    public function getToolbarAppointmentRecording($data = null, $arr_Patient = null){
        if($data != null){
            $arr = explode('$$', $data['SelectedDateTime']);
            if($arr['0'] != '' && $arr['1'] != ''){                       //Если выбранно время, то отображаем его.
                $ret .= JText::_('REG_DATE').' '.'<b>'.$arr[0].'</b>'.'  ';
                $ret .= JText::_('REG_TIME').' '.'<b>'.medical_registryModelAppointment_doctor::PreparationTime($arr[1]).'</b>'.'<br/>';
            }
        }
        if($arr_Patient['id_user'] != ''){                              //Если пациент записан, то показываем соот. кнопку.
            if($arr[0] != '' && $arr['1'] != '' && Rights::getRights('17')){
                $ret .= '<input type="submit" class="button" value="'.JText::_('REG_TO_MOVE_THE_SELECTED').'" name="TransferRecordedt">'."\n"; 
                $ret .= '<br/>'; 
            }
            if(Rights::getRights('18')){
                $ret .= '<input type="submit" class="button" value="'.JText::_('REG_REMOVE').'" name="DeleteRecordedt">'."\n";       
                if($arr_Patient['id_status'] == '4'){                       //Если статус пациента не активен, то выводим кнопку активации.
                    $ret .= '<input type="submit" class="button" value="'.JText::_('REG_ACTIVATE').'" name="ActivateRecordedt">'."\n";  
                }
                else{
                    $ret .= '<input type="submit" class="button" value="'.JText::_('REG_BLOCK').'" name="OffRecordedt">'."\n"; 
                } 
            }
            if(Rights::getRights('17')){
                $ret .= '<input type="submit" class="button" value="'.JText::_('REG_CHANGE').'" name="СhangedRecordedt">'."\n"; 
            }
        }
        else{                                                           //Иначе паказываем кнопку для записи. 
            if(                                                         //Если зарееастрирован, то показываем ккнопку записи на прием.  
            $arr_Patient != ''
            && $arr[0] != '' && $arr['1'] != ''                         //и выбранно время.
            && Rights::getRights('16')
            ){
                $ret .= '<input type="submit" class="button" value="'.JText::_('REG_TO_MAKE_AN_APPOINTMENT').'" name="MakeAppointment">'."\n"; 
            }      
        }
        return $ret;                                                    //Возвращаем результат.     
    }
     
    
    //Метод для получения данных для формы для выбора пользователя.
    public function getFormSelectUser($id_component = null, $id_specialty = null, $id_user, $size = null){
        $specialty = $this->getListSpecialtySelect();
        if($id_specialty != ''){
            $users = $this->getListUser($id_specialty);
            if($size == null){
                $size = '6';
            }
            if($id_user == ''){
                $arr_ur['n'] = JText::_('REG_NOT_CHOSEN');
            }
            if($users != ''){
                foreach($users as $key => $value){
                    $arr_ur[$value['id_login']] = $value['surname_login'].' '.$value['name_login'].' '.$value['patronymic_login']; 
                }
                unset($key, $value);
            }
        }
        if($specialty != '' && $id_component != null && $arr_ur != ''){
            $onChange = "this.form.submit()";
            $ret .= "\t".'<div>'."\n";
            $ret .= "\t"."\t".'<div>'."\n"; 
            $ret .= "\t"."\t"."\t".JText::_('REG_SELECT_DOCTOR');
            $ret .= "\t"."\t".'</div>'."\n";  
            $ret .= "\t"."\t".'<div>'."\n"; 
            $ret .= JHTML::_('select.genericlist', $arr_ur, 'id_login'.$id_component, 'size="'.$size.'" onChange="'.$onChange.'"', 'id', 'title', $id_user)."\n";
            $ret .= "\t"."\t".'</div>'."\n";
            $ret .= "\t".'</div>'."\n";
        }
        return $ret;
    }
    
    
    //Метод формирования полей формы.
    public function setFieldsForm($form = null, $table = null){ 
        $arr = Tinterface::object_to_array($form);              //Формируем массив с объекта.
        foreach($arr as $key => $value){                        //Ставим на последний ключ.    
        }
        $arr = $value['fields'];                                //Формируем новый массив.
        unset($key, $value);                                    //Обнуляем переменные. 
        if($arr['0'] == ''){									//Если вкладка одна, то исправляем масив.
			$arr[] = $arr;
        }
        foreach($arr as $key => $value){                        //Обходим массив.
            $fields = $value['@attributes']['name'];            //Формируем переменную для вкладок.
            foreach($value['fieldset']['field'] as $k => $v){   //Обходим элементы в группе.            
                $name = $v['@attributes']['name'];              //Формируем переменные.
                $type = $v['@attributes']['type'];
                if(
                $type == 'captcha' ||
                $type == 'calendar' ||
                $type == 'textarea' ||
                $type == 'text' || 
                $type == 'email' || 
                $type == 'sql' || 
                $type == 'tel' ||
                $type == 'checkbox' ||
                $type == 'time'
                ){
                    if(is_array($table)){                       //Если переменная массив, то обрабатываем как массив.
                        $vv = $table[$name]; 
                    }
                    else{                                       //Иначе как объект.
                        $vv = $table->$name; 
                    }                       
                    $form->setValue($name, $fields, $vv);
                }   
            }
            unset($k, $v);                                      //Обнуляем переменные.  
        }
        unset($key, $value);                             //Обнуляем переменные. 
        return $form;
    }
    
    
    
    
    
    //Метод получения данных для формы выбора объекта управления.
    public function getControlObject($setObject = null){
        if($setObject == null){
            $arr['n'] = JText::_('REG_NOT_CHOSEN');
        }
        $arr['0'] = JText::_('REG_GROUP');
        $arr['1'] = JText::_('REG_USERS');
        $onChange = "this.form.submit()";
        $ret .= "\t".'<div>'."\n";
        $ret .= "\t"."\t".'<div>'."\n"; 
        $ret .= "\t"."\t"."\t".JText::_('REG_SELECTION_OBJECT')."\n";
        $ret .= "\t"."\t".'</div>'."\n";
        $ret .= "\t"."\t".'<div>'."\n";
        $ret .= JHTML::_('select.genericlist', $arr, 'setObject', 'size="1" onChange="'.$onChange.'"', 'id', 'title', $setObject)."\n";
        $ret .= "\t"."\t".'</div>'."\n";
        $ret .= "\t".'</div>'."\n"; 
        return  $ret;
    }
    
    
    //Метод получения данных для формы выбора группы пользователей.
    public function getGroupSelection($id_GroupSelection = null){
        $a = $this->getListGroups();
        if($id_GroupSelection == null){
            $arr['n'] = JText::_('REG_NOT_CHOSEN');
        }
        foreach($a as $key => $value){
            $arr[$value['id_role']] = JText::_($value['name_role']);
        }
        unset($key, $value);
        $onChange = "this.form.submit()"; 
        $ret .= "\t".'<div>'."\n";
        $ret .= "\t"."\t".'<div>'."\n"; 
        $ret .= "\t"."\t"."\t".JText::_('REG_SELECT_GROUP')."\n"; 
        $ret .= "\t"."\t".'</div>'."\n";
        $ret .= "\t"."\t".'<div>'."\n";
        $ret .= JHTML::_('select.genericlist', $arr, 'id_GroupSelection', 'size="1" onChange="'.$onChange.'"', 'id', 'title', $id_GroupSelection)."\n"; 
        $ret .= "\t"."\t".'</div>'."\n";
        $ret .= "\t".'</div>'."\n"; 
        return $ret;
    }
    
    
    
     //Метод получения данных для формы выбора действи с объектом
     public function getSelectionAction($Users = null){
        if($Users != null){
            $onChange = "this.form.submit()"; 
            if($Users['setObject'] == '1'){
                $ret .= "\t".'<div>'."\n";
                $ret .= "\t"."\t".'<div>'."\n"; 
                $ret .= "\t"."\t"."\t".JText::_('REG_SELECT_ACTION')."\n"; 
                $ret .= "\t"."\t".'</div>'."\n";
                $ret .= "\t"."\t".'<div>'."\n";
                $arr['n'] = JText::_('REG_NOT_CHOSEN');  
                $arr['AddUsers'] = JText::_('REG_ADD_USER'); 
                if($Users['id_Users'] != '' && $Users['id_Users'] != 'n'){
                    $arr['EditUsers'] = JText::_('REG_EDIT_USER');
                    $arr['RightsUsers'] = JText::_('REG_USER_RIGHTS');  
                }
                $ret .= JHTML::_('select.genericlist', $arr, 'id_SelectionAction', 'size="1" onChange="'.$onChange.'"', 'id', 'title', $Users['id_SelectionAction'])."\n";
                $ret .= "\t"."\t".'</div>'."\n";
                $ret .= "\t".'</div>'."\n";    
            }
            return $ret;
        }    
     }
     
     
     
     //Метод получения данных для формы прав.
     public function getEditRights($Users = null){
         if($Users != ''){
             //Получение массивов.
             $arr_task = $this->getAllRecord('registry_task');
             $arr_users = $this->getAllRecord('registry_user_rights');
             $arr_role = $this->getAllRecord('registry_role_rights');
             $ret .= "\t".'<div class="inputbox">'."\n";
             $ret .= "\t"."\t".'<input type="submit" class="button" name="Cancel" value="'.JText::_('REG_CANCEL').'">'."\n";
             $ret .= "\t"."\t".'<input type="submit" class="button" name="Save" value="'.JText::_('REG_SAVE').'">'."\n";
             $ret .= "\t".'</div>'."\n";
             foreach($arr_task as $key => $value){                                                          //Главный цикл, перебирает все задачи.
                 if(                                                                                        //При выборе группы, формируем массив.  
                 $Users['setObject'] == '0' &&
                 $Users['id_GroupSelection'] != ''
                 ){                                                          
                     $checked = '';
                     $arr[$value['id']] = false;
                     foreach($arr_role as $k_role => $v_role){                                             //Формируем массив.
                        if(                                                                                 //Если есть соответствие, то устанавливаем его.
                        $value['id'] == $v_role['id_task'] && 
                        $v_role['id_role'] == $Users['id_GroupSelection']
                        ){
                            $arr[$value['id']] = true;
                            $checked = 'checked';
                        }
                     }
                     $ret .= "\t".'<div>'."\n"; 
                     $ret .= "\t"."\t".JText::_($value['Task']).' '.'<input type="checkbox" name="'.$value['id'].'" value="'.$value['id'].'" '.$checked.'>'."\n";
                     $ret .= "\t".'</div>'."\n"; 
                     unset($k_role, $v_role);                         
                 }
                 if(                                                                                        //При выборе пользователей, формируем массив.  
                 $Users['setObject'] == '1' &&
                 $Users['id_Users'] != '' &&
                 $Users['id_SelectionAction'] == 'RightsUsers'
                 ){
                     $arr[$value['id']] = false;
                     $checked = ''; 
                     foreach($arr_users as $k_users => $v_users){                                             //Формируем массив.
                        if(                                                                                     //Если есть соответствие, то устанавливаем его. 
                        $value['id'] == $v_users['id_task'] && 
                        $Users['id_Users'] == $v_users['id_user']
                        ){      
                            $arr[$value['id']] = true;
                            $checked = 'checked';
                        }
                     }
                     $ret .= "\t".'<div>'."\n"; 
                     $ret .= "\t"."\t".JText::_($value['Task']).' '.'<input type="checkbox" name="'.$value['id'].'" value="'.$value['id'].'" '.$checked.'>'."\n";
                     $ret .= "\t".'</div>'."\n";
                     unset($k_users, $v_users);
                 }
             }
             unset($key, $value);      
         return $ret;
         }
     }
     
     
     //Метод для получения данных для формы выбора дня недели.
     public function getWeek($id_Week = null){
         $arr_Week = $this->getAllRecord('registry_week');                              //Получаем массив задач.
         if($id_Week == null){
              $arr['n'] = JText::_('REG_NOT_CHOSEN'); 
         }
         foreach($arr_Week as $key => $value){
             $arr[$value['id_week']]  = JText::_($value['week']);                       //Готовим массив.
         }
         unset($key, $value);
         $onChange = "this.form.submit()"; 
         $ret .= "\t".'<div>'."\n";
         $ret .= "\t"."\t".'<div>'."\n"; 
         $ret .= "\t"."\t"."\t".JText::_('REG_THE_CHOICE_OF_DAY_OF_THE_WEEK')."\n"; 
         $ret .= "\t"."\t".'</div>'."\n";
         $ret .= "\t"."\t".'<div>'."\n";
         $ret .= JHTML::_('select.genericlist', $arr, 'id_Week', 'size="1" onChange="'.$onChange.'"', 'id', 'title', $id_Week)."\n";
         $ret .= "\t"."\t".'</div>'."\n";
         $ret .= "\t".'</div>'."\n";
         return $ret; 
     }
     
     //Метод подготовки кнопки для печати.
    public function getButtonPrint($id = null){
        return medical_registryModelAppointment_doctor::getButtonPrint($id);
    }
    
    //Метод представления контента как панель инструментов.
    public function getDivTools($content = null){
        if($content != null){
            $ret .= "\t".'<div class="inputbox">';
            $ret .= "\t"."\t".$content; 
            $ret .= "\t".'</div>';
            return $ret; 
        }
    }
     
     
     //Метод формирование данных для формы панели инструментов управления шаблоном.
     public function getTemplateTools($Template = null, $Complete = false){
         if($Template['id_login'] != ''){
             if($Template['id_record_schedule'] == ''){
                 if(Rights::getRights('12') && $Complete){
                     $ret .= '<input type="submit" class="button" title="'.JText::_('REG_ADD_NEW_PERIOD_OF_WORKING_TIME_TT').'" name="Add" value="'.JText::_('REG_ADD').'">';   
                 }
             }
             else{
                 if(Rights::getRights('14')){
                     $ret .= '<input type="submit" class="button" title="'.JText::_('REG_DELETE_A_SET_AMOUNT_OF_WORKING_TIME_TT').'" name="Delete" value="'.JText::_('REG_REMOVE').'">';  
                 }
             }
         }
         if($Complete){
			  $ret .= '<input type="submit" class="button" title="'.JText::_('REG_GET_THE_SCHEDULE_OF_ALL_THE_DOCTORS_IN_THE_WEEK_TT').'" name="TemplateReset" value="'.JText::_('REG_VIEW_ALL').'">'; 
         }
         return $ret;         
     }
     
     
     //Метод сохранения прав.
     public function SaveRights($Users = null, $post = null){
         if($Users != null){
             $arr_task = $this->getAllRecord('registry_task');                          //Получаем массив задач.
             $db = & JFactory::getDbo();
             
             if($Users['id_SelectionAction'] == 'RightsUsers'){
                 $nameTable1 = $db->nameQuote('#__registry_user_rights');
                 $query = 'DELETE FROM
                            '.$nameTable1.'
                            WHERE
                            id_user = \''.$Users['id_Users'].'\'';
             }
             
             if($Users['setObject'] == '0'){
                 $nameTable2 = $db->nameQuote('#__registry_role_rights');
                  $query = 'DELETE FROM
                            '.$nameTable2.'
                            WHERE
                            id_role = \''.$Users['id_GroupSelection'].'\'';   
             }
             $db->setQuery($query);
             $db->execute();                                                            //Удаление старых данных.  
               
             foreach($arr_task as $k => $v){                                            //Обходим все задачи.
                 if($Users['id_SelectionAction'] == 'RightsUsers' &&  $post[$v['id']] != ''){
                     $nameTable1 = $db->nameQuote('#__registry_user_rights');
                     $query = 'INSERT INTO 
                                '.$nameTable1.'
                                SET 
                                id_task = \''.$v['id'].'\',
                                id_user = \''.$Users['id_Users'].'\'';
                     $db->setQuery($query);
                     $db->execute();
                 }
                
                 if($Users['setObject'] == '0' &&  $post[$v['id']] != ''){
                     $nameTable2 = $db->nameQuote('#__registry_role_rights');
                     $query = 'INSERT INTO 
                                '.$nameTable2.'
                                SET 
                                id_task = \''.$v['id'].'\',
                                id_role = \''.$Users['id_GroupSelection'].'\'';
                     $db->setQuery($query);
                     $db->execute();   
                 }
             }
             unset($k, $v);
         }
     }
     
     
    //Метод получения таблици шаблона.
    public function getTableWeekTemplate($id_Week = null, $id_specialty = null, $id_login = null){
        $Array = $this->getAllRecord('registry_week');
        if($Array != ''){
            foreach($Array as $key => $value){
                if($id_Week != null && $value['id_week'] == $id_Week){
                    $ret .= $this->getTableTemplate(JText::_('REG_SCHEDULE_FOR_THE').' '.'<b>'.mb_strtolower(JText::_($value['week'])).'</b>', $value['id_week'], $id_specialty, $id_login);  
                }
                else{
                    if($id_Week == null){
                        $ret .= $this->getTableTemplate(JText::_('REG_SCHEDULE_FOR_THE').' '.'<b>'.mb_strtolower(JText::_($value['week'])).'</b>', $value['id_week'], $id_specialty, $id_login);  
                    }
                }
                
            }
            unset($key, $value);
            return $ret;
        }
    }
     
     
     
    //Метод получения таблицы шаблона расписания с массива на день и по возможности по ИД пользователю.
    public function getTableTemplate($Head = null, $id_Week = null, $id_specialty = null, $id_login = null){
        if($id_Week != null){
            $arr = $this->getListTemplate($id_Week, $id_login);
            if($arr != ''){
                $arr_Specialty = $this->getArraySpecialty($arr, $id_Week, $id_specialty);
                if($arr_Specialty != ''){
                	//Фромируем классы ячеек таблицы.
                	$class_row0 = 'cat-list-row0';
                	$class_row1 = 'cat-list-row1';
                	$n = true;													//Установка флага.
                    //Формируем шапку таблии.
                    $table .= '<table class="category" style="width: 100%"><tr><th colspan="5">'.$Head.'</th></tr>';
                    //Формируем названия столбцов таблици.
                    $table .= '<tr class="inputbox"><th style="border: 1px solid black;" class="list-title">'.JText::_('REG_DOCTOR').'</th><th style="border: 1px solid black;" class="list-title">'.JText::_('REG_FULL_NAME').'</th><th style="border: 1px solid black;" class="list-title">'.JText::_('REG_BEGINNING').'</th><th style="border: 1px solid black;" class="list-title">'.JText::_('REG_THE_END').'</th><th style="border: 1px solid black;" class="list-title">'.JText::_('REG_THE_RECEIVE_LOCATION').'</th></tr>';
                    //$table .= '<tbody>';
                    foreach($arr_Specialty as $key => $value){
                        //Формируем клас строк таблици.
                        if($n){
							$cl = $class_row0;
							$n = false;
                        }
                        else{
							$cl = $class_row1;
							$n = true;
                        }
                        
                        $table .= '<tr class="'.$cl.'">';
                        $rowspan = $this->getQuantitySchedule($arr, $value['id_specialty_login'], $id_Week);
                        if($rowspan >= 2){
                            $table .= '<td rowspan="'.$rowspan.'" style="border: 1px solid black;" class="item-title">'.JText::_($value['ru_specialty']).'</td>'; 
                        }
                        else{
                            $table .= '<td style="border: 1px solid black;" class="item-title">'.JText::_($value['ru_specialty']).'</td>';  
                        } 
                        if($value['id_specialty_login'] != ''){
                            $arr_fio = $this->getArrayFio($arr, $value['id_specialty_login']);       
                            if($arr_fio != ''){
                            	$end1 = false;
                                foreach($arr_fio as $k_fio => $_fio){
                                    $arr_sh = $this->getArraySchedule($arr, $_fio['id_login']); 
                                    $rowspan = count($arr_sh);
                                    if($end1){
										$table .= '<tr class="'.$cl.'">';
			                        }
			                        $end1 = true;
                                    if($rowspan >= 2){
                                        $table .= '<td style="border: 1px solid black;" class="item-title" rowspan="'.$rowspan.'">'.$_fio['surname_login'].' ';
                                        $table .= $_fio['name_login'].' '; 
                                        $table .= $_fio['patronymic_login'].'</td>';
                                    }
                                    else{
                                        $table .= '<td style="border: 1px solid black;" class="item-title">'.$_fio['surname_login'].' ';
                                        $table .= $_fio['name_login'].' '; 
                                        $table .= $_fio['patronymic_login'].'</td>';
                                    }
                                    if($arr_sh != ''){
                                    	$end2 = false;
                                        foreach($arr as $k_a => $v_a){
                                            if($_fio['id_login'] == $v_a['id_login']){
                                            	if($end2){
													$table .= '<tr class="'.$cl.'">';
			                                    }
                                                $table .= '<td style="border: 1px solid black;" class="item-title">'.medical_registryModelAppointment_doctor::PreparationTime($v_a['with_schedule']).'</td>';
                                                $table .= '<td style="border: 1px solid black;" class="item-title">'.medical_registryModelAppointment_doctor::PreparationTime($v_a['to_schedule']).'</td>';
                                                $table .= '<td style="border: 1px solid black;" class="item-title">'.$v_a['cabinet_schedule'].'</td></tr>';
                                                $end2 = true;
                                            }   
                                        }
                                    }
                                }
                                unset($k_fio, $_fio);
                            }
                        }
                    }
                    unset($key, $value);
                }
                //$table .= '</tbody>';
                //Формируем концовку таблици.
                $table .= '</table>';
                return $table; 
            }
        }
    }
    
    
    //Метод получения специальностей на день недели если есть.
    public function getArraySpecialty($arr = null, $id_Week = null, $id_specialty = null){
        if($id_Week != null && $arr != null){
            foreach($arr as $key => $value){
                if($id_specialty != null){
                    if($value['id_week'] == $id_Week && $value['id_specialty_login'] == $id_specialty){
                        $rr[$value['id_specialty_login']]['id_specialty_login'] = $value['id_specialty_login'];
                        $rr[$value['id_specialty_login']]['ru_specialty'] = JText::_($value['ru_specialty']);
                    }
                }
                else{
                    if($value['id_week'] == $id_Week){
                        $rr[$value['id_specialty_login']]['id_specialty_login'] = $value['id_specialty_login'];
                        $rr[$value['id_specialty_login']]['ru_specialty'] = JText::_($value['ru_specialty']);
                    }
                }     
            }
            unset($key, $value);
            return $rr; 
        }
         if($arr != null){
             foreach($arr as $key => $value){
                if($id_specialty != null){
                    if($value['id_specialty_login'] == $id_specialty){
                        $rr[$value['id_specialty_login']]['id_specialty_login'] = $value['id_specialty_login'];
                        $rr[$value['id_specialty_login']]['ru_specialty'] = JText::_($value['ru_specialty']);
                    }
                }
                else{
                    $rr[$value['id_specialty_login']]['id_specialty_login'] = $value['id_specialty_login'];
                    $rr[$value['id_specialty_login']]['ru_specialty'] = JText::_($value['ru_specialty']);
                }     
            }
            unset($key, $value);
            return $rr;
         }
    }
     
    
    //Метод получения количества разписаний для формирования полей таблицы.
    public function getQuantitySchedule($arr = null, $specialty = null, $id_Week = null){
        if($arr != null && $specialty != null && $id_Week != null){
            foreach($arr as $key => $value){
                if($value['id_specialty_login'] == $specialty){
                    $arr_f[] = $value['id_specialty_login'];
                }
            }
            unset($key, $value);
        }   return count($arr_f);
    }
    
    
    //Метод получения фамилий по дате и специальносте.
    public function getArrayFio($arr = null, $specialty = null){
        if($arr != null && $specialty != null){
            foreach($arr as $key => $value){
                if($value['id_specialty_login'] == $specialty){ 
                    $ret[$value['id_login']]['id_specialty_login'] = $value['id_specialty_login'];
                    $ret[$value['id_login']]['surname_login'] = $value['surname_login']; 
                    $ret[$value['id_login']]['name_login'] = $value['name_login'];
                    $ret[$value['id_login']]['patronymic_login'] = $value['patronymic_login'];
                    $ret[$value['id_login']]['id_login'] = $value['id_login']; 
                }
            }
            unset($key, $value);
            return $ret;
        }
    }
    
    
    
    //Метод получения массива расписаний по фамилии.
    public function getArraySchedule($arr = null, $Id = null){
        if($arr != null && $Id != null){
            foreach($arr as $key => $value){
                if($value['id_login'] == $Id){
                    $ret[] = $value;
                }
            }
            unset($key, $value);
            return $ret; 
        }
    }
    
    
    //Метод записи в расписание шаблона раписаний на неделю.
    public function ScheduleTemplateRecord($date = null, $id_login = null){
        if($date != ''){
            $d = new data();                                                                            //Получаем объект дата.
            $db = & JFactory::getDbo();
            $Session = &JFactory::getSession();
            $id_s = $Session->get('Medical_Registry_id'); 
            $id_login_cr = $id_s[0]['id_login'];                                                        //Идентификатор текущего пользователя. 
            $nameTableSchedule = $db->nameQuote('#__registry_schedule');                                //Имя таблици расписаний.
             
            $objSchedule = $this->getInstance('medical_registryModelSchedule');                         //Получаем объкет модели расписаний.
            $arr_date =  $objSchedule->getArrayDate($date);                                             //Массив дат на неделю.
            foreach($arr_date as $k_d => $v_d){                                                         //Обходим каждую дату недели.
                $arr_Template = $this->getListTemplate($k_d, $id_login);                                //Получеам массив шаблонов на день недели для пользователя.
                if($id_login != null){	                                                                //Выполняем удаление.
					$query = 'DELETE FROM
	                			'.$nameTableSchedule.'
	                            WHERE
	                            date_schedule = \''.$v_d.'\' AND
	                            id_login_schedule = \''.$id_login.'\'';                                                                    
	                $db->setQuery($query);
	                $db->execute();
                }
                
                if(                                                          							//Если есть элементы массива, то выполяем обработку.
                count($arr_Template) >= 1
                && $id_login != null
                ){    
                    foreach($arr_Template as $k_t => $v_t){                                             //Обходим массив шаблона.                   
                        $query = 'INSERT INTO 
                                    '.$nameTableSchedule.'
                                    SET 
                                    id_login_schedule = \''.$v_t['id_login_schedule'].'\',
                                    date_schedule = \''.$v_d.'\',
                                    with_schedule = \''.$v_t['with_schedule'].'\',
                                    to_schedule = \''.$v_t['to_schedule'].'\',
                                    cabinet_schedule = \''.$v_t['cabinet_schedule'].'\',
                                    hidden_flag_schedule = '.$v_t['hidden_flag_schedule'].',
                                    id_create_schedule = \''.$id_login_cr.'\',
                                    data_create_schedule = \''.$d->data_i.'\',
                                    time_create_schedule = \''.$d->time.'\',
                                    ip_create_schedule = \''.$_SERVER['REMOTE_ADDR'].'\'';              //Добавление из шаблона.
                        $db->setQuery($query);
                        $db->execute();   
                    }
                    unset($k_t, $v_t);
                    
                }
                
            }
            unset($k_d, $v_d);
        }
    }
    
    
    
    //Метод формирования кнопки отправки сообщения пользователю
    public function getSendMessage($arr_patient = null){
		if(
		$arr_patient['mail'] != '' || $arr_patient['phone'] != ''									//Проверка наличмя телефона и почтового адреса.
		){
          	$param['id'] = 'idmessage';
			$ret .= '<input type="button" class="button" title="'.JText::_('REG_TO_SEND_A_MESSAGE').' '.JText::_('REG_FOR_COMMUNICATION_WITH_THE_PATIENT').'" onclick=" $(\'#'.$param['id'].'modal\').arcticmodal();"  value="'.JText::_('REG_TO_SEND_A_MESSAGE').'">'."\n";
			
            $form .= JText::_('REG_MESSAGE_TO_PATIENT');
            $form .= '<br/>';
            $form .= '<b>';
            $form .= $arr_patient['surname'];
            $form .= '<br/>';
            $form .= $arr_patient['name'].' '.$arr_patient['patronymic'];
            $form .= '</b>';
            if($arr_patient['mail'] != ''){																//Если есть почтовый адрес, то показываем его.
				$form .= '<br/>';
            	$form .= JText::_('REG_EMAIL').' - '. $arr_patient['mail'];
            }
            if($arr_patient['phone'] != ''){															//Если есть телефон, то показываем его.
				$form .= '<br/>';
            	$form .= JText::_('REG_PHONE').' - '. $arr_patient['phone'];
            }
            $form .= '<br/>';
            $form .= '<textarea name="message_patient" rows="10" cols="40"></textarea>'."\n";
            $form .= '<br/>';
            $form .= '<input type="submit" title="'.JText::_('REG_SENDING_A_MESSAGE_TO_THE_PATIENT').'" value="'.JText::_('REG_SEND').'" >'."\n";
            $ret .= Tinterface::getModalWindow($param['id'], Tinterface::getUniFormModal($form));
			return $ret;
		}
    }
    

    
    //Метод получения пенели управления регистрацией.
    public function getButtonRegTools($arr_patient = null){
		if(count($arr_patient) >= 1){
			$ret .= '<input type="submit" class="button" name="cancel_registration" value="'.JText::_('REG_RESET_REGISTRATION').'">'."\n";
			if(
			$arr_patient['mail'] != ''																	//Проверка наличия почтового адреса.
			|| $arr_patient['phone'] != ''																//Проверка наличия мобильного телефона.
			){
				$ret .= $this->getSendMessage($arr_patient);
			}
		}
		else{
			$ret .= '<input type="submit" class="button" name="registration" value="'.JText::_('REG_REGISTRATION').'">'."\n";
		}
		return Tinterface::getUniFormed($ret);
    }
    
    
    //Метод логирования действий пользователя.
    public function LogEvents($TypeLog = null, $category = null, $message = null, $array = null){
        Tinterface::LogEvents($TypeLog, $category, $message, $array);
    }
     
     
}