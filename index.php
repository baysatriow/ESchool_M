<?php
// Error reporting untuk development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Define base path
define('BASE_PATH', __DIR__);
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']));

// Autoload required files
require_once BASE_PATH . '/config/Database.php';
require_once BASE_PATH . '/models/BaseModel.php';
require_once BASE_PATH . '/models/Session.php';
require_once BASE_PATH . '/config/Router.php';
require_once BASE_PATH . '/controllers/BaseController.php';

// Autoload all models
$modelFiles = glob(BASE_PATH . '/models/*.php');
foreach ($modelFiles as $file) {
    if (basename($file) !== 'BaseModel.php') {
        require_once $file;
    }
}

// Autoload all controllers
$controllerFiles = glob(BASE_PATH . '/controllers/*.php');
foreach ($controllerFiles as $file) {
    if (basename($file) !== 'BaseController.php') {
        require_once $file;
    }
}

// Initialize database connection
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Failed to establish database connection');
    }
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Initialize router and dispatch request
try {
    $router = new Router();
    $router->dispatch();
} catch (Exception $e) {
    // Log error and show user-friendly message
    error_log('Router Error: ' . $e->getMessage());
    
    // Show 500 error page
    http_response_code(500);
    if (file_exists(BASE_PATH . '/views/errors/500.php')) {
        include BASE_PATH . '/views/errors/500.php';
    } else {
        echo '<h1>Internal Server Error</h1><p>Something went wrong. Please try again later.</p>';
        if (ini_get('display_errors')) {
            echo '<pre>' . $e->getMessage() . '</pre>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
        }
    }
}
?>
