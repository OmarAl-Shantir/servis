<?php
  session_start();

  if ($_GET['logout'] == 1) session_destroy();
  if ($_SESSION['admin_logged']>0) header("location: index.php");
  require_once __DIR__ . '/init.php';
  $adminV = new AdminView();
  $adminC = new AdminController();

  if (isset($_GET['u']) && isset($_GET['hash'])){
    if ($adminV->is_hash_active($_GET['u'], $_GET['hash'])){
      $recovery = 1;
    }
  }
  if(isset($_POST['reset'])){
    if ($_POST['email'] != ""){
      $mailV = new MailView();
      $admin_data = $adminV->adminDataByMail($_POST['email']);
      if ($admin_data['active'] == 1){
        $hash = $adminC->new_hash($admin_data['id_admin']);
        $user_data['_USERNAME_'] = $admin_data['fullname'];
        $user_data['_RESET_LINK_'] = HOMEPAGE."forgot-pass.php?u=".$admin_data['id_admin']."&hash=$hash";
        $content = $mailV->generate_mail(1, $user_data);
        $subject = $mailV->get_data(1)[0]['subject'];
        $mailV->send_mail(array("email" => $_POST['email'], "fullname" => $admin_data['fullname']), array("subject" => $subject, "content" => $content));
        $reset = 1;
      }
    }
  }

  if (isset($_POST['change']) && $adminV->is_hash_active($_GET['u'], $_GET['hash'])){
    if($_POST['password'] == $_POST['password_re']){
      $res = $adminC->change_password($_GET['u'], "", $_POST['password'],1);
    } else {
      $res = "Heslá sa nezhodujú";
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

    <!-- Custom fonts for this template-->
    <link href="theme/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="theme/css/sb-admin-2.min.css" rel="stylesheet">

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
                                        <?php
                                          if ($reset == 1){
                                            echo '<h1 class="h4 text-gray-900 mb-4">Na Vašu e-mailovú adresu bol odoslaný link na obnovu hesla.</h1>';
                                          } else {
                                            echo '<h1 class="h4 text-gray-900 mb-4"><?php echo WEBPAGE." (".WEBPAGE_SHORT.")";?></h1>';
                                          }
                                          if ($res == "Zlé pôvodné heslo"){
                                            echo '<h1 class="h4 text-gray-900 mb-4">Pôvodné heslo sa nezhoduje.</h1>';
                                          }
                                          if ($res == "Heslá sa nezhodujú"){
                                            echo '<h1 class="h4 text-gray-900 mb-4">Vami zadané heslá sa nezhodujú.</h1>';
                                          }
                                          if ($res == "Heslo bolo zmenené"){
                                            echo '<h1 class="h4 text-gray-900 mb-4">Heslo bolo zmenené.</h1>';
                                            header("refresh:3;url=index.php");
                                          } ?>
                                    </div>
                                    <?php
                                    if (!$reset == 1){
                                        if ($recovery == 1 && $res != "Heslo bolo zmenené"){?>
                                        <form class="user" method="post">
                                            <div class="form-group">
                                                <input type="password" class="form-group form-control form-control-user" aria-describedby="Nové heslo"
                                                placeholder="Nové heslo" name="password">
                                                <input type="password" class="form-group form-control form-control-user" aria-describedby="Nové heslo, pre kontrolu:"
                                                placeholder="Nové heslo, pre kontrolu:"name="password_re">
                                            </div>
                                            <input class="btn btn-primary btn-user btn-block" type="submit" value="Zmeniť heslo" name="change">
                                        </form>
                                      <?php } elseif (empty($recovery) && empty($res)){?>
                                        <form class="user" method="post">
                                          <div class="form-group">
                                              <input type="email" class="form-control form-control-user"
                                                  id="inputEmail" aria-describedby="email"
                                                  placeholder="e-mail" name="email">
                                          </div>
                                          <input class="btn btn-primary btn-user btn-block" type="submit" value="Zmeniť heslo" name="reset">
                                      </form>
                                    <?php }
                                    }?>
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
