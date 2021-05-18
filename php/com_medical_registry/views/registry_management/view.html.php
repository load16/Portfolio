<?php
  // Запрет прямого доступа.
defined('_JEXEC') or die;

// Подключаем библиотеку представления Joomla.
jimport('joomla.application.component.view');
jimport('joomla.form.form');  
jimport('joomla.html.html');
//Подлючение библиотеки для элементов интерфейса.
require_once (JPATH_COMPONENT.DS.'libraries'.DS.'interface.php');
require_once (JPATH_COMPONENT.DS.'classes'.DS.'rights.php');
require_once (JPATH_ADMINISTRATOR.DS.'includes'.DS.'toolbar.php');
   
 
/**
 * HTML представление сообщения компонента Registry_Management.
 */
class medical_registryViewRegistry_Management extends JViewLegacy{
    /**
     * Сообщение.
     *
     * @var  string
     */
    protected $HeadItemManagement;              //Заглавие.
    public $DescriptionItemManagement;          //Описание задачи.
    public $ToolsItemManagement;                //Панель инструментов. 
    public $MenuSelectSpecialty;                //Меню для выбора специальности.
    public $MenuItem;
    public $ContentItem;
    public $MenuSelectDoctors;                  //Меню для выбора доктора.
    public $MenuSelectTime1;                    //Меню для выбора времени. 
    public $MenuSelectTime2;                    //Меню для выбора времени.   
    public $ViewItemManagement;                 //Просмотр результата.
    public $ViewItemDebugging;                  //Просмотр отладочного результата. 
    

    /**
     * Переопределяем метод display класса JViewLegacy.
     *
     * @param   string  $tpl  Имя файла шаблона.
     *
     * @return  void
     */
    
    public function display($tpl = null){
        //Начальние установки свойств.
        $this->HeadItemManagement = JText::_('REG_MANAGING_RECEPTION');
        parent::display($tpl);
    }
    
    
    //Метод для получения формы для выбора расписаний по дате и пользователю.
    public function getFormSelectSchedule($arr = null, $date = null, $Id = null){
        if($date != null && $Id != null && $arr != null){
            $url = & JFactory::getURI();
            $onChange = "this.form.submit();\n";    
            $ret .= JText::_('REG_THE_SCHEDULE_FOR_THE_DATES').' '.'<b>'.$date.'</b>'."<br/>\n";
            foreach($arr as $key => $value){                                    //Формируе массив для отображения.
                $kk = $value['with_schedule'].'$$'.$value['to_schedule'];
                $content[$kk] = '('.$this->PreparationTime($value['with_schedule']).' - '.$this->PreparationTime($value['to_schedule']).')   '.JText::_('REG_THE_RECEIVE_LOCATION').' - '.$value['cabinet_schedule']; 
            }    
            $ret .= JHTML::_('select.genericlist', $content, 'select_schedule'.$Id, 'class = "MenuSchedule" size=5 onChange='.$onChange)."\n";      
            return $this->getFormContent($ret);
        }
    }
    
    
    //Метод формирования меню для редактирования личного расписания.
    public function getMenuSchedule($arr = null, $date = null, $id = null, $Complete = false){
        $line['0'] = $this->getFormSelectDate($Complete);
        $line['1'] = $this->getFormSelectSchedule($arr, $date, $id);
        $ret = $this->getElementsLine($line); 
        if($Complete == false){							//Если укроченый вариант меню, то возвращаем его.
			return $line['0'];
        }
        return $ret;    
    }
    
    
    
    
    //Метод полуения формы для выбора даты для редактирования расписания.
    public function getFormSelectDate($Complete = false){
        $session = JFactory::getSession();
        $Registry_id = $session->get('Medical_Registry_id'); 
        $schedule = $session->get('Medical_Registry_schedule');
        $date_editing = $schedule['date_editing'];
        
        $id = 'created'.rand();																	//Генерируем ИД.
        $calendar .= JText::_('REG_SELECT_DATE'). '<br/>';
        $calendar .= Tinterface::getPluginDate($id, 'DataSelectSchedule', $date_editing, true);	//Формируем поле календаря.
        $calendar .= Tinterface::getEventHandlingMake($id, 'change', 'this.form.submit()');		//Формируем собитие к полю.
        
        if($date_editing != '' && (Rights::getRights('5') || Rights::getRights('4'))){      	//Если выбрана дата то показываем меню операций.
            if(Rights::getRights('4')){
                $ret .= '<input type="submit" class="button" value="'.JText::_('REG_ADD').'" name="Add"><br/>'."\n"; 
            }
            if(
            	$schedule['date_editing'] != '' && 
            	$schedule['id_schedule_editing'] != '' &&
            	Rights::getRights('6')
            ){
                $ret_c .= '<input type="submit" class="button" value="'.JText::_('REG_REMOVE').'" name="Remove">'."\n";
            }   
        }
        if($Complete == false){																	//Если укороченныое расписание, то возвращаем его.
			return $ret_c;
        }
        $ret .= $ret_c;
        $calendar = Tinterface::getUniFormed($calendar);
        $ret = Tinterface::getUniFormed($ret);
        $ret = $calendar.$ret;
        return $ret;
    }
    
    
    
