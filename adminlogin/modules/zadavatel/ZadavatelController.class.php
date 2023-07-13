<?php

class ZadavatelController extends Zadavatel{
  public function update_zadavatel($parametre, $id_zadavatel){
    extract($parametre);
    $this->updateZadavatel($id_zadavatel, $meno, $firma, $firma_popis, $ico, $dic, $ic_dph, $ulica_cislo, $psc, $mesto, $telefon, $email1, $email2, $email3, $predajca, $zakaznik);
  }

  public function add_zadavatel($parametre){
    extract($parametre);
    $id_zadavatel = $this->addZadavatel($meno, $firma, $firma_popis, $ico, $dic, $ic_dph, $ulica_cislo, $psc, $mesto, $telefon, $email1, $email2, $email3, $predajca, $zakaznik);
    return $id_zadavatel;
  }
}
?>
