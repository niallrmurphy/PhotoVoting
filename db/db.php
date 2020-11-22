<?php

class MyDB {
  public $db;
  function __construct($db_name = null) {
    if ($db_name === null) {
      $db_path = "test.db";
    }
    try {
      $this->db = new \PDO("sqlite:$db_path", '', '', array(
        \PDO::ATTR_EMULATE_PREPARES => false,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
      ));
    } catch (PDOException $e) {
      throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
  }
}
?>
