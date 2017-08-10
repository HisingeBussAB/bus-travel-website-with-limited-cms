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

root\includes\classes\Sessions::secSessionStart(TRUE);
$token = root\includes\classes\Tokens::getFormToken('program', 2500, true);
$clienthash = md5($_SERVER['HTTP_USER_AGENT']);


try {
  $toururl = filter_var(trim($toururl), FILTER_SANITIZE_URL);
  try {
    $pdo = DB::get();

    $sql = "SELECT id, namn, pris, personnr, fysiskadress, url FROM " . TABLE_PREFIX . "resor WHERE aktiv = 1 AND url = :url;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':url', $toururl, \PDO::PARAM_STR);
    $sth->execute();
    $result = $sth->fetch(\PDO::FETCH_ASSOC);
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    throw new \RuntimeException("Databasfel.");
  }

  if ((count($result) > 0) && ($result !== false)) {
    $tourid = filter_var($result['id'], FILTER_SANITIZE_NUMBER_INT);
    $tour['namn'] = htmlspecialchars($result['namn']);
    $tour['url'] = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/" . rawurlencode($result['url']);
    $tour['pris'] = filter_var($result['pris'], FILTER_SANITIZE_NUMBER_INT);
    $tour['personnr'] = filter_var($result['personnr'], FILTER_VALIDATE_BOOLEAN);
    $tour['fysiskadress'] = filter_var($result['fysiskadress'], FILTER_VALIDATE_BOOLEAN);
  } else {
    throw new \UnexpectedValueException("Resan finns inte.");
  }

  try {
    $sql = "SELECT namn, pris FROM " . TABLE_PREFIX . "tillaggslistor WHERE resa_id = :id;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':id', $tourid, \PDO::PARAM_STR);
    $sth->execute();
    $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    throw new \RuntimeException("Databasfel.");
  }

  if ((count($result) > 0) && ($result !== false)) {
    $i = 0;
    foreach ($result as $row) {
      $tour['addons'][$i]['namn'] = htmlspecialchars($row['namn']);
      $tour['addons'][$i]['pris'] = filter_var($row['pris'], FILTER_SANITIZE_NUMBER_INT);
      $i++;
    }
  }

  try {
    $sql = "SELECT datum FROM " . TABLE_PREFIX . "datum WHERE resa_id = :id;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':id', $tourid, \PDO::PARAM_STR);
    $sth->execute();
    $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    throw new \RuntimeException("Databasfel.");
  }

  if ((count($result) > 0) && ($result !== false)) {
    $i = 0;
    foreach ($result as $row) {
      $tour['departures'][$i] = date('Y-m-d', strtotime($row['datum']));
      $i++;
    }
  }


  try {
    $sql = "SELECT plats, ort, tid_ut FROM " . TABLE_PREFIX . "hallplatser AS hlp
      LEFT OUTER JOIN " . TABLE_PREFIX . "hallplatser_resor AS hlp_r ON hlp.id = hlp_r.hallplatser_id WHERE resa_id = :tourid
      ORDER BY tid_ut;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':tourid', $tourid, \PDO::PARAM_STR);
    $sth->execute();
    $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    throw new \RuntimeException("Databasfel.");
  }

  if ((count($result) > 0) && ($result !== false)) {
    $i = 0;
    foreach ($result as $row) {
      $tour['stops'][$i]['tid'] = date('H:m', strtotime($row['tid_ut']));
      $tour['stops'][$i]['plats'] = htmlspecialchars($row['plats']);
      $tour['stops'][$i]['ort'] = htmlspecialchars($row['ort']);
      $i++;
    }
  } else {
    $tour['departures'][0] = [];
  }


  try {
    $sql = "SELECT boende, pris FROM " . TABLE_PREFIX . "boenden AS b
      LEFT OUTER JOIN " . TABLE_PREFIX . "boenden_resor AS b_r ON b.id = b_r.boenden_id WHERE resa_id = :tourid;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':tourid', $tourid, \PDO::PARAM_STR);
    $sth->execute();
    $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    throw new \RuntimeException("Databasfel.");
  }

  if ((count($result) > 0) && ($result !== false)) {
    $i = 0;
    foreach ($result as $row) {
      $tour['roomopts'][$i]['boende'] = htmlspecialchars($row['boende']);
      $tour['roomopts'][$i]['pris'] = filter_var($row['pris'], FILTER_SANITIZE_NUMBER_INT);
      $i++;
    }
  }


  var_dump($tour);



  $pageTitle = "Boka resa " . $tour['namn'];

  $morestyles = "<link rel='stylesheet' href='/css/booking.min.css' >";
  $morescripts = "<script src='/js/booking.js'></script>";

  header('Content-type: text/html; charset=utf-8');
  include __DIR__ . '/shared/header.php';

  echo "<main class='main-section container'>";

  echo "<div class='row'>";


      echo "
      <form action='/ajax/booktour' method='post' accept-charset='utf-8' enctype='application/json' id='booktour-form'>";

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
      <p><input type='text' placeholder='Namn' name='name' required /></p>
      <p><input type='text' placeholder='Gatuadress' name='address' required /></p>
      <p><input type='text' placeholder='Postnr.' name='zip' required /><input type='text' placeholder='Postort' name='city' required /></p>
      <p><input type='email' placeholder='E-post' name='email' /></p>
      <input type='hidden' value='" . $token['id'] . "' name='tokenid' id='tokenid' />
      <input type='hidden' value='" . $token['token'] . "' name='token' id='token' />
      <input type='hidden' value='$clienthash' name='client' />
      <p class='antispam'>Leave this empty: <input type='text' name='url' /></p>";


      if (empty($toururl)) {
        echo "<h3>Välj program</h3>";
        echo "<ul><li><input type='checkbox' name='category[]' value='Alla program' checked />Hela katalogen (alla program)</li>";
        foreach($categories as $category) {
          echo "<li><input type='checkbox' name='category[]' value='" . htmlspecialchars($category->kategori) . "' />" . htmlspecialchars($category->kategori) . "</li>";
        }
        echo "</ul>";
      }



      echo "<p><input type='submit' value='Skicka' id='get-program-button' /><span class='ajax-loader'><i class='fa fa-spinner fa-pulse fa-2x' aria-hidden='true'></i></span></p>
      <div class='ajax-response' id='ajax-response'><div>
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
