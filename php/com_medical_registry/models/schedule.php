<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
/**
 * Model Scheduke
 * @author Олег Борисович Дубик
 */ 
 
 /**
  * Модель задачи просмотра расписания.
  */ 
     
// Подключаем библиотеку modelitem Joomla.
jimport('joomla.application.component.modelitem');   

require_once (JPATH_COMPONENT.DS.'libraries'.DS.'data.php'); 
require_once (JPATH_COMPONENT.DS.'libraries'.DS.'calendar.url.php');  
 
/**
 * Модель расписание приема.
 */
class medical_registryModelSchedule extends JModelItem{
    
    
    
    
    //Получить списак специальностей если есть на дату у кого есть расписание.
    public function getListSpecialtyInDate($date = null){
        $db = & JFactory::getDbo();
        $nameTable1 = $db->nameQuote('#__registry_login');
        $nameTable2 = $db->nameQuote('#__registry_specialty');
        $nameTable3 = $db->nameQuote('#__registry_schedule');  
        if($date == null){
            $d = new data();  
            $date = $d->data_i;
        }
         $query = 'SELECT DISTINCT
                        '.$nameTable2.'.ru_specialty,
                        '.$nameTable1.'.id_specialty_login
                        FROM
                        '.$nameTable1.'
                        Inner Join '.$nameTable2.' ON '.$nameTable1.'.id_specialty_login = '.$nameTable2.'.id_specialty
                        Inner Join '.$nameTable3.' ON '.$nameTable3.'.id_login_schedule = '.$nameTable1.'.id_login
                        WHERE
                        '.$nameTable3.'.date_schedule = \''.$date.'\' AND
                        '.$nameTable1.'.id_activation_login = 1';
        $db->setQuery($query);
        return $db->loadAssocList();
    }
    
    //Получить список врачей на ИД специальности у кого есть расписание на дату.
    public function getListDoctorsInDate($id_Specialty = null, $date = null){
        if($id_Specialty != null){
            $db = & JFactory::getDbo();
            $nameTable1 = $db->nameQuote('#__registry_login');
            $nameTable2 = $db->nameQuote('#__registry_specialty');
            $nameTable3 = $db->nameQuote('#__registry_schedule');  
            if($date == null){
                $d = new data();  
                $date = $d->data_i;
            }
             $query = 'SELECT DISTINCT
                            '.$nameTable1.'.id_login,
                            '.$nameTable1.'.surname_login,
                            '.$nameTable1.'.name_login,
                            '.$nameTable1.'.patronymic_login,
                            '.$nameTable2.'.ru_specialty,
                            '.$nameTable1.'.id_specialty_login
                            FROM
                            '.$nameTable1.'
                            Inner Join '.$nameTable2.' ON '.$nameTable1.'.id_specialty_login = '.$nameTable2.'.id_specialty
                            Inner Join '.$nameTable3.' ON '.$nameTable3.'.id_login_schedule = '.$nameTable1.'.id_login
                            WHERE
                            '.$nameTable3.'.date_schedule = \''.$date.'\' AND
                            '.$nameTable1.'.id_specialty_login = '.$id_Specialty.' AND
                            '.$nameTable1.'.id_activation_login = 1';
            $db->setQuery($query);
            return $db->loadAssocList();
        }
            
    }
    
    
    
    
    //Получить список специальностей врачей.
    public function getListSpecialtyDoctors(){
        $db = & JFactory::getDbo(); 
        $nameTable1 = $db->nameQuote('#__registry_specialty');
        $nameTable2 = $db->nameQuote('#__registry_login');  
        $query = 'SELECT DISTINCT
                    '.$nameTable1.'.ru_specialty,
                    '.$nameTable1.'.id_specialty
                    FROM
                    '.$nameTable2.'
                    Inner Join '.$nameTable1.' ON '.$nameTable2.'.id_specialty_login = '.$nameTable1.'.id_specialty
                    WHERE
                    '.$nameTable2.'.id_activation_login = 1';          
        $db->setQuery($query);
        return $db->loadAssocList();
    }
    
    //Получить список врачей на ИД специальности.
    public function getListDoctors($IdSpecialty){
        if($IdSpecialty != ''){
            $db = & JFactory::getDbo();
            $nameTable1 = $db->nameQuote('#__registry_specialty');
            $nameTable2 = $db->nameQuote('#__registry_login');   
            $query = 'SELECT
                        '.$nameTable2.'.name_login,
                        '.$nameTable2.'.patronymic_login,
                        '.$nameTable2.'.surname_login,
                        '.$nameTable2.'.id_login,
                        '.$nameTable1.'.ru_specialty
                        FROM
                        '.$nameTable2.'
                        Inner Join '.$nameTable1.' ON '.$nameTable2.'.id_specialty_login = '.$nameTable1.'.id_specialty
                        WHERE
                        '.$nameTable2.'.id_specialty_login = '.$IdSpecialty.' AND
                        '.$nameTable2.'.id_activation_login = 1';          
            $db->setQuery($query);
            return $db->loadAssocList();
        }  
    }
    
