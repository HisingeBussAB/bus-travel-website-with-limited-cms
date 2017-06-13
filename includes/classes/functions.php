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
    $split_string       = explode($end,$string);
    echo "<br><br>";
    echo "split_string:<br>";
    var_dump($split_string);
    foreach($split_string as $data) {
      echo "<br><br>";
      echo "data:<br>";
         $str_pos       = strpos($data,$start);
         $last_pos      = strlen($data);
         $capture_len   = $last_pos - $str_pos;
         $return[]      = substr($data,$str_pos+1,$capture_len);
    }
    return $return;
  }
}
