<?php
  session_start();
  if ($_SESSION['admin_logged']==0){
    header("location: login.php");
  }
  require __DIR__ . '/../../init.php';
  require __DIR__ . '/translate/en.php';
  include ABSPATH."adminlogin/theme/page.php";

  switch ($_GET['t']) {
    case 'L':
      include 'list.php';
      break;
    case 'A':
      include 'add_mail.php';
      break;
    default:
      break;
  }

  include ABSPATH."adminlogin/theme/footer.php";
?>
