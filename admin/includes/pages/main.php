<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * ADMIN
 *
 */
namespace HisingeBussAB\RekoResor\website\admin\includes\pages;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\admin as admin;


class Main {
  public static function showAdminMain() {
  root\includes\classes\Sessions::secSessionStart();
  if (admin\includes\classes\Login::isLoggedIn() !== TRUE) {
    admin\includes\classes\Login::renderLoginForm();
  } else {

    header('Content-type: text/html; charset=utf-8');
    include __DIR__ . '/shared/header.php';

    $pageTitle = "Admin Huvudmeny";

    ?>


    <main class="clearfix">
      <div class="col-md-4">
        <h2>Resor</h2>
        <ul>
          <li><a href="/adminp/nyresa/" title="Lägg in en ny resa">Ny resa</a></li>
          <li>Resa, datum | aktiv/inaktiv X</li>
        </ul>
      </div>
      <div class="col-md-2">
        <h2>Kategorier</h2>
        <ul>
          <li><a href="/adminp/nykategori/" title="Lägg in en ny kategori">Ny kategori</a></li>
          <li>Kategori | aktiv/inaktiv X</li>
        </ul>
      </div>
      <div class="col-md-2">
        <h2>Hållplatser</h2>
        <ul>
          <li><a href="/adminp/nyhallplats/" title="Lägg in en nytt stopp">Ny hållplats</a></li>
          <li>Plats, ort | aktiv/inaktiv X</li>
        </ul>
      </div>
      </div>
      <div class="col-md-4">
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
