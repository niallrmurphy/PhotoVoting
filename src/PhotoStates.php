<?php

require_once __DIR__ . '/../db/db.php';

class PhotoStates {

  function __construct($mydbh = null) {
    if ($mydbh === null) {
      $this->db = new MyDB();
      error_log("LOG: default test DB opening\n");
    } else {
      $this->db = $mydbh;
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
  }

  function countImages () {
    $sql = 'SELECT COUNT(*) FROM photoVotes';
    $stmt = $this->db->db->query($sql);
    $number = $stmt->fetch()['COUNT(*)'];
    return $number;
  }

  function createPhotoArray() {
    $distinct_photo_array = Array();
    $stmt = $this->db->db->query('SELECT DISTINCT photoID FROM photoVotes');
    foreach ($stmt as $row) {
      array_push($distinct_photo_array, $row['photoID']);
    }
    return $distinct_photo_array;
  }

  function buildImageStructure () {
    $total_photo_array = Array();
    $stmt = $this->db->db->query('SELECT * FROM photoVotes');
    foreach ($stmt as $row) {
      array_push($total_photo_array, $row['photoID']);
    }
    return $total_photo_array;
  }

  function buildGroups() {
    $total_group_array = Array();
    $stmt = $this->db->db->query('SELECT photoID, groups FROM photoVotes');
    foreach ($stmt as $row) {
      $id = $row['photoID'];
      $groupstring = $row['groups'];
      echo "VD"; var_dump($groupstring);
      if ($groupstring = '') {
        $groups = explode(",", $groupstring);
      }
      foreach ($groups as $elem) {
        array_push($total_group_array[$elem], $photo_id);
      }
    }
    return $total_photo_array;
  }

  function groupsIn ($photo_id) {
    if ($photo_id === null OR !isset($photo_id)) {
      return null;
    }
    $groups = Array();
    $stmt = $this->db->db->query('SELECT groups FROM photoVotes WHERE ' .
      'photoID = ?');
    $groupstring = $stmt->fetch()['groups'];
    return (explode(",", $groupstring));
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
    $number = countImages();
    $distinct_pics = createPhotoArray();
    $selections = Array();
    //
    if ($group_oriented === null) {
      array_push($selections).array_rand($distinct_pics, $display_size);
    } else {
      $group_struct = buildGroups();
      $group = array_rand($group_struct); // Need display_size check
      $selections = array_rand($group_struct[$group], $display_dize);
    }
    return $selections;
  }

// const buildImageStructure = () => {
//   let sql = 'SELECT * FROM photoVotes';
//   db.serialize(() => {
//     db.all(sql, [], (err, rows) => {
//       if (err) {
//         throw err;
//       };
//       rows.forEach((row) => {
//         console.log(row.name);
//       });
//     });
//   });
}

// module.exports = { addVote, buildImageStructure, countImages, createPhotoArray };
