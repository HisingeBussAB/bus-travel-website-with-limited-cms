<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\admincp\includes\classes;

use HisingeBussAB\RekoResor\website as root;

class Login
{
  public static function isLoggedIn() {
    return false;
  }

  public static function renderLoginForm() {
    include __DIR__ . '/../pages/loginForm.php';
  }

  public static function setLogin() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      if (root\includes\classes\HammerGuard::hammerGuard($_SERVER['REMOTE_ADDR']) === true) {
        echo "För många försök. Prova senare HTTP 406.";
        http_response_code(406);
        return false;
        exit;
      }

      if ($_SESSION['TOKEN'] !== $_POST['token']) {
        echo "Fel token skickad. HTTP 401.";
        http_response_code(401);
        return false;
        exit;
      }






    } else {
      echo "Felformaterad förfrågan HTTP 405.";
      http_response_code(405);
      return false;
      exit;
    }

  }
}
