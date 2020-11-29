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

  function countVotes() {
    $sql = 'SELECT SUM(upVote + downVote) FROM photoVotes';
    $stmt = $this->dbh->query($sql);
    $number = $stmt->fetch()['SUM(upVote + downVote)'];
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

  function selectRandomFromPhotoArray($limit) {
    $distinct_photo_array = Array();
    $query = "SELECT * FROM photoVotes WHERE ".
      "photoID IN (SELECT photoID FROM photoVotes ORDER BY RANDOM() LIMIT $limit)";
    $stmt = $this->dbh->query($query);
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

  function selectRandomFromGroups() {
    return array_rand($this->buildGroups());
  }

  function selectRandomMembersFromGroup($group = null, $display_size = 2) {
    if ($group === null) {
      $group = $this->selectRandomFromGroups();
    }
    $total_group_array = $this->buildGroups();
    $selections = array_rand($total_group_array[$group], $display_size);
    return $selections;
  }

  function randomImageURL($image_array) {
    return array_rand($image_array);
  }

  function decideWhichImages($group_name = null, $display_size = null) {
    /**
     * If group_name is null:
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
    $selections = Array();
    // Non-group (i.e. select random from whole set)
    if ($group_name === null) {
      $distinct_pics = $this->createPhotoArray();
      $array_keys = array_rand($distinct_pics, $display_size);
      // This returns a zero-indexed list, but images might start at 1 or more.
      //foreach ($array_keys as $elem) {
      //  array_push($selections, $group_struct[$elem]);
      //}
      if (gettype($array_keys) == "integer") {
        $selections = array($array_keys);
        //echo "FOUND YOU WERE INTEGER\n";
      }
      if (gettype($array_keys) == "array") {
        //echo "ITS AN ARRAY\n";
        $selections = $array_keys;
      }
      return array('selections' => $selections,
        'group_name' => null);
    }
    // Group-oriented (i.e. select random from within group)
    $group_struct = $this->buildGroups();
    if ($group_name == "random") {
      $group_name = array_rand($group_struct);
      //echo "GROUP NAME: $group_name\n<br>";
    }
    $group_obj = $group_struct[$group_name];
    if ((sizeof($group_obj) < $display_size) && ($display_size > 1)) {
      throw new OutOfBoundsException();
    }
    // $array_keys = array_rand($group_obj, $display_size);
    // echo('AK\n<br>');
    // var_dump($array_keys);

    // foreach ($array_keys as $elem) {
    //   $selections[], $group_struct[$elem]);
    // }
    $array_keys = array_rand($group_obj, $display_size);
    foreach ($array_keys as $x) {
      $selections[$x] = $group_obj[$x];
    }
    return array('selections' => $selections,
      'group_name' => $group_name);
  }
}
