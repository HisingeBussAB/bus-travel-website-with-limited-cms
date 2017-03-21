<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * (c) Rekå Resor AB
 *
 * This file is the router for internal AJAX
 *
 * @link      https://github.com/HisingeBussAB/bus-travel-website-with-limited-cms
 * @copyright CC BY-SA 4.0 (http://creativecommons.org/licenses/by-sa/4.0/)
 * @license   GNU General Public License v3.0
 * @author    Håkan Arnoldson
 */



if ($id == 'adminlogin')
require_once __DIR__ . '/../admin-cp/php/take-login.php';

if ($id == 'logout')
require_once __DIR__ . '/../admin-cp/php/take-logout.php';
