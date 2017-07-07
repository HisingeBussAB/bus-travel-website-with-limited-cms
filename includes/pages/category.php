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
  $cat = filter_var(trim($cat), FILTER_SANITIZE_URL);
  $allowed_tags = ALLOWED_HTML_TAGS;
  try {
    $pdo = DB::get();

    $sql = "SELECT id,kategori,ingress,seo_description,og_description,og_title,seo_keywords,meta_data_extra FROM " . TABLE_PREFIX . "kategorier WHERE aktiv = 1 AND uri_kategori = :cat;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':cat', $cat, \PDO::PARAM_STR);
    $sth->execute();
    $category = $sth->fetch(\PDO::FETCH_ASSOC);
  } catch(\PDOException $e) {
    include __DIR__ . '/shared/header.php';
    DBError::showError($e, __CLASS__, $sql);
    throw new \RuntimeException("Databasfel.");
  }

    if (count($category) > 0) {
      $catid = $category['id'];
      $heading = strip_tags($category['kategori'], $allowed_tags);
      $text = nl2br(strip_tags($category['ingress'], $allowed_tags));
    } else {
      include __DIR__ . '/shared/header.php';
      throw new \UnexpectedValueException("Kategorin finns inte");
    }


$pageTitle = $heading;

header('Content-type: text/html; charset=utf-8');
include __DIR__ . '/shared/header.php';


  $cat = filter_var(trim($cat), FILTER_SANITIZE_URL);




try {
  $sql = "SELECT resor.id, resor.namn, resor.url, resor.bildkatalog, resor.antaldagar, resor.ingress,resor.pris, MIN(datum.datum) AS datum FROM " . TABLE_PREFIX . "resor AS resor
          LEFT OUTER JOIN " . TABLE_PREFIX . "datum AS datum ON resor.id = datum.resa_id
          LEFT OUTER JOIN " . TABLE_PREFIX . "kategorier_resor AS k_r ON resor.id = k_r.resa_id
          LEFT OUTER JOIN " . TABLE_PREFIX . "kategorier AS kategorier ON kategorier.id = k_r.kategorier_id
          WHERE kategorier.id = :catid
          GROUP BY resor.id
          ORDER BY datum;";
  $sth = $pdo->prepare($sql);
  $sth->bindParam(':catid', $catid, \PDO::PARAM_INT);
  $sth->execute();
  $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
} catch(\PDOException $e) {
  DBError::showError($e, __CLASS__, $sql);
  throw new \RuntimeException("Databasfel.");
}


  $tours = [];

  $i=0;
  foreach($result as $tour) {

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
        $tours[$i]['imgsrc'] = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/generic/1-thumb.jpg";
      }
    $i++;
  }


  echo "<main class='main-section clearfix container'>
          <article class='col-md-12 col-xs-12 clearfix'>
            <h1>" .  $heading . "</h1>
            <p>" . $text . "</p>
          </article>";

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

catch(\UnexpectedValueException $e) {
 if (DEBUG_MODE) echo $e->getMessage();
 include 'error/404.php';
}
