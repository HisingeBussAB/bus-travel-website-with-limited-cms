<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\includes\classes;

class ResetToken
{
  /**
   * getRandomToken
   *
   * Function returns a random token string
   *
   * @return string
   */
  public static function getRandomToken() {
    $token = bin2hex(openssl_random_pseudo_bytes(32));
    $_SESSION["token"] = $token;
    return $token;
  }
}
