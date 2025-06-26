<?php

require_once 'models/BaseModel.php';
require_once 'models/Payroll.php';
require_once 'models/Employee.php';
require_once 'models/PayrollComponent.php';

class PayrollController extends BaseController {
    
    public function index() {
        $payroll = new Payroll($this->db);
        $payrolls = $payroll->getPayrollsWithEmployee();
        
        $data = [
            'page_title' => 'Data Penggajian',
            'payrolls' => $payrolls,
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
        
        $this->view('payroll/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $payroll = new Payroll($this->db);
            $employee = new Employee($this->db);
            
            // Get employee data for gaji_pokok
            $emp_data = $employee->findById($_POST['pegawai_id']);
            
            $payroll_data = [
                'pegawai_id' => $_POST['pegawai_id'],
                'user_id' => Session::get('user_id'),
                'periode_gaji' => $_POST['periode_gaji'],
                'tanggal_pembayaran' => $_POST['tanggal_pembayaran'],
                'gaji_pokok' => $emp_data['gaji_pokok'],
                'total_tunjangan' => $_POST['total_tunjangan'] ?? 0,
                'total_potongan' => $_POST['total_potongan'] ?? 0,
                'gaji_bersih' => $emp_data['gaji_pokok'] + ($_POST['total_tunjangan'] ?? 0) - ($_POST['total_potongan'] ?? 0)
            ];
            
            $components = json_decode($_POST['components'], true) ?? [];
            
            try {
                $result = $payroll->createPayrollWithDetails($payroll_data, $components);
                if ($result) {
                    $this->redirect('payroll', 'Data penggajian berhasil ditambahkan!', 'success');
                } else {
                    $this->redirect('payroll', 'Gagal menambahkan data penggajian!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('payroll', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
}
