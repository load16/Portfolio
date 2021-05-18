<?php
  // Запрет прямого доступа.
defined('_JEXEC') or die;

// Подключаем библиотеку представления Joomla.
jimport('joomla.application.component.view');
// Подключаем библиотеку меню Joomla. 
jimport('joomla.html.html');
jimport('joomla.form.form'); 
require_once (JPATH_COMPONENT.DS.'libraries'.DS.'interface.php');  
/**
 * HTML представление сообщения компонента Appointment.
 */
class medical_registryViewAppointment_doctor extends JViewLegacy{
    /**
     * Сообщение.
     *
     * @var  string
     */
     public $HeadItemAppointment;
     public $DescriptionItemAppointment;
     public $ToolsItemAppointment;
     public $MenuItemAppointment;
     public $ViewItemAppointment;
     
     //public $ModelItem;                                         //Сылка на объект модели.
     
     

    /**
     * Переопределяем метод display класса JViewLegacy.
     *
     * @param   string  $tpl  Имя файла шаблона.
     *
     * @return  void
     */
    
    public function display($tpl = null){
        //Начальние установки свойств.
        
        // Получаем сообщение из модели.        
        // Отображаем представление.
        parent::display($tpl);
    }
    
    
    
    //Метод для получения формы.
    public function getForm($form = null){
        if($form != null){
            $url = & JFactory::getURI();  
            $ret .= '<form action="'.$url.'" method="post" class="form-validate">'."\n";
            $ret .= $form."\n";  
            $ret .= JHTML::_('form.token')."\n";                    // Вставляем ТОКЕН скрытое поле в форму.
            $ret .= '</form>'."\n";
            return $ret; 
        }
    }
    
    
    //Метод для получения формы.
    public function getFormXml($form = null, $id = null, $timer = null){
        return Tinterface::getForm($form, $id, $timer);
    }
    
    //Метод постройки элементов в рад.
    public function getElementsLine($arr = null){
        return Tinterface::getElementsLine($arr);
    }
    
    //Метод постройки элементов в столбик.
    public function getElementsColumn($arr = null){
        return Tinterface::getElementsColumn($arr);
    }
    
    
    //Метод обрамления элементов как панель инструментов.
    public function getToolbar($var = null){
        if($var != null){
            $ret .= "\t".'<div class="inputbox">'."\n";
            $ret .= $var;   
            $ret .= "\t".'</div>'."\n";
            return $ret;
        }
    }
    
    
    //Метод получения вкладок.
    public function getTabs($arr = null, $id = null){
        return Tinterface::getTabs($arr, $id);
    }
    
    
    //Метод приготовки блока к печати.
    public function getDivPrint($content = null, $id = null){
        return Tinterface::getDivPrint($content, $id);
    }
    
    
    //Метод получения таблицы выбора времени приема.
    public function getTableFixedBlockSize($arr){
		return Tinterface::getTableFormat($arr);
    }
}

