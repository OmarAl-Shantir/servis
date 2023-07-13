<?php
  session_start();
  if ($_SESSION['admin_logged']==0){
    header("location: login.php");
  }
  if(isset($_POST['add_record_auto'])){

    $parametre = array(
      'sposob_prepravy' => $_POST['delivery_in'],
      'cislo_prepravy' => (empty($_POST['cislo_prepravy']))?"":"#".$_POST['cislo_prepravy'],
      'cislo_reklamacie_predajcu' => $_POST['cislo_reklamacie_predajcu'],
      'vzdialenost' => $_POST['vzdialenost'],
      'hmotnost' => $_POST['hmotnost'],
      'servisny_list' => $_POST['service_list'],
      'product_ref' => $_POST['product_ref'],
      'firma' => $_POST['shop'],
      'info' => $_POST['info'],
      'datum_vzniku' => $_POST['datum_vzniku'],
      'datum_prijatia' => $_POST['datum_prijatia'],
      'email_zakaznika' => $_POST['email_zakaznika'],
      'id_typ' => $_POST['id_typ']
    );
    $serviceC = new ServiceController();
    $serviceV = new ServiceView();
    if($serviceV->isRegistered($_POST['service_list'])){
      $_SESSION["message"] = "duplicit";
      $_SESSION["message_data"] = $_POST['service_list'];
      header("Location:?". $_SERVER['QUERY_STRING']);
    } else {
      $id_opravy = $serviceC->add_zarucny_service_from_eta($parametre);
      $serviceC->changeStatus($id_opravy, 2, $_SESSION['admin_logged']);
      $_SESSION["message"] = "saved";
      $_SESSION["message_data"] = $id_opravy;
      header("Location:?". $_SERVER['QUERY_STRING']);
    }
    die();
  }

  if(isset($_POST['add_record_manual'])){
    $parametre = array(
      'id_typ' => $_POST['id_typ'],
      'id_delivery_in' => $_POST['delivery_in'],
      'cislo_prepravy' => (empty($_POST['cislo_prepravy']))?"":"#".$_POST['cislo_prepravy'],
      'cislo_reklamacie_predajcu' => $_POST['cislo_reklamacie_predajcu'],
      'vzdialenost' => $_POST['vzdialenost'],
      'hmotnost' => $_POST['hmotnost'],
      'servisny_list' => $_POST['service_list'],
      'product_ref' => $_POST['product_ref'],
      'firma' => $_POST['shop'],
      'info' => $_POST['info'],
      'datum_vzniku' => $_POST['datum_vzniku'],
      'datum_prijatia' => $_POST['datum_prijatia'],
      'email_zakaznika' => $_POST['email_zakaznika'],
      'id_predajcu' => $_POST['id_predajcu'],
      'predajca_meno' => $_POST['predajca_meno'],
      'predajca_ico' => $_POST['predajca_ico'],
      'predajca_dic' => $_POST['predajca_dic'],
      'predajca_ic_dph' => $_POST['predajca_ic_dph'],
      'predajca_firma' => $_POST['predajca_firma'],
      'predajca_adresa' => $_POST['predajca_adresa'],
      'predajca_psc' => $_POST['predajca_psc'],
      'predajca_mesto' => $_POST['predajca_mesto'],
      'predajca_telefon' => $_POST['predajca_telefon'],
      'predajca_mail' => $_POST['predajca_mail'],
      'id_zakaznik' => $_POST['id_zakaznik'],
      'obj_meno' => $_POST['obj_meno'],
      'obj_ico' => $_POST['obj_ico'],
      'obj_dic' => $_POST['obj_dic'],
      'obj_ic_dph' => $_POST['obj_ic_dph'],
      'obj_firma' => $_POST['obj_firma'],
      'obj_adresa' => $_POST['obj_adresa'],
      'obj_psc' => $_POST['obj_psc'],
      'obj_mesto' => $_POST['obj_mesto'],
      'obj_telefon' => $_POST['obj_telefon'],
      'obj_mail' => $_POST['obj_mail'],
      'datum_kupy' => $_POST['datum_kupy'],
      'vyrobne_cislo' => $_POST['vyrobne_cislo'],
      'pozadovane_riesenie' => $_POST['pozadovane_riesenie'],
      'original_obal' => $_POST['original_obal'],
      'prislusenstvo' => $_POST['prislusenstvo'],
      'stav_vyrobku' => $_POST['stav'],
      'popis' => $_POST['popis_chyby'],
      'pocet_vyjadreni' => $_POST['serv_vyjadrenia'],
      'predajca_je_objednavatel' => $_POST['predajca_je_objednavatel']
    );
    $serviceC = new ServiceController();
    $id_opravy = $serviceC->add_service($parametre);
    $_SESSION["message"] = "saved";
    $_SESSION["message_data"] = $id_opravy;
    header("Location:?". $_SERVER['QUERY_STRING']);
    die();
  }
