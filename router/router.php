<?php
/**
 * Rekå Resor (www.rekoresor.se)
 *
 * @author    Håkan Arnoldson
 * @link      https://github.com/HisingeBussAB/bus-travel-website-with-limited-cms
 */

namespace HisingeBussAB\RekoResor\website\router;

/**
 * Router class
 */
class Router
{
  /**
   * @var array $routes Array of initated Map objects
   */
  private $routes;

  public function addRoute($pattern, $target, $method='ANY') {
    $obj = new Map;
    $obj->mapRoute($pattern, $target, $method);
    array_push($this->routes,$obj);
  }

  public function go() {
    $method = $_SERVER['REQUEST_METHOD'];

    $uri = explode($_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI'], 2);
    $path = $uri[0];
    $path = filter_var($path, FILTER_SANITIZE_URL);
    $path = trim($path, '/');

    $flag = false;
    foreach($this->routes as $route) {
      $result = $route->matchRoute($path, $method);
      if ($result !== false) {
        call_user_func_array($route->getTarget(), $result);
        $flag = true;
      }
    }
    if ($flag === false) require __DIR__ . '/../includes/pages/error/404.php';

  }

  public function __construct() {
    $this->routes = [];

    //DEFAULT START MAP FOR GET REQUESTS
    $mapget = [
      '/(^$)/'                    => "\HisingeBussAB\RekoResor\website\\router\Render::inc",
      '/^(admin)\/?([\w-]+)?\/?$/'=> "\HisingeBussAB\RekoResor\website\admincp\Admin::startAdmin",
      '/^resa\/([\w-]+)\/?$/'     => "\HisingeBussAB\RekoResor\website\\router\Render::inc",
      '/^galleri\/([\w-]+)\/?$/'  => "\HisingeBussAB\RekoResor\website\\router\Render::inc",
      '/^kategori\/([\w-]+)\/?$/' => "\HisingeBussAB\RekoResor\website\\router\Render::inc",
      '/^(bestallkatalog)$/'      => "\HisingeBussAB\RekoResor\website\\router\Render::inc",
      '/^(inforresan)$/'          => "\HisingeBussAB\RekoResor\website\\router\Render::inc",
      '/^(bussresorgoteborg)$/'   => "\HisingeBussAB\RekoResor\website\\router\Render::inc",
      '/^(kontaktarekaresor)$/'   => "\HisingeBussAB\RekoResor\website\\router\Render::inc",
    ];

    //DEFAULT START MAP FOR POST REQUESTS
    $mappost = [
      '/^ajax\/([\w-]+)\/?$/'          => "\HisingeBussAB\RekoResor\website\ajax\Ajax::startAjax", //ajax requests need to have a subtarget
    ];

    foreach($mapget as $pattern => $route) {
      $this->addRoute($pattern, $route, 'GET');
    }
    foreach($mappost as $pattern => $route) {
      $this->addRoute($pattern, $route, 'POST');
    }
  }
}
