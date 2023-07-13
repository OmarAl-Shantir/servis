<?php include "theme/page.php";

$adminV = new AdminView();
$per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 12);
if($per == 0 ){
  header("Location: ".HOMEPAGE);
}
$adminC = new AdminController();
if($per == 3 || $_SESSION['admin_logged']==$_GET['id']){
  $data = $adminV->adminDatabyId($_GET['id']);
} else {
  die('
    <div class="card shadow mb-4 alert-danger">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">Nemáte dostatočné oprávnenie.</h6>
      </div>
    </div>
  ');
}


if($per == 3 || $_SESSION['admin_logged']==$_GET['id']){
  if(isset($_POST['save'])){
    $adminC->update_admin_data($_GET['id'],$_POST['fullname'], $_POST['email'], $_POST['active_input']);
    header("Refresh:0");
    die();
  }

  if(isset($_POST['password_change'])){
    if($_POST['new_pass'] == $_POST['new_pass_2']){
      $res = $adminC->change_password($_GET['id'], $_POST['old_pass'], $_POST['new_pass']);
      if ($res == "Zlé pôvodné heslo"){
        echo '
        <div class="card shadow mb-4 alert-danger">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Pôvodné heslo sa nezhoduje.</h6>
          </div>
        </div>';
      }
      if ($res == "Heslo bolo zmenené"){
        echo '
        <div class="card shadow mb-4 alert-success">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Heslo bolo zmenené.</h6>
          </div>
        </div>';
      }
    }
  }
}

if($per > 0 ){
?>
<script src="/adminlogin/theme/js/scripts.js"></script>
<link rel="stylesheet" href="/adminlogin/theme/css/admin.css">
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Profil</h6>
  </div>
  <div class="card-body">
    <form method="post">
      <div class="form-group row" id="meno">
        <label for="fullname" class="col-sm-2 col-form-label">Meno:</label>
        <div class="col-sm-10">
          <input type="text" name="fullname" class="form-control bg-light border-1" id="fullname_input" value="<?php echo (isset($_POST['fullname']))?$_POST['fullname']:$data['fullname']?>">
        </div>
      </div>
      <div class="form-group row" id="email">
        <label for="email" class="col-sm-2 col-form-label">E-mail:</label>
        <div class="col-sm-10">
          <input type="text" name="email" class="form-control bg-light border-1" id="email_input" value="<?php echo (isset($_POST['email']))?$_POST['email']:$data['email']?>">
        </div>
      </div>
      <?php if ($_GET['id'] != $_SESSION['admin_logged']){?>
        <div class="pb-4 form-check col-lg-4">
          <input type="checkbox" class="btn-check" name="active_input" id="active_input" value="1" autocomplete="off" onChange="activeChange(this)" <?php echo ($data['active'] == 1)?"checked":""?>>
          <label class="btn <?php echo ($data['active'] == 1)?"btn-success":"btn-danger"?>" for="active_input" id="active_label"><?php echo ($data['active'] == 1)?"Aktívny":"Neaktívny";?></label>
        </div>
      <?php }?>
      <div class="row justify-content-between">
        <button type="submit" class="btn btn-success btn-icon-split col-sm-3" name="save" id="save">
          <span class="text">Uložiť</span>
        </button>
        <button type="button" class="btn btn-danger btn-icon-split col-sm-3" name="reset" id="reset" data-toggle='modal' data-target='.bd-example-modal-lg-password'>
          <span class="text">Zmeniť heslo</span>
        </button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade bd-example-modal-lg-password" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Upozornenie</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="#">
        <div class="modal-body">
          <?php if ($_GET['id'] == $_SESSION['admin_logged']){?>
            <div class="form-group row">
              <label for="fullname" class="col-sm-2 col-form-label">Pôvodné heslo:</label>
              <div class="col-sm-10">
                <input type="password" name="old_pass" class="form-control bg-light border-1">
              </div>
            </div>
          <?php }?>
          <div class="form-group row">
            <label for="fullname" class="col-sm-2 col-form-label">Nové heslo:</label>
            <div class="col-sm-10">
              <input type="password" name="new_pass" id="new_password" class="form-control bg-light border-1" onkeyup="checkPassword()">
            </div>
          </div>
          <div class="form-group row">
            <label for="fullname" class="col-sm-2 col-form-label">Nové heslo, pre kontrolu:</label>
            <div class="col-sm-10">
              <input type="password" name="new_pass_2" id="new_password2" class="form-control bg-light border-1" onkeyup="checkPassword()">
            </div>
          </div>
          <div class="form-group row">
            <span id="message"></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zrušiť</button>
          <button class="btn btn-primary" name='password_change' type='submit' value='16'>Uložiť</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php
}
  include 'theme/footer.php';
 ?>
