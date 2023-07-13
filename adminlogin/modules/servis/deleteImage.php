<?php
//header('Content-Type: application/json');
session_start();

require '../../init.php';

$target_dir = "image_docs/";

if (($_SESSION['admin_role']==1) || ($_SESSION['admin_role']==2)){
  $res = deletePhotos($_POST['id_image']);
  echo json_encode("Súbor bol vymazaný. $res");
} else {
  echo json_encode("Nedostatočné oprávnenie.");
}
function deletePhotos ($id_image){
  $serviceC = new ServiceController();
  $res = $serviceC->deleteServiceImage($id_image);
  unlink($res);
  return $res;
}
?>
