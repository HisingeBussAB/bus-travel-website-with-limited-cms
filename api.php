<?php 
/**
 * Rekå Resor Bokningssystem - API
 * index.php
 * @author    Håkan Arnoldson
 * 
 * Squential front controller
 * 
 * - Imports config.php global constants
 * - Sets up error handling and some basic php.ini configuration
 * - Starts the output cache ob_start
 * - Sets default headers
 * - Handles pre-flight (OPTIONS)
 * - Handles basic 403 request rejection (wrong IP or no API-key)
 * - Initalize Monolog
 * - Initalize Moment to CET and se_SV
 * - Initalize AltoRouter and route request to second controller
 * - Handles 404 response
 */

namespace HisingeBussAB\RekoResor\website;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;
use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\Functions as functions;


class Api {

  public static function resor() {

    header("Content-Type: application/json; charset=UTF-8");
    header("Accept-Charset: utf-8");
    header("Cache-Control: no-cache, must-revalidate");
    header("Content-Language: sv-SE");
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
    header('X-Robots-Tag: noindex, nofollow');
    header('Allow: OPTIONS, GET');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header("Access-Control-Allow-Origin: *");

    try {
      $pdo = DB::get();

      $sql = "SELECT 	
        id
        ,namn
        ,pris
        ,program
        ,ingress
        ,antaldagar
        ,ingar
        ,bildkatalog
        ,url
        ,seo_description
        ,og_description
        ,og_title
        ,seo_keywords
        ,meta_data_extra
        ,personnr
        ,fysiskadress
        ,aktiv	
        ,utvald
        ,hotel
        ,hotellink
        ,facebook
        ,cat_addr_street
        ,cat_addr_city
        ,cat_addr_region
        ,cat_addr_country
        ,cat_addr_zip
        ,cat_lat
        ,cat_long
        ,cat_neighborhood
        ,cat_type FROM " . TABLE_PREFIX . "resor AS resor      
        LEFT JOIN " . TABLE_PREFIX . "datum AS datum ON datum.resa_id = resor.id  
              WHERE resor.aktiv = 1 AND datum.datum > NOW()
              GROUP BY datum.datum
              ORDER BY datum.datum;";
        $sth = $pdo->prepare($sql);
        $sth->execute();
        $resor = $sth->fetchAll(\PDO::FETCH_ASSOC);
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql);
        $errorType = "Databasfel";
        throw new \RuntimeException("Databasfel vid laddning av resor.");
      }
      foreach ($resor as $key=>$resa) {
        try {
          $sql = "SELECT 	
          pris
          ,namn
          FROM " . TABLE_PREFIX . "tillaggslistor AS tillaggslistor          
                WHERE resa_id = " . $resa['id'] . ";";
          $sth = $pdo->prepare($sql);
          $sth->execute();
          $tillagg = $sth->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
          DBError::showError($e, __CLASS__, $sql);
          $errorType = "Databasfel";
          throw new \RuntimeException("Databasfel vid laddning av resor.");
        }
        try {
          $sql = "SELECT 	
          boenden.boende as boende
          ,boenden_resor.pris as pris
          FROM " . TABLE_PREFIX . "boenden AS boenden  
          INNER JOIN " . TABLE_PREFIX . "boenden_resor as boenden_resor ON boenden_resor.boenden_id = boenden.id
          WHERE boenden_resor.resa_id = " . $resa['id'] . ";";
          $sth = $pdo->prepare($sql);
          $sth->execute();
          $boenden = $sth->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
          DBError::showError($e, __CLASS__, $sql);
          $errorType = "Databasfel";
          throw new \RuntimeException("Databasfel vid laddning av resor.");
        }
        try {
          $sql = "SELECT 	
          datum
          FROM " . TABLE_PREFIX . "datum AS datum 
          WHERE resa_id = " . $resa['id'] . ";";
          $sth = $pdo->prepare($sql);
          $sth->execute();
          $datum = $sth->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
          DBError::showError($e, __CLASS__, $sql);
          $errorType = "Databasfel";
          throw new \RuntimeException("Databasfel vid laddning av resor.");
        }
        try {
          $sql = "SELECT 	
          hallplatser.plats as plats
          ,hallplatser.ort as ort
          ,hallplatser_resor.tid_in as tid_in
          ,hallplatser_resor.tid_ut as tid_ut
          FROM " . TABLE_PREFIX . "hallplatser AS hallplatser  
          INNER JOIN " . TABLE_PREFIX . "hallplatser_resor as hallplatser_resor ON hallplatser_resor.hallplatser_id = hallplatser.id
          WHERE hallplatser_resor.resa_id = " . $resa['id'] . " ORDER BY tid_ut ASC;";
          $sth = $pdo->prepare($sql);
          $sth->execute();
          $hallpatser = $sth->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
          DBError::showError($e, __CLASS__, $sql);
          $errorType = "Databasfel";
          throw new \RuntimeException("Databasfel vid laddning av resor.");
        }
        try {
          $sql = "SELECT 	
          kategorier.kategori as kategori
          FROM " . TABLE_PREFIX . "kategorier AS kategorier  
          INNER JOIN " . TABLE_PREFIX . "kategorier_resor as kategorier_resor ON kategorier_resor.kategorier_id = kategorier.id
          WHERE kategorier_resor.resa_id = " . $resa['id'] . " ORDER BY kategorier.sort;";
          $sth = $pdo->prepare($sql);
          $sth->execute();
          $kategorier = $sth->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
          DBError::showError($e, __CLASS__, $sql);
          $errorType = "Databasfel";
          throw new \RuntimeException("Databasfel vid laddning av resor.");
        }
        $resor[$key]['datum']   = $datum;
        $resor[$key]['kategorier']   = $kategorier;
        $resor[$key]['hallpatser']   = $hallpatser;
        $resor[$key]['boenden'] = $boenden;
        $resor[$key]['tillagg'] = $tillagg;
      }
      echo json_encode($resor);
    }

    public static function boenden() {
      header("Content-Type: application/json; charset=UTF-8");
      header("Accept-Charset: utf-8");
      header("Cache-Control: no-cache, must-revalidate");
      header("Content-Language: sv-SE");
      header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
      header('X-Robots-Tag: noindex, nofollow');
      header('Allow: OPTIONS, GET');
      header('Access-Control-Allow-Methods: GET, OPTIONS');
      header("Access-Control-Allow-Origin: *");
  

        
  
          try {
            $pdo = DB::get();
            $sql = "SELECT 	
            boende as boende
            FROM " . TABLE_PREFIX . "boenden AS boenden  
            WHERE aktiv = 1;";
            $sth = $pdo->prepare($sql);
            $sth->execute();
            $boenden = $sth->fetchAll(\PDO::FETCH_ASSOC);
          } catch(\PDOException $e) {
            DBError::showError($e, __CLASS__, $sql);
            $errorType = "Databasfel";
            throw new \RuntimeException("Databasfel vid laddning av resor.");
          }
      
        
        echo json_encode($boenden);
    }



    public static function kategorier() {
      header("Content-Type: application/json; charset=UTF-8");
      header("Accept-Charset: utf-8");
      header("Cache-Control: no-cache, must-revalidate");
      header("Content-Language: sv-SE");
      header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
      header('X-Robots-Tag: noindex, nofollow');
      header('Allow: OPTIONS, GET');
      header('Access-Control-Allow-Methods: GET, OPTIONS');
      header("Access-Control-Allow-Origin: *");
  

        
  
          try {
            $pdo = DB::get();
            $sql = "SELECT 	
kategori
,ingress
,meta_data_extra
,og_description
,og_title
,seo_description
,seo_keywords
,sort
,uri_kategori
            FROM " . TABLE_PREFIX . "kategorier AS kategorier  
            WHERE aktiv = 1 ORDER BY kategorier.sort;";
            $sth = $pdo->prepare($sql);
            $sth->execute();
            $kategorier = $sth->fetchAll(\PDO::FETCH_ASSOC);
          } catch(\PDOException $e) {
            DBError::showError($e, __CLASS__, $sql);
            $errorType = "Databasfel";
            throw new \RuntimeException("Databasfel vid laddning av resor.");
          }
      
        
        echo json_encode($kategorier);
    }

  }

