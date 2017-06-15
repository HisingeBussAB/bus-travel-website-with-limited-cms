<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * Admin main menu
 *
 */
namespace HisingeBussAB\RekoResor\website\admin\includes\pages;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\admin as admin;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;
use HisingeBussAB\RekoResor\website\includes\classes\Functions as functions;

/**
 * Creates new or edits a trip post.
 */
class Trip {

  /**
   * @uses Sessions
   * @uses Login
   * @uses DB
   * @uses DBError
   * @uses resetToken
   * @uses '/shared/header.php'
   * @uses '/shared/footer.php'
   */
  public static function showTrip($tripid = "new") {

  root\includes\classes\Sessions::secSessionStart();
  $token = root\includes\classes\ResetToken::getRandomToken();

  if (admin\includes\classes\Login::isLoggedIn() === TRUE) {
    //Is logged in

    header('Content-type: text/html; charset=utf-8');
    include __DIR__ . '/shared/header.php';

    $pageTitle = "Rekå Admin - Ny resa";

    $pdo = DB::get();

    //Form structure DB catches
    try {
      $sql = "SELECT * FROM " . TABLE_PREFIX . "hallplatser;";
      $sth = $pdo->prepare($sql);
      $sth->execute();
      $stops = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
    }

    try {
      $sql = "SELECT * FROM " . TABLE_PREFIX . "boenden;";
      $sth = $pdo->prepare($sql);
      $sth->execute();
      $rooms = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
    }

    try {
      $sql = "SELECT * FROM " . TABLE_PREFIX . "kategorier;";
      $sth = $pdo->prepare($sql);
      $sth->execute();
      $categories = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
    }

    //ID specific DB catch
    if ($tripid != "new") {
      $tripid = filter_var(trim($tripid), FILTER_SANITIZE_NUMBER_INT);
      try {
        $sql = "SELECT * FROM " . TABLE_PREFIX . "resor WHERE id = :id;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':id', $tripid, \PDO::PARAM_INT);
        $sth->execute();
        $trip = $sth->fetch(\PDO::FETCH_ASSOC);
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql);
      }

      try {
        $sql = "SELECT * FROM " . TABLE_PREFIX . "datum WHERE resa_id = :id ORDER BY datum;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':id', $tripid, \PDO::PARAM_INT);
        $sth->execute();
        $departures = $sth->fetchAll(\PDO::FETCH_ASSOC);
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql);
      }

      try {
        $sql = "SELECT * FROM " . TABLE_PREFIX . "tillaggslistor WHERE resa_id = :id;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':id', $tripid, \PDO::PARAM_INT);
        $sth->execute();
        $addons = $sth->fetchAll(\PDO::FETCH_ASSOC);
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql);
      }

