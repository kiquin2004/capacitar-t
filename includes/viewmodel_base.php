<?php
abstract class ViewModelBase {
    protected $db;
    protected $data = [];
    protected $errors = [];
    protected $validationRules = [];

    public function __construct() {
        $this->db = Database::getInstance();
        $this->init();
    }

    // Override in child classes for initialization
    protected function init() {}

    // Data binding methods
    public function bind($key, $value) {
        $this->data[$key] = $value;
    }

    public function get($key, $default = null) {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = array_merge($this->data, $data);
    }

    // View rendering
    protected function view($viewFile, $data = []) {
        // Merge ViewModel data with passed data
        $viewData = array_merge($this->data, $data);
        
        // Extract variables for view
        extract($viewData);
        
        // Include view file
        $viewPath = "views/{$viewFile}.php";
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new Exception("View file not found: {$viewPath}");
        }
    }

    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect($url, $message = null, $type = 'info') {
        if ($message) {
            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_type'] = $type;
        }
        
        // Handle both absolute and relative URLs
        if (strpos($url, 'http') !== 0) {
            $url = SITE_URL . $url;
        }
        
        header("Location: " . $url);
        exit;
    }

    // Validation methods
    public function validate($data, $rules = null) {
        $rules = $rules ?: $this->validationRules;
        $this->errors = [];

        foreach ($rules as $field => $rule) {
            $value = isset($data[$field]) ? trim($data[$field]) : '';
            $this->validateField($field, $value, $rule);
        }

        return empty($this->errors);
    }

    protected function validateField($field, $value, $rules) {
        $rulesArray = explode('|', $rules);

        foreach ($rulesArray as $rule) {
            $this->applyValidationRule($field, $value, $rule);
        }
    }

    protected function applyValidationRule($field, $value, $rule) {
        if (strpos($rule, ':') !== false) {
            list($ruleName, $parameter) = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $parameter = null;
        }

        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, "El campo {$field} es requerido");
                }
                break;
            
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "El formato del email no es válido");
                }
                break;
            
            case 'min':
                if (!empty($value) && strlen($value) < $parameter) {
                    $this->addError($field, "El campo {$field} debe tener al menos {$parameter} caracteres");
                }
                break;
            
            case 'max':
                if (!empty($value) && strlen($value) > $parameter) {
                    $this->addError($field, "El campo {$field} no puede exceder {$parameter} caracteres");
                }
                break;
            
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "El campo {$field} debe ser numérico");
                }
                break;
            
            case 'phone':
                if (!empty($value) && !preg_match('/^[\+]?[0-9\s\-\(\)]{10,}$/', $value)) {
                    $this->addError($field, "El formato del teléfono no es válido");
                }
                break;
            
            case 'unique':
                // Parameter should be table:column
                if ($parameter && !empty($value)) {
                    list($table, $column) = explode(':', $parameter);
                    $existing = $this->db->fetchOne(
                        "SELECT id FROM {$table} WHERE {$column} = ?", 
                        [$value]
                    );
                    if ($existing) {
                        $this->addError($field, "Este {$field} ya está en uso");
                    }
                }
                break;
        }
    }

    public function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function hasErrors() {
        return !empty($this->errors);
    }

    // Security helpers
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    protected function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    // Validate redirect URLs to prevent open redirect attacks
    protected function validateRedirectUrl($url) {
        // If empty or null, return default
        if (empty($url)) {
            return '/';
        }
        
        // Only allow relative URLs that start with / but not //
        if (preg_match('/^\/[^\/]/', $url)) {
            // Additional validation: only allow alphanumeric, -, _, /, and ?
            if (preg_match('/^[a-zA-Z0-9\-_\/?=&]*$/', $url)) {
                return $url;
            }
        }
        
        // If validation fails, return safe default
        return '/';
    }

    protected function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    // Session helpers
    protected function setSession($key, $value) {
        $_SESSION[$key] = $value;
    }

    protected function getSession($key, $default = null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    protected function removeSession($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    protected function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            $this->redirect('/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        }
    }

    // Utility methods
    protected function generateToken() {
        return bin2hex(random_bytes(32));
    }

    protected function formatDate($date, $format = 'Y-m-d H:i:s') {
        if ($date instanceof DateTime) {
            return $date->format($format);
        }
        return date($format, strtotime($date));
    }

    protected function timeAgo($datetime) {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'hace un momento';
        if ($time < 3600) return 'hace ' . floor($time/60) . ' minutos';
        if ($time < 86400) return 'hace ' . floor($time/3600) . ' horas';
        if ($time < 2592000) return 'hace ' . floor($time/86400) . ' días';
        if ($time < 31536000) return 'hace ' . floor($time/2592000) . ' meses';
        
        return 'hace ' . floor($time/31536000) . ' años';
    }

    // File upload helper
    protected function uploadFile($file, $allowedTypes = null) {
        $allowedTypes = $allowedTypes ?: ALLOWED_EXTENSIONS;
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir el archivo');
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('El archivo es demasiado grande');
        }
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedTypes)) {
            throw new Exception('Tipo de archivo no permitido');
        }
        
        $filename = uniqid() . '.' . $extension;
        $uploadPath = UPLOAD_PATH . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception('Error al mover el archivo');
        }
        
        return $filename;
    }

    // Pagination helper
    protected function paginate($totalItems, $currentPage = 1, $itemsPerPage = 10) {
        $totalPages = ceil($totalItems / $itemsPerPage);
        $currentPage = max(1, min($currentPage, $totalPages));
        $offset = ($currentPage - 1) * $itemsPerPage;
        
        return [
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'items_per_page' => $itemsPerPage,
            'total_items' => $totalItems,
            'offset' => $offset,
            'has_previous' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'previous_page' => $currentPage - 1,
            'next_page' => $currentPage + 1
        ];
    }
    
    // View rendering method
    protected function view($viewPath, $data = null) {
        // Extract data for the view
        if ($data === null) {
            $data = $this->data;
        }
        extract($data);
        
        // Include the view file
        $viewFile = VIEW_PATH . '/' . $viewPath . '.php';
        
        if (!file_exists($viewFile)) {
            throw new Exception("View file not found: $viewFile");
        }
        
        include $viewFile;
    }
}
?>