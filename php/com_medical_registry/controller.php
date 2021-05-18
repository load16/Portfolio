<?php
/**
 * Default Controller
 * @author Олег Борисович Дубик
 */
/**
  * Главный онтроллер сервиса "Электронная регистратура".
  */
  

// Запрет прямого доступа.
defined( '_JEXEC' ) or die;  
  
jimport('joomla.application.router');
jimport('joomla.error.log');

//Подключаем классы
require_once (JPATH_COMPONENT.DS.'classes'.DS.'aut.php');
require_once (JPATH_COMPONENT.DS.'classes'.DS.'rights.php');
require_once (JPATH_COMPONENT.DS.'libraries'.DS.'data.php'); 
 
require_once (JPATH_COMPONENT.DS.'libraries'.DS.'interface.php');   



class Medical_RegistryController extends JControllerLegacy{

    public $ModelItem;               //Ссылка на объект модели.
    public $ViewlItem;               //Ссылка на объект просмотра.
    public $SessionItem;             //Ссылка на объект сесии.
    public $AppItem;                 //Ссылка на объект приложение.
    public $PathToMyXMLFiles;        //Путь у файлам XML.
    public $LogInfo;                 //Ссылка на объект логорования информационных логов.
    public $LogErrors;               //Ссылка на объект логорования логов ошибок.  
    
    
    
    /**
     * Methot to load and display current view
     * @param Boolean $cachable
     */ 
    function display($cachable = false, $urlparams = array()){  
        $viewName = JRequest::getCmd('view');
        if($viewName == $this->default_view){
            //Инициализируем параметры.   
            $this->setParameters($viewName);
            //Загружаем данные с модели.
            $this->ViewlItem->MenuItem = $this->ModelItem->getMenuItem();
        }
        $this->ViewlItem->MenuItem = $this->ModelItem->getMenuItem();
        parent::display();
        return $this;
    }
    
    
    
