<?php

/*
pridávanie fotiek
  štítok prepravy | cena prepravy
  predajný doklad
  reklamačný list
  foto výrobku

*/
class Service extends Dbh{

// ------------View------------
  protected function getAllDelivery($id_delivery) {
    $sql = (is_null($id_delivery))?"SELECT * FROM ".$this->dbPrefix."service_delivery WHERE 1":"SELECT * FROM ".$this->dbPrefix."service_delivery WHERE id_delivery=?";
    $stmt = $this->connect()->prepare($sql);
    if(is_null($id_delivery)){
      $stmt->execute([]);
    } else {
      $stmt->execute([$id_delivery]);
    }
    $results = $stmt->fetchAll();
    $res = array();
    foreach ($results as $row) {
      $res[$row['id_delivery']] = array("description" => $row['description'], "price" => $row['price']);
    }
    return $res;
  }

  protected function getAllImagetyTypes() {
    $sql = "SELECT * FROM ".$this->dbPrefix."service_item_image_type WHERE 1";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([]);
    $results = $stmt->fetchAll();
    $res = array();
    foreach ($results as $row) {
      $res[$row['id_service_item_image_type']] = $row['description'];
    }
    return $res;
  }

  protected function getZadavatel($typ, $ico = NULL){
    if ($typ == 1){
      $sql = "SELECT * FROM ".$this->dbPrefix."service_zadavatel WHERE predajca = 1";
    } else {
      if ($ico == "y"){
        $sql = "SELECT * FROM ".$this->dbPrefix."service_zadavatel WHERE ico <> ''";
      } else {
        $sql = "SELECT * FROM ".$this->dbPrefix."service_zadavatel WHERE ico = ''";
      }
    }

    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([]);
    $results = $stmt->fetchAll();
    $res = array();
    foreach ($results as $row) {
      $res[]=$row['id_zadavatel'];
    }
    return $results;
  }

  protected function getObjednavatelByFirma($firma) {
    $sql = "SELECT * FROM ".$this->dbPrefix."service_zadavatel WHERE firma=?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$firma]);
    $results = $stmt->fetchAll();
    $res = array();
    foreach ($results as $row) {
      $res[]=$row['id_zadavatel'];
    }
    return $res;
  }

  protected function getDataByEmail($email) {
  }

