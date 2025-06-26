<?php

require_once 'models/BaseModel.php';
require_once 'models/PaymentRate.php';

class PaymentRateController extends BaseController {
    
    public function index() {
        $paymentRate = new PaymentRate($this->db);
        $paymentRates = $paymentRate->getRatesWithDetails();
        
        $data = [
            'page_title' => 'Tarif Pembayaran',
            'payment_rates' => $paymentRates,
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
        
        $this->view('payment-rates/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $paymentRate = new PaymentRate($this->db);
            
            $data = [
                'jenis_pembayaran_id' => $_POST['jenis_pembayaran_id'],
                'kelas_id' => $_POST['kelas_id'],
                'tahun_ajaran_id' => $_POST['tahun_ajaran_id'],
                'nominal' => $_POST['nominal']
            ];
            
            try {
                $result = $paymentRate->create($data);
                if ($result) {
                    $this->redirect('payment-rates', 'Tarif pembayaran berhasil ditambahkan!', 'success');
                } else {
                    $this->redirect('payment-rates', 'Gagal menambahkan tarif pembayaran!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('payment-rates', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
}
