<?php



//DEVELOPMENT SIMPLE DB MANIPULATION SCRIPT


require __DIR__ . '/../includes/classes/db.php';
require __DIR__ . '/../includes/classes/dberror.php';
require __DIR__ . '/../includes/classes/sessions.php';
require __DIR__ . '/../config/config.php';

use HisingeBussAB\RekoResor\website\includes\classes\DB as DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError as DBError;
use HisingeBussAB\RekoResor\website\includes\classes\Sessions as Sessions;
use HisingeBussAB\RekoResor\website\admin\includes\classes\Login as Login;

$pdo = DB::get();
try {
  $table = TABLE_PREFIX . 'nyheter';
  $sql = "CREATE TABLE " . $table . " (
    id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nyheter TIMESTAMP DEFAULT CURRENT_TIMESTAMP);";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

/*
try {
  $table = TABLE_PREFIX . 'settings';
  $sql = "CREATE TABLE " . $table . " (
    id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    mode VARCHAR(20),
    oauth_clientid VARCHAR(200),
    oauth_clientsecret VARCHAR(100),
    oauth_initalized BOOLEAN,
    email VARCHAR(200),
    server VARCHAR(200),
    port INT UNSIGNED,
    auth BOOLEAN,
    tls VARCHAR(3),
    smtpuser VARCHAR(200),
    smtppwd VARCHAR(200));";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}
*/
