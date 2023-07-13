<?php
  session_start();
  if ($_SESSION['admin_logged']==0){
    header("location: ../login.php");
  }
  require __DIR__ . '/../../init.php';
  require __DIR__ . '/translate/en.php';
  //include_once 'Service.php';
  include ABSPATH."adminlogin/theme/page.php";
  include('../../theme/vendor/phpqrcode/lib/full/qrlib.php');

  $adminV = new AdminView();
  $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 5);



  $serviceV = new ServiceView();
  $serviceC = new ServiceController();
  $data = $serviceV->getRecordDetails($_GET['s']);
  if(($per == 0) && ($data['id_vybavuje'] != $_SESSION['admin_logged'])){
    header("Location: ".HOMEPAGE);
  }
  $oprava = $serviceV->getRecordHistory($_GET['s']);
  $operacie = $serviceV->getRecordOperations($_GET['s']);
  //$s_text2 = $serviceV->getStatuses($_GET['s']); //všetky možné stavy
  $s_text = $serviceV->getStatuses($_GET['s']); //všetky možné stavy
  $s_ids = array_keys($s_text); //pole id všetkých možných stavov
  /*foreach ($s_ids as $key => $id) {
    $s_text[$id]=$s_ids[$key]." - ".$s_text2[$id];
  }*/
  $s_history = $serviceV->getStatusHistory($_GET['s']); //história stavov
  $s_active = array_keys($s_history)[0]; //aktuálny stav
  $s_activep = array_intersect(array_keys($s_text),array_keys($s_history)); // už prešlo stavmi
  $cena_prace_available = array(7, 8, 9, 10, 11, 12, 16, 17, 18, 19); //stavy v ktorých je povolené zadávať cenu za prácu

  $isCenaPrace = $serviceV->is_set($_GET['s'],"Cena práce");
  $isImport = $serviceV->is_set($_GET['s'],"Import");
  $isExport = $serviceV->is_set($_GET['s'],"Export");
  $isSadzobnaJednotka = $serviceV->is_set($_GET['s'],"Sadzobná jednotka");

  foreach ($s_text as $key => $value) {
    if ($key == $s_active) {
      $s_class[$key] = "status_active";
    } else if (in_array($key, $s_activep)){
      $s_class[$key] = "status_activep";
    } else {
      $s_class[$key] = "status_normal";
    }
  }

  if ($_SESSION['admin_role'] != 2) {
    $hash = $serviceC->generateQRaccess($_GET['s'],$_SESSION['admin_logged']);
    $url = "https://servis.al-shantir.com/adminlogin/modules/servis/uploadPhotoMobile.php?token=".$hash;
    $svgCode = QRcode::svg($url);
  }
  $urlRefresh = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 7); //stav reklamácie
  if(($per > 1) || ($data['id_vybavuje'] == $_SESSION['admin_logged'])){
    if(isset($_POST['status'])){
      if($_POST['status'] == 3){
        $serviceC->addoptItem($_GET['s'],$_POST['technik']);
      } else {
        $serviceC->changeStatus($_GET['s'],$_POST['status'], $_SESSION['admin_logged']);
      }
      header("Refresh:0");
    }

    if(isset($_POST['save_vyjadrenie'])){
      $serviceC->save_vyjadrenie($_GET['s'], $_POST['vyjadrenie']);
      header("Refresh:0");
    }
  }

  if(isset($_POST['kat_ukoncenia'])){
    $serviceC->kategoria_ukoncenia($_GET['s'],$_POST['kat_ukon']);
    header("Refresh:0");
  }

  $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 4); //pridelený technik
  if($per == 3){
    if(isset($_POST['pridelit_technikovi'])){
      $serviceC->addoptItem($_GET['s'],$_POST['technik']);
      header("Refresh:0");
    }
  }

  $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 6); //ceny za opravu
  if(($per > 1) || ($data['id_vybavuje'] == $_SESSION['admin_logged'])){
    if(isset($_POST['pridat_ukon'])){
      $mnozstvo = str_replace(",",".",$_POST['mnozstvo_send']);
      $cena = str_replace(",",".",$_POST['cena_send']);
      $serviceC->addAction($_GET['s'], $_POST['ukon_send'], $cena, $mnozstvo);
      header("Refresh:0");
    }
  }

  $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 7); //stav reklamácie
  if(($per > 1) || ($data['id_vybavuje'] == $_SESSION['admin_logged'])){
    if(isset($_POST['note'])){
      $serviceC->addNote($_GET['s'], $_POST['note']);
      header("Refresh:0");
    }
  }

  $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 6); //ceny za opravu
  if($per == 3){
    if(isset($_POST['ukon_edit']) && !isset($_POST['zmazat_ukon'])){
      $serviceC->updateAction($_GET['action'], $_POST['ukon_edit'], $_POST['cena_edit'], $_POST['mnozstvo_edit']);
      $returnUrl = substr($urlRefresh,0,strpos($url,"&"));
      header("Location: $returnUrl");
    }

    if(isset($_POST['zmazat_ukon'])){
      $serviceC->deleteAction($_GET['action']);

      $returnUrl = substr($urlRefresh,0,strpos($urlRefresh,"&"));
      header("Location: $returnUrl");
    }
  }

  $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 9); //objednávka servisu
  if($per == 3){
    if(isset($_POST['ulozit_likvidacia'])){
      $serviceC->updateLikvidacia($_GET['s'], $_POST['cislo_prepravy_likvidacia']);
      header("Refresh:0");
    }

    if(isset($_POST['ulozit_dorucenie_out'])){
      $serviceC->updateDeliveryOut($_GET['s'], $_POST['id_delivery_out'], $_POST['cislo_prepravy_out'], $_POST['vzdialenost_out'], $_POST['hmotnost_out']);
      header("Refresh:0");
    }

    if(isset($_POST['save_delivery_in'])){
      $serviceC->update_delivery_in($_GET['s'], $_POST['cislo_reklamacie_predajcu_delivery_in'], $_POST['vzdialenost_delivery_in'], $_POST['hmotnost_delivery_in']);
      header("Refresh:0");
    }

    if(isset($_POST['save_product'])){
      $serviceC->update_product_info($_GET['s'], $_POST['popis_product'], $_POST['prislusenstvo_product'], $_POST['vyrobne_cislo_product'], $_POST['stav_vyrobku_product']);
      header("Refresh:0");
    }

    if(empty($data['servisny_list']) && (isset($_POST['servisny_list_update']))){
      if($serviceV->isRegistered($_POST['servisny_list'])){
        $_SESSION["message"] = "duplicit";
        $_SESSION["message_data"] = $_POST['servisny_list'];
        header("Refresh:0");
        die();
        //header("Location:?". $_SERVER['QUERY_STRING']);
      } else {
        $serviceC->addSL($_GET['s'], $_POST['servisny_list']);
        header("Refresh:0");
      }
    }
  }
  if($per == 2){
    if(isset($_POST['save_product'])){
      $_POST['popis_produktu'] = (empty($data['popis']))?$_POST['popis_produktu']:$data['popis'];
      $_POST['prislusenstvo_product'] = (empty($data['prislusenstvo']))?$_POST['prislusenstvo_product']:$data['prislusenstvo'];
      $_POST['vyrobne_cislo_product'] = (empty($data['vyrobne_cislo']))?$_POST['vyrobne_cislo_product']:$data['vyrobne_cislo'];
      $_POST['stav_vyrobku_product'] = (empty($data['stav_vyrobku']))?$_POST['stav_vyrobku_product']:$data['stav_vyrobku'];
      $serviceC->update_product_info($_GET['s'], $_POST['popis_product'], $_POST['prislusenstvo_product'], $_POST['vyrobne_cislo_product'], $_POST['stav_vyrobku_product']);
      header("Refresh:0");
    }
  }
?>
<link rel="stylesheet" href="css/servis.css">
<link rel="stylesheet" href="../../theme/vendor/lightbox/css/lightbox.min.css">

