<?php

namespace HisingeBussAB\RekoResor\website\admin\includes\pages;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\admin as admin;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

class TestFiles {

  public static function start() {


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
    }
  }

}
