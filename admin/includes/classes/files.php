<?php

/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\admin\includes\classes;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

class Files {

  public static function uploadfile($allowreduce = TRUE) {

    //FIX TO SET ALLOW REDUCE FROM FORM LOC LATER

    $img_exts = [
      'jpg',
      'png',
      'gif'
    ];

    root\includes\classes\Sessions::secSessionStart(FALSE);
    //code mostly copied from http://php.net/manual/en/features.file-upload.php
    header('Content-Type: text/plain; charset=utf-8');

    if (root\admin\includes\classes\Login::isLoggedIn(FALSE) === TRUE) {
      if (!root\includes\classes\Tokens::checkFormToken(trim($_POST['token']),trim($_POST['tokenid']),"ultoken")) {
        echo "Felaktig token. Prova <a href='javascript:window.location.href=window.location.href'>ladda om</a> sidan.</p>";
        http_response_code(401);
        exit;
      }

      $id = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
      $pos = filter_var(trim($_POST['position']), FILTER_SANITIZE_NUMBER_INT);
      //Get the upload folder from DB
      try {
        $pdo = DB::get();
        $sql = "SELECT bildkatalog FROM " . TABLE_PREFIX . "resor WHERE id = :id;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetch(\PDO::FETCH_ASSOC);
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql);
      }

      try {

        if (!empty($result)) {
          $dir = $result['bildkatalog'];
        } else {
          throw new \RuntimeException('Relaterad resa hittades inte.');
        }



        // Undefined | Multiple Files | $_FILES Corruption Attack
        // If this request falls under any of them, treat it invalid.
        if (
            !isset($_FILES['upfile']['error']) ||
            is_array($_FILES['upfile']['error'])
        ) {
            throw new \RuntimeException('Ogiltig eller ingen fil skickad.');
        }

        // Check $_FILES['upfile']['error'] value.
        switch ($_FILES['upfile']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new \RuntimeException('Ingen fil skickad.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new \RuntimeException('Filen är för stor.');
            default:
                throw new \RuntimeException('Okänt serverfel.');
        }

        // You should also check filesize here.
        if ($_FILES['upfile']['size'] > 6000000) {
          throw new \RuntimeException('Filen kan inte vara större än 6MB.');
        }

        // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
        // Check MIME Type by yourself.

        /* TODO rewrite when access to live server
        $finfo = finfo(FILEINFO_MIME_TYPE)
        if (false === $ext = array_search(
            $finfo->file($_FILES['upfile']['tmp_name']),
            array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'pdf' => 'application/pdf'
            ),
            true
        )) {
            throw new \RuntimeException('Det går bara att ladda upp .jpg .png eller .gif bilder, samt .pdf filer.');
        }
        */
        $mimet = array(
          'jpg' => 'image/jpeg',
          'png' => 'image/png',
          'gif' => 'image/gif',
          'pdf' => 'application/pdf'
        );

        $ext= array_search($_FILES['upfile']["type"], $mimet);

        if (empty($ext)){
          throw new \RuntimeException('Det går bara att ladda upp .jpg .png eller .gif bilder, samt .pdf filer.');
        }



        //Make directory if not alredy exists
        if (!file_exists("./upload/resor/" . $dir)) {
          if (!mkdir("./upload/resor/" . $dir, 0755, true)) {
            throw new \RuntimeException('Misslyckades med att skapa bildkatalog.');
          }
        }

        //Check if the image file exists already with another extension and delete
        if ($ext !== "pdf") {
          foreach ($img_exts as $img_ext) {
            if (file_exists("./upload/resor/" . $dir . "/" .$pos . "_" . $dir . "." . $img_ext)) {
              if (!unlink("./upload/resor/" . $dir . "/" .$pos . "_" . $dir . "." . $img_ext)) {
                throw new \RuntimeException('Misslyckades med att radera tidigare bild.');
              }
            }
          }
        }




        // You should name it uniquely.
        // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
        // On this example, obtain safe unique name from its binary data.


        if (!move_uploaded_file(
            $_FILES['upfile']['tmp_name'],
            sprintf("./upload/resor/" . $dir . "/%s.%s",
                $pos . "_" . $dir,
                $ext
            )
        )) {
            throw new \RuntimeException('Lyckades inte spara uppladdad fil.');
        }

        if ($ext != "pdf") {
          //Reduce and or create thumbnail if file is large $source_img = 'source.jpg';
          $imagesize = getimagesize($file = "./upload/resor/" . $dir . "/" .$pos . "_" . $dir . "." . $ext);

          if ($imagesize[1] > 300) {
            if (!self::smart_resize_image('./upload/resor/' . $dir . '/' .$pos . '_' . $dir . '.' . $ext, './upload/resor/' . $dir . '/small_' . $pos . '_' . $dir . ".$ext",0,300)) {
              throw new \RuntimeException('Lyckades inte skapa thumbnail till bilden. Förminskning misslyckades.');
            }
          } else {
            if (!copy($file = "./upload/resor/" . $dir . "/" .$pos . "_" . $dir . "." . $ext, $output = "./upload/resor/" . $dir . "/small_" .$pos . "_" . $dir . "." . $ext)) {
              throw new \RuntimeException('Lyckades inte skapa thumbnail till bilden. Kopiering misslyckades.');
            }
          }

          //Reduce if allowed
          if ($imagesize[1] > 700 && $allowreduce === TRUE) {
            if (!self::smart_resize_image("./upload/resor/" . $dir . "/" .$pos . "_" . $dir . "." . $ext, "./upload/resor/" . $dir . "/" .$pos . "_" . $dir . ".$ext",0,700)) {
              throw new \RuntimeException('Lyckades inte reducera bilden. Förminskning misslyckades.');
            }
          }

        echo 'Bilden sparades.';
        http_response_code(200);
        } else {
        echo "PDF sparades.";
        http_response_code(200);
        }

      } catch (\RuntimeException $e) {
        echo $e->getMessage();
        http_response_code(400);
        exit;
      }

    } else {
      // Not logged in
      echo "Felaktigt utförd begäran - du är inte inloggad.";
      http_response_code(401);
      exit;
    }
  }

