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

  private $tripid; //int db id of trip, or "new" if new
  private $duration; //int number of days trip will last
  private $heading; //string name of trip
  private $summary; //string summary of trip
  private $text; //string the trip program
  private $hotel; //string the hotel name and information
  private $hotellink; //string url to hotel
  private $facebooklink; //string url to facebook event for trip
  private $includes; //array(strings) stuff included in trip
  private $addons; //array(array(name(string)),array(price(int))) optional paid stuff for trip
  private $price; //int base price for trip
  private $rooms; //array(array(id(ints)), array(price(ints))) id db and price for room options
  private $stops; //array(array(id(ints)), array(to(date(H:m:s))), array(from(date(H:m:s)))) stop db id and times
  private $categoryids; //array(ints) db ids of related categories
  private $addressrequired; //bool adress mandatory when booking
  private $personalidrequired; //bool personal id number mandatory when booking
  private $formateddates; //array(dates) departure dates.


  public function __construct($formData) {

    if (trim($formData["tripid"]) == "new") {
      $this->tripid = "new";
    } else {
      $this->tripid = filter_var(trim($formData["tripid"]), FILTER_SANITIZE_NUMBER_INT);
    }
    $this->processForm($formData);
  }




  public static function newTrip($formData) {
    //VALIDATE SOME FORM DATA
    $passedValidation = TRUE;
    $errorMessage = "";
    if (empty($formData["tripid"])) {
      echo "<p>Det finns ingen resa associerad med den här förfrågan!</p>";
      http_response_code(400);
      exit;
    }

    if (empty($formData["trip-heading"])) {
      $passedValidation = FALSE;
      $errorMessage .= "<p>Resan måste ha ett namn!</p>";
    }

    if (empty($formData["trip-date"][1])) {
      $passedValidation = FALSE;
      $errorMessage .= "<p>Resan måste ha ett datum!</p>";
    }

    if (!$passedValidation) {
      echo $errorMessage;
      http_response_code(400);
      exit;
    }

    //initialize instance of me
    $hej = new NewTrip($formData);
    var_dump($hej);
  }



  private function processForm($input) {
    $allowed_tags = ALLOWED_HTML_TAGS;

    $this->duration = filter_var(trim($input["trip-duration"]), FILTER_SANITIZE_NUMBER_INT);

    $this->heading = strip_tags(trim($input["trip-heading"]), $allowed_tags);
    $this->summary = nl2br(strip_tags(trim($input["trip-summary"]), $allowed_tags));

    $this->text = "";
    foreach ($input["trip-text-heading"] as $id => $texthead) {
      $texthead = strip_tags(trim($texthead), $allowed_tags);
      $textbody = nl2br(strip_tags(trim($input["trip-text"][$id]), $allowed_tags));
      $textbody = trim($textbody, "<br />");
      if (!empty($texthead))
      {
        $this->text += "<h3>" . $texthead . "</h3><p>" . $textbody . "</p>";
      }
    }


    $hotelname = strip_tags(trim($input["trip-text-hotel-heading"]), $allowed_tags);
    $hoteltext = nl2br(strip_tags(trim($input["trip-text-hotel-text"]), $allowed_tags));
    $hoteltext = trim($hoteltext, "<br />");
    $this->hotel = "<h3>" . $hotelname . "</h3><p>" . $hoteltext . "</p>";

    $this->hotellink = filter_var(trim($input["trip-text-hotel-link"]), FILTER_SANITIZE_URL);
    if ((substr( $this->hotellink, 0, 7 ) !== "http://") || (substr( $this->hotellink, 0, 8 ) !== "https://")) {
      $this->hotellink = "http://" . $this->hotellink;
    }

    $this->facebooklink = filter_var(trim($input["trip-facebook"]), FILTER_SANITIZE_URL);
    if ((substr( $this->facebooklink, 0, 7 ) !== "http://") || (substr( $this->facebooklink, 0, 8 ) !== "https://")) {
      $this->facebooklink = "http://" . $this->facebooklink;
    }

    $this->includes = "";
    foreach ($input["trip-ingar"] as $include) {
      if (!empty($include)) {
        $this->includes += "<p>" . strip_tags(trim($include), $allowed_tags) . "</p>";
      }
    }

    $this->addons = [];

    $i = 0;
    foreach ($input["trip-tillagg"] as $addon) {
      $this->addons['name'][$i] = strip_tags(trim($addon), $allowed_tags);

      $i++;
    }
    $i = 0;
    foreach ($input["trip-tillagg-pris"] as $addonprice) {
      $this->addons['price'][$i] = filter_var(trim($addonprice), FILTER_SANITIZE_NUMBER_INT);

      $i++;
    }

    $this->price = filter_var(trim($input["trip-price"]), FILTER_SANITIZE_NUMBER_INT);

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
        $stopfroms[$i] = date('H:i:s', strtotime(filter_var(trim($input["stopfrom"][$id]), FILTER_SANITIZE_STRING)));
        $stoptos[$i] = date('H:i:s', strtotime(filter_var(trim($input["stopto"][$id]), FILTER_SANITIZE_STRING)));
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


    $i = 0;
    $dates = [];
    foreach ($input["trip-date"] as $date) {
      $dates[$i] = filter_var(trim($date), FILTER_SANITIZE_STRING);
      $i++;
    }

    //VALIDATION
    $i = 0;
    $formateddates = [];
    foreach ($dates as $date) {
      $date = date('Y-m-d', strtotime($date));
      if ($date > date('Y-m-d', strtotime("2000-01-01")) && $date < date('Y-m-d', strtotime("2917-01-01"))) {
        $formateddates[$i] = $date;
        $i++;
      } else {
        echo $date . " datumet är i felaktikgt format eller i fel millenium. Försök igen. Använd Chrome för bäst datumstöd eller skriv in i format YYYY-MM-DD.";
        http_response_code(416);
        exit;
      }
    }

    $photofolder = $heading . "_" . $formateddates[0];
    $photofolder = filter_var(trim($photofolder), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
    $photofolder = filter_var($photofolder, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $photofolder = filter_var($photofolder, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_BACKTICK);
    $photofolder = filter_var($photofolder, FILTER_SANITIZE_EMAIL);
  }


  private function writeToDB() {
    $pdo = DB::get();

    try {
      $pdo->beginTransaction();
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

        $sth->execute();
        $tripid = intval($pdo->lastInsertId());

      $i = 0;
      foreach ($addons as $addon) {
        $sql = "INSERT INTO " . TABLE_PREFIX . "tillaggslistor (
          resa_id,
          pris,
          namn
        ) VALUES (
          :tripid,
          :price,
          :name
        );";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':tripid', $tripid, \PDO::PARAM_INT);
        $sth->bindParam(':price', $addonsprices[$i], \PDO::PARAM_INT);
        $sth->bindParam(':name', $addon, \PDO::PARAM_STR);

        $sth->execute();
        $i++;
      }

      foreach ($categoryids as $categoryid) {

        $sql = "INSERT INTO " . TABLE_PREFIX . "kategorier_resor (
          resa_id,
          kategori_id
        ) VALUES (
          :tripid,
          :categoriyid
        );";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':tripid', $tripid, \PDO::PARAM_INT);
        $sth->bindParam(':categoriyid', $categoryid, \PDO::PARAM_INT);

        $sth->execute();
      }

      $i = 0;
      foreach ($stopids as $stopid) {
        $sql = "INSERT INTO " . TABLE_PREFIX . "resor_hallplatser (
          resa_id,
          hallplats_id,
          tid_in,
          tid_ut
        ) VALUES (
          :tripid,
          :stopid,
          :in,
          :out
        );";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':tripid', $tripid, \PDO::PARAM_INT);
        $sth->bindParam(':stopid', $stopid, \PDO::PARAM_INT);
        $sth->bindParam(':in', $stoptos[$i], \PDO::PARAM_STR);
        $sth->bindParam(':out', $stopfroms[$i], \PDO::PARAM_STR);

        $sth->execute();
      }

      $i = 0;
      foreach ($roomids as $roomid) {

        $sql = "INSERT INTO " . TABLE_PREFIX . "boenden_resor (
          resa_id,
          boende_id,
          pris
        ) VALUES (
          :tripid,
          :roomid,
          :price
        );";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':tripid', $tripid, \PDO::PARAM_INT);
        $sth->bindParam(':roomid', $roomid, \PDO::PARAM_INT);
        $sth->bindParam(':price', $roomprices[$i], \PDO::PARAM_INT);

        $sth->execute();

      }

        if (count($formateddates) > 1) {
          for($i = 1; $i < count($formateddates); $i++) {
            if (isset($formateddates[$i])) {
            var_dump($i);
            var_dump($formateddates[$i]);
            $sql = "INSERT INTO " . TABLE_PREFIX . "extra_datum (
              resa_id,
              datum
            ) VALUES (
              :tripid,
              :date
            );";
            $sth = $pdo->prepare($sql);
            $sth->bindParam(':tripid', $tripid, \PDO::PARAM_INT);
            $sth->bindParam(':date', $formateddates[$i], \PDO::PARAM_STR);
            $sth->execute();
          } else {
            throw new \Exception("ERROR: Date out of bounds!<br />");
            break;
          }
        }
      }

      $pdo->commit();
    } catch(\Exception $e) {
      $pdo->rollBack();
      var_dump($e->getMessage());
      http_response_code(500);
      exit;
    } catch(\PDOException $e) {
      $pdo->rollBack();
      DBError::showError($e, __CLASS__, $sql);
      http_response_code(500);
      exit;
    }
  }
}