    //Метод для получения формы.
    public function getForm($form = null, $id = null, $tools = null){
        return Tinterface::getForm($form, $id, '', $tools);
    }
    
    //Метод получения формы для сонтента.
    public function getFormContent($content){
        $url = & JFactory::getURI();
        $ret .= '<form action="'.$url.'" method="post" class="form-validate">'."\n";
        $ret .= $content."\n";
        $ret .= JHTML::_('form.token')."\n";                // Вставляем ТОКЕН скрытое поле в форму. 
        $ret .= '</form>'."\n";
        return $ret;
    }
    
    
    
    //Метод получения вкладок для панели управления записями на прием.
    public function getDataControlPanel($content, $id_tabs1 = null, $id_Tabs2 = null){
        if(is_array($content)){
            $onChange = "this.form.submit()";
            foreach($content as $key => $value){                //Обходим массив.
                $prefix = str_replace(' ', '_', $key);          //Готови префикс для имени.               
                if(is_array($value['Specialty'])){              //Если есть данные для специальности , то поговим их.
                    $Specialty = $value['Specialty'];           //Получаем массив данных специальностей.
                    //Готовим данные для меню.
                    $menuSpecialty = JText::_('REG_SELECT_SPECIALTY');
                    $menuSpecialty .= '<br/>';
                    $menuSpecialty .= JHTML::_('select.genericlist', $Specialty, 'Specialty'.$prefix, 'size="8" onChange="'.$onChange.'"', 'id', 'title')."\n";   
                    $var = $this->getFormContent($menuSpecialty);   //Данные одеваем в форму.
                    $menuLine['0'] = $this->getFormContent($menuSpecialty);   //Данные одеваем в форму.
                }
                if(is_array($value['Users'])){
                    $Users = $value['Users'];
                    $menuUsers = $value['Name_Specialty'];
                    $menuUsers .= '<br/>';
                    $menuUsers .= JHTML::_('select.genericlist', $Users, 'Users'.$prefix, 'size="8" onChange="'.$onChange.'"', 'id', 'title')."\n";
                    $var = $this->getFormContent($menuUsers);       //Данные одеваем в форму.
                    $menuLine['1'] = $var;   
                }
                if($value['Name_Users'] != ''){                     //Если выбран доктор то показываем его данные.
                    $menuLine['2'] .= $value['Name_Specialty'];  
                    $menuLine['2'] .= '<br/>';
                    $menuLine['2'] .= $value['Name_Users'];
                    $menuLine['2'] .= '<br/>';
                    $menuLine['2'] .= JText::_('REG_DURATION_OF_RECEPTION_A_PATIENT').':';
                    $menuLine['2'] .= '<br/>';
                    $menuLine['2'] .= $value['time_login']; 
                    $menuLine['2'] .= '<br/>';
                    $menuLine['2'] .= JText::_('REG_PHONE').':';
                    $menuLine['2'] .= '<br/>';
                    $menuLine['2'] .= $value['phone_login'];
                    $menuLine['2'] .= '<br/>';
                    $menuLine['2'] .= JText::_('REG_SKYPE').':';
                    $menuLine['2'] .= '<br/>';
                    $menuLine['2'] .= $value['skype_login'];
                    $menuLine['2'] .= '<br/>';
                    $menuLine['2'] .= JText::_('REG_EMAIL').':'; 
                    $menuLine['2'] .= '<br/>';
                    $menuLine['2'] .= $value['post_login'];
                }
                $ret[$key] = Tinterface::getElementsLine($menuLine);
                unset($menuLine);
                if($key == JText::_('REG_ALL_DOCTORS')){										//Если есть данные для вкладки ВСЕ, то показываем данные.
					$AllColumn['0'] = $value['tools_all'];
					$AllColumn['1'] = Tinterface::getTableFormat($value['data_all']);
					$ret[$key] = '<div id="idTableFormat">'.Tinterface::getElementsColumn($AllColumn).'</div>';
					
                }
                
                //Обработка вкладок 2-го уровня.
                if($content[$key]['data'] != ''){                       //Если есть вкладки второго уровня, то показываем их.
                    $tabs[JText::_('REG_DOCTOR')] = $ret[$key];
                    $dd = $content[$key]['data'];
                    foreach($dd as $k => $v){                           //Готовим данные.
                        $tabs[$k] = $v;
                    }
                    unset($v, $k, $dd); 
                    $ret[$key] = '<div>'.Tinterface::getTabs($tabs, $prefix).'</div>';   //Одеваем в вкладку 2-го уровня. 
                }
                
                if($key == 'REG_SELECTED_PATIENT'){           //Если отобран пациент, то показываем его данные.
                    if(Rights::getRights('15')){                        //Если есть права, то показываем данные.
                        $fio .= $content[$key]['surname'].' '.$content[$key]['name'].' '.$content[$key]['patronymic'];
                        $linePatient[$fio] .= '<b>'; 
                        $linePatient[$fio] .= $content[$key]['surname'].' ';
                        $linePatient[$fio] .= $content[$key]['name'].' ';
                        $linePatient[$fio] .= $content[$key]['patronymic'].' ';
                        $linePatient[$fio] .= '</b>';
                        $linePatient[$fio] .= '<br/>'; 
                        $linePatient[$fio] .= '<br/>';
                        $linePatient[$fio] .= JText::_('REG_DATE_OF_BIRTH').' - ';
                        $linePatient[$fio] .= $content[$key]['data_of_birth']; 
                        $linePatient[$fio] .= '<br/>';
                        $linePatient[$fio] .= JText::_('REG_EMAIL').' - ';
                        $linePatient[$fio] .= $content[$key]['mail'];
                        $linePatient[$fio] .= '<br/>';
                        $linePatient[$fio] .= JText::_('REG_PHONE').' - ';
                        $linePatient[$fio] .= $content[$key]['phone'];
                        $linePatient[$fio] .= '<br/>';
                        $linePatient[$fio] .= '<br/>'; 
                        $linePatient[$fio] .= JText::_('REG_ADDRESS_OF_RESIDENCE').':';
                        $linePatient[$fio] .= '<br/>'; 
                        $linePatient[$fio] .= $content[$key]['region'].' ';
                        $linePatient[$fio] .= JText::_('REG_REGION'); 
                        $linePatient[$fio] .= '<br/>';
                        $linePatient[$fio] .= $content[$key]['district'].' ';
                        $linePatient[$fio] .= JText::_('REG_DISTRICT'); 
                        $linePatient[$fio] .= '<br/>';
                        if($content[$key]['city'] != ''){
                            $linePatient[$fio] .= JText::_('REG_CITY').' '; 
                            $linePatient[$fio] .= $content[$key]['city'];
                            $linePatient[$fio] .= '<br/>';
                        }
                        if($content[$key]['village'] != ''){
                            $linePatient[$fio] .= JText::_('REG_VILLAGE').' '; 
                            $linePatient[$fio] .= $content[$key]['village'];
                            $linePatient[$fio] .= '<br/>';
                        }
                        if($content[$key]['street'] != ''){
                            $linePatient[$fio] .= JText::_('REG_STREET_HOUSE').' '; 
                            $linePatient[$fio] .= $content[$key]['street'];
                            $linePatient[$fio] .= '<br/>';
                        }
                        $target = JText::_('REG_THE_PURPOSE_OF_THE_APPOINTMENT'); 
                        $linePatient[$target] .= JText::_('REG_TYPE_OF_CONSULTATION').' - ';
                        $linePatient[$target] .= '<b>';
                        $linePatient[$target] .= mb_strtolower(JText::_($content[$key]['type']));
                        $linePatient[$target] .= '</b>';
                        $linePatient[$target] .= '<br/>';
                        $linePatient[$target] .= JText::_('REG_SHORT_DESCRIPTION_OF_THE_PURPOSE_MAKE_AN_APPOINTMENT').''; 
                        $linePatient[$target] .= '<br/>'; 
                        $linePatient[$target] .= '<textarea rows="6" cols="35"  readonly>'.$content[$key]['description_recording'].'</textarea>' ; 
                        if($content[$key]['id_login'] != ''){
                            $recorded = JText::_('REG_RECORDED');
                            $linePatient[$recorded] .= JText::_('REG_THE_PATIENT_RECORDED').':'; 
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= JText::_($content[$key]['ru_specialty']);
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= '<b>';
                            $linePatient[$recorded] .= $content[$key]['surname_login'];
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= $content[$key]['name_login'];
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= $content[$key]['patronymic_login'];
                            $linePatient[$recorded] .= '</b>'; 
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= JText::_('REG_NUMBER').' - ';
                            $linePatient[$recorded] .= '<b>';
                            $linePatient[$recorded] .= $content[$key]['data_record'];
                            $linePatient[$recorded] .= '</b>';
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= JText::_('REG_AT').' - ';
                            $linePatient[$recorded] .= '<b>'; 
                            $linePatient[$recorded] .= $content[$key]['time_record'];
                            $linePatient[$recorded] .= '</b>';
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= JText::_('REG_THE_RECEIVE_LOCATION').' - '; 
                            $linePatient[$recorded] .= $content[$key]['cabinet_schedule'];;
                            $linePatient[$recorded] .= '<br/>';
                        }
                    }
                    else{
                        if($content[$key]['id_login'] != ''){
                            $recorded = JText::_('REG_RECORDED');
                            $linePatient[$recorded] .= JText::_('REG_THE_PATIENT_RECORDED').':'; 
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= JText::_($content[$key]['ru_specialty']);
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= '<b>';
                            $linePatient[$recorded] .= $content[$key]['surname_login'];
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= $content[$key]['name_login'];
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= $content[$key]['patronymic_login'];
                            $linePatient[$recorded] .= '</b>'; 
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= JText::_('REG_NUMBER').' - ';
                            $linePatient[$recorded] .= '<b>';
                            $linePatient[$recorded] .= $content[$key]['data_record'];
                            $linePatient[$recorded] .= '</b>';
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= JText::_('REG_AT').' - ';
                            $linePatient[$recorded] .= '<b>'; 
                            $linePatient[$recorded] .= $content[$key]['time_record'];
                            $linePatient[$recorded] .= '</b>';
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= '<br/>';
                            $linePatient[$recorded] .= JText::_('REG_THE_RECEIVE_LOCATION').' - '; 
                            $linePatient[$recorded] .= $content[$key]['cabinet_schedule'];;
                            $linePatient[$recorded] .= '<br/>';  
                        }
                    }
                    if(Rights::getRights('24')){                            //Проверка прав на просмотр служебной информации.
                        $ServiceInformation = JText::_('REG_SERVICE_INFORMATION');
                        if($content[$key]['id_create'] != '0'){
                            $linePatient[$ServiceInformation] .= JText::_('REG_CREATOR').' - ';
                            $linePatient[$ServiceInformation] .= $content[$key]['id_create'];
                            $linePatient[$ServiceInformation] .= '<br/>';  
                        }
                        $linePatient[$ServiceInformation] .= JText::_('REG_CREATED_DATE').' - ';
                        $linePatient[$ServiceInformation] .= $content[$key]['data_create'];
                        $linePatient[$ServiceInformation] .= '<br/>';
                        $linePatient[$ServiceInformation] .= JText::_('REG_CREATION_TIME').' - ';
                        $linePatient[$ServiceInformation] .= $content[$key]['time_create'];
                        $linePatient[$ServiceInformation] .= '<br/>';
                        $linePatient[$ServiceInformation] .= JText::_('REG_THE_IP_CREATOR').' - ';
                        $linePatient[$ServiceInformation] .= $content[$key]['ip_create'];
                        $linePatient[$ServiceInformation] .= '<br/>';
                        if($content[$key]['data_modification'] != '0'){
                            $linePatient[$ServiceInformation] .= '<br/>'; 
                            $linePatient[$ServiceInformation] .= JText::_('REG_MODIFICATOR').' - ';
                            $linePatient[$ServiceInformation] .= $content[$key]['id_modification'];
                            $linePatient[$ServiceInformation] .= '<br/>'; 
                            $linePatient[$ServiceInformation] .= JText::_('REG_DATE_OF_MODIFICATION').' - ';
                            $linePatient[$ServiceInformation] .= $content[$key]['data_modification'];
                            $linePatient[$ServiceInformation] .= '<br/>';
                            $linePatient[$ServiceInformation] .= JText::_('REG_THE_TIME_OF_MODIFICATION').' - ';
                            $linePatient[$ServiceInformation] .= $content[$key]['time_modification'];
                            $linePatient[$ServiceInformation] .= '<br/>';
                            $linePatient[$ServiceInformation] .= JText::_('REG_IP_MODIFICATOR').' - ';
                            $linePatient[$ServiceInformation] .= $content[$key]['ip_modification'];
                        }
                    }
                    $ret[$key] = Tinterface::getTabs($linePatient, 'patient');
                    unset($linePatient);       
                }      
            }
            
            unset($key, $value);

            //return Tinterface::getTabs($ret, '1level', $id_tabs1);                //Возвращаем во вкладках.
            return Tinterface::getTabs($ret, '1level');                             //Возвращаем во вкладках. 
        }
    }
    
    
    
