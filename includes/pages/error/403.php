<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * 403 error main content page.
 */

header("HTTP/1.0 403 Forbidden");
require_once __DIR__ . '/../shared/header.php';
?>
403 Forbidden request.
<a href="http://<?php echo DOMAIN;?>">Tillbaka till huvudsidan.</a>


<?php
require_once __DIR__ . '/../shared/footer.php';
?>