    //Получить расписание на дату по ИД пользователя если есть.
    public function getDataSchedule($data = null, $id_login = null, $admin = false){
        if($data != ''){
            $db = & JFactory::getDbo();
            $nameTable1 = $db->nameQuote('#__registry_login');
            $nameTable2 = $db->nameQuote('#__registry_schedule');
            $nameTable3 = $db->nameQuote('#__registry_specialty');
            $nameTable4 = $db->nameQuote('#__registry_sex'); 
            $nameTable5 = $db->nameQuote('#__registry_role'); 
            $nameTable6 = $db->nameQuote('#__registry_activation'); 
            
            $query1 = 'SELECT
                        '.$nameTable1.'.name_login,
                        '.$nameTable1.'.patronymic_login,
                        '.$nameTable1.'.surname_login,
                        '.$nameTable3.'.ru_specialty,
                        '.$nameTable1.'.id_activation_login,
                        '.$nameTable1.'.id_role_login,
                        '.$nameTable1.'.id_specialty_login,
                        '.$nameTable1.'.id_sex_login,
                        '.$nameTable4.'.sex_ru,
                        '.$nameTable5.'.name_role,
                        '.$nameTable2.'.date_schedule,
                        '.$nameTable2.'.with_schedule,
                        '.$nameTable2.'.to_schedule,
                        '.$nameTable2.'.cabinet_schedule,
                        '.$nameTable6.'.name_activation,
                        '.$nameTable1.'.id_login,
                        '.$nameTable1.'.time_login,
                        '.$nameTable2.'.hidden_flag_schedule
                        FROM
                        '.$nameTable6.'
                        Inner Join '.$nameTable1.' ON '.$nameTable1.'.id_activation_login = '.$nameTable6.'.id_activation
                        Inner Join '.$nameTable5.' ON '.$nameTable5.'.id_role = '.$nameTable1.'.id_role_login
                        Inner Join '.$nameTable3.' ON '.$nameTable3.'.id_specialty = '.$nameTable1.'.id_specialty_login
                        Inner Join '.$nameTable4.' ON '.$nameTable4.'.id_sex = '.$nameTable1.'.id_sex_login
                        Inner Join '.$nameTable2.' ON '.$nameTable2.'.id_login_schedule = '.$nameTable1.'.id_login
                        WHERE
                        '.$nameTable2.'.date_schedule = \''.$data.'\' AND
                        ';
                        if($id_login != null){
							$query1 .= $nameTable1.'.id_login = \''.$id_login.'\' AND
						';
                        }
                        
                        if($admin == false){
							$query1 .= $nameTable2.'.hidden_flag_schedule = \'0\' AND
						';
                        }
                        
                        $query1 .= $nameTable1.'.id_activation_login = 1
                        ORDER BY TIME(`with_schedule`) ASC';
             
            $db->setQuery($query1);
            return $db->loadAssocList();
        }
    }
    
    
    //Метод получения списка расписаний по дате и по фамилии.
    public function getListIdData($Id = null, $data = null){
        if($Id != null && $data != null){
            $db = & JFactory::getDbo();
            $nameTable1 = $db->nameQuote('#__registry_login');
            $nameTable2 = $db->nameQuote('#__registry_schedule');
            $query = 'SELECT
                    '.$nameTable1.'.name_login,
                    '.$nameTable1.'.patronymic_login,
                    '.$nameTable1.'.surname_login,
                    '.$nameTable1.'.id_activation_login,
                    '.$nameTable1.'.id_role_login,
                    '.$nameTable1.'.id_specialty_login,
                    '.$nameTable1.'.id_sex_login,
                    '.$nameTable2.'.date_schedule,
                    '.$nameTable2.'.with_schedule,
                    '.$nameTable2.'.to_schedule,
                    '.$nameTable2.'.cabinet_schedule
                    FROM
                    '.$nameTable1.'
                    Inner Join '.$nameTable2.' ON '.$nameTable2.'.id_login_schedule = '.$nameTable1.'.id_login
                    WHERE
                    '.$nameTable1.'.id_login = '.$Id.' AND
                    '.$nameTable2.'.date_schedule = \''.$data.'\' AND
                    '.$nameTable1.'.id_activation_login = 1
                    ORDER BY '.$nameTable2.'.with_schedule ASC';
            $db->setQuery($query);
            return $db->loadAssocList(); 
        }
    }
    
    
    //Метод получения текущей даты.
    public function getCurrentDate(){
        $d = new data();
        return $d->data_i;
    }
    
    
    //Метод получения массива дат на неделю по дате.
    public function getArrayDate($date = null){
        $d = new data();
        return $d->getArray_Week($date); 
    }
    
    
    //Метод получения таблици расписания на неделю.
    public function getTableWeekSchedule($data = null, $id_specialty = null, $id_login = null, $admin = false){
        $ArrayDate = $this->getArrayDate($data);
        if($ArrayDate != ''){
            foreach($ArrayDate as $key => $value){
                //$ret .= $this->getTableSchedule('<h2>'.JText::_('REG_SCHEDULE_FOR_THE').' '.'<b>'.$value.'</b>'.'</h2>', $value, $id_specialty, $id_login, $admin);
                $ret .= $this->getTableSchedule(JText::_('REG_SCHEDULE_FOR_THE').' '.'<b>'.$value.'</b>', $value, $id_specialty, $id_login, $admin);
            }
            unset($key, $value);
            return $ret;
        }
    }
    
    
    //Метод получения списка фамилий по специальности и дате.
    public function getListFio($specialty = null, $data = null){
        if($data != null && $specialty != null){
            $db = & JFactory::getDbo();
            $nameTable1 = $db->nameQuote('#__registry_login');
            $nameTable2 = $db->nameQuote('#__registry_schedule');
            $query = 'SELECT DISTINCT
                    '.$nameTable1.'.surname_login,
                    '.$nameTable1.'.name_login,
                    '.$nameTable1.'.patronymic_login, 
                    '.$nameTable1.'.id_login
                    FROM
                    '.$nameTable1.'
                    Inner Join '.$nameTable2.' ON '.$nameTable2.'.id_login_schedule = '.$nameTable1.'.id_login
                    WHERE
                    '.$nameTable1.'.id_specialty_login = '.$specialty.' AND 
                    '.$nameTable2.'.date_schedule = \''.$data.'\' AND
                    '.$nameTable1.'.id_activation_login = 1';
            $db->setQuery($query);
            return $db->loadAssocList(); 
        }
    }
    
    
    //Метод фильтрации массива по ИД специальности.
    public function FilterArraySpecialty($arr = null, $id_specialty = null){
        if(count($arr) >= 1 && $id_specialty != null ){
            foreach($arr as $key => $value){
                if($value['id_specialty_login'] == $id_specialty){
                    $arr_ret[] = $value;
                }
            }
            unset($key, $value);
            return $arr_ret;
        }
    }
    
    
    
