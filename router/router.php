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



    //                Pattern with (arguments)               function to run on matchRoute                                                    METHOD
    $this->addRoute(  '/(^$)/',                              '\HisingeBussAB\RekoResor\website\router\Render::inc',                          'GET');
    $this->addRoute(  '/^adminp$/',                          '\HisingeBussAB\RekoResor\website\admin\includes\pages\Main::showAdminMain',    'GET');
    $this->addRoute(  '/^adminp\/nyresa\/?([\w-%]+)?\/?$/',  '\HisingeBussAB\RekoResor\website\admin\includes\pages\Trip::showTrip',         'GET');
    $this->addRoute(  '/^adminp\/kategori\/?([\w-%]+)?\/?$/','\HisingeBussAB\RekoResor\website\admin\includes\pages\Category::showCategory', 'GET');
    $this->addRoute(  '/^adminp\/resetpw\/?([\w-%]+)?\/?$/', '\HisingeBussAB\RekoResor\website\admin\includes\pages\PWReset::doReset',       'ANY');
    $this->addRoute(  '/^adminp\/settings$/',                '\HisingeBussAB\RekoResor\website\admin\includes\pages\Settings::show',         'GET');
    $this->addRoute(  '/^adminp\/logout$/',                  '\HisingeBussAB\RekoResor\website\admin\includes\classes\Logout::doLogout',     'GET');
    $this->addRoute(  '/^adminp\/filemanager\/upload$/',     '\HisingeBussAB\RekoResor\website\admin\includes\classes\Files::uploadFile',    'POST');
    $this->addRoute(  '/^resa\/([\w-%]+)\/?$/',              '\HisingeBussAB\RekoResor\website\router\Render::tour',                         'GET');
    $this->addRoute(  '/^boka\/([\w-%]+)\/?$/',              '\HisingeBussAB\RekoResor\website\router\Render::booktour',                     'GET');
    $this->addRoute(  '/^program\/([\w-%]+)\/?$/',           '\HisingeBussAB\RekoResor\website\router\Render::ordertourprogram',             'GET');
    $this->addRoute(  '/^galleri\/([\w-%]+)\/?$/',           '\HisingeBussAB\RekoResor\website\router\Render::inc',                          'GET');
    $this->addRoute(  '/^kategori\/([\w-%]+)\/?$/',          '\HisingeBussAB\RekoResor\website\router\Render::category',                     'GET');
    $this->addRoute(  '/^(bestallkatalog)$/',                '\HisingeBussAB\RekoResor\website\router\Render::inc',                          'GET');
    $this->addRoute(  '/^(inforresan)$/',                    '\HisingeBussAB\RekoResor\website\router\Render::inc',                          'GET');
    $this->addRoute(  '/^(bussresorgoteborg)$/',             '\HisingeBussAB\RekoResor\website\router\Render::inc',                          'GET');
    $this->addRoute(  '/^(kontaktarekaresor)$/',             '\HisingeBussAB\RekoResor\website\router\Render::inc',                          'GET');
    $this->addRoute(  '/^ajax\/([\w-%]+)$/',                 '\HisingeBussAB\RekoResor\website\ajax\Ajax::startAjax',                        'POST');
    $this->addRoute(  '/^adminajax\/([\w-%]+)$/',            '\HisingeBussAB\RekoResor\website\ajax\AdminAjax::startAjax',                   'POST');

    //XOAuth2
    $this->addRoute(  '/^authenticatexoauth2WVeAebVuKl$/',$func = function() {if (!include __DIR__ . '/../dependencies/vendor/phpmailer/get_oauth_token.php'){require __DIR__ . '/../includes/pages/error/404.php';} },'ANY');

    //INSTALL ROUTE
    $this->addRoute(  '/^installme$/',$func = function() {if (!include __DIR__ . '/../install/install.php'){require __DIR__ . '/../includes/pages/error/404.php';} },'ANY');

    //TEST ROUTES
    $this->addRoute(  '/^adminp\/testfiles$/',  '\HisingeBussAB\RekoResor\website\admin\includes\pages\TestFiles::start',                   'GET');
  }
}
