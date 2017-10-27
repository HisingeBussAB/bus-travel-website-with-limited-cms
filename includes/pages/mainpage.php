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

    $sql = "SELECT resor.id, resor.utvald, resor.seo_description, resor.namn, resor.url, resor.bildkatalog, resor.antaldagar, resor.ingress,resor.pris, MIN(datum.datum) AS datum FROM " . TABLE_PREFIX . "resor AS resor
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


  $featured = [];
  $featuredcounter = 0;
  $tours = [];

  $i=0;
  foreach($result as $tour) {
    if ($tour['utvald'] && $featuredcounter < 4) {

      $featured[$featuredcounter]['link']    = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/". str_replace("'", "", $tour['url']), FILTER_SANITIZE_URL);
      $featured[$featuredcounter]['days'] = strtr(strip_tags($tour['antaldagar'], $allowed_tags), $html_ents);
      $featured[$featuredcounter]['price'] = number_format(filter_var($tour['pris'], FILTER_SANITIZE_NUMBER_INT), 0, ",", " ");
      $featured[$featuredcounter]['tour']    = strtr(strip_tags($tour['namn'], $allowed_tags), $html_ents);
      $featured[$featuredcounter]['desc']    = strtr(strip_tags($tour['seo_description'], $allowed_tags), $html_ents);
      $featured[$featuredcounter]['imgpath'] = filter_var($tour['bildkatalog'], FILTER_SANITIZE_URL);
      $server_path = __DIR__ . '/../../upload/resor/' . $tour['bildkatalog'] . '/';
      $web_path = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/" . $tour['bildkatalog'] . "/", FILTER_SANITIZE_URL);
        if ($files = Functions::get_img_files($server_path)) {
          $featured[$featuredcounter]['imgpath'] = $web_path . $files[0]['thumb'];
        } else {
          $featured[$featuredcounter]['imgpath'] = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/generic/small_1_generic.jpg", FILTER_SANITIZE_URL);
        }
      $featuredcounter++;
    }

    $tours[$i]['tour'] = strtr(strip_tags($tour['namn'], $allowed_tags), $html_ents);
    $tours[$i]['link'] = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/". str_replace("'", "", $tour['url']), FILTER_SANITIZE_URL);
    $tours[$i]['days'] = strtr(strip_tags($tour['antaldagar'], $allowed_tags), $html_ents);
    $tours[$i]['summary'] = functions::linksaver(strtr(nl2br(strip_tags($tour['ingress'], $allowed_tags)), $html_ents));
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

<main class="main-section clearfix container-fluid">
  <div class="row-fluid">
  <h1 class="hidden">Välkommen till Rekå Resor</h1>
  <h2 class="hidden">Utvalda resor</h2>
  <?php foreach($featured as $featuredtrip) {
    if ($featuredcounter > 3)       { echo "<div class='col-lg-6 col-md-12 col-xs-12 featured-box'>"; }
    elseif ($featuredcounter === 3)  { echo "<div class='col-lg-4 col-md-12 col-xs-12 featured-box'>"; }
    elseif ($featuredcounter === 2)   { echo "<div class='col-lg-6 col-md-12 col-xs-12 featured-box'>"; }
    elseif ($featuredcounter < 2)   {
      echo "<div class='col-lg-6 col-md-12 col-xs-12 featured-box'>
            <h1>Välkommen till Rekå Resor</h1>
            <p>För att genomföra en bra bussresa så krävs det planering och genomförande, oavsett om den ska gå inom Sverige eller ut i Europa.
            Det är där vi på Rekå Resor kommer in i bilden. Vi lyssnar på de önskemål du har och bidrar sedan med råd och idéer för bästa möjliga resultat.
            Med mer än 60 år i branschen har vi både erfarenheten såväl som kontaktnätet och det gör att vi kan ta fram i princip vilka gruppresor som helst –
            från korta endagsresor i närområdet runt Göteborg till veckolånga resor runt om i Europa. Alla bussresor kryddas med det lilla extra.</p>
            <h3>Följ med oss på bussresor som har det lilla extra.</h3></div>
            <div class='col-lg-6 col-md-12 col-xs-12 featured-box'>";
          }

    ?>
      <a href="<?php echo $featuredtrip['link']; ?>">
    <div class="trip-featured lazy" style="background-image: url('<?php echo $featuredtrip['imgpath']; ?>')">
    <h3 class="trip-featured-head"><?php echo $featuredtrip['tour']; ?></h3>
    <div class="trip-featured-details text-center">
      <div class="trip-featured-details-wrapper text-center">
        <div class="trip-featured-dur text-center"><?php if ($featuredtrip['days'] > 1) echo $featuredtrip['days'] . " dagar"; else echo "Dagsresa"; ?></div>
        <div class="trip-featured-price text-center"><?php echo $featuredtrip['price']; ?>:-</div>
      </div>
    </div>
    <div class="trip-featured-desc" aria-label="<?php echo $featuredtrip['tour']; ?>"><p><?php echo $featuredtrip['desc']; ?></p><i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></div>
  </div></a>
  </div>
<?php } ?>
  </div>

  <section class="row-fluid">
    <div class="col-md-12 col-xs-12">
      <h2>Aktuellt från Rekå Resor</h2>
      <p><?php echo Functions::linksaver(strtr(nl2br(strip_tags($result['nyheter'], $allowed_tags)), $html_ents)); ?></p>
    </div>
  </section>

  <div class="row-fluid">
  <div class="col-lg-3 col-md-3 text-center hidden-sm hidden-xs">
    <a class="btn btn-default action-btn" href="boka">Boka resa</a>
  </div>
  <div class="col-lg-3 col-md-3 col-sm-4 text-center hidden-xs">
    <a class="btn btn-default action-btn" href="bestall-program">Beställ program</a>
  </div>
  <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 text-center">
    <a class="btn btn-default action-btn" href="kategori/gruppresor-och-konferens">Gruppresor</a>
  </div>
  <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 text-center">
      <a class="facebook-visit action-btn btn btn-default" href="https://www.facebook.com/rekoresor/" target="_blank"><img src='/img/facebook.png' alt="Besök oss på Facebook" /><span class='sr-only'>Besök vår Facebooksida</span></a>
  </div>
</div>

  <div class="row-fluid">
    <h2 class='col-md-12' id='resekalender'>Resekalender</h2>
  </div>
  <div class="row-fluid">

  <?php

    $i = 0;
    $lenght = count($tours);
    foreach ($tours as $tour) {
      $output = "";
      if ($i % 2 == 0) { $output .= "<div class='row-fluid'>"; }
      $output .=  "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12 tour-box'>";


      $output .= "<div class='tour-quick-facts'><h3><a href='" . $tour['link'] . "'>" . $tour['tour'] . "</a></h3>";
      $output .= "<p><i class='fa fa-hourglass fa-lg blue' aria-hidden='true'></i> Antal dagar: ";
      if ($tour['days'] == 1) {
        $output .= "Dagsresa";
      } else {
        $output .= $tour['days'] . " dagar</p>";
      }
      $output .= "<p><i class='fa fa-calendar fa-lg blue' aria-hidden='true'></i> Avresedatum: " . $tour['departure'] . "</p>";
      $output .= "<p><i class='fa fa-money fa-lg blue' aria-hidden='true'></i> Pris per person: " . $tour['price'] . " kr</p></div>";
      $output .= "<a href='" . $tour['link'] . "'><figure class='trip-featured-img-list'>";
      $output .= "<img class='lazy' src='" . $tour['imgsrc'] . "'  alt='" . $tour['tour'] . "'/> ";
      $output .= "</figure></a>";
      $output .= "<div class='tour-summary'>" . $tour['summary'] . "</div></div>";
      if ($i % 2 != 0) { $output .= "</div>"; }
      elseif ($i+1 >= $lenght) { $output .= "</div>"; }

      echo $output;
      $i++;
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
