<?php
/**
 * Environment Configuration Loader
 * Loads environment-specific configuration from .env file
 */

class Environment {
    private static $loaded = false;
    
    public static function load($path = null) {
        if (self::$loaded) {
            return;
        }
        
        if ($path === null) {
            $path = dirname(__DIR__) . '/.env';
        }
        
        if (!file_exists($path)) {
            // Use defaults for development if no .env file exists
            self::setDefaults();
            self::$loaded = true;
            return;
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse key=value pairs
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                $value = trim($value, '"\'');
                
                // Set environment variable if not already set
                if (!isset($_ENV[$key]) && !getenv($key)) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
        
        self::$loaded = true;
    }
    
    private static function setDefaults() {
        $defaults = [
            'APP_ENV' => 'development',
            'APP_DEBUG' => 'true',
            'APP_URL' => 'http://localhost/capacitar-t.com.mx',
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'capacitar_t_mx',
            'DB_USER' => 'root',
            'DB_PASS' => '',
            'DB_CHARSET' => 'utf8mb4',
            'SESSION_TIMEOUT' => '7200',
            'PASSWORD_MIN_LENGTH' => '8'
        ];
        
        foreach ($defaults as $key => $value) {
            if (!isset($_ENV[$key]) && !getenv($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
    
    public static function get($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
        }
        
        // Convert string booleans to actual booleans
        if (in_array(strtolower($value), ['true', 'false'])) {
            return strtolower($value) === 'true';
        }
        
        return $value;
    }
    
    public static function isDevelopment() {
        return self::get('APP_ENV') === 'development';
    }
    
    public static function isProduction() {
        return self::get('APP_ENV') === 'production';
    }
    
    public static function isDebug() {
        return self::get('APP_DEBUG', false) === true;
    }
}

// Auto-load environment on include
Environment::load();