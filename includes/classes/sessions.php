<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * (c) Rekå Resor AB
 *
 * @link      https://github.com/HisingeBussAB/bus-travel-website-with-limited-cms
 * @copyright CC BY-SA 4.0 (http://creativecommons.org/licenses/by-sa/4.0/)
 * @license   GNU General Public License v3.0
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\includes\classes;

class Sessions
{
  public static function secSessionStart() {
    /**
     * Function initalizes a more secure session handler
     */
    $session_name = '_RRSESSID';   // Set a custom session name
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
