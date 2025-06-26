<?php

require_once 'models/BaseModel.php';
require_once 'models/PaymentType.php';

class PaymentTypeController extends BaseController {
    
    public function index() {
        $paymentType = new PaymentType($this->db);
        $paymentTypes = $paymentType->getPaymentTypesWithTotal();
        
        $data = [
            'page_title' => 'Jenis Pembayaran',
            'payment_types' => $paymentTypes,
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $paymentType = new PaymentType($this->db);
            
            $data = [
                'kode_pembayaran' => $_POST['kode_pembayaran'],
                'nama_pembayaran' => $_POST['nama_pembayaran'],
                'tipe' => $_POST['tipe'],
                'nominal_default' => $_POST['nominal_default'] ?? 0,
                'keterangan' => $_POST['keterangan'] ?? ''
            ];
            
            try {
                $result = $paymentType->create($data);
                if ($result) {
                    $this->redirect('payment-types', 'Jenis pembayaran berhasil ditambahkan!', 'success');
                } else {
                    $this->redirect('payment-types', 'Gagal menambahkan jenis pembayaran!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('payment-types', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $paymentType = new PaymentType($this->db);
            
            $id = $_POST['id'];
            $data = [
                'kode_pembayaran' => $_POST['kode_pembayaran'],
                'nama_pembayaran' => $_POST['nama_pembayaran'],
                'tipe' => $_POST['tipe'],
                'nominal_default' => $_POST['nominal_default'],
                'keterangan' => $_POST['keterangan']
            ];
            
            try {
                $result = $paymentType->update($id, $data);
                if ($result) {
                    $this->redirect('payment-types', 'Jenis pembayaran berhasil diperbarui!', 'success');
                } else {
                    $this->redirect('payment-types', 'Gagal memperbarui jenis pembayaran!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('payment-types', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $paymentType = new PaymentType($this->db);
            $id = $_POST['id'];
            
            try {
                $result = $paymentType->delete($id);
                if ($result) {
                    $this->redirect('payment-types', 'Jenis pembayaran berhasil dihapus!', 'success');
                } else {
                    $this->redirect('payment-types', 'Gagal menghapus jenis pembayaran!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('payment-types', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
}
