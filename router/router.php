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

  public function addRoute($pattern, $target, $targetarg, $method='ANY') {
    $obj = new Map;
    $obj->mapRoute($pattern, $target, $targetarg, $method);
    array_push($this->routes,$obj);
  }

  public function go() {
    $method = $_SERVER['REQUEST_METHOD'];

    $uri = explode($_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI'], 2);
    $path = $uri[0];
    $path = filter_var($path, FILTER_SANITIZE_URL);
    $path = trim($path, '/');


    //@todo TODO check this, catch errors, show 404.php
    foreach($this->routes as $route) {
      if ($route->matchRoute($path, $method)) {
        call_user_func($route->getTarget(), $route->getTargetarg());
      }
    }

  }

  public function __construct() {
    $this->routes = [];

    //DEFAULT START MAP FOR GET REQUESTS
    $mapget = [
      '/^$/'                  => "\HisingeBussAB\RekoResor\website\\router\Render::inc('/includes/pages/mainpage.php')",
      '/^admin\S*/'           => "\HisingeBussAB\RekoResor\website\admincp\Admin::startAdmin()",
      '/^resa\S*/'            => "render\Render::inc('NOTHING')",
      '/^galleri\S*/'         => "render\Render::inc('NOTHING')",
      '/^kategori\S*$/'        => "render\Render::inc('NOTHING')",
      '/^bestallkatalog$/'    => "render\Render::inc('NOTHING')",
      '/^inforresan$/'        => "render\Render::inc('NOTHING')",
      '/^bussresorgoteborg$/' => "render\Render::inc('NOTHING')",
      '/^kontaktarekaresor$/' => "render\Render::inc('NOTHING')",
    ];

    //DEFAULT START MAP FOR POST REQUESTS
    $mappost = [
      '/^ajax\/\S*/'          => "render\Render::inc('NOTHING')", //ajax requests need to have a subtarget
    ];

    foreach($mapget as $pattern => $route) {
      $this->addRoute($pattern, $this->getMethod($route), $this->getArgument($route), 'GET');
    }
    foreach($mappost as $pattern => $route) {
      $this->addRoute($pattern, $this->getMethod($route), $this->getArgument($route), 'POST');
    }
  }


  private function getArgument($string) {
    $arg = substr($string, strpos($string, '('));
    $arg = trim($arg, '()');
    $arg = trim($arg, '\'');
    return $arg;
  }

  private function getMethod($string) {
    $method = explode("(", $string, 2);
    $method = $method[0];
    return $method;
  }


  /*
  public function route() {



    $uri = explode($_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI'], 2);
    $path = $uri[0];
    $path = filter_var($path, FILTER_SANITIZE_URL);
    $path = trim($path, '/');

    $map = Map::gMap();

    preg_match($map, $path);


    return $map;


    //$m = new Map();
    //$m->construct();


/*
    if (array_key_exists($section, $map)) {
      echo '<br>array_key_exists($section, $map)<br><br>';
      var_dump($map[$section]);

      if (array_key_exists($page, $map[$section])) {
        echo '<br><br>array_key_exists($section[$page], $map)<br>';
      }


      } else {
        include __DIR__ . '/../includes/pages/error/404.php';
      }

  }
  */
}
