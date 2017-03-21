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

root\includes\classes\Sessions::secSessionStart();

if (includes\classes\Login::isLoggedIn() === true) {
  echo "YOU ARE LOGGED IN<br>\n";
} else {
  includes\classes\Login::renderLoginForm();
}
