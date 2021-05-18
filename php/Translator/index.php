<?php
  gc_collect_cycles();	 
  include_once 'classes/router.php';
  $router  = new router();
  $obj = $router->getRoute();
  unset($obj);
  gc_collect_cycles();

  
  //print '<pre>';
  //print_r($_SESSION);
  //print_r($_SERVER);
  //print '</pre>';
?>