<script src="../../theme/vendor/lightbox/js/lightbox-plus-jquery.min.js"></script>
  <?php
  $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 6); //ceny za opravu
  if($per == 3){
    if(isset($_GET['action'])){
      $data = $serviceV->getRecordOperations($_GET['s'], $_GET['action']);
      ?>
      <div class="row">
      <div class="col-sm-12 col-md-12">
        <div class="card shadow mb-4">
          <div class="card-header py-3" style="background: #e59733">
            <h6 class="m-0 font-weight-bold text-white">Upraviť</h6>
          </div>
          <div class="card-body" style="background: #ffe0b8;">
            <form method="POST">
              <div class="form-group row">
                <div class="row col-sm-12 col-md-12 col-lg-4">
                  <label for="ukon_edit" class="col-sm-3 col-form-label">Úkon/názov dielu:</label>
                  <div class="col-sm-8 col-md-8">
                    <input class="form-control bg-light border-1" type="text" name="ukon_edit" id="ukon_edit" value="<?php echo $data[0]['action']; ?>" required>
                  </div>
                </div>
                <div class="row col-sm-12 col-md-12 col-lg-4">
                  <label for="mnozstvo_edit" class="col-sm-3 col-form-label">množstvo:</label>
                  <div class="col-sm-8 col-md-8">
                    <input class="form-control bg-light border-1" type="number" step="1" name="mnozstvo_edit" id="mnozstvo_edit" value="<?php echo $data[0]['mnozstvo']; ?>" required>
                  </div>
                </div>
                <div class="row col-sm-12 col-md-12 col-lg-4">
                  <label for="cena_edit" class="col-sm-3 col-form-label">Cena:</label>
                  <div class="col-sm-7 col-md-7">
                    <input class="form-control bg-light border-1" type="number" step="0.01" name="cena_edit" id="cena_edit" value="<?php echo $data[0]['jednotkova_cena']; ?>" required>
                  </div>
                  <label for="cena_edit" class="col-sm-1 col-form-label">€</label>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-sm-2 col-md-2 col-lg-1">
                  <button type="submit" class="btn btn-success btn-icon-split w-100" name="upravit_ukon" id="upravit_ukon">
                    <span class="text">Upraviť</span>
                  </button>
                </div>
                <div class="col-sm-2 col-md-2 col-lg_1">
                  <button type="submit" class="btn btn-danger btn-icon-split w-100" name="zmazat_ukon" id="zmazat_ukon">
                    <span class="text">Zmazať</span>
                  </button>
                </div>
                <div class="col-sm-2 col-md-2 col-lg-1">
                  <?php
                    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    $returnUrl = substr($url,0,strpos($url,"&"));
                  ?>
                  <a href="<?php echo $returnUrl;?>" class="btn btn-success btn-icon-split w-100" >
                    <span class="text">Zrušiť</span>
                  </a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  <?php
    }
  }
  ?>

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
  <div class="col-sm-12 col-md-12 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-body">
        <form method="POST">
          <div class="form-group row">
            <div class="row col-sm-12 col-md-12">
              <label class="col-sm-12 col-form-label">ID: <strong>EBS<?php echo $_GET['s']?></strong></label>
            </div>
            <div class="row col-sm-12 col-md-12">
              <?php if($data['id_typ']<4){
                echo "<label class='col-sm-12 col-form-label'>Číslo reklamácie: <strong><a class='text-dark' href='https://spares.eta.cz/b2b/reclamationServiceRecord?id=".$data['servisny_list']."' target='_blank'>".$data['servisny_list']."</a></strong>";
              } else {
                echo "<label class='col-sm-12 col-form-label'>Číslo reklamácie: <strong>".$data['servisny_list']."</strong>";
              }
              ?>
                <?php if (empty($data['servisny_list']) && $_SESSION['admin_role'] != 2){?>
                  <a type="button" class="" data-toggle="modal" data-target=".bd-example-modal-lg-sl">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                <?php }?>
              </label>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label class="col-sm-12 col-form-label">Číslo predajcu: <strong><?php echo $data['cislo_reklamacie_predajcu'];?></strong>
                <?php if($_SESSION['admin_role'] != 2){?>
                <a type="button" class="" data-toggle="modal" data-target=".bd-example-modal-lg-delivery-in">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <?php }?>
              </label>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label class="col-sm-12 col-form-label">Produkt: <?php
                echo "<a class='text-dark' href='https://spares.eta.cz/b2b/p/".$data['product_ref']."' target='_blank'><strong>".$data['product_ref']."</strong></a>";
                ?></label>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label class="col-sm-12 col-form-label">Vybavuje: <strong><?php echo $data['fullname'];?></strong></label>
            </div>
            <?php if(!is_null($data['cislo_prepravy_likvidacia']) || !is_null($data['id_delivery_out'])){?>
                <div class="row col-sm-12 col-md-12 mt-3">
                  <hr class="rounded">
                </div>
            <?php }  ?>
            <?php if(!is_null($data['cislo_prepravy_likvidacia'])){?>
              <div class="row col-sm-12 col-md-12">
                <label class="col-sm-12 col-form-label">Číslo prepravy ETA.cz: <strong><?php echo $data['cislo_prepravy_likvidacia'];?></strong></label>
              </div>
            <?php }?>
            <?php if(!is_null($data['id_delivery_out'])){?>
              <div class="row col-sm-12 col-md-12">
                <label class="col-sm-12 col-form-label">Spôsob odoslania: <strong><?php echo $serviceV->getDelivery($data['id_delivery_out'])[$data['id_delivery_out']]['description'];?></strong></label>
              </div>
            <?php }?>
            <?php if(!is_null($data['cislo_prepravy_out'])){?>
              <div class="row col-sm-12 col-md-12">
                <label class="col-sm-12 col-form-label">Číslo prepravy: <strong>
                <?php
                if ($data['id_delivery_out'] == 1){
                  $link_p[0] = substr($data['cislo_prepravy_out'],0,3);
                  $link_p[1] = substr($data['cislo_prepravy_out'],3,3);
                  $link_p[2] = substr($data['cislo_prepravy_out'],6);
                  echo "<a class='text-dark' href='http://t-t.sps-sro.sk/result.php?cmd=SDG_SEARCH&sprache=&sdg_landnr=".$link_p[0]."&sdg_mandnr=".$link_p[1]."&sdg_lfdnr=".$link_p[2]."' target='_blank'>".$data['cislo_prepravy_out']."</a>";
                } else {
                  echo $data['cislo_prepravy_out'];
                }
                ?>
              </strong></label>
              </div>
            <?php }?>
            <?php if($data['hmotnost_out'] > 0){?>
              <div class="row col-sm-12 col-md-12">
                <label class="col-sm-12 col-form-label">Hmotnosť: <strong><?php echo $data['hmotnost_out'];?> kg</strong></label>
              </div>
            <?php }?>
            <?php if($data['vzdialenost_out'] > 0){?>
              <div class="row col-sm-12 col-md-12">
                <label class="col-sm-12 col-form-label">Vzdialenosť: <strong><?php echo $data['vzdialenost_out'];?> km</strong></label>
              </div>
            <?php }?>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php
  $av = array(7, 8, 10, 11, 12);
  if (in_array($data['id_stav_opravy'], $av)){
?>
  <div class="col-sm-12 col-md-12 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Doručenie k zákazníkovi</h6>
      </div>
      <div class="card-body">
        <?php if ($_SESSION['admin_role'] != 2){ ?>
          <form method="post">
        <?php }?>
          <div class="form-group row">
            <label for="service_list" class="col-sm-3 col-form-label">Spôsob:</label>
            <div class="col-sm-9">
              <select class="form-control bg-light border-1" id="deliveryOut" name="id_delivery_out" onchange="checkDeliveryOut()">
                <?php
                  $s = new ServiceView();
                  $delivery_types = $s->getDelivery();
                  foreach ($delivery_types as $id => $delivery_type){
                    if (empty($data['id_delivery_out'])){
                      $selected = ($data['id_delivery_in'] == $id)?"selected":"";
                    }else {
                      $selected = ($data['id_delivery_out']== $id)?"selected":"";
                    }
                    echo "<option value='".$id."' $selected>".$delivery_type['description']."</option>";
                  }
                  ?>
              </select>
            </div>
          </div>
          <div class="form-group row" id="cislo_prepravy">
            <label for="cislo_prepravy_input" class="col-sm-3 col-form-label">Číslo prepravy:</label>
            <div class="col-sm-9">
              <input type="text" name="cislo_prepravy_out" class="form-control bg-light border-1" id="cislo_prepravy_input" value="<?php echo $data['cislo_prepravy_out']?>">
            </div>
          </div>
          <div class="form-group row" id="vzdialenost">
            <label for="vzdialenost_input" class="col-sm-3 col-form-label">Vzdialenosť [km]:</label>
            <div class="col-sm-9">
              <input type="number" name="vzdialenost_out" class="form-control bg-light border-1" id="vzdialenost_input" value="<?php echo $data['vzdialenost_out']?>">
            </div>
          </div>
          <div class="form-group row" id="hmotnost">
            <label for="hmotnost_input" class="col-sm-3 col-form-label">Hmotnosť [kg]:</label>
            <div class="col-sm-9">
              <input type="number" name="hmotnost_out" class="form-control bg-light border-1" id="hmotnost_input" value="<?php echo $data['hmotnost_out']?>">
            </div>
          </div>
          <?php if ($_SESSION['admin_role'] != 2){ ?>
            <button type="submit" class="btn btn-success btn-icon-split col-sm-3" name="ulozit_dorucenie_out" id="ulozit_dorucenie_out">
              <span class="text">Uložiť</span>
            </button>
        </form>
        <?php }?>
      </div>
    </div>
  </div>
<?php } ?>
</div>
<?php
$av = array(9, 10);
  if (in_array($data['id_stav_opravy'], $av)){
?>
<div class="row d-flex justify-content-end">
  <div class="col-sm-12 col-md-12 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Doručenie ETAcz</h6>
      </div>
      <div class="card-body">
        <form method="post">
          <div class="form-group row" id="cislo_prepravy">
            <label for="service_list" class="col-sm-3 col-form-label">Číslo prepravy:</label>
            <div class="col-sm-9">
              <input type="text" name="cislo_prepravy_likvidacia" class="form-control bg-light border-1" id="cislo_prepravy_input" value="<?php echo $data['cislo_prepravy_likvidacia']?>">
            </div>
          </div>
          <button type="submit" class="btn btn-success btn-icon-split col-sm-3" name="ulozit_likvidacia" id="ulozit_likvidacia">
            <span class="text">Uložiť</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

  <?php
}

  if($data['id_typ'] == 1 || $data['id_typ'] == 2){
    if($_SESSION['admin_role'] == 3){
      $available = array(
        2 => array(3),
        3 => array(4, 5, 6, 8),
        4 => array(5, 6, 7, 8, 22),
        5 => array(4, 6, 22),
        6 => array(4, 5, 22),
        7 => array(),
        8 => array(),
        10 => array(),
        11 => array(),
        12 => array(),
        22 => array(9, 10, 11, 12)
      );
    } else {
      $available = array(
        2 => array(3),
        3 => array(4, 5, 6, 8),
        4 => array(5, 6, 7, 8, 22),
        5 => array(4, 6, 22),
        6 => array(4, 5, 22),
        7 => array(16),
        8 => array(16),
        10 => array(16),
        11 => array(16),
        12 => array(16),
        22 => array(9, 10, 11, 12)
      );
    }
    $available_buttons = ($_SESSION['admin_role'] != 2)?$available[$s_active]:array();
    foreach ($s_ids as $key => $id) {
      if (in_array($id,$available_buttons)){
        if($s_ids[$key] == 16 && (!$isCenaPrace || !$isImport || !$isExport)){
          $buttons[$key] = "<button class='status' type='button' value='".$s_ids[$key]."' data-toggle='modal' data-target='.bd-example-modal-lg-save'>".$s_text[$id]."</button>";
        } else {
          $buttons[$key] = "<button class='status' name='status' type='submit' value='".$s_ids[$key]."'>".$s_text[$id]."</button>";
        }
      } else {
        $buttons[$key] = $s_text[$id];
      }
    }
    $svg ="
    <div class='row'>
    <form method='post'>
    <svg style='left: 0px; top: 0px; margin: 0 auto; height: 100%; display: block; min-width: 985px; min-height: 455px; background-color: transparent; background-image: none;'>
    <defs>
      <filter id='dropShadow'>
        <feGaussianBlur in='SourceAlpha' stdDeviation='1.7' result='blur'></feGaussianBlur>
        <feOffset in='blur' dx='3' dy='3' result='offsetBlur'></feOffset>
        <feFlood flood-color='#3D4574' flood-opacity='0.4' result='offsetColor'></feFlood>
        <feComposite in='offsetColor' in2='offsetBlur' operator='in' result='offsetBlur'></feComposite>
        <feBlend in='SourceGraphic' in2='offsetBlur'></feBlend>
      </filter>
    </defs>
    <g id='svg' transformOrigin='0 0' transform='scale(1,1)translate(-52,-132)'>
      <g></g>
      <g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 180 420 L 200 420 L 190 420 L 203.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 180 420 L 200 420 L 190 420 L 203.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 208.88 420 L 201.88 423.5 L 203.63 420 L 201.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <rect class='".current($s_class)."' x='60' y='390' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
        </g>
        <g style=''>
          <g>
            <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
              <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 420px; margin-left: 61px;'>

                <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                  <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".current($buttons)."</div>
                </div>
              </div>
            </foreignObject>
          </g>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 330 420 L 423.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 330 420 L 423.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 428.88 420 L 421.88 423.5 L 423.63 420 L 421.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 330 420 L 350 420 L 350 310 L 363.63 310' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 330 420 L 350 420 L 350 310 L 363.63 310' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 368.88 310 L 361.88 313.5 L 363.63 310 L 361.88 306.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 330 420 L 350 420 L 350 530 L 363.63 530' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 330 420 L 350 420 L 350 530 L 363.63 530' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 368.88 530 L 361.88 533.5 L 363.63 530 L 361.88 526.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 330 420 L 350 420 L 350 170 L 573.63 170' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 330 420 L 350 420 L 350 170 L 573.63 170' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 578.88 170 L 571.88 173.5 L 573.63 170 L 571.88 166.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <rect class='".next($s_class)."' x='210' y='390' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
        </g>
        <g style=''>
          <g>
            <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
              <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 420px; margin-left: 211px;'>
                <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                  <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                </div>
              </div>
            </foreignObject>
          </g>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 430 340 L 430 365 L 490 365 L 490 383.63' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 430 340 L 430 365 L 490 365 L 490 383.63' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 490 388.88 L 486.5 381.88 L 490 383.63 L 493.5 381.88 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 400 340 L 400 493.63' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 400 340 L 400 493.63' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 400 498.88 L 396.5 491.88 L 400 493.63 L 403.5 491.88 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 490 310 L 535 310 L 535 250 L 573.63 250' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 490 310 L 535 310 L 535 250 L 573.63 250' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 578.88 250 L 571.88 253.5 L 573.63 250 L 571.88 246.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 490 310 L 560 310 L 560 420 L 573.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 490 310 L 560 310 L 560 420 L 573.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 578.88 420 L 571.88 423.5 L 573.63 420 L 571.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <rect class='".next($s_class)."' x='370' y='280' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
        </g>
        <g style=''>
          <g>
            <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
              <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 310px; margin-left: 371px;'>
                <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                  <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                </div>
              </div>
            </foreignObject>
          </g>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 490 390 L 490 365 L 430 365 L 430 346.37' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 490 390 L 490 365 L 430 365 L 430 346.37' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 430 341.12 L 433.5 348.12 L 430 346.37 L 426.5 348.12 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 490 450 L 490 475 L 430 475 L 430 493.63' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 490 450 L 490 475 L 430 475 L 430 493.63' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 430 498.88 L 426.5 491.88 L 430 493.63 L 433.5 491.88 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 550 420 L 570 420 L 560 420 L 573.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 550 420 L 570 420 L 560 420 L 573.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 578.88 420 L 571.88 423.5 L 573.63 420 L 571.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <rect class='".next($s_class)."' x='430' y='390' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
        </g>
        <g style=''>
          <g>
            <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
              <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 420px; margin-left: 431px;'>
                <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                  <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                </div>
              </div>
            </foreignObject>
          </g>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 430 500 L 430 475 L 490 475 L 490 456.37' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 430 500 L 430 475 L 490 475 L 490 456.37' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 490 451.12 L 493.5 458.12 L 490 456.37 L 486.5 458.12 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 400 500 L 400 346.37' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 400 500 L 400 346.37' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 400 341.12 L 403.5 348.12 L 400 346.37 L 396.5 348.12 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 490 530 L 560 530 L 560 420 L 573.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 490 530 L 560 530 L 560 420 L 573.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 578.88 420 L 571.88 423.5 L 573.63 420 L 571.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <rect class='".next($s_class)."' x='370' y='500' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
        </g>
        <g style=''>
          <g>
            <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
              <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 530px; margin-left: 371px;'>
                <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                  <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                </div>
              </div>
            </foreignObject>
          </g>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'></g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 700 420 L 720 420 L 720 300 L 733.63 300' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 700 420 L 720 420 L 720 300 L 733.63 300' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 738.88 300 L 731.88 303.5 L 733.63 300 L 731.88 296.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 700 420 L 720 420 L 720 380 L 733.63 380' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 700 420 L 720 420 L 720 380 L 733.63 380' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 738.88 380 L 731.88 383.5 L 733.63 380 L 731.88 376.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 700 420 L 720 420 L 720 460 L 733.63 460' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 700 420 L 720 420 L 720 460 L 733.63 460' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 738.88 460 L 731.88 463.5 L 733.63 460 L 731.88 456.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 700 420 L 720 420 L 720 540 L 733.63 540' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 700 420 L 720 420 L 720 540 L 733.63 540' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 738.88 540 L 731.88 543.5 L 733.63 540 L 731.88 536.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <rect class='".next($s_class)."' x='580' y='390' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
        </g>
        <g style=''>
          <g>
            <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
              <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 420px; margin-left: 581px;'>
                <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                  <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                </div>
              </div>
            </foreignObject>
          </g>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 700 170 L 880 170 L 880 420 L 893.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 700 170 L 880 170 L 880 420 L 893.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 898.88 420 L 891.88 423.5 L 893.63 420 L 891.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <rect class='".next($s_class)."' x='580' y='140' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
        </g>
        <g style=''>
          <g>
            <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
              <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 170px; margin-left: 581px;'>
                <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                  <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                </div>
              </div>
            </foreignObject>
          </g>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 700 250 L 880 250 L 880 420 L 893.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 700 250 L 880 250 L 880 420 L 893.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 898.88 420 L 891.88 423.5 L 893.63 420 L 891.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <rect class='".next($s_class)."' x='580' y='220' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
        </g>
        <g style=''>
          <g>
            <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
              <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 250px; margin-left: 581px;'>
                <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                  <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                </div>
              </div>
            </foreignObject>
          </g>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 860 380 L 880 380 L 880 420 L 893.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 860 380 L 880 380 L 880 420 L 893.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 898.88 420 L 891.88 423.5 L 893.63 420 L 891.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <rect class='".next($s_class)."' x='740' y='350' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
        </g>
        <g style=''>
          <g>
            <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
              <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 380px; margin-left: 741px;'>
                <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                  <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                </div>
              </div>
            </foreignObject>
          </g>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <rect class='".next($s_class)."' x='740' y='270' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
        </g>
        <g style=''>
          <g>
            <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
              <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 300px; margin-left: 741px;'>
                <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                  <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                </div>
              </div>
            </foreignObject>
          </g>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 860 540 L 880 540 L 880 420 L 893.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 860 540 L 880 540 L 880 420 L 893.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 898.88 420 L 891.88 423.5 L 893.63 420 L 891.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <rect class='".next($s_class)."' x='740' y='510' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
        </g>
        <g style=''>
          <g>
            <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
              <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 540px; margin-left: 741px;'>
                <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                  <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                </div>
              </div>
            </foreignObject>
          </g>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <path d='M 860 460 L 880 460 L 880 420 L 893.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
          <path d='M 860 460 L 880 460 L 880 420 L 893.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
          <path d='M 898.88 420 L 891.88 423.5 L 893.63 420 L 891.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <rect class='".next($s_class)."' x='740' y='430' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
        </g>
        <g style=''>
          <g>
            <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
              <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 460px; margin-left: 741px;'>
                <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                  <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                </div>
              </div>
            </foreignObject>
          </g>
        </g>
        <g transform='translate(0.5,0.5)' style='visibility: visible;'>
          <rect class='".next($s_class)."' x='900' y='390' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
        </g>
        <g style=''>
          <g>
            <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
              <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 420px; margin-left: 901px;'>
                <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                  <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                </div>
              </div>
            </foreignObject>
          </g>
        </g>
      </g>
      <g></g>
      <g></g>
    </g>
    </svg>
    </form>
    </div>
    ";
    echo $svg;
  } else if($data['id_typ'] == 3){
    if($_SESSION['admin_role'] == 3){
      $available = array(
        2 => array(3),
        3 => array(4, 5, 6, 8),
        4 => array(5, 6, 7, 8),
        5 => array(4, 6, 8),
        6 => array(4, 5, 8),
        7 => array(),
        8 => array(),
        17 => array(),
        19 => array()
      );
    } else {
      $available = array(
        2 => array(3),
        3 => array(4, 5, 6, 8),
        4 => array(5, 6, 7, 8, 17),
        5 => array(4, 6, 8, 17),
        6 => array(4, 5, 8, 17),
        7 => array(16),
        8 => array(16),
        17 => array(19),
        19 => array(21)
      );
    }
    $tooltips = array(
      18 => 'data-toggle="tooltip" data-placement="top" title="Aktívne po podaní balíku"',
      19 => 'data-toggle="tooltip" data-placement="top" title="Aktívne po zmene statusu balíka na odoslané"'
    );
    $available_buttons = ($_SESSION['admin_role'] != 2)?$available[$s_active]:array();
    $notes = $serviceV->get_record_notes($_GET['s']);
    foreach ($s_ids as $key => $id) {
      $note = isset($notes[$id])?$notes[$id]:"";
      if (in_array($id,$available_buttons)){
        if(in_array($s_ids[$key], array(19, 21))){
          $buttons[$key] = "<button class='status' name='status' type='submit' value='".$s_ids[$key]."'>".$s_text[$id]." ".$note."</button>";
        } else {
          $buttons[$key] = "<button class='status' name='status' type='submit' value='".$s_ids[$key]."'>".$s_text[$id]."</button>";
        }
      } else {
        if(in_array($s_ids[$key], array(19, 21))){
          $buttons[$key] = $s_text[$id]." ".$note;
        } else {
          $buttons[$key] = $s_text[$id];
        }
      }
      if (in_array($id,$available_buttons)){
        $tooltips[$key] = "<button class='status' name='status' type='submit' value='".$s_ids[$key]."'>".$s_text[$id]."</button>";
      } else {
        $tooltips[$key] = "";
      }
    }
    $svg ="
    <div class='row'>
    <form method='post'>
    <svg style='left: 0px; top: 0px; margin: 0 auto; height: 100%; display: block; min-width: 975px; min-height: 445px; background-color: transparent; background-image: none;'>
      <defs>
        <filter id='dropShadow'>
          <feGaussianBlur in='SourceAlpha' stdDeviation='1.7' result='blur'></feGaussianBlur>
          <feOffset in='blur' dx='3' dy='3' result='offsetBlur'></feOffset>
          <feFlood flood-color='#3D4574' flood-opacity='0.4' result='offsetColor'></feFlood>
          <feComposite in='offsetColor' in2='offsetBlur' operator='in' result='offsetBlur'></feComposite>
          <feBlend in='SourceGraphic' in2='offsetBlur'></feBlend>
        </filter>
      </defs>
      <g id='svg' transformOrigin='0 0' transform='scale(1,1)translate(-52,-132)'>
        <g></g>
        <g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 180 420 L 200 420 L 190 420 L 203.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 180 420 L 200 420 L 190 420 L 203.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 208.88 420 L 201.88 423.5 L 203.63 420 L 201.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".current($s_class)."' x='60' y='390' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 420px; margin-left: 61px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".current($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 330 420 L 423.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 330 420 L 423.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 428.88 420 L 421.88 423.5 L 423.63 420 L 421.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 330 420 L 350 420 L 350 310 L 363.63 310' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 330 420 L 350 420 L 350 310 L 363.63 310' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 368.88 310 L 361.88 313.5 L 363.63 310 L 361.88 306.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 330 420 L 350 420 L 350 530 L 363.63 530' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 330 420 L 350 420 L 350 530 L 363.63 530' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 368.88 530 L 361.88 533.5 L 363.63 530 L 361.88 526.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 330 420 L 350 420 L 350 170 L 573.63 170' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 330 420 L 350 420 L 350 170 L 573.63 170' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 578.88 170 L 571.88 173.5 L 573.63 170 L 571.88 166.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='210' y='390' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 420px; margin-left: 211px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 430 340 L 430 365 L 490 365 L 490 383.63' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 430 340 L 430 365 L 490 365 L 490 383.63' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 490 388.88 L 486.5 381.88 L 490 383.63 L 493.5 381.88 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 400 340 L 400 493.63' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 400 340 L 400 493.63' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 400 498.88 L 396.5 491.88 L 400 493.63 L 403.5 491.88 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 490 310 L 535 310 L 535 250 L 573.63 250' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 490 310 L 535 310 L 535 250 L 573.63 250' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 578.88 250 L 571.88 253.5 L 573.63 250 L 571.88 246.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 490 310 L 560 310 L 560 420 L 573.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 490 310 L 560 310 L 560 420 L 573.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 578.88 420 L 571.88 423.5 L 573.63 420 L 571.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='370' y='280' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 310px; margin-left: 371px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 490 390 L 490 365 L 430 365 L 430 346.37' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 490 390 L 490 365 L 430 365 L 430 346.37' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 430 341.12 L 433.5 348.12 L 430 346.37 L 426.5 348.12 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 490 450 L 490 475 L 430 475 L 430 493.63' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 490 450 L 490 475 L 430 475 L 430 493.63' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 430 498.88 L 426.5 491.88 L 430 493.63 L 433.5 491.88 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 550 420 L 570 420 L 560 420 L 573.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 550 420 L 570 420 L 560 420 L 573.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 578.88 420 L 571.88 423.5 L 573.63 420 L 571.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='430' y='390' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 420px; margin-left: 431px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 430 500 L 430 475 L 490 475 L 490 456.37' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 430 500 L 430 475 L 490 475 L 490 456.37' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 490 451.12 L 493.5 458.12 L 490 456.37 L 486.5 458.12 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 400 500 L 400 346.37' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 400 500 L 400 346.37' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 400 341.12 L 403.5 348.12 L 400 346.37 L 396.5 348.12 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 490 530 L 560 530 L 560 420 L 573.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 490 530 L 560 530 L 560 420 L 573.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 578.88 420 L 571.88 423.5 L 573.63 420 L 571.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='370' y='500' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 530px; margin-left: 371px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'></g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 700 170 L 795 170 L 795 210 L 883.63 210' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 700 170 L 795 170 L 795 210 L 883.63 210' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 888.88 210 L 881.88 213.5 L 883.63 210 L 881.88 206.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='580' y='140' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 170px; margin-left: 581px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 700 250 L 795 250 L 795 210 L 883.63 210' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 700 250 L 795 250 L 795 210 L 883.63 210' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 888.88 210 L 881.88 213.5 L 883.63 210 L 881.88 206.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='580' y='220' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 250px; margin-left: 581px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 700 420 L 720 420 L 720 380 L 733.63 380' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 700 420 L 720 420 L 720 380 L 733.63 380' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 738.88 380 L 731.88 383.5 L 733.63 380 L 731.88 376.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 700 420 L 720 420 L 720 470 L 733.63 470' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 700 420 L 720 420 L 720 470 L 733.63 470' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 738.88 470 L 731.88 473.5 L 733.63 470 L 731.88 466.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='580' y='390' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 420px; margin-left: 581px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 860 380 L 880 380 L 870 380 L 883.63 380' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 860 380 L 880 380 L 870 380 L 883.63 380' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 888.88 380 L 881.88 383.5 L 883.63 380 L 881.88 376.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='740' y='350' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 380px; margin-left: 741px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='890' y='180' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 210px; margin-left: 891px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 860 470 L 880 470 L 870 470 L 883.63 470' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 860 470 L 880 470 L 870 470 L 883.63 470' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 888.88 470 L 881.88 473.5 L 883.63 470 L 881.88 466.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='740' y='440' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 470px; margin-left: 741px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='890' y='350' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 380px; margin-left: 891px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='890' y='440' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 470px; margin-left: 891px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
        </g>
        <g></g>
        <g></g>
      </g>
    </svg>
    </form>
    </div>";
    echo $svg;
  } else if($data['id_typ'] == 4){
    if($_SESSION['admin_role'] == 3){
      $available = array(
        2 => array(3),
        3 => array(4, 5, 6, 8),
        4 => array(5, 6, 7, 8, 22),
        5 => array(4, 6, 22),
        6 => array(4, 5, 22),
        7 => array(),
        8 => array(),
        10 => array(),
        11 => array(),
        12 => array(),
        22 => array(9, 10, 11, 12)
      );
    } else {
      $available = array(
        2 => array(3),
        3 => array(4, 5, 6, 8),
        4 => array(5, 6, 7, 8, 22),
        5 => array(4, 6, 22),
        6 => array(4, 5, 22),
        7 => array(16),
        8 => array(16),
        10 => array(16),
        11 => array(16),
        12 => array(16),
        22 => array(9, 10, 11, 12)
      );
    }
    $available_buttons = ($_SESSION['admin_role'] != 2)?$available[$s_active]:array();
    foreach ($s_ids as $key => $id) {
      if (in_array($id,$available_buttons)){
        $buttons[$key] = "<button class='status' name='status' type='submit' value='".$s_ids[$key]."'>".$s_text[$id]."</button>";
      } else {
        $buttons[$key] = $s_text[$id];
      }
    }
    $svg ="
    <div class='row'>
    <form method='post'>
    <svg style='left: 0px; top: 0px; margin: 0 auto; height: 100%; display: block; min-width: 985px; min-height: 455px; background-color: transparent; background-image: none;'>
      <defs>
        <filter id='dropShadow'>
          <feGaussianBlur in='SourceAlpha' stdDeviation='1.7' result='blur'></feGaussianBlur>
          <feOffset in='blur' dx='3' dy='3' result='offsetBlur'></feOffset>
          <feFlood flood-color='#3D4574' flood-opacity='0.4' result='offsetColor'></feFlood>
          <feComposite in='offsetColor' in2='offsetBlur' operator='in' result='offsetBlur'></feComposite>
          <feBlend in='SourceGraphic' in2='offsetBlur'></feBlend>
        </filter>
      </defs>
      <g id='svg' transformOrigin='0 0' transform='scale(1,1)translate(-52,-172)'>
        <g></g>
        <g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 180 420 L 200 420 L 190 420 L 203.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 180 420 L 200 420 L 190 420 L 203.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 208.88 420 L 201.88 423.5 L 203.63 420 L 201.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".current($s_class)."' x='60' y='390' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 420px; margin-left: 61px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".current($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 330 420 L 423.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 330 420 L 423.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 428.88 420 L 421.88 423.5 L 423.63 420 L 421.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 330 420 L 350 420 L 350 310 L 363.63 310' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 330 420 L 350 420 L 350 310 L 363.63 310' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 368.88 310 L 361.88 313.5 L 363.63 310 L 361.88 306.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 330 420 L 350 420 L 350 530 L 363.63 530' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 330 420 L 350 420 L 350 530 L 363.63 530' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 368.88 530 L 361.88 533.5 L 363.63 530 L 361.88 526.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='210' y='390' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 420px; margin-left: 211px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 430 340 L 430 365 L 490 365 L 490 383.63' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 430 340 L 430 365 L 490 365 L 490 383.63' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 490 388.88 L 486.5 381.88 L 490 383.63 L 493.5 381.88 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 400 340 L 400 493.63' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 400 340 L 400 493.63' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 400 498.88 L 396.5 491.88 L 400 493.63 L 403.5 491.88 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 490 310 L 535 310 L 535 210 L 573.63 210' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 490 310 L 535 310 L 535 210 L 573.63 210' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 578.88 210 L 571.88 213.5 L 573.63 210 L 571.88 206.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 490 310 L 560 310 L 560 420 L 573.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 490 310 L 560 310 L 560 420 L 573.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 578.88 420 L 571.88 423.5 L 573.63 420 L 571.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='370' y='280' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 310px; margin-left: 371px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 490 390 L 490 365 L 430 365 L 430 346.37' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 490 390 L 490 365 L 430 365 L 430 346.37' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 430 341.12 L 433.5 348.12 L 430 346.37 L 426.5 348.12 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 490 450 L 490 475 L 430 475 L 430 493.63' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 490 450 L 490 475 L 430 475 L 430 493.63' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 430 498.88 L 426.5 491.88 L 430 493.63 L 433.5 491.88 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 550 420 L 570 420 L 560 420 L 573.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 550 420 L 570 420 L 560 420 L 573.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 578.88 420 L 571.88 423.5 L 573.63 420 L 571.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='430' y='390' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 420px; margin-left: 431px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 430 500 L 430 475 L 490 475 L 490 456.37' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 430 500 L 430 475 L 490 475 L 490 456.37' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 490 451.12 L 493.5 458.12 L 490 456.37 L 486.5 458.12 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 400 500 L 400 346.37' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 400 500 L 400 346.37' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 400 341.12 L 403.5 348.12 L 400 346.37 L 396.5 348.12 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 490 530 L 560 530 L 560 420 L 573.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 490 530 L 560 530 L 560 420 L 573.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 578.88 420 L 571.88 423.5 L 573.63 420 L 571.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='370' y='500' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 530px; margin-left: 371px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'></g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 700 420 L 720 420 L 720 340 L 733.63 340' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 700 420 L 720 420 L 720 340 L 733.63 340' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 738.88 340 L 731.88 343.5 L 733.63 340 L 731.88 336.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 700 420 L 733.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 700 420 L 733.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 738.88 420 L 731.88 423.5 L 733.63 420 L 731.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 700 420 L 720 420 L 720 500 L 733.63 500' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 700 420 L 720 420 L 720 500 L 733.63 500' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 738.88 500 L 731.88 503.5 L 733.63 500 L 731.88 496.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 700 420 L 720 420 L 720 580 L 733.63 580' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 700 420 L 720 420 L 720 580 L 733.63 580' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 738.88 580 L 731.88 583.5 L 733.63 580 L 731.88 576.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 700 420 L 720 420 L 720 260 L 733.63 260' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 700 420 L 720 420 L 720 260 L 733.63 260' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 738.88 260 L 731.88 263.5 L 733.63 260 L 731.88 256.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='580' y='390' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 420px; margin-left: 581px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 700 210 L 880 210 L 880 420 L 893.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 700 210 L 880 210 L 880 420 L 893.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 898.88 420 L 891.88 423.5 L 893.63 420 L 891.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='580' y='180' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 210px; margin-left: 581px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 860 420 L 893.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 860 420 L 893.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 898.88 420 L 891.88 423.5 L 893.63 420 L 891.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='740' y='390' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 420px; margin-left: 741px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 860 340 L 880 340 L 880 420 L 893.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 860 340 L 880 340 L 880 420 L 893.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 898.88 420 L 891.88 423.5 L 893.63 420 L 891.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='740' y='310' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 340px; margin-left: 741px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 860 580 L 880 580 L 880 420 L 893.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 860 580 L 880 580 L 880 420 L 893.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 898.88 420 L 891.88 423.5 L 893.63 420 L 891.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='740' y='550' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 580px; margin-left: 741px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 860 500 L 880 500 L 880 420 L 893.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 860 500 L 880 500 L 880 420 L 893.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 898.88 420 L 891.88 423.5 L 893.63 420 L 891.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='740' y='470' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 500px; margin-left: 741px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='900' y='390' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 420px; margin-left: 901px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <path d='M 860 260 L 880 260 L 880 420 L 893.63 420' fill='none' stroke='white' stroke-miterlimit='10' pointer-events='stroke' visibility='hidden' stroke-width='9'></path>
            <path d='M 860 260 L 880 260 L 880 420 L 893.63 420' fill='none' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='stroke'></path>
            <path d='M 898.88 420 L 891.88 423.5 L 893.63 420 L 891.88 416.5 Z' fill='rgb(0, 0, 0)' stroke='rgb(0, 0, 0)' stroke-miterlimit='10' pointer-events='all'></path>
          </g>
          <g transform='translate(0.5,0.5)' style='visibility: visible;'>
            <rect class='".next($s_class)."' x='740' y='230' width='120' height='60' rx='9' ry='9' fill='rgb(255, 255, 255)' stroke='rgb(0, 0, 0)' pointer-events='all'></rect>
          </g>
          <g style=''>
            <g>
              <foreignObject pointer-events='none' width='100%' height='100%' style='overflow: visible; text-align: left;'>
                <div style='display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: 260px; margin-left: 741px;'>
                  <div data-drawio-colors='color: rgb(0, 0, 0); ' style='box-sizing: border-box; font-size: 0px; text-align: center;'>
                    <div style='display: inline-block; font-size: 12px; font-family: Helvetica; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;'>".next($buttons)."</div>
                  </div>
                </div>
              </foreignObject>
            </g>
          </g>
        </g>
        <g></g>
        <g></g>
      </g>
    </svg>
    </form>
    </div>";
    echo $svg;
  }
