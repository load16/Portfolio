<?php
/**
 * Default Controller
 * @author Олег Борисович Дубик
 */
/**
  * Контроллер сервиса "Электронная регистратура" для задачи "Registry_management" , "Records".
  */
  

// Запрет прямого доступа.
defined( '_JEXEC' ) or die;  
  

class Medical_RegistryControllerRecords extends Medical_RegistryController{

   
    
    //Задача управления регистратурой.
    function Registry_ManagementTask($cachable = false, $urlparams = array()){
        $this->setParameters('Registry_Management');   
        if(JRequest::getVar('logout') != ''){                                       //Обработка комманды logout.
            Aut::logout();
        }
              
        if(Aut::login()){                                                      		//Проверка аутентифиации пользователя.
        	//Инициализация переменных.
            $TaskManagement = JRequest::getCmd('TaskManagement');
            $session = JFactory::getSession(); 
            $session_data = $session->get('Medical_Registry_data');
                
                
            //Выполнение задач по управлению записями на прием. 
            if(                                                                             
                $TaskManagement == 'Records' 
                && (Rights::getRights('15') || Rights::getRights('16') || Rights::getRights('17') || Rights::getRights('18'))
            ){
            	$this->ActionRecords();                                                                         //Обработка действий пользователя.
                $Records = $this->SessionItem->get('Medical_Registry_Records');                                 //Инициализация переменных.
                $data = $Records['data'];
                if((JRequest::getVar('СhangedRecordedt') != '' ||
                    JRequest::getVar('registration')) &&
                    Rights::getRights('16', true)                                                               //Проверка прав.
                ){                                                                                              //Обработка регистрации пациента.
                	$this->ViewlItem->DescriptionItemManagement = JText::_('REG_REGISTER_A_PATIENT_TO_MAKE_AN_APPOINTMENT');
                    $form = $this->ModelItem->getForm('', 'Record', 'RegisterData', $data['REG_SELECTED_PATIENT']);
                    $this->ViewlItem->MenuItem = $this->ViewlItem->getForm($form, 'registration');
                    $this->ViewlItem->MenuItem = '<div style="text-align: center;">'.$this->ViewlItem->MenuItem.'</div>';//Располагаем фому поцентру.
                }
                else{                                                                                           //Иначе ведем обычную обработку.
                	$this->ViewlItem->DescriptionItemManagement = JText::_('REG_MANAGING_APPOINTMENTS');        //Инициализация переменных.
                    //$this->ViewlItem->ToolsItemManagement = $this->ViewlItem->getFormContent($this->ModelItem->getMenuTools().$this->ModelItem->getToolsManagementRecords());
                    $this->ViewlItem->ToolsItemManagement = $this->ViewlItem->getFormContent($this->ModelItem->getMenuTools().$this->ModelItem->getButtonRegTools($data['REG_SELECTED_PATIENT']));

                        
                    $data = $this->ModelItem->getDataControlPanel($data, true);                                 //Получаем данные для вкладок
                    $this->ViewlItem->MenuItem = '<div style="text-align: center; margin: auto;">'.$this->ViewlItem->getDataControlPanel($data, $Records['id_Tabs1']).'</div>';
                }
                $this->ViewlItem->ViewItemDebugging = $Records;   
                $Records['data'] = $data;
                $this->SessionItem->set('Medical_Registry_Records', $Records);                                  //Фиксация переменной.           
            }
            $this->ViewlItem->display($cachable, $urlparams);
        } 
        return $this; 
    }
    
    
     
    
    //Метод обработки действий пользователя Records
    private function ActionRecords(){
        $Records = $this->SessionItem->get('Medical_Registry_Records');                 //Инициализация переменных. 
        $data = $Records['data'];
        $dd = new data();
        $ModelAppointment = &$this->getModel('Appointment_doctor');
        $registration = JRequest::getVar('registration');
        $cancel_registration = JRequest::getVar('cancel_registration');
        $SelectDateTime = JRequest::getVar('SelectDateTime');
        $Save = JRequest::getVar('Save');
        $DeleteRecordedt = JRequest::getVar('DeleteRecordedt');
        $OffRecordedt = JRequest::getVar('OffRecordedt');
        $ActivateRecordedt = JRequest::getVar('ActivateRecordedt'); 
        $TransferRecordedt = JRequest::getVar('TransferRecordedt');
        $MakeAppointment = JRequest::getVar('MakeAppointment');
        $message_patient = JRequest::getVar('message_patient');
        
        $id_specialtyAppointment = JRequest::getVar('id_specialtyAppointment');
        $weekdown = JRequest::getVar('weekdown');
        $weekup = JRequest::getVar('weekup');
        $cancel_id_specialty = JRequest::getVar('cancel_id_specialty');
        $SelectDateAppointment1 = JRequest::getVar('SelectDateAppointment1');
            
        $arr_SelectDateTime = explode('$$', $SelectDateTime);                           //Разделяем полученное время приема.   
        if(count($arr_SelectDateTime) >= 2){                                            //Если выбранно время приемя, то готовим данные.
            $D = $arr_SelectDateTime['0'];
            $T = $arr_SelectDateTime['1']; 
            $I = $arr_SelectDateTime['2'];
            foreach($data as $k => $v){                                                 //Находим и удаляем все данные выбранного времени.
                if($data[$k]['SelectedDateTime'] != ''){
                    unset($data[$k]['SelectedDateTime'], $data[$k]['SelectedTime']);
                }
            }
            unset($k, $v);
        }
        if($Save != ''){                                                                //Обработка сохранения пациента.
            $post = Tinterface::getPostForm();
            $data['REG_SELECTED_PATIENT'] = $post; 
        }
        if($registration != ''){
            $this->ModelItem->ValidationRedactedData();                                 //Удаляем данные для валидации.
            unset($data['REG_SELECTED_PATIENT']);
        }
        if($cancel_registration != ''){													//Обработка отмены регистрации.
			unset($data['REG_SELECTED_PATIENT']);
        }
        if(																				//Обработка отправки сообщения пользователю.
        $message_patient != ''  
        && $data['REG_SELECTED_PATIENT']
        ){
			$ModelAppointment->sendPersonalMessage($message_patient, $data['REG_SELECTED_PATIENT']);
			JRequest::setVar('message_patient', '');									//Сброс использованного запроса.
			unset($_POST['message_patient']);
			unset($data['REG_SELECTED_PATIENT']);
        }
        
        foreach($data as $key => $value){                                               //Цикл обработки действий.
            $prefix = str_replace(' ', '_', $key);                                      //Готови префикс для имени. 
            $Specialty = JRequest::getVar('Specialty'.$prefix);
            $Users = JRequest::getVar('Users'.$prefix);
            $SelectedDate = JRequest::getVar('SelectDate'.$prefix);
            $SelectedWeek = JRequest::getVar('ScheduleWeek'.$prefix);
            $ScheduleDate = JRequest::getVar('ScheduleDate'.$prefix);
            
            if($data['REG_SELECTED_PATIENT'] == ''){                                    //Если есть нет выбранного пациента, то удаляем выбраеную дату и время.
                unset($data[$key]['SelectedDateTime']);
            }
           
            if(																			//Обработка выбора времени приема.
            ($D != '' && $T != '' && $I == $data[$key]['id_Users'])						//Если есть выбранное время во вкладках докторов.
            || ($D != '' && $T != '' && $I != '' &&  $key == JText::_('REG_ALL_DOCTORS'))//Если есть выбранное время во вкладке "все доктора".
            ){
               if(																		//Обработка удаления лишних блокировок.
               $data['REG_SELECTED_PATIENT'] != ''                                 		//Если есть отобранные.
               && $ModelAppointment->ValidationLock($I, $D, $T)							//Валидация блокированной записи.
               ){
                    if($data[$key]['SelectedDateTime'] != ''){							//Удаляем прошлые блокировки.
						$arr_SelectDateTime_reset = explode('$$', $data[$key]['SelectedDateTime']);
						$ModelAppointment->delObsoleteData($arr_SelectDateTime_reset['2'], $arr_SelectDateTime_reset['0'], $arr_SelectDateTime_reset['1']);
						unset($arr_SelectDateTime_reset);								//Сбрасываем созданный массив с временем приема.
                    }   	                    
               }
               $data_Patient = $ModelAppointment->DataRecordedPatient($I, $D, $T);      //Получаем данные записанного пациента.
               if($data_Patient != ''){                                                 //Если есть данные, то отбираем его.
                   $data['REG_SELECTED_PATIENT'] = $data_Patient;
                    if($data_Patient['id_record'] != ''){								//Если есть ИД пользователя, готовим данные для валидации.
                        $this->ModelItem->getDataAreaRedactedData('Record', $data_Patient['id_record']);//Готовим данные.
                    }
               }
               else{																	//Иначе фиксируем дату и время.
			   		if(																	//Условие фиксации выбранной даты и времени.
			   		$data['REG_SELECTED_PATIENT'] != ''									//Проверка на наличие выбранного пациента
			   		//Проверка на корректность даты и времени.
			   		&& $ModelAppointment->ValidationReceptionRecord('', $SelectDateTime, $SelectDateTime, true, true)
			   		){
						$data[$key]['SelectedTime'] = $T;
		                $data[$key]['SelectedDateTime'] = $SelectDateTime;				//Фиксируем выбранную дату и время.
		                $ModelAppointment->CreateWriteLock($I, $D, $T);					//Созданем блокировку. 
			   		} 
               }
               $Records['id_Tabs1'] = $key;
            }
            
            
            if($Specialty != ''){                                                       //Обработка выбора специальности.
                $Records['id_Tabs1'] = $key;
                $data[$key]['id_Specialty'] = $Specialty;
                $data[$key]['Name_Specialty'] = $data[$key]['Specialty'][$Specialty];
                unset($data[$key]['id_Users'], $data[$key]['Name_Users']);
                break;
            }
            
            if($key == JText::_('REG_ALL_DOCTORS')){									//Обработка вкладки ВСЕ.
				if($id_specialtyAppointment != ''){										//Обработка выбора специальности.
					$data[$key]['id_Specialty'] = $id_specialtyAppointment;
	            }
	            if($weekup != ''){														//Обработка переключеня недель во вкладке ВСЕ.
					$data[$key]['d'] = $dd->getDateWeekUp($data[$key]['d']);
	            }
	            if($weekdown != ''){
					$data[$key]['d'] = $dd->getDateWeekDown($data[$key]['d']);
	            }
	            if($cancel_id_specialty != ''){											//Обработка выбора специальности во вкладке ВСЕ.
					unset($data[$key]['id_Specialty']);
	            }
	            if($SelectDateAppointment1 != ''){										//Обработка выбора даты во вкладке ВСЕ.
					$data[$key]['d'] = $SelectDateAppointment1;
	            }
            }
                
            if($Users != ''){                                                           //Обработка выбора пользователя.
                $Records['id_Tabs1'] = $key; 
                $nameUser = $data[$key]['Users'][$Users];
                if($nameUser != ''){                                                    //Условие для исправления бага.
                    $data[$nameUser] = $data[$key];
                    $data[$nameUser]['id_Users'] = $Users;
                    $data[$nameUser]['Name_Users'] = $nameUser;
                    $data[$nameUser]['SelectedDate'] = $dd->data_i;						//Установка текущей даты.
                    unset($data[$key], $data['blank']);
                    break;
                }            
            }
            if($SelectedDate != ''){                                                    //Обработка выбора даты.
                $Records['id_Tabs1'] = $key;
                $data[$key]['SelectedDate'] = $SelectedDate;
            }
            if($data[$key]['SelectedDate'] == '' && $key != 'REG_SELECTED_PATIENT'){
                $data[$key]['SelectedDate'] = $dd->data_i;
            }
            if($SelectedWeek != ''){                                                    //Обработка выбора расписание на неделю.
                $Records['id_Tabs1'] = $key;
                $data[$key]['SelectedWeek'] = $data[$key]['SelectedDate'];
            }
            if($ScheduleDate != ''){                                                    //Обработка выбора расписание на дату.
                $Records['id_Tabs1'] = $key;
                unset($data[$key]['SelectedWeek']);
            }
            
            $SelectedDateTime_vv =  $data['REG_SELECTED_PATIENT']['data_record'].'$$'.$data['REG_SELECTED_PATIENT']['time_record'].'$$'.$data['REG_SELECTED_PATIENT']['id_user'];
            if(                                                                         //Обработка удаления записи.
            $data['REG_SELECTED_PATIENT'] != '' &&
            $DeleteRecordedt != '' &&
            Rights::getRights('18', true) &&
            //Валидация выбраной даты и времени.
            $ModelAppointment->ValidationReceptionRecord('', $SelectedDateTime_vv, $data['REG_SELECTED_PATIENT'], true, true)
            ){
                $patient = $data['REG_SELECTED_PATIENT'];
                $this->ModelItem->delData($patient['id_record'], 'Record');
                JError::raiseWarning( 100, JText::_('REG_A_PATIENT').' '. $patient['surname'].' '.$patient['name'].' '.$patient['patronymic'].' '.JText::_('REG_DELETEDN').'!');
                //Логирование действия. 
                $this->ModelItem->LogEvents($this->LogInfo, 'RECORDS MANAGEMENT:', 'Recording patient removed', $patient);
                //Удаление блокировки.
                $arr_SelectDateTime_reset = explode('$$', $data[$key]['SelectedDateTime']);
				$ModelAppointment->delObsoleteData($arr_SelectDateTime_reset['2'], $arr_SelectDateTime_reset['0'], $arr_SelectDateTime_reset['1']);
				unset($arr_SelectDateTime_reset);
                unset($data['REG_SELECTED_PATIENT']); 
                unset($data[$key]['SelectedDateTime']);
                $ModelAppointment->PatientMessage($patient, JText::_('REG_YOUR_POST_DELETED'));//Оповещаем пользователя.   
            }
            
            if(                                                                         //Обработка блокирования записи на прием.
            ($data['REG_SELECTED_PATIENT']['id_status'] == '1' ||  $data['REG_SELECTED_PATIENT']['id_status'] == '2') && 
            $OffRecordedt != '' &&
            Rights::getRights('18', true) &&
            //Валидация выбраной даты и времени.
            $ModelAppointment->ValidationReceptionRecord('', $SelectedDateTime_vv, $data['REG_SELECTED_PATIENT'], true, true)
            ){
                $pp = $data['REG_SELECTED_PATIENT']; 
                $patient['id_status'] = '4';
                $this->ModelItem->setData($patient, $pp['id_record'], 'Record');
                //Логирование действия. 
                $this->ModelItem->LogEvents($this->LogInfo, 'RECORDS MANAGEMENT:', 'Recording patient blocked', $patient);
                 //Удаление блокировки.
                $arr_SelectDateTime_reset = explode('$$', $data[$key]['SelectedDateTime']);
				$ModelAppointment->delObsoleteData($arr_SelectDateTime_reset['2'], $arr_SelectDateTime_reset['0'], $arr_SelectDateTime_reset['1']);
				unset($arr_SelectDateTime_reset);
                unset($data['REG_SELECTED_PATIENT']); 
                unset($data[$key]['SelectedDateTime']);
                $this->AppItem->enqueueMessage(JText::_('REG_RECORDING_PATIENT').' '.$patient['surname'].' '.$patient['name'].' '.$patient['patronymic'].JText::_('REG_LOCKEDN').'!'); 
                $arr_patient = $ModelAppointment->DataRecordedPatient($pp['id_user'], $pp['data_record'], $pp['time_record']);
                $ModelAppointment->PatientMessage($arr_patient, JText::_('REG_YOUR_ENTRY_IS_LOCKED'));    //Оповещаем пользователя. 
            }
            
            if(                                                                         //Обработка активации записи на прием.
            $data['REG_SELECTED_PATIENT']['id_status'] == '4' &&
            $ActivateRecordedt != '' &&
            Rights::getRights('18', true) &&
            //Валидация выбраной даты и времени.
            $ModelAppointment->ValidationReceptionRecord('', $SelectedDateTime_vv, $data['REG_SELECTED_PATIENT'], true, true)
            ){
                $pp = $data['REG_SELECTED_PATIENT']; 
                $patient['id_status'] = '1';
                $this->ModelItem->setData($patient, $pp['id_record'], 'Record');
                //Логирование действия. 
                $this->ModelItem->LogEvents($this->LogInfo, 'RECORDS MANAGEMENT:', 'Recording patient activated', $pp);
                 //Удаление блокировки.
                $arr_SelectDateTime_reset = explode('$$', $data[$key]['SelectedDateTime']);
				$ModelAppointment->delObsoleteData($arr_SelectDateTime_reset['2'], $arr_SelectDateTime_reset['0'], $arr_SelectDateTime_reset['1']);
				unset($arr_SelectDateTime_reset);
                unset($data['REG_SELECTED_PATIENT']); 
                unset($data[$key]['SelectedDateTime']);
                $this->AppItem->enqueueMessage(JText::_('REG_RECORDING_PATIENT').' '.$patient['surname'].' '.$patient['name'].' '.$patient['patronymic'].JText::_('REG_ACTIVATEN').'!'); 
                $arr_patient = $ModelAppointment->DataRecordedPatient($pp['id_user'], $pp['data_record'], $pp['time_record']);
                $ModelAppointment->PatientMessage($arr_patient, JText::_('REG_YOUR_RECORDING_IS_ACTIVATED'), true);    //Оповещаем пользователя. 
            }
            
            
            if(                                                                         //Обработка записи на прием.
            $data[$key]['SelectedDateTime'] != '' &&
            $MakeAppointment != '' &&
            Rights::getRights('16', true) &&											//Валидация разрешения на выполнение действия.
            //Валидация выбраной даты и времени.
            $ModelAppointment->ValidationReceptionRecord('', $data[$key]['SelectedDateTime'], $data['REG_SELECTED_PATIENT'], true, true)
            ){
                $patient = $data['REG_SELECTED_PATIENT']; 
                if(
                //Валидация записи на прием.
                $ModelAppointment->ValidationReceptionRecord($data[$key]['id_Users'], $data[$key]['SelectedDateTime'], $patient, true, true)
                ){
                     $arr_SelectDateTime = explode('$$', $data[$key]['SelectedDateTime']);
                     $patient['data_record'] = $arr_SelectDateTime['0'];
                     $patient['time_record'] = $arr_SelectDateTime['1'];
                     $patient['id_user'] = $arr_SelectDateTime['2'];
                     $patient['id_status'] = '1';
                     $this->ModelItem->setData($patient, '', 'Record');
                     //Логирование действия. 
                     $this->ModelItem->LogEvents($this->LogInfo, 'RECORDS MANAGEMENT:', 'Recorded on the patient reception', $patient);
                     //Удаление блокировки.
	                 $arr_SelectDateTime_reset = explode('$$', $data[$key]['SelectedDateTime']);
					 $ModelAppointment->delObsoleteData($arr_SelectDateTime_reset['2'], $arr_SelectDateTime_reset['0'], $arr_SelectDateTime_reset['1']);
					 unset($arr_SelectDateTime_reset);
                     unset($data['REG_SELECTED_PATIENT']);
                     unset($data[$key]['SelectedDateTime']);
                     $this->AppItem->enqueueMessage(JText::_('REG_THE_PATIENT_RECORDED').'!');
                     $arr_patient = $ModelAppointment->DataRecordedPatient($patient['id_user'], $patient['data_record'], $patient['time_record']);
                     $ModelAppointment->PatientMessage($arr_patient, JText::_('REG_YOU_MAKE_AN_APPOINTMENT'), true);    //Оповещаем пользователя. 
                 }
            }
            
            if(                                                                         //Обработка переноса записи.
            $data[$key]['SelectedDateTime'] != '' &&
            $TransferRecordedt != '' &&
            Rights::getRights('17', true) &&
            //Валидация выбраной даты и времени.
            $ModelAppointment->ValidationReceptionRecord('', $data[$key]['SelectedDateTime'], $data['REG_SELECTED_PATIENT'], true, true)
            ){
                if($ModelAppointment->ValidationReceptionRecord($data[$key]['id_Users'], $data[$key]['SelectedDateTime'], $data['REG_SELECTED_PATIENT'], true, true)){
                    //Подготовка данных для сохранения.
                    $pp = $data['REG_SELECTED_PATIENT'];                    
                    $arr_SelectDateTime = explode('$$', $data[$key]['SelectedDateTime']);
                    $patient['data_record'] = $arr_SelectDateTime['0'];
                    $patient['time_record'] = $arr_SelectDateTime['1'];
                    $patient['id_user'] = $arr_SelectDateTime['2'];
                    $patient['id_status'] = '1';
                    $this->ModelItem->setData($patient, $pp['id_record'], 'Record');
                    //Логирование действия. 
                    $this->ModelItem->LogEvents($this->LogInfo, 'RECORDS MANAGEMENT:', 'Recording patient moved', $pp);
                    //Удаление блокировки.
	                $arr_SelectDateTime_reset = explode('$$', $data[$key]['SelectedDateTime']);
					$ModelAppointment->delObsoleteData($arr_SelectDateTime_reset['2'], $arr_SelectDateTime_reset['0'], $arr_SelectDateTime_reset['1']);
					unset($arr_SelectDateTime_reset);
                    unset($data['REG_SELECTED_PATIENT']); 
                    unset($data[$key]['SelectedDateTime']);
                    $this->AppItem->enqueueMessage(JText::_('REG_THE_PATIENT_IS_MOVED'));
                    //Оповещаем пользователя.
                    $arr_Patient = $ModelAppointment->DataRecordedPatient($patient['id_user'], $patient['data_record'], $patient['time_record']);
                    if($arr_Patient != ''){
                        $ModelAppointment->PatientMessage($arr_Patient, JText::_('REG_RECORDING_MOVED'), true);    //Оповещаем пользователя о перемещении.  
                    }
                }
            } 
        }
        unset($key, $value);
        $Records['data'] = $data;
        $this->SessionItem->set('Medical_Registry_Records', $Records);                  //Фиксация переменной. 
        $this->Ajax($data, 'Medical_Registry_Records', 'REG_ALL_DOCTORS'); 
    }
    
    
    
}