<?php
  //Класс ответсвенный работу с пациентом.
  
  
  include_once 'library/s_adobe.php';
  include_once 'library/select.php';
  include_once 'classes/log.php';  
  
  class pat extends room{
      
      var $content_pat;                                                                 //Готовый контент.
      var $log;                                                                         //Объект логирования.  
      
      //Конструктор класса.
      function __construct($permission = false){
          parent::__construct($permission);
          $this->log = new log('true');                                                 //Создание объекта логирования.
      }
      
      
      //Метод получения панели выбора пациента в случае наличия в базе двойника.
      public function getSelectDouble($arr = null, $fio = null){
          if(count($arr) >= 2 && $fio != null){
               $select = new select();                                                  //Если есть данные, то показываем список.
               $ret = '<div class="module_heat">';
               $ret .= 'Выбор пациента</br>двойника</br>"'.$fio.'"</br>по ID:</br>'."\n";
               $ret .= $select('fio_sel_double', 'id', $arr, 5)."\n";
               $ret = $this->getForm($ret);
               $ret .= '</div>';
               unset($select);
          }
          return $ret;          
      }
      
      
      
      
      //Метод получения панели поиска пациента.
      public function getSearchPat($name = null, $value = null, $selected = null){
          if($name != null && $value != null){
              $arr = $this->register->searchDinData('patients', $name, $value);         //Получаем данные с базы
              $ret .= '<div class="module_heat" style="font-size: 85%;">'."\n";
              if(count($arr) >> 0){                                                     //Проверка наличия данных.
                  $select = new select();                                               //Если есть данные, то показываем список.
                  $ret .= '<b>Выбор пациента:</b></br>'."\n";
                  $ret .= $select('fio_send', 'fio', $arr, 9, $selected)."\n";
                  $ret = $this->getForm($ret);
                  unset($select);
              }
          }
          //Готовим кнопку поиска.
          if($name == 'fio'){
              $b_search = '<div>ФИО <input type="text" name="search_fio" value="'.$value.'"" size="15"> <input type="submit" name="newuser" value="Поиск"></div>';
          }
          else{
              $b_search = '<div>ФИО <input type="text" name="search_fio" size="15"> <input type="submit" name="newuser" value="Поиск"></div>';
          }
          $b_form = $this->getForm($b_search);
          if($name == 'id'){
              $a_search = '<div>ИД.. <input type="text" name="search_id" value="'.$value.'"" size="15"> <input type="submit" name="newuser_id" value="Поиск"></div>';
          }
          else{
              $a_search = '<div>ИД.. <input type="text" name="search_id" size="15"> <input type="submit" name="newuser_id" value="Поиск"></div>';
          }
          
          $a_form = $this->getForm($a_search);
          
          $ret .= $b_form.$a_form;
          $ret .= '</div>'."\n";
          return $ret;
      }
      
      
      //Метод получения сводных данных по пациенту для просмотра.
      public function getDataTable($fio = null, $id = null, $full = null, $wiev = false, $print = null, $data = null){
          if($data != null){
              $a[] = '<div class="module_heat"> На дату - '.$data.'</div>';
          }
          if($fio != null && $data == null){
              $a[] = $this->getDataTableFIO($fio, $full, $wiev);
          }
          if($id != null){
              $a[] = $this->getDataTableID($id, $full, $print, $data);
              $a[] = $this->getTableMat($id, $full, $print, $data);
          }
          if(count($a) >= 2){
              if($data != null){
                  return $this->getElementsColumn($a);
              }
              else{
                  return $this->getElementsLine($a); 
              }
          }
          else{
              return $a['0'];
          }
      }
      
      
      
      
      //Метод получения сводной таблици с данными о готовых нанализах отобранного пациента по ИД
      public function getDataTableFIO($fio = null, $full = false, $wiev = false){
          if($fio != null){                                                                             //Проверка необходимого.
              //$fio = $this->register->prepareFIO($fio);                                                 //Подготовка ФИО для поиска.
              $arr = $this->register->getListFileFio($fio, 'full_files');                        //Получаем данные.
              if(count($arr) >= 1){                                                                     //Проверка наличия данных.
                  if($full){                                                                            //Проверка флага полгого просмотра.
                       $ret = '<div style="width:150;height:250;overflow:scroll;font-size:80%" >';
                  }
                  else{
                      $ret = '<div style="width:150;height:100;overflow:scroll;font-size:60%" >';
                  }
                  $s_adbobe = new s_adobe();
                  $n = 0;                                                                               //Установка счетчика элементов массива.
                  foreach($arr as $k => $v){                                                            //Обходим массив.
                      if($wiev){
                          $ret .= $s_adbobe->getUrlFile($v['name'], $v['fullname'], $this->config->conf['patchseach']).'<br/>'."\n"; 
                      }
                      else{
                          $ret .= $v['name'].'<br/>'."\n"; 
                      }
                      $n++;                                                                             //Инкремент счетчика.
                      if($n >> 60){                                                                     //Условия прерывания цикла.
                          break;
                      }
                  }
                  unset($k, $v);
                  $ret .= '</div>';
                  return $ret;
              }
          }
      }
      
      
      
      
      //Метод получения сводной таблици с данными о сданным анадизам на отобранного пациента по ИД.
      public function getDataTableID($id = null, $full = false, $print  = false, $data = null){
          if($id != null){
              $arr = $this->register->geteRecordId($id, 'patients');                                    //Получаем данные с базы.
              //$arr_all = $this->register->getAllDataID($id);                                            //Получаем данные
              $arr_all = $this->register->searchData('analyzes_patients', 'id_p', $id);
              if(count($arr_all) >= 1){
                  if($full){                                                                            //Полноэкранный вывод.
                       $ret = '<div style="width250;height:250;overflow:scroll;" >';
                       $ret .= '<table style="font-size:80%" border="1">';
                  }
                  else{                                                                                 //Краткий вывод.
                      $ret = '<div style="width:220;height:100;overflow:scroll;" >';
                      $ret .= '<table style="font-size:60%" border="1">';
                  }
                  $n = 0;                                                                               //Начальная установка счетчика.
                  $ret .= '<tr class="htable">'."\n";
                  $ret .= '<td >Время</td>'."\n";
                  $ret .= '<td>Анализ</td>'."\n";
                  //$ret .= '<td>Время</td>'."\n";  
                  $ret .= '</tr>'."\n";  
                  foreach($arr_all as $k => $v){                                                        //Обходим данные.
                      $p = false;                                                                       //Начальная установка флага.
                      if($data != null){                                                                //Если есть дата, то 
                          if($data == $v['data']){                                                      //показываем данные на дату
                              $p = true;
                          }
                      }
                      else{
                          $p = true;
                      }
                      if($p){
                          $ret .= '<tr class="dtable">'."\n";
                          $ret .= '<td>'.$v['datatime'].'</td>'."\n";
                          $arr_s = $this->register->geteRecordId($v['id_a'], 'analyzes');                     //Получаем данные с справочника. 
                          if(count($arr_s) == 1){
                              $ret .= '<td>'.$arr_s['0']['name_a'].'</td>'."\n"; 
                          }
                          else{
                              $ret .= '<td></td>'."\n";
                          }
                          //$ret .= '<td>'.$v['datatime'].'</td>'."\n"; 
                          $ret .= '</tr>';
                      }
                          
                      $n++;                                                                             //Инкремент счетчика.
                      if($n >> 60){                                                                     //Условия прерывания цикла.
                          break;
                      }
                  }
                  unset($k, $v);                                                                        //Штатный сброс.
                  $ret .= '</table>'."\n";
                  $ret .= '</div>';
                  return $ret;
              }
          }
      }
      
      
      
      //Метод получения сводной таблици использованных материальных средств.
      public function getTableMat($id = null, $full = false, $print  = false, $data = null){
          if($id != null){
              $arr_m = $this->register->searchData('issued', 'id_p', $id);                  //ПОлчаем данные с базы.
              if(count($arr_m) >= 1){                                                       //Проверка наличия данных.
                  if($full){                                                                            //Полноэкранный вывод.
                       $ret = '<div style="width280;height:250;overflow:scroll;" >';
                       $ret .= '<table style="font-size:80%" border="1">';
                  }
                  else{                                                                                 //Краткий вывод.
                      $ret = '<div style="width:220;height:100;overflow:scroll;" >';
                      $ret .= '<table style="font-size:60%" border="1">';
                  }
                  $n = 0;                                                                               //Начальная установка счетчика.
                  $ret .= '<tr class="htable">'."\n";
                  $ret .= '<td >Дата</td>'."\n";
                  $ret .= '<td>Использовано</td>'."\n";
                  $ret .= '<td>Кол.</td>'."\n";
                  $ret .= '<td>Мера</td>'."\n";   
                  $ret .= '</tr>'."\n";
                  
                  foreach($arr_m as $k => $v){                                                          //Обходим данные.
                      $p = false;                                                                       //Начальная установка флага.
                      if($data != null){                                                                //Если есть дата, то
                          if($v['data'] == $data){                                                      //показываем данные на дату.
                              $p = true;
                          }
                      }
                      else{
                          $p = true;
                      }
                      if($p){                                                                           //Если есть флаг, то показываем данные.
                          $arr_s = $this->register->geteRecordId($v['id_f'], 'funds');                  //Получаем данные с справочника.
                          $ret .= '<tr class="dtable">'."\n"; 
                          $ret .= '<td>'.$v['data'].'</td>'."\n";
                          if(count($arr_s) >= 1){                                                       //Проверка наличия данных.
                              $ret .= '<td>'.$arr_s['0']['name_f'].'</td>'."\n";
                          }
                          else{
                              $ret .= '<td></td>'."\n";
                          }
                          $ret .= '<td>'.$v['amount'].'</td>'."\n";
                          $ret .= '<td>'.$arr_s['0']['measure'].'</td>'."\n"; 
                          $ret .= '</tr>';
                          $n++;
                      }
                                                                                                        //Инкремент счетчика.
                      if($n >> 60){                                                                     //Условия прерывания цикла.
                          break;
                      }
                  }
                  unset($k, $v);
                  $ret .= '</table>'."\n";
                  $ret .= '</div>';
                  return $ret;
              }
          }
      }
      
      
      
      
      
      //Метод получения данных отобранного пациента.
      public function getDataPat($id = null, $arr = null, $selected = false, $new = false, $update = false){
          
          //$read = '<div style="font-size: 80%;">'."\n"; 
          if($id != null && $arr == null){                                                  //Проверка наличия ИД пациента.
              $arr = $this->register->geteRecordId($id, 'patients');                        //Получаем данные с базы.
          }
          if($id == null && $arr == null){
              $ret .= '<div class="module_heat">Создание нового пациента!</div></br>'."\n";
          }
          if($update){
              $ret .= '<div class="module_heat">Изменение пациента!</div></br>'."\n";
          }
          
          
          if($new || $update || $id == null && $arr == null){
              $ret .= '<div class="module_content" style="font-size: 80%;">'."\n";
              if($arr['0']['id'] != ''){
                  $ret .= '<b>Идентификатор '.$arr['0']['id'].'</b></br>'."\n";
              }
              $ret .= 'ФИО <input type="text" name="fio" value="'.$arr['0']['fio'].'" size="37"></br>'."\n";
              $ret .= 'e-mail <input type="text" name="e-mail" value="'.$arr['0']['e-mail'].'" size="36"></br>'."\n"; 
              $ret .= 'Город <input type="text" name="town" value="'.$arr['0']['town'].'" size="36"></br>'."\n"; 
              $ret .= 'Адрес <textarea name="address" title="'.$arr['0']['address'].'"  rows="1" cols="28">'.$arr['0']['address'].'</textarea></br>'."\n";
              $ret .= 'Дата рождения <input type="text" name="birth" value="'.$arr['0']['birth'].'" size="10"></br>'."\n"; 
              $ret .= 'Телефон <input type="text" name="phone" value="'.$arr['0']['phone'].'" size="20"></br>'."\n"; 
              $ret .= 'Эл. направление <input type="text" name="electronic" value="'.$arr['0']['electronic'].'" size="25"></br>'."\n"; 
              $ret .= 'Как узнали про нас <input type="text" name="about" value="'.$arr['0']['about'].'" size="25"></br>'."\n";
              $ret .= 'Консультант <input type="text" name="consultant" value="'.$arr['0']['consultant'].'" size="30"></br>'."\n"; 
              $ret .= 'Тип приема <input type="text" name="reception" value="'.$arr['0']['reception'].'" size="25"></br>'."\n"; 
              $ret .= 'Описание проблемы <textarea name="description" title="'.$arr['0']['description'].'" rows="1" cols="16">'.$arr['0']['description'].'</textarea></br>'."\n"; 
              $ret .= 'Дата создания <input type="text" name="data_create" value="'.$arr['0']['data_create'].'" size="20"></br>'."\n";
          }
          else{
              $ret .= '<div class="module_content" style="font-size: 80%;">'."\n";
              $ret .= '<b>Идентификатор - '.$arr['0']['id'].'</b></br>'."\n";  
              $ret .= 'ФИО - <b>'.$arr['0']['fio'].'</b></br>'."\n";
              $ret .= 'e-mail - '.$arr['0']['e-mail'].'</br>'."\n"; 
              $ret .= 'Город - '.$arr['0']['town'].'</br>'."\n"; 
              $ret .= 'Адрес - <textarea name="address" title="'.$arr['0']['address'].'" disabled readonly  rows="1" cols="28">'.$arr['0']['address'].'</textarea></br>'."\n";
              $ret .= 'Дата рождения - '.$arr['0']['birth'].'</br>'."\n"; 
              $ret .= 'Телефон - '.$arr['0']['phone'].'</br>'."\n"; 
              $ret .= 'Эл. направление - '.$arr['0']['electronic'].'</br>'."\n"; 
              $ret .= 'Как узнали про нас - '.$arr['0']['about'].'</br>'."\n";
              $ret .= 'Консультант - '.$arr['0']['consultant'].'</br>'."\n"; 
              $ret .= 'Тип приема - '.$arr['0']['reception'].'</br>'."\n"; 
              $ret .= 'Описание проблемы <textarea name="description" title="'.$arr['0']['description'].'" disabled readonly  rows="1" cols="16">'.$arr['0']['description'].'</textarea></br>'."\n"; 
              $ret .= 'Дата создания -'.$arr['0']['data_create'].'</br>'."\n";
          }
          
              
          
          
          if($selected){
              if($arr['0']['fio'] != null){
                  $ret .= '<input type="submit" name="select_p_reset" value="Сброс">'; 
              }
          }
          else{
              
              if($id == null && $arr == null || $new){
                  $button = '<input type="submit" name="select_p_new" value="Создать">';
                  $button .= '<input type="submit" name="select_p_reset" value="Отмена">';  
              }
              else{
                  $button = '<input type="submit" name="select_p" value="Выбрать">'; 
              }
              if($update){
                  $button = '<input type="submit" name="select_change" value="Изменить">';
                  $button .= '<input type="submit" name="select_p_reset" value="Отмена">';
              }
              
          }
          $ret .= $button."\n"; 
          $ret .= '</div>'."\n";  
          return $this->getForm($ret);
      }
      
      
      
      //Метод получения контента для работы с пациентом.
      public function getContent(){
          if(isset($_SESSION['global']['search']['arr'])){                    //Проверка наличия данных выбранного пациента.
              if(isset($_SESSION['global']['search']['fio_sel_double'])){     //Проверка наличия двойников.
                  //$this->content_pat = $this->getSelectDouble($_SESSION['global']['search']['arr'], $_SESSION['global']['search']['fio_send']);
                  $this->content_pat = $this->getSelectDouble($_SESSION['global']['search']['arr'], $_SESSION['global']['search']['fio_send']); 
              }
              else{
                  if(isset($_SESSION['global']['search']['mod_p'])){              //Условия для показа панели изменения данных пациента.
                      $this->content_pat = $this->getDataPat($_SESSION['global']['search']['id'], $_SESSION['global']['search']['arr'], false, false, true);
                  }
                  if(!isset($_SESSION['global']['search']['select_p'])){          //Условие для показа панели поиска.
                      $a[]  = $this->getSearchPat($_SESSION['global']['search']['name'], $_SESSION['global']['search']['search'], $_SESSION['global']['search']['fio_send']); 
                  }
                  if(isset($_SESSION['global']['search']['arr'])
                  && !isset($_SESSION['global']['search']['mod_p'])){                //Условие для показа панели тображение данных.
                      $a[] = $this->getDataPat($_SESSION['global']['search']['id'], $_SESSION['global']['search']['arr'], $_SESSION['global']['search']['select_p']);

                  }
              }                  
          }
          else{                                                                 //Есди нет выбранных данных то показываем панель для поиска.
              $this->content_pat = $this->getSearchPat($_SESSION['global']['search']['name'], $_SESSION['global']['search']['search'], $_SESSION['global']['search']['fio_send']);
              if(isset($_SESSION['global']['search']['new_p'])){                  //Условие показа формы создания нового пациента.
                  if(isset($_POST['select_p_new'])){
                      $arr[] = $_POST;
                      $this->content_pat = $this->getDataPat('', $arr, '', true);
                  }
                  else{
                      $this->content_pat = $this->getDataPat();
                  }                  
              }
          }
          return $a;
      }
      
      
      //Метод обработки действий работы с пациентом.
      public function action_pat($nameModule = null){
          if($nameModule != null){
              if(isset($_POST['select_change'])){                           //Обработка изменение данных пациента.
              $arr = $_POST;                                                //Фиксируем данные.
              unset($arr['select_change']);                                 //Убираем лишнее.
              $message = $this->validationPat($arr);                        //Получаем сообщение валидационное.
               if($message == ''){                                          //Проверяем валидацию
                   $this->register->updateDataId($arr, $_SESSION['global']['search']['id']);
                   $this->log->sendLog('Пациент! '.$arr['fio'].' - обновлен!', $arr);
                   $_SESSION['global']['search']['arr']['0'] = $arr;
                   print '<script> alert(\'Пациент! '.$arr['fio'].' - обновлен!\') </script>';//Показываем сообщение о новом пациете.
               }
               else{
                   print '<script> alert(\'Валидация не пройдена! '.$message.'\') </script>';//ВПОказываем сообщение.
               }
              unset($arr, $message);
              unset($_SESSION['global']['search']['mod_p']); 
          }
          
          
          if(isset($_POST['mod_p'])){                                       //Обработка модификации пациента.
              $_SESSION['global']['search']['mod_p'] = true;
          }
          
          
          if(isset($_POST['select_p_new'])){                                //ОБработка ввода нового пациента.            
              $arr = $_POST;                                                //Фиксируем данные.
              unset($arr['select_p_new']);                                  //Убираем лишнее.
              if($this->createNewPat($arr)){                                //Создаем нового пациента.
                  $this->log->sendLog('Пациент! '.$_POST['fio'].' - создан!', $arr); 
                  $_SESSION['global']['search']['arr'] = $this->register->searchDinData('patients', 'fio', $_POST['fio']);
                  if(count($_SESSION['global']['search']['arr']) == 1){       //Проверка количесва пациентов на введенное ФИО.
                      $_SESSION['global']['search']['id'] = $_SESSION['send']['search']['arr']['0']['id'];
                      $_SESSION['global']['search']['select_p'] = true;
                  }
                  
              }
              else{                                                         //При не удачном создании пациента.
                  
              }
              unset($arr);                                                  //Сброс отработанных данных.
              
          }
          
          
          if(isset($_POST['new_p'])){                                       //Обработка выбора нового пациента.
              unset($_SESSION['global']['search']['arr']);                           //Збрасываем все настройки.
              $_SESSION['global']['search']['new_p'] = true;                  //Устанавливаем флаг нового пациента.
          }
              
              
              
              
              if(isset($_POST['fio_sel_double'])){                              //Обработка выбора пациента двойника по ФИО.
                  //$_SESSION[$nameModule]['search']['arr'] = $this->register->geteRecordId($_POST['fio_sel_double'], 'patients');
                  $_SESSION['global']['search']['arr'] = $this->register->geteRecordId($_POST['fio_sel_double'], 'patients');
                  $_SESSION['global']['search']['id'] = $_POST['fio_sel_double'];
                  $_SESSION['global']['search']['select_p'] = true;
                  //unset($_SESSION[$nameModule]['search']['fio_sel_double']); 
                  unset($_SESSION['global']['search']['fio_sel_double']);
              }
              if(isset($_POST['select_p'])){                                    //Обработка выбора пациента для просмотра файлов.
                  $_SESSION['global']['search']['select_p'] = true;
              }
              if(isset($_POST['select_p_reset'])){                              //Обработка сброса поиска.
                  //unset($_SESSION['global']['search']['select_p'], $_SESSION[$nameModule]['arr_send'], $_SESSION[$nameModule]['search']['new_p']);
                  unset($_SESSION['global']['search']['select_p'], $_SESSION['global']['arr_send'], $_SESSION['global']['search']['new_p']);
                  //unset($_SESSION[$nameModule]['search']['mod_p']);
                  unset($_SESSION['global']['search']['mod_p']);
              }
              if(isset($_POST['fio_send'])){                                    //Обработка выбора пациента в списке.
                  //$_SESSION[$nameModule]['search']['fio_send'] = $_POST['fio_send'];
                  $_SESSION['global']['search']['fio_send'] = $_POST['fio_send'];
                  //$arr_p = $this->register->searchDinData('patients', 'fio', $_SESSION[$nameModule]['search']['fio_send']);
                  $arr_p = $this->register->searchDinData('patients', 'fio', $_SESSION['global']['search']['fio_send']);
                  if(count($arr_p) >> 1){
                       //$_SESSION[$nameModule]['search']['fio_many'] = $arr_p;
                       $_SESSION['global']['search']['fio_many'] = $arr_p;
                  }
                  if(count($arr_p) == 1){                                       //Если нашли одного то фиксируем.
                      $_SESSION['global']['search']['id'] = $arr_p['0']['id'];
                      //$_SESSION[$nameModule]['search']['arr'] =  $arr_p;
                      $_SESSION['global']['search']['arr'] =  $arr_p;
                  }
                  if(count($arr_p) >=2){                                        //Если нашли много, то фиксируем двойников.
                      //$_SESSION[$nameModule]['search']['arr'] =  $arr_p;
                      //$_SESSION[$nameModule]['search']['fio_sel_double'] = true;
                      $_SESSION['global']['search']['arr'] =  $arr_p;
                      $_SESSION['global']['search']['fio_sel_double'] = true;
                  }
              }
              if(isset($_POST['search_id'])){                                //Обработка поиска пациента по ИД.
                  unset($_SESSION['global']);
                  $arr = $this->register->geteRecordId($_POST['search_id'], 'patients');
                  if(count($arr) == 1){
                      $_SESSION['global']['search']['id'] = $_POST['search_id'];
                      $_SESSION['global']['search']['select_p'] = true;
                      $_SESSION['global']['search']['arr'] = $arr;
                      $_SESSION['global']['search']['name'] = 'fio';
                      $_SESSION['global']['search']['search'] =  $arr['0']['fio'];
                      unset($arr);
                  }
                  
              }
              
              if(isset($_POST['search_fio'])){                                //Обработка поиска пациента по ФИО.
                  unset($_SESSION['global']);
                  $_SESSION['global']['search']['search'] = $_POST['search_fio'];
                  //$_SESSION[$nameModule]['search']['name'] = 'fio';
                  $_SESSION['global']['search']['name'] = 'fio';
              }
          }
      }
      
      
      
      //Метод валидации вводимых данных пациента.
      public function validationPat($arr = null, $mail = false, $phone = false){
          if(count($arr) >> 1){                                                         //ПРоверка наличия данных.
              foreach($arr as $k => $v){                                                //Обходим массив данных.
                  if($this->register->validationSring($v) == false && $v != ''){        //Проводим валидацию на враждебный код.
                      $message = 'Не пройдена валидация на враждебный код!';            //Если валидация не прошла, то формируем сообщение.
                  }
              }
              unset($k, $v);                                                            //Штатный сброс переменных.
               if($arr['fio'] == ''){
                   $message = 'ФИО пустое!';
               }
               /*
               if($arr['town'] == ''){
                   $message = 'Нет города!';
               }
               */
               if($arr['phone'] == '' && $phone){
                   $message = 'Нет телефона!';
               }
               else{
                   if($this->validationPhone($arr['phone']) == false){
                       $message = 'Телефон не прошел валидацию!';
                   }
               }
               if($arr['e-mail'] == '' && $mail){
                   $message = 'Нет мейла!';
               }
               else{
                   if(filter_var($arr['e-mail'], FILTER_VALIDATE_EMAIL) == false && $mail){
                       $message = 'Мейл указан не верно!';
                   }
               }
          }
          else{
              $message = 'Нет данных!';
          }
          return $message;
      }
      
      
      
      //Метод создания нового пациента.
      public function createNewPat($arr = null){
          if(count($arr) >> 1){                                                             //Проверка наличия данных.
              $arr['phone'] = str_replace(' ', '', $arr['phone']);                          //Исправление данных.
              $arr['e-mail'] = str_replace(' ', '', $arr['e-mail']);                        //Удаляем пробел из строки.
              $message = $this->validationPat($arr);                                        //ПОлучаем сообщение валидатора.
              if($message == ''){                                                           //Проводим валидацию.
                  $aa[] = $arr;                                                             //Готовим массив. 
                   $this->register->writeDataBase($aa);                                         //Заносим данные в базу.
                   print '<script> alert(\'Пациент! '.$arr['fio'].' - создан!\') </script>';//Показываем сообщение о новом пациете.
                   unset($_POST['select_p_new'], $_SESSION['send']['search']['new_p']);
                   unset($aa, $arr);                                                      //Штатный сброс.
                   return true;                                                             //Возвращаем истину если создан пациент.
              }
              else{
                  print '<script> alert(\'Валидация не пройдена! '.$message.'\') </script>';//ВПОказываем сообщение. 
                  return false;                                                             //При не удачном создании, возвращаем ложь.
              }
          }    
      }
      
      
      //Метод валидации тедефона.
      public function validationPhone($phone = null){
          if($phone != null){
              //Формипуе массив символов для валидации.
              $s[] = '0';
              $s[] = '1';
              $s[] = '2';
              $s[] = '3';
              $s[] = '4';
              $s[] = '5';
              $s[] = '6';
              $s[] = '7';
              $s[] = '8';
              $s[] = '9';
              $s[] = '-';
              $s[] = '(';
              $s[] = ')';
              $s[] = '+';
              $arr = str_split($phone);                                 //Превращаем строку в массив.
              foreach($arr as $k => $v){                                //Обходим массив строки.
                  $rr = false;                                          //Установка флага.
                  foreach($s as $kk => $vv){                            //Обходим массив валидационных символов.
                      if($vv == $v){                                    //Если символ соответствует валидационному,  то меняем флаг.
                          $rr = true;
                      }
                  }
                  unset($kk, $vv);
                  if(!$rr){
                      return false;                                     //При не верной валидации возвращаем ложь.
                  }
              }
              unset($k, $v);
              return true;                                              //При успешной валидации, возвращаем истину.
          }
          return false;
      }
      
      
  }
?>
