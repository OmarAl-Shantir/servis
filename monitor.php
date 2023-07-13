<?php include "theme/page.php";

  $datum = date("d.m.Y");
  $den = date("D");
  $cas = date("G:i");
  switch ($den){
    case "Mon":
      $den = "Po";
      break;
    case "Tue":
      $den = "Ut";
      break;
    case "Wed":
      $den = "St";
      break;
    case "Thu":
      $den = "Št";
      break;
    case "Fri":
      $den = "Pi";
      break;
    case "Sat":
      $den = "So";
      break;
    case "Sun":
      $den = "Ne";
      break;
  }
?>
<meta http-equiv="refresh" content="60">
<link rel="stylesheet" href="theme/grayscale/css/monitor.css">
<div class="col-lg-12">
  <div class="col-lg-12" style="display: table; padding: 0.2em 0; border-bottom: solid 1px #333;">
    <div id="datum">
    <?php
      echo "$datum | $den";
    ?>
  </div>
    <div id="logo"><img style="margin: 0.5em auto 0 auto; display: block; height: 3em;" src="theme/grayscale/img/logo.jpg"></div>
    <div id="cas">
    <?php
      echo $cas;
    ?>
  </div>
  </div>
  <table class="col-lg-12">
    <?php
      $m = new MonitorView();
      $items = $m->getServiceItems();
      foreach ($items as $key => $item) {
        echo "<tr style='border-bottom: dotted 1px #888;'>";
        switch ($item['typ']) {
          case 'Záručná':
              $t = "Z";
            break;
          case 'Predpredajná':
            $t = "P";
            break;
          case 'Pozáručná':
            $t = "E";
            break;
          default:
             $t = "N";
            break;
        }
        if ($item['v_servise'] >= 23){
          echo "<td class='col-lg-1 text-center align-middle bg-danger'>$item[v_servise]</td>";
        } elseif (($item['v_servise'] >= 20) and ($item['v_servise'] <= 22)){
          echo "<td class='col-lg-1 text-center align-middle bg-warning'>$item[v_servise]</td>";
        } else {
          echo "<td class='col-lg-1 text-center align-middle bg-success'>$item[v_servise]</td>";
        }
        echo "
            <td class='col-lg-1 text-center align-middle bold'>$t</td>
            <td class='col-lg-3 text-center align-middle bold'>$item[servisny_list]</td>
            <td class='col-lg-3 text-center align-middle bold'>$item[product_ref]</td>
            <td class='col-lg-4 text-center align-middle bg-gray'>$item[stav]</td>
          </tr>";
        }
    ?>
  </table>
</div>


<!--<script type="text/javascript">

    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();

    today = mm + '/' + dd + '/' + yyyy;
    var days = ['Ne', 'Po', 'Ut', 'St', 'Št', 'Pi', 'So'];
    var d = new Date(today);
    var dayName = days[d.getDay()];

    today = dd + '.' + mm + '.' + yyyy + ' | ' + dayName;
    document.getElementById('datum').innerHTML = today;
    document.getElementById('cas').innerHTML = time;
    setTimeout("location.reload(true);", 1000);
</script>-->
<?php include "theme/footer.php";?>
