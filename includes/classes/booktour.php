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

class BookTour {

  public static function sendForm($data) {

    $reply = "";

    try {

      //VALIDATE REQUEST
      if( stripos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === FALSE ) {
        throw new \RuntimeException("Fel urspringssida.");
      }

      if( !empty($_SERVER['HTTP_ORIGIN']) ) {
        if( stripos($_SERVER['HTTP_ORIGIN'], $_SERVER['HTTP_HOST']) === FALSE ) {
          throw new \RuntimeException("Förfrågan ser inte ut att komma från Rekå Resors hemsida.");
        }
      }

      if (!Tokens::checkFormToken($data['token'], $data['tokenid'], 'book')) {
        throw new \RuntimeException("Fel säkerhetstoken skickad. <a href='javascript:window.location.href=window.location.href'>Prova att ladda om sidan.</a>");
      }

      if (!empty($data['url'])) {
        throw new \RuntimeException("Inte skickad. Lämna fältet \"Leave this empty:\" tomt.");
      }

      $clienthash = md5($_SERVER['HTTP_USER_AGENT']);
      if ($clienthash !== trim($data['client'])) {
        throw new \RuntimeException("Något har ändrats, inte längre samma klient. <a href='javascript:window.location.href=window.location.href'>Prova att ladda om sidan.</a>");
      }

      var_dump($data);
      //VALIDATE FORM DATA
      if (empty($data['email'])) {
        $data['email'] = FALSE;
      }

      $data['tour'] = filter_var(trim($data['tour']), FILTER_SANITIZE_STRING);
      $data['departure'] = filter_var(trim($data['departure']), FILTER_SANITIZE_STRING);
      $data['stop'] = filter_var(trim($data['stop']), FILTER_SANITIZE_STRING);
      $data['room'] = filter_var(trim($data['room']), FILTER_SANITIZE_STRING);
      $data['phone'] = filter_var(trim($data['phone']), FILTER_SANITIZE_STRING);

      $data['resenar1'] = filter_var(trim($data['resenar1']), FILTER_SANITIZE_STRING);
      $data['resenar1-pnr'] = filter_var(trim($data['resenar1-pnr']), FILTER_SANITIZE_STRING);
      $data['resenar2'] = filter_var(trim($data['resenar2']), FILTER_SANITIZE_STRING);
      $data['resenar2-pnr'] = filter_var(trim($data['resenar2-pnr']), FILTER_SANITIZE_STRING);
      $data['resenar3'] = filter_var(trim($data['resenar3']), FILTER_SANITIZE_STRING);
      $data['resenar3-pnr'] = filter_var(trim($data['resenar3-pnr']), FILTER_SANITIZE_STRING);
      $data['resenar4'] = filter_var(trim($data['resenar4']), FILTER_SANITIZE_STRING);
      $data['resenar4-pnr'] = filter_var(trim($data['resenar4-pnr']), FILTER_SANITIZE_STRING);
      $data['resenar5'] = filter_var(trim($data['resenar5']), FILTER_SANITIZE_STRING);
      $data['resenar5-pnr'] = filter_var(trim($data['resenar5-pnr']), FILTER_SANITIZE_STRING);
      $data['resenar6'] = filter_var(trim($data['resenar6']), FILTER_SANITIZE_STRING);
      $data['resenar6-pnr'] = filter_var(trim($data['resenar6-pnr']), FILTER_SANITIZE_STRING);

      $data['terms'] = filter_var(trim($data['terms']), FILTER_SANITIZE_STRING);
      $data['misc'] = filter_var(trim($data['misc']), FILTER_SANITIZE_STRING);

      $data['name'] = filter_var(trim($data['name']), FILTER_SANITIZE_STRING);
      $data['address'] = filter_var(trim($data['address']), FILTER_SANITIZE_STRING);
      $data['zip'] = filter_var(trim($data['zip']), FILTER_SANITIZE_STRING);
      $data['city'] = filter_var(trim($data['city']), FILTER_SANITIZE_STRING);

      if ($data['email'] !== FALSE) {
        $data['email'] = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
      }

      if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) && $data['email'] !== FALSE) {
        throw new \RuntimeException("Det här verkar inte vara en giltig e-mail. Försök igen.");
      }

      if ( empty($data['email']) && empty($data['phone']) && empty($data['address'])) {
        throw new \RuntimeException("Vänligen fyll i mer information och försök igen.");
      }

      if ( empty($data['terms']) OR $data['terms'] !== 'ja' ) {
        throw new \RuntimeException("Du måste godkänna resevillkoren.");
      }


