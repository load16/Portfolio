<?php
  // Запрет прямого доступа.
defined('_JEXEC') or die;


// Подключаем библиотеку представления Joomla.
jimport('joomla.application.component.view');
//Подлючение библиотеки для элементов интерфейса.
require_once (JPATH_COMPONENT.DS.'libraries'.DS.'interface.php');
   
 
/**
 * HTML представление компонента Recall.
 */
class medical_registryViewRecall extends JViewLegacy{
    /**
     * Сообщение.
     *
     * @var  string
     */
     protected $HeadItemRecall;            		//Заглавие.
	 public $ToolsItemRecall;                	//Панель инструментов. 
	 public $ContentItemRecall;               	//Контент.
	 public $ViewItemRecall;                 	//Просмотр результата.
	 public $ViewItemDebugging;                 //Просмотр отладочного результата. 
    
    

    /**
     * Переопределяем метод display класса JViewLegacy.
     *
     * @param   string  $tpl  Имя файла шаблона.
     *
     * @return  void
     */
    
    public function display($tpl = null){
        //Начальные установки свойств.
        $this->HeadItemRecall = JText::_('REG_VIEW_REVIEWS');
        parent::display($tpl);
    }
    
    
    //Метод отображения информации в табличном виде.
    public function getTable($arr = null){
		if($arr != null){
			$arr = $this->getHeadTable($arr);
			$arr = Tinterface::delColumn($arr, '1');
			$table = $this->getTableFormat($arr, 'idTableRecall');
			return $table;
		}
    }
    
    
    //Метод формирования шипки таблици.
    private function getHeadTable($arr = null){
		if($arr != null){
			$n = 1;
			foreach($arr as $key => $value){
				if($n == 1){
					$ret[$n] = array("id" => "ID", "name" => JText::_("REG_NAME"), "proposal" => JText::_("REG_REVIEWS"), "time" => JText::_("REG_TIME"), "data" => JText::_("REG_DATE"), "ip" => "IP");
					$n++;
				}
				$ret[$n] = $value;
				$n++;
			}
			return $ret;
		}
    }
    
    
	//Метод получения таблици форматированной.
    public function getTableFormat($arr, $id_table = 'id_TableFormat'){
		if(count($arr) >= 1){
			$n_l = 1;													//Установка счетчика строки.
			$ret .= '<table  class="display responsive nowrap" style="width:100%" id="'.$id_table.'">'."\n";
			$id = 1;													//Устанока счетчика ИД элемета.
            $class_row0 = 'cat-list-row0';								//Фромируем классы ячеек таблицы.
            $class_row1 = 'cat-list-row1';
            $n = true;													//Установка флага.
			foreach($arr as $k_l => $v_l){
				$n_s = 1;												//Установка счетчика ячейки в строке.
				$line = $v_l;
				if($n){													//Формируем чередующися класс.
					$cl = $class_row0;
					$n = false;
                }
                else{
					$cl = $class_row1;
					$n = true;
                }
                if($n_l == 1){											//Первую строчку выдиляем.
					$cl = 'inputbox';
					$ret .= '<thead>';
                }
                
                
                
				$ret .= "\t".'<tr class="'.$cl.'">'."\n";
				foreach($line as $k_s => $v){
					
					$param['id'] = $id.'cell';
					if($v['class'] != ''){
						$param['class'] = $v['class'];
					}
					else{
						unset($param['class']);
					}
					
					if($n_l == 1 || $n_s == 1){							//Находим первую строку или столбик.
						
						
						
						if($n_l == 1 && $n_s == 1){
							$ret .= "\t"."\t".'<th class="list-title" style="font-size: 150%;" class="item-suburb">'."\n";
						}
						else{
							$ret .= "\t"."\t".'<th class="list-title" style="font-size: 120%;" class="item-suburb">'."\n";
						}
						$ret .= "\t"."\t"."\t".$v."\n";
						$ret .= "\t"."\t".'</th>'."\n";
						
					}
					else{
						$param['style'] = '
						font-size: 120%;
						';
						$ret .= "\t"."\t".'<td style="text-align: left;">'."\n";
						$ret .= "\t"."\t"."\t".Tinterface::getStringSize($v, 6)."\n";
						//$ret .= "\t"."\t"."\t".'<pre>'.$v.'</pre>'."\n";
						$ret .= "\t"."\t".'</td>'."\n";
					}
					$id++;												//Инкремент ИД.
					$n_s++;												//Инкремент счетчика.
				}
				unset($k_s, $v);
				$ret .= "\t".'</tr>'."\n";
				if($n_l == 1){
					$ret .= '</thead><tbody>';
				}
				$n_l++;													//Инкремент счетчика.
			}
			unset($k_l, $v_l);
			
			
			
			$ret .= '</tbody></table>
			<script>
			  $(function(){
			    $("#'.$id_table.'").dataTable({
			    	"scrollX": true,
			    	"bSort" : false,

			    	language: {
				      "processing": "Подождите...",
				      "search": "Поиск:",
				      "lengthMenu": "Показать _MENU_ записей",
				      "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
				      "infoEmpty": "Записи с 0 до 0 из 0 записей",
				      "infoFiltered": "(отфильтровано из _MAX_ записей)",
				      "infoPostFix": "",
				      "loadingRecords": "Загрузка записей...",
				      "zeroRecords": "Записи отсутствуют.",
				      "emptyTable": "В таблице отсутствуют данные",
				      "paginate": {
				        "first": "Первая",
				        "previous": "Предыдущая",
				        "next": "Следующая",
				        "last": "Последняя"
				      },
				      "aria": {
				        "sortAscending": ": активировать для сортировки столбца по возрастанию",
				        "sortDescending": ": активировать для сортировки столбца по убыванию"
				      }
				  }
			    });
			  })
			</script>	
			'."\n";
			
			return $ret;
		}
    } 

		
    

    
}

