<?php

require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/../src/PhotoStates.php';

class Render {

  protected $dbh;
  protected $pstates;
  const IMG_PREFIX = "img/";

  function __construct($mydbh = null) {
    if ($mydbh === null) {
      $newobj = new MyDB();
      $this->dbh = $newobj->dbh;
      // error_log("LOG: default test DB opening\n");
    } else {
      $this->dbh = $mydbh;
    }
    $this->pstates = new PhotoStates($this->dbh);
  }

  function redirectByPolicy() {
    $roll = rand(1, 100); // Should be a tunable
    $assumed_path = $_SERVER['PHP_SELF'];
    $assumed_path = "";
    if ($roll > 0 && $roll < 33) {
      // hot-or-not two-off, non-group
      $postfix = "/rand/2";
    } elseif ($roll >= 33 and $roll < 66) {
      // hot-or-not two-off, group
      $postfix = "/group/2";
    } elseif ($roll >= 66 and $roll < 80) {
      $postfix = "/group/4";
    } elseif ($roll >=80) {
      // hot-or-not N-off, non-group
      $postfix = "/rand/4";
    }
    $prefix = $assumed_path;
    $url = $prefix . $postfix;
    return $url;
  }

  function renderPage($group_oriented = null,
    $display_size = null,
    $group = null) {
    display_header();
    if (!isset($_REQUEST['n'])) {
      $display_size = 2;
    }
    if ($_REQUEST['n'] < 1 or $_REQUEST['n'] > 10) {
      $display_size = 2;
    }
    if (!isset($_REQUEST['group'])) {
      $group_oriented = false;
    } elseif ($_REQUEST['group'] == 'no') {
      $group_oriented = false;
    } elseif ($_REQUEST['group'] == 'rand') {
      $group_oriented = true;
    } elseif (is_numeric($_REQUEST['group'])) {
      $group_oriented = true;
      $group_number = $_REQUEST['group'];
    } else {
      // Potentially dangerous catchall
      $group_oriented = true;
      $group_number = $group;
    }
    $image_set = $this->pstates->decideWhichImages();
    // If we have a display size of one
    for ($x = 0; $x <= $n; $x++) {
      image_display_block();
    }

  }

  function displayHeader($number = null, $votes = null) {
    if ((!isset($number)) or ($number === null)) {
      $number = 100;
    }
    if ((!isset($votes)) or ($votes === null)) {
      $votes = "(uncounted)";
    }
    echo('<!DOCTYPE html>
    <html>

    <head>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">');
//      <link rel="stylesheet" href="/'.$number.'/style.css" type="text/css"/>
// <!--      <link rel="stylesheet" href="/public/style.css"> -->
    echo('<style>');
    $this->displayCSS($number);
    echo('</style>');
    echo('
      <meta charset="utf-8">
      <title></title>
    </head>

    <body>
      <h1>Photo Voting Service</h1>
      <h2>Select what you think is the best image.</h2>
      <h4>'.$votes.' total votes.</h4>');
  }

  function displayCSS($number = null) {
    if ((!isset($number)) or ($number === null)) {
      $number = 100;
    }
    echo ('
    * {
      box-sizing: border-box;
    }

    img:hover {
        border: 5px solid red;
    }

    img {
        cursor: pointer;
        border: 5px solid white;
    }

    /* Three image containers (use 25% for four, and 50% for two, etc) */
    .column {
      float: left;
      width: '.$number.'%;
      padding: 5px;
    }

    /* Clear floats after image containers */
    .row::after {
      content: "";
      clear: both;
      display: table;
    }

    /* Responsive layout - makes the three columns stack on top of each other instead of next to each other */
@media screen and (max-width: 800px) {
  .column {
    width: 100%;
  }
}

    /*.responsive {
      min-width: 300px;
      height: auto;
    }*/');
  }

  function displayLikeButtons() {
    echo('  <div id="button-div">
        <button class="unpicked fa fa-thumbs-o-up" id="upbtn">  </button>
        <button class="unpicked fa fa-thumbs-o-down" id="downbtn">  </button>
        <div id="voteTally"></div>
      </div>');
  }

  function imageSideBySideBlock($number, $urls) {
    $division = round(100 / $number);
    $pat = '%\/img\/(\d+)%';
    echo ('<div class="row">');
    for ($x = 0; $x < $number; $x++) {
      $matches = array();
      preg_match($pat, $urls[$x], $matches);
      $id = $matches[1];
      echo('<div class="column">');
      echo('<a href="/addVote/'.$id.'">');
      echo('<img src="'.$urls[$x].'" style="width:100%" class="responsive">');
      echo('</a>');
      echo('</div>');
    }
    echo ('</div>');
  }

  function imageDisplayBlock() {
    echo('
      <img src="?image=1" id="photoItem" />
      <div id="button-div">
        <button class="unpicked fa fa-thumbs-o-up" id="upbtn">  </button>
        <button class="unpicked fa fa-thumbs-o-down" id="downbtn">  </button>
        <div id="voteTally"></div>
      </div>');
  }

  function imageFooter($group = null) {
    echo('<!-- <script src="/main.js"></script> -->');
    if (!empty($group)) {
      echo ("<i>Group name: '$group'</i>");
    }
    echo ('
    </body>
    </html>');
  }

  function displayImageByFilename($filename) {
    // Nuke this odbc_setoption
    $real_filename = "./".self::IMG_PREFIX . pathinfo($filename)['filename'] .
      ".jpg";
    $handle = fopen($real_filename, "rb");
    $contents = fread($handle, filesize($real_filename));
    fclose($handle);
    header("Content-type: ".$image_mime);
    $image_mime = image_type_to_mime_type(exif_imagetype($real_filename));
    echo $contents;
  }

  function displayImageById($photoid) {
    if (!isset($photoid)) {
      return FALSE;
    }
    $path = $this->pstates->getPathForId($photoid);
    if (!isset($path) or $path == "") {
      echo "<b>No such image.</b>";
      return FALSE;
    } else {
      $this->displayImageByFilename($path);
    }
  }

  function generateImageURI($photoid) {
    if (!isset($photoid)) {
      return FALSE;
    }
    //$url = $_SERVER["SERVER_NAME"] . $_SERVER["DOCUMENT_ROOT"] .
    $url = "/" . self::IMG_PREFIX . "$photoid";
    return $url;
  }
}

//$R = new Render();
//header('Location: $url');
