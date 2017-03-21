<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * Sub-router for ajax calls.
 */

namespace HisingeBussAB\RekoResor\website\ajax;

use HisingeBussAB\RekoResor\website as root;

root\includes\classes\Sessions::secSessionStart();

if ($id === 'admindologin') {
  //Login attempt requested
  if (!root\admincp\includes\classes\Login::setLogin()) {
    exit;
  }
}


if ($id == 'logout')
require_once __DIR__ . '/../admin-cp/php/take-logout.php';
