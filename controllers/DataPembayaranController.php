<?php

require_once 'BaseController.php';
require_once 'models/DataPembayaran.php';
require_once 'models/PaymentType.php';
require_once 'models/AcademicYear.php';
require_once 'models/AssignPembayaran.php';
require_once 'models/Class.php';

class DataPembayaranController extends BaseController {
    
    public function index() {
        $dataPembayaran = new DataPembayaran($this->db);
        $data_pembayaran = $dataPembayaran->getAll();
        
        // Get dropdown data
        $paymentType = new PaymentType($this->db);
        $payment_types = $paymentType->getAll();
        
        $academicYear = new AcademicYear($this->db);
        $academic_years = $academicYear->getAll();

        $data = [
            'page_title' => 'Dashboard',
            'data_pembayaran' => $data_pembayaran,
            'payment_types' => $payment_types,
            'academic_years' => $academic_years,
            'additional_css' => 1,
            'additional_js' => 1
        ];
        
        $this->view('data-pembayaran/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $dataPembayaran = new DataPembayaran($this->db);
                
                $data = [
                    'jenis_pembayaran_id' => $_POST['jenis_pembayaran_id'],
                    'tahun_ajaran_id' => $_POST['tahun_ajaran_id'],
                    'nama_pembayaran' => $_POST['nama_pembayaran'],
                    'nominal' => $_POST['nominal'],
                    'keterangan' => $_POST['keterangan'] ?? null,
                    'batas_waktu' => $_POST['batas_waktu'] ?? null,
                    'dapat_dicicil' => isset($_POST['dapat_dicicil']) ? 1 : 0,
                    'maksimal_cicilan' => $_POST['maksimal_cicilan'] ?? 1
                ];
                
                // Check if it's monthly payment type
                $paymentType = new PaymentType($this->db);
                $jenisData = $paymentType->getById($data['jenis_pembayaran_id']);
                
                if ($jenisData && $jenisData['tipe'] === 'bulanan' && isset($_POST['generate_monthly'])) {
                    // Generate monthly payments
                    if ($dataPembayaran->isExists($data['nama_pembayaran'])) {
                        $this->redirect('data-pembayaran', 'Nama Pembayaran ' . $data['nama_pembayaran'] . ' sudah ada! Silakan gunakan nama pembayaran yang berbeda.', 'error');
                        return;
                    }
                    
                    $result = $dataPembayaran->generateMonthlyPayments(
                        $data['jenis_pembayaran_id'],
                        $data['tahun_ajaran_id'],
                        $data['nominal'],
                        $data['nama_pembayaran'],
                        $data['batas_waktu'], // Pass the selected due date
                        $data['dapat_dicicil'],
                        $data['maksimal_cicilan']
                    );
                    
                    if ($result['success']) {
                        $this->redirect('data-pembayaran', 'Berhasil generate ' . $result['count'] . ' pembayaran bulanan', 'success');
                    } else {
                        $this->redirect('data-pembayaran', 'Gagal generate pembayaran bulanan: ' . $result['message'], 'error');
                    }
                } else {
                    // Create single payment
                    if ($dataPembayaran->isExists($data['nama_pembayaran'])) {
                        $this->redirect('data-pembayaran', 'Nama Pembayaran ' . $data['nama_pembayaran'] . ' sudah ada! Silakan gunakan nama pembayaran yang berbeda.', 'error');
                        return;
                    }
                    $result = $dataPembayaran->create($data);
                    if ($result) {
                        $this->redirect('data-pembayaran', 'Data pembayaran berhasil ditambahkan', 'success');
                    } else {
                        $this->redirect('data-pembayaran', 'Gagal menambahkan data pembayaran', 'error');
                    }
                }
            } catch (Exception $e) {
                error_log("Error in DataPembayaranController::create: " . $e->getMessage());
                $this->redirect('data-pembayaran', 'Terjadi kesalahan: ' . $e->getMessage(), 'error');
            }
        }
        
