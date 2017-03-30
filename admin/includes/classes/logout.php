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

namespace HisingeBussAB\RekoResor\website\admin\includes\classes;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

class Logout
{

  public static function doLogout() {
    $sessionid  = $_SESSION['id'];
    $pdo = DB::get();

    try {
      $sql = "DELETE FROM " . TABLE_PREFIX . "loggedin WHERE sessionid = :sessionid AND user = :user;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':sessionid', $sessionid, \PDO::PARAM_STR);
      $sth->bindParam(':user', $username, \PDO::PARAM_STR);
      $sth->execute();
      $result = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      return FALSE;
    }
    unset($_COOKIE['RRJWT']);
    setcookie( 'RRJWT', '', 0, '/');
    $_SESSION['id'] = FALSE;
    session_unset();
    session_destroy();
    session_write_close();
    setcookie(session_name(),'',0,'/');
    return TRUE;
  }
}
