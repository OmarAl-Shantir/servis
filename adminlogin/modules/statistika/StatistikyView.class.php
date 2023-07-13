<?php

class StatistikyView extends Statistiky
{
  public function get_finished_services($limit, $typ = NULL){
    $typ = ($typ==0)?NULL:$typ;
    return $this->getFinishedServices($limit, $typ);
  }

  public function get_employees($service_ids){
    foreach ($service_ids as $service_id => $admin){
      $employees[key($admin)] = array("fullname" => $admin[key($admin)]);
    }
    return $employees;
  }

  public function get_import_by_employee($employee, $service_ids){
    $data = $this->getImportByEmployee($employee, $service_ids);
    $sum = 0;
    //var_dump($data);
    foreach ($data as $row) {
      $sum += $row['jednotkova_cena']*$row['mnozstvo'];
    }
    return $sum;
  }

  public function get_export_by_employee($employee, $service_ids){
    $data = $this->getExportByEmployee($employee, $service_ids);
    $sum = 0;
    foreach ($data as $row) {
      $sum += $row['jednotkova_cena']*$row['mnozstvo'];
    }
    return $sum;
  }

  public function get_cena_prace_by_employee($employee, $service_ids){
    $data = $this->getCenaPraceByEmployee($employee, $service_ids);
    $sum = 0;
    foreach ($data as $row) {
      $sum += $row['jednotkova_cena']*$row['mnozstvo'];
    }
    return $sum;
  }

  public function get_nd_by_employee($employee, $service_ids){
    $data = $this->getNDByEmployee($employee, $service_ids);
    $sum = 0;
    foreach ($data as $row) {
      $sum += $row['jednotkova_cena']*$row['mnozstvo'];
    }
    return $sum;
  }

  public function get_cas_od_pridelenia_by_employee($employee, $service_ids){
    $data = $this->getCasOdPrideleniaByEmployee($employee, $service_ids);
    $sum = $i = 0;
    foreach ($data as $row) {
      //echo $row['date_action']." - ".$this->getDatumPriradenia($row['id_service'])."<br>";
      $priradene = strtotime($this->getDatumPriradenia($row['id_service']));
      $ukoncene = strtotime($row['date_action']);
      $sum += $ukoncene-$priradene;
      $i++;
    }
    $cas = $sum/$i;
    return array(date("d", $cas), $i);
  }

  public function get_cas_od_prijatia($service_ids){
    $data = $this->getCasOdPrijatia($service_ids);
    $sum = $i = 0;
    foreach ($data as $row) {
      //echo $row['date_action']." - ".$this->getDatumPriradenia($row['id_service'])."<br>";
      $prijatie = strtotime($this->getDatumPrijatia($row['id_service']));
      $ukoncene = strtotime($row['date_action']);
      $sum += $ukoncene-$prijatie;
      $i++;
    }
    $cas = $sum/$i;
    return date("d", $cas);
  }

  public function get_sposov_ukoncenia_by_employee($employee, $service_ids){
    $data = $this->getSposovUkonceniaByEmployee($employee, $service_ids);
    $res = array();
    foreach ($data as $row) {
      if(in_array($row['description'],array_keys($res))){

        $res[$row['description']]++;
      } else {
        $res[$row['description']] = 1;
      }
    }
    arsort($res);
    return $res;
  }

  public function get_modely($service_ids){
    $data = $this->getModely($service_ids);
    $res = array();
    foreach ($data as $row) {
      if(in_array($row['product_ref'],array_keys($res))){

        $res[$row['product_ref']]++;
      } else {
        $res[$row['product_ref']] = 1;
      }
    }
    arsort($res);
    return $res;
  }

  public function get_ukoncenia($service_ids){
    $data = $this->getUkoncenia($service_ids);
    $res = array();
    foreach ($data as $row) {
      if(in_array($row['description'],array_keys($res))){

        $res[$row['description']]++;
      } else {
        $res[$row['description']] = 1;
      }
    }
    arsort($res);
    return $res;
  }

  public function get_prijatia($service_ids){
    $data = $this->getPrijatia($service_ids);
    $res = array();
    foreach ($data as $row) {
      $row['description'] = ($row['description']==NULL)?chr(127):$row['description'];
      if(in_array($row['description'],array_keys($res))){

        $res[$row['description']]++;
      } else {
        $res[$row['description']] = 1;
      }
    }
    arsort($res);
    return $res;
  }

  public function get_reklamujuci($service_ids){
    $data = $this->getReklamujuci($service_ids);
    $res = array();
    foreach ($data as $row) {
      if(in_array($row['firma'],array_keys($res))){

        $res[$row['firma']]++;
      } else {
        $res[$row['firma']] = 1;
      }
    }
    arsort($res);
    return $res;
  }

  public function get_zarucne_by_employee($employee, $service_ids){

  }

  public function get_predpredajne_by_employee($employee, $service_ids){

  }

  public function get_pozarucne_by_employee($employee, $service_ids){

  }
}

?>
