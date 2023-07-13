<?php
session_start();

if ($_SESSION['admin_role']!=1){
  $redirect = "Location: " . HOMEPAGE . "login.php";
  header($redirect);
  die();
}

require __DIR__ . '/../adminlogin/init.php';
$serviceV = new ServiceView();
$image_types = $serviceV->getImageTypes();
$imgs = array();
foreach ($image_types as $id => $value) {
  $images = $serviceV->getServiceImages($_GET['id'],$id);
  if(!empty($images)){
    $imgs = array_merge($imgs,$images);
  }
}
$zipFile = "zip/".$_GET['id'].'.zip';


  $name = $data['fullname'];
  $task = $_GET['task'];
  $type = $data['type'];
  $path = "modules/servis/image_docs/";
  $rootPath = realpath($path);

  $zip = new ZipArchive();

  $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

  $files = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($rootPath),
      RecursiveIteratorIterator::LEAVES_ONLY
  );
  foreach ($files as $name => $file)
  {
    if(in_array("image_docs/".$file->getBasename(), $imgs)){
      // Skip directories (they would be added automatically)
      if (!$file->isDir())
      {
          // Get real and relative path for current file
          $filePath = $file->getRealPath();
          $relativePath = substr($filePath, strlen($rootPath) + 1);

          // Add current file to archive
          $zip->addFile($filePath, $relativePath);
      }
    }
  }

  // Zip archive will be created only after closing object
  $zip->close();
  header('Content-Description: File Transfer');
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename="'.basename($zipFile).'"');
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Content-Length: ' . filesize($zipFile));
  readfile($zipFile);
  unlink($zipFile);
?>
