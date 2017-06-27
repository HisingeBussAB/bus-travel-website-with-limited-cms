<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * Admin settings menu
 *
 */
namespace HisingeBussAB\RekoResor\website\admin\includes\pages;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\admin as admin;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

class Settings {

  public static function show() {
    try {
      root\includes\classes\Sessions::secSessionStart();
      if (admin\includes\classes\Login::isLoggedIn() === TRUE) {

        $pageTitle = "Rekå Admin - Inställningar";
        header('Content-type: text/html; charset=utf-8');
        include __DIR__ . '/shared/header.php';

        $token = bin2hex(openssl_random_pseudo_bytes(16));
        $_SESSION['token'] = $token;
        $userid = $_SESSION['isloggedin'];

        $pdo = DB::get();

        try {
          $sql = "SELECT * FROM " . TABLE_PREFIX . "settings WHERE id = 0;";
          $sth = $pdo->prepare($sql);
          $sth->execute();
          $stmpresult = $sth->fetch(\PDO::FETCH_ASSOC);

        } catch(\PDOException $e) {
          DBError::showError($e, __CLASS__, $sql);
          throw new \RuntimeException("<p>Databasfel! Kontakta systemadministratören om problemet består.</p>");
        }

        try {
          $sql = "SELECT username FROM " . TABLE_PREFIX . "logins WHERE id = :userid;";
          $sth = $pdo->prepare($sql);
          $sth->bindParam(':userid', $userid, \PDO::PARAM_INT);
          $sth->execute();
          $userresult = $sth->fetch(\PDO::FETCH_ASSOC);

        } catch(\PDOException $e) {
          DBError::showError($e, __CLASS__, $sql);
          throw new \RuntimeException("<p>Databasfel! Kontakta systemadministratören om problemet består.</p>");
        }

        echo "<form action='/adminajax/updatepassword' method='post' accept-charset='utf-8' class='settings-form' id='pwd-form'>
                <h2>Ändra användarnamn/lösenord</h2>
                <input type='hidden' name='token' value='$token' />
                <input type='hidden' name='userid' value='$userid' />
                <fieldset>
                  <label for='newuser'>Användarnamn:</label>
                  <input type='text' maxlength='63' name='newuser' value='" . $userresult['username'] . "' required />
                </fieldset>
                <fieldset>
                  <label for='mainpwd'>Nuvarande lösenord:</label>
                  <input type='password' maxlength='250' name='mainpwd' required />
                </fieldset>
                <fieldset>
                  <label for='newpwd'>Nytt lösenord (minst 6 tecken):</label>
                  <input type='password' pattern='.{6,}' maxlength='250' name='newpwd' id='new-password' />
                  <div class='new-password-div' id='new-password-strength_human'></div><div class='new-password-div' id='new-password-strength_score'></div>
                </fieldset>
                <fieldset>
                  <label for='verifynewpwd'>Nytt lösenord igen:</label>
                  <input type='password' pattern='.{6,}' maxlength='250' name='verifynewpwd' />
                </fieldset>
                <fieldset>
                  <button type='submit' id='send-pwd'>Byt lösenord/användarnamn</button>
                </fieldset>
                <fieldset>
                  <p id='password-reply'></p>
                </fieldset>
              </form>
              <hr class='settings-form'>
              <form action='/adminajax/updatesettings' method='post' accept-charset='utf-8' class='settings-form' id='settings-form'>
                <h2>Ändra SMTP inställningar</h2>
                <input type='hidden' name='token' value='$token' />
                <input type='hidden' name='userid' value='$userid' />
                <fieldset>
                  <label for='adminemail'>Administratörs e-mail:</label>
                  <input type='email' maxlength='200' name='adminemail' value='" . $stmpresult['email'] . "' />
                </fieldset>
                <fieldset>
                  <label for='smtpserver'>SMTP server:</label>
                  <input type='input' maxlength='200' name='smtpserver' value='" . $stmpresult['server'] . "' />
                </fieldset>
                <fieldset>
                  <label for='smtpport'>SMTP port:</label>
                  <input type='number' min='0' max='65535' name='smtpport' value='" . $stmpresult['port'] . "' />
                </fieldset>
                <fieldset>
                  <label for='stmpauth' id='smtp-auth-label'> Använd SMTP autentisering:</label>
                  <input type='checkbox' name='stmpauth'  id='smtp-auth-box' ";
        if ($stmpresult['auth'] === "1") { echo "checked"; }
        echo      " />
                </fieldset>
                <fieldset>
                  <label for='adminemail'>SMTP encryption:</label>
                  <select name='tls'>
                    <option value='tls' ";
        if ($stmpresult['tls'] === "tls") { echo "selected"; }
        echo      " >TLS</option>
                    <option value='ssl' ";
        if ($stmpresult['tls'] === "ssl") { echo "selected"; }
        echo      " >SSL</option>
                    <option value='no' ";
        if ($stmpresult['tls'] === "no") { echo "selected"; }
        echo      " >None</option>
                  </select>
                </fieldset>
                <fieldset>
                  <label for='stmppwd'>SMTP användarnamn:</label>
                  <input type='input' maxlength='200' name='stmppwd' value='" . $stmpresult['smtpuser'] . "' />
                </fieldset>
                <fieldset>
                  <label for='stmpuser'>SMTP lösenord:</label>
                  <input type='input' maxlength='200' name='stmpuser' value='" . $stmpresult['smtppwd'] . "' />
                </fieldset>
                <fieldset>
                  <label for='mainpwd'>Lösenord för inloggning på den här sidan:</label>
                  <input type='password' maxlength='250' name='mainpwd' required />
                </fieldset>
                <fieldset>
                  <button type='submit' id='send-settings'>Ändra inställningar</button>
                </fieldset>
                <fieldset>
                  <p id='settings-reply'></p>
                </fieldset>
              </form>";


        include __DIR__ . '/shared/scripts.php';
        echo "<script src='/admin/js/settings.js'></script>";
        include __DIR__ . '/shared/footer.php';


      } else {
        //Not logged in
        admin\includes\classes\Login::renderLoginForm();
      }

    } catch(\RuntimeException $e) {
      echo $e->getMessage();
      echo "<p><a href='/adminp'>Tillbaka till huvudsidan för administrering.</p>";
    }
  }

