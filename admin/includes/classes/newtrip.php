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
  private $photofolder; //string


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
    $form = new NewTrip($formData);
    if ($form->tripid == "new") {
      $write = "new";
    } else {
      $write = "update";
    }
    if ($form->writeToDB($write)) {
      echo "<p>Resan sparad</p>";
      http_response_code(200);
    } else {
      echo "<p>Kritiskt databasfel. Kontrollera informationen i formuläret eller kontakta systemadministratör.</p>";
      http_response_code(500);
      exit;
    }
  }

  public static function getTripsJSON() {

    $pdo = DB::get();
    try {
      $sql = "SELECT resor.id, resor.namn, resor.aktiv, MIN(datum.datum) AS datum FROM " . TABLE_PREFIX . "resor AS resor INNER JOIN " . TABLE_PREFIX . "datum AS datum ON resor.id = datum.resa_id GROUP BY resor.id ORDER BY datum ;";
      $sth = $pdo->prepare($sql);
      $sth->execute();
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
    }
      return json_encode($result);
  }



  private function processForm($input) {
    $allowed_tags = ALLOWED_HTML_TAGS;

    $this->duration = filter_var(trim($input["trip-duration"]), FILTER_SANITIZE_NUMBER_INT);

    $this->heading = strip_tags(trim($input["trip-heading"]), $allowed_tags);
    $this->summary = nl2br(strip_tags(trim($input["trip-summary"]), $allowed_tags));
    $this->summary = str_replace(array("\r\n", "\n","\r", PHP_EOL), '', $this->summary);

    $this->text = "";
    foreach ($input["trip-text-heading"] as $id=>$texthead) {
      $texthead = strip_tags(trim($texthead), $allowed_tags);
      $textbody = nl2br(strip_tags(trim($input["trip-text"][$id]), $allowed_tags));
      $textbody = str_replace(array("\r\n", "\n","\r", PHP_EOL), '', $textbody);

      if ((strpos($texthead, '<h3>') !== false) || (strpos($texthead, '<p>') !== false)) {
        echo "Inte accpterad. Det går inte att använda taggarna h3 eller p i programtext och underrubrik. Underrubriken visas automatiskt som h3 och knappen lägg till dag/paragraf skall användas för att få rätt formaterade paragrafer.";
        http_response_code(400);
        exit;
      }
      $this->text .= "<h3>" . $texthead . "</h3><p>" . $textbody . "</p>";
    }


    $hotelname = strip_tags(trim($input["trip-text-hotel-heading"]), $allowed_tags);
    $hoteltext = nl2br(strip_tags(trim($input["trip-text-hotel-text"]), $allowed_tags));
    $hoteltext = str_replace(array("\r\n", "\n","\r", PHP_EOL), '', $hoteltext);
    if ((strpos($hotelname, '<h3>') !== false) || (strpos($hoteltext, '<p>') !== false)) {
      echo "Inte accpterad. Det går inte att använda taggarna h3 eller p i hotellnamn eller hotellbeskrivning/adress.";
      http_response_code(400);
      exit;
    }
    $this->hotel = "<h3>" . $hotelname . "</h3><p>" . $hoteltext . "</p>";

    $this->hotellink = filter_var(trim($input["trip-text-hotel-link"]), FILTER_SANITIZE_URL);
    if ((substr($this->hotellink, 0, 7 ) !== "http://") && (substr($this->hotellink, 0, 8 ) !== "https://")) {
      $this->hotellink = "http://" . $this->hotellink;
    }

    $this->facebooklink = filter_var(trim($input["trip-facebook"]), FILTER_SANITIZE_URL);
    if ((substr($this->facebooklink, 0, 7 ) !== "http://") && (substr($this->facebooklink, 0, 8 ) !== "https://")) {
      $this->facebooklink = "http://" . $this->facebooklink;
    }

    $this->includes = "";
    foreach ($input["trip-ingar"] as $include) {
      if (!empty($include)) {
        if (strpos($include, '<p>') !== false) {
          echo "Inte accpterad. Det går inte att använda p taggen i ett listan med som ingår. Varje fält är en egen rad.";
          http_response_code(400);
          exit;
        }
        $this->includes .= "<p>" . strip_tags(trim($include), $allowed_tags) . "</p>";
      }
    }

    $this->addons = [];
    $this->addons['name'] = [];
    $this->addons['price'] = [];

    $i = 0;
    foreach ($input["trip-tillagg"] as $key=>$addon) {
      if (!empty($addon)) {
        $this->addons['name'][$i] = strip_tags(trim($addon), $allowed_tags);
        $this->addons['price'][$i] = filter_var(trim($input["trip-tillagg-pris"][$key]), FILTER_SANITIZE_NUMBER_INT);
        $i++;
      }
    }


    $this->price = filter_var(trim($input["trip-price"]), FILTER_SANITIZE_NUMBER_INT);

    $this->rooms = [];
    $this->rooms['id'] = [];
    $this->rooms['price'] = [];
    if (isset($input["useroom"])) {
      $i = 0;
      foreach ($input["useroom"] as $id) {
        $id = filter_var(trim($id), FILTER_SANITIZE_NUMBER_INT);
        $this->rooms['id'][$i] = $id;
        $this->rooms['price'][$i] = filter_var(trim($input["roomprice"][$id]), FILTER_SANITIZE_NUMBER_INT);
        $i++;
      }
    }

    $this->stops = [];
    $this->stops['id'] = [];
    $this->stops['from'] = [];
    $this->stops['to'] = [];
    if (isset($input["usestop"])) {
      $i = 0;
      foreach ($input["usestop"] as $id) {
        $id = filter_var(trim($id), FILTER_SANITIZE_NUMBER_INT);
        $this->stops['id'][$i] = $id;
        $this->stops['from'][$i] = date('H:i:s', strtotime(filter_var(trim($input["stopfrom"][$id]), FILTER_SANITIZE_STRING)));
        $this->stops['to'][$i] = date('H:i:s', strtotime(filter_var(trim($input["stopto"][$id]), FILTER_SANITIZE_STRING)));
        $i++;
      }
    }

    $this->categoryids = [];
    if (isset($input["usecategory"])) {
      $i = 0;
      foreach ($input["usecategory"] as $id) {
        $id = filter_var(trim($id), FILTER_SANITIZE_NUMBER_INT);
        $this->categoryids[$i] = $id;
        $i++;
      }
    }

    if (isset($input["trip-address-required"])) {
      $this->addressrequired = 1;
    } else {
      $this->addressrequired = 0;
    }

    if (isset($input["trip-personalid-required"])) {
      $this->personalidrequired = 1;
    } else {
      $this->personalidrequired = 0;
    }


    $i = 0;
    $dates = [];
    foreach ($input["trip-date"] as $date) {
      $dates[$i] = filter_var(trim($date), FILTER_SANITIZE_STRING);
      $i++;
    }
    $i = 0;
    $this->formateddates = [];
    foreach ($dates as $date) {
      $date = date('Y-m-d', strtotime($date));
      if ($date > date('Y-m-d', strtotime("2000-01-01")) && $date < date('Y-m-d', strtotime("2917-01-01"))) {
        $this->formateddates[$i] = $date;
        $i++;
      } else {
        echo "\"" . $date . "\" datumet är i felaktikgt format eller i fel millenium. Försök igen. Använd Chrome för bäst datumstöd eller skriv in i format YYYY-MM-DD.";
        http_response_code(416);
        exit;
      }
    }

    $photofolder = $this->heading . "_" . $this->formateddates[0];
    $photofolder = filter_var(trim($photofolder), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
    $photofolder = filter_var($photofolder, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $photofolder = filter_var($photofolder, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_BACKTICK);
    $this->photofolder = filter_var($photofolder, FILTER_SANITIZE_EMAIL);
  }


  private function writeToDB($mode) {
    $pdo = DB::get();

    if ($mode == "new") {
      $mode = TRUE;
    } elseif ($mode == "update") {
      $mode = FALSE;
    } else {
      echo "Den här funktioner behöver ett argument som är antingen new eller update";
      http_response_code(500);
      exit;
    }

    try {
      $pdo->beginTransaction();
      if ($mode) {
        $sql = "INSERT INTO " . TABLE_PREFIX . "resor (
          pris,
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
      } else {
        $sql = "UPDATE " . TABLE_PREFIX . "resor SET
          pris = :price,
          namn = :name,
          ingress = :summary,
          program = :program,
          ingar = :includes,
          personnr = :personalid,
          fysiskadress = :address,
          aktiv = 1,
          hotel = :hotel,
          hotellink = :hotellink,
          facebook = :facebook,
          antaldagar = :duration
        WHERE id = :tripid
        ;";
      }
      $sth = $pdo->prepare($sql);
      if (!$mode) {
        $sth->bindParam(':tripid', $this->tripid, \PDO::PARAM_INT);
      } else {
        $sth->bindParam(':photofolder', $this->photofolder, \PDO::PARAM_INT);
      }
      $sth->bindParam(':price', $this->price, \PDO::PARAM_INT);
      $sth->bindParam(':name', $this->heading, \PDO::PARAM_STR);
      $sth->bindParam(':summary', $this->summary, \PDO::PARAM_LOB);
      $sth->bindParam(':program', $this->text, \PDO::PARAM_LOB);
      $sth->bindParam(':includes', $this->includes, \PDO::PARAM_LOB);
      $sth->bindParam(':personalid', $this->personalidrequired, \PDO::PARAM_INT);
      $sth->bindParam(':address', $this->addressrequired, \PDO::PARAM_INT);
      $sth->bindParam(':hotel', $this->hotel, \PDO::PARAM_LOB);
      $sth->bindParam(':hotellink', $this->hotellink, \PDO::PARAM_STR);
      $sth->bindParam(':facebook', $this->facebooklink, \PDO::PARAM_STR);
      $sth->bindParam(':duration', $this->duration, \PDO::PARAM_INT);

      $sth->execute();

      if ($mode) {$this->tripid = intval($pdo->lastInsertId());}

      if (!$mode) {
        $sql = "DELETE FROM " . TABLE_PREFIX . "tillaggslistor WHERE
          resa_id = :tripid
          ;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':tripid', $this->tripid, \PDO::PARAM_INT);
        $sth->execute();
      }

      $i = 0;
      foreach ($this->addons['name'] as $addon) {
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
        $sth->bindParam(':tripid', $this->tripid, \PDO::PARAM_INT);
        $sth->bindParam(':price', $this->addons['price'][$i], \PDO::PARAM_INT);
        $sth->bindParam(':name', $addon, \PDO::PARAM_STR);

        $sth->execute();
        $i++;
      }


      if (!$mode) {
        $sql = "DELETE FROM " . TABLE_PREFIX . "kategorier_resor WHERE
          resa_id = :tripid
          ;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':tripid', $this->tripid, \PDO::PARAM_INT);
        $sth->execute();
      }

      foreach ($this->categoryids as $categoryid) {

        $sql = "INSERT INTO " . TABLE_PREFIX . "kategorier_resor (
          resa_id,
          kategorier_id
        ) VALUES (
          :tripid,
          :categoriyid
        );";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':tripid', $this->tripid, \PDO::PARAM_INT);
        $sth->bindParam(':categoriyid', $categoryid, \PDO::PARAM_INT);

        $sth->execute();
      }


      if (!$mode) {
        $sql = "DELETE FROM " . TABLE_PREFIX . "hallplatser_resor WHERE
          resa_id = :tripid
          ;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':tripid', $this->tripid, \PDO::PARAM_INT);
        $sth->execute();
      }

      $i = 0;
      foreach ($this->stops['id'] as $stopid) {
        $sql = "INSERT INTO " . TABLE_PREFIX . "hallplatser_resor (
          resa_id,
          hallplatser_id,
          tid_in,
          tid_ut
        ) VALUES (
          :tripid,
          :stopid,
          :in,
          :out
        );";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':tripid', $this->tripid, \PDO::PARAM_INT);
        $sth->bindParam(':stopid', $stopid, \PDO::PARAM_INT);
        $sth->bindParam(':in', $this->stops['to'][$i], \PDO::PARAM_STR);
        $sth->bindParam(':out', $this->stops['from'][$i], \PDO::PARAM_STR);

        $sth->execute();
        $i++;
      }



      if (!$mode) {
        $sql = "DELETE FROM " . TABLE_PREFIX . "boenden_resor WHERE
          resa_id = :tripid
          ;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':tripid', $this->tripid, \PDO::PARAM_INT);
        $sth->execute();
      }

      $i = 0;
      foreach ($this->rooms['id'] as $roomid) {

        $sql = "INSERT INTO " . TABLE_PREFIX . "boenden_resor (
          resa_id,
          boenden_id,
          pris
        ) VALUES (
          :tripid,
          :roomid,
          :price
        );";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':tripid', $this->tripid, \PDO::PARAM_INT);
        $sth->bindParam(':roomid', $roomid, \PDO::PARAM_INT);
        $sth->bindParam(':price', $this->rooms['price'][$i], \PDO::PARAM_INT);

        $sth->execute();
        $i++;

      }

      if (!$mode) {
        $sql = "DELETE FROM " . TABLE_PREFIX . "datum WHERE
          resa_id = :tripid
          ;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':tripid', $this->tripid, \PDO::PARAM_INT);
        $sth->execute();
      }

      foreach($this->formateddates as $date) {
        $sql = "INSERT INTO " . TABLE_PREFIX . "datum (
          resa_id,
          datum
        ) VALUES (
          :tripid,
          :date
        );";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':tripid', $this->tripid, \PDO::PARAM_INT);
        $sth->bindParam(':date', $date, \PDO::PARAM_STR);
        $sth->execute();
    }
      $pdo->commit();


    } catch(\PDOException $e) {
      $pdo->rollBack();
      DBError::showError($e, __CLASS__, $sql);
      http_response_code(500);
      return false;
    }
    return true;
  }
}
