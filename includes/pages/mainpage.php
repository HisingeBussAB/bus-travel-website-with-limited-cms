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

function drawFeaturedItem($featuredtrip) {
  echo "<a href='" . $featuredtrip['link'] . "'>";
  echo "<div class='trip-featured lazy' style=\"background-image: url('" . $featuredtrip['imgpath'] . "')\">";

  if (strlen($featuredtrip['tour']) > 33) {
    echo "<h3 class='trip-featured-head long-featured-head'>".  $featuredtrip['tour'] . "</h3>";
  } else {
    echo "<h3 class='trip-featured-head'>".  $featuredtrip['tour'] . "</h3>";
  }

  echo "<div class='trip-featured-details text-center'>";
  echo "<div class='trip-featured-details-wrapper text-center'>";
  echo "<div class='trip-featured-dur text-center'>";
  if ($featuredtrip['days'] > 1) echo $featuredtrip['days'] . " dagar"; else echo "Dagsresa";
  echo "</div>";
  echo "<div class='trip-featured-price text-center'>" . $featuredtrip['price'] . ":-</div>";
  echo "</div>";
  echo "</div>";
  echo "<div class='trip-featured-desc' aria-label='". $featuredtrip['tour'] . "'><p>" . $featuredtrip['desc'] . "</p><i class='fa fa-chevron-right pull-right' aria-hidden='true'></i></div>";
  echo "</div></a>";
}

try {
  $pageTitle = "Bussresor i Norden och Europa";
  $allowed_tags = ALLOWED_HTML_TAGS;
  $html_ents = Functions::set_html_list();

  $dataLayer = "{
    'pageTitle': 'MainPage',
    'visitorType': 'low-value',
    'product': false
    }";

header('Content-type: text/html; charset=utf-8');
include __DIR__ . '/shared/header.php';



