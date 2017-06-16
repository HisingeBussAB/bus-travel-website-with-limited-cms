<?php

namespace HisingeBussAB\RekoResor\website\admin\includes\pages;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\admin as admin;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;
use HisingeBussAB\RekoResor\website\includes\classes\Functions as functions;

class TestFiles {

  public static function start() {
    functions::render_var_dump(functions::get_string_between("<h3>H3</h3>", "<h3>", "</h3>"), "H");
    functions::render_var_dump(functions::get_string_between("<h3></h3>", "<h3>", "</h3>"), "EMT H3");
    functions::render_var_dump(functions::get_string_between("<p>P</p>", "<p>", "</p>"), "P");
    functions::render_var_dump(functions::get_string_between("<p></p>", "<p>", "</p>"), ">EMT PH3");
    functions::render_var_dump(functions::get_string_between("<h3>h1</h3><p></p>", "<p>", "</p>"), "H and empty P");
    echo "<br><br>H3 P H3 P...<br>";
    functions::render_var_dump(functions::get_string_between("<h3>h1</h3><p>p1</p><h3>h2</h3><p>p2</p><h3>h3</h3><p>p3</p>", "<h3>", "</h3>"), "H RESULTS");
    functions::render_var_dump(functions::get_string_between("<h3>h1</h3><p>p1</p><h3>h2</h3><p>p2</p><h3>h3</h3><p>p3</p>", "<p>", "</p>"), "P RESULTS");


/*
    $upload_dir = __DIR__ . '/../../../upload/resor/test-foldersscan/';
    $web_path = "http" . APPEND_SSL . "://rekoresor.busspoolen.se/upload/resor/test-foldersscan/";


    $files = scandir($upload_dir);
    var_dump($files);

    echo "<br>";

    foreach($files as $file) {
      if (substr($file, 0, 1) != ".") {
        echo "<br>";
        var_dump($file);
        echo "<br>";
        var_dump(filetype($upload_dir . $file));

        echo "<br>";
        var_dump(mime_content_type($upload_dir . $file));

        if (strpos(mime_content_type($upload_dir . $file), "image") !== FALSE) {
          echo "<a href='" . $web_path  . $file . "'><img src='" . $web_path  . $file . "'></a>";


        }
      }
    }*/
  }

}
