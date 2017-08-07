<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\includes\classes;
use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\Functions;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

class ProgramForm {

  public static function sendForm($data) {
    var_dump($data);

    try {
      $reply = "";
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

      $SMTPDebug = 0; //Should always be 0 or the authtoken/password will show on the page directly. Set to 2 if specifc problems with this piece of code
      $mail->SMTPDebug = $SMTPDebug;
      $mail->CharSet = 'UTF-8';
      $mail->isSMTP();
      $mail->SMTPAuth   = ($smtpresult['auth'] === '1');

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
      $mail->Subject  = 'Rekå Resor - Beställt program';
      $mail->Body     = 'Tack för att du beställt program.\n\rVi kommer skicka aktuella reseprogram till dig inom kort.\n\r' . $data;
      if(!$mail->send()) {
        $reply .=  '<br>Meddelandet kunde inte skickas.<br>';
        $reply .=  '<br>Mailer error: ' . $mail->ErrorInfo;
        throw new \Exception("Fel vid kommunikation med mailservern");
      } else {
        $reply .= "Tack! Vi skickar programmen snart.";
        echo json_encode($reply);
        return true;
      }
  } catch(\Exception $e) {
    $reply .=  "Ett fel har uppstått.";
    echo json_encode($reply);
    return false;
  }
  }
}
