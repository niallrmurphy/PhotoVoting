<?php

require_once __DIR__ . '/../db/db.php';

class Importing {

  protected $db;
  protected $fh;
  protected $filepath;
  const EXPORT_IDENTIFIER = "Experimental";

  function __construct($mydbh = null, $filepath = null) {
    if ($mydbh === null) {
      $this->db = new MyDB();
      // error_log("LOG: default test DB opening\n");
    } else {
      $this->db = $mydbh;
    }
    if ($filepath === null) {
      $filepath = "export.csv";
    } else {
      $this->filepath = $filepath;
    }
    try {
      $fh = fopen($filepath, "r");
      $this->fh = $fh;
    } catch (Exception $e) {
      die("Can't access CSV: ".$e->getMessage()."\n");
    }
  }

  function addImage ($path, $groups) {
    if (!isset($path)) {
      echo ("Can't add an image without knowing what the path is");
      return FALSE;
    }
    // For the moment, assume an image can only be in one group. FIXME later.
    // Lots of duplicated code here, but not much we can do about it right now.
    if (isset($groups)) {
      $sql = "INSERT INTO photoVotes (imgpath, groups) VALUES (?, ?) ";
      $statement = $this->db->db->prepare($sql);
      if (!$statement) {
        die ("Could not prepare statement in addVote");
      }
      try {
        $result = $statement->execute([$path, $groups]);
      } catch (Exception $e) {
        die("DB exception in addImage");
      }
      return TRUE;
    } else {
      $sql = "INSERT INTO photoVotes (imgpath) VALUES (?)";
      $statement = $this->db->db->prepare($sql);
      if (!$statement) {
        die ("Could not prepare statement in addVote");
      }
      try {
        $result = $statement->execute([$path]);
      } catch (Exception $e) {
        die("DB exception in addImage");
      }
      return TRUE;
    }
  }

  function parseCSV ($fh = null) {
    if ($fh === null) {
      $myfh = $this->fh;
    }
    $picture_array = [];
    // Discard the extraneous stuff. For our purposes, that means we
    // only include filename & collections (which becomes groups).
    while (($data = fgetcsv($myfh, 0)) !== FALSE) {
      $num = count($data);
      $filename = $data[2];
      $collections = $data[7];
      # "quick collection, Reviews/2020/Static Selects Per Year/Static Collections,
      # Potential Calendar/2020/Static Selects Per Year/Static Collections,
      # Stormy Seas/Experimental Grouping Set/2020/Static Selects Per Year/Static Collections,
      # Reviews Copy/2020/Static Selects Per Year/Static Collections"
      $group_subset = preg_grep("%".self::EXPORT_IDENTIFIER."%",
        explode(',', $collections));
      // Bit of a hack, but it works
      if ($gs = reset($group_subset)) {
        // Now grep out the group name
        $count = 0;
        $pattern = "/^(.+)\/.*".self::EXPORT_IDENTIFIER.".*$/";
        $matches = array();
        if (!preg_match($pattern, $gs, $matches)) {
          // Could be header line, just ignore
          // echo ('Group identified string not found in line: ' . $group_subset);
        } else {
          $groupname = trim($matches[1]);
        }
        array_push($picture_array, [$filename, $groupname]);
        $row++;
      } else {
        // Probably a header line
        // echo "Export identifier not found";
      }
    }
    if (!$picture_array) {
      die("Didn't parse CSV from ".$this->filepath."\n");
    }
    return $picture_array;
  }
}
