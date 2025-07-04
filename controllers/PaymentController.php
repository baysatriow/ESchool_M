<?php

require_once 'BaseController.php';
require_once 'models/Payment.php';
require_once 'models/Student.php';
require_once 'models/Class.php';
require_once 'models/AcademicYear.php';

class PaymentController extends BaseController {
    
    public function index() {
        try {
            $paymentModel = new Payment($this->db);
            
            // Get filters from request
            $filters = [
                'tahun_ajaran_id' => $_GET['tahun_ajaran_id'] ?? '',
                'kelas_id' => $_GET['kelas_id'] ?? '',
                'status_pembayaran' => $_GET['status_pembayaran'] ?? ''
            ];
            
            // Get students with payment status
            $students = $paymentModel->getStudentsWithPaymentStatus($filters);
            
            // Get academic years for filter
            $academicYearModel = new AcademicYear($this->db);
            $academic_years = $academicYearModel->getAll();
            
            // Get classes for filter
            $classModel = new ClassModel($this->db);
            $classes = $classModel->getAll();
            
            // Get payment summary
            $summary = $paymentModel->getPaymentSummary($filters);
            
            // Load view
            $data = [
                'page_title' => 'Pembayaran Siswa',
                'students' => $students,
                'academic_years' => $academic_years,
                'classes' => $classes,
                'summary' => $summary,
                'filters' => $filters,
                'additional_css' => 1,
                'additional_js' => 1
            ];
            
            $this->view('student-payments/index', $data);
            
        } catch (Exception $e) {
            $this->redirect('dashboard', 'Error: ' . $e->getMessage(), 'error');
        }
    }
    
    public function detail() {
        try {
            $siswa_id = $_GET['id'] ?? null;
            $tahun_ajaran_id = $_GET['tahun_ajaran_id'] ?? null;
            
            if (!$siswa_id) {
                $this->redirect('student-payments', 'ID Siswa tidak ditemukan', 'error');
            }
            
            $paymentModel = new Payment($this->db);
            
            // Get payment detail
            $paymentDetail = $paymentModel->getPaymentDetail($siswa_id, $tahun_ajaran_id);
            
            if (!$paymentDetail) {
                $this->redirect('student-payments', 'Data siswa tidak ditemukan', 'error');
            }
            
            // Get academic years for filter
            $academicYearModel = new AcademicYear($this->db);
            $academic_years = $academicYearModel->getAll();
            
            // Load view
            $this->view('student-payments/detail', [
                'student' => $paymentDetail['student'],
                'assignments' => $paymentDetail['assignments'],
                'history' => $paymentDetail['history'],
                'academic_years' => $academic_years,
                'current_tahun_ajaran_id' => $tahun_ajaran_id
            ]);
            
        } catch (Exception $e) {
            $this->redirect('student-payments', 'Error: ' . $e->getMessage(), 'error');
        }
    }
    
public function pay() {
    try {
        $siswa_id = $_GET['siswa_id'] ?? null;
        $tahun_ajaran_id = $_GET['tahun_ajaran_id'] ?? null;
        
        if (!$siswa_id) {
            $this->redirect('student-payments', 'ID Siswa tidak ditemukan', 'error');
        }
        
        $paymentModel = new Payment($this->db);
        
        // Get payment detail
        $paymentDetail = $paymentModel->getPaymentDetail($siswa_id, $tahun_ajaran_id);
        
        if (!$paymentDetail) {
            $this->redirect('student-payments', 'Data siswa tidak ditemukan', 'error');
        }
        
        // Filter only unpaid or partially paid assignments
        $unpaidAssignments = array_filter($paymentDetail['assignments'], function($assignment) {
            return in_array($assignment['status_pembayaran'], ['belum_bayar', 'sebagian']);
        });
        
        // Get installments for assignments that can be paid in installments
        foreach ($unpaidAssignments as &$assignment) {
            if ($assignment['dapat_dicicil'] == 1 && $assignment['status_pembayaran'] != 'sudah_bayar') {
                $assignment['installments'] = $paymentModel->getInstallments($assignment['id']);
            }
        }
        
        // Load view
        $this->view('student-payments/pay', [
            'student' => $paymentDetail['student'],
            'assignments' => $unpaidAssignments
        ]);
        
    } catch (Exception $e) {
        $this->redirect('student-payments', 'Error: ' . $e->getMessage(), 'error');
    }
}
    