  protected function getIdTypeRecords($limit = NULL, $offset = NULL, $id_type = NULL, $filter = NULL){
    $input = array();

    $filter_arr = array('ITEM.id_service_item LIKE ?', 'ITEM.servisny_list LIKE ?', 'ITEM.product_ref LIKE ?', 'ITEM.cislo_reklamacie_predajcu LIKE ?', 'ITEM.id_typ LIKE ?', 'ITEM.cislo_prepravy LIKE ?', 'ITEM.cislo_prepravy_out LIKE ?', 'ss.description = ?');
    if ($filter != NULL){
      $input = array_merge($input, array_fill(0,count($filter_arr)-1,"%".$filter."%"));
      $input[] = $filter;
    }
    if ($id_type != NULL){
      $input[] = $id_type;
    }
    if ($limit != NULL){
      $input[] = $limit;
    }
    if ($offset != NULL){
      $input[] = $offset;
    }
    $lim = ($limit != NULL)?" LIMIT ?":"";
    $off = ($offset != NULL)?" OFFSET ?":"";
    $fil = ($filter != '')? implode(" OR ",$filter_arr):"";
    $fil = ($filter != '')? $fil.' AND ITEM.deleted IS NULL':'ITEM.deleted IS NULL';
    if ($id_type == NULL) {
      if ($_SESSION['admin_role'] == 1) {
        $whe = (empty($fil))?"": "WHERE ";
        $sql = "SELECT *, ss.description as stav_opravy, s.description as typ FROM ".$this->dbPrefix."service_item ITEM
        LEFT JOIN ".$this->dbPrefix."service_zadavatel O
		      ON ITEM.id_objednavatel = O.id_zadavatel
	      LEFT JOIN ".$this->dbPrefix."service_types s
		      ON ITEM.id_typ = s.id_service_type
        LEFT JOIN ".$this->dbPrefix."admins a
          ON ITEM.id_vybavuje = a.id_admin
        LEFT JOIN ".$this->dbPrefix."service_status ss
          ON ss.id_status = ITEM.id_stav_opravy
        $whe ".$fil."
        ORDER BY ITEM.id_service_item DESC".$lim.$off;
      } else if($_SESSION['admin_role'] == 2) {
        $fil = (empty($fil))?"":"($fil) AND ";
        $sql = "SELECT *, ss.description as stav_opravy, s.description as typ FROM ".$this->dbPrefix."service_item ITEM
        LEFT JOIN ".$this->dbPrefix."service_zadavatel O
  		    ON ITEM.id_objednavatel = O.id_zadavatel
  	     LEFT JOIN ".$this->dbPrefix."service_types s
  		     ON ITEM.id_typ = s.id_service_type
        LEFT JOIN ".$this->dbPrefix."admins a
          ON ITEM.id_vybavuje = a.id_admin
        LEFT JOIN ".$this->dbPrefix."service_status ss
          ON ss.id_status = ITEM.id_stav_opravy
        WHERE ".$fil." (ITEM.id_typ < 4)
        ORDER BY ITEM.id_service_item DESC".$lim.$off;
      } else if($_SESSION['admin_role'] == 3) {
        $fil = (empty($fil))?"":"($fil) AND ";
        $sql = "SELECT *, ss.description as stav_opravy, s.description as typ FROM ".$this->dbPrefix."service_item ITEM
        LEFT JOIN ".$this->dbPrefix."service_zadavatel O
  		    ON ITEM.id_objednavatel = O.id_zadavatel
  	     LEFT JOIN ".$this->dbPrefix."service_types s
  		     ON ITEM.id_typ = s.id_service_type
        LEFT JOIN ".$this->dbPrefix."admins a
          ON ITEM.id_vybavuje = a.id_admin
        LEFT JOIN ".$this->dbPrefix."service_status ss
          ON ss.id_status = ITEM.id_stav_opravy
        WHERE ".$fil." (ITEM.id_stav_opravy <> 16 AND ITEM.id_stav_opravy <> 18 AND ITEM.id_stav_opravy <> 19 AND ITEM.id_stav_opravy <> 20 AND ITEM.id_stav_opravy <> 21) AND (ITEM.id_vybavuje IS NULL OR ITEM.id_vybavuje = ".$_SESSION['admin_logged'].")
        ORDER BY ITEM.id_service_item DESC".$lim.$off;
      }
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute($input);
    } else {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_item ITEM LEFT JOIN ".$this->dbPrefix."service_zadavatel O ON ITEM.id_objednavatel = O.id_zadavatel WHERE (".$fil.") AND `id_typ`=?".$lim.$off;
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute($input);
    }
    $results = $stmt->fetchAll();
    $res = array();
    foreach ($results as $id => $row) {
      $row['cislo_prepravy'] = substr($row['cislo_prepravy'],1);
      $res[]=$row;
    }
    return $res;
  }

