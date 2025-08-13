<?php
/**
 * Component Loading Test Script
 * Tests all critical components of the Capacitar-T application
 */

// Set error reporting to catch all issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Capacitar-T Component Loading Test ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

$tests = [];
$errors = [];

// Test 1: Environment Configuration
echo "1. Testing Environment Configuration...\n";
try {
    require_once 'config/environment.php';
    $tests['environment'] = "✓ PASS - Environment class loaded successfully";
    
    // Test environment methods
    $appEnv = Environment::get('APP_ENV');
    $dbHost = Environment::get('DB_HOST');
    
    if ($appEnv && $dbHost) {
        $tests['environment_vars'] = "✓ PASS - Environment variables accessible";
    } else {
        $tests['environment_vars'] = "✗ FAIL - Environment variables not accessible";
        $errors[] = "Environment variables not properly loaded";
    }
    
} catch (Exception $e) {
    $tests['environment'] = "✗ FAIL - " . $e->getMessage();
    $errors[] = "Environment: " . $e->getMessage();
}

// Test 2: Session Configuration
echo "2. Testing Session Configuration...\n";
try {
    require_once 'config/session.php';
    $tests['session'] = "✓ PASS - Session configuration loaded";
    
    // Check if session can be started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        $tests['session_start'] = "✓ PASS - Session started successfully";
    } else {
        $tests['session_start'] = "✓ PASS - Session already active";
    }
    
} catch (Exception $e) {
    $tests['session'] = "✗ FAIL - " . $e->getMessage();
    $errors[] = "Session: " . $e->getMessage();
}

// Test 3: Main Configuration
echo "3. Testing Main Configuration...\n";
try {
    require_once 'config/config.php';
    $tests['config'] = "✓ PASS - Main configuration loaded";
    
    // Check if critical constants are defined
    $constants = ['SITE_URL', 'ROOT_PATH', 'VIEW_PATH', 'DB_HOST', 'DB_NAME'];
    $missing_constants = [];
    
    foreach ($constants as $constant) {
        if (!defined($constant)) {
            $missing_constants[] = $constant;
        }
    }
    
    if (empty($missing_constants)) {
        $tests['constants'] = "✓ PASS - All critical constants defined";
    } else {
        $tests['constants'] = "✗ FAIL - Missing constants: " . implode(', ', $missing_constants);
        $errors[] = "Missing constants: " . implode(', ', $missing_constants);
    }
    
} catch (Exception $e) {
    $tests['config'] = "✗ FAIL - " . $e->getMessage();
    $errors[] = "Config: " . $e->getMessage();
}

// Test 4: Database Configuration and Connection
echo "4. Testing Database Configuration...\n";
try {
    require_once 'config/database.php';
    $tests['database_config'] = "✓ PASS - Database configuration loaded";
    
    // Test database connection
    $db = Database::getInstance();
    $connection = $db->getConnection();
    
    if ($connection) {
        $tests['database_connection'] = "✓ PASS - Database connection established";
        
        // Test simple query
        $result = $db->fetchOne("SELECT 1 as test");
        if ($result && $result['test'] == 1) {
            $tests['database_query'] = "✓ PASS - Database query execution works";
        } else {
            $tests['database_query'] = "✗ FAIL - Database query failed";
            $errors[] = "Database query execution failed";
        }
    } else {
        $tests['database_connection'] = "✗ FAIL - Database connection failed";
        $errors[] = "Database connection could not be established";
    }
    
} catch (Exception $e) {
    $tests['database_config'] = "✗ FAIL - " . $e->getMessage();
    $errors[] = "Database: " . $e->getMessage();
}

// Test 5: Router
echo "5. Testing Router...\n";
try {
    require_once 'includes/router.php';
    $tests['router'] = "✓ PASS - Router class loaded";
    
    // Test router instantiation
    $router = new Router();
    if ($router instanceof Router) {
        $tests['router_instance'] = "✓ PASS - Router can be instantiated";
    } else {
        $tests['router_instance'] = "✗ FAIL - Router instantiation failed";
        $errors[] = "Router instantiation failed";
    }
    
} catch (Exception $e) {
    $tests['router'] = "✗ FAIL - " . $e->getMessage();
    $errors[] = "Router: " . $e->getMessage();
}

