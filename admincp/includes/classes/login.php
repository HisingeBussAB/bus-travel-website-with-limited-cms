<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\admincp\includes\classes;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBException;

require __DIR__ . '/../../../dependencies/vendor/phpjwt/JWT.php';
require __DIR__ . '/../../../dependencies/vendor/phpjwt/BeforeValidException.php';
require __DIR__ . '/../../../dependencies/vendor/phpjwt/ExpiredException.php';
require __DIR__ . '/../../../dependencies/vendor/phpjwt/SignatureInvalidException.php';

use \Firebase\JWT\JWT;


class Login
{
  public static function isLoggedIn() {
    return false;
  }

  public static function renderLoginForm() {
    include __DIR__ . '/../pages/loginForm.php';
  }

  public static function setLogin() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      if (root\includes\classes\HammerGuard::hammerGuard($_SERVER['REMOTE_ADDR']) === true) {
        echo "För många försök. Prova senare HTTP 406.";
        http_response_code(406);
        return false;
      }

      if ($_SESSION['TOKEN'] !== $_POST['token']) {
        echo "Fel token skickad. HTTP 401.";
        http_response_code(401);
        return false;
      }

      $username = filter_var(trim($_POST['user']), FILTER_SANITIZE_STRING);
      if (empty($username) || empty(trim($_POST['pwd']))) {
        echo "Både användarnamn och lösenord måste anges. Försök igen. HTTP 401.";
        http_response_code(401);
        return false;
      }


      /* FIXME DEBUG
      if (!reCaptcha::tryReCaptcha()) {
        echo "Tyvärr reCaptcha misslyckades. Försök igen. HTTP 401.";
        http_response_code(401);
        return false;
      }
      */

      $pdo = DB::get();

      $result = false;
      try {
        $sql = "SELECT id, username, pwd FROM " . TABLE_PREFIX . "logins WHERE username = :user LIMIT 1;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':user', $username, \PDO::PARAM_STR);
        $sth->execute();
        $result = $sth->fetch(\PDO::FETCH_ASSOC);
      } catch(\PDOException $e) {
        DBException::getMessage($e, __CLASS__, $sql);
        return false;
      }

      if ($result === false) {
        http_response_code(401);
        echo "Fel lösenord eller användare. Prova igen. HTTP 401.";
        return false;
      }

      if (($result['username'] == $username) && (password_verify(trim($_POST['pwd']) . FIX_PWD_PEPPER, $result['pwd']))) {
        //LOGIN SUCCESS. SET LOGGED IN STATUS
        $username = $result['id']; //sets username to internal id
        $userAgent = hash('sha256', $_SERVER['HTTP_USER_AGENT']);
        $userIP = hash('sha256', $_SERVER['REMOTE_ADDR']);

        //Set session status logged in
        $_SESSION['useragent']  = $userAgent;
        $_SESSION['isloggedin'] = $username;

        //Set JWT status logged in
        $tokenKey = base64_encode(openssl_random_pseudo_bytes(96));
        $tokenId = self::setJWT($tokenKey, 80, $username, $userAgent);

        try {
          $sql = "INSERT INTO " . TABLE_PREFIX . "loggedin (
            user,
            ip,
            jwtkey,
            jwttoken,
            time)
            VALUES (
            :user,
            :ip,
            :jwtkey,
            :jwttoken,
            :time);";

          $sth = $pdo->prepare($sql);
          $sth->bindParam(':user', $username, \PDO::PARAM_INT);
          $sth->bindParam(':ip', $userIP, \PDO::PARAM_STR);
          $sth->bindParam(':jwtkey', $tokenKey, \PDO::PARAM_STR);
          $sth->bindParam(':jwttoken', $tokenId, \PDO::PARAM_STR);
          $sth->bindParam(':time', $_SERVER['REQUEST_TIME'], \PDO::PARAM_INT);
          $sth->execute();

        } catch(\PDOException $e) {
          DBException::getMessage($e, __CLASS__, $sql);
          return false;
        }








        var_dump(self::getJWT($tokenKey));


      } else {
        http_response_code(401);
        echo "Fel lösenord eller användare. Prova igen. HTTP 401.";
        return false;
      }










    } else {
      echo "Felformaterad förfrågan HTTP 405.";
      http_response_code(405);
      return false;
    }

  }

  private static function setJWT($secretKey, $duration, $username, $userAgent=false) {

    if (!$userAgent) $userAgent = hash('sha256', $_SERVER['HTTP_USER_AGENT']);
    $tokenId = base64_encode(openssl_random_pseudo_bytes(96));
    $issuedAt = $_SERVER['REQUEST_TIME'];
    $notBefore = $issuedAt;
    $expire = $notBefore + $duration;

    $data = [
      'iat'  => $issuedAt,                  // Issued at: time when the token was generated
      'jti'  => $tokenId,                   // Json Token Id: an unique identifier for the token
      'iss'  => $_SERVER['SERVER_NAME'],    // Issuer
      'nbf'  => $notBefore,                 // Not before
      'exp'  => $expire,                    // Expire
      'data' => [                           // Data related to the signer user
        'userId'   => $username,            // userid from the users table
        'stamp'    => JWT_STAMP,            // server wide JWT watermark
        'userAgent'=> $userAgent            // make sure HTTP_USER_AGENT is consisent
      ]
    ];

    $jwt = JWT::encode($data, $secretKey,'HS512');
    $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
    setcookie( 'RRJWT', $jwt, 0, '/', $domain, HTTPS, true);
    return $tokenId;
  }

  private static function getJWT($secretKey, $leeway=0) {

    if (isset($_COOKIE['RRJWT'])) {
      JWT::$leeway = $leeway;
      try {
        $jwt = JWT::decode($_COOKIE['RRJWT'], $secretKey, array('HS512'));
        return $jwt;
      } catch(Exception $e) {
        echo $e;
        return false;
      }
    } else {
      return false;
    }
  }

}
