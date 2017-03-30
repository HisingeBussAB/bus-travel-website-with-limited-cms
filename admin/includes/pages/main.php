<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * ADMIN
 *
 */

namespace HisingeBussAB\RekoResor\website\admin\includes\pages;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\admin as admin;


class Main {
  public static function showAdminMain() {
  root\includes\classes\Sessions::secSessionStart();
  if (admin\includes\classes\Login::isLoggedIn() !== TRUE) {
    admin\includes\classes\Login::renderLoginForm();
  } else {
    $pageTitle = "Admin Huvudmeny";
    ?>
    <!DOCTYPE html>
    <html lang="sv">
    <head>
      <meta http-equiv="content-type" content="text/html;charset=utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta http-equiv="x-ua-compatible" content="ie=edge">
      <meta name="author" content="Håkan K Arnoldson">
      <title>Rekå Resor - <?php echo $pageTitle ?></title>

      <meta name="robots" content="noindex, nofollow">

      <link rel="icon" href="/favicon/favicon.ico">

            <link rel="stylesheet" href="http<?php echo APPEND_SSL ?>://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
      <link href="http<?php echo APPEND_SSL ?>://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
      <link href="/css/main.min.css" rel="stylesheet">

      <script src="http<?php echo APPEND_SSL ?>://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="/dependencies/jquery-3.1.1/jquery-3.1.1.min.js"><\/script>')</script>
      <script src="http<?php echo APPEND_SSL ?>://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
      <script>
        if(typeof($.fn.modal) === 'undefined') {
          document.write('<script src="/dependencies/bootstrap-3.3.7-dist/js/bootstrap.min.js"><\/script>');
          $("head").prepend('<link rel="stylesheet" href="/dependencies/bootstrap-3.3.7-dist/css/bootstrap.min.css" />');
          $("head").prepend('<link rel="stylesheet" href="/dependencies/font-awesome-4.7.0/css/font-awesome.min.css" />');
        }
      </script>

    </head>
    <body>

      <header class="text-center">
        <div class="text-right header-wrap">
          <figure>
            <a href="/"><img src="/img/logo.gif" alt="Rekå Resor AB"></a>
            <figcaption>- mer än 60 år av resor -</figcaption>
          </figure>
          <nav>
            <ul class="nav nav-pills" aria-label="Meny">
              <li role="presentation"><a href="/">Hem</a></li>
              <li role="presentation"><a href="/bestallkatalog">Beställ katalog</a></li>
              <li role="presentation"><a href="/inforresan">Inför resan</a></li>
              <li role="presentation"><a href="/galleri">Bildgalleri</a></li>
              <li role="presentation"><a href="/bussresorgoteborg">Om Rekå Resor</a></li>
              <li role="presentation"><a href="/kontaktarekaresor">Kontakt</a></li>
              <li role="presentation"><a href="https://sv-se.facebook.com/rekoresor/" aria-label="Besök oss på Facebook"><i class="fa fa-facebook-square" aria-hidden="true"></i></a></li>
            </ul>
          </nav>
        </div>
        <ul aria-label="Resekategorier" id="categories-wrap">
          <li><a href="/kategori/dagsresor">Dagsresor</a></li>
          <li><a href="/kategori/operaresor">Opera</a></li>
          <li><a href="/kategori/teaterresor">Teater</a></li>
          <li><a href="/kategori/marknader">Marknader</a></li>
          <li><a href="/kategori/storhelg">Storhelg</a></li>
          <li><a href="/kategori/noje">Nöje &amp; dans</a></li>
          <li><a href="/kategori/sparesor">Spa &amp; må bra</a></li>
          <li><a href="/kategori/weekendresor">Weekend</a></li>
          <li><a href="/kategori/gruppresor">Gruppresor</a></li>
        </ul>
      </header>


    <?php

  }
  }
}
