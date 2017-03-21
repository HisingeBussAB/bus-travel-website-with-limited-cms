<?php
define('DB_NAME', 'db name');
define('DB_USER', 'db user');
define('DB_PASSWORD', 'db password');
define('DB_HOST', 'localhost');


define('LOGGED_IN_USER_PEPPER',    'salt');
define('FIXED_TRACKER_TOKEN',      'salt');

define('FIX_PWD_PEPPER',           'salt');

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
