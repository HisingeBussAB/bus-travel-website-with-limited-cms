<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\includes\classes;

final class DB
{
  protected static $pdo;

  public static function get() {
    if(!isset(self::$pdo)) {
      try{
        self::$pdo = new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASSWORD);
        self::$pdo->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER); //Forces table names to lower case
        self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); //Error throw exceptions, catch with code.
        self::$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false); //Not compatible with all drives, defaults to false if not supported. Prepare each statement instead.
      } catch(\PDOException $e) {
        DBException::getMessage($e, __CLASS__);
        return false;
      }
    }
   return self::$pdo;
  }

  private function __construct() {} //create error on wrongfull use
}
