<?php
  session_start();
  if ($_SESSION['admin_logged']==0){
    header("location: ../login.php");
  }
  require __DIR__ . '/../../init.php';
  require __DIR__ . '/translate/en.php';
  //include_once 'Service.php';
  include ABSPATH."adminlogin/theme/page.php";
  $adminV = new AdminView();
  $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 10); //firma a zakaznici
  if($per < 1){
    die();
  }

  if($per == 3){
    if (isset($_POST['save'])){
      $parametre = array(
        "meno" => $_POST['meno'],
        "firma" => $_POST['firma'],
        "firma_popis" => $_POST['firma_popis'],
        "ico" => $_POST['ico'],
        "dic" => $_POST['dic'],
        "ic_dph" => $_POST['ic_dph'],
        "ulica_cislo" => $_POST['ulica_cislo'],
        "psc" => $_POST['psc'],
        "mesto" => $_POST['mesto'],
        "telefon" => $_POST['telefon'],
        "email1" => $_POST['email1'],
        "email2" => $_POST['email2'],
        "email3" => $_POST['email3'],
        "predajca" => $_POST['predajca'],
        "zakaznik" => $_POST['zakaznik'],
      );
      $zadavatelC = new ZadavatelController();
      $zadavatelC->update_zadavatel($parametre, $_GET['s']);
      //header("Refresh:0");
    }
  }

  $zadavatelV = new ZadavatelView();
  if($zadavatelV->is_predajca($_GET['s'])){
    $data = $zadavatelV->get_predajca($_GET['s']);
  } else {
    $data = $zadavatelV->get_zakaznik($_GET['s']);
  }
  $data = $zadavatelV->get_data($_GET['s']);

  ?>
<link rel="stylesheet" href="css/zadavatel.css">
<link rel="stylesheet" href="../../theme/vendor/lightbox/css/lightbox.min.css">

<script src="../../theme/vendor/lightbox/js/lightbox-plus-jquery.min.js"></script>
  <?php
    if($_SESSION["message"]=="duplicit"){
      $servisny_list = $_SESSION['message_data'];
      echo '<div class="alert alert-warning">
            <strong>Duplicitná hodnota!</strong> Servisný list: '.$servisny_list.' už bol vložený.
          </div>';
    }
    $_SESSION["message"] = "";
    $_SESSION['message_data'] = "";
  ?>

