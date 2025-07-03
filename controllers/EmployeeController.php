<?php

require_once 'models/BaseModel.php';
require_once 'models/Employee.php';
require_once 'models/Position.php';
require_once 'models/User.php';

class EmployeeController extends BaseController {
    
    public function index() {
        $employee = new Employee($this->db);
        $employees = $employee->getEmployeesWithDetails();
        
        // Get positions for dropdown
        $position = new Position($this->db);
        $positions = $position->findAll();
        
        // Get users for dropdown
        $user = new User($this->db);
        $users = $user->findAll();
        
        $data = [
            'page_title' => 'Data Pegawai',
            'employees' => $employees,
            'positions' => $positions,
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
        
        $this->view('employees/index', $data);
    }
    
    public function detail() {
        $id = $_GET['id'] ?? null;
        $employee = new Employee($this->db);
        $employee_data = $employee->getEmployeeWithFullDetails($id);
        
        if (!$employee_data) {
            $this->redirect('employees', 'Data pegawai tidak ditemukan!', 'error');
            return;
        }
        
        // Get attendance history (last 30 days)
        $attendance_history = $employee->getAttendanceHistory($id, 30);
        
        // Get payroll history
        // $payroll_history = $employee->getPayrollHistory($id);
        
        $data = [
            'page_title' => 'Detail Pegawai - ' . $employee_data['nama_lengkap'],
            'employee' => $employee_data,
            'attendance_history' => $attendance_history
            // 'payroll_history' => $payroll_history
        ];
        
        $this->view('employees/detail', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $employee = new Employee($this->db);
            
            $data = [
                'nip' => trim($_POST['nip']),
                'nama_lengkap' => trim($_POST['nama_lengkap']),
                'jenis_kelamin' => $_POST['jenis_kelamin'],
                'jabatan_id' => $_POST['jabatan_id'],
                'alamat' => trim($_POST['alamat']),
                'no_telepon' => trim($_POST['no_telepon']),
                'email' => trim($_POST['email']),
                'tanggal_masuk' => $_POST['tanggal_masuk'],
                'gaji_pokok' => $_POST['gaji_pokok'] ?? 0,
                'status' => $_POST['status'] ?? 'aktif',
                'user_id' => !empty($_POST['user_id']) ? $_POST['user_id'] : null
            ];
            
            try {
                $result = $employee->create($data);
                if ($result) {
                    $this->redirect('employees', 'Data pegawai berhasil ditambahkan!', 'success');
                } else {
                    $this->redirect('employees', 'Gagal menambahkan data pegawai!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('employees', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $employee = new Employee($this->db);
            
            $id = $_POST['id'];
            $data = [
                'nip' => trim($_POST['nip']),
                'nama_lengkap' => trim($_POST['nama_lengkap']),
                'jenis_kelamin' => $_POST['jenis_kelamin'],
                'jabatan_id' => $_POST['jabatan_id'],
                'alamat' => trim($_POST['alamat']),
                'no_telepon' => trim($_POST['no_telepon']),
                'email' => trim($_POST['email']),
                'tanggal_masuk' => $_POST['tanggal_masuk'],
                'gaji_pokok' => $_POST['gaji_pokok'] ?? 0,
                'status' => $_POST['status'],
                'user_id' => !empty($_POST['user_id']) ? $_POST['user_id'] : null
            ];
            
            try {
                $result = $employee->update($id, $data);
                if ($result) {
                    $this->redirect('employees', 'Data pegawai berhasil diperbarui!', 'success');
                } else {
                    $this->redirect('employees', 'Gagal memperbarui data pegawai!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('employees', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $employee = new Employee($this->db);
            $id = $_POST['id'];
            
            try {
                $result = $employee->delete($id);
                if ($result) {
                    $this->redirect('employees', 'Data pegawai berhasil dihapus!', 'success');
                } else {
                    $this->redirect('employees', 'Gagal menghapus data pegawai!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('employees', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
}
