<?php
/**
 * Helper class to render HTML and semi-HTML docs
 */

namespace HisingeBussAB\RekoResor\website\router;

class Render
{

  /**
   * Will include the file given as param
   * @uses DEBUG_MODE
   * @param string $target path and file to include
   */
  public static function inc($target) {
    try {
      include __DIR__ . '/..' . $target;
    } catch (Exception $e) {
      if (DEBUG_MODE) echo $e->getMessage(); else include '/../includes/pages/error/404.php';
    }
  }
}
