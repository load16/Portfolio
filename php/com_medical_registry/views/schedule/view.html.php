<?php
  // Запрет прямого доступа.
defined('_JEXEC') or die;

// Подключаем библиотеку представления Joomla.
jimport('joomla.application.component.view');
jimport('joomla.html.html');
 
/**
 * HTML представление сообщения компонента medical_registry.
 */
class medical_registryViewSchedule extends JViewLegacy{
    /**
     * Сообщение.
     *
     * @var  string
     */
    protected $HeadItemSchedule;            //Заглавие разписания.
    public $DescriptionItemSchedule;        //Описание задачи.
    public $ToolsItemSchedule;              //Панель инструментов. 
    public $MenuItemSchedule;               //Меню для выбора.
    public $ViewItemSchedule;               //Просмотр результата.
    
    public $SelectSpecialty;                //Ссылки для выбора специальности.
    public $SelectFIO;                      //Ссылки для выбора фамилии врача.
    public $SelectAction;                   //Выбод действия.
    public $Debug;                          //Просмотр отладочной информации.
    

    /**
     * Переопределяем метод display класса JViewLegacy.
     *
     * @param   string  $tpl  Имя файла шаблона.
     *
     * @return  void
     */
    
    
    public function display($tpl = null){
        //Начальние установки свойств.
        $this->HeadItemSchedule = JText::_('REG_SCHEDULE');
        // Получаем сообщение из модели.        
        // Отображаем представление.
        parent::display($tpl);
    }
    
    
    //Метод получения меню специальностей докторов в виде ссылок.
    public function getMenuSpecialty($arr = null, $url = null){
        if($url == null){
			$url = & JFactory::getURI();
    	}
        if(count($arr) >= 1){
            $MenuItem .= JText::_('REG_SELECT_DOCTOR').'<br/>';
            $MenuItem .= '<div style="display: inline-block; overflow-y: auto; border: 1px solid #C1C1C1; width:200px; height:70px;">';
            foreach($arr as $key => $value){
                //Готовим ссылки для меню.
                $MenuItem .= JHTML::_('link', JRoute::_($url.'&specialty='.$value['id_specialty_login']), JText::_($value['ru_specialty']), array('title'=>JText::_($value['ru_specialty']),'class'=>'MEDICAL_REGISTRY_MainMenuButton1')).'<br/>';    
            }
            $MenuItem .= '</div>'; 
            return $MenuItem; 
        }
    }
    
    
    //Метод получения меню докторов в виде ссылок. 
    public function getMenuDoctor($arr = null, $url = null){
    	if($url == null){
			$url = & JFactory::getURI();
    	}
        $Session = &JFactory::getSession();
        $Schedule = $Session->get('Medical_Registry_Schedule');
        if(count($arr) >= 1){
            $MenuItem .= JText::_('REG_SELECTION_NAME').'<br/>';
            $MenuItem .= '<div style="display: inline-block; overflow-y: auto; border: 1px solid #C1C1C1; width:200px; height:70px;">'; 
            foreach($arr as $key => $value){
                //Готовим ссылку.
                $aa = JRoute::_($url.'&doctor='.$value['id_login']);
                $MenuItem .= JHTML::_('link', $aa, $value['surname_login'].' '.$value['name_login'].' '.$value['patronymic_login'], array('title'=>$value['surname_login'].' '.$value['name_login'].' '.$value['patronymic_login'],'class'=>'MEDICAL_REGISTRY_MainMenuButton1')).'<br/>';     
            }
            $MenuItem .= '</div>';
            return $MenuItem; 
        }
    }
    
    
    //Метод для получения формы.
    public function getForm($form = null){
        if($form != null){
            $url = & JFactory::getURI();
            $ret .= '<form action="'.$url.'" method="post" class="form-validate">'."\n";
            $ret .= "\t".$form."\n";  
            $ret .= "\t".JHTML::_('form.token')."\n";                    // Вставляем ТОКЕН скрытое поле в форму.
            $ret .= '</form>'."\n";
            return $ret; 
        }
    }
    
    
    //Метод определения наличия строки в коде.
    public function PresenceLineCode($text = null, $code = null){
        return Tinterface::PresenceLineCode($text, $code);
    }

    
}

