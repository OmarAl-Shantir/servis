<?php

class Admin extends Dbh{

// ------------View------------
  protected function getDataByEmail($email) {
    $sql = "SELECT * FROM ".$this->dbPrefix."admins WHERE email = ?";
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

  protected function getRole($id){
    $sql = "SELECT role FROM ".$this->dbPrefix."admins WHERE id_admin = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$id]);
    $results = $stmt->fetchAll();
    return $res;
  }

  protected function getDatabyId($id) {
    $sql = "SELECT * FROM ".$this->dbPrefix."admins WHERE id_admin = ?";
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

  protected function getDataByMail($mail) {
    $sql = "SELECT * FROM ".$this->dbPrefix."admins WHERE email = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$mail]);
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
    $sql = "SELECT * FROM ".$this->dbPrefix."admins WHERE 1";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([]);
    $results = $stmt->fetchAll();
    $res = array();
    foreach ($results as $row) {
      $res[$row['id_admin']] = array("email" => $row['email'], "fullname" => $row['fullname'], "role" => $row['role'], "active" => $row['active']);
    }
    return $res;
  }

  protected function getPermissions(){
    $sql = "SELECT * FROM ".$this->dbPrefix."permission_type WHERE 1";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([]);
    $results = $stmt->fetchAll();
    $res = array();
    foreach ($results as $row) {
      $res[$row['id_permission_type']] = $row['name'];
    }
    return $res;
  }

  protected function getAdminPermision($id_admin, $id_permission){
    $sql = "SELECT * FROM ".$this->dbPrefix."admin_permission WHERE id_admin = ? AND id_permission_type = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$id_admin, $id_permission]);
    $results = $stmt->fetchAll();
    $res = $results[0]['value'];
    return $res;
  }

  protected function isHashActive($id_user, $hash){
    $sql = "SELECT * FROM ".$this->dbPrefix."hash_access HA
    WHERE (HA.`user`= ? AND HA.`hash`= ? AND HA.`end` >= CURRENT_TIMESTAMP)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$id_user, $hash]);
    $results = $stmt->fetchAll();
    return $results[0]['user'];
  }

  protected function getHash($user){
    $sql = "SELECT * FROM ".$this->dbPrefix."hash_access HA
    WHERE HA.`user` = ? AND `end` >= CURRENT_TIMESTAMP)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$user]);
    $results = $stmt->fetchAll();
    return $results[0]['hash'];
  }

// ------------Controller------------

  protected function addAdmin($email, $pass, $fullname, $role){
    $sql = "INSERT INTO `".$this->dbPrefix."admins`(`email`, `pass`, `fullname`, `role`) VALUES (?,?,?,?)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$email, $pass, $fullname, $role]);
  }

  protected function addAdminRole($rolename){
    $sql = "INSERT INTO `".$this->dbPrefix."admin_roles`(`name`) VALUES (?)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$rolename]);
  }

  protected function addAdminModuleRole($id_module, $id_adminrole){
    $sql = "INSERT INTO `".$this->dbPrefix."admin_module_roles`(`id_module`, `id_admin_role`) VALUES (?,?)";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$id_module, $id_adminrole]);
  }

  protected function updatePermission($id_admin, $id_permission, $value){
    $sql = "UPDATE `".$this->dbPrefix."admin_permission` SET `value` = ? WHERE `id_admin` = ? AND `id_permission_type` = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$value, $id_admin, $id_permission]);
  }

  protected function updateAdminData($id_admin, $fullname, $email, $active){
    $sql = "UPDATE `".$this->dbPrefix."admins` SET `fullname` = ?, `email` = ?, `active` = ? WHERE `id_admin` = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$fullname, $email, $active, $id_admin]);
  }

  protected function changePassword($id_admin, $hash){
    $sql = "UPDATE `".$this->dbPrefix."admins` SET `pass` = ? WHERE `id_admin` = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$hash, $id_admin]);
  }

  protected function newHash($user){
    $hash = hash_hmac('sha3-512', bin2hex(random_bytes(25)), bin2hex(random_bytes(25)));
    $end = date("Y-m-d H:i:s", strtotime("+15 minutes"));
    $sql = "INSERT INTO `".$this->dbPrefix."hash_access` (`hash`, `user`, `end`) VALUES (?, ?, ?)";
    $con = $this->connect();
    $stmt = $con->prepare($sql);
    $stmt->execute([$hash, $user, $end]);
    return $hash;
  }
}
?>