  protected function getAllIdTypeRecords($id_type = NULL, $filter = NULL){
    $input = array();

    $filter_arr = array('ITEM.id_service_item LIKE ?', 'ITEM.servisny_list LIKE ?', 'ITEM.product_ref LIKE ?', 'ITEM.cislo_reklamacie_predajcu LIKE ?', 'ITEM.id_typ LIKE ?','ITEM.cislo_prepravy LIKE ?', 'ITEM.cislo_prepravy_out LIKE ?', 'ss.description = ?');
    if ($filter != NULL){
      $input = array_merge($input, array_fill(0,count($filter_arr)-1,"%".$filter."%"));
      $input[] = $filter;
    }
    if ($id_type != NULL){
      $input[] = $id_type;
    }

    $fil = ($filter != '')? implode(" OR ",$filter_arr):"";
    $fil = ($filter != '')? $fil.' AND ITEM.deleted IS NULL':'ITEM.deleted IS NULL';
    if ($id_type == NULL) {
      if ($_SESSION['admin_role'] == 1) {
        $whe = (empty($fil))?"": "WHERE ";
        $sql = "SELECT COUNT(ITEM.id_service_item) as spolu FROM ".$this->dbPrefix."service_item ITEM
        LEFT JOIN ".$this->dbPrefix."service_zadavatel O
		      ON ITEM.id_objednavatel = O.id_zadavatel
	      LEFT JOIN ".$this->dbPrefix."service_types s
		      ON ITEM.id_typ = s.id_service_type
        LEFT JOIN ".$this->dbPrefix."admins a
          ON ITEM.id_vybavuje = a.id_admin
        LEFT JOIN ".$this->dbPrefix."service_status ss
          ON ss.id_status = ITEM.id_stav_opravy
        $whe ".$fil."
        ORDER BY ITEM.id_service_item DESC";
      } else if($_SESSION['admin_role'] == 2) {
        $fil = (empty($fil))?"":"($fil) AND ";
        $sql = "SELECT COUNT(ITEM.id_service_item) as spolu FROM ".$this->dbPrefix."service_item ITEM
        LEFT JOIN ".$this->dbPrefix."service_zadavatel O
  		    ON ITEM.id_objednavatel = O.id_zadavatel
  	     LEFT JOIN ".$this->dbPrefix."service_types s
  		     ON ITEM.id_typ = s.id_service_type
        LEFT JOIN ".$this->dbPrefix."admins a
          ON ITEM.id_vybavuje = a.id_admin
        LEFT JOIN ".$this->dbPrefix."service_status ss
          ON ss.id_status = ITEM.id_stav_opravy
        WHERE ".$fil." (ITEM.id_typ < 4)
        ORDER BY ITEM.id_service_item DESC";
      } else if($_SESSION['admin_role'] == 3) {
        $fil = (empty($fil))?"":"($fil) AND ";
        $sql = "SELECT COUNT(ITEM.id_service_item) as spolu FROM ".$this->dbPrefix."service_item ITEM
        LEFT JOIN ".$this->dbPrefix."service_zadavatel O
  		    ON ITEM.id_objednavatel = O.id_zadavatel
  	     LEFT JOIN ".$this->dbPrefix."service_types s
  		     ON ITEM.id_typ = s.id_service_type
        LEFT JOIN ".$this->dbPrefix."admins a
          ON ITEM.id_vybavuje = a.id_admin
        LEFT JOIN ".$this->dbPrefix."service_status ss
          ON ss.id_status = ITEM.id_stav_opravy
        WHERE ".$fil." (ITEM.id_stav_opravy <> 16 AND ITEM.id_stav_opravy <> 18 AND ITEM.id_stav_opravy <> 19 AND ITEM.id_stav_opravy <> 20 AND ITEM.id_stav_opravy <> 21) AND (ITEM.id_vybavuje IS NULL OR ITEM.id_vybavuje = ".$_SESSION['admin_logged'].")
        ORDER BY ITEM.id_service_item DESC";
      }
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute($input);
    } else {
      $sql = "SELECT COUNT(ITEM.id_service_item) as spolu FROM ".$this->dbPrefix."service_item ITEM LEFT JOIN ".$this->dbPrefix."service_zadavatel O ON ITEM.id_objednavatel = O.id_zadavatel WHERE (".$fil.") AND `id_typ`=?";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute($input);
    }
    $results = $stmt->fetchAll();
    return $results[0]['spolu'];
  }

  protected function getRecordData($id_record = NULL){
    if ($id_record == NULL) {
      return "neplatný vstup";
    } else {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_item ITEM
      LEFT JOIN ".$this->dbPrefix."service_zadavatel O
        ON ITEM.id_objednavatel = O.id_zadavatel
      LEFT JOIN ".$this->dbPrefix."admins a
        ON ITEM.id_vybavuje = a.id_admin
      WHERE ITEM.`id_service_item`=?";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$id_record]);
    }
    $results = $stmt->fetchAll();
    $results[0]['cislo_prepravy'] = substr($results[0]['cislo_prepravy'],1);
    return $results;
  }

