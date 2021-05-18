<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;

// Подключаем библиотеку представления Joomla.
jimport('joomla.application.component.view');
// Подключаем библиотеку меню Joomla. 
jimport('joomla.html.html');  
/**
 * HTML представление сообщения компонента medical_registry.
 */
class medical_registryViewmedical_registry extends JViewLegacy
{
	/**
	 * Сообщение.
	 *
	 * @var  string
	 */
    protected $HeadItem;
    public $MenuItem;

	/**
	 * Переопределяем метод display класса JViewLegacy.
	 *
	 * @param   string  $tpl  Имя файла шаблона.
	 *
	 * @return  void
	 */
    
    public function display($tpl = null){
        //Полуаем текущую URL.
        $url = & JFactory::getURI();
		//Начальние установки свойств.
        $this->HeadItem = JText::_('COM_MEDICAL_REGISTRY'); 
		parent::display($tpl);
	}
}
