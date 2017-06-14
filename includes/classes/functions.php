<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\includes\classes;

class Functions
{

  public static function uri_recode($string) {
    $string = strtolower(trim($string));
    $search = array('å', 'ä', 'ö', ' ', '&');
    $replace = array('a', 'a', 'o', '-', 'och');
    $string = urlencode(str_replace($search,$replace,$string));
    return $string;
  }

  public static function br2nl($string) {
    return preg_replace('#<br\s*?/?>#i', "\n", $string);
  }

  public static function get_string_between($string, $start, $end){
    $split_string       = array_filter(explode($start,$string));
    if (empty($split_string)) {
      return  false;
    }
    foreach($split_string as $string) {
      if (!empty($string))
      {
        $capture = substr($string,0,strpos($string,$end));
        if (!empty($capture)) {
          $return[] = $capture;
        }
      }
    }
    return $return;
  }

}
