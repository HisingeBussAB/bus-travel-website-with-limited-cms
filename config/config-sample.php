<?php
date_default_timezone_set ('Europe/Stockholm');


//TEMP
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_HOST', '');
//


define('RECAPTCHA_PUBLIC', '');
define('RECAPTCHA_SECRET', '');
define('INV_RECAPTCHA_PUBLIC', '');
define('INV_RECAPTCHA_SECRET', '');

define('GMAPS_API_KEY', '');

if ($_SERVER['HTTP_HOST'] == 'rekodev.busspoolen.se' || $_SERVER['HTTP_HOST'] == 'www.rekodev.busspoolen.se') {
  error_reporting(-1);
  ini_set('error_reporting', E_ALL);
  ini_set("display_errors", 1);

  define('DB_NAME', '');
  define('DB_USER', '');
  define('DB_PASSWORD', '');
  define('DB_HOST', '');


  define('DOMAIN', 'rekodev.busspoolen.se');
  define('DEBUG_MODE', true);

} else {
  //WWW.REKORESOR.SE
  error_reporting(0);
  ini_set("display_errors", 0);
  define('DB_NAME', '');
  define('DB_USER', '');
  define('DB_PASSWORD', '');
  define('DB_HOST', 'localhost');

  define('DOMAIN', 'www.rekoresor.se');
  define('DEBUG_MODE', false);
}



define('ALLOWED_HTML_TAGS', '<b><i><u><p><s><blockquote><q><br><h1><h2><h3><h4><h5><h6><del><sub><sup><style><small><big><pre><hr><li><ul><iframe>');


define('FIX_PWD_PEPPER',         ''); //pepper for passwords
define('JWT_KEY_PEPPER',         ''); //pepper for jwt keys
define('JWT_STAMP',              ''); //watermark for jwts



define('TABLE_PREFIX', 'site17test_');

define('LOGIN_IP_LOCK', true);


if (empty($_SERVER['HTTPS'])) {
  define('HTTPS',              false);
  define('APPEND_SSL',         '');
} else {
  define('HTTPS',              true);
  define('APPEND_SSL',         's');
}
