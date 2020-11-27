<?php
// router.php
include_once 'db/db.php';
include_once 'src/PhotoStates.php';
include_once 'src/Render.php';

$db = new MyDB("./production.db", FALSE);
$ps = new PhotoStates($db->dbh);
$r = new Render($db->dbh);

if (php_sapi_name() == 'cli-server') {
  $req = $_SERVER["REQUEST_URI"];
  /* route static assets and return false */
  if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
    return false; // serve the requested resource as-is.
  } else {
    $matches = array();
    if (preg_match('%img\/(\d+)$%', $req, $matches)) {
      $r->displayImageById($matches[1]);
    }
  }
} else {

}

?>
