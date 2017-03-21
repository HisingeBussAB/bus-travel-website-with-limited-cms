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
header("HTTP/1.0 404 Not Found");
require_once __DIR__ . '/../shared/header.php';
?>
Sidan finns inte. Kontrollera stavningen.
<a href="http://<?php echo DOMAIN;?>">Tillbaka till huvudsidan.</a>


<?php
require_once __DIR__ . '/../shared/footer.php';
?>
