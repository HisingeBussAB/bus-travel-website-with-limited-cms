<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\includes\classes\dbwriters;

use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

class Lead {

  

  public static function save($data) {
    $email = empty($data['email']) ? "" : $data['email'];
    $name = empty($data['name']) ? "" : $data['name'];
    $street = empty($data['address']) ? "" : $data['address'];
    $terms = empty($data['terms']) && $data['terms'] != 'ja' ? 0 : 1;
    $zip = empty($data['zip']) ? "" : $data['zip'];
    $city = empty($data['city']) ? "" : $data['city'];
    $ip = empty($_SERVER['REMOTE_ADDR']) ? "" : $_SERVER['REMOTE_ADDR'];
    $tourid = empty($data['tourid']) ? NULL : filter_var($data['tourid'], FILTER_SANITIZE_NUMBER_INT);
    $category = empty($data['category']) || !is_array($data['category']) ? array() : $data['category'];
    $leadid = NULL;
    $sqlmain = "INSERT INTO leads(tourid, name, street, zip, city, email, gdpr, processed, ip)
          VALUES (
            :tourid
            ,:name
            ,:street
            ,:zip
            ,:city
            ,:email
            ,:gdpr
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
      $sth->bindParam(':tourid', $tourid, \PDO::PARAM_INT);
      $sth->bindParam(':name', $name, \PDO::PARAM_STR);
      $sth->bindParam(':street', $street, \PDO::PARAM_STR);
      $sth->bindParam(':zip', $zip, \PDO::PARAM_STR);
      $sth->bindParam(':city', $city, \PDO::PARAM_STR);
      $sth->bindParam(':email', $email, \PDO::PARAM_STR);
      $sth->bindParam(':gdpr', $terms, \PDO::PARAM_INT);
      $sth->bindParam(':ip', $ip, \PDO::PARAM_STR);
      $sth->execute();
      $sql = "SELECT LAST_INSERT_ID() as id;";
      $sth = $pdoBooking->prepare($sql);
      $sth->execute(); 
      $leadid = $sth->fetch(\PDO::FETCH_ASSOC); 
        foreach ($category as $cat) {
          if (!empty($cat)) {
            $sql = "INSERT INTO leads_categories(leadid, category)
            VALUES (
              :id
              ,:category
            )
          ;";
          $sth = $pdoBooking->prepare($sql);
          $sth->bindParam(':id', $leadid['id'], \PDO::PARAM_INT);
          $sth->bindParam(':category', $cat, \PDO::PARAM_STR);
          $sth->execute();
          }
        }
      $pdoBooking->commit();
    } catch(\PDOException $e) {
      $pdoBooking->rollBack();
      if (DEBUG_MODE) {throw new \Exception($reply .= DBError::showError($e, __CLASS__, $sql));} else {throw new \Exception("Internt serverfel!");}
    }
  try {
    //Anonymize for test
    $sql = $sqlmain;
    $email = substr(md5($email),0,5) . '@' . substr(md5($email),0,5) . ".test";
    $name = 'test' . substr(md5($name),0,10);
    $street = substr(md5($street),0,15);
    $terms = empty($data['terms']) && $data['terms'] != 'ja' ? 0 : 1;
    $zip = '111 11';
    $city = substr(md5($city),0,15);
    $ip = '127.0.0.1';
    $tourid = empty($data['tourid']) ? NULL : filter_var($data['tourid'], FILTER_SANITIZE_NUMBER_INT);
    $category = empty($category) && is_array($category) ? array() : $category;
    $pdoBookingTest = DB::getBookingDB_test();
    $pdoBookingTest->beginTransaction();
    $sth = $pdoBookingTest->prepare($sql);
    $sth->bindParam(':tourid', $tourid, \PDO::PARAM_INT);
    $sth->bindParam(':name', $name, \PDO::PARAM_STR);
    $sth->bindParam(':street', $street, \PDO::PARAM_STR);
    $sth->bindParam(':zip', $zip, \PDO::PARAM_STR);
    $sth->bindParam(':city', $city, \PDO::PARAM_STR);
    $sth->bindParam(':email', $email, \PDO::PARAM_STR);
    $sth->bindParam(':gdpr', $terms, \PDO::PARAM_INT);
    $sth->bindParam(':ip', $ip, \PDO::PARAM_STR);
    $sth->execute();
    $sql = "SELECT LAST_INSERT_ID() as id;";
      $sth = $pdoBookingTest->prepare($sql);
      $sth->execute(); 
      $leadid = $sth->fetch(\PDO::FETCH_ASSOC); 
        foreach ($category as $cat) {
          if (!empty($cat)) {
            $sql = "INSERT INTO leads_categories(leadid, category)
            VALUES (
              :id
              ,:category
            )
          ;";
          $sth = $pdoBookingTest->prepare($sql);
          $sth->bindParam(':id', $leadid['id'], \PDO::PARAM_INT);
          $sth->bindParam(':category', $cat, \PDO::PARAM_STR);
          $sth->execute();
          }
        }
    $pdoBookingTest->commit();
  } catch(\PDOException $e) {
    $pdoBookingTest->rollBack();
    if (DEBUG_MODE) {throw new \Exception($reply .= DBError::showError($e, __CLASS__, $sql));} else {throw new \Exception("Internt serverfel!");}
  }
}


}