<?php
  session_start();
  if ($_SESSION['admin_logged']==0){
    header("location: login.php");
  }

  if (isset($_GET['a']) && $_SESSION['admin_role'] != 2){
    $serviceC = new ServiceController();
    $serviceC->addoptItem($_GET['a']);
    //$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    //$returnUrl = substr($url,0,strpos($url,"&a"));
    $returnUrl = "./detail.php?s=".$_GET['a'];
    //var_dump($returnUrl);
    header("Location: $returnUrl");
  }
?>
<script type="text/javascript" src="js/service.js"></script>
<!--<script type="text/javascript" src="js/pagination.js"></script>-->
<link rel="stylesheet" href="css/servis.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js" integrity="sha512-n/4gHW3atM3QqRcbCn6ewmpxcLAHGaDjpEBu4xZd47N0W2oQ+6q7oc3PXstrJYXcbNU1OHdQ1T7pAP+gi5Yu8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js" integrity="sha512-qzgd5cYSZcosqpzpn7zF2ZId8f/8CHmFKZ8j7mU4OUXTNRd5g+ZHBPsgKEwoqxCtdQvExE5LprwwPAgoicguNg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js" integrity="sha512-dj/9K5GRIEZu+Igm9tC16XPOTz0RdPk9FGxfZxShWf65JJNU2TjbElGjuOo3EhwAJRPhJxwEJ5b+/Ouo+VqZdQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/css/jquery.tablesorter.pager.min.css" integrity="sha512-TWYBryfpFn3IugX13ZCIYHNK3/2sZk3dyXMKp3chZL+0wRuwFr1hDqZR9Qd5SONzn+Lja10hercP2Xjuzz5O3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php
  $serviceV = new ServiceView();
  $data = $serviceV->getAllRecords($_GET['limit'], ($_GET['page']-1)*$_GET['limit']);
?>
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Servis zoznam</h6>
  </div>
  <div class="card-body">
    <div class="form-group">
      <div class="row col-sm-12 col-md-12 col-lg-12">
        <label for="searchBox" class="col-sm-1 col-form-label">Hľadať:</label>
        <div class="col-sm-8 col-md-9">
          <input class="form-control bg-light border-1" type="search" id="searchBox" onkeyup="preLoad('',100,0,this.value)" placeholder="ID | ser. list | ref. číslo | číslo predajcu | typ | číslo prepravy" aria-controls="dataTable">
        </div>
        <div class="col-sm-3 col-md-2">
        <select class="form-control bg-light border-1" id="statusType" onChange="preLoad('',100,0,this.value)">
          <option id="status_all" value="0">Všetky</option>
          <?php
            $status_types = $serviceV->get_all_statuses();
            foreach ($status_types as $id => $status_type){
              echo "<option id='status_$id' value='".$status_type['description']."'>".$status_type['description']."</option>";
            }
          ?>
        </select>
        </div>
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
                  <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 100px;">Servisný list</th>
                  <th class="sorting" tabindex="0" aria-controls="dataTable"  colspan="1" aria-label="Office: activate to sort column ascending" style="width: 193px;">Ref.číslo</th>
                  <th class="sorting" tabindex="0" aria-controls="dataTable"  colspan="1" aria-label="Office: activate to sort column ascending" style="width: 150px;">Č. predajcu</th>
                  <th class="sorting" tabindex="0" aria-controls="dataTable"  colspan="1" aria-label="Office: activate to sort column ascending" style="width: 150px;">Výrobné č.</th>
                  <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 97px;">Vznik</th>
                  <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 97px;">Prijatie</th>
                  <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 97px;">Typ</th>
                  <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 197px;">Stav</th>
                  <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 300px;">Pridelené</th>
                  <th style="width: 96px;"></th>
                </tr>
              </thead>
              <tbody id="servisData">
                <?php
                  /*foreach ($data as $row) {
                    $notes = $serviceV->get_record_notes($row['id_service_item']);
                    $vznik = date("d.m.Y", strtotime($row['datum_vzniku']));
                    $prijatie = date("d.m.Y", strtotime($row['datum_prijatia']));
                    $lz = $serviceV->in_package($row['id_service_item']);
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
                    echo '
                    <tr class="'.$status.' data" role="row" >
                      <td class="id sorting_1">'.$row['id_service_item'].'</td>';
                      if($row['id_typ']<4){
                        echo '<td class="s"><a class="text-dark" href="https://spares.eta.cz/b2b/reclamationServiceRecord?id='.$row['servisny_list'].'" target="_blank">'.$row['servisny_list'].'</a></td>';
                      } else {
                        echo '<td class="s">'.$row['servisny_list'].'</td>';
                      }
                      echo '<td class="r">'.$row['product_ref'].'</td>
                      <td class="cislo_predajcu">'.$row['cislo_reklamacie_predajcu'].'</td>
                      <td class="vyrobne_cislo">'.$row['vyrobne_cislo'].'</td>
                      <td class="d-none cislo_prepravy_in">'.$row['cislo_prepravy'].'</td>
                      <td class="d-none cislo_prepravy_out">'.$row['cislo_prepravy_out'].'</td>
                      <td>'.$vznik.'</td>
                      <td>'.$prijatie.'</td>
                      <td class="t">'.$row['typ'].'</td>';
                      $stav = "<td class='stav'>";
                      if (in_array($row['id_stav_opravy'],array(19,21))) {
                        $stav .= $row['stav_opravy'].'<br>'.$notes[$row['id_stav_opravy']];
                      } else {
                        $stav .= $row['stav_opravy'];
                      }
                      if (($serviceV->to_ETA($row['id_service_item'])) && !is_null($lz)){
                        $stav.='<br><strong>'.$lz.'</strong></td>';
                      } else {
                        $stav.='</td>';
                      }
                      echo $stav;
                      if (empty($row['fullname']) && $_SESSION['admin_role'] != 2){
                        echo '<td class="text-center"><a href=/adminlogin/modules/servis/?t=A&p&a='.$row['id_service_item'].' class="btn btn-primary">Prevziať</a></td>';
                      } else {
                        echo '<td>'.$row['fullname'].'</td>';
                      }
                      echo '<td class="text-center"><a href=/adminlogin/modules/servis/detail.php?s='.$row['id_service_item'].' class="btn btn-primary">detaily</a></td>
                    </tr>
                    ';
                  }*/
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <nav aria-label="pagination" id="navPagination">
      <ul class="pagination" id="pagination">
      </ul>
    </nav>
  </div>
</div>
<script type="text/javascript">
  window.onload = function() {
    preLoad();
  };
</script>
