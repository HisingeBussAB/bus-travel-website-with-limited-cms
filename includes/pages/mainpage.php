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
  $pageTitle = "Bussresor i Norden och Europa";
  $allowed_tags = ALLOWED_HTML_TAGS;
  $html_ents = Functions::set_html_list();

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
      $featured['link']    = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/". str_replace("'", "", $tour['url']), FILTER_SANITIZE_URL);
      $featured['tour']    = strtr(strip_tags($tour['namn'], $allowed_tags), $html_ents);
      $featured['desc']    = strtr(strip_tags($tour['seo_description'], $allowed_tags), $html_ents);
      $featured['imgpath'] = filter_var($tour['bildkatalog'], FILTER_SANITIZE_URL);
      $server_path = __DIR__ . '/../../upload/resor/' . $tour['bildkatalog'] . '/';
      $web_path = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/" . $tour['bildkatalog'] . "/", FILTER_SANITIZE_URL);
        if ($files = Functions::get_img_files($server_path)) {
          $featured['imgpath'] = $web_path . $files[0]['thumb'];
        } else {
          $featured['imgpath'] = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/generic/small_1_generic.jpg", FILTER_SANITIZE_URL);
        }
      $featuredset = TRUE;
    }

    $tours[$i]['tour'] = strtr(strip_tags($tour['namn'], $allowed_tags), $html_ents);
    $tours[$i]['link'] = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/". str_replace("'", "", $tour['url']), FILTER_SANITIZE_URL);
    $tours[$i]['days'] = strtr(strip_tags($tour['antaldagar'], $allowed_tags), $html_ents);
    $tours[$i]['summary'] = strtr(nl2br(strip_tags($tour['ingress'], $allowed_tags)), $html_ents);
    $tours[$i]['price'] = number_format(filter_var($tour['pris'], FILTER_SANITIZE_NUMBER_INT), 0, ",", " ");
    $tours[$i]['departure'] = strtr(strip_tags($tour['datum'], $allowed_tags), $html_ents);

    $server_path = __DIR__ . '/../../upload/resor/' . filter_var($tour['bildkatalog'], FILTER_SANITIZE_URL) . '/';
    $web_path = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/" . $tour['bildkatalog'] . "/", FILTER_SANITIZE_URL);
      if ($files = Functions::get_img_files($server_path)) {
        $tours[$i]['imgsrc'] = $web_path . $files[0]['thumb'];
      } else {
        filter_var($tours[$i]['imgsrc'] = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/generic/small_1_generic.jpg", FILTER_SANITIZE_URL);
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
<main class="main-section clearfix">
  <div class="container-fluid">
  <div class="row-fluid">
  <section class="col-md-6 col-xs-12">
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

  <section class="row-fluid">
    <div class="col-md-12 col-xs-12">
      <h2>Aktuellt från Rekå Resor</h2>
      <p><?php echo strtr(nl2br(strip_tags($result['nyheter'], $allowed_tags)), $html_ents); ?></p>
    </div>
  </section>

  <div class="row-fluid">
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

  <div class="row-fluid">
    <h2 class='col-md-12'>Resekalender</h2>
  </div>
  <div class="row-fluid">

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
</div>
</main>
<?php
include __DIR__ . '/shared/footer.php';

} catch(\RuntimeException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/500.php';
}
