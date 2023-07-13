<?php
session_start();
//header('Content-Type: application/json');
require __DIR__ . '/../../init.php';

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

if(isset($_POST['function']) && $_POST['function']=="getRecordsData") {
     $serviceV = new ServiceView();
     switch($_POST['typeArg']){
          case 1:
               $type = 1;
               break;
          case 2:
               $type = 2;
               break;
          case 3:
               $type = 3;
               break;
          case 4:
               $type = 4;
               break;
          default:
               $type = 0;
     }
     $result = $serviceV->get_records_data_by_type($type);
     echo json_encode(sizeof($result));
}

function preload ($type, $limit, $offset, $filter){
     $sV = new ServiceView();
     $dataS = $sV->get_all_service_records($type, $limit, $offset, $filter);
     if(empty($dataS)) return "";
     else return $dataS;
}

//preload("",100,0,"Registrovaná");

?>