<?php

class ZadavatelView extends Zadavatel{
  public function is_predajca($id_zadavatel){
    return $this->isPredajca($id_zadavatel);
  }

  public function get_predajca($id_zadavatel = NULL){
    $data = $this->getPredajca($id_zadavatel);
    return $data;
  }

  public function get_zakaznik($id_zadavatel = NULL){
    $data = $this->getZakaznik($id_zadavatel);
    return $data;
  }

  public function get_data($id_zadavatel = NULL){
    $data = $this->getData($id_zadavatel);
    return $data[0];
  }
}
?>
