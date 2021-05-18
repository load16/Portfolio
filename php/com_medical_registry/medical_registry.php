<?php

// Запрет прямого доступа.
defined('_JEXEC') or die;
/**
 * Component tst
 * @author Олег Борисович Дубик
 */
 

//Подключаем скрипты javascript
JHTML::stylesheet('medical_registry.css', 'components/com_medical_registry/assets/styles/');
JHTML::script('print.js','components/com_medical_registry/assets/scripts/');
JHTML::script('jquery-3.1.1.min.js', 'components/com_medical_registry/assets/scripts/');
JHTML::script('scroll_doc.js', 'components/com_medical_registry/assets/scripts/');
JHTML::script('ajax.js', 'components/com_medical_registry/assets/scripts/');

//Модальное окно. Перенесено в библиотеку.
//JHTML::script('jquery.arcticmodal-0.3.min.js', 'components/com_medical_registry/assets/scripts/');
//JHTML::stylesheet('jquery.arcticmodal-0.3.css', 'components/com_medical_registry/assets/styles/');
//JHTML::stylesheet('simple.css', 'components/com_medical_registry/assets/styles/themes/');

//Таймер. Перенесен в библиотеку.
//JHTML::stylesheet('dscountdown.css', 'components/com_medical_registry/assets/styles/');
//JHTML::script('dscountdown.js', 'components/com_medical_registry/assets/scripts/');

//Выбор времени. Перенесено в библиотеку.
//JHTML::stylesheet('jquery-clockpicker.min.css', 'components/com_medical_registry/assets/styles/');
//JHTML::stylesheet('clockpicker.css', 'components/com_medical_registry/assets/styles/');
//JHTML::script('jquery-clockpicker.min.js', 'components/com_medical_registry/assets/scripts/');

//Работа с таблицами перенесено в библиотеку.
//JHTML::script('jquery.dataTables.min.js', 'components/com_medical_registry/assets/scripts/');
//JHTML::script('dataTables.responsive.js', 'components/com_medical_registry/assets/scripts/');
//JHTML::stylesheet('responsive.css', 'components/com_medical_registry/assets/styles/');

//Работа с датой. Перенесено в библиотеку.
//JHTML::script('jquery.datetimepicker.full.min.js', 'components/com_medical_registry/assets/scripts/');
//JHTML::stylesheet('jquery.datetimepicker.min.css', 'components/com_medical_registry/assets/styles/');

//JHTML::script('dataTables.fixedColumns.js', 'components/com_medical_registry/assets/scripts/');
//JHTML::stylesheet('fixedColumns.dataTables.css', 'components/com_medical_registry/assets/styles/');


$language = JFactory::getLanguage();
$teg = $language->getTag();								//Получаем языковый тег.




 // Подключаем библиотеку контроллера Joomla.
jimport('joomla.application.component.controller');

$task = JRequest::getCmd('task');										//Получаем имя задачи.
$file = mb_strtolower($task);
$arr_file = explode('task', $file);
$file = $arr_file['0'];													//Получаем имя файла.

$TaskManagement = JRequest::getCmd('TaskManagement');
$file1 = mb_strtolower($TaskManagement);
$arr_file1 = explode('TaskManagement', $file1);
$file1 = $arr_file1['0'];	


 
if($TaskManagement != ''){
	require_once (JPATH_COMPONENT.DS.'controller.php');
	JRequest::setVar('task', $file1.'.'.$task);							//Формируем соответствующею задачу.
}
else{
	if($task != ''){
		require_once (JPATH_COMPONENT.DS.'controller.php'); 
		JRequest::setVar('task', $file.'.'.$task);						//Формируем соответствующею задачу.
	}
}


/*
if($task != ''){
	require_once (JPATH_COMPONENT.DS.'controller.php'); 
	JRequest::setVar('task', $file.'.'.$task);						//Формируем соответствующею задачу.
}
*/

//print $file1;

// Получаем экземпляр контроллера с префиксом medical_registry
$controller = &JControllerLegacy::getInstance('Medical_Registry');
// Исполняем задачу task из Запроса.
$controller->execute(JFactory::getApplication()->input->get('task', 'display'));
// Перенаправляем, если перенаправление установлено в контроллере.
$controller->redirect();