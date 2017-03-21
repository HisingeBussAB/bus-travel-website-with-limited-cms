<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * (c) Rekå Resor AB
 *
 * @link      https://github.com/HisingeBussAB/bus-travel-website-with-limited-cms
 * @copyright CC BY-SA 4.0 (http://creativecommons.org/licenses/by-sa/4.0/)
 * @license   GNU General Public License v3.0
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\includes\classes;

class DBConnect
{
  public $pdo;

  public function __construct() {
    try{
      $this->pdo = new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASSWORD);
      $this->pdo->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER); //Forces table names to lower case
      $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); //Error throw exceptions, catch with code.
      $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false); //Not compatible with all drives, defaults to false if not supported. Prepare each statement instead.
    } catch(\PDOException $e) {
      if (DEBUG_MODE) { echo "Databasfel: " . $e->getMessage(); } else { echo "Databasen svarar inte. Kontakta <a href=\"mailto:webmaster@rekoresor.se\">webmaster@rekoresor.se</a>"; }
      exit;
    }
  }
}
?>
