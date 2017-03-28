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

class Logout{

  function doLogout() {
    $sessionid  = $_SESSION['id'];

    try {
      $sql = "DELETE FROM " . TABLE_PREFIX . "loggedin WHERE sessionid = :sessionid AND user = :user;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':sessionid', $sessionid, \PDO::PARAM_STR);
      $sth->bindParam(':user', $username, \PDO::PARAM_STR);
      $sth->bindParam(':timelimit', $timelimit, \PDO::PARAM_INT);
      $sth->execute();
      $result = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBException::getMessage($e, __CLASS__, $sql);
      return false;
    }
    $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
    unset($_COOKIE['RRJWT']);
    setcookie( 'RRJWT', '', 0, '/');
    $_SESSION['id'] = false;
    session_unset();
    session_destroy();
    session_write_close();
    setcookie(session_name(),'',0,'/');
    return true;
  }
}
