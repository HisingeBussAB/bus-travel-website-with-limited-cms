<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\admincp\includes\classes;

class Login
{
  public static function isLoggedIn() {
    return false;
  }

  public static function renderLoginForm() {
    include __DIR__ . '/../pages/loginForm.php';
  }


}
