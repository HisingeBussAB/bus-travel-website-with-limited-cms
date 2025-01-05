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



  $pageTitle = "Resevillkor – Rekå Resor";





  $morestyles = "<link rel='stylesheet' href='/css/static.min.css' >";



  $dataLayer = "{

    'pageTitle': 'Terms',

    'visitorType': 'high-value',

    'product': false

    }";



  header('Content-type: text/html; charset=utf-8');

  include __DIR__ . '/shared/header.php';



  echo "<main class='main-section container-fluid'><div class='row-fluid'><div class='col-xs-12 col-lg-12'>";



  echo "<h1>Resevillkor</h1>



<p><h2>Särskilda Resevillkor</h2>

( I övrigt hänvisar vi till Allmänna villkor för paketresor.)



Lagstadgad resegaranti har ställts. Org.nr 556176-4456

<h4>Expeditionsavgift</h4>

Denna är 250:- och uttages vid ändringar, avbokningar och andra extraordinära åtgärder som görs utöver bokning av resa.

<h4>Anmälan och betalning</h4>

Anmälningsavgift anges på resebekräftelsen om sådan ska betalas och ska i så fall vara oss tillhanda 10 dagar från bokning. Återstående belopp erlägges senast 30 dagar före avresan. Om anmälan görs senare än 30 dagar före avresan betalas hela beloppet vid erhållandet av resebekräftelsen.

<h4>Avbokning av resa</h4>

Avbeställer resenären resan tidigare än 30 dagar före avresan debiteras endast 250:-



Avbeställer resenären senare än 30 dagar men tidigare än 14 dagar före avresan är anmälningsavgiften förverkad och därtill debiteras expeditionsavgiften på 250:-



Avbeställer resenären senare än 14 dagar men tidigare än 24 timmar före avresan debiteras 50 % av resans totalpris.



Sker avbeställning inom 24 timmar före avresan debiteras 100 % av resans totala pris.



Vid tillfälle då en av \"dubbelgäster\" avbokar, uttages alltid enkelrumstillägg för kvarvarande gäst.


Kostnad för biljetter som ingår i resans pris tex teater, opera, idrott återbetalas ej.


<h4>Inställelse av resa</h4>

Rekå Resor äger rätt att inställa resa som inte samlat tillräckligt antal resenärer. Detta skall meddelas senast 7 dagar före avresan om reslängden är mer än en dag. Vid inställelse av dagsresa meddelas detta senast 2 dagar före avresa.

<h4>Barnrabatter</h4>

Normalt utgår barnrabatter på samtliga destinationer. Dessa anges i respektive reseprogram eller vid förfrågan.

Reservation för valutakurser



Vi reserverar oss för betydande valutaförändringar från det att resan arrangerats till dess genomförande och att detta kan innebära att vi blir tvingade att ta ut valutatillägg om sådan förändring avsevärt påverkar vår prissättning.



&nbsp;

<h2>Allmänna villkor för paketresor</h2>

Konsumentverket, å ena sidan och Svenska Rese- och Turistindustrins Samarbetsorganisation, å andra sidan, har träffat följande överenskommelse att gälla från och med 1993-02-02. Punkten 5.5 har ändrats och punkten 5.6 har tillkommit efter överenskommelse mellan parterna 1994-08-30.

<h4>1. Avtalet</h4>

1.1 Arrangören ansvarar gentemot resenären för vad denna har rätt att fordra till följd av avtalet. Ansvaret gäller även för sådana prestationer som skall fullföras av någon annan än arrangören. Om återförsäljaren är part i avtalet, ansvarar han mot resenären på samma sätt som arrangören.



1.2 Uppgifter i arrangörens kataloger och broschyrer är bindande för denne. En arrangör får dock ändra uppgifter i kataloger eller broschyrer innan avtal har träffats. Detta får dock endast ske om ett uttryckligt förbehåll om det har gjorts i katalogen eller broschyren och om resenären tydligt informeras om ändringarna.



1.3 Arrangören ska hålla resenären underrättad om frågor av betydelse för resenären som sammanhänger med avtalet.



1.4 Anslutningsresa ingår i avtalet endast om den säljs eller marknadsförs tillsammans med huvudarrangemanget för ett gemensamt pris eller för skilda priser som är knutna till varandra.