        $this->redirect('data-pembayaran');
    }
    
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $dataPembayaran = new DataPembayaran($this->db);
                
                $data = [
                    'jenis_pembayaran_id' => $_POST['jenis_pembayaran_id'],
                    'tahun_ajaran_id' => $_POST['tahun_ajaran_id'],
                    'nama_pembayaran' => $_POST['nama_pembayaran'],
                    'nominal' => $_POST['nominal'],
                    'keterangan' => $_POST['keterangan'] ?? null,
                    'batas_waktu' => $_POST['batas_waktu'] ?? null,
                    'dapat_dicicil' => isset($_POST['dapat_dicicil']) ? 1 : 0,
                    'maksimal_cicilan' => $_POST['maksimal_cicilan'] ?? 1
                ];
                if ($dataPembayaran->isExists($data['nama_pembayaran'], $_POST['id'])) {
                    $this->redirect('data-pembayaran', 'Nama Pembayaran ' . $data['nama_pembayaran'] . ' sudah ada! Silakan gunakan nama pembayaran yang berbeda.', 'error');
                    return;
                }
                $result = $dataPembayaran->update($_POST['id'], $data);
                
                if ($result) {
                    $this->redirect('data-pembayaran', 'Data pembayaran berhasil diupdate', 'success');
                } else {
                    $this->redirect('data-pembayaran', 'Gagal mengupdate data pembayaran', 'error');
                }
            } catch (Exception $e) {
                error_log("Error in DataPembayaranController::edit: " . $e->getMessage());
                $this->redirect('data-pembayaran', 'Terjadi kesalahan: ' . $e->getMessage(), 'error');
            }
        }
        
        $this->redirect('data-pembayaran');
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $dataPembayaran = new DataPembayaran($this->db);
                $result = $dataPembayaran->delete($_POST['id']);
                
                if ($result['success']) {
                    $this->redirect('data-pembayaran', $result['message'], 'success');
                } else {
                    $this->redirect('data-pembayaran', $result['message'], 'error');
                }
            } catch (Exception $e) {
                error_log("Error in DataPembayaranController::delete: " . $e->getMessage());
                $this->redirect('data-pembayaran', 'Terjadi kesalahan: ' . $e->getMessage(), 'error');
            }
        }
        
        $this->redirect('data-pembayaran');
    }
    
    public function assign() {
        $data_pembayaran_id = $_GET['id'] ?? null;
        
        if (!$data_pembayaran_id) {
            $this->redirect('data-pembayaran', 'ID data pembayaran tidak valid', 'error');
        }
        
        try {
            $dataPembayaran = new DataPembayaran($this->db);
            $assignPembayaran = new AssignPembayaran($this->db);
            
            $payment_data = $dataPembayaran->getById($data_pembayaran_id);
            if (!$payment_data) {
                $this->redirect('data-pembayaran', 'Data pembayaran tidak ditemukan', 'error');
            }
            
            $assignments = $assignPembayaran->getAssignmentsByPaymentId($data_pembayaran_id);
            $summary = $assignPembayaran->getPaymentSummary($data_pembayaran_id);
            
            // Get students for assignment
            $students = $assignPembayaran->getStudentsForAssignment($payment_data['tahun_ajaran_id']);
            
            // Get classes for filter
            $classModel = new ClassModel($this->db);
            $classes = $classModel->getAll();
            
            $this->view('data-pembayaran/assign', [
                'payment_data' => $payment_data,
                'assignments' => $assignments,
                'summary' => $summary,
                'students' => $students,
                'classes' => $classes
            ]);
        } catch (Exception $e) {
            error_log("Error in DataPembayaranController::assign: " . $e->getMessage());
            $this->redirect('data-pembayaran', 'Terjadi kesalahan: ' . $e->getMessage(), 'error');
        }
    }
    
    public function processAssign() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $assignPembayaran = new AssignPembayaran($this->db);
                
                $data_pembayaran_id = $_POST['data_pembayaran_id'];
                $student_ids = $_POST['student_ids'] ?? [];
                $jumlah_cicilan = $_POST['jumlah_cicilan'] ?? 1;
                
                if (empty($student_ids)) {
                    $this->redirect('data-pembayaran/assign?id=' . $data_pembayaran_id, 'Pilih minimal satu siswa untuk di-assign', 'error');
                } else {
                    $result = $assignPembayaran->assignToStudents($data_pembayaran_id, $student_ids, $jumlah_cicilan);
                    
                    if ($result['success']) {
                        $this->redirect('data-pembayaran/assign?id=' . $data_pembayaran_id, 'Berhasil assign pembayaran ke ' . $result['assigned_count'] . ' siswa', 'success');
                    } else {
                        $this->redirect('data-pembayaran/assign?id=' . $data_pembayaran_id, 'Gagal assign pembayaran: ' . $result['message'], 'error');
                    }
                }
            } catch (Exception $e) {
                error_log("Error in DataPembayaranController::processAssign: " . $e->getMessage());
                $this->redirect('data-pembayaran/assign?id=' . $data_pembayaran_id, 'Terjadi kesalahan: ' . $e->getMessage(), 'error');
            }
        }
        
        $data_pembayaran_id = $_POST['data_pembayaran_id'] ?? $_GET['id'];
        $this->redirect('data-pembayaran/assign?id=' . $data_pembayaran_id);
    }
    
    public function removeAssign() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $assignPembayaran = new AssignPembayaran($this->db);
                
                $result = $assignPembayaran->removeAssignment($_POST['data_pembayaran_id'], $_POST['siswa_id']);
                
                if ($result['success']) {
                    $this->redirect('data-pembayaran/assign?id=' . $_POST['data_pembayaran_id'], $result['message'], 'success');
                } else {
                    $this->redirect('data-pembayaran/assign?id=' . $_POST['data_pembayaran_id'], $result['message'], 'error');
                }
            } catch (Exception $e) {
                error_log("Error in DataPembayaranController::removeAssign: " . $e->getMessage());
                $this->redirect('data-pembayaran/assign?id=' . $_POST['data_pembayaran_id'], 'Terjadi kesalahan: ' . $e->getMessage(), 'error');
            }
        }
        
        $data_pembayaran_id = $_POST['data_pembayaran_id'] ?? $_GET['id'];
        $this->redirect('data-pembayaran/assign?id=' . $data_pembayaran_id);
    }
}