// Test 6: ViewModelBase
echo "6. Testing ViewModelBase...\n";
try {
    require_once 'includes/viewmodel_base.php';
    $tests['viewmodel_base'] = "✓ PASS - ViewModelBase class loaded";
    
    // Check if abstract class methods exist
    $reflection = new ReflectionClass('ViewModelBase');
    if ($reflection->hasMethod('view') && $reflection->hasMethod('json')) {
        $tests['viewmodel_methods'] = "✓ PASS - ViewModelBase has required methods";
    } else {
        $tests['viewmodel_methods'] = "✗ FAIL - ViewModelBase missing required methods";
        $errors[] = "ViewModelBase missing required methods";
    }
    
} catch (Exception $e) {
    $tests['viewmodel_base'] = "✗ FAIL - " . $e->getMessage();
    $errors[] = "ViewModelBase: " . $e->getMessage();
}

// Test 7: Models
echo "7. Testing Models...\n";
try {
    require_once 'models/BaseModel.php';
    $tests['base_model'] = "✓ PASS - BaseModel loaded";
    
    require_once 'models/Course.php';
    require_once 'models/User.php';
    
    $tests['models'] = "✓ PASS - All models loaded successfully";
    
} catch (Exception $e) {
    $tests['models'] = "✗ FAIL - " . $e->getMessage();
    $errors[] = "Models: " . $e->getMessage();
}

// Test 8: ViewModels
echo "8. Testing ViewModels...\n";
$viewModels = [
    'HomeViewModel',
    'CourseViewModel', 
    'UserViewModel',
    'EnrollmentViewModel',
    'AdminViewModel',
    'CourseApiViewModel',
    'AdminApiViewModel',
    'CalendarViewModel',
    'ErrorViewModel'
];

$loadedViewModels = 0;
$failedViewModels = [];

foreach ($viewModels as $viewModel) {
    try {
        $viewModelFile = ROOT_PATH . "/viewmodels/{$viewModel}.php";
        if (file_exists($viewModelFile)) {
            require_once $viewModelFile;
            $loadedViewModels++;
        } else {
            $failedViewModels[] = $viewModel . " (file not found)";
        }
    } catch (Exception $e) {
        $failedViewModels[] = $viewModel . " (" . $e->getMessage() . ")";
    }
}

if (empty($failedViewModels)) {
    $tests['viewmodels'] = "✓ PASS - All {$loadedViewModels} ViewModels loaded successfully";
} else {
    $tests['viewmodels'] = "✗ PARTIAL - {$loadedViewModels} loaded, " . count($failedViewModels) . " failed";
    $errors[] = "Failed ViewModels: " . implode(', ', $failedViewModels);
}

// Test 9: Views Directory Structure
echo "9. Testing Views Directory Structure...\n";
$requiredViews = [
    'views/home/index.php',
    'views/includes/header.php',
    'views/includes/footer.php',
    'views/errors/404.php'
];

$missingViews = [];
foreach ($requiredViews as $view) {
    if (!file_exists($view)) {
        $missingViews[] = $view;
    }
}

if (empty($missingViews)) {
    $tests['views'] = "✓ PASS - All required view files exist";
} else {
    $tests['views'] = "✗ FAIL - Missing view files: " . implode(', ', $missingViews);
    $errors[] = "Missing views: " . implode(', ', $missingViews);
}

// Test 10: File Permissions
echo "10. Testing File Permissions...\n";
$writeableDirectories = [
    'assets/uploads',
    'logs'
];

$permissionIssues = [];
foreach ($writeableDirectories as $dir) {
    if (is_dir($dir)) {
        if (!is_writable($dir)) {
            $permissionIssues[] = $dir . " (not writable)";
        }
    } else {
        $permissionIssues[] = $dir . " (directory doesn't exist)";
    }
}

if (empty($permissionIssues)) {
    $tests['permissions'] = "✓ PASS - All directories have correct permissions";
} else {
    $tests['permissions'] = "⚠ WARNING - Permission issues: " . implode(', ', $permissionIssues);
}

// Print Results
echo "\n=== TEST RESULTS ===\n";
foreach ($tests as $test => $result) {
    echo str_pad($test, 25) . ": " . $result . "\n";
}

if (!empty($errors)) {
    echo "\n=== ERRORS FOUND ===\n";
    foreach ($errors as $error) {
        echo "• " . $error . "\n";
    }
}

$passCount = count(array_filter($tests, function($result) { return strpos($result, '✓ PASS') === 0; }));
$totalCount = count($tests);

echo "\n=== SUMMARY ===\n";
echo "Tests Passed: {$passCount}/{$totalCount}\n";
echo "Total Errors: " . count($errors) . "\n";

if (count($errors) === 0) {
    echo "🎉 ALL TESTS PASSED! The application should load correctly.\n";
} elseif (count($errors) <= 2) {
    echo "⚠️  Minor issues found. Application may work with warnings.\n";
} else {
    echo "❌ Multiple issues found. Application may not load correctly.\n";
}

echo "\nTest completed at: " . date('Y-m-d H:i:s') . "\n";
?>