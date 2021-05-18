<?php
/**
 * Default Controller
 * @author Олег Борисович Дубик
 */
/**
  * Контроллер сервиса "Электронная регистратура" для задачи "Registry_management" ,"Schedules".
  */
  

// Запрет прямого доступа.
defined( '_JEXEC' ) or die;  
  

class Medical_RegistryControllerSchedules extends Medical_RegistryController{

   
    
    //Задача управления регистратурой.
    function Registry_ManagementTask($cachable = false, $urlparams = array()){
        $this->setParameters('Registry_Management');   
        if(JRequest::getVar('logout') != ''){                                       			//Обработка комманды logout.
            Aut::logout();
        }
              
        if(Aut::login()){                                                       				//Проверка аутентифиации пользователя.
        	//Инициализация переменных.
            $TaskManagement = JRequest::getCmd('TaskManagement');
            $session = JFactory::getSession(); 
            $session_data = $session->get('Medical_Registry_data');

            //Выполнение задач по управления всеми расписаниями.  
            if($TaskManagement == 'Schedules'){                                                
            	$this->ActionSchedules();
                $Schedules = $this->SessionItem->get('Medical_Registry_Schedules');             //Инициализация переменных.
                $this->ViewlItem->DescriptionItemManagement = JText::_('REG_EDITING_SCHEDULES');
                    
                $id_component = 'ss';
                $id_login = $Schedules['id_login'];
                $date_editing = $Schedules['date_editing'];  
                $Schedules['id_component'] = $id_component;
                $id_select_schedule = $Schedules['id_select_schedule']; 
                $id_specialty = $Schedules['id_specialty'];
                $Add = JRequest::getVar('Add');  
                //Получаем ссылку на модель.  
                $ModelItemSchedule = &$this->getModel('schedule');                              //Получаем экземпляр модели с просмотра расписаний.
                    
                    
                $formSecectUser = $this->ViewlItem->getFormContent($this->ModelItem->getFormSpecialty($id_component, $id_specialty));
                $formSecectUser .= '<br/>'.$this->ViewlItem->getFormContent($this->ModelItem->getFormSelectUser($id_component, $id_specialty, $id_login, '1')); 
                //Формирование панели интструментов.
                $this->ViewlItem->ToolsItemManagement = $this->ViewlItem->getFormContent($this->ModelItem->getMenuTools().$this->ModelItem->getScheduleTools($date_editing));
                    
                if(                                                                             //Просмотр расписаний на неделю. 
                    $Schedules['WeekSchedule_schedule_editing'] != '' 
                    //&& $Add == ''
                    //&& $id_select_schedule == '' 
                ){
                	$this->ViewlItem->ViewItemManagement = $ModelItemSchedule->getTableWeekSchedule($date_editing, $Schedules['id_specialty'], $Schedules['id_login'], true);
                    $this->ViewlItem->ViewItemManagement = $this->ViewlItem->getDivPrint($this->ViewlItem->ViewItemManagement, 'PrintSchedules');
                    $this->ViewlItem->ViewItemManagement = $this->ModelItem->getDivTools($this->ModelItem->getButtonPrint('PrintSchedules')).$this->ViewlItem->ViewItemManagement;
                }
                if(                                                                             //Просмотр расписаний на дату. 
                    $Schedules['DateSchedule_schedule_editing'] != '' 
                    //&& $Add == ''
                    //&& $id_select_schedule == ''
                ){
                	$this->ViewlItem->ViewItemManagement = $ModelItemSchedule->getTableSchedule(JText::_('REG_SCHEDULE_FOR_THE').' '.'<b>'.$date_editing.'</b>', $date_editing, $Schedules['id_specialty'], $Schedules['id_login'], true);   
                    $this->ViewlItem->ViewItemManagement = $this->ViewlItem->getDivPrint($this->ViewlItem->ViewItemManagement, PrintSchedules);
                    $this->ViewlItem->ViewItemManagement = $this->ModelItem->getDivTools($this->ModelItem->getButtonPrint('PrintSchedules')).$this->ViewlItem->ViewItemManagement;
                }
                     
                $lineSchedulesMenu['0'] = $formSecectUser;
                if($id_login != ''){
                	$formSelectDate = $this->ViewlItem->getFormContent($this->ModelItem->getFormDate($id_component, $date_editing)); 
                    $lineSchedulesMenu['1'] = $formSelectDate;
                    if($date_editing != ''){
                    	//Выводим меню управления
                        $lineSchedulesMenu['2'] = $this->ViewlItem->getFormContent($this->ModelItem->getFormControlSchedule($id_select_schedule, $date_editing, true));
                        $arr = $this->ModelItem->getScheduleDataId($date_editing, $id_login);                            //Получаем данные расписаний на дату и ИД.
                        if(count($arr) != 0){
                        	$lineSchedulesMenu['3'] = $this->ViewlItem->getFormSelectSchedule($arr, $date_editing, $id_login);  //Получаем меню расписаний
                        }
                        if($id_select_schedule != ''){
                        	$form = $this->ModelItem->getForm($id_select_schedule, 'Schedule', 'SchedulelData');      //Получаем данные формы.  
                            //$this->ViewlItem->ContentItem = $this->ViewlItem->getForm($form);                         //Выводим форму.
                            $printForm = Tinterface::getTimeModalWindow('idform', $this->ViewlItem->getForm($form, '', $this->ModelItem->getFormControlSchedule($id_select_schedule, $date_editing)), 1000, true);
                        }
                        
                         if(                                                                                           //Если выбрана дата, то паказываем меню с расписаниями.
                            ($date_editing != '' && $id_select_schedule != '' && Rights::getRights('9')) || 
                            ($date_editing != '' && $Add != '' && Rights::getRights('7'))
                         ){  
                         	//Получаем данные формы.
                            $form = $this->ModelItem->getForm($id_select_schedule, 'Schedule', 'SchedulelData');      //Получаем данные формы.  
                            //$this->ViewlItem->ContentItem = $this->ViewlItem->getForm($form);                         //Выводим форму.    
                            $printForm = Tinterface::getTimeModalWindow('idform', $this->ViewlItem->getForm($form, '', $this->ModelItem->getFormControlSchedule($id_select_schedule, $date_editing)), 1000, true);
                         }
                         if($printForm != ''){																			//Если есть форма в модальном окне, то показываем ее.
							 $this->ViewlItem->ContentItem .= $printForm;
                         }
                    }
                }
                //$this->ViewlItem->ViewItemDebugging = $Schedules;
                $this->ViewlItem->MenuItem = $this->ViewlItem->getElementsLine($lineSchedulesMenu);
                $this->ViewlItem->MenuItem = '<div class="inputbox">'.$this->ViewlItem->MenuItem.'</div>';
                $this->SessionItem->set('Medical_Registry_Schedules', $Schedules);        //Фиксация переменной.  
            }
            $this->ViewlItem->display($cachable, $urlparams);
        } 
        return $this; 
    }
    
    
    
    
    
