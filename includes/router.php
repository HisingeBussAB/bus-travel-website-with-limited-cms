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

namespace HisingeBussAB\RekoResor\website\includes;

class Router
{
  private $map = array(
   'admin' => __DIR__ . '/../admin-cp/admin.php',                         //admin panel router
   'resa' => __DIR__ . '/pages/showtrip.php',                             //dynamic server specific trip
   'galleri' => __DIR__ . '/pages/showgallery.php',                       //static/dynamic fixed
   'kategori' => __DIR__ . '/pages/showcategory.php',                     //dynamic server trips per category
   '' => __DIR__ . '/pages/mainpage.php',                                 //dynamic serves current trips, news and offers
   'bestallkatalog' => __DIR__ . '/pages/showorderinfo.php',              //static/dynamic fixed
   'inforresan' => __DIR__ . '/pages/showbeforetrip.php',                 //static/dynamic fixed
   'bussresorgoteborg' => __DIR__ . '/pages/showabout.php',               //static/dynamic fixed
   'kontaktarekaresor' => __DIR__ . '/pages/showcontact.php',             //static/dynamic fixed
   'ajax' => __DIR__ . '/../ajax/ajax.php'                                //internal AJAX call router
  );

  public function route() {
    $uri = explode($_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI'], 2);
    $path = $uri[0];
    $path = filter_var($path, FILTER_SANITIZE_URL);
    $path = trim($path, '/');

    list($page, $id) = array_pad(explode('/', $path, 2), 2, '');
    echo $page;
    echo $id;

    if(!in_array($page, array_keys($this->map))){
      require __DIR__ . '/pages/error/404.php';
    } else {
      require $this->map[$page];
    }
  }
}
