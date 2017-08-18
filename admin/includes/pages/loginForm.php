<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

use HisingeBussAB\RekoResor\website as root;

$token = root\includes\classes\Tokens::getFormToken("login",1000);

?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta http-equiv="content-type" content="text/html;charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="author" content="Håkan K Arnoldson">
  <title>Rekå Resor - Administration</title>

  <meta name="robots" content="noindex, nofollow">

  <link rel="icon" href="/favicon/favicon.ico">

  <link rel="stylesheet" href="/dependencies/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
  <link rel="stylesheet" href="/admin/css/admin.min.css" />

  <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
  <script src="/dependencies/jquery-3.1.1/jquery-3.1.1.min.js"></script>
  <script src="/admin/js/login.js"></script>

  <img class="login-screen" src="/img/logo.gif" alt="Rekå Resor AB">

  <form action="/ajax/admindologin" method="post" accept-charset="utf-8" id="login-form">
    <input type="hidden" name="tokenid" id="tokenid" value="<?php echo $token['id'] ?>">
    <input type="hidden" name="token" id="token" value="<?php echo $token['token'] ?>">
    <fieldset>
      <label for="user">Användarnamn:</label>
      <input type="input" name="user" id="login-user" required>
    </fieldset>
    <fieldset>
      <label for="pwd">Lösenord:</label>
      <input type="password" name="pwd" id="loginpwd" required>
    </fieldset>
    <fieldset>
      <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_PUBLIC ?>"></div>
    </fieldset>
    <fieldset>
      <button type="submit" id="login-submit">Logga in</button>
    </fieldset>
    <fieldset>
      <p id="login-message"></p>
    </fieldset>
  </form>
  <h6 class="text-center">
    <a href="/adminp/resetpw/requestnew">Återställ lösenord</a>
  </h6>
</body>
</html>
