<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * (c) Rekå Resor AB
 *
 * @link      https://github.com/HisingeBussAB/bus-travel-website-with-limited-cms
 * @copyright CC BY-SA 4.0 (http://creativecommons.org/licenses/by-sa/4.0/)
 * @license   GNU General Public License v3.0
 * @author    Håkan Arnoldson
 */

require_once  __DIR__ . '/../includes/functions/mainfunc.php';

sec_session_start();

require_once __DIR__ . "/check-login.php";
$loggedin = loginCheck();

if ($loggedin === true) {




} else {
  require_once __DIR__ . "/login.php";
  exit;
}
