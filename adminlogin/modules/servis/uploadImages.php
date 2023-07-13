<?php
//header('Content-Type: application/json');


require '../../init.php';

$target_dir = "image_docs/";

$imageFileType = strtolower(pathinfo($target_dir . basename($_FILES["photo"]["name"]),PATHINFO_EXTENSION));
$name = bin2hex(random_bytes(20)) . "." . $imageFileType;
$target_file = $target_dir . $name;

while (file_exists($target_file)) {
  $name = bin2hex(random_bytes(20)) . "." . $imageFileType;
  $target_file = $target_dir . $name;
}
$width = 1920;
$height = 1920;

list($width_orig, $height_orig) = getimagesize($_FILES["photo"]["tmp_name"]);

$ratio_orig = $width_orig/$height_orig;

if ($width/$height > $ratio_orig) {
   $width = $height*$ratio_orig;
} else {
   $height = $width/$ratio_orig;
}

$image_p = imagecreatetruecolor($width, $height);
$image = imagecreatefromjpeg($_FILES["photo"]["tmp_name"]);
imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

// Output
imagejpeg($image_p, $target_dir . $name);
//imagejpeg($image_p, null, 100);

//move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);

$id = uploadPhotos($_POST['id_record'], $target_file, $_POST['image_type']);
echo json_encode(array('id' => $id, "file" => $target_file));

function uploadPhotos ($id_record, $data, $type){
  $serviceC = new ServiceController();
  $res = $serviceC->uploadServiceImage($id_record, $data, $type);
  return $res;
}
?>
