<?php
//ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//ERRORS

$uri = explode($_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI'], 2);
$path = $uri[0];
$path = filter_var ($path, FILTER_SANITIZE_URL);
$path = trim($path, '/');

list($page, $id) = array_pad(explode('/', $path, 2), 2, '');

$uris = array(
 'admin' => __DIR__ . '/admin-cp/admin.php',
 'resa' => __DIR__ . '/includes/pages/showtrip.php',
 'galleri' => __DIR__ . '/includes/pages/showgallery.php',
 'kategori' => __DIR__ . '/includes/pages/showcategory.php',
 '' => __DIR__ . '/includes/pages/mainpage.php',
 'bestallkatalog' => __DIR__ . '/includes/pages/showorderinfo.php',
 'inforresan' => __DIR__ . '/includes/pages/showbeforetrip.php',
 'bussresorgoteborg' => __DIR__ . '/includes/pages/showabout.php',
 'kontaktarekaresor' => __DIR__ . '/includes/pages/showcontact.php',
 'api' => __DIR__ . '/api/api.php'
);

if ($page == 'admin' || $page == 'api') {
  require_once $uris[$page];
} else {
  if(!in_array($page, array_keys($uris))){
    require_once __DIR__ . '/404.php';
 } else {
    require_once __DIR__ . '/includes/header.php';
    require_once $uris[$page];
    require_once __DIR__ . '/includes/footer.php';
  }
}
?>
