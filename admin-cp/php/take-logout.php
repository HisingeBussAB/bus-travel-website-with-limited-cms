<?php
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
