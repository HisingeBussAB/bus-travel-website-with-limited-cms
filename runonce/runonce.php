<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

require '../config/config.php';
require '../includes/classes/db.php';
require '../includes/classes/dbexception.php';

use HisingeBussAB\RekoResor\website\includes\classes\DB as DB;

$pdo = DB::get();


$options = [
    'cost' => 10,
];

$default_login = filter_var(trim(DEFAULT_ADMIN_USER), FILTER_SANITIZE_STRING);
$default_pwd = password_hash(DEFAULT_ADMIN_PWD . FIX_PWD_PEPPER, PASSWORD_DEFAULT, $options);

//FIXME REMOVE DROP TABLES DEBUG
try {
  $table = TABLE_PREFIX . 'logins';
  $sql = "DROP TABLE " . $table . ";";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " dropped succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'loggedin';
  $sql = "DROP TABLE " . $table . ";";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " dropped succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'hammerguard';
  $sql = "DROP TABLE " . $table . ";";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " dropped succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}
//FIXME END DROP TABLES DEBUG


try {
  $sql = "SELECT count(*) FROM " . TABLE_PREFIX . "logins;";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  $count = $sth->fetch(\PDO::FETCH_NUM); // Return array indexed by column number
  $thecount = reset($count); // Resets array cursor and returns first value (the count)

  if ($thecount > 0) {
    echo "Database already setup! Breaking!.<br>";
    $pdo = NULL;
    exit;
  }
  } catch(\PDOException $e) {
    echo "Database don't exist. Initializing DB.<br>";
  }

try {
  $table = TABLE_PREFIX . 'logins';
  $sql = "CREATE TABLE " . $table . " (
    id INT NOT NULL PRIMARY KEY,
    username CHAR(64),
    pwd CHAR(255));";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}


try {
$sql = "INSERT INTO " . TABLE_PREFIX . "logins (
  id,
  username,
  pwd)
  VALUES (
  0,'" .
  $default_login . "','" .
  $default_pwd . "');";

  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Default username set: " . $default_login . "<br>Default password set: " . DEFAULT_ADMIN_PWD . "<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}


try {
  $table = TABLE_PREFIX . 'hammerguard';
  $sql = "CREATE TABLE " . $table . " (
    iphash CHAR(64),
    time BIGINT);";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'loggedin';
  $sql = "CREATE TABLE " . $table . " (
    user INT,
    ip CHAR(64),
    time BIGINT,
    jwtkey VARCHAR(100),
    jwttoken VARCHAR(100));";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}
