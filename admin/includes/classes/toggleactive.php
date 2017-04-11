<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\admin\includes\classes;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

class ToggleActive {

  /**
   * toggle
   *
   * @return boolean
   */
  public static function toggle($id, $table) {

    $pdo = DB::get();

    try {
      $sql = "SELECT * FROM " . TABLE_PREFIX . $table . " WHERE id = :id;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':id', $id, \PDO::PARAM_INT);
      $sth->execute();
      $result = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      return FALSE;
    }
    if (!empty($result)) {

      if ($result["aktiv"]) $newstatus = 0; else $newstatus = 1;

      try {
        $sql = "UPDATE " . TABLE_PREFIX . $table . " SET aktiv = " . $newstatus . " WHERE id = :id;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        $sth->execute();
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql);
        return FALSE;
      }
      return TRUE;
    } else {
      return FALSE;
    }



  }

}
