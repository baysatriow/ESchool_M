<?php

require_once 'models/Payment.php';
require_once 'models/Student.php';
require_once 'models/PaymentType.php';
require_once 'models/Class.php';
require_once 'models/AcademicYear.php';
require_once 'models/PaymentRate.php';

class PaymentController extends BaseController {
    
    public function index() {
        $student = new Student($this->db);
        $class = new ClassModel($this->db);
        $academicYear = new AcademicYear($this->db);
        
        // Get filter parameters
        $kelas_id = $_GET['kelas_id'] ?? '';
        $tahun_ajaran_id = $_GET['tahun_ajaran_id'] ?? '';
        $status = $_GET['status'] ?? '';
        
        // Get active academic year if not specified
        if (!$tahun_ajaran_id) {
            $activeYear = $academicYear->getActiveYear();
            $tahun_ajaran_id = $activeYear['id'] ?? '';
        }
        
        // Get students with payment status
        $students_payment_status = $this->getStudentsPaymentStatus($kelas_id, $tahun_ajaran_id, $status);
        
        $data = [
            'page_title' => 'Pembayaran Siswa',
            'students_payment_status' => $students_payment_status,
            'classes' => $class->read(),
            'academic_years' => $academicYear->read()
        ];
        
        $this->view('payments/index', $data);
    }
    
    private function getStudentsPaymentStatus($kelas_id = '', $tahun_ajaran_id = '', $status = '') {
        $query = "SELECT s.*, k.nama_kelas,
                         COALESCE(SUM(dp.nominal_bayar), 0) as total_bayar,
                         COUNT(DISTINCT p.id) as jumlah_pembayaran,
                         (
                            SELECT SUM(tr.nominal) 
                            FROM m_tarif_pembayaran tr 
                            JOIN m_jenis_pembayaran jp ON tr.jenis_pembayaran_id = jp.id
                            WHERE tr.kelas_id = s.kelas_id 
                            AND tr.tahun_ajaran_id = :tahun_ajaran_id
                            AND jp.tipe = 'bulanan'
                         ) * 12 as total_tarif_bulanan,
                         (
                            SELECT SUM(tr.nominal) 
                            FROM m_tarif_pembayaran tr 
                            JOIN m_jenis_pembayaran jp ON tr.jenis_pembayaran_id = jp.id
                            WHERE tr.kelas_id = s.kelas_id 
                            AND tr.tahun_ajaran_id = :tahun_ajaran_id2
                            AND jp.tipe = 'bebas'
                         ) as total_tarif_bebas
                  FROM m_siswa s
                  LEFT JOIN m_kelas k ON s.kelas_id = k.id
                  LEFT JOIN t_pembayaran_siswa p ON s.id = p.siswa_id
                  LEFT JOIN t_detail_pembayaran_siswa dp ON p.id = dp.pembayaran_id
                  WHERE s.status = 'aktif'";
        
        if ($kelas_id) {
            $query .= " AND s.kelas_id = :kelas_id";
        }
        
        $query .= " GROUP BY s.id ORDER BY s.nama_lengkap";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':tahun_ajaran_id', $tahun_ajaran_id);
        $stmt->bindValue(':tahun_ajaran_id2', $tahun_ajaran_id);
        
        if ($kelas_id) {
            $stmt->bindValue(':kelas_id', $kelas_id);
        }
        
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate arrears for each student
        foreach ($results as &$row) {
            $total_expected = ($row['total_tarif_bulanan'] ?? 0) + ($row['total_tarif_bebas'] ?? 0);
            $row['total_tunggakan'] = max(0, $total_expected - $row['total_bayar']);
            
            // Filter by status if specified
            if ($status == 'lunas' && $row['total_tunggakan'] > 0) {
                unset($row);
                continue;
            }
            if ($status == 'tunggakan' && $row['total_tunggakan'] == 0) {
                unset($row);
                continue;
            }
        }
        
