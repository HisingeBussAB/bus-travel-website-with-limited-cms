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

        break;

        case 'getcategories':
          header('Content-Type: application/json');
          echo root\admin\includes\classes\Categories::getCategoriesJSON();
          http_response_code(200);
          exit;

        break;

        case 'resettoken':
          $token = root\admin\includes\classes\ResetToken::getRandomToken();
          header('Content-Type: application/json');
          echo json_encode(array('token' => $token));
          http_response_code(200);
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
}
