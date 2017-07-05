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
<footer>
  <section>
    <h1>Vill du ha information om våra resor?</h1>
    <form>
      <input type="email" placeholder="E-post...">
      <input type="submit" value="Prenumerera på vårt nyhetsbrev">
    </form>
    <div>Följ oss på Facebook</div>
  </section>
  <section>
    Telefon: 031-22 21 20 | E-post: info@rekoresor.se | Fler kontaktuppgifter
  </section>
</footer>
<script src="/dependencies/jquery-3.1.1/jquery-3.1.1.min.js"></script>
<script src="/dependencies/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<?php
if (!empty($morescripts)) echo $morescripts;
?>
</body>
</html>
