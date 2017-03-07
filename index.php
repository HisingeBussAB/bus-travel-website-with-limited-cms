<?php
//ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//ERRORS
require_once __DIR__ . '/includes/db_connect.php';

$uri = explode(DOMAIN, $_SERVER['REQUEST_URI'], 2);
$path = $uri[0];
$path = filter_var ($path, FILTER_SANITIZE_URL);
$path = trim($path, '/');

list($page, $id) = array_pad(explode('/', $path, 2), 2, '');

$uris = array(
 'admin' => __DIR__ . '/admin-cp/login.php',
 'resa' => __DIR__ . '/showtrip.php',
 'galleri' => __DIR__ . '/showgallery.php',
 'kategori' => __DIR__ . '/showcategory.php',
 '' => __DIR__ . '/showmain.php'
);

if(!in_array($page, array_keys($uris))){
   require_once __DIR__ . '/404.php';
} else {
  require_once __DIR__ . '/includes/header.php';
  require_once $uris[$page];
  require_once __DIR__ . '/includes/footer.php';
}

?>
