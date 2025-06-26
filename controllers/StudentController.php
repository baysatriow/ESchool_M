<?php
require_once 'models/BaseModel.php';
require_once 'models/Student.php';
require_once 'models/Class.php';

class StudentController extends BaseController {
    
    public function index() {
        $student = new Student($this->db);
        $students = $student->getStudentsWithClass();
        
        // Get classes for dropdown
        $class = new ClassModel($this->db);
        $classes = $class->findAll();
        
        $data = [
            'page_title' => 'Data Siswa',
            'students' => $students,
            'classes' => $classes,
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
        
        $this->view('students/index', $data);
    }
    
    public function detail() {
        // Get ID from GET parameter
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->redirect('students', 'ID siswa tidak ditemukan!', 'error');
            return;
        }
        
        $student = new Student($this->db);
        $student_data = $student->getStudentWithDetails($id);
        
        if (!$student_data) {
            $this->redirect('students', 'Data siswa tidak ditemukan!', 'error');
            return;
        }
        
        // Get payment history
        $payment_history = $student->getPaymentHistory($id);
        
        // Get status history
        $status_history = $student->getStatusHistory($id);
        
        $data = [
            'page_title' => 'Detail Siswa - ' . $student_data['nama_lengkap'],
            'student' => $student_data,
            'payment_history' => $payment_history,
            'status_history' => $status_history
        ];
        
        $this->view('students/detail', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $student = new Student($this->db);
            
            $data = [
                'kelas_id' => $_POST['kelas_id'],
                'nis' => $_POST['nis'],
                'nama_lengkap' => $_POST['nama_lengkap'],
                'jenis_kelamin' => $_POST['jenis_kelamin'],
                'tahun_masuk' => $_POST['tahun_masuk'],
                'status' => $_POST['status'] ?? 'aktif',
                'nama_wali' => $_POST['nama_wali'] ?? null,
                'no_hp_wali' => $_POST['no_hp_wali'] ?? null
            ];
            
            try {
                $result = $student->create($data);
                if ($result) {
                    $this->redirect('students', 'Data siswa berhasil ditambahkan!', 'success');
                } else {
                    $this->redirect('students', 'Gagal menambahkan data siswa!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('students', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $student = new Student($this->db);
            
            $id = $_POST['id'];
            $data = [
                'kelas_id' => $_POST['kelas_id'],
                'nis' => $_POST['nis'],
                'nama_lengkap' => $_POST['nama_lengkap'],
                'jenis_kelamin' => $_POST['jenis_kelamin'],
                'tahun_masuk' => $_POST['tahun_masuk'],
                'status' => $_POST['status'],
                'nama_wali' => $_POST['nama_wali'] ?? null,
                'no_hp_wali' => $_POST['no_hp_wali'] ?? null
            ];
            
            try {
                $result = $student->update($id, $data);
                if ($result) {
                    $this->redirect('students', 'Data siswa berhasil diperbarui!', 'success');
                } else {
                    $this->redirect('students', 'Gagal memperbarui data siswa!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('students', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $student = new Student($this->db);
            $id = $_POST['id'];
            
            try {
                $result = $student->delete($id);
                if ($result) {
                    $this->redirect('students', 'Data siswa berhasil dihapus!', 'success');
                } else {
                    $this->redirect('students', 'Gagal menghapus data siswa!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('students', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
}
