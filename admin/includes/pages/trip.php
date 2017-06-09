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

    try {
      $sql = "SELECT * FROM " . TABLE_PREFIX . "resor;";
      $sth = $pdo->prepare($sql);
      $sth->execute();
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
    }

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


    ?>


    <main class="clearfix">
      <form action="/adminajax/newtrip" method="post" accept-charset="utf-8" id="trip" enctype='application/json'>
        <div class="col-md-12">
          <fieldset>
            <input type="hidden" name="tripid" value="<?php echo $tripid ?>">
            <label for="trip-heading">Rubrik</label>
            <input type="text" maxlength="200" name="trip-heading" id="trip-heading">
          </fieldset>
          <fieldset>
            <label for="trip-summary">Summary</label>
            <textarea type="text" name="trip-summary" id="trip-summary"></textarea>
          </fieldset>

          <div id="trip-text">
            <fieldset id="trip-text-1">
              <label for="trip-text-heading[1]">Dag 1</label>
              <input type="text" maxlength="80" name="trip-text-heading[1]" id="trip-text-1-heading" placeholder="Dag 1">
              <textarea type="text" name="trip-text[1]" id="trip-text-1-text"></textarea>
            </fieldset>
          </div>

          <fieldset>
            <button type="button" name="trip-add-paragraph" id="trip-add-paragraph">Lägg till en dag/paragraf</button>
            <button type="button" name="trip-remove-paragraph" id="trip-remove-paragraph">Ta bort en dag/paragraf</button>
          </fieldset>

          <fieldset>
            <label for="trip-text-hotel-heading">Hotel</label>
            <input type="text" maxlength="100" name="trip-text-hotel-heading" id="trip-text-1-heading" placeholder="Hotellets namn">
            <textarea type="text" name="trip-text-hotel-text" id="trip-text-1-text" placeholder="Hotellvägen 5&#10;888 88 Hotellstaden&#10;+46888888"></textarea>
            <input type="text" maxlength="250" name="trip-text-hotel-link" id="trip-text-1-heading" placeholder="http://www.hotel.se">
          </fieldset>

          <fieldset>
            <h3>Avresedatum</h3>
            <div id="dates-list">
              <p id="date-1">
                <input type="date" name="trip-date[1]" id="trip-date-1" placeholder="YYYY-MM-DD">
              </p>
            </div>
            <p>
              <button type="button" name="trip-add-date" id="trip-add-date">Fler avgångar</button>
              <button type="button" name="trip-remove-date" id="trip-remove-date">Färre avgångar</button>
            <p>
          </fieldset>

          <fieldset>
            <h3>Antal dagar</h3>
            <p>
              <input type="number" name="trip-duration" id="trip-duration" placeholder="0">
            </p>
          </fieldset>

          <fieldset>
            <label for="trip-facebook">Facebook event url</label>
            <input type="text" maxlength="250" name="trip-facebook" id="trip-text-1-heading" placeholder="Vårt hotel">
          </fieldset>

          <fieldset>
            <h3>Ingår i resan</h3>
            <div id="includes-list">
              <p id="include-1">
                <input type="text" maxlength="150" name="trip-ingar[1]" id="trip-tillagg-1" value="Resa med modern helturistbuss t/r">
              </p>
            </div>
            <p>
              <button type="button" name="trip-add-includes" id="trip-add-includes">Fler ingår</button>
              <button type="button" name="trip-remove-includes" id="trip-remove-includes">Färre ingår</button>
            <p>
          </fieldset>

          <fieldset>
            <h3>Frivilliga tillägg</h3>
            <div id="addons-list">
            <p id="addon-1">
              <input type="text" maxlength="79" name="trip-tillagg[1]" id="trip-tillagg-1" placeholder="Tillägg">
              <input type="number" name="trip-tillagg-pris[1]" id="trip-tillagg-1-pris" placeholder="0"> :-
            </p>
          </div>
            <p>
              <button type="button" name="trip-add-addon" id="trip-add-addon">Fler tillägg</button>
              <button type="button" name="trip-remove-addon" id="trip-remove-addon">Färre tillägg</button>
            <p>
          </fieldset>

          <fieldset>
            <h3>Grundpris</h3>
            <p>
              <input type="number" name="trip-price" id="trip-price" placeholder="0"> :-
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
                  echo "<tr><td><input type='checkbox' name='useroom[]' value='" . $room['id'] . "' class='room-checkbox'></td><td>" . $room['boende'];
                  echo "</td>";
                  echo "<td><input type='number' name='roomprice[" . $room['id'] . "]' placeholder='0' class='room-price'> :-</td></tr>";
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
                  echo "<tr><td><input type='checkbox' name='usestop[]' value='" . $stop['id'] . "' class='stop-checkbox'></td><td>" . $stop['plats'];
                  echo "</td><td><input type='time' name='stopfrom[" . $stop['id'] . "]' placeholder='HH:MM' class='stop-input'></td>";
                  echo "<td><input type='time' name='stopto[" . $stop['id'] . "]' placeholder='HH:MM' class='stop-input'></td></tr>";
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
                  echo "<tr><td><input type='checkbox' name='usecategory[]' value='" . $category['id'] . "' class='category-checkbox'></td><td>" . $category['kategori'];
                  echo "</td></tr>";
                }
               ?>
              </tr></table>
          </fieldset>

          <fieldset>
            <h3>Inställningar för bokning</h3>
            <p><input type="checkbox" name="trip-address-required" id="trip-address-required" value="address-required" checked>Fysisk address behöver anges vid bokning.</p>
            <p><input type="checkbox" name="trip-personalid-required" id="trip-personalid-required" value="personalid-required">Personnummer behöver anges vid bokning.</p>
          </fieldset>

          <fieldset>
            <input type="hidden" name="token" value="<?php echo $token ?>">
          </fieldset>

          <fieldset>
            <p><button type="submit">Spara resa</button></p>
          </fieldset>
        </div>
      </form>

        <h3>Lägg till bilder</h3>
        <div id="pictures-list">
          <p id="picture-1">
            <input type="file" name="trip-bild[1]" id="trip-picture-1" value="">
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
