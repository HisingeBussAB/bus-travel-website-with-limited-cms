<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\router;

class Map
{

  const map = [
   'admin'              => [
     ''              => __DIR__ . '/../admincp/admin.php'],

   'resa'               => __DIR__ . '/../includes/pages/showtrip.php',
   'galleri'            => __DIR__ . '/../includes/pages/showgallery.php',
   'kategori'           => __DIR__ . '/../includes/pages/showcategory.php',
   'root'               => [
     'root'               => __DIR__ . '/../includes/pages/mainpage.php',
     'bestallkatalog'     => __DIR__ . '/../includes/pages/showorderinfo.php',
     'inforresan'         => __DIR__ . '/../includes/pages/showbeforetrip.php',
     'bussresorgoteborg'  => __DIR__ . '/../includes/pages/showabout.php',
     'kontaktarekaresor'  => __DIR__ . '/../includes/pages/showcontact.php'],
   'ajax'               => __DIR__ . '/../ajax/ajax.php',
   'test'               => [
    'root'                => __DIR__ . '/../tests/test.php',
    'test1sub'            => __DIR__ . '/../tests/testsub.php',
    'test2'               => [
      'root'                  => __DIR__ . '/../tests/test2.php',
      'test2sub'              => __DIR__ . '/../tests/test2sub.php',
        'test3'                 => [
          'root'                  => __DIR__ . '/../tests/test3.php',
          'test3sub'              => __DIR__ . '/../tests/test3sub.php'
        ]
      ]
    ]
  ];

  public static function mapSite() {
    
  }
}
