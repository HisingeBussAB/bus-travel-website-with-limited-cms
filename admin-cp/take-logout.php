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

require_once __DIR__ . '/../../includes/functions/mainfunc.php';
require_once __DIR__ . '/../../includes/db_connect.php';

sec_session_start();
  if (isset($_SESSION['LOGGED_IN_TOKEN'])) {
    $orgtoken = $_SESSION['LOGGED_IN_TOKEN'];
  } else {
    $orgtoken = "none";
  }
  try {
    $sql = "DELETE FROM " . TABLE_PREFIX . "loggedin WHERE token = :token;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':token', $orgtoken);
    $sth->execute();
  } catch(PDOException $e) {
    echo "Databasfel:<br>";
    echo $sql . "<br>" . $e->getMessage();
    $pdo = NULL;
    exit;
  }
  session_unset();
  session_destroy();
  session_write_close();
  setcookie(session_name(),'',0,'/');
?>
