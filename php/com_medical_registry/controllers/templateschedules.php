<?php
/**
 * Default Controller
 * @author Олег Борисович Дубик
 */
/**
  * Контроллер сервиса "Электронная регистратура" для задачи "Registry_management", "Templateschedules".
  */
  

// Запрет прямого доступа.
defined( '_JEXEC' ) or die;  
  

class Medical_RegistryControllerTemplateschedules extends Medical_RegistryController{

   
    
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
                
                
            //Задача управления шаблонами расписаний.
            if(                                                                                             //Выполнение задач по управлению Шаблоном расписаний
                $TaskManagement == 'TemplateSchedules' &&
                Rights::getRights('11')                                                                     //Проверка прав просмотра пользователей. 
            ){
            	$this->ActionTemplate();
                $this->ViewlItem->DescriptionItemManagement = JText::_('REG_MANAGING_TEMPLATE_SCHEDULES');  //Инициализация переменных.
                $this->ViewlItem->ToolsItemManagement = $this->ModelItem->getMenuTools();
                     
                $Template = $this->SessionItem->get('Medical_Registry_Template');
                    
                //Предварительный просмотр расписания.
                $LineUsers1['0'] = $this->ViewlItem->getFormContent($this->ModelItem->getWeek($Template['id_Week']));
                $LineUsers1['1'] = $this->ViewlItem->getFormContent($this->ModelItem->getFormSpecialty('Template', $Template['id_specialty']));
                if($Template['id_specialty'] != ''){
                	$LineUsers2['0'] = $this->ViewlItem->getFormContent($this->ModelItem->getFormSelectUser('Template', $Template['id_specialty'], $Template['id_login'], '1')); 
                } 
                    
                $Line['1'] = $this->ViewlItem->getElementsLine($LineUsers1);                                    //Формируем 1 строку.
                $Line['2'] = $this->ViewlItem->getElementsLine($LineUsers2);                                    //Формируем 2 строку
                $Column[0] = $this->ViewlItem->getElementsColumn($Line);                                        //Формируем колонки.
                    
                $this->ViewlItem->ViewItemManagement = $this->ModelItem->getTableWeekTemplate($Template['id_Week'], $Template['id_specialty'], $Template['id_login']); 
                    
                //Подготовка расписания к печати.
                $this->ViewlItem->ToolsItemManagement = $this->ViewlItem->getFormContent($this->ViewlItem->ToolsItemManagement.$this->ModelItem->getTemplateTools($Template, true).$this->ModelItem->getButtonPrint('PrintTemplateSchedules')); 
                $this->ViewlItem->ViewItemManagement = $this->ViewlItem->getDivPrint($this->ViewlItem->ViewItemManagement, 'PrintTemplateSchedules');
                    
                if($Template['id_login'] != ''){
                	$Column[1] = $this->ViewlItem->getFormContent($this->ModelItem->getSelectTemplate('Template', $Template['id_Week'], $Template['id_login'], $Template['id_record_schedule']));
                    if($Template['id_record_schedule'] != '' ||  (JRequest::getVar('Add') && Rights::getRights('12', true))){
                    	$objForm = $this->ModelItem->getForm($Template['id_record_schedule'], 'Template', 'TemplateData'); 
                        $this->ViewlItem->ViewItemManagement .= Tinterface::getTimeModalWindow('idmodal', $this->ViewlItem->getForm($objForm, 'sfgsef', $this->ModelItem->getTemplateTools($Template)), 1000, true);
                    }
                } 
                $this->ViewlItem->MenuItem = $this->ViewlItem->getElementsLine($Column); 
                $this->ViewlItem->MenuItem = '<div class="inputbox">'.$this->ViewlItem->MenuItem.'</div>';   
                //$this->ViewlItem->ViewItemDebugging = $this->ModelItem->getTableWeekTemplate();
                $this->ViewlItem->ViewItemDebugging = $Template;
                $this->SessionItem->set('Medical_Registry_Template', $Template);                            //Фиксация переменной. 
            }
            $this->ViewlItem->display($cachable, $urlparams);
        }   
        return $this; 
    }
    
    
    
    //Метод обработки действий пользователя Template
    private function ActionTemplate(){
        $prefix = 'Template';
        $Template = $this->SessionItem->get('Medical_Registry_Template');                           //Инициализация переменных. 
        $id_Week = JRequest::getVar('id_Week');
        $TemplateReset = JRequest::getVar('TemplateReset'); 
        $id_specialty = JRequest::getVar('id_specialty'.$prefix);
        $id_login = JRequest::getVar('id_login'.$prefix);
        $id_record_schedule = JRequest::getVar('id_record_schedule'.$prefix);
        
        $Save = JRequest::getVar('Save');
        $Cancel = JRequest::getVar('Cancel');
        $Delete = JRequest::getVar('Delete');
        $Add = JRequest::getVar('Add');
        
        
        if($TemplateReset != ''){                                                                   //Обработка просмотра всех шаблонов.
            unset($Template);
        }  
        
        if($id_Week != ''){                                                                         //Обработка выбора дня недели.
            $Template['id_Week'] = $id_Week;
            unset($Template['id_record_schedule']);
            unset($Template['id_specialty']);
            unset($Template['id_login']);  
        }
        
        if($Template['id_Week'] != ''){
            if($id_specialty != ''){                                                                //Обработка выбора специальности.
                $Template['id_specialty'] = $id_specialty;
                unset($Template['id_login']);
                unset($Template['id_record_schedule']);  
            }
            if($Template['id_specialty'] != ''){
                if($id_login != ''){                                                                //Обработка выбора пользователя.
                    $Template['id_login'] = $id_login;
                    unset($Template['id_record_schedule']);
                }
                if($Template['id_login'] != ''){
                    if($id_record_schedule != ''){                                                  //Обработка выбора расписания.
                        $Template['id_record_schedule'] = $id_record_schedule;
                    }
                    $post = Tinterface::getPostForm();                                 	//Получение массива POST с формы.    
                    if(isset($post['hidden_flag_schedule'])){							//Если выбранно скрытое поле, то 
						$post['hidden_flag_schedule'] = true;							//Формируем флаг выбраного поля
				    }
				    else{
						$post['hidden_flag_schedule'] = false;							//Иначе сбрасываем флаг вибранного поля.
				    }
                    if($Cancel != ''){
                    	unset($Template['id_record_schedule']);
                        //unset($Template['id_login']);  
                    }
                    if($Template['id_record_schedule'] != ''){                          //Проверка на предмет добавления, или сохранения. 
                        if(                                                             //Обработка сохранения.
                        $Save != '' && Rights::getRights('13', true) &&
                        $this->ModelItem->ValidationLogicSchedule($post, '', '', $Template)
                        ){                                                              //Обработка сохранения
                            JRequest::checkToken() or jexit('Invalid Token');           //Проверка токена.
                            if($this->ViewlItem->ValidationArray($post, true)){         //При непрохождении валидации выводим сообщение
                                $this->ModelItem->setData($post, $Template['id_record_schedule'], 'Template');
                                //Логирование действия.
                                $this->ModelItem->LogEvents($this->LogInfo, 'SCHEDULE TEMPLATE:', 'Change the template', $post); 
                                $this->AppItem->enqueueMessage(JText::_('REG_CHANGES_MADE_SUCCESSFULLY')); //Формируем сообщение об успешном изменении.
                                //unset($Template['id_login']);
                                unset($Template['id_record_schedule']); 
                            }
                        }
                        
                        if($Delete != '' && Rights::getRights('14', true)){             //Обработка удаления
                            JRequest::checkToken() or jexit('Invalid Token');           //Проверка токена.
                            $this->ModelItem->delData($Template['id_record_schedule'], 'Template');
                            $this->AppItem->enqueueMessage(JText::_('REG_SCHEDULE_REMOVED'));
                            //Логирование действия.
                            $this->ModelItem->LogEvents($this->LogInfo, 'SCHEDULE TEMPLATE:', 'The template schedules deleted', $Template); 
                            //unset($Template['id_login']);
                            unset($Template['id_record_schedule']);
                        }
                    }
                    else{
                        if(
                        $Save != '' &&
                        $this->ModelItem->ValidationLogicSchedule($post, '', '', $Template)
                        ){
                            if(Rights::getRights('12', true)){                              //Обработка сохранения нового расписания.
                                JRequest::checkToken() or jexit('Invalid Token');           //Проверка токена.
                                if($this->ViewlItem->ValidationArray($post, true)){         //При непрохождении валидации выводим сообщение 
                                    $post['id_week'] = $Template['id_Week'];
                                    $post['id_login_schedule'] = $Template['id_login'];
                                    $this->ModelItem->setData($post, '', 'Template'); 
                                    $this->AppItem->enqueueMessage(JText::_('REG_THE_NEW_SCHEDULE_CREATED')); //Формируем сообщение об успешном изменении. 
                                    //Логирование действия. 
                                    $this->ModelItem->LogEvents($this->LogInfo, 'SCHEDULE TEMPLATE:', 'Created a new template schedules', $post);
                                    //unset($Template['id_login']); 
                                }
                            }
                        }  
                    }
                }
            }
        }
        $this->ViewlItem->ViewItemDebugging = $Template;
        $this->SessionItem->set('Medical_Registry_Template', $Template);                            //Фиксация переменной.
        $this->Ajax();   
    }   
}