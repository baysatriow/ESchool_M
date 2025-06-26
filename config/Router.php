<?php

class Router {
    private $routes = [];
    private $middlewares = [];
    
    public function __construct() {
        $this->initializeRoutes();
    }
    
    private function initializeRoutes() {
        // Public routes
        $this->routes = [
            'login' => ['controller' => 'AuthController', 'method' => 'login', 'middleware' => 'guest'],
            'logout' => ['controller' => 'AuthController', 'method' => 'logout', 'middleware' => 'auth'],
        
            // Dashboard - All authenticated users
            '' => ['controller' => 'DashboardController', 'method' => 'index', 'middleware' => 'auth'],
            'dashboard' => ['controller' => 'DashboardController', 'method' => 'index', 'middleware' => 'auth'],
        
            // Students Management - Admin & Operator only
            'students' => ['controller' => 'StudentController', 'method' => 'index', 'middleware' => 'operator'],
            'students/create' => ['controller' => 'StudentController', 'method' => 'create', 'middleware' => 'operator'],
            'students/edit' => ['controller' => 'StudentController', 'method' => 'edit', 'middleware' => 'operator'],
            'students/delete' => ['controller' => 'StudentController', 'method' => 'delete', 'middleware' => 'admin_only'],
            'students/detail' => ['controller' => 'StudentController', 'method' => 'detail', 'middleware' => 'operator'],
        
            // Employee Management - Admin & Operator only
            'employees' => ['controller' => 'EmployeeController', 'method' => 'index', 'middleware' => 'operator'],
            'employees/create' => ['controller' => 'EmployeeController', 'method' => 'create', 'middleware' => 'operator'],
            'employees/edit' => ['controller' => 'EmployeeController', 'method' => 'edit', 'middleware' => 'operator'],
            'employees/delete' => ['controller' => 'EmployeeController', 'method' => 'delete', 'middleware' => 'admin_only'],
            'employees/detail' => ['controller' => 'EmployeeController', 'method' => 'detail', 'middleware' => 'operator'],

            // Income Management - Admin & Bendahara only
            'income' => ['controller' => 'IncomeController', 'method' => 'index', 'middleware' => 'bendahara'],
            'income/create' => ['controller' => 'IncomeController', 'method' => 'create', 'middleware' => 'bendahara'],
            'income/edit' => ['controller' => 'IncomeController', 'method' => 'edit', 'middleware' => 'bendahara'],
            'income/delete' => ['controller' => 'IncomeController', 'method' => 'delete', 'middleware' => 'bendahara'],
            'income/detail' => ['controller' => 'IncomeController', 'method' => 'detail', 'middleware' => 'bendahara'],

            // Income Categories Management - Admin & Bendahara only
            'income-categories' => ['controller' => 'IncomeCategoryController', 'method' => 'index', 'middleware' => 'bendahara'],
            'income-categories/create' => ['controller' => 'IncomeCategoryController', 'method' => 'create', 'middleware' => 'bendahara'],
            'income-categories/edit' => ['controller' => 'IncomeCategoryController', 'method' => 'edit', 'middleware' => 'bendahara'],
            'income-categories/delete' => ['controller' => 'IncomeCategoryController', 'method' => 'delete', 'middleware' => 'bendahara'],

            // Payment Management - Admin & Bendahara only
            'student-payments' => ['controller' => 'PaymentController', 'method' => 'index', 'middleware' => 'bendahara'],
            'student-payments/create' => ['controller' => 'PaymentController', 'method' => 'create', 'middleware' => 'bendahara'],
            'student-payments/edit' => ['controller' => 'PaymentController', 'method' => 'edit', 'middleware' => 'bendahara'],
            'student-payments/delete' => ['controller' => 'PaymentController', 'method' => 'delete', 'middleware' => 'bendahara'],
            'student-payments/detail' => ['controller' => 'PaymentController', 'method' => 'detail', 'middleware' => 'bendahara'],
            'student-payments/form' => ['controller' => 'PaymentController', 'method' => 'form', 'middleware' => 'bendahara'],
            'student-payments/print' => ['controller' => 'PaymentController', 'method' => 'print', 'middleware' => 'bendahara'],
        
            // Expense Management - Admin & Bendahara only
            'expenses' => ['controller' => 'ExpenseController', 'method' => 'index', 'middleware' => 'bendahara'],
            'expenses/create' => ['controller' => 'ExpenseController', 'method' => 'create', 'middleware' => 'bendahara'],
            'expenses/edit' => ['controller' => 'ExpenseController', 'method' => 'edit', 'middleware' => 'bendahara'],
            'expenses/delete' => ['controller' => 'ExpenseController', 'method' => 'delete', 'middleware' => 'bendahara'],
        
            // Reference Data - Admin & Operator only
            'classes' => ['controller' => 'ClassController', 'method' => 'index', 'middleware' => 'operator'],
            'academic-years' => ['controller' => 'AcademicYearController', 'method' => 'index', 'middleware' => 'operator'],
            'positions' => ['controller' => 'PositionController', 'method' => 'index', 'middleware' => 'operator'],
            'payment-types' => ['controller' => 'PaymentTypeController', 'method' => 'index', 'middleware' => 'bendahara'],
            'expense-categories' => ['controller' => 'ExpenseCategoryController', 'method' => 'index', 'middleware' => 'bendahara'],

            // Reference Data CRUD Routes
            'classes/create' => ['controller' => 'ClassController', 'method' => 'create', 'middleware' => 'operator'],
            'classes/edit' => ['controller' => 'ClassController', 'method' => 'edit', 'middleware' => 'operator'],
            'classes/delete' => ['controller' => 'ClassController', 'method' => 'delete', 'middleware' => 'admin_only'],

            'academic-years/create' => ['controller' => 'AcademicYearController', 'method' => 'create', 'middleware' => 'operator'],
            'academic-years/edit' => ['controller' => 'AcademicYearController', 'method' => 'edit', 'middleware' => 'operator'],
            'academic-years/delete' => ['controller' => 'AcademicYearController', 'method' => 'delete', 'middleware' => 'admin_only'],

            'positions/create' => ['controller' => 'PositionController', 'method' => 'create', 'middleware' => 'operator'],
            'positions/edit' => ['controller' => 'PositionController', 'method' => 'edit', 'middleware' => 'operator'],
            'positions/delete' => ['controller' => 'PositionController', 'method' => 'delete', 'middleware' => 'admin_only'],

            'payment-types/create' => ['controller' => 'PaymentTypeController', 'method' => 'create', 'middleware' => 'bendahara'],
            'payment-types/edit' => ['controller' => 'PaymentTypeController', 'method' => 'edit', 'middleware' => 'bendahara'],
            'payment-types/delete' => ['controller' => 'PaymentTypeController', 'method' => 'delete', 'middleware' => 'bendahara'],

            'expense-categories/create' => ['controller' => 'ExpenseCategoryController', 'method' => 'create', 'middleware' => 'bendahara'],
            'expense-categories/edit' => ['controller' => 'ExpenseCategoryController', 'method' => 'edit', 'middleware' => 'bendahara'],
            'expense-categories/delete' => ['controller' => 'ExpenseCategoryController', 'method' => 'delete', 'middleware' => 'bendahara'],
        
            // Reports - Role specific
            'financial-reports' => ['controller' => 'ReportController', 'method' => 'financial', 'middleware' => 'bendahara'],
            'arrears-reports' => ['controller' => 'ReportController', 'method' => 'arrears', 'middleware' => 'bendahara'],
            'salary-reports' => ['controller' => 'ReportController', 'method' => 'salary', 'middleware' => 'bendahara'],
        
            // Attendance - Admin & Bendahara only
            'attendance' => ['controller' => 'AttendanceController', 'method' => 'index', 'middleware' => 'bendahara'],
            'attendance/clock-in' => ['controller' => 'AttendanceController', 'method' => 'clockIn', 'middleware' => 'bendahara'],
            'attendance/clock-out' => ['controller' => 'AttendanceController', 'method' => 'clockOut', 'middleware' => 'bendahara'],
        
            // Settings - Admin only
            'school-identity' => ['controller' => 'SettingController', 'method' => 'schoolIdentity', 'middleware' => 'admin_only'],
            'app-settings' => ['controller' => 'SettingController', 'method' => 'appSettings', 'middleware' => 'admin_only'],
            'user-management' => ['controller' => 'UserController', 'method' => 'index', 'middleware' => 'admin_only'],
            'user-management/create' => ['controller' => 'UserController', 'method' => 'create', 'middleware' => 'admin_only'],
            'user-management/edit' => ['controller' => 'UserController', 'method' => 'edit', 'middleware' => 'admin_only'],
            'user-management/delete' => ['controller' => 'UserController', 'method' => 'delete', 'middleware' => 'admin_only'],
        
            // Student Status - Admin & Operator only
            'student-status' => ['controller' => 'StudentStatusController', 'method' => 'index', 'middleware' => 'operator'],
            'student-status/update' => ['controller' => 'StudentStatusController', 'method' => 'updateStatus', 'middleware' => 'operator'],
            'status-history' => ['controller' => 'StudentStatusController', 'method' => 'history', 'middleware' => 'operator'],

            // Payroll Management - Admin & Bendahara only
            'payroll' => ['controller' => 'PayrollController', 'method' => 'index', 'middleware' => 'bendahara'],
            'payroll/create' => ['controller' => 'PayrollController', 'method' => 'create', 'middleware' => 'bendahara'],
            'payroll/edit' => ['controller' => 'PayrollController', 'method' => 'edit', 'middleware' => 'bendahara'],
            'payroll/delete' => ['controller' => 'PayrollController', 'method' => 'delete', 'middleware' => 'bendahara'],
            'payroll/print' => ['controller' => 'PayrollController', 'method' => 'print', 'middleware' => 'bendahara'],

            // Payroll Components - Admin & Bendahara only
            'payroll-components' => ['controller' => 'PayrollComponentController', 'method' => 'index', 'middleware' => 'bendahara'],
            'payroll-components/create' => ['controller' => 'PayrollComponentController', 'method' => 'create', 'middleware' => 'bendahara'],
            'payroll-components/edit' => ['controller' => 'PayrollComponentController', 'method' => 'edit', 'middleware' => 'bendahara'],
            'payroll-components/delete' => ['controller' => 'PayrollComponentController', 'method' => 'delete', 'middleware' => 'bendahara'],

            // Payment Rates - Admin & Bendahara only
            'payment-rates' => ['controller' => 'PaymentRateController', 'method' => 'index', 'middleware' => 'bendahara'],
            'payment-rates/create' => ['controller' => 'PaymentRateController', 'method' => 'create', 'middleware' => 'bendahara'],
            'payment-rates/edit' => ['controller' => 'PaymentRateController', 'method' => 'edit', 'middleware' => 'bendahara'],
            'payment-rates/delete' => ['controller' => 'PaymentRateController', 'method' => 'delete', 'middleware' => 'bendahara'],
        ];
    }
    
