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
}
