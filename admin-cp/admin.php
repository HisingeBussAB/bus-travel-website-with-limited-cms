<?php
require_once __DIR__ . "/check-login.php";
$loggedin = loginCheck();

if ($loggedin === true) {
  echo "HURRA!";
  var_dump($_SESSION);

} else {
  require_once __DIR__ . "/login.php";
  exit;
}
