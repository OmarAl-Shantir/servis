<?php

/*
pridávanie fotiek
  štítok prepravy | cena prepravy
  predajný doklad
  reklamačný list
  foto výrobku

*/
class Zadavatel extends Dbh{

// ------------View------------
  protected function ispredajca($id_zadavatel){
      $sql = "SELECT `predajca`, `zakaznik` FROM ".$this->dbPrefix."service_zadavatel WHERE `id_zadavatel` = ?";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$id_zadavatel]);
      $results = $stmt->fetchAll();
      return $results;
  }

  protected function getpredajca($id_zadavatel){
    if ($id_record == NULL) {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_zadavatel WHERE `predajca` = 1";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([]);
    } else {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_zadavatel WHERE `id_zadavatel` = ? AND `predajca` = 1";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$id_zadavatel]);
    }
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getZakaznik($id_zadavatel){
    if ($id_record == NULL) {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_zadavatel WHERE `zakaznik` = 1";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([]);
    } else {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_zadavatel WHERE `id_zadavatel` = ? AND `zakaznik` = 1";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$id_zadavatel]);
    }
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getData($id_zadavatel){
    if ($id_zadavatel == NULL) {
      return "neplatný vstup";
    } else {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_zadavatel WHERE `id_zadavatel` = ?";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$id_zadavatel]);
      $results = $stmt->fetchAll();
    }
    return $results;
  }

// ------------Controller------------
  protected function updateZadavatel($id_zadavatel, $meno, $firma, $firma_popis, $ico, $dic, $ic_dph, $ulica_cislo, $psc, $mesto, $telefon, $email1, $email2, $email3, $predajca, $zakaznik){
    $sql = "UPDATE `".$this->dbPrefix."service_zadavatel` SET `meno` = ?, `firma` = ?, `firma_popis` = ?, `ico` = ?, `dic` = ?, `ic_dph` = ?, `ulica_cislo` = ?, `psc` = ?, `mesto` = ?, `telefon` = ?, `email1` = ?, `email2` = ?, `email3` = ?, `predajca` = ?, `zakaznik` = ? WHERE `id_zadavatel` = ?";

    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$meno, $firma, $firma_popis, $ico, $dic, $ic_dph, $ulica_cislo, $psc, $mesto, $telefon, $email1, $email2, $email3, $predajca, $zakaznik, $id_zadavatel]);
  }

  protected function addZadavatel($meno, $firma, $firma_popis, $ico, $dic, $ic_dph, $ulica_cislo, $psc, $mesto, $telefon, $email1, $email2, $email3, $predajca, $zakaznik){
    $sql = "INSERT INTO `".$this->dbPrefix."service_zadavatel`(`meno`, `firma`, `firma_popis`, `ico`, `dic`, `ic_dph`, `ulica_cislo`, `psc`, `mesto`, `telefon`, `email1`, `email2`, `email3`, `predajca`, `zakaznik`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$meno, $firma, $firma_popis, $ico, $dic, $ic_dph, $ulica_cislo, $psc, $mesto, $telefon, $email1, $email2, $email3, $predajca, $zakaznik]);
    $id = $con->lastInsertId();
    return $id_zadavatel;
  }
}
 ?>
