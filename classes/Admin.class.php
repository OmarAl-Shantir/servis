<?php

class Admin extends Dbh{

// ------------View------------
  protected function getDataByEmail($email) {
    $sql = "SELECT * FROM admins WHERE email = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$email]);
    $results = $stmt->fetchAll();
    $res = array();
    foreach ($results as $row) {
      foreach ($row as $key=>$value){
        $res[$key] = $value;
      }
    }
    return $res;
  }

  protected function getDatabyId($id) {
    $sql = "SELECT * FROM admins WHERE id_admin = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$id]);
    $results = $stmt->fetchAll();
    $res = array();
    foreach ($results as $row) {
      foreach ($row as $key=>$value){
        $res[$key] = $value;
      }
    }
    return $res;
  }

  protected function getAllAdmins(){
    $sql = "SELECT * FROM admins WHERE 1";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([]);
    $results = $stmt->fetchAll();
    $res = array();
    foreach ($results as $row) {
      $res[$row['id_admin']] = array("email" => $row['email'], "fullname" => $row['fullname'], "role" => $row['role']);
    }
    return $res;
  }

// ------------Controller------------

  protected function addAdmin($email, $pass, $fullname, $role){
    $sql = "INSERT INTO `admins`(`email`, `pass`, `fullname`, `role`) VALUES (?,?,?,?)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$email, $pass, $fullname, $role]);
  }

  protected function addAdminRole($rolename){
    $sql = "INSERT INTO `admin_roles`(`name`) VALUES (?)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$rolename]);
  }

  protected function addAdminModuleRole($id_module, $id_adminrole){
    $sql = "INSERT INTO `admin_module_roles`(`id_module`, `id_admin_role`) VALUES (?,?)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$id_module, $id_adminrole]);
  }
}
?>
