<?php

class ConfigController extends Config{

  public function addConfigData($name, $value){
    $this->addConfig($name, $value);
  }

  public function updateConfigData($name, $value){
    $this->updateConfig($name, $value);
  }

  public function deleteParameter($name){
    $this->deleteParameterByName($name);
  }
}
?>
