<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * SUB-ROUTER FOR ADMIN PANEL
 *
 */

namespace HisingeBussAB\RekoResor\website\admincp;

use HisingeBussAB\RekoResor\website as root;


class Admin {
  public static function startAdmin() {
  root\includes\classes\Sessions::secSessionStart();

  if (includes\classes\Login::isLoggedIn() === true) {
    echo "YOU ARE LOGGED IN<br>\n";
    $router = new includes\AdminRouter();
    $router->route();
  } else {
    includes\classes\Login::renderLoginForm();
  }
  }
}
