<?php

namespace HisingeBussAB\RekoResor\website;
use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;
use HisingeBussAB\RekoResor\website as root;


class RenderSitemap {

  public static function render($sitemap) {

    header('Content-type: text/xml; charset="utf-8"',true);
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo "<urlset xmlns=\"https://www.sitemaps.org/schemas/sitemap/0.9\">\n";


    try {
      $pdo = DB::get();

      $sql = "SELECT uri_kategori FROM " . TABLE_PREFIX . "kategorier;";
      $sth = $pdo->prepare($sql);
      $sth->execute();
      $categories = $sth->fetchAll(\PDO::FETCH_ASSOC);

      $sql = "SELECT url, aktiv FROM " . TABLE_PREFIX . "resor;";
      $sth = $pdo->prepare($sql);
      $sth->execute();
      $tours = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      // Do nothing
    } catch(\Exception $e) {
      // Do nothing
    }
    echo "<url>
    <loc>http" . APPEND_SSL . "://" . $_SERVER['HTTP_HOST'] . "/</loc>
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
    </url>\n";

    if (!empty($sitemap)) {
      foreach ($sitemap as $url) {
        echo "<url>
        <loc>http" . APPEND_SSL . "://" . $_SERVER['HTTP_HOST'] . "/kategori/" . $url . "/</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
        </url>\n";
      }
    }

    if (!empty($categories)) {
      foreach ($categories as $url) {
        echo "<url>
        <loc>http" . APPEND_SSL . "://" . $_SERVER['HTTP_HOST'] . "/" . $url['uri_kategori'] . "/</loc>
        <changefreq>weekly</changefreq>
        <priority>0.4</priority>
        </url>\n";
      }
    }

    if (!empty($tours)) {
      foreach ($tours as $url) {
        echo "<url>
        <loc>http" . APPEND_SSL . "://" . $_SERVER['HTTP_HOST'] . "/resa/" . $url['url'] . "/</loc>
        <changefreq>monthly</changefreq>";
        if ($url['aktiv'] == 1) {
          echo "<priority>0.3</priority>";
        } else {
          echo "<priority>0.1</priority>";
        }

        echo "</url>\n";
      }
    }

    echo "</urlset>\n";

  }
}
