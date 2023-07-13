<?php
  include "theme/page.php";
  if ($_SESSION['admin_role'] == 1){
    header("Location: ".HOMEPAGE."modules/statistika");
  } else {
    header("Location: ".HOMEPAGE."modules/servis/?t=A&p");
  }
  include 'theme/footer.php';
 ?>