    //Метод получения данных для меню панели инструментов.
    public function getMenuTools($specialty = null, $doctor = null ){
        if($specialty != null){
            $ret .= '<input type="button" class="button" onclick="history.back();" title="'.JTEXT::_('REG_RETURN_TO_PREVIOUS_PAGE').'" value="'.JText::_('REG_AGO').'" class="MEDICAL_REGISTRY_ButtonTools"/>'; 
        }
        $ret .= '<input type="submit" class="button" title="'.JText::_('REG_THE_MAIN_SERVICE_MENU_WHICH_YOU_SAW_AT_THE_SERVICE_ENTRANCE_TT').'"  value="'.JText::_('REG_MAIN_MENU').'" name="ManeMenu">'."\n"; 
        $ret .= '<input type="submit" class="button" title="'.JText::_('REG_GET_THE_SCHEDULE_FOR_TODAY_TT').'" value="'.JText::_('REG_SCHEDULE_FOR_TODAY').'" name="TodaySchedule">'."\n"; 
        $ret .= '<input type="submit" class="button" title="'.JText::_('REG_GET_THE_SCHEDULE_FOR_THE_SELECTED_DATE_TT').'" value="'.JText::_('REG_THE_SCHEDULE_FOR_THE_DATES').'" name="ToDateSchedule">'."\n";
        $ret .= '<input type="submit" class="button" title="'.JText::_('REG_GET_THE_SCHEDULE_OF_ALL_THE_DOCTORS_IN_THE_WEEK_TT').'" value="'.JText::_('REG_THE_SCHEDULE_FOR_THE_WEEK').'" name="ScheduleWeek">'."\n";    
        return $ret;
    }
    
    

    
    //Метод получения количества разписаний для фромарования полей таблицы.
    public function getQuantitySchedule($arr = null, $specialty = null, $data = null){
        if($arr != null && $specialty != null && $data != null){
            foreach($arr as $key => $value){
                if($value['id_specialty_login'] == $specialty){
                    $arr_f[] = $value['id_specialty_login'];
                }
            }
            unset($key, $value);
        }   return count($arr_f);
    }
    
    
    //Метод получения массива расписаний по фамилии.
    public function getArraySchedule($arr = null, $Id = null){
        if($arr != null && $Id != null){
            foreach($arr as $key => $value){
                if($value['id_login'] == $Id){
                    $ret[] = $value;
                }
            }
            unset($key, $value);
            return $ret; 
        }
    }
    
