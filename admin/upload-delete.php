<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * (c) Rekå Resor AB
 *
 * @link      https://github.com/HisingeBussAB/bus-travel-website-with-limited-cms
 * @copyright CC BY-SA 4.0 (http://creativecommons.org/licenses/by-sa/4.0/)
 * @license   GNU General Public License v3.0
 * @author    Håkan Arnoldson
 */

$fh = fopen('../upload/testa.txt', 'a');
fwrite($fh, '<h1>Hello world!</h1>');
fclose($fh);

unlink('../upload/testa.txt');
unlink('../upload/test.txt');
