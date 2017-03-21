<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\includes\classes;

class DBException
{
  public static function getMessage($e, $class, $sql='NO QUERY') {
    if (DEBUG_MODE) {
      echo "<p>Databasfel från " . $class . ": " . $e->getMessage();
      echo "\n<br>SQL:" . $sql . "</p>";
    } else {
      echo "<p>Databasen svarar inte. Kontakta <a href=\"mailto:webmaster@rekoresor.se\">webmaster@rekoresor.se</a></p>\n";
    }
  }
}
