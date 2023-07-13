<?php

class ConfigView extends Config{

  public function getConfigbyName($name) {
    $res = $this->getDatabyName($name);
    return $res;
  }

  public function get_configby_value($name) {
    $res = $this->getDatabyName($name);
    return $res['value'];
  }

  public function getAllConfig(){
    $res = $this->getAllData();
    return $res;
  }

  public function generateEmail($content){
    
  }
}
?>
