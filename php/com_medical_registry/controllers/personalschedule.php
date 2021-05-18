<?php
/**
 * Default Controller
 * @author Олег Борисович Дубик
 */
/**
  * Контроллер сервиса "Электронная регистратура" для задачи "Registry_management" , "Personalschedule".
  */
  

// Запрет прямого доступа.
defined( '_JEXEC' ) or die;  
  

class Medical_RegistryControllerPersonalschedule extends Medical_RegistryController{

   
    
    //Задача управления регистратурой.
    function Registry_ManagementTask($cachable = false, $urlparams = array()){
        $this->setParameters('Registry_Management');   
        if(JRequest::getVar('logout') != ''){                                       //Обработка комманды logout.
            Aut::logout();
        }
              
        if(Aut::login()){                                                       	//Проверка аутентифиации пользователя.
        	//Инициализация переменных.
            $TaskManagement = JRequest::getCmd('TaskManagement');
            $session = JFactory::getSession(); 
            $session_data = $session->get('Medical_Registry_data');
                
                    
            if(                                                                                    //Выполнение задач по редактирования личного расписания.
            	$TaskManagement == 'PersonalSchedule' &&
                Rights::getRights('3')                                                             //Проверка прав на чтение
            ){                      
            	$this->ActionPersonalSchedule();                                                   //Обрабатываем действия пользователя.                               
                $this->ViewlItem->DescriptionItemManagement = JText::_('REG_EDITING_PERSONAL_SCHEDULE');
                $this->ViewlItem->ToolsItemManagement = $this->ViewlItem->getFormContent($this->ModelItem->getMenuTools()); 
                $id_s = $session->get('Medical_Registry_id');
                $schedule = $session->get('Medical_Registry_schedule');
                $data = $schedule['date_editing'];
                $id_Schedule = $schedule['id_schedule_editing']; 
                $id = $id_s[0]['id_login'];
                         
                $arr = $this->ModelItem->getScheduleDataId($data, $id);                            //Получаем данные расписаний на дату и ИД.
                $this->ViewlItem->MenuItem = $this->ViewlItem->getMenuSchedule($arr, $data, $id, true);//Получаем меню расписаний.
                $this->ViewlItem->MenuItem = '<div class="inputbox">'.$this->ViewlItem->MenuItem.'</div>';         
                if(Rights::getRights('4')){                                    						//Проверка прав на добавления личного расписания.
                	$Add = JRequest::getVar('Add'); 
                }
                if(($data != '' && $id_Schedule != '') || $data != '' && $Add != ''){               //Если выбрана дата, то паказываем меню с расписаниями. 
                	$form = $this->ModelItem->getForm($id_Schedule, 'Schedule', 'SchedulelData');   //Получаем данные формы.  
                    //$this->ViewlItem->ContentItem = $this->ViewlItem->getForm($form);               //Выводим форму.
                    //Выводим форму.
                    $this->ViewlItem->ContentItem .= Tinterface::getTimeModalWindow('idformschedule', $this->ViewlItem->getForm($form, '', $this->ViewlItem->getMenuSchedule($arr, $data, $id)), 1000, true);                   
                }     
            }
            $this->ViewlItem->display($cachable, $urlparams);
        } 
        return $this; 
    }
    
    
    
