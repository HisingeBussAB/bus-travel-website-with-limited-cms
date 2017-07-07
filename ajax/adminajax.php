<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * Sub-router for ajax calls.
 */

namespace HisingeBussAB\RekoResor\website\ajax;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;


  /**
   * @uses Sessions
   * @uses Login
   * @uses Categories
   */
class AdminAjax
{
  public static function startAjax($request) {
    root\includes\classes\Sessions::secSessionStart(FALSE);

    header('Content-Type: application/json; charset=utf-8');


    // Verify login status before running
    if (root\admin\includes\classes\Login::isLoggedIn(FALSE) === TRUE) {
      // Is logged in
      /**
       * List of possible admin ajax calls to make and handlers
       * Works for a limited number of calls, this code is not ideal and has to be re-written with proper routing or otherwise to scale.
       * For the limited number of options we will be dealing this this saves development time.
       */
      switch ($request) {
        case 'newcategory':
          self::newItemLauncher("category");
        break;

        case 'editcategory':
          root\admin\includes\classes\Categories::updateCategory();
        break;

        case 'newroomopt':
          self::newItemLauncher("roomopt");
        break;

        case 'newstop':
          self::newItemLauncher("stop");
        break;

        case 'getcategory':
          echo root\admin\includes\classes\Categories::getCategoriesJSON();
          http_response_code(200);
          exit;
        break;

        case 'getstop':
          $sort = filter_var(trim($_POST['sort']), FILTER_SANITIZE_STRING);
          echo root\admin\includes\classes\Stops::getStopsJSON($sort);
          http_response_code(200);
          exit;
        break;

        case 'getroomopt':
          echo root\admin\includes\classes\Roomopts::getRoomoptsJSON();
          http_response_code(200);
          exit;
        break;

        case 'gettrip':
          echo root\admin\includes\classes\NewTrip::getTripsJSON();
          http_response_code(200);
          exit;
        break;

        case 'toggleitem':
          self::changeFilter();
        break;

        case 'deleteitem':
          self::changeFilter();
        break;

        case 'reorderitem':
          self::changeFilter();
        break;

        case 'newtrip':
        if (root\includes\classes\Tokens::checkFormToken(trim($_POST['token']),trim($_POST['tokenid']),"newtour")) {
          if (!empty($_POST)) {
            root\admin\includes\classes\NewTrip::newTrip($_POST);
          } else {
            echo "<p>Ingen data skickad.</p>";
            http_response_code(401);
            exit;
          }
        } else {
          echo "<p>Token stämmer inte. Prova <a href='javascript:window.location.href=window.location.href'>ladda om</a> sidan.</p>";
          http_response_code(401);
          exit;
        }
        break;

        case 'updatesettings':
          root\admin\includes\pages\Settings::update("settings");
        break;

        case 'updatepassword':
          root\admin\includes\pages\Settings::update("password");
        break;

        case 'news':
          if (root\includes\classes\Tokens::checkCommonToken(trim($_POST['token']),trim($_POST['tokenid']))) {
            $news = strip_tags(trim($_POST['nyheter']), ALLOWED_HTML_TAGS);
            try {
              $pdo = DB::get();
              $sql = "UPDATE " . TABLE_PREFIX . "nyheter SET nyheter = :nyheter WHERE id = 1;";
              $sth = $pdo->prepare($sql);
              $sth->bindParam(':nyheter', $news, \PDO::PARAM_STR);
              $sth->execute();
            } catch(\PDOException $e) {
              DBError::showError($e, __CLASS__, $sql);
            }
            echo json_encode("Texten sparad");
            http_response_code(200);
            exit;
        } else {
          echo "<p>Token stämmer inte. Prova <a href='javascript:window.location.href=window.location.href'>ladda om</a> sidan.</p>";
          http_response_code(401);
          exit;
        }
        break;

        default:
          echo "Sidan finns inte!";
          http_response_code(404);
          exit;
      }

    } else {
      // Not logged in
      echo "Felaktigt utförd begäran - du är inte inloggad.";
      http_response_code(401);
      exit;
    }
  }


  /**
   * newItemLauncher
   *
   * Function to create a new item
   *
   */
  private static function newItemLauncher($type) {
    if (root\includes\classes\Tokens::checkCommonToken(trim($_POST['token']),trim($_POST['tokenid']))) {

      if (!empty($_POST["name"])) {
        $name = filter_var(trim($_POST["name"]), FILTER_SANITIZE_STRING);
        if ($type == "stop") $city = filter_var(trim($_POST["ort"]), FILTER_SANITIZE_STRING);
        $flag = FALSE;
        if ($type == "category")  $flag = root\admin\includes\classes\Categories::createCategory($name);
        if ($type == "stop")      $flag = root\admin\includes\classes\Stops::createStop($name, $city);
        if ($type == "roomopt")   $flag = root\admin\includes\classes\Roomopts::createRoomopt($name);

        if ($flag) {
          echo json_encode(array('success' => TRUE, 'created' => $name));
          http_response_code(200);
        } else {
          echo "Databasfel!";
          http_response_code(500);
        }
      } else {
        echo "Kategorinamn är tomt";
        http_response_code(411);
      }
    } else {
      echo "Säkerhetstoken stämmer inte. <a href='javascript:window.location.href=window.location.href'>Ladda om sidan.</a>";
      http_response_code(401);
    }
    exit;
  }

  /**
   * changeFilter
   *
   * Starts an item change operation and handles response
   *
   */
  private static function changeFilter() {
    if (root\includes\classes\Tokens::checkCommonToken(trim($_POST['token']),trim($_POST['tokenid']))) {
      if (!empty($_POST["table"]) || !empty($_POST["id"]) || !empty($_POST["direction"]) || !empty($_POST["method"])) {
        $id = filter_var(trim($_POST["id"]), FILTER_SANITIZE_NUMBER_INT);
        $table = self::translateTable(trim($_POST["table"])); //whitelist filter and translation
        $direction = filter_var(trim($_POST["direction"]), FILTER_SANITIZE_STRING);
        $method = filter_var(trim($_POST["method"]), FILTER_SANITIZE_STRING);
        if (root\admin\includes\classes\ItemFunctions::launch($id, $table, $direction, $method)) {
          echo json_encode(array('success' => TRUE));
          http_response_code(200);
        } else {
          echo "Misslyckades.";
          http_response_code(400);
        }
      } else {
        echo "För lite data skickad";
        http_response_code(411);
      }
    } else {
      echo "Token stämmer inte. <a href='javascript:window.location.href=window.location.href'>Ladda om sidan.</a>";
      http_response_code(401);
    }
    exit;
  }

  /**
   * translateTable
   *
   * Translate and whitelist filter! of table input
   *
   * @return string
   */
  private static function translateTable($table) {
    if ($table == "category") return "kategorier";
    if ($table == "roomopt") return "boenden";
    if ($table == "stop") return "hallplatser";
    if ($table == "trip") return "resor";
  }

}
