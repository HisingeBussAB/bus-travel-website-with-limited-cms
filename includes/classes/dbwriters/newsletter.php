<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\includes\classes\dbwriters;

use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;


class Newsletter {

  public static function save($data) {
    $email = empty($data['email']) ? "" : $data['email'];
    $ip = empty($_SERVER['REMOTE_ADDR']) ? "" : $_SERVER['REMOTE_ADDR'];
    $sqlmain = "INSERT INTO newsletter(email, processed, ip)
          VALUES (
            :email
            ,0
            ,:ip
          )
        ;";
    $reply = "";
    try {
      $sql = $sqlmain;
      $pdoBooking = DB::getBookingDB();
      $pdoBooking->beginTransaction();
      $sth = $pdoBooking->prepare($sql);
      $sth->bindParam(':email', $email, \PDO::PARAM_STR);
      $sth->bindParam(':ip', $ip, \PDO::PARAM_STR);
      $sth->execute();
      $sql = "SELECT LAST_INSERT_ID() as id;";
      $sth = $pdoBooking->prepare($sql);
      $sth->execute(); 
      $pdoBooking->commit();
    } catch(\PDOException $e) {
      $pdoBooking->rollBack();
      if (DEBUG_MODE) {throw new \Exception($reply .= DBError::showError($e, __CLASS__, $sql));} else {throw new \Exception("Internt serverfel!");}
    }
  try {
    //Anonymize for test
    $sql = $sqlmain;
    $email = substr(md5($email),0,5) . '@' . substr(md5($email),0,5) . ".test";
    $ip = '127.0.0.1';
    $pdoBookingTest = DB::getBookingDB_test();
    $pdoBookingTest->beginTransaction();
    $sth = $pdoBookingTest->prepare($sql);
    $sth->bindParam(':email', $email, \PDO::PARAM_STR);
    $sth->bindParam(':ip', $ip, \PDO::PARAM_STR);
    $sth->execute();
    $pdoBookingTest->commit();
  } catch(\PDOException $e) {
    $pdoBookingTest->rollBack();
    if (DEBUG_MODE) {throw new \Exception($reply .= DBError::showError($e, __CLASS__, $sql));} else {throw new \Exception("Internt serverfel!");}
  }
}


}