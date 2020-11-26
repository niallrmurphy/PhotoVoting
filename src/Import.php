<?php

require_once __DIR__ . '/../db/db.php';

class Importing {

  function __construct($mydbh = null) {
    if ($mydbh === null) {
      $this->db = new MyDB();
      // error_log("LOG: default test DB opening\n");
    } else {
      $this->db = $mydbh;
    }
  }

  function addImage ($groups) {
    // Can't vote for a non-existent ID
    $sql = "INSERT INTO photoVotes (path, groups) SET " . $column_name . " = " . $column_name .
      " " . $delta . " WHERE photoID = ?";
    $statement = $this->db->db->prepare($sql);
    if (!$statement) {
      die ("Could not prepare statement in addVote");
    }
    try {
      $result = $statement->execute([$photo_id]);
      $rows = $statement->rowCount();
    } catch (Exception $e) {
      die("DB exception in addVote");
    }
    if ($rows == 0) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  function parseCSV ($filepath = null) {
    $picture_array = [];
    if (!isset($filepath)) {
      $filepath = "export.csv";
    }
    try {
      $fh = fopen($filepath, "r");
    } catch (Exception $e) {
      die("Can't access CSV: ".$e->getMessage()."\n");
    }
    $parsed_csv = fgetcsv($fh, 0);
    if (!$parsed_csv) {
      die("Can't parse CSV from $filepath\n");
    }
    # Main thing is to build the groups identity
    for ($x = 0; $x < $length; $x++) {
      echo ('test');
    }
    return $picture_array;
  }
}