1.5 Avtalet är bindande för parterna när arrangören skriftligen har bekräftat resenärens beställning och resenären inom avtalad tid betalt överenskommen anmälningsavgift enligt arrangörens anvisningar. Arrangören skall bekräfta resenärens beställning utan dröjsmål.

<h4>2. Betalning av priset för resan</h4>

2.1 Resenären skall betala resans pris senast vid den tidpunkt som framgår av avtalet.



2.2 Arrangören får inte kräva slutbetalning av resans pris tidigare än 40 dagar före avresan, om inte annat särskilt överenskommits.



2.3 Arrangören får i samband med bekräftelsen ta ut en första delbetalning, (anmälningsavgift). Anmälningsavgiften skall vara skälig i förhållande till resans pris och omständigheterna i övrigt.



2.4 Om resenären inte betalar resans pris i enlighet med avtalet har arrangören rätt att häva avtalet och behålla anmälningsavgiften som skadestånd om inte detta är oskäligt.

<h4>3. Resenärens rätt till avbeställning</h4>

3.1 Resenären har rätt att avbeställa resan enligt följande.



3.1.1 Om avbeställning sker tidigare än 30 dagar före avresan mot erläggande av en kostnad motsvarande 5% av resans pris, dock lägst 200 kronor.



3.1.2 Om avbeställning sker därefter men tidigare än 14 dagar före avresan, mot erläggande av en kostnad motsvarande 15% av resans pris.



3.1.3 Om avbeställning sker därefter men tidigare än 24 timmar före avresan, mot erläggande en kostnad motsvarande 50% av resans pris.



3.1.4 Sker avbeställning inom 24 timmar före avresan skall resenären betala hela resans pris med avdrag för eventuell reseskatt.



3.1.5 För s.k. bilpaketresor (resa med egen bil, färjetransport och inkvartering i stuga eller lägenhet) gäller att resenären skall betala hela resans pris vid avbeställning senare än 30 dagar före avresan.



3.2 Om resenären träffat avtal om avbeställningsskydd har resenären i fall som anges i punkt 3.2.1-3.2.3 rätt att avbeställa resan utan annan kostnad än den expeditionsavgift som framgår av arrangörens katalog eller broschyr. Expeditionsavgiften får uppgå till 5% av resans pris dock högst 200 kronor. Vad resenären erlagt för avbeställningsskyddet skall inte återbetalas.



3.2.1 Om resenären eller dennes make/maka/sambo, resenärens eller dennes makes/makas eller sambos släkting i rakt upp- eller nedstigande led eller syskon eller person med vilken resenären gemensamt beställt resan före avresan men efter det att avtalet blivit bindande för resenären enligt punkt 1.5 drabbas av allvarlig sjukdom, försämrat sjukdomstillstånd eller olycksfall och denna händelse är av sådan art, att resenären inte rimligen kan genomföra resan.



3.2.2 Om en annan händelse drabbar resenären efter det att avtalet blivit bindande enligt punkt 1.5 och om händelsen är av så ingripande karaktär för resenären att det inte är rimligt att kräva att resenären skall genomföra resan. Resenären skall inte ha kunnat råda över händelsen och vare sig känt till eller bort känna till denna då resan beställdes. Sådan ingripande händelse är t. ex. brand i den egna bostaden.



3.2.3 Om person med vilken resenären gemensamt beställt resan avbeställer sin resa med stöd av punkterna 3.2.1 eller 3.2.2 och det är oskäligt att resenären skall genomföra resan utan den andra personens sällskap.



3.2.4 Resenär som bokat gemensam inkvartering med person som avbeställt resan med stöd av punkterna 3.2.1-3.2.3 skall erhålla inkvartering av samma standard som enligt avtalet på avtalet eller likvärdigt hotell/anläggning, i rum/lägenhet som är storleksmässigt anpassad till det kvarstående antalet resenärer i sällskapet. Kvarstående resenärer skall vara berättigade att mot erläggande av det av arrangören uppgivna tillägget behålla det ursprungligen inbokade inkvarteringsalternativet. Vid paketresor med buss, båt, tåg eller reguljärt flyg skall resenären i fall som ovan angivits i denna punkt erlägga det tillägg som enligt gällande prislista skall utgå vid inkvartering med högre standard, t ex. enkelrum i stället för dubbelrum.



