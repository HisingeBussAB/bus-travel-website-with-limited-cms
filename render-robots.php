<?php
namespace HisingeBussAB\RekoResor\website;

use HisingeBussAB\RekoResor\website\includes\classes\DB;
use HisingeBussAB\RekoResor\website\includes\classes\DBError;

header('Content-type: text/plain; charset="utf-8"',true);

echo "User-agent: *
Disallow: /cgi-bin/
Disallow: /adminp/
Disallow: /installme/
Disallow: /ignore/
Disallow: /tests/
Disallow: /error/

Allow: /*.css$
Allow: /*.js$

Sitemap: http" . APPEND_SSL . "://" . $_SERVER['HTTP_HOST'] . "/sitemap.xml";
