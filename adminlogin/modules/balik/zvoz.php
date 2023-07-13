<?php

session_start();
if ($_SESSION['admin_logged']==0){
  header("location: ../login.php");
}
require __DIR__ . '/../../init.php';
require __DIR__ . '/translate/en.php';
//include_once 'Service.php';
  if (isset($_POST['balik'])){
    $id_balik = $_POST['balik'];
    $balikV = new BalikView();
    $inPackage = $balikV->in_package($id_balik);
    $balik = $balikV->get_balik_type($id_balik);
    $data = ($balik['typ']=="likvidácia")?$balikV->get_balik($id_balik):$balikV->get_balik_kat($id_balik);
    $data = $data[0];
    $recPpage = 15;
    $pages = ceil(count($inPackage)/$recPpage);
    $serviceV = new ServiceView();
    $s = 0;
    $cb = explode(" ",$data['cislo_balika']);
    $cb = (count($cb))==3?$cb[0]." <span style='font-size: 1.5em; font-weight: bold;'>".$cb[1]."</span> ".$cb[2]:$cb[0]." ".$cb[1]." ".$cb[2];
    $header = '
     <div class="header">
       <span>číslo balíka: </span><h2>'.$cb.'</h2>
      </div>
      <div class="row">
        <div class="col-6">
          <p>Odosielateľ:<br>
          <b>Electrobeta s.r.o. (servis)<br>
          Na vŕšku 3<br>
          949 01 Nitra<br>
          Slovenská republika</b></p>
        </div>

        <div class="col-6">
          <p>Príjemca:<br>
          <b>ETA a.s.<br>
          Prštné Kútiky 637<br>
          76001 Zlín<br>
          Česká republika</b></p>
        </div>
      </div>

      <div class="row">
        <div class="col-6">
          <p>Kontaktná osoba:<br>
          <b>Ing.Michal Rybánsky<br>
          Manažér predaja a servisného oddelenia</b><br>
          <small>e-mail: <b>michal.rybansky@electrobeta.sk</b></small></p>
        </div>

        <div class="col-6">
          <p>Kontaktná osoba:<br>
          <b>'.ETA_KONTAKT.'<br>
          '.ETA_POZICIA.'</b></p>
        </div>
      </div>
      ';

     $text = '
     <div class="body">
     <h3>Zoznam produktov v balíku</h3>
     ';
     if(is_null($data['datum_podania'])){
       $footer = '
       <div class="footer">
        <p>Dátum podania: <b>NEPODANÉ</b></p>
       </div>
       </div>
       ';
     } else {
       $footer = '
       <div class="footer">
        <p>Dátum podania: <b>'.date("d. m. Y",strtotime($data['datum_podania'])).'</b></p>
       </div>
       </div>
       ';
      }
   }
?>
<!DOCTYPE html>
<html lang="sk" dir="ltr">
  <head>
    <link rel="stylesheet" href="css/balik.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <button class="btn btn-primary" id="print_button" onclick="window.print()">Tlač</button>
    <?php
     for($page = 1; $page <= $pages; $page++){
    ?>
    <div class="content">
      <?php
        echo $header;
        echo $text;
      ?>
      <div class="inventory">
        <?php
        echo "<table class='osobna_karta'>
                <thead>
                  <tr>
                    <th class='text-center'>ID Electrobeta</th>
                    <th class='text-center'>Servisný list</th>
                    <th class='text-center'>Produkt</th>
                  </tr>
                </thead>
                <tbody>";
          for($r = $s; $r < count($inPackage);$r++){
            //$prod = $serviceV->getRecordDetails($inPackage[$r]);
            //var_dump($inPackage[$r]);
            echo "<tr><td>".sprintf("EBS%d",$inPackage[$r]['id_service_item'])."</td><td>".$inPackage[$r]['servisny_list']."</td><td>".$inPackage[$r]['product_ref']."</td></tr>";
            if ($r==$recPpage-1){
              $s += $r+1;
              break;
            }
          }
        echo "</tbody></table>";
        ?>
      </div>
      <?php
        echo $footer;
      ?>
      <div class="page">
        <span>Strana <?php echo "$page|$pages"?></span>
      </div>
    </div>
    <?php
      }
    ?>
  </body>
</html>
