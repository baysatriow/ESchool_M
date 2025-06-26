<?php
require_once 'models/BaseModel.php';

class BaseController {
    protected $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    protected function view($view, $data = []) {
        extract($data);
        include "views/{$view}.php";
    }
    
    protected function redirect($url, $message = null, $type = 'success') {
        if ($message) {
            Session::setFlash($type, $message);
        }
        header('Location: ' . Router::url($url));
        exit;
    }
    
    protected function redirectBack($message = null, $type = 'success') {
        if ($message) {
            Session::setFlash($type, $message);
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
