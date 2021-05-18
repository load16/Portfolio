<?php
  //Класс rights.
  
  
  defined('_JEXEC') or die;   
  require_once (JPATH_COMPONENT.DS.'libraries'.DS.'interface.php');
  
  
      
  abstract class Rights{
      //Метод проверки прав по задаче.
      public function getRights($id_task = null, $message = false){
          if($id_task != null){ 
              $session = JFactory::getSession(); 
              $id_s = $session->get('Medical_Registry_id'); 
              $id_user = $id_s[0]['id_login'];
              $id_role = $id_s[0]['id_role_login'];
              $ret = Rights::getRightsAll($id_task, $id_user, $id_role);
              if($message != false && $ret != true){
                  $arr_message = Rights::getListTask();
                  foreach($arr_message as $key => $value){              //Плучаем значение задачи п коду.
                      if($id_task == $value['id']){
                          $m = $value['Task'];
                      }
                  }
                  unset($key, $value);
                  $m = mb_strtolower(JText::_($m));                     //Преобразуем строку в нижний регистр и переводим.  
                  JError::raiseWarning( 100, JText::_('REG_NO_RIGHTS').' '.$m.'!');    //Формируем сообщение.
                  $LogErrors = &JLog::getInstance('Error_Medical_Registry.'.date('Y_m_d').'.log.php'); 
                  Tinterface::LogEvents($LogErrors, 'RIGHTS:', 'No rights '.$m);
              }  
              return $ret;
          }
          else{
              return false;
          }    
      }
      
      
      
      //Мотод проверки прав по всем параметрам.
      public function getRightsAll($id_task = null, $id_user = null, $id_role = null){
          if(count(Rights::getRoleRights($id_task, $id_role)) != 0 || count(Rights::getUserRights($id_task, $id_user )) != 0){
              return true;
          }
          else{
              return false;
          }
      }
      
      
      
      //Метод проверки прав пользователя.
      public function getUserRights($id_task = null, $id_user = null){
          if($id_task != null && $id_user != null){
              $db = & JFactory::getDbo();
              $nameTable1 = $db->nameQuote('#__registry_user_rights');
              $nameTable2 = $db->nameQuote('#__registry_login'); 
              $query = 'SELECT
                        '.$nameTable1.'.id_task,
                        '.$nameTable1.'.id_user,
                        '.$nameTable2.'.surname_login,
                        '.$nameTable2.'.name_login,
                        '.$nameTable2.'.patronymic_login
                        FROM
                        '.$nameTable1.'
                        Inner Join '.$nameTable2.' ON '.$nameTable2.'.id_login = '.$nameTable1.'.id_user
                        WHERE
                        '.$nameTable1.'.id_task = '.$id_task.' AND
                        '.$nameTable1.'.id_user = '.$id_user.' OR
                        '.$nameTable1.'.id_task = '.$id_task.' AND
                        '.$nameTable1.'.id_user = 0';
              $db->setQuery($query);
              return $db->loadAssocList();
          }
      }
      
      
      //Метод получения списка задач.
      public function getListTask(){
          $db = & JFactory::getDbo();
          $nameTable = $db->nameQuote('#__registry_task'); 
          $query = 'SELECT
                    *
                    FROM
                    '.$nameTable;
          $db->setQuery($query);
          return $db->loadAssocList();
      }
      
      //Метод проверки прав роли.
      public function getRoleRights($id_task = null, $id_role = null){
          if($id_task != null && $id_role != null){
              $db = & JFactory::getDbo();
              $nameTable1 = $db->nameQuote('#__registry_role_rights');
              $nameTable2 = $db->nameQuote('#__registry_task'); 
              $query = 'SELECT
                        '.$nameTable1.'.id_task,
                        '.$nameTable1.'.id_role,
                        '.$nameTable2.'.Task,
                        '.$nameTable2.'.id
                        FROM
                        '.$nameTable1.'
                        Inner Join '.$nameTable2.' ON '.$nameTable2.'.id = '.$nameTable1.'.id_task
                        WHERE
                        '.$nameTable1.'.id_task = '.$id_task.' AND
                        '.$nameTable1.'.id_role = '.$id_role.' OR
                        '.$nameTable1.'.id_task = '.$id_task.' AND
                        '.$nameTable1.'.id_role = 0';
              $db->setQuery($query);
              return $db->loadAssocList();
          }
      }
      
      
  }
?>
