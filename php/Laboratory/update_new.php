<?php
  //Скрипт отвественный обновление базы новых анализов.
  gc_collect_cycles();
  include_once 'classes/config.php'; 
  include_once 'classes/register.php';
  include_once 'classes/files.php'; 
  
  $config = config::getInstance(); 
  $files = new files();
  $reg = new register();
    
  //Обновляем базу новых анализов.
  $reg->updateBaseAnalizFile($files->getArryBaseFile($config->conf['patchseach']));
  gc_collect_cycles(); 
  
?>
