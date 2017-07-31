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
use HisingeBussAB\RekoResor\website\includes\classes\Functions as functions;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

$allowed_tags = ALLOWED_HTML_TAGS;

try {
  if (!empty($toururl)) {
    $toururl = filter_var(trim($toururl), FILTER_SANITIZE_URL);
    try {
      $pdo = DB::get();

      $sql = "SELECT * FROM " . TABLE_PREFIX . "resor WHERE aktiv = 1 AND url = :url;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':url', $toururl, \PDO::PARAM_STR);
      $sth->execute();
      $result = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      throw new \RuntimeException("Databasfel.");
    }

    if ((count($result) > 0) && ($result !== false)) {
      $tour['id'] = filter_var($result['id'], FILTER_SANITIZE_NUMBER_INT);
      $tour['namn'] = strip_tags($result['namn'], $allowed_tags);
      $tour['url'] = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/" . rawurlencode($result['url']);

    } else {
      throw new \UnexpectedValueException("Resan finns inte.");
    }

    $pageTitle = "Beställ program för " . $tour['namn'];

  } else {
    $pageTitle = "Beställ katalog";
  }



  $morestyles = "<link rel='stylesheet' href='/css/program.min.css' >";

  header('Content-type: text/html; charset=utf-8');
  include __DIR__ . '/shared/header.php';

  echo "<main class='main-section container'>";

  echo "<input type='checkbox' name=category[] value='Alla program'>Hela katalogen (alla program)</option>";
  foreach($categories as $category){
  echo "<input type='checkbox' name=category[] value='" . htmlspecialchars($category->kategori) . "'>" . htmlspecialchars($category->kategori) . "</option>";
 }

  echo "<div class='row'>";

    if (!empty($toururl)) {
      echo "TOUR SET!";
    } else {
      echo "TOUR NOT SET!";
    }


  echo "</div>";
  echo "</main>";

  include __DIR__ . '/shared/footer.php';


} catch(\UnexpectedValueException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/404.php';
} catch(\RuntimeException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/500.php';
}
