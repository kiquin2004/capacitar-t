<?php
/**
 * Session Security Configuration
 * This file must be loaded BEFORE session_start() is called
 */

// Only configure once
if (defined('SESSION_CONFIG_LOADED')) {
    return;
}

// Only configure session if no session is active
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session cookie parameters
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', // Only over HTTPS if available
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    
    // Additional PHP session security settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    ini_set('session.use_strict_mode', 1);
    // Session ID configuration (deprecated in PHP 8.1+, using defaults)
    if (PHP_VERSION_ID < 80100) {
        ini_set('session.sid_length', 48);
        ini_set('session.sid_bits_per_character', 6);
    }
    ini_set('session.gc_maxlifetime', 7200); // 2 hours
    ini_set('session.cookie_lifetime', 0); // Session cookie
}

// Mark as loaded
define('SESSION_CONFIG_LOADED', true);