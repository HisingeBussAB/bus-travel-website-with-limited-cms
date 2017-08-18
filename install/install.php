<?php
require __DIR__ . '/../includes/classes/db.php';
require __DIR__ . '/../includes/classes/dberror.php';

use HisingeBussAB\RekoResor\website\includes\classes\DB as DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError as DBError;
use HisingeBussAB\RekoResor\website\includes\classes\Sessions as Sessions;

echo "<html><head><meta http-equiv='content-type' content='text/html;charset=utf-8'><title>Installera</title></head><body>";

$allow_references = TRUE; //if the database does not grand rights to set references this can be disabled.

$firstinstall = NULL;

$pdo = DB::get();
try {
  $sql = "SELECT count(*) FROM " . TABLE_PREFIX . "logins;";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  $count = $sth->fetch(\PDO::FETCH_NUM); // Return array indexed by column number
  $thecount = reset($count); // Resets array cursor and returns first value (the count)

  if ($thecount > 0) {
    echo "<br>Sidan är redan installerad.<br><br>";
    $firstinstall = FALSE;
  } else {
    $firstinstall = TRUE;
  }
} catch(\PDOException $e) {
  $firstinstall = TRUE;
  echo "<br>Installation!<br><br>";
}



$options = [
    'cost' => 10, //difficulty for password_hash
];

$default_pwd_plain = "12345";
$default_login = filter_var(trim("admin"), FILTER_SANITIZE_STRING);
$default_pwd = password_hash($default_pwd_plain . FIX_PWD_PEPPER, PASSWORD_DEFAULT, $options);



if ($firstinstall) {
  echo "<p>Installing...</p>";
  require "runonce.php";

  echo "<p>Allt klart! <a href='/adminp'>Till adminpanelen</a></p>";


}

echo "</body></html>";
