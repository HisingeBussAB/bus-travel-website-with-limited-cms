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
  var_dump($admin);
  if (includes\classes\Login::isLoggedIn() === true) {

    switch ($subpath) {
    case '':

        break;
    case 'label2':

        break;
    case 'label3':

        break;

    default:
        require __DIR__ . '/../includes/pages/error/404.php';

    }


  } else {
    includes\classes\Login::renderLoginForm();
  }
  }
}
