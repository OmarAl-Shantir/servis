<?php
  session_start();
  if ($_SESSION['admin_logged']==0){
    header("location: login.php");
  }
?>
<script type="text/javascript" src="js/statistika.js"></script>
<link rel="stylesheet" href="css/statistika.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js" integrity="sha512-n/4gHW3atM3QqRcbCn6ewmpxcLAHGaDjpEBu4xZd47N0W2oQ+6q7oc3PXstrJYXcbNU1OHdQ1T7pAP+gi5Yu8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js" integrity="sha512-qzgd5cYSZcosqpzpn7zF2ZId8f/8CHmFKZ8j7mU4OUXTNRd5g+ZHBPsgKEwoqxCtdQvExE5LprwwPAgoicguNg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js" integrity="sha512-dj/9K5GRIEZu+Igm9tC16XPOTz0RdPk9FGxfZxShWf65JJNU2TjbElGjuOo3EhwAJRPhJxwEJ5b+/Ouo+VqZdQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/css/jquery.tablesorter.pager.min.css" integrity="sha512-TWYBryfpFn3IugX13ZCIYHNK3/2sZk3dyXMKp3chZL+0wRuwFr1hDqZR9Qd5SONzn+Lja10hercP2Xjuzz5O3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
-->
<?php
  if(empty($_GET)){
    $_GET['from'] = date('Y-m-d',strtotime('first day of last month'));
    $_GET['to'] = date('Y-m-t',strtotime('last day of last month'));
  }

  $types = array(0 => "Všetky", 1 => "Záručný", 2 => "Predpredajný", 3 => "NAY", 4 => "Pozáručný");
  $_SESSION['statistic_type'] = (isset($_POST['typ']))?$_POST['typ']:$_SESSION['statistic_type'];
  $statistikyV = new StatistikyView();
  $limits = array($_GET['from'], $_GET['to']);
  //$limits = NULL;
  $service_ids = $statistikyV->get_finished_services($limits, $_SESSION['statistic_type']);
  $employees = $statistikyV->get_employees($service_ids);
  $maxImport = $maxExport = $maxCenaPrace = $maxND = $maxDni = $maxObrat = 0.01;
