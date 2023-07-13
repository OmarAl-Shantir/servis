<?php

class Config extends Dbh{

// ------------View------------
  protected function getDatabyName($name) {
    $sql = "SELECT * FROM ".$this->dbPrefix."config WHERE `name` = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$name]);
    $results = $stmt->fetchAll();
    $res = array();
    foreach ($results as $row) {
      foreach ($row as $key=>$value){
        $res[$key] = $value;
      }
    }
    return $res;
  }

  protected function getAllData(){
    $sql = "SELECT * FROM ".$this->dbPrefix."config WHERE 1";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([]);
    $results = $stmt->fetchAll();
    $res = array();
    foreach ($results as $row) {
      $res[$row['name']] = array("value" => $row['value'], "description" => $row['description'],"hint" =>  $row['hint']);
    }
    return $res;
  }

// ------------Controller------------

  protected function addConfig($name, $value){
    $sql = "INSERT INTO `".$this->dbPrefix."config`(`name`, `value`) VALUES (?,?)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$email, $pass, $fullname, $role]);
  }

  protected function updateConfig($name, $value){
    $sql = "UPDATE `".$this->dbPrefix."config` SET `value` = ? WHERE `name` = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$value, $name]);
  }

  protected function deleteParameterByName($name){
    $sql = "DELETE FROM `".$this->dbPrefix."config` WHERE `name` = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$name]);
  }
}
?>
