<?php
  session_start();
  if ($_SESSION['admin_logged']==0){
    header("location: ../login.php");
  }
  require __DIR__ . '/../../init.php';
  require __DIR__ . '/translate/en.php';
  //include_once 'Service.php';
  include ABSPATH."adminlogin/theme/page.php";
  $adminV = new AdminView();
  $per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 3); //balik
  if($per < 1){
    die();
  }

  $mailV = new MailView();
  $data = $mailV->get_data($_GET['s'])[0];
  if($per == 3){
    $mailC = new MailController();
    if (isset($_POST['save'])){
      $mailC->update_template($_GET['s'], $_POST['subject'], $_POST['filename']);
      header("Refresh:0");
      die();
    }
  }

  $mailContent = file_get_contents('templates/'.$data['filename'].".html");
  $mailContent = $mailV->generate_mail($mailContent);

  ?>
<link rel="stylesheet" href="css/mails.css">
<link rel="stylesheet" href="../../theme/vendor/lightbox/css/lightbox.min.css">

<?php
  if ($editable){
    echo '<script src="js/mails.js"></script>';
  }
?>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Prehľad</h6>
      </div>
      <div class="card-body">
        <form method="POST">
          <div class="form-group row">
            <div class="form-group row">
              <div class="col col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <label class="px-1" for="datum_podania">Predmet: </label>
                <input type="text" name="subject" class="form-control bg-light border-1" value="<?php echo $data['subject'];?>">
              </div>
            </div>
            <div class="form-group row">
              <div class="col col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <label class="px-1" for="datum_podania">Názov súboru: </label>
                <input type="text" name="filename" class="form-control bg-light border-1" value="<?php echo $data['filename'];?>">
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 col-md-3 col-lg-2 col-xl-2">
                <button type="submit" name="save" class="btn btn-success">Uložiť</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Náhľad</h6>
      </div>
      <div class="card-body table-responsive">
        <?php
          echo $mailContent;
        ?>
      </div>
    </div>
  </div>
</div>
<?php
  include ABSPATH."adminlogin/theme/footer.php";
?>
