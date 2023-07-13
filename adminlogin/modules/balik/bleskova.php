<?php
  session_start();
  if ($_SESSION['admin_logged']==0){
    header("location: login.php");
  }
?>
<script type="text/javascript" src="js/zadavatel.js"></script>
<link rel="stylesheet" href="css/zadavatel.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js" integrity="sha512-n/4gHW3atM3QqRcbCn6ewmpxcLAHGaDjpEBu4xZd47N0W2oQ+6q7oc3PXstrJYXcbNU1OHdQ1T7pAP+gi5Yu8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js" integrity="sha512-qzgd5cYSZcosqpzpn7zF2ZId8f/8CHmFKZ8j7mU4OUXTNRd5g+ZHBPsgKEwoqxCtdQvExE5LprwwPAgoicguNg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js" integrity="sha512-dj/9K5GRIEZu+Igm9tC16XPOTz0RdPk9FGxfZxShWf65JJNU2TjbElGjuOo3EhwAJRPhJxwEJ5b+/Ouo+VqZdQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/css/jquery.tablesorter.pager.min.css" integrity="sha512-TWYBryfpFn3IugX13ZCIYHNK3/2sZk3dyXMKp3chZL+0wRuwFr1hDqZR9Qd5SONzn+Lja10hercP2Xjuzz5O3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
-->
<?php
  $balikV = new BalikView();
  $data = $balikV->get_balik_kat();
  $adminV = new AdminView();
  $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 11); //balík
  if($per < 1){
    die();
  }

  if($per > 1){
    if (isset($_POST['add_record'])){
      $balikC = new BalikController();
      $id_balik = $balikC->add_balik_kat($_POST['kat']);
      $_SESSION["message"] = "saved";
      $_SESSION["message_data"] = $id_balik;
      header("Refresh:0");
      die();
    }
  }
?>
<?php
 if($_SESSION["message"]=="saved"){
  echo '<div class="alert alert-success">
          <strong>Uložené!</strong> Záznam bol úspešne uložený do servisného systému.
        </div>';
  }
  $_SESSION["message"] = "";
  $_SESSION['message_data'] = "";
?>
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Predajcovia zoznam</h6>
  </div>
  <div class="card-body">
    <div class="form-group row">
      <div class="row col-sm-12 col-md-12 col-lg-12">
        <label for="searchBox" class="col-sm-1 col-form-label">Hľadať:</label>
        <div class="col-sm-10 col-md-10">
          <input class="form-control bg-light border-1" type="search" id="searchBox" onkeyup="searchRecord()" placeholder="ID | číslo balíka | dátum podania | reklamačné číslo predajcu*" aria-controls="dataTable">
        </div>
        <?php
        if($per > 1){
        ?>
          <div class="col-sm-10 col-md-1">
            <a type="button" class="" data-toggle="modal" data-target=".bd-example-modal-lg-new">
              <span class="btn btn-primary">Pridať</span>
            </a>
          </div>
        <?php }?>
      </div>
    </div>
    <div class="table-responsive">
      <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
          <div class="row col-sm-12 col-md-12">
            <div class="col-sm-12">
              <form target="_blank" method="POST" action="zvoz.php">
                <table class="table table-bordered dataTable tablesorter" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
                  <thead>
                    <tr role="row">
                      <th class="sorting_asc" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 40px;">ID</th>
                      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 100px;">číslo balíka</th>
                      <th class="sorting" tabindex="0" aria-controls="dataTable"  colspan="1" aria-label="Office: activate to sort column ascending" style="width: 193px;">Dátum podania</th>
                      <?php
                      if($per > 0){
                        echo '<th style="width: 96px;"></th>';
                      }
                      ?>
                      <th style="width: 96px;"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($data as $row) {
                      $podane = date("d.m.Y", strtotime($row['datum_podania']));
                      echo '
                      <tr class="active data" role="row" >
                        <td class="id_balik sorting_1">'.$row['id_balik'].'</td>
                        <td class="cislo_balika">'.$row['cislo_balika'].'</td>
                        <td class="datum_podania">';
                        echo ($podane == "01.01.1970")?"":$podane;
                        echo "</td>";
                        if($per > 0){
                          echo '<td class="text-center"><a href=/adminlogin/modules/balik/detail.php?s='.$row['id_balik'].' class="btn btn-primary">detaily</a></td>';
                        }
                        echo '
                        <td class="text-center"><button type="submit" name="balik" value="'.$row['id_balik'].'" class="btn btn-primary"><i class="fa-solid fa-file-pdf"></i></button></td>
                        </tr>
                        ';
                    }
                    ?>
                  </tbody>
                </table>
              </form>
            </div>
          </div>
        </div>
    </div>
  </div>
</div>

<?php
if($per > 1){
  ?><div class="modal fade bd-example-modal-lg-new" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
          <p>Checete vytvoriť nový balík?</p>
          <select name="kat" class="form-control">
            <?php
              for($i=1;$i<=8;$i++){
                echo "<option value='$i'>Kategória $i</option>";
              }
            ?>
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zrušiť</button>
          <button type="submit" class="btn btn-primary" name="add_record">Vytvoriť</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php } ?>
