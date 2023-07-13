<?php

class Monitor extends Dbh{

// ------------View------------
  protected function getAllServiceItems(){
    $sql = "
    SELECT DATEDIFF(NOW(), si.datum_vzniku)  AS 'v_servise', st.description AS 'typ' , si.servisny_list, si.product_ref, ss.description AS 'stav'
    FROM ".$this->dbPrefix."service_item si
    LEFT JOIN ".$this->dbPrefix."service_status ss
    ON ss.id_status = si.id_stav_opravy
    LEFT JOIN ".$this->dbPrefix."service_types st
    ON st.id_service_type = si.id_typ
    WHERE si.id_stav_opravy < 7 AND si.deleted IS NULL
    ORDER BY v_servise DESC LIMIT 13;
    ";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([]);
    $results = $stmt->fetchAll();
    return $results;
  }

// ------------Controller------------
}
?>
