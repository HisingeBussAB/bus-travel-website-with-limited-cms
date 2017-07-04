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


  public static function getFormToken($form, $expiration = 7000, $onetimeuse = true) {
    $expiration = time() + $expiration;
    $tokenid = bin2hex(openssl_random_pseudo_bytes(4)) . microtime(true);
    $token = bin2hex(openssl_random_pseudo_bytes(16));
    $_SESSION["tokens"][$form][$tokenid] = array('token' => $token, 'unique' => $onetimeuse, 'expiration' => $expiration);
    return array('id' => $tokenid, 'token' => $token);
  }

  public static function checkFormToken($token, $tokenid, $form) {
    if (empty($_SESSION["tokens"][$form][$tokenid])) {
      return false;
    } else {
      $tokenarr = $_SESSION["tokens"][$form][$tokenid];
    }

    if ($tokenarr['expiration'] < time()) {
      unset($_SESSION["tokens"][$form][$tokenid]);
      return false;
    }

    if ($tokenarr['unique'] && ($tokenarr['token'] === $token)) {
      $_SESSION["tokens"][$form][$tokenid]['token'] = "destroyed";
      unset($_SESSION["tokens"][$form][$tokenid]);
    }

    return ($tokenarr['token'] === $token);
  }

  public static function getCommonToken($expiration = 7000, $new = false) {
    $expiration = time() + $expiration;
    if ($new  || empty($_SESSION["tokens"]["common"]) || (!empty($_SESSION["tokens"]["common"]) && count($_SESSION["tokens"]["common"])<1)) {
      $tokenid = bin2hex(openssl_random_pseudo_bytes(4)) . microtime(true);
      $token = bin2hex(openssl_random_pseudo_bytes(16));
      $_SESSION["tokens"]["common"][$tokenid] = array('token' => $token, 'expiration' => $expiration);
      return array('id' => $tokenid, 'token' => $token);
    } else {
      foreach ($_SESSION["tokens"]["common"] as $id=>$token) {
        //only increase expiration if is less
        if ($token['expiration'] < $expiration) {
          $_SESSION["tokens"]["common"][$id]['expiration'] = $expiration;
        }
        return array('id' => $id, 'token' => $token['token']);
      }
    }
  }

  public static function checkCommonToken($token, $tokenid) {
    if (empty($_SESSION["tokens"]["common"][$tokenid])) {
      return false;
    } else {
      $tokenarr = $_SESSION["tokens"]["common"][$tokenid];
    }

    if ($tokenarr['expiration'] < time()) {
      unset($_SESSION["tokens"]["common"][$tokenid]);
      return false;
    }

    return ($tokenarr['token'] === $token);
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
