<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * ADMIN
 *
 */

namespace HisingeBussAB\RekoResor\website\admin\includes\classes\pages;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\admin\includes\classes as adminclasses;


class AdminMain {
  public static function mainAdmin() {

  root\includes\classes\Sessions::secSessionStart();

  if (adminclasses\Login::isLoggedIn() !== TRUE) {
    adminclasses\Login::renderLoginForm();
  } else {
    

  }
  }
}
