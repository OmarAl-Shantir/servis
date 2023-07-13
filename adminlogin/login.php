<?php
  session_start();

  if ($_GET['logout'] == 1) session_destroy();
  if ($_SESSION['admin_logged']>0) header("location: index.php");
  require_once __DIR__ . '/init.php';

  if(isset($_POST["login"])){
    $admin = new AdminView();
    $login = $admin->logIn($_POST['email'],$_POST['pass']);
    if ($login > 0){
      $adminC = new AdminController();
      $_SESSION['admin_logged'] = $login;
      $_SESSION['admin_name'] = $admin->adminDatabyId($login)['fullname'];
      $_SESSION['admin_role'] = $admin->adminDatabyId($login)['role'];
      header("location: index.php");
    }
  }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo WEBPAGE_SHORT;?> | Login </title>
    <script>
    document.addEventListener('keyup', (e) => {
      if (e.getModifierState('CapsLock')) {
        document.getElementById('caps_check').style.visibility = 'visible';
      } else {
        document.getElementById('caps_check').style.visibility = 'hidden';
      }
     });
    </script>
    <!-- Custom fonts for this template-->
    <link href="theme/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="theme/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="theme/css/admin.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">
      <div class="nav-item dropdown no-arrow show">
      </div>
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-7 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4"><?php echo WEBPAGE." (".WEBPAGE_SHORT.")";?></h1>
                                    </div>
                                    <form class="user" method="post">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"
                                                id="inputEmail" aria-describedby="email"
                                                placeholder="e-mail" name="email">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                id="inputPassword" placeholder="heslo" name="pass">
                                            <div id="caps_check" class="form-control btn-arrow-top" style="visibility: hidden;">
                                              <span >CapsLock je zapnutý</span>
                                            </div>
                                        </div>
                                        <input class="btn btn-primary btn-user btn-block" type="submit" value="Prihlásiť" name="login">
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-pass.php">Zabudol som heslo</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="theme/vendor/jquery/jquery.min.js"></script>
    <script src="theme/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="theme/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="theme/js/sb-admin-2.min.js"></script>

</body>

</html>
