<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * Sub-router for ajax calls.
 */

namespace HisingeBussAB\RekoResor\website\ajax;

use HisingeBussAB\RekoResor\website as root;

if ($id === 'admindologin') {
  //Login attempt requested
  if (!root\admincp\includes\classes\Login::setLogin()) {
    http_response_code(403);
    exit;
  }
}


if ($id == 'logout')
require_once __DIR__ . '/../admin-cp/php/take-logout.php';
