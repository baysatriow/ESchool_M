<?php

require_once 'models/User.php';

class UserController extends BaseController {
    
    public function index() {
        $user = new User($this->db);
        $users = $user->read();
        
        $data = [
            'page_title' => 'Manajemen Pengguna',
            'users' => $users,
            'additional_css' => [
                'assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
                'assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'
            ],
            'additional_js' => [
                'assets/libs/datatables.net/js/jquery.dataTables.min.js',
                'assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js',
                'assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js',
                'assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js'
            ]
        ];
        
        $this->view('users/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new User($this->db);
            
            $data = [
                'nama_lengkap' => $_POST['nama_lengkap'],
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'role' => $_POST['role']
            ];
            
            try {
                $result = $user->createUser($data);
                if ($result) {
                    $this->redirect('user-management', 'Pengguna berhasil ditambahkan!', 'success');
                } else {
                    $this->redirect('user-management', 'Gagal menambahkan pengguna!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('user-management', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new User($this->db);
            
            $id = $_POST['id'];
            $data = [
                'nama_lengkap' => $_POST['nama_lengkap'],
                'username' => $_POST['username'],
                'role' => $_POST['role']
            ];
            
            // Only update password if provided
            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            try {
                $result = $user->update($id, $data);
                if ($result) {
                    $this->redirect('user-management', 'Data pengguna berhasil diperbarui!', 'success');
                } else {
                    $this->redirect('user-management', 'Gagal memperbarui data pengguna!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('user-management', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new User($this->db);
            $id = $_POST['id'];
            
            // Prevent deleting current user
            if ($id == Session::get('user_id')) {
                $this->redirect('user-management', 'Tidak dapat menghapus akun yang sedang digunakan!', 'warning');
                return;
            }
            
            try {
                $result = $user->delete($id);
                if ($result) {
                    $this->redirect('user-management', 'Pengguna berhasil dihapus!', 'success');
                } else {
                    $this->redirect('user-management', 'Gagal menghapus pengguna!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('user-management', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
}