$note_text = isset($oprava[0]['note'])?$oprava[0]['note']:"";
$note_button = empty($note_text)?"Pridať":"Upraviť";
  ?>

<div class="row">
  <?php if ($_SESSION['admin_role'] != 2){?>
<div class="col-sm-12 col-md-12 col-lg-6">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Pridať poznámku k stavu</h6>
    </div>
    <div class="card-body">
      <form method="POST">
        <div class="form-group row" id="poznamka">
          <div class="col-sm-2 col-md-2">
            <label for="service_list col-sm-12" class="col-form-label">Poznámka:</label>
          </div>
          <div class="col-sm-7 col-md-7">
            <input type="text" name="note" class="form-control bg-light border-1" value="<?php echo $note_text?>">
          </div>
          <div class="col-sm-3 col-md-3">
            <button type="submit" class="btn btn-success btn-icon-split col-sm-12 col-md-12" name="pridat_poznamku" id="pridat_poznamku">
              <span class="text"><?php echo $note_button?></span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
}
  if($s_active == 17 && $_SESSION['admin_role'] != 2){
    $kat_text = isset($data['kat_ukoncenia'])?$data['kat_ukoncenia']:"";
    $kat_button = empty($kat_text)?"Pridať":"Upraviť";
    $kat_button_color = empty($data['kat_ukoncenia'])?"warning":"success";
?>
    <div class="col-sm-12 col-md-12 col-lg-6">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Spôsob ukončenia</h6>
        </div>
        <div class="card-body">
          <?php if(is_null($data['cislo_prepravy_likvidacia'])){ ?>
          <form method="POST">
            <div class="form-group row">
              <div class="col-sm-2 col-md-2">
                <label for="kat_ukon" class="col-form-label">Typ:</label>
              </div>
              <div class="col-sm-7 col-md-7">
                <select type="text" name="kat_ukon" class="form-control bg-light border-1">
                  <?php
                    for ($i = 1; $i <= 8; $i++){
                      echo ($data['kat_ukoncenia'] == $i)?"<option value='$i' selected>Kategória $i</option>":"<option value='$i'>Kategória $i</option>";
                    }
                  ?>
                </select>
              </div>
              <div class="col-sm-3 col-md-3">
                <button type="submit" class="btn btn-<?php echo $kat_button_color?> btn-icon-split col-sm-12 col-md-12" name="kat_ukoncenia" id="kat_ukoncenia">
                  <span class="text"><?php echo $kat_button?></span>
                </button>
              </div>
            </div>
          </form>
        <?php } else { ?>
          <div class="form-group row">
              <div class="col-sm-12 col-md-12">
                <label for="kat_ukon" class="col-form-label"><strong>Kategória <?php echo $data['kat_ukoncenia'];?></strong></label>
              </div>
            </div>
        <?php } ?>
        </div>
      </div>
    </div>
<?php
  }