  public static function update($update) {

    $httpcode = 500;
    $result['responseText'] = "";


    try {

      if (empty($_POST)) {
        $httpcode = 401;
        throw new \RuntimeException("<p>Ingen data skickad.</p>");
      }

      $pdo = DB::get();
      //Check token
      if ($_SESSION['token'] !== trim($_POST['token'])) {
        $httpcode = 401;
        throw new \RuntimeException("<p>Fel säkerhetstoken, prova ladda om sidan.</p>");
      }

      //Read and sanitize common form variables
      $userid = filter_var(trim($_POST['userid']), FILTER_SANITIZE_NUMBER_INT);
      $password = filter_var(trim($_POST['mainpwd']), FILTER_UNSAFE_RAW);


      //Verify user password first
      try {
        $sql = "SELECT * FROM " . TABLE_PREFIX . "logins WHERE id = :userid;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':userid', $userid, \PDO::PARAM_INT);
        $sth->execute();
        $userresult = $sth->fetch(\PDO::FETCH_ASSOC);
      } catch(\PDOException $e) {
        $result['responseText'] .= DBError::showError($e, __CLASS__, $sql);
        $httpcode = 500;
        throw new \RuntimeException("<p>Databasfel! Kontakta systemadministratören om problemet består.</p>");
      }

      if (!password_verify($password . FIX_PWD_PEPPER, $userresult['pwd'])) {
        $httpcode = 401;
        throw new \RuntimeException("<p>Fel lösenord för användare " . $userresult['username'] . ".</p>");
      }

      if ($update === "password") {
        try {

          //Read and sanitize data from this form
          $newusername = filter_var(trim($_POST['newuser']), FILTER_SANITIZE_NUMBER_STRING);

          if (!empty($_POST['newpwd'])) {
            $newpassword1 = filter_var(trim($_POST['newpwd']), FILTER_UNSAFE_RAW);
            $newpassword2 = filter_var(trim($_POST['verifynewpwd']), FILTER_UNSAFE_RAW);

            if ($newpassword1 !== $newpassword2) {
              $httpcode = 400;
              throw new \RuntimeException("<p>Lösenorden matchar inte. Prova igen.</p>");
            }
          }

          if ($userresult['username'] === $newusername) {
            $username = $userresult['username'];
          } else {
            $username = $newusername;
          }

          $sql = "UPDATE " . TABLE_PREFIX . "logins SET
          username = :username";
          if (!empty($newpassword1)) {
          $sql .= ", pwd = :pwd";
          }
          $sql .= " WHERE id = :userid;";
          $sth = $pdo->prepare($sql);
          $sth->bindParam(':userid', $userid, \PDO::PARAM_INT);
          $sth->bindParam(':username', $username, \PDO::PARAM_STR);
          if (!empty($newpassword1)) {
            //hash password first
            $options = [
                'cost' => 10, //difficulty for password_hash
            ];
            $newpwd = password_hash($newpassword1 . FIX_PWD_PEPPER, PASSWORD_DEFAULT, $options);
            $sth->bindParam(':pwd', $newpwd, \PDO::PARAM_STR);
          }
          $sth->execute();
        } catch(\PDOException $e) {
          $result['responseText'] .= DBError::showError($e, __CLASS__, $sql);
          $httpcode = 500;
          throw new \RuntimeException("<p>Databasfel! Kontakta systemadministratören om problemet består.</p>");
        }


      } elseif ($update === "settings") {

      } else {
        $httpcode = 401;
        throw new \RuntimeException("<p>Felaktigt utförd begäran.</p>");
      }



    } catch(\RuntimeException $e) {
      $result['responseText'] .= $e->getMessage();
      echo json_encode($result);
      http_response_code($httpcode);
      exit;
    }


  }



}
