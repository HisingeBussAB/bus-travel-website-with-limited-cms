<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\includes\classes;

/**
 * Check for hammering
 */
class HammerGuard {

  /**
   * Function checks for brute force attacks using hammerguard table with sha256 has of REMOTE_ADDR for more then 20 tries in the last 3600 sec
   *
   * @uses TABLE_PREFIX
   * @uses \HisingeBussAB\RekoResor\website\includes\classes\DB
   * @uses \HisingeBussAB\RekoResor\website\includes\classes\DBException
   * @param string $ip A sha256 hash of $_SERVER['REMOTE_ADDR']
   * @return bool FALSE = OK, TRUE = REQUEST IS SPAMMY
   */
  public static function hammerGuard($ip) {

  $pdo = DB::get();

  $ip = hash('sha256',$ip);
  $time = $_SERVER['REQUEST_TIME'];
  $timelimit = $time-3600;
  try {
    $sql = "DELETE FROM " . TABLE_PREFIX . "hammerguard WHERE time < :timelimit;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':timelimit', $timelimit);
    $sth->execute();
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    exit;
  }

  $thecount = 0;
  try {
    $sql = "SELECT count(*) FROM " . TABLE_PREFIX . "hammerguard WHERE iphash = :ip;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':ip', $ip);
    $sth->execute();
    $count = $sth->fetch(\PDO::FETCH_NUM);
    $thecount = reset($count);
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    exit;
  }

  if ($thecount < 21) {
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
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      exit;
    }
      return false;
  } else {
    return true;
  }
}
}
