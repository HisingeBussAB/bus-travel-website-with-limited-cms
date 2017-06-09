<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * Create/Update trip in database from form data
 */

namespace HisingeBussAB\RekoResor\website\admin\includes\classes;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;


  /**
   * @uses DB
   * @uses DBError
   */
class NewTrip
{
  public static function newTrip($input) {
    $allowed_tags = ALLOWED_HTML_TAGS;

    var_dump($input);

    if (trim($input["tripid"]) == "new") {
      $tripid = "new";
    } else {
      $tripid = filter_var(trim($input["tripid"]), FILTER_SANITIZE_NUMBER_INT);
    }

    $duration = filter_var(trim($input["trip-duration"]), FILTER_SANITIZE_NUMBER_INT);

    $heading = strip_tags(trim($input["trip-heading"]), $allowed_tags);
    $summary = nl2br(strip_tags(trim($input["trip-summary"]), $allowed_tags));

    //$text = nl2br(strip_tags(trim($input["trip-text"]), $allowed_tags));

    $text = "";
    foreach ($input["trip-text-heading"] as $id => $texthead) {
      $texthead = strip_tags(trim($texthead), $allowed_tags);
      $textbody = nl2br(strip_tags(trim($input["trip-text"][$id]), $allowed_tags));
      $textbody = trim($textbody, "<br />");
      if (!empty($texthead))
      {
        $text += "<h3>" . $texthead . "</h3><p>" . $textbody . "</p>";
      }
    }


    $hotelname = strip_tags(trim($input["trip-text-hotel-heading"]), $allowed_tags);
    $hoteltext = nl2br(strip_tags(trim($input["trip-text-hotel-text"]), $allowed_tags));
    $hoteltext = trim($hoteltext, "<br />");
    $hotel = "<h3>" . $hotelname . "</h3><p>" . $hoteltext . "</p>";

    $hotellink = filter_var(trim($input["trip-text-hotel-link"]), FILTER_SANITIZE_URL);
    if ((substr( $hotellink, 0, 7 ) !== "http://") || (substr( $hotellink, 0, 8 ) !== "https://")) {
      $hotellink = "http://" . $hotellink;
    }

    $facebooklink = filter_var(trim($input["trip-facebook"]), FILTER_SANITIZE_URL);
    if ((substr( $facebooklink, 0, 7 ) !== "http://") || (substr( $facebooklink, 0, 8 ) !== "https://")) {
      $facebooklink = "http://" . $facebooklink;
    }

    $includes = "";
    foreach ($input["trip-ingar"] as $include) {
      if (!empty($include)) {
        $includes += "<p>" . strip_tags(trim($include), $allowed_tags) . "</p>";
      }
    }

    $addons = [];
    $addonsprices = [];
    $i = 0;
    foreach ($input["trip-tillagg"] as $addon) {
      $addons[$i] = strip_tags(trim($addon), $allowed_tags);
      $i++;
    }
    $i = 0;
    foreach ($input["trip-tillagg-pris"] as $addonprice) {
      $addonsprices[$i] = filter_var(trim($addonprice), FILTER_SANITIZE_NUMBER_INT);
      $i++;
    }

    $price = filter_var(trim($input["trip-price"]), FILTER_SANITIZE_NUMBER_INT);

    $i = 0;
    foreach ($input["trip-date"] as $date) {
      $dates[$i] = filter_var(trim($date), FILTER_SANITIZE_STRING);
      $i++;
    }

    $roomids = [];
    $roomprices = [];
    if (isset($input["useroom"])) {
      $i = 0;
      foreach ($input["useroom"] as $id) {
        $id = filter_var(trim($id), FILTER_SANITIZE_NUMBER_INT);
        $roomids[$i] = $id;
        $roomprices[$i] = filter_var(trim($input["roomprice"][$id]), FILTER_SANITIZE_NUMBER_INT);
        $i++;
      }
    }

    $stopids = [];
    $stopfroms = [];
    $stoptos = [];
    if (isset($input["usestop"])) {
      $i = 0;
      foreach ($input["usestop"] as $id) {
        $id = filter_var(trim($id), FILTER_SANITIZE_NUMBER_INT);
        $stopids[$i] = $id;
        $stopfroms[$i] = filter_var(trim($input["stopfrom"][$id]), FILTER_SANITIZE_STRING);
        $stoptos[$i] = filter_var(trim($input["stopto"][$id]), FILTER_SANITIZE_STRING);
        $i++;
      }
    }

    $categoryids = [];
    if (isset($input["usecategory"])) {
      $i = 0;
      foreach ($input["usecategory"] as $id) {
        $id = filter_var(trim($id), FILTER_SANITIZE_NUMBER_INT);
        $categoryids[$i] = $id;
        $i++;
      }
    }

    if (isset($input["trip-address-required"])) {
      $addressrequired = 1;
    } else {
      $addressrequired = 0;
    }

    if (isset($input["trip-personalid-required"])) {
      $personalidrequired = 1;
    } else {
      $personalidrequired = 0;
    }

    //VALIDATION
    $i = 0;
    foreach ($dates as $date) {
      $date = date('Y-m-d', strtotime($date));
      if ($date > date('Y-m-d', strtotime("2000-01-01")) && $date < date('Y-m-d', strtotime("2917-01-01"))) {
        $formateddates[$i] = $date;
      } else {
        echo $date . " datumet är i felaktikgt format eller i fel millenium. Försök igen. Använd Chrome för bäst datumstöd eller skriv in i format YYYY-MM-DD.";
        http_response_code(416);
        break;
      }
    }

    $photofolder = $heading . "_" . $formateddates[0];
    $photofolder = filter_var(trim($photofolder), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
    $photofolder = filter_var($photofolder, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $photofolder = filter_var($photofolder, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_BACKTICK);
    $photofolder = filter_var($photofolder, FILTER_SANITIZE_EMAIL);

    //DB OPERATIONS
    $pdo = DB::get();

    try {
      $sql = "INSERT INTO " . TABLE_PREFIX . "resor (
        pris,
        datum,
        namn,
        ingress,
        program,
        ingar,
        bildkatalog,
        personnr,
        fysiskadress,
        aktiv,
        hotel,
        hotellink,
        facebook,
        antaldagar
      ) VALUES (
        :price,
        :date,
        :name,
        :summary,
        :program,
        :includes,
        :photofolder,
        :personalid,
        :address,
        1,
        :hotel,
        :hotellink,
        :facebook,
        :duration
      );";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':price', $price, \PDO::PARAM_INT);
      $sth->bindParam(':date', $formateddates[0], \PDO::PARAM_STR);
      $sth->bindParam(':name', $heading, \PDO::PARAM_STR);
      $sth->bindParam(':summary', $summary, \PDO::PARAM_LOB);
      $sth->bindParam(':program', $text, \PDO::PARAM_LOB);
      $sth->bindParam(':includes', $includes, \PDO::PARAM_LOB);
      $sth->bindParam(':photofolder', $photfolder, \PDO::PARAM_STR);
      $sth->bindParam(':personalid', $personalidrequired, \PDO::PARAM_INT);
      $sth->bindParam(':address', $addressrequired, \PDO::PARAM_INT);
      $sth->bindParam(':hotel', $hotel, \PDO::PARAM_LOB);
      $sth->bindParam(':hotellink', $hotellink, \PDO::PARAM_STR);
      $sth->bindParam(':facebook', $facebooklink, \PDO::PARAM_STR);
      $sth->bindParam(':duration', $duration, \PDO::PARAM_INT);

      if (!$sth) {
        echo "\nPDO::errorInfo():\n";
        print_r($pdo->errorInfo());
      }
      var_dump($pdo);
      var_dump($sth);
      var_dump($sql);
      $sth->execute();
      var_dump($pdo);
      var_dump($sth);
      var_dump($sql);

      print_r($pdo->errorInfo());
      echo "SUCCESS";
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      http_response_code(500);
      break;
    }



  }

}
