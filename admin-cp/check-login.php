<?php
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions/mainfunc.php";

function loginCheck() {
  global $pdo;
  $token = $_SESSION['LOGGED_IN_TOKEN'];
  $usertoken = $_SESSION['LOGGED_IN_USER'];
  $user = $_SESSION['USER'];
  $microtime = $_SESSION['MICROTIME'];
  return false;
}

?>
