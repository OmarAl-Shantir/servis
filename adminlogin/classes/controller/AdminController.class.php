<?php

class AdminController extends Admin{
  public function signIn($email, $pass, $fullname){
    $this->addAdmin($email, $pass, $fullname, 1);
  }

  public function savePermissions($data){
    foreach ($data as $a_id => $permissions) {
      foreach ($permissions as $p_id => $value) {
        $this->updatePermission($a_id, $p_id, $value);
      }
    }
  }

  public function update_admin_data($id, $fullname, $email, $active){
    $active = ($active==NULL)?0:$active;
    $this->updateAdminData($id, $fullname, $email, $active);
  }

  public function change_password($id, $old_pass, $new_pass,$was_lost = 0){
    $cost = 12;
    $pwd = password_hash($new_pass, PASSWORD_DEFAULT, ['cost' => $cost]);
    if ($was_lost == 1){
      if (!empty($pwd)){
        $this->changePassword($id, $pwd);
        return "Heslo bolo zmenené";
      } else {
        return "Heslo nebolo zmenené";
      }
    } else {
      if (($id == $_SESSION['admin_logged'] && password_verify($old_pass, $this->getDatabyId($id)['pass'])) || ($_SESSION['admin_role'] == 1)) {
        if (!empty($pwd)){
          $this->changePassword($id, $pwd);
          return "Heslo bolo zmenené";
        }
      } else {
        return "Zlé pôvodné heslo";
      }
    }
  }

  public function new_hash($user){
    return $this->newHash($user);
  }
}

/*
if (password_needs_rehash($row['account_passwd'], PASSWORD_DEFAULT, $options))
    {
      $hash = password_hash($password, PASSWORD_DEFAULT, $options);

      // Update the password hash on the database.
      $query = 'UPDATE accounts SET account_passwd = :passwd WHERE account_id = :id';
      $values = [':passwd' => $hash, ':id' => $row['account_id']];

      try
      {
        $res = $pdo->prepare($query);
        $res->execute($values);
      }
      catch (PDOException $e)
      {
        // Query error.
        echo 'Query error.';
        die();
      }
    }
*/
?>
