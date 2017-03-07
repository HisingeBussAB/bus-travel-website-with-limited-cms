<?php
require_once __DIR__ . '/../config/config.php';
$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASSWORD);
$pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER); //Forces table names to lower case
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Error throw exceptions, catch with code.
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); //Not compatible with all drives, defaults to false if not supported. Prepare each statement instead.
?>