    //Метод обработки действий пользователя PersonalSchedule
    private function ActionPersonalSchedule(){                                        
        $session_data = $this->SessionItem->get('Medical_Registry_data');           //Инициализация переменных. 
        
        $id_s = $this->SessionItem->get('Medical_Registry_id'); 
        $id = $id_s[0]['id_login'];                                                 //Полуаем идентификатор пользователя.
          
        $schedule = $this->SessionItem->get('Medical_Registry_schedule');
        $Name_post_select_schedule = 'select_schedule'.$id;                         //Формируем имя переменной POST выбора расписания.
        $select_schedule = JRequest::getVar($Name_post_select_schedule);            //Получаем данные с запроса. 
         
        $Save = JRequest::getVar('Save');
        $Cancel = JRequest::getVar('Cancel');
        $Select = JRequest::getVar('Select'); 
        $Add = JRequest::getVar('Add');
        $Remove = JRequest::getVar('Remove');  
        $DataSelectSchedule = JRequest::getVar('DataSelectSchedule');               //Получаем выбранную дату. 
        
        if(                             											//Обработка выбора даты.
        $DataSelectSchedule != '' &&
        $this->ModelItem->ValidationLogicSchedule('', $DataSelectSchedule, $id)		//Валидация выбранной даты.
        ){
            
            $schedule['date_editing'] = $DataSelectSchedule;
            unset($schedule['id_schedule_editing']);
        }
        if($schedule['date_editing'] != '' && $select_schedule != ''){              //Если выбрано разписание, то определяем и фиксируем ИД расписания.
            $schedule['id_schedule_editing'] = $this->ModelItem->getIdSchedule($select_schedule, $schedule['date_editing'], $id); 
        }
        if($schedule['date_editing'] != ''){
            $post = Tinterface::getPostForm();                                      //Полуения данных с формы.
            if($Cancel != ''){                                                      //Обработка зброса редактирования.
                unset($schedule['id_schedule_editing']);   
            }
            
            if($post != '' && $schedule['id_schedule_editing'] != ''){              //Если выбрали расписание и редактируем его.
                $post['id_login_schedule'] = $id;                                   //то сохраняем иденитификатор пользователя.
            }
            if(isset($post['hidden_flag_schedule'])){								//Если выбранно скрытое поле, то 
				$post['hidden_flag_schedule'] = true;								//Формируем флаг выбраного поля
            }
            else{
				$post['hidden_flag_schedule'] = false;								//Иначе сбрасываем флаг вибранного поля.
            }
            if(                                                                     //Обработка созранения.
            $Save != ''
            && $this->ModelItem->ValidationLogicSchedule($post, $schedule['date_editing'], $id)
            ){
                JRequest::checkToken() or jexit('Invalid Token');                   //Проверка токена. 
                $post['date_schedule'] = $schedule['date_editing'];
                $post['id_login_schedule'] = $id; 
                
                if(                                                                 //Обработка редактировани расписания. 
                $post['id_login_schedule'] != ''
                ){
                    if(Rights::getRights('5', true)){
                        $this->ModelItem->setData($post, $schedule['id_schedule_editing'], 'Schedule');
                        //Логирование действия. 
                        $this->ModelItem->LogEvents($this->LogInfo, 'PERSONAL SCHEDULE:', 'Schedule changed', $post);
                        $this->AppItem->enqueueMessage(JText::_('REG_SCHEDULE_CHANGED'));
                    }  
                }
                else{
                    if( Rights::getRights('4', true)){                              //Обработка добавления расписания.
                        $this->ModelItem->setData($post, '', 'Schedule');
                        //Логирование действия. 
                        $this->ModelItem->LogEvents($this->LogInfo, 'PERSONAL SCHEDULE:', 'Adding schedules', $post);
                        $this->AppItem->enqueueMessage(JText::_('REG_SCHEDULE_ADDED'));
                    } 
                }
                unset($schedule['id_schedule_editing']);
            }
            
            if($Remove != '' &&                                                     //Обработка удаления расписания  
            $schedule['id_schedule_editing'] != '' &&
            Rights::getRights('6', true)){                                                  
                $this->ModelItem->delData($schedule['id_schedule_editing'], 'Schedule');
                $this->AppItem->enqueueMessage(JText::_('REG_SCHEDULE_REMOVED')); 
                //Логирование действия. 
                $this->ModelItem->LogEvents($this->LogInfo, 'PERSONAL SCHEDULE:', 'Schedule removed', $schedule);
                unset($schedule['id_schedule_editing']); 
            }
        }
        $this->SessionItem->set('Medical_Registry_schedule', $schedule);            //Фиксация переменной.  
        $this->Ajax(); 
    } 
    
    
    
}