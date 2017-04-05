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
   * @uses '/shared/header.php'
   * @uses '/shared/footer.php'
   */
  public static function showAdminMain() {

  root\includes\classes\Sessions::secSessionStart();

  if (admin\includes\classes\Login::isLoggedIn() !== TRUE) {
    admin\includes\classes\Login::renderLoginForm();
  } else {

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
        <ul>
          <li><a href="/adminp/nykategori/" title="Lägg in en ny kategori">Ny kategori</a></li>
          <li>Kategori | aktiv/inaktiv X</li>
        </ul>
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

  }
  }
}
