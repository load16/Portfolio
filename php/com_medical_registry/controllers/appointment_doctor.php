<?php
/**
 * Default Controller
 * @author Олег Борисович Дубик
 */
/**
  * Контроллер сервиса "Электронная регистратура" для задачи "Appointment_doctor".
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



class Medical_RegistryControllerAppointment_doctor extends Medical_RegistryController{
    
    
    //Задача записи на прием.
    function Appointment_doctorTask($cachable = false, $urlparams = array()){
        $this->setParameters('Appointment_doctor');
        $this->ActionAppointment();                                                                 //Обработка действий пользователя.
        $idCmd = JRequest::getCmd('u');                                                             //Сбор комманд контроллера.
        $dateCmd = JRequest::getCmd('d');
        $timeCmd = JRequest::getCmd('t'); 
        $mailCmd = JRequest::getCmd('m');
        if(                                                                                             //Проверка на предмат ссылки активации.  
	        ($idCmd != '' && $dateCmd != '' && $timeCmd != '' && $mailCmd != '') &&
	        $this->ModelItem->ActivationRecord($idCmd, $dateCmd, $timeCmd, $mailCmd)
        ){                     
            $d = new data();
            $timeCmd = $d->conversion_seconds_data($timeCmd);
            $arr = $this->ModelItem->DataRecordedPatient($idCmd, $dateCmd, $timeCmd);
            $this->ViewlItem->HeadItemAppointment = $arr['surname'].' '.$arr['name'].' '.$arr['patronymic'];
            $this->ViewlItem->DescriptionItemAppointment .= JText::_('REG_YOU_MAKE_AN_APPOINTMENT_WITH_THE_DOCTOR').' '.mb_strtolower(JText::_($arr['ru_specialty'])).'<br/>';
            $this->ViewlItem->DescriptionItemAppointment .= $arr['surname_login'].' '.$arr['name_login'].' '.$arr['patronymic_login'];
            $this->ViewlItem->ToolsItemAppointment .= JText::_('REG_RECORDING_DATE').' - '.$arr['data_record'].','.'<br/>';
            $this->ViewlItem->ToolsItemAppointment .= JText::_('REG_RECORDING_TIME').' - '.$this->ModelItem->PreparationTime($arr['time_record']).','.'<br/>';
            if($arr['id_type'] != 3){
                $this->ViewlItem->ToolsItemAppointment .= JText::_('REG_THE_RECEIVE_LOCATION').' - '.$arr['cabinet_schedule'].','.'<br/>'; 
            }
            else{
                $this->ViewlItem->ToolsItemAppointment .= JText::_('REG_AT_THE_SPECIFIED_TIME_TO_BE_AT_THE_COMPUTER').','.'<br/>';
                $this->ViewlItem->ToolsItemAppointment .= JText::_('REG_SKYPE_FOR_COMMUNICATION').' - '.$arr['skype_login'];
            }
        }
        else{
            $Appointment = $this->SessionItem->get('Medical_Registry_Appointment');
            $ModelRegistry_Management = &$this->getModel('Registry_Management');                        //Получаем ссылку на модель Registry_Management. 
            $ModelSchedule = &$this->getModel('Schedule');                                              //Получаем ссылку на модель Schedule. 
                                                                                                                      
            if($Appointment['Registration'] != ''){
            	$arr_rec = explode('$$', $Appointment['SelectDateTime']);
            	$arr_user = $this->ModelItem->getScheduleArray($arr_rec['0'], $arr_rec['1'], $arr_rec['2']);
                $this->ViewlItem->HeadItemAppointment = JText::_('REG_ENTER_PATIENT_DATA_TO_MAKE_AN_APPOINTMENT_TT');
                $message = JText::_('REG_MESSAGE_ONE');													//Формируем сообщение пользователю.
                //Показываем его.
                $this->ViewlItem->HeadItemAppointment .= Tinterface::getTimeModalWindow('message', $message, '1000');
                //$this->ViewlItem->HeadItemAppointment = $message;
                
                
                $this->ViewlItem->DescriptionItemAppointment = JText::_('REG_RECORD_ON_RECEPTION_TO_THE_DOCTOR').'<br/>';
                $this->ViewlItem->DescriptionItemAppointment .= $arr_user['surname_login'].' '.$arr_user['name_login'].' '.$arr_user['patronymic_login'].'<br/>';
                $this->ViewlItem->DescriptionItemAppointment .= JText::_($arr_user['ru_specialty']).'<br/>';
                $this->ViewlItem->DescriptionItemAppointment .= JText::_('REG_DATE').' '.'<b>'.$arr_rec['0'].'</b>'.'<br/>';
                $this->ViewlItem->DescriptionItemAppointment .= JText::_('REG_TIME').' '.'<b>'.$this->ModelItem->PreparationTime($arr_rec['1']).'</b>';
                $this->ViewlItem->DescriptionItemAppointment  = '<div class="inputbox">'.$this->ViewlItem->DescriptionItemAppointment.'</div>';
                if($Appointment['patient_temp'] != ''){                                                              //Если есть временные данные, то показываем их.
                    $objForm = $this->ModelItem->getForm('Patient', 'PatientData', $Appointment['patient_temp']); 
                }
                else{
                    $objForm = $this->ModelItem->getForm('Patient', 'PatientData', $Appointment['patient']); 
                }
                $this->ViewlItem->MenuItemAppointment = $this->ViewlItem->getFormXml($objForm, 'Registration', '00:10:00');   
            }
            else{
                $this->ViewlItem->HeadItemAppointment = JText::_('REG_RECORD_ON_RECEPTION_TO_THE_DOCTOR');
                $this->ViewlItem->DescriptionItemAppointment = JText::_('REG_BE_CAREFUL_ON_THE_PAGE_THERE_ARE_TABS');
                $this->ViewlItem->ToolsItemAppointment = $this->ViewlItem->getForm($this->ModelItem->getMenuTools());//Показываем панель инструментов.
                //Формируем вкладку записи на прием.
                $tt = $this->ModelItem->getMenuTimeTable($Appointment['SelectDate'], $Appointment['id_specialty']);
                
                //Готовым данные для ajax запроса для вкладки расписание.
                $url = &JFactory::getURI();
    			$ControlId = Tinterface::getIdFromLine(JText::_('REG_SCHEDULE'));	//ИД перезагружаемого объекта.
    			$NameModule = true;
    			$NamePost = 'getTableSchedule';										//Готовим данные для AJAX запроса.
    			$ValuePost = 'reboot';												//Посылаемые данные.
    			$LoadImg = JUri::root(true).'/components/com_medical_registry/assets/images/plex/huge_load_style.GIF';
    			$FailureImg = JUri::root(true).'/components/com_medical_registry/assets/images/plex/huge_failure.GIF';
    			$NullImg = JUri::root(true).'/components/com_medical_registry/assets/images/plex/huge_no_data.GIF';
                
                //$arrTabs[JText::_('REG_SELECTED_TIME')] .= $this->ModelItem->getToolsTable($Appointment['SelectDate'], $Appointment['id_specialty']);               
                $arrTabs[JText::_('REG_SELECTED_TIME')] .= '<div onmousemove="AjaxQuery.AjaxPostAsync(\''.$NamePost.'\',\''.$ValuePost.'\',\''.$ControlId.'\',\''.$NameModule.'\',\''.$url.'\',\''.$LoadImg.'\',\''.$FailureImg.'\',\''.$NullImg.'\');">'.$this->ModelItem->getToolsTable($Appointment['SelectDate'], $Appointment['id_specialty']).'</div>';
            	
            	
            	$arrTabs[JText::_('REG_SELECTED_TIME')] .= $this->ViewlItem->getTableFixedBlockSize($tt);
            	$arrTabs[JText::_('REG_SELECTED_TIME')] = '<div id="idTableFormat">'.$arrTabs[JText::_('REG_SELECTED_TIME')].'</div>'; 
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
                    $arrTabs[JText::_('REG_SCHEDULE')] = $this->ViewlItem->getElementsLine($ScheduleElemetsLine);
                    
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
                    $arrTabs[JText::_('REG_SCHEDULE')] = $this->ViewlItem->getElementsColumn($ScheduleElemetsColumn);    
                }               
                $this->ViewlItem->MenuItemAppointment = $this->ViewlItem->getTabs($arrTabs);                //Показываем вкладки.
            }
            $this->ViewlItem->ViewItemAppointment = $Appointment;
            $this->SessionItem->set('Medical_Registry_Appointment', $Appointment);                          //Фиксация переменной.   
        }
        $this->ViewlItem->display($cachable, $urlparams);  
        return $this;    
    }
    
    
    
    //Метод обработки действий пользователя Appointment
     private function ActionAppointment(){
         $Appointment = $this->SessionItem->get('Medical_Registry_Appointment');
         $ModelRegistry_Management = &$this->getModel('Registry_Management');                        //Получаем ссылку на модель Registry_Management. 
         $ModelSchedule = &$this->getModel('Schedule');                                              //Получаем ссылку на модель Schedule.  
         $dd = new data();
         $id_specialty = JRequest::getVar('id_specialtyAppointment');
         $id_login = JRequest::getVar('id_loginAppointment');
         $SelectDate = JRequest::getVar('SelectDateAppointment');
         if(JRequest::getVar('SelectDateAppointment1') != ''){                                      //Если есть выбор даты во второй вкладке, то фиксируем.
             $SelectDate = JRequest::getVar('SelectDateAppointment1');
         }
         $SelectDateTime = JRequest::getVar('SelectDateTime');
         $MakeAppointment = JRequest::getVar('MakeAppointment');  
         $ScheduleWeek = JRequest::getVar('ScheduleWeek');
         $ScheduleDate = JRequest::getVar('ScheduleDate');
         $Registration = JRequest::getVar('Registration');
         $Cancel = JRequest::getVar('Cancel');
         $Save = JRequest::getVar('Save');
         $weekdown = JRequest::getVar('weekdown');
         $weekup = JRequest::getVar('weekup');
         $cancel_id_specialty = JRequest::getVar('cancel_id_specialty');
                  
         if($weekdown != ''){																		//Обработка переключение недель.
			 $Appointment['SelectDate'] = $dd->getDateWeekDown($Appointment['SelectDate']);
         }
         if($weekup != ''){
			 $Appointment['SelectDate'] = $dd->getDateWeekUp($Appointment['SelectDate']);
         }
         
         if($SelectDate != ''){                                                             		//Обработка выбора даты.
         	$Appointment['SelectDate'] = $SelectDate;
            unset($Appointment['ScheduleWeek'], $Appointment['SelectDateTime'], $Appointment['id_login']); 
         }
         
         if($Cancel != ''){																			//Обработка отмены регистрации.
         	//Подготоыка к удалению блокировки.
         	$SelectDateTime = $Appointment['SelectDateTime'];
         	$arr_SelectDateTime = explode('$$', $SelectDateTime);
         	//Удаление блокировки.
         	$this->ModelItem->delObsoleteData($arr_SelectDateTime['2'], $arr_SelectDateTime['0'], $arr_SelectDateTime['1']);
         	unset($Appointment['Registration'], $Appointment['patient_temp'], $Appointment['SelectDateTime'], $Appointment['id_login'], $arr_SelectDateTime, $SelectDateTime);
         }
         
         
         if($Appointment['SelectDate'] == ''){														//Если не выбранна дата, то устанавливаем текущую.
		 	$Appointment['SelectDate'] = $dd->data_i;
		 	$Appointment['ScheduleWeek'] = $dd->data_i;												//Активируем расписание по умолчанию на неделю.
         }
          
         if($Appointment['SelectDate'] != ''){
         	if($ScheduleWeek != ''){                                                       			//Обработка выбора расписания на неделю. 
				$Appointment['ScheduleWeek'] = $Appointment['SelectDate'];
            }
            if($ScheduleDate != ''){                                                       			//Обработка выбора расисания на дату.
            	unset($Appointment['ScheduleWeek']);
            }
         }
         
         if($id_specialty != ''){																	//Обработка выбора фильтра.
			 $Appointment['id_specialty'] = $id_specialty;
         }
         if($cancel_id_specialty != ''){
			 unset($Appointment['id_specialty']);
         }
         
         if($SelectDateTime != ''){																	//Обработка выбора времени приема.
			  $arr_SelectDateTime = explode('$$', $SelectDateTime);
			  //Валидация времени и даты.
			  if(
			  $this->ModelItem->ValidationReceptionRecord($arr_SelectDateTime['2'], $SelectDateTime, '', true)
			  && $this->ModelItem->ValidationLock($arr_SelectDateTime['2'], $arr_SelectDateTime['0'], $arr_SelectDateTime['1'], true)
			  ){
			  	  $Appointment['SelectDateTime'] = $SelectDateTime;
			  	  $Appointment['Registration'] = true;
				  $Appointment['id_login'] = $arr_SelectDateTime['2'];
				  //Установка блокировки.
				  $this->ModelItem->CreateWriteLock($arr_SelectDateTime['2'], $arr_SelectDateTime['0'], $arr_SelectDateTime['1']);
			  }
         }
          
         if($Appointment['Registration'] == true){
             if($Save != ''){                                                                       //Обработка сохранения регистрационных данных.
                 JRequest::checkToken() or jexit('Invalid Token');                                  //Проверка токена.
                 $post = Tinterface::getPostForm();                                                 //Збор данны с формы. 
                 $Appointment['patient_temp'] = $post;												//Временное сохранение.
                 if($this->ModelItem->ValidationKcaptcha($_POST['kcaptcha_value'], true)){			//Валидация Kcaptcha
                     $Appointment['patient'] = $post;                     							//То сохраняем введенные данные
                     if($Appointment['patient'] != '' && $Appointment['SelectDateTime'] != ''){		//Обработка записи на прием. 
	                     if(																		//Если прошла валидация, то выполняем запись.
	                     $this->ModelItem->ValidationReceptionRecord($Appointment['id_login'], $Appointment['SelectDateTime'], $Appointment['patient'], true, true, true)
	                     ){
	                         $this->ModelItem->MakingAppointment($Appointment['id_login'], $Appointment['SelectDateTime'], $Appointment['patient']);
	                         $this->AppItem->enqueueMessage(JText::_('REG_YOU_MAKE_AN_APPOINTMENT_YOUR_ENTRY_IS_NOT_YET_ACTIVE'));
	                         $this->AppItem->enqueueMessage(JText::_('REG_TO_ACTIVATE_RECORDING_CLICK_ON_THE_LINK_SENT_TO_YOU_IN_THE_MAIL'));
	                         //Логирование действия. 
	                         $ModelRegistry_Management->LogEvents($this->LogInfo, 'MAKE AN APPOINTMENT:', 'А patient can make an appointment', $Appointment);
	                         //Подготоыка к удалению блокировки.
	                         $SelectDateTime = $Appointment['SelectDateTime'];
         					 $arr_SelectDateTime = explode('$$', $SelectDateTime);
         					 //Удаление блокировки.
         					 $this->ModelItem->delObsoleteData($arr_SelectDateTime['2'], $arr_SelectDateTime['0'], $arr_SelectDateTime['1']);
         					 unset($SelectDateTime, $arr_SelectDateTime);							//Удаляем данные.
	                         unset($Appointment['SelectDateTime'], $Appointment['patient']);
	                         unset($Appointment['Registration'], $Appointment['patient_temp']);
	                     }
	                 }     
                 }
             }
         }
         $this->SessionItem->set('Medical_Registry_Appointment', $Appointment);        				//Фиксация переменной.
         $this->Ajax($Appointment, 'appointment_doctor'); 
     }
     
    
    
}