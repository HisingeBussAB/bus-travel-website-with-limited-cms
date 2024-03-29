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


try {

  $allowed_tags = ALLOWED_HTML_TAGS;

  root\includes\classes\Sessions::secSessionStart(TRUE);
  $token = root\includes\classes\Tokens::getFormToken('program', 2000, true);
  $clienthash = md5($_SERVER['HTTP_USER_AGENT']);
  $html_ents = Functions::set_html_list();
  $tour['id'] = '';
  $tour['namn'] = '';
  $tour['url'] = '';
  $tour['datum'] = '';
  $tour['antaldagar'] = 0;
  if (!empty($toururl)) {
    $toururl = str_replace("'", "", $toururl); //Is urlencoded there should not be any ' and they will break the html if value is echoed and user enters a malicious query
    $toururl = filter_var(trim($toururl), FILTER_SANITIZE_URL);
    try {
      $pdo = DB::get();
      $sql = "SELECT resor.id as id, namn, url, datum.datum as datum, datum.antaldagar as antaldagar FROM " . TABLE_PREFIX . "resor as resor
        LEFT JOIN " . TABLE_PREFIX . "datum as datum on datum.resa_id = resor.id
        WHERE aktiv = 1 AND url = :url LIMIT 1;";
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
      $tour['namn'] = strtr(strip_tags($result['namn'], $allowed_tags), $html_ents);
      $tour['url'] = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/" . rawurlencode($result['url']), FILTER_SANITIZE_URL);

    } else {
      throw new \UnexpectedValueException("Resan finns inte.");
    }

    $pageTitle = "Beställ program för " . $tour['namn'];
    $dataLayerString = "'content_ids': ['" . $tour['id'] . "'],
    'travel_start': '" . $tour['datum'] . "',
    'travel_end': '" . date("Y-m-d", strtotime($tour['datum'] . " +" . $tour['antaldagar'] . " days")) . "',
    'destination_catalog_id': '268103017365451',
    'product_catalog_id': '576404639873615',
    'product': '" . html_entity_decode($tour['namn']) . "',
    ";

  } else {
    $pageTitle = "Beställ katalog";
    $dataLayerString = "";
  }

  $dataLayer = "{
    'pageTitle': 'Order_Program',
    'visitorType': 'medium-value',
    " . $dataLayerString . "
    }";

  $morestyles = "<link rel='stylesheet' href='/css/program.min.css' >";
  $morescripts = "<script src='/js/program.js'></script>";

  header('Content-type: text/html; charset=utf-8');
  include __DIR__ . '/shared/header.php';

  echo "<main class='main-section container-fluid'>";

  echo "<div class='row-fluid'>";


      echo "
      <form action='/ajax/program' method='post' accept-charset='utf-8' enctype='application/json' id='get-program-form'>";

      if (empty($toururl)) {
        echo "
        <h1>Beställ program</h1>
        <p>Vi producerar kontinuerligt nya resor för att hålla vårt utbud uppdaterat och relevant.
        Här kan du beställa tryckta reseprogram för våra nu aktuella resor. Vi skickar dem via post till dig.</p>";
      } else {
        echo "
        <h1>Beställ tryckt program för " . $tour['namn'] . "</h1>
        <p>Vi skickar programmet via post till dig.</p>
        <input type='hidden' value='" . $tour['namn'] . "' name='category[]' />
        <input type='hidden' value='" . $tour['id'] . "' name='tourid' />";
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

      $i = 1;
      if (empty($toururl)) {
        echo "<h2>Välj program</h2>";
        echo "<ul><li><input type='checkbox' id='check0'name='category[]' value='Alla program' checked /><label class='checklabel' for='check0'>Alla program</label></li>";
        foreach($categories as $category) {
          echo "<li><input type='checkbox' id='check" . $i . "' name='category[]' value='" . htmlspecialchars($category->kategori, ENT_QUOTES) . "' /><label class='checklabel' for='check" . $i . "'>" . htmlspecialchars($category->kategori, ENT_QUOTES) . "</label></li>";
          $i++;
        }
        echo "</ul>";
      }
      echo "<h4>Godkänn villkor</h4>";
      echo "<ul class='termsBox'><li><input type='checkbox' name='terms' id='terms' value='ja' required /><label for='terms' id='terms-label'>Ja, jag godkänner <a href='/resevillkor/' target='_blank'>behandling av personuppgifter.</a></label></li></ul>";

      echo "<p><input type='submit' value='Beställ program' id='get-program-button' /><button class='ajax-loader'><i class='fa fa-spinner fa-pulse fa-2x' aria-hidden='true'></i></button></p>
      <div class='ajax-response' id='ajax-response'></div>
      ";
      ?>
      <div id='recaptcha-body' class="g-recaptcha"
                  data-sitekey="<?php echo INV_RECAPTCHA_PUBLIC; ?>"
                  data-callback="onVerifyForm"
                  data-size="invisible"
                  data-badge='inline'></div>
      <?php


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
