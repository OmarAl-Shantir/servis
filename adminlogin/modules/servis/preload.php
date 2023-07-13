<?php
///header('Content-Type: application/json');
require __DIR__ . '/../../init.php';
//require '../../../class-autoloader.php';

$res = array();

if(isset($_POST['function']) && $_POST['function']=="serviceItems") {
  $type = (empty($_POST['typArg'])) ? NULL : $_POST['typArg'];
  $limit = (empty($_POST['limArg'])) ? 100 : $_POST['limArg'];
  $offset = (empty($_POST['offArg'])) ? 0 : $_POST['offArg'];
  $filter = (empty($_POST['filArg'])) ? '' : $_POST['filArg'];

  $res['result'] = preload($type, $limit, $offset, $filter);

  echo json_encode($res['result']);
}

if(isset($_POST['function']) && $_POST['function']=="getAdminRole") {
  echo json_encode($_SESSION['admin_role']);
}

function preload ($type, $limit, $offset, $filter){
  $sV = new ServiceView();
  $dataS = $sV->get_all_service_records($type, $limit, $offset, $filter);
  if(empty($dataS)) return "";
  else return $dataS;
}


?>