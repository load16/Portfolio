<?php
  //Класс ответственный за деаутентифиацию пользователей.
  include_once 'classes/login.php';
  class logout extends login{
	  
	  
	  //Конструктор класса.
	  function __construct(){
	  	  session_start();                              						//Стартуем сесию.
		  $url = router::getDefaultUrl();										//Получаем URL по умолчанию.
		  unset($_SESSION['login']);											//Сбрасываем сесию.
		  header('Location:'.$url);												//Перенаправляем туда.
		  
	  }
  }
?>