?>

<?php
$per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 4); //pridanie technika
if((($per == 2) && ($data['id_stav_opravy'] == 2)) || (($per == 3) && ($data['id_stav_opravy'] < 7))){
      $t = $serviceV->canBeTechnician();
    ?>

      <div class="col-sm-12 col-md-12 col-lg-6">
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Prideliť technikovi</h6>
          </div>
          <div class="card-body">
            <form method="POST">
              <div class="form-group row" id="technik">
                <div class="col-sm-2 col-md-2">
                  <label for="service_list col-sm-12" class="col-form-label">Meno:</label>
                </div>
                <div class="col-sm-7">
                  <select class="form-control bg-light border-1" name="technik" id="technik_input">
                    <?php
                      foreach ($t as $id_admin => $fullname) {
                        if ($id_admin == $data['id_vybavuje']){
                          echo "<option value='$id_admin' selected>$fullname</option>";
                        } else {
                          echo "<option value='$id_admin'>$fullname</option>";
                        }
                      }
                    ?>
                  </select>
                </div>
                <div class="col-sm-3 col-md-3">
                  <button type="submit" class="btn btn-success btn-icon-split col-sm-12 col-md-12" name="pridelit_technikovi" id="pridelit_technikovi">
                    <span class="text">Prideliť</span>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
<?php }?>
</div>
<?php
 if ($_SESSION['admin_role'] != 3){
?>
<div class="row">
  <div class="col-sm-12 col-md-12 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Objednávateľ</h6>
      </div>
      <div class="card-body">
        <?php echo
          $serviceV->getFullName($data['titul_pred'],$data['meno'],$data['priezvisko'],$data['titul_za'])."</br>".
          $data['firma']."</br>".
          "IČO: $data[ico]</br>".
          "DIC: $data[dic]</br>".
          "IČ DPH: $data[ic_dph]</br>".
          "$data[ulica_cislo]</br>".
          "$data[psc], $data[mesto]</br>".
          "$data[telefon]</br>".
          "$data[mail]</br>";
        ?>
      </div>
    </div>
  </div>
  <div class="col-sm-12 col-md-12 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Zákazník</h6>
      </div>
      <div class="card-body">
        <?php echo
          $serviceV->getFullName($data['titul_pred'],$data['meno'],$data['priezvisko'],$data['titul_za'])."</br>".
          $data['firma']."</br>".
          "IČO: $data[ico]</br>".
          "DIC: $data[dic]</br>".
          "IČ DPH: $data[ic_dph]</br>".
          "$data[ulica_cislo]</br>".
          "$data[psc], $data[mesto]</br>".
          "$data[telefon]</br>".
          "$data[mail]</br>";
        ?>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<div class="row">
  <div class="col-sm-12 col-md-12 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Informácie o prijatí</h6>
      </div>
      <div class="card-body">
        <form method="POST">
          <div class="form-group row">
            <div class="row col-sm-12 col-md-12">
              <label class="col-sm-3 col-form-label">Dátum vzniku:</label>
              <label class="col-sm-9 col-form-label">
                <?php echo date("d.m.Y", strtotime($data['datum_vzniku'])); ?>
              </label>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label class="col-sm-3 col-form-label">Dátum prijatia:</label>
              <label class="col-sm-9 col-form-label">
                <?php echo date("d.m.Y", strtotime($data['datum_prijatia'])); ?>
              </label>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label class="col-sm-3 col-form-label">Spôsob doručenia:</label>
              <label class="col-sm-9 col-form-label">
                <?php
                  echo $serviceV->getDelivery($data['id_delivery_in'])[$data['id_delivery_in']]['description'];
                  ?>
              </label>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label class="col-sm-3 col-form-label">Číslo prepravy:</label>
              <label class="col-sm-9 col-form-label">
                <?php
                if ($data['id_delivery_in'] == 1){
                  $link_p[0] = substr($data['cislo_prepravy'],0,3);
                  $link_p[1] = substr($data['cislo_prepravy'],3,3);
                  $link_p[2] = substr($data['cislo_prepravy'],6);
                  echo "<a class='text-dark' href='http://t-t.sps-sro.sk/result.php?cmd=SDG_SEARCH&sprache=&sdg_landnr=".$link_p[0]."&sdg_mandnr=".$link_p[1]."&sdg_lfdnr=".$link_p[2]."' target='_blank'>".$data['cislo_prepravy']."</a>";
                } else {
                  echo $data['cislo_prepravy'];
                }
                ?>
              </label>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label for="cislo_reklamacie_predajcu_info" class="col-sm-3 col-form-label">Číslo predajcu:</label>
              <div class="col-sm-9 col-md-9">
                <label><?php echo $data['cislo_reklamacie_predajcu']; ?></label>
                <?php if($_SESSION['admin_role'] != 2){?>
                <a type="button" class="" data-toggle="modal" data-target=".bd-example-modal-lg-delivery-in">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <?php }?>
              </div>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label class="col-sm-3 col-form-label">Hmotnosť:</label>
              <div class="col-sm-7 col-md-7">
                <label><?php echo $data['hmotnost']; ?> kg</label>
                <?php if($_SESSION['admin_role'] != 2){?>
                <a type="button" class="" data-toggle="modal" data-target=".bd-example-modal-lg-delivery-in">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <?php }?>
              </div>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label class="col-sm-3 col-form-label">Vzdialenosť:</label>
              <div class="col-sm-7 col-md-7">
                <label><?php echo $data['vzdialenost']; ?> km</label>
                <?php if($_SESSION['admin_role'] != 2){?>
                <a type="button" class="" data-toggle="modal" data-target=".bd-example-modal-lg-delivery-in">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <?php }?>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-sm-12 col-md-12 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Informácie o produkte</h6>
      </div>
      <div class="card-body">
        <form method="POST">
          <div class="form-group row">
            <div class="row col-sm-12 col-md-12">
              <label class="col-sm-3 col-form-label">Popis chyby:</label>
              <div class="col-sm-7 col-md-7">
                <label><?php echo $data['popis']; ?></label>
                <?php if($_SESSION['admin_role'] != 2){?>
                <a type="button" class="" data-toggle="modal" data-target=".bd-example-modal-lg-product">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <?php }?>
              </div>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label class="col-sm-3 col-form-label">Príslušenstvo:</label>
              <div class="col-sm-7 col-md-7">
                <label><?php echo $data['prislusenstvo']; ?></label>
                <?php if($_SESSION['admin_role'] != 2){?>
                <a type="button" class="" data-toggle="modal" data-target=".bd-example-modal-lg-product">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <?php }?>
              </div>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label class="col-sm-3 col-form-label">Výrobné číslo:</label>
              <div class="col-sm-7 col-md-7">
                <label><?php echo $data['vyrobne_cislo']; ?></label>
                <?php if($_SESSION['admin_role'] != 2){?>
                <a type="button" class="" data-toggle="modal" data-target=".bd-example-modal-lg-product">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <?php }?>
              </div>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label class="col-sm-3 col-form-label">Stav:</label>
              <div class="col-sm-7 col-md-7">
                <label><?php echo $data['stav_vyrobku']; ?></label>
                <?php if($_SESSION['admin_role'] != 2){?>
                <a type="button" class="" data-toggle="modal" data-target=".bd-example-modal-lg-product">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <?php }?>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <?php
  $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 8); //fotky servisu
  if($per > 0){
  ?>
  <div class="col-sm-12 col-md-12 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Fotodokumentácia</h6>
      </div>
      <div class="card-body">
        <form id="jqdata" enctype="multipart/form-data" method="post" class="add-new-item-form-form" name="jqdata">
          <ul class="nav nav-tabs mb-3">
            <?php
              $types = $serviceV->getImageTypes();
              $i = 0;
              foreach ($types as $value => $type) {
                $temp = ($i==0)?"active":"";
                echo "
                <li class='nav-item'>
                  <span class='nav-link $temp image-type-link' aria-current='page' onclick='chooseImageTab(event, ".'"tab-'.$value.'"'.")'>".$type."</span>
                </li>
                ";
                $i++;
              }
              foreach ($types as $value => $type) {
                $vis = ($value==1)?"style='display: block';":"style='display: none'";
                echo "
                <div id='tab-$value' $vis class='image-type-content'>
                  <div class='row' id='image_docs_$value'>";
                      $images = $serviceV->getServiceImages($_GET['s'],$value);
                      foreach ($images as $key => $image) {
                        echo "
                        <div class='col-sm-4 col-md-4'>
                          <a href='".$image."' data-lightbox='set'><img src='".$image."' class='col-md-12'></a>";
                          if ($per > 1){
                          echo "<button class='btn btn-danger col-md-12' onclick='deleteImage(this)' data='".$key."' type='button'>ODSTRANIŤ</button>";
                          }
                        echo "</div>";
                      }
                    echo "
                  </div>
                </div>
                ";
              }
            ?>
          </ul>
          <?php
          $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 8); //pridanie technika
          if($per > 1){
          ?>
          <div class="form-group row">
            <div class="form-check col-sm-5 col-md-5 col-lg-5">
              <label class="btn btn-outline-secondary col-sm-12 col-md-12">
                <input type="file" name="photo" accept="image/jpeg" id="photo" onchange="uploadImage()">
                <input type="hidden" name="image-type" id="image-type" value="1">
                <input type="hidden" name="id_record" id="id_record" value="<?php echo $_GET['s']?>">
                Nahrať
              </label>
            </div>
            <div class="form-check col-sm-2 col-md-2 col-lg-2">
            </div>
            <?php
              if($per == 3){
            ?>
            <div class="form-check col-sm-5 col-md-5 col-lg-5">
              <a class="btn btn-outline-secondary col-sm-12 col-md-12" href="/adminlogin/dir.php?id=<?php echo $_GET['s']; ?>">Stiahnuť</a>
            </div>
            <?php } ?>
          </div>
          <?php
            }
          ?>
          <div class="form-group row">
            <div class="form-check col-sm-12 col-md-12 col-lg-12">
              <div class="col-sm-12 col-md-12 col-lg-12 text-center">
                <?php echo $svgCode;?>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php
    }
  ?>

  <?php
    $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 6);
    if(($per > 0) || ($data['id_vybavuje'] == $_SESSION['admin_logged'])){
  ?>
  <div class="col-sm-12 col-md-12 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Úkony</h6>
      </div>
      <div class="card-body">
        <table class="table table-bordered dataTable tablesorter" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
          <thead>
            <tr role="row">
              <th tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 180px;">Úkon / diel</th>
              <th tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 140px;">Jednotková cena</th>
              <th tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 100px;">Množstvo</th>
              <th tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 100px;">Dátum</th>
              <?php
                if($per == 3) {
              ?>
              <th tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 20px;"></th>
              <?php } ?>
            </tr>
          </thead>
          <tbody>
            <?php
              $spolu = 0;
              foreach ($operacie as $row) {
                $datum = date("d.m.Y H:i:s", strtotime($row['date_action']));
                echo '
                <tr role="row" class="odd">
                  <td>';
                if ($row['action'] == 'Cena práce' || $row['action'] == "Import" || $row['action'] == "Export"){
                  echo $row['action'];
                } else {
                  echo "<a class='text-dark' href='https://spares.eta.cz/b2b/search/?text=".$row['action']."' target='_blank'>".$row['action']."</a>";
                }
                echo '</td>
                  <td>'.sprintf("%.2f €", $row['jednotkova_cena']).'</td>
                  <td>'.$row['mnozstvo'].'</td>
                  <td>'.$datum.'</td>';
                  if($per == 3) {
                    echo '<td><a href="?s='.$_GET['s'].'&action='.$row['id_action'].'"><i class="fa-regular fa-pen-to-square"></i>upraviť</a></td>';
                  }
                echo '</tr>
                ';
                $spolu += ($row['mnozstvo'] == 0)?$row['jednotkova_cena']:$row['jednotkova_cena']*$row['mnozstvo'];
              }
              echo '
              <tr style="border-top: solid 2px;" role="row" class="odd">
                <th>Spolu:</th>
                <th colspan="4">'.sprintf("%.2f €", $spolu).'</th>
              </tr>';
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php } ?>
</div>
<div class="row">
  <div class="col-sm-12 col-md-12 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Stav opravy</h6>
      </div>
      <div class="card-body">
        <table class="table table-bordered dataTable tablesorter" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
          <thead>
            <tr role="row">
              <th tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 200px;">Stav</th>
              <th tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 200px;">Poznámka</th>
              <th tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 100px;">Zamestnanec</th>
              <th tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 100px;">Dátum</th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($oprava as $row) {
                $datum = date("d.m.Y H:i:s", strtotime($row['date_action']));
                echo '
                <tr role="row" class="odd">
                  <td>'.$row['description'].'</td>
                  <td>'.$row['note'].'</td>
                  <td>'.$row['fullname'].'</td>
                  <td>'.$datum.'</td>
                </tr>
                ';
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <?php
    $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 6);
    if(($per > 1) || ($data['id_vybavuje'] == $_SESSION['admin_logged'])){
  ?>
  <div class="col-sm-12 col-md-12 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Pridať úkony</h6>
      </div>
      <div class="card-body">
        <div class="form-group row">
          <?php if(($data['id_typ'] == 1) || ($data['id_typ'] == 2) || ($data['id_typ'] == 3)){?>
              <div class="form-check col-lg-4">
                <?php
                  if(!$isCenaPrace){
                    if(in_array($s_active,$cena_prace_available)){?>
                      <input type="checkbox" class="btn-check" name="cenaPrace" id="cenaPrace" autocomplete="off" checked onChange="cenaPrace(this.parentElement)">
                      <label class="btn btn-success w-100" for="cenaPrace"><i class="fa-solid fa-user-gear"></i> Cena práce</label>
              <?php }
                  } ?>
              </div>
        <?php } else { ?>
          <div class="form-check col-lg-4">
            <?php if($isSadzobnaJednotka){?>
              <input type="checkbox" class="btn-check" name="sadzobnaJednotka" id="sadzobnaJednotka" autocomplete="off" checked onChange="sadzobnaJednotka(this.parentElement)">
              <label class="btn btn-success w-100" for="sadzobnaJednotka"><i class="fa-solid fa-user-gear"></i> Sadzobná jednotka</label>
            <? } ?>
          </div>
        <?php } ?>
          <div class="form-check col-lg-4">
            <input type="checkbox" class="btn-check" name="nahradnyDiel" id="nahradnyDiel" autocomplete="off" onChange="nahradnyDiel(this.parentElement)">
            <label class="btn btn-outline-secondary w-100" for="nahradnyDiel"><i class="fas fa-cogs"></i> Náhradný diel</label>
          </div>
          <div class="form-check col-lg-4">
            <?php if(!$isImport or !$isExport){?>
              <input type="checkbox" class="btn-check" name="cenaDopravy" id="cenaDopravy" autocomplete="off" onChange="cenaDopravy(this.parentElement)">
              <label class="btn btn-outline-secondary w-100" for="cenaDopravy"><i class="fas fa-truck"></i> Cena dopravy</label>
            <?php } ?>
          </div>
        </div>
        <form method="POST">
          <input hidden type="text" name="ukon_send" id="ukon_send" value="" required>
          <input hidden type="number" step="1" name="mnozstvo_send" id="mnozstvo_send" value="" required>
          <input hidden type="number" step="0.01" name="cena_send" id="cena_send" value="" required>
          <div class="form-group row" id="ukon" style="display: none;">
            <label for="ukon" class="col-sm-3 col-form-label">Názov/kód:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control bg-light border-1" name="ukon" id="ukon_input" onkeyup="calculationDiel()">
            </div>
          </div>
          <div class="form-group row" id="jednotkova_cena" style="display: none;">
            <label for="jc" class="col-sm-3 col-form-label">Jednotková cena:</label>
            <div class="col-sm-8">
              <input type="number" step="0.01" class="form-control bg-light border-1" name="jc" id="jc_input" onkeyup="calculationDiel()"  onchange="calculationDiel()">
            </div>
            <label for="jc" class="col-sm-1 col-form-label">€</label>
          </div>
          <div class="form-group row" id="mnozstvo" style="display: none;">
            <label for="mnozstvo" class="col-sm-3 col-form-label">Množstvo:</label>
            <div class="col-sm-8">
              <input type="number" step="1" class="form-control bg-light border-1" name="mnozstvo" id="mnozstvo_input" onkeyup="calculationDiel()" onchange="calculationDiel()">
            </div>
            <label for="mnozstvo" class="col-sm-1 col-form-label">ks</label>
          </div>

          <?php if(($data['id_typ'] == 1) || ($data['id_typ'] == 2) || ($data['id_typ'] == 3)){
            if(!$isCenaPrace){

              if (in_array($s_active,$cena_prace_available)){?>
                <div class="form-group row" id="cena">
                  <label for="cena" class="col-sm-3 col-form-label">Cena práce:</label>
                  <div class="col-sm-8">
                    <input type="number" step="0.01" class="form-control bg-light border-1" name="cena" id="cena_input" onkeyup="calculationPraca()" onchange="calculationPraca()">
                  </div>
                  <label for="cena" class="col-sm-1 col-form-label">€</label>
                </div>

          <?php }
              }
            } else { ?>

            <div class="form-group row" id="cas">
              <label for="cas" class="col-sm-3 col-form-label">Čas:</label>
              <div class="col-sm-8">
                <input type="number" class="form-control bg-light border-1" name="cas" id="cas_input" onkeyup="sjcalculation(this.value)">
              </div>
              <label for="cas" class="col-sm-1 col-form-label">min.</label>
            </div>
            <div class="form-group row" id="sj">
              <label class="col-sm-3 col-form-label"></label>
              <label class="col-sm-9 col-form-label" id="sj_value"></label>
            </div>

          <?php } ?>

            <div class="form-group row" id="doprava" style="display: none;">
              <div class="form-grop row" id="dopTyp">
                <?php if(!$isImport){?>
                  <label class="text-right col-sm-4"for="prichadzajucaDoprava">Prichádzajúca</label>
                  <input class="col-sm-1"type="radio" id="prichadzajucaDoprava" name="typDopravy" value="in" checked onchange='calculationDoprava()'>
                <?php } ?>
                <label class="text-center col-sm-2"></label>
                <?php if(!$isExport){?>
                  <input class="col-sm-1"type="radio" id="odchadzajucaDoprava" name="typDopravy" value="out" onchange='calculationDoprava()'>
                  <label class="text-left col-sm-4"for="odchadzajucaDoprava">Odchádzajúca</label>
                <?php }?>
              </div>
              <label for="doprava_input" class="col-sm-3 col-form-label">Doprava:</label>
              <div class="col-sm-8">
                <input type="number" step="0.01" class="form-control bg-light border-1" name="doprava" id="doprava_input" onkeyup='calculationDoprava()' onchange='calculationDoprava()'>
              </div>
              <label for="doprava_input" class="col-sm-1 col-form-label">€</label>
            </div>

            <button type="submit" class="btn btn-success btn-icon-split col-sm-3" name="pridat_ukon" id="pridat_ukon">
              <span class="text">Pridať</span>
            </button>

        </form>
      </div>
    </div>
  </div>
<?php
  }
