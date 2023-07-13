<?php
  session_start();
  if ($_SESSION['admin_logged']==0){
    header("location: login.php");
  }
  require __DIR__ . '/../../init.php';
  require __DIR__ . '/translate/en.php';
  include ABSPATH."adminlogin/theme/page.php";

  include 'prehlad.php';

  include ABSPATH."adminlogin/theme/footer.php";
?>
