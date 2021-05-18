<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
/**
 * Model Report
 * @author Олег Борисович Дубик
 * load16@rambler.ru
 */
 
 /**
  * Модель задачи "Recall" просмотра отзывов.
  */ 
 
     
// Подключаем библиотеку modelitem Joomla.
//jimport('joomla.application.component.modegetInstance');
jimport('joomla.application.component.modelitem'); 
jimport('joomla.database.table'); 
//jimport('joomla.form.form'); 
require_once (JPATH_COMPONENT.DS.'libraries'.DS.'interface.php');
require_once (JPATH_COMPONENT.DS.'libraries'.DS.'data.php'); 
require_once (JPATH_COMPONENT.DS.'models'.DS.'schedule.php');
require_once (JPATH_COMPONENT.DS.'models'.DS.'appointment_doctor.php');       
//jimport( 'joomla.application.component.modelform' );
//jimport('joomla.form.form'); 
 
/**
 * Модель Просмотра отзывов.
 */
class medical_registryModelRecall extends JModelItem{
    
   
    //Метод получения списка отзывов отсортированого убыванию.
    public function getListRecall(){
		$db = & JFactory::getDbo();
        $nameTable1 = $db->nameQuote('#__registry_reviews');
        $query = 'SELECT 
        			*
                    FROM
                    '.$nameTable1.'
                    ORDER BY TIME(`id`) DESC';
        $db->setQuery($query);
        return $db->loadAssocList();
    }
     
     
}