?>
<div class="col-sm-12 col-md-12">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Vyjadrenie</h6>
    </div>
    <div class="card-body">
      <form method="POST">
        <div class="form-group row col-sm-12 col-md-12">
          <?php echo '<textarea type="text" name="vyjadrenie" class="form-control bg-light border-1" rows="4"';
            if($_SESSION['admin_role'] == 2){echo " readonly";}
            echo ">";
          if (empty($data['vyjadrenie']) && $data['id_typ'] == 3 && !empty($data['kat_ukoncenia'])){
            $popis_text = array_reverse($oprava);
            $str = "";
            foreach ($popis_text as $row) {
              $str .= (empty($row['note']))?"":"# $row[note]";
            }
            $vyjadrenie = $constants['NAY_VYJADRENIE_PREFIX'];
            eval("\$vyjadrenie = \"$vyjadrenie\";");
            echo $vyjadrenie.$str;
          } elseif (!empty($data['vyjadrenie'])){
            echo $data['vyjadrenie'];
          } else {
            if($serviceV->isWasStatus($_GET['s'],10)){
              $popis_text = array_reverse($oprava);
              $str = "";
              foreach ($popis_text as $row) {
                $str .= (empty($row['note']))?"":"# $row[note]";
              }
              $vymena = $constants['VYMENA_VYJADRENIE_PREFIX'];
              eval("\$vymena = \"$vymena\";");
              echo $vymena.$str;
            } elseif ($serviceV->isWasStatus($_GET['s'],9)){
              $popis_text = array_reverse($oprava);
              $str = "";
              foreach ($popis_text as $row) {
                $str .= (empty($row['note']))?"":"# $row[note]";
              }
              $dobropis = $constants['DOBROPIS_VYJADRENIE_PREFIX'];
              eval("\$dobropis = \"$dobropis\";");
              echo $dobropis.$str;
            } else {
              $popis_text = array_reverse($oprava);
              $str = "";
              foreach ($popis_text as $row) {
                $str .= (empty($row['note']))?"":"# $row[note]";
              }
              echo $str;
            }
          }

          echo "</textarea>";
          ?>
        </div>
        <?php if($_SESSION['admin_role'] != 2){
              if (empty($data['vyjadrenie'])){
                echo "<button type='submit' class='btn btn-warning btn-icon-split col-sm-2' name='save_vyjadrenie'>Uložiť</button>";
              } else {
                echo "<button type='submit' class='btn btn-success btn-icon-split col-sm-2' name='save_vyjadrenie'>Upraviť</button>";
              }
            }?>
      </form>
    </div>
  </div>
