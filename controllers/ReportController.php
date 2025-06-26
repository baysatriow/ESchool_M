<?php

require_once 'models/BaseModel.php';
require_once 'models/Payment.php';
require_once 'models/Expense.php';
require_once 'models/Student.php';

class ReportController extends BaseController {
    
    public function financial() {
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-t');
        
        // Get payment data
        $payment = new Payment($this->db);
        $query = "SELECT DATE(tanggal_bayar) as tanggal, SUM(total_bayar) as total_pendapatan
                  FROM t_pembayaran_siswa 
                  WHERE DATE(tanggal_bayar) BETWEEN :start_date AND :end_date
                  GROUP BY DATE(tanggal_bayar)
                  ORDER BY tanggal";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':start_date', $start_date);
        $stmt->bindValue(':end_date', $end_date);
        $stmt->execute();
        $payment_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get expense data
        $expense = new Expense($this->db);
        $query = "SELECT DATE(tanggal) as tanggal, SUM(nominal) as total_pengeluaran
                  FROM t_pengeluaran 
                  WHERE DATE(tanggal) BETWEEN :start_date AND :end_date
                  GROUP BY DATE(tanggal)
                  ORDER BY tanggal";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':start_date', $start_date);
        $stmt->bindValue(':end_date', $end_date);
        $stmt->execute();
        $expense_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $data = [
            'page_title' => 'Laporan Keuangan',
            'payment_data' => $payment_data,
            'expense_data' => $expense_data,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
        
        $this->view('reports/financial', $data);
    }
    
    public function arrears() {
        $student = new Student($this->db);
        
        // Get students with payment arrears
        $query = "SELECT s.*, k.nama_kelas,
                         COALESCE(SUM(dp.nominal_bayar), 0) as total_bayar,
                         COUNT(DISTINCT p.id) as jumlah_pembayaran
                  FROM m_siswa s
                  LEFT JOIN m_kelas k ON s.kelas_id = k.id
                  LEFT JOIN t_pembayaran_siswa p ON s.id = p.siswa_id
                  LEFT JOIN t_detail_pembayaran_siswa dp ON p.id = dp.pembayaran_id
                  WHERE s.status = 'aktif'
                  GROUP BY s.id
                  ORDER BY s.nama_lengkap";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $arrears_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $data = [
            'page_title' => 'Laporan Tunggakan Siswa',
            'arrears_data' => $arrears_data,
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
        
        $this->view('reports/arrears', $data);
    }
    
    public function salary() {
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        
        // Get attendance data for salary calculation
        $query = "SELECT p.*, j.nama_jabatan,
                         COUNT(pp.id) as total_hadir,
                         SUM(CASE WHEN pp.status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                         SUM(CASE WHEN pp.status = 'izin' THEN 1 ELSE 0 END) as izin,
                         SUM(CASE WHEN pp.status = 'sakit' THEN 1 ELSE 0 END) as sakit,
                         SUM(CASE WHEN pp.status = 'alpha' THEN 1 ELSE 0 END) as alpha
                  FROM m_pegawai p
                  LEFT JOIN m_jabatan j ON p.jabatan_id = j.id
                  LEFT JOIN t_presensi_pegawai pp ON p.id = pp.pegawai_id 
                       AND MONTH(pp.tanggal) = :month AND YEAR(pp.tanggal) = :year
                  WHERE p.status = 'aktif'
                  GROUP BY p.id
                  ORDER BY p.nama_lengkap";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':month', $month);
        $stmt->bindValue(':year', $year);
        $stmt->execute();
        $salary_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $data = [
            'page_title' => 'Laporan Gaji Pegawai',
            'salary_data' => $salary_data,
            'month' => $month,
            'year' => $year,
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
        
        $this->view('reports/salary', $data);
    }
}
