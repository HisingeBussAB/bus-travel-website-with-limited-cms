<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * 500 error main content page.
 */

$pageTitle = "Serverfel";
$robots = "<meta name='robots' content='noindex, follow'>";
header($_SERVER["SERVER_PROTOCOL"]." 500 Server Error");
require_once __DIR__ . '/../shared/header.php';

echo "<main class='main-section clearfix container'>
Ett fel har tyvärr inträffat på servern.<br>
Prova gärna att, ringa <a href='tel:+4631222120'>031-222120</a> eller <a href='mailto:info@rekoresor.se'>maila oss</a> om problemet består.
<br><br>
<a href='http" . APPEND_SSL . "://" . DOMAIN . "'>Tillbaka till huvudsidan för att försöka igen.</a></main>";


require_once __DIR__ . '/../shared/footer.php';
