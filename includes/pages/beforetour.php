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

  $pageTitle = "Inför Resan - Rekå Resor";


  $morestyles = "<link rel='stylesheet' href='/css/static.min.css' >";

  $dataLayer = "{
    'pageTitle': 'Before-Tour',
    'visitorType': 'low-value',
    'product': false
    }";

  header('Content-type: text/html; charset=utf-8');
  include __DIR__ . '/shared/header.php';

  echo "<main class='main-section container-fluid'><div class='row-fluid'><div class='col-xs-12 col-lg-12'>";

  echo "<h1>Inför resan</h1>
  <h2>Bra att veta</h2>
  <p>Innan resan ber vi dig att kontrollera din resebekräftelse, så att den till alla delar stämmer med vad du har beställt. Dessutom ber vi dig tänka till om du uppgett eventuella allergier eller på annat sätt gett uppgifter till oss som är viktiga för att din resa skall bli så bra som möjligt!</p>

</div></div><div class='row-fluid'><div class='col-sm-12 col-md-6'>



<h3>Färdhandling</h3>
<p>Efter bokning skickas bekräftelse/resebevis med inbetalningskort hem till dig. I resebeviset framgår det vilken tid bussen går samt vilken avreseort du valt. Kontrollera noga så att bekräftelsen stämmer överens med dina önskemål. Bekräftelse/resebevis gäller som biljett när resan är slutbetald.</p>

<h3>Avbokning av resa</h3>
<p>Avbokning av resa skall omgående ske till Rekå Resor. Se <a href='/resevillkor'>resevillkoren</a> för ytterligare information om avbokning.</p>



<h3>Programändringar</h3>

<p>Förändringar i det beställda arrangemanget kan inträffa även de sista dagarna före avresan p.g.a. händelser som ligger utanför vår kontroll. Alla väsentliga förändringar meddelas skriftligen eller per telefon till alla som berörs under sin resa så snart ändringen kommer till vår kännedom.</p>



<h3>Bagage</h3>

<p>Bussarnas bagageutrymmen är begränsade. Därför måste också ditt bagage begränsas till en resväska som inte överstiger 25 kg samt ett handbagage som inte överstiger 5 kg. Du ansvarar själv för ditt bagage och att ömtåliga saker förpackas så att de ej går sönder. På resor som kräver speciell utrustning, tex skidresor gäller andra regler.</p>



<h3>Våra bussar</h3>

<p>Alla våra bussar är högkomfortbussar av nyare årsmodell. Detta innebär att det finns toalett, video, kaffeautomat och bra benutrymme.</p>



<h3>Måltiderna</h3>

<p>Önskemål om vegetarisk kost, glutenfri mat, laktos med mera skall alltid meddelas vid bokningen. På våra Europaresor meddelar vi detta till hotell och restauranger senast 1 månad innan avresa. Därför är detta viktigt att vi får information om detta vid bokningstillfället.</p>


</div><div class='col-sm-12 col-md-6'>
<h3>Gäster med speciella behov</h3>

<p>Eventuella handikapp meddelas alltid vid bokning så att hotellen blir informerade i god tid. Vi rekommenderar att resenären har med sig någon medresenär som kan hjälpa till med praktiska saker under resan. Vår personal måste ta hänsyn till samtliga resenärer och kan därför inte ägna alltför mycket tid åt varje enskild resenär.</p>



<h3>Kvarglömda saker</h3>

<p>För kvarglömda saker ansvaras ej. Det som upphittas i våra bussar tar vi hand om och kan återfås mot beskrivning.</p>



<h3>Pass</h3>

<p>Vi rekommenderar att du tar med dig ditt pass på resor utanför Norden. Enligt gällande regler för tull och polissamarbetet inom ramen för Schengenavtalet behöver du inte ha med dig pass vid in- och utresa i land som ingår i avtalet. Dock måste du i många länder kunna styrka din identitet och ditt medborgarskap vid incheckning på hotell eller på anmodan av polis eller annan myndighet. Då är pass oftast den enda giltiga identitetshandlingen. Vi rekommenderar också att du har ett pass som är giltigt sex månader efter avresan eftersom det i många länder ställs som krav.</p>



<h3>Reseförsäkring</h3>

<p>Tänk på att ha ett ordentligt försäkringsskydd. Vi råder dig att teckna en riktig reseförsäkring i samband med bokning av resan. Rekå Resor kan hjälpa dig med detta.</p>



<h3>Valuta</h3>

<p>För information om lämplig valuta och valutakurser rekommenderar vi dig att du tar kontakt med bank eller växlingskontor, som t ex Forex.</p>



<h3>Personuppgifter</h3>

<p>Rekå Resor för register över de personuppgifter du lämnat i enlighet med personuppgiftslagen (PUL). Uppgifterna används för att vi ska kunna fullgöra våra åtaganden gentemot dig. Nödvändiga uppgifter lämnas till våra samarbetspartners, som hotell och rederier. Vi använder även uppgifterna i registret för att kunna ge information, erbjudanden och service rörande resan via e-mail, telefon och postala utskick.

Om du inte motsatt dig att de uppgifter du lämnat vid bokningen får användas efter resan, godkänner du att Rekå Resor skickar erbjudanden, information, nyhetsbrev etc via postala utskick. Du kan när som helst välja att tacka nej till detta.

När du rest med oss sparas personuppgifterna i ett kundregister. Vill du inte att uppgifterna om dig ska finnas kvar eller att de ska ändras kontaktar du oss via e-post på <a href='mailto:info@rekoresor.se'>info@rekoresor.se</a></p>
</div></div><div class='row-fluid'><div class='col-sm-12 col-md-6'>
<h2>Resevillkor</h2>

<p>Våra fullständiga <a href='/resevillkor'>resevillkor</a> finns att läsa <a href='/resevillkor'>här</a>.</p>

<h2>Påstigning</h2>

<p>Påstigningsplatser visas under respektive resa. Här är ett urval av vanliga påstigningsplatser vi använder oss utav.</p>

<h3>Samtliga resor</h3>

<p>Göteborg / Clarion Post Hotel</p>

<h3>Södergående resor</h3>

<p>Alingsås / Järnvägsstationen<br>
Floda / Statoil<br>
Lerum / Lerums station<br>
Mölndal / Mölndalsbro (under bron Hlp G)<br>
Kållered / Busstationen<br>
Kungsbacka / Tingberget<br>
Varberg / Björkäng Vägkrog<br>
Falkenberg / McDonalds-Preem<br>
Halmstad / Eurostop-Statoil</p>

<h3>Norrgående resor via E6</h3>

<p>Kungälv / Kungälvs Motet<br>
Stenungsund / Munkeröd/Stenungsundsmotet<br>
Uddevalla / Torp Köpcentrum alt. Kampenhof</p>

<h3>Norrgående resor via E20</h3>

<p>Alingsås / Järnvägsstationen<br>
Vårgårda / Rasta</p>

<h3>Resor via R40 mot Stockholm</h3>

<p>Bollebygd / Statoil<br>
Borås / Järnvägsstationen<br>
Ulricehamn / Busstation</p>
</div><div class='col-sm-12 col-md-6'>
<img src='/img/suitcase-pixabay.jpg' />
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
