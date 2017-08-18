<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\admin\includes\pages;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

class PWReset
{

  public static function doReset($request) {
    try {
      root\includes\classes\Sessions::secSessionStart(TRUE);
      if (trim($request) === "requestnew" && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $token = root\includes\classes\Tokens::getFormToken("pwreset",600);
        echo "<html><head><title>Password Reset</title></head><body>";
        echo "<script src='https://www.google.com/recaptcha/api.js'></script>";
        echo "<form action='/adminp/resetpw/new' method='POST' accept-charset='utf-8'>
              <div class='g-recaptcha' data-sitekey='" . RECAPTCHA_PUBLIC . "'></div>
              <input type='hidden' value='" . $token['id'] . "' name='tokenid'>
              <input type='hidden' value='" . $token['token'] . "' name='token'>
              <p><button type='submit' id='login-submit'>Begär återställningslänk</button></p>
              </form></body></html>";
      } elseif (trim($request) === "new" && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!root\includes\classes\Tokens::checkFormToken(trim($_POST['token']),trim($_POST['tokenid']),"pwreset")) {
          throw new \RuntimeException("Fel säkerhetstoken skickad med begäran. Ladda om.");
        }

        if (!root\admin\includes\classes\reCaptcha::tryReCaptcha()) {
          throw new \RuntimeException("Tyvärr reCaptcha misslyckades. Gå tillbaka och försök igen.");
        }
        self::runReset("new");


      } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && trim($request) !== "new") {
        self::runReset(trim($request));
      } else {
        echo "<html><head><title>Password Reset</title></head><body><p>Okänt fel i begäran</p><p><a href='/adminp'>Åter till inloggningssidan.</a></p></body></html>";
      }

    } catch(\RuntimeException $e) {
      echo $e->getMessage();
      echo "<br>Prova ladda om tidigare sida och gå hit på nytt.<br><br><a href='/adminp'>Tillbaka till admin sidan</a>.</body></html>";
    }
  }




  private static function runReset($request) {



    try {


      $pdo = DB::get();

      try {
        $sql = "SELECT * FROM " . TABLE_PREFIX . "settings WHERE id = 1;";
        $sth = $pdo->prepare($sql);
        $sth->execute();
        $smtpresult = $sth->fetch(\PDO::FETCH_ASSOC);
      } catch(\PDOException $e) {
        throw new \Exception(DBError::showError($e, __CLASS__, $sql));
      }
      $auth = ($smtpresult['auth'] === '1');

      if ($request === "new") {

        try {
          $sql = "SELECT username FROM " . TABLE_PREFIX . "logins WHERE id = 1;";
          $sth = $pdo->prepare($sql);
          $sth->execute();
          $user = $sth->fetch(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
          throw new \Exception(DBError::showError($e, __CLASS__, $sql));
        }

        //Generate and save reset auth token
        $unixtime = $_SERVER['REQUEST_TIME'];
        $authtoken = hash('sha512', bin2hex(openssl_random_pseudo_bytes(128)) . $unixtime);

        $expired = $unixtime - 600;

        //Purge table of expires auth tokens
        try {
          $sql = "DELETE FROM " . TABLE_PREFIX . "pwreset WHERE time < :expiration;";
          $sth = $pdo->prepare($sql);
          $sth->bindParam(':expiration', $expired, \PDO::PARAM_INT);
          $sth->execute();
        } catch(\PDOException $e) {
          throw new \Exception(DBError::showError($e, __CLASS__, $sql));
        }


        //Check for excessive requests
        try {
          $sql = "SELECT token FROM " . TABLE_PREFIX . "pwreset;";
          $sth = $pdo->prepare($sql);
          $sth->execute();
          $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
          throw new \Exception(DBError::showError($e, __CLASS__, $sql));
        }

        if (count($result) > 11) {
          throw new \RuntimeException("För många återställningsförsök har gjorts. Prova igen om 10 minuter.");
        }


        //Save our token
        try {
          $sql = "INSERT INTO " . TABLE_PREFIX . "pwreset (
            token,
            time)
            VALUES (
            :token,
            :timestamp);";
          $sth = $pdo->prepare($sql);
          $sth->bindParam(':token', $authtoken, \PDO::PARAM_STR);
          $sth->bindParam(':timestamp', $unixtime, \PDO::PARAM_STR);
          $sth->execute();
        } catch(\PDOException $e) {
          throw new \Exception(DBError::showError($e, __CLASS__, $sql));
        }




        require __DIR__ . '/../../../vendor/autoload.php';
        $mail = new \PHPMailer;


        $SMTPDebug = 0; //Should always be 0 or the authtoken/password will show on the page directly. Set to 2 if specifc problems with this piece of code
        $mail->SMTPDebug = $SMTPDebug;
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->SMTPAuth   = $auth;

        $mail->Port       = $smtpresult['port'];

        if ($smtpresult['tls'] === "tls") {
          $mail->SMTPSecure = 'tls';
        }
        elseif ($smtpresult['tls'] === "ssl") {
          $mail->SMTPSecure = 'ssl';
        }

        $mail->Host       = $smtpresult['server'];
        $mail->Username   = $smtpresult['smtpuser'];
        $mail->Password   = $smtpresult['smtppwd'];

        $mail->setFrom($smtpresult['smtpuser'], 'Hemsidan');
        $mail->addAddress($smtpresult['email']);
        $mail->Subject  = 'Rekå Resor - Återställ lösenord';
        $mail->Body     = 'Använd den här länken för att återställa lösenordet för ' .  $user['username'] . ': http' . APPEND_SSL . '://' . $_SERVER['SERVER_NAME'] . '/adminp/resetpw/' . $authtoken;
        if(!$mail->send()) {
          echo '<br>Meddelandet kunde inte skickas.<br>';
          echo '<br>Mailer error: ' . $mail->ErrorInfo;
          throw new \Exception("Fel vid kommunikation med mailservern");
        } else {
          echo "<p>Ett e-postmeddelande med återställningslänk har skickats. Länken är giltig i 10 minuter.</p>
          <p>Har du inte tillgång till e-post kontot måste du kontakta systemadministratören.</p>
          <p><a href='/adminp'>Åter till inloggningssidan.</a></p></body></html>";
        }




      } else {
        echo "<html><head><title>Password Reset</title></head><body>";
        $authtoken = $request;

        try {
          $sql = "SELECT token FROM " . TABLE_PREFIX . "pwreset WHERE token = :token;";
          $sth = $pdo->prepare($sql);
          $sth->bindParam(':token', $authtoken, \PDO::PARAM_STR);
          $sth->execute();
          $result = $sth->fetch(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
          throw new \Exception(DBError::showError($e, __CLASS__, $sql));
        }

        if (!empty($result['token'])) {


          $i = 0;
          do {
          $password = openssl_random_pseudo_bytes(3+$i, $strong);
            $i++;
          } while ($strong !== TRUE);

          $keyspace = '0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ'; //no capital i or small l to improve readability
          $specialchars = '!%&?+#$_*.=';

          $password = bin2hex($password);

          $length = mt_rand(2, 4);
          $password .= substr(str_shuffle($specialchars),0,$length);
          $length = mt_rand(1, 3);
          $password .= substr(str_shuffle($keyspace),0,$length);
          $password = str_shuffle($password);
          $password .= substr(str_shuffle($keyspace),0,1); //Just make sure it doesn't end or start with a specialchar for readability
          $password = substr(str_shuffle($keyspace),0,1) . $password;



          $options = [
              'cost' => 10, //difficulty for password_hash
          ];

          $hashedpassword = password_hash($password . FIX_PWD_PEPPER, PASSWORD_DEFAULT, $options);


          try {

            $sql = "UPDATE " . TABLE_PREFIX . "logins SET pwd = :pwd WHERE id = 0;";

            $sth = $pdo->prepare($sql);
            $sth->bindParam(':pwd', $hashedpassword, \PDO::PARAM_STR);
            $sth->execute();
          } catch(\PDOException $e) {
            throw new \Exception(DBError::showError($e, __CLASS__, $sql));
          }

          require __DIR__ . '/../../../dependencies/vendor/phpmailer/PHPMailerAutoload.php';

          $mail = new \PHPMailer;


          $SMTPDebug = 0; //Should always be 0 or the authtoken/password will show on the page directly. Set to 2 if specifc problems with this piece of code
          $mail->SMTPDebug = $SMTPDebug;
          $mail->CharSet = 'UTF-8';
          $mail->isSMTP();
          $mail->SMTPAuth   = $auth;

          $mail->Port       = $smtpresult['port'];

          if ($smtpresult['tls'] === "tls") {
            $mail->SMTPSecure = 'tls';
          }
          elseif ($smtpresult['tls'] === "ssl") {
            $mail->SMTPSecure = 'ssl';
          }

          $mail->Host       = $smtpresult['server'];
          $mail->Username   = $smtpresult['smtpuser'];
          $mail->Password   = $smtpresult['smtppwd'];

          $mail->setFrom($smtpresult['smtpuser'], 'Hemsidan');
          $mail->addAddress($smtpresult['email']);
          $mail->Subject  = 'Återställt';
          $mail->Body     = $password;
          if(!$mail->send()) {
            echo '<br>Meddelandet kunde inte skickas.<br>';
            echo '<br>Mailer error: ' . $mail->ErrorInfo;
            throw new \Exception("Fel vid kommunikation med mailservern");
          } else {
            echo "<p>Ett e-postmeddelande med nytt lösenord har skickats till administratörs e-posten.</p>
            <p>Har du inte tillgång till e-post kontot måste du kontakta systemadministratören.</p>
            <p><a href='/adminp'>Åter till inloggningssidan.</a></p></body></html>";
          }

          //Purge used token
          try {
            $sql = "DELETE FROM " . TABLE_PREFIX . "pwreset WHERE token = :token;";
            $sth = $pdo->prepare($sql);
            $sth->bindParam(':token', $authtoken, \PDO::PARAM_STR);
            $sth->execute();
          } catch(\PDOException $e) {
            throw new \Exception(DBError::showError($e, __CLASS__, $sql));
          }


        } else {
          throw new \RuntimeException("Authentieringstoken hittades inte. Du måste skapa en begäran med länken på login sidan.");
        }

      }






    } catch(\RuntimeException $e) {
      echo $e->getMessage();
      echo "<br>Prova ladda om tidigare sida och gå hit på nytt.<br><br><a href='/adminp'>Tillbaka till admin sidan</a>.</body></html>";
    } catch(\Exception $e) {
      echo $e->getMessage();
      echo "<br>Ett fel har inträffat. Tillbaka till <a href='/adminp'>admin sidan</a>.</body></html>";
    }
  }


}
