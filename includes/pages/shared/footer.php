<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * (c) Rekå Resor AB
 *
 * Footer HTML for standard pages.
 *
 * @link      https://github.com/HisingeBussAB/bus-travel-website-with-limited-cms
 * @author    Håkan Arnoldson
 */ ?>

<link rel="stylesheet" href="/css/main.min.css" >
<?php
if (!empty($morestyles)) echo $morestyles;
?>



<footer class="clearfix container main-footer">
  <section>
    <h2 class='footer-heading'>Vill du ha information om våra resor?</h2>
    <form>
      <input type="email" placeholder="E-post...">
      <input type="submit" value="Prenumerera på vårt nyhetsbrev">
    </form>
    <div><a href="#">Följ oss på Facebook</a></div>
  </section>
  <address>
    Telefon: <a rel="nofollow" href="tel:+4631222120">031-22 21 20</a> | E-post: <a rel="nofollow" href="mailto:info@rekoresor.se">info@rekoresor.se</a><br>
    Aröds Industriväg 30, 422 43 Hisings Backa | Box 8797, 402 76 Göteborg
  </address>


  <section class="text-center container-fluid">
    <div class="row-fluid">
      <h2 class='footer-heading col-md-12'>Företag i koncernen</h2>
    </div>
    <div class="row-fluid">
      <figure class="koncern-logo text-center">
        <img src="/img/hb-logga.png" alt="Hisinge Buss AB" />
        <span class='sr-only'>Hisinge Buss AB</span>
      </figure>
      <figure class="koncern-logo text-center">
        <img src="/img/reka-logga.jpg" alt="Rekå Resor AB" />
        <span class='sr-only'>Rekå Resor AB</span>
      </figure>
      <figure class="koncern-logo text-center">
        <img src="/img/bp-logga.png" alt="Buss- och Resepoolen i Göteborg AB" />
        <span class='sr-only'>Buss- och Resepoolen i Göteborg AB</span>
      </figure>
    </div>
  </section>

</footer>

<link rel="stylesheet" href="/dependencies/font-awesome-4.7.0/css/font-awesome.min.css" >
<script src="/dependencies/jquery-3.1.1/jquery-3.1.1.min.js"></script>
<script src="/dependencies/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<script src="/js/main.js"></script>
<?php
if (!empty($morescripts)) echo $morescripts;
?>
</body>
</html>
