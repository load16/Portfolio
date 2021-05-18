<?php
  //Класс ответственный за аутентифиацию пользователей.
  include_once 'classes/router.php';
  class login extends router{
  	  
  	  
  	  //Конструктор класса.
	  function __construct(){
		  session_start();                              //Стартуем сесию.
		  parent::__construct();						//Выполнение конструктора предшесвенника.
		  login::action_login();						//Обрабатываем действия пользователя.
		  login::aut();									//Аутентифицируем пользователя.
	  }
	  
	  //Деструктор класса.
	  function __destruct(){
	  	  parent::__destruct();
		  gc_collect_cycles();
	  }
	  
	  
	  
	  //Метод обработки действия пользователя.
	  private function action_login(){
		  //$l = $_POST['login'];							//Фиксируем посты.
		  //$p = $_POST['pass'];
		  
		  if(isset($_POST['login']) && isset($_POST['pass'])){		//Если есть посты, то проверяем их.
			  login::check($_POST['login'], $_POST['pass']);
		  }
		  if(isset($_POST['logout'])){					//Если нажато розлогиница, то убиваем сесию.
			  unset($_SESSION['login']);
		  }
	  }
	  
	  
	  //Метод аутентификации пользователя.
	  public function aut(){
		  $arr = $_SESSION['login'];
		  if($arr == ''){
			  login::getLogin();
		  }
		  unset($arr);
	  }
	  
	  //Метод проверки наличия аутентифицированого ползователя
	  public function autUser(){
		  session_start();
		  $arr = $_SESSION['login'];
		  if($arr != ''){
			  return true;
		  }
		  else{
			  return false;
		  }
	  }
	  
	  
	  //Метод отображения запроса на аутентыфикацию.
	  private function getLogin(){
	  	  $url = router::getCurrentUrl();
		  print '<div>
		  		<form action="'.$url.'" method="post">
				 <p>login: <input type="text" name="login" /></p>
				 <p>Pass: <input type="password" name="pass" /></p>
				 <p><input name="Enter" type="submit"></p>
				</form>
				</div>';
		  die();
	  }
	  
	  
	  //Метод проверки учетных данных
	  private function check($login = null, $pass = null){
		  if($login != null && $pass != null){
			  $r = login::getDate($login, $pass);
			  if(count($r) == 1){
			  	  $_SESSION['login'] = $r['0'];
				  return true;
			  }   
			  else{
			  	  unset($_SESSION['login']);
				  return false;
			  }
		  }
	  }
	  
	  
	  //Метод получения данных с базы
	  private function getDate($login = null, $pass = null){
	  	  $pass = md5($pass);
		  if($login != null && $pass != null){
			  $query = "SELECT
						*
						FROM
						login
						WHERE
						login = '".$login."' AND
						pass = '".$pass."'";          
			  return SqlAdapter::select_sql($query);
		  }
	  }
	  
  }
?>