</div>

</div>

<!--<div class="row mb-5">
  <div class="col-sm-12 col-md-12 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Vybavuje</h6>
      </div>
      <div class="card-body">
        <?php echo
          $data['fullname'];
        ?>
      </div>
    </div>
  </div>
</div>-->
<?php if($_SESSION['admin_role'] != 2){?>
<div class="modal fade bd-example-modal-lg-delivery-in" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Zmeniť</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="#">
        <div class="modal-body">
          <div class="form-group row">
            <div class="row col-sm-12 col-md-12">
              <label for="cislo_reklamacie_predajcu_delivery_in" class="col-sm-3 col-form-label">Číslo predajcu:</label>
              <div class="col-sm-9 col-md-9">
                <input class="form-control bg-light border-1" type="text" name="cislo_reklamacie_predajcu_delivery_in" id="cislo_reklamacie_predajcu_delivery_in" value="<?php echo $data['cislo_reklamacie_predajcu']; ?>">
              </div>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label for="hmotnost_delivery_in" class="col-sm-3 col-form-label">Hmotnosť:</label>
              <div class="col-sm-7 col-md-8">
                <input class="form-control bg-light border-1" type="number" step="0.01" name="hmotnost_delivery_in" id="cislo_reklamacie_predajcu_delivery_in" value="<?php echo $data['hmotnost']; ?>">
              </div>
              <label for="hmotnost_info" class="col-sm-1 col-form-label">kg</label>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label for="vzdialenost_delivery_in" class="col-sm-3 col-form-label">Vzdialenosť:</label>
              <div class="col-sm-7 col-md-8">
                <input class="form-control bg-light border-1" type="number" step="0.1" name="vzdialenost_delivery_in" id="cislo_reklamacie_predajcu_delivery_in" value="<?php echo $data['vzdialenost']; ?>">
              </div>
              <label for="vzdialenost_info" class="col-sm-1 col-form-label">km</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zrušiť</button>
          <button type="submit" class="btn btn-primary" name="save_delivery_in">Uložiť</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php }?>
