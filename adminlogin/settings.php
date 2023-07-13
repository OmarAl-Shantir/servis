<?php include "theme/page.php";

$adminV = new AdminView();
$per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 2);
if($per == 0 ){
  header("Location: ".HOMEPAGE);
}
$configV = new ConfigView();
$configC = new ConfigController();
$data = $configV->getAllConfig();

if($per == 3 ){
  if(isset($_POST['upravit_parameter']) && !isset($_POST['zmazat_ukon'])){
    $configC->updateConfigData($_POST['nazov_edit'], $_POST['hodnota_edit']);
    $returnUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $returnUrl = substr($returnUrl,0,strpos($returnUrl,"?"));
    header("Location: $returnUrl");
  }
}

if($per == 3 ){
  if(isset($_POST['zmazat_parameter'])){
    $configC->deleteParameter($_GET['name']);
    $returnUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $returnUrl = substr($returnUrl,0,strpos($returnUrl,"?"));
    header("Location: $returnUrl");
  }
}

if(isset($_GET['name'])){
  $dataE = $configV->getConfigbyName($_GET['name']);
  ?>
  <div class="col-sm-12 col-md-12">
    <div class="card shadow mb-4">
      <div class="card-header py-3" style="background: #e59733">
        <h6 class="m-0 font-weight-bold text-white">Upraviť</h6>
      </div>
      <div class="card-body" style="background: #ffe0b8;">
        <form method="POST">
          <div class="form-group row">
            <div class="row col-sm-12 col-md-12 col-lg-6">
              <label for="nazov_edit" class="col-sm-3 col-form-label">Názov:</label>
              <div class="col-sm-8 col-md-8">
                <input class="form-control bg-light border-1" type="text" name="nazov_edit" id="nazov_edit" value="<?php echo $dataE['name']; ?>" required>
              </div>
            </div>
            <div class="row col-sm-12 col-md-12 col-lg-6">
              <label for="hodnota_edit" class="col-sm-3 col-form-label">Hodnota:</label>
              <div class="col-sm-8 col-md-8">
                <input class="form-control bg-light border-1" type="text" name="hodnota_edit" id="hodnota_edit" value="<?php echo $dataE['value']; ?>" required>
              </div>
            </div>
          </div>
          <?php if($per == 3 ){?>
          <div class="form-group row">
            <div class="col-sm-2 col-md-2 col-lg-1">
              <button type="submit" class="btn btn-success btn-icon-split w-100" name="upravit_parameter" id="upravit_parameter">
                <span class="text">Upraviť</span>
              </button>
            </div>
            <div class="col-sm-2 col-md-2 col-lg_1">
              <button type="submit" class="btn btn-danger btn-icon-split w-100" name="zmazat_parameter" id="zmazat_parameter">
                <span class="text">Zmazať</span>
              </button>
            </div>
            <div class="col-sm-2 col-md-2 col-lg-1">
              <?php
                $returnUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
              ?>
              <a href="<?php echo $returnUrl;?>" class="btn btn-success btn-icon-split w-100" >
                <span class="text">Zrušiť</span>
              </a>
            </div>
          <?php } ?>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php
} if($per > 0 ){
?>
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Parametre</h6>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
          <div class="row col-sm-12 col-md-12">
            <div class="col-sm-12">
              <table class="table table-bordered dataTable tablesorter" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
                <thead>
                  <tr role="row">
                    <th class="sorting_asc" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 400px;">Názov</th>
                    <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 400px;">Hodnota</th>
                    <?php if($per == 3 ){?>
                      <th style="width: 96px;"></th>
                    <?php }?>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    foreach ($data as $name => $value) {
                      echo '
                      <tr class="data" role="row" >
                        <td class="name" data-toggle="tooltip" data-placement="top" title="'.$value['hint'].'">'.$value['description'].'</td>
                        <td class="value">'.$value['value'].'</td>';
                      if($per == 3 ){
                        echo '<td class="text-center"><a href="?name='.$name.'" class="btn btn-primary">Upraviť</a></td>';
                      }
                      echo '</tr>'
                      ;
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
  include 'theme/footer.php';
 ?>
