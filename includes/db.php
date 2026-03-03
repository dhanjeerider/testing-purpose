<?php
$config = require __DIR__ . '/../config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name($config['session_name']);
    session_start();
}

$dbDir = dirname($config['db_path']);
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0777, true);
}

$pdo = new PDO('sqlite:' . $config['db_path']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
