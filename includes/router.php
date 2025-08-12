<?php
class Router {
    private $routes = [];
    private $middlewares = [];
    
    public function get($uri, $handler) {
        $this->addRoute('GET', $uri, $handler);
    }
    
    public function post($uri, $handler) {
        $this->addRoute('POST', $uri, $handler);
    }
    
    public function put($uri, $handler) {
        $this->addRoute('PUT', $uri, $handler);
    }
    
    public function delete($uri, $handler) {
        $this->addRoute('DELETE', $uri, $handler);
    }
    
    private function addRoute($method, $uri, $handler) {
        $this->routes[$method][$uri] = $handler;
    }
    
    public function middleware($middleware) {
        $this->middlewares[] = $middleware;
    }
    
    public function resolve() {
        $requestUri = $this->getRequestUri();
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Apply middlewares
        foreach ($this->middlewares as $middleware) {
            if (is_callable($middleware)) {
                call_user_func($middleware);
            }
        }
        
        // Check for exact match first
        if (isset($this->routes[$requestMethod][$requestUri])) {
            $this->callHandler($this->routes[$requestMethod][$requestUri]);
            return;
        }
        
        // Check for parameterized routes
        foreach ($this->routes[$requestMethod] as $route => $handler) {
            $pattern = $this->convertRouteToRegex($route);
            if (preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches); // Remove full match
                $this->callHandler($handler, $matches);
                return;
            }
        }
        
        // 404 Not Found
        $this->handleNotFound();
    }
    
    private function getRequestUri() {
        $uri = $_SERVER['REQUEST_URI'];
        
        // Remove query string
        $uri = strtok($uri, '?');
        
        // Remove base path if running in subdirectory
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Ensure URI starts with /
        return $uri ?: '/';
    }
    
    private function convertRouteToRegex($route) {
        // Convert route parameters like {id} to regex groups
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
        return '#^' . $pattern . '$#';
    }
    
    private function callHandler($handler, $params = []) {
        if (is_string($handler)) {
            // Format: 'ViewModelClass@method'
            list($viewModelClass, $method) = explode('@', $handler);
            
            $viewModelFile = CONTROLLER_PATH . "/{$viewModelClass}.php";
            if (!file_exists($viewModelFile)) {
                throw new Exception("ViewModel file not found: {$viewModelFile}");
            }
            
            require_once $viewModelFile;
            
            if (!class_exists($viewModelClass)) {
                throw new Exception("ViewModel class not found: {$viewModelClass}");
            }
            
            $viewModel = new $viewModelClass();
            
            if (!method_exists($viewModel, $method)) {
                throw new Exception("Method {$method} not found in {$viewModelClass}");
            }
            
            // Call the method with parameters
            call_user_func_array([$viewModel, $method], $params);
            
        } elseif (is_callable($handler)) {
            call_user_func_array($handler, $params);
        } else {
            throw new Exception("Invalid route handler");
        }
    }
    
    private function handleNotFound() {
        http_response_code(404);
        
        // Try to load 404 ViewModel
        $errorViewModelFile = CONTROLLER_PATH . '/ErrorViewModel.php';
        if (file_exists($errorViewModelFile)) {
            require_once $errorViewModelFile;
            $errorViewModel = new ErrorViewModel();
            $errorViewModel->notFound();
        } else {
            // Fallback 404 page
            $fallback404 = VIEW_PATH . '/errors/404.php';
            if (file_exists($fallback404)) {
                include $fallback404;
            } else {
                echo "<h1>404 - Page Not Found</h1><p>The requested page was not found.</p>";
            }
        }
    }
    
    // Helper method to generate URLs
    public static function url($path = '', $params = []) {
        $url = SITE_URL;
        
        if ($path && $path !== '/') {
            $url .= '/' . ltrim($path, '/');
        }
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        return $url;
    }
    
    // Helper method to get current URL
    public static function currentUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    
    // Helper method to check if current route matches
    public static function isActive($path) {
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        
        if ($basePath !== '/') {
            $currentPath = substr($currentPath, strlen($basePath));
        }
        
        return $currentPath === $path;
    }
}

// Add global middleware for CSRF protection, session management, etc.
class Middleware {
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Load session configuration before starting session
            if (!defined('SESSION_CONFIG_LOADED')) {
                require_once __DIR__ . '/../config/session.php';
                define('SESSION_CONFIG_LOADED', true);
            }
            session_start();
        }
    }
    
    public static function csrfProtection() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_csrf_token'] ?? '';
            $sessionToken = $_SESSION['_csrf_token'] ?? '';
            
            if (!hash_equals($sessionToken, $token)) {
                http_response_code(419);
                die('CSRF token mismatch');
            }
        }
    }
    
    public static function cors() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
    
    public static function rateLimit() {
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = "rate_limit_{$ip}";
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'time' => time()];
        }
        
        $rateData = $_SESSION[$key];
        
        // Reset if hour has passed
        if (time() - $rateData['time'] > 3600) {
            $rateData = ['count' => 0, 'time' => time()];
        }
        
        $rateData['count']++;
        $_SESSION[$key] = $rateData;
        
        if ($rateData['count'] > API_RATE_LIMIT) {
            http_response_code(429);
            die('Rate limit exceeded');
        }
    }
    
    public static function auth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . SITE_URL . '/login');
            exit;
        }
    }
}
?>