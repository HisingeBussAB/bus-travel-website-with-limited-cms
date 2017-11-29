<?php

namespace HisingeBussAB\RekoResor\website\includes\pages;
use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\Functions;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

 //Get dynamic content for this include
 $categories = root\admin\includes\classes\Categories::getActiveCategories(true);
 if ($categories !== false) {
   $categories = json_decode($categories);

   $allowed_tags = ALLOWED_HTML_TAGS;
   $html_ents = Functions::set_html_list();
 }


?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta http-equiv="content-type" content="text/html;charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name='description' content='Rekå Resor erbjuder bussresor inom Sverige och till hela Europa med utgångspunkt från Göteborg. Välkommen till en trevlig bussresa och ett spännande äventyr.' />
  <meta name='robots' content='noindex, nofollow'>
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
  </script>
  <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-TLG9DXN');</script>
  <!-- End Google Tag Manager -->

  <meta name="author" content="Håkan K Arnoldson">
  <title>Facebook - Rekå Resor</title>

  <meta property="og:type" content="website">
  <meta property="og:locale" content="sv_SE">
  <meta property="og:site_name" content="Rekå Resor" />

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
  <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TLG9DXN"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->


  <header class="text-center hidden-print">
    <!--<div class="text-right header-wrap">
      <figure class="top-logo clear-on-tiny center-on-tiny">
        <a href="/"><img src="/img/logo.gif" alt="Rekå Resor AB"><span class="sr-only">Välkommen till Rekå Resor</span></a>
        <figcaption>- mer än 60 år av reko resor -</figcaption>
      </figure>
      <nav class="clear-on-tiny">
        <ul class="nav nav-pills center-on-tiny" aria-label="Meny">
          <li role="presentation"><a href="/#resekalender">Resekalender</a></li>
          <li role="presentation"><a href="/bestall-program">Beställ program</a></li>
          <li role="presentation"><a href="/inforresan">Inför resan</a></li>
          <li role="presentation"><a href="/efterresan">Efter resan</a></li>
          <li role="presentation"><a href="/galleri">Bildgalleri</a></li>
          <li role="presentation"><a href="/bussresorgoteborg">Mer om oss</a></li>
          <li role="presentation"><a href="https://sv-se.facebook.com/rekoresor/" aria-label="Besök oss på Facebook"><i class="fa fa-facebook-square" aria-hidden="true"></i><span class="sr-only">Facebook länk</span><span class="hidden-sm hidden-xs">&nbsp;Facebook</span></a></li>
          <li role="presentation" class="hidden-xs"><a href="/kontakt">Kontakt</a></li>
          <li role="presentation" class="visible-xs-block"><a rel="nofollow" href="tel:+4631222120"><i class="fa fa-phone" aria-hidden="true"></i><span class="sr-only">Telefon</span>&nbsp;Ring oss</a></li>
        </ul>
      </nav>
    </div>-->
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


<?php
try {
  $pdo = DB::get();

  $sql = "SELECT resor.id, resor.utvald, resor.seo_description, resor.namn, resor.url, resor.bildkatalog, resor.antaldagar, resor.ingress,resor.pris, datum.datum AS datum FROM " . TABLE_PREFIX . "resor AS resor
          LEFT OUTER JOIN " . TABLE_PREFIX . "datum AS datum ON resor.id = datum.resa_id
          LEFT OUTER JOIN " . TABLE_PREFIX . "kategorier_resor AS k_r ON resor.id = k_r.resa_id
          LEFT OUTER JOIN " . TABLE_PREFIX . "kategorier AS kategorier ON kategorier.id = k_r.kategorier_id
          WHERE kategorier.kategori != 'gruppresor' AND resor.aktiv = 1 AND datum > NOW()
          GROUP BY datum
          ORDER BY datum;";



    $sth = $pdo->prepare($sql);
    $sth->execute();
    $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    $errorType = "Databasfel";
    throw new \RuntimeException("Databasfel vid laddning av resor.");
  }



  $tours = [];
  $usedtours = [];

  $i=0;
  foreach($result as $tour) {

    $tours[$i]['tour'] = strtr(strip_tags($tour['namn'], $allowed_tags), $html_ents);
    $tours[$i]['link'] = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/resa/". str_replace("'", "", $tour['url']), FILTER_SANITIZE_URL);
    $tours[$i]['days'] = strtr(strip_tags($tour['antaldagar'], $allowed_tags), $html_ents);
    $tours[$i]['summary'] = Functions::linksaver(strtr(nl2br(strip_tags($tour['ingress'], $allowed_tags)), $html_ents));
    $tours[$i]['price'] = number_format(filter_var($tour['pris'], FILTER_SANITIZE_NUMBER_INT), 0, ",", " ");
    $tours[$i]['departure'] = strtr(strip_tags($tour['datum'], $allowed_tags), $html_ents);

    $server_path = __DIR__ . '/../../upload/resor/' . filter_var($tour['bildkatalog'], FILTER_SANITIZE_URL) . '/';
    $web_path = filter_var("http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/" . $tour['bildkatalog'] . "/", FILTER_SANITIZE_URL);
      if ($files = Functions::get_img_files($server_path)) {
        $tours[$i]['imgsrc'] = $web_path . $files[0]['thumb'];
      } else {
        filter_var($tours[$i]['imgsrc'] = "http" . APPEND_SSL . "://" . $_SERVER['SERVER_NAME'] . "/upload/resor/generic/small_1_generic.jpg", FILTER_SANITIZE_URL);
      }
    $i++;
  }


?>

<main class="main-section clearfix container-fluid">
  <div class="row-fluid">
      <h2 class='col-md-12' id='resekalender'>Resekalender</h2>
    </div>
    <div class="row-fluid">

    <?php

      $i = 0;
      $lenght = count($tours);
      foreach ($tours as $tour) {
        $output = "";
        if ($i % 2 == 0) { $output .= "<div class='row-fluid'>"; }
        $output .=  "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12 tour-box'>";


        $output .= "<div class='tour-quick-facts'><h3><a href='" . $tour['link'] . "'>" . $tour['tour'] . "</a></h3>";
        $output .= "<p><i class='fa fa-hourglass fa-lg blue' aria-hidden='true'></i> Antal dagar: ";
        if ($tour['days'] == 1) {
          $output .= "Dagsresa";
        } else {
          $output .= $tour['days'] . " dagar</p>";
        }
        $output .= "<p><i class='fa fa-calendar fa-lg blue' aria-hidden='true'></i> Avresedatum: " . $tour['departure'] . "</p>";
        $output .= "<p><i class='fa fa-money fa-lg blue' aria-hidden='true'></i> Pris per person: " . $tour['price'] . " kr</p></div>";
        $output .= "<a href='" . $tour['link'] . "'><figure class='trip-featured-img-list'>";
        $output .= "<img class='lazy' src='" . $tour['imgsrc'] . "'  alt='" . $tour['tour'] . "'/> ";
        $output .= "</figure></a>";
        $output .= "<div class='tour-summary'>" . $tour['summary'] . "</div></div>";
        if ($i % 2 != 0) { $output .= "</div>"; }
        elseif ($i+1 >= $lenght) { $output .= "</div>"; }

        echo $output;
        $i++;
    }
    ?>
    </div>
  </main>
<?php
include __DIR__ . '/shared/footer.php';
