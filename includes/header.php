<!DOCTYPE html>
<html lang="sv">
<head>
  <meta http-equiv="content-type" content="text/html;charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="author" content="Håkan K Arnoldson">
  <title>Rekå Resor - Bussresor i Norden och Europa</title>
  <meta property="og:site_name" content="Rekå Resor - Bussresor i Norden och Europa">
  <meta property="og:title" content="Rekå Resor - Bussresor i Norden och Europa">

  <meta property="og:url" content="http://www.rekoresor.se/">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="sv_SE">

  <meta name="description" content="Rekå Resor erbjuder bussresor inom Sverige och till hela Europa med utgångspunkt från Göteborg. Välkommen till en trevlig bussresa och ett spännande äventyr">
  <meta property="og:description" content="Rekå Resor erbjuder bussresor inom Sverige och till hela Europa med utgångspunkt från Göteborg. Välkommen till en trevlig bussresa och ett spännande äventyr">

  <meta name="robots" content="index, follow">

  <meta property="DC.date.issued" content="2017-02-27T22:45:59+01:00">

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


  <link rel="stylesheet" href="http<?php echo $append_ssl ?>://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link href="http<?php echo $append_ssl ?>://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link href="css/main.min.css" rel="stylesheet">

  <script src="http<?php echo $append_ssl ?>://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
  <script>window.jQuery || document.write('<script src="dependencies/jquery-3.1.1/jquery-3.1.1.min.js"><\/script>')</script>
  <script src="http<?php echo $append_ssl ?>://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  <script>
    if(typeof($.fn.modal) === 'undefined') {
      document.write('<script src="/dependencies/bootstrap-3.3.7-dist/js/bootstrap.min.js"><\/script>');
      $("head").prepend('<link rel="stylesheet" href="dependencies/bootstrap-3.3.7-dist/css/bootstrap.min.css" />');
      $("head").prepend('<link rel="stylesheet" href="dependencies/font-awesome-4.7.0/css/font-awesome.min.css" />');
    }
  </script>

</head>
<body>

  <header class="text-center">
    <div class="text-center header-wrap">
      <figure>
        <img src="/img/logo.gif" alt="Rekå Resor AB">
        <figcaption>- mer än 60 år av resor -</figcaption>
      </figure>
      <nav>
        <ul class="nav nav-pills">
          <li role="presentation"><a href="#">Hem</a></li>
          <li role="presentation"><a href="#">Beställ katalog</a></li>
          <li role="presentation"><a href="#">Inför resan</a></li>
          <li role="presentation"><a href="#">Bildgalleri</a></li>
          <li role="presentation"><a href="#">Om Rekå Resor</a></li>
          <li role="presentation"><a href="#">Kontakt</a></li>
          <li role="presentation"><a href="#" aria-label="Besök oss på Facebook"><i class="fa fa-facebook-square" aria-hidden="true"></i></a></li>
        </ul>
      </nav>
    </div>
    <!--TODO: LÄS KATEGORIER FRÅN DATABAS-->
    <div class="text-center categories-wrap">
      <div class="btn-group" role="group" aria-label="Resekategorier">
        <button type="button" class="btn btn-default">Dagsresor</button>
        <button type="button" class="btn btn-default">Opera</button>
        <button type="button" class="btn btn-default">Teater</button>
        <button type="button" class="btn btn-default">Marknader</button>
        <button type="button" class="btn btn-default">Storhelg</button>
        <button type="button" class="btn btn-default">Nöje &amp; dans</button>
        <button type="button" class="btn btn-default">Spa &amp; må bra</button>
        <button type="button" class="btn btn-default">Weekend</button>
        <button type="button" class="btn btn-default">Gruppresor</button>
      </div>
    </div>
    <div class="text-center categories-wrap">
      <ul aria-label="Resekategorier" id="resekategorier-test2">
        <li><a href="#">Dagsresor</a></li>
        <li><a href="#">Opera</a></li>
        <li><a href="#">Teater</a></li>
        <li><a href="#">Marknader</a></li>
        <li><a href="#">Storhelg</a></li>
        <li><a href="#">Nöje &amp; dans</a></li>
        <li><a href="#">Spa &amp; må bra</a></li>
        <li><a href="#">Weekend</a></li>
        <li><a href="#">Gruppresor</a></li>
      </ul>
    </div>
  </header>
