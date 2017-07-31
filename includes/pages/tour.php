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
    DBError::showError($e, __CLASS__, $sql);
    throw new \RuntimeException("Databasfel.");
  }

    if ((count($result) > 0) && ($result !== false)) {
      $tour['id'] = filter_var($result['id'], FILTER_SANITIZE_NUMBER_INT);
      $tour['namn'] = strip_tags($result['namn'], $allowed_tags);
      $tour['ingress'] = nl2br(strip_tags($result['ingress'], $allowed_tags));
      $tour['url'] = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/" . rawurlencode($result['url']);

      $tour['pris'] = strip_tags($result['pris']);
      $tour['program'] = nl2br(strip_tags($result['program'], $allowed_tags));
      $tour['antaldagar'] = strip_tags($result['antaldagar']);
      $search = array('<li>', '</li>');
      $replace = array('<tr><td>', '</td></tr>');
      $tour['ingar'] = str_replace($search, $replace, strip_tags($result['ingar'], $allowed_tags . "<tr><td>"));
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
          $tour['img'][0] = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/generic/small_1_generic.jpg";
        }

      $pdffiles = functions::get_pdf_files($server_path);

      $meta  = "<meta property='description' content='" . strip_tags($result['seo_description']) . "' />";
      $meta .= "<meta property='keywords' content='" . strip_tags($result['seo_keywords']) . "' />";

      $meta .= "<meta property='og:description' content='" . strip_tags($result['og_description']) . "' />";
      $meta .= "<meta property='og:title' content='" . strip_tags($tour['namn']) . "' />";
      $meta .= "<meta property='og:url' content='" . $tour['url'] . "' />";
      $meta .= "<meta property='og:image' content='" . $tour['img'][0] . "' />";

      if (strpos($result['meta_data_extra'], '<meta') !== false) {
        $meta .= strip_tags($result['meta_data_extra'], "<meta>");
      }

    } else {
      throw new \UnexpectedValueException("Resan finns inte.");
    }

    try {
      $sql = "SELECT datum FROM " . TABLE_PREFIX . "datum WHERE resa_id = :tourid ORDER BY datum;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':tourid', $tour['id'], \PDO::PARAM_STR);
      $sth->execute();
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
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

    try {
      $sql = "SELECT pris, namn FROM " . TABLE_PREFIX . "tillaggslistor WHERE resa_id = :tourid;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':tourid', $tour['id'], \PDO::PARAM_STR);
      $sth->execute();
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      throw new \RuntimeException("Databasfel.");
    }

    $i = 0;
    foreach ($result as $row) {
      $tour['tillagg'][$i]['pris'] = $row['pris'];
      $tour['tillagg'][$i]['namn'] = $row['namn'];
      $i++;
    }

    try {
      $sql = "SELECT plats, ort, tid_in, tid_ut FROM " . TABLE_PREFIX . "hallplatser AS hlp
        LEFT OUTER JOIN " . TABLE_PREFIX . "hallplatser_resor AS hlp_r ON hlp.id = hlp_r.hallplatser_id WHERE resa_id = :tourid
        ORDER BY tid_ut;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':tourid', $tour['id'], \PDO::PARAM_STR);
      $sth->execute();
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      throw new \RuntimeException("Databasfel.");
    }

    $i = 0;
    foreach ($result as $row) {
      $tour['hlp'][$i]['in'] = $row['tid_in'];
      $tour['hlp'][$i]['ut'] = $row['tid_ut'];
      $tour['hlp'][$i]['ort'] = $row['ort'];
      $tour['hlp'][$i]['plats'] = $row['plats'];
      $i++;
    }

    $pageTitle = $tour['namn'];

    $morestyles = "<link rel='stylesheet' href='/css/tour.min.css' >";


header('Content-type: text/html; charset=utf-8');
include __DIR__ . '/shared/header.php';

