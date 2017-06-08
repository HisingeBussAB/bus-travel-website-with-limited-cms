<?php

date_default_timezone_set ('Europe/Stockholm');

define('DB_NAME',     'db name');
define('DB_USER',     'db user');
define('DB_PASSWORD', 'db password');
define('DB_HOST',      'localhost');

define('DEFAULT_ADMIN_USER',  'admin');  //default admin username to use at setup, can be commented out after setup
define('DEFAULT_ADMIN_PWD',   '12345');  //default admin username to use at setup, can be commented out after setup

define('ALLOWED_HTML_TAGS', '<b><i><u><p><s><blockquote><q><br><h1><h2><h3><h4><h5><h6><del><sub><sup><style><small><big><pre><hr>');

define('FIX_PWD_PEPPER',         'pepper');
define('JWT_KEY_PEPPER',         'pepper');

define('DEFAULT_ADMIN_USER',  'admin');
define('DEFAULT_ADMIN_PWD',   '12345');

define('RECAPTCHA_PUBLIC', 'public recaptha key from Google');
define('RECAPTCHA_SECRET', 'private recaptcha key frpm Goole');

define('TABLE_PREFIX', 's17test_');

define('DEBUG_MODE', false);

if (empty($_SERVER['HTTPS'])) {
  define('HTTPS',              true);
  define('APPEND_SSL',         '');
} else {
  define('HTTPS',              false);
  define('APPEND_SSL',         's');
}