<?php if($_SESSION['admin_role'] != 2){?>
<div class="modal fade bd-example-modal-lg-product" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Zmeniť</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="#">
        <div class="modal-body">
          <div class="form-group row">
            <div class="row col-sm-12 col-md-12">
              <label for="popis_product" class="col-sm-3 col-form-label">Popis chyby:</label>
              <div class="col-sm-7 col-md-9">
                <input class="form-control bg-light border-1" type="text" name="popis_product" id="popis_product" value="<?php echo $data['popis']; ?>">
              </div>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label for="prislusenstvo_product" class="col-sm-3 col-form-label">Príslušenstvo:</label>
              <div class="col-sm-7 col-md-9">
                <input class="form-control bg-light border-1" type="text" name="prislusenstvo_product" id="prislusenstvo_product" value="<?php echo $data['prislusenstvo']; ?>">
              </div>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label for="vyrobne_cislo_product" class="col-sm-3 col-form-label">Výrobné číslo:</label>
              <div class="col-sm-7 col-md-9">
                <input class="form-control bg-light border-1" type="text" name="vyrobne_cislo_product" id="vyrobne_cislo_product" value="<?php echo $data['vyrobne_cislo']; ?>">
              </div>
            </div>
            <div class="row col-sm-12 col-md-12">
              <label for="stav_vyrobku_product" class="col-sm-3 col-form-label">Stav:</label>
              <div class="col-sm-7 col-md-9">
                <input class="form-control bg-light border-1" type="text" name="stav_vyrobku_product" id="stav_vyrobku_product" value="<?php echo $data['stav_vyrobku']; ?>">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zrušiť</button>
          <button type="submit" class="btn btn-primary" name="save_product">Uložiť</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php }?>
