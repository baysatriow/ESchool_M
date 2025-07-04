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
            'additional_css' => 1,
            'additional_js' => 1
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
            'status_history' => $status_history,
            'additional_css' => 1,
            'additional_js' => 1
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
                if ($student->isExists($data['nis'])) {
                    $this->redirect('students', 'Tahun ajaran ' . $data['nis'] . ' sudah ada! Silakan gunakan NIY yang berbeda.', 'error');
                    return;
                }

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
                if ($student->isExists($data['nis'], $id)) {
                    $this->redirect('students', 'Nomor Induk Siswa ' . $data['nis'] . ' sudah ada! Silakan gunakan NIS yang berbeda.', 'error');
                    return;
                }
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
                // Cek apakah siswa memiliki data pembayaran
                $payment_check = $this->db->prepare("
                    SELECT COUNT(*) as count 
                    FROM t_pembayaran_siswa 
                    WHERE siswa_id = ?
                ");
                $payment_check->execute([$id]);
                $payment_count = $payment_check->fetch(PDO::FETCH_ASSOC)['count'];
                
                if ($payment_count > 0) {
                    $this->redirect('students', 
                        'Data siswa tidak dapat dihapus karena masih memiliki riwayat pembayaran. Silakan hapus riwayat pembayaran terlebih dahulu atau ubah status siswa menjadi tidak aktif.', 
                        'error'
                    );
                    return;
                }
                
                // Cek apakah siswa memiliki assignment pembayaran
                $assign_check = $this->db->prepare("
                    SELECT COUNT(*) as count 
                    FROM t_assign_pembayaran_siswa 
                    WHERE siswa_id = ?
                ");
                $assign_check->execute([$id]);
                $assign_count = $assign_check->fetch(PDO::FETCH_ASSOC)['count'];
                
                if ($assign_count > 0) {
                    $this->redirect('students', 
                        'Data siswa tidak dapat dihapus karena masih memiliki assignment pembayaran. Silakan hapus assignment pembayaran terlebih dahulu atau ubah status siswa menjadi tidak aktif.', 
                        'error'
                    );
                    return;
                }
                
                // Cek apakah ada referensi di kas mutasi melalui pembayaran siswa
                $kas_check = $this->db->prepare("
                    SELECT COUNT(*) as count 
                    FROM t_kas_mutasi km
                    INNER JOIN t_pembayaran_siswa ps ON km.sumber_transaksi_id = ps.id
                    WHERE ps.siswa_id = ? AND km.tipe_sumber = 'PEMBAYARAN_SISWA'
                ");
                $kas_check->execute([$id]);
                $kas_count = $kas_check->fetch(PDO::FETCH_ASSOC)['count'];
                
                if ($kas_count > 0) {
                    $this->redirect('students', 
                        'Data siswa tidak dapat dihapus karena terkait dengan mutasi kas. Silakan hubungi administrator sistem.', 
                        'error'
                    );
                    return;
                }
                
                // Jika semua pengecekan lolos, lakukan penghapusan
                // Note: log_status_siswa akan terhapus otomatis karena CASCADE
                $result = $student->delete($id);
                
                if ($result) {
                    $this->redirect('students', 'Data siswa berhasil dihapus!', 'success');
                } else {
                    $this->redirect('students', 'Gagal menghapus data siswa!', 'error');
                }
                
            } catch (Exception $e) {
                // Handle specific database constraint errors
                $error_message = $e->getMessage();
                
                if (strpos($error_message, 'foreign key constraint') !== false || 
                    strpos($error_message, 'RESTRICT') !== false) {
                    $this->redirect('students', 
                        'Data siswa tidak dapat dihapus karena masih terkait dengan data lain dalam sistem. Silakan ubah status siswa menjadi tidak aktif sebagai alternatif.', 
                        'error'
                    );
                } else {
                    $this->redirect('students', 'Error: ' . $e->getMessage(), 'error');
                }
            }
        }
    }
}
