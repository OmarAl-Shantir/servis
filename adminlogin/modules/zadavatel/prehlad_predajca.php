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
  $zadavatelV = new ZadavatelView();
  $data = $zadavatelV->get_predajca();
  $adminV = new AdminView();
  $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 10); //firma a zakaznici
  if($per < 1){
    die();
  }

  if($per > 1){
    if (isset($_POST['add_record'])){
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
      $id_zadavatel = $zadavatelC->add_zadavatel($parametre);
      $_SESSION["message"] = "saved";
      $_SESSION["message_data"] = $id_zadavatel;
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
          <input class="form-control bg-light border-1" type="search" id="searchBox" onkeyup="searchRecord()" placeholder="ID | meno | email | ičo | dič" aria-controls="dataTable">
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
              <table class="table table-bordered dataTable tablesorter" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
                <thead>
                  <tr role="row">
                    <th class="sorting_asc" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 40px;">ID</th>
                    <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 100px;">Firma</th>
                    <th class="sorting" tabindex="0" aria-controls="dataTable"  colspan="1" aria-label="Office: activate to sort column ascending" style="width: 193px;">Kontaktná osoba</th>
                    <th class="sorting" tabindex="0" aria-controls="dataTable"  colspan="1" aria-label="Office: activate to sort column ascending" style="width: 150px;">Email</th>
                    <th class="sorting" tabindex="0" aria-controls="dataTable"  colspan="1" aria-label="Office: activate to sort column ascending" style="width: 150px;">IČO</th>
                    <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 97px;">DIČ</th>
                    <?php
                    if($per > 0){
                      echo '<th style="width: 96px;"></th>';
                    }
                    ?>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    foreach ($data as $row) {
                      $vznik = date("d.m.Y", strtotime($row['datum_vzniku']));
                      $prijatie = date("d.m.Y", strtotime($row['datum_prijatia']));
                      switch ($row['id_stav_opravy']) {
                        case 9:
                          $status = "inactive";
                          break;
                        case 16:
                          $status = "inactive";
                          break;
                        case 20:
                          $status = "inactive";
                          break;
                        case 21:
                          $status = "inactive";
                          break;
                        default:
                          $status = "active";
                          break;
                      }
                      $emails = array($row['email1'], $row['email2'], $row['email3']);
                      $emails = array_filter($emails, fn($value) => !is_null($value) && $value !== '');
                      $email = implode($emails, ', ');
                      //$email = str_replace(", ,","",$email);
                      echo '
                      <tr class="'.$status.' data" role="row" >
                        <td class="id sorting_1">'.$row['id_zadavatel'].'</td>
                        <td class="firma">'.$row['firma'].'</td>
                        <td class="meno">'.$row['meno'].'</td>
                        <td class="email">'.$email.'</td>
                        <td class="ico">'.$row['ico'].'</td>
                        <td class="dic">'.$row['dic'].'</td>';
                        if($per > 0){
                          echo '<td class="text-center"><a href=/adminlogin/modules/zadavatel/detail.php?s='.$row['id_zadavatel'].' class="btn btn-primary">detaily</a></td>';
                        }
                        echo '
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
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zrušiť</button>
          <button type="submit" class="btn btn-primary" name="add_record">Uložiť</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php } ?>
