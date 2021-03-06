<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * Sub-router for ajax calls.
 */

namespace HisingeBussAB\RekoResor\website\ajax;

use HisingeBussAB\RekoResor\website as root;


  /**
   * @uses Sessions
   * @uses Login
   * @uses Logout
   * @uses ResetToken
   */
class Ajax
{
  public static function startAjax($request) {

    root\includes\classes\Sessions::secSessionStart(FALSE);

    /**
     * List of possible ajax calls to make and handlers
     * Works for a limited number of calls, this code is not ideal and has to be re-written with proper routing or otherwise to scale.
     * For the limited number of options we will be dealing this this saves development time.
     */
    switch ($request) {
      case 'admindologin':

        if (!root\admin\includes\classes\Login::setLogin()) {
          exit;
        } else {
          http_response_code(200);
          exit;
        }
      break;

      case 'gettoken':
        if (!empty($_POST['form'])) {
          $form = $_POST['form'];
        } else {
          $form = "form";
        }

        if (!empty($_POST['expiration'])) {
          $exp = $_POST['expiration'];
        } else {
          $exp = 5000;
        }

        if (!empty($_POST['unique'])) {
          $unique = $_POST['unique'];
        } else {
          $unique = true;
        }


        $token = root\includes\classes\Tokens::getFormToken($form, $exp, $unique);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: http' . APPEND_SSL . '://' .$_SERVER['HTTP_HOST']);


        //These two checks below aren't safe but that is still no reason to let sloppy written spambots thru.
        //All browsers do not set origin header on internal requests so we have to allow it to be empty also.

        if( stripos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === FALSE ) {
          echo 'Fel urspringssida.';
          http_response_code(403);
          exit;
        }

        if( !empty($_SERVER['HTTP_ORIGIN']) ) {
          if( stripos($_SERVER['HTTP_ORIGIN'], $_SERVER['HTTP_HOST']) === FALSE ) {
            echo 'Fel urspringssida.';
            http_response_code(403);
            exit;
          }
        }



        echo json_encode(array('token' => $token));
        http_response_code(200);
        exit;

      break;

      case 'program':

        root\includes\classes\ProgramForm::sendForm($_POST);
        exit;
      break;

      case 'contact':

        root\includes\classes\ContactForm::sendForm($_POST);
        exit;
      break;

      case 'booktour':

        root\includes\classes\BookTour::sendForm($_POST);
        exit;
      break;

      case 'newsletter':

        root\includes\classes\NewsletterForm::sendForm($_POST);
        exit;
      break;


      default:
        echo "Not found!";
        http_response_code(404);
        exit;


    }
  }

}
