<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\admin\includes\classes;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

class Categories {

  /**
   * getCategoriesJSON
   *
   * Returns kategorier from DB as JSON
   *
   * @return array json
   */
  public static function getCategoriesJSON() {

    $pdo = DB::get();

    try {
      $sql = "SELECT * FROM " . TABLE_PREFIX . "kategorier;";
      $sth = $pdo->prepare($sql);
      $sth->execute();
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
    }

      return json_encode($result);

  }

  /**
   * createCategory
   *
   * Creates new row in table kategorier
   *
   * @return
   */
  public static function createCategory($name) {

    $pdo = DB::get();

    try {
      $sql = "INSERT INTO " . TABLE_PREFIX . "kategorier (
        kategori,
        aktiv
      ) VALUES (
        :name,
        TRUE
      );";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':name', $name, \PDO::PARAM_STR);
      $sth->execute();
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
    }
  }

}
