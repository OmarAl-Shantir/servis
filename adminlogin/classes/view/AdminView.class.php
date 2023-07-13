<?php

class AdminView extends Admin{

  public function logIn($email, $pass){
    $cost = 12;
    $admin_data = $this->getDataByEmail($email);
    if (password_verify($pass, $admin_data['pass']) && $admin_data['active'] == 1){
      if (password_needs_rehash($admin_data['pass'], PASSWORD_DEFAULT, ['cost' => $cost])) {
        $pwd = password_hash($pass, PASSWORD_DEFAULT, ['cost' => $cost]);
        if (!empty($pwd)){
          $this->changePassword($admin_data['id_admin'], $pwd);
        }
      }
      return $admin_data['id_admin'];
    } else {
      return 0;
    }
  }

  public function getAdminRole($id_admin){
    //return getRole($id_admin);
  }

  public function adminDatabyId($id){
    $admin_data = $this->getDatabyId($id);
    return $admin_data;
  }

  public function adminDataByMail($mail){
    $admin_data = $this->getDataByMail($mail);
    return $admin_data;
  }

  public function getAdmins(){
    $admins = $this->getAllAdmins();
    return $admins;
  }

  public function getPermissionsTypes(){
    $permissions = $this->getPermissions();
    return $permissions;
  }

  public function getPermissionByAdmin($id_admin, $id_permission){
    $permission = $this->getAdminPermision($id_admin, $id_permission);
    $cls = array("btn-danger", "btn-info", "btn-warning", "btn-success");
    $text = array("Zakázané", "Čítanie", "Zápis", "Zmena");
    return array("class" => $cls[$permission], "text" => $text[$permission], "value" => $permission);
  }

  public function getAdminPermissionByIds($id_admin, $id_permission){
    $permission = $this->getAdminPermision($id_admin, $id_permission);
    return $permission;
  }

  public function is_hash_active($id_user, $hash){
    return $this->isHashActive($id_user, $hash);
  }

  public function get_hash($user){
    return $this->getHash($user);
  }
}

?>
