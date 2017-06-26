<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * Sub-router for ajax calls.
 */

namespace HisingeBussAB\RekoResor\website\ajax;

use HisingeBussAB\RekoResor\website as root;


  /**
   * @uses Sessions
   * @uses Login
   * @uses Logout
   * @uses ResetToken
   */
class Ajax
{
  public static function startAjax($request) {
    root\includes\classes\Sessions::secSessionStart(FALSE);

    /**
     * List of possible ajax calls to make and handlers
     * Works for a limited number of calls, this code is not ideal and has to be re-written with proper routing or otherwise to scale.
     * For the limited number of options we will be dealing this this saves development time.
     */
    switch ($request) {
      case 'admindologin':
        if (!root\admin\includes\classes\Login::setLogin()) {
          exit;
        } else {
          http_response_code(200);
          exit;
        }
      break;

      case 'resettoken':
        $token = root\includes\classes\ResetToken::getRandomToken();
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
  }

}
