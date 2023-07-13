<?php
  session_start();
  if ($_SESSION['admin_logged']==0){
    header("location: login.php");
  }
  require __DIR__ . '/../../init.php';
  require __DIR__ . '/translate/en.php';
  include ABSPATH."adminlogin/theme/page.php";

  switch ($_GET['t']) {
    case 'O':
      include 'prehlad_predajca.php';
      break;
    case 'Z':
      include 'prehlad_zakaznik.php';
      break;
    default:
      break;
  }

  include ABSPATH."adminlogin/theme/footer.php";
?>