  protected function getAllStatuses(){
    $sql = "SELECT SS.description FROM ".$this->dbPrefix."service_status SS";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([]);
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getRecordStatusHistory($id_record = NULL){
    if ($id_record == NULL) {
      return "neplatný vstup";
    } else {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_history SH
      LEFT JOIN ".$this->dbPrefix."admins a
        ON SH.employee = a.id_admin
      LEFT JOIN ".$this->dbPrefix."service_status SS
        ON SH.status = SS.id_status
      WHERE SH.`id_service`=?
      ORDER BY SH.`date_action` DESC";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$id_record]);
    }
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getRecordNotes($id_record = NULL){
    if ($id_record == NULL) {
      return "neplatný vstup";
    } else {
      $sql = "SELECT status, note FROM ".$this->dbPrefix."service_history SH
      LEFT JOIN ".$this->dbPrefix."admins a
        ON SH.employee = a.id_admin
      LEFT JOIN ".$this->dbPrefix."service_status SS
        ON SH.status = SS.id_status
      WHERE SH.`id_service`=?
      ORDER BY SH.`date_action` DESC";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$id_record]);
    }
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getOperations($id_record = NULL, $id_operation = NULL){
    if ($id_record == NULL) {
      return "neplatný vstup";
    } else if($id_operation == NULL){
      $sql = "SELECT * FROM ".$this->dbPrefix."service_item_operations IO
      WHERE IO.`id_service_item`=?
      ORDER BY IO.`date_action` DESC";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$id_record]);
    } else {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_item_operations IO
      WHERE IO.`id_action` = ?";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$id_operation]);
    }
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getStatusesByIds($ids){
    $results = array();
    foreach ($ids as $id) {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_status SS
      WHERE SS.`id_status`=?";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$id]);
      $results[] = $stmt->fetch();
    }
    return $results;
  }

  protected function getStatusHistoryByIds($id_record){
    if ($id_record == NULL) {
      return "neplatný vstup";
    } else {
      $sql = "SELECT * FROM ".$this->dbPrefix."service_history SH
      WHERE SH.`id_service`=?
      ORDER BY SH.`date_action` DESC";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$id_record]);
    }
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getTechnicians(){
    $sql = "SELECT * FROM ".$this->dbPrefix."admins A
    WHERE (A.`role`=1 OR A.`role`=3) AND A.active = 1";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([]);
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getImages($id_record, $image_type){
    $sql = "SELECT * FROM ".$this->dbPrefix."service_item_images SII
    WHERE (SII.`id_service_item`= ? AND SII.`id_service_item_image_type` = ?)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$id_record, $image_type]);
    $results = $stmt->fetchAll();
    return $results;
  }

  protected function getQRAceess($id_record, $employee){
    $sql = "SELECT * FROM ".$this->dbPrefix."image_hash_access HA
    WHERE (HA.`id_service_item`= ? AND HA.`employee` = ? AND `end` >= CURRENT_TIMESTAMP)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$id_record, $employee]);
    $results = $stmt->fetchAll();
    return $results[0]['hash'];
  }

  protected function isActive($hash){
    $sql = "SELECT * FROM ".$this->dbPrefix."image_hash_access HA
    WHERE (HA.`hash`= ? AND HA.`end` >= CURRENT_TIMESTAMP)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$hash]);
    $results = $stmt->fetchAll();
    return array("employee" => $results[0]['employee'], "id_service_item" => $results[0]['id_service_item']);
  }

  protected function checkServisnyList($servisny_list){
    $sql = "SELECT COUNT(*) FROM ".$this->dbPrefix."service_item WHERE `servisny_list` = ? AND `deleted` IS NULL";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$servisny_list]);
    $results = $stmt->fetchAll();
    return ($results[0]["COUNT(*)"]==0)?false:true;
  }

  protected function toETA($id_item){
    $statuses = $this->getStatusHistoryByIds($id_item);
    foreach ($statuses as $row) {
      if(($row['status'] == 9) || ($row['status'] == 10)){ //dobropis alebo vymena
        return true;
      }
    }
    return false;
  }

  protected function inPackage($id_item){
    $data = $this->getRecordData($id_item);
  }

  protected function isSet($id_item, $operation){
    $sql = "SELECT COUNT(*) FROM ".$this->dbPrefix."service_item_operations WHERE `id_service_item` = ? AND `action` = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$id_item, $operation]);
    $results = $stmt->fetchAll();
    return ($results[0]["COUNT(*)"]==0)?false:true;
  }

  //ajax
  protected function getRecordsDataByType($id_type = NULL){
    if ($id_type == 0){
      $sql = "SELECT id_service_item, servisny_list, product_ref, cislo_reklamacie_predajcu, vyrobne_cislo, datum_vzniku, datum_prijatia, id_typ, id_stav_opravy, id_vybavuje FROM ".$this->dbPrefix."service_item WHERE 1";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([]);
    } else {
      $sql = "SELECT COUNT(*) FROM ".$this->dbPrefix."service_item WHERE `id_typ` = ?";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$id_type]);
    }
    $results = $stmt->fetchAll();
    return $results;
  }

