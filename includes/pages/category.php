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
  $html_ents = Functions::set_html_list();
  $cat = str_replace("'", "", $cat); //Is urlencoded there should not be any ' and they will break the html if value is echoed and user enters a malicious query
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
      $heading = strtr(strip_tags($category['kategori'], $allowed_tags), $html_ents);
      $text = strtr(nl2br(strip_tags($category['ingress'], $allowed_tags)), $html_ents);
    } else {
      include __DIR__ . '/shared/header.php';
      throw new \UnexpectedValueException("Kategorin finns inte");
    }


$pageTitle = $heading;

header('Content-type: text/html; charset=utf-8');
include __DIR__ . '/shared/header.php';


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

    $tours[$i]['tour'] = strtr(strip_tags($tour['namn'], $allowed_tags), $html_ents);
    $tours[$i]['link'] = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/". str_replace("'", "", $tour['url']), FILTER_SANITIZE_URL);
    $tours[$i]['days'] = strtr(strip_tags($tour['antaldagar'], $allowed_tags), $html_ents);
    $tours[$i]['summary'] = strtr(nl2br(strip_tags($tour['ingress'], $allowed_tags)), $html_ents);
    $tours[$i]['price'] = number_format(filter_var($tour['pris'], FILTER_SANITIZE_NUMBER_INT), 0, ",", " ");
    $tours[$i]['departure'] = strtr(strip_tags($tour['datum'], $allowed_tags), $html_ents);

    $server_path = __DIR__ . '/../../upload/resor/' . $tour['bildkatalog'] . '/';
    $web_path = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/" . $tour['bildkatalog'] . "/", FILTER_SANITIZE_URL);
      if ($files = functions::get_img_files($server_path)) {
        $tours[$i]['imgsrc'] = $web_path . $files[0]['thumb'];
      } else {
        $tours[$i]['imgsrc'] = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/generic/1-thumb.jpg", FILTER_SANITIZE_URL);
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
