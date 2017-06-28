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
 * Edits category settings.
 */
class Category {


  public static function showCategory($id = false) {

    root\includes\classes\Sessions::secSessionStart();
    $token = root\includes\classes\ResetToken::getRandomToken();

    if (admin\includes\classes\Login::isLoggedIn() === TRUE) {
      //Is logged in
      try {
        $pageTitle = "Rekå Admin - Ändra kategori";
        $more_stylesheets = "<link href='/admin/css/trip.min.css' rel='stylesheet'>";
        $show_navigation = true;

        header('Content-type: text/html; charset=utf-8');
        include __DIR__ . '/shared/header.php';

        $pdo = DB::get();

        if (!$id) {
          throw new \RuntimeException("<p>Ingen kategori är vald. Gå tillbaka</p>");
        }

        $id = filter_var(trim($id), FILTER_SANITIZE_NUMBER_INT);

        try {
          $sql = "SELECT * FROM " . TABLE_PREFIX . "kategorier WHERE id = :id;";
          $sth = $pdo->prepare($sql);
          $sth->bindParam(':id', $id, \PDO::PARAM_INT);
          $sth->execute();
          $cat = $sth->fetch(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
          DBError::showError($e, __CLASS__, $sql);
          throw new \RuntimeException("<p>Databasfel! Kontakta systemadministratören om problemet består.</p>");
        }

        if (!$cat) {
          http_response_code(404);
          throw new \RuntimeException("<p>Någon kategori med id:$id kan inte hittas i databasen.</p>");
        }

        ?>
        <main class="clearfix">
          <form action="/adminajax/editcategory" method="post" accept-charset="utf-8" enctype="application/json">
            <div class="col-md-12">
              <input type="hidden" name="token" value="<?php echo $token; ?>" />
              <input type="hidden" name="token" value="<?php echo $cat['id']; ?>" />
              <fieldset>
                <label for="kategori">Kategori</label>
                <input type="text" name="kategori" value="<?php echo $cat['kategori']; ?>" />
              </fieldset>
              <fieldset>
                <label for="kategori">Kategori URL (SEO vänlig sub-url)</label>
                <input type="text" name="kategori" value="<?php echo $cat['uri_kategori']; ?>" />
              </fieldset>
              <fieldset>
                <label for="kategori">Kategori URL (SEO vänlig sub-url)</label>
                <input type="text" name="kategori" value="<?php echo $cat['uri_kategori']; ?>" />
              </fieldset>
              <fieldset>
                <label for="kategori">Kategori URL (SEO vänlig sub-url)</label>
                <input type="text" name="kategori" value="<?php echo $cat['uri_kategori']; ?>" />
              </fieldset>
              <fieldset>
                <label for="kategori">Kategori URL (SEO vänlig sub-url)</label>
                <input type="text" name="kategori" value="<?php echo $cat['uri_kategori']; ?>" />
              </fieldset>
              <fieldset>
                <label for="kategori">Kategori URL (SEO vänlig sub-url)</label>
                <input type="text" name="kategori" value="<?php echo $cat['uri_kategori']; ?>" />
              </fieldset>
              <fieldset>
                <label for="kategori">Kategori URL (SEO vänlig sub-url)</label>
                <textarea type="text" name="kategori" value="" /><?php echo functions::br2htmlnl($cat['uri_kategori']); ?></fieldset>
              </fieldset>
            </div>
          </form>
        </main>


        include __DIR__ . '/shared/scripts.php';
        echo "<script src='/admin/js/categoryform.js'></script>";
        include __DIR__ . '/shared/footer.php';
      } catch(\RuntimeException $e) {
        echo $e->getMessage();
        echo "<p><a href='/adminp'>Tillbaka till huvudsidan för administrering.</p></body></html>";
        exit;
      }

    } else {
        //Not logged in
        admin\includes\classes\Login::renderLoginForm();
    }
  }
}