      //FINAL HAMMER CHECK
      if (HammerGuard::hammerGuard($_SERVER['REMOTE_ADDR'])) {
        throw new \RuntimeException("För många försök. Vänta lite innan du försöker igen.");
      }


      try {
        $pdo = DB::get();
        $sql = "SELECT * FROM " . TABLE_PREFIX . "settings WHERE id = 1;";
        $sth = $pdo->prepare($sql);
        $sth->execute();
        $smtpresult = $sth->fetch(\PDO::FETCH_ASSOC);
      } catch(\PDOException $e) {
        if (DEBUG_MODE) {throw new \Exception($reply .= DBError::showError($e, __CLASS__, $sql));} else {throw new \Exception($reply .= "Databasfel.");}
      }

      if (!require_once __DIR__ . '/../../dependencies/vendor/phpmailer/PHPMailerAutoload.php') {throw new \Exception($reply .= "Mailer hittades inte.");}

      $mail = new \PHPMailer;

      $mailbody = "Resebokning från hemsidan:\r\n\r\n" .
                  "Resa: " . $data['tour'] . "\r\n" .
                  "Datum: " . $data['departure'] . "\r\n" .
                  "Påstigning: " . $data['stop'] . "\r\n\r\n" .
                  $data['name'] . "\r\n" .
                  $data['address'] . "\r\n" .
                  $data['zip'] . " " . $data['city'] . "\r\n\r\n" .
                  "E-mail: " . $data['email'] . " \r\n" .
                  "Telefon: " . $data['phone'] . "\r\n\r\n" .
                  "Resenärer: \r\n" .
                  $data['resenar1'] . " " . $data['resenar1-pnr'] . "\r\n" .
                  $data['resenar2'] . " " . $data['resenar2-pnr'] . "\r\n" .
                  $data['resenar3'] . " " . $data['resenar3-pnr'] . "\r\n" .
                  $data['resenar4'] . " " . $data['resenar4-pnr'] . "\r\n" .
                  $data['resenar5'] . " " . $data['resenar5-pnr'] . "\r\n" .
                  $data['resenar6'] . " " . $data['resenar6-pnr'] . "\r\n\r\n" .
                  "Övrigt: \r\n" . $data['misc'] .  "\r\n\r\n" .
                  "Villkor: " . $data['terms'];

      $mailbody .= "\r\n\r\nSkickad: " . date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

      $SMTPDebug = 0; //Should always be 0 or the authtoken/password will show on the page directly. Set to 2 if specifc problems with this piece of code
      $mail->SMTPDebug = $SMTPDebug;
      $mail->CharSet = 'UTF-8';
      $mail->isSMTP();
      $mail->SMTPAuth   = ($smtpresult['auth'] === '1');
      $mail->ClearAllRecipients();

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

      $mail->setFrom('info@rekoresor.se', 'Hemsidan - Rekå Resor');
      $mail->addAddress('program@rekoresor.se');
      $mail->Subject  = "Rekå Resor - Resebokning från hemsidan";
      $mail->Body     = $mailbody;

      if(!$mail->send()) {
        $reply .=  '<br>Meddelandet kunde inte skickas.<br>';
        $reply .=  '<br>Mailer error: ' . $mail->ErrorInfo;
        throw new \Exception("Fel vid kommunikation med mailservern.");
      } else {
        $mail->ClearAllRecipients();
        $mail->setFrom('info@rekoresor.se', 'Rekå Resor');
        if (!empty($data['email'])) { $mail->addAddress($data['email']); }
        $mail->Subject  = "Tack för din bokning.";
        $mail->Body     = "Tack för att du bokat en resa.\r\nIfall det är en fleragarsresa kommer bokningsbekräftelse och inbetalningskort på posten inom kort.\r\nNedan är en kopia på uppgifterna vi tagit emot.\r\n\r\n" . $mailbody;

        if ($data['email'] !== FALSE) {
          if(!$mail->send()) {
            $reply .=  '<br>Meddelandet kunde inte skickas.<br>';
            $reply .=  '<br>Mailer error: ' . $mail->ErrorInfo;
            throw new \Exception("Fel vid kommunikation med mailservern");
          }
        }
          $reply .= "Tack! Vi har tagit emot din bokning.";
          echo json_encode($reply);
          http_response_code(200);
          return true;
      }




  } catch(\RuntimeException $e) {
    if (DEBUG_MODE) echo $e->getMessage();
    echo "Åtgärden har stoppats.";
    http_response_code(403);
    return false;
  } catch(\Exception $e) {
  $reply .=  "Ett tekniskt fel har uppstått.";
  if (DEBUG_MODE) echo $e->getMessage();
  echo $reply;
  http_response_code(500);
  return false;
  }
  }
}