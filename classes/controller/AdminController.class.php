<?php

class AdminController extends Admin{
  public function signIn($email, $pass, $fullname){
    $this-addAdmin($email, $pass, $fullname, 1);
  }
}

?>
