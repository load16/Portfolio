<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
/**
 * Model Appointment_doctor
 * @author Олег Борисович Дубик
 */
 /**
  * Модель задачи по умолчанию.
  */
    
// Подключаем библиотеку modelitem Joomla.
jimport('joomla.application.component.modelitem');
jimport('joomla.html.html');  
 
/**
 * Модель для подготоки главного меню.
 */
class medical_registryModelMedical_registry extends JModelItem{
    
    public function getMenuItem(){
        //Начальние установки свойств. Формируем меню.
        $MenuItem = '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/Thing" class="pagenav">'.'<div itemprop="mainEntityOfPage" style="display:none">'.rtrim(JUri::base(), JUri::base(true)).JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=scheduleTask').'</div>'.'<div itemprop="name" style="display:none">'.JText::_('REG_SCHEDULE').'</div>'.JHTML::_('link', JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=scheduleTask'), JText::_('REG_SCHEDULE'), array('title'=>JText::_('REG_SCHEDULE_RECEPTION_ESTABLISHMENTS'),'class'=>'item-title')).'</li>'.'<br/>';   
        $MenuItem .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/Thing" class="pagenav">'.'<div itemprop="mainEntityOfPage" style="display:none">'.rtrim(JUri::base(), JUri::base(true)).JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Appointment_doctorTask').'</div>'.'<div itemprop="name" style="display:none">'.JText::_('REG_RECORD_ON_RECEPTION_TO_THE_DOCTOR').'</div>'.JHTML::_('link', JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Appointment_doctorTask'), JText::_('REG_RECORD_ON_RECEPTION_TO_THE_DOCTOR'), array('title'=>JText::_('REG_SERVICE_APPOINTMENT_TO_THE_DOCTOR_REMOTELY'),'class'=>'item-title')).'</li>'.'<br/>';
        $MenuItem .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/Thing" class="pagenav">'.'<div itemprop="mainEntityOfPage" style="display:none">'.rtrim(JUri::base(), JUri::base(true)).JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Registry_ManagementTask').'</div>'.'<div itemprop="name" style="display:none">'.JText::_('REG_MANAGING_RECEPTION').'</div>'.JHTML::_('link', JRoute::_('index.php?option=com_medical_registry&view=medical_registry&task=Registry_ManagementTask'), JText::_('REG_MANAGING_RECEPTION'), array('title'=>JText::_('REG_SERVICE_MANAGEMENT_OF_ELECTRONIC_REGISTRY'),'class'=>'item-title'));
        return $MenuItem;
    }
    
}