<?php

class MyDB extends SQLite3 {
   function __construct() {
      if (!$this->open('production.db') {
        echo $db->lastErrorMsg();
        die();
      }
   }
}

class photo_states {
  function __construct() {
    $this->$db = new MyDB();
  }

  function addVote ($photo_id, $thumb_direction) {
    // Can't vote for a non-existent ID
    if (!isset($photo_id)) {
      return FALSE;
    } else {
      $column_name = $thumb_direction . "Vote";
      ($thumb_direction == "Up") ? ($delta = +1) : ($delta = -1);
      $sql = "UPDATE photoVotes SET " . $column_name . " + " . $delta .
        " WHERE photoID = :id";
      echo ('SQL $sql');
      $statement = $db->prepare($sql);
      $statement->bindValue(':id', $photo_id);
      $result = $statement->execute();
      $rows = $result->rowCount();
      echo ("Rows affected: $rows");
      return TRUE;
    }
  }

  function countImages {
    $sql = 'SELECT COUNT(*) FROM photoVotes';
    $statement = $db->prepare($sql);
    $result = $sql->execute();
    var_dump($result);
    echo ("Rows affected: $rows");
    return TRUE;
    count = db.get(, (err, row) => {
      if (err) {
        console.log('DB COUNT(*) ERROR', err);
      };
      return row['COUNT(*)']
        ? console.dir(row)
        : console.log(`No count found for photoVotes (this shoudl not happen)`);
    });
  };

const createPhotoArray =() => {
  var total_photo_array = Array();
  db.each('SELECT DISTINCT photoID FROM photoVotes', (err, row) => {
    if (err) {
      console.log('DB SELECT DISTINCT ERROR', err);
    };
    var pid = row.photoID;
    total_photo_array.push(pid);
  });
  return total_photo_array;
};

// close the database connection
function cleanup() {
  //db.close((err) => {
  //  if (err) {
  //    return console.error(err.message);
  //  }
  //};
};

const buildImageStructure = () => {
  let sql = 'SELECT * FROM photoVotes';
  db.serialize(() => {
    db.all(sql, [], (err, rows) => {
      if (err) {
        throw err;
      };
      rows.forEach((row) => {
        console.log(row.name);
      });
    });
  });
}

module.exports = { addVote, buildImageStructure, countImages, createPhotoArray };
