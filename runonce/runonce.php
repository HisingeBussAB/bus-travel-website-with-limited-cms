<?php
require_once '../includes/db_connect.php';

$default_login = "admin";
$default_pwd = password_hash("12345" . FIX_PWD_SALT, PASSWORD_DEFAULT);

//FIXME REMOVE DROP TABLES DEBUG
try {
  $table = TABLE_PREFIX . 'logins';
  $sql = "DROP TABLE " . $table . ";";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " dropped succesfully.<br>";
} catch(PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'loggedin';
  $sql = "DROP TABLE " . $table . ";";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " dropped succesfully.<br>";
} catch(PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'hammerguard';
  $sql = "DROP TABLE " . $table . ";";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " dropped succesfully.<br>";
} catch(PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}
//FIXME END DROP TABLES DEBUG


try {
  $sql = "SELECT count(*) FROM " . TABLE_PREFIX . "logins;";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  $count = $sth->fetch(PDO::FETCH_NUM); // Return array indexed by column number
  $thecount = reset($count); // Resets array cursor and returns first value (the count)

  if ($thecount > 0) {
    echo "Database already setup! Breaking!.<br>";
    $pdo = NULL;
    exit;
  }
} catch(PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
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
} catch(PDOException $e) {
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
  echo "Default username set: " . $default_login . "<br>Default password set: " . $default_pwd . "<br>";
} catch(PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}


try {
  $table = TABLE_PREFIX . 'hammerguard';
  $sql = "CREATE TABLE " . $table . " (
    iphash CHAR(64),
    time INT);";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'loggedin';
  $sql = "CREATE TABLE " . $table . " (
    time INT,
    microtime VARCHAR(40),
    token CHAR(64),
    salt CHAR(64),
    user  CHAR(64));";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}
