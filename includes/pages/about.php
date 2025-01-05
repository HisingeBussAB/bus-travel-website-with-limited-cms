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



  $pageTitle = "Om Rekå Resor – Bussresor i Norden och Europa";





  $morestyles = "<link rel='stylesheet' href='/css/static.min.css' >";



  $dataLayer = "{

    'pageTitle': 'About',

    'visitorType': 'low-value',

    'product': false,

    }";



  header('Content-type: text/html; charset=utf-8');

  include __DIR__ . '/shared/header.php';



  echo "<main class='main-section container-fluid'><div class='row-fluid'><div class='col-xs-12 col-lg-12'>";



  echo "<h1>Om Rekå Resor</h1><h3>Bussresor i Norden och Europa</h3>



<h2>Allt börjar i Göteborg</h2>



<p>Allt börjar i Göteborg, din nästa resa, men också vår historia som bussbolag som nu sträcker sig mer än sex decennier tillbaka i tiden. 2013 firade Rekå Resor 60 år, genom alla dessa år har vi haft glädjen att få se många lyckliga resenärer få sin drömresa gå i uppfyllelse, där vi har varit en del i planeringen. I samma anda fortsätter vi än idag och vi är övertygade om att också din bussresa blir något alldeles speciellt med oss.

Vår strävan är att hela tiden bli bättre och försöka hitta nya idéer som ska locka dig som resenär. På många av våra resor har vi med en reseledare som tillsammans med våra fantastiska förare ser till att resorna kryddas med det lilla extra. De visar dig alla smultronställen längs vägen och ger dig trygghet, underhållning och intressant guidning.</p>

<p>Vi på Rekå Resor hoppas kunna bidra med extra mycket inspiration och resglädje i vårt magasin och välkomnar dig till en spännande säsong.</p>





</div></div><div class='row-fluid'><div class='col-xs-12 col-sm-9 col-lg-7'>

<h2>Smidigt och lätt att komma iväg</h2>

<p>Vill du göra din resa så enkel som möjligt? Det vill vi också. Med Rekå Resor är det enkelt att stiga av och på. Vi har många olika busstopp för dagsresor, flerdagsresor, södergående resor och norrgående resor. Till exempel stiger du smidigt på vid First Hotell G Centralstationen i Göteborg och Eurostop-Statoil i Halmstad, bara för att nämna några. Läs mer om våra hållplatser under fliken ”Var stiger jag på?” och se avresetider i resekalendern.</p>





<h2>Nästa destination – vart du vill</h2>

<p>Svårt att bestämma dig? Vi erbjuder hjälp med att hitta inriktningen på din resa, sen tar vi er till destinationen. Rekå Resor har genom åren hjälpt otaliga semestersugna familjer, så väl som konferensinriktade företag, att hitta rätt med sina bussresor från Göteborg. Vi har koll på svenska smultronställen, vilket garanterar att din resa får det där lilla extra. Dessutom har du många spännande resepaket att välja mellan – endast med Rekå Resor.

Gör som många nöjda resenärer – boka din bussresa från Göteborg med oss.</p>







<h2>Miljö och Kvalitet</h2>

<p>Miljön är viktig för oss, därför arbetar vi kontinuerligt med att hitta sätt att ta hänsyn till miljön som är en naturlig del i vår verksamhet. För att kunna leva upp till våra egna krav inom Rekå Resor använder vi oss av miljövänliga bussar som är ett energisnålt sätt att färdas och skonsamt mot vår natur.

Kvalitet är lika viktigt, därför är våra chaufförer erfarna, professionella och våra bussar noga kontrollerade och servade av vår personal i vår anläggning i Göteborg. Detta säkerställer att vi kan utföra säkra och trygga bussresor med hög kvalitet.</p>







</div><div class='hidden-xs col-sm-3 col-lg-5'><p><img src='/img/bussreko2.jpg' /></p><p><img src='/img/bussreko3.jpg' /></p></div>



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

