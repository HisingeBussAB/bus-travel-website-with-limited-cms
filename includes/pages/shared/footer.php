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

<link rel="stylesheet" href="/dependencies/font-awesome-4.7.0/css/font-awesome.min.css" >
<link rel="stylesheet" href="/css/main.min.css" >
<?php
if (!empty($morestyles)) echo $morestyles;
?>



<footer class="clearfix container main-footer text-center">





  <section class="text-center container-fluid clearfix">
    <div class="row-fluid">
      <section class='col-md-6 col-xs-12'>
        <h2 class='footer-heading'>Vill du ha information om våra resor?</h2>
        <form action='/ajax/newsletter' method='post' accept-charset='utf-8' enctype='application/json' id='newsletter-form'>
          <input type="email" name="email" placeholder="E-post..." />
          <input type="url" name="url" class="hidden" placeholder="Lämna tomt!" />
          <input type="hidden" name="client" value="<?php echo md5($_SERVER['HTTP_USER_AGENT']); ?>" />
          <button type="submit" id='newsletter-form-send'><span id='newsletter-form-send-default'>Prenumerera</span><span id='newsletter-loader'><i class="fa fa-spinner fa-lg fa-spin" aria-hidden="true"></i></span></button>
          <div id='newsletter-response' class='text-center'></div>
        </form>

      </section>
      <section class='col-md-6 col-xs-12'>
      <h2 class='footer-heading'>Företag i koncernen</h2>
      <div class='koncern-logo-container'>
          <a href="http://www.hisingebuss.se"><img src="/img/hb-logga.png" alt="Hisinge Buss AB" /></a>
          <a href="http://www.rekoresor.se"><img src="/img/reka-logga.jpg" alt="Rekå Resor AB" /></a>
          <a href="http://www.busspoolen.se"><img src="/img/bp-logga.png" alt="Buss- och Resepoolen i Göteborg AB" /></a>
      </div>
      </section>
    </div>
  </section>



  <address class='text-center'>
      <p>Telefon: <a rel="nofollow" href="tel:+4631222120" class="white-link">031-22 21 20</a> | E-post: <a rel="nofollow" href="mailto:info@rekoresor.se" class="white-link">info@rekoresor.se</a></p>
      <p>Aröds Industriväg 30, 422 43 Hisings Backa | Box 8797, 402 76 Göteborg</p>
  </address>

</footer>

<script src="/dependencies/jquery-3.1.1/jquery-3.1.1.min.js"></script>
<script src="/dependencies/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<script src="/js/main.js"></script>
<?php
if (!empty($morescripts)) echo $morescripts;
?>
</body>
</html>