<div class="row">
<div class="col-sm-12 col-md-12">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Upraviť informácie</h6>
    </div>
    <div class="card-body">
      <form method="POST">
        <div class="form-group row" id="poznamka">
          <div class="row">
            <div class="col col-sm-12 col-md-12 col-lg-3 col-xl-2 ">
              <label for="meno" class="col-form-label">Kontaktná osoba:</label>
            </div>
            <div class="col col-sm-12 col-md-12 col-lg-9 col-xl-10">
              <input type="text" name="meno" class="form-control bg-light border-1" value="<?php echo $data['meno']?>">
            </div>

            <div class="col col-sm-12 col-md-12 col-lg-3 col-xl-2">
              <label for="email1" class="col-form-label">Email 1:</label>
            </div>
            <div class="col col-sm-12 col-md-12 col-lg-9 col-xl-10">
              <input type="text" name="email1" class="form-control bg-light border-1" value="<?php echo $data['email1']?>">
            </div>

            <div class="col col-sm-12 col-md-12 col-lg-3 col-xl-2">
              <label for="email2" class="col-form-label">Email 2:</label>
            </div>
            <div class="col col-sm-12 col-md-12 col-lg-9 col-xl-10">
              <input type="text" name="email2" class="form-control bg-light border-1" value="<?php echo $data['email2']?>">
            </div>

            <div class="col col-sm-12 col-md-12 col-lg-3 col-xl-2">
              <label for="email3" class="col-form-label">Email 3:</label>
            </div>
            <div class="col col-sm-12 col-md-12 col-lg-9 col-xl-10">
              <input type="text" name="email3" class="form-control bg-light border-1" value="<?php echo $data['email3']?>">
            </div>

            <div class="col col-sm-12 col-md-12 col-lg-3 col-xl-2">
              <label for="telefon" class="col-form-label">Telefón:</label>
            </div>
            <div class="col col-sm-12 col-md-12 col-lg-9 col-xl-10">
              <input type="text" name="telefon" class="form-control bg-light border-1" value="<?php echo $data['telefon']?>">
            </div>

            <div class="col col-sm-12 col-md-12 col-lg-3 col-xl-2">
              <label for="firma" class="col-form-label">Firma:</label>
            </div>
            <div class="col col-sm-12 col-md-12 col-lg-9 col-xl-10">
              <input type="text" name="firma" class="form-control bg-light border-1" value="<?php echo $data['firma']?>">
            </div>

            <div class="col col-sm-12 col-md-12 col-lg-3 col-xl-2">
              <label for="firma_popis" class="col-form-label">Firma popis:</label>
            </div>
            <div class="col col-sm-12 col-md-12 col-lg-9 col-xl-10">
              <input type="text" name="firma_popis" class="form-control bg-light border-1" value="<?php echo $data['firma_popis']?>">
            </div>

            <div class="col col-sm-12 col-md-12 col-lg-3 col-xl-2">
              <label for="ico" class="col-form-label">IČO:</label>
            </div>
            <div class="col col-sm-12 col-md-12 col-lg-9 col-xl-10">
              <input type="text" name="ico" class="form-control bg-light border-1" value="<?php echo $data['ico']?>">
            </div>

            <div class="col col-sm-12 col-md-12 col-lg-3 col-xl-2">
              <label for="dic" class="col-form-label">DIČ:</label>
            </div>
            <div class="col col-sm-12 col-md-12 col-lg-9 col-xl-10">
              <input type="text" name="dic" class="form-control bg-light border-1" value="<?php echo $data['dic']?>">
            </div>

            <div class="col col-sm-12 col-md-12 col-lg-3 col-xl-2">
              <label for="ic_dph" class="col-form-label">IČ DPH:</label>
            </div>
            <div class="col col-sm-12 col-md-12 col-lg-9 col-xl-10">
              <input type="text" name="ic_dph" class="form-control bg-light border-1" value="<?php echo $data['ic_dph']?>">
            </div>

            <div class="col col-sm-12 col-md-12 col-lg-3 col-xl-2">
              <label for="ulica_cislo" class="col-form-label">Ulica a číslo:</label>
            </div>
            <div class="col col-sm-12 col-md-12 col-lg-9 col-xl-10">
              <input type="text" name="ulica_cislo" class="form-control bg-light border-1" value="<?php echo $data['ulica_cislo']?>">
            </div>

            <div class="col col-sm-12 col-md-12 col-lg-3 col-xl-2">
              <label for="psc" class="col-form-label">PSČ:</label>
            </div>
            <div class="col col-sm-12 col-md-12 col-lg-9 col-xl-10">
              <input type="text" name="psc" class="form-control bg-light border-1" value="<?php echo $data['psc']?>">
            </div>

            <div class="col col-sm-12 col-md-12 col-lg-3 col-xl-2">
              <label for="mesto" class="col-form-label">Mesto:</label>
            </div>
            <div class="col col-sm-12 col-md-12 col-lg-9 col-xl-10">
              <input type="text" name="mesto" class="form-control bg-light border-1" value="<?php echo $data['mesto']?>">
            </div>

            <div class="col col-sm-12 col-md-12 col-lg-3 col-xl-2">
              <label for="predajca" class="col-form-label">predajca:</label>
            </div>
            <div class="col col-sm-12 col-md-12 col-lg-9 col-xl-10">
              <input type="checkbox" name="predajca" class="bg-light border-1" <?php echo ($data['predajca']==1)?"checked":"";?> value="1">
            </div>

            <div class="col col-sm-12 col-md-12 col-lg-3 col-xl-2">
              <label for="zakaznik" class="col-form-label">Zákazník:</label>
            </div>
            <div class="col col-sm-12 col-md-12 col-lg-9 col-xl-10">
              <input type="checkbox" name="zakaznik" class="bg-light border-1" <?php echo ($data['zakaznik']==1)?"checked":"";?> value="1">
            </div>
          </div>
          <?php
          if($per == 3){
          ?>
          <div class="row">
            <div class="col-sm-12 col-md-3 col-lg-2 col-xl-2">
              <button type="submit" class="btn btn-success btn-icon-split col-sm-12 col-md-12" name="save" id="save">
                <span class="text">Uložiť</span>
              </button>
            </div>
          </div>
        <?php }?>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
  include ABSPATH."adminlogin/theme/footer.php";
?>
