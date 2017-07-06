<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\includes\classes;

/*
uri-encode(string)
replaces å, ä, ö, space, & with a, a, o, -, och
Used before sanitize url when creating a link text, for cleaner and more SEO friendly uris
return: $string

br2nl(string)
replaces <br> and <br /> with \n
return: string

br2htmlnl(string)
replaces <br> and <br /> with &#10
return: string

get_string_between(haystack string, start string, end string)
Gets all substrings between an instance of start and end in haystack.
return: array of strings

get_img_files(string directory)
scandir for images, returned sorted numbered array with file info under each number as follows.
return: array(
  ['file'] filename string
  ['type'] = image type string
  ['thumb'] = tumbnail location (if a file named small_[file] in same folder else same as file)
)

get_pdf_files(string directory)
return: array of pdf file names in directory

*/

class Functions
{

  public static function uri_recode($string) {
    $string = strtolower(trim($string));
    $string = strip_tags($string);
    $search = array('å', 'ä', 'ö', ' ', '&', 'ü', 'é', 'è', '+');
    $replace = array('a', 'a', 'o', '-', 'och', 'u', 'e', 'e', '');
    $string = urlencode(str_replace($search,$replace,$string));
    return $string;
  }

  public static function br2nl($string) {
    return preg_replace('#<br\s*?/?>#i', "\n", $string);
  }

  public static function br2htmlnl($string) {
    return preg_replace('#<br\s*?/?>#i', "&#10;", $string);
  }

  public static function get_string_between($string, $start, $end) {
    $split_string       = array_filter(explode($start,$string));
    if (empty($split_string)) {
      return false;
    }
    foreach($split_string as $string) {
      $capture = substr($string,0,strpos($string,$end));
      if (!empty($capture)) {
        $return[] = $capture;
      }
    }
    if (!isset($return)) {
      $return[] = "";
    }
    return $return;
  }


  public static function get_img_files($dir) {

    $results = [];
    if (!is_dir($dir)) {
      return false;
    }

    if (!($files = scandir($dir))) {
      return false;
    }
    sort($files);
    $i = 0;
    foreach($files as $file) {
      if (substr($file, 0, 1) != ".") {
        $mime = mime_content_type($dir . $file);
        if ((strpos($mime, "image") !== FALSE) && (substr($file, 0, 6) !== "small_")) {
          $results[$i]['file'] = $file;
          $results[$i]['type'] = str_replace("image/", "", $mime);
          if (in_array("small_" . $file, $files)) {
            $results[$i]['thumb'] = "small_" . $file;
          } else {
            $results[$i]['thumb'] = $file;
          }
        }
      }
      $i++;
    }
    sort($results);
    return $results;
  }


  public static function get_pdf_files($dir) {

    $files = scandir($dir);
    foreach($files as $file) {
      if (substr($file, 0, 1) != ".") {
        if (strpos(mime_content_type($dir . $file), "pdf") !== FALSE) {
          $results[] = $file;
        }
      }
    }
    sort($results);
    return $results;
  }


  //DEBUGGING FUNCTIONS
  public static function render_var_dump($var, $desc = "VARIABLE") {
    echo "<pre>";
    echo "$desc: ";
    var_dump($var);
    echo "
    ";
    echo "<pre>";

  }

}
