<?php

class Dbh {

  private $host = DB_HOST;
  private $port = DB_HOST_PORT;
  private $user = DB_USER;
  private $pwd = DB_PASSWORD;
  private $dbName = DB_NAME;
  protected $dbPrefix = DB_PREFIX;

  protected function connect() {
    try {
      $dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->dbName;
      $pdo = new PDO($dsn, $this->user, $this->pwd);
      $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      return $pdo;
    } catch(PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }
  }
}
?>