3.2.5 Resenären skall avbeställa resan så snart som möjligt efter att det att avbeställningsanledningen uppkommit. Grunden för avbeställningen skall på tillförlitligt sätt styrkas med läkar- och/eller släktskapsintyg.



3.3 Avbeställning skall ske på det sätt som anges i katalog, broschyr eller i färdhandlingarna.



3.4 Efter avbeställning skall belopp som resenären har tillgodo enligt ovan återbetalas utan dröjsmål.

<h4>4. Resenärens rätt att överlåta avtalet</h4>

4.1 Resenären får överlåta avtalet till någon som uppfyller alla villkor för att få delta i resan. Ett sådant villkor kan t. ex. vara att transportföretag eller annan som arrangören har anlitat eller gällande regler skall godta byte av resenär. Resenären måste i skälig tid före avresan före avresan underrätta arrangören eller återförsäljaren om sin avsikt.



4.2 När avtalet överlåts är överlåtaren och förvärvaren solidariskt ansvariga gentemot den andra parten i avtalet för vad som återstår att betala för resan och för extra kostnader, dock högst 200 kr som kan uppkomma på grund av överlåtelsen.

<h4>5. Arrangörens ändringar före avresan och inställande av resan</h4>

5.1 Arrangörens rätt att ändra avtalsvillkoren Arrangören får ändra avtalsvillkoren till resenärens nackdel endast om det framgår tydligt av avtalet att detta får ske. Priset får höjas endast om det dessutom av avtalet framgår hur det nya priset skall fastställas.



5.2 Resenärens rätt att frånträda avtalet Resenären får frånträda avtalet, om arrangören förklarar att han inte kommer att fullgöra vad han åtagit sig och avtalsbrottet är av väsentlig betydelse för resenären. Resenären får också frånträda avtalet om avtalsvillkoren ändras väsentligt till hans nackdel. Om arrangören avser att bryta avtalet, eller om han vill ändra avtalsvillkoren, skall han underrätta resenären snarast och därvid lämna besked om dennes rätt att frånträda avtalet enligt första stycket. Resenären skall inom skälig tid meddela arrangören eller återförsäljaren om han vill frånträda avtalet. Gör han inte det, förlorar han sin rätt att frånträda avtalet.



5.3 Resenärens rätt till ersättningsresa Frånträder resenären avtalet enligt 5.2 har han rätt till annan paketresa som är av likvärdig eller högre kvalitet, om arrangören eller återförsäljaren kan erbjuda detta. Om resenären godtar en sämre ersättningsresa har han rätt till ersättning för prisskillnaden. Avstår resenären från sin rätt till ersättningsresa, eller kan en sådan resa inte erbjudas, skall han snarast få tillbaka vad han har betalat enligt avtalet. Bestämmelserna i första och andra stycket gäller också om arrangören inställer resan utan att resenären är skuld till det.



5.4 Resenärens rätt till skadestånd, arrangörens inställande av resa I sådana fall som avses i 5.3 har resenären rätt till skadestånd från arrangören, om det är skäligt. Rätt till skadestånd på grund av att arrangören ställt in resan föreligger inte, om arrangören visar

1. att färre personer än ett i avtalet angivet minimiantal anmält sig till resan och resenären senast 14 dagar före avresan skriftligen underrättats om att resan ställts in (vid resor med en varaktighet av högst 5 dagar gäller att resenären skall underrättas senast 10 dagar före avresan), eller

2. att resan inte kunnat genomföras på grund av ett hinder utanför arrangörens kontroll som denne inte skäligen kunde förväntas ha räknat med när avtalet ingicks och vars följder denne inte heller skäligen kunde ha undvikit eller övervunnit. Beror det på någon som arrangören har anlitat att resan har ställts in, är arrangören fri från skadeståndsansvar enligt första stycket 2 endast om också den som han har anlitat skulle vara fri enligt den bestämmelsen. Detsamma gäller om orsaken än hänförlig till någon annan i ett tidigare led.



