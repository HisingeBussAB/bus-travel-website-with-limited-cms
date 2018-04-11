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

  $pageTitle = "Bussresor Göteborg – Rekå Resor";


  $morestyles = "<link rel='stylesheet' href='/css/static.css' >";

  $dataLayer = "{
    'pageTitle': 'LandingPage_Bussresor',
    'visitorType': 'low-value',
    }";

  header('Content-type: text/html; charset=utf-8');
  include __DIR__ . '/shared/header.php';

  echo "<main class='main-section container-fluid'><div class='row-fluid'><div class='col-xs-12 col-lg-12'>";

  echo "<h1>Bussresor Göteborg</h1>

Har du sökt efter bussresor Göteborg? Att åka på bussresor är något som funnits med länge och som fortsätter vara väldigt populärt. I och med att det finns så många olika sätt att resa på, resmål samt saker att göra under resan gäller det att du hittar det du tycker är roligast. Det är det vi jobbar för, att du ska ha en så rolig resväg som möjligt när du är ute på våra bussturer. För att lyckas med detta gäller det att prova många olika typer av resor, därför har vi en rad olika typer av val för att du ska hitta något som faller dig i smaken. För även om du redan har hittat din favorit kan det vara kul att ibland testa ett annat sätt att resa på, för att helt enkelt få lite variation och se något nytt. Det finns resor som går från de allra flesta städerna så det är inte svårt att boka bussresor Göteborg till exempel är en stad där vi erbjuder många olika turer ifrån. Perfekt för dig som bor i Göteborg och som är sugen på att testa en annan typ av resa. Om du aldrig tidigare har bokat en bussresa kanske det blir en ny favorit och för dig som har åkt på dessa typer av utflykter förut, kanske det blir en favorit i repris.
  <h2>Boka bussresor Göteborg</h2>
  Idag väljer många automatiskt att åka bil eller flyga när de till exempel ska ut och resa, men just bussresor är något som alla inte tänker på. Det finns många fördelar med att välja just bussresor Göteborg ligger verkligen perfekt till för detta, därför har vi flera resor som går därifrån. Göteborg ligger nära väldigt många vackra städer och det finns även många bra förbindelser till andra länder därifrån. När du reser med våra bussar Göteborg slipper du dessutom gå igenom omständliga säkerhetskontroller och oroa dig för att ditt bagage kanske försvinner på vägen om du har otur.

Något som är extra kul med bussresorna är att du enkelt kan välja hur länge du vill vara borta. Du kan till exempel göra en dagsresa men du kan även vara borta en längre tid, om du känner för det. Bussresorna som avgår från Göteborg kan ta dig till operahusen i Oslo och Köpenhamn eller en teater till exempel, men du kan även åka på spa om det är något som känns mer lockande. Tänk så mycket roligt du kan hitta på med din partner, dina vänner eller varför inte själv, när du väljer att åka på en av våra spännande resor. Att åka tillsammans med någon är kul i och med att det blir en delad upplevelse, fast det kan även vara skönt att få lite tid med själv och kanske lära känna nya människor på vägen.
  ";
  echo "</div></div><div class='row-fluid'><div class='col-xs-12 col-lg-12'><a href='/'><img src='https://www.rekoresor.se/img/bussreko2.jpg' style='border-radius:5px; max-width: 800px; display: block; margin: 50px auto;' /></a>";
  echo "</div></div></main>";

  include __DIR__ . '/shared/footer.php';


} catch(\UnexpectedValueException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/404.php';
} catch(\RuntimeException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/500.php';
}
