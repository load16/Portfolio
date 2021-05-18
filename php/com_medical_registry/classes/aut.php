<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;

//jimport('joomla.html.html');  
require_once (JPATH_COMPONENT.DS.'libraries'.DS.'interface.php'); 
  //Класс Authentication.    
  abstract class Aut{
      var $id;                  //Данные уатентифицированного пользователя.
      var $data;                //Данные неуатентифицированноо пользователя.
      var $session;             //Объект сессии. 
      
      
      //Мотод проверки на предмет аутентифмкации пользователя.
      public function check(){
          $this->session = &JFactory::getSession();
          $this->id = $this->session->get('Medical_Registry_id');
          $this->data = $this->session->get('Medical_Registry_data');
          
          if(count($this->data) == null){
              Aut::setAutDataUser();
          }
          
          
          if($this->id != null){ 
              return true;
          }
          else{
              return false;
          }
      }
      
      //Метод отмены аутентификации.
      public function logout(){
          if($this->check() == true){
              $this->session = &JFactory::getSession();
              $this->session->destroy();  
          }
      }   
      
      
      //Мутод аутентификации.
      public function login(){
          Aut::actions();
          if(Aut::check() == false){
              print Aut::getForm();
              return false;
          }
          else{
              return true;
          }
      }
      
      //Метод обработки действий пользователя.
      function actions(){
          $this->session = &JFactory::getSession();
          $logout = JRequest::getVar('logout');    
          $login = JRequest::getVar('login');
          $pass = JRequest::getVar('pass');
          $LogInfo = &JLog::getInstance('Info_Medical_Registry.'.date('Y_m_d').'.log.php');
          $LogErrors = &JLog::getInstance('Error_Medical_Registry.'.date('Y_m_d').'.log.php');
          if($logout != ''){
              $this->logout();
          }
          if($login != '' && $pass != '' && Tinterface::Validation($login) && Tinterface::Validation($pass)){
              $arr = Aut::getDataUser($login, $pass);
              $this->data = $this->session->get('Medical_Registry_data');
              $this->data['sum_aut']++;
              $this->session->set('Medical_Registry_data', $this->data);                
              if(count($arr) == 1 && 
              $this->data['sum_aut'] <= 4 &&
              Tinterface::Validation($login) &&
              Tinterface::Validation($pass)
              ){
                  $this->session->set('Medical_Registry_id' ,$arr);
                  $this->data['sum_aut'] = 0;
                  $this->session->set('Medical_Registry_data', $this->data);
                  $LogInfo->addEntry(array('category' => 'LOGIN:', 'message' => 'authenticated user - '.$login)); 
              }
              else{
                  $LogErrors->addEntry(array('category' => 'LOGIN:', 'message' => 'Error authentication - '.$login));
                  if($this->data['sum_aut'] >= 4){
                      $LogErrors->addEntry(array('category' => 'LOGIN:', 'message' => 'Error. It exceeded the limit of the number of authentications. User - '.$login)); 
                  }
              }
              unset($_POST);
          }
      }
      
      //Метод вывда формы.
      function getForm(){
          $url = & JFactory::getURI(); 
          $ret = '
          <form action="'.$url.'" method="post" class="login"><br/>
           Login<input type="text" name="login" size="20"><br/>
           Pass<input type="password" name="pass" size="20"><br/>
           <input type="submit" value="Ввод">
          </form>
          ';
          return $ret;
      }
      
      //Метод установки данных неаутентифицированного пользователя.
      function setAutDataUser(){
          $arr['ip'] = $_SERVER['REMOTE_ADDR'];
          $arr['data'] = date("Y.m.d");
          $arr['time'] = date('H:i:s', gmdate('U'));
          $arr['sum_aut'] = 0;
          $arr['browser'] = getenv("HTTP_USER_AGENT");
          $arr['host'] = gethostbyaddr(getenv('REMOTE_ADDR'));
          $this->session->set('Medical_Registry_data', $arr);
      }
      
      
      
      
      //Метод получения данных о пользователе.
      public function getDataUser($login = null, $pass = null){
          if($login != null && $pass != null){
              $pass = md5($pass);
              $db = & JFactory::getDbo();
              $nameTable1 = $db->nameQuote('#__registry_login');
              $nameTable2 = $db->nameQuote('#__registry_role');
              $nameTable3 = $db->nameQuote('#__registry_sex');
              $nameTable4 = $db->nameQuote('#__registry_activation'); 
              $nameTable5 = $db->nameQuote('#__registry_specialty');
              $query = 'SELECT
                        '.$nameTable2.'.name_role,
                        '.$nameTable2.'.description_role,
                        '.$nameTable3.'.sex_ru,
                        '.$nameTable1.'.id_login,
                        '.$nameTable1.'.login_login,
                        '.$nameTable1.'.id_activation_login,
                        '.$nameTable1.'.id_role_login,
                        '.$nameTable1.'.id_sex_login,
                        '.$nameTable1.'.surname_login,
                        '.$nameTable1.'.name_login,
                        '.$nameTable1.'.patronymic_login,
                        '.$nameTable1.'.post_login,
                        '.$nameTable1.'.id_create_login,
                        '.$nameTable1.'.data_create_login,
                        '.$nameTable1.'.time_create_login,
                        '.$nameTable1.'.ip_create_login,
                        '.$nameTable1.'.id_modification_login,
                        '.$nameTable1.'.date_modification_login,
                        '.$nameTable1.'.time_modification_login,
                        '.$nameTable1.'.ip_modification_login,
                        '.$nameTable1.'.phone_login,
                        '.$nameTable1.'.cabinet_login,
                        '.$nameTable1.'.time_login,
                        '.$nameTable1.'.id_specialty_login,
                        '.$nameTable4.'.name_activation,
                        '.$nameTable5.'.ru_specialty
                        FROM
                        Joomla_registry_login
                        Inner Join '.$nameTable2.' ON '.$nameTable2.'.id_role ='.$nameTable1.'.id_role_login
                        Inner Join '.$nameTable3.' ON '.$nameTable3.'.id_sex = '.$nameTable1.'.id_sex_login
                        Inner Join '.$nameTable4.' ON '.$nameTable4.'.id_activation = '.$nameTable1.'.id_activation_login
                        Inner Join '.$nameTable5.' ON '.$nameTable1.'.id_specialty_login = '.$nameTable5.'.id_specialty
                        WHERE
                        '.$nameTable1.'.login_login = \''.$login.'\' AND
                        '.$nameTable1.'.pass_login = \''.$pass.'\' AND
                        '.$nameTable1.'.id_activation_login = 1';
              $db->setQuery($query);
              return $db->loadAssocList();
          }
      }
      
      
      
  }
?>