5.5 Ändring av priset Inträffar kostandsökningar för arrangören efter det att avtalet enligt 1.5 ovan blivit bindande för parterna, får arrangören höja priset för resan med ett belopp som motsvarar kostandsökningarna om dessa beror på;

1. Ändringar i transportkostnader,

2. Ändringar i skatter, tullar eller avgifter avseende tjänster som ingår i resan, eller

3. Ändringar i växelkurser som påverkar arrangörens kostnader för resan.

Rätt till prishöjning enligt 1 och 3 ovan föreligger endast om kostnadsökningarna överstiger 60 kronor. Priset får inte höjas under de sista 20 dagarna före den avtalade avresedagen. Arrangören skall så snart som möjligt underrätta resenären om prisförändringarna. Resans pris skall sänkas om arrangörens kostnader tidigare än 20 dagar före den avtalade avresedagen, av samma skäl som ovan angivits, minskar. Vid kostandsminskningar enligt 1 och 3 ovan skall priset sänkas endast om kostandsminskningarna överstiger 60 kronor.



5.6 Arrangörens och resenärens rätt att frånträda avtalet vid ingripande händelser m.m. Arrangören och resenären har var och en rätt att frånträda avtalet, om det efter det att avtalet blivit bindande för parterna enligt punkt 1.5 på eller i närheten av resmålet eller utefter den planerade färdvägen inträffar katastrof, krigshandling, generalstrejk eller annan ingripande händelse, som väsentligt påverkar resans genomförande eller förhållandena på resmålet vid den tidpunkt då resans skall genomföras. För att avgöra om händelsen är av sådan allvarlig karaktär som ovan angivits skall sakkunniga svenska eller internationella myndigheter rådfrågas.

<h4>6. Arrangörens ändringar efter avresan, fel och brister</h4>

6.1 Uteblivna prestationer Om efter avresan en väsenlig del av de avtalade tjänsterna inte kan tillhandahållas, skall arrangören ordna lämpliga ersättningsarrangemang utan extra kostnad för resenären. Kan ersättningsarrangemang inte ordnas eller avvisar resenären på godtagbara grunder sådana arrangemang, skall arrangören, om det är skäligt, utan extra kostnad för resenären tillhandahålla likvärdig transport tillbaka till platsen för avresan eller till någon annan ort som resenären godkänner. Innebär en förändring i avtalet enligt första eller andra stycket en försämring för resenären är han om det är skäligt berättigad till prisavdrag och skadestånd.



6.2 Andra fel och brister

Vid andra fel i de avtalade tjänsterna är sådana som anges i 6.1 har resenären rätt till prisavdrag och skadestånd om inte felet beror på honom.

Resenären har inte rätt till skadestånd, om arrangören visar att felet beror på ett hinder utanför arrangörens kontroll som denne inte skäligen kunde förväntas ha räknat med när avtalet ingicks och vars följder denne inte heller skäligen kunde ha undvikit eller övervunnit.

Om felet beror på någon som arrangören har anlitat, är arrangören fri från skadeståndsansvar enligt andra stycket endast om också den som han har anlitat skulle vara fri enligt den bestämmelsen. Det samma gäller om felet beror på någon annan i ett tidigare led.

Vid fel som har sin grund i omständigheter som beskrivs i andra eller tredje stycket skall arrangören genast ge resenären den hjälp som behövs.



6.3 Skadeståndets omfattning

Skadestånd enligt dessa villkor omfattar förutom ersättning för ren förmögenhetsskada, ersättning för personskada och sakskada.

Skador som omfattas av bestämmelserna i sjölagen (1891:35 s.1), luftfartslagen (1957:297), järnvägstrafiklagen (1985:192) eller lagen (1985:193) om internationell järnvägstrafik ersätts enligt de lagarna i stället för enligt dessa villkor. Arrangören är dock alltid skyldig att ersätta resenären för vad denne har rätt att fordra enligt de nämnda lagarna.

Det åligger resenären att i möjligaste mån begränsa skadan.

<h4>7. Reklamation och avhjälpande</h4>

