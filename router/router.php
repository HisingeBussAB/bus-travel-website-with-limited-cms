<?php
/**
 * Rekå Resor (www.rekoresor.se)
 *
 * @author    Håkan Arnoldson
 * @link      https://github.com/HisingeBussAB/bus-travel-website-with-limited-cms
 */

namespace HisingeBussAB\RekoResor\website\router;
use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\router\Render as Render;

/**
 * Router class
 */
class Router
{
  /**
   * @var array $routes Array of initated Map objects
   */
  private $routes;

  private $sitemap;

  public function addRoute($pattern, $target, $method='ANY') {
    $obj = new Map;
    $obj->mapRoute($pattern, $target, $method);
    array_push($this->routes,$obj);
  }

  public function go() {
    $method = $_SERVER['REQUEST_METHOD'];

    $request = explode($_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI'], 2);

    $path = explode('?', $request[0], 2);

    $path = $path[0];


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

    $this->sitemap = [];



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
    $this->addRoute(  '/^(boka)$/',                          '\HisingeBussAB\RekoResor\website\router\Render::booktour',                     'GET');
    array_push($this->sitemap, "boka");
    $this->addRoute(  '/^program\/([\w-%]+)\/?$/',           '\HisingeBussAB\RekoResor\website\router\Render::ordertourprogram',             'GET');
    $this->addRoute(  '/^galleri\/([\w-%]+)\/?$/',           '\HisingeBussAB\RekoResor\website\router\Render::inc',                          'GET');
    $this->addRoute(  '/^(galleri)$/',                       '\HisingeBussAB\RekoResor\website\router\Render::inc',                          'GET');
    array_push($this->sitemap, "galleri");
    $this->addRoute(  '/^kategori\/([\w-%]+)\/?$/',          '\HisingeBussAB\RekoResor\website\router\Render::category',                     'GET');
    $this->addRoute(  '/^(bestall-program)$/',               '\HisingeBussAB\RekoResor\website\router\Render::inc',                          'GET');
    array_push($this->sitemap, "bestall-program");
    $this->addRoute(  '/^(inforresan)$/',                    '\HisingeBussAB\RekoResor\website\router\Render::inc',                          'GET');
    array_push($this->sitemap, "inforresan");
    $this->addRoute(  '/^(efterresan)$/',                    '\HisingeBussAB\RekoResor\website\router\Render::inc',                          'GET');
    array_push($this->sitemap, "efterresan");
    $this->addRoute(  '/^(bussresorgoteborg)$/',             '\HisingeBussAB\RekoResor\website\router\Render::inc',                          'GET');
    array_push($this->sitemap, "bussresorgoteborg");
    $this->addRoute(  '/^(kontakt)$/',                       '\HisingeBussAB\RekoResor\website\router\Render::inc',                          'GET');
    array_push($this->sitemap, "kontakt");
    $this->addRoute(  '/^(resevillkor)$/',                   '\HisingeBussAB\RekoResor\website\router\Render::inc',                          'GET');
    array_push($this->sitemap, "resevillkor");
    $this->addRoute(  '/^(facebooktab)$/',                   '\HisingeBussAB\RekoResor\website\router\Render::inc',                          'ANY');
    array_push($this->sitemap, "facebooktab");


    //SEO landing pages
    $this->addRoute(  '/^(pensionarsresor)$/',                        $func = function() { Render::landing(1); },                     'GET');
    array_push($this->sitemap, "pensionarsresor");
    $this->addRoute(  '/^(pensionarsresor-med-buss)$/',               $func = function() { Render::landing(2); },                     'GET');
    array_push($this->sitemap, "pensionarsresor-med-buss");
    $this->addRoute(  '/^(bussresor-goteborg-2)$/',                   $func = function() { Render::landing(3); },                     'GET');
    array_push($this->sitemap, "bussresor-goteborg-2");

    //Campaigns
    $this->addRoute(  '/^(julbord)$/',                        '\HisingeBussAB\RekoResor\website\router\Render::inc',                     'GET');
    array_push($this->sitemap, "julbord");

    //Admin and middleware
    $this->addRoute(  '/^ajax\/([\w-%]+)$/',                 '\HisingeBussAB\RekoResor\website\ajax\Ajax::startAjax',                        'POST');
    $this->addRoute(  '/^adminajax\/([\w-%]+)$/',            '\HisingeBussAB\RekoResor\website\ajax\AdminAjax::startAjax',                   'POST');

    //Sitemap robots, data exports
    $this->addRoute(  '/^robots.txt$/',                      $func = function() {if (!include __DIR__ . '/../render-robots.php')  { require __DIR__ . '/../includes/pages/error/404.php'; } },  'GET');
    $this->addRoute(  '/^sitemap.xml$/',                     $func = function() { root\RenderSitemap::render($this->sitemap); },                        'GET');
    $this->addRoute(  '/^feed\/get-destinations.xml$/',      $func = function() { root\RenderDestCatalog::render(); },                                  'GET');
    $this->addRoute(  '/^feed\/get-products.xml$/',          $func = function() { root\RenderProdCatalog::render(); },                                  'GET');


  $this->addRoute(  '/^api\/resor$/',                            $func = function() { root\Api::resor(); },         'GET');
    $this->addRoute(  '/^api\/boenden$/',                            $func = function() { root\Api::boenden(); },                                               'GET');
    $this->addRoute(  '/^api\/kategorier$/',                         $func = function() { root\Api::kategorier(); },                                               'GET');

    //INSTALL ROUTE
    $this->addRoute(  '/^installme$/',$func = function() {if (!include __DIR__ . '/../install/install.php') {require __DIR__ . '/../includes/pages/error/404.php';} },'ANY');

    //TEST ROUTES


    //REDIRECTS FROM OLD SITE STRUCTURE
    $this->addRoute(  '/^Dagsresor$/',      $func = function() {header('Location: https://www.rekoresor.se/kategori/dagsresor/', true, 301); exit;} , 'ANY');
    $this->addRoute(  '/^Operaresor$/',     $func = function() {header('Location: https://www.rekoresor.se/kategori/operaresor/', true, 301); exit;} , 'ANY');
    $this->addRoute(  '/^Teater$/',         $func = function() {header('Location: https://www.rekoresor.se/kategori/teaterresor/', true, 301); exit;} , 'ANY');
    $this->addRoute(  '/^Storhelg$/',       $func = function() {header('Location: https://www.rekoresor.se/kategori/storhelg/', true, 301); exit;} , 'ANY');
    $this->addRoute(  '/^Julmarknader$/',   $func = function() {header('Location: https://www.rekoresor.se/kategori/julmarknader/', true, 301); exit;} , 'ANY');
    $this->addRoute(  '/^Noje-och-dans$/',  $func = function() {header('Location: https://www.rekoresor.se/kategori/noje-och-dans/', true, 301); exit;} , 'ANY');
    $this->addRoute(  '/^spa-och-ma-bra$/', $func = function() {header('Location: https://www.rekoresor.se/kategori/spa-och-ma-bra/', true, 301); exit;} , 'ANY');
    $this->addRoute(  '/^Weekend$/',        $func = function() {header('Location: https://www.rekoresor.se/kategori/weekend/', true, 301); exit;} , 'ANY');

    $this->addRoute(  '/^grupp-och-konferens$/',$func = function() {header('Location: https://www.rekoresor.se/kategori/gruppresor-och-konferens/', true, 301); exit;} , 'ANY');
    $this->addRoute(  '/^gruppresor-dagsresor$/',$func = function() {header('Location: https://www.rekoresor.se/kategori/gruppresor-och-konferens/', true, 301); exit;} , 'ANY');
    $this->addRoute(  '/^gruppresor-flerdagsresor$/',$func = function() {header('Location: https://www.rekoresor.se/kategori/gruppresor-och-konferens/', true, 301); exit;} , 'ANY');
    $this->addRoute(  '/^gruppresor-foretag$/',$func = function() {header('Location: https://www.rekoresor.se/kategori/gruppresor-och-konferens/', true, 301); exit;} , 'ANY');

    $this->addRoute(  '/^resekalender$/', $func = function() {header('Location: https://www.rekoresor.se/#resekalender', true, 301); exit;} , 'ANY');
    $this->addRoute(  '/^vara-resor$/',   $func = function() {header('Location: https://www.rekoresor.se/#resekalender', true, 301); exit;} , 'ANY');

    $this->addRoute(  '/^var-stiger-jag-pa$/',   $func = function() {header('Location: https://www.rekoresor.se/inforresan', true, 301); exit;} , 'ANY');
    $this->addRoute(  '/^bra-att-veta$/',   $func = function() {header('Location: https://www.rekoresor.se/inforresan', true, 301); exit;} , 'ANY');

    $this->addRoute(  '/^om-oss$/',     $func = function() {header('Location: https://www.rekoresor.se/bussresorgoteborg/', true, 301); exit;} , 'ANY');

    $this->addRoute(  '/^bruksvallarna2016$/',  $func = function() {header('Location: https://photos.google.com/share/AF1QipN1t70VQWXPyLGwuDyf8Do9aQGoS9KMdseCw7SmQAmSJvI7594vP7RRfxb-K2_SsA?key=ZUVSWEpaUHFWOXFjcVNWUnY3U1V3VHk1YzRhNjN3', true, 301); exit;} ,'ANY');
    $this->addRoute(  '/^midsommar2016$/',      $func = function() {header('Location: https://www.flickr.com/photos/134541462@N04/sets/72157670307084845', true, 301); exit;} ,'ANY');

  }
}
