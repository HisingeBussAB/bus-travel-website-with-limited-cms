<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * (c) Rekå Resor AB
 *
 * @link      https://github.com/HisingeBussAB/bus-travel-website-with-limited-cms
 * @copyright CC BY-SA 4.0 (http://creativecommons.org/licenses/by-sa/4.0/) or respective owners
 * @license   GNU General Public License v3.0
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website;
use HisingeBussAB\RekoResor\website\router\Router;


ini_set("default_charset", "UTF-8");
require __DIR__ . '/config/config.php';

//ERRORS
if (DEBUG_MODE) {
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
}

//START AUTOLOADER
spl_autoload_register(function($class){
  $class = strtolower(str_replace(__NAMESPACE__, '', $class));
  if(file_exists($file = __DIR__ . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php')) require $file;
});

//ROUTE REQUEST
$router = new Router;
$router->go();
