<?php
  //Скрипт отвественный обновление полной базы анализов.
  gc_collect_cycles();
  ini_set('memory_limit', '5120M');                                         //Увеличить лимит использованной памяти до 4096Мб.
  include_once 'classes/config.php'; 
  include_once 'classes/register.php';
  include_once 'classes/files.php'; 
  
  $config = config::getInstance(); 
  $files = new files();
  $reg = new register();
  
  $os = PHP_OS;                                                         //Определяем ОС.
  $os = strtolower($os);
    
  //Обновляем полную базу анализов.
  $reg->updateBaseAnalizFile($files->getArryBaseFile($config->conf['patchfullseach']), true);           
  unset($arr_dir, $os, $config, $files, $reg);                          //Штатный сброс.
  gc_collect_cycles(); 
  
?>
