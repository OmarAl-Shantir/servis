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
  $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 11); //balik
  if($per < 1){
    die();
  }

  $balikV = new BalikView();
  $produkty = $balikV->get_volne($_GET['s']);
  $inPackage = $balikV->in_package($_GET['s']);
  $balik = $balikV->get_balik_type($_GET['s']);
  $data = ($balik['typ']=="likvidácia")?$balikV->get_balik($_GET['s']):$balikV->get_balik_kat($_GET['s']);
  $editable = empty($data[0]['datum_podania']);
  if ($editable){
    if($per == 3){
      $balikC = new BalikController();
      if (isset($_POST['save'])){
        $balikC->update_balik($_GET['s'], $_POST['datum_podania']);
        if(($balik['typ']>=1) && ($balik['typ']<=8)){
          $serviceC = new ServiceController();
          foreach ($inPackage as $row) {
            $serviceC->changeStatus($row['id_service_item'],18, $_SESSION['admin_logged']);
          }
        }
        header("Refresh:0");
      }

      if (isset($_POST['savePackage'])){
        $balikC->save_in_package_likvidacia($_GET['s'], $_POST['inPackage']);
        header("Refresh:0");
        die();
      }
    }
  }

  ?>
<link rel="stylesheet" href="css/balik.css">
<link rel="stylesheet" href="../../theme/vendor/lightbox/css/lightbox.min.css">

<?php
  if ($editable){
    echo '<script src="js/balik.js"></script>';
  }
?>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Prehľad</h6>
      </div>
      <div class="card-body">
        <form method="POST">
          <div class="form-group row">
            <div class="row">
              <div class="col col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <label class="px-1"><strong><?php echo $data[0]['cislo_balika'];?></strong></label>
              </div>
            </div>
            <div class="row">
              <div class="col col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <label class="px-1" for="datum_podania">Dátum podania: </label>
                <?php if($editable){ ?>
                  <input type="date" name="datum_podania" class="form-control bg-light border-1" value="<?php echo date("Y-m-d");?>" onchange='datumPodania(this)' onkeyup='datumPodania(this)'>
                <?php } else {
                  echo "<label>".date("d.m.Y", strtotime($data[0]['datum_podania']))."</label>";
                }?>
              </div>
            </div>
            <?php
              if ($editable){
                if($per == 3){
              ?>
                <div class="row">
                  <div class="col-sm-12 col-md-3 col-lg-2 col-xl-2">
                    <a type="button" class="pt-3" data-toggle="modal" data-target=".bd-example-modal-lg-save">
                      <span class="btn btn-success">Uložiť a uzavrieť</span>
                    </a>
                  </div>
                </div>
            <?php }
            }?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row">

  <div class="col-sm-12 col-md-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Produkty v balíku</h6>
      </div>
      <div class="card-body table-responsive">
        <form method="POST">
        <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
            <div class="row col-sm-12 col-md-12">
              <div class="col-sm-12">
                <table class="table table-bordered dataTable tablesorter" id="package" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
                  <thead>
                    <tr role="row">
                      <th class="sorting_asc" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 40px;">ID</th>
                      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 100px;">Servisný list</th>
                      <th class="sorting" tabindex="0" aria-controls="dataTable"  colspan="1" aria-label="Office: activate to sort column ascending" style="width: 193px;">Produkt</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      foreach ($inPackage as $row) {
                        if($editable){
                          echo '
                          <tr class="active data" role="row" ondblclick="outOfPackage(this)">
                            <input type="hidden" name="inPackage[]" value="'.$row['id_service_item'].'">
                            <td class="id_balik sorting_1">'.$row['id_service_item'].'</td>
                            <td class="cislo_balika">'.$row['servisny_list'].'</td>
                            <td class="cislo_balika">'.$row['product_ref'].'</td>
                          </tr>
                          ';
                        } else {
                          echo '
                          <tr class="active data" role="row">
                              <td class="id_balik sorting_1"><a class="text-reset" href="'.HOMEPAGE.'modules/servis/detail.php?s='.$row['id_service_item'].'">'.$row['id_service_item'].'</a></td>
                              <td class="cislo_balika"><a class="text-reset" href="'.HOMEPAGE.'modules/servis/detail.php?s='.$row['id_service_item'].'">'.$row['servisny_list'].'</a></td>
                              <td class="cislo_balika"><a class="text-reset" href="'.HOMEPAGE.'modules/servis/detail.php?s='.$row['id_service_item'].'">'.$row['product_ref'].'</a></td>
                          </tr>
                          ';
                        }
                      }
                    ?>
                  </tbody>
                </table>
                <?php
                  if ($editable){
                    echo '<button type="submit" class="btn btn-success" name="savePackage">Uložiť</button>';
                  } ?>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>

  <?php
    if ($editable){
  ?>
  <div class="col-sm-12 col-md-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Všetky dostupné produkty</h6>
      </div>
      <div class="card-body table-responsive">
        <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
            <div class="row col-sm-12 col-md-12">
              <div class="col-sm-12">
                <table class="table table-bordered dataTable tablesorter" id="products" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
                  <thead>
                    <tr role="row">
                      <th class="sorting_asc" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 40px;">ID</th>
                      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 100px;">Servisný list</th>
                      <th class="sorting" tabindex="0" aria-controls="dataTable"  colspan="1" aria-label="Office: activate to sort column ascending" style="width: 193px;">Produkt</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      foreach ($produkty as $row) {
                        echo '
                        <tr class="active data" role="row" ondblclick="toPackage(this)">
                          <input type="hidden" value="'.$row['id_service_item'].'">
                          <td class="id_balik sorting_1">'.$row['id_service_item'].'</td>
                          <td class="cislo_balika">'.$row['servisny_list'].'</td>
                          <td class="cislo_balika">'.$row['product_ref'].'</td>
                        </tr>
                        ';
                      }
                    ?>

                  </tbody>
                </table>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
  <?php
    }
  ?>
</div>
<div class="modal fade bd-example-modal-lg-save" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="exampleModalLabel">Uložiť</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <form method="POST" action="#">
      <div class="modal-body">
        <p>Ste si istý že chcete uložiť dátum podania balíku? Tento úkon uzavrie balík a už ho nebudete môcť ďalej upravovať.</p>
      </div>
      <div class="modal-footer">
        <input type="hidden" name="datum_podania" id="datum_podania" class="form-control bg-light border-1" value="<?php echo empty($data[0]['datum_podania'])? date("Y-m-d"):date("Y-m-d", strtotime($data[0]['datum_podania']));?>">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zrušiť</button>
        <button type="submit" class="btn btn-primary" name="save">Uložiť a uzavrieť</button>
      </div>
    </form>
  </div>
</div>
</div>
<?php
  include ABSPATH."adminlogin/theme/footer.php";
?>
