<?php
//Router for internal AJAX call

if ($id == 'adminlogin')
require_once __DIR__ . '/../admin-cp/php/take-login.php';

if ($id == 'logout')
require_once __DIR__ . '/../admin-cp/php/take-logout.php';
