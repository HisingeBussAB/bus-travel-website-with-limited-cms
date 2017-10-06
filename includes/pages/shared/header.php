<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * (c) Rekå Resor AB
 *
 * Header HTML for standard pages.
 *
 * @link      https://github.com/HisingeBussAB/bus-travel-website-with-limited-cms
 * @author    Håkan Arnoldson
 */

 use HisingeBussAB\RekoResor\website as root;

 //Get dynamic content for this include
 $categories = root\admin\includes\classes\Categories::getActiveCategories(true);
 if ($categories !== false) {
   $categories = json_decode($categories);
 }

 if (empty($meta)) {
 $meta = "<meta property='og:title' content='Rekå Resor - Bussresor i Norden och Europa'>

 <meta property='og:url' content='http" . APPEND_SSL . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "' />
 <meta name='description' content='Rekå Resor erbjuder bussresor inom Sverige och till hela Europa med utgångspunkt från Göteborg. Välkommen till en trevlig bussresa och ett spännande äventyr.' />
 <meta property='og:description' content='Rekå Resor erbjuder bussresor inom Sverige och till hela Europa med utgångspunkt från Göteborg. Välkommen till en trevlig bussresa och ett spännande äventyr.' />";
}

if (empty($robots)) {
  $robots = "<meta name='robots' content='index, follow'>";
}


?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta http-equiv="content-type" content="text/html;charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="author" content="Håkan K Arnoldson">
  <title><?php echo $pageTitle . " - Rekå Resor"?></title>

  <meta property="og:type" content="website">
  <meta property="og:locale" content="sv_SE">
  <meta property="og:site_name" content="Rekå Resor" />
  <?php
  echo $meta;
  echo $robots;
  ?>


  <link rel="icon" href="/favicon/favicon.ico">

  <!--FAVICON START-->
  <link rel="apple-touch-icon" sizes="57x57" href="/favicon/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="/favicon/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="/favicon/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="/favicon/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="/favicon/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="/favicon/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="/favicon/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/favicon/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192"  href="/favicon/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="/favicon/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
  <link rel="manifest" href="/favicon/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="/favicon/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">
  <!--FAVICON END-->




  <link rel="stylesheet" href="/dependencies/bootstrap-3.3.7-dist/css/bootstrap.min.css">
</head>
<body>


  <header class="text-center hidden-print">
    <div class="text-right header-wrap">
      <figure class="top-logo clear-on-tiny center-on-tiny">
        <a href="/"><img src="/img/logo.gif" alt="Rekå Resor AB"><span class="sr-only">Välkommen till Rekå Resor</span></a>
        <figcaption>- mer än 60 år av reko resor -</figcaption>
      </figure>
      <nav class="clear-on-tiny">
        <ul class="nav nav-pills center-on-tiny" aria-label="Meny">
          <li role="presentation"><a href="/#resekalender">Resekalender</a></li>
          <li role="presentation"><a href="/bestall-katalog">Beställ program</a></li>
          <li role="presentation"><a href="/inforresan">Inför resan</a></li>
          <li role="presentation"><a href="/efterresan">Efter resan</a></li>
          <li role="presentation"><a href="/galleri">Bildgalleri</a></li>
          <li role="presentation"><a href="/bussresorgoteborg">Mer om oss</a></li>
          <li role="presentation"><a href="https://sv-se.facebook.com/rekoresor/" aria-label="Besök oss på Facebook"><i class="fa fa-facebook-square" aria-hidden="true"></i><span class="sr-only">Facebook länk</span><span class="hidden-sm hidden-xs">&nbsp;Facebook</span></a></li>
          <li role="presentation" class="hidden-xs"><a href="/kontakt">Kontakt</a></li>
          <li role="presentation" class="visible-xs-block"><a rel="nofollow" href="tel:+4631222120"><i class="fa fa-phone" aria-hidden="true"></i><span class="sr-only">Telefon</span>&nbsp;Ring oss</a></li>
        </ul>
      </nav>
    </div>
    <ul aria-label="Resekategorier" id="categories-wrap">
      <?php
      foreach($categories as $category){
        $category->uri_kategori;
      echo "<li><a href=\"/kategori/" . $category->uri_kategori . "\">" . htmlentities($category->kategori, ENT_QUOTES) . "</a></li>";
     }
    ?>
    </ul>
  </header>
  <nav class="visible-xs-block" id="to-top-chevron"><a href="#top" aria-label="Till toppen av sidan"><i class="fa fa-chevron-up fa-2x" aria-hidden="true"></i><span class="sr-only">Till toppen av sidan</span></a></nav>
