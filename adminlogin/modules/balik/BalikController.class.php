<?php

class BalikController extends Balik{
  public function update_balik($id_balik, $datum_podania){
    $this->updateBalik($id_balik, $datum_podania);
  }

  public function add_balik(){
    $cislo_balika = sprintf("LZ ETA%d",date("ymd"));
    $i=2;
    while (!is_null($this->getBalikByName($cislo_balika))){
      $cislo_balika = sprintf("LZ ETA%d-%d",date("ymd"),$i);
      $i++;
    }
    $id_balik = $this->addBalik($cislo_balika);
    return $id_balik;
  }

  public function add_balik_kat($kat){
    $cislo_balika = sprintf("OV 00%d NAY%d",$kat, date("ymd"));
    $i=2;
    while (!is_null($this->getBalikByName($cislo_balika))){
      $cislo_balika = sprintf("OV 00%d NAY%d-%d",$kat, date("ymd"),$i);
      $i++;
    }
    $id_balik = $this->addBalik($cislo_balika, $kat);
    return $id_balik;
  }

  public function save_in_package_likvidacia($id_balik, $products){
    $origin = $this->inPackage($id_balik);
    foreach ($origin as $value) {
      $ori[] = $value['id_service_item'];
    }
    $diff = array_diff($ori,$products);
    if(!empty($diff)){
      foreach ($diff as $id_product) {
        $this->removeFromPackageLikvidacia($id_product);
      }
    } elseif(empty($products)){
      foreach ($ori as $id_product) {
        $this->removeFromPackageLikvidacia($id_product);
      }
    }
    foreach ($products as $id_product) {
      $this->addToPackageLikvidacia($id_product, $id_balik);
    }
  }
}
?>
