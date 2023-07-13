<?php
  session_start();
  if ($_SESSION['admin_logged']==0){
    header("location: login.php");
  }
  require __DIR__ . '/../../init.php';
  require __DIR__ . '/translate/en.php';
  include ABSPATH."adminlogin/theme/page.php";

  switch ($_GET['t']) {
    case 1:
      if(isset($_GET['p'])) include 'zarucnyPrehlad.php';
      else include 'zarucny.php';
      break;
    case 2:
      if(isset($_GET['p'])) include 'predpredajnyPrehlad.php';
      break;
    case 4:
      if(isset($_GET['p'])) include 'pozarucnyPrehlad.php';
      else include 'pozarucny.php';
      break;
    case 0:
      include 'allPrehlad.php';
      break;
    default:
      break;
  }

  include ABSPATH."adminlogin/theme/footer.php";
?>
