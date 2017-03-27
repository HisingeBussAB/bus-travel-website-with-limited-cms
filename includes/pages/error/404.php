<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * 404 error main content page.
 */

header("HTTP/1.0 404 Not Found");
require_once __DIR__ . '/../shared/header.php';
?>
Sidan finns inte. Kontrollera stavningen.
<a href="http://<?php echo DOMAIN;?>">Tillbaka till huvudsidan.</a>


<?php
require_once __DIR__ . '/../shared/footer.php';
?>
