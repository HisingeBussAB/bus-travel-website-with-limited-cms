<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\includes\classes;

/*
  Token storage
  $_SESSION["tokens"]["form"]["token"]["expiration"]  //This token type is used to prevent multiple form submission
  $_SESSION["tokens"]["common"]["expiration"]         //Basic CSRF prevention token for anonymous sessions. Session wide but several tokens can be created.
  Since these token handlers need to exist anyway for the open pages they are also used in the administrator section to give some extra control of form submissions.
  */

class Tokens
{


  public static function setFormToken($form, $onetimeuse = true, $expiration = 7500) {
    $expiration = time() + $expiration;
    $tokenname = bin2hex(openssl_random_pseudo_bytes(4)) . microtime(true);
    $token = bin2hex(openssl_random_pseudo_bytes(16));
    $_SESSION["tokens"][$form][$tokenname] = array('token' => $token, 'unique' => $onetimeuse, 'expiration' => $expiration);
    return array('id' => $tokenname, 'token' => $token);
  }

  public static function getFormToken($tokenname, $form) {
    $_SESSION["tokens"][] = $token;
    return $tokenname[$token];
  }

  public static function setCommonToken($expiration = 7500) {
    $expiration = time() + $expiration;
    $tokenname = bin2hex(openssl_random_pseudo_bytes(4)) . microtime(true);
    $token = bin2hex(openssl_random_pseudo_bytes(16));
    $_SESSION["tokens"]["common"][$tokenname] = array('token' => $token, 'expiration' => $expiration);
    return array('id' => $tokenname, 'token' => $token);
  }

  public static function getCommonToken($tokenname, $expiration = 7500) {
    $token = bin2hex(openssl_random_pseudo_bytes(32));
    $_SESSION["token"] = $token;
    return $tokenname[$token];
  }


  //clear all expired token variables
  public static function cleanTokens() {

    foreach ($_SESSION["tokens"] as $form => $tokens) {
      foreach ($tokens as $tokenkey => $token) {
        if ($token["expiration"] < time()) {
          unset($_SESSION["tokens"][$form][$tokenkey]);
        }
      }
    }
  }

}
