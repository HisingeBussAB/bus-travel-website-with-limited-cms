<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\includes\classes;

final class DB
{
  /**
   * @var object PDO connection object
   */
  protected static $pdo;

  /**
   * @uses DB_HOST DB_NAME DB_USER DB_PASSWORD
   * @uses HisingeBussAB\RekoResor\website\includes\classes\DBException
   * @return object PDO connection
   */
  public static function get() {
    if(!isset(self::$pdo)) {
      try{
        self::$pdo = new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASSWORD);
        self::$pdo->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER); //Forces table names to lower case
        self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); //Error throw exceptions, catch with code.
        self::$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false); //Not compatible with all drives, defaults to false if not supported. Prepare each statement instead.
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__);
        return false;
      }
    }
   return self::$pdo;
  }

  /**
   * Private contructor - Creates error if trying to call this class with new
   */
  private function __construct() {}
}
