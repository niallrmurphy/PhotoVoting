<?php

require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/../src/PhotoStates.php';

class Render {

  function __construct($mydbh = null) {
    if ($mydbh === null) {
      $this->db = new MyDB();
      // error_log("LOG: default test DB opening\n");
    } else {
      $this->db = $mydbh;
    }
    $this->pstates = new PhotoStates();
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
    header('Location: $url');
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

  function display_header() {
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

  function image_display_block() {
    echo('
      <img src="https://picsum.photos/800/500/?image=1" id="photoItem" />
      <div id="button-div">
        <button class="unpicked fa fa-thumbs-o-up" id="upbtn">  </button>
        <button class="unpicked fa fa-thumbs-o-down" id="downbtn">  </button>
        <div id="voteTally"></div>
      </div>');
  }

}

$R = new Render();
