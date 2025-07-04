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
            'additional_css' => 1,
            'additional_js' => 1
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
            'attendance_history' => $attendance_history,
            'additional_css' => 1,
            'additional_js' => 1
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
                if ($employee->isExists($data['nip'])) {
                    $this->redirect('employees', 'Nomor Induk Yayasan ' . $data['nip'] . ' sudah ada! Silakan gunakan NIY yang berbeda.', 'error');
                    return;
                }

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
                if ($employee->isExists($data['nip'], $id)) {
                    $this->redirect('employees', 'Nomor Induk Yayasan ' . $data['nip'] . ' sudah ada! Silakan gunakan NIY yang berbeda.', 'error');
                    return;
                }

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
