<?php

require_once 'models/BaseModel.php';
require_once 'models/User.php';
require_once 'models/Session.php';

class AuthController extends BaseController {
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (!empty($username) && !empty($password)) {
                $user = new User($this->db);
                $authenticated_user = $user->authenticate($username, $password);
                
                if ($authenticated_user) {
                    Session::set('user_id', $authenticated_user['id']);
                    Session::set('user_name', $authenticated_user['nama_lengkap']);
                    Session::set('user_role', $authenticated_user['role']);
                    Session::set('username', $authenticated_user['username']);
                    
                    $this->redirect('dashboard', 'Login berhasil! Selamat datang di ESchool_M.', 'success');
                } else {
                    $this->view('auth/login', ['error' => 'Username atau password salah!']);
                    return;
                }
            } else {
                $this->view('auth/login', ['error' => 'Harap isi username dan password!']);
                return;
            }
        }
        
        $this->view('auth/login');
    }
    
    public function logout() {
        Session::destroy();
        $this->redirect('login', 'Anda telah berhasil logout.', 'info');
    }
}
