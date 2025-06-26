<?php

class Session {
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    public static function destroy() {
        self::start();
        session_destroy();
    }
    
    public static function isLoggedIn() {
        return self::get('user_id') !== null;
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . Router::url('login'));
            exit;
        }
    }
    
    public static function getUserRole() {
        $role = self::get('user_role');
        // Debug: pastikan role tidak null atau empty
        if (empty($role)) {
            error_log("Session: User role is empty or null");
            return null;
        }
        // Trim whitespace dan convert ke lowercase untuk konsistensi
        return trim(strtolower($role));
    }
    
    public static function getUserId() {
        return self::get('user_id');
    }
    
    public static function getUserName() {
        return self::get('user_name');
    }
    
    public static function hasRole($roles) {
        $userRole = self::getUserRole();
        if (empty($userRole)) {
            return false;
        }
        
        if (is_array($roles)) {
            // Convert semua roles ke lowercase untuk comparison
            $roles = array_map('strtolower', $roles);
            return in_array($userRole, $roles);
        }
        
        return $userRole === strtolower($roles);
    }
    
    public static function setFlash($type, $message) {
        self::set('flash_' . $type, $message);
    }
    
    public static function getFlash($type) {
        $message = self::get('flash_' . $type);
        if ($message) {
            self::set('flash_' . $type, null);
        }
        return $message;
    }
    
    public static function hasFlash($type) {
        return self::get('flash_' . $type) !== null;
    }
    
    // Debug method - hapus setelah testing
    public static function debugSession() {
        self::start();
        return [
            'user_id' => self::get('user_id'),
            'user_role' => self::get('user_role'),
            'user_name' => self::get('user_name'),
            'all_session' => $_SESSION
        ];
    }
}
