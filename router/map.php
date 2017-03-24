<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\router;

final class Map
{

  public static function gMap() {

  $map = [
   'admin'              => [
     ''              => function() {\HisingeBussAB\RekoResor\website\admincp\admin::startAdmin();}],

   'resa'               => function() {include __DIR__ . '/../includes/pages/showtrip.php';},
   'galleri'            => function() {include __DIR__ . '/../includes/pages/showgallery.php';},
   'kategori'           => [
    ''                    => function() {include __DIR__ . '/../includes/pages/showcategory.php';},
    'any'                 => function() {include __DIR__ . '/../includes/pages/any.php';},],
   ''               => [
     ''               => function() {include __DIR__ . '/../includes/pages/mainpage.php';},
     'bestallkatalog'     => function() {include __DIR__ . '/../includes/pages/showorderinfo.php';},
     'inforresan'         => function() {include __DIR__ . '/../includes/pages/showbeforetrip.php';},
     'bussresorgoteborg'  => function() {include __DIR__ . '/../includes/pages/showabout.php';},
     'kontaktarekaresor'  => function() {include __DIR__ . '/../includes/pages/showcontact.php';}],
   'ajax'               => function() {include __DIR__ . '/../ajax/ajax.php';},
   'test'               => [
    ''                => function() {include __DIR__ . '/../tests/test.php';},
    'test1sub'            => function() {include __DIR__ . '/../tests/testsub.php';},
    'test2'               => [
      ''                  => function() {include __DIR__ . '/../tests/test2.php';},
      'test2sub'              => function() {include __DIR__ . '/../tests/test2sub.php';},
        'test3'                 => [
          ''                  => function() {include __DIR__ . '/../tests/test3.php';},
          'test3sub'              => function() {include __DIR__ . '/../tests/test3sub.php';}
        ]
      ]
    ]
  ];
  return $map;
  }
}