//import
  foreach ($employees as $id_admin => $fullname) {
    $import = $statistikyV->get_import_by_employee($id_admin, $service_ids);
    $maxImport = ($maxImport < $import)?$import:$maxImport;
    $employees[$id_admin]['import'] = $import;
  }
  //Export
  foreach ($employees as $id_admin => $fullname) {
    $export = $statistikyV->get_export_by_employee($id_admin, $service_ids);
    $maxExport = ($maxExport < $export)?$export:$maxExport;
    $employees[$id_admin]['export'] = $export;
  }
  // Cena práce
  foreach ($employees as $id_admin => $fullname) {
    $praca = $statistikyV->get_cena_prace_by_employee($id_admin, $service_ids);
    $maxCenaPrace = ($maxCenaPrace < $praca)?$praca:$maxCenaPrace;
    $employees[$id_admin]['cena_prace'] = $praca;
  }
  // Cena ND
  foreach ($employees as $id_admin => $fullname) {
    $nd = $statistikyV->get_nd_by_employee($id_admin, $service_ids);
    $maxND = ($maxND < $nd)?$nd:$maxND;
    $employees[$id_admin]['nd'] = $nd;
  }
  //cas od pridelenia technikovi
  foreach($employees as $id_admin => $fullname) {
    $d = $statistikyV->get_cas_od_pridelenia_by_employee($id_admin, $service_ids);
    $dni = $d[0];
    $maxDni = ($maxDni < $dni/$d[1])?$dni/$d[1]:$maxDni;
    $employees[$id_admin]['dni'] = $dni/$d[1];
  }

  foreach($employees as $id_admin => $fullname) {
    $obrat = $fullname['import'] + $fullname['export'] + $fullname['cena_prace'] + $fullname['nd'];
    $maxObrat = ($maxObrat < $obrat)?$obrat:$maxObrat;
    $employees[$id_admin]['obrat'] = $obrat;
  }
  //cas v servise
  $vServise = $statistikyV->get_cas_od_prijatia($service_ids);

  //spôsob ukončenia podľa technika
  $aUkoncenia = array();
  foreach($employees as $id_admin => $fullname) {
    $tUkoncenia = $statistikyV->get_sposov_ukoncenia_by_employee($id_admin, $service_ids);
    $aUkoncenia = array_merge($aUkoncenia, array_keys($tUkoncenia));
    $employees[$id_admin]['ukoncenie'] = $tUkoncenia;
  }
  $aUkoncenia = array_unique($aUkoncenia);
  sort($aUkoncenia);
  foreach ($employees as $id_admin => $row) {
    $toAdd = array_diff($aUkoncenia, array_keys($employees[$id_admin]['ukoncenie']));
    foreach ($toAdd as $key) {
      $employees[$id_admin]['ukoncenie'][$key] = 0;
    }
  }

  // spôsoby ukončenia
  $vUkoncenia = $statistikyV->get_ukoncenia($service_ids);
  $maxVUkoncenia = array_values($vUkoncenia)[0];

  // spôsoby prijatia
  $vPrijatia = $statistikyV->get_prijatia($service_ids);
  $maxVPrijatia = array_values($vPrijatia)[0];


  // najviac reklamované modely
  $vModely = $statistikyV->get_modely($service_ids);
  $maxVModely = array_values($vModely)[0];

  //najviac reklamuje (predajca)
  $vReklamujuci = $statistikyV->get_reklamujuci($service_ids);
  $maxVReklamujuci = array_values($vReklamujuci)[0];

  //najviac reklamované značky
  foreach ($vModely as $model => $pocet) {
    switch (strtoupper(substr($model,0,3))) {
      case "GOG":
        $vZnacky["GoGEN"] += $pocet;
        break;
      case "ETA":
        $vZnacky["ETA"] += $pocet;
        break;
      case "VAL":
        $vZnacky["Valera"] += $pocet;
        break;
      case "GAL":
        $vZnacky["Gallet"] += $pocet;
        break;
      case "GOD":
        $vZnacky["Goddess"] += $pocet;
        break;
      case "JVC":
        $vZnacky["JVC"] += $pocet;
        break;
      case "HYU":
        $vZnacky["HYUNDAI"] += $pocet;
        break;
      case "GND":
        $vZnacky["GND"] += $pocet;
        break;
      default:
        $vZnacky["INE"] += $pocet;
    }
  }
  arsort($vZnacky);
  $maxVZnacky = array_values($vZnacky)[0];


  $adminV = new AdminView();
  $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 10); //firma a zakaznici
  if($per < 1){
    die();
  }

  if(isset($_POST['export'])){

  }

