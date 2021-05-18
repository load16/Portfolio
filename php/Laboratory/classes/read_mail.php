<?php
  //Класс отвественный просмотр отправленных писем пациентам и получение отчетов.
  include_once 'classes/room.php';
  include_once 'library/select.php';
  include_once 'library/s_adobe.php';
  include_once 'classes/log.php';
  include_once 'classes/mail.php';
  include_once 'classes/pat.php'; 
  class read_mail extends room{
      
      
       var $heat;                                            //Заглавие задачи.
       var $footer;                                          //Фитер модуля.
       var $tools;                                           //Панель инструментов.
       var $content;                                         //Выводимый контент модуля.
       var $log;                                             //Объект логирования.
       var $mail;                                            //Объект отправки писем.
       var $pat;                                             //Объект для работы с пациентами.
      
      
      //Конструктор класса.
      function __construct($permission = false){
           parent::__construct();                                        //Выполнение конструктора предшественника.     
           $config = $this->config;
           $this->mail = new mail($config->conf['imaphost'], $config->conf['smtpuser'], $config->conf['smtppass']);
           $this->log = new log('true');
           $this->pat = new pat($permission);                                 //Создание объекта логирования.
           unset($config);
           read_mail::action_read_mail();                                          //Обрабатываем действие пользоватей.
           print room::getView();                                        //Отображаем кабинет.
      }
      
      
      
      //Деструктор класса.
      function __destruct(){
          unset($this->mail, $this->log, $this->pat);
          parent::__destruct();
      }
      
      //Метод обработки действия пользователя.
      private function action_read_mail(){
          if(isset($_SESSION['global']['search']['id']) && isset($_POST['closeView'])){//Обработка закрития просмотра.
               print '<script> alert(\'Закритие невозможно пока выбран пациент. Сбросте выбранного пациента!\') </script>';//ВПОказываем сообщение. 
          }
          if(isset($_POST['reset_selected_pat'])){
              unset($_SESSION['global']['search']);
          }
          if(isset($_POST['reset_parameter'])){                                     //Обработка зброса параметров.
              unset($_SESSION['read_mail']['seach']);
          }
          
          if(isset($_POST['sel_year'])){                                            //Обработка выбора года.
              unset($_SESSION['read_mail']['seach']);
              $_SESSION['read_mail']['seach']['year'] = $_POST['sel_year'];
          }
          if(isset($_POST['sel_month'])){                                           //Обработка выбора месяца.
              $_SESSION['read_mail']['seach']['month'] = $_POST['sel_month'];
              unset($_SESSION['read_mail']['seach']['day'], $_SESSION['read_mail']['seach']['mail'], $_SESSION['read_mail']['seach']['id']);
          }
          if(isset($_POST['sel_day'])){                                             //Обработка выбора дня.
              $_SESSION['read_mail']['seach']['day'] = $_POST['sel_day'];
              unset($_SESSION['read_mail']['seach']['mail'], $_SESSION['read_mail']['seach']['id']); 
          }
          if(isset($_POST['select_mail'])){                                         //Обработка выбора письма.
              $_SESSION['read_mail']['seach']['id'] = $this->definitionIdPat($_POST['select_mail']);
              $_POST['search_id'] = $_SESSION['read_mail']['seach']['id'];          //Фиксируем данные глобально.
              $_SESSION['read_mail']['seach']['mail'] = $_POST['select_mail'];
          }
          
          
          $this->pat->action_pat('read_mail');                                      //Обрабатываем действия пользователя по выбору пациента.
          
          //$contentColumn[] = $this->content = $this->selectMenuPeriod('year', 'sel_year', 'Выбор года', $_SESSION['read_mail']['seach']['year']);
          //Получаем данные для меню выбора года.
          $arr_y = $this->getDataMunuPeriod($_SESSION['read_mail']['seach']['year']);
          $contentColumn[] = $this->selectMenuPeriod($arr_y, 'year', 'sel_year', 'Выбор года', $_SESSION['read_mail']['seach']['year']);
          if(isset($_SESSION['read_mail']['seach']['year'])){                       //Условие показа панели выбора месяца..
              $arr_m = $this->getDataMunuPeriod($_SESSION['read_mail']['seach']['year']);
              $contentColumn[] = $this->selectMenuPeriod($arr_m, 'month', 'sel_month', 'Выбор месяца', $_SESSION['read_mail']['seach']['month']);
          }
          if(isset($_SESSION['read_mail']['seach']['month'])){                       //Условие показа панели выбора дня..
              $arr_d = $this->getDataMunuPeriod($_SESSION['read_mail']['seach']['year'], $_SESSION['read_mail']['seach']['month']);
              $contentColumn[] = $this->selectMenuPeriod($arr_d, 'day', 'sel_day', 'Выбор дня', $_SESSION['read_mail']['seach']['day']);
          }
          
          unset($arr_y, $arr_m, $arr_d);                                            //Штатный сброс.
          
          if(                                                                        //Показываем данные при выбранном пациенте глобально.
          !isset($_SESSION['read_mail']['seach']['year'])
          && isset($_SESSION['global']['search']['id']))
          {
              //Получаем список отправленных писем.
              $data_w = $this->getListFileSend($_SESSION['global']['search']['id']);
              $this->content = $this->getViewWindow($this->getViewData($data_w), 'Печать списка отправленных писем.');
              $_SESSION['read_mail']['seach'] = $data_w;
              if($data_w == ''){                                                    //Если писам нет, то паказываем сообщение.
                  $this->content = 'Отправленных писем выбранного пациента '.$_SESSION['global']['search']['fio_send'].' - нет!';
              }
              unset($data_w);
          }
          else{                                                                     //Штатный показ.
              if(isset($_POST['view_data'])){                                               //Обработка печати и просмотра данных.
                  //Получаем список отправленных писем.
                  $data_w = $this->getListFileSend($_SESSION['read_mail']['seach']['id'], $_SESSION['read_mail']['seach']['year'], $_SESSION['read_mail']['seach']['month'], $_SESSION['read_mail']['seach']['day']);
                  $this->content = $this->getViewWindow($this->getViewData($data_w), 'Печать списка отправленных писем.');
                  unset($data_w);
              }
              else{
                  if(count($contentColumn) >= 2){
                      $contentLine[] = $this->getElementsColumn($contentColumn);
                  }
                  else{
                      $this->content = $contentColumn['0'];
                  }
                  
                  if(isset($_SESSION['read_mail']['seach']['year'])){                                   //Усдовие показа панели выбоа писем.
                      $contentLine[] = $this->selectMail('Выбор письма', $_SESSION['read_mail']['seach']['year'], $_SESSION['read_mail']['seach']['month'], $_SESSION['read_mail']['seach']['day'], $_SESSION['read_mail']['seach']['mail']);
                  }
                  
                  if(count($contentLine) >= 2){
                       $this->content = $this->getElementsLine($contentLine);
                  }
              }
          }
          $this->heat = 'Отчеты по отправленным письмам и файлам.';
          $this->tools = $this->getForm($this->getTools());
          room::outContent(read_mail::getView());                            //Вывод всего модуля.
      }
      
      
      //Метод отображения модуля.
      public function getViewModule(){
          $ret = '
          <div class="module">
              <div class="module_heat">
                  '.$this->heat.'
              </div>
              <div class="module_tools">
                  '.$this->tools.'
              </div>
              <div class="module_content">
                  '.$this->content.
              '</div>
              <div class="module_footer">
                  '.$this->footer.'
              </div>
          </div>';
          return $ret;
      }
      
      //Метод отображения модуля.
      public function getView(){
          return room::getViewModule();
      }
      
      
     
     
      
      //Метод получения панели инструментов.
      private function getTools($create = false){
          if(isset($_SESSION['read_mail']['seach']['year'])){
              $ret .= '<input type="submit" name="view_data" value="Просмотр">';
              $ret .=  '<input type="submit" name="reset_parameter" value="Зброс параметров">';
          }
          if(isset($_SESSION['global']['search']['id'])){                       //Кнопка при выбранном пациенте
              $ret .= '<input type="submit" name="reset_selected_pat" value="Зброс выбранного пациента">';
          }   
          return $ret;
      }
      
      
      //Метод получения меню выбора периода.
      private function selectMenuPeriod($arr = null, $name = null, $namePost = null, $declaration = null, $selected = null){
          if($name != null && $namePost != null && count($arr) >= 1){
              $select[] = 'data';
              //$arr = $this->register->searchUniqueData('send_mail', $select);                           //Получаем данные с базы.
              //$arr = $this->getDataMenuMail($)
              if(count($arr) >= 1){                                                                     //Проверка наличия данных.
                  foreach($arr as $k => $v){                                                            //Обходим данные.
                      $r = $this->date->extraction($v[$select['0']]);                                   //Разделяем отпечаток времени на составляющие.
                      $y[$r[$name]][$name] = $r[$name];                                                          //Получаем массив.
                      unset($r);                                                                        //Штатный сброс. 
                  }
                  unset($k, $v);
                  $select = new select();                                                               //Формируем элемент тнтерфейса.                                                               
                  $ret = '<div class="oval">';
                  $ret .= $declaration.' '."\n";
                  $ret .= $select($namePost, $name, $y, '', $selected)."\n";
                  $ret = $this->getForm($ret);
                  $ret .= '</div>';
                  unset($select);
                  
                  return $ret;                                                                          //возвращаем результат.
              }         
          }    
      }
      
      
      //Метод получения меню писем.
      private function selectMail($declaration = null, $year = null, $month = null, $day = null, $selected = null){
          $arr_select = $this->getDataMenuMail($year, $month, $day);                                        //Полуаем данные для меню  
              if(count($arr_select) >= 1 && $declaration != null){                                                                  //Проверка наличия данных для меню.
                  $select = new select();                                                                   //Формируем элемент тнтерфейса.                                                               
                  $ret = '<div style="font-size: 80%;" class="module_heat">';
                  $ret .= '<b>'.$declaration.'</b><br/>'."\n";
                  $ret .= $select('select_mail', 'select', $arr_select, 9, $selected)."\n";
                  $ret = $this->getForm($ret);
                  $ret .= '</div>';
                  unset($select);
                  return $ret;                                                                              //Возвращаем результат.
              }
      }
      
      
      //Метод для получения данных для меню выбоа периода.
      private function getDataMunuPeriod($year = null, $month = null, $day = null){
          if($year != null){
              if($month != null){                                                                           //Проверка наличия месяца.
                  if($day != null){                                                                         //Проверка наличия дня.
                      $arr = $this->register->searchDinData('send_mail', 'data', $year.'-'.$month.'-'.$day); //Получаем данные.
                  }
                  if(count($arr) == 0){                                                                     //Если нет результата, то ищем по месяцу.
                      $arr = $this->register->searchDinData('send_mail', 'data', $year.'-'.$month.'-');
                  }
              }
              if(count($arr) == 0){                                                                         //Если нет результата, то ищем по году.
                  $arr = $this->register->searchDinData('send_mail', 'data', $year.'-');
              }
          }
          else{
              $arr = $this->register->getAllRecord('send_mail');
          }
          return $arr;
      }
      
      
      //Метод получения данных для меню писем.
      private function getDataMenuMail($year = null, $month = null, $day = null){
          if($year != null){                                                                                //Проверка наличия года.
              if($month != null){                                                                           //Проверка наличия месяца.
                  if($day != null){                                                                         //Проверка наличия дня.
                      $arr = $this->register->searchDinData('send_mail', 'data', $year.'-'.$month.'-'.$day); //Получаем данные.
                  }
                  if(count($arr) == 0){                                                                     //Если нет результата, то ищем по месяцу.
                      $arr = $this->register->searchDinData('send_mail', 'data', $year.'-'.$month.'-');
                  }
              }
              if(count($arr) == 0){                                                                         //Если нет результата, то ищем по году.
                  $arr = $this->register->searchDinData('send_mail', 'data', $year.'-');
              }
              if(count($arr) >= 1){                                                                         //Проверка наличия найденных данных.
                  foreach($arr as $k => $v){                                                                //Обходим данные.
                      $id_p = $this->register->geteRecordId($v['id_p'], 'patients');                        //Получаем данные пациента.
                      if(count($id_p) == 1){                                                                //Проверка наличия данных.
                          $fio = $id_p['0']['fio'];                                                         //Формируем ФИО пациента.
                      }
                      $arr_select[]['select'] = $fio.'%  '.$v['e-mail'];                                     //Формируем массив для меню
                  }
                  unset($k, $v);                                                                            //Штатный сброс.
              }
              if(count($arr_select) >= 1){                                                                  //Проверка наличия данных для меню.
                  return $arr_select;                                                                       //Возвращаем результат.
              }
          }
      }
      
      
      //Метод для определения ИД пациента по выбранноме письму.
      private function definitionIdPat($data = null){
          if($data != null){                                                                                //Проверка наличия данных.
              $arr_d = explode('%  ', $data);                                                               //Разделяем данные. \
              //return $arr_d;
              if(count($arr_d) == 2){                                                                       //Проверка наличия данных после разделения.
                  $arr = $this->register->searchData('patients', 'fio', $arr_d['0']);                       //Получаем данные с базы
                  if(count($arr) >= 1){                                                                     //Проверка наличия данных.
                      foreach($arr as $k => $v){                                                            //Обходим данные.
                          if($v['e-mail'] == $arr_d['1']){                                                  //Находим ныжный маел.
                              return $v['id'];                                                              //Возвращаем ИП.
                          }
                      }
                      unset($k, $v);
                  }
              }
          }
      }
      
      
      //Метод получения окна для печати и просмотра.
      private function getViewWindow($data = null, $declaration = null){
          if($data != null && $declaration != null){
              $ret = '<div>'."\n";
              $ret .= '<div class="mini_tools">'."\n";
              $ret .= $this->getForm($this->getButtonPrint('print_mail').'<input type="submit" name="closeView" value="Закрыть">');
              $ret .= '</div>'."\n";
              $ret .= '<div class="oval">'."\n";
              $ret .= $declaration;
              $ret .= '</div>'."\n";
              $ret .= '<div class="oval" style="border: solid 1px; width: 700px; height: 250px; resize: both; overflow: auto; font-size:20%;">'."\n";
              $ret .= $data; 
              $ret .= '</div>'."\n";
              //$ret .= '<div>'."\n"; 
              $ret .= '<div style="display: none; visibility: hidden;">'."\n";
              $ret .= $this->getDivPrint($data, 'print_mail');  
              $ret .= '</div>'."\n";
              $ret .= '</div>'."\n";
              return $ret;
          }
      }
      
      
      //Метод получения данных окна для просмотра данных для распечатки.
      private function getViewData($arr = null){
          if(count($arr) >= 1){                                                                     //Проверка наличия данных.
              $table = '<table  border="1">'."\n";                                                              //Формируем шапку таблыци.
              $table .= '<tr>'."\n";
              $table .= '<th>'."\n";
              $table .= 'ФИО';
              $table .= '</th>'."\n";
              $table .= '<th>'."\n";
              $table .= 'E-MAIL';
              $table .= '</th>'."\n";
              $table .= '<th>'."\n";
              $table .= 'Отправленные файлы';
              $table .= '</th>'."\n";
              $table .= '<th>'."\n";
              $table .= 'Дата отправки';
              $table .= '</th>'."\n";
              $table .= '</tr>'."\n";
              foreach($arr as $k => $v){                                                            //Обходим массив.
                  $unique[] = $v['id_p'];                                                   //Формируем массив ИД пациентв.
              }
              unset($k, $v);
              $unique = array_unique($unique);                                                      //Формируем уникальный массив.
              foreach($unique as $ku => $vu){                                                       //Обходим уникальний масив.
                  $n = 0;                                                                           //Начальная установка счетчика.
                  $table .= '<tr>'."\n";
                  foreach($arr as $k => $v){                                                        //Обходим массив данных
                      if($vu == $v['id_p']){                                                        //Находим записи по ИД пациента.
                          $arr_table[] = $v;                                                        //Формируем данные для таблыци.
                          $line_f = $v['fio'];
                          $line_e = $v['e-mail'];
                          $n++;                                                                     //Инкремент счетчика.
                      }
                  }
                  unset($k, $v);                                                                //Штатный сброс.
                  if($n >= 2){
                      $table .= '<td rowspan="'.$n.'">'."\n";
                      $table .= $line_f;
                      $table .= '</td>'."\n";
                      $table .= '<td rowspan="'.$n.'">'."\n";
                      $table .= $line_e;
                      $table .= '</td>'."\n";
                  }
                  else{
                       $table .= '<td>'."\n";
                       $table .= $line_f;
                       $table .= '</td>'."\n";
                       $table .= '<td>'."\n";
                       $table .= $line_e;
                       $table .= '</td>'."\n";
                  }
                  unset($line_e, $line_f);    
                  if(count($arr_table) >= 1){                                                       //Проверка наличия данных для таблици.
                      $nn = 0;
                      foreach($arr_table as $kt => $vt){                                            //Обходим массив таблыци и рисуем строку ее.
                          if($nn >= 1){
                              $table .= '</tr>'."\n";  
                              $line .= '<tr>'."\n";
                          }
                          $table .= '<td>'."\n";
                          $table .= $vt['file'];
                          $table .= '</td>'."\n";
                          $table .= '<td>'."\n";
                          $table .= $vt['data'];
                          $table .= '</td>'."\n";
                          
                          $nn++;
                      } 
                      unset($kt, $vt, $arr_table);
                  }
                  $table .= '</tr>'."\n";
              }
              $table .= '</table>'."\n";
              unset($ku, $vu);
              return $table;
          }
      }
      
      
      
      //Метод получения списка отправленных файлов.
      private function getListFileSend($id_pat = null, $year = null, $month = null, $day = null){
          if($id_pat != null){                                                                              //Проверка наличия ИД.
              $arr = $this->register->searchData('send_files', 'id_p', $id_pat);                            //Получаем список оп ИД пациента.
          }
          else{                                                                                                 //Если ИД нет, то ищем по времени.
              if($year != null){                                                                                //Проверка наличия года.
                  if($month != null){                                                                           //Проверка наличия месяца.
                      if($day != null){                                                                         //Проверка наличия дня.
                          $arr = $this->register->searchDinData('send_files', 'data', $year.'-'.$month.'-'.$day); //Получаем данные.
                      }
                      if(count($arr) == 0){                                                                     //Если нет результата, то ищем по месяцу.
                          $arr = $this->register->searchDinData('send_files', 'data', $year.'-'.$month.'-');
                      }
                  }
                  if(count($arr) == 0){                                                                         //Если нет результата, то ищем по году.
                      $arr = $this->register->searchDinData('send_files', 'data', $year.'-');
                  }
                  
              }
          }
          if(count($arr) >= 1){                                                                         //Проверка наличия найденных данных.
                      foreach($arr as $k => $v){                                                                //Обходим данные.
                          $id_p = $this->register->geteRecordId($v['id_p'], 'patients');                        //Получаем данные пациента.
                          if(count($id_p) == 1){                                                                //Проверка наличия данных.
                              $arr[$k]['fio'] = $id_p['0']['fio'];                                              //Формируем данные пациента.
                              $arr[$k]['phone'] = $id_p['0']['phone'];
                              $arr[$k]['birth'] = $id_p['0']['birth'];
                              $arr[$k]['town'] = $id_p['0']['town'];
                              $arr[$k]['address'] = $id_p['0']['address'];
                              $arr[$k]['consultant'] = $id_p['0']['consultant']; 
                              $arr[$k]['data_updates'] = $id_p['0']['data_updates'];  
                          }
                      }
                      unset($k, $v);                                                                            //Штатный сброс.
          }
          if(count($arr) >= 1){                                                                         //Проверка наличия данных.  
            return $arr;                                                                              //Возвращаем результат/
          }    
      }
      
      
  }
?>
