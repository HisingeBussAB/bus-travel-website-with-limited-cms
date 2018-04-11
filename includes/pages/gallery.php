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
namespace HisingeBussAB\RekoResor\website\includes\pages;
use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\Functions as functions;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;


try {

  $pageTitle = "Efter resan – Rekå Resor";


  $morestyles = "<link rel='stylesheet' href='/css/static.css' >";

  $dataLayer = "{
    'pageTitle': 'Gallery',
    'visitorType': 'low-value',
    }";

  header('Content-type: text/html; charset=utf-8');
  include __DIR__ . '/shared/header.php';

  echo "<main class='main-section container-fluid'><div class='row-fluid'><div class='col-xs-12 col-lg-12'>";

  echo "<h1>Bilder</h1>

<p><a href='https://www.flickr.com/photos/134541462@N04/collections/72157658328450860/'>Bruksvallarna 2015</a></p>
<p><a href='https://www.flickr.com/photos/134541462@N04/sets/72157670307084845'>Midsommar 2016</a></p>
<p><a href='https://photos.google.com/share/AF1QipN1t70VQWXPyLGwuDyf8Do9aQGoS9KMdseCw7SmQAmSJvI7594vP7RRfxb-K2_SsA?key=ZUVSWEpaUHFWOXFjcVNWUnY3U1V3VHk1YzRhNjN3'>Bruksvallarna 2016</a></p>
<p>Mer bilder från våra resor kommer inom kort.</p>

  ";

  echo "</div></div></main>";

  include __DIR__ . '/shared/footer.php';


} catch(\UnexpectedValueException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/404.php';
} catch(\RuntimeException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/500.php';
}
