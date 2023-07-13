<?php
  session_start();
  if ($_SESSION['admin_logged']==0){
    header("location: login.php");
  }
  if(isset($_POST['add_record_auto'])){
    $parametre = array(
      'sposob_prepravy' => $_POST['delivery_in'],
      'cislo_prepravy' => $_POST['cislo_prepravy'],
      'vzdialenost' => $_POST['vzdialenost'],
      'hmotnost' => $_POST['hmotnost'],
      'servisny_list' => $_POST['service_list'],
      'product_ref' => $_POST['product_ref'],
      'firma' => $_POST['shop'],
      'info' => $_POST['info'],
      'datum_vzniku' => $_POST['datum_vzniku'],
      'datum_prijatia' => $_POST['datum_prijatia'],
      'email_zakaznika' => $_POST['email_zakaznika'],
      'id_typ' => 3
    );
    $serviceC = new ServiceController();
    $id_opravy = $serviceC->add_zarucny_service($parametre);
    $_SESSION["message"] = "saved";
    $_SESSION["message_data"] = $id_opravy;
    header("Location:?". $_SERVER['QUERY_STRING']);
    die();
  }
?>
<script type="text/javascript" src="js/service.js"></script>
<?php
 if($_SESSION["message"]=="saved"){
  echo '<div class="alert alert-success">
          <strong>Uložené!</strong> Záznam bol úspešne uložený do servisného systému pod ID '.$_SESSION['message_data'].'.
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
            $s = new ServiceView();
            $delivery_types = $s->getDeliveriIn();
            foreach ($delivery_types as $id => $delivery_type){
              echo "<option value='".$id."'>".$delivery_type['description']."</option>";
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
    <div class="form-group row" id="vzdialenost" style="display:none;">
      <label for="service_list" class="col-sm-2 col-form-label">Vzdialenosť [km]:</label>
      <div class="col-sm-10">
        <input type="text" name="vzdialenost" class="form-control bg-light border-1" id="vzdialenost_input" onChange="preprava_copy()">
      </div>
    </div>
    <div class="form-group row" id="hmotnost">
      <label for="service_list" class="col-sm-2 col-form-label">Hmotnosť [kg]:</label>
      <div class="col-sm-10">
        <input type="text" name="hmotnost" class="form-control bg-light border-1" id="hmotnost_input" onChange="preprava_copy()">
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
        <input type="checkbox" class="btn-check" name="servis_input" id="auto_input" autocomplete="off"checked onChange="zarucnaReklamaciaAuto()">
        <label class="btn btn-outline-secondary" for="auto_input">Vložiť z ETA systému</label>
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
      <input type="hidden" name="cislo_prepravy" id="paste_cislo_prepravy">
      <input type="hidden" name="vzdialenost" id="paste_vzdialenost">
      <input type="hidden" name="hmotnost" id="paste_hmotnost">
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
          <input type="text" name="datum_prijatia" class="form-control bg-light border-1" id="date_adopt" onblur="checkRequirementsAuto()">
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
  <div class="card shadow mb-4" id="manual">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Predajca</h6>
    </div>
    <input type="hidden" name="cislo_prepravy" id=new_cislo_prepravy>
    <input type="hidden" name="vzdialenost" id="new_vzdialenost">
    <input type="hidden" name="hmotnost" id="new_hmotnost">
    <div class="card-body">
      <div class="form-group row">
        <label for="product_ref" class="col-sm-2 col-form-label">Ulica a číslo:</label>
        <div class="col-sm-10">
          <input type="text" name="predajca_adresa" class="form-control bg-light border-1" id="predajca_adresa">
        </div>
      </div>
      <div class="form-group row">
        <label for="shop" class="col-sm-2 col-form-label">PSČ:</label>
        <div class="col-sm-10">
          <input type="text" name="predajca_psc" class="form-control bg-light border-1" id="predajca_psc">
        </div>
      </div>
      <div class="form-group row">
        <label for="info" class="col-sm-2 col-form-label">Mesto:</label>
        <div class="col-sm-10">
          <input type="text" name="predajca_mesto" class="form-control bg-light border-1" id="predajca_mesto">
        </div>
      </div>
      <div class="form-group row">
        <label for="date_start" class="col-sm-2 col-form-label">Telefón:</label>
        <div class="col-sm-10">
          <input type="text" name="predajca_telefon" class="form-control bg-light border-1" id="predajca_telefon">
        </div>
      </div>
      <div class="form-group row">
        <label for="date_adopt" class="col-sm-2 col-form-label">E-mail:</label>
        <div class="col-sm-10">
          <input type="text" name="predajca_mail" class="form-control bg-light border-1" id="predajca_mail">
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow mb-4" id="manual">
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
      <div class="form-row">
        <div class="form-group col-lg-2" id="obj_titul_pred" style="display: none;">
          <label for="service_list">Titul pred</label>
          <input type="text" name="obj_titul_pred" class="form-control bg-light border-1" onblur="searchICO()">
        </div>
        <div class="form-group col-lg-4" id="obj_meno" style="display: none;">
          <label for="service_list">Meno</label>
          <input type="text" name="obj_meno" class="form-control bg-light border-1">
        </div>
        <div class="form-group col-lg-4" id="obj_priezisko" style="display: none;">
          <label for="service_list">Priezvisko</label>
          <input type="text" name="obj_priezisko" class="form-control bg-light border-1">
        </div>
        <div class="form-group col-lg-2" id="obj_titul_za" style="display: none;">
          <label for="service_list">Titul za</label>
          <input type="text" name="obj_titul_za" class="form-control bg-light border-1">
        </div>
      </div>
      <div class="form-group row" id="obj_ico">
        <label for="service_list" class="col-sm-2 col-form-label">IČO:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_ico" class="form-control bg-light border-1" onChange="searchICO()">
        </div>
      </div>
      <div class="form-group row" id="obj_dic">
        <label for="service_list" class="col-sm-2 col-form-label">DIČ:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_dic" class="form-control bg-light border-1" onChange="searchICO()">
        </div>
      </div>
      <div class="form-group row" id="obj_ic_dph">
        <label for="service_list" class="col-sm-2 col-form-label">IČ DPH:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_ic_dph" class="form-control bg-light border-1" onChange="searchICO()">
        </div>
      </div>
      <div class="form-group row" id="obj_firma">
        <label for="service_list" class="col-sm-2 col-form-label">Firma:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_firma" class="form-control bg-light border-1">
        </div>
      </div>
      <div class="form-group row">
        <label for="product_ref" class="col-sm-2 col-form-label">Ulica a číslo:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_adresa" class="form-control bg-light border-1" id="obj_adresa">
        </div>
      </div>
      <div class="form-group row">
        <label for="shop" class="col-sm-2 col-form-label">PSČ:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_psc" class="form-control bg-light border-1" id="obj_psc">
        </div>
      </div>
      <div class="form-group row">
        <label for="info" class="col-sm-2 col-form-label">Mesto:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_mesto" class="form-control bg-light border-1" id="obj_mesto">
        </div>
      </div>
      <div class="form-group row">
        <label for="date_start" class="col-sm-2 col-form-label">Telefón:</label>
        <div class="col-sm-10">
          <input type="text" name="obj_telefon" class="form-control bg-light border-1" id="obj_telefon">
        </div>
      </div>
      <div class="form-group row">
        <label for="date_adopt" class="col-sm-2 col-form-label">E-mail:</label>
        <div class="col-sm-10">
          <input type="email" name="obj_mail" class="form-control bg-light border-1" id="obj_mail">
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow mb-4" id="manual">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Produkt</h6>
    </div>
    <div class="card-body">
      <div class="form-group row">
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
          <input type="text" name="pozadovane_riesenie" class="form-control bg-light border-1" id="pozadovane_riesenie">
        </div>
      </div>
      <div class="form-group row">
        <label for="product_ref" class="col-sm-2 col-form-label">Originálny obal:</label>
        <div class="col-sm-10">
          <input type="checkbox" name="original_obal" class="form-control bg-light border-1" id="orig_obal">
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
          <select type="text" name="info" class="form-control bg-light border-1" id="stav">
            <option>Použitý</option>
            <option>Nový</option>
          </select>
        </div>
      </div>
      <div class="form-group row">
        <label for="date_start" class="col-sm-2 col-form-label">Popis chyby:</label>
        <div class="col-sm-10">
          <input type="textarea" name="popis_chyby" class="form-control bg-light border-1" id="chyba">
        </div>
      </div>
      <div class="form-group row">
        <label for="date_adopt" class="col-sm-2 col-form-label">Počet servisnýc vyjadrení:</label>
        <div class="col-sm-10">
          <input type="text" name="date_end" class="form-control bg-light border-1" id="serv_vyjadrenia">
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
