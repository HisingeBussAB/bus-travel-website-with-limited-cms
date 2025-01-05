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

namespace HisingeBussAB\RekoResor\website\includes\pages;

use HisingeBussAB\RekoResor\website as root;

use HisingeBussAB\RekoResor\website\includes\classes\Functions as functions;

use HisingeBussAB\RekoResor\website\includes\classes\DB;

use HisingeBussAB\RekoResor\website\includes\classes\DBError;





try {



  $allowed_tags = ALLOWED_HTML_TAGS;



  root\includes\classes\Sessions::secSessionStart(TRUE);

  $token = root\includes\classes\Tokens::getFormToken('contact', 4000, true);

  $clienthash = md5($_SERVER['HTTP_USER_AGENT']);



  $pageTitle = "Kontakta oss";





  $morestyles = "<link rel='stylesheet' href='/css/contact.min.css' >";

  $morescripts = "<script src='/js/contact.js'></script>";



  $dataLayer = "{

    'pageTitle': 'Contact',

    'visitorType': 'low-value',

    'product': false

    }";



  header('Content-type: text/html; charset=utf-8');

  include __DIR__ . '/shared/header.php';



  echo "<main class='main-section container-fluid'>

          <div class='row-fluid'>

            <div class='col-xs-12'>

              <h1>Kontakta oss</h1>

              <iframe id='embedded-map' frameborder='0' src='https://www.google.com/maps/embed/v1/place?key=" . GMAPS_API_KEY . "&q=Rekå+Resor,Grimboåsen+14' allowfullscreen></iframe>

            </div>

          </div>

        <form action='/ajax/contact' method='post' accept-charset='utf-8' enctype='application/json' id='get-contact-form'>

          <div class='row-fluid'>

            <div class='col-xs-12'>

              <div class='row-fluid'>

                <div class='col-md-6 col-sm-12'>



                  <h2>Skicka meddelande</h2>



                  <p><input type='text' placeholder='Namn' name='name' /></p>

                  <p><input type='text' placeholder='Gatuadress' name='address' /></p>

                  <p><input type='text' placeholder='Postnr.' name='zip /><input type='text' placeholder='Postort' name='city' /></p>

                  <p><input type='tel' placeholder='Telefonnummer' name='tel' /></p>

                  <p><input type='email' placeholder='E-post' name='email' /></p>

                  <input type='hidden' value='" . $token['id'] . "' name='tokenid' id='tokenid' />

                  <input type='hidden' value='" . $token['token'] . "' name='token' id='token' />

                  <input type='hidden' value='$clienthash' name='client' />

                  <p class='antispam'>Leave this empty: <input type='text' name='url' /></p>

                  <p><textarea placeholder='Ditt meddelande...' id='contact-text' name='message' required></textarea>



                  <p><input type='submit' value='Skicka meddelande' id='get-contact-button' /><button class='ajax-loader'><i class='fa fa-spinner fa-pulse fa-2x' aria-hidden='true'></i></button></p>

                  <div class='ajax-response' id='ajax-response'></div>





                </div>

                <div class='col-md-6 col-sm-12'>



                  <h4>Telefon</h4>

                  <p><a href='tel:+463122120'>031-22 21 20</a><br />

                  <h5>Jourtelefon</h5>

                  <p>Ibland kan det oförutsedda hända.

                  Något som gör att du måste få tag på oss när kontoret är stängt.

                  Då når du oss lättast genom vår jourtelefon via vår växel 031-22 21 20.</p></p>



                  <h4>Öppettider</h4>

                  <p>Vardagar 09:00 - 16:30<br />

                  Lördagar och Söndagar stängt</p>



                        <h4>Postadress</h4>

                        <p>Rekå Resor AB<br />

                        Box 8797<br />

                        402 76 Göteborg</p>



                </div>

              </div>

              <div id='recaptcha-body' class='g-recaptcha'

                data-sitekey='" . INV_RECAPTCHA_PUBLIC . "'

                data-callback='onVerifyForm'

                data-size='invisible'

                data-badge='inline'></div>

            </div>

          </div>

        </form>";

  echo "</main>";



  include __DIR__ . '/shared/footer.php';





} catch(\UnexpectedValueException $e) {

  if (DEBUG_MODE) echo $e->getMessage();

  include 'error/404.php';

} catch(\RuntimeException $e) {

  if (DEBUG_MODE) echo $e->getMessage();

  include 'error/500.php';

}

