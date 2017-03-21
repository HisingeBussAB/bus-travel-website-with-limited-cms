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
        echo "För många försök. Prova senare HTTP 403.";
        http_response_code(403);
        return false;
        exit;
      }





    } else {
      echo "Felformaterad förfrågan HTTP 403.";
      http_response_code(403);
      return false;
      exit;
    }

  }
}