      try {
        $sql = "SELECT hallplats_id, tid_in, tid_ut FROM " . TABLE_PREFIX . "resor_hallplatser WHERE resa_id = :id;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':id', $tripid, \PDO::PARAM_INT);
        $sth->execute();
        $stops_trip = $sth->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_UNIQUE);
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql);
      }

      try {
        $sql = "SELECT kategori_id FROM " . TABLE_PREFIX . "kategorier_resor WHERE resa_id = :id;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':id', $tripid, \PDO::PARAM_INT);
        $sth->execute();
        $categories_trip = $sth->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_UNIQUE);
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql);
      }

      try {
        $sql = "SELECT boende_id, pris FROM " . TABLE_PREFIX . "boenden_resor WHERE resa_id = :id;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':id', $tripid, \PDO::PARAM_INT);
        $sth->execute();
        $rooms_trip = $sth->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_UNIQUE);

      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql);
      }
    }

    if (isset($trip)) {

      $textheads = functions::get_string_between($trip['program'], "<h3>", "</h3>");
      $textbodies = functions::get_string_between($trip['program'], "<p>", "</p>");
      if (count($textheads) !== count($textbodies))
      {
        echo "<p class='php-warning'>Varning: Troligen felformaterrad programdata. Det går bra att fortsätta men kontrollera att inget saknas i programtexten.</p>";
      }

      $hotelhead = functions::get_string_between($trip['hotel'], "<h3>", "</h3>");
      $hoteltext = functions::get_string_between($trip['hotel'], "<p>", "</p>");
      if (count($hotelhead) !== count($hoteltext))
      {
        echo "<p class='php-warning'>Varning: Hotellinformation troligen korruperad. Det går att fortsätta men kontrollera att hotellfälten stämmer.</p>";
      }
      $includes = functions::get_string_between($trip['ingar'], "<p>", "</p>");

    }
    ?>


    <main class="clearfix">
      <form action="/adminajax/newtrip" method="post" accept-charset="utf-8" id="trip" enctype='application/json'>
        <div class="col-md-12">
          <fieldset>
            <label for="trip-heading">Rubrik</label>
            <input type="text" maxlength="200" name="trip-heading" id="trip-heading" placeholder="Resansnamn" <?php if (isset($trip)) {echo "value='" . $trip['namn'] . "'";} ?>>
          </fieldset>
          <fieldset>
            <label for="trip-summary">Summary</label>
            <textarea type="text" name="trip-summary" id="trip-summary" placeholder="Ingress"><?php if (isset($trip)) {echo functions::br2nl($trip['ingress']);} ?></textarea>
          </fieldset>


          <div id="trip-text">
            <?php
            if (!isset($trip)) {
              echo "<fieldset id='trip-text-1' class='trip-text'>";
              echo  "<label for='trip-text-heading[1]'>Dag 1</label>";
              echo  "<input type='text' maxlength='80' name='trip-text-heading[1]' id='trip-text-heading-1' placeholder='Dag 1'>";
              echo  "<textarea type='text' name='trip-text[1]' id='trip-text-1-text' placeholder='Programtext dag 1'></textarea>";
              echo "</fieldset>";
            } else {
              foreach ($textheads as $id=>$texthead) {
                echo "<fieldset id='trip-text-" . ($id+1) . "' class='trip-text'>";
                echo "<label for='trip-text-heading[" . ($id+1) . "]'>Dag " . ($id+1) . "</label>";
                echo "<input type='text' maxlength='80' name='trip-text-heading[" . ($id+1) . "]' class='trip-text-heading' value='" . $texthead . "'>";
                echo "<textarea type='text' name='trip-text[" . ($id+1) . "]' class='trip-text-text'>" . functions::br2nl($textbodies[$id]) . "</textarea>";
                echo "</fieldset>";
              }
            }
            ?>
          </div>

          <fieldset>
            <button type="button" name="trip-add-paragraph" id="trip-add-paragraph">Lägg till en dag/paragraf</button>
            <button type="button" name="trip-remove-paragraph" id="trip-remove-paragraph">Ta bort en dag/paragraf</button>
          </fieldset>

          <fieldset>
            <label for="trip-text-hotel-heading">Hotel</label>
            <input type="text" maxlength="100" name="trip-text-hotel-heading" id="trip-hotel-heading" placeholder="Hotellets namn" value="<?php if (isset($trip)) {echo $hotelhead[0];} ?>">
            <textarea type="text" name="trip-text-hotel-text" id="trip-hotel-text" placeholder="Hotellvägen 5&#10;888 88 Hotellstaden&#10;+46888888"><?php if (isset($trip)) {echo functions::br2nl($hoteltext[0]);} ?></textarea>
            <input type="text" maxlength="250" name="trip-text-hotel-link" id="trip-hotel-link" placeholder="http://www.hotel.se" value="<?php if (isset($trip)) {echo $trip['hotellink'];} ?>">
          </fieldset>

          <fieldset>
            <h3>Avresedatum</h3>
            <div id="dates-list">
              <?php
              if (!isset($trip)) {
               ?>
              <p id="date-1" class="date-item">
                <input type="date" name="trip-date[1]" id="trip-date-1" placeholder="YYYY-MM-DD">
              </p>
              <?php
              } else {
                foreach ($departures as $id=>$departure) {
                  echo "<p id='date-" . ($id+1) . "' class='date-item'>";
                  echo  "<input type='date' name='trip-date[" . ($id+1) . "]' id='trip-date-" . ($id+1) . "' placeholder='YYYY-MM-DD' value='" . $departure['datum'] . "'>";
                  echo "</p>";
                }
              }
               ?>
            </div>
            <p>
              <button type="button" name="trip-add-date" id="trip-add-date">Fler avgångar</button>
              <button type="button" name="trip-remove-date" id="trip-remove-date">Färre avgångar</button>
            <p>
          </fieldset>

          <fieldset>
            <h3>Antal dagar</h3>
            <p>
              <input type="number" name="trip-duration" id="trip-duration" placeholder="0" value="<?php if (isset($trip)) {echo $trip['antaldagar'];} ?>">
            </p>
          </fieldset>

          <fieldset>
            <label for="trip-facebook">Facebook event url</label>
            <input type="text" maxlength="250" name="trip-facebook" id="trip-text-1-heading" placeholder="Vårt hotel" value="<?php if (isset($trip)) {echo $trip['hotellink'];} ?>">
          </fieldset>

          <fieldset>
            <h3>Ingår i resan</h3>
            <div id="includes-list">
              <?php
              if (!isset($trip)) {
               ?>
              <p id="include-1" class="include-item">
                <input type="text" maxlength="150" name="trip-ingar[1]" id="trip-tillagg-1" value="Resa med modern helturistbuss t/r">
              </p>
              <?php
              } else {
                foreach ($includes as $id=>$include) {
                  echo "<p id='include-" . ($id+1) . "' class='include-item'>";
                  echo  "<input type='text' maxlength='150' name='trip-ingar[" . ($id+1) . "]' id='trip-tillagg-" . ($id+1) . "' value='" . $include . "'>";
                  echo "</p>";
                }
              }
               ?>
            </div>
            <p>
              <button type="button" name="trip-add-includes" id="trip-add-includes">Fler ingår</button>
              <button type="button" name="trip-remove-includes" id="trip-remove-includes">Färre ingår</button>
            <p>
          </fieldset>

          <fieldset>
            <h3>Frivilliga tillägg</h3>
            <div id="addons-list">
            <?php
            if (!isset($trip)) {
             ?>
            <p id="addon-1" class="addon-item">
              <input type="text" maxlength="79" name="trip-tillagg[1]" id="trip-tillagg-1" placeholder="Tillägg">
              <input type="number" name="trip-tillagg-pris[1]" id="trip-tillagg-1-pris" placeholder="0"> :-
            </p>
            <?php
            } else {
              foreach ($addons as $id=>$addon) {
                echo "<p id='addon-" . ($id+1) . "' class='addon-item'>";
                echo  "<input type='text' maxlength='79' name='trip-tillagg[" . ($id+1) . "]' id='trip-tillagg-" . ($id+1) . "' placeholder='Tillägg' value='" . $addon['namn'] . "'>";
                echo  "<input type='number' name='trip-tillagg-pris[" . ($id+1) . "]' id='trip-tillagg-" . ($id+1) . "-pris' placeholder='0' value='" . $addon['pris'] . "'> :-";
                echo "</p>";
              }
            }
             ?>
          </div>
            <p>
              <button type="button" name="trip-add-addon" id="trip-add-addon">Fler tillägg</button>
              <button type="button" name="trip-remove-addon" id="trip-remove-addon">Färre tillägg</button>
            <p>
          </fieldset>

          <fieldset>
            <h3>Grundpris</h3>
            <p>
              <input type="number" name="trip-price" id="trip-price" placeholder="0" value="<?php if (isset($trip)) {echo $trip['pris'];} ?>"> :-
            </p>
          </fieldset>

          <fieldset>
            <h3>Boenden</h3>
            <p>
              <table><tr>
                <td>Använd</td>
                <td>Boendetyp</td>
                <td>Pris</td>
              </tr>
              <?php
                foreach($rooms as $room) {
                  if ((isset($trip)) && (array_key_exists($room['id'], $rooms_trip))) {
                    echo "<tr><td><input type='checkbox' name='useroom[]' value='" . $room['id'] . "' class='room-checkbox' checked></td><td>" . $room['boende'];
                    echo "</td>";
                    echo "<td><input type='number' name='roomprice[" . $room['id'] . "]' placeholder='0' class='room-price' value='" . $rooms_trip[$room['id']]['pris'] . "'> :-</td></tr>";
                  } else {
                    echo "<tr><td><input type='checkbox' name='useroom[]' value='" . $room['id'] . "' class='room-checkbox'></td><td>" . $room['boende'];
                    echo "</td>";
                    echo "<td><input type='number' name='roomprice[" . $room['id'] . "]' placeholder='0' class='room-price'> :-</td></tr>";
                  }
                }
               ?>
              </tr></table>
          </fieldset>

          <fieldset>
            <h3>Turlista</h3>
            <p>
              <table><tr>
                <td>Stannar</td>
                <td>Plats</td>
                <td>Ut</td>
                <td>Hem</td>
              </tr>
              <?php
                foreach($stops as $stop) {
                  if ((isset($trip)) && (array_key_exists($stop['id'], $stops_trip))) {
                    echo "<tr><td><input type='checkbox' name='usestop[]' value='" . $stop['id'] . "' class='stop-checkbox' checked></td><td>" . $stop['plats'];
                    echo "</td><td><input type='time' name='stopfrom[" . $stop['id'] . "]' placeholder='HH:MM' class='stop-input' value='" . $stops_trip[$stop['id']]['tid_in'] . "'></td>";
                    echo "<td><input type='time' name='stopto[" . $stop['id'] . "]' placeholder='HH:MM' class='stop-input' value='" . $stops_trip[$stop['id']]['tid_ut'] . "'></td></tr>";
                  } else {
                    echo "<tr><td><input type='checkbox' name='usestop[]' value='" . $stop['id'] . "' class='stop-checkbox'></td><td>" . $stop['plats'];
                    echo "</td><td><input type='time' name='stopfrom[" . $stop['id'] . "]' placeholder='HH:MM' class='stop-input'></td>";
                    echo "<td><input type='time' name='stopto[" . $stop['id'] . "]' placeholder='HH:MM' class='stop-input'></td></tr>";
                }
              }
               ?>
              </tr></table>
          </fieldset>

          <fieldset>
            <h3>Kategorier</h3>
            <p>
              <table><tr>
                <td>Använd</td>
                <td>Boendetyp</td>
              </tr>
              <?php
                foreach($categories as $category) {
                  if ((isset($trip)) && (array_key_exists($category['id'], $categories_trip))) {
                    echo "<tr><td><input type='checkbox' name='usecategory[]' value='" . $category['id'] . "' class='category-checkbox' checked></td><td>" . $category['kategori'];
                    echo "</td></tr>";
                  } else {
                    echo "<tr><td><input type='checkbox' name='usecategory[]' value='" . $category['id'] . "' class='category-checkbox'></td><td>" . $category['kategori'];
                    echo "</td></tr>";
                  }
                }
               ?>
              </tr></table>
          </fieldset>

          <fieldset>
            <h3>Inställningar för bokning</h3>
            <p><input type="checkbox" name="trip-address-required" id="trip-address-required" value="address-required"<?php if (isset($trip)) {if ($trip['fysiskadress'] == 1) { echo " checked "; }} else { echo " checked "; } ?>>Fysisk address behöver anges vid bokning.</p>
            <p><input type="checkbox" name="trip-personalid-required" id="trip-personalid-required" value="personalid-required"<?php if (isset($trip)) {if ($trip['personnr'] == 1) { echo " checked "; }} ?>>Personnummer behöver anges vid bokning.</p>
          </fieldset>

          <fieldset>
            <input type="hidden" name="tripid" value="<?php echo $tripid ?>">
            <input type="hidden" name="token" value="<?php echo $token ?>">
          </fieldset>

          <fieldset>
            <p><button type="submit">Spara resa</button></p>
          </fieldset>
        </div>
      </form>

        <h3>Bilder</h3>
        <div id="pictures">
          <p id="pictures-list">

            <?php
            $server_path = __DIR__ . '/../../../upload/resor/' . $trip['bildkatalog'] . '/';
            $web_path = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/" . $trip['bildkatalog'] . "/";
            $files = functions::get_img_files($server_path);
            $i = 0;
            foreach ($files as $file) {
              echo "<form>";
              echo "<ul><li>";
              if ($i === 0) { echo "Huvudbild"; } else { echo "Bild " . $i; }
              echo "</li><li><a href='" . $web_path . $file['file'] . "' target='_blank'><img src='" . $web_path . $file['thumb'] . "' class='picture-list-thumb'></a></li>";
              echo "<li><input type='file' name='new-file' name= value=''><input type='hidden' name='old-file' value='" . $server_path . $file . "'></li>";
              echo "<li><button type='submit' name='' >Byt ut</button></li></ul>";
              echo "</form>";
              $i++;
            }



             ?>
          </p>
          <p id="picture-new">
            <input type="file" name="trip-bild" id="trip-picture" value="">
          </p>
          <p id="picture-new">
            <input type="file" name="trip-bild" id="trip-picture" value="">
          </p>
        </div>

        <h3>PDF program</h3>
        <p id="pdf-new">
          <input type="file" name="trip-pdf" id="trip-pdf" value="">
        </p>
        </div>
        <p>
          <button type="button" name="trip-add-picture" id="trip-add-picture">Fler bilder</button>
          <button type="button" name="trip-remove-picture" id="trip-remove-picture">Färre bilder</button>
        <p>


    </main>


    <?php
    include __DIR__ . '/shared/scripts.php';
    echo "<script src='/admin/js/tripform.js'></script>";
    include __DIR__ . '/shared/footer.php';

    } else {
      //Not logged in
      admin\includes\classes\Login::renderLoginForm();
    }
  }
}
