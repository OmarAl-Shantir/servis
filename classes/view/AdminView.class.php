<?php

class AdminView extends Admin{

  public function logIn($email, $pass){
    $admin_data = $this->getDataByEmail($email);
    if (password_verify($pass, $admin_data['pass'])){
      return $admin_data['id_admin'];
    } else {
      return 0;
    }
  }

  public function adminDatabyId($id){
    $admin_data = $this->getDatabyId($id);
    return $admin_data;
  }

  public function getAdmins(){
    $admins = $this->getAllAdmins();
    return $admins;
  }
}

?>
