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

$allowed_tags = ALLOWED_HTML_TAGS;

try {
  $toururl = filter_var(trim($toururl), FILTER_SANITIZE_URL);
  try {
    $pdo = DB::get();

    $sql = "SELECT * FROM " . TABLE_PREFIX . "resor WHERE aktiv = 1 AND url = :url;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':url', $toururl, \PDO::PARAM_STR);
    $sth->execute();
    $result = $sth->fetch(\PDO::FETCH_ASSOC);
  } catch(\PDOException $e) {
    include __DIR__ . '/shared/header.php';
    DBError::showError($e, __CLASS__, $sql);
    throw new \RuntimeException("Databasfel.");
  }

    if (count($result) > 0) {
      $tour['id'] = filter_var($result['id'], FILTER_SANITIZE_NUMBER_INT);
      $tour['namn'] = strip_tags($result['namn'], $allowed_tags);
      $tour['ingress'] = nl2br(strip_tags($result['ingress'], $allowed_tags));
      $tour['url'] = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/" . rawurlencode($result['url']);

      $tour['pris'] = strip_tags($result['pris']);
      $tour['program'] = nl2br(strip_tags($result['program'], $allowed_tags));
      $tour['antaldagar'] = strip_tags($result['antaldagar']);
      $tour['ingar'] = strip_tags($result['ingar'], $allowed_tags);

      $tour['personnr'] = ($result['personnr'] === "1");
      $tour['fysiskadress'] = ($result['fysiskadress'] === "1");
      $tour['hotel'] = nl2br(strip_tags($result['hotel'], $allowed_tags));

      $tour['hotellink'] = implode('/', array_map('rawurlencode', explode('/', $result['hotellink'])));
      $tour['hotellink'] = str_replace("%3A//", "://", $tour['hotellink']);
      $tour['facebook'] = implode('/', array_map('rawurlencode', explode('/', $result['facebook'])));
      $tour['facebook'] = str_replace("%3A//", "://", $result['facebook']);






      $server_path = __DIR__ . '/../../upload/resor/' . $result['bildkatalog'] . '/';
      $web_path = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/" . rawurlencode($result['bildkatalog']) . "/";
        if ($imgfiles = functions::get_img_files($server_path)) {
          $tour['img'][0] = $web_path . $imgfiles[0]['thumb'];
        } else {
          $tour['img'][0] = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/generic/1-thumb.jpg";
        }

      $pdffiles = functions::get_pdf_files($server_path);

      $meta  = "<meta property='description' content='" . strip_tags($result['seo_description']) . "' />";
      $meta .= "<meta property='keywords' content='" . strip_tags($result['seo_keywords']) . "' />";

      $meta .= "<meta property='og:description' content='" . strip_tags($result['og_description']) . "' />";
      $meta .= "<meta property='og:title' content='" . strip_tags($tour['namn']) . "' />";
      $meta .= "<meta property='og:url' content='" . $tour['url'] . "' />";
      $meta .= "<meta property='og:image' content='" . $tour['img'][0] . "' />";

      $meta .= strip_tags($result['meta_data_extra'], "<meta>");

    } else {
      include __DIR__ . '/shared/header.php';
      throw new \UnexpectedValueException("Resan finns inte.");
    }

    try {
      $pdo = DB::get();

      $sql = "SELECT datum FROM " . TABLE_PREFIX . "datum WHERE resa_id = :tourid ORDER BY datum;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':tourid', $tour['id'], \PDO::PARAM_STR);
      $sth->execute();
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      include __DIR__ . '/shared/header.php';
      DBError::showError($e, __CLASS__, $sql);
      throw new \RuntimeException("Databasfel.");
    }

    $i = 0;
    foreach ($result as $row) {
      $tour['datum'][$i]['short'] = date( "Y-m-d", strtotime($row['datum']) );
      $tour['datum'][$i]['mini'] = date( "j/n", strtotime($row['datum']) );
      $tour['datum'][$i]['long'] = functions::swedish_long_date($row['datum']);
      $i++;
    }


    $pageTitle = $tour['namn'];

    $morestyles = "<link rel='stylesheet' href='/css/tour.min.css' >";


header('Content-type: text/html; charset=utf-8');
include __DIR__ . '/shared/header.php';

echo "<main class='main-section clearfix container'>";
  echo "<div class='col-md-8'>
    <h1>" . $tour['namn'];
  $flag = TRUE;
  if (!empty($tour['datum'])) {
    foreach ($tour['datum'] as $datum) {
      if ($flag) {
      echo  " " . $datum['mini'];
      $flag = FALSE;
    } else {
      echo  ", " . $datum['mini'];
    }
    }
  }

  echo  "</h1><div>
    <p><i class='fa fa-hourglass blue' aria-hidden='true'></i> " . $tour['antaldagar'] . " dagar</p>
    <p><i class='fa fa-money blue' aria-hidden='true'></i> " . $tour['pris'] . " kr/person</p>";

    if (!empty($tour['datum'])) {
      foreach ($tour['datum'] as $datum) {
        echo "<p><i class='fa fa-calendar blue' aria-hidden='true'></i> " . $datum['long'] . "</p>";
      }
    }


  echo "
    </div>

    <div>" . $tour['ingress'] . "</div></div>";


    echo "<div>" . $tour['program'] . "</div>";
    echo "<div>" . $tour['hotel'] . "</div>";
    echo "<div>" . $tour['hotellink'] . "</div>";
    echo "<div>" . $tour['facebook'] . "</div>";

    echo "<div>" . $tour['personnr'] . "</div>";
    echo "<div>" . $tour['fysiskadress'] . "</div>";

/*


    $tour['program'] = nl2br(strip_tags($result['program'], $allowed_tags));
    $tour['ingar'] = strip_tags($result['ingar'], $allowed_tags);

    $tour['personnr'] = ($result['personnr'] === "1");
    $tour['fysiskadress'] = ($result['fysiskadress'] === "1");
    $tour['hotel'] = nl2br(strip_tags($result['hotel'], $allowed_tags));
    $tour['hotellink'] = urlencode($result['hotellink']);
    $tour['facebook'] = urlencode($result['facebook']);
*/


  echo "<div class='col-md-4'>";
  echo "<div class='tourSlide-container'>";
  if (!empty($imgfiles)) {
    foreach ($imgfiles as $img) {
      echo "<img class='tourSlide' src='" . $web_path . $img['thumb'] . "' data='" . $web_path . $img['file'] . "'>";
    }
  }

  echo "</div>";

  $flag = TRUE;
  if (!empty($pdffiles)) {
    foreach ($pdffiles as $pdf) {
      if ($flag) {
        $flag = FALSE;
        echo "<li><a href='" . $web_path . $pdf . "'><i class='fa fa-file-pdf-o' aria-hidden='true'></i> Öppna/Ladda ner program</a></li>";
      } else {
        echo "<li><a href='" . $web_path . $pdf . "'><i class='fa fa-file-pdf-o' aria-hidden='true'></i> Öppna/Ladda ner pdf</a></li>";
      }

    }
  }





echo "</main>";

include __DIR__ . '/shared/footer.php';

} catch(\RuntimeException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/500.php';
}

catch(\UnexpectedValueException $e) {
 if (DEBUG_MODE) echo $e->getMessage();
 include 'error/404.php';
}
