<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * Sub-router for ajax calls.
 */

namespace HisingeBussAB\RekoResor\website\ajax;

use HisingeBussAB\RekoResor\website as root;



class Ajax
{
  public static function startAjax($request) {
    root\includes\classes\Sessions::secSessionStart();

    switch ($request) {
      case 'admindologin':
        if (!root\admin\includes\classes\Login::setLogin()) {
          exit;
        } else {
          http_response_code(200);
          exit;
        }
      break;

      case 'admindologout':
        if (!root\admin\includes\classes\Logout::doLogout()) {
          echo "Failed logout";
          http_response_code(418);
          exit;
        }

      break;

      case 'label3':

      break;

      default:
        require __DIR__ . '/../includes/pages/error/404.php';


    }
  }

}
