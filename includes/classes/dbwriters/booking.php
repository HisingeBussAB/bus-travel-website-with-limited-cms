<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\includes\classes\dbwriters;

use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

class Booking {

  

  public static function save($data) {
  

    $paxlist = array();
    if (!empty($data['resenar1'] && $data['resenar1'] != $data['name'])) { array_push($paxlist, array($data['resenar1'], $data['resenar1-pnr'])); }
    if (!empty($data['resenar2'])) { array_push($paxlist, array($data['resenar2'], $data['resenar2-pnr'])); }
    if (!empty($data['resenar3'])) { array_push($paxlist, array($data['resenar3'], $data['resenar3-pnr'])); }
    if (!empty($data['resenar4'])) { array_push($paxlist, array($data['resenar4'], $data['resenar4-pnr'])); }
    if (!empty($data['resenar5'])) { array_push($paxlist, array($data['resenar5'], $data['resenar5-pnr'])); }
    if (!empty($data['resenar6'])) { array_push($paxlist, array($data['resenar6'], $data['resenar6-pnr'])); }
    $departloc = empty($data['stop']) ? "" : $data['stop'];
    $date = empty($data['departure']) ? "" : $data['departure'];
    $tour = empty($data['tour']) ? "" : $data['tour'];
    $phone = empty($data['phone']) ? "" : $data['phone'];
    $room = empty($data['room']) ? "" : $data['room'];
    $email = empty($data['email']) ? "" : $data['email'];
    $name = empty($data['name']) ? "" : $data['name'];
    $street = empty($data['address']) ? "" : $data['address'];
    $terms = empty($data['terms']) && $data['terms'] != 'ja' ? 0 : 1;
    $zip = empty($data['zip']) ? "" : $data['zip'];
    $city = empty($data['city']) ? "" : $data['city'];
    $request = empty($data['misc']) ? "" : $data['misc'];
    $pnr = empty($data['resenar1-pnr'] || $data['resenar1'] != $data['name']) ? '' : $data['resenar1-pnr'];
    $ip = empty($_SERVER['REMOTE_ADDR']) ? "" : $_SERVER['REMOTE_ADDR'];
    $tourid = empty($data['tourid']) ? NULL : filter_var($data['tourid'], FILTER_SANITIZE_NUMBER_INT);
    $stops = explode(', ',$data['stop']);
    $ort = empty($stops[0]) ? '' : '%' . trim(trim($stops[0]),',') . '%';
    $hlp = empty($stops[1]) ? '' : '%' . trim(trim($stops[1]),',') . '%';

    $departtime = NULL;
    $bokid = NULL;
    try {
      $pdo = DB::get();
      $sql = "SELECT tid_ut FROM " . TABLE_PREFIX . "hallplatser_resor as hlp_r
      LEFT JOIN " . TABLE_PREFIX . "hallplatser as hlp on hlp_r.hallplatser_id = hlp.id
        WHERE hlp_r.resa_id = :tourid AND hlp.ort LIKE :ort AND hlp.plats LIKE :hlp LIMIT 1;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':tourid', $tourid, \PDO::PARAM_INT);
      $sth->bindParam(':ort', $ort, \PDO::PARAM_STR);
      $sth->bindParam(':hlp', $hlp, \PDO::PARAM_STR);
      $sth->execute();
      $result = $sth->fetch(\PDO::FETCH_ASSOC);
      $departtime = $result['tid_ut'];
    } catch(\PDOException $e) {
      if (DEBUG_MODE) {$departtime = NULL; throw new \Exception($reply .= DBError::showError($e, __CLASS__, $sql));} else {$departtime = NULL;}
    }


