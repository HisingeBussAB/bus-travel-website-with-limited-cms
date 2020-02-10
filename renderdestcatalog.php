<?php

namespace HisingeBussAB\RekoResor\website;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;
use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\Functions as functions;


class RenderDestCatalog {

  public static function render() {

    header('Content-type: text/csv; charset="utf-8"',true);

    try {
      $pdo = DB::get();

      $sql = "SELECT resor.id, resor.seo_description, resor.namn, resor.url, resor.bildkatalog, resor.pris, resor.cat_addr_street, resor.cat_addr_city,
                     resor.cat_addr_region, resor.cat_addr_country, resor.cat_addr_zip, resor.cat_lat, resor.cat_long, resor.cat_neighborhood, resor.cat_type,
                     datum.datum AS datum, resor.antaldagar FROM " . TABLE_PREFIX . "resor AS resor
              LEFT OUTER JOIN " . TABLE_PREFIX . "datum AS datum ON resor.id = datum.resa_id
              LEFT OUTER JOIN " . TABLE_PREFIX . "kategorier_resor AS k_r ON resor.id = k_r.resa_id
              LEFT OUTER JOIN " . TABLE_PREFIX . "kategorier AS kategorier ON kategorier.id = k_r.kategorier_id
              WHERE kategorier.kategori != 'gruppresor' AND resor.aktiv = 1 AND datum > NOW()
              GROUP BY datum
              ORDER BY datum;";



        $sth = $pdo->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
        //var_dump($result);
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql);
        $errorType = "Databasfel";
        throw new \RuntimeException("Databasfel vid laddning av resor.");
      }



      header('Content-type: text/xml; charset="utf-8"',true);
      echo '<?xml version="1.0" encoding="UTF-8"?>
              <listings>
                <title>Rekå Resor Destinations</title>
                <link rel="self" href="http' . APPEND_SSL . '://' . DOMAIN . '/feed/get-destinations.xml"/>
                ';
                foreach($result as $item) {
                  if (!empty($item['cat_addr_city']) && !empty($item['cat_addr_region']) && !empty($item['cat_addr_country'])
                      && !empty($item['cat_addr_zip']) && !empty($item['cat_type'])) {
                    $server_path = __DIR__ . '/upload/resor/' . $item['bildkatalog'] . '/';
                    $web_path = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/" . rawurlencode($item['bildkatalog']) . "/";
                    $imgfiles = functions::get_img_files($server_path);
                    if (empty($imgfiles)) {
                      $imgfiles[0]['file'] = "1_generic.jpg";
                      $imgfiles[0]['thumb'] = "small_1_generic.jpg";
                      $web_path = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/generic/";
                    }
                    $neighborhoods = explode(",", $item['cat_neighborhood']);
                    $categories = explode(",", $item['cat_type']);
                    $cat_lat = str_replace(",",".",$item['cat_lat']);
                    $cat_long = str_replace(",",".",$item['cat_long']);
                    echo '<listing>';
                    if ($lastitemid == $item['id']) {
                      $repeatitem++;
                      echo '<item_group_id>' . htmlentities(trim($item['id']), ENT_XML1) . '</item_group_id>';
                      echo '<destination_id>' . htmlentities(trim($item['id'] . '0' . $repeatitem), ENT_XML1) . '</destination_id>';
                    } else {
                      echo '<item_group_id>' . htmlentities(trim($item['id']), ENT_XML1) . '</item_group_id>';
                      echo '<destination_id>' . htmlentities(trim($item['id']), ENT_XML1) . '</destination_id>';
                      $repeatitem = 0;
                    }
                    $lastitemid = $item['id'];
                    $destname = trim($item['namn']);
                    if ($item['antaldagar'] > 1) {
                      $destname = $destname . ' - ' .  trim($item['antaldagar']) . ' dgr';
                    }
                    echo '<name>' . htmlentities($destname, ENT_XML1) .  '</name>
                    <description>' . htmlentities(trim($item['seo_description']), ENT_XML1) . '</description>
                    <address format="simple">
                      <component name="addr1">' . htmlentities(trim($item['cat_addr_street']), ENT_XML1) . '</component>
                      <component name="city">' . htmlentities(trim($item['cat_addr_city']), ENT_XML1) . '</component>
                      <component name="region">' . htmlentities(trim($item['cat_addr_region']), ENT_XML1) . '</component>
                      <component name="country">' . htmlentities(trim($item['cat_addr_country']), ENT_XML1) . '</component>
                      <component name="postal_code">' . htmlentities(trim($item['cat_addr_zip']), ENT_XML1) . '</component>
                    </address>
                    <latitude>' . htmlentities(trim($cat_lat), ENT_XML1) . '</latitude>
                    <longitude>' . htmlentities(trim($cat_long), ENT_XML1) . '</longitude>
                    ';
                    foreach($neighborhoods as $neighborhood) {
                      echo '<neighborhood>' . htmlentities(trim($neighborhood), ENT_XML1) . '</neighborhood>
                      ';
                    }
                    foreach($imgfiles as $imgfile) {
                      echo '<image>
                        <url>' . $web_path . rawurlencode(trim($imgfile['file'])) . '</url>
                      </image>
                      ';
                    }
                    foreach($categories as $category) {
                      echo '<type>' . htmlentities(trim($category), ENT_XML1) . '</type>
                      ';
                    }
                    echo '<price>' . htmlentities(trim($item['pris']), ENT_XML1) . ' SEK</price>
                    <url>http' . APPEND_SSL . '://' . DOMAIN . '/resa/' . rawurlencode(trim($item['url'])) . '</url>
                    </listing>
                    ';
                  }
                }
              echo '
              </listings>
              ';


    }
}
