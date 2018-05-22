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


try {
  $allowed_tags = ALLOWED_HTML_TAGS;
  $html_ents = Functions::set_html_list();

  $toururl = str_replace("'", "", $toururl); //Is urlencoded there should not be any ' and they will break the html if value is echoed and user enters a malicious query
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
      $tour['namn'] = strtr(strip_tags($result['namn'], $allowed_tags), $html_ents);
      $tour['ingress'] = functions::linksaver(strtr(nl2br(strip_tags($result['ingress'], $allowed_tags)), $html_ents));
      $tour['url'] = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/" . $result['url'], FILTER_SANITIZE_URL);

      $tour['pris'] = number_format(filter_var($result['pris'], FILTER_SANITIZE_NUMBER_INT), 0, ",", " ");
      $tour['program'] = functions::linksaver(strtr(nl2br(strip_tags($result['program'], $allowed_tags)), $html_ents));
      $tour['antaldagar'] = strtr(strip_tags($result['antaldagar']), $html_ents);
      $search = array('<li>', '</li>');
      $replace = array('<tr><td>', '</td></tr>');
      $tour['ingar'] = strtr(str_replace($search, $replace, strip_tags($result['ingar'], $allowed_tags . "<tr><td>")), $html_ents);
      $tour['personnr'] = ($result['personnr'] === "1");
      $tour['fysiskadress'] = ($result['fysiskadress'] === "1");
      $tour['hotel'] = strtr(nl2br(strip_tags($result['hotel'], $allowed_tags)), $html_ents);

      $tour['hotellink'] = implode('/', array_map('rawurlencode', explode('/', $result['hotellink'])));
      $tour['hotellink'] = str_replace("'", "", $tour['hotellink']);
      $tour['hotellink'] = str_replace("%3A//", "://", $tour['hotellink']);

      $tour['facebook'] = implode('/', array_map('rawurlencode', explode('/', $result['facebook'])));
      $tour['facebook'] = str_replace("'", "", $tour['facebook']);
      $tour['facebook'] = str_replace("%3A//", "://", $tour['facebook']);

      $tour['hotellink'] = filter_var($tour['hotellink'], FILTER_SANITIZE_URL);
      $tour['facebook'] = filter_var($tour['facebook'], FILTER_SANITIZE_URL);






      $server_path = __DIR__ . '/../../upload/resor/' . $result['bildkatalog'] . '/';
      $web_path = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/" . rawurlencode($result['bildkatalog']) . "/";
        if ($imgfiles = functions::get_img_files($server_path)) {
          $tour['img'][0] = $web_path . $imgfiles[0]['thumb'];
        } else {
          $tour['img'][0] = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/generic/small_1_generic.jpg";
        }

      $pdffiles = functions::get_pdf_files($server_path);

      $meta  = "<meta property='description' content='" . htmlspecialchars(strip_tags($result['seo_description']), ENT_QUOTES) . "' />";

      $meta .= "<meta property='og:description' content='" . htmlspecialchars(strip_tags($result['og_description']), ENT_QUOTES) . "' />";
      $meta .= "<meta property='og:title' content='" . htmlspecialchars(strip_tags($tour['namn']), ENT_QUOTES) . "' />";
      $meta .= "<meta property='og:url' content='" . str_replace("'", "", filter_var($tour['url'], FILTER_SANITIZE_URL)) . "' />";
      $meta .= "<meta property='og:image' content='" . str_replace("'", "", filter_var($tour['img'][0], FILTER_SANITIZE_URL)) . "' />";

      if (strpos($result['meta_data_extra'], '<meta') !== false) {
        $html_ents2 = get_html_translation_table(HTML_ENTITIES);
        unset($html_ents2['<'], $html_ents2['>'], $html_ents2['='], $html_ents2['"'], $html_ents2["'"], $html_ents2[":"] ,$html_ents2["/"]);
        $meta .= strtr(strip_tags($result['meta_data_extra'], "<meta>"), $html_ents2);
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
      $tour['tillagg'][$i]['pris'] = number_format(filter_var($row['pris'], FILTER_SANITIZE_NUMBER_INT), 0, ",", " ");
      $tour['tillagg'][$i]['namn'] = strtr($row['namn'], $html_ents);
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
      $tour['hlp'][$i]['in'] = strtr($row['tid_in'], $html_ents);
      $tour['hlp'][$i]['ut'] = strtr($row['tid_ut'], $html_ents);
      $tour['hlp'][$i]['ort'] = strtr($row['ort'], $html_ents);
      $tour['hlp'][$i]['plats'] = strtr($row['plats'], $html_ents);
      $i++;
    }

    try {
      $sql = "SELECT kategori FROM " . TABLE_PREFIX . "kategorier AS kategorier
        LEFT OUTER JOIN " . TABLE_PREFIX . "kategorier_resor AS kategorier_resor ON kategorier.id = kategorier_resor.kategorier_id WHERE resa_id = :tourid AND kategori = 'gruppresor';";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':tourid', $tour['id'], \PDO::PARAM_STR);
      $sth->execute();
      $result = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      throw new \RuntimeException("Databasfel.");
    }

    if(!empty($result)) {
      $grouptour = true;
    } else {
      $grouptour = false;
    }



    $pageTitle = $tour['namn'];

    $morestyles = "<link rel='stylesheet' href='/css/tour.min.css' >";

    $dataLayer = "{
      'pageTitle': '" . html_entity_decode($tour['namn']) . "',
      'pageCategory': 'Tour_Details',
      'visitorType': 'low-value',
      }";