<?php if(empty($data['servisny_list']) && $_SESSION['admin_role'] != 2){ ?>
  <div class="modal fade bd-example-modal-lg-sl" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Pridať</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="POST" action="#">
          <div class="modal-body">
            <div class="form-group row">
              <div class="row col-sm-12 col-md-12">
                <label for="cislo_reklamacie_predajcu_info" class="col-sm-3 col-form-label">Číslo reklamácie:</label>
                <div class="col-sm-9 col-md-9">
                  <input class="form-control bg-light border-1" type="text" pattern="[0-9]{10}" name="servisny_list" id="servisny_list" value="<?php echo $data['servisny_list']; ?>">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Zrušiť</button>
            <button type="submit" class="btn btn-primary" name="servisny_list_update">Uložiť</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php }?>

<?php if((!$isCenaPrace || !$isImport || !$isExport) && $_SESSION['admin_role'] != 2){ ?>
  <div class="modal fade bd-example-modal-lg-save" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Upozornenie</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="POST" action="#">
          <div class="modal-body">
            <div class="form-group row">
              <div class="row col-sm-12 col-md-12">
                <p>Si si istý že chceš uzavrieť opravu bez:
                  <div>
                    <ul>
                    <?php
                      if(!$isImport){echo "<li>cena za import</li>";}
                      if(!$isCenaPrace){echo "<li>cena práce</li>";}
                      if(!$isExport){echo "<li>cena za export</li>";}
                    ?>
                  </ul>
                </div>
                </p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Zrušiť</button>
            <button class="btn btn-primary" name='status' type='submit' value='16'>Uložiť</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php }?>
</script>
  <script type="text/javascript">
  var ajaxUrl = $(this).attr('action');
  $(document).ready(function() {
    $('#submitNow').click(function(e) {
      e.preventDefault();
      var form = $("#jqdata");
      var formdata = new FormData(form[0]);
      $.ajax({
        url: ajaxUrl,
        data: formdata,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function(output) {
          alert(output);
          $("#jqdata")[0].reset(); //reset all data from form.
        }
      });
    });
  });
  </script>
  <script src="js/service.js"></script>

  <?php
    if(($data['id_type']<3) && (!in_array($s_active,$cena_prace_available))){
      ?>
      <script type="text/javascript">
        window.onload = function(){
          var input = document.getElementById('nahradnyDiel').parentElement;
          nahradnyDiel(input);
        };
      </script>
      <?php
    }
  ?>

<?php
  include ABSPATH."adminlogin/theme/footer.php";
?>
