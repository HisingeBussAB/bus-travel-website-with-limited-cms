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
    root\includes\classes\Sessions::secSessionStart();

    header('Content-Type: application/json');

    // Verify login status before running
    if (root\admin\includes\classes\Login::isLoggedIn() === TRUE) {
      // Is logged in
      /**
       * List of possible admin ajax calls to make and handlers
       * Works for a limited number of calls, this code is not ideal and has to be re-written with proper routing or otherwise to scale.
       * For the limited number of options we will be dealing this this saves development time.
       */
      switch ($request) {
        case 'newcategory':

          if (!empty($_POST["name"])) {
            $category = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
            echo json_encode(array('success' => TRUE, 'created' => $category));
            root\admin\includes\classes\Categories::createCategory($category);
            http_response_code(200);
          } else {
            echo "Kategorinamn är tomt";
            http_response_code(411);
          }
            exit;

        break;

        case 'getcategory':
          echo root\admin\includes\classes\Categories::getCategoriesJSON();
          http_response_code(200);
          exit;

        break;

        case 'toggleitem':
          if (!empty($_POST["table"]) || !empty($_POST["id"])) {
            $id = filter_var($_POST["id"], FILTER_SANITIZE_STRING);
            $table = filter_var($_POST["table"], FILTER_SANITIZE_STRING);
            $table = self::translatetable($table);
            if (root\admin\includes\classes\MiscFunctions::toggle($id, $table)) {
              echo json_encode(array('success' => TRUE));
              http_response_code(200);
            } else
              echo "Misslyckades. Hittade ingen matchning i databasen att ändra!";
          } else {
            echo "För lite data skickad";
            http_response_code(411);
          }
          exit;

        break;

        case 'reorderitem':
          if (!empty($_POST["table"]) || !empty($_POST["id"]) || !empty($_POST["direction"])) {
            $id = filter_var($_POST["id"], FILTER_SANITIZE_STRING);
            $table = filter_var($_POST["table"], FILTER_SANITIZE_STRING);
            $direction = filter_var($_POST["direction"], FILTER_SANITIZE_STRING);
            $table = self::translatetable($table);
            if (root\admin\includes\classes\MiscFunctions::reorder($id, $table, $direction)) {
              echo json_encode(array('success' => TRUE));
              http_response_code(200);
            } else
              echo "Misslyckades. Hittade ingen matchning i databasen att ändra!";
          } else {
            echo "För lite data skickad";
            http_response_code(411);
          }
          exit;

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
   * translatetable
   *
   * Translate and whitelist filter! of table input
   *
   * @return string
   */
  private static function translatetable($table) {
    if ($table == "category") {
      return "kategorier";
    }
  }

}
