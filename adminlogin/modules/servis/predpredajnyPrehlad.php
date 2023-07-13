<?php
  session_start();
  if ($_SESSION['admin_logged']==0){
    header("location: login.php");
  }
?>
<script type="text/javascript" src="js/service.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js" integrity="sha512-n/4gHW3atM3QqRcbCn6ewmpxcLAHGaDjpEBu4xZd47N0W2oQ+6q7oc3PXstrJYXcbNU1OHdQ1T7pAP+gi5Yu8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js" integrity="sha512-qzgd5cYSZcosqpzpn7zF2ZId8f/8CHmFKZ8j7mU4OUXTNRd5g+ZHBPsgKEwoqxCtdQvExE5LprwwPAgoicguNg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js" integrity="sha512-dj/9K5GRIEZu+Igm9tC16XPOTz0RdPk9FGxfZxShWf65JJNU2TjbElGjuOo3EhwAJRPhJxwEJ5b+/Ouo+VqZdQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" src="js/jquery.tablesorter.pager.js"></script>
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/css/jquery.tablesorter.pager.min.css" integrity="sha512-TWYBryfpFn3IugX13ZCIYHNK3/2sZk3dyXMKp3chZL+0wRuwFr1hDqZR9Qd5SONzn+Lja10hercP2Xjuzz5O3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
-->
<?php
  $serviceV = new ServiceView();
  $data = $serviceV->getPredpredajnyRecords();
?>
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">ETA záručný servis</h6>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div id="dataTable_filter" class="dataTables_filter">
              <label>Search:
                <input type="search" class="form-control form-control-sm" placeholder="" aria-controls="dataTable">
              </label>
            </div>
          </div>
          <div class="row col-sm-12 col-md-12">
            <div class="col-sm-12">
              <table class="table table-bordered dataTable tablesorter" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
                <thead>
                  <tr role="row">
                    <th class="sorting_asc" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 40px;">ID</th>
                    <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 100px;">Servisný list</th>
                    <th class="sorting" tabindex="0" aria-controls="dataTable"  colspan="1" aria-label="Office: activate to sort column ascending" style="width: 193px;">Ref.číslo</th>
                    <th class="sorting" tabindex="0" aria-controls="dataTable"  colspan="1" aria-label="Office: activate to sort column ascending" style="width: 300px;">Obchod</th>
                    <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 97px;">Dátum vzniku</th>
                    <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 97px;">Dátum prijatia</th>
                    <th style="width: 96px;"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    foreach ($data as $row) {
                      $vznik = date("d.m.Y", strtotime($row['datum_vzniku']));
                      $prijatie = date("d.m.Y", strtotime($row['datum_prijatia']));
                      echo '
                      <tr role="row" class="odd">
                        <td class="sorting_1">'.$row['id_service_item'].'</td>
                        <td>'.$row['servisny_list'].'</td>
                        <td>'.$row['product_ref'].'</td>
                        <td>'.$row['firma'].'</td>
                        <td>'.$vznik.'</td>
                        <td>'.$prijatie."</td>
                        <td><a href='detail.php?s=$row[id_service_item]'>detail</a></td>
                      </tr>
                      ";
                    }
                  ?>

                </tbody>
              </table>
            </div>
          </div>
          <div class="row pager">
            <div class="col-sm-12 col-md-5">
              <div class="dataTables_info" id="dataTable_info" role="status" aria-live="polite">
                <span class="pagedisplay"></span>
              </div>
            </div>
            <div class="col-sm-12 col-md-7">
              <div class="dataTables_paginate paging_simple_numbers pager" id="dataTable_paginate">
                <ul class="pagination">
                  <li class="paginate_button page-item prev">
                    <span class="prev page-link">Predchádzajúce</span>
                  </li>
                  <ul class="pagination pager gotoPage">
                  </ul>
                  <li class="paginate_button page-item next" id="dataTable_next">
                    <span class="next page-link">Ďalšie</span>
                  </li>
                </ul>
                <!--<ul class="pagination">

                  <li class="paginate_button page-item" active>
                    <a href="#" aria-controls="dataTable" data-dt-idx="1" tabindex="0" class="page-link">1</a>
                  </li>
                  <li class="paginate_button page-item">
                    <a href="#" aria-controls="dataTable" data-dt-idx="2" tabindex="0" class="page-link">2</a>
                  </li>
                  <li class="paginate_button page-item">
                    <a href="#" aria-controls="dataTable" class="page-link">3</a>
                  </li>
                  <li class="paginate_button page-item ">
                    <a href="#" aria-controls="dataTable" data-dt-idx="4" tabindex="0" class="page-link">4</a>
                  </li>
                  <li class="paginate_button page-item ">
                    <a href="#" aria-controls="dataTable" data-dt-idx="5" tabindex="0" class="page-link">5</a>
                  </li>
                  <li class="paginate_button page-item ">
                    <a href="#" aria-controls="dataTable" data-dt-idx="6" tabindex="0" class="page-link">6</a>
                  </li>
                  <li class="paginate_button page-item next" id="dataTable_next">
                    <span class="next page-link">Ďalšie</span>
                  </li>
                </ul>-->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="js/pager.js"></script>
