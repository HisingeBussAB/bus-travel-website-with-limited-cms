<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\admin\includes\classes;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

class MiscFunctions {

  /**
   * toggle
   *
   * @return boolean
   */
  public static function toggle($id, $table) {

    $pdo = DB::get();
    $state = FALSE;

    try {
      $pdo->beginTransaction();
      $sql = "SELECT * FROM " . TABLE_PREFIX . $table . " WHERE id = :id;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':id', $id, \PDO::PARAM_INT);
      $sth->execute();
      $result = $sth->fetch(\PDO::FETCH_ASSOC);

      if (!empty($result)) {

        if ($result["aktiv"]) $newstatus = 0; else $newstatus = 1;

        $sql = "UPDATE " . TABLE_PREFIX . $table . " SET aktiv = " . $newstatus . " WHERE id = :id;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        $sth->execute();
        $state = TRUE;
      } else {
        $state = FALSE;
      }
      $pdo->commit();
    } catch(\PDOException $e) {
      $pdo->rollBack();
      DBError::showError($e, __CLASS__, $sql);
      return FALSE;
    }
      return $state;
  }



  /**
   * reorder
   *
   * @return boolean
   */
  public static function reorder($id, $table, $direction) {

    $pdo = DB::get();
    $state = FALSE;
    $move = 0;
    if ($direction == "down") $move = 1;
    if ($direction == "up") $move = -1;


    try {
      $pdo->beginTransaction();

      $sql = "SELECT sort FROM " . TABLE_PREFIX . $table . " WHERE id = :id;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':id', $id, \PDO::PARAM_INT);
      $sth->execute();
      $result = $sth->fetch(\PDO::FETCH_ASSOC);

      $sql = "SELECT sort, id FROM " . TABLE_PREFIX . $table . " WHERE sort = " . ($result['sort'] + $move) . ";";
      $sth = $pdo->prepare($sql);
      $sth->execute();
      $result2 = $sth->fetch(\PDO::FETCH_ASSOC);

      //This will also check if we are at the end of the list. Second result will be empty.
      if (!empty($result) && !empty($result2)) {
        $sort = $result['sort'];
        $sort2 = $result2['sort'];
        $id2 = $result2['id'];
        $sql = "UPDATE " . TABLE_PREFIX . $table . " SET sort = " . ($sort + $move) . " WHERE id = :id;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        $sth->execute();

        $sql = "UPDATE " . TABLE_PREFIX . $table . " SET sort = " . ($sort2 - $move) . " WHERE id = :id;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':id', $id2, \PDO::PARAM_INT);
        $sth->execute();
        $state = TRUE;
      } else {
        $state = FALSE;
        //if it fails because we are at the end of the list we dont want to send error feedback
        if (empty($result2)) $state = TRUE;
      }


      $pdo->commit();
    } catch(\PDOException $e) {
      $pdo->rollBack();
      DBError::showError($e, __CLASS__, $sql);
      return FALSE;
    }
    return $state;
  }

}
