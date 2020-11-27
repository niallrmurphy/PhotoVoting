<?php

require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/../src/PhotoStates.php';

class Render {

  protected $dbh;
  protected $pstates;
  const IMG_PREFIX = "./img/";

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
    if ($roll > 0 && $roll < 33) {
      // hot-or-not two-off, non-group
      $postfix = "$?n=2&group=no";
    } elseif ($roll >= 33 and $roll < 66) {
      // hot-or-not two-off, group
      $postfix = "$?n=2&group=rand";
    } elseif ($roll >=66) {
      // hot-or-not N-off, non-group
      $postfix = "?n=4&group=no";
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

  function displayHeader() {
    echo('<!DOCTYPE html>
    <html>

    <head>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <link rel="stylesheet" href="./style.css">
      <meta charset="utf-8">
      <title></title>
    </head>

    <body>
      <h1>Photo Voting Service</h1>');
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

  function displayImageByFilename($filename) {
    // Nuke this odbc_setoption
    $real_filename = self::IMG_PREFIX . pathinfo($filename)['filename'] .
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

    $this->displayImageByFilename($path);
  }

}

//$R = new Render();
//header('Location: $url');
