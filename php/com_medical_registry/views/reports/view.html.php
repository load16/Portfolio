<?php
  // Запрет прямого доступа.
defined('_JEXEC') or die;

// Подключаем библиотеку представления Joomla.
jimport('joomla.application.component.view');
jimport('joomla.form.form');  
jimport('joomla.html.html');
//Подлючение библиотеки для элементов интерфейса.
require_once (JPATH_COMPONENT.DS.'libraries'.DS.'interface.php');
require_once (JPATH_COMPONENT.DS.'classes'.DS.'rights.php');
//require_once (JPATH_ADMINISTRATOR.DS.'includes'.DS.'toolbar.php');
   
 
/**
 * HTML представление сообщения компонента Registry_Management.
 */
class medical_registryViewReports extends JViewLegacy{
    /**
     * Сообщение.
     *
     * @var  string
     */
     protected $HeadItemReports;            	//Заглавие.
	 public $ToolsItemReports;                	//Панель инструментов. 
	 public $ContentItemReports;               	//Контент.
	 public $ViewItemReports;                 	//Просмотр результата.
	 public $ViewItemDebugging;                 //Просмотр отладочного результата. 
    
    

    /**
     * Переопределяем метод display класса JViewLegacy.
     *
     * @param   string  $tpl  Имя файла шаблона.
     *
     * @return  void
     */
    
    public function display($tpl = null){
        //Начальние установки свойств.
        $this->HeadItemReports = JText::_('REG_SEARCH_SYSTEM');
        parent::display($tpl);
    }
    
    
    
    //Метод подготовки контента.
    /* $arr_query - массив со поисковыми значениями.
    *  $arr_data - массив с данніми для отображения.
    * $model - ссылка на боъект модели.
    */
    public function getContent($arr_query = null, $arr_data = null, $model){
		if(
		count($arr_query) >= 2
		){
			foreach($arr_query as $k => $v){												//Цикл обхода массива перменных.
				$vv = $v;
				foreach($vv as $k_v => $v_v){
					//Штатное формирование поля.
					$pole = $k_v .' - '.'<input type="text" name="'.$v_v['pole'].'"  title="1" value="'.$v_v['value'].'">';
					$typePole = $model->getTypePole($v_v['pole']);							//Получаем тип поля.
					if($v_v['pole'] == 'ru_specialty'){										//Показываеи список специальностей.
						$pole = $k_v .' - '.$model->getDataSelect('registry_specialty', $v_v['pole'], $v_v['pole'], $v_v['value']);
					}
					if($v_v['pole'] == 'type'){												//Показываем список типов записей.
						$pole = $k_v .' - '.$model->getDataSelect('registry_type', $v_v['pole'], $v_v['pole'], $v_v['value']);
					}
					if($v_v['pole'] == 'surname_login'){									//Показываем список доктор.
						$pole = $k_v .' - '.$model->getDataSelect('registry_login', $v_v['pole'], $v_v['pole'], $v_v['value']);
					}
					if($v_v['pole'] == 'status'){											//Показываем список статусов приема.
						$pole = $k_v .' - '.$model->getDataSelect('registry_status', $v_v['pole'], $v_v['pole'], $v_v['value']);
					}
					
					if($typePole == 'date'){												//Если тип поля дата, то формируем его.
						$pole = $k_v .' - '.$model->getDateSelect($v_v['pole'], $v_v['value']);
					}
					if($typePole == 'time'){												//Если тип поля время, то формируем его.
						$pole = $k_v .' - '.$model->getTimeSelect($v_v['pole'], $v_v['value']);
					}
					
					$tabs[$k] .= $pole;
					$tabs[$k] .= '<br/>'."\n";
				}
				unset($k_v, $v_v);
	        }
	        unset($k, $v, $arr);
	        $ret = '<div class="inputbox">'.'<input type="submit" class="button" name="Search_report"  value="'.JText::_('REG_SEARCH').'" title="'.JText::_('REG_THE_RESULT_OF_THE_QUERY').'">'.'</div>';
	        return Tinterface::getUniFormed($ret.Tinterface::getTabs($tabs, 'search_tabs'));
		}
    }
    

    
    

    
}

