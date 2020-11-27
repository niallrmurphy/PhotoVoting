<?php

require_once __DIR__ . '/../db/db.php';

class PhotoStates {

  protected $dbh;

  function __construct($mydbh = null) {
    if ($mydbh === null) {
      $newobj = new MyDB();
      $this->dbh = $newobj->dbh;
      // error_log("LOG: default test DB opening\n");
    } else {
      $this->dbh = $mydbh;
    }
  }

  function addVote ($photo_id, $thumb_direction = "Up") {
    // Can't vote for a non-existent ID
    if (!isset($photo_id)) {
      return FALSE;
    } else {
      ($thumb_direction == "Up") ? ($delta = "+1") : ($delta = "-1");
      $column_name = $thumb_direction . "Vote";
      $sql = "UPDATE photoVotes SET " . $column_name . " = " . $column_name .
        " " . $delta . " WHERE photoID = ?";
      $statement = $this->dbh->prepare($sql);
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
  }

  function countImages () {
    $sql = 'SELECT COUNT(*) FROM photoVotes';
    $stmt = $this->dbh->query($sql);
    $number = $stmt->fetch()['COUNT(*)'];
    return $number;
  }

  function createPhotoArray() {
    $distinct_photo_array = Array();
    $stmt = $this->dbh->query('SELECT DISTINCT photoID FROM photoVotes');
    foreach ($stmt as $row) {
      array_push($distinct_photo_array, $row['photoID']);
    }
    return $distinct_photo_array;
  }

  function buildImageStructure () {
    $total_photo_array = Array();
    $stmt = $this->dbh->query('SELECT * FROM photoVotes');
    foreach ($stmt as $row) {
      $total_photo_array[$row['photoID']] = array(
        $row['upVote'], $row['downVote'], $row['groups']);
    }
    return $total_photo_array;
  }

  function groupsIn ($photo_id) {
    if ($photo_id === null OR !isset($photo_id)) {
      return null;
    }
    $groups = Array();
    $sql = 'SELECT groups FROM photoVotes WHERE photoID = ?';
    $statement = $this->dbh->prepare($sql);
    $statement->execute([$photo_id]);
    while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
        $result = $row['groups'];
    }
    return (explode(",", $result));
  }

  function getPathForId($photo_id) {
    if ($photo_id === null OR !isset($photo_id)) {
      return null;
    }
    $path = "";
    $sql = 'SELECT imgpath FROM photoVotes WHERE photoID = ?';
    $statement = $this->dbh->prepare($sql);
    $statement->execute([$photo_id]);
    $found = 0;
    while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
      $found++;
      $result = $row['imgpath'];
    }
    if (!isset($result)) {
      return FALSE;
    } else {
      return $result;
    }
  }

  function buildGroups() {
    $total_group_array = Array();
    $stmt = $this->dbh->query('SELECT photoID, groups FROM photoVotes');
    foreach ($stmt as $row) {
      $id = $row['photoID'];
      $groupstring = $row['groups'];
      $groups = explode(",", $groupstring);
      foreach ($groups as $elem) {
        // elem = a group number; want elem => [ids_using_it]
        if (empty($total_group_array[$elem])) {
          $total_group_array[$elem] = Array($id);
        } else {
          array_push($total_group_array[$elem], $id);
        }
      }
    }
    return $total_group_array;
  }

  function randomImageURL($image_array) {
    return array_rand($image_array);
  }

  function decideWhichImages($group_oriented = null, $display_size = null) {
    /**
     * If group_oriented is false:
     * Scan the database to find all images, pick display_size random selections
     * (2 by default).
     * If group_oriented is true:
     * Scan the database to find out, for every image, what group(s)
     * it is in (if any). For example, image 1 in groups [1, 2, 3] and
     * image 2 in groups [15, 1].
     * Build a table of all groups, and a mapping between all groups and images.
     * Randomly select a group (with > display_size members).
     * Randomly select display_size images within that group.
     **/
    // Things we'll need no matter what
    if($display_size === null) {
      $display_size = 2;
    }
    $number = $this->countImages();
    $distinct_pics = $this->createPhotoArray();
    $selections = Array();
    //
    if ($group_oriented === null) {
      //array_push($selections).
      $selections = array_rand($distinct_pics, $display_size);
    } else {
      $group_struct = buildGroups();
      $group = array_rand($group_struct); // Need display_size check
      $selections = array_rand($group_struct[$group], $display_dize);
    }
    //echo "\nSELECTIONS ", var_dump($selections);
    return $selections;
  }

}
