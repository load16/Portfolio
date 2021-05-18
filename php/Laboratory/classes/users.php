<?php
  //Класс ответственный за аутентифиацию пользователей.
  include_once 'classes/room.php';
  include_once 'library/select.php';
  include_once 'classes/log.php';
  class users extends room{
  	  
  	  
  	  var $tools;
  	  var $heat;														//Заглавие задачи.
  	  var $footer;														//Фитер.
  	  var $content;
  	  var $log;
  	  
  	  
  	  
  	  //Конструктор класса.
	  function __construct(){
	  	  parent::__construct();										//Выполнение конструктора предшественника.	 
		  $this->log = new log('true');									//Создание объекта логирования.
		  users::action_users();										//Обрабатываем действие пользоватей.
		  print room::getView();										//Отображаем кабинет.
	  }
	  
	  
	  //Метод обработки действия пользователя.
	  private function action_users(){
	  	  $listUsers = users::getListUsers();
	  	  $selectUser = users::getSelectUsers($listUsers);
	  	  $this->content = $selectUser;
	  	  
	  	  if(isset($_POST['user'])){									//Обработка выбора пользователя.
	  	  	  $id = users::getIdUser($_POST['user']);
	  	  	  $formUser = users::getFormUser($_POST['user']);			//Формируем форму редектирования.
	  	  	  room::outContent($formUser);								//Показываем форму.
	  	  	  $_SESSION['users']['id'] = $id;							//Фиксируем ИД.
	  	  	  //Формируем данные для логирования.
	  	  	  $log = users::getDataUser($_POST['user']);
	  	  	  $_SESSION['users']['log'] = $log['0'];
	  	  }
	  	  if(															//Обработка удаления.
	  	  isset($_POST['deluser'])
	  	  && $_SESSION['users']['id'] != ''
	  	  ){ 
			  
			  if($_SESSION['users']['id'] == '1'){
			  	  unset($_SESSION['users']['id']);
				  print '<script> alert(\'Создатель не может быть удален!\') </script>';
			  }
			  else{
				  users::DeleteUser($_SESSION['users']['id']);
				  $listUsers = users::getListUsers();
	  			  $selectUser = users::getSelectUsers($listUsers);
	  			  $this->content = $selectUser;
	  			   $this->log->sendLog('Пользователь удален!', $_SESSION['users']['log']);
				  unset($_SESSION['users']);
				  print '<script> alert(\'Пользователь удален!\') </script>';
			  } 
	  	  }
	  	  if(isset($_POST['newuser'])){									//Обработка создания пользователя.
			  $formUser = users::getFormUser();							//Формируем форму редектирования.
	  	  	  room::outContent($formUser);
	  	  	  $_SESSION['users']['newuser'] = $_POST['newuser'];		//Фиксируем флаг нового пользователя.
	  	  	  
	  	  }
	  	  if(isset($_POST['save'])){									//Обработка сохранения.
			  if($_SESSION['users']['newuser'] != ''){					//Сохранение нового пользователя.
				  if(
				  users::ValidationCodeData($_POST)						//Валидация на враждебный код.
				  &&
				  users::ValidationData($_POST['name_user'], $_POST['login_user'])//Валидация на коректность.
				  ){
				  	  users::UpdateUser('', $_POST['name_user'], $_POST['login_user'], $_POST['pass_user'], $_POST['mail_user']);
				  	  $listUsers = users::getListUsers();
	  				  $selectUser = users::getSelectUsers($listUsers);
	  				  $this->content = $selectUser;
	  				  unset($_POST['pass_user']);
	  				  $this->log->sendLog('Пользователь создан!', $_POST);
				  	  unset($_SESSION['users']);
					  print '<script> alert(\'Пользователь создан!\') </script>';
				  }
			  }
			  
			  if($_SESSION['users']['id'] != ''){						//Обработка изменений пользователя.
				  if(
				  users::ValidationCodeData($_POST)
				  && users::ValidationData($_POST['name_user'], $_POST['login_user'], $_SESSION['users']['id'])
				  ){
					  users::UpdateUser($_SESSION['users']['id'], $_POST['name_user'], $_POST['login_user'], $_POST['pass_user'], $_POST['mail_user']);
				  	  $listUsers = users::getListUsers();
	  				  $selectUser = users::getSelectUsers($listUsers);
	  				  $this->content = $selectUser;
	  				  unset($_POST['pass_user']);						//Удаляем рароль для лога.
	  				  $this->log->sendLog('Пользователь обновлен!', $_POST);
				  	  unset($_SESSION['users']);
					  print '<script> alert(\'Пользователь обновлен!\') </script>';
				  }
			  }
			  unset($_SESSION['users']);								//Сброс после сохранения.
	  	  }
	  	  if(isset($_POST['cancel'])){									//Обработка отмены редактирования.
			  unset($_SESSION['users']);
	  	  }
	  	  $this->heat = 'Управления пользователями системы';
	  	  $this->tools = room::getForm(users::getTools());
	  	  room::outContent(users::getView());							//Вывод всего модуля.
	  }
	  
	  
	  //Метод отображения модуля.
	  public function getView(){
		  return room::getViewModule();
	  }
	  
	  
	  //Метод получения панели инстркментов.
	  private function getTools(){
		  $ret .= '<input type="submit" name="newuser" value="Создать">';
		  if($_SESSION['users']['id'] != ''){										//Если есть выбранный пользователь, то показываем кнопку.
			  $ret .= '<input type="submit" name="deluser" value="Удалить">';
		  }
		  return $ret;
	  }
	  
	  
	  
	  //Метод получения меню SELECT пользователей.
	  private function getSelectUsers($listUsers = null){
		  if($listUsers != null){
			  $url = router::getCurrentUrl();
			  $select = new select();
			  $ret = $select('user', 'name', $listUsers, 5)."\n";
			  $ret = room::getForm($ret);
			  return $ret;
		  }
	  }
	  
	  
	  //Метод получения формы для редактирования пользовател.
	  private function getFormUser($Name = null){
		   $user = users::getDataUser($Name);
		   $ret .= 'Имя:<input type="text" name="name_user" value="'.$user['0']['name'].'" size="40"></br>'."\n";
		   $ret .= 'login:<input type="text" name="login_user" value="'.$user['0']['login'].'" size="40"></br>'."\n";
		   $ret .= 'Pass:<input type="password" name="pass_user" value="" size="40"></br>';
		   $ret .= 'Mail:<input type="text" name="mail_user" value="'.$user['0']['mail'].'" size="40"></br>';
		   $ret .= '<input type="submit" name="save" value="Сохранить"><input type="submit" name="cancel" value="Отмена">'."\n";
		   $ret = room::getForm($ret);
		   return $ret;
	  }
	  
	  
	  //Удаление данных пользователя.
	  private function DeleteUser($id = null){
		  if($id != null){
			  $query = 'DELETE FROM 
			  			login
			  			WHERE 
			  			`id` = \''.$id.'\'';
			  if($id == '1'){														//Проверка на предмет создателя.
				  print '<script> alert(\'Удалить создателя нельзя!\') </script>';
				  return;
			  }
			  else{
				  return SqlAdapter::select_sql($query);
			  }
		  }
	  }
	  
	  
	  //Обновление данных пользователя.
	  private function UpdateUser($id = null, $name = null, $login = null, $pass = null, $mail = null){
		  if($name != null && $login != null && $mail != null){ 
			  if($id != null){
			  	  if($pass != null){
			  	  	  $pass = md5($pass);
					  $query = 'UPDATE LOW_PRIORITY
				  				`login`
				  				SET
				  				`mail` = \''.$mail.'\',
				  				`name` = \''.$name.'\',
				  				`login` = \''.$login.'\',
				  				`pass` = \''.$pass.'\'
				  				WHERE
								`id` = \''.$id.'\'';
			  	  }
			  	  else{
					  $query = 'UPDATE LOW_PRIORITY
				  				`login`
				  				SET
				  				`mail` = \''.$mail.'\',
				  				`name` = \''.$name.'\',
				  				`login` = \''.$login.'\'
				  				WHERE
								`id` = \''.$id.'\'';
			  	  }
			  }
			  else{
			  	  if($pass != null){
					  $pass = md5($pass);
					  $query = 'INSERT INTO
				  				`login`
				  				SET
				  				`mail` = \''.$mail.'\',
				  				`name` = \''.$name.'\',
				  				`login` = \''.$login.'\',
				  				`pass` = \''.$pass.'\'';
			  	  }
			  }
			  if($query != ''){ 
				  return SqlAdapter::select_sql($query);
			  }
		  }
	  }
	  
	  
	  //Метод валидации вводимых данных.
  	  public function ValidationCodeData($var = null){  
		  return room::ValidationCodeData($var);  
  	  }
  	  
  	  
  	  
  	  //Метод валидации данных на коректность.
  	  private function ValidationData($name = null, $login = null, $id = null){
		  if($name != null && $login != null){
			  $arr = users::getListUsers();
			  if($arr != ''){
				  foreach($arr as $key => $value){								//Обходим массив.
					  if(														//Находим повторение.
					  $login == $value['login'] && $id != $value['id']
					  ){
					  	  print '<script> alert(\'Имена не должны повторяться!\') </script>';
					  	  return false;					  	  	
					  }
					  if($login == $value['login'] && $id != $value['id']
					  ){
					  	  print '<script> alert(\'Логин не должен повторяться!\') </script>';	 
					  	  return false;					  						//Возвращаем результат.	 
					  }
				  }
				  return true;
			  }
		  }
		  print '<script> alert(\'Поля не должны быть пустыми!\') </script>';
		  return false;
  	  }
  	  
  	  
  	  
	  
	  //Метод получения данных пользователя по Имени
	  private function getDataUser($Name = null){
		  if($Name != null){
			  $query = 'SELECT
						*
						FROM
						`login`
						WHERE
						`name` = \''.$Name.'\'';
			  return SqlAdapter::select_sql($query);  
		  }
	  }
	  
	  
	  //Метод получения данных пользователя по Логин
	  private function getDataUserlogin($login = null){
		  if($Name != null){
			  $query = 'SELECT
						*
						FROM
						`login`
						WHERE
						`login` = \''.$login.'\'';
			  return SqlAdapter::select_sql($query);  
		  }
	  }
	  
	  
	  
	  //Метод получения данных пользователя по ИД
	  private function getDataUserId($id = null){
		  if($id != null){
			  $query = 'SELECT
						*
						FROM
						login
						WHERE
						`id` = \''.$id.'\'';
			  return SqlAdapter::select_sql($query);  
		  }
	  }
	  
	  //Метод получения данных ИД пользователя по Имени
	  private function getIdUser($Name = null){
		  if($Name != null){
			  $arr = users::getDataUser($Name);
			  return $arr['0']['id'];
		  }
	  }
	  
	  
	  
	  //Метод получения списка пользователей.
	  private function getListUsers(){
	  	  $query = "SELECT
		  			*
					FROM
					login";
		  return SqlAdapter::select_sql($query);
	  }
	  
	  
	  
  }
?>
