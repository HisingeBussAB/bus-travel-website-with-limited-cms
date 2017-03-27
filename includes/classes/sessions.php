<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\includes\classes;

class Sessions
{

  /**
   * Overrides default settings for session_start() to be a little more secure.
   * @uses HTTPS
   */
  public static function secSessionStart() {

    $session_name = 'RRSESSID';   // Set a custom session name
    /*Sets the session name.
     *This must come before session_set_cookie_params due to an undocumented bug/feature in PHP.
     */
    session_name($session_name);
    ini_set('session.use_only_cookies', 1);
    $currentCookieParams = session_get_cookie_params();

    session_set_cookie_params($currentCookieParams["lifetime"],
      $currentCookieParams["path"],
      $currentCookieParams["domain"],
      HTTPS,
      true);

    session_start();
    session_regenerate_id(true);
  }
}
