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




    ?>


    <main class="clearfix">
      <form action="/adminajax/newtrip" method="post" accept-charset="utf-8" id="trip" enctype='application/json'>
        <div class="col-md-12">
          <fieldset>
            <label for="trip-heading">Rubrik</label>
            <input type="text" maxlength="80" name="trip-heading" id="trip-heading">
          </fieldset>
          <fieldset>
            <label for="trip-summary">Summary</label>
            <textarea type="text" name="trip-summary" id="trip-summary"></textarea>
          </fieldset>
          <div id="trip-text">
            <fieldset id="trip-text-1">
              <label for="trip-text-heading[1]">Dag 1</label>
              <input type="text" maxlength="80" name="trip-text-heading[1]" id="trip-text-1-heading" placeholder="Dag 1">
              <textarea type="text" name="trip-text-text[1]" id="trip-text-1-text"></textarea>
            </fieldset>
          </div>

          <fieldset>
            <button type="button" name="trip-add-paragraph" id="trip-add-paragraph">Lägg till en dag/paragraf</button>
            <button type="button" name="trip-remove-paragraph" id="trip-remove-paragraph">Ta bort en dag/paragraf</button>
          </fieldset>

          <fieldset>
            <label for="trip-text-hotel-heading">Hotel</label>
            <input type="text" maxlength="80" name="trip-text-hotel-heading" id="trip-text-1-heading" placeholder="Vårt hotel">
            <textarea type="text" name="trip-text-hotel-text" id="trip-text-1-text"></textarea>
            <input type="text" maxlength="80" name="trip-text-hotel-link" id="trip-text-1-heading" placeholder="http://www.hotel.se">
          </fieldset>

          <fieldset>
            <label for="trip-facebook">Facebook event url</label>
            <input type="text" maxlength="80" name="trip-facebook" id="trip-text-1-heading" placeholder="Vårt hotel">
          </fieldset>

          <fieldset>
            <h3>Ingår i resan</h3>
            <div id="includes-list">
              <p id="include-1">
                <input type="text" maxlength="80" name="trip-ingar[1]" id="trip-tillagg-1" value="Resa med modern helturistbuss t/r">
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
              <input type="text" maxlength="80" name="trip-tillagg[1]" id="trip-tillagg-1" placeholder="Tillägg">
              <input type="text" maxlength="80" name="trip-tillagg-pris[1]" id="trip-tillagg-1-pris" placeholder="100">:-
            </p>
          </div>
            <p>
              <button type="button" name="trip-add-addon" id="trip-add-addon">Fler tillägg</button>
              <button type="button" name="trip-remove-addon" id="trip-remove-addon">Färre tillägg</button>
            <p>
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
                  echo "<tr><td><input type='checkbox' name='usestop-" . $stop['id'] . "' class='stop-checkbox'></td><td>" . $stop['plats'];
                  echo "</td><td><input type='text' name='stopfrom-" . $stop['id'] . "' placeholder='00:00' class='stop-input'></td>";
                  echo "<td><input type='text' name='stopto-" . $stop['id'] . "' placeholder='00:00' class='stop-input'></td></tr>";
                }
               ?>
              </tr></table>
          </fieldset>

          <fieldset>
            <h3>Inställningar för bokning</h3>
            <p><input type="checkbox" name="trip-address-required" id="trip-address-required" value="address-required" checked>Fysisk address behöver anges vid bokning.</p>
            <p><input type="checkbox" name="trip-personalid-required" id="trip-personalid-required" value="personalid-required">Personnummer behöver anges vid bokning.</p>
          </fieldset>

        </div>
      </form>
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