echo "<main class='main-section container'>";

  echo "<div class='row'>";
  //LEFT COLUMN
  echo "<div class='col-lg-7 col-md-12'>
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

  echo  "</h1>
    <p><i class='fa fa-hourglass blue' aria-hidden='true'></i> " . $tour['antaldagar'] . " dagar</p>
    <p><i class='fa fa-money blue' aria-hidden='true'></i> " . $tour['pris'] . " kr/person</p>";

    if (!empty($tour['datum'])) {
      foreach ($tour['datum'] as $datum) {
        echo "<p><i class='fa fa-calendar blue' aria-hidden='true'></i> " . $datum['long'] . "</p>";
      }
    }


  echo "

    <div>" . $tour['ingress'] . "</div>";

    echo "<div class='text-center'><a href='/boka/" . $toururl . "' class='btn btn-default action-btn'>Boka resan</a><a href='/program/" . $toururl . "' class='btn btn-default action-btn'>Beställ tryckt program</a></div>";

    echo "</div>";

    echo "<div class='tourSlide-container col-lg-5 col-md-12'>";
    echo "<ul class='slides'>";
    if (empty($imgfiles)) {
      $imgfiles[0]['file'] = "1_generic.jpg";
      $imgfiles[0]['thumb'] = "small_1_generic.jpg";
      $web_path = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/generic/";
    }
    $max = count($imgfiles);
    $i = 1;
    $prev = $max;
    $next = $i+1;
    foreach ($imgfiles as $img) {
      echo "
            <input type='radio' name='radio-btn' id='img-" . $i . "'";
      if ($i === 1) { echo " checked "; }
      echo " />
            <li class='slide-container'>
            <div class='slide'>
              <img src='" . $web_path . $img['thumb'] . "' data='" . $web_path . $img['file'] . "' />
            </div>
            <div class='slide-nav'>
              <label for='img-" . $prev . "'' class='prev'>&#x2039;</label>
              <label for='img-" . $next . "' class='next'>&#x203a;</label>
            </div>
            </li>";

      if ($i < $max) {
        $i++;
        $prev = $i - 1;
        $next = $i + 1;
      } else {
        $i++;
        $prev = $i - 1;
        $next = 1;
      }

    }

    echo "<li class='nav-dots'>";
    $i = 1;
    foreach ($imgfiles as $img) {
      echo "<label for='img-" . $i . "' class='nav-dot' id='img-dot-" . $i . "'></label>";

      $i++;
    }
    echo "</li>";
    echo "</ul></div>";








    echo "</div><div class='row'>";
    //LEFT COLUMN

    echo "<div class='col-md-8 col-xs-12'>";
    echo "<div>" . $tour['program'] . "</div>";
    echo "<div>" . $tour['hotel'] . "</div>";
    echo "<div>" . $tour['hotellink'] . "</div>";
    echo "<div>" . $tour['facebook'] . "</div>";

    //echo "<div>" . $tour['personnr'] . "</div>";
    //echo "<div>" . $tour['fysiskadress'] . "</div>";
    echo "</div>";


  echo "<div class='col-md-4 col-xs-12'>";
  //RIGHT COLUMN


  echo "<table>";
  echo "<thead><tr><th>Program för nedladdning/utskrift</th></tr></thead><tbody>";
  $flag = TRUE;
  if (!empty($pdffiles)) {
    foreach ($pdffiles as $pdf) {
      if ($flag) {
        $flag = FALSE;
        echo "<tr><td><a href='" . $web_path . $pdf . "'><i class='fa fa-file-pdf-o' aria-hidden='true'></i> - Öppna/Ladda ner program</a><tr><td>";
      } else {
        echo "<tr><td><a href='" . $web_path . $pdf . "'><i class='fa fa-file-pdf-o' aria-hidden='true'></i> - Öppna/Ladda ner pdf</a><tr><td>";
      }

    }
  }

  echo "</tbody></table>";

  if (!empty($tour['ingar'])) {
  echo "<table>";
  echo "<thead><tr><th>Ingår i priset</th></tr></thead><tbody>";
  echo $tour['ingar'];
  echo "</tbody></table>";
  }

  if (!empty($tour['tillagg'])) {
  echo "<table>";
  echo "<thead><tr><th>Friviliiga tillägg</th></tr></thead><tbody>";

    foreach ($tour['tillagg'] as $key => $addon) {
      echo "<tr><td>" . $tour['tillagg'][$key]['namn'] . "</td><td>" . $tour['tillagg'][$key]['pris'] . " :-</td></tr>";
    }


  echo "</tbody></table>";
  }

  if (!empty($tour['hlp'])) {
  echo "<table>";
  echo "<thead><tr><th>Turlista</th></tr></thead><tbody>";

  foreach ($tour['hlp'] as $key => $addon) {
    echo "<tr><td>" . $tour['hlp'][$key]['ort'] . "</td><td>" . date('H:i',strtotime($tour['hlp'][$key]['ut'])) . "</td><td>" . date('H:i',strtotime($tour['hlp'][$key]['in'])) . "</td></tr>";
  }


  echo "</tbody></table>";
  }


  echo "</div>";
      echo "</div>";




echo "</main>";

include __DIR__ . '/shared/footer.php';

} catch(\UnexpectedValueException $e) {
 if (DEBUG_MODE) echo $e->getMessage();
 include 'error/404.php';
} catch(\RuntimeException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/500.php';
}
