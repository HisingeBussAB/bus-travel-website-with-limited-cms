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

    $i = 0;
    foreach ($input["trip-text-heading"] as $id => $texthead) {
      //trip-text-text[1]
      var_dump($id);
      var_dump($texthead);
      var_dump($input["trip-text"][$id]);
    }


    $hotelname = strip_tags(trim($input["trip-text-hotel-heading"]), $allowed_tags);
    $hoteltext = nl2br(strip_tags(trim($input["trip-text-hotel-text"]), $allowed_tags));
    $hotellink = filter_var(trim($input["trip-text-hotel-link"]), FILTER_SANITIZE_URL);

    $facebooklink = filter_var(trim($input["trip-facebook"]), FILTER_SANITIZE_URL);

    $includes = [];
    $i = 0;
    foreach ($input["trip-ingar"] as $include) {
      $includes[$i] = strip_tags(trim($include), $allowed_tags);
      $i++;
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
      $addressrequired = TRUE;
    } else {
      $addressrequired = FALSE;
    }

    if (isset($input["trip-personalid-required"])) {
      $personalidrequired = TRUE;
    } else {
      $personalidrequired = FALSE;
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

/*

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
        aktiv,
        fysiskadress,
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
        TRUE,
        :hotel,
        :hotellink,
        :facebook,
        :duration
      );";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':price', $price, \PDO::PARAM_INT);
      $sth->bindParam(':date', $formateddates[0], \PDO::PARAM_STR);
      $sth->bindParam(':name', $heading, \PDO::PARAM_STR);
      $sth->bindParam(':summary', $summary, \PDO::PARAM_STR);
      $sth->bindParam(':program', $text, \PDO::PARAM_STR);
      $sth->bindParam(':includes', , \PDO::PARAM_STR);
      $sth->bindParam(':photofolder', , \PDO::PARAM_STR);
      $sth->bindParam(':personalid', , \PDO::PARAM_STR);
      $sth->bindParam(':hotel', , \PDO::PARAM_STR);
      $sth->bindParam(':hotellink', , \PDO::PARAM_STR);
      $sth->bindParam(':facebook', , \PDO::PARAM_STR);
      $sth->bindParam(':duration', , \PDO::PARAM_STR);
      $sth->execute();
      return TRUE;
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      return FALSE;
    }

*/

  }

}
