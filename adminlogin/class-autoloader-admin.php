<?php
spl_autoload_register('autoLoaderAdmin');

//if (!function_exists("autoLoaderAdmin")){
  function autoLoaderAdmin($className){
    $modules = scandir(ABSPATH."adminlogin/modules");
    unset($modules[0]);unset($modules[1]);
    $paths[] = ABSPATH."adminlogin/classes/";
    $paths[] = ABSPATH."adminlogin/classes/view/";
    $paths[] = ABSPATH."adminlogin/classes/controller/";
    foreach ($modules as $module){
      $paths[] = ABSPATH."adminlogin/modules/".$module."/";
    }
    //PHPMailer
    $paths[] = ABSPATH."adminlogin/theme/vendor/phpmailer/";
    //
    $extension = ".class.php";
    foreach ($paths as $path){
      $fileName = $path . $className . $extension;
      $fileNameVendor = $path . $className . ".php";
      //$fileName = strtolower($fileName);
      if (!file_exists($fileName)){
        if (!file_exists($fileNameVendor)){
          continue;
        } else {
          include_once $fileNameVendor;
        }
      } else {
        include_once $fileName;
      }

    }
  }
//}
