<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * (c) Rekå Resor AB
 *
 * @link      https://github.com/HisingeBussAB/bus-travel-website-with-limited-cms
 * @copyright CC BY-SA 4.0 (http://creativecommons.org/licenses/by-sa/4.0/)
 * @license   GNU General Public License v3.0
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website;

//ERRORS FIXME
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//ERRORS


date_default_timezone_set ('Europe/Stockholm');
require __DIR__ . '/config/config.php';

spl_autoload_register(function($class){
  $class = str_replace(__NAMESPACE__, '', $class);
  $class = strtolower($class);
  if(file_exists($file = __DIR__ . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php')) require $file;
  var_dump($file);
});

includes\classes\Sessions::secSessionStart();

$router = new includes\Router();
$router->route();

includes\classes\HammerGuard::hammerGuard('88.88.88.88');
