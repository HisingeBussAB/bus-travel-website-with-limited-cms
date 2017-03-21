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

$remoteip = $_SERVER['REMOTE_ADDR'];
if (filter_var($remoteip, FILTER_VALIDATE_IP) === false) {
  $remoteip = '0.0.0.0';
}

if (hammerguard($remoteip) === true) {
  http_response_code(403);
  echo "För många försök. Prova senare 403";
  $pdo = NULL;
  exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  if (($_SESSION['FIX_TOKEN'] == FIXED_LOGIN_TOKEN) && (filter_var ($_SESSION['RAND_TOKEN'], FILTER_SANITIZE_STRING) == filter_var ($_POST['rand_token'], FILTER_SANITIZE_STRING))) {

    //RECAPTA START Check I am not a robot recaptcha

    $post_data = http_build_query(
        array(
          'secret' => RECAPTCHA_SECRET,
          'response' => $_POST['g-recaptcha-response'],
          'remoteip' => $_SERVER['REMOTE_ADDR']
        )
      );
      $opts = array('http' =>
        array(
          'method'  => 'POST',
          'header'  => 'Content-type: application/x-www-form-urlencoded',
          'content' => $post_data
        )
      );
      $context  = stream_context_create($opts);
      $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
      $result = json_decode($response);
      if (!$result->success) {
        http_response_code(403);
        echo "reCAPTCHA missyckades.";
        $pdo = NULL;
        exit;
      }
      //RECAPTCA END

    $username = trim(filter_input (INPUT_POST, 'user', FILTER_SANITIZE_STRING));
    $result = false;
    try {
      $sql = "SELECT id, username, pwd FROM " . TABLE_PREFIX . "logins WHERE username = :user LIMIT 1;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':user', $username, PDO::PARAM_STR);
      $sth->execute();
      $result = $sth->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
      echo "Databasen är nere. Inloggning kan inte verifieras.<br>";
      echo $sql . "<br>" . $e->getMessage();
      $pdo = NULL;
      exit;
    }

    if ($result == false) {
      http_response_code(403);
      echo "Fel lösenord eller användare. Prova igen.";
      $pdo = NULL;
      exit;
    };

    if ($result["username"] == $username) {
      if (password_verify(trim($_POST['pwd']) . FIX_PWD_PEPPER, $result['pwd'])) {
      //login success
      $username = $result['id']; //Change user identification to internal id.
      $loginsalt = bin2hex(openssl_random_pseudo_bytes(32));
      $time = $_SERVER['REQUEST_TIME'];
      $useragent = trim(filter_var ($_SERVER['HTTP_USER_AGENT'], FILTER_SANITIZE_STRING));
      $usertoken = hash('sha256', $username . $useragent . $loginsalt . LOGGED_IN_USER_PEPPER);
      $_SESSION['LOGGED_IN_USER'] = $usertoken;
      $_SESSION['USER'] = $username;

      //GARBAGE COLLECTOR loggedin table
      $timelimit = $_SERVER['REQUEST_TIME']-14400;
      try {
        $sql = "DELETE FROM " . TABLE_PREFIX . "loggedin WHERE time < :timelimit;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':timelimit', $timelimit, PDO::PARAM_INT);
        $sth->execute();
      } catch(PDOException $e) {
        echo "Databasfel:<br>";
        echo $sql . "<br>" . $e->getMessage();
        $pdo = NULL;
        exit;
      }

      //Create logged in token in database loggedin.
      try {
        $sql = "INSERT INTO " . TABLE_PREFIX . "loggedin (
          time,
          user,
          salt)
          VALUES (
          :time,
          :user,
          :salt);";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':time', $time, PDO::PARAM_INT);
        $sth->bindParam(':salt', $loginsalt, PDO::PARAM_STR);
        $sth->bindParam(':user', $username, PDO::PARAM_STR);
        $sth->execute();
      } catch(PDOException $e) {
        echo "Databasfel:<br>";
        echo $sql . "<br>" . $e->getMessage();
        $pdo = NULL;
        exit;
      }
      http_response_code(200);
      echo "Login OK!";
      $pdo = NULL;
      exit;

    } else {
      http_response_code(403);
      echo "Fel lösenord eller användare. Prova igen.";
      $pdo = NULL;
      exit;
    }
    } else {
      http_response_code(403);
      echo "Fel lösenord eller användare. Prova igen.";
      $pdo = NULL;
      exit;
    }

  } else {
    http_response_code(403);
    echo "Felaktiga ursprungstokens. 403";
    $pdo = NULL;
    exit;
  }

} else {
  http_response_code(403);
  echo "Förbjuden metod. 403";
  $pdo = NULL;
  exit;
}
