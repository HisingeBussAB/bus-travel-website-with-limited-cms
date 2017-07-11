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
<footer class="clearfix container main-footer">
  <section>
    <h2>Vill du ha information om våra resor?</h2>
    <form>
      <input type="email" placeholder="E-post...">
      <input type="submit" value="Prenumerera på vårt nyhetsbrev">
    </form>
    <div><a href="#">Följ oss på Facebook</a></div>
  </section>
  <div>
    Telefon: <a rel="nofollow" href="tel:+4631222120">031-22 21 20</a> | E-post: <a rel="nofollow" href="mailto:info@rekoresor.se">info@rekoresor.se</a> | <a href="/kontakt">Fler kontaktuppgifter</a>
  </div>
  <section class="text-center container-fluid">
    <h3>Företag i koncernen</h3>
    <figure class="koncern-logo col-md-4 col-xs-12 text-center">
      <img src="/img/hb-logga.png" alt="Hisinge Buss AB" />
    </figure>
    <figure class="koncern-logo col-md-4 col-xs-12 text-center">
      <img src="/img/reka-logga.jpg" alt="Rekå Resor AB" />
    </figure>
    <figure class="koncern-logo col-md-4 col-xs-12 text-center">
      <img src="/img/bp-logga.png" alt="Buss- och Resepoolen i Göteborg AB" />
    </figure>
  </section>
</footer>
<script src="/dependencies/jquery-3.1.1/jquery-3.1.1.min.js"></script>
<script src="/dependencies/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<script src="/js/main.js"></script>
<?php
if (!empty($morescripts)) echo $morescripts;
?>
</body>
</html>