    //Метод инициализация параметров.
    public function setParameters($var, $ajax = false){
        //Получаем ссылку на модель.  
        $this->ModelItem = &$this->getModel($var);
        if($ajax == false){											//Если нет AJAX запроса, то выполняем код.
			//Получаем ссылку на вюшку.  
	        $this->ViewlItem = &$this->getView($var, 'html');
        }  
        //Создаем ссылку на объект сесии.
        $this->SessionItem = &JFactory::getSession();
        //Создаем ссілку на объект приложения.
        $this->AppItem = &JFactory::getApplication();
        //Создаем ссылку на объект логирования.
        $this->LogInfo = &JLog::getInstance('Info_Medical_Registry.'.date('Y_m_d').'.log.php');
        $this->LogErrors = &JLog::getInstance('Error_Medical_Registry.'.date('Y_m_d').'.log.php'); 
        $this->UserActionsItem();
    }
    
    
    //Метод обработки действий пользователя.
    public function UserActionsItem(){
        $ManeMenu = JRequest::getVar('ManeMenu');  
        $Logout = JRequest::getVar('Logout');
        $MenuManagement = JRequest::getVar('MenuManagement');
        $sendProposal = JRequest::getVar('sendProposal');
        $reviews = JRequest::getVar('reviews');
        
        $ModelAppointment = &$this->getModel('Appointment_doctor');												//Получаем ссылку на модель Appointment_doctor. 
        $ModelManagement = &$this->getModel('Registry_management');												//Получаем ссылку на модель Appointment_doctor.     
        $ModelAppointment->DeletingObsoleteRecords();															//Удаляем устаревшие записи.
        $ModelAppointment->delObsoleteData();																	//Удаляем устаревшие блокировки.
    	
        $post = $_POST;
        if(count($post) >= 1){
			foreach($post as $k => $v){
				if(Tinterface::Validation($v, true) == false){													//Штатная валидация всех постов на враждебный код.
					die;
				}
	        }
	        unset($k, $v);
        }
        
        $post_form = Tinterface::getPostForm();                                                 				//Збор данны с формы. 
        if(count($post_form) >= 1){
			foreach($post_form as $k => $v){
				if(Tinterface::Validation($v, true) == false){	//Штатная валидация всех постов на враждебный код идущщих с форм.
					die;
				}
	        }
	        unset($k, $v);
        }
        
        if($reviews != ''){																						//обработка нажатия на кнопу ОТЗЫВ.
			print $ModelAppointment->getReviews();
        }
        if($post['REG_OPINION'] != ''){																			//Обработка фиксации отзывов.
			 JRequest::checkToken() or jexit('Invalid Token');                                  				//Проверка токена.
			 $ModelManagement->setData(Tinterface::getPostForm(), '', 'reviews');								//Добавляем запись в базу.
			 //Логирование действия. 
             Tinterface::LogEvents($this->LogInfo, 'REVIEW:', 'Send feedback to developers', Tinterface::getPostForm());
             $this->AppItem->enqueueMessage(JText::_('REG_THANK_YOU_FOR_YOUR_COOPERATION_YOUR_FEEDBACK_HAS_BEEN_SENT'));									//Выводим сообщение.
        }
        
        if($Logout != ''){
            $this->SessionItem->set('Medical_Registry_id', '');
            $this->AppItem->redirect(JRoute::_('index.php?option=com_medical_registry&view=medical_registry'));   
        }
        if($MenuManagement != ''){
            JRequest::checkToken() or jexit('Invalid Token');           //Проверка токена.
            $ret = JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Registry_ManagementTask');     
            $this->AppItem->redirect($ret);
        }
        if($ManeMenu != ''){
            JRequest::checkToken() or jexit('Invalid Token');           //Проверка токена.     
            $this->AppItem->redirect(JRoute::_('index.php?option=com_medical_registry&view=medical_registry'));
        }
        $NamePost = JRequest::getVar('NamePost');						//Получаем переменные с AJAX запроса.
        $ValuePost = JRequest::getVar('ValuePost');
        if($NamePost != '' && $ValuePost != ''){
			unset($_POST['NamePost'], $_POST['ValuePost']);				//Сбрасываем лишнее переменные.
			JRequest::setVar($NamePost, $ValuePost);					//Устанавливаем переменную.
        }
        print Tinterface::getSystemMessage();							//Выводим системные сообщения в модальном окне.    
    }
    
    
    //Задача обработки AJAX запросов.
    /*
    $parametrs - массив параметров от обработтчика действий пользователей.
    $NameTask - имя задачи.
    $key - ключ доступа к переменной массива (дополнительный параметр).
    */
    public function Ajax($parametrs = null, $NameTask = null, $key = null){
        $var_ajax = JRequest::getVar('ajax');															//Получаем флаг AJAX запроса.
        if($var_ajax != ''){																			//Если есть AJAX запрос, то обрабатываем его.
			$ModelRegistry_Management = &$this->getModel('Registry_Management');                        //Получаем ссылку на модель Registry_Management. 
	        $ModelAppointment_doctor = &$this->getModel('Appointment_doctor');							//Получаем ссылку на объект.
	        $ModelSchedule = &$this->getModel('Schedule');	
	        $Ajax = &$this->getModel('Ajax');	        												//Получаем ссылку на объект.
	        
	        $AjaxControlledId = JRequest::getVar('AjaxControlledId');									//ИД управляемого объекта.	
	        $NameModule = JRequest::getVar('NameModule');
	        $RebootTableFormat = JRequest::getVar('RebootTableFormat');									//Презегрухка таблицы
	        $cancel_id_specialty = JRequest::getVar('cancel_id_specialty');								//Сброс выбора специальности.
	        $weekdown = JRequest::getVar('weekdown');													//Переключение недель.
	        $weekup = JRequest::getVar('weekup');
	        $getTableSchedule = JRequest::getVar('getTableSchedule');
	        
	        if(																							//Обработка перезагрузки форматированной таблицы.
	        $parametrs != '' && 
	        ($RebootTableFormat != '' || 
	        $cancel_id_specialty != '' || 
	        $weekdown != '' || 
	        $weekup != '' ||
	        $getTableSchedule != ''
	        )
	        ){
				if($NameTask == 'appointment_doctor'){													//Обработка задачи "appointment_doctor".
					$Appointment = $parametrs;															//Перенос параметров.
					if($getTableSchedule == 'reboot'){													//Проверка запроса на перезагрузку таблици расписаний.
						//Формируем вкладку просмотра расписания.
		                if($Appointment['SelectDate'] != ''){
							$ScheduleElemetsLine['0'] = $this->ViewlItem->getForm($ModelRegistry_Management->getFormDate('Appointment', $Appointment['SelectDate'])); 
		                    $ScheduleElemetsLine['1'] = $this->ViewlItem->getForm($this->ModelItem->getMenuSchedule($Appointment['SelectDate'], $Appointment['ScheduleWeek']));
		                    //Поготовка кнопки печати.
		                    if($Appointment['SelectDate'] != ''|| $Appointment['ScheduleWeek'] != ''){
		                        $ScheduleElemetsLine['2'] = $this->ModelItem->getButtonPrint('PrintSchedule'); 
		                    }
		                    $ScheduleElemetsColumn['0'] = $this->ViewlItem->getToolbar($this->ViewlItem->getElementsLine($ScheduleElemetsLine));
		                    
		                    //Предварительное формирование вкладки просмотра расписаний.
		                    $tableSedule = $this->ViewlItem->getElementsLine($ScheduleElemetsLine);
		                    
		                    if($Appointment['ScheduleWeek'] != ''){
                    			//Формируем колонковый элемент просмотра расписания.
		                        $ScheduleElemetsColumn['1'] = $ModelSchedule->getTableWeekSchedule($Appointment['SelectDate'], $Appointment['id_specialty'], $Appointment['id_login']);
		                        //Формирунм колонковый элемент выбора времени.
		                        $TimeElemetsColumn = $this->ModelItem->getSelectTimeWeek($Appointment['SelectDate'], $Appointment['id_login']); 
		                        $ModelRegistry_Management->LogEvents($this->LogInfo, 'MAKE AN APPOINTMENT:', 'View weekly schedule', $Appointment); 
		                    }
		                    else{
                    			//Формируем колонковый элемент просмотра расписания. 
		                        $ScheduleElemetsColumn['1'] = $ModelSchedule->getTableSchedule(JText::_('REG_SCHEDULE_FOR_THE').' '.$Appointment['SelectDate'], $Appointment['SelectDate'], $Appointment['id_specialty'], $Appointment['id_login']);
		                        //Формирунм колонковый элемент выбора времени. 
		                        $TimeElemetsColumn = $this->ModelItem->getSelectTime($Appointment['SelectDate'], $Appointment['id_login']); 
		                        $ModelRegistry_Management->LogEvents($this->LogInfo, 'MAKE AN APPOINTMENT:', 'View the schedule of the date', $Appointment);
		                    }
		                    
		                    //Подготовка к печати расписания.
		                    $ScheduleElemetsColumn['1'] = $this->ViewlItem->getDivPrint($ScheduleElemetsColumn['1'], 'PrintSchedule');
		                    //Формирование вкладки просмотра расписаний.
		                    
		                    $tableSedule = $this->ViewlItem->getElementsColumn($ScheduleElemetsColumn); 
		                    unset($Appointment, $parametrs);
		                    $Ajax->getAjaxData($tableSedule);    
		                }	
					}
					else{
						//Готовым данные для ajax запроса для вкладки расписание.
		                $url = &JFactory::getURI();
    					$ControlId = Tinterface::getIdFromLine(JText::_('REG_SCHEDULE'));	//ИД перезагружаемого объекта.
    					$NameModule = true;
    					$NamePost = 'getTableSchedule';										//Готовим данные для AJAX запроса.
    					$ValuePost = 'reboot';												//Посылаемые данные.
    					$LoadImg = JUri::root(true).'/components/com_medical_registry/assets/images/plex/huge_load_style.GIF';
    					$FailureImg = JUri::root(true).'/components/com_medical_registry/assets/images/plex/huge_failure.GIF';
    					$NullImg = JUri::root(true).'/components/com_medical_registry/assets/images/plex/huge_no_data.GIF';
						$tt['0'] = '<div onmousemove="AjaxQuery.AjaxPostAsync(\''.$NamePost.'\',\''.$ValuePost.'\',\''.$ControlId.'\',\''.$NameModule.'\',\''.$url.'\',\''.$LoadImg.'\',\''.$FailureImg.'\',\''.$NullImg.'\');">'.$ModelAppointment_doctor->getToolsTable($Appointment['SelectDate'], $Appointment['id_specialty']).'</div>';
						//$tt['0'] = $ModelAppointment_doctor->getToolsTable($Appointment['SelectDate'], $Appointment['id_specialty']);
						
						$tt['1'] = $this->ModelItem->getMenuTimeTable($Appointment['SelectDate'], $Appointment['id_specialty']);
            			$tt['1'] = $this->ViewlItem->getTableFixedBlockSize($tt['1']);
            			$TableFormat = Tinterface::getElementsColumn($tt);
            			unset($tt, $Appointment, $parametrs);												//Освобождение памяти.
            			$Ajax->getAjaxData($TableFormat); 
					}						
				}
				if($NameTask == 'Medical_Registry_Records' && $key == 'REG_ALL_DOCTORS'){
					
					if($parametrs[JText::_('REG_ALL_DOCTORS')]['ControlPanelPatient'] != ''){			//Если есть панел управления пациентом, то формируем ее.
						$tools_all['0'] = '<div class="inputbox">'.Tinterface::getUniFormed($parametrs[JText::_('REG_ALL_DOCTORS')]['ControlPanelPatient']).'</div>';
					}
					$tools_all['1'] = $ModelAppointment_doctor->getToolsTable($parametrs[JText::_('REG_ALL_DOCTORS')]['d'], $parametrs[JText::_('REG_ALL_DOCTORS')]['id_Specialty']);
					$tt['0'] = Tinterface::getElementsColumn($tools_all);
					$tt['1'] = $ModelAppointment_doctor->getMenuTimeTable($parametrs[JText::_('REG_ALL_DOCTORS')]['d'], $parametrs[JText::_('REG_ALL_DOCTORS')]['id_Specialty'], true);
					$tt['1'] = Tinterface::getTableFormat($tt['1']);
					
					$TableFormat = Tinterface::getElementsColumn($tt);					
					unset($tt, $parametrs, $tools_all);
					$Ajax->getAjaxData($TableFormat); 
				}
	        }
	        
			$ModalWindow = JRequest::getVar('ModalWindow');
	        if($ModalWindow != '' && $AjaxControlledId != ''){											//Обработка получения модального окна.
				$arr = explode('$$', $ModalWindow);														//Разделяем переменную.
				$list_data_schedules = $ModelSchedule->getDataSchedule($arr['1'], $arr['0'], $NameModule);			//Получаем расписание на дату и на пользователя.
				$ArraySchedule = $ModelAppointment_doctor->getArraySchedule($arr['1'], $arr['0'],$NameModule);		//Полуаем список временных позиций.
				//Получаем модальное окно.
				$Moddal_Window = "\t"."\t".'<div onclick="$(\'#'.$AjaxControlledId.'\').arcticmodal(\'close\');" class="box-modal_close arcticmodal-close">X</div>';
				$Moddal_Window .= "\t"."\t".$ModelAppointment_doctor->getDataForModalWindodw($arr['1'], $list_data_schedules, $ArraySchedule, $NameModule);
				$arr_wiev = $ModelAppointment_doctor->getSumTimeSegments($list_data_schedules);
				$view = $ModelAppointment_doctor->PreparationTime($arr_wiev['with_schedule']).'<br/>';
				$view .= $ModelAppointment_doctor->PreparationTime($arr_wiev['to_schedule']);
				$Ajax->getAjaxData($Moddal_Window);														//Возвращаем результат.
	        }
        }
    }
    

    
    
     //Метод получения массива POST с формы.
     public function getPostForm(){
         return Tinterface::getPostForm();
     }
    
}