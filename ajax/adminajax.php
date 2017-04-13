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

    header('Content-Type: application/json');

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
          echo root\admin\includes\classes\Stops::getStopsJSON();
          http_response_code(200);
          exit;
        break;

        case 'getroomopt':
          echo root\admin\includes\classes\Roomopts::getRoomoptsJSON();
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

        default:
          echo "Not found!";
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
    if ($_SESSION["token"] == trim($_POST['token'])) {

      if (!empty($_POST["name"])) {
        $name = filter_var(trim($_POST["name"]), FILTER_SANITIZE_STRING);
        $flag = FALSE;
        if ($type == "category")  $flag = root\admin\includes\classes\Categories::createCategory($name);
        if ($type == "stop")      $flag = root\admin\includes\classes\Stops::createStop($name);
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
      echo "Token stämmer inte";
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
    if ($_SESSION["token"] == trim($_POST['token'])) {
      if (!empty($_POST["table"]) || !empty($_POST["id"]) || !empty($_POST["direction"]) || !empty($_POST["method"])) {
        $id = filter_var(trim($_POST["id"]), FILTER_SANITIZE_NUMBER_INT);
        $table = self::translateTable(trim($_POST["table"])); //whitelist filter and translation
        $direction = filter_var(trim($_POST["direction"]), FILTER_SANITIZE_STRING);
        $method = filter_var(trim($_POST["method"]), FILTER_SANITIZE_STRING);
        if (root\admin\includes\classes\ItemFunctions::launch($id, $table, $direction, $method)) {
          echo json_encode(array('success' => TRUE));
          http_response_code(200);
        } else {
          echo "Misslyckades. Hittade ingen matchning i databasen att ändra!";
          http_response_code(400);
        }
      } else {
        echo "För lite data skickad";
        http_response_code(411);
      }
    } else {
      echo "Token stämmer inte";
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
  }

}