    //Метод получения фамилий по дате и специальносте.
    public function getArrayFio($arr = null, $specialty = null){
        if($arr != null && $specialty != null){
            foreach($arr as $key => $value){
                if($value['id_specialty_login'] == $specialty){ 
                    $ret[$value['id_login']]['id_specialty_login'] = $value['id_specialty_login'];
                    $ret[$value['id_login']]['surname_login'] = $value['surname_login']; 
                    $ret[$value['id_login']]['name_login'] = $value['name_login'];
                    $ret[$value['id_login']]['patronymic_login'] = $value['patronymic_login'];
                    $ret[$value['id_login']]['id_login'] = $value['id_login']; 
                }
            }
            unset($key, $value);
            return $ret;
        }
    }
    
    
    //Метод получения специальностей по дате.
    public function getArraySpecialty($arr = null, $data = null){
        if($data != null && $arr != null){
            foreach($arr as $key => $value){
                if($value['date_schedule'] == $data){
                    $rr[$value['id_specialty_login']]['id_specialty_login'] = $value['id_specialty_login'];
                    $rr[$value['id_specialty_login']]['ru_specialty'] = JText::_($value['ru_specialty']);
                }   
            }
            unset($key, $value);
            return $rr; 
        }
    }
    
    
    
    //Метод получения таблици расписаний.
    //По дате, специальности и по фамилии.
    public function getTableSchedule($Head = null, $data = null, $id_specialty = null, $id_login = null, $admin = false){
        if($data != null){
            $arr = $this->getDataSchedule($data, $id_login, $admin);
            if($id_specialty != ''){                                           //Если есть ИД специальности, то выполняем фильтрацию.
                $arr = $this->FilterArraySpecialty($arr, $id_specialty);
            }
            if($arr != ''){
                $arr_Specialty = $this->getArraySpecialty($arr, $data);
                if($arr_Specialty != ''){
                	//Фромируем классы ячеек таблицы.
                	$class_row0 = 'cat-list-row0';
                	$class_row1 = 'cat-list-row1';
                	$n = true;													//Установка флага.
                	
                    
                    $json = '{';                                                 //Кстанавливаем разметки.
                    //Формируем шапку таблии.
                    $table .= '<table class="category" style="width: 100%"><thead class="inputbox"><tr><th style="border: 1px solid black;" colspan="5" class="list-title"><h3>'.$Head.'</h3></th></tr>';
                   
                    //Формируем названия столбцов таблици.
                    $table .= '<tr><th style="border: 1px solid black;" class="item-title">'.JText::_('REG_DOCTOR').'</th><th style="border: 1px solid black;" class="item-title">'.JText::_('REG_FULL_NAME').'</th><th style="border: 1px solid black;" class="item-title">'.JText::_('REG_BEGINNING').'</th><th style="border: 1px solid black;" class="item-title">'.JText::_('REG_THE_END').'</th><th style="border: 1px solid black;" class="item-title">'.JText::_('REG_THE_RECEIVE_LOCATION').'</th></tr>';
                    $table .= '</thead><tbody>';
                    foreach($arr_Specialty as $key => $value){
                        if($n){
							$cl = $class_row0;
							$n = false;
                        }
                        else{
							$cl = $class_row1;
							$n = true;
                        }
                        $rowspan = $this->getQuantitySchedule($arr, $value['id_specialty_login'], $data);
                        $table .= '<tr class="'.$cl.'">';
                        if($rowspan >= 2){
                            //$table .= '<td style="border: 1px solid black;" rowspan="'.$rowspan.'">'.'<h3>'.JText::_($value['ru_specialty']).'</h3>'.'</td>'; 
                            $table .= '<td style="border: 1px solid black;" rowspan="'.$rowspan.'">'.JText::_($value['ru_specialty']).'</td>';
                        }
                        else{
                            //$table .= '<td style="border: 1px solid black;">'.'<h3>'.JText::_($value['ru_specialty']).'</h3>'.'</td>';  
                            $table .= '<td style="border: 1px solid black;">'.JText::_($value['ru_specialty']).'</td>'; 
                        }
                          
                            if($value['id_specialty_login'] != ''){
                        	
                            $arr_fio = $this->getArrayFio($arr, $value['id_specialty_login']);       
                            if($arr_fio != ''){
                                $end1 = false;
                                foreach($arr_fio as $k_fio => $_fio){
                                    $arr_sh = $this->getArraySchedule($arr, $_fio['id_login']); 
                                    $rowspan = count($arr_sh);
                                      
        
                                    if($end1){
										$table .= '<tr class="'.$cl.'">';
                                    }
                                    if($rowspan >= 2){
                                        //$table .= '<td style="border: 1px solid black;" rowspan="'.$rowspan.'">'.'<h4>'.$_fio['surname_login'].' ';
                                        $table .= '<td style="border: 1px solid black;" rowspan="'.$rowspan.'">'.$_fio['surname_login'].' ';
                                        $table .= $_fio['name_login'].' '; 
                                        //$table .= $_fio['patronymic_login'].'</h4>'.'</td>';
                                        $table .= $_fio['patronymic_login'].'</td>';
                                    }
                                    else{
                                        //$table .= '<td style="border: 1px solid black;">'.'<h4>'.$_fio['surname_login'].' ';
                                        $table .= '<td style="border: 1px solid black;">'.$_fio['surname_login'].' ';
                                        $table .= $_fio['name_login'].' '; 
                                        //$table .= $_fio['patronymic_login'].'</h4>'.'</td>';
                                        $table .= $_fio['patronymic_login'].'</td>';
                                    }
                                    
                                    $end1 = true;
                                    if($arr_sh != ''){
                                    	$end2 = false;
                                        foreach($arr as $k_a => $v_a){
                                            if($_fio['id_login'] == $v_a['id_login']){
                                            	if($end2){
													$table .= '<tr class="'.$cl.'">';
                                            	}
                                                //Формируем данные JSON.
                                                $json = '
{
"@context": "http://schema.org",
"@type": "OrganizeAction",
"name": "'.$_fio['surname_login'].' '.$_fio['name_login'].' '.$_fio['patronymic_login'].' '.$data.' '.$v_a['with_schedule'].'"';
                                    $json .= ',
"object": {
    "@type": "ScheduleAction",
    "name": "'.JText::_($value['ru_specialty']).'",
    "startTime": "'.$data.' '.$v_a['with_schedule'].'",
    "endTime": "'.$data.' '.$v_a['to_schedule'].'",
    "location": {
        "@type": "PostalAddress",
        "postOfficeBoxNumber": "'.$v_a['cabinet_schedule'].'"
    
    }';
                                    $json .= ',
    "agent": {
        "@type": "Person",
        "name": "'.$_fio['surname_login'].' '.$_fio['name_login'].' '.$_fio['patronymic_login'].'",
        "familyName": "'.$_fio['surname_login'].'",
        "givenName" : "'.$_fio['name_login'].'",
        "additionalName": "'.$_fio['patronymic_login'].'"';
                                                $json .= '
        }                                        
    }                                                                                            
}';
                                                

                                                //Формируем разметку JSON.
                                                 $json = '<script type="application/ld+json">
'.$json.'
</script>'; 
                                                $table .= '<td style="border: 1px solid black;">'.$this->PreparationTime($v_a['with_schedule']).'</td>';
                                                $table .= '<td style="border: 1px solid black;">'.$this->PreparationTime($v_a['to_schedule']).'</td>';
                                                $table .= '<td style="border: 1px solid black;"><div style="display:none">'.$json.'</div>'.$v_a['cabinet_schedule'].'</td>';
                                                $table .= '</tr>';
                                                $end2 = true;
                                            }   
                                        }
                                    }
                                
                                $json .= '
    }';                             
                                }
                                unset($k_fio, $_fio);
                            }
                        }
                        $json .= '
    }';
                    }
                    unset($key, $value);
                }
                //Формируем концовку таблици.
                $table .= '</tbody></table>';
               
                return $table;
            } 
        }
    }
    
    
    
    //Метод подготовки значения времени для отображения.
    public function PreparationTime($var = null){
        if($var != null){
            $arr = explode(':', $var);
            $ret = $arr['0'].'-'.$arr['1'];
            return $ret;
        }
    }
    
}