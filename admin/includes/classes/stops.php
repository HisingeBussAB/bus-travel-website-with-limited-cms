<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\admin\includes\classes;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

class Stops {

  /**
   * getStopsJSON
   *
   * Returns hallplatser from DB as JSON
   *
   * @return array json
   */
  public static function getStopsJSON() {

    $pdo = DB::get();

    try {
      $sql = "SELECT * FROM " . TABLE_PREFIX . "hallplatser ORDER BY sort;";
      $sth = $pdo->prepare($sql);
      $sth->execute();
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
    }

      return json_encode($result);

  }

  /**
   * createStop
   *
   * Creates new row in table hallplatser
   *
   * @return bool success/fail
   */
  public static function createStop($name) {

    $pdo = DB::get();

    try {
      $sql = "INSERT INTO " . TABLE_PREFIX . "hallplatser (
        plats,
        sort,
        aktiv
      ) VALUES (
        :name,
        (SELECT IFNULL(MAX(sort), 0) FROM " . TABLE_PREFIX . "hallplatser K) + 1,
        TRUE
      );";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':name', $name, \PDO::PARAM_STR);
      $sth->execute();
      return TRUE;
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      return FALSE;
    }

  }

}
