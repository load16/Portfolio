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


class Medical_RegistryControllerRegistry_management extends Medical_RegistryController{

   
    
    //Задача управления регистратурой.
    function Registry_ManagementTask($cachable = false, $urlparams = array()){
        $this->setParameters('Registry_Management');   
        if(JRequest::getVar('logout') != ''){                                   //Обработка комманды logout.
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
        	$this->ViewlItem->display($cachable, $urlparams);
    	} 
        return $this; 
    }
    
    
}