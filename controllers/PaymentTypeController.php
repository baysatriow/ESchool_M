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
            'additional_css' => 1,
            'additional_js' => 1
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
                
                if ($paymentType->isExists($data['kode_pembayaran'])) {
                    $this->redirect('payment-types', 'Kode Pembayaran ' . $data['kode_pembayaran'] . ' sudah ada! Silakan gunakan kode pembayaran yang berbeda.', 'error');
                    return;
                }

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
                if ($paymentType->isExists($data['kode_pembayaran'], $_POST['id'])) {
                    $this->redirect('payment-types', 'Kode Pembayaran ' . $data['kode_pembayaran'] . ' sudah ada! Silakan gunakan kode pembayaran yang berbeda.', 'error');
                    return;
                }

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