header('Content-type: text/html; charset=utf-8');
include __DIR__ . '/shared/header.php';

echo "<main class='main-section container-fluid'>";

  echo "<div class='row-fluid'>";
  //LEFT COLUMN
  echo "<div class='col-lg-6 col-md-12'>
    <h1>" . $tour['namn'] . "</h1><h4 class='dates'>";
  $flag = TRUE;
  if (!empty($tour['datum']) && !$grouptour) {
    foreach ($tour['datum'] as $datum) {
      if ($flag) {
      echo  " " . $datum['mini'];
      $flag = FALSE;
    } else {
      echo  ", " . $datum['mini'];
    }
    }
  }

  echo  "</h4>";
  if ($grouptour) { echo "<h4 class='dates'>Gruppreseförslag</h4>"; }

  echo "<div class='quickfacts'><p><i class='fa fa-hourglass blue' aria-hidden='true'></i> " . $tour['antaldagar'] . " dagar</p>
    <p><i class='fa fa-money blue' aria-hidden='true'></i> ";
    if ($grouptour) { echo "Ca "; }
    echo $tour['pris'] . " kr/person</p>";

    if (!empty($tour['datum'])) {
      foreach ($tour['datum'] as $datum) {
        echo "<p><i class='fa fa-calendar blue' aria-hidden='true'></i> ";
        if ($grouptour) { echo "Enlight önskemål"; } else { echo $datum['long'] . "</p>"; }
      }
    }


  echo "</div>

    <div class='lead'>" . $tour['ingress'] . "</div>";

    echo "<div class='text-center'><a href='/boka/" . $toururl . "' class='btn btn-default action-btn'>Boka resan</a><a href='/program/" . $toururl . "' class='btn btn-default action-btn'>Beställ tryckt program</a></div>";

    echo "</div>";

    echo "<div class='tourSlide-container col-lg-6 col-md-12'>";
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

      if ($i+1 < $max) {
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








    echo "</div><div class='row-fluid'>";
    //LEFT COLUMN

    if ((!empty($tour['hotellink']) && $tour['hotellink'] != "http://" && $tour['hotellink'] != "https://") && !empty($tour['program'])) {
    echo "<div class='col-md-8 col-xs-12'>";
    echo "<div class='program'>" . $tour['program'] . "</div>";
    echo "<div class='hotel'>" . $tour['hotel'] . "";

    if (!empty($tour['hotellink']) && $tour['hotellink'] != "http://" && $tour['hotellink'] != "https://") {
      echo "<div class='hotel-link'><a href='" . $tour['hotellink'] . "' target='_blank'><i class='fa fa-external-link' aria-hidden='true' title='Hemsida för resans boendeanläggning'></i> Hemsida</a></div>";
    }
    echo "</div>";
    echo "</div>";

    //RIGHT COLUMN
    echo "<div class='col-md-4 col-xs-12'>";
  } else {
    echo "<div class='col-xs-12'>";
  }






  echo "<table class='pdfs'>";
  echo "<caption>Program för nedladdning/utskrift</caption>";
  echo "<tbody>";
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
  echo "<table class='included'>";
  echo "<caption>Ingår i priset</caption>";
  echo "<tbody>";
  echo $tour['ingar'];
  echo "</tbody></table>";
  }

  if (!empty($tour['tillagg'])) {
  echo "<table class='addons'>";
  echo "<caption>Friviliiga tillägg</caption>";
  echo "<tbody>";

    foreach ($tour['tillagg'] as $key => $addon) {
      echo "<tr><td>" . $tour['tillagg'][$key]['namn'] . "</td><td>" . $tour['tillagg'][$key]['pris'] . " :-</td></tr>";
    }


  echo "</tbody></table>";
  }

  if (!empty($tour['hlp'])) {
  echo "<table class='timetable'>";
  echo "<caption>Turlista</caption>";
  echo "<thead><tr><th scope='col'>Ort</th><th scope='col'>Ut</th><th scope='col'>Hem</th></tr></thead><tbody>";

  foreach ($tour['hlp'] as $key => $addon) {
    echo "<tr><th scope='row'>" . $tour['hlp'][$key]['ort'] . "</th><td>" . date('H:i',strtotime($tour['hlp'][$key]['ut'])) . "</td><td>" . date('H:i',strtotime($tour['hlp'][$key]['in'])) . "</td></tr>";
  }


  echo "</tbody></table>";
  }


  echo "</div>";
  echo "</div><div class='row-fluid'><div class='text-center col-xs-12'>";

  echo "<div class='text-center bottom-btns'><a href='/boka/" . $toururl . "' class='btn btn-default action-btn'>Boka resan</a><a href='/program/" . $toururl . "' class='btn btn-default action-btn'>Beställ tryckt program</a>";
  if (!empty($tour['facebook']) && $tour['facebook'] != "http://" && $tour['facebook'] != "https://") {
    echo "<a href='" . $tour['facebook'] . "' target='_blank' class='facebook-event' title='Besök Facebook-eventet för resan'><img src='/img/join-facebook-event.png' alt='Besök Facebook-eventet för resan' /></a></div>";
  }
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
