<?php
require __DIR__ . '/../includes/classes/db.php';
require __DIR__ . '/../includes/classes/dberror.php';
require __DIR__ . '/../includes/classes/sessions.php';

use HisingeBussAB\RekoResor\website\includes\classes\DB as DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError as DBError;
use HisingeBussAB\RekoResor\website\includes\classes\Sessions as Sessions;
use HisingeBussAB\RekoResor\website\admin\includes\classes\Login as Login;

echo "<html><head><meta http-equiv='content-type' content='text/html;charset=utf-8'><title>Installera</title></head><body>
      <script src='https://www.google.com/recaptcha/api.js'></script>";


$firstinstall = NULL;
$pdo = DB::get();
try {
  $sql = "SELECT count(*) FROM " . TABLE_PREFIX . "logins;";
  $sth = $pdo->prepare($sql);
  $sth->execute();
  $count = $sth->fetch(\PDO::FETCH_NUM); // Return array indexed by column number
  $thecount = reset($count); // Resets array cursor and returns first value (the count)

  if ($thecount > 0) {
    echo "<br>Sidan är redan installerad.<br>Du kan uppdatera SMTP inställningar nedan:<br><br>";
    $firstinstall = FALSE;
  }
} catch(\PDOException $e) {
  $firstinstall = TRUE;
  echo "<br>Installation!<br>Skriv in SMTP inställningar nedan:<br><br>";
}

Sessions::secSessionStart();

$loggedin = FALSE;
if (empty($_POST['user']) && !$firstinstall) {
  $loggedin = Login::isLoggedIn();
}
if (!$loggedin && $_SERVER['REQUEST_METHOD'] === 'POST' && !$firstinstall) {
  if (!Login::setLogin()) {
    echo "<br><br>Inloggning misslyckades! <br>

          <a href='/installme'>Tillbaka!</a></body></html>";
    exit;
  } else {
    $loggedin = TRUE;
  }
}


$options = [
    'cost' => 10, //difficulty for password_hash
];

$default_pwd_plain = "12345";
$default_login = filter_var(trim("admin"), FILTER_SANITIZE_STRING);
$default_pwd = password_hash($default_pwd_plain . FIX_PWD_PEPPER, PASSWORD_DEFAULT, $options);


$email = "";
$server = "";
$port = "";
$auth = FALSE;
$smtpuser = "";
$smtppwd = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
  $email = filter_var(trim($_POST['adminemail']), FILTER_SANITIZE_EMAIL);
  $server = filter_var(trim($_POST['smtpserver']), FILTER_SANITIZE_URL);
  $port = filter_var(trim($_POST['smtpport']), FILTER_SANITIZE_NUMBER_INT);
  if (!empty($_POST['smtpuseauth']) && trim($_POST['smtpuseauth']) === "on") {
    $auth = TRUE;
    }
  $smtpuser = filter_var(trim($_POST['smtpuser']), FILTER_SANITIZE_STRING);
  $smtppwd = filter_var(trim($_POST['smtppwd']), FILTER_UNSAFE_RAW);

  if ($_SESSION['token'] !== trim($_POST['token'])) {
    echo "Fel säkerhetstoken! <br>

          <a href='/installme'>Tillbaka!</a></body></html>";
    exit;
    }

}
$token = bin2hex(openssl_random_pseudo_bytes(64));
$_SESSION['token'] = $token;

echo "
      <form action='/installme' method='POST' enctype='multipart/form-data' accept-charset='utf-8'>
      <input type='hidden' name='token' value='$token' />
      <table>
      <tr><td>Administratörens e-post:</td><td><input type='email' name='adminemail' maxlength='200' placeholder='info@domain.com' value='$email' /></td></tr>
      <tr><td>SMTP server:</td><td><input type='text' name='smtpserver' maxlength='200' placeholder='smtp@domain.com' value='$server' /></td></tr>
      <tr><td>SMTP port:</td><td><input type='number' name='smtpport' placeholder='25' min='0' max='65535' value='$port' /></td></tr>
      <tr><td>SMTP använd authentisering:</td><td><input type='checkbox' value='on' name='smtpuseauth' ";
if ($auth) { echo "checked "; }
echo " /></td></tr>
      <tr><td>SMTP användarnamn:</td><td><input type='text' name='smtpuser' maxlength='200' placeholder='smtp@domain.com' value='$smtpuser' /></td></tr>
      <tr><td>SMTP lösenord:</td><td><input type='text' name='smtppwd' maxlength='200' placeholder='' value='$smtppwd' /></td></tr>";

if ($firstinstall === FALSE && !$loggedin) {
  echo "
      <tr><td colspan=2>&nbsp;</td></tr>
      <tr><td colspan=2>Dina inloggningsuppgifter till admin på den här hemsidan:</td></tr>
      <tr><td>Admin användarnamn:</td><td><input type='text' name='user' placeholder='' value='' /></td></tr>
      <tr><td>Admin lösenord:</td><td><input type='password' name='pwd' placeholder='' value='' /></td></tr>
      <tr><td colspan=2><div class='g-recaptcha' data-sitekey='" . RECAPTCHA_PUBLIC . "'></div></td></tr>
      <tr><td colspan=2>Behöver du återställa lösenord gå till <a href='/adminp'>admin inloggning</a></td></tr>";
}
echo "
      <tr><td></td><td><button type='submit'>Spara inställningar</button></p></td></tr>
      </table>
      </form>";
$auth = intval($auth);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST) && $firstinstall) {
  echo "<p>Installing...</p>";
  require "runonce.php";

  try {
    $sql = "INSERT INTO " . TABLE_PREFIX . "settings (
      email,
      server,
      port,
      auth,
      smtpuser,
      smtppwd
    ) VALUES (
      :email,
      :server,
      :port,
      :auth,
      :smtpuser,
      :smtppwd
    );";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':email', $email, \PDO::PARAM_STR);
    $sth->bindParam(':server', $server, \PDO::PARAM_STR);
    $sth->bindParam(':port', $port, \PDO::PARAM_INT);
    $sth->bindParam(':auth',  $auth, \PDO::PARAM_INT);
    $sth->bindParam(':smtpuser', $smtpuser, \PDO::PARAM_STR);
    $sth->bindParam(':smtppwd', $smtppwd, \PDO::PARAM_STR);

    $sth->execute();
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    echo "<br>Databasfel!<br>";
    exit;
  }
  echo "<p>SMTP inställningar sparade.</p>";
  echo "<p>Allt klart! <a href='/adminp'>Till adminpanelen</a></p>";


}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST) && !$firstinstall && $loggedin) {
  echo "<p>Updating...</p>";
  try {
    $sql = "UPDATE " . TABLE_PREFIX . "settings SET
      email = :email,
      server = :server,
      port = :port,
      auth = :auth,
      smtpuser = :smtpuser,
      smtppwd = :smtppwd
    WHERE id = 0;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':email', $email, \PDO::PARAM_STR);
    $sth->bindParam(':server', $server, \PDO::PARAM_STR);
    $sth->bindParam(':port', $port, \PDO::PARAM_INT);
    $sth->bindParam(':auth',  $auth, \PDO::PARAM_INT);
    $sth->bindParam(':smtpuser', $smtpuser, \PDO::PARAM_STR);
    $sth->bindParam(':smtppwd', $smtppwd, \PDO::PARAM_STR);

    $sth->execute();
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    echo "<br>Databasfel!<br>";
    exit;
  }
  echo "<p>SMTP inställningar sparade.</p>";
  echo "<p>Allt klart! <a href='/adminp'>Till adminpanelen</a></p>";

}

echo "</body></html>";
