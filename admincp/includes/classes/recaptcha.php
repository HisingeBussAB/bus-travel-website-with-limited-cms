<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\admincp\includes\classes;

class reCaptcha
{
  public static function tryReCaptcha() {
    //START RECAPTCHA - Google snipplet
    $post_data = http_build_query(
      array(
        'secret' => RECAPTCHA_SECRET,
        'response' => $_POST['g-recaptcha-response'],
        'remoteip' => $_SERVER['REMOTE_ADDR']
      )
    );
    $opts = array('http' =>
      array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $post_data
      )
    );
    $context  = stream_context_create($opts);
    $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
    $result = json_decode($response);
    //END RECAPTCHA Google snipplet
    if (!$result->success) {
      return false;
    } else {
      return true;
    }
  }
}
