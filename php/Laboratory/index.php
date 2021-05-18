<?php
  gc_collect_cycles();	
  include_once 'classes/room.php'; 
  $router  = new router();
  //print $router->getCurrentUrl();
  $obj = $router->getRoute();
  unset($obj);
  gc_collect_cycles();

  
  //print '<pre>';
  //print_r($_SESSION);
  //print_r($_SERVER);
  //print '</pre>';
?>
