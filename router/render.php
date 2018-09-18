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

    //LIST OF STANDARD PAGES

    if (empty($target)) $target = "mainpage";

    if ($target === "bestall-program")    $target = "orderprogram";
    if ($target === "inforresan")         $target = "beforetour";
    if ($target === "efterresan")         $target = "aftertour";
    if ($target === "bussresorgoteborg")  $target = "about";
    if ($target === "kontakt")            $target = "contact";
    if ($target === "resevillkor")        $target = "terms";
    if ($target === "galleri")            $target = "gallery";

    //END LIST OF STANDARD PAGES
    //START CAMPAIGNS
    if ($target === "julbord")            $target = "campaign1";
    //END CAMPAIGNS

    try {
      include __DIR__ . '/../includes/pages/' . $target . '.php';
    } catch (Exception $e) {
      if (DEBUG_MODE) echo $e->getMessage(); else include '/../includes/pages/error/404.php';
    }
  }

  public static function landing($target) {

    if (empty($target)) $target = "mainpage";

    try {
      include __DIR__ . '/../includes/pages/landingp' . $target . '.php';
    } catch (Exception $e) {
      if (DEBUG_MODE) echo $e->getMessage(); else include '/../includes/pages/error/404.php';
    }
  }

  public static function category($cat) {

    try {
      include __DIR__ . '/../includes/pages/category.php';
    } catch (Exception $e) {
      if (DEBUG_MODE) echo $e->getMessage(); else include __DIR__ . '/../includes/pages/error/404.php';
    }
  }

  public static function tour($toururl) {
    try {
      include __DIR__ . '/../includes/pages/tour.php';
    } catch (Exception $e) {
      if (DEBUG_MODE) echo $e->getMessage(); else include __DIR__ . '/../includes/pages/error/404.php';
    }
  }

  public static function booktour($toururl) {

    try {
      include __DIR__ . '/../includes/pages/booking.php';
    } catch (Exception $e) {
      if (DEBUG_MODE) echo $e->getMessage(); else include __DIR__ . '/../includes/pages/error/404.php';
    }
  }

  public static function ordertourprogram($toururl) {

    try {
      include __DIR__ . '/../includes/pages/orderprogram.php';
    } catch (Exception $e) {
      if (DEBUG_MODE) echo $e->getMessage(); else include __DIR__ . '/../includes/pages/error/404.php';
    }
  }
}
