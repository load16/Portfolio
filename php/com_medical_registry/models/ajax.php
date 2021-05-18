<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem'); 


class medical_registryModelAjax extends JModelItem {
	
	//Метод получения данных AJAX запросов.
	public function getAjaxData($data = null){
		$app = JFactory::getApplication();						//Получаем ссылку на объект приложения
		JResponse::setHeader('Content-Type', 'text/plain', TRUE);
		if($data != null){
			echo $data; 										//Возвращаемые данные.
		}
	$app->close();												//Закрываем приложение.	
	}
	
}
?>