?>
<script type="text/javascript" src="js/service.js"></script>
<link rel="stylesheet" href="css/servis.css">
<?php
  if($_SESSION["message"]=="saved"){
    $service_item_id = $_SESSION['message_data'];
    echo '<div class="alert alert-success">
          <strong>Uložené!</strong> Záznam bol úspešne uložený do servisného systému pod ID '.$service_item_id.'.
        </div>';

    header("Location: detail.php?s=$service_item_id");
  }
  if($_SESSION["message"]=="duplicit"){
    $servisny_list = $_SESSION['message_data'];
    echo '<div class="alert alert-warning">
          <strong>Duplicitná hodnota!</strong> Servisný list: '.$servisny_list.' už bol vložený.
        </div>';
  }
  $_SESSION["message"] = "";
  $_SESSION['message_data'] = "";
?>
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Doručenie</h6>
  </div>
  <div class="card-body">
    <div class="form-group row">
      <label for="service_list" class="col-sm-2 col-form-label">Spôsob:</label>
      <div class="col-sm-10">
        <select class="form-control bg-light border-1" id="deliveryIn" onChange="checkDeliveryIn()">
          <?php
            $serviceV = new ServiceView();
            $delivery_types = $serviceV->getDelivery();
            foreach ($delivery_types as $id => $delivery_type){
              if($id == 1){
                echo "<option id='sposob_$id' value='".$id."' selected>".$delivery_type['description']."</option>";
              } else {
                echo "<option id='sposob_$id' value='".$id."'>".$delivery_type['description']."</option>";
              }
            }
          ?>
        </select>
      </div>
    </div>
    <div class="form-group row" id="cislo_prepravy">
      <label for="service_list" class="col-sm-2 col-form-label">Číslo prepravy:</label>
      <div class="col-sm-10">
        <input type="text" name="cislo_prepravy" class="form-control bg-light border-1" id="cislo_prepravy_input" onChange="preprava_copy()">
      </div>
    </div>
    <div class="form-group row" id="cislo_reklamacie_predajcu">
      <label for="service_list" class="col-sm-2 col-form-label">Číslo reklamácie predajcu:</label>
      <div class="col-sm-10">
        <input type="text" name="cislo_reklamacie_predajcu" class="form-control bg-light border-1" id="cislo_reklamacie_predajcu_input" onChange="preprava_copy()">
      </div>
    </div>
    <div class="form-group row" id="vzdialenost" style="display:none;">
      <label for="service_list" class="col-sm-2 col-form-label">Vzdialenosť [km]:</label>
      <div class="col-sm-10">
        <input type="text" name="vzdialenost" class="form-control bg-light border-1" id="vzdialenost_input" onChange="preprava_copy()">
      </div>
    </div>
    <div class="form-group row" id="hmotnost">
      <label for="service_list" class="col-sm-2 col-form-label">Hmotnosť [kg]:</label>
      <div class="col-sm-10">
        <input type="number" name="hmotnost" class="form-control bg-light border-1" id="hmotnost_input" onChange="preprava_copy()">
      </div>
    </div>
  </div>
