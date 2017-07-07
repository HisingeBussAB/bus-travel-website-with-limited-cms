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


  if (admin\includes\classes\Login::isLoggedIn() === TRUE) {
    $token = root\includes\classes\Tokens::getFormToken("newtour",6500);
    //Is logged in

    $pageTitle = "Rekå Admin - Ny/Ändra resa";
    $more_stylesheets = "<link href='/admin/css/trip.min.css' rel='stylesheet'>";
    $show_navigation = true;

    header('Content-type: text/html; charset=utf-8');
    include __DIR__ . '/shared/header.php';

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

      if (!$trip) {
        echo "<p>Någon resa med id:$tripid kan inte hittas i databasen. <a href='http://rekoresor.busspoolen.se/adminp/'>Tillbaka till adminaströspanelen.</a>";
        http_response_code(404);
        exit;
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
        $sql = "SELECT hallplatser_id, tid_in, tid_ut FROM " . TABLE_PREFIX . "hallplatser_resor WHERE resa_id = :id;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':id', $tripid, \PDO::PARAM_INT);
        $sth->execute();
        $stops_trip = $sth->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_UNIQUE);
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql);
      }

      try {
        $sql = "SELECT kategorier_id FROM " . TABLE_PREFIX . "kategorier_resor WHERE resa_id = :id;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':id', $tripid, \PDO::PARAM_INT);
        $sth->execute();
        $categories_trip = $sth->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_UNIQUE);
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql);
      }

      try {
        $sql = "SELECT boenden_id, pris FROM " . TABLE_PREFIX . "boenden_resor WHERE resa_id = :id;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':id', $tripid, \PDO::PARAM_INT);
        $sth->execute();
        $rooms_trip = $sth->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_UNIQUE);

      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql);
      }
    }

    if (isset($trip)) {
      if (!empty($trip['program'])) {
        $textheads = functions::get_string_between($trip['program'], "<h3>", "</h3>");
        $textbodies = functions::get_string_between($trip['program'], "<p>", "</p>");
        if (count($textheads) !== count($textbodies))
        {
          echo "<p class='php-warning'>Varning: Troligen felformaterrad programdata. Det går bra att fortsätta men kontrollera att inget saknas i programtexten.</p>";
        }
      }

      if (!empty($trip['hotel'])) {
        $hotelhead = functions::get_string_between($trip['hotel'], "<h3>", "</h3>");
        $hoteltext = functions::get_string_between($trip['hotel'], "<p>", "</p>");
        if (count($hotelhead) !== count($hoteltext))
        {
          echo "<p class='php-warning'>Varning: Hotellinformation troligen korruperad. Det går att fortsätta men kontrollera att hotellfälten stämmer.</p>";
        }
      }

      if (!empty($trip['ingar'])) {
        $includes = functions::get_string_between($trip['ingar'], "<p>", "</p>");
      }
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
            <label for="trip-summary">Ingress</label>
            <textarea type="text" name="trip-summary" id="trip-summary" placeholder="Ingress"><?php if (isset($trip)) {echo $trip['ingress'];} ?></textarea>
          </fieldset>
          <fieldset>
            <label for="seo_description">Kort beskrivning (max 160 tecken, används i sökresultat och i månadens resa):</label>
            <input type="text" name="seo_description" maxlength="160" value="<?php if (isset($trip)) {echo $trip['seo_description'];} ?>" />
          </fieldset>

          <div id="trip-text">
            <?php
            if (!isset($trip)) {
              echo "<fieldset id='trip-text-1' class='trip-text'>";
              echo  "<label for='trip-text-heading[1]'>Dag 1</label>";
              echo  "<input type='text' maxlength='200' name='trip-text-heading[1]' id='trip-text-heading-1' placeholder='Dag 1'>";
              echo  "<textarea type='text' name='trip-text[1]' id='trip-text-1-text' placeholder='Programtext dag 1'></textarea>";
              echo "</fieldset>";
            } else {
              foreach ($textheads as $id=>$texthead) {
                echo "<fieldset id='trip-text-" . ($id+1) . "' class='trip-text'>";
                echo "<label for='trip-text-heading[" . ($id+1) . "]'>Dag " . ($id+1) . "</label>";
                echo "<input type='text' maxlength='200' name='trip-text-heading[" . ($id+1) . "]' class='trip-text-heading' value='" . $texthead . "'>";
                echo "<textarea type='text' name='trip-text[" . ($id+1) . "]' class='trip-text-text'>" . $textbodies[$id] . "</textarea>";
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
            <label for="trip-text-hotel-heading">Hotell</label>
            <input type="text" maxlength="120" name="trip-text-hotel-heading" id="trip-hotel-heading" placeholder="Hotellets namn" value="<?php if (isset($trip)) {echo $hotelhead[0];} ?>">
            <textarea type="text" name="trip-text-hotel-text" id="trip-hotel-text" placeholder="Om hotellet...&#10;&#10;Hotellvägen 5&#10;888 88 Hotellstaden&#10;+46888888"><?php if (isset($trip)) {echo $hoteltext[0];} ?></textarea>
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
                  echo "<div id='date-" . ($id+1) . "' class='date-item'>";
                  echo  "<input type='date' name='trip-date[" . ($id+1) . "]' id='trip-date-" . ($id+1) . "' placeholder='YYYY-MM-DD' value='" . $departure['datum'] . "'>";
                  echo "</div>";
                }
              }
               ?>
            </div>
            <div>
              <button type="button" name="trip-add-date" id="trip-add-date">Fler avgångar</button>
              <button type="button" name="trip-remove-date" id="trip-remove-date">Färre avgångar</button>
            <div>
          </fieldset>

          <fieldset>
            <h3>Antal dagar</h3>
            <div>
              <input type="number" name="trip-duration" id="trip-duration" placeholder="0" value="<?php if (isset($trip)) {echo $trip['antaldagar'];} ?>">
            </div>
          </fieldset>

          <fieldset>
            <label for="trip-facebook">Facebook event url</label>
            <input type="text" maxlength="255" name="trip-facebook" id="trip-text-1-heading" placeholder="https://www.facebook.com/..." value="<?php if (isset($trip)) {echo $trip['hotellink'];} ?>">
          </fieldset>

          <fieldset>
            <h3>Ingår i resan</h3>
            <div id="includes-list">
              <?php
              if (!isset($trip)) {
               ?>
              <div id="include-1" class="include-item">
                <input type="text" maxlength="400" name="trip-ingar[1]" id="trip-tillagg-1" value="Resa med modern helturistbuss t/r">
              </div>
              <?php
              } else {
                foreach ($includes as $id=>$include) {
                  echo "<div id='include-" . ($id+1) . "' class='include-item'>";
                  echo  "<input type='text' maxlength='400' name='trip-ingar[" . ($id+1) . "]' id='trip-tillagg-" . ($id+1) . "' value='" . $include . "'>";
                  echo "</div>";
                }
              }
               ?>
            </div>
            <div>
              <button type="button" name="trip-add-includes" id="trip-add-includes">Fler ingår</button>
              <button type="button" name="trip-remove-includes" id="trip-remove-includes">Färre ingår</button>
            <div>
          </fieldset>

          <fieldset>
            <h3>Frivilliga tillägg</h3>
            <div id="addons-list">
            <?php
            if (!isset($trip)) {
             ?>
            <div id="addon-1" class="addon-item">
              <input type="text" maxlength="255" name="trip-tillagg[1]" id="trip-tillagg-1" placeholder="Tillägg"><!--
           --><input type="number" name="trip-tillagg-pris[1]" id="trip-tillagg-1-pris" placeholder="0">&nbsp:-
            </div>
            <?php
            } else {
              foreach ($addons as $id=>$addon) {
                echo "<div id='addon-" . ($id+1) . "' class='addon-item'>";
                echo  "<input type='text' maxlength='255' name='trip-tillagg[" . ($id+1) . "]' id='trip-tillagg-" . ($id+1) . "' placeholder='Tillägg' value='" . $addon['namn'] . "'>";
                echo  "<input type='number' name='trip-tillagg-pris[" . ($id+1) . "]' id='trip-tillagg-" . ($id+1) . "-pris' placeholder='0' value='" . $addon['pris'] . "'> :-";
                echo "</div>";
              }
            }
             ?>
          </div>
            <div>
              <button type="button" name="trip-add-addon" id="trip-add-addon">Fler tillägg</button>
              <button type="button" name="trip-remove-addon" id="trip-remove-addon">Färre tillägg</button>
            <div>
          </fieldset>

          <fieldset>
            <h3>Grundpris</h3>
            <div>
              <input type="number" name="trip-price" id="trip-price" placeholder="0" value="<?php if (isset($trip)) {echo $trip['pris'];} ?>"> :-
            </div>
          </fieldset>

          <fieldset>
            <h3>Boenden</h3>
            <p>
              <table>
                <thead>
                  <tr>
                    <th scope="col">Använd</th>
                    <th scope="col">Boendetyp</th>
                    <th scope="col">Pris/person</th>
                  </tr>
                </thead>
                <tbody>
              <?php
                foreach($rooms as $room) {
                  if ((isset($trip)) && (array_key_exists($room['id'], $rooms_trip))) {
                    echo "<tr><td><input type='checkbox' name='useroom[]' value='" . $room['id'] . "' class='room-checkbox' checked></td><td>" . $room['boende'];
                    echo "</td>";
                    echo "<td><input type='number' name='roomprice[" . $room['id'] . "]' placeholder='0' class='room-price' value='" . $rooms_trip[$room['id']]['pris'] . "'> :-</td></tr>";
                  } else {
                    if ($room['aktiv'] == 1) {
                      echo "<tr><td><input type='checkbox' name='useroom[]' value='" . $room['id'] . "' class='room-checkbox'></td><td>" . $room['boende'];
                      echo "</td>";
                      echo "<td><input type='number' name='roomprice[" . $room['id'] . "]' placeholder='0' class='room-price'> :-</td></tr>";
                    }
                  }
                }
               ?>
             </tr></tbody></table>
          </fieldset>

          <fieldset>
            <h3>Turlista</h3>
            <p>
              <table>
                <thead>
                  <tr>
                    <th scope="col">Stannar</th>
                    <th scope="col">Plats</th>
                    <th scope="col">Ut</th>
                    <th scope="col">Hem</th>
                  </tr>
                </thead>
                <tbody>
              <?php
                foreach($stops as $stop) {
                  if ((isset($trip)) && (array_key_exists($stop['id'], $stops_trip))) {
                    echo "<tr><td><input type='checkbox' name='usestop[]' value='" . $stop['id'] . "' class='stop-checkbox' checked></td><td>" . $stop['ort'] . ", " . $stop['plats'];
                    echo "</td><td><input type='time' name='stopfrom[" . $stop['id'] . "]' placeholder='HH:MM' class='stop-input' value='" . $stops_trip[$stop['id']]['tid_ut'] . "'></td>";
                    echo "<td><input type='time' name='stopto[" . $stop['id'] . "]' placeholder='HH:MM' class='stop-input' value='" . $stops_trip[$stop['id']]['tid_in'] . "'></td></tr>";
                  } else {
                    if ($stop['aktiv'] == 1) {
                      echo "<tr><td><input type='checkbox' name='usestop[]' value='" . $stop['id'] . "' class='stop-checkbox'></td><td>" . $stop['ort'] . ", " . $stop['plats'];
                      echo "</td><td><input type='time' name='stopfrom[" . $stop['id'] . "]' placeholder='HH:MM' class='stop-input'></td>";
                      echo "<td><input type='time' name='stopto[" . $stop['id'] . "]' placeholder='HH:MM' class='stop-input'></td></tr>";
                    }
                }
              }
               ?>
             </tr></tbody></table>
          </fieldset>

          <fieldset>
            <h3>Kategorier</h3>
            <p>
              <table>
                <thead>
                  <tr>
                    <th scope="col">Använd</th>
                    <th scope="col">Boendetyp</th>
                  </tr>
                </thead>
                <tbody>
              <?php
                foreach($categories as $category) {
                  if ((isset($trip)) && (array_key_exists($category['id'], $categories_trip))) {
                    echo "<tr><td><input type='checkbox' name='usecategory[]' value='" . $category['id'] . "' class='category-checkbox' checked></td><td>" . $category['kategori'];
                    echo "</td></tr>";
                  } else {
                    if ($category['aktiv'] == 1) {
                      echo "<tr><td><input type='checkbox' name='usecategory[]' value='" . $category['id'] . "' class='category-checkbox'></td><td>" . $category['kategori'];
                      echo "</td></tr>";
                    }
                  }
                }
               ?>
             </tr></tbody></table>
          </fieldset>

          <fieldset>
            <h3>Inställningar för bokning</h3>
            <div class="extra-settings"><input type="checkbox" name="trip-address-required" class="settings-checkbox" value="address-required"<?php if (isset($trip)) {if ($trip['fysiskadress'] == 1) { echo " checked "; }} else { echo " checked "; } ?>>Fysisk address behöver anges vid bokning.</div>
            <div class="extra-settings"><input type="checkbox" name="trip-personalid-required" class="settings-checkbox" value="personalid-required"<?php if (isset($trip)) {if ($trip['personnr'] == 1) { echo " checked "; }} ?>>Personnummer behöver anges vid bokning.</div>
          </fieldset>
          <fieldset>
            <h3>Visa som utvald resa</h3>
            <div class="extra-settings"><input type="checkbox" name="tour-featured" class="settings-checkbox" value="featured"<?php if (isset($trip)) {if ($trip['utvald'] == 1) { echo " checked "; }} else { echo " checked "; } ?>>Visa som utvald resa (när ingen annan utvald resa avgår tidigare).</div>
          </fieldset>

          <fieldset>
            <h3>Tekniska SEO &amp; social media inställningar</h3>
            <p>Valfritt, ställs in automatiskt om det lämnas tomt.</p>
            <fieldset>
              <label for="uri_kategori" class="small-label">Resa URL (SEO vänlig sub-url):</label>
              <input type="text" name="tour_url" maxlength="85" value="<?php if (isset($trip)) {echo $trip['url'];} ?>" />
            </fieldset>
            <fieldset>
              <label for="og_title" class="small-label">Social media titel:</label>
              <input type="text" name="og_title" maxlength="40" value="<?php if (isset($trip)) {echo $trip['og_title'];} ?>" />
            </fieldset>
            <fieldset>
              <label for="og_description" class="small-label">Social media beskrivning (max 255 tecken):</label>
              <input type="text" name="og_description" maxlength="255" value="<?php if (isset($trip)) {echo $trip['og_description'];} ?>" />
            </fieldset>
            <fieldset>
              <label for="seo_keywords" class="small-label">Nyckelord. Separerade med komma. (Använd bara ett eller två):</label>
              <input type="text" name="seo_keywords" maxlength="255" value="<?php if (isset($trip)) {echo $trip['seo_keywords'];} ?>" />
            </fieldset>
            <fieldset>
              <label for="meta_data_extra" class="small-label">Extra meta taggar (skriv in full HTML för ev extra meta taggar):</label>
              <textarea type="text" name="meta_data_extra" value=""><?php if (isset($trip)) {echo $trip['meta_data_extra'];} ?></textarea>
            </fieldset>

          </fieldset>


          <fieldset>
            <input type="hidden" name="tripid" value="<?php echo $tripid; ?>">
            <input type="hidden" name="tokenid" value="<?php echo $token['id']; ?>" id="tokenid">
            <input type="hidden" name="token" value="<?php echo $token['token']; ?>" id="token">
          </fieldset>

          <fieldset>
            <div><button type="submit" id="save-trip-button">Spara resa</button></div>
          </fieldset>
          <div id="sumbit-error"></div>
        </div>
      </form>

        <h3>Bilder</h3>
        <div id="pictures">
          <div id="pictures-list">

            <?php
            $i = 0;
            if (isset($trip)) {
              $server_path = __DIR__ . '/../../../upload/resor/' . $trip['bildkatalog'] . '/';
              $web_path = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/" . $trip['bildkatalog'] . "/";
                if ($files = functions::get_img_files($server_path)) {
                  foreach ($files as $file) {
                    $ultoken = root\includes\classes\Tokens::getFormToken("ultoken",3600);
                    echo "<div class='picture-new'>";
                    echo "<form action='/adminp/filemanager/upload' method='POST' enctype='multipart/form-data'>";
                    echo "<label for='upfile' class='file-label'>";
                    if ($i === 0) { echo "Huvudbild"; } else { echo "Bild " . ($i+1); }
                    echo "</label>";
                    echo "<input type='hidden' value='" . $tripid . "' name='id'>";
                    echo "<input type='hidden' value='" . ($i+1) . "' name='position'>";
                    echo "<input type='hidden' name='tokenid' value='" . $ultoken['id'] . "'>";
                    echo "<input type='hidden' name='token' value='" . $ultoken['token'] . "'>";
                    echo "<input type='file' name='upfile' class='trip-picture' name= value=''><input type='hidden' name='old-file' value='" . $server_path . $file['file'] . "'>";
                    echo "<button type='submit' name='' >Byt ut bild</button>";
                    echo "</form>";
                    echo "<div><a href='" . $web_path . $file['file'] . "' target='_blank'><img src='" . $web_path . $file['thumb'] . "' class='picture-list-thumb'></a>";
                    echo "<p class='picture-info'>URL: <a href='" . $web_path . $file['file'] . "' target='_blank'>" . $web_path . $file['file'] . "</a><br>";
                    echo "URL thumbnail: <a href='" . $web_path . $file['thumb'] . "' target='_blank'>" . $web_path . $file['thumb'] . "</a><br>";
                    echo "Orginalstorlek: " . (getimagesize($server_path . $file['file'])[3]) . "<br>";
                    echo "Filtyp: " . (mime_content_type($server_path . $file['file'])) . "<br>";
                    echo "<p></div>";
                    echo "</div>";
                    $i++;
                  }
                } else {
                  echo "<p class='importaint'>Inga bilder hittades. Prova ladda upp nya.</p>";
                }
              } else {
                echo "<p class='importaint'>Spara reseinformationen först, sedan kan du ladda upp bilder.</p>";
              }

              while ($i < 6) {
                $ultoken = root\includes\classes\Tokens::getFormToken("ultoken",3600);
                echo "<div class='picture-new'>";
                echo "<form action='/adminp/filemanager/upload' method='POST' enctype='multipart/form-data'>";
                echo "<label for='upfile' class='file-label'>";
                if ($i === 0) { echo "Huvudbild"; } else { echo "Bild " . ($i+1); }
                echo "</label>";
                echo "<input type='hidden' value='" . $tripid . "' name='id'>";
                echo "<input type='hidden' value='" . ($i+1) . "' name='position'>";
                echo "<input type='hidden' name='tokenid' value='" . $ultoken['id'] . "'>";
                echo "<input type='hidden' name='token' value='" . $ultoken['token'] . "'>";
                echo "<input type='file' name='upfile' class='trip-picture' name= value=''>";
                echo "<button type='submit' name='' >Ladda upp bild</button>";
                echo "</form>";
                echo "</div>";
                $i++;
            }
            $ultoken[0] = root\includes\classes\Tokens::getFormToken("ultoken",3600);
            $ultoken[1] = root\includes\classes\Tokens::getFormToken("ultoken",3600);
           ?>
        </div>

        <h3>PDF-program</h3>
        <div class="pdf-new">
          <form action="/adminp/filemanager/upload" method="POST" enctype="multipart/form-data">
            <label for="upfile" class="file-label">Huvudprogram</label>
          <input type="file" name="upfile" class="trip-pdf" value="">
          <input type="hidden" value="<?php echo $tripid ?>" name="id">
          <input type="hidden" value="1" name="position">
          <input type="hidden" name="tokenid" value="<?php echo $ultoken[0]['id']; ?>">
          <input type="hidden" name="token" value="<?php echo $ultoken[0]['token']; ?>">
          <button type="submit">Ladda upp / Byt ut pdf</button>
          </form>
        </div>
        <div class="pdf-new">
          <form action="/adminp/filemanager/upload" method="POST" enctype="multipart/form-data">
            <label for="upfile" class="file-label">Extra pdf</label>
          <input type="hidden" value="<?php echo $tripid ?>" name="id">
          <input type="hidden" value="2" name="position">
          <input type="hidden" name="tokenid" value="<?php echo $ultoken[1]['id']; ?>">
          <input type="hidden" name="token" value="<?php echo $ultoken[1]['token']; ?>">
          <input type="file" name="upfile" class="trip-pdf" value="">
          <button type="submit">Ladda upp / Byt ut pdf</button>
          </form>
        </div>
        </div>



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
