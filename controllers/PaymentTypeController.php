<?php

require_once 'BaseController.php';
require_once 'models/PaymentType.php';

class PaymentTypeController extends BaseController {
    
    public function index() {
        $paymentType = new PaymentType($this->db);
        $payment_types = $paymentType->getPaymentTypesWithUsage();
        
        $data = [
            'page_title' => 'Jenis Pembayaran',
            'payment_types' => $payment_types,
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

        $this->view('payment-types/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $paymentType = new PaymentType($this->db);
                
                $data = [
                    'kode_pembayaran' => $_POST['kode_pembayaran'],
                    'nama_pembayaran' => $_POST['nama_pembayaran'],
                    'tipe' => $_POST['tipe'],
                    'keterangan' => $_POST['keterangan'] ?? ''
                ];
                
                $result = $paymentType->create($data);
                
                if ($result) {
                    $this->redirect('payment-types', 'Jenis pembayaran berhasil ditambahkan', 'success');
                } else {
                    $this->redirect('payment-types', 'Gagal menambahkan jenis pembayaran', 'error');
                }
            } catch (Exception $e) {
                error_log("Error in PaymentTypeController::create: " . $e->getMessage());
                $this->redirect('payment-types', 'Terjadi kesalahan: ' . $e->getMessage(), 'error');
            }
        }
        
        $this->redirect('payment-types');
    }
    
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $paymentType = new PaymentType($this->db);
                
                $data = [
                    'kode_pembayaran' => $_POST['kode_pembayaran'],
                    'nama_pembayaran' => $_POST['nama_pembayaran'],
                    'tipe' => $_POST['tipe'],
                    'keterangan' => $_POST['keterangan'] ?? ''
                ];
                
                $result = $paymentType->update($_POST['id'], $data);
                
                if ($result) {
                    $this->redirect('payment-types', 'Jenis pembayaran berhasil diupdate', 'success');
                } else {
                    $this->redirect('payment-types', 'Gagal mengupdate jenis pembayaran', 'error');
                }
            } catch (Exception $e) {
                error_log("Error in PaymentTypeController::edit: " . $e->getMessage());
                $this->redirect('payment-types', 'Terjadi kesalahan: ' . $e->getMessage(), 'error');
            }
        }
        
        $this->redirect('payment-types');
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $paymentType = new PaymentType($this->db);
                $result = $paymentType->delete($_POST['id']);
                
                if ($result['success']) {
                    $this->redirect('payment-types', $result['message'], 'success');
                } else {
                    $this->redirect('payment-types', $result['message'], 'error');
                }
            } catch (Exception $e) {
                error_log("Error in PaymentTypeController::delete: " . $e->getMessage());
                $this->redirect('payment-types', 'Terjadi kesalahan: ' . $e->getMessage(), 'error');
            }
        }
        
        $this->redirect('payment-types');
    }
}
