<?php include "theme/page.php";

$adminV = new AdminView();
$permissions = $adminV->getPermissionsTypes();
$adminC = new AdminController();
$admins = $adminV->getAdmins();

$per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 1);
if($per == 0 ){
  header("Location: ".HOMEPAGE);
}
if($per == 3 ){
  if(isset($_POST['save'])){
    foreach ($admins as $a_id => $name) {
      foreach ($permissions as $p_id => $perm) {
        $values[$a_id][$p_id] = $_POST["$a_id-$p_id"];
      }
    }
    $adminC->savePermissions($values);
    header("Refresh:0");
  }
}
?>
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Oprávnenia</h6>
  </div>
  <div class="card-body">
    <form method="post">
      <div class="table-responsive">
        <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
          <div class="col-sm-12">
            <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
              <thead>
                <tr role="row">
                  <th class="sorting_asc" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 200px;">Meno</th>
                  <?php
                    foreach ($permissions as $id => $name) {
                      echo "<th class='rotated-text' tabindex='0' aria-controls='dataTable' rowspan='1' colspan='1' aria-label='$name' style='width: 10px;'><div><span>$name</span></div></th>";
                    }
                  ?>
                </tr>
              </thead>
              <tbody>
                <?php
                  foreach ($admins as $a_id => $data) {
                    echo "<tr role='row' class='text-center'>
                      <td>".$data['fullname']."</td>";
                    foreach ($permissions as $p_id => $name) {
                      $admin_permission = $adminV->getPermissionByAdmin($a_id, $p_id);
                      $class = $admin_permission['class'];
                      $text = $admin_permission['text'];
                      $value = $admin_permission['value'];
                      echo "
                        <td><label class='btn $class' for='$a_id-$p_id' onmousedown='changePermission(this)'><input hidden id='$a_id-$p_id' name='$a_id-$p_id' type='text' value='$value'>$text</label></td>
                        ";
                    }
                    echo '</tr>';
                  }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <?php
          if($per == 3 ){
          ?>
          <div class="row">
            <div class="col-md-2">
              <button type="submit" name="save" class="btn btn-success w-100">Uložiť</button>
            </div>
          </div>
          <?php } ?>
        </form>
  </div>
  <?php if($per == 3 ){?>
    <script src="theme/js/scripts.js"></script>
  <?php }?>
  <?php include 'theme/footer.php';?>
