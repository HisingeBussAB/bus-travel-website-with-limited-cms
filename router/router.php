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
  public static function route() {
    $uri = explode($_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI'], 2);
    $path = $uri[0];
    $path = filter_var($path, FILTER_SANITIZE_URL);
    $path = trim($path, '/');



    //$m = new Map();
    //$m->construct();
    $map = Map::gMap();

    call_user_func($map['admin']['']);
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
      */
  }
}
