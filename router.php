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
    $pa = $ps->createPhotoArray();
    if (preg_match('%\/img\/(\d+)$%', $req, $matches)) {
      $r->displayImageById($matches[1]);
    } elseif (preg_match('%\/rand\/(\d+)$%', $req, $matches)) {
      $count = $matches[1];
      $chosen_ones = $ps->selectRandomFromPhotoArray($count);
      displayImagePage($r, $matches[1], $ps->countVotes(), $chosen_ones, "total collection");
    } elseif (preg_match('%(\d+)?\/style\.css%', $req, $matches)) {
      if (isset($matches[1])) {
        $r->displayCSS($matches[1]);
      } else {
        return false;
      }
    } elseif (preg_match('%redirect%', $req)) {
      header('Location: ', $r->redirectByPolicy());
    } elseif (preg_match('%\/group/(\d+)$%', $req, $matches)) {
      // Pick a random group, display N images from it
      $display_size = $matches[1];
      $resultarray = $ps->decideWhichImages(True, $display_size);
      $selections = $resultarray['selections'];
      $group_name = $resultarray['group_name'];
      displayImagePage($r, $matches[1], $ps->countVotes(), $selections, $group_name);
    } elseif (preg_match('%\/addVote/(\d+)$%', $req, $matches)) {
      $id = $matches[1];
      $ps->addVote($id);
      $redirect = $r->redirectByPolicy();
      //$redirect = "/group/2";
      header("Location: $redirect");
      exit;
    }
  }
} else { // We're not a CLI-server, so we're presumably a 'real' server...

}

function displayImagePage ($r, $number, $total_votes, $chosen_ones, $text) {
  $division = round(100/$number);
  $r->displayHeader($division, $total_votes);
  foreach (array_values($chosen_ones) as $elem) {
    $uri = $r->generateImageURI($elem);
    $urls[] = $uri;
  }
  $r->imageSideBySideBlock($number, $urls);
  $r->imageFooter($text);
}

?>
