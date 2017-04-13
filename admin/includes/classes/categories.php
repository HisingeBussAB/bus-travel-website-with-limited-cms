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
      $sql = "SELECT * FROM " . TABLE_PREFIX . "kategorier ORDER BY sort;";
      $sth = $pdo->prepare($sql);
      $sth->execute();
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
    }

      return json_encode($result);

  }

  /**
   * getActiveCategories
   *
   * Returns kategorier from DB as for rendering on public pages
   *
   * @return array json
   */
  public static function getActiveCategories() {

    $pdo = DB::get();

    try {
      $sql = "SELECT kategori,uri_kategori FROM " . TABLE_PREFIX . "kategorier WHERE aktiv = 1 ORDER BY sort;";
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
   * @return bool success/fail
   */
  public static function createCategory($name) {

    $name = trim($name);

    $pdo = DB::get();

    $uri = root\includes\classes\Functions::uri_recode($name);

    try {
      $sql = "INSERT INTO " . TABLE_PREFIX . "kategorier (
        kategori,
        uri_kategori,
        sort,
        aktiv
      ) VALUES (
        :name,
        :uri,
        (SELECT IFNULL(MAX(sort), 0) FROM " . TABLE_PREFIX . "kategorier K) + 1,
        TRUE
      );";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':name', $name, \PDO::PARAM_STR);
      $sth->bindParam(':uri', $uri, \PDO::PARAM_STR);
      $sth->execute();
      return TRUE;
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      return FALSE;
    }
  }

}