    //Метод валидации вводиммых данных
    public function ValidationArray($arr = null, $message = false){
        if($arr != null){
            foreach($arr as $key => $value){
                if(Tinterface::Validation($value, $message) == false){
                    unset($arr, $key, $value);
                    return false;
                }
            }
            unset($arr, $key, $value);
            return true;
        }
    }
    
    
    
    
    
    //Метод приготовки блока к печати.
    public function getDivPrint($content = null, $id = null){
        return Tinterface::getDivPrint($content, $id);
    }
    
    

    //Метод постройки элементов в рад.
    public function getElementsLine($arr = null){
        return Tinterface::getElementsLine($arr);
    }
    
    //Метод постройки элементов в столбик.
    public function getElementsColumn($arr = null){
        return Tinterface::getElementsColumn($arr);
    }
    
    //Метод размещения элемента по центру.
    public function getElementsСenter($content = null){
        return Tinterface::getElementsСenter($content);
    }
    
    //Метод размещения элемента в верху.
    public function getElementsUP($content = null){
        return Tinterface::getElementsUP($content);
    }
    
    //Метод размещения элемента в внизу.
    public function getElementsDOWN($content = null){
        return Tinterface::getElementsDOWN($content);
    }
    
    
    //Метод подготовки значения времени для отображения.
    public function PreparationTime($var = null){
        if($var != null){
            $arr = explode(':', $var);
            $ret = $arr['0'].'-'.$arr['1'];
            return $ret;
        }
    }
    
    

    
}

