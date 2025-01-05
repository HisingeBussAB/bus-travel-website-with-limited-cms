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
  $morestyles = "<link rel='stylesheet' href='/css/category.min.css' >";

  try {
    $pdo = DB::get();

    $sql = "SELECT id,kategori,ingress,`brödtext`,seo_description,og_description,og_title,seo_keywords,meta_data_extra FROM " . TABLE_PREFIX . "kategorier WHERE aktiv = 1 AND uri_kategori = :cat;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':cat', $cat, \PDO::PARAM_STR);
    $sth->execute();
    $thiscategory = $sth->fetch(\PDO::FETCH_ASSOC);
  } catch(\PDOException $e) {
    include __DIR__ . '/shared/header.php';
    DBError::showError($e, __CLASS__, $sql);
    throw new \RuntimeException("Databasfel.");
  }

    if (count($thiscategory) > 0) {
      $catid = $thiscategory['id'];
      $heading = strtr(strip_tags($thiscategory['kategori'], $allowed_tags), $html_ents);
      $text = Functions::linksaver(strtr(nl2br(strip_tags($thiscategory['ingress'], $allowed_tags)), $html_ents));
      $textbottom = Functions::linksaver(strtr(nl2br(strip_tags($thiscategory['brödtext'], $allowed_tags)), $html_ents));
    } else {
      include __DIR__ . '/shared/header.php';
      throw new \UnexpectedValueException("Kategorin finns inte");
    }

    if (mb_strtolower($thiscategory['kategori']) === 'gruppresor') {
      $grouptour = true;
    } else {
      $grouptour = false;
    }


$pageTitle = $heading;

$dataLayer = "{
  'pageTitle': '" . $heading . "',
  'pageCategory': 'Category_Details',
  'visitorType': 'low-value',
  'product': false
  }";

header('Content-type: text/html; charset=utf-8');
include __DIR__ . '/shared/header.php';



