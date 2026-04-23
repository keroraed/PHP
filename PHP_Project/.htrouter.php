<?php


$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requested_file = __DIR__ . $request_uri;

if (is_file($requested_file)) {
    return false;
}

if (is_dir($requested_file)) {
    if (file_exists($requested_file . '/index.php')) {
        $_SERVER['SCRIPT_FILENAME'] = $requested_file . '/index.php';
        include $requested_file . '/index.php';
        return true;
    }
}

$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/index.php';
include __DIR__ . '/index.php';
?>