    //Метод боработки действий пользователя Schedules
    private function ActionSchedules(){
        $Schedules = $this->SessionItem->get('Medical_Registry_Schedules');             			//Инициализация переменных.  
        $id_component = $Schedules['id_component'];                                     			//Идентификатор компонента.
        $id_specialty = JRequest::getVar('id_specialty'.$id_component);                       		//Идентификатор специальности.
        $id_login = JRequest::getVar('id_login'.$id_component);
        $SelectDate = JRequest::getVar('SelectDate'.$id_component);                                	//Идентификатор пользователя.
        $select_schedule = JRequest::getVar('select_schedule'.$Schedules['id_login']);
        $DateSchedule = JRequest::getVar('DateSchedule');
        $WeekSchedule = JRequest::getVar('WeekSchedule');
        $AddForTemplate = JRequest::getVar('AddForTemplate');
        
        $AddForTemplateMonth = JRequest::getVar('AddForTemplateMonth');
        $AddForTemplateDecade = JRequest::getVar('AddForTemplateDecade');
        $AddForTemplateFifty = JRequest::getVar('AddForTemplateFifty');
        
        $Save = JRequest::getVar('Save');
        $Cancel = JRequest::getVar('Cancel');
        $Select = JRequest::getVar('Select'); 
        $Add = JRequest::getVar('Add');
        $Remove = JRequest::getVar('Remove');
        
        $dd = new data();
        
        if($id_specialty != ''){                                                                //Обработка выбора специальности.
            $Schedules['id_specialty'] = $id_specialty;
            unset($Schedules['id_select_schedule'], $Schedules['date_editing'], $Schedules['id_login']);
        }
        if($id_login != '' && $Schedules['id_specialty'] != ''){                                //Обработка выбора доктора.
            JRequest::checkToken() or jexit('Invalid Token');                                   //Проверка токена.
            $Schedules['id_login'] = $id_login;
            unset($Schedules['id_select_schedule'], $Schedules['date_editing']);
        }
        if(    																					//Обработка выбора даты.
        	$SelectDate != '' && 
        	$Schedules['id_specialty'] != '' && 
        	$Schedules['id_login'] &&
        	$this->ModelItem->ValidationLogicSchedule('', $SelectDate, $Schedules['id_login'])
        ){
            $Schedules['date_editing'] = $SelectDate;
            unset($Schedules['id_select_schedule']);
            unset($Schedules['DateSchedule_schedule_editing'], $Schedules['WeekSchedule_schedule_editing']);
        }
        if($select_schedule != ''){                                                             //Обработка выбора расписания.
            $Schedules['id_select_schedule'] = $this->ModelItem->getIdSchedule($select_schedule, $Schedules['date_editing'], $Schedules['id_login']);
        }
        if($Schedules['id_specialty'] != '' && $Schedules['id_login'] != '' && $Schedules['date_editing'] != ''){
             if($AddForTemplate != ''){                                                     //Обработка выбора записи расписания из шаблона.
                 if(Rights::getRights('8', true)){
                     //Логирование действия. 
                     $this->ModelItem->LogEvents($this->LogInfo, 'CONTROL SCHEDULES:', 'Schedule filling of the template', $Schedules);
                     $this->ModelItem->ScheduleTemplateRecord($Schedules['date_editing'], $Schedules['id_login']);
                     $this->AppItem->enqueueMessage(JText::_('REG_SCHEDULE_IS_FULL_OF_THE_TEMPLATE'));
                     //unset($Schedules['date_editing']);
                 }
             }
             if($AddForTemplateMonth != ''){                                                 //Обработка выбора записи расписания из шаблона не 5 недель.
                 if(Rights::getRights('8', true)){
                 	 for($i = 1; $i <= 5; $i++){											//Выполняем процедуру 5 раз.
					    //Логирование действия. 
	                    $this->ModelItem->LogEvents($this->LogInfo, 'CONTROL SCHEDULES:', 'Schedule filling of the template', $Schedules);
	                    $this->ModelItem->ScheduleTemplateRecord($Schedules['date_editing'], $Schedules['id_login']);
					    $Schedules['date_editing'] = $dd->getDateWeekUp($Schedules['date_editing']);//Получаем дату на неделю вперед.
					}
					$this->AppItem->enqueueMessage(JText::_('REG_SCHEDULE_IS_FULL_FIVE_WEEKS'));
                 }
             }
             if($AddForTemplateDecade != ''){                                                //Обработка выбора записи расписания из шаблона не 10 недель.
                 if(Rights::getRights('8', true)){
                 	 for($i = 1; $i <= 10; $i++){											//Выполняем процедуру 10 раз.
					    //Логирование действия. 
	                    $this->ModelItem->LogEvents($this->LogInfo, 'CONTROL SCHEDULES:', 'Schedule filling of the template', $Schedules);
	                    $this->ModelItem->ScheduleTemplateRecord($Schedules['date_editing'], $Schedules['id_login']);	                    
					    $Schedules['date_editing'] = $dd->getDateWeekUp($Schedules['date_editing']);//Получаем дату на неделю вперед.
					}
					$this->AppItem->enqueueMessage(JText::_('REG_SCHEDULE_IS_FULL_TEN_WEEKS'));
                 }
             }
             
             if($AddForTemplateFifty != ''){                                                //Обработка выбора записи расписания из шаблона не 50 недель.
                 if(Rights::getRights('8', true)){
                 	 for($i = 1; $i <= 50; $i++){											//Выполняем процедуру 50 раз.
					    //Логирование действия. 
	                    $this->ModelItem->LogEvents($this->LogInfo, 'CONTROL SCHEDULES:', 'Schedule filling of the template', $Schedules);
	                    $this->ModelItem->ScheduleTemplateRecord($Schedules['date_editing'], $Schedules['id_login']);
					    $Schedules['date_editing'] = $dd->getDateWeekUp($Schedules['date_editing']);//Получаем дату на неделю вперед.
					}
					$this->AppItem->enqueueMessage(JText::_('REG_THE_SCHEDULE_IS_FIFTY_WEEKS'));
                 }
             }
             
             
             if($DateSchedule != ''){                                                       //Обработка выбора расписания надату.
                 $Schedules['DateSchedule_schedule_editing'] = $Schedules['date_editing'];
                 unset($Schedules['WeekSchedule_schedule_editing']);
             }
             if($WeekSchedule != ''){
                 $Schedules['WeekSchedule_schedule_editing'] = true;
                 unset($Schedules['DateSchedule_schedule_editing']);
             }
             if($Add != ''){                                                                //Обработка добавления расписаний.
                if(Rights::getRights('7', true)){
                    unset($Schedules['id_schedule_editing']); 
                } 
             }
             if($Remove != '' && $Schedules['id_select_schedule'] != ''){                   //Обработка удаления расписаний. 
                if(Rights::getRights('10', true)){
                    $this->ModelItem->delData($Schedules['id_select_schedule'], 'Schedule');
                    $this->AppItem->enqueueMessage(JText::_('REG_SCHEDULE_REMOVED'));
                    //Логирование действия. 
                    $this->ModelItem->LogEvents($this->LogInfo, 'CONTROL SCHEDULES:', 'Removing schedules', $Schedules); 
                    unset($Schedules['id_select_schedule']); 
                }     
             }
             $post = Tinterface::getPostForm();
             
             
             if($post != '' && $Schedules['id_select_schedule'] != ''){                     //Если выбрали расписание и редактируем его.
                 $post['id_login_schedule'] = $Schedules['id_login'];                       //то сохраняем иденитификатор пользователя.
             }
             
             
             if(isset($post['hidden_flag_schedule'])){										//Если выбранно скрытое поле, то 
			 	$post['hidden_flag_schedule'] = true;										//Формируем флаг выбраного поля
             }
             else{
			 	$post['hidden_flag_schedule'] = false;										//Иначе сбрасываем флаг вибранного поля.
             }
             
             
             if($Cancel != ''){                                                             //Обработка сброса сохранеия в форме.
                unset($Schedules['id_select_schedule']);   
             } 
             
             if(
             $Save != ''                                                                    //Обработка сохранения. 
             && $this->ModelItem->ValidationLogicSchedule($post, $Schedules['date_editing'], $Schedules['id_login'])
             ){
                JRequest::checkToken() or jexit('Invalid Token');                           //Проверка токена. 
                $post['date_schedule'] = $Schedules['date_editing'];
                $post['id_login_schedule'] = $Schedules['id_login'];
                
                if($Schedules['id_select_schedule'] != ''){                                 //Разделения на сохренение и добавление.
                    if(Rights::getRights('9', true)){                                       //Проверка на предмет изменения.
                        //Логирование действия. 
                        $this->ModelItem->LogEvents($this->LogInfo, 'CONTROL SCHEDULES:', 'Changing the schedule', $Schedules);
                        $this->AppItem->enqueueMessage(JText::_('REG_SCHEDULE_CHANGED'));
                        $this->ModelItem->setData($post, $Schedules['id_select_schedule'], 'Schedule');
                        unset($Schedules['id_select_schedule']);
                    }    
                }
                else{
                    if(Rights::getRights('7', true)){                                       //Проверка на предмет добавления.
                        $this->ModelItem->setData($post, '', 'Schedule');
                        //Логирование действия. 
                        $this->ModelItem->LogEvents($this->LogInfo, 'CONTROL SCHEDULES:', 'Adding schedules', $Schedules);
                        $this->AppItem->enqueueMessage(JText::_('REG_SCHEDULE_ADDED'));
                    }    
                }
             }    
        }
        
        $this->SessionItem->set('Medical_Registry_Schedules', $Schedules);        //Фиксация переменной.
        $this->Ajax(); 
    }
       
}