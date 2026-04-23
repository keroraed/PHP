<?php

$requested = $_SERVER['REQUEST_URI'];

if (file_exists(__DIR__ . $requested)) {
    return false; // Let PHP server handle static files
}

require_once __DIR__ . '/index.php';
