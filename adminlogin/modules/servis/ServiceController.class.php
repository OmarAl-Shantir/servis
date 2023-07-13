<?php

class ServiceController extends Service{
  public function add_zarucny_service_from_eta($parametre){
    extract($parametre);
    $id_objednavatel = $this->addZadavatel("", $firma, "", "", "", "", "", "", "", "", "", "");
    $id_opravy = $this->addServiceItemFromETA($servisny_list, $product_ref, $info, $email_zakaznika, $datum_vzniku, $datum_prijatia, $sposob_prepravy, $cislo_prepravy, $cislo_reklamacie_predajcu, $vzdialenost, $hmotnost, $id_typ, $id_objednavatel, $id_stav_opravy, $id_vybavuje);
    return $id_opravy;
  }

  public function add_service($parametre){
    extract($parametre);
    if($parametre['id_typ'] = 1){ // zarucná
      if(isset($_POST['predajca_je_objednavatel'])){
        if(empty($parametre['id_predajcu'])){
          $id_predajcu = $id_predajcu = $this->add_zadavatel($predajca_meno, $predajca_firma, $predajca_ico, $predajca_dic, $predajca_ic_dph, $predajca_adresa, $predajca_psc, $predajca_mesto, $predajca_telefon, $predajca_mail, '', '', 1, NULL);
        }
        $id_opravy = $this->addService($datum_kupy, $datum_vzniku, $datum_prijatia, $id_delivery_in, $cislo_prepravy, $cislo_reklamacie_predajcu, $vzdialenost, $hmotnost, $id_typ, $id_predajcu, $id_predajcu, $id_stav_opravy, $pozadovane_riesenie, $original_obal, $prislusenstvo, $popis, $vyrobne_cislo, $stav_vyrobku, $pocet_vyjadreni);
      } else {
        if(empty($parametre['id_predajcu'])){
          $id_predajcu = $id_predajcu = $this->add_zadavatel($predajca_meno, $predajca_firma, $predajca_ico, $predajca_dic, $predajca_ic_dph, $predajca_adresa, $predajca_psc, $predajca_mesto, $predajca_telefon, $predajca_mail, '', '', 1, NULL);
        }
        if(empty($parametre['id_zakaznik'])){
          $id_zakaznik = $id_predajcu = $this->add_zadavatel($obj_meno, $obj_firma, $obj_ico, $obj_dic, $obj_ic_dph, $obj_adresa, $obj_psc, $obj_mesto, $obj_telefon, $obj_mail, '', '', NULL, 1);
        }
        $id_opravy = $this->addService($datum_kupy, $datum_vzniku, $datum_prijatia, $id_delivery_in, $cislo_prepravy, $cislo_reklamacie_predajcu, $vzdialenost, $hmotnost, $id_typ, $id_predajcu, $id_zakaznik, $id_stav_opravy, $pozadovane_riesenie, $original_obal, $prislusenstvo, $popis, $vyrobne_cislo, $stav_vyrobku, $pocet_vyjadreni);
      }


    } else if($parametre['id_typ'] = 2){ // blesková výmena
      if(empty($parametre['id_predajcu'])){
        $id_predajcu = $id_predajcu = $this->add_zadavatel($predajca_meno, $predajca_firma, $predajca_ico, $predajca_dic, $predajca_ic_dph, $predajca_adresa, $predajca_psc, $predajca_mesto, $predajca_telefon, $predajca_mail, '', '', 1, NULL);
      }
      $id_opravy = $this->addService($datum_kupy, $datum_vzniku, $datum_prijatia, $id_delivery_in, $cislo_prepravy, $cislo_reklamacie_predajcu, $vzdialenost, $hmotnost, $id_typ, $id_predajcu, $id_predajcu, $id_stav_opravy, $pozadovane_riesenie, $original_obal, $prislusenstvo, $popis, $vyrobne_cislo, $stav_vyrobku, $pocet_vyjadreni);
    } else if($parametre['id_typ'] = 3){ // pozáručný servis

    }
    $this->changeStatus($id_opravy, 1, $_SESSION['admin_logged']);
    return $id_opravy;
  }

  public function nay_service(){

  }

  public function electrobeta_service(){

  }

  public function add_zadavatel($meno, $firma, $ico, $dic, $ic_dph, $ulica, $psc, $mesto, $telefon, $email1, $email2, $email3, $predajca, $zakaznik){
    $id_zadavatel = $this->addZadavatel($meno, $firma, $ico, $dic, $ic_dph, $ulica, $psc, $mesto, $telefon, $email1, $email2, $email3, $predajca, $zakaznik);
  }

  public function update_delivery_in($id_item, $crp, $vzdialenost, $hmotnost){
    $this->updateDeliveryIN($id_item, $crp, $vzdialenost, $hmotnost);
  }

  public function update_product_info($id_item, $popis, $prislusenstvo, $vyrobne_cislo, $stav){
    $this->updateProductInfo($id_item, $popis, $prislusenstvo, $vyrobne_cislo, $stav);
  }

  public function addSL($id_item, $servisny_list){
    $this->updateSL($id_item, $servisny_list);
  }

  public function addoptItem($id_item, $id_admin = NULL){
    if ($id_admin == null) {
      $id_admin = $_SESSION['admin_logged'];
    }
    $serviceV = new ServiceView();
    $item = $serviceV->getRecordDetails($id_item);
    //if (empty($item['id_vybavuje'])){
      $this->addopt($id_item, $id_admin);
      $this->changeStatus($id_item, 3, $id_admin);
    //}
  }

  public function changeStatus($id_item, $id_status, $employee){
    $this->changeStatusById($id_item, $id_status, $employee);
  }

  public function addAction($id_item, $action, $price, $amount){
    $employee = $_SESSION['admin_logged'];
    $this->addServiceAction($id_item, $action, $employee, $amount, $price);
  }

  public function updateAction($id_action, $action, $price, $amount){
    $employee = $_SESSION['admin_logged'];
    $this->updateServiceAction($id_action, $action, $employee, $amount, $price);
  }

  public function deleteAction($id_action){
    $this->deleteServiceAction($id_action);
  }

  public function addNote($id_item, $note){
    $this->addStatusNote($id_item, $note);
  }

  public function uploadServiceImage($id_record, $image, $type){
    $res = $this->uploadServiceImg($id_record, $image, $type);
    return $res;
  }

  public function deleteServiceImage($id_image){
    $res = $this->deleteServiceImg($id_image);
    return $res;
  }

  public function generateQRaccess($id_item, $employee){
    $serviceV = new ServiceView();
    $hash = $serviceV->getActiveQRaccess($id_item, $employee);
    if(empty($hash)){
      $hash = $this->newQRccess($id_item, $employee);
    }
    return $hash;
  }

  public function updateLikvidacia($id_item, $vzdialenost){
    $this->updateLik($id_item, $vzdialenost);
  }

  public function updateDeliveryOut($id_item, $id_delivery_out, $cislo_prepravy, $vzdialenost, $hmotnost){
    $this->updateDelOut($id_item, $id_delivery_out, $cislo_prepravy, $vzdialenost, $hmotnost);
  }

  public function kategoria_ukoncenia($id_item, $kategoria){
    $this->kategoriaUkoncenia($id_item, $kategoria);
  }

  public function save_vyjadrenie($id_item, $vyjadrenie){
    $this->saveVyjadrenie($id_item, $vyjadrenie);
  }
}
?>