    public function processPayment() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('student-payments', 'Method not allowed', 'error');
            }
            
            // Validate required fields
            $siswa_id = $_POST['siswa_id'] ?? null;
            $tanggal_bayar = $_POST['tanggal_bayar'] ?? null;
            $metode_bayar = $_POST['metode_bayar'] ?? null;
            $payment_items = $_POST['payment_items'] ?? []; // Ubah dari 'items' ke 'payment_items'
            
            if (!$siswa_id || !$tanggal_bayar || !$metode_bayar || empty($payment_items)) {
                $this->redirect('student-payments/pay?siswa_id=' . $siswa_id, 'Data pembayaran tidak lengkap', 'error');
            }
            
            // Handle file upload
            $bukti_foto = null;
            if (isset($_FILES['bukti_foto']) && $_FILES['bukti_foto']['error'] === UPLOAD_ERR_OK) {
                $bukti_foto = $this->handleFileUpload($_FILES['bukti_foto']);
                if (!$bukti_foto) {
                    $this->redirect('student-payments/pay?siswa_id=' . $siswa_id, 'Gagal upload bukti pembayaran', 'error');
                }
            }
            
            // Calculate total payment and prepare items
            $total_bayar = 0;
            $items = [];
            
            // Filter hanya item yang dipilih (selected = 1)
            foreach ($payment_items as $item_data) {
                // Skip jika tidak dipilih
                if (!isset($item_data['selected']) || $item_data['selected'] != '1') {
                    continue;
                }
                
                $nominal_bayar = floatval($item_data['nominal_bayar'] ?? 0);
                
                // Validasi nominal bayar
                if ($nominal_bayar <= 0) {
                    $this->redirect('student-payments/pay?siswa_id=' . $siswa_id, 'Nominal pembayaran harus lebih dari 0', 'error');
                }
                
                $total_bayar += $nominal_bayar;
                $current_month = date('m'); // Format: 'MM', misal '07' untuk Juli
                $current_year = date('Y');  // Format: 'YYYY', misal '2025'
                $items[] = [
                    'assign_pembayaran_id' => $item_data['assign_pembayaran_id'],
                    'jenis_pembayaran_id' => $item_data['jenis_pembayaran_id'],
                    'bulan_pembayaran' => $item_data['bulan_pembayaran'] ?? $current_month,
                    'tahun_pembayaran' => $item_data['tahun_pembayaran'] ?? $current_year,
                    'nominal_bayar' => $nominal_bayar,
                    'cicilan_ke' => $item_data['cicilan_ke'] ?? 0
                ];
            }
            
            // Validasi apakah ada item yang dipilih
            if (empty($items)) {
                $this->redirect('student-payments/pay?siswa_id=' . $siswa_id, 'Pilih minimal satu tagihan untuk dibayar', 'error');
            }
            
            // Validate total amount
            if ($total_bayar <= 0) {
                $this->redirect('student-payments/pay?siswa_id=' . $siswa_id, 'Total pembayaran harus lebih dari 0', 'error');
            }
            
            // Prepare payment data
            $paymentData = [
                'siswa_id' => $siswa_id,
                'user_id' => $_SESSION['user_id'] ?? 1,
                'tanggal_bayar' => $tanggal_bayar,
                'total_bayar' => $total_bayar,
                'metode_bayar' => $metode_bayar,
                'bukti_foto' => $bukti_foto,
                'items' => $items
            ];
            
            // Process payment
            $paymentModel = new Payment($this->db);
            $result = $paymentModel->createPayment($paymentData);
            
            if ($result['success']) {
                $this->redirect('student-payments/receipt?payment_id=' . $result['payment_id'], 
                            'Pembayaran berhasil diproses dengan nomor kuitansi: ' . $result['no_kuitansi'], 'success');
            } else {
                $this->redirect('student-payments/pay?siswa_id=' . $siswa_id, 
                            'Gagal memproses pembayaran: ' . $result['message'], 'error');
            }
            
        } catch (Exception $e) {
            $siswa_id = $_POST['siswa_id'] ?? '';
            $this->redirect('student-payments/pay?siswa_id=' . $siswa_id, 'Error: ' . $e->getMessage(), 'error');
        }
    }
    
