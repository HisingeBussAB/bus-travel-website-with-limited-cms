<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\admin\includes\classes;

use HisingeBussAB\RekoResor\website as root;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

class Categories {

  /**
   * getCategoriesJSON
   *
   * Returns kategorier from DB as JSON
   *
   * @return array json
   */
  public static function getCategoriesJSON() {

    $pdo = DB::get();

    try {
      $sql = "SELECT * FROM " . TABLE_PREFIX . "kategorier ORDER BY sort;";
      $sth = $pdo->prepare($sql);
      $sth->execute();
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
    }

      return json_encode($result);

  }

  /**
   * getActiveCategories
   *
   * Returns kategorier from DB as for rendering on public pages
   *
   * @return array json
   */
  public static function getActiveCategories() {



    try {
      $pdo = DB::get();
      $sql = "SELECT * FROM " . TABLE_PREFIX . "kategorier WHERE aktiv = 1 ORDER BY sort;";
      $sth = $pdo->prepare($sql);
      $sth->execute();
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      return false;
    }

      return json_encode($result);
  }


  /**
   * createCategory
   *
   * Creates new row in table kategorier
   *
   * @return bool success/fail
   */
  public static function createCategory($name) {

    $name = trim($name);

    $pdo = DB::get();

    $uri = root\includes\classes\Functions::uri_recode($name);

    $seo_keywords = $name;
    $og_title = "Rekå Resor - " . $name;

    try {
      $sql = "INSERT INTO " . TABLE_PREFIX . "kategorier (
        kategori,
        uri_kategori,
        seo_keywords,
        og_title,
        sort,
        aktiv
      ) VALUES (
        :name,
        :uri,
        :seo_keywords,
        :og_title,
        (SELECT IFNULL(MAX(sort), 0) FROM " . TABLE_PREFIX . "kategorier K) + 1,
        TRUE
      );";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':name', $name, \PDO::PARAM_STR);
      $sth->bindParam(':uri', $uri, \PDO::PARAM_STR);
      $sth->bindParam(':seo_keywords', $seo_keywords, \PDO::PARAM_STR);
      $sth->bindParam(':og_title', $og_title, \PDO::PARAM_STR);
      $sth->execute();
      return TRUE;
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      return FALSE;
    }
  }

  public static function updateCategory() {

    try {
      if (!$pdo = DB::get()) {
        throw new \RuntimeException("<p>Kan inte ansluta till databasen.</p>");
      }

      if (!root\includes\classes\Tokens::checkFormToken(trim($_POST['token']),trim($_POST['tokenid']),"category")) {
        throw new \RuntimeException("<p>Fel säkerhetstoken. Prova <a href='javascript:window.location.href=window.location.href'>ladda om</a> sidan.</p>");
      }

      if (empty($_POST['id'])) {
        throw new \RuntimeException("<p>Inget kategoriid angivet.</p>");
      }

      $id               = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
      $kategori         = filter_var(trim($_POST['kategori']), FILTER_SANITIZE_STRING);
      $ingress          = strip_tags(trim($_POST['ingress']), ALLOWED_HTML_TAGS);
      $uri_kategori     = filter_var(trim($_POST['uri_kategori']), FILTER_SANITIZE_URL);
      $seo_description  = filter_var(trim($_POST['seo_description']), FILTER_SANITIZE_STRING);
      $og_description   = filter_var(trim($_POST['og_description']), FILTER_SANITIZE_STRING);
      $og_title         = filter_var(trim($_POST['og_title']), FILTER_SANITIZE_STRING);
      $seo_keywords     = filter_var(trim($_POST['seo_keywords']), FILTER_SANITIZE_STRING);
      $meta_data_extra  = strip_tags(trim($_POST['meta_data_extra']), ALLOWED_HTML_TAGS . "<meta>");



      try {
        $sql = "UPDATE " . TABLE_PREFIX . "kategorier SET
          kategori = :kategori,
          ingress = :ingress,
          uri_kategori = :uri_kategori,
          seo_description = :seo_description,
          og_description = :og_description,
          og_title = :og_title,
          seo_keywords = :seo_keywords,
          meta_data_extra = :meta_data_extra
          WHERE id = :id;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':id', $id, \PDO::PARAM_STR);
        $sth->bindParam(':kategori', $kategori, \PDO::PARAM_STR);
        $sth->bindParam(':ingress', $ingress, \PDO::PARAM_STR);
        $sth->bindParam(':uri_kategori', $uri_kategori, \PDO::PARAM_STR);
        $sth->bindParam(':seo_description', $seo_description, \PDO::PARAM_STR);
        $sth->bindParam(':og_description', $og_description, \PDO::PARAM_STR);
        $sth->bindParam(':og_title', $og_title, \PDO::PARAM_STR);
        $sth->bindParam(':seo_keywords', $seo_keywords, \PDO::PARAM_STR);
        $sth->bindParam(':meta_data_extra', $meta_data_extra, \PDO::PARAM_STR);
        $sth->execute();
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql);
        throw new \RuntimeException("<p>Databasfel!</p>");
      }

      $result['responseText'] = "Kategoriinformationen har sparats.";
      echo json_encode($result);
      http_response_code(200);
      exit;

    } catch(\RuntimeException $e) {
      echo $e->getMessage();
      echo "<p><a href='/adminp'>Tillbaka till huvudsidan för administrering.</p>";
      http_response_code(500);
      exit;
  }

  }

}
