<?php
/**
 * Default Controller
 * @author Олег Борисович Дубик
 */
/**
  * Контроллер сервиса "Электронная регистратура" для задачи "Registry_management", "Users".
  */
  

// Запрет прямого доступа.
defined( '_JEXEC' ) or die;  
  

class Medical_RegistryControllerUsers extends Medical_RegistryController{

   
    
    //Задача управления регистратурой.
    function Registry_ManagementTask($cachable = false, $urlparams = array()){
        $this->setParameters('Registry_Management');   
        if(JRequest::getVar('logout') != ''){                                       						//Обработка комманды logout.
            Aut::logout();
        }
              
        if(Aut::login()){                                                       							//Проверка аутентифиации пользователя.
        	//Инициализация переменных.
            $TaskManagement = JRequest::getCmd('TaskManagement');
            $session = JFactory::getSession(); 
            $session_data = $session->get('Medical_Registry_data');
                
            //Выполнение задач по управлению пользователями 
            if(                                                                                             
                $TaskManagement == 'Users' &&
                Rights::getRights('19')                                                                     //Проверка прав просмотра пользователей. 
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
                    	//$this->ViewlItem->ViewItemManagement = $this->ViewlItem->getFormContent($this->ModelItem->getEditRights($Users));
                    	$getForm = Tinterface::getTimeModalWindow('idformuser', $this->ViewlItem->getFormContent($this->ModelItem->getEditRights($Users)), 1000);
                    }
                }
                    
                //При выборе объекта пользователи.
                if($Users['setObject'] == '1'){
                	if($Users['id_SelectionAction'] == 'AddUsers'){                                         //Выбор добавления пользователя.
                    	$FormEditUser = $this->ModelItem->getForm('', 'Login', 'UserData');
                        $this->ViewlItem->ViewItemManagement = $this->ViewlItem->getForm($FormEditUser);
                        //$getForm = Tinterface::getTimeModalWindow('idformuser', $this->ViewlItem->getForm($FormEditUser), 1000);
                    }
                    //Выбор специальности.
                    $LineUsers1['1'] = $this->ViewlItem->getFormContent($this->ModelItem->getFormSpecialty('Users', $Users['id_specialty']));
                    if($Users['id_specialty'] != ''){                                                       //При выборе специальности.
	                    //Выбор пользователя.
	                    $LineUsers2['2'] = $this->ViewlItem->getFormContent($this->ModelItem->getFormSelectUser('Users', $Users['id_specialty'], $Users['id_Users'], '1'));   
	                    //Выбор дейсвия.
	                    $LineUsers2['3'] = $this->ViewlItem->getFormContent($this->ModelItem->getSelectionAction($Users));  
	                    if($Users['id_Users'] != ''){                                                        //При выборе пользователя. 
                    		if($Users['id_SelectionAction'] == 'EditUsers'){                                 //При выборе редактрования показываем форму.
                        		$FormEditUser = $this->ModelItem->getForm($Users['id_Users'], 'Login', 'UserData');
	                            $this->ViewlItem->ViewItemManagement = $this->ViewlItem->getForm($FormEditUser);
	                            //$getForm = Tinterface::getTimeModalWindow('idformuser', $this->ViewlItem->getForm($FormEditUser), 1000);
	                        }
	                                
	                        if($Users['id_SelectionAction'] == 'RightsUsers'){                                //Выбор редактированя прав пользователя.
                        		//$this->ViewlItem->ViewItemManagement = $this->ViewlItem->getFormContent($this->ModelItem->getEditRights($Users));
                        		$getForm = Tinterface::getTimeModalWindow('idformuser', $this->ViewlItem->getFormContent($this->ModelItem->getEditRights($Users)), 1000);
	                        }
	                    }
                	}      
            	}
	            $Line['1'] = $this->ViewlItem->getElementsLine($LineUsers1);                                  //Формируем 1 строку.
	            $Line['2'] = $this->ViewlItem->getElementsLine($LineUsers2);                                  //Формируем 2 строку
	            $Column = $this->ViewlItem->getElementsColumn($Line);                                         //Формируем колонки.
	                    
	                     
	            $this->ViewlItem->MenuItem = $Column;                                                         //Показываем.
	            $this->ViewlItem->MenuItem = '<div class="inputbox">'.$this->ViewlItem->MenuItem.'</div>'; 
	                    
	            if($this->ViewlItem->ViewItemManagement != ''){
            		$this->ViewlItem->ViewItemManagement = '<div class="form-validate">'.$this->ViewlItem->ViewItemManagement.'</div>';
	            }
	            if($getForm != ''){																				//Если есть форма, то показываем ее.
					$this->ViewlItem->ViewItemManagement .= $getForm;
	            }
	                    
	            $this->ViewlItem->ViewItemDebugging = $Users;
	            $this->SessionItem->set('Medical_Registry_Users', $Users);                                  	//Фиксация переменной.   
        	}
        	$this->ViewlItem->display($cachable, $urlparams);
        } 
        return $this; 
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
   
}