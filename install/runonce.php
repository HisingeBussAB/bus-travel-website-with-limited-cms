<?php
/**
 * Rekå Resor (www.rekoresor.se)
 *
 * This script will initalize the database.
 *
 * @author    Håkan Arnoldson
 * @link      https://github.com/HisingeBussAB/bus-travel-website-with-limited-cms
 */

//Prevent accidental re-install. This conditon must be changed to allow dropping and reinitalizing any existing db tables.
if (!$firstinstall) {
  try {
    $table = TABLE_PREFIX . 'hallplatser_resor';
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
    $table = TABLE_PREFIX . 'boenden';
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
    $table = TABLE_PREFIX . 'datum';
    $sql = "DROP TABLE " . $table . ";";
    $sth = $pdo->prepare($sql);
    $sth->execute();
    echo "Table: " . $table . " dropped succesfully.<br>";
  } catch(\PDOException $e) {
    echo $sql . "<br>" . $e->getMessage() . "<br>";
  }

  try {
    $table = TABLE_PREFIX . 'settings';
    $sql = "DROP TABLE " . $table . ";";
    $sth = $pdo->prepare($sql);
    $sth->execute();
    echo "Table: " . $table . " dropped succesfully.<br>";
  } catch(\PDOException $e) {
    echo $sql . "<br>" . $e->getMessage() . "<br>";
  }

  try {
    $table = TABLE_PREFIX . 'pwreset';
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
    $table = TABLE_PREFIX . 'nyheter';
    $sql = "DROP TABLE " . $table . ";";
    $sth = $pdo->prepare($sql);
    $sth->execute();
    echo "Table: " . $table . " dropped succesfully.<br>";
  } catch(\PDOException $e) {
    echo $sql . "<br>" . $e->getMessage() . "<br>";
  }
}



try {
  $table = TABLE_PREFIX . 'logins';
  $sql = "CREATE TABLE " . $table . " (
    id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
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
  0,
  :user,
  :pwd);";

  $sth = $pdo->prepare($sql);
  $sth->bindParam(':user', $default_login, \PDO::PARAM_STR);
  $sth->bindParam(':pwd', $default_pwd, \PDO::PARAM_STR);
  $sth->execute();
  echo "Default username set: " . $default_login . "<br>Default password set: " . $default_pwd_plain . "<br>";
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
    user BIGINT UNSIGNED,
    ip CHAR(64),
    time BIGINT,
    sessionid CHAR(64),
    jwtkey VARCHAR(200),
    jwttoken VARCHAR(200)";
    if ($allow_references) {
    $sql .= ",
    CONSTRAINT
      FOREIGN KEY (user) REFERENCES " . TABLE_PREFIX . "logins (id)
    );"; }
    else { $sql .= ");"; }
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'kategorier';
  $sql = "CREATE TABLE " . $table . " (
    id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    kategori VARCHAR(80),
    uri_kategori VARCHAR(191) UNIQUE,
    ingress TEXT,
    seo_description VARCHAR(160),
    og_description VARCHAR(255),
    og_title VARCHAR(40),
    seo_keywords VARCHAR(255),
    meta_data_extra TEXT,
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
    id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    plats VARCHAR(80),
    ort VARCHAR(80),
    sort BIGINT UNSIGNED,
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
    id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    namn VARCHAR(188),
    pris INT,
    program MEDIUMTEXT,
    ingress TEXT,
    antaldagar INT UNSIGNED,
    ingar TEXT,
    bildkatalog VARCHAR(191) UNIQUE,
    url VARCHAR(191) UNIQUE,
    seo_description VARCHAR(160),
    og_description VARCHAR(255),
    og_title VARCHAR(40),
    seo_keywords VARCHAR(255),
    meta_data_extra TEXT,
    personnr BOOLEAN,
    fysiskadress BOOLEAN,
    aktiv BOOLEAN,
    utvald BOOLEAN DEFAULT 0,
    hotel TEXT,
    hotellink VARCHAR(255),
    facebook VARCHAR(255));";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'hallplatser_resor';
  $sql = "CREATE TABLE " . $table . " (
    hallplatser_id BIGINT UNSIGNED,
    resa_id BIGINT UNSIGNED,
    tid_in TIME,
    tid_ut TIME";
    if ($allow_references) {
    $sql .= ",
    CONSTRAINT
      FOREIGN KEY (resa_id) REFERENCES drs116573." . TABLE_PREFIX . "resor (id),
    CONSTRAINT
      FOREIGN KEY (hallplatser_id) REFERENCES " . TABLE_PREFIX . "hallplatser (id));"; }
    else { $sql .= ");"; }
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'kategorier_resor';
  $sql = "CREATE TABLE " . $table . " (
    resa_id BIGINT UNSIGNED,
    kategorier_id BIGINT UNSIGNED";
    if ($allow_references) {
    $sql .= ",
    CONSTRAINT
      FOREIGN KEY (resa_id) REFERENCES " . TABLE_PREFIX . "resor (id),
    CONSTRAINT
      FOREIGN KEY (kategorier_id) REFERENCES " . TABLE_PREFIX . "kategorier (id));"; }
    else { $sql .= ");"; }
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'tillaggslistor';
  $sql = "CREATE TABLE " . $table . " (
    resa_id BIGINT UNSIGNED,
    pris INT,
    namn VARCHAR(255)";
    if ($allow_references) {
    $sql .= ",
    CONSTRAINT
      FOREIGN KEY (resa_id) REFERENCES " . TABLE_PREFIX . "resor (id));"; }
    else { $sql .= ");"; }

  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'boenden';
  $sql = "CREATE TABLE " . $table . " (
    id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
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
    resa_id BIGINT UNSIGNED,
    boenden_id BIGINT UNSIGNED,
    pris INT";
    if ($allow_references) {
    $sql .= ",
    CONSTRAINT
      FOREIGN KEY (resa_id) REFERENCES " . TABLE_PREFIX . "resor (id),
    CONSTRAINT
      FOREIGN KEY (boenden_id) REFERENCES " . TABLE_PREFIX . "boenden (id));"; }
    else { $sql .= ");"; }

  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'datum';
  $sql = "CREATE TABLE " . $table . " (
    resa_id BIGINT UNSIGNED,
    datum DATE";
    if ($allow_references) {
    $sql .= ",
    CONSTRAINT
      FOREIGN KEY (resa_id) REFERENCES " . TABLE_PREFIX . "resor (id));"; }
    else { $sql .= ");"; }

  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'settings';
  $sql = "CREATE TABLE " . $table . " (
    id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    mode VARCHAR(20),
    oauth_clientid VARCHAR(200),
    oauth_clientsecret VARCHAR(100),
    oauth_initalized BOOLEAN,
    oauth_refreshtoken VARCHAR(200),
    googleemail VARCHAR(200),
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

try {
  $table = TABLE_PREFIX . 'pwreset';
  $sql = "CREATE TABLE " . $table . " (
    token CHAR(128),
    username CHAR(64),
    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP);";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}

try {
  $table = TABLE_PREFIX . 'nyheter';
  $sql = "CREATE TABLE " . $table . " (
    id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nyheter MEDIUMTEXT);";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  echo "Table: " . $table . " created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}
try {
$sql = "INSERT INTO " . $table . " (nyheter) VALUES (' ');";
$sth = $pdo->prepare($sql);
$sth->execute();
echo "Table: " . $table . " dummy news post created succesfully.<br>";
} catch(\PDOException $e) {
  echo $sql . "<br>" . $e->getMessage() . "<br>";
}
