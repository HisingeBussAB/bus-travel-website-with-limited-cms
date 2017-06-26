<?php

date_default_timezone_set ('Europe/Stockholm');

define('DB_NAME', 'database name');
define('DB_USER', 'database username');
define('DB_PASSWORD', 'database password');
define('DB_HOST', 'localhost');


define('ALLOWED_HTML_TAGS', '<b><i><u><p><s><blockquote><q><br><h1><h2><h3><h4><h5><h6><del><sub><sup><style><small><big><pre><hr>'); //Tags allowed in content posts

/*
Default login and password can be changed in runonce.php if not it is
User: admin
Pwd:  12345

SMTP settings will be queries and saved in db on running /installme
Delete install folder from live setup after initializing site for extra security.
*/

define('FIX_PWD_PEPPER',         'pwdpepper'); //pepper for passwords
define('JWT_KEY_PEPPER',         'jwtpepper'); //pepper for jwt keys
define('JWT_STAMP',              'jwtstamp'); //watermark for jwts


define('RECAPTCHA_PUBLIC', 'google recapta public key');
define('RECAPTCHA_SECRET', 'google recapta private key');

define('TABLE_PREFIX', 'prefix_');

define('LOGIN_IP_LOCK', true);

define('DEBUG_MODE', true);

if (empty($_SERVER['HTTPS'])) {
  define('HTTPS',              false);
  define('APPEND_SSL',         '');
} else {
  define('HTTPS',              true);
  define('APPEND_SSL',         's');
}
