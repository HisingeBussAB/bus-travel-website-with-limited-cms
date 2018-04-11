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

function generateIndividualRoomSelectors($i) {
  $return = "";
  $return = "<select></select>";
}

try {
  root\includes\classes\Sessions::secSessionStart(TRUE);
  $token = root\includes\classes\Tokens::getFormToken('book', 2500, true);
  $clienthash = md5($_SERVER['HTTP_USER_AGENT']);
  if (empty($toururl)) { throw new \UnexpectedValueException("Ingen resa vald."); }

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
    $tour['pris'] = number_format(filter_var($result['pris'], FILTER_SANITIZE_NUMBER_INT), 0, ",", " ");
    $tour['pris-int'] = filter_var($result['pris'], FILTER_SANITIZE_NUMBER_INT);
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
    $sql = "SELECT datum FROM " . TABLE_PREFIX . "datum WHERE resa_id = :id ORDER BY datum;";
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
      $tour['stops'][$i]['tid'] = date('H:i', strtotime($row['tid_ut']));
      $tour['stops'][$i]['plats'] = htmlspecialchars($row['plats']);
      $tour['stops'][$i]['ort'] = htmlspecialchars($row['ort']);
      $i++;
    }
  }


  try {
    $sql = "SELECT boende, pris FROM " . TABLE_PREFIX . "boenden AS b
      LEFT OUTER JOIN " . TABLE_PREFIX . "boenden_resor AS b_r ON b.id = b_r.boenden_id WHERE resa_id = :tourid ORDER BY pris;";
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



  $pageTitle = "Boka resa " . $tour['namn'];

  $morestyles = "<link rel='stylesheet' href='/css/booking.css' >";
  $morescripts = "<script src='/js/booking.js'></script>";

  $dataLayer = "{
    'pageTitle': 'Booking_Page',
    'visitorType': 'high-value',
    'product': '" + $tour['namn'] + "',
    }";

  header('Content-type: text/html; charset=utf-8');
  include __DIR__ . '/shared/header.php';

  echo "<main class='main-section container-fluid'>";

  echo "<div class='row-fluid'>";

      echo "
      <form action='/ajax/booktour' method='post' accept-charset='utf-8' enctype='application/json' id='booktour-form'>";

        echo "
        <h1>Boka " . $tour['namn'] . "</h1>";
        if ($tour['fysiskadress']) {
          echo "<p>Vi skickar bekräftelse och inbetalningskort till dig med posten.</p>";
        } else {
          echo "<p>Vi skriver upp dig på påstigningslistan. Resan betalas kontant på bussen.</p>";
        }
        echo "<input type='hidden' value='" . $tour['namn'] . "' name='tour' />";
        echo "<input type='hidden' value='" . $tour['pris-int'] . "' name='price-int' id='price-int'>";

      if (!empty($tour['departures'])) {
        echo "<h3>Avgångsdatum:</h3>";
        echo "<ul>";
        if (count($tour['departures']) > 1) {

          foreach ($tour['departures'] as $departures) {
            echo "<li><input type='radio' name='departure' value='" . $departures . "' id='" . $departures . "' required />";
            echo "<label for='" . $departures  . "'>" . $departures . "</label></li>";
          }

        } else {
          echo "<li><input type='radio' name='departure' value='" . $tour['departures'][0] . "' id='" . $tour['departures'][0] . "' required' checked />";
          echo "<label for='" . $tour['departures'][0]  . "'>" . $tour['departures'][0] . "</label></li>";
        }



        echo "</ul>";
      }


      if (!empty($tour['stops'])) {
        echo "<h3>Påstigningsplats:</h3>";
        echo "<ul>";

          foreach ($tour['stops'] as $stops) {

            echo "<li><input type='radio' name='stop' value='" . $stops['ort'];
            if (!empty($stops['plats'])) { echo ", " . $stops['plats']; }
            echo "' id='" . $stops['plats'] . $stops['ort'] . "' required />";
            echo "<label for='" . $stops['plats'] . $stops['ort']  . "'>" . $stops['ort'];
            if (!empty($stops['plats'])) { echo ", " . $stops['plats']; }
            echo " - kl " . $stops['tid'] . "</label></li>";
          }

        echo "</ul>";
      }


      if (!empty($tour['roomopts'])) {
        echo "<h3>Boende:</h3>";
        echo "<ul>";
        foreach ($tour['roomopts'] as $roomopts) {
          echo "<li><input type='radio' name='room' value='" . $roomopts['boende'] . "' id='" . $roomopts['boende'] . "' required />";
          echo "<label for='" . $roomopts['boende']  . "'>" . $roomopts['boende'] . " - " . $roomopts['pris'] . " kr</label></li>";
        }
        echo "</ul>";
      }




      echo "
      <h3>Dina uppgifter</h3>
      <p><input type='text' placeholder='Namn' name='name' required /></p>";
      if ($tour['fysiskadress']) {
        echo "<p><input type='text' placeholder='Gatuadress' name='address' required='required' /></p>
              <p><input type='text' placeholder='Postnr.' name='zip' required /><input type='text' placeholder='Postort' name='city' required='required' /></p>";
      }
      echo "<p><input type='email' placeholder='E-post' name='email' /></p>
      <p><input type='tel' placeholder='Telefon' name='phone' ";
      if (!$tour['fysiskadress']) { echo "required "; }
      echo "/></p>";

      echo "<h3>Resenärer</h3>
      <p>Ange namnen på alla resenärer inkusive dig själv.";
      if ($tour['personnr']) { echo "<br />Födelsedatum behöver anges på den här resan då den inkluderar färja eller liknande."; }
      echo "</p>";

      echo "<ul><li><input type='text' name='resenar1' placeholder='Resenär 1' maxlength='120' required />";
      if ($tour['personnr']) { echo "<input type='text' name='resenar1-pnr' placeholder='Fördelsedatum för resenär 1' maxlength='16' required />"; }
      echo "</li><li><input type='text' name='resenar2' placeholder='Resenär 2' maxlength='120' />";
      if ($tour['personnr']) { echo "<input type='text' name='resenar2-pnr' placeholder='Fördelsedatum för resenär 2' maxlength='16' />"; }
      echo "</li><li><input type='text' name='resenar3' placeholder='Resenär 3' maxlength='120' />";
      if ($tour['personnr']) { echo "<input type='text' name='resenar3-pnr' placeholder='Fördelsedatum för resenär 3' maxlength='16' />"; }
      echo "</li><li><input type='text' name='resenar4' placeholder='Resenär 4' maxlength='120' />";
      if ($tour['personnr']) { echo "<input type='text' name='resenar4-pnr' placeholder='Fördelsedatum för resenär 4' maxlength='16' />"; }
      echo "</li><li><input type='text' name='resenar5' placeholder='Resenär 5' maxlength='120' />";
      if ($tour['personnr']) { echo "<input type='text' name='resenar5-pnr' placeholder='Fördelsedatum för resenär 5' maxlength='16' />"; }
      echo "</li><li><input type='text' name='resenar6' placeholder='Resenär 6' maxlength='120' />";
      if ($tour['personnr']) { echo "<input type='text' name='resenar6-pnr' placeholder='Fördelsedatum för resenär 6' maxlength='16' />"; }
      echo "</li><li><small>För bokning av större grupper vänligen ring 031 - 22 21 20</small></li></ul>";

      echo "<input type='hidden' value='" . $token['id'] . "' name='tokenid' id='tokenid' />
      <input type='hidden' value='" . $token['token'] . "' name='token' id='token' />
      <input type='hidden' value='$clienthash' name='client' />
      <p class='antispam'>Leave this empty: <input type='text' name='url' /></p>";

      echo "<h3>Villkor</h3>";
      echo "<ul><li>";
      echo "<input type='checkbox' name='terms' id='terms' value='ja' /><label for='terms' id='terms-label'>Ja, jag godkänner <a href='/resevillkor/' target='_blank'>resevillkoren</a>.</label>";
      echo "</li></ul>";
      echo "<h3>Övriga önskemål/frågor</h3>";
      echo "<textarea maxlength='800' name='misc' placeholder='Eventuella övriga önskemål eller frågor.'></textarea>";
      echo "<p><input type='submit' value='Skicka bokning' id='booktour-button' /><span class='ajax-loader'><i class='fa fa-spinner fa-pulse fa-2x' aria-hidden='true'></i></span></p>
      <div class='ajax-response' id='ajax-response'></div>
      ";



  echo "</form></div>";
  echo "</main>";

  include __DIR__ . '/shared/footer.php';


} catch(\UnexpectedValueException $e) {


  try {
    $allowed_tags = ALLOWED_HTML_TAGS;
    $html_ents = Functions::set_html_list();
    $pdo = DB::get();

    $sql = "SELECT resor.namn, resor.url FROM " . TABLE_PREFIX . "resor AS resor
            LEFT OUTER JOIN " . TABLE_PREFIX . "datum AS datum ON resor.id = datum.resa_id
            LEFT OUTER JOIN " . TABLE_PREFIX . "kategorier_resor AS k_r ON resor.id = k_r.resa_id
            LEFT OUTER JOIN " . TABLE_PREFIX . "kategorier AS kategorier ON kategorier.id = k_r.kategorier_id
            WHERE kategorier.kategori != 'gruppresor' AND resor.aktiv = 1 AND datum > NOW()
            GROUP BY resor.id
            ORDER BY datum;";



    $sth = $pdo->prepare($sql);
    $sth->execute();
    $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    $errorType = "Databasfel";
    throw new \RuntimeException("Databasfel vid laddning av resor.");
  }

    $pageTitle = "Boka resa";

    $morestyles = "<link rel='stylesheet' href='/css/booking.min.css' >";

    header('Content-type: text/html; charset=utf-8');
    include __DIR__ . '/shared/header.php';

    echo "<main class='main-section container-fluid'>";
    echo "<div class='row-fluid'><div class='col-xs-12'>";

    echo "<h1>Boka resa</h1>";
    echo "<h4>Vänligen välj vilken resa Du vill åka på:<h4>";


    foreach($result as $tour) {

      $thistour['tour'] = strtr(strip_tags($tour['namn'], $allowed_tags), $html_ents);
      $thistour['link'] = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/boka/". str_replace("'", "", $tour['url']), FILTER_SANITIZE_URL);
      echo "<p><a href='" . $thistour['link'] . "'>" . $thistour['tour'] . "</a></p>";
    }



    echo "</div></div></main>";
    include __DIR__ . '/shared/footer.php';


} catch(\RuntimeException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/500.php';
}
