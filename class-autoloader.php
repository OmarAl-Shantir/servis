<?php

spl_autoload_register('autoLoader');

//if (!function_exists("autoLoader")){
  function autoLoader($className){
    $paths[] = ABSPATH."classes/";
    $paths[] = ABSPATH."classes/view/";
    $paths[] = ABSPATH."classes/controller/";
    $extension = ".class.php";
    foreach ($paths as $path){
      $fileName = $path . $className . $extension;
      //$fileName = strtolower($fileName);
      if (!file_exists($fileName)) continue;
      include_once $fileName;
    }
  }
//}
