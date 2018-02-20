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


  $morestyles = "<link rel='stylesheet' href='/css/static.min.css' >";

  $dataLayer = "{
    'pageTitle': 'After-Tour',
    'visitorType': 'low-value',
    }";

  header('Content-type: text/html; charset=utf-8');
  include __DIR__ . '/shared/header.php';

  echo "<main class='main-section container-fluid'><div class='row-fluid'><div class='col-xs-12 col-lg-12'>";

  echo "<h1>Efter resan</h1>
  <p>Efter resan lägger vi ut eventuella <a href='#bilder'>bilder</a> från den på <a href='https://plus.google.com/collection/Qt0SWE'>Google Photos</a>. Du får gärna sända bilder som du skulle vilja kommer andra resenärer till del genom att vi lägger in dem. Skicka i så fall bilderna eller länk till dessa till <a href='mailto:info@rekoresor.se'>info@rekoresor.se</a>.
  </p>
  <p>Eftersom många av våra resor kommer igen då och då är vi tacksamma för förslag som skulle kunna förädla innehållet i resan, vilket innebär att vi mottar sådana synpunkter med största intresse. Ett riktigt bra förslag som vi tar in i nästa program kan till och med ge dig en rabatt vi nästa bokning av den eller annan resa!

<h2>Synpunkter</h2>
<p>För att våra resor skall bli så bra som möjligt är era synpunkter av stort värde. Vi är väldigt tacksamma om ni tar er tiden att fylla i ett litet formulär om resan.</p>
<p>Vilken resan har ni åkt på? (Länk och val av resa och formulär här)</p>


<h2 id='bilder'>Bilder</h2>
<p><a href='https://www.flickr.com/photos/134541462@N04/collections/72157658328450860/'>Bruksvallarna 2015</a></p>
<p><a href='https://www.flickr.com/photos/134541462@N04/sets/72157670307084845'>Midsommar 2016</a></p>
<p><a href='https://photos.google.com/share/AF1QipN1t70VQWXPyLGwuDyf8Do9aQGoS9KMdseCw7SmQAmSJvI7594vP7RRfxb-K2_SsA?key=ZUVSWEpaUHFWOXFjcVNWUnY3U1V3VHk1YzRhNjN3'>Bruksvallarna 2016</a></p>



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
