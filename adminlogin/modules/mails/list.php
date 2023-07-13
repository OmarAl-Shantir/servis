<?php
  session_start();
  if ($_SESSION['admin_logged']==0){
    header("location: login.php");
  }

  if (isset($_GET['a']) && $_SESSION['admin_role'] != 2){
    $mailC = new MailController();
    //$mailC->addoptItem($_GET['a']);
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $returnUrl = substr($url,0,strpos($url,"&a"));
    header("Location: $returnUrl");
  }
?>
<script type="text/javascript" src="js/mails.js"></script>
<link rel="stylesheet" href="css/mails.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js" integrity="sha512-n/4gHW3atM3QqRcbCn6ewmpxcLAHGaDjpEBu4xZd47N0W2oQ+6q7oc3PXstrJYXcbNU1OHdQ1T7pAP+gi5Yu8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js" integrity="sha512-qzgd5cYSZcosqpzpn7zF2ZId8f/8CHmFKZ8j7mU4OUXTNRd5g+ZHBPsgKEwoqxCtdQvExE5LprwwPAgoicguNg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js" integrity="sha512-dj/9K5GRIEZu+Igm9tC16XPOTz0RdPk9FGxfZxShWf65JJNU2TjbElGjuOo3EhwAJRPhJxwEJ5b+/Ouo+VqZdQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/css/jquery.tablesorter.pager.min.css" integrity="sha512-TWYBryfpFn3IugX13ZCIYHNK3/2sZk3dyXMKp3chZL+0wRuwFr1hDqZR9Qd5SONzn+Lja10hercP2Xjuzz5O3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
-->
<?php
  $mailV = new MailView();
  $data = $mailV->get_all_records($_GET['limit']);
?>
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Zoznam šablón</h6>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
          <div class="row col-sm-12 col-md-12">
            <div class="col-sm-12">
              <table class="table table-bordered dataTable tablesorter" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
                <thead>
                  <tr role="row">
                    <th class="sorting_asc" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 40px;">ID</th>
                    <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 200px;">Predmet</th>
                    <th class="sorting" tabindex="0" aria-controls="dataTable"  colspan="1" aria-label="Office: activate to sort column ascending" style="width: 193px;">Názov</th>
                    <th style="width: 96px;"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    foreach ($data as $row) {
                      echo '
                      <tr role="row" >
                        <td class="id">'.$row['id_mail'].'</td>
                        <td class="s">'.$row['subject'].'</td>
                        <td class="r">'.$row['filename'].'</td>
                        <td class="text-center"><a href=/adminlogin/modules/mails/detail.php?s='.$row['id_mail'].' class="btn btn-primary">detaily</a></td>
                      </tr>
                      ';
                    }
                  ?>

                </tbody>
              </table>
            </div>
          </div>
        </div>
    </div>
  </div>
</div>
