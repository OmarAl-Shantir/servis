<?php

class ServiceView extends Service{
  public function getDelivery($id_delivery = NULL){
    $delivery = $this->getAllDelivery($id_delivery);
    return $delivery;
  }

  public function getImageTypes(){
    $imageTypes = $this->getAllImagetyTypes();
    return $imageTypes;
  }

  public function getZarucnyRecords($limit, $offset){
    $data = $this->getIdTypeRecords($limit, $offset, 1);
    return $data;
  }

  public function getPredpredajnyRecords($limit, $offset){
    $data = $this->getIdTypeRecords($limit, $offset, 2);
    return $data;
  }

  public function getPozarucnyRecords($limit, $offset){
    $data = $this->getIdTypeRecords($limit, $offset, 3);
    return $data;
  }

  public function getAllRecords($limit = null, $offset = null){
    $data = $this->getIdTypeRecords($limit, $offset);
    return $data;
  }

  public function get_all_service_records($type, $limit, $offset, $filter){
    $all = $this->getAllIdTypeRecords($type, $filter);
    $data = $this->getIdTypeRecords($limit, $offset, $type, $filter);
    return array($data,$all);
  }

  public function isRegistered($servisny_list){
    return $this->checkServisnyList($servisny_list);
  }

  public function getRecordDetails($id_record){
    $data = $this->getRecordData($id_record);
    return $data[0];
  }

  public function getRecordHistory($id_record){
    $data = $this->getRecordStatusHistory($id_record);
    return $data;
  }

  public function get_record_notes($id_record){
    $data = $this->getRecordNotes($id_record);
    foreach ($data as $row) {
      $res[$row['status']] = $row['note'];
    }
    return $res;
  }

  public function getRecordOperations($id_record, $id_operation = null){
    $data = $this->getOperations($id_record, $id_operation);
    return $data;
  }

  public function getFullName($pred,$meno, $priezvisko, $za){
    if (empty($pred) and empty($za)){
      $fullname = "$meno $priezvisko $za";
    } else if (!empty($pred) and empty($za)) {
      $fullname = "$pred $meno $priezvisko";
    } else if (empty($pred) and !empty($za)){
      $fullname = "$meno $priezvisko $za";
    } else {
      $fullname = "$meno $priezvisko";
    }
    return $fullname;
  }

  public function get_all_statuses(){
    return $this->getAllStatuses();
  }
  public function getStatuses($id_record){
    $zarucny = array(2,3,4,5,6,22,8,7,10,9,12,11,16);
    $nay = array(2,3,4,5,6,8,7,17,18,16,19,20,21);
    $pozarucny = array(2,3,4,5,6,22,7,15,14,12,11,16,13);
    $data = $this->getRecordData($id_record);
    switch ($data[0]['id_typ']) {
      case 1:
        $ids = $zarucny;
        break;
      case 2:
        $ids = $zarucny;
        break;
      case 3:
        $ids = $nay;
        break;
      case 4:
        $ids = $pozarucny;
        break;
    }
    $statuses = $this->getStatusesByIds($ids);
    $s_value = array();
    foreach ($statuses as $status) {
      $s_value[$status['id_status']] = $status['description'];
    }
    return $s_value;
  }

  public function getStatusHistory($id_record){
    $statuses = $this->getStatusHistoryByIds($id_record);
    foreach ($statuses as $status) {
      $history[$status['status']] = $status['date_action'];
    }
    return $history;
  }

  public function canBeTechnician(){
    $t = $this->getTechnicians();
    foreach ($t as $technician) {
      $data[$technician['id_admin']] = $technician['fullname'];
    }
    return $data;
  }

  public function getServiceImages($id_record, $image_type){
    $images = $this->getImages($id_record, $image_type);
    foreach ($images as $key => $value) {
      $img[$value['id_image']] = $value['data'];
    }
    return $img;
  }

  public function getActiveQRaccess($id_item, $employee){
    $hash = $this->getQRAceess($id_item, $employee);
    return $hash;
  }

  public function hashIsActive($hash){
    $employee = $this->isActive($hash);
    return $employee;
  }

  public function to_ETA($id_item){
    return ($this->toETA($id_item))?true:false;
  }

  public function in_package($id_item){
    $data = $this->getRecordData($id_item);
    return $data[0]['cislo_prepravy_likvidacia'];
  }

  public function is_set($id_item, $operation){
    return $this->isSet($id_item, $operation);
  }

  public function get_predajca(){
    $data = $this->getZadavatel(1);
    foreach ($data as $row) {
      $r = $row;
      unset($r['id_zadavatel']);
      $res[$row['id_zadavatel']] = $r;
    }
    return $res;
  }

  public function get_objednavatel_with_ico(){
    $data = $this->getZadavatel(0,"y");
    foreach ($data as $row) {
      $r = $row;
      unset($r['id_zadavatel']);
      $res[$row['id_zadavatel']] = $r;
    }
    return $res;
  }

  public function get_objednavatel_without_ico(){
    $data = $this->getZadavatel(0,"n");
    foreach ($data as $row) {
      $r = $row;
      unset($r['id_zadavatel']);
      $res[$row['id_zadavatel']] = $r;
    }
    return $res;
  }

  public function isWasStatus($id_item, $id_status){
    $statuses = $this->getStatusHistory($id_item);
    return (in_array($id_status,array_keys($statuses)));
  }

  //ajax
  public function get_records_data_by_type($id_type){
    return $this->getRecordsDataByType($id_type);
  }
}
?>
