<?php
/**
 * Default Controller
 * @author Олег Борисович Дубик
 */
/**
  * Контроллер сервиса "Электронная регистратура" для задачи "Schedule".
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



require_once (JPATH_COMPONENT.DS.'libraries'.DS.'calendar.url.php');  



class Medical_RegistryControllerSchedule extends Medical_RegistryController{
    
    
    //Задача просмотра расписания.
    public function scheduleTask($cachable = false, $urlparams = array()){
        //print calendarURL::getBaseUrl('/Joomla/index.php/registratura/medical_registry/scheduleTask', 'year', '2018');
        
        //print_r(calendarURL::getArrDay('2018', '2', '2'));
        //print calendarURL::getUrlforMenu('/Joomla/index.php/registratura/medical_registry/scheduleTask', 'y', '2018', 'Текущий год!');
        //print '<pre>';
        //print calendarURL::getCalendarURL('/Joomla/index.php/registratura/medical_registry/scheduleTask');
        //print_r(calendarURL::getCalendarURL('/Joomla/index.php/registratura/medical_registry/scheduleTask'));
        //print_r (Tinterface::getUrlBases('Schedule'));
        //print_r (calendarURL::getCalendarUrlTabs('/Joomla/index.php/registratura/medical_registry/scheduleTask'));
        //print '</pre>';
        //$s1['123'] = 'sssss';
        //$s1['345'] = 'gggggg';
        //print calendarURL::getTabsVerical($s1);
        //print calendarURL::getCalendarUrlTabs('/Joomla/index.php/registratura/medical_registry/scheduleTask');
        
        
        
        
        $this->setParameters('Schedule');
        $ViewRegistry_management = &$this->getView('Registry_Management', 'html');          //Получаем ссылку на объект Registry_Management
        $ModelRegistry_management = &$this->getModel('Registry_Management');
        $Schedule = $this->SessionItem->get('Medical_Registry_Schedule');                   //Получаем данные с сессии. 
           
        //Обработка комманд.                                                  
        $specialty = JRequest::getCmd('specialty');                                         //Инициализация переменных.
        $doctor = JRequest::getCmd('doctor');
        $WeekSchedule = JRequest::getCmd('WeekSchedule'); 
        $ToDateSchedule = JRequest::getVar('ToDateSchedule');
        $ScheduleWeek = JRequest::getVar('ScheduleWeek');
        
        $SelectedDate = JRequest::getVar('SelectDate'.'Schedule_date');
        
        if($SelectedDate == ''){
			$SelectedDate = JRequest::getVar('date');
        }
        $TodaySchedule = JRequest::getVar('TodaySchedule');
        
        //Подготовка данных к валидации.
        $validation[] = $specialty;
        $validation[] = $doctor;
        $validation[] = $WeekSchedule;
        $validation[] = $ToDateSchedule;
        $validation[] = $ScheduleWeek;
        $validation[] = $SelectedDate;
        $validation[] = $TodaySchedule; 
        
        
        $dd = new data();      
        
        if($ViewRegistry_management->ValidationArray($validation, true)){                       //Валидируем вводимые данные.
            //Обработка действий пользователя.
            if($SelectedDate != ''){                                                            //Обработка выбора даты.
                $Schedule['SelectedDate'] = $SelectedDate;
                unset($Schedule['ToDateSchedule'], $Schedule['ScheduleWeek']);
            }
            
            if($Schedule['SelectedDate'] == ''){                                                //Если дата не выбранна, то ставим сегодняшнюю.
                $Schedule['SelectedDate'] = $dd->data_i;
                $Schedule['ScheduleWeek'] = $dd->data_i;										//Активируем по умолчанию расписание на неделю.
            }
            if($ScheduleWeek != ''){                                                            //Обработка выбора расписания на неделю.
                $Schedule['ScheduleWeek'] = $Schedule['SelectedDate'];
                //Логирование действия. 
                $ModelRegistry_management->LogEvents($this->LogInfo, 'VIEW SCHEDULE:', 'View your schedule for the week', $Schedule);
                unset($Schedule['ToDateSchedule']);
            }
            
            if($ToDateSchedule != ''){                                                          //Обработка выбора расписаний на дату.
                //$Schedule['SelectedDate'] = $dd->data_i;
                $Schedule['ToDateSchedule'] = $Schedule['SelectedDate'];
                //Логирование действия. 
                $ModelRegistry_management->LogEvents($this->LogInfo, 'VIEW SCHEDULE:', 'View schedule at date', $Schedule);
                unset($Schedule['ScheduleWeek']); 
            }
            
            if($TodaySchedule != ''){                                                           //Обработка выбора расписаний сегодня.                 
                $Schedule['SelectedDate'] = $this->ModelItem->getCurrentDate();
                //Логирование действия. 
                $ModelRegistry_management->LogEvents($this->LogInfo, 'VIEW SCHEDULE:', 'View today is schedule', $Schedule);
                unset($Schedule['ScheduleWeek'], $Schedule['ToDateSchedule']);
                $app = JFactory::getApplication();
				$app->redirect(JRoute::_(Tinterface::getUrlBases().'?date='.$dd->data_i));		//Перенаправляем на текущую дату.
            }
            if($specialty != ''){                                                               //Обработка выбора специальности.
                $Schedule['id_specialty'] = $specialty;
                if($doctor == ''){
                    unset($Schedule['id_doctor']);
                }
            }
            
            if($doctor != ''){                                                                  //Обработка выбора доктора.
                $Schedule['id_doctor'] = $doctor;
            }
            
            $urlBase = Tinterface::getUrlBases();												//Формируем начальный базовы УРЛ.
            $this->SessionItem->set('Medical_Registry_Schedule', $Schedule);                    //Фиксация переменной.
            //Формируем меню выбора времени.
            //$MenuLine['0'] = (calendarURL::getCalendarURL(Tinterface::getUrlBases(), $y, $m, $d));
            //$MenuLine['0'] = calendarURL::getCalendarUrlTabs(Tinterface::getUrlBases());
            $MenuLine['0'] = calendarURL::getPlaginCalendarUrlTabs($urlBase, $Schedule['SelectedDate']);
            if($Schedule['SelectedDate'] != ''){												//Если выбрана дата, то меняем базовый УРЛ
				$urlBase = JRoute::_($urlBase.'?date='.$Schedule['SelectedDate']);
            }
            //$MenuLine['1'] = $this->ViewlItem->getForm($ModelRegistry_management->getFormDate('Schedule_date', $Schedule['SelectedDate']));
            $arr_specialty  = $this->ModelItem->getListSpecialtyInDate($Schedule['SelectedDate']);           //Формируем меню специальностей.
            $MenuLine['2'] = $this->ViewlItem->getMenuSpecialty($arr_specialty, $urlBase);
            if($Schedule['id_specialty'] != '' && $Schedule['SelectedDate'] != ''){				//Если выбрана специальность, то меняем базовый УРЛ.
				$urlBase = JRoute::_($urlBase.'&specialty='.$Schedule['id_specialty']);
            }
            if($specialty != ''){
                $arr_doctor = $this->ModelItem->getListDoctorsInDate($Schedule['id_specialty'], $Schedule['SelectedDate']); //Формируем меню докторов.
                if(count($arr_doctor) >= 1){
                    $MenuLine['3'] = $this->ViewlItem->getMenuDoctor($arr_doctor, $urlBase);
                }
            }
            
            $this->ViewlItem->ToolsItemSchedule = $this->ViewlItem->getForm($this->ModelItem->getMenuTools($specialty));
            //Показываем приготовленное меню.
            $this->ViewlItem->MenuItemSchedule = $ViewRegistry_management->getElementsLine($MenuLine);
            
            //Показать расписание на сегодня если дата совпадаем с текущей.
            if($Schedule['SelectedDate'] != '' && $Schedule['ScheduleWeek'] == '' && $Schedule['ToDateSchedule'] == '' && ($Schedule['SelectedDate'] == $dd->data_i)){
                $this->ViewlItem->ViewItemSchedule = $this->ModelItem->getTableSchedule(JText::_('REG_SCHEDULE_FOR_TODAY').' - '.$Schedule['SelectedDate'], $Schedule['SelectedDate'], $specialty, $doctor); 
            }
            //Показать расписание на даду.
            if($Schedule['SelectedDate'] != '' && $Schedule['ScheduleWeek'] == '' && $Schedule['ToDateSchedule'] == '' && ($Schedule['SelectedDate'] != $dd->data_i)){
                $this->ViewlItem->ViewItemSchedule = $this->ModelItem->getTableSchedule(JText::_('REG_SCHEDULE_FOR_THE').' - '.$Schedule['SelectedDate'], $Schedule['SelectedDate'], $specialty, $doctor); 
            }
            //Показать расписание на неделю. при выборе этого.
            if($Schedule['SelectedDate'] != '' && $Schedule['ScheduleWeek'] != '' && $Schedule['ToDateSchedule'] == ''){
                $this->ViewlItem->ViewItemSchedule = $this->ModelItem->getTableWeekSchedule($Schedule['ScheduleWeek'], $specialty, $doctor);
            }
            //Показать расписание на дату при выбора этого.
            if($Schedule['SelectedDate'] != '' && $Schedule['ScheduleWeek'] == '' && $Schedule['ToDateSchedule']!= ''){
                $this->ViewlItem->ViewItemSchedule = $this->ModelItem->getTableSchedule(JText::_('REG_SCHEDULE_FOR_THE').' - '.$Schedule['ToDateSchedule'], $Schedule['SelectedDate'], $specialty, $doctor); 
            }
            //Готовим данные к печати.
            if($this->ViewlItem->PresenceLineCode($this->ViewlItem->ViewItemSchedule, 'table')){
                 $this->ViewlItem->ViewItemSchedule = $ViewRegistry_management->getDivPrint($this->ViewlItem->ViewItemSchedule, 'PirntSchedule');
                //Показываем кнопку для печати.
                $this->ViewlItem->ViewItemSchedule = '<div class="inputbox">'.$ModelRegistry_management->getButtonPrint('PirntSchedule').'</div>'.$this->ViewlItem->ViewItemSchedule;
            }
            else{
                unset($this->ViewlItem->ViewItemSchedule);
            }
            
            $this->ViewlItem->Debug = $Schedule;
            $this->ViewlItem->display($cachable, $urlparams); 
            return $this;
        }        
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
        if($SelectDate != '' && $Schedules['id_specialty'] != '' && $Schedules['id_login']){    //Обработка выбора даты.
            $Schedules['date_editing'] = $SelectDate;
            unset($Schedules['id_select_schedule']);
            unset($Schedules['DateSchedule_schedule_editing'], $Schedules['WeekSchedule_schedule_editing']);
        }
        if($select_schedule != ''){                                                             //Обработка выбора расписания.
            $Schedules['id_select_schedule'] = $this->ModelItem->getIdSchedule($select_schedule, $Schedules['date_editing'], $Schedules['id_login']);
        }
        if($Schedules['id_specialty'] != '' && $Schedules['id_login'] != '' && $Schedules['date_editing'] != ''){
             if($AddForTemplate != ''){                                                     //Обработка выбора записи расписания из шаблона.
                 if(Rights::getRights('8' , true)){
                     //Логирование действия. 
                     $this->ModelItem->LogEvents($this->LogInfo, 'CONTROL SCHEDULES:', 'Schedule filling of the template', $Schedules);
                     $this->ModelItem->ScheduleTemplateRecord($Schedules['date_editing'], $Schedules['id_login']);
                     $this->AppItem->enqueueMessage(JText::_('REG_SCHEDULE_IS_FULL_OF_THE_TEMPLATE'));
                     //unset($Schedules['date_editing']);
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
                if(Rights::getRights('7' , true)){
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
             $post = $this->getPostForm();
             
             
             if($post != '' && $Schedules['id_select_schedule'] != ''){                     //Если выбрали расписание и редактируем его.
                 $post['id_login_schedule'] = $Schedules['id_login'];                       //то сохраняем иденитификатор пользователя.
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