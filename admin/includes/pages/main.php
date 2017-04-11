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
  $token = bin2hex(openssl_random_pseudo_bytes(32));
  $_SESSION["token"] = $token;

  if (admin\includes\classes\Login::isLoggedIn() === TRUE) {
    //Is logged in

    header('Content-type: text/html; charset=utf-8');
    include __DIR__ . '/shared/header.php';

    $pageTitle = "Admin Huvudmeny";

    $pdo = DB::get();

    try {
      $sql = "SELECT * FROM " . TABLE_PREFIX . "resor;";
      $sth = $pdo->prepare($sql);
      $sth->execute();
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
    }



    ?>


    <main class="clearfix">
      <div class="col-lg-3 col-md-7">
        <h2>Resor</h2>
        <ul>
          <li><a href="/adminp/nyresa/" title="Lägg in en ny resa">Ny resa</a></li>
          <li>Resa, datum | aktiv/inaktiv X</li>
        </ul>
      </div>
      <div class="col-lg-2 col-md-5">
        <h2>Kategorier</h2>
        <ul id="category-list">
          <li>
            <form action="/adminajax/newcategory" method="post" accept-charset="utf-8" id="form-new-category" enctype='application/json'>
              <input type="text" maxlength="80" name="name" placeholder="Ny kateogori" required id="form-new-category-name">
              <input type="hidden" name="token" value="<?php echo $token ?>" class="form-token">
              <input type="submit" value="Skapa" id="form-new-category-submit">
            </form>
          </li>
          <!--<li>Kategori | aktiv/inaktiv X</li>-->
        </ul>
        <div id="category-list-loading">
          <i class="fa fa-spinner fa-4x fa-spin" aria-hidden="true"></i>
        </div>
      </div>
      <div class="col-lg-2 col-md-4">
      <h2>Boenden</h2>
      <ul>
        <li><a href="/adminp/nyboende/" title="Lägg in en ny boendetyp">Nytt boende</a></li>
        <li>Benämning | aktiv/inaktiv X</li>
      </ul>
      </div>
      <div class="col-lg-2 col-md-4">
        <h2>Hållplatser</h2>
        <ul>
          <li><a href="/adminp/nyhallplats/" title="Lägg in en nytt stopp">Ny hållplats</a></li>
          <li>Plats, ort | aktiv/inaktiv X</li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-4">
        <h2>Bildgallerier</h2>
        <ul>
          <li>Nytt galleri</li>
          <li>Galleri | aktiv/inaktiv X</li>
        </ul>
      </div>
    </main>



    <?php
    include __DIR__ . '/shared/footer.php';

    } else {
      //Not logged in
      admin\includes\classes\Login::renderLoginForm();
    }
  }
}
