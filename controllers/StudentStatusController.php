<?php

require_once 'models/BaseModel.php';
require_once 'models/Student.php';

class StudentStatusController extends BaseController {
    
    public function index() {
        $student = new Student($this->db);
        $students = $student->getStudentsWithClass();
        
        $data = [
            'page_title' => 'Ubah Status Siswa',
            'students' => $students,
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
        
        $this->view('student-status/index', $data);
    }
    
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $student = new Student($this->db);
            
            $siswa_id = $_POST['siswa_id'];
            $new_status = $_POST['status'];
            $keterangan = $_POST['keterangan'] ?? '';
            $user_id = Session::get('user_id');
            
            try {
                $result = $student->updateStatus($siswa_id, $new_status, $user_id, $keterangan);
                if ($result) {
                    $this->redirect('student-status', 'Status siswa berhasil diperbarui!', 'success');
                } else {
                    $this->redirect('student-status', 'Gagal memperbarui status siswa!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('student-status', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function history() {
        $student = new Student($this->db);
        $student_id = $_GET['student_id'] ?? null;
        
        if ($student_id) {
            $history = $student->getStatusHistory($student_id);
            $student_data = $student->findById($student_id);
        } else {
            $history = $student->getStatusHistory();
            $student_data = null;
        }
        
        $data = [
            'page_title' => 'Riwayat Status Siswa',
            'history' => $history,
            'student_data' => $student_data,
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
        
        $this->view('student-status/history', $data);
    }
}
