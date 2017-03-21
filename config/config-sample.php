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

define('HTTPS', false); //set false for http:// true for https://

define('TABLE_PREFIX', 'site17test_');

if (HTTPS == true) {
  define('APPEND_SSL', 's');
} else {
  define('APPEND_SSL', '');
}
