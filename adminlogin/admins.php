<?php include "theme/page.php";

$adminV = new AdminView();
$admins_data = $adminV->getAdmins();

$per = $adminV->getAdminPermissionByIds($_SESSION['admin_logged'], 1);
if($per == 0 ){
  header("Location: ".HOMEPAGE);
}
?>
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Admins</h6>
  </div>
  <div class="card-body">
    <div class="table-responsive">
          <div class="col-sm-12">
            <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
              <thead>
                <tr role="row">
                  <th class="sorting_asc" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 261px;">ID</th>
                  <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 391px;">Email</th>
                  <th class="sorting" tabindex="0" aria-controls="dataTable"  colspan="1" aria-label="Office: activate to sort column ascending" style="width: 193px;">Fullname</th>
                  <th class="sorting" tabindex="0" aria-controls="dataTable"  colspan="1" aria-label="Office: activate to sort column ascending" style="width: 193px;">Role</th>
                  <?php if($per == 3 ){?>
                    <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 97px;"></th>
                  <?php }?>
                </tr>
              </thead>
              <tbody>
                <?php
                  foreach ($admins_data as $id => $data) {
                    $add_class = ($data['active'] == 0)?" btn-secondary text-dark":"";
                    echo '
                    <tr role="row" class="odd'.$add_class.'">
                      <td class="sorting_1">'.$id.'</td>
                      <td>'.$data['email'].'</td>
                      <td>'.$data['fullname'].'</td>
                      <td>'.$data['role'].'</td>';
                      if($per == 3 ){
                        echo '<td><a class="btn btn-primary" href="profil.php?id='.$id.'">Upraviť</a></td>';
                      }
                      echo '</tr>'
                      ;
                    }
                    ?>
              </tbody>
            </table>
          </div>
        </div>
    </div>
  </div>

  <?php include 'theme/footer.php';?>
