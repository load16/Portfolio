<?php
/**
 * Default Controller
 * @author Олег Борисович Дубик
 */
/**
  * Контроллер сервиса "Электронная регистратура" для задачи "Registry_management".
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



class Medical_RegistryControllerRegistry_management extends Medical_RegistryController{

   
    
    //Задача управления регистратурой.
    function Registry_ManagementTask($cachable = false, $urlparams = array()){
        $this->setParameters('Registry_Management');   
        if(JRequest::getVar('logout') != ''){                                       //Обработка комманды logout.
            Aut::logout();
        }
              
            if(Aut::login()){                                                       //Проверка аутентифиации пользователя.
                //Инициализация переменных.
                $TaskManagement = JRequest::getCmd('TaskManagement');
                $session = JFactory::getSession(); 
                $session_data = $session->get('Medical_Registry_data');
                
                if($TaskManagement != ''){                                          //Если нет задач, по показывает меню.
                    
                }
                else{                                                               //Выполнение задач по управлению.
                    $this->ViewlItem->DescriptionItemManagement = JText::_('REG_SELECT_THE_TASK');
                    $this->ViewlItem->ToolsItemManagement = $this->ViewlItem->getFormContent($this->ModelItem->getMenuTools());
                    $this->ViewlItem->MenuItem = $this->ModelItem->getMenuButton(); 
                }
                
                
                
                                                         
                if(                                                             //Выполнение задачи по редактированию личных данных. 
                    $TaskManagement == 'PersonalData' &&
                    (Rights::getRights('1') || Rights::getRights('2'))
                ){
                    $this->ActionPersonalData();                                //Обрабатываем действия пользователе.
                    $this->ViewlItem->DescriptionItemManagement = JText::_('REG_EDIT_PERSONAL_DATA');
                    $this->ViewlItem->ToolsItemManagement = $this->ViewlItem->getFormContent($this->ModelItem->getMenuTools()); 
                    $id_s = $session->get('Medical_Registry_id');
                    $this->ViewlItem->ContentItem = $this->ViewlItem->getForm($this->ModelItem->getForm($id_s[0]['id_login'], 'Login', 'PersonalData')); //Выводим форму для редактирования.;
                }
                    
                    
                    
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
                    $this->ViewlItem->MenuItem = $this->ViewlItem->getMenuSchedule($arr, $data, $id);  //Получаем меню расписаний.
                         
                    if(Rights::getRights('4')){                                    //Проверка прав на добавления личного расписания.
                        $Add = JRequest::getVar('Add'); 
                    }
                    if(($data != '' && $id_Schedule != '') || $data != '' && $Add != ''){                  //Если выбрана дата, то паказываем меню с расписаниями. 
                        $form = $this->ModelItem->getForm($id_Schedule, 'Schedule', 'SchedulelData');      //Получаем данные формы.  
                        $this->ViewlItem->ContentItem = $this->ViewlItem->getForm($form);                  //Выводим форму.    
                    }
                             
                }
                
                
                
                
                
                
                
                
                
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
                    && $Add == ''
                    && $id_select_schedule == '' 
                    ){
                        $this->ViewlItem->ViewItemManagement = $ModelItemSchedule->getTableWeekSchedule($date_editing, $Schedules['id_specialty'], $Schedules['id_login']);
                        $this->ViewlItem->ViewItemManagement = $this->ViewlItem->getDivPrint($this->ViewlItem->ViewItemManagement, 'PrintSchedules');
                        $this->ViewlItem->ViewItemManagement = $this->ModelItem->getDivTools($this->ModelItem->getButtonPrint('PrintSchedules')).$this->ViewlItem->ViewItemManagement;
                    }
                    if(                                                                             //Просмотр расписаний на дату. 
                    $Schedules['DateSchedule_schedule_editing'] != '' 
                    && $Add == ''
                    && $id_select_schedule == ''
                    ){
                        $this->ViewlItem->ViewItemManagement = $ModelItemSchedule->getTableSchedule(JText::_('REG_SCHEDULE_FOR_THE').' '.'<b>'.$date_editing.'</b>', $date_editing, $Schedules['id_specialty'], $Schedules['id_login']);   
                        $this->ViewlItem->ViewItemManagement = $this->ViewlItem->getDivPrint($this->ViewlItem->ViewItemManagement, PrintSchedules);
                        $this->ViewlItem->ViewItemManagement = $this->ModelItem->getDivTools($this->ModelItem->getButtonPrint('PrintSchedules')).$this->ViewlItem->ViewItemManagement;
                    }
                     
                    $lineSchedulesMenu['0'] = $formSecectUser;
                    if($id_login != ''){
                        $formSelectDate = $this->ViewlItem->getFormContent($this->ModelItem->getFormDate($id_component, $date_editing)); 
                        $lineSchedulesMenu['1'] = $formSelectDate;
                        if($date_editing != ''){
                            //Выводим меню управления
                            $lineSchedulesMenu['2'] = $this->ViewlItem->getFormContent($this->ModelItem->getFormControlSchedule($id_select_schedule, $date_editing));
                            $arr = $this->ModelItem->getScheduleDataId($date_editing, $id_login);                            //Получаем данные расписаний на дату и ИД.
                            if(count($arr) != 0){
                                $lineSchedulesMenu['3'] = $this->ViewlItem->getFormSelectSchedule($arr, $date_editing, $id_login);  //Получаем меню расписаний
                            }
                            if($id_select_schedule != ''){
                                $form = $this->ModelItem->getForm($id_select_schedule, 'Schedule', 'SchedulelData');      //Получаем данные формы.  
                                $this->ViewlItem->ContentItem = $this->ViewlItem->getForm($form);                         //Выводим форму.
                            }
                            
                         
                            if(                                                                                           //Если выбрана дата, то паказываем меню с расписаниями. 
                            ($date_editing != '' && $id_select_schedule != '' && Rights::getRights('9')) || 
                            ($date_editing != '' && $Add != '' && Rights::getRights('7'))
                            ){  
                                //Получаем данные формы.
                                $form = $this->ModelItem->getForm($id_select_schedule, 'Schedule', 'SchedulelData');      //Получаем данные формы.  
                                $this->ViewlItem->ContentItem = $this->ViewlItem->getForm($form);                         //Выводим форму.    
                             }
                        }
                    }
                    //$this->ViewlItem->ViewItemDebugging = $Schedules;
                    $this->ViewlItem->MenuItem = $this->ViewlItem->getElementsLine($lineSchedulesMenu);
                    $this->SessionItem->set('Medical_Registry_Schedules', $Schedules);        //Фиксация переменной.  
                }
                
                
                
                
                
                
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
                    Rights::getRights('16', true)                                                                   //Проверка прав.
                    ){                                                                                              //Обработка регистрации пациента.
                        $this->ViewlItem->DescriptionItemManagement = JText::_('REG_REGISTER_A_PATIENT_TO_MAKE_AN_APPOINTMENT');
                        $form = $this->ModelItem->getForm('', 'Record', 'RegisterData', $data['REG_SELECTED_PATIENT']);
                        $this->ViewlItem->MenuItem = $this->ViewlItem->getForm($form, 'registration');
                    }
                    else{                                                                                           //Иначе ведем обычную обработку.
                        $this->ViewlItem->DescriptionItemManagement = JText::_('REG_MANAGING_APPOINTMENTS');        //Инициализация переменных.
                        $this->ViewlItem->ToolsItemManagement = $this->ViewlItem->getFormContent($this->ModelItem->getMenuTools().$this->ModelItem->getToolsManagementRecords());

                        
                        $data = $this->ModelItem->getDataControlPanel($data);                                       //Получаем данные для вкладок
                        $this->ViewlItem->MenuItem = $this->ViewlItem->getDataControlPanel($data, $Records['id_Tabs1']);
                    }
                    $this->ViewlItem->ViewItemDebugging = $Records;   
                    $Records['data'] = $data;
                    $this->SessionItem->set('Medical_Registry_Records', $Records);                                  //Фиксация переменной.           
                }
                        
                
                
                //Выполнение задач по управлению пользователями 
                if(                                                                                             
                $TaskManagement == 'Users' &&
                Rights::getRights('19')                                                                         //Проверка прав просмотра пользователей. 
                ){
                    $this->ActionUsers();                                                                       //Обработка действий пользователя.
                    $this->ViewlItem->DescriptionItemManagement = JText::_('REG_USER_MANAGEMENT');              //Инициализация переменных.
                    $this->ViewlItem->ToolsItemManagement = $this->ViewlItem->getFormContent($this->ModelItem->getMenuTools());
                    $Users = $this->SessionItem->get('Medical_Registry_Users');
                    $LineUsers1['0'] = $this->ViewlItem->getFormContent($this->ModelItem->getControlObject($Users['setObject']));
                    
                    //При выборе объекта группы.
                    if($Users['setObject'] == '0' && Rights::getRights('23')){
                        $LineUsers1['1'] = $this->ViewlItem->getFormContent($this->ModelItem->getGroupSelection($Users['id_GroupSelection']));
                        if($Users['id_GroupSelection'] != ''){                                                  //Выбор редактирования прав группе.
                            $this->ViewlItem->ViewItemManagement = $this->ViewlItem->getFormContent($this->ModelItem->getEditRights($Users));
                        }
                    }
                    
                    //При выборе объекта пользователи.
                    if($Users['setObject'] == '1'){
                        if($Users['id_SelectionAction'] == 'AddUsers'){                                         //Выбор добавления пользователя.
                            $FormEditUser = $this->ModelItem->getForm('', 'Login', 'UserData');
                            $this->ViewlItem->ViewItemManagement = $this->ViewlItem->getForm($FormEditUser);
                        }
                        //Выбор специальности.
                        $LineUsers1['1'] = $this->ViewlItem->getFormContent($this->ModelItem->getFormSpecialty('Users', $Users['id_specialty']));
                        if($Users['id_specialty'] != ''){                                                       //При выборе специальности.
                            //Выбор пользователя.
                            $LineUsers2['2'] = $this->ViewlItem->getFormContent($this->ModelItem->getFormSelectUser('Users', $Users['id_specialty'], $Users['id_Users'], '1'));   
                            //Выбор дейсвия.
                            $LineUsers2['3'] = $this->ViewlItem->getFormContent($this->ModelItem->getSelectionAction($Users));  
                            if($Users['id_Users'] != ''){                                                            //При выборе пользователя. 
                                if($Users['id_SelectionAction'] == 'EditUsers'){                                     //При выборе редактрования показываем форму.
                                    $FormEditUser = $this->ModelItem->getForm($Users['id_Users'], 'Login', 'UserData');
                                    $this->ViewlItem->ViewItemManagement = $this->ViewlItem->getForm($FormEditUser);
                                }
                                
                                if($Users['id_SelectionAction'] == 'RightsUsers'){                                  //Выбор редактированя прав пользователя.
                                    $this->ViewlItem->ViewItemManagement = $this->ViewlItem->getFormContent($this->ModelItem->getEditRights($Users));
                                }
                            }
                        }      
                    }
                    $Line['1'] = $this->ViewlItem->getElementsLine($LineUsers1);                                    //Формируем 1 строку.
                    $Line['2'] = $this->ViewlItem->getElementsLine($LineUsers2);                                    //Формируем 2 строку
                    $Column = $this->ViewlItem->getElementsColumn($Line);                                           //Формируем колонки.
                    
                     
                    $this->ViewlItem->MenuItem = $Column;                                                           //Показываем.
                    
                    if($this->ViewlItem->ViewItemManagement != ''){
                        $this->ViewlItem->ViewItemManagement = '<div class="MEDICAL_REGISTRY_Form">'.$this->ViewlItem->ViewItemManagement.'</div>';
                    }
                    
                    
                    
                    $this->ViewlItem->ViewItemDebugging = $Users;
                    $this->SessionItem->set('Medical_Registry_Users', $Users);                                  //Фиксация переменной.   
                }
                
                
                //Задача управления шаблонами расписаний.
                if(                                                                                             //Выполнение задач по управлению Шаблоном расписаний
                $TaskManagement == 'TemplateSchedules' &&
                Rights::getRights('11')                                                                         //Проверка прав просмотра пользователей. 
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
                    $this->ViewlItem->ToolsItemManagement = $this->ViewlItem->getFormContent($this->ViewlItem->ToolsItemManagement.$this->ModelItem->getTemplateTools($Template).$this->ModelItem->getButtonPrint('PrintTemplateSchedules')); 
                    $this->ViewlItem->ViewItemManagement = $this->ViewlItem->getDivPrint($this->ViewlItem->ViewItemManagement, 'PrintTemplateSchedules');
                    
                    if($Template['id_login'] != ''){
                        $Column[1] = $this->ViewlItem->getFormContent($this->ModelItem->getSelectTemplate('Template', $Template['id_Week'], $Template['id_login'], $Template['id_record_schedule']));
                        if($Template['id_record_schedule'] != '' ||  (JRequest::getVar('Add') && Rights::getRights('12', true))){
                            $objForm = $this->ModelItem->getForm($Template['id_record_schedule'], 'Template', 'TemplateData'); 
                            $this->ViewlItem->ViewItemManagement = $this->ViewlItem->getForm($objForm);
                        }
                    } 
                    $this->ViewlItem->MenuItem = $this->ViewlItem->getElementsLine($Column); 
                    
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
                    if($Template['id_record_schedule'] != ''){                          //Проверка на предмет добавления, или сохранения.
                        if($Cancel != ''){
                            unset($Template['id_record_schedule']);
                            unset($Template['id_login']);  
                        }
                        $post = Tinterface::getPostForm();                              //Получение массива POST с формы. 
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
                                unset($Template['id_login']);
                                unset($Template['id_record_schedule']); 
                            }
                        }
                        
                        if($Delete != '' && Rights::getRights('14', true)){             //Обработка удаления
                            JRequest::checkToken() or jexit('Invalid Token');           //Проверка токена.
                            $this->ModelItem->delData($Template['id_record_schedule'], 'Template');
                            $this->AppItem->enqueueMessage(JText::_('REG_SCHEDULE_REMOVED'));
                            //Логирование действия.
                            $this->ModelItem->LogEvents($this->LogInfo, 'SCHEDULE TEMPLATE:', 'The template schedules deleted', $Template); 
                            unset($Template['id_login']);
                            unset($Template['id_record_schedule']);
                        }
                    }
                    else{
                        $post = Tinterface::getPostForm();                                 //Получение массива POST с формы.    
                        if(
                        $Save != '' &&
                        $this->ModelItem->ValidationLogicSchedule($post, '', '', $Template)
                        ){
                            if(Rights::getRights('12', true)){                              //Обработка сохранения нового расписания.
                                JRequest::checkToken() or jexit('Invalid Token');           //Проверка токена.
                                $post = Tinterface::getPostForm();                          //Получение массива POST с формы.
                                if($this->ViewlItem->ValidationArray($post, true)){         //При непрохождении валидации выводим сообщение 
                                    $post['id_week'] = $Template['id_Week'];
                                    $post['id_login_schedule'] = $Template['id_login'];
                                    $this->ModelItem->setData($post, '', 'Template'); 
                                    $this->AppItem->enqueueMessage(JText::_('REG_THE_NEW_SCHEDULE_CREATED')); //Формируем сообщение об успешном изменении. 
                                    //Логирование действия. 
                                    $this->ModelItem->LogEvents($this->LogInfo, 'SCHEDULE TEMPLATE:', 'Created a new template schedules', $post);
                                    unset($Template['id_login']); 
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
    
    
    //Метод обработки действий пользователя Users
    private function ActionUsers(){
        $prefix = 'Users';
        $Users = $this->SessionItem->get('Medical_Registry_Users');
        $setObject = JRequest::getVar('setObject');                                                     //Инициализация переменных.
        $id_GroupSelection = JRequest::getVar('id_GroupSelection');
        $id_specialty = JRequest::getVar('id_specialty'.$prefix); 
        $id_Users = JRequest::getVar('id_login'.$prefix);
        $id_SelectionAction = JRequest::getVar('id_SelectionAction');
        
        $Save = JRequest::getVar('Save');
        $Cancel = JRequest::getVar('Cancel');
        
        if($setObject != ''){                                                                           //Обработка выбора объекта управления.
            $Users['setObject'] = $setObject;
            unset($Users['id_SelectionAction']);
        }
        
        if($Users['setObject'] == '0'){
            if($Users['id_GroupSelection'] != ''){
                if($Save != ''){
                    if(Rights::getRights('23', true)){                              //Проверка прав на редактирование прав пользователя. 
                        JRequest::checkToken() or jexit('Invalid Token');           //Проверка токена.
                        $this->ModelItem->SaveRights($Users, $_POST);
                        $this->AppItem->enqueueMessage(JText::_('REG_CHANGES_MADE_SUCCESSFULLY')); //Формируем сообщение об успешном изменении
                        //Логирование действия. 
                        $this->ModelItem->LogEvents($this->LogInfo, 'USER MANAGEMENT:', 'User group the right to change', $_POST); 
                        unset($Users['id_GroupSelection']);
                    }
                }
                if($Cancel != ''){                                                     //Обработка отмены сохранения.
                    unset($Users['id_GroupSelection']);                                //Отмена выбора действия.
                }
            }    
            if($id_GroupSelection != ''){                                                               //Обработка выбора группы пользователей.
                $Users['id_GroupSelection'] = $id_GroupSelection;
            }
        }
        
        if($Users['setObject'] == '1'){
            if($id_specialty != ''){                                                                    //Обработка выбора специальности.
                $Users['id_specialty'] = $id_specialty;
                unset($Users['id_Users']);
                unset($Users['id_SelectionAction']); 
            }
            if($id_Users != ''){                                                                        //Обработка выбора пользователя.
                $Users['id_Users'] = $id_Users;
                unset($Users['id_SelectionAction']); 
            }
            if($id_SelectionAction != ''){                                                              //Обработка выбора действия.
                $Users['id_SelectionAction'] = $id_SelectionAction;
            }
            if($Users['id_SelectionAction'] == 'AddUsers'){                         //Обработка добавления пользователя.
                if($Save != ''){
                    if(                              								//Проверка прав на создание пользователя.
                    Rights::getRights('21', true)
                    ){
                        JRequest::checkToken() or jexit('Invalid Token');           //Проверка токена.
                        $post = Tinterface::getPostForm();                          //Получение массива POST с формы.
                        if(         												//При непрохождении валидации выводим сообщение
                        $this->ViewlItem->ValidationArray($post, true) && $this->ModelItem->ValidationRedactedUserData($post, '', true)
                        ){
                            $this->ModelItem->setData($post, '', 'Login'); 
                            $this->AppItem->enqueueMessage(JText::_('REG_USER_ADDED')); //Формируем сообщение об успешном изменении. 
                            //Логирование действия. 
                            $this->ModelItem->LogEvents($this->LogInfo, 'USER MANAGEMENT:', 'Added new user', $post); 
                            unset($Users['id_SelectionAction']);
                        }
                    }
                    unset($post);
                }
                if($Cancel != ''){                                                     //Обработка отмены сохранения.
                    unset($Users['id_SelectionAction']);                               //Отмена выбора действия.
                }
            }
            
            if($Users['id_specialty'] != ''){
                if($Users['id_Users'] != ''){
                    if($Users['id_SelectionAction'] == 'RightsUsers'){                      //Обработка сохраниения прав пользователя.
                        if($Save != ''){
                            if(Rights::getRights('22', true &&                              //Проверка прав на редактирование прав пользователя. 
                            $this->ViewlItem->ValidationArray($_POST, true)                 //И на враждебный код.
                            )){                                                             
                                JRequest::checkToken() or jexit('Invalid Token');           //Проверка токена.
                                $this->ModelItem->SaveRights($Users, $_POST);
                                $this->AppItem->enqueueMessage(JText::_('REG_USER_RIGHTS_CHANGED')); //Формируем сообщение об успешном изменении
                                //Логирование действия. 
                                $this->ModelItem->LogEvents($this->LogInfo, 'USER MANAGEMENT:', 'User rights changed', $_POST);
                                unset($Users['id_SelectionAction']);
                            }
                        }
                        if($Cancel != ''){                                                     //Обработка отмены сохранения.
                            unset($Users['id_SelectionAction']);                               //Отмена выбора действия.
                        }
                            
                    }
                                        
                    if($Users['id_SelectionAction'] == 'EditUsers'){                        	//Обработка редактирования пользователя.
                        if($Save != ''){
                            if(                              									//Проверка прав на редактирование.
                            Rights::getRights('20', true) 
                            ){
                                JRequest::checkToken() or jexit('Invalid Token');           	//Проверка токена.
                                $post = Tinterface::getPostForm();                              //Получение массива POST с формы.
                                if(      														//При непрохождении валидации выводим сообщение
                                $this->ViewlItem->ValidationArray($post, true) && $this->ModelItem->ValidationRedactedUserData($post, $Users['id_Users'], true)
                                ){
                                    $this->ModelItem->setData($post, $Users['id_Users'], 'Login'); 
                                    $this->AppItem->enqueueMessage(JText::_('REG_USER_DATA_CHANGED')); //Формируем сообщение об успешном изменении. 
                                    //Логирование действия. 
                                    $this->ModelItem->LogEvents($this->LogInfo, 'USER MANAGEMENT:', 'User data changed', $post);
                                    unset($Users['id_SelectionAction']);
                                }
                                unset($post);
                            }
                        }
                        if($Cancel != ''){                                                     //Обработка отмены сохранения.
                            unset($Users['id_SelectionAction']);                               //Отмена выбора действия.
                        }
                    }
                }
            }
        }                                                    
        $this->SessionItem->set('Medical_Registry_Users', $Users);                             //Фиксация переменной. 
        $this->Ajax();   
    }
    
    
    
     
     
    
    //Метод обработки действий пользователя Records
    private function ActionRecords(){
        $Records = $this->SessionItem->get('Medical_Registry_Records');                 //Инициализация переменных. 
        $data = $Records['data'];
        $dd = new data();
        $ModelAppointment = &$this->getModel('Appointment_doctor');
        $registration = JRequest::getVar('registration');
        $SelectDateTime = JRequest::getVar('SelectDateTime');
        $Save = JRequest::getVar('Save');
        $DeleteRecordedt = JRequest::getVar('DeleteRecordedt');
        $OffRecordedt = JRequest::getVar('OffRecordedt');
        $ActivateRecordedt = JRequest::getVar('ActivateRecordedt'); 
        $TransferRecordedt = JRequest::getVar('TransferRecordedt');
        $MakeAppointment = JRequest::getVar('MakeAppointment');
        
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
             $post = Tinterface::getPostForm();
             
             
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
        
        if($DataSelectSchedule != '' && $Select != ''){                             //Если выбрана дата то фиксируем изменение.
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
    
    
    //Метод обработки действий пользователя PersonalData
    private function ActionPersonalData(){                             
        $session_data = $this->SessionItem->get('Medical_Registry_data');       //Инициализация переменных.    
        $id_s = $this->SessionItem->get('Medical_Registry_id'); 
        $id = $id_s[0]['id_login'];
        $Save = JRequest::getVar('Save');
        $Cancel = JRequest::getVar('Cancel');
        $url = & JFactory::getURI();
       
        if(
        $Save != '' &&
        Rights::getRights('2', true)                                    //Проверка прав.
        ){
            JRequest::checkToken() or jexit('Invalid Token');           //Проверка токена.
            $post = Tinterface::getPostForm();                          //Получение массива POST с формы.
            if($this->ViewlItem->ValidationArray($post, true)){         //При непрохождении валидации выводим сообщение
                 //Логирование действия. 
                $this->ModelItem->LogEvents($this->LogInfo, 'PERSONAL DATA:', 'Personal data changed', $post);
                $this->ModelItem->setData($post, $id, 'Login'); 
                $this->AppItem->enqueueMessage(JText::_('REG_CHANGES_MADE_SUCCESSFULLY').'.');//Формируем сообщение об успешном изменении.                     
                $redirect = JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Registry_ManagementTask'); //Формируем URL.  
                $this->AppItem->redirect($redirect);                                  //Формируем сообщение.
            }
        }
        if($Cancel != ''){                                                      //Обработка отмены сохранения.
            $redirect = JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Registry_ManagementTask');  //Формируем URL.  
            $this->AppItem->redirect($redirect);                                //Перенаправляем на новый URL. $redirect = JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Registry_ManagementTask');
        }
        $this->Ajax();
    }
    
    
}