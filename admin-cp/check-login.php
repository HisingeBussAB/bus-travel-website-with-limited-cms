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

require_once __DIR__ . "/../includes/db_connect.php";

function loginCheck() {
  global $pdo;

  if (isset($_SESSION['LOGGED_IN_TOKEN']) && isset($_SESSION['LOGGED_IN_USER']) && isset($_SESSION['USER']) && isset($_SESSION['MICROTIME'])) {
    $orgtoken = $_SESSION['LOGGED_IN_TOKEN'];
    $orgusertoken = $_SESSION['LOGGED_IN_USER'];
    $user = $_SESSION['USER'];
    $microtime = $_SESSION['MICROTIME'];
    $sessionsalt = $_SESSION['SALT'];
    $timelimit = $_SERVER['REQUEST_TIME']-14600;

    $flag = false;
    $result = array();
    try {
      $sql = "SELECT salt FROM " . TABLE_PREFIX . "loggedin WHERE user = :user AND token = :token AND time > :timelimit;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':timelimit', $timelimit);
      $sth->bindParam(':token', $orgtoken);
      $sth->bindParam(':user', $user);
      $sth->execute();

      while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
          $flag = true;
          $result[] = $row;
      }
    } catch(PDOException $e) {
      echo "Databasfel:<br>";
      echo $sql . "<br>" . $e->getMessage();
      $pdo = NULL;
      return false;
      exit;
    }

    if ($flag !== false && count($result) == 1) {
      /* We found the corresponding database token entry and only one of it.
      Proceed to check integrity of hashes */
      $result = $result[0];
      $token = hash('sha256', $user . $microtime . $sessionsalt . LOGGED_IN_TOKEN_SALT);
      $usertoken = hash('sha256', $user . filter_var ($_SERVER['HTTP_USER_AGENT'], FILTER_SANITIZE_STRING) . $result['salt'] . LOGGED_IN_USER_SALT);

      if ($token === $orgtoken && $usertoken === $orgusertoken) {
        return true;
        //logged in session accepted
      }
    }
  }

  return false;
}
