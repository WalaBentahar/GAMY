<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'gamydb');
define('DB_USER', 'root');
define('DB_PASS', '');

// Base URL
define('BASE_URL', 'http://localhost/gamyy/public/');

// Path Constants
define('ROOT', dirname(__DIR__)); // Points to the project root (htdocs/gamy)
define('APP_PATH', ROOT . '/');
define('VIEWS_PATH', APP_PATH . 'views/'); // Now points to app/views/
define('PARTIALS_PATH', VIEWS_PATH . 'partials/'); // Now points to app/views/partials/
// Add admin URL
define('ADMIN_URL', BASE_URL . 'admin');
// Add this route constant
define('STREAMS_URL', BASE_URL . 'streams');
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Change the .env path to this
$envPath = dirname(__DIR__) . '/.env';  // Points to app/.env

// Add error reporting for .env loading
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        die('Error reading .env file');
    }
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = array_map('trim', explode('=', $line, 2));
            $_ENV[$name] = $value;
        }
    }
}

// Add fallback for API key
if (!defined('YOUTUBE_API_KEY')) {
    define('YOUTUBE_API_KEY', $_ENV['YOUTUBE_API_KEY'] ?? 'your_fallback_key_here');
}