    public function dispatch() {
        $uri = trim($_SERVER['REQUEST_URI'], '/');
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = str_replace('eschool_m/', '', $uri); // Remove base path if needed
        
        if (isset($this->routes[$uri])) {
            $route = $this->routes[$uri];
            
            // Check middleware
            if (isset($route['middleware'])) {
                $this->runMiddleware($route['middleware']);
            }
            
            $controllerName = $route['controller'];
            $methodName = $route['method'];
            
            $controllerFile = "controllers/{$controllerName}.php";
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controller = new $controllerName();
                $controller->$methodName();
            } else {
                $this->notFound();
            }
        } else {
            $this->notFound();
        }
    }
    
    private function runMiddleware($middleware) {
        if (is_array($middleware)) {
            // Handle multiple middlewares
            foreach ($middleware as $mw) {
                $this->runSingleMiddleware($mw);
            }
        } else {
            $this->runSingleMiddleware($middleware);
        }
    }

    private function runSingleMiddleware($middleware) {
        switch ($middleware) {
            case 'auth':
                Session::requireLogin();
                break;
            case 'guest':
                if (Session::isLoggedIn()) {
                    header('Location: dashboard');
                    exit;
                }
                break;
            case 'admin':
                $this->checkRole('admin');
                break;
            case 'operator':
                $this->checkRole(['admin', 'operator']);
                break;
            case 'bendahara':
                $this->checkRole(['admin', 'bendahara']);
                break;
            case 'admin_only':
                $this->checkRole('admin');
                break;
        }
    }

    private function checkRole($allowedRoles) {
        if (!Session::isLoggedIn()) {
            header('Location: ' . self::url('login'));
            exit;
        }
    
        $userRole = Session::getUserRole();
    
        if (is_array($allowedRoles)) {
            if (!in_array($userRole, $allowedRoles)) {
                $this->accessDenied();
            }
        } else {
            if ($userRole !== $allowedRoles) {
                $this->accessDenied();
            }
        }
    }

    private function accessDenied() {
        http_response_code(403);
        Session::setFlash('error', 'Anda tidak memiliki akses untuk halaman ini!');
        header('Location: ' . self::url('dashboard'));
        exit;
    }
    
    private function notFound() {
        http_response_code(404);
        include 'views/errors/404.php';
        exit;
    }
    
    public static function url($path = '') {
        $baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/eschool_m/';
        return $baseUrl . ltrim($path, '/');
    }
}
