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
      $featured['link']    = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/". $tour['url'];
      $featured['tour']    = $tour['namn'];
      $featured['desc']    = $tour['seo_description'];
      $featured['imgpath'] = $tour['bildkatalog'];
      $server_path = __DIR__ . '/../../upload/resor/' . $tour['bildkatalog'] . '/';
      $web_path = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/" . $tour['bildkatalog'] . "/";
        if ($files = functions::get_img_files($server_path)) {
          $featured['imgpath'] = $web_path . $files[0]['thumb'];
        } else {
          $featured['imgpath'] = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/generic/1-thumb.jpg";
        }
      $featuredset = TRUE;
    }

    $tours[$i]['tour'] = $tour['namn'];
    $tours[$i]['link'] = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/". $tour['url'];
    $tours[$i]['days'] = $tour['antaldagar'];
    $tours[$i]['summary'] = $tour['ingress'];
    $tours[$i]['price'] = $tour['pris'];
    $tours[$i]['departure'] = $tour['datum'];

    $server_path = __DIR__ . '/../../upload/resor/' . $tour['bildkatalog'] . '/';
    $web_path = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/" . $tour['bildkatalog'] . "/";
      if ($files = functions::get_img_files($server_path)) {
        $tours[$i]['imgsrc'] = $web_path . $files[0]['thumb'];
      } else {
        $tours[$i]['imgsrc'] = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/generic/1-thumb.jpg";
      }
    $i++;
  }


?>
<main class="main-section clearfix container">
  <article class="col-md-6 col-xs-12 clearfix">
    <h1>Välkommen till Rekå Resor</h1>
    <p>För att genomföra en bra bussresa så krävs det planering och genomförande, oavsett om den ska gå inom Sverige eller ut i Europa.
    Det är där vi på Rekå Resor kommer in i bilden. Vi lyssnar på de önskemål du har och bidrar sedan med råd och idéer för bästa möjliga resultat.
    Med mer än 60 år i branschen har vi både erfarenheten såväl som kontaktnätet och det gör att vi kan ta fram i princip vilka gruppresor som helst –
    från korta endagsresor i närområdet runt Göteborg till veckolånga resor runt om i Europa. Alla bussresor kryddas med det lilla extra.</p>
  </article>
  <a href="<?php echo $featured['link']; ?>"><article class="col-md-6 col-xs-12 trip-featured" style="background-image: url('<?php echo $featured['imgpath']; ?>')">
    <h2 class="invisible">Månadens resa</h2>
    <h3 aria-label="<?php echo $featured['tour']; ?>"><?php echo $featured['desc']; ?><i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></h3>
  </article></a>
  <section class="col-md-12">
  <article class="col-md-4 col-xs-12 text-center">
    <button class="btn btn-default action-btn">Boka resa här</button>
  </article>
  <article class="col-md-4 col-xs-12 text-center">
    <button class="btn btn-default action-btn">Beställ program</button>
  </article>
  <article class="col-md-4 col-xs-12 text-center">
    <a rel="nofollow" href="tel:+4631222120"><button class="btn btn-default action-btn"><i class="fa fa-phone" aria-hidden="true"></i> 031 - 22 21 20</button></a>
  </article>
  </section>
  <article class="col-md-12 col-xs-12">
    <h2>Några nyheter</h2>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a metus non enim elementum egestas at ac urna.
    Integer ultricies, arcu ac consequat porttitor, nibh nisi accumsan eros, ac pretium felis erat et lectus. Nullam pulvinar fermentum interdum. Integer lacinia orci elit,
    a commodo odio scelerisque in. Pellentesque sed tincidunt libero. Quisque dignissim odio ut nisl efficitur sollicitudin. Ut at justo vitae metus varius gravida.
    Quisque rhoncus sit amet quam egestas commodo. Morbi semper vestibulum diam, ac bibendum odio tristique et.
    Pellentesque malesuada, ipsum a sodales laoreet, lacus sapien vestibulum urna, eu rutrum sapien justo vel ex.
    Quisque porttitor sed enim sit amet vulputate. Nunc hendrerit, dui vel molestie pharetra, risus odio mattis lacus, gravida porta nunc ligula non ligula.</p>
  </article>
  <?php
    $output = "";
    foreach ($tours as $tour) {
      $output =  "<article class='col-md-6 col-xs-12'>";
      $output .= "<h2><a href='" . $tour['link'] . "'>" . $tour['tour'] . "</a></h2>";
      $output .= "<a href='" . $tour['link'] . "'><figure class='trip-featured-img-list'>";
      $output .= "<img src='" . $tour['imgsrc'] . "'  alt='" . $tour['tour'] . "'/>";
      $output .= "</figure></a><div>";
      $output .= "<p><i class='fa fa-hourglass blue' aria-hidden='true'></i> Antal dagar: " . $tour['days'] . " dagar</p>";
      $output .= "<p><i class='fa fa-calendar blue' aria-hidden='true'></i> Avresedatum: " . $tour['departure'] . "</p>";
      $output .= "<p><i class='fa fa-money blue' aria-hidden='true'></i> Pris per person: " . $tour['price'] . " kr</p>";
      $output .= "</div>";
      $output .= "<p>" . $tour['summary'] . "</p></article>";
      echo $output;
  }
  ?>
</main>
<?php
include __DIR__ . '/shared/footer.php';

} catch(\RuntimeException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/500.php';
}
