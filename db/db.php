<?php

class MyDB {
  public $db;
  function __construct($db_name = null, $make_schema = null) {
    if ($db_name === null) {
      $db_path = "test.db";
    } else {
      $db_path = $db_name;
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
    if ($make_schema == True) {
      $make_table = "CREATE TABLE photoVotes (
        photoID INTEGER PRIMARY KEY,
        upVote INTEGER DEFAULT 0,
        downVote INTEGER DEFAULT 0,
        imgpath TEXT,
        groups TEXT
      );";
      // Make table
      $stmt = $this->db->prepare($make_table);
      if (!$stmt) {
        die ("Could not prepare statement in makeSchema");
      }
      try {
        $result = $stmt->execute();
      } catch (Exception $e) {
        echo $e->getMessage()."\n";
        die("DB make table exception in PhotoStatesTest::setUp\n");
      }
    }
  }
}
?>
