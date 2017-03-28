<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * ADMIN
 *
 */

namespace HisingeBussAB\RekoResor\website\admincp;

use HisingeBussAB\RekoResor\website as root;


class Admin {
  public static function startAdmin($admin, $page=false) {
  root\includes\classes\Sessions::secSessionStart();
  //var_dump($subpath);
  if (includes\classes\Login::isLoggedIn() === true) {

    switch ($page) {
    case false:
        echo "LOGGED IN!";
        break;
    case 'label2':

        break;
    case 'logout':
        if (includes\classes\Logout::doLogout()) {
          http_response_code(501);
          echo "Logout failed!";
          exit;
        }
        break;

    default:
        require __DIR__ . '/../includes/pages/error/404.php';

    }


  } else {
    includes\classes\Login::renderLoginForm();
  }
  }
}
