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
          <fieldset>
            <label for="trip-text-1-heading">Dag 1</label>
            <input type="text" maxlength="80" name="trip-text-1-heading" id="trip-text-1-heading" placeholder="Dag 1">
            <textarea type="text" name="trip-text-1-text" id="trip-text-1-text"></textarea>
          </fieldset>

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
            <h3>Ingår i resan</h3>
            <p>
              <input type="text" maxlength="80" name="trip-ingar-1" id="trip-tillagg-1" value="Resa med modern helturistbuss t/r">
            </p>
            <p>
              <button type="button" name="trip-add-paragraph" id="trip-add-paragraph">Fler tillägg</button>
              <button type="button" name="trip-remove-paragraph" id="trip-remove-paragraph">Färre tillägg</button>
            <p>
          </fieldset>

          <fieldset>
            <h3>Frivilliga tillägg</h3>
            <p>
              <input type="text" maxlength="80" name="trip-tillagg-1" id="trip-tillagg-1" placeholder="Tillägg">
              <input type="text" maxlength="80" name="trip-tillagg-1-pris" id="trip-tillagg-1-pris" placeholder="100">:-
            </p>
            <p>
              <button type="button" name="trip-add-paragraph" id="trip-add-paragraph">Fler tillägg</button>
              <button type="button" name="trip-remove-paragraph" id="trip-remove-paragraph">Färre tillägg</button>
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
                  var_dump($stop);
                  //echo "<td>" . <input type="checkbox" name="trip-address-required" id="trip-address-required" value="address-required">
                }
               ?>
              </tr></table>

              <input type="text" maxlength="80" name="trip-tillagg-1" id="trip-tillagg-1" placeholder="Tillägg">
              <input type="text" maxlength="80" name="trip-tillagg-1-pris" id="trip-tillagg-1-pris" placeholder="100">:-
            </p>
            <p>
              <button type="button" name="trip-add-paragraph" id="trip-add-paragraph">Fler tillägg</button>
              <button type="button" name="trip-remove-paragraph" id="trip-remove-paragraph">Färre tillägg</button>
            <p>
          </fieldset>

          <fieldset>
            <h3>Inställningar för bokning</h3>
            <p><input type="checkbox" name="trip-address-required" id="trip-address-required" value="address-required">Fysisk address behöver anges vid bokning.</p>
            <p><input type="checkbox" name="trip-personalid-required" id="trip-personalid-required" value="personalid-required">Personnummer behöver anges vid bokning.</p>
          </fieldset>

        </div>
      </form>

      <!--
      <div class="col-lg-3 col-md-6">
        <h2>Resor</h2>
        <ul>
          <li><a href="/adminp/nyresa/" title="Lägg in en ny resa">Ny resa</a></li>
          <li>Resa, datum | aktiv/inaktiv X</li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6">
        <h2>Kategorier</h2>
        <ul id="category-list">
          <li>
            <form action="/adminajax/newcategory" method="post" accept-charset="utf-8" id="form-new-category" enctype='application/json'>
              <input type="text" maxlength="80" name="name" placeholder="Kategori" required id="form-new-category-name">
              <input type="hidden" name="token" value="<?php echo $token ?>" class="form-token">
              <input type="submit" value="Skapa" id="form-new-category-submit">
            </form>
          </li>
          <li id="category-list-content"></li>
          <li id="category-list-error"></li>
        </ul>
        <div id="category-list-loading">
          <i class="fa fa-spinner fa-4x fa-spin" aria-hidden="true"></i>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
      <h2>Boenden</h2>
        <ul id="roomopt-list">
          <li>
            <form action="/adminajax/newroomopt" method="post" accept-charset="utf-8" id="form-new-roomopt" enctype='application/json'>
              <input type="text" maxlength="100" name="name" placeholder="Boendealternativ" required id="form-new-roomopt-name">
              <input type="hidden" name="token" value="<?php echo $token ?>" class="form-token">
              <input type="submit" value="Skapa" id="form-new-roomopt-submit">
            </form>
          </li>
          <li id="roomopt-list-content"></li>
          <li id="roomopt-list-error"></li>
        </ul>
        <div id="roomopt-list-loading">
          <i class="fa fa-spinner fa-4x fa-spin" aria-hidden="true"></i>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <h2>Hållplatser</h2>
        <ul id="stop-list">
          <li>
            <form action="/adminajax/newstop" method="post" accept-charset="utf-8" id="form-new-stop" enctype='application/json'>
              <input type="text" maxlength="80" name="name" placeholder="Plats, Ort" required id="form-new-stop-name">,
              <input type="hidden" name="token" value="<?php echo $token ?>" class="form-token">
              <input type="submit" value="Skapa" id="form-new-stop-submit">
            </form>
          </li>
          <li id="stop-list-content"></li>
          <li id="stop-list-error"></li>
        </ul>
        <div id="stop-list-loading">
          <i class="fa fa-spinner fa-4x fa-spin" aria-hidden="true"></i>
        </div>
      </div>
      <div class="col-lg-12 col-md-12">
        <h2>Bildgallerier</h2>
        <ul>
          <li>Nytt galleri</li>
          <li>Galleri | aktiv/inaktiv X</li>
        </ul>
      </div>
    -->
    </main>



    <?php
    include __DIR__ . '/shared/footer.php';

    } else {
      //Not logged in
      admin\includes\classes\Login::renderLoginForm();
    }
  }
}
