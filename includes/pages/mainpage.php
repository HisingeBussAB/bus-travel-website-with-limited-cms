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

$pageTitle = "Bussresor i Norden och Europa";
$allowed_tags = ALLOWED_HTML_TAGS;

try {

header('Content-type: text/html; charset=utf-8');
include __DIR__ . '/shared/header.php';


  try {
    $pdo = DB::get();

    $sql = "SELECT id, namn, url, bildkatalog, antaldagar, ingress, pris, utvald, seo_description, MIN(datum.datum) AS datum FROM " . TABLE_PREFIX . "resor AS resor INNER JOIN " . TABLE_PREFIX . "datum AS datum ON resor.id = datum.resa_id WHERE aktiv = 1 GROUP BY resor.id ORDER BY datum;";
    $sth = $pdo->prepare($sql);
    $sth->execute();
    $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    $errorType = "Databasfel";
    throw new \RuntimeException("Databasfel vid laddning av resor.");
  }

  $featured = [];
  $featuredset = FALSE;
  $tours = [];

  $i=0;
  foreach($result as $tour) {
    if ($tour['utvald'] && !$featuredset) {
      $featured['link']    = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/". rawurlencode($tour['url']);
      $featured['tour']    = strip_tags($tour['namn'], $allowed_tags);
      $featured['desc']    = strip_tags($tour['seo_description'], $allowed_tags);
      $featured['imgpath'] = $tour['bildkatalog'];
      $server_path = __DIR__ . '/../../upload/resor/' . $tour['bildkatalog'] . '/';
      $web_path = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/" . rawurlencode($tour['bildkatalog']) . "/";
        if ($files = functions::get_img_files($server_path)) {
          $featured['imgpath'] = $web_path . $files[0]['thumb'];
        } else {
          $featured['imgpath'] = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/generic/small_1_generic.jpg";
        }
      $featuredset = TRUE;
    }

    $tours[$i]['tour'] = strip_tags($tour['namn'], $allowed_tags);
    $tours[$i]['link'] = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/". rawurlencode($tour['url']);
    $tours[$i]['days'] = strip_tags($tour['antaldagar'], $allowed_tags);
    $tours[$i]['summary'] = nl2br(strip_tags($tour['ingress'], $allowed_tags));
    $tours[$i]['price'] = strip_tags($tour['pris'], $allowed_tags);
    $tours[$i]['departure'] = strip_tags($tour['datum'], $allowed_tags);

    $server_path = __DIR__ . '/../../upload/resor/' . $tour['bildkatalog'] . '/';
    $web_path = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/" . rawurlencode($tour['bildkatalog']) . "/";
      if ($files = functions::get_img_files($server_path)) {
        $tours[$i]['imgsrc'] = $web_path . $files[0]['thumb'];
      } else {
        $tours[$i]['imgsrc'] = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/generic/small_1_generic.jpg";
      }
    $i++;
  }

  try {
    $sql = "SELECT nyheter FROM " . TABLE_PREFIX . "nyheter WHERE id = 1;";
    $sth = $pdo->prepare($sql);
    $sth->execute();
    $result = $sth->fetch(\PDO::FETCH_ASSOC);
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    throw new \RuntimeException("Databasfel vid laddning av nyheter.");
  }


?>
<main class="main-section clearfix container">
  <div class="row">
  <section class="col-md-6 col-xs-12 clearfix">
    <h1>Välkommen till Rekå Resor</h1>
    <p>För att genomföra en bra bussresa så krävs det planering och genomförande, oavsett om den ska gå inom Sverige eller ut i Europa.
    Det är där vi på Rekå Resor kommer in i bilden. Vi lyssnar på de önskemål du har och bidrar sedan med råd och idéer för bästa möjliga resultat.
    Med mer än 60 år i branschen har vi både erfarenheten såväl som kontaktnätet och det gör att vi kan ta fram i princip vilka gruppresor som helst –
    från korta endagsresor i närområdet runt Göteborg till veckolånga resor runt om i Europa. Alla bussresor kryddas med det lilla extra.</p>
  </section>
  <a href="<?php echo $featured['link']; ?>"><div class="col-md-6 col-xs-12 trip-featured" style="background-image: url('<?php echo $featured['imgpath']; ?>')">
    <h2 class="invisible">Månadens resa</h2>
    <h3 aria-label="<?php echo $featured['tour']; ?>"><?php echo $featured['desc']; ?><i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></h3>
  </div></a>
  </div>

  <section class="row">
    <h2>Aktuellt från Rekå Resor</h2>
    <p><?php echo nl2br(strip_tags($result['nyheter'], $allowed_tags)); ?></p>
  </section>

  <div class="row">
  <div class="col-md-3 col-xs-6 text-center">
    <a class="btn btn-default action-btn" href="/boka">Boka resa här</a>
  </div>
  <div class="col-md-3 col-xs-6 text-center">
    <a class="btn btn-default action-btn" href="program">Beställ program</a>
  </div>
  <div class="col-md-3 col-xs-6 text-center">
    <a class="btn btn-default action-btn" href="/gruppresor">Gruppresor</a>
  </div>
  <div class="col-md-3 col-xs-6 text-center">
    <a class="btn btn-default action-btn" href="/kontakt">Kontakta oss</a>
  </div>
</div>

  <div class="row">
  <h2>Resekalender</h2>

  <?php
    $output = "";
    foreach ($tours as $tour) {
      $output =  "<div class='col-md-6 col-xs-12'>";
      $output .= "<h3><a href='" . $tour['link'] . "'>" . $tour['tour'] . "</a></h3>";
      $output .= "<a href='" . $tour['link'] . "'><figure class='trip-featured-img-list'>";
      $output .= "<img src='" . $tour['imgsrc'] . "'  alt='" . $tour['tour'] . "'/>";
      $output .= "</figure></a>";
      $output .= "<p><i class='fa fa-hourglass blue' aria-hidden='true'></i> Antal dagar: " . $tour['days'] . " dagar</p>";
      $output .= "<p><i class='fa fa-calendar blue' aria-hidden='true'></i> Avresedatum: " . $tour['departure'] . "</p>";
      $output .= "<p><i class='fa fa-money blue' aria-hidden='true'></i> Pris per person: " . $tour['price'] . " kr</p>";
      $output .= "<p>" . $tour['summary'] . "</p></div>";
      echo $output;
  }
  ?>
  </div>
</main>
<?php
include __DIR__ . '/shared/footer.php';

} catch(\RuntimeException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/500.php';
}
