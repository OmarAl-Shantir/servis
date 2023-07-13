<?php
/**
 *
 */
class Cron extends Dbh
{
  public function clear(){
    $sql = "DELETE FROM ".$this->dbPrefix."hash_access WHERE `end` < CURRENT_TIMESTAMP";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([]);
    $results = $stmt->fetchAll();

    $sql = "DELETE FROM ".$this->dbPrefix."image_hash_access WHERE `end` < CURRENT_TIMESTAMP";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([]);
    $results = $stmt->fetchAll();
    return 1;
  }
}

 ?>
