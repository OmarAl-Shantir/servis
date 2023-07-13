<?php
session_start();
require '../../init.php';
$serviceV = new ServiceView();
$data = $serviceV->hashIsActive($_GET['token']);
if ($data['employee']>0){
?>
<head>
  <head>

      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="description" content="">
      <meta name="author" content="Omar Al-Shantir">

      <title>ESP | Dashboard</title>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
      <!-- Custom fonts for this template-->
      <link href="../../theme/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
      <link
          href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
          rel="stylesheet">

      <!-- Custom styles for this template-->
      <link href="../../theme/css/sb-admin-2.min.css" rel="stylesheet">
      <!--<link href="/adminlogin/theme/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">-->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
      <link rel="stylesheet" href="css/servis.css">
      <link rel="stylesheet" href="../../theme/vendor/lightbox/css/lightbox.min.css">

      <script src="../../theme/vendor/lightbox/js/lightbox-plus-jquery.min.js"></script>
  </head>
</head>
<body>
  <div class="col-sm-6 col-md-6">
    <div class="card shadow mb-4">
      <div class="card-body">
        <form id="jqdata" enctype="multipart/form-data" method="post" class="add-new-item-form-form" name="jqdata">
          <ul class="nav nav-tabs">
            <?php
              $types = $serviceV->getImageTypes();
              $i = 0;
              foreach ($types as $value => $type) {
                $temp = ($i==0)?"active":"";
                echo "
                <li class='nav-item'>
                  <span class='nav-link $temp image-type-link' aria-current='page' onclick='chooseImageTab(event, ".'"tab-'.$value.'"'.")'>".$type."</span>
                  </li>
                  ";
                  $i++;
                }
                foreach ($types as $value => $type) {
                  $vis = ($value==1)?"style='display: block';":"style='display: none'";
                  echo "
                  <div id='tab-$value' $vis class='image-type-content'>
                  <div class='row' id='image_docs_$value'>";
                      $images = $serviceV->getServiceImages($data['id_service_item'],$value);
                      foreach ($images as $key => $image) {
                        echo "
                        <div class='col-sm-4 col-md-4'>
                          <a href='".$image."' data-lightbox='set'><img src='".$image."' class='col-md-12'></a>
                        </div>";
                        }
                        echo "
                        </div>
                        </div>
                        ";
                      }
                      ?>
          </ul>
          <div class="form-group row">
            <div class="form-check col-sm-5 col-md-5 col-lg-5">
              <label class="btn btn-outline-secondary col-sm-12 col-md-12">
                <input type="file" name="photo" accept="image/jpeg" capture="camera" id="photo" onchange="uploadImage('mobile')">
                <input type="hidden" name="image-type" id="image-type" value="1">
                <input type="hidden" name="id_record" id="id_record" value="<?php echo $data['id_service_item'];?>">
                Nahrať
              </label>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="js/service.js"></script>
</body>

<?php
} else {
  echo "Platnosť relácie vypršala";
}
 ?>
