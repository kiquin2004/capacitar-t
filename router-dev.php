<?php
// Development router for PHP built-in server
// Serves existing static files and routes everything else to index.php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$fullPath = __DIR__ . $path;

// If the requested path is an existing file (e.g., CSS/JS/image), let the server handle it
if ($path !== '/' && file_exists($fullPath) && !is_dir($fullPath)) {
    return false;
}

// Otherwise, forward the request to the front controller
require __DIR__ . '/index.php';