try {
  $pdo = DB::get();

  $sql = "SELECT resor.id, resor.utvald, resor.seo_description, resor.namn, resor.url, resor.bildkatalog, resor.antaldagar, resor.ingress,resor.pris, datum.datum AS datum FROM " . TABLE_PREFIX . "resor AS resor
          LEFT OUTER JOIN " . TABLE_PREFIX . "datum AS datum ON resor.id = datum.resa_id
          LEFT OUTER JOIN " . TABLE_PREFIX . "kategorier_resor AS k_r ON resor.id = k_r.resa_id
          LEFT OUTER JOIN " . TABLE_PREFIX . "kategorier AS kategorier ON kategorier.id = k_r.kategorier_id
          WHERE kategorier.kategori != 'gruppresor' AND resor.aktiv = 1 AND datum > NOW()
          GROUP BY datum
          ORDER BY datum;";



    $sth = $pdo->prepare($sql);
    $sth->execute();
    $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    //var_dump($result);
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    $errorType = "Databasfel";
    throw new \RuntimeException("Databasfel vid laddning av resor.");
  }



  $featured = [];
  $featuredcounter = 0;
  $tours = [];
  $usedtours = [];

  $i=0;
  foreach($result as $tour) {



    if (($tour['utvald'] && $featuredcounter < 6) && (!in_array($tour['id'], $usedtours))) {
      array_push($usedtours, $tour['id']);

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
    $tours[$i]['summary'] = Functions::linksaver(strtr(nl2br(strip_tags($tour['ingress'], $allowed_tags)), $html_ents));
    $tours[$i]['price'] = number_format(filter_var($tour['pris'], FILTER_SANITIZE_NUMBER_INT), 0, ",", " ");
    $tours[$i]['departure'] = strtr(strip_tags($tour['datum'], $allowed_tags), $html_ents);
    $tours[$i]['desc'] = strtr(strip_tags($tour['seo_description'], $allowed_tags), $html_ents);

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
  <?php

  $x = 0;
  if ($featuredcounter === 2) {
    foreach($featured as $featuredtrip) {
    echo "<div class='col-lg-12 col-md-12 col-xs-12 featured-box'>";
    drawFeaturedItem($featuredtrip);
    echo "</div>";
    }
  }

  elseif ($featuredcounter === 3 || $featuredcounter === 5) {
    foreach($featured as $featuredtrip) {
    echo "<div class='col-lg-6 col-md-12 col-xs-12 featured-box'>";
    drawFeaturedItem($featuredtrip);
    echo "</div>";
    }
    echo "<div class='col-lg-6 col-md-12 col-xs-12 featured-box'>
          <a href='kategori/gruppresor-och-konferens'>
          <div class='trip-featured lazy' style='background-image: url(\"upload/resor/generic/small_1_generic.jpg\")'>
          <h3 class='trip-featured-head'>Gruppresor</h3>
          <div class='trip-featured-desc' aria-label='Gruppresor'><p>Med mer än 60 år av erfarenheter inom resor kan vi erbjuda något inom i stort sett vilket intresseområde som helst. Läs mer om våra erbjudanden för grupper här.</p><i class='fa fa-chevron-right pull-right' aria-hidden='true'></i></div>
          </div></a>
          </div>";
  }


  elseif ($featuredcounter < 2) {
    foreach($featured as $featuredtrip) {
    echo "<div class='col-lg-6 col-md-12 col-xs-12 featured-box'>";
    drawFeaturedItem($featuredtrip);
    echo "</div>";
    }
    echo "<div class='col-lg-6 col-md-12 col-xs-12 featured-box'>
          <h1>Välkommen till Rekå Resor</h1>
          <p>För att genomföra en bra bussresa så krävs det planering och genomförande, oavsett om den ska gå inom Sverige eller ut i Europa.
          Det är där vi på Rekå Resor kommer in i bilden. Vi lyssnar på de önskemål du har och bidrar sedan med råd och idéer för bästa möjliga resultat.
          Med mer än 60 år i branschen har vi både erfarenheten såväl som kontaktnätet och det gör att vi kan ta fram i princip vilka gruppresor som helst –
          från korta endagsresor i närområdet runt Göteborg till veckolånga resor runt om i Europa. Alla bussresor kryddas med det lilla extra.</p>
          <h3>Följ med oss på bussresor som har det lilla extra.</h3></div>";
  }

  else {
    foreach($featured as $featuredtrip) {
    echo "<div class='col-lg-6 col-md-12 col-xs-12 featured-box'>";
    drawFeaturedItem($featuredtrip);
    echo "</div>";
    }
  }



  ?>
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
  <table class="tour-calendar-table">
  <thead><tr><th colspan="7"><h2 class='col-md-12' id='resekalender'>Resekalender</h2></th></tr>
  <tbody>
  <?php

    $i = 0;
    $lenght = count($tours);
    $lastmonth = false;
    foreach ($tours as $tour) {

      $mon = Functions::se_month($tour['departure']);
      if ($mon !== $lastmonth) {
        echo "<tr><th colspan='7'><h3 class='month'>$mon</h3></th></tr>";
        $lastmonth = $mon;
      }
      $output = "";

      $output .= "<tr>";

      $output .= "<td class='tour-calendar-table-img'>";
      $output .= "<a href='" . $tour['link'] . "'><figure class='trip-compressed-img-list'>";
      $output .= "<div style='background-image: url(\"" . $tour['imgsrc'] . "\");' aria-label='" . $tour['tour'] . "' titel='" . $tour['tour'] . "'/>";
      $output .= "</figure></div></a>";
      $output .= "</td>";

      $output .= "<td class='tour-calendar-table-date'>";
      $output .= "<a href='" . $tour['link'] . "'><h3>" . date( "j/n", strtotime($tour['departure'])) . "</h3></a>";
      $output .= "</td>";

      $output .= "<th class='tour-calendar-table-title' scope='row'>";
      $output .= "<a href='" . $tour['link'] . "'><h3>" . $tour['tour'] . "</h3></a>";
      $output .= "</th>";




      $output .= "<td class='tour-calendar-table-duration'><h3>";
      if ($tour['days'] == 1) {
        $output .= "Dagsresa";
      } else {
        $output .= $tour['days'] . " dagar";
      }
      $output .= "</h3></td>";

      $output .= "<td class='tour-calendar-table-price'><h3>";
      $output .= $tour['price'] . ":-";
      $output .= "</h3></td>";

      $output .= "<td class='tour-calendar-table-action'>";
      $output .= "<a class='btn btn-default action-btn' href='" . $tour['link'] . "'>Läs mer</a>";
      $output .= "</td>";

      $output .= "</tr>";
/*
      $output .=  "<div class='col-xs-12 tour-box'>";
      $output .= "<div class='tour-quick-facts'><h3><a href='" . $tour['link'] . "'>" . $tour['tour'] . " - " . date( "j/n", strtotime($tour['departure'])) . "</a></h3>";
      $output .= "<p><i class='fa fa-hourglass fa-lg blue' aria-hidden='true'></i> Antal dagar: ";
      if ($tour['days'] == 1) {
        $output .= "Dagsresa";
      } else {
        $output .= $tour['days'] . " dagar</p>";
      }
      $output .= "<p><i class='fa fa-money fa-lg blue' aria-hidden='true'></i> Pris per person: " . $tour['price'] . " kr</p>";
      $output .= "<p><i class='fa fa-calendar fa-lg blue' aria-hidden='true'></i> Avresedatum: " . $tour['departure'] . "</p>";
      $output .= "<div class='tour-summary'>" . $tour['summary'] . " | <a href='" . $tour['link'] . "'>Läs mer och boka.</a></div></div>";

      $output .= "<a href='" . $tour['link'] . "'><figure class='trip-img-list'>";
      $output .= "<div style='background-image: url(\"" . $tour['imgsrc'] . "\");' aria-label='" . $tour['tour'] . "' titel='" . $tour['tour'] . "'/>";
      $output .= "</figure></a></div>";
*/
      echo $output;
      $i++;
  }
  ?>
  </tbody>
  </table>
  </div>
</main>
<?php
include __DIR__ . '/shared/footer.php';

} catch(\RuntimeException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/500.php';
}
