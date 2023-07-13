<?php
  session_start();
  require_once __DIR__ . '/../init.php';
  require_once __DIR__ . '/translate/sk.php';

  if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged']==0){
    $redirect = "Location: " . HOMEPAGE . "login.php";
    header($redirect);
    die();
    //header("location: ".ABSPATH."adminlogin");
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Omar Al-Shantir">

    <title><?php echo WEBPAGE_SHORT;?> | Úvod</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Custom fonts for this template-->
    <link href="/adminlogin/theme/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="/adminlogin/theme/css/sb-admin-2.min.css" rel="stylesheet">
    <!--<link href="/adminlogin/theme/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
</head>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
      <?php include __DIR__."/sidebar.php";?>
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include __DIR__."/header.php"; ?>
                <div class="container-fluid">
