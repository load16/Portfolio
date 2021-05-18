<?php
/**
 * Default Controller
 * @author Олег Борисович Дубик
 */
/**
  * Контроллер сервиса "Электронная регистратура" для задачи "Recall" , просмотра отзывов.
  */
  

// Запрет прямого доступа.
defined( '_JEXEC' ) or die;  
  

class Medical_RegistryControllerRecall extends Medical_RegistryController{

   
    
    //Задача управления регистратурой.
    function RecallTask($cachable = false, $urlparams = array()){
        $this->setParameters('Recall');												//Установка параметров.   
        if(JRequest::getVar('logout') != ''){                                       //Обработка комманды logout.
            Aut::logout();
        }
              
        if(Aut::login()){                                                      		//Проверка аутентифиации пользователя.
            $this->ActionRecall();													//Обработка действый пользователей.
            $ModelRegistry_management = &$this->getModel('Registry_management');	//Получаем модели с управления регистратурой.
            //Показывем панель инструментов.
            $this->ViewlItem->ToolsItemRecall = Tinterface::getUniFormed($ModelRegistry_management->getMenuTools());
            $list = $this->ModelItem->getListRecall();								//Получаем лист отзывов.
            $this->ViewlItem->ContentItemRecall = $this->ViewlItem->getTable($list);//Показываем таблицу отзывов.
            $this->ViewlItem->display($cachable, $urlparams);
        } 
        return $this; 
    }
    
    
     
    
    //Метод обработки действий пользователя Records
    private function ActionRecall(){
    	//Логирование действия. 
        Tinterface::LogEvents($this->LogErrors, 'REVIEWS:', 'Feedback and suggestions received from users by the registry');
    }
    
    
    
}