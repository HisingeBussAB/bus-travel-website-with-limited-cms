<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 *
 * Sub-router for ajax calls.
 */



if ($id == 'adminlogin')
require_once __DIR__ . '/../admin-cp/php/take-login.php';

if ($id == 'logout')
require_once __DIR__ . '/../admin-cp/php/take-logout.php';
