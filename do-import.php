<?php

include_once 'db/db.php';
include_once 'src/PhotoStates.php';
include_once 'src/Import.php';

$dbh = new MyDB("./production.db", TRUE);
$ps = new PhotoStates($dbh);
$imp = new Importing($dbh, "./actual-final-list.csv");
$parse_result = $imp->parseCSV();
foreach ($parse_result as $row) {
  $imp->addImage($row[0], $row[1]);
}

?>
