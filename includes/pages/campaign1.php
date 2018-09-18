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


try {

  $pageTitle = "Julbord – Rekå Resor";


  $morestyles = "<link rel='stylesheet' href='/css/static.min.css' >";

  $dataLayer = "{
    'pageTitle': 'Christmas Buffet',
    'visitorType': 'low-value',
    'product': false
    }";

  header('Content-type: text/html; charset=utf-8');
  include __DIR__ . '/shared/header.php';

  echo "<main class='main-section container-fluid'><div class='row-fluid'><div class='col-xs-12 col-lg-12'>";

  echo "<h1 class='mb-3'>Göteborgsnära Julbord 2018</h1>
  <p class='lead mx-2'>Julbord på trevliga restauranger där man får en fin julstämning inför den stundande högtiden.<br />
  Våra förslag nedan är på <strong>julbord med busstransport</strong>. För mer information om respektive läge klicka på länkarna.<br/>
  Priserna inkluderar bussresa till och från det ställe du själv väljer i Göteborg och julbord (exkl. dryck) med julgodis, glögg och annat som hör julen till.<br />
  Bokningstelefon: <a href='tel:+4631222120'>031 - 22 21 20</a> eller <a href='/kontakt'>kontaktformulär</a>.<br /></p>
  <div class='row-fluid'>
  <div class='col-lg-6 col-xs-12'>
  <table class='table table-hover table-responsive mr-2 mx-2'>
  <thead>
    <tr>
      <th scope='col'>Priser per person vid antal personer</th>
      <th scope='col' class='text-center'>20</th>
      <th scope='col' class='text-center'>25</th>
      <th scope='col' class='text-center'>30</th>
      <th scope='col' class='text-center'>35</th>
      <th scope='col' class='text-center'>40</th>
      <th scope='col' class='text-center'>45</th> 
      <th scope='col'>pers</th> 
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope='row'><a href='http://jordhammarsherrgard.se/' target='_blank' rel='noopener'>Jordhammars Herrgård, söndagar</a></th>
      <td class='text-right'>785</td><td class='text-right'>745</td><td class='text-right'>695</td><td class='text-right'>675</td><td class='text-right'>650</td><td class='text-right'>635</td><td>kr</td>
    </tr>
    <tr>
      <th scope='row'><a href='https://www.marstrands.se/restaurang/' target='_blank' rel='noopener'>Havshotellet Marstrand</a></th>
      <td class='text-right'>1.050</td><td class='text-right'>995</td><td class='text-right'>955</td><td class='text-right'>925</td><td class='text-right'>900</td><td class='text-right'>875</td><td>kr</td>
    </tr>
    <tr>
      <th scope='row'><a href='https://www.toftaherrgard.se/' target='_blank' rel='noopener'>Tofta Herrgård</a></th>
      <td class='text-right'>895</td><td class='text-right'>830</td><td class='text-right'>795</td><td class='text-right'>775</td><td class='text-right'>750</td><td class='text-right'>730</td><td>kr</td>
    </tr>
    <tr>
      <th scope='row'><a href='http://www.blomstermala.se/' target='_blank' rel='noopener'>Blomstermåla</a></th>
      <td class='text-right'>835</td><td class='text-right'>780</td><td class='text-right'>760</td><td class='text-right'>735</td><td class='text-right'>710</td><td class='text-right'>695</td><td>kr</td>
    </tr>
    <tr>
      <th scope='row'><a href='https://www.sarohus.se/' target='_blank' rel='noopener'>Säröhus</a></th>
      <td class='text-right'>890</td><td class='text-right'>830</td><td class='text-right'>795</td><td class='text-right'>775</td><td class='text-right'>750</td><td class='text-right'>730</td><td>kr</td>
    </tr>
    <tr>
      <th scope='row'><a href='https://hallsnas.se/' target='_blank' rel='noopener'>Hällsnäs</a></th>
      <td class='text-right'>895</td><td class='text-right'>865</td><td class='text-right'>830</td><td class='text-right'>810</td><td class='text-right'>785</td><td class='text-right'>765</td><td>kr</td>
    </tr>
    <tr>
      <th scope='row'><a href='http://www.tullhuset.se/' target='_blank' rel='noopener'>Tullhuset, Hönö</a></th>
      <td class='text-right'>895</td><td class='text-right'>830</td><td class='text-right'>795</td><td class='text-right'>775</td><td class='text-right'>750</td><td class='text-right'>730</td><td>kr</td>
    </tr>
    <tr>
      <th scope='row'><a href='https://www.aspenasherrgard.se/restaurang' target='_blank' rel='noopener'>Aspenäs Herrgård, Lerum</a></th>
      <td class='text-right'>795</td><td class='text-right'>765</td><td class='text-right'>735</td><td class='text-right'>710</td><td class='text-right'>690</td><td class='text-right'>675</td><td>kr</td>
    </tr>
    <tr>
      <th scope='row'><a href='http://ojersjomat.se/' target='_blank' rel='noopener'>Öjersjö Golf, Partille</a></th>
      <td class='text-right'>685</td><td class='text-right'>650</td><td class='text-right'>625</td><td class='text-right'>595</td><td class='text-right'>570</td><td class='text-right'>550</td><td>kr</td>
    </tr>

  
  </tbody>
  </table>
  </div>
  <div class='col-lg-6 col-xs-12'>
  <figure>
  <img class='img-thumbnail m-2' src='/../img/campaign/christmas-table-1909796_1280.jpg' alt='Julbord uppdukat'/>
  </figure>
  </div>
  </div>
  <div class='row-fluid'>
  <div class='col-lg-4 col-md-4 col-sm-4 col-xs-12 text-center mx-2'>
  <a class='btn btn-default action-btn' href='tel:+4631222120'>Boka på telefon 031 - 22 21 20</a>
  </div>
  <div class='col-lg-4 col-md-4 col-sm-4 col-xs-12 text-center mx-2'>
  <a class='btn btn-default action-btn' href='/kontakt'>Boka via kontaktformulär</a>
  </div>
  <div class='col-lg-4 col-md-4 col-sm-4 col-xs-12 text-center mx-2'>
  <a class='btn btn-default action-btn' href='http://www.rekoresor.se/upload/resor/julbord-november-december-2018/1_julbord-november-december-2018.pdf'>Ladda ner PDF-program</a>
  </div>
  </div>
  <p class='lead mx-2 text-center'><strong>Du reser med <a href='http://www.hisingebuss.se' target='_blank'>Hisinge Buss</a> helturistbussar och vi sköter bokningen till det ställe du väljer!</strong></p>
  <div class='row-fluid'>
  <div class='col-lg-6 col-xs-12 mx-2'>
  <figure>
  <img class='img-thumbnail m-2' src='/../img/campaign/franz-schekolin-797696-unsplash.jpg' alt='Julbord'/>
  </figure>
  </div>
  <div class='col-lg-6 col-xs-12 mx-2'>
  <figure>
  <img class='img-thumbnail m-2' src='/../img/campaign/20150815_142145.jpg' alt='Buss'/>
  </figure>
  </div>
  </div>
  ";

  echo "</div></div></main>";

  include __DIR__ . '/shared/footer.php';


} catch(\UnexpectedValueException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/404.php';
} catch(\RuntimeException $e) {
  if (DEBUG_MODE) echo $e->getMessage();
  include 'error/500.php';
}
