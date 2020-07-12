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


    if (admin\includes\classes\Login::isLoggedIn() === TRUE) {
      $token = root\includes\classes\Tokens::getFormToken("category",5400);
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
          <form action="/adminajax/editcategory" method="post" accept-charset="utf-8" enctype="application/json" id="category-form">
            <div class="col-md-12">
              <input type="hidden" name="tokenid" value="<?php echo $token['id']; ?>" id="tokenid" />
              <input type="hidden" name="token" value="<?php echo $token['token']; ?>" id="token" />
              <input type="hidden" name="id" value="<?php echo $cat['id']; ?>" />
              <fieldset>
                <label for="kategori">Kategori titel:</label>
                <input type="text" name="kategori" maxlength="80" value="<?php echo $cat['kategori']; ?>" />
              </fieldset>
              <fieldset>
                <label for="ingress">Ingress (text överst på kategorisidan):</label>
                <textarea type="text" name="ingress" value=""><?php echo $cat['ingress']; ?></textarea>
              </fieldset>
              <fieldset>
                <label for="brödtext">Brödtext (ytterligare text, visas under resorna):</label>
                <textarea type="text" name="brödtext" value=""><?php echo $cat['brödtext']; ?></textarea>
              </fieldset>
              <fieldset>
                <label for="uri_kategori">Kategori URL (SEO vänlig sub-url):</label>
                <input type="text" name="uri_kategori" maxlength="85" value="<?php echo $cat['uri_kategori']; ?>" />
              </fieldset>
              <fieldset>
                <label for="og_title">Social media titel:</label>
                <input type="text" name="og_title" maxlength="40" value="<?php echo $cat['og_title']; ?>" />
              </fieldset>
              <fieldset>
                <label for="og_description">Social media beskrivning (max 255 tecken):</label>
                <input type="text" name="og_description" maxlength="255" value="<?php echo $cat['og_description']; ?>" />
              </fieldset>
              <fieldset>
                <label for="seo_description">SEO beskrivning (i sökresultat, max 160 tecken):</label>
                <input type="text" name="seo_description" maxlength="160" value="<?php echo $cat['seo_description']; ?>" />
              </fieldset>
              <fieldset>
                <label for="seo_keywords">Nyckelord. Separerade med komma. (Använd bara ett eller två):</label>
                <input type="text" name="seo_keywords" maxlength="255" value="<?php echo $cat['seo_keywords']; ?>" />
              </fieldset>
              <fieldset>
                <label for="meta_data_extra">Extra meta taggar (skriv in full HTML för ev extra meta taggar):</label>
                <textarea type="text" name="meta_data_extra" value=""><?php echo $cat['meta_data_extra']; ?></textarea>
              </fieldset>
              <fieldset>
                <button type="submit" id="save-trip-button">Spara</button>
              </fieldset>
              <div id="form-reply">
              </div>
            </div>
          </form>
        </main>
        <?php

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
