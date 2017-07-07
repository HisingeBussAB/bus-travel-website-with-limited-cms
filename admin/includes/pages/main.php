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
 * The administration main menu page
 */
class Main {

  /**
   * @uses Sessions
   * @uses Login
   * @uses DB
   * @uses DBError
   * @uses resetToken
   * @uses '/shared/header.php'
   * @uses '/shared/footer.php'
   */
  public static function showAdminMain() {



  root\includes\classes\Sessions::secSessionStart();




  if (admin\includes\classes\Login::isLoggedIn() === TRUE) {
    //Is logged in

    $token = root\includes\classes\Tokens::getCommonToken(1300);

    $pageTitle = "Rekå Admin - Huvudmeny";
    $more_stylesheets = "<link href='/dependencies/jquery-confirm/dist/jquery-confirm.min.css' rel='stylesheet'>";


    header('Content-type: text/html; charset=utf-8');
    include __DIR__ . '/shared/header.php';

    $pdo = DB::get();

    try {
      $sql = "SELECT nyheter FROM " . TABLE_PREFIX . "nyheter WHERE id = 1;";
      $sth = $pdo->prepare($sql);
      $sth->execute();
      $result = $sth->fetch(\PDO::FETCH_ASSOC);

    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
    }



    ?>


    <main class="clearfix">
      <div class="col-lg-3 col-md-6">
        <h2>Resor</h2>
        <ul id="trip-list">
          <li><a href="/adminp/nyresa/" title="Lägg in en ny resa"><button>Skapa ny resa</button></a></li>
          <li id="trip-list-content"></li>
          <li id="trip-list-error"></li>
        </ul>
        <div id="trip-list-loading">
          <i class="fa fa-spinner fa-4x fa-spin" aria-hidden="true"></i>
        </div>
      </div>
      <div class="col-lg-2 col-md-6">
        <h2>Kategorier</h2>
        <ul id="category-list">
          <li>
            <form action="/adminajax/newcategory" method="post" accept-charset="utf-8" id="form-new-category" enctype='application/json'>
              <input type="text" maxlength="80" name="name" placeholder="Kategori" required id="form-new-category-name">
              <input type="hidden" name="tokenid" value="<?php echo $token['id'] ?>" class="form-token-id">
              <input type="hidden" name="token" value="<?php echo $token['token'] ?>" class="form-token">
              <button type="submit"id="form-new-category-submit" class="button-right">Skapa</button>
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
              <input type="hidden" name="tokenid" value="<?php echo $token['id'] ?>" class="form-token-id">
              <input type="hidden" name="token" value="<?php echo $token['token'] ?>" class="form-token">
              <button type="submit"id="form-new-roomopt-submit" class="button-right">Skapa</button>
            </form>
          </li>
          <li id="roomopt-list-content"></li>
          <li id="roomopt-list-error"></li>
        </ul>
        <div id="roomopt-list-loading">
          <i class="fa fa-spinner fa-4x fa-spin" aria-hidden="true"></i>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <h2>Hållplatser</h2>
        <ul id="stop-list">
          <li>
            <form action="/adminajax/newstop" method="post" accept-charset="utf-8" id="form-new-stop" enctype='application/json'>
              <input type="text" maxlength="80" name="name" placeholder="Plats" required id="form-new-stop-name">
              <input type="text" maxlength="80" name="ort" placeholder="Ort" required id="form-new-stop-ort">
              <input type="hidden" name="tokenid" value="<?php echo $token['id'] ?>" class="form-token-id">
              <input type="hidden" name="token" value="<?php echo $token['token'] ?>" class="form-token">
              <button type="submit"id="form-new-stop-submit" class="button-right">Skapa</button>
              <div class="clearfix">
                <div class="sort-stop"><a href="#" id="sort-stop-name">Sortera på plats</a></div>
                <div class="sort-stop"><a href="#" id="sort-stop-ort">Sortera på ort</a></div>
              </div>
            </form>

          </li>
          <li id="stop-list-content"></li>
          <li id="stop-list-error"></li>
        </ul>
        <div id="stop-list-loading">
          <i class="fa fa-spinner fa-4x fa-spin" aria-hidden="true"></i>
        </div>
      </div>
      <div class="col-md-6">
        <h2>Nyheter</h2>
        <form action="/adminajax/news" method="post" accept-charset="utf-8" id="form-news" enctype='application/json'>
          <input type="hidden" name="tokenid" value="<?php echo $token['id'] ?>" class="form-token-id">
          <input type="hidden" name="token" value="<?php echo $token['token'] ?>" class="form-token">
          <div><textarea name="nyheter"><?php echo $result['nyheter']; ?></textarea></div>
          <button type="submit">Spara</button>
        </form>
      </div>
      <div class="col-md-6">
        <h2>Bildgallerier</h2>
        <ul>
          <li>Nytt galleri</li>
          <li>Galleri | aktiv/inaktiv X</li>
        </ul>
      </div>
    </main>



    <?php
    include __DIR__ . '/shared/scripts.php';
    echo "<script src='/dependencies/jquery-confirm/dist/jquery-confirm.min.js'></script>";
    echo "<script src='/admin/js/main.js'></script>";
    include __DIR__ . '/shared/footer.php';

    } else {
      //Not logged in
      admin\includes\classes\Login::renderLoginForm();
    }
  }
}