  /**
   * https://github.com/Nimrod007/PHP_image_resize
   * easy image resize function
   * @param  $file - file name to resize
   * @param  $string - The image data, as a string
   * @param  $width - new image width
   * @param  $height - new image height
   * @param  $proportional - keep image proportional, default is no
   * @param  $output - name of the new file (include path if needed)
   * @param  $delete_original - if true the original image will be deleted
   * @param  $use_linux_commands - if set to true will use "rm" to delete the image, if false will use PHP unlink
   * @param  $quality - enter 1-100 (100 is best quality) default is 100
   * @return boolean|resource
   */

    private static function smart_resize_image($file,$output = 'file',
                                $width              = 0,
                                $height             = 0,
                                $quality            = 80,
                                $string             = null,
                                $proportional       = true,
                                $delete_original    = false,
                                $use_linux_commands = false

    		 ) {

      if ( $height <= 0 && $width <= 0 ) return false;
      if ( $file === null && $string === null ) return false;

      # Setting defaults and meta
      $info                         = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
      $image                        = '';
      $final_width                  = 0;
      $final_height                 = 0;
      list($width_old, $height_old) = $info;
  	$cropHeight = $cropWidth = 0;

      # Calculating proportionality
      if ($proportional) {
        if      ($width  == 0)  $factor = $height/$height_old;
        elseif  ($height == 0)  $factor = $width/$width_old;
        else                    $factor = min( $width / $width_old, $height / $height_old );

        $final_width  = round( $width_old * $factor );
        $final_height = round( $height_old * $factor );
      }
      else {
        $final_width = ( $width <= 0 ) ? $width_old : $width;
        $final_height = ( $height <= 0 ) ? $height_old : $height;
  	  $widthX = $width_old / $width;
  	  $heightX = $height_old / $height;

  	  $x = min($widthX, $heightX);
  	  $cropWidth = ($width_old - $width * $x) / 2;
  	  $cropHeight = ($height_old - $height * $x) / 2;
      }

      # Loading image to memory according to type
      switch ( $info[2] ) {
        case IMAGETYPE_JPEG:  $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);  break;
        case IMAGETYPE_GIF:   $file !== null ? $image = imagecreatefromgif($file)  : $image = imagecreatefromstring($string);  break;
        case IMAGETYPE_PNG:   $file !== null ? $image = imagecreatefrompng($file)  : $image = imagecreatefromstring($string);  break;
        default: return false;
      }


      # This is the resizing/resampling/transparency-preserving magic
      $image_resized = imagecreatetruecolor( $final_width, $final_height );
      if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
        $transparency = imagecolortransparent($image);
        $palletsize = imagecolorstotal($image);

        if ($transparency >= 0 && $transparency < $palletsize) {
          $transparent_color  = imagecolorsforindex($image, $transparency);
          $transparency       = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
          imagefill($image_resized, 0, 0, $transparency);
          imagecolortransparent($image_resized, $transparency);
        }
        elseif ($info[2] == IMAGETYPE_PNG) {
          imagealphablending($image_resized, false);
          $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
          imagefill($image_resized, 0, 0, $color);
          imagesavealpha($image_resized, true);
        }
      }
      imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);


      # Taking care of original, if needed
      if ( $delete_original ) {
        if ( $use_linux_commands ) exec('rm '.$file);
        else @unlink($file);
      }

      # Preparing a method of providing result
      switch ( strtolower($output) ) {
        case 'browser':
          $mime = image_type_to_mime_type($info[2]);
          header("Content-type: $mime");
          $output = NULL;
        break;
        case 'file':
          $output = $file;
        break;
        case 'return':
          return $image_resized;
        break;
        default:
        break;
      }

      # Writing image according to type to the output destination and image quality
      switch ( $info[2] ) {
        case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
        case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output, $quality);   break;
        case IMAGETYPE_PNG:
          $quality = 9 - (int)((0.9*$quality)/10.0);
          imagepng($image_resized, $output, $quality);
          break;
        default: return false;
      }

      return true;
    }





}
