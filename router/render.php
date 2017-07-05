<?php
/**
 * Helper class to render HTML and semi-HTML docs
 */

namespace HisingeBussAB\RekoResor\website\router;
use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

class Render
{

  /**
   * Will include the file given as param
   * @uses DEBUG_MODE
   * @param string $target path and file to include
   */
  public static function inc($target) {

    if (empty($target)) $target = "mainpage";

    try {
      include __DIR__ . '/../includes/pages/' . $target . '.php';
    } catch (Exception $e) {
      if (DEBUG_MODE) echo $e->getMessage(); else include '/../includes/pages/error/404.php';
    }
  }
}
