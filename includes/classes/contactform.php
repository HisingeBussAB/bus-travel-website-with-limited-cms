<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\includes\classes;
use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\Functions;
use HisingeBussAB\RekoResor\website\includes\classes\HammerGuard;
use HisingeBussAB\RekoResor\website\includes\classes\Tokens;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

class ContactForm {

  public static function sendForm($data) {



    try {

      $reply = "";

      $pdo = DB::get();

      try {
        $pdo = DB::get();
        $sql = "SELECT * FROM " . TABLE_PREFIX . "settings WHERE id = 1;";
        $sth = $pdo->prepare($sql);
        $sth->execute();
        $smtpresult = $sth->fetch(\PDO::FETCH_ASSOC);
      } catch(\PDOException $e) {
        if (DEBUG_MODE) {throw new \Exception($reply .= DBError::showError($e, __CLASS__, $sql));} else {throw new \Exception("Internt serverfel!");}
      }

      if (!require __DIR__ . '/../../vendor/autoload.php') { throw new \Exception("Internt serverfel!"); };

      $auth = ($smtpresult['auth'] === '1');

      $SMTPDebug = 0; //Should always be 0 or the authtoken/password will show on the page directly. Set to 2 if specifc problems with this piece of code

      if ($smtpresult['mode'] === "smtp") {

        $mail = new PHPMailer;


        $mail->SMTPDebug = $SMTPDebug;
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        //$mail->SMTPAuth   = $auth;
        $mail->SMTPAuth   = true;

        //$mail->Port       = $smtpresult['port'];
        $mail->Port       = '53';

        /*
        if ($smtpresult['tls'] === "tls") {
          $mail->SMTPSecure = 'tls';
        }
        elseif ($smtpresult['tls'] === "ssl") {
          $mail->SMTPSecure = 'ssl';
        }
        */

        //$mail->Host       = $smtpresult['server'];
        //$mail->Username   = $smtpresult['smtpuser'];
        //$mail->Password   = $smtpresult['smtppwd'];
        $mail->Host       = SMTP_HOST;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;

      } elseif ($smtpresult['mode'] === "gmail") {

        $mail = new \PHPMailerOAuth;

        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = $SMTPDebug;

        $mail->oauthUserEmail = $smtpresult['googleemail'];
        $mail->oauthClientId = $smtpresult['oauth_clientid'];
        $mail->oauthClientSecret = $smtpresult['oauth_clientsecret'];
        $mail->oauthRefreshToken = $smtpresult['oauth_refreshtoken'];
        $smtpresult['smtpuser'] = $smtpresult['googleemail'];



      } else {
        throw new \Exception("Felkonfigurerade e-post inställningar för sidan.");
      }

      //VALIDATE REQUEST
      if( stripos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === FALSE ) {
        throw new \RuntimeException("Fel urspringssida.");
      }

      if( !empty($_SERVER['HTTP_ORIGIN']) ) {
        if( stripos($_SERVER['HTTP_ORIGIN'], $_SERVER['HTTP_HOST']) === FALSE ) {
          throw new \RuntimeException("Förfrågan ser inte ut att komma från Rekå Resors hemsida.");
        }
      }

      if (!Tokens::checkFormToken($data['token'], $data['tokenid'], 'contact')) {
        throw new \RuntimeException("Fel säkerhetstoken skickad. <a href='javascript:window.location.href=window.location.href'>Prova att ladda om sidan.</a>");
      }

      if (!empty($data['url'])) {
        throw new \RuntimeException("Inte skickad. Lämna fältet \"Leave this empty:\" tomt.");
      }

      $clienthash = md5($_SERVER['HTTP_USER_AGENT']);
      if ($clienthash !== trim($data['client'])) {
        throw new \RuntimeException("Något har ändrats, inte längre samma klient. <a href='javascript:window.location.href=window.location.href'>Prova att ladda om sidan.</a>");
      }


      //VALIDATE FORM DATA
      if (empty($data['email'])) {
        $data['email'] = FALSE;
      }

      if ( empty($data['email']) && empty($data['tel'])) {
        throw new \RuntimeException("Vänligen fyll i antingen e-post eller telefonummer så vi kan svara dig.");
      }

      if ( empty($data['message'])) {
        throw new \RuntimeException("Du måste fylla i ett meddelande.");
      }


      if (empty($data['name']))
        $data['name'] = "";
      if (empty($data['tel']))
        $data['tel'] = "";
      if (empty($data['address']))
        $data['addres'] = "";
      if (empty($data['zip']))
        $data['zip'] = "";
      if (empty($data['city']))
        $data['city'] = "";


      $data['name'] = filter_var(trim($data['name']), FILTER_SANITIZE_STRING);
      $data['address'] = filter_var(trim($data['address']), FILTER_SANITIZE_STRING);
      $data['zip'] = filter_var(trim($data['zip']), FILTER_SANITIZE_STRING);
      $data['city'] = filter_var(trim($data['city']), FILTER_SANITIZE_STRING);
      $data['message'] = filter_var(trim($data['message']), FILTER_SANITIZE_STRING);
      $data['tel'] = filter_var(trim($data['tel']), FILTER_SANITIZE_STRING);

      if ($data['email'] !== FALSE) {
        $data['email'] = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
      }

      if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) && $data['email'] !== FALSE) {
        throw new \RuntimeException("Det här verkar inte vara en giltig e-mail. Försök igen.");
      }