// ------------Controller------------

  protected function addZadavatel($meno = "", $firma = "", $ico = "", $dic = "", $ic_dph = "", $ulica = "", $psc = "", $mesto = "", $telefon = '', $email1 = '', $email2 = '', $email3 = '', $predajca = NULL, $zakaznik = NULL) {
    $sql = "SELECT id_zadavatel FROM `".$this->dbPrefix."service_zadavatel` WHERE `firma` = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$firma]);
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
      $res = $row['id_zadavatel'];
    }
    if (!empty($res)){
      return $res;
    } else {
      $sql = "INSERT INTO ".$this->dbPrefix."service_zadavatel (`meno`, `firma`, `ico`, `dic`, `ic_dph`, `ulica_cislo`, `psc`, `mesto`, `telefon`, `email1`, `email2`, `email3`, `predajca`, `zakaznik`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $con = $this->connect();
      $stmt = $con->prepare($sql);
      $stmt->execute([$meno, $firma, $ico, $dic, $ic_dph, $ulica, $psc, $mesto, $telefon, $email1, $email2, $email3, $predajca, $zakaznik]);
      $id = $con->lastInsertId();
      return $id;
    }
  }

  protected function addServiceItemFromETA($servisny_list, $product_ref, $info, $email_zakaznika, $datum_vzniku, $datum_prijatia, $id_delivery_in, $cislo_prepravy, $cislo_reklamacie_predajcu, $vzdialenost, $hmotnost, $id_typ, $id_zadavatel, $id_stav_opravy, $id_vybavuje){
    $sql = "INSERT INTO `".$this->dbPrefix."service_item`(`servisny_list`, `product_ref`, `info`, `email_zakaznika`, `datum_vzniku`, `datum_prijatia`, `id_delivery_in`, `cislo_prepravy`, `cislo_reklamacie_predajcu`, `vzdialenost`, `hmotnost`, `id_typ`, `id_objednavatel`, `id_stav_opravy`, `id_vybavuje`) VALUES (?, ?, ?, ?, STR_TO_DATE(?, '%d.%m.%Y'), STR_TO_DATE(?, '%d.%m.%Y'), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$servisny_list, $product_ref, $info, $email_zakaznika, $datum_vzniku, $datum_prijatia, $id_delivery_in, addslashes($cislo_prepravy), $cislo_reklamacie_predajcu, $vzdialenost, $hmotnost, $id_typ, $id_zadavatel, $id_stav_opravy, $id_vybavuje]);
    $id = $con->lastInsertId();
    return $id;
  }

  protected function addService($datum_kupy, $datum_vzniku, $datum_prijatia, $id_delivery_in, $cislo_prepravy, $cislo_reklamacie_predajcu, $vzdialenost, $hmotnost, $id_typ, $id_objednavatel, $id_zakaznik, $id_stav_opravy, $pozadovane_riesenie, $original_obal, $prislusenstvo, $popis, $vyrobne_cislo, $stav_vyrobku, $pocet_vyjadreni){
    $sql = "INSERT INTO `".$this->dbPrefix."service_item`(`datum_kupy`, `datum_vzniku`, `datum_prijatia`, `id_delivery_in`, `cislo_prepravy`, `cislo_reklamacie_predajcu`, `vzdialenost`, `hmotnost`, `id_typ`, `id_objednavatel`, `id_zakaznik`, `id_stav_opravy`, `pozadovane_riesenie`, `original_obal`, `prislusenstvo`, `popis`, `vyrobne_cislo`, `stav_vyrobku`, `pocet_vyjadreni`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,  ?, ?, ?, ?, ?, ?)";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$datum_kupy, $datum_vzniku, $datum_prijatia, $id_delivery_in, $cislo_prepravy, $cislo_reklamacie_predajcu, $vzdialenost, $hmotnost, $id_typ, $id_objednavatel, $id_zakaznik, $id_stav_opravy, $pozadovane_riesenie, $original_obal, $prislusenstvo, $popis, $vyrobne_cislo, $stav_vyrobku, $pocet_vyjadreni]);
    $id = $con->lastInsertId();
    return $id;
  }

  protected function updateDeliveryIn($id_item, $crp, $vzdialenost, $hmotnost){
    $sql = "UPDATE `".$this->dbPrefix."service_item` SET `cislo_reklamacie_predajcu` = ?, `vzdialenost` = ?, `hmotnost` = ? WHERE `id_service_item` = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$crp, $vzdialenost, $hmotnost, $id_item]);
  }

  protected function updateProductInfo($id_item, $popis, $prislusenstvo, $vyrobne_cislo, $stav){
    $sql = "UPDATE `".$this->dbPrefix."service_item` SET `popis` = ?, `prislusenstvo` = ?, `vyrobne_cislo` = ?, `stav_vyrobku` = ? WHERE `id_service_item` = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$popis, $prislusenstvo, $vyrobne_cislo, $stav, $id_item]);
  }

  protected function updateSL($id_item, $servisny_list){
    $sql = "UPDATE `".$this->dbPrefix."service_item` SET `servisny_list` = ? WHERE `id_service_item` = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$servisny_list, $id_item]);
  }

  protected function addopt($id_record, $id_admin){
    $sql = "UPDATE `".$this->dbPrefix."service_item` SET `id_vybavuje` = ? WHERE id_service_item = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$id_admin, $id_record]);
  }

  protected function changeStatusById($id_record, $id_status, $employee){
    $sql = "INSERT INTO `".$this->dbPrefix."service_history`(`id_service`, `status`, `employee`) VALUES (?, ?, ?)";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$id_record, $id_status, $employee]);
    $id = $con->lastInsertId();
    $sql = "UPDATE `".$this->dbPrefix."service_item` SET `id_stav_opravy` = ? WHERE id_service_item = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$id_status, $id_record]);
    return $id;
  }

  protected function addServiceAction($id_item, $action, $employee, $amount, $price){
    $sql = "INSERT INTO `".$this->dbPrefix."service_item_operations`(`id_service_item`, `action`, `employee`, `mnozstvo`,`jednotkova_cena`) VALUES (?, ?, ?, ?, ?)";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$id_item, $action, $employee, $amount, $price]);
    $id = $con->lastInsertId();
  }

  protected function updateServiceAction($id_action, $action, $employee, $amount, $price){
    $sql = "UPDATE `".$this->dbPrefix."service_item_operations` SET `action` = ?, `employee` = ?, `mnozstvo` = ?,`jednotkova_cena` = ? WHERE `id_action` = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$action, $employee, $amount, $price, $id_action]);
  }

  protected function deleteServiceAction($id_action){
    $sql = "DELETE FROM `".$this->dbPrefix."service_item_operations` WHERE `id_action` = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$id_action]);
  }

  protected function addStatusNote($id_item, $note){
    $data = $this->getRecordStatusHistory($id_item);
    $sql = "UPDATE `".$this->dbPrefix."service_history` SET `note` = ? WHERE `id_history` = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$note, $data[0]['id_history']]);
  }

  protected function uploadServiceImg($id_record, $data, $type){
    $sql = "INSERT INTO `".$this->dbPrefix."service_item_images`(`id_service_item`, `id_service_item_image_type`, `data`) VALUES (?, ?, ?)";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$id_record, $type, $data]);
    $id = $con->lastInsertId();
    return $id;
  }

  protected function deleteServiceImg($id_image){
    $sql = "SELECT `data` FROM `".$this->dbPrefix."service_item_images` WHERE `id_image` = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$id_image]);
    $res = $stmt->fetchAll();
    $res = $res[0]['data'];
    $sql = "DELETE FROM `".$this->dbPrefix."service_item_images` WHERE `id_image` = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$id_image]);
    return $res;
  }

  protected function newQRccess($id_record, $employee){
    $hash = hash_hmac('sha3-512', bin2hex(random_bytes(25)), bin2hex(random_bytes(25)));
    $end = date("Y-m-d H:i:s", strtotime("+10 minutes"));
    $sql = "INSERT INTO `".$this->dbPrefix."image_hash_access` (`id_service_item`, `hash`, `employee`, `end`) VALUES (?, ?, ?, ?)";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$id_record, $hash, $employee, $end]);
    return $hash;
  }

  protected function updateLik($id_item, $vzdialenost){
    $sql = "UPDATE `".$this->dbPrefix."service_item` SET `cislo_prepravy_likvidacia` = ? WHERE `id_service_item` = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$vzdialenost, $id_item]);
  }

  protected function updateDelOut($id_item, $id_delivery_out, $cislo_prepravy, $vzdialenost, $hmotnost){
    $sql = "UPDATE `".$this->dbPrefix."service_item` SET `id_delivery_out` = ?, `cislo_prepravy_out` = ?, `vzdialenost_out` = ?, `hmotnost_out` = ? WHERE `id_service_item` = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$id_delivery_out, $cislo_prepravy, $vzdialenost, $hmotnost, $id_item]);
  }

  protected function kategoriaUkoncenia($id_item, $kategoria){
    $sql = "UPDATE `".$this->dbPrefix."service_item` SET `kat_ukoncenia` = ? WHERE `id_service_item` = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$kategoria, $id_item]);
  }

  protected function saveVyjadrenie($id_item, $vyjadrenie){
    $sql = "UPDATE `".$this->dbPrefix."service_item` SET `vyjadrenie` = ? WHERE `id_service_item` = ?";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$vyjadrenie, $id_item]);
  }
}
 ?>
