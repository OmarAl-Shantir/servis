<?php

class BalikView extends Balik{
  public function get_volne($id_balik){
    $balik = $this->getBalikType($id_balik);
    if($balik['typ'] == "likvidácia" && $balik['active'] == "nepodané"){
      return $this->getVolneLikvidacie();
    }
    if(($balik['typ'] >= 1 && $balik['typ'] <= 8) && $balik['active'] == "nepodané"){
      return $this->getVolneForCat($balik['typ']);
    }
  }

  public function in_package($id_balik){
      return $this->inPackage($id_balik);
  }

  public function get_balik($id_balik = NULL){
    $data = $this->getBalik($id_balik);
    return $data;
  }

  public function get_balik_kat($id_balik = NULL){
    $data = $this->getBalikKat($id_balik);
    return $data;
  }

  public function get_balik_type($id_balik){
    return $this->getBalikType($id_balik);
  }
}
?>
