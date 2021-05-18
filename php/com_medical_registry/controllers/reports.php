<?php
/**
 * Default Controller
 * @author Олег Борисович Дубик
 */
/**
  * Контроллер сервиса "Электронная регистратура" для задачи "Reports" .
  */
  

// Запрет прямого доступа.
defined( '_JEXEC' ) or die;  
  

class Medical_RegistryControllerReports extends Medical_RegistryController{

   
    
    //Задача управления регистратурой.
    function ReportsTask($cachable = false, $urlparams = array()){
        $this->setParameters('Reports');   
        if(JRequest::getVar('logout') != ''){                                       //Обработка комманды logout.
            Aut::logout();
        }
              
        if(Aut::login()){                                                      		//Проверка аутентифиации пользователя.
            $this->ActionReports();
            $ModelAppointment = &$this->getModel('Appointment_doctor');
            $ModelRegistry_management = &$this->getModel('Registry_management');
            $Report = $this->SessionItem->get('Medical_Registry_Reports');
            //Панель инструментов.
            $this->ViewlItem->ToolsItemReports = Tinterface::getUniFormed($ModelRegistry_management->getMenuTools()).Tinterface::getUniFormed($this->ModelItem->getToolsQuery($Report['Type'], $Report['Query']));
            
            
            if($this->ModelItem->determineOption($Report['Query'])){
				//$this->ViewlItem->ContentItemReports = 'Content';
				$this->ViewlItem->ContentItemReports = $this->ViewlItem->getContent($Report['Query'], '', $this->ModelItem);
            }
            if($this->ViewlItem->ViewItemReports != ''){							//Если есть отчет, то готовим его к печати.
				//Готовим отчет к печати.
				$this->ViewlItem->ViewItemReports = Tinterface::getDivPrint($this->ModelItem->getInfo($Report['Query']).$this->ViewlItem->ViewItemReports, 'PrintReport');
				//Показываем кнопку ПЕЧАТЬ.
				$this->ViewlItem->ToolsItemReports = Tinterface::getUniFormed($ModelRegistry_management->getMenuTools().$ModelAppointment->getButtonPrint('PrintReport')).Tinterface::getUniFormed($this->ModelItem->getToolsQuery($Report['Type'], $Report['Query']));
            }
            $this->ViewlItem->ViewItemDebugging = $Report['Query'];
            $this->SessionItem->set('Medical_Registry_Reports', $Report);			//Фиксация переменной. 
            $this->ViewlItem->display($cachable, $urlparams);
        } 
        return $this; 
    }
    
    
     
    
    //Метод обработки действий пользователя Records
    private function ActionReports(){
        $Report = $this->SessionItem->get('Medical_Registry_Reports');                 //Инициализация переменных. 
        $dd = new data();
        $ModelAppointment = &$this->getModel('Appointment_doctor');
        $Select_poles = JRequest::getVar('Select_poles');
        $Reset_search = JRequest::getVar('Reset_search');
        $Search_report = JRequest::getVar('Search_report');
        

        if($Reset_search != ''){														//Обработка сброса настроек запроса.
			unset($Report['Query'], $Report['Table'], $Report['Serach']);
        }
        /*
        if($Search_report != ''){														//Обработка поиска.
			if($this->ModelItem->determineOption($Report['Query'])){					//Если есть выбранные поля, то получаем поисковый параметр 1-вого уровня.
				$serach_param_one = $this->ModelItem->DeterminingFirstLevelSearchParameter($Report['Query']);
			}
		}
		*/
        
        if($Report['Query'] == ''){														//Если массив запросов пуст, то наполняем его.
			$Report['Query'] = $this->ModelItem->getArrQurey();
        }
        $arr = $Report['Query'];														//Создаем массив для цикла.
        foreach($arr as $k => $v){														//Цикл обхода массива перменных.
			$vv = $v;
			foreach($vv as $k_v => $v_v){
				if(																		//Обработка поиска.
				$Search_report != ''													//Если нажато поиска.
				&& JRequest::getVar($v_v['pole']) != 'not'								//Если выбранно в списках.
				){
					$arr[$k][$k_v]['value'] = JRequest::getVar($v_v['pole']);			//Сохраняем данные запроса в масив.
					$log[$k_v] = $arr[$k][$k_v]['value'];								//Формируем данные для логирования.
					$typePole = $this->ModelItem->getTypePole($v_v['pole']);			//Получаем тип поля.
					$from = JRequest::getVar('from'.$v_v['pole']);						//Формируе поле даты.
					$to = JRequest::getVar('to'.$v_v['pole']);
					if(																	//Обработка полей даты
					($typePole == 'date' || $typePole == 'time')
					&& $from != ''
					){
						
						if(
						$dd->comparison_date($from, $to)
						|| $to == ''
						){																//Валидация дат.
							$arr[$k][$k_v]['value'] = $from;							//Формируем начальнй предел.
							if($typePole == 'time'){									//Если тип поля TIME то исправляем.
								$arr[$k][$k_v]['value'] .= ':00';
							}
							if($to != ''){												//Если есть конечний предел, то 
								$arr[$k][$k_v]['value'] .= '$$'.$to;					//формируем его.
								if($typePole == 'time'){								//Если тип поля TIME то исправляем.
									$arr[$k][$k_v]['value'] .= ':00';
								}
							}
						}
						else{															//Иначе формируем сообщение.
							//Выводим сообщение
							$this->AppItem->enqueueMessage(JText::_('REG_THE_START_DATE_OR_TIME_CANNOT_BE_OLDER_THAN_END_DATE_OR_TIME').'!');
							//Логирование действия. 
                     		Tinterface::LogEvents($this->LogErrors, 'VALIDATION DATE TIME:', 'Start date cannot be older than the end!', $_POST);
						}
					}
					
				}
				
				if($Select_poles != ''){												//Обработка выбора полей.
					if(JRequest::getVar($v_v['pole']) == 'on'){
						$arr[$k][$k_v]['selected'] = true;
					}
					else{
						$arr[$k][$k_v]['selected'] = false;
					}
				}
			}
			unset($k_v, $v_v);
        }
        $Report['Query'] = $arr;														//Фиксируем изменения.
        unset($k, $v, $arr);
        
        
        
        if(																				//Обрабока поиска.
        $Search_report != ''
        && $this->ModelItem->determineOption($Report['Query'])							//Проверка на предмет наличия поисковый нистроек.
        ){				
			$Report['Serach'] = $this->ModelItem->getRecordParameters($Report['Query']);
			//Формируем поля таблыцы.
			$Report['Serach'] = $this->ModelItem->delPoleTable($Report['Serach'], $Report['Query']);
			//Формируем таблицу.
			$Report['Table'] = $this->ModelItem->getTableSerach($Report['Serach']);
			//Логирование действия. 
			Tinterface::LogEvents($this->LogInfo, 'SEARCH:', 'Search for recorded patients.', $log);
		}
        
        if($Report['Table'] != ''){														//Если есть сформированная таблица. то показываем ее.
			$this->ViewlItem->ViewItemReports = $Report['Table'];
        }
        $this->SessionItem->set('Medical_Registry_Reports', $Report);                  //Фиксация переменной. 
        //$this->Ajax($data, 'Medical_Registry_Records', 'REG_ALL_DOCTORS'); 
    }
    
    
    
}