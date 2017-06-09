<?php
/**
 * Rekå Resor (www.rekoresor.se)
 *
 * This script will initalize the database.
 *
 * @author    Håkan Arnoldson
 * @link      https://github.com/HisingeBussAB/bus-travel-website-with-limited-cms
 */

require '../config/config.php';
require '../includes/classes/db.php';
require '../includes/classes/dberror.php';

use HisingeBussAB\RekoResor\website\includes\classes\DB as DB;

$pdo = DB::get();


$options = [
    'cost' => 10,
];

$default_login = filter_var(trim(DEFAULT_ADMIN_USER), FILTER_SANITIZE_STRING);
$default_pwd = password_hash(DEFAULT_ADMIN_PWD . FIX_PWD_PEPPER, PASSWORD_DEFAULT, $options);

//FIXME REMOVE DROP TABLES DEBUG
try {
  $table = TABLE_PREFIX . 'resor_hallplatser';
  $sql = "DROP TABLE " . $table . ";";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " dropped succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'resor';
  $sql = "DROP TABLE " . $table . ";";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " dropped succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'boenden';
  $sql = "DROP TABLE " . $table . ";";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " dropped succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'boenden_resor';
  $sql = "DROP TABLE " . $table . ";";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " dropped succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'kategorier_resor';
  $sql = "DROP TABLE " . $table . ";";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " dropped succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'tillaggslistor';
  $sql = "DROP TABLE " . $table . ";";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " dropped succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'kategorier';
  $sql = "DROP TABLE " . $table . ";";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " dropped succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'hallplatser';
  $sql = "DROP TABLE " . $table . ";";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " dropped succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

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

try {
  $table = TABLE_PREFIX . 'extra_datum';
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
    sessionid CHAR(64),
    jwtkey VARCHAR(200),
    jwttoken VARCHAR(200));";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'kategorier';
  $sql = "CREATE TABLE " . $table . " (
    id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    kategori VARCHAR(80),
    uri_kategori VARCHAR(80),
    sort INT UNSIGNED,
    aktiv BOOLEAN);";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'hallplatser';
  $sql = "CREATE TABLE " . $table . " (
    id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    plats VARCHAR(80),
    sort INT UNSIGNED,
    aktiv BOOLEAN);";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'resor';
  $sql = "CREATE TABLE " . $table . " (
    id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    namn VARCHAR(200),
    pris INT,
    datum DATE,
    program TEXT,
    ingress TINYTEXT,
    antaldagar INT UNSIGNED,
    ingar TEXT,
    bildkatalog VARCHAR(100),
    personnr BOOLEAN,
    fysiskadress BOOLEAN,
    aktiv BOOLEAN,
    hotel TINYTEXT,
    hotellink VARCHAR(255),
    facebook VARCHAR(255));";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'resor_hallplatser';
  $sql = "CREATE TABLE " . $table . " (
    hallplats INT UNSIGNED,
    resa INT UNSIGNED,
    tid_in TIME,
    tid_ut TIME);";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'kategorier_resor';
  $sql = "CREATE TABLE " . $table . " (
    resa INT UNSIGNED,
    kategori INT UNSIGNED);";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'tillaggslistor';
  $sql = "CREATE TABLE " . $table . " (
    resa_id INT UNSIGNED,
    pris INT,
    namn VARCHAR(255));";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'boenden';
  $sql = "CREATE TABLE " . $table . " (
    id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    boende VARCHAR(100),
    aktiv BOOLEAN);";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'boenden_resor';
  $sql = "CREATE TABLE " . $table . " (
    resa INT UNSIGNED,
    boende INT UNSIGNED,
    pris INT)";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'extra_datum';
  $sql = "CREATE TABLE " . $table . " (
    resa_id INT UNSIGNED,
    datum DATE)";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}