        return array_values($results);
    }
    
    public function detail() {
        if (!isset($_GET['id'])) {
            $this->redirect('student-payments', 'ID pembayaran tidak valid!', 'error');
            return;
        }

        $id = $_GET['id'];
        $payment = new Payment($this->db);
        $payment_data = $payment->findById($id);

        if (!$payment_data) {
            $this->redirect('student-payments', 'Data pembayaran tidak ditemukan!', 'error');
            return;
        }

        // Get payment details
        $query = "SELECT dp.*, jp.nama_pembayaran, jp.kode_pembayaran
                  FROM t_detail_pembayaran_siswa dp
                  LEFT JOIN m_jenis_pembayaran jp ON dp.jenis_pembayaran_id = jp.id
                  WHERE dp.pembayaran_id = :payment_id";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':payment_id', $id);
        $stmt->execute();
        $payment_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get student data
        $student = new Student($this->db);
        $student_data = $student->findById($payment_data['siswa_id']);

        $data = [
            'page_title' => 'Detail Pembayaran',
            'payment' => $payment_data,
            'payment_details' => $payment_details,
            'student' => $student_data
        ];

        $this->view('payments/detail', $data);
    }
    
    public function form() {
        if (!isset($_GET['student_id'])) {
            echo "Student ID tidak valid";
            return;
        }
        
        $student_id = $_GET['student_id'];
        $student = new Student($this->db);
        $academicYear = new AcademicYear($this->db);
        $paymentType = new PaymentType($this->db);
        
        // Get student data
        $student_data = $student->findById($student_id);
        if (!$student_data) {
            echo "Data siswa tidak ditemukan";
            return;
        }
        
        // Get active academic year
        $activeYear = $academicYear->getActiveYear();
        
        // Get available payment types for this class
        $available_payments = $this->getAvailablePayments($student_data['kelas_id'], $activeYear['id'], $student_id);
        
        $data = [
            'student' => $student_data,
            'available_payments' => $available_payments,
            'active_year' => $activeYear
        ];
        
        $this->view('payments/form', $data);
    }
    
    private function getStudentPaymentHistory($student_id) {
        $query = "SELECT p.*, dp.*, jp.nama_pembayaran, jp.tipe
                  FROM t_pembayaran_siswa p
                  JOIN t_detail_pembayaran_siswa dp ON p.id = dp.pembayaran_id
                  JOIN m_jenis_pembayaran jp ON dp.jenis_pembayaran_id = jp.id
                  WHERE p.siswa_id = :student_id
                  ORDER BY p.tanggal_bayar DESC, dp.tahun_pembayaran DESC, dp.bulan_pembayaran DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':student_id', $student_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getStudentPaymentSchedule($kelas_id, $tahun_ajaran_id) {
        $query = "SELECT jp.*, tr.nominal
                  FROM m_jenis_pembayaran jp
                  JOIN m_tarif_pembayaran tr ON jp.id = tr.jenis_pembayaran_id
                  WHERE tr.kelas_id = :kelas_id AND tr.tahun_ajaran_id = :tahun_ajaran_id
                  ORDER BY jp.tipe, jp.nama_pembayaran";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':kelas_id', $kelas_id);
        $stmt->bindValue(':tahun_ajaran_id', $tahun_ajaran_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getAvailablePayments($kelas_id, $tahun_ajaran_id, $student_id) {
        // Get all payment types for this class
        $payment_schedule = $this->getStudentPaymentSchedule($kelas_id, $tahun_ajaran_id);
        
        // Get already paid items
        $paid_query = "SELECT dp.jenis_pembayaran_id, dp.bulan_pembayaran, dp.tahun_pembayaran
                       FROM t_pembayaran_siswa p
                       JOIN t_detail_pembayaran_siswa dp ON p.id = dp.pembayaran_id
                       WHERE p.siswa_id = :student_id";
        
        $stmt = $this->db->prepare($paid_query);
        $stmt->bindValue(':student_id', $student_id);
        $stmt->execute();
        $paid_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Create paid items lookup
        $paid_lookup = [];
        foreach ($paid_items as $item) {
            $key = $item['jenis_pembayaran_id'] . '_' . $item['bulan_pembayaran'] . '_' . $item['tahun_pembayaran'];
            $paid_lookup[$key] = true;
        }
        
        // Generate available payments
        $available = [];
        $current_year = date('Y');
        
        foreach ($payment_schedule as $schedule) {
            if ($schedule['tipe'] == 'bulanan') {
                // Generate monthly payments
                for ($month = 1; $month <= 12; $month++) {
                    $key = $schedule['id'] . '_' . $month . '_' . $current_year;
                    if (!isset($paid_lookup[$key])) {
                        $available[] = [
                            'jenis_pembayaran_id' => $schedule['id'],
                            'nama_pembayaran' => $schedule['nama_pembayaran'],
                            'tipe' => $schedule['tipe'],
                            'bulan' => $month,
                            'tahun' => $current_year,
                            'nominal' => $schedule['nominal'],
                            'display_name' => $schedule['nama_pembayaran'] . ' - ' . $this->getMonthName($month) . ' ' . $current_year
                        ];
                    }
                }
            } else {
                // One-time payment
                $key = $schedule['id'] . '__' . $current_year;
                if (!isset($paid_lookup[$key])) {
                    $available[] = [
                        'jenis_pembayaran_id' => $schedule['id'],
                        'nama_pembayaran' => $schedule['nama_pembayaran'],
                        'tipe' => $schedule['tipe'],
                        'bulan' => null,
                        'tahun' => $current_year,
                        'nominal' => $schedule['nominal'],
                        'display_name' => $schedule['nama_pembayaran']
                    ];
                }
            }
        }
        
        return $available;
    }
    
    private function getMonthName($month) {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $months[$month] ?? '';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $payment = new Payment($this->db);
            
            $payment_data = [
                'siswa_id' => $_POST['siswa_id'],
                'user_id' => Session::get('user_id'),
                'tanggal_bayar' => $_POST['tanggal_bayar'],
                'no_kuitansi' => $this->generateReceiptNumber(),
                'total_bayar' => $_POST['total_bayar'],
                'metode_bayar' => $_POST['metode_bayar'] ?? 'tunai'
            ];

            // Handle photo upload
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/payments/';
                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $filename = uniqid() . '_' . $_FILES['photo']['name'];
                $filepath = $uploadDir . $filename;

                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['photo']['type'], $allowedTypes)) {
                    $this->redirect('student-payments', 'Tipe file tidak diizinkan. Hanya JPEG, PNG, dan GIF yang diperbolehkan.', 'error');
                    return;
                }

                // Validate file size (max 2MB)
                if ($_FILES['photo']['size'] > 2000000) {
                    $this->redirect('student-payments', 'Ukuran file terlalu besar. Maksimal 2MB.', 'error');
                    return;
                }

                if (move_uploaded_file($_FILES['photo']['tmp_name'], $filepath)) {
                    $payment_data['photo'] = $filepath;
                } else {
                    $this->redirect('student-payments', 'Gagal mengunggah foto.', 'error');
                    return;
                }
            }
            
            $details = [];
            if (isset($_POST['payment_items'])) {
                foreach ($_POST['payment_items'] as $item) {
                    $details[] = [
                        'jenis_pembayaran_id' => $item['jenis_pembayaran_id'],
                        'bulan_pembayaran' => $item['bulan'] ?? null,
                        'tahun_pembayaran' => $item['tahun'],
                        'nominal_bayar' => $item['nominal']
                    ];
                }
            }
            
            try {
                $result = $payment->createPaymentWithDetails($payment_data, $details);
                if ($result) {
                    $this->redirect('student-payments', 'Pembayaran berhasil disimpan!', 'success');
                } else {
                    $this->redirect('student-payments', 'Gagal menyimpan pembayaran!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('student-payments', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    private function generateReceiptNumber() {
        $date = date('Ymd');
        $query = "SELECT COUNT(*) as count FROM t_pembayaran_siswa WHERE DATE(created_at) = CURDATE()";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $sequence = str_pad($result['count'] + 1, 3, '0', STR_PAD_LEFT);
        return 'PAY' . $date . $sequence;
    }
    
    public function print() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('student-payments', 'ID pembayaran tidak valid!', 'error');
            return;
        }
        
        $payment = new Payment($this->db);
        $payment_data = $payment->findById($id);
        
        if (!$payment_data) {
            $this->redirect('student-payments', 'Data pembayaran tidak ditemukan!', 'error');
            return;
        }
        
        // Get payment details
        $query = "SELECT dp.*, jp.nama_pembayaran, jp.kode_pembayaran
                  FROM t_detail_pembayaran_siswa dp
                  LEFT JOIN m_jenis_pembayaran jp ON dp.jenis_pembayaran_id = jp.id
                  WHERE dp.pembayaran_id = :payment_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':payment_id', $id);
        $stmt->execute();
        $payment_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get student data
        $student = new Student($this->db);
        $student_data = $student->findById($payment_data['siswa_id']);
        
        $data = [
            'page_title' => 'Cetak Kuitansi',
            'payment' => $payment_data,
            'payment_details' => $payment_details,
            'student' => $student_data
        ];
        
        $this->view('payments/print', $data);
    }
}
