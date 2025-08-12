<?php
// Load environment configuration
require_once __DIR__ . '/environment.php';

// Database Configuration (from environment)
define('DB_HOST', Environment::get('DB_HOST', 'localhost'));
define('DB_NAME', Environment::get('DB_NAME', 'capacitar_t_mx'));
define('DB_USER', Environment::get('DB_USER', 'root'));
define('DB_PASS', Environment::get('DB_PASS', ''));
define('DB_CHARSET', Environment::get('DB_CHARSET', 'utf8mb4'));

// Site Configuration (from environment)
define('SITE_URL', Environment::get('APP_URL', 'http://localhost/capacitar-t.com.mx'));
define('SITE_NAME', 'Capacitar-T México');
define('SITE_DESCRIPTION', 'Centro de Capacitación Médica y Primeros Auxilios - Formamos profesionales de la salud con los más altos estándares');
define('SITE_KEYWORDS', 'capacitación médica, primeros auxilios, BLS, ACLS, PALS, stop the bleed, heartsaver, emergencias médicas, México');

// Path Configuration
define('ROOT_PATH', __DIR__ . '/..');
define('VIEW_PATH', ROOT_PATH . '/views');
define('MODEL_PATH', ROOT_PATH . '/models');
define('CONTROLLER_PATH', ROOT_PATH . '/viewmodels');
define('ASSET_PATH', ROOT_PATH . '/assets');

// File Upload Configuration
define('UPLOAD_PATH', 'assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('FROM_EMAIL', 'info@capacitar-t.com.mx');
define('FROM_NAME', 'Capacitar-T México');

// Security Configuration
define('HASH_ALGORITHM', 'sha256');
define('SESSION_TIMEOUT', 3600 * 2); // 2 hours
define('PASSWORD_MIN_LENGTH', 8);

// API Configuration
define('API_VERSION', 'v1');
define('API_RATE_LIMIT', 100); // requests per hour

// Course Configuration
define('DEFAULT_COURSE_DURATION', 8); // hours
define('MAX_PARTICIPANTS_DEFAULT', 20);
define('CERTIFICATION_VALIDITY_YEARS', 2);

// Medical Certification Bodies
define('AHA_CERTIFIED', true); // American Heart Association
define('ERC_CERTIFIED', true); // European Resuscitation Council
define('ILCOR_CERTIFIED', true); // International Liaison Committee on Resuscitation

// Localization
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_MX.UTF-8', 'es_MX', 'spanish');

// Error Reporting
if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
}

// Content Security Policy Headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Define medical course types
define('COURSE_TYPES', [
    'MEDICAL_PROFESSIONAL' => 'Profesionales Médicos',
    'COMMUNITY_FIRST_AID' => 'Primeros Auxilios Comunitarios',
    'MEDICAL_MANAGEMENT' => 'Gestión Médica'
]);

// Define certification levels
define('CERTIFICATION_LEVELS', [
    'BASIC' => 'Básico',
    'INTERMEDIATE' => 'Intermedio',
    'ADVANCED' => 'Avanzado',
    'INSTRUCTOR' => 'Instructor'
]);

// Medical specialization areas
define('MEDICAL_SPECIALIZATIONS', [
    'CARDIOLOGY' => 'Cardiología',
    'PEDIATRICS' => 'Pediatría',
    'EMERGENCY' => 'Medicina de Emergencia',
    'TRAUMA' => 'Traumatología',
    'CRITICAL_CARE' => 'Cuidados Críticos',
    'GENERAL' => 'Medicina General'
]);

// Age group targets (40+, millennials, gen beta)
define('TARGET_DEMOGRAPHICS', [
    'GEN_X' => '40-55 años (Generación X)',
    'MILLENNIALS' => '25-40 años (Millennials)',
    'GEN_BETA' => '18-25 años (Generación Beta)'
]);

// Creative Commons image sources
define('CC_IMAGE_SOURCES', [
    'unsplash' => 'https://unsplash.com',
    'pixabay' => 'https://pixabay.com',
    'pexels' => 'https://pexels.com',
    'wikimedia' => 'https://commons.wikimedia.org'
]);
?>