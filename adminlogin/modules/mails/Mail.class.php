<?php

class Mail extends Dbh{

// ------------View------------

  protected function getAllRecords($limit){
    $sql = "SELECT * FROM ".$this->dbPrefix."mail WHERE 1";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([]);
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getData($id_mail){
    $sql = "SELECT * FROM ".$this->dbPrefix."mail WHERE `id_mail` = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$id_mail]);
    $results = $stmt->fetchAll();
    return $results;
  }

  /*protected function getVolneLikvidacie(){
    $sql = "SELECT * FROM ".$this->dbPrefix."service_item SI LEFT JOIN ".
    $this->dbPrefix."service_history SH
    ON SI.id_service_item = SH.id_service
    WHERE (IsNull(SI.cislo_prepravy_likvidacia) OR SI.cislo_prepravy_likvidacia = '') AND  (SH.status = 9 OR SH.status = 10) AND IsNull(SI.kat_ukoncenia)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getVolneForCat($cat){
    $sql = "SELECT * FROM ".$this->dbPrefix."service_item SI LEFT JOIN ".
    $this->dbPrefix."service_history SH
    ON SI.id_service_item = SH.id_service
    WHERE (IsNull(SI.cislo_prepravy_likvidacia) OR SI.cislo_prepravy_likvidacia = '') AND (SI.id_stav_opravy = 17) AND (SH.status = 17) AND (SI.kat_ukoncenia = ?)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$cat]);
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getReklamacieByTyp(){
    $sql = "SELECT * FROM ".$this->dbPrefix."service_item SI LEFT JOIN ".
    $this->dbPrefix."service_history SH
    ON SI.id_service_item = SH.id_service
    WHERE (IsNull(SI.cislo_prepravy_out) OR SI.cislo_prepravy_likvidacia = '') AND  (SH.status = 9 OR SH.status = 10)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function inPackage($id_balik){
    if($id_balik == NULL){
      return "neplatný vstup";
    }
    $sql = "SELECT * FROM ".$this->dbPrefix."service_item WHERE `id_balik` = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$id_balik]);
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getBalikAll($id_balik){
    if ($id_balik == NULL) {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_balik WHERE 1";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([]);
    } else {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_balik WHERE `id_balik` = ?";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$id_balik]);
    }
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getBalik($id_balik){
    if ($id_balik == NULL) {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_balik WHERE `kategoria` IS NULL";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([]);
    } else {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_balik WHERE `id_balik` = ? AND `kategoria` IS NULL";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$id_balik]);
    }
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getBalikKat($id_balik){
    if ($id_balik == NULL) {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_balik WHERE `kategoria` IS NOT NULL";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([]);
    } else {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_balik WHERE `id_balik` = ? AND `kategoria` IS NOT NULL";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$id_balik]);
    }
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getBalikByName($cislo_balika){
    $sql = "SELECT * FROM ".$this->dbPrefix."service_balik WHERE `cislo_balika` = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$cislo_balika]);
    $results = $stmt->fetchAll();
    return $results[0];
  }

 */
// ------------Controller------------

  protected function addTemplate($subject, $filename){
    $sql = "INSERT INTO `".$this->dbPrefix."mail`(`subject`, `filename`) VALUES (?, ?)";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$subject, $filename]);
    $id = $con->lastInsertId();
    return $id;
  }

  protected function updateTemplate($id_mail, $subject, $filename){
    $sql = "UPDATE `".$this->dbPrefix."mail` SET `subject` = ? , `filename` = ? WHERE `id_mail` = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$subject, $filename, $id_mail]);
    return 1;
  }

  /*protected function removeFromPackageLikvidacia($id_product){
    $sql = "UPDATE `".$this->dbPrefix."service_item` SET `cislo_prepravy_likvidacia` = NULL, `id_balik` = NULL WHERE `id_service_item` = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$id_product]);
  }

  protected function addToPackageLikvidacia($id_product, $id_balik){
    $balik = $this->getBalikAll($id_balik);
    $sql = "UPDATE `".$this->dbPrefix."service_item` SET `cislo_prepravy_likvidacia` = ?, `id_balik` = ? WHERE `id_service_item` = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$balik[0]['cislo_balika'], $id_balik, $id_product]);
  }*/
}
 ?>