7.1 Resenären får inte åberopa fel i vad han har rätt att fordra till följd av avtalet, om han inte inom skälig tid efter det att han märkt eller bort märka felet underrättar arrangören eller återförsäljaren om felet. Detta bör om möjligt ske på resmålet. 7.2 utan hinder av 7.1 får resenären åberopa fel, om arrangören eller återförsäljaren har handlat grovt vårdslöst eller i strid mot tro och heder.



7.3 Om resenären framför klagomål som inte är obefogade, skall arrangören eller dennes lokala representant genast vidta åtgärder för att finna en lämplig lösning.

<h4>8. Resenärens ansvar under resan</h4>

8.1 Arrangörens anvisningar m.m.

Resenären är skyldig att följa de anvisningar för resans genomförande som lämnas av reseledaren eller av annan person som arrangören anlitar. Resenären är skyldig att respektera de ordningsregler som gäller för resan och för transporter, hotell etc. och uppträda så att medresenärer eller andra inte störs. Om resenären på ett väsentligt sätt bryter mot detta, kan arrangören häva avtalet.



8.2 Resenärens ansvar för skada

Resenären är ansvarig för skada som denne vållar arrangören genom försummelse, t ex genom att inte följa lämnade anvisningar eller föreskrifter.

Det åligger resenären att ersätta skada som är lagligen grundad gentemot någon som arrangören anlitar för att medverka vid resans genomförande.



8.3 Pass, visum, hälsobestämmelser m.m.

Innan avtal sluts skall arrangören eller återförsäljaren på lämpligt sätt informera resenären om sådana hälsobestämmelser som blir tillämpliga under resan samt, i den mån det har betydelse för resenären om vad som gäller i fråga om pass och visum för medborgare i stater inom europeiska samarbetsområdet.

Resenären är dock själv ansvarig för att iaktta nödvändiga formaliteter för resans genomförande som t. ex. innehav av giltigt pass, visum, vaccinationer, försäkring.

Resenären är själv ansvarig för alla kostnader som uppkommer på grund av brister i nämnda formaliteter, t. ex. hemtransport till följd av avsaknaden av pass, om inte bristerna orsakats av felaktig information från arrangören eller återförsäljaren.



8.4 Avvikande från arrangemanget

Resenär som efter det att resan påbörjats avviker från arrangemanget är skylig att meddela detta till arrangören eller till dennes representant. Resenären skall senast 24 timmar före av arrangören uppgiven tid för återresa kontakta denne för kontroll av uppgifter om hemresan.

<h4>9. Tvistlösning</h4>

Parterna bör försöka lösa tvist som gäller tolkningen eller tillämpningen av avtalet genom förhandlingar. Om parterna inte kan enas, kan tvisten prövas av allmänna reklamationsnämnden eller av allmän domstol.



Vi reserverar oss för eventuella tryckfel.</p>



<h1>Personuppgifter</h1>



<p>Vi sparar personuppgifter för att kunna utföra våra tjänster. Vi delar inte några personuppgifter med tredje part för annonseringsändamål. Vi delar bara uppgifter med trejde part när detta är nödvändigt för att kunna levererea våra tjänster.

Exempelvis så delar vi namn på bokade resenärer med de hotell, rederier, flygbolag osv som är involverade i att utföra våra resan.</p>

<p>Ifall uppgifter lämnas i samband med en betalning är vi oftast skyliga att spara dessa uppgifter i 10 år enligt bokförinslagen. Uppgifter sparade för bokföringslagen hålls separat och används inte för andra syften.</p>

<p>Vi sparar uppgifter för egna informationssyften, dessa rensas kontinuerligt och används sparsamt för att skicka ut så för kunden relevant och intressant information som möjligt.</p>

<p>Vid begärd prenumeration på vårt nyhetsbrev sparas e-postadress i prenumerationsregister till dess den begärs borttagen.</p>

<p>För att få ut kopior på uppgifter vi har om dig eller för att begära att uppgifter raderas kontaka <a href='mailto:info@rekoresor.se'>info@rekoresor.se</a></p>";



  echo "</div></div></main>";



  include __DIR__ . '/shared/footer.php';





} catch(\UnexpectedValueException $e) {

  if (DEBUG_MODE) echo $e->getMessage();

  include 'error/404.php';

} catch(\RuntimeException $e) {

  if (DEBUG_MODE) echo $e->getMessage();

  include 'error/500.php';

}

