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



    root\includes\classes\Sessions::secSessionStart(TRUE);
    try {
      if (!isset($_SESSION['token'])) {
        throw new \RuntimeException("Ingen säkerhetstoken hittad.");
      }
      $token = $_SESSION['token'];
      $_SESSION['token'] = "destroyed";

      if (substr($request , 0 , 3) == "new") {
        if ($request != "new" . $token) {
          throw new \RuntimeException("Fel säkerhetstoken.");
        }

        require __DIR__ . '/../../../dependencies/vendor/phpmailer/PHPMailerAutoload.php';
        $mail = new \PHPMailer;

        $mail->SMTPDebug = \SMTP::DEBUG_SERVER;
        $mail->SMTPDebug = 2;                    //Alternative to above constant
        $mail->isSMTP();                         // tell the class to use SMTP
        $mail->SMTPAuth   = true;                // enable SMTP authentication
        $mail->Port       = 25;                  // set the SMTP port
        $mail->Host       = "mail.yourhost.com"; // SMTP server
        $mail->Username   = "name@yourhost.com"; // SMTP account username
        $mail->Password   = "your password";     // SMTP account password



        echo "<html><head><title>Password Reset</title></head><body><p>Ett e-postmeddelande med återställningslänk har skickats.</p>
        <p>Har du inte tillgång till e-post kontot måste du kontakta systemadministratören.</p>
        <p><a href='/adminp'>Åter till inloggningssidan.</a></p></body></html>";
      } else {

        //DO RESET STUFF

      }






    } catch(\RuntimeException $e) {
      echo $e->getMessage();
      echo "<br>Prova ladda om tidigare sida och gå hit på nytt.<br><br>Du skickas snart till <a href='/adminp'>admin sidan</a>.";
      header("Refresh:3; url=/adminp");
    }
  }


}
