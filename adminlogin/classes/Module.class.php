<?php

class Module extends Dbh{

// ------------View------------
  protected function getModules() {
    $sql = "SELECT * FROM ".$this->dbPrefix."modules WHERE 1";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([]);
    $results = $stmt->fetchAll();
    $res = array();
    foreach ($results as $row) {
      $res[$row['id_module']] = array("name" => $row['name'], "description" => $row['description'], "path" => $row['path'], "icon" => $row['icon'], "position" => $row['position'], "active" => $row['active']);
    }
    return $res;
  }
}