public function receipt() {
    try {
        // Validasi parameter
                $payment_id = filter_input(INPUT_GET, 'payment_id', FILTER_VALIDATE_INT);
        
        if (!$payment_id) {
            $this->redirect('student-payments', 'ID Pembayaran tidak valid', 'error');
            return;
        }

        // Get payment data dengan field yang lebih lengkap
        $paymentQuery = "SELECT ps.*, s.nama_lengkap, s.nis, k.nama_kelas,
                            u.nama_lengkap as petugas_nama,
                            DATE_FORMAT(ps.tanggal_bayar, '%d %M %Y') as tanggal_bayar_formatted,
                            DATE_FORMAT(ps.created_at, '%d %M %Y %H:%i:%s') as waktu_transaksi
                        FROM t_pembayaran_siswa ps
                        JOIN m_siswa s ON ps.siswa_id = s.id
                        LEFT JOIN m_kelas k ON s.kelas_id = k.id
                        LEFT JOIN m_users u ON ps.user_id = u.id
                        WHERE ps.id = :payment_id";

        $stmt = $this->db->prepare($paymentQuery);
        $stmt->execute([':payment_id' => $payment_id]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$payment) {
            $this->redirect('student-payments', 'Data pembayaran tidak ditemukan', 'error');
            return;
        }

        // Get payment details dengan informasi lebih lengkap
        $detailQuery = "SELECT dps.*, jp.nama_pembayaran, jp.kode_pembayaran, jp.tipe
                       FROM t_detail_pembayaran_siswa dps
                       JOIN m_jenis_pembayaran jp ON dps.jenis_pembayaran_id = jp.id
                       WHERE dps.pembayaran_id = :payment_id
                       ORDER BY jp.nama_pembayaran";

        $detailStmt = $this->db->prepare($detailQuery);
        $detailStmt->execute([':payment_id' => $payment_id]);
        $details = $detailStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($details)) {
            $this->redirect('student-payments', 'Detail pembayaran tidak ditemukan', 'error');
            return;
        }

        // Get school settings
        $schoolQuery = "SELECT * FROM app_pengaturan LIMIT 1";
        $schoolStmt = $this->db->prepare($schoolQuery);
        $schoolStmt->execute();
        $schoolInfo = $schoolStmt->fetch(PDO::FETCH_ASSOC);

        // Load view dengan data yang lebih lengkap
        $this->view('student-payments/receipt', [
            'payment' => $payment,
            'details' => $details,
            'school' => $schoolInfo
        ]);

    } catch (PDOException $e) {
        error_log('Database Error in receipt(): ' . $e->getMessage());
        $this->redirect('student-payments', 'Terjadi kesalahan database', 'error');
    } catch (Exception $e) {
        error_log('Error in receipt(): ' . $e->getMessage());
        $this->redirect('student-payments', 'Terjadi kesalahan: ' . $e->getMessage(), 'error');
    }
}
    
    private function handleFileUpload($file) {
        try {
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!in_array($file['type'], $allowed_types)) {
                return false;
            }
            
            // Validate file size (max 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                return false;
            }
            
            // Create upload directory if not exists
            $upload_dir = 'uploads/payment_proofs/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate unique filename
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'payment_' . date('Ymd_His') . '_' . uniqid() . '.' . $file_extension;
            $filepath = $upload_dir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                return $filename;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("File upload error: " . $e->getMessage());
            return false;
        }
    }
}
