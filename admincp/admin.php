<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\admincp;


includes\classes\Sessions::secSessionStart();

require_once __DIR__ . "/check-login.php";
$loggedin = loginCheck();

if ($loggedin === true) {




} else {
  require_once __DIR__ . "/login.php";
  exit;
}
