<?php

class MonitorView extends Monitor{

  public function getServiceItems(){
    $items = $this->getAllServiceItems();
    return $items;
  }
}

?>
