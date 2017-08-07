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

root\includes\classes\Sessions::secSessionStart(TRUE);
$token = root\includes\classes\Tokens::getFormToken('program', 3600, true);
$clienthash = md5($_SERVER['HTTP_USER_AGENT']);


try {
  if (!empty($toururl)) {
    $toururl = filter_var(trim($toururl), FILTER_SANITIZE_URL);
    try {
      $pdo = DB::get();

      $sql = "SELECT namn, url FROM " . TABLE_PREFIX . "resor WHERE aktiv = 1 AND url = :url;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':url', $toururl, \PDO::PARAM_STR);
      $sth->execute();
      $result = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      throw new \RuntimeException("Databasfel.");
    }

    if ((count($result) > 0) && ($result !== false)) {
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
  $morescripts = "<script src='/js/program.js'></script>";

  header('Content-type: text/html; charset=utf-8');
  include __DIR__ . '/shared/header.php';

  echo "<main class='main-section container'>";

  echo "<div class='row'>";


      echo "
      <form action='/ajax/program' method='post' accept-charset='utf-8' enctype='application/json'>";

      if (empty($toururl)) {
        echo "
        <h1>Beställ katalog.</h1>
        <p>Beställ vår tryckta katalog, eller delar av katalogen. Vi skickar dem via post till dig.</p>";
      } else {
        echo "
        <h1>Beställ tryckt program för " . htmlspecialchars($tour['namn']) . "</h1>
        <p>Vi skickar programmet via post till dig.</p>
        <input type='hidden' value='" . htmlspecialchars($tour['namn']) . "' name='category[]' />";
      }



      echo "
      <p><input type='text' placeholder='Namn' name='name' /></p>
      <p><input type='text' placeholder='Gatuadress' name='address' /></p>
      <p><input type='text' placeholder='Postnr.' name='zip' /><input type='text' placeholder='Postort' name='city' /></p>
      <p><input type='email' placeholder='E-post' name='e-mail' /></p>
      <input type='hidden' value='" . $token['id'] . "' name='tokenid' />
      <input type='hidden' value='" . $token['token'] . "' name='token' />
      <input type='hidden' value='$clienthash' name='client' />
      <p class='antispam'>Leave this empty: <input type='text' name='url' /></p>";


      if (empty($toururl)) {
        echo "<h3>Välj program</h3>";
        echo "<ul><li><input type='checkbox' name='category[]' value='Alla program' />Hela katalogen (alla program)</li>";
        foreach($categories as $category) {
          echo "<li><input type='checkbox' name='category[]' value='" . htmlspecialchars($category->kategori) . "' />" . htmlspecialchars($category->kategori) . "</li>";
        }
        echo "</ul>";
      }



      echo "<p><input type='submit' value='Skicka' /><span class='ajax-loader'></span></p>
      <div class='ajax-response'><div>
      ";



  echo "</form></div>";
  echo "</main>";

  include __DIR__ . '/shared/footer.php';


} catch(\UnexpectedValueException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/404.php';
} catch(\RuntimeException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/500.php';
}
