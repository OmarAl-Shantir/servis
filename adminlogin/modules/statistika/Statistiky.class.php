<?php

class Statistiky extends Dbh
{
  protected function getFinishedServices($limit, $typ){
    if (is_null($limit)) {
      $sql = "SELECT SI.`id_service_item`, SI.`id_vybavuje`, A.`fullname` FROM ".$this->dbPrefix."service_item SI
      LEFT JOIN ".$this->dbPrefix."service_history SH
      ON SH.status = SI.id_stav_opravy AND SH.id_service = SI.id_service_item
      LEFT JOIN ".$this->dbPrefix."admins A
      ON A.id_admin = SI.id_vybavuje
      WHERE SI.id_stav_opravy IN (9, 16, 17) ";
      $sql .= (isset($typ))?"AND SI.`id_typ` = ? ":"";
      $sql .="ORDER BY SI.id_service_item ";
      $stmt = $this->connect()->prepare($sql);
      if(isset($typ)){
        $stmt->execute([$typ]);
      } else {
        $stmt->execute([]);
      }
    } else {
      $limit[0].= ' 00:00:00.000';
      $limit[1].= ' 23:59:59.999';
      $sql = "SELECT SI.`id_service_item`, SI.`id_vybavuje`, A.`fullname` FROM ".$this->dbPrefix."service_item SI
      LEFT JOIN ".$this->dbPrefix."service_history SH
      ON SH.status = SI.id_stav_opravy AND SH.id_service = SI.id_service_item
      LEFT JOIN ".$this->dbPrefix."admins A
      ON A.id_admin = SI.id_vybavuje
      WHERE SI.id_stav_opravy IN (9, 16, 17) AND SH.`date_action` >= ? AND SH.`date_action` <= ? ";
      $sql .= (isset($typ))?"AND SI.`id_typ` = ? ":"";
      $sql .= "ORDER BY SI.id_service_item ";
      $stmt = $this->connect()->prepare($sql);
      if(isset($typ)){
        $limit[2] = $typ;
      }
      $stmt->execute($limit);
    }
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
      $res[$row['id_service_item']] = array($row['id_vybavuje'] => $row['fullname']);
    }
    return $res;
  }

  protected function getImportByEmployee($employee, $ids){
    $sql = "SELECT SIO.jednotkova_cena , SIO.mnozstvo FROM ".$this->dbPrefix."service_item_operations SIO
      LEFT JOIN ".$this->dbPrefix."service_item SI
      ON SIO.id_service_item = SI.id_service_item
      LEFT JOIN ".$this->dbPrefix."service_history SH
      ON SH.id_service = SIO.id_service_item
      WHERE SI.id_vybavuje = ? AND SIO.action = 'Import' AND SIO.id_service_item IN (".implode(",",array_keys($ids)).")
      GROUP BY SIO.id_action";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$employee]);
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getExportByEmployee($employee, $ids){
    $sql = "SELECT SIO.jednotkova_cena , SIO.mnozstvo FROM ".$this->dbPrefix."service_item_operations SIO
      LEFT JOIN ".$this->dbPrefix."service_item SI
      ON SIO.id_service_item = SI.id_service_item
      LEFT JOIN ".$this->dbPrefix."service_history SH
      ON SH.id_service = SIO.id_service_item
      WHERE SI.id_vybavuje = ? AND SIO.action = 'Export' AND SIO.id_service_item IN (".implode(",",array_keys($ids)).")
      GROUP BY SIO.id_action";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$employee]);
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getCenaPraceByEmployee($employee, $ids){
    $sql = "SELECT SIO.jednotkova_cena , SIO.mnozstvo FROM ".$this->dbPrefix."service_item_operations SIO
      LEFT JOIN ".$this->dbPrefix."service_item SI
      ON SIO.id_service_item = SI.id_service_item
      LEFT JOIN ".$this->dbPrefix."service_history SH
      ON SH.id_service = SIO.id_service_item
      WHERE SI.id_vybavuje = ? AND SIO.action = 'Cena práce' AND SIO.id_service_item IN (".implode(",",array_keys($ids)).")
      GROUP BY SIO.id_action";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$employee]);
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getNDByEmployee($employee, $ids){
    $sql = "SELECT SIO.jednotkova_cena , SIO.mnozstvo FROM ".$this->dbPrefix."service_item_operations SIO
      LEFT JOIN ".$this->dbPrefix."service_item SI
      ON SIO.id_service_item = SI.id_service_item
      LEFT JOIN ".$this->dbPrefix."service_history SH
      ON SH.id_service = SIO.id_service_item
      WHERE SI.id_vybavuje = ? AND (SIO.action != 'Import' AND SIO.action != 'Export' AND SIO.action != 'Cena práce') AND SIO.id_service_item IN (".implode(",",array_keys($ids)).")
      GROUP BY SIO.id_action";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$employee]);
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getCasOdPrideleniaByEmployee($employee, $ids){
    $sql = "SELECT SH.id_service, SH.`date_action` FROM ".$this->dbPrefix."service_item SI
      LEFT JOIN ".$this->dbPrefix."service_history SH
      ON SH.id_service = SI.id_service_item
      WHERE SI.id_vybavuje = ? AND SI.id_service_item IN (".implode(",",array_keys($ids)).")
      GROUP BY SI.id_service_item";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$employee]);
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getDatumPriradenia($id_service){
    $sql = "SELECT SH.`date_action` FROM ".$this->dbPrefix."service_history SH
      WHERE SH.`id_service` = ? AND SH.status = 3;";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$id_service]);
    $results = $stmt->fetchAll();
    return $results[0]['date_action'];
  }

  protected function getCasOdPrijatia($ids){
    if(empty($ids)){
      return;
    } else {
      $sql = "SELECT SH.`id_service`, SH.`date_action` FROM ".$this->dbPrefix."service_item SI
        LEFT JOIN ".$this->dbPrefix."service_history SH
        ON SH.id_service = SI.id_service_item
        WHERE SI.id_service_item IN (".implode(",",array_keys($ids)).") AND SH.status IN (9, 16, 20, 21)
        GROUP BY SI.id_service_item";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute();
      $results = $stmt->fetchAll();
      return $results;
    }
  }

  protected function getDatumPrijatia($id_service){
    $sql = "SELECT SH.`date_action` FROM ".$this->dbPrefix."service_history SH
      WHERE SH.`id_service` = ? AND SH.status = 2";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$id_service]);
    $results = $stmt->fetchAll();
    return $results[0]['date_action'];
  }

  protected function getSposovUkonceniaByEmployee($employee, $ids){
    $sql = "SELECT SS.description FROM ".$this->dbPrefix."service_item SI
      LEFT JOIN ".$this->dbPrefix."service_history SH
      ON SH.id_service = SI.id_service_item
      LEFT JOIN ".$this->dbPrefix."service_status SS
      ON SS.id_status = SH.status
      WHERE SI.id_vybavuje = ? AND SH.status IN (7,8,9,10,11,12,13,14,15,17) AND SI.id_service_item IN (".implode(",",array_keys($ids)).")
      GROUP BY SI.id_service_item";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$employee]);
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getUkoncenia($ids){
    if(empty($ids)){
      return;
    } else {
      $sql = "SELECT SS.description FROM ".$this->dbPrefix."service_item SI
        LEFT JOIN ".$this->dbPrefix."service_history SH
        ON SH.id_service = SI.id_service_item
        LEFT JOIN ".$this->dbPrefix."service_status SS
        ON SS.id_status = SH.status
        WHERE SH.status IN (7,8,9,10,11,12,13,14,15,17) AND SI.id_service_item IN (".implode(",",array_keys($ids)).")";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute();
      $results = $stmt->fetchAll();
      return $results;
    }
  }

  protected function getPrijatia($ids){
    if(empty($ids)){
      return;
    } else {
      $sql = "SELECT SD.description FROM ".$this->dbPrefix."service_item SI
        LEFT JOIN ".$this->dbPrefix."service_delivery SD
        ON SD.id_delivery = SI.id_delivery_in
        WHERE SI.id_service_item IN (".implode(",",array_keys($ids)).")";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute();
      $results = $stmt->fetchAll();
      return $results;
    }
  }

  protected function getModely($ids){
    if(empty($ids)){
      return;
    } else {
      $sql = "SELECT SI.product_ref FROM ".$this->dbPrefix."service_item SI
        WHERE SI.id_service_item IN (".implode(",",array_keys($ids)).")
        GROUP BY SI.id_service_item";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute();
      $results = $stmt->fetchAll();
      return $results;
    }
  }

  protected function getReklamujuci($ids){
    if(empty($ids)){
      return;
    } else {
      $sql = "SELECT SZ.firma FROM ".$this->dbPrefix."service_item SI
        LEFT JOIN ".$this->dbPrefix."service_zadavatel SZ
        ON SZ.id_zadavatel = SI.id_objednavatel
        WHERE SI.id_service_item IN (".implode(",",array_keys($ids)).")
        GROUP BY SI.id_service_item";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute();
      $results = $stmt->fetchAll();
      return $results;
    }
  }
}

?>