try {

  $sql = "SELECT resor.id, resor.namn, resor.url, resor.bildkatalog, resor.antaldagar, resor.ingress,resor.pris, datum.datum FROM " . TABLE_PREFIX . "resor AS resor
          LEFT OUTER JOIN " . TABLE_PREFIX . "kategorier_resor AS k_r ON resor.id = k_r.resa_id
          LEFT OUTER JOIN " . TABLE_PREFIX . "kategorier AS kategorier ON kategorier.id = k_r.kategorier_id
          JOIN " . TABLE_PREFIX . "datum AS datum ON resor.id = datum.resa_id
          WHERE kategorier.id = :catid AND resor.aktiv = 1 ";
          if (!$grouptour) { $sql .= "AND datum.datum > (NOW() - INTERVAL 1 DAY) "; }
 $sql .= "GROUP BY resor.id, datum.datum
          ORDER BY ";
          if ($grouptour) { $sql .= "resor.antaldagar, resor.namn;"; }
          else { $sql .= "datum.datum;"; }
  $sth = $pdo->prepare($sql);
  $sth->bindParam(':catid', $catid, \PDO::PARAM_INT);
  $sth->execute();
  $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
} catch(\PDOException $e) {
  DBError::showError($e, __CLASS__, $sql);
  throw new \RuntimeException("Databasfel.");
}

  $tours = [];
  $usedtours = [];
  foreach($result as $tour) {
    if (!in_array($tour['id'], $usedtours)) {
      $tours[$tour['id']]['departure'] = [];
      array_push($usedtours, $tour['id']);
      $tours[$tour['id']]['tour'] = strtr(strip_tags($tour['namn'], $allowed_tags), $html_ents);
      $tours[$tour['id']]['link'] = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/". str_replace("'", "", $tour['url']), FILTER_SANITIZE_URL);
      $tours[$tour['id']]['bookurl'] = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/boka/". str_replace("'", "", $tour['url']), FILTER_SANITIZE_URL);
      $tours[$tour['id']]['days'] = strtr(strip_tags($tour['antaldagar'], $allowed_tags), $html_ents);
      $tours[$tour['id']]['summary'] = functions::linksaver(strtr(nl2br(strip_tags($tour['ingress'], $allowed_tags)), $html_ents));
      $tours[$tour['id']]['price'] = number_format(filter_var($tour['pris'], FILTER_SANITIZE_NUMBER_INT), 0, ",", " ");
      array_push($tours[$tour['id']]['departure'], strtr(strip_tags($tour['datum'], $allowed_tags), $html_ents));
      $server_path = __DIR__ . '/../../upload/resor/' . $tour['bildkatalog'] . '/';
      $web_path = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/" . $tour['bildkatalog'] . "/", FILTER_SANITIZE_URL);
        if ($files = functions::get_img_files($server_path)) {
          $tours[$tour['id']]['imgsrc'] = $web_path . $files[0]['thumb'];
        } else {
          $tours[$tour['id']]['imgsrc'] = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/generic/small_1_generic.jpg", FILTER_SANITIZE_URL);
        }
    } else {
      array_push($tours[$tour['id']]['departure'], strtr(strip_tags($tour['datum'], $allowed_tags), $html_ents));
      if ($tours[$tour['id']]['price'] < number_format(filter_var($tour['pris'], FILTER_SANITIZE_NUMBER_INT), 0, ",", " ")) {
        $tours[$tour['id']]['price'] = number_format(filter_var($tour['pris'], FILTER_SANITIZE_NUMBER_INT), 0, ",", " ");
      }

    }
  }



  echo "<main class='main-section clearfix container-fluid'>
    <div class='row-fluid'>
              <div class='col-md-12 col-xs-12'>
            <h1>" .  $heading . "</h1>
            <p>" . $text . "</p>
          </div></div>";

    $i = 0;
    $lenght = count($tours);
    foreach ($tours as $tour) {
      $output = "";
      if ($i % 2 == 0) { $output .= "<div class='row-fluid'>"; }
      $output .= "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12 tour-box'>";
      $output .= "<h2><a href='" . $tour['link'] . "'>" . $tour['tour'] . "</a></h2>";


      $output .= "<a href='" . $tour['link'] . "'><figure class='trip-featured-img-list'>";
      $output .= "<div style='background-image: url(\"" . $tour['imgsrc'] . "\");' aria-label='" . $tour['tour'] . "' titel='" . $tour['tour'] . "'/>";
      $output .= "</figure></a>";
      if ($grouptour) {
        if ($tour['days'] <= 1) {
          $output .= "<p class='larger'><i class='fa fa-lg fa-hourglass blue' aria-hidden='true'></i> Dagsresa</p>";
        } else {
          $output .= "<p class='larger'><i class='fa fa-lg fa-hourglass blue' aria-hidden='true'></i> Flerdagarsresa</p>";
        }
        $output .= "<p class='larger'><i class='fa fa-lg fa-money blue' aria-hidden='true'></i> Pris per person: " . $tour['price'] . " kr</p>";
      } else {
      $output .= "<p class='larger'><i class='fa fa-hourglass blue' aria-hidden='true'></i> Antal dagar: " . $tour['days'] . " dagar</p>";
      foreach ($tour['departure'] as $departure) {
        $output .= "<p class='larger'><i class='fa fa-calendar blue' aria-hidden='true'></i> Avresedatum: " . $departure . "</p>";
      }
      $output .= "<p class='larger'><i class='fa fa-money blue' aria-hidden='true'></i> Pris per person: " . $tour['price'] . " kr</p>";
      }


      $output .= "<p class='summary-text'>" . $tour['summary'] . "</p>";
      $output .= "<footer class='summary-footer'><a href='" . $tour['link'] . "' class='btn btn-default action-btn btn-margin-right'>Läs mer om resan</a>
                  <a href='" . $tour['bookurl'] . "' class='btn btn-default action-btn'>Boka resan</a></footer>";
      $output .= "</div>";
      if ($i % 2 != 0) { $output .= "</div>"; }
      elseif ($i+1 >= $lenght) { $output .= "</div>"; }
      echo $output;
      $i++;
  }

  echo "<div class='row-fluid'>
  <div class='col-md-12 col-xs-12'>
<p>" . $textbottom . "</p>
</div></div>";
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
