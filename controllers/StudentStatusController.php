<?php

require_once 'models/BaseModel.php';
require_once 'models/StudentStatus.php';
require_once 'models/Class.php';

class StudentStatusController extends BaseController {
    
    public function index() {
        $studentStatus = new StudentStatus($this->db);
        $classModel = new ClassModel($this->db);
        
        $kelas_id = $_GET['kelas_id'] ?? null;
        $students = $studentStatus->getStudentsWithClass($kelas_id);
        $classes = $classModel->getActiveClasses();
        $statistics = $studentStatus->getStatusStatistics();
        $recent_changes = $studentStatus->getRecentChanges(5);
        
        $status_options = [
            'aktif' => 'Aktif',
            'lulus' => 'Lulus',
            'pindah' => 'Pindah',
            'dikeluarkan' => 'Dikeluarkan',
            'ALUMNI' => 'Alumni',
            'naik_kelas' => 'Naik Kelas'
        ];
        
        $data = [
            'page_title' => 'Manajemen Status & Kelas Siswa',
            'students' => $students,
            'classes' => $classes,
            'selected_class' => $kelas_id,
            'status_options' => $status_options,
            'statistics' => $statistics,
            'recent_changes' => $recent_changes,
            'additional_css' => [
                'assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
                'assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
                'assets/libs/select2/css/select2.min.css',
                'assets/libs/select2/css/select2-bootstrap4.min.css'
            ],
            'additional_js' => [
                'assets/libs/datatables.net/js/jquery.dataTables.min.js',
                'assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js',
                'assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js',
                'assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js',
                'assets/libs/select2/js/select2.min.js'
            ]
        ];
        
        $this->view('student-status/index', $data);
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $studentStatus = new StudentStatus($this->db);
            
            $siswa_ids = $_POST['siswa_ids'] ?? [];
            $new_status = $_POST['status_baru'] ?? '';
            $new_class_id = $_POST['kelas_id'] ?? '';
            $keterangan = $_POST['keterangan'] ?? '';
            $user_id = Session::get('user_id');
            
            // Validasi input
            if (empty($new_status)) {
                $this->redirect('student-status', 'Status harus dipilih!', 'error');
                return;
            }
            
            if (empty($siswa_ids)) {
                $this->redirect('student-status', 'Tidak ada siswa yang dipilih!', 'error');
                return;
            }
            
            try {
                if (is_array($siswa_ids) && count($siswa_ids) > 1) {
                    // Bulk update
                    $result = $studentStatus->bulkUpdateStatusAndClass($siswa_ids, $new_status, $new_class_id, $user_id, $keterangan);
                    
                    if ($result['success_count'] > 0) {
                        $message = "Berhasil memperbarui status dan kelas {$result['success_count']} siswa!";
                        if ($result['failed_count'] > 0) {
                            $message .= " ({$result['failed_count']} siswa gagal diperbarui)";
                        }
                        $this->redirect('student-status', $message, 'success');
                    } else {
                        $this->redirect('student-status', 'Gagal memperbarui semua siswa!', 'error');
                    }
                } else {
                    // Single update
                    $siswa_id = is_array($siswa_ids) ? $siswa_ids[0] : $siswa_ids;
                    $result = $studentStatus->updateStatusAndClass($siswa_id, $new_status, $new_class_id, $user_id, $keterangan);
                    if ($result) {
                        $this->redirect('student-status', 'Status dan kelas siswa berhasil diperbarui!', 'success');
                    } else {
                        $this->redirect('student-status', 'Gagal memperbarui status dan kelas siswa!', 'error');
                    }
                }
            } catch (Exception $e) {
                error_log('StudentStatus Update Error: ' . $e->getMessage());
                $this->redirect('student-status', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function history() {
        $studentStatus = new StudentStatus($this->db);
        $student_id = $_GET['student_id'] ?? null;
        
        if ($student_id) {
            $history = $studentStatus->getStatusHistory($student_id);
            $student_data = $studentStatus->findById($student_id);
        } else {
            $history = $studentStatus->getStatusHistory();
            $student_data = null;
        }
        
        $data = [
            'page_title' => 'Riwayat Perubahan Status & Kelas',
            'history' => $history,
            'student_data' => $student_data,
            'additional_css' => [
                'assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
                'assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
                'assets/libs/select2/css/select2.min.css',
                'assets/libs/select2/css/select2-bootstrap4.min.css'
            ],
            'additional_js' => [
                'assets/libs/datatables.net/js/jquery.dataTables.min.js',
                'assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js',
                'assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js',
                'assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js',
                'assets/libs/select2/js/select2.min.js'
            ]
        ];
        
        $this->view('student-status/history', $data);
    }
}
