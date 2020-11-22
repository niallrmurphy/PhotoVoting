<?php

require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/../src/PhotoStates.php';

class Render {

  function __construct($mydbh = null) {
    if ($mydbh === null) {
      $this->db = new MyDB();
      // error_log("LOG: default test DB opening\n");
    } else {
      $this->db = $mydbh;
    }
  }

}