</div>
<div class="card shadow mb-4">
  <div class="card-body">
    <div class="row">
      <div class="form-check col-lg-6">
        <input type="checkbox" class="btn-check" name="servis_input" id="manual_input" autocomplete="off" onChange="zarucnaReklamaciaManual()">
        <label class="btn btn-outline-secondary" for="manual_input">Nová reklamácia</label>
      </div>
      <div class="form-check col-lg-6">
        <input type="checkbox" class="btn-check" name="servis_input" id="auto_input" autocomplete="off" checked onChange="zarucnaReklamaciaAuto()">
        <label class="btn btn-outline-secondary" for="auto_input">Vložiť z ETA systému</label>
      </div>
    </div>
  </div>
</div>

<div class="card shadow mb-4" id="typ_reklamacie">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Typ reklamácie</h6>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="form-check col-lg-4">
        <input type="checkbox" class="btn-check" name="servis_type" id="zarucna_reklamacia" autocomplete="off" checked onChange="zarucnaReklamaciaTyp()">
        <label class="btn btn-outline-secondary" for="zarucna_reklamacia">Záručná</label>
      </div>
      <div class="form-check col-lg-4">
        <input type="checkbox" class="btn-check" name="servis_type" id="predpredajna_reklamacia" autocomplete="off" onChange="predpredajnaReklamaciaTyp()">
        <label class="btn btn-outline-secondary" for="predpredajna_reklamacia">Predpredajná</label>
      </div>
      <div class="form-check col-lg-4">
        <input type="checkbox" class="btn-check" name="servis_type" id="bleskova_vymena" autocomplete="off" onChange="bleskovaVymenaTyp()">
        <label class="btn btn-outline-secondary" for="bleskova_vymena">Blesková výmena</label>
      </div>
    </div>
  </div>
</div>

<form method="POST" id="auto_form_input">
  <div class="card shadow mb-4" id="automatic">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Vložiť z ETA reklamačného systému</h6>
    </div>
    <div class="card-body">
      <div class="form-group row">
        <div class="col-sm-12">
          <input type="text" class="form-control bg-light border-1" id="pasteText" placeholder="Vlož text ..." onChange="textSeparate()">
        </div>
      </div>
      <input type="hidden" name="delivery_in" id="paste_delivery_in">
      <input type="text" name="cislo_prepravy" id="paste_cislo_prepravy" pattern="70+[0-9]+" required style="display: none;">
      <span id="message"></span>
      <input type="hidden" name="cislo_reklamacie_predajcu" id="paste_cislo_reklamacie_predajcu">
      <input type="hidden" name="vzdialenost" id="paste_vzdialenost">
      <input type="hidden" name="hmotnost" id="paste_hmotnost">
      <input type="hidden" name="id_typ" id="paste_id_typ" value="1">
      <div class="form-group row">
        <label for="service_list" class="col-sm-2 col-form-label">Servisný list:</label>
        <div class="col-sm-10">
          <input type="text" name="service_list" class="form-control bg-light border-1" id="service_list" onblur="checkRequirementsAuto()">
        </div>
      </div>
      <div class="form-group row">
        <label for="product_ref" class="col-sm-2 col-form-label">Ref. číslo:</label>
        <div class="col-sm-10">
          <input type="text" name="product_ref" class="form-control bg-light border-1" id="product_ref" onblur="checkRequirementsAuto()">
        </div>
      </div>
      <div class="form-group row">
        <label for="shop" class="col-sm-2 col-form-label">Obchod:</label>
        <div class="col-sm-10">
          <input type="text" name="shop" class="form-control bg-light border-1" id="shop" onblur="checkRequirementsAuto()">
        </div>
      </div>
      <div class="form-group row">
        <label for="info" class="col-sm-2 col-form-label">Info:</label>
        <div class="col-sm-10">
          <input type="text" name="info" class="form-control bg-light border-1" id="info" onblur="checkRequirementsAuto()">
        </div>
      </div>
      <div class="form-group row">
        <label for="date_start" class="col-sm-2 col-form-label">Dátum vzniku:</label>
        <div class="col-sm-10">
          <input type="text" name="datum_vzniku" class="form-control bg-light border-1" id="date_start" onblur="checkRequirementsAuto()">
        </div>
      </div>
      <div class="form-group row">
        <label for="date_adopt" class="col-sm-2 col-form-label">Dátum prijatia:</label>
        <div class="col-sm-10">
          <input type="text" name="datum_prijatia" class="form-control bg-light border-1" id="date_adopt" onblur="checkRequirementsAuto()" required>
        </div>
      </div>
      <div class="form-group row">
        <label for="email_zakaznik" class="col-sm-2 col-form-label">Email na zákazníka:</label>
        <div class="col-sm-10">
          <input type="text" name="email_zakaznika" class="form-control bg-light border-1" id="email_zakaznik" onblur="checkRequirementsAuto()">
        </div>
      </div>
    </div>
  </div>

  <div class="form-group row">
    <div class="col-sm-2 right">
      <button type="submit" class="btn btn-success btn-icon-split" name="add_record_auto" id="add_record_auto">
        <span class="icon text-white-50">
          <i class="fas fa-check"></i>
        </span>
        <span class="text">Pridať</span>
      </button>
    </div>
  </div>