?>
<div id="statistic">
  <div class="row" id="type">
    <div class="col-xl-12 col-lg-12">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Typ servisu</h6>
        </div>
        <div class="card-body">
          <form method="POST">
            <div class="row justify-content-md-center">
              <?php
              foreach ($types as $key => $value) {
                if ($key == $_SESSION['statistic_type']){
                  echo "
                  <div class='col-xl-2 col-lg-2'>
                    <button name='typ' type='submit' class='form-group col-xl-12 col-lg-12 btn btn-success' value='$key'>$value</button>
                  </div>
                    ";
                  $service_type = $value;
                } else {
                  echo "
                  <div class='col-xl-2 col-lg-2'>
                    <button name='typ' type='submit' class='form-group col-xl-12 col-lg-12 btn btn-secondary' value='$key'>$value</button>
                  </div>
                    ";
                }

              }
              ?>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-6 col-lg-6">
      <div class="card shadow mb-4">
          <div class="card-body">
            <div class="info" style="display: none">
              <p class="info">
                <?php
                if (isset($_GET['from']) && isset($_GET['to'])){
                  $date = new DateTime($_GET['from']);
                  $od = $date->format('d.m.Y');
                  $date = new DateTime($_GET['to']);
                  $do = $date->format('d.m.Y');
                }
                  echo "Typ servisu: <b>$service_type</b><br>
                        obdobie: <b>$od - $do</b>";
                ?>
              </p>
            </div>
            <div class="form-group row" id="selector">
              <form method="GET">
                <div class="form-group row col-sm-12 col-md-12 col-lg-12">
                  <label for="searchBox" class="col-sm-2 col-form-label">Od:</label>
                  <div class="col-sm-10 col-md-10">
                    <input type="date" name="from" value="<?php echo $_GET['from'];?>" class="form-control bg-light border-1">
                  </div>
                </div>
                <div class="form-group row col-sm-12 col-md-12 col-lg-12">
                  <label for="searchBox" class="col-sm-2 col-form-label">Do:</label>
                  <div class="col-sm-10 col-md-10">
                    <input type="date" name="to" value="<?php echo $_GET['to'];?>" class="form-control bg-light border-1">
                  </div>
                </div>
                <div class="form-group row col-md-6 offset-md-3">
                  <button type="submit" class="col-sm-12 btn btn-primary">Zobraziť</button>
                </div>
              </form>
              <form method="POST">
                <div class="form-group row col-md-6 offset-md-3">
                  <button type="button" name="export" class="col-sm-12 btn btn-success" onclick="printStatistics()"><i class="fa-solid fa-file-pdf"></i> Vytlačiť</button>
                </div>
              </form>
            </div>
          </div>
      </div>
    </div>
    <div class="col-xl-6 col-lg-6">
      <div class="card shadow mb-4">
          <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Obrat (oprava + ND + doprava)</h6>
          </div>
          <div class="card-body">
              <?php
              $sum = 0;
                foreach ($employees as $id_admin => $row) {
                  echo "<h4 class='small font-weight-bold'>".$row['fullname']."
                          <span class='float-right'>".sprintf("%.2f €", $row['obrat'])."</span>
                        </h4>
                        <div class='progress mb-4'>
                          <div class='progress-bar bg-danger' role='progressbar' style='width: calc(".$row['obrat']." / $maxObrat * 100%)' aria-valuenow='".$row['obrat']."' aria-valuemin='0' aria-valuemax='$maxObrat'></div>
                        </div>";
                        $sum += $row['obrat'];
                      }
                      echo "<h4 class='border-top border-danger pt-2 small font-weight-bold'>Spolu
                              <span class='float-right'>".sprintf("%.2f €", $sum)."</span>
                            </h4>";
                    ?>
          </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xl-6 col-lg-6">
      <div class="card shadow mb-4">
          <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Cena za import</h6>
          </div>
          <div class="card-body">
              <?php
              $sum = 0;
                foreach ($employees as $id_admin => $row) {
                  echo "<h4 class='small font-weight-bold'>".$row['fullname']."
                          <span class='float-right'>".sprintf("%.2f €", $row['import'])."</span>
                        </h4>
                        <div class='progress mb-4'>
                          <div class='progress-bar bg-danger' role='progressbar' style='width: calc(".$row['import']." / $maxImport * 100%)' aria-valuenow='".$row['import']."' aria-valuemin='0' aria-valuemax='$maxImport'></div>
                        </div>";
                        $sum += $row['import'];
                      }
                      echo "<h4 class='border-top border-danger pt-2 small font-weight-bold'>Spolu
                              <span class='float-right'>".sprintf("%.2f €", $sum)."</span>
                            </h4>";
                    ?>
          </div>
      </div>
    </div>

    <div class="col-xl-6 col-lg-6">
      <div class="card shadow mb-4">
          <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Cena za export</h6>
          </div>
          <div class="card-body">
              <?php
              $sum = 0;
                foreach ($employees as $id_admin => $row) {
                  echo "<h4 class='small font-weight-bold'>".$row['fullname']."
                          <span class='float-right'>".sprintf("%.2f €", $row['export'])."</span>
                        </h4>
                        <div class='progress mb-4'>
                          <div class='progress-bar bg-danger' role='progressbar' style='width: calc(".$row['export']." / $maxExport * 100%)' aria-valuenow='".$row['export']."' aria-valuemin='0' aria-valuemax='$maxExport'></div>
                        </div>";
                        $sum += $row['export'];
                      }
                      echo "<h4 class='border-top border-danger pt-2 small font-weight-bold'>Spolu
                              <span class='float-right'>".sprintf("%.2f €", $sum)."</span>
                            </h4>";
              ?>
          </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-6 col-lg-6">
      <div class="card shadow mb-4">
          <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Cena za prácu</h6>
          </div>
          <div class="card-body">
              <?php
              $sum = 0;
                foreach ($employees as $id_admin => $row) {
                  echo "<h4 class='small font-weight-bold'>".$row['fullname']."
                          <span class='float-right'>".sprintf("%.2f €", $row['cena_prace'])."</span>
                        </h4>
                        <div class='progress mb-4'>
                          <div class='progress-bar bg-danger' role='progressbar' style='width: calc(".$row['cena_prace']." / $maxCenaPrace * 100%)' aria-valuenow='".$row['cena_prace']."' aria-valuemin='0' aria-valuemax='$maxCenaPrace'></div>
                        </div>";
                  $sum += $row['cena_prace'];
                }
                echo "<h4 class='border-top border-danger pt-2 small font-weight-bold'>Spolu
                        <span class='float-right'>".sprintf("%.2f €", $sum)."</span>
                      </h4>";
              ?>
          </div>
      </div>
    </div>

    <div class="col-xl-6 col-lg-6">
      <div class="card shadow mb-4">
          <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Cena za náhradné diely</h6>
          </div>
          <div class="card-body">
              <?php
              $sum = 0;
                foreach ($employees as $id_admin => $row) {
                  echo "<h4 class='small font-weight-bold'>".$row['fullname']."
                          <span class='float-right'>".sprintf("%.2f €", $row['nd'])."</span>
                        </h4>
                        <div class='progress mb-4'>
                          <div class='progress-bar bg-danger' role='progressbar' style='width: calc(".$row['nd']." / $maxND * 100%)' aria-valuenow='".$row['nd']."' aria-valuemin='0' aria-valuemax='$maxND'></div>
                        </div>";
                  $sum += $row['nd'];
                }
                echo "<h4 class='border-top border-danger pt-2 small font-weight-bold'>Spolu
                        <span class='float-right'>".sprintf("%.2f €", $sum)."</span>
                      </h4>";
              ?>
          </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-6 col-lg-6">
      <div class="card shadow mb-4">
          <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Priemerný čas u technika</h6>
          </div>
          <div class="card-body">
              <?php
              $sum = $i = 0;
                foreach ($employees as $id_admin => $row) {
                  echo "<h4 class='small font-weight-bold'>".$row['fullname']."
                          <span class='float-right'>".sprintf("%.2f d/oprava", $row['dni'])."</span>
                        </h4>
                        <div class='progress mb-4'>
                          <div class='progress-bar bg-danger' role='progressbar' style='width: calc(".$row['dni']." / $maxDni * 100%)' aria-valuenow='".$row['dni']."' aria-valuemin='0' aria-valuemax='$maxDni'></div>
                        </div>";
                  $sum += $row['dni'];
                  $i++;
                }
                echo "<h4 class='border-top border-danger pt-2 small font-weight-bold'>Priemer
                        <span class='float-right'>".sprintf("%.2f d/oprava", $sum/$i, $text)."</span>
                      </h4>";
              ?>
          </div>
      </div>
    </div>

    <div class="col-xl-6 col-lg-6">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Spôsob ukončenia podľa technika</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
              <div class="row col-sm-12 col-md-12">
                <div class="col-sm-12">
                  <?php
                  echo "
                  <table class='table table-bordered dataTable' id='dataTable' width='100%' cellspacing='0' role='grid' aria-describedby='dataTable_info' style='width: 100%;'>
                    <thead>
                    <tr role='row'>
                      <th style='width: 100px;'>Meno</th>
                      ";
                      foreach ($aUkoncenia as $key) {
                        echo "<th style='width: 40px;' class='rotate'><div><span>$key</span></div></th>";
                      }
                      echo "
                        <th style='width: 40px;' class='rotate'><div><span>Spolu</span></div></th>
                      </tr>
                    </thead>
                    <tbody>";
                    foreach ($employees as $id_admin => $row) {
                      echo "<tr class='data' role='row'>
                      <td>".$row['fullname']."</td>";
                      ksort($row['ukoncenie']);
                      $sum = 0;
                      foreach ($row['ukoncenie'] as $key => $value) {
                        echo "<td>".$value."</td>";
                        $sum += $value;
                      }
                      echo "<td>".$sum."</td>";
                      echo "</tr>";
                    }
                    echo "</tbody>
                    </table>";
                    ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-6 col-lg-6">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Najčastejšie reklamované</h6>
        </div>
        <div class="card-body">
          <?php
          $i = 0;
            foreach ($vModely as $model => $pocet) {
              if($i<10){
                $i++;
              } else {
                break;
              }
              echo "<h4 class='small font-weight-bold'>".$model."
                      <span class='float-right'>".sprintf("%d", $pocet)."</span>
                    </h4>
                    <div class='progress mb-4'>
                      <div class='progress-bar bg-danger' role='progressbar' style='width: calc(".$pocet." / $maxVModely * 100%)' aria-valuenow='".$pocet."' aria-valuemin='0' aria-valuemax='$maxVModely'></div>
                    </div>";
            }
          ?>
        </div>
      </div>
    </div>

    <div class="col-xl-6 col-lg-6">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Najčastejšie reklamuje</h6>
        </div>
        <div class="card-body">
          <?php
          $i = 0;
            foreach ($vReklamujuci as $firma => $pocet) {
              if($i<10){
                $i++;
              } else {
                break;
              }
              echo "<h4 class='small font-weight-bold'>".$firma."
                      <span class='float-right'>".sprintf("%d", $pocet)."</span>
                    </h4>
                    <div class='progress mb-4'>
                      <div class='progress-bar bg-danger' role='progressbar' style='width: calc(".$pocet." / $maxVReklamujuci * 100%)' aria-valuenow='".$pocet."' aria-valuemin='0' aria-valuemax='$maxVReklamujuci'></div>
                    </div>";
            }
          ?>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-6 col-lg-6">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Spôsob prijatia</h6>
        </div>
        <div class="card-body">
          <?php
          $i = 0;
            foreach ($vPrijatia as $sposob => $pocet) {
              if($i<10){
                $i++;
              } else {
                break;
              }
              echo "<h4 class='small font-weight-bold'>".$sposob."
                      <span class='float-right'>".sprintf("%d", $pocet)."</span>
                    </h4>
                    <div class='progress mb-4'>
                      <div class='progress-bar bg-danger' role='progressbar' style='width: calc(".$pocet." / $maxVPrijatia * 100%)' aria-valuenow='".$pocet."' aria-valuemin='0' aria-valuemax='$maxVPrijatia'></div>
                    </div>";
            }
          ?>
        </div>
      </div>
    </div>

    <div class="col-xl-6 col-lg-6">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Spôsoby ukončenia</h6>
        </div>
        <div class="card-body">
          <?php
          $i = 0;
            foreach ($vUkoncenia as $sposob => $pocet) {
              if($i<10){
                $i++;
              } else {
                break;
              }
              echo "<h4 class='small font-weight-bold'>".$sposob."
                      <span class='float-right'>".sprintf("%d", $pocet)."</span>
                    </h4>
                    <div class='progress mb-4'>
                      <div class='progress-bar bg-danger' role='progressbar' style='width: calc(".$pocet." / $maxVUkoncenia * 100%)' aria-valuenow='".$pocet."' aria-valuemin='0' aria-valuemax='$maxVUkoncenia'></div>
                    </div>";
            }
          ?>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-6 col-lg-6">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Najčastejšie reklamované (Značky)</h6>
        </div>
        <div class="card-body">
          <?php
          $i = 0;
            foreach ($vZnacky as $znacka => $pocet) {
              if($i<10){
                $i++;
              } else {
                break;
              }
              echo "<h4 class='small font-weight-bold'>".$znacka."
                      <span class='float-right'>".sprintf("%d", $pocet)."</span>
                    </h4>
                    <div class='progress mb-4'>
                      <div class='progress-bar bg-danger' role='progressbar' style='width: calc(".$pocet." / $maxVZnacky * 100%)' aria-valuenow='".$pocet."' aria-valuemin='0' aria-valuemax='$maxVZnacky'></div>
                    </div>";
            }
          ?>
        </div>
      </div>
    </div>

    <div class="col-xl-6 col-lg-6">
      <div class="card shadow mb-4">
          <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Priemerný čas v servise</h6>
          </div>
          <div class="card-body">
              <?php
                  echo "<h4 class='small font-weight-bold'>".chr(127)."
                          <span class='float-right'>".sprintf("%d d", $vServise)."</span>
                        </h4>
                        <div class='progress mb-4'>
                          <div class='progress-bar bg-danger' role='progressbar' style='width: calc(".$vServise." / 30 * 100%)' aria-valuenow='".$vServise."' aria-valuemin='0' aria-valuemax='30'></div>
                        </div>";
              ?>
          </div>
      </div>
    </div>
  </div>
</div>
<script src="/adminlogin/theme/vendor/chart.js/Chart.min.js"></script>

<!-- Page level custom scripts -->
<script src="/adminlogin/theme/js/demo/chart-area-demo.js"></script>
<script src="/adminlogin/theme/js/demo/chart-pie-demo.js"></script>
