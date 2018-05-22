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
  $robots = "<meta name='robots' content='index, follow' />";
}

if (empty($dataLayer)) {
  $dataLayer = "{}";
}


?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta http-equiv="content-type" content="text/html;charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <script>
    var bgImg = new Image();
    bgImg.src = '../upload/background-1.jpg';
    bgImg.onload = function(){
      var node = document.createElement('style');
      node.setAttribute('type', 'text/css')
      var t = document.createTextNode('body::after {background-image: url("' + bgImg.src + '");}');
      node.appendChild(t);
      document.head.appendChild(node);
    };
    window.dataLayer = window.dataLayer || [];
    dataLayer.push(<?php echo $dataLayer ?>)
  </script>
<?php if (!DEBUG_MODE) { ?>
  <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-TLG9DXN');</script>
  <!-- End Google Tag Manager -->
<?php } ?>
  <!--Invisible reCAPTCHA-->
  <script src="https://www.google.com/recaptcha/api.js?hl=sv" async defer></script>
  <!--end Invisible reCAPTCHA-->
  <meta name="author" content="Håkan K Arnoldson">
  <title><?php echo $pageTitle . " - Rekå Resor"?></title>
  <meta name="theme-color" content="#0856fb">
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
<?php if (!DEBUG_MODE) { ?>
  <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TLG9DXN"
      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
  <!-- Facebook Pixel Code -->
    <noscript><img height="1" width="1" style="display:none"
      src="https://www.facebook.com/tr?id=957297874347023&ev=PageView&noscript=1"
      /></noscript>
  <!-- End Facebook Pixel Code -->
<?php } ?>




  <header class="text-center hidden-print" id="main-header">
    <div class="text-right header-wrap">
      <figure class="top-logo clear-on-tiny center-on-tiny">
        <a href="/"><img src="/img/logo.gif" alt="Rekå Resor AB"><span class="sr-only">Välkommen till Rekå Resor</span></a>
        <figcaption>- mer än 60 år av reko resor -</figcaption>
      </figure>
      <div id="main-navigation-toggle"><i class="fa fa-bars fa-2x" aria-hidden="true"></i></div>
      <nav class="clear-on-tiny hidden-collapsable-nav" id="main-navigation">
        <ul class="nav nav-pills center-on-tiny" aria-label="Meny">
          <li role="presentation"><a href="/#resekalender">Resekalender</a></li>
          <li role="presentation" class="hide-on-tiny-show-in-docked hidden-xs"><a href="/bestall-program">Beställ program</a></li>
          <li role="presentation" class="hide-on-tiny-show-in-docked hidden-xs"><a href="/inforresan">Inför resan</a></li>
          <li role="presentation" class="hide-on-tiny-show-in-docked hidden-xs"><a href="/efterresan">Efter resan</a></li>
          <li role="presentation" class="hide-on-tiny-show-in-docked hidden-xs"><a href="/galleri">Bildgalleri</a></li>
          <li role="presentation" class="hide-on-tiny-show-in-docked hidden-xs"><a href="/bussresorgoteborg">Mer om oss</a></li>
          <li role="presentation" class="hide-on-tiny-show-in-docked hidden-xs"><a href="https://sv-se.facebook.com/rekoresor/" aria-label="Besök oss på Facebook"><i class="fa fa-facebook-square" aria-hidden="true"></i><span class="sr-only">Facebook länk</span><span class="hidden-sm hidden-xs">&nbsp;Facebook</span></a></li>
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