    $sqlmain = "INSERT INTO bokningar(
              tourid
              ,date
              ,departloc
              ,departtime
              ,room
              ,name
              ,street
              ,zip
              ,city
              ,email
              ,phone
              ,gdpr
              ,pnr
              ,request
              ,processed
              ,ip)
          VALUES (
            :tourid
              ,:date
              ,:departloc
              ,:departtime
              ,:room
              ,:name
              ,:street
              ,:zip
              ,:city
              ,:email
              ,:phone
              ,:gdpr
              ,:pnr
              ,:request
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
      $sth->bindParam(':date', $date, \PDO::PARAM_STR);
      $sth->bindParam(':departloc', $departloc, \PDO::PARAM_STR);
      $sth->bindParam(':departtime', $departtime, \PDO::PARAM_STR);
      $sth->bindParam(':room', $room, \PDO::PARAM_STR);
      $sth->bindParam(':name', $name, \PDO::PARAM_STR);
      $sth->bindParam(':street', $street, \PDO::PARAM_STR);
      $sth->bindParam(':zip', $zip, \PDO::PARAM_STR);
      $sth->bindParam(':city', $city, \PDO::PARAM_STR);
      $sth->bindParam(':email', $email, \PDO::PARAM_STR);
      $sth->bindParam(':phone', $phone, \PDO::PARAM_STR);
      $sth->bindParam(':gdpr', $terms, \PDO::PARAM_INT);
      $sth->bindParam(':pnr', $pnr, \PDO::PARAM_STR);
      $sth->bindParam(':request', $request, \PDO::PARAM_STR);
      $sth->bindParam(':ip', $ip, \PDO::PARAM_STR);
      $sth->execute();
      $sql = "SELECT LAST_INSERT_ID() as id;";
      $sth = $pdoBooking->prepare($sql);
      $sth->execute(); 
      $bokid = $sth->fetch(\PDO::FETCH_ASSOC); 
        foreach ($paxlist as $pax) {
          if (!empty($pax)) {
            $name = empty($pax[0]) ? '' : $pax[0];
            $pnr = empty($pax[1]) ? '' : $pax[1];
            $sql = "INSERT INTO extrapax(bokningar_id, name, pnr)
            VALUES (
              :id
              ,:name
              ,:pnr
            )
          ;";
          $sth = $pdoBooking->prepare($sql);
          $sth->bindParam(':id', $bokid['id'], \PDO::PARAM_INT);
          $sth->bindParam(':name', $name, \PDO::PARAM_STR);
          $sth->bindParam(':pnr', $pnr, \PDO::PARAM_STR);
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
      $email = substr(md5($email),0,5) . '@' . substr(md5($email),0,5) . ".test";
      $name = 'test' . substr(md5($name),0,10);
      $phone = '031-888888';
      $request = md5($request);
      $street = substr(md5($street),0,15);
      $terms = empty($data['terms']) && $data['terms'] != 'ja' ? 0 : 1;
      $zip = '111 11';
      $pnr = '000000-0000';
      $city = substr(md5($city),0,15);
      $ip = '127.0.0.1';
      $tourid = empty($data['tourid']) ? NULL : filter_var($data['tourid'], FILTER_SANITIZE_NUMBER_INT);
      $sql = $sqlmain;
      $pdoBookingTest = DB::getBookingDB_test();
      $pdoBookingTest->beginTransaction();
      $sth = $pdoBookingTest->prepare($sql);
      $sth->bindParam(':tourid', $tourid, \PDO::PARAM_INT);
      $sth->bindParam(':date', $date, \PDO::PARAM_STR);
      $sth->bindParam(':departloc', $departloc, \PDO::PARAM_STR);
      $sth->bindParam(':departtime', $departtime, \PDO::PARAM_STR);
      $sth->bindParam(':room', $room, \PDO::PARAM_STR);
      $sth->bindParam(':name', $name, \PDO::PARAM_STR);
      $sth->bindParam(':street', $street, \PDO::PARAM_STR);
      $sth->bindParam(':zip', $zip, \PDO::PARAM_STR);
      $sth->bindParam(':city', $city, \PDO::PARAM_STR);
      $sth->bindParam(':email', $email, \PDO::PARAM_STR);
      $sth->bindParam(':phone', $phone, \PDO::PARAM_STR);
      $sth->bindParam(':gdpr', $terms, \PDO::PARAM_INT);
      $sth->bindParam(':pnr', $pnr, \PDO::PARAM_STR);
      $sth->bindParam(':request', $request, \PDO::PARAM_STR);
      $sth->bindParam(':ip', $ip, \PDO::PARAM_STR);
      $sth->execute();
      $sql = "SELECT LAST_INSERT_ID() as id;";
      $sth = $pdoBookingTest->prepare($sql);
      $sth->execute(); 
      $bokid = $sth->fetch(\PDO::FETCH_ASSOC); 
        foreach ($paxlist as $pax) {
          if (!empty($pax)) {
            $name = empty($pax[0]) ? '' : $pax[0];
            $pnr = empty($pax[1]) ? '' : $pax[1];
            $name = 'test' . substr(md5($name),0,10);
            $pnr = '000000-0000';
            $sql = "INSERT INTO extrapax(bokningar_id, name, pnr)
            VALUES (
              :id
              ,:name
              ,:pnr
            )
          ;";
          $sth = $pdoBookingTest->prepare($sql);
          $sth->bindParam(':id', $bokid['id'], \PDO::PARAM_INT);
          $sth->bindParam(':name', $name, \PDO::PARAM_STR);
          $sth->bindParam(':pnr', $pnr, \PDO::PARAM_STR);
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