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

  $pageTitle = "Pensionärsresor – Rekå Resor";


  $morestyles = "<link rel='stylesheet' href='/css/static.css' >";

  $dataLayer = "{
    'pageTitle': 'LandingPage_Pensionarsresor_med_buss',
    'visitorType': 'low-value',
    }";

  header('Content-type: text/html; charset=utf-8');
  include __DIR__ . '/shared/header.php';

  echo "<main class='main-section container-fluid'><div class='row-fluid'><div class='col-xs-12 col-lg-12'>";

  echo "<h1>Pensionärsresor med buss</h1>

  <h1>Pensionärsresor med buss</h1>
  Är du kanske nybliven pensionär? Äntligen har du tid för någonting annat än att bara jobba och vi på Rekå Resor vet att resa är ett av de bästa sätten att få komma bort, ha lite extra roligt eller bara för att få koppla av. Därför erbjuder vi pensionärsresor med buss för att göra det möjligt även för de äldre att få komma ut en sväng och se sig om, utan att behöva hålla koll på trafiken och körningen.

  Just pensionärsresor behöver inte kosta särskilt mycket och passar därför de allra flesta seniorer. Du kan dessutom välja hur länge du vill vara borta samt var du vill åka. Här kan du komponera din egna resa bara för dig själv eller kanske för en större grupp som en förening eller liknande. Det finns många roliga resor som kan göras med buss. Du och dina vänner kan till exempel åka iväg på en av våra dagsresor till kanske någon stad som ni inte besökt tidigare och upptäcka stället tillsammans. Det finns även möjlighet att åka på spa, en matresa eller till exempel till en julmarknad. Känner du dig klar med Sverige finns det många andra intressanta resor till resten av Europa också, bland annat Holland och Tyskland.
  <h2>Pensionärsresor med buss upplevelse</h2>
  Oavsett var du och ditt sällskap åker kommer ni att få två upplevelser i en, dels en rolig bussresa att minnas samt en upptäcktsfärd när ni anländer vid slutdestinationen. Vi har arbetat med dessa typer av bussresor i 60 år och vet hur lyckade pensionärsresor med buss ska läggas upp. Till oss kan du vända dig med förslag, tankar och idéer och så utgår vi från dem för att ordna en så personlig och trevlig resa som möjligt.

  Något som är lite extra roligt när det kommer till att åka på just pensionärsresor med buss är att du och din vän eller dina vänner har möjlighet att prata, umgås och lära känna varandra ännu bättre under resans gång. Om du väljer att åka själv har du möjlighet att lära känna andra pensionärer under bussturen.

  Det finns många olika tips på upplägg och redan färdiga resmål där du och dina vänner kan leta efter inspiration, när ni ska planera era bussutflykter. Likaväl som att åka på bussresan med dina vänner kan du så klart åka med din partner eller helt själv. Det är helt enkelt upp till dig att bestämma hur planeringen av resan ska se ut, sen ser vi på Rekå Resor till att du kommer iväg.

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