</form>

<form method="POST"  id="manual_form_input" style="display: none;">
  <div class="card shadow mb-4" id="predajca">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Predajca</h6>
    </div>
    <input type="hidden" name="delivery_in" id="new_delivery_in">
    <input type="text" name="cislo_prepravy" id="new_cislo_prepravy" pattern="70+[0-9]+" required style="display: none;">
    <span id="message"></span>
    <input type="hidden" name="cislo_reklamacie_predajcu" id=new_cislo_reklamacie_predajcu>
    <input type="hidden" name="vzdialenost" id="new_vzdialenost">
    <input type="hidden" name="hmotnost" id="new_hmotnost">
    <input type="hidden" name="id_typ" id="new_id_typ" value="1">
    <div class="card-body">
      <div class="row">
        <div class="form-group col-lg-6">
          <button type="button" class="btn btn-outline-secondary" onClick="clearForm('obj_')">Vyčistiť</button>
        </div>
      </div>
      <div class="form-group row" id="hladat_predajcu">
        <label class="col-sm-2 col-form-label">Hľadať v databáze (ID): </label>
        <div class="col-sm-10">
          <?php
            $predajcovia = $serviceV->get_predajca();
            $js_predajcovia = json_encode($predajcovia);
          ?>
          <input class="form-control bg-light border-1" name = "id_predajcu" id="hladat_predajcu_input" list="predajca_list" onchange='fillPredajca(this.value, <?php echo $js_predajcovia;?>)'>
          <datalist id="predajca_list">
            <?php
              foreach ($predajcovia as $id => $nazov) {
                echo "<option value='$id'>$nazov[firma]</option>";
              }
            ?>
          </datalist>
        </div>
      </div>
      <div class="form-group row" id="div_pradajca_meno">
        <label for="product_ref" class="col-sm-2 col-form-label">Meno:</label>
        <div class="col-sm-10">
          <input type="text" name="predajca_meno" class="form-control bg-light border-1" id="predajca_meno" onchange="predajca_manual()">
        </div>
      </div>
      <div class="form-group row" id="div_pradajca_firma">
        <label for="product_ref" class="col-sm-2 col-form-label">Firma:</label>
        <div class="col-sm-10">
          <input type="text" name="predajca_firma" class="form-control bg-light border-1" id="predajca_firma" onchange="predajca_manual()">
        </div>
      </div>
      <div class="form-group row" id="div_pradajca_ico">
        <label for="service_list" class="col-sm-2 col-form-label">IČO:</label>
        <div class="col-sm-10">
          <input type="text" name="predajca_ico" class="form-control bg-light border-1" id="predajca_ico" data-type="hladat_predajcu_input" onChange="searchICO(this,'predajca_')">
        </div>
      </div>
      <div class="form-group row" id="div_pradajca_dic">
        <label for="service_list" class="col-sm-2 col-form-label">DIČ:</label>
        <div class="col-sm-10">
          <input type="text" name="predajca_dic" class="form-control bg-light border-1" id="predajca_dic" onchange="predajca_manual()">
        </div>
      </div>
      <div class="form-group row" id="div_pradajca_ic_dph">
        <label for="service_list" class="col-sm-2 col-form-label">IČ DPH:</label>
        <div class="col-sm-10">
          <input type="text" name="predajca_ic_dph" class="form-control bg-light border-1" id="predajca_ic_dph" onchange="predajca_manual()">
        </div>
      </div>
      <div class="form-group row" id="div_pradajca_adresa">
        <label for="product_ref" class="col-sm-2 col-form-label">Ulica a číslo:</label>
        <div class="col-sm-10">
          <input type="text" name="predajca_adresa" class="form-control bg-light border-1" id="predajca_adresa" onchange="predajca_manual()">
        </div>
      </div>
      <div class="form-group row" id="div_pradajca_psc">
        <label for="shop" class="col-sm-2 col-form-label">PSČ:</label>
        <div class="col-sm-10">
          <input type="text" name="predajca_psc" class="form-control bg-light border-1" id="predajca_psc" onchange="predajca_manual()">
        </div>
      </div>
      <div class="form-group row" id="div_pradajca_mesto">
        <label for="info" class="col-sm-2 col-form-label">Mesto:</label>
        <div class="col-sm-10">
          <input type="text" name="predajca_mesto" class="form-control bg-light border-1" id="predajca_mesto" onchange="predajca_manual()">
        </div>
      </div>
      <div class="form-group row" id="div_pradajca_telefon">
        <label for="date_start" class="col-sm-2 col-form-label">Telefón:</label>
        <div class="col-sm-10">
          <input type="text" name="predajca_telefon" class="form-control bg-light border-1" id="predajca_telefon" onchange="predajca_manual()">
        </div>
      </div>
      <div class="form-group row" id="div_pradajca_email">
        <label for="date_adopt" class="col-sm-2 col-form-label">E-mail:</label>
        <div class="col-sm-10">
          <input type="text" name="predajca_mail" class="form-control bg-light border-1" id="predajca_mail" onchange="predajca_manual()">
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow mb-4" id="predajca-objednavatel">
    <div class="card-body">
      <div class="row">
        <div class="form-check col-lg-4">
          <input type="checkbox" class="btn-check" name="predajca_je_objednavatel" id="predajca_je_objednavatel" value="1" autocomplete="off" onChange="predajcaObjednavatel()">
          <label class="btn btn-outline-secondary" for="predajca_je_objednavatel" id="predajca_je_objednavatel_label">Predajca nie je objednávatel</label>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow mb-4" id="objednavatel">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Objednávateľ</h6>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="form-check col-lg-6">
          <input type="checkbox" class="btn-check" name="nema_ico" id="nema_ico" autocomplete="off" onChange="nemaICO()">
          <label class="btn btn-outline-secondary" for="nema_ico">nemá IČO</label>
        </div>
        <div class="form-check col-lg-6">
          <input type="checkbox" class="btn-check" name="ma_ico" id="ma_ico" autocomplete="off" checked onChange="maICO()">
          <label class="btn btn-outline-secondary" for="ma_ico">má IČO</label>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="form-check col-lg-6">
          <button type="button" class="btn btn-outline-secondary" onClick="clearForm('obj_')">Vyčistiť</button>
        </div>
      </div>
      <div class="form-group row" id="hladat_objednavatel_without_ico" style="display: none;">
        <label class="col-sm-2 col-form-label">Hľadať v databáze (ID): </label>
        <div class="col-sm-10">
          <?php
            $objednavatelia_bez_ico = $serviceV->get_objednavatel_without_ico();
            $js_objednavatelia_bez_ico = json_encode($objednavatelia_bez_ico);
          ?>
          <input class="form-control bg-light border-1" id="hladat_nema_ico" list="obj_list_nema_ico" onchange='fillObjednavatel(this.value, <?php echo $js_objednavatelia_bez_ico;?>)'>
          <datalist id="obj_list_nema_ico">
            <?php
              foreach ($objednavatelia_bez_ico as $id => $nazov) {
                echo "<option value='$id'>$nazov[firma]</option>";
              }
            ?>
          </datalist>
        </div>
      </div>
      <div class="form-group row" id="hladat_objednavatel_with_ico">
        <label class="col-sm-2 col-form-label">Hľadať v databáze (ID): </label>
        <div class="col-sm-10">
          <?php
            $objednavatelia_s_ico = $serviceV->get_objednavatel_with_ico();
            $js_objednavatelia_s_ico = json_encode($objednavatelia_s_ico);
          ?>
          <input class="form-control bg-light border-1" id="hladat_ma_ico" list="obj_list_ma_ico" onchange='fillObjednavatel(this.value, <?php echo $js_objednavatelia_s_ico;?>)'>
          <datalist id="obj_list_ma_ico">
            <?php
              foreach ($objednavatelia_s_ico as $id => $nazov) {
                echo "<option value='$id'>$nazov[firma]</option>";
              }
            ?>
          </datalist>
        </div>
      </div>
      <input type="hidden" name="id_zakaznik" id="id_zakaznik_input">
      <div class="form-group row" id="div_obj_firma">
        <label for="service_list" class="col-sm-2 col-form-label">Firma:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_firma" class="form-control bg-light border-1" id="obj_firma">
        </div>
      </div>
      <div class="form-group row" id="div_obj_ico">
        <label for="service_list" class="col-sm-2 col-form-label">IČO:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_ico" class="form-control bg-light border-1" id="obj_ico" data-type="hladat_predajcu_input" onChange="searchICO(this, 'obj_')">
        </div>
      </div>
      <div class="form-group row" id="div_obj_dic">
        <label for="service_list" class="col-sm-2 col-form-label">DIČ:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_dic" class="form-control bg-light border-1" id="obj_dic">
        </div>
      </div>
      <div class="form-group row" id="div_obj_ic_dph">
        <label for="service_list" class="col-sm-2 col-form-label">IČ DPH:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_ic_dph" class="form-control bg-light border-1" id="obj_ic_dph">
        </div>
      </div>
      <div class="form-group row" id="div_obj_meno">
        <label for="service_list" class="col-sm-2 col-form-label">Meno:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_meno" class="form-control bg-light border-1" id="obj_meno">
        </div>
      </div>
      <div class="form-group row" id="div_adresa">
        <label for="product_ref" class="col-sm-2 col-form-label">Ulica a číslo:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_adresa" class="form-control bg-light border-1" id="obj_adresa">
        </div>
      </div>
      <div class="form-group row" id="div_psc">
        <label for="shop" class="col-sm-2 col-form-label">PSČ:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_psc" class="form-control bg-light border-1" id="obj_psc">
        </div>
      </div>
      <div class="form-group row" id="div_mesto">
        <label for="info" class="col-sm-2 col-form-label">Mesto:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_mesto" class="form-control bg-light border-1" id="obj_mesto">
        </div>
      </div>
      <div class="form-group row" id="div_telefon">
        <label for="date_start" class="col-sm-2 col-form-label">Telefón:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_telefon" class="form-control bg-light border-1" id="obj_telefon">
        </div>
      </div>
      <div class="form-group row" id="div_email">
        <label for="date_adopt" class="col-sm-2 col-form-label">E-mail:</label>
        <div class="col-sm-10">
          <input type="email" name="obj_mail" class="form-control bg-light border-1" id="obj_mail">
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow mb-4" id="produkt">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Produkt</h6>
    </div>
    <div class="card-body">
      <div class="form-group row" id="datum_kupy_div">
        <label for="service_list" class="col-sm-2 col-form-label">Dátum kúpy/predaja:</label>
        <div class="col-sm-10">
          <input type="date" name="datum_kupy" class="form-control bg-light border-1" id="datum_kupy">
        </div>
      </div>
      <div class="form-group row">
        <label for="service_list" class="col-sm-2 col-form-label">Dátum vzniku reklamácie:</label>
        <div class="col-sm-10">
          <input type="date" name="datum_vzniku" class="form-control bg-light border-1" id="datum_vzniku">
        </div>
      </div>
      <div class="form-group row">
        <label for="service_list" class="col-sm-2 col-form-label">Dátum prijatia do servisu:</label>
        <div class="col-sm-10">
          <input type="date" name="datum_prijatia" class="form-control bg-light border-1" id="datum_prijatia">
        </div>
      </div>
      <div class="form-group row">
        <label for="service_list" class="col-sm-2 col-form-label">Výrobné číslo:</label>
        <div class="col-sm-10">
          <input type="text" name="vyrobne_cislo" class="form-control bg-light border-1" id="vyrobne_cislo">
        </div>
      </div>
      <div class="form-group row">
        <label for="service_list" class="col-sm-2 col-form-label">Požadované riešenie:</label>
        <div class="col-sm-10">
          <select class="form-control bg-light border-1" name="pozadovane_riesenie" id="pozadovane_riesenie">
            <option value='Oprava'>Oprava</option>
            <option value='Výmena'>Výmena</option>
            <option value='Odstúpenie od zmluvy'>Odstúpenie od zmluvy</option>
            <option value='Zľava'>Zľava</option>
            <option value='Doplnenie príslušenstva'>Doplnenie príslušenstva</option>
          </select>
        </div>
      </div>
      <div class="form-group row">
        <label for="product_ref" class="col-sm-2 col-form-label">Originálny obal:</label>
        <div class="col-sm-10">
          <select class="form-control bg-light border-1" name="original_obal" id="orig_obal">
            <option value='Áno'>Áno</option>
            <option value='Nie'>Nie</option>
          </select>
        </div>
      </div>
      <div class="form-group row">
        <label for="shop" class="col-sm-2 col-form-label">Príslušenstvo:</label>
        <div class="col-sm-10">
          <input type="textarea" name="prislusenstvo" class="form-control bg-light border-1" id="prislusenstvo">
        </div>
      </div>
      <div class="form-group row">
        <label for="info" class="col-sm-2 col-form-label">Stav výrobku:</label>
        <div class="col-sm-10">
          <input type="textarea" name="stav" class="form-control bg-light border-1" id="stav">
        </div>
      </div>
      <div class="form-group row">
        <label for="date_start" class="col-sm-2 col-form-label">Popis chyby:</label>
        <div class="col-sm-10">
          <input type="textarea" name="popis_chyby" class="form-control bg-light border-1" id="chyba">
        </div>
      </div>
      <div class="form-group row">
        <label for="date_adopt" class="col-sm-2 col-form-label">Počet servisných vyjadrení:</label>
        <div class="col-sm-10">
          <select class="form-control bg-light border-1" name="serv_vyjadrenia" id="serv_vyjadrenia">
            <option value='0'>0</option>
            <option value='1'>1</option>
            <option value='2'>2</option>
            <option value='3'>3</option>
            <option value='4'>4</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="form-group row">
    <div class="col-sm-2 right">
      <button type="submit" class="btn btn-success btn-icon-split" name="add_record_manual" id="add_record_manual">
        <span class="icon text-white-50">
          <i class="fas fa-check"></i>
        </span>
        <span class="text">Pridať</span>
      </button>
    </div>
  </div>
</form>
<script type="text/javascript">checkRequirementsAuto();</script>
<script type="text/javascript">checkRequirementsManual();</script>
