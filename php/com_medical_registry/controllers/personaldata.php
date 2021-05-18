<?php
/**
 * Default Controller
 * @author Олег Борисович Дубик
 */
/**
  * Контроллер сервиса "Электронная регистратура" для задачи "Registry_management" , "PersonalData".
  */
  

// Запрет прямого доступа.
defined( '_JEXEC' ) or die;  


class Medical_RegistryControllerPersonalData extends Medical_RegistryController{

   
    
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
                                                        
            if(                                                             		//Выполнение задачи по редактированию личных данных. 
            $TaskManagement == 'PersonalData' &&
            (Rights::getRights('1') || Rights::getRights('2'))
            ){
            	$this->ActionPersonalData();                                		//Обрабатываем действия пользователе.
                $this->ViewlItem->DescriptionItemManagement = JText::_('REG_EDIT_PERSONAL_DATA');
                $this->ViewlItem->ToolsItemManagement = $this->ViewlItem->getFormContent($this->ModelItem->getMenuTools()); 
                $id_s = $session->get('Medical_Registry_id');
                $form = $this->ModelItem->getForm($id_s[0]['id_login'], 'Login', 'PersonalData');
                $this->ViewlItem->ContentItem = $this->ViewlItem->getForm($form, 'idLogin'); //Выводим форму для редактирования.;
            }
            $this->ViewlItem->display($cachable, $urlparams);
        } 
        return $this; 
    }
    
    
    //Метод обработки действий пользователя PersonalData
    private function ActionPersonalData(){                             
        $session_data = $this->SessionItem->get('Medical_Registry_data');       	//Инициализация переменных.    
        $id_s = $this->SessionItem->get('Medical_Registry_id'); 
        $id = $id_s[0]['id_login'];
        $Save = JRequest::getVar('Save');
        $Cancel = JRequest::getVar('Cancel');
        $url = & JFactory::getURI();
       
        if(
        $Save != '' &&
        Rights::getRights('2', true)                                    			//Проверка прав.
        ){
            JRequest::checkToken() or jexit('Invalid Token');           			//Проверка токена.
            $post = Tinterface::getPostForm();                          			//Получение массива POST с формы.
            if($this->ViewlItem->ValidationArray($post, true)){         			//При непрохождении валидации выводим сообщение
                 //Логирование действия. 
                $this->ModelItem->LogEvents($this->LogInfo, 'PERSONAL DATA:', 'Personal data changed', $post);
                $this->ModelItem->setData($post, $id, 'Login'); 
                $this->AppItem->enqueueMessage(JText::_('REG_CHANGES_MADE_SUCCESSFULLY').'.');//Формируем сообщение об успешном изменении.                     
                $redirect = JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Registry_ManagementTask'); //Формируем URL.  
                $this->AppItem->redirect($redirect);                                 //Формируем сообщение.
            }
        }
        if($Cancel != ''){                                                     		 //Обработка отмены сохранения.
            $redirect = JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Registry_ManagementTask');  //Формируем URL.  
            $this->AppItem->redirect($redirect);                                	//Перенаправляем на новый URL.
        }
        $this->Ajax();
    }
    
    
}