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


  $morestyles = "<link rel='stylesheet' href='/css/static.min.css' >";

  header('Content-type: text/html; charset=utf-8');
  include __DIR__ . '/shared/header.php';

  echo "<main class='main-section container-fluid'><div class='row-fluid'><div class='col-xs-12 col-lg-12'>";

  echo "<h1>Pensionärsresor</h1>

<p><h1><strong>Pensionärsresor</strong></h1>
Har du sökt efter pensionärsresor? Du som är pensionär har ofta lite tid över till att hitta på sådant som du tycker är roligt att göra i och med att du inte har ett jobb som du måste gå till. Om du är den typen som gillar äventyr, se dig om på nya platser och dessutom träffa nya människor, så tror vi du kommit helt rätt. Vi på Rekå Resor har ett helt register i bagaget med resor vi planerat och anordnat i många år. Vi kan det här med bussturer och gruppresor vare sig det är över dagen eller för en weekend.

Att resa med buss tycker vi är ett av de allra mest optimala sätten för att få komma ut på en avkopplande utflykt. Du behöver varken anstränga dig för att köra och du får se mer av färden än om du sitter i en låg liten bil eller flyger dit du ska. Här kan du se ut över vägarna och njuta av själva resan dit du är på väg istället för att bara se det som en transportsträcka. För oss är själva bussfärden en stor del av våra pensionärsresor som vi arrangerar och ibland är det resan som är målet. Därför är din upplevelse det allra viktigaste för oss, vi vill sätta guldkant på din vardag och göra det möjligt även för dig som är lite äldre att få ta dig ut och se dig om tillsammans med andra liksinta pensionärer. Det är aldrig försent att träffa nya vänner och det kan vi nästan garantera dig att du gör på någon av våra bussar i alla fall för resan.
<h2><strong>Pensionärsresor som passar dig</strong></h2>
Vi erbjuder redan färdiga resor som du kan boka in dig på och om ni är fler går även det bra, dessutom om ni är en grupp så går det utmärkt att kontakta oss om ni vill planera era egna pensionärsresor. Vi hjälper er att komma överens om ett resmål och en agenda som passar allas intresse och plånbok. Tack vare vårt breda kontaktnät efter över 60 år i branschen så kan vi skräddarsy de allra flesta resor exakt efter ditt önskemål, inga planer är för svåra för oss oavsett om ni är 2 eller 50 personer som vill ut och åka.

Hos oss anordnas massa olika turer varje år, så det finns definitivt roliga och spännande alternativ för dig som är äldre, och vill ut och göra lite roliga pensionärsresor. Om du och din man eller fru känner för att göra en resa i Europa tillsammans men vill slippa flyga, så är en bussresa med Rekå Resor ett helt klart prisvärt och smidigt alternativ.</p>


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
