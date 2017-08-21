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
        $show_navigation = TRUE;
        $pageTitle = "Rekå Admin - Inställningar";
        header('Content-type: text/html; charset=utf-8');
        include __DIR__ . '/shared/header.php';

        $ptoken = root\includes\classes\Tokens::getFormToken("password",5400);
        $stoken = root\includes\classes\Tokens::getFormToken("settings",5400);
        $userid = $_SESSION['isloggedin'];

        $pdo = DB::get();

        try {
          $sql = "SELECT * FROM " . TABLE_PREFIX . "settings WHERE id = 1;";
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

        if ($stmpresult['mode'] === 'smtp') {
          $usesmtp = TRUE;
        } elseif ($stmpresult['mode'] === 'gmail') {
          $usegmail = TRUE;
        }


        echo "<form action='/adminajax/updatepassword' method='post' accept-charset='utf-8' class='settings-form' id='pwd-form'>
                <h2>Ändra användarnamn/lösenord</h2>
                <input type='hidden' name='tokenid' value='" . $ptoken['id'] . "' id='tokenid-password' />
                <input type='hidden' name='token' value='" . $ptoken['token'] . "' id='token-password' />
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
                  <input type='radio' name='mode' id='usesmtp' value='smtp' ";
                  if ($usesmtp) { echo " checked "; }
                  echo "/>
                  <label for='usesmtp'>Använd SMTP</label>
                <input type='hidden' name='tokenid' value='" . $stoken['id'] . "' id='tokenid-settings' />
                <input type='hidden' name='token' value='" . $stoken['token'] . "' id='token-settings' />
                <input type='hidden' name='userid' value='$userid' />
                <fieldset>
                  <label for='adminemail'>Administratörs e-mail:</label>
                  <input type='email' maxlength='200' name='adminemail' value='" . htmlspecialchars($stmpresult['email'], ENT_QUOTES) . "' />
                </fieldset>
                <fieldset>
                  <label for='smtpserver'>SMTP server:</label>
                  <input type='input' maxlength='200' name='smtpserver' value='" . htmlspecialchars($stmpresult['server'], ENT_QUOTES) . "' />
                </fieldset>
                <fieldset>
                  <label for='smtpport'>SMTP port:</label>
                  <input type='number' min='0' max='65535' name='smtpport' value='" . $stmpresult['port'] . "' />
                </fieldset>
                <fieldset>
                  <label for='stmpauth' id='smtp-auth-label'> Använd SMTP autentisering:</label>
                  <input type='checkbox' name='stmpauth'  id='smtp-auth-box' value='on' ";
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
                  <label for='smtpuser'>SMTP användarnamn:</label>
                  <input type='input' maxlength='200' name='smtpuser' value='" . htmlspecialchars($stmpresult['smtpuser'], ENT_QUOTES) . "' />
                </fieldset>
                <fieldset>
                  <label for='smtppwd'>SMTP lösenord:</label>
                  <input type='input' maxlength='200' name='smtppwd' value='" . htmlspecialchars($stmpresult['smtppwd'], ENT_QUOTES) . "' />
                </fieldset>
                <h2>Ändra Gmail API inställningar</h2>
                  <input type='radio' name='mode' id='usegmail' value='gmail' ";
                  if ($usegmail) { echo " checked "; }
                  echo "/>
                  <label for='usegmail'>Använd Gmail API</label>
                  <fieldset>
                    <label for='googleemail'>Google konto (e-mail):</label>
                    <input type='input' maxlength='200' name='googleemail' id='googleemail' value='" . htmlspecialchars($stmpresult['googleemail'], ENT_QUOTES) . "' />
                  </fieldset>
                <fieldset>
                  <label for='clientid'>ClientId:</label>
                  <input type='input' maxlength='200' name='clientid' id='clientid' value='" . htmlspecialchars($stmpresult['oauth_clientid'], ENT_QUOTES) . "' />
                </fieldset>
                <fieldset>
                  <label for='clientsecret'>ClientSecret:</label>
                  <input type='input' maxlength='200' name='clientsecret' id='clientsecret' value='" . htmlspecialchars($stmpresult['oauth_clientsecret'], ENT_QUOTES) . "' />
                </fieldset>
                <p>
                Authorize this site with Google by using the script at " . $_SERVER['HTTP_HOST'] . "/ignore/get_oauth_token.php<br>
                You will need to manually remove the exit line from the top of that file to get it to run.<br>
                Put the token from the script below.
                </p>
                <fieldset>
                  <label for='refreshtoken'>Refresh Token:</label>
                  <input type='input' maxlength='200' name='refreshtoken' id='refreshtoken' value='" . htmlspecialchars($stmpresult['oauth_refreshtoken'], ENT_QUOTES) . "' />
                </fieldset>
                <fieldset>
                  <label for='mainpwd'>Lösenord för inloggning på den här sidan:</label>
                  <input type='password' maxlength='250' name='mainpwd' id='mainpwd' required />
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
    $reply['responseText'] = "";


    try {

      if (empty($_POST)) {
        $httpcode = 401;
        throw new \RuntimeException("<p>Ingen data skickad.</p>");
      }

      $pdo = DB::get();
      //Check token

      if (!root\includes\classes\Tokens::checkFormToken(trim($_POST['token']),trim($_POST['tokenid']),$update)) {
        $httpcode = 401;
        throw new \RuntimeException("<p>Fel säkerhetstoken. Prova <a href='javascript:window.location.href=window.location.href'>ladda om</a> sidan.</p>");
      }

      //Read and sanitize common form variables
      $userid = filter_var(trim($_POST['userid']), FILTER_SANITIZE_NUMBER_INT);
      $password = filter_var(trim($_POST['mainpwd']), FILTER_UNSAFE_RAW);

      //Verify user password
      try {
        $sql = "SELECT * FROM " . TABLE_PREFIX . "logins WHERE id = :userid;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':userid', $userid, \PDO::PARAM_INT);
        $sth->execute();
        $userresult = $sth->fetch(\PDO::FETCH_ASSOC);
      } catch(\PDOException $e) {
        $reply['responseText'] .= DBError::showError($e, __CLASS__, $sql);
        $httpcode = 500;
        throw new \RuntimeException("<p>Databasfel! Kontakta systemadministratören om problemet består.</p>");
      }

      if (!password_verify($password . FIX_PWD_PEPPER, $userresult['pwd'])) {
        $httpcode = 401;
        throw new \RuntimeException("<p>Fel lösenord för användare " . $userresult['username'] . ".</p>");
      }

      if ($update === "password") {

          //Read and sanitize data from this form
          $newusername = filter_var(trim($_POST['newuser']), FILTER_SANITIZE_STRING);

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
        try {
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
          $reply['responseText'] .= DBError::showError($e, __CLASS__, $sql);
          $httpcode = 500;
          throw new \RuntimeException("<p>Databasfel! Kontakta systemadministratören om problemet består.</p>");
        }
        $reply['responseText'] .= "Lösenord/Användarnamn uppdaterat.";
        $httpcode = 200;

      } elseif ($update === "settings") {

        try {
          $sql = "SELECT * FROM " . TABLE_PREFIX . "settings WHERE id = 1;";
          $sth = $pdo->prepare($sql);
          $sth->execute();
          $result = $sth->fetch(\PDO::FETCH_ASSOC);

        } catch(\PDOException $e) {
          DBError::showError($e, __CLASS__, $sql);
          throw new \RuntimeException("<p>Databasfel! Kontakta systemadministratören om problemet består.</p>");
        }

        if (empty($result)) {
          try {
            $sql = "INSERT INTO " . TABLE_PREFIX . "settings (email) VALUES ('voidline');";
            $sth = $pdo->prepare($sql);
            $sth->execute();
            $result = $sth->fetch(\PDO::FETCH_ASSOC);

          } catch(\PDOException $e) {
            DBError::showError($e, __CLASS__, $sql);
            throw new \RuntimeException("<p>Databasfel! Kontakta systemadministratören om problemet består.</p>");
          }


        }



        //Read and sanitie form
        $email = filter_var(trim($_POST['adminemail']), FILTER_SANITIZE_EMAIL);
        $smtpserver = filter_var(trim($_POST['smtpserver']), FILTER_SANITIZE_URL);
        $smtpport = filter_var(trim($_POST['smtpport']), FILTER_SANITIZE_NUMBER_INT);
        if (!empty($_POST['stmpauth']) && trim($_POST['stmpauth']) === "on") {
          $stmpauth = 1;
        } else {
          $stmpauth = 0;
        }
        if (!empty($_POST['tls']) && trim($_POST['tls']) === "tls") {
          $smtptls = "tls";
        }
        elseif (!empty($_POST['tls']) && trim($_POST['tls']) === "ssl") {
          $smtptls = "ssl";
        }
        else {
          $smtptls = "no";
        }

        $smtpuser = filter_var(trim($_POST['smtpuser']), FILTER_SANITIZE_STRING);
        $smtppwd = filter_var(trim($_POST['smtppwd']), FILTER_UNSAFE_RAW);

        $gmailsec = filter_var(trim($_POST['clientsecret']), FILTER_UNSAFE_RAW);

        $gmailid = filter_var(trim($_POST['clientid']), FILTER_UNSAFE_RAW);

        $googleemail = filter_var(trim($_POST['googleemail']), FILTER_SANITIZE_EMAIL);

        $gmailtoken = filter_var(trim($_POST['refreshtoken']), FILTER_UNSAFE_RAW);

        $mailmode = filter_var(trim($_POST['mode']), FILTER_SANITIZE_STRING);

        if ($mailmode !== 'gmail' && $mailmode !== 'smtp') { $mailmode = 'invalid'; }

        try {
          $sql = "UPDATE " . TABLE_PREFIX . "settings SET
          email = :email,
          server = :server,
          port = :port,
          auth = :auth,
          tls = :tls,
          smtpuser = :user,
          smtppwd = :pwd,
          oauth_clientid = :clientid,
          oauth_clientsecret = :clientsecret,
          oauth_refreshtoken = :refreshtoken,
          googleemail = :googleemail,
          mode = :mode,
          oauth_initalized = 0
            WHERE id = 1;";
          $sth = $pdo->prepare($sql);
          $sth->bindParam(':email', $email, \PDO::PARAM_STR);
          $sth->bindParam(':server', $smtpserver, \PDO::PARAM_STR);
          $sth->bindParam(':port', $smtpport, \PDO::PARAM_INT);
          $sth->bindParam(':auth', $stmpauth, \PDO::PARAM_INT);
          $sth->bindParam(':tls', $smtptls, \PDO::PARAM_STR);
          $sth->bindParam(':user', $smtpuser, \PDO::PARAM_STR);
          $sth->bindParam(':pwd', $smtppwd, \PDO::PARAM_STR);
          $sth->bindParam(':clientid', $gmailid, \PDO::PARAM_STR);
          $sth->bindParam(':clientsecret', $gmailsec, \PDO::PARAM_STR);
          $sth->bindParam(':refreshtoken', $gmailtoken, \PDO::PARAM_STR);
          $sth->bindParam(':googleemail', $googleemail, \PDO::PARAM_STR);
          $sth->bindParam(':mode', $mailmode, \PDO::PARAM_STR);
          $sth->execute();
        } catch(\PDOException $e) {
          $reply['responseText'] .= DBError::showError($e, __CLASS__, $sql);
          $httpcode = 500;
          throw new \RuntimeException("<p>Databasfel! Kontakta systemadministratören om problemet består.</p>");
        }

        $reply['responseText'] .= "Inställningarna har uppdaterats.";
        $httpcode = 200;
      } else {
        $httpcode = 401;
        throw new \RuntimeException("<p>Felaktigt utförd begäran.</p>");
      }

      echo json_encode($reply);
      http_response_code($httpcode);

    } catch(\RuntimeException $e) {
      $reply['responseText'] .= $e->getMessage();
      echo $reply['responseText'];
      http_response_code($httpcode);
      exit;
    }


  }



}
