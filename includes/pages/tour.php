<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * (c) Rekå Resor AB
 *
 * @link      https://github.com/HisingeBussAB/bus-travel-website-with-limited-cms
 * @copyright CC BY-SA 4.0 (http://creativecommons.org/licenses/by-sa/4.0/)
 * @license   GNU General Public License v3.0
 * @author    Håkan Arnoldson
 */
namespace HisingeBussAB\RekoResor\website\includes\pages;
use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\Functions;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;



try {
  $toururl = filter_var(trim($toururl), FILTER_SANITIZE_URL);
  try {
    $pdo = DB::get();

    $sql = "SELECT * FROM " . TABLE_PREFIX . "resor WHERE aktiv = 1 AND url = :url;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':url', $toururl, \PDO::PARAM_STR);
    $sth->execute();
    $tour = $sth->fetch(\PDO::FETCH_ASSOC);
  } catch(\PDOException $e) {
    include __DIR__ . '/shared/header.php';
    DBError::showError($e, __CLASS__, $sql);
    throw new \RuntimeException("Databasfel.");
  }

    if (count($tour) > 0) {
      $tourid = $tour['id'];
      $heading = $tour['namn'];
      $text = $tour['ingress'];
    } else {
      include __DIR__ . '/shared/header.php';
      throw new \UnexpectedValueException("Resan finns inte");
    }


$pageTitle = $heading;

header('Content-type: text/html; charset=utf-8');
include __DIR__ . '/shared/header.php';

echo "<main class='main-section clearfix container'>";
  echo "<div class='col-md-8'>
    <h1>$heading</h1>
    <p>$text</p></div>";


  echo "<div class='col-md-4'>Other column</div>";





echo "</main>";

include __DIR__ . '/shared/footer.php';

} catch(\RuntimeException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/500.php';
}

catch(\UnexpectedValueException $e) {
 if (DEBUG_MODE) echo $e->getMessage();
 include 'error/404.php';
}
