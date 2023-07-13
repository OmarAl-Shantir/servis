<?php
  session_start();
  /*if ($_SESSION['user_logged']==0){
    header("location: login.php");
  }*/
  require __DIR__ . '/../init.php';
  require __DIR__ . '/translate/en.php';
?>
<!DOCTYPE html>
<html lang="sk" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Font Awesome icons (free version)-->
    <script src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" crossorigin="anonymous"></script>
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="theme/grayscale/css/styles.css" rel="stylesheet" />
    <title></title>
  </head>
  <body>
