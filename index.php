<?php
//ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//ERRORS

$uri = explode('busspoolen.se', $_SERVER['REQUEST_URI'], 2);
$path = $uri[0];
$path = filter_var ($path, FILTER_SANITIZE_URL);
$path = trim($path, '/');

list($page, $id) = explode('/', $path, 2);

$uris = array(
 'admin' => __DIR__ . '/visaadminpanelen.php',
 'resa' => __DIR__ . '/visaresa.php',
 'galleri' => __DIR__ . '/visabildgalleri.php'
);

if(!in_array($page, array_keys($uris))){
   require_once __DIR__ . '/404.php';
} else {
 require_once __DIR__ . $uris[$page];
 echo "I am here";
}

?>
