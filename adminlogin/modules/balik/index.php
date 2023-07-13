<?php
  session_start();
  if ($_SESSION['admin_logged']==0){
    header("location: login.php");
  }
  require __DIR__ . '/../../init.php';
  require __DIR__ . '/translate/en.php';
  include ABSPATH."adminlogin/theme/page.php";

  switch ($_GET['t']) {
    case 'E':
      include 'etacz.php';
      break;
    case 'B':
      include 'bleskova.php';
      break;
    case 'A':
      include 'add_balik.php';
      break;
    default:
      break;
  }

  include ABSPATH."adminlogin/theme/footer.php";
?>