      //FINAL HAMMER CHECK
      if (HammerGuard::hammerGuard($_SERVER['REMOTE_ADDR'])) {
        throw new \RuntimeException("För många försök. Vänta lite innan du försöker igen.");
      }



      $mail->ClearAllRecipients();
      $mailbody = "Programbeställning från hemsidan:\r\n\r\n" .
                  $data['name'] . "\r\n" .
                  $data['address'] . "\r\n" .
                  $data['zip'] . " " . $data['city'] . "\r\n" .
                  $data['tel'] . "\r\n\r\n" .
                  $data['email'] . " \r\n\r\nMeddelande:\r\n" . $data['message'];

      $mailbody .= "\r\n\r\nSkickad: " . date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);



      $mail->setFrom('hakan@rekoresor.se', 'Hemsidan - Rekå Resor');
      $mail->Sender='hakan@rekoresor.se';
      $mail->AddReplyTo('hakan@rekoresor.se');
      $mail->addAddress('hakan@rekoresor.se');
      $mail->Subject  = "Meddelande från hemsidan - Rekå Resor";
      $mail->Body     = $mailbody;

      if(!$mail->send()) {
        $reply .=  '<br>Meddelandet kunde inte skickas.<br>';
        if (DEBUG_MODE) { $reply .=  '<br>Mailer error: ' . $mail->ErrorInfo; }
        throw new \Exception("Fel vid kommunikation med mailservern.");
      } else {
        $mail->ClearAllRecipients();
        $mail->ClearReplyTos();
      $mail->setFrom('hakan@rekoresor.se', 'Hemsidan - Rekå Resor');
        $mail->AddReplyTo('hakan@rekoresor.se');
        if (!empty($data['email'])) { $mail->addAddress($data['email']); }
        $mail->Subject  = "Tack för ditt meddelande.";
        $mail->Body     = "Vi svarar så snart vi kan.";

        if ($data['email'] !== FALSE) {
          if(!$mail->send()) {
            $reply .=  '<br>Meddelandet kunde inte skickas.<br>';
            if (DEBUG_MODE) { $reply .=  '<br>Mailer error: ' . $mail->ErrorInfo; }
            throw new \Exception("Fel vid kommunikation med mailservern");
          }
        }
          $reply .= "Tack! Vi svarar så snart vi kan.";
          header('Content-Type: application/json; charset=utf-8');
          header('Access-Control-Allow-Origin: http' . APPEND_SSL . '://' .$_SERVER['HTTP_HOST']);
          http_response_code(200);
          echo json_encode($reply);
          return true;
      }




  } catch(\RuntimeException $e) {
    header('Content-Type: text/html; charset=utf-8');
    echo $e->getMessage();
    http_response_code(403);
    return false;
  } catch(\Exception $e) {
    header('Content-Type: text/html; charset=utf-8');
    echo $e->getMessage();
    echo "Misslyckades tyvärr! Prova gärna igen.<br />Vänligen skicka e-post till <a href='mailto:info@rekoresor.se'>info@rekoresor.se</a> eller ring oss på <a href='tel:+4631222120'>031-22 21 20</a> istället.";
    if (DEBUG_MODE) echo $reply;
    http_response_code(500);
    return false;
  }
  }
}
