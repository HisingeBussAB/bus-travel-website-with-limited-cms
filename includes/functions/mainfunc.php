<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../db_connect.php';


function sec_session_start() {
  /**
  * Function initalizes the more secure session handler
  */
   $session_name = 'auth_session';   // Set a custom session name
    /*Sets the session name.
     *This must come before session_set_cookie_params due to an undocumented bug/feature in PHP.
     */
    session_name($session_name);

    // This stops JavaScript being able to access the session id.
    $httponly = true;
    ini_set('session.use_only_cookies', 1);
    // Gets current cookies params.
    $currentCookieParams = session_get_cookie_params();

    session_set_cookie_params($currentCookieParams["lifetime"],
        $currentCookieParams["path"],
        $currentCookieParams["domain"],
        HTTPS,
        $httponly);

    session_start();            // Start the PHP session
    session_regenerate_id(true);    // regenerated the session, delete the old one.
}


function hammerguard($ip) {
  /**
  * Function checks for brute force attacks using hammerguard table with sha1(REMOTE_ADDR) for more then 29 tries in the last 1800 sec
  */
  global $pdo;

  $ip = hash('sha256',$ip);
  $time = $_SERVER['REQUEST_TIME'];
  $timelimit = $time-1801;
  try {
    $sql = "DELETE FROM " . TABLE_PREFIX . "hammerguard WHERE time < :timelimit;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':timelimit', $timelimit);
    $sth->execute();
  } catch(PDOException $e) {
    echo "Databasfel från hammerguard():<br>";
    echo $sql . "<br>" . $e->getMessage();
    $pdo = NULL;
    exit;
  }

  $thecount = 0;
  try {
    $sql = "SELECT count(*) FROM " . TABLE_PREFIX . "hammerguard WHERE iphash = :ip;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':ip', $ip);
    $sth->execute();
    $count = $sth->fetch(PDO::FETCH_NUM);
    $thecount = reset($count);
  } catch(PDOException $e) {
    echo "Databasfel från hammerguard():<br>";
    echo $sql . "<br>" . $e->getMessage();
    $pdo = NULL;
    exit;
  }

  if ($thecount < 30) {
    try {
      $sql = "INSERT INTO " . TABLE_PREFIX . "hammerguard (
        iphash,
        time)
        VALUES (
        :ip,
        :time);";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':ip', $ip);
      $sth->bindParam(':time', $time);
      $sth->execute();
    } catch(PDOException $e) {
      echo "Databasfel från hammerguard():<br>";
      echo $sql . "<br>" . $e->getMessage();
      $pdo = NULL;
      exit;
    }
      return false;
  } else {
    return true;
  }
}
?>
