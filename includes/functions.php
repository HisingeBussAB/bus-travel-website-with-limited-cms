<?php
//needs config.php included before

function sec_session_start() {
   $session_name = 'name';   // Set a custom session name
    /*Sets the session name.
     *This must come before session_set_cookie_params due to an undocumented bug/feature in PHP.
     */
    session_name($session_name);

    $secure = false;
    // This stops JavaScript being able to access the session id.
    $httponly = true;
    ini_set('session.use_only_cookies', 1);

    // Gets current cookies params.
    $currentCookieParams = session_get_cookie_params();
    session_set_cookie_params($currentCookieParams["lifetime"],
        $currentCookieParams["path"],
        $currentCookieParams["domain"],
        $secure,
        $httponly);

        echo $currentCookieParams["lifetime"];
        echo $currentCookieParams["path"];
        echo $currentCookieParams["domain"];

    session_start();            // Start the PHP session

    session_regenerate_id(true);    // regenerated the session, delete the old one.


print_r($_SESSION);

}
?>
