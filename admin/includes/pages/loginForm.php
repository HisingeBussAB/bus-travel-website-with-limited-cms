<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

$token = bin2hex(openssl_random_pseudo_bytes(64));
$_SESSION['token'] = $token;

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

  <link rel="stylesheet" href="/admin/css/admin.min.css" />

  <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
  <script src="/dependencies/jquery-3.1.1/jquery-3.1.1.min.js"></script>
  <script src="/admin/js/login.js"></script>

  <img class="login-screen" src="/img/logo.gif" alt="Rekå Resor AB">

  <form action="/ajax/admindologin" method="post" accept-charset="utf-8" id="login-form">
    <input type="hidden" name="token" value="<?php echo $token ?>">
    <fieldset>
      <label for="user">Användarnamn:</label>
      <input type="input" name="user" id="login-user">
    </fieldset>
    <fieldset>
      <label for="pwd">Lösenord:</label>
      <input type="password" name="pwd" id="loginpwd">
    </fieldset>
    <fieldset>
      <input type="submit" value="Logga in" id="login-submit">
    </fieldset>
    <fieldset>
      <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_PUBLIC ?>"></div>
    </fieldset>
    <fieldset>
      <p id="login-message"></p>
    </fieldset>
  </form>
</body>
</html>
