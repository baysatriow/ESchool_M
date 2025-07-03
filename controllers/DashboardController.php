<?php

require_once 'models/BaseModel.php';
require_once 'models/Student.php';
require_once 'models/Employee.php';
require_once 'models/Payment.php';
require_once 'models/Income.php';
require_once 'models/Expense.php';

class DashboardController extends BaseController {

    public function index() {
        $student = new Student($this->db);
        $employee = new Employee($this->db);
        $payment = new Payment($this->db);
        $income = new Income($this->db);
        $expense = new Expense($this->db);

        // Get basic statistics
        $total_students = count($student->read(['status' => 'aktif']));
        // Add students with 'naik_kelas' status to the total
        $total_students += count($student->read(['status' => 'naik_kelas']));
        
        $total_employees = count($employee->read(['status' => 'aktif']));

        // Get financial data for current month
        $current_month = date('Y-m');
        $monthly_income = $this->getMonthlyIncome($current_month);
        $monthly_expense = $this->getMonthlyExpense($current_month);
        $monthly_payments = $this->getMonthlyPayments($current_month);

        // Calculate total monthly income (income + student payments)
        $total_monthly_income = $monthly_income + $monthly_payments;

        // Get financial trends for last 12 months
        $financial_trends = $this->getFinancialTrends();

        // Get payment status distribution
        $payment_status = $this->getPaymentStatusDistribution();

        // Get expense categories
        $expense_categories = $this->getExpenseCategories();

        // Get income sources
        $income_sources = $this->getIncomeSources();

        // Get student status distribution
        $student_status = $this->getStudentStatusDistribution();

        // Get recent transactions
        $recent_transactions = $this->getRecentTransactions();

        // Get outstanding payments (tunggakan)
        $outstanding_payments = $this->getOutstandingPayments();

        // Get yearly comparison
        $yearly_comparison = $this->getYearlyComparison();

        $data = [
            'page_title' => 'Dashboard',
            'total_students' => $total_students,
            'total_employees' => $total_employees,
            'monthly_income' => $total_monthly_income,
            'monthly_expense' => $monthly_expense,
            'net_income' => $total_monthly_income - $monthly_expense,
            'financial_trends' => $financial_trends,
            'payment_status' => $payment_status,
            'expense_categories' => $expense_categories,
            'income_sources' => $income_sources,
            'student_status' => $student_status,
            'recent_transactions' => $recent_transactions,
            'outstanding_payments' => $outstanding_payments,
            'yearly_comparison' => $yearly_comparison,
            'additional_css' => [
                'assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
                'assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'
            ],
            'additional_js' => [
                'assets/libs/datatables.net/js/jquery.dataTables.min.js',
                'assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js',
                'assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js',
                'assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js',
            ]
        ];

        $this->view('dashboard/index', $data);
    }

    private function getMonthlyIncome($month) {
        $query = "SELECT COALESCE(SUM(nominal), 0) as total 
                  FROM t_pendapatan 
                  WHERE DATE_FORMAT(tanggal, '%Y-%m') = :month";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':month' => $month]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] ?? 0;
    }

    private function getMonthlyExpense($month) {
        $query = "SELECT COALESCE(SUM(nominal), 0) as total 
                  FROM t_pengeluaran 
                  WHERE DATE_FORMAT(tanggal, '%Y-%m') = :month";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':month' => $month]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] ?? 0;
    }

    private function getMonthlyPayments($month) {
        $query = "SELECT COALESCE(SUM(total_bayar), 0) as total 
                  FROM t_pembayaran_siswa 
                  WHERE DATE_FORMAT(tanggal_bayar, '%Y-%m') = :month";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':month' => $month]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] ?? 0;
    }

    private function getFinancialTrends() {
        $trends = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $month_name = date('M Y', strtotime("-$i months"));
            
            $income = $this->getMonthlyIncome($month);
            $payments = $this->getMonthlyPayments($month);
            $expenses = $this->getMonthlyExpense($month);
            
            $trends[] = [
                'month' => $month_name,
                'income' => (int)($income + $payments),
                'expenses' => (int)$expenses,
                'net' => (int)(($income + $payments) - $expenses)
            ];
        }
        
        return $trends;
    }

    private function getPaymentStatusDistribution() {
        $query = "SELECT 
                    COUNT(CASE WHEN aps.status_pembayaran = 'sudah_bayar' THEN 1 END) as lunas,
                    COUNT(CASE WHEN aps.status_pembayaran = 'sebagian' THEN 1 END) as sebagian,
                    COUNT(CASE WHEN aps.status_pembayaran = 'belum_bayar' THEN 1 END) as belum_bayar
                  FROM t_assign_pembayaran_siswa aps
                  JOIN m_siswa s ON aps.siswa_id = s.id
                  WHERE s.status = 'aktif'";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'lunas' => (int)($result['lunas'] ?? 0),
            'sebagian' => (int)($result['sebagian'] ?? 0),
            'belum_bayar' => (int)($result['belum_bayar'] ?? 0)
        ];
    }

    private function getExpenseCategories() {
        $query = "SELECT 
                    COALESCE(kp.nama_kategori, 'Tidak Dikategorikan') as nama_kategori, 
                    COALESCE(SUM(p.nominal), 0) as total
                  FROM t_pengeluaran p
                  LEFT JOIN m_kategori_pengeluaran kp ON p.kategori_id = kp.id
                  WHERE p.tanggal >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
                  GROUP BY kp.id, kp.nama_kategori
                  HAVING total > 0
                  ORDER BY total DESC
                  LIMIT 10";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ensure numeric values
        foreach ($results as &$result) {
            $result['total'] = (int)$result['total'];
        }
        
        return $results;
    }

    private function getIncomeSources() {
        $sources = [];
        
        // Get student payments
        $query1 = "SELECT COALESCE(SUM(total_bayar), 0) as total
                   FROM t_pembayaran_siswa 
                   WHERE tanggal_bayar >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)";
        
        $stmt1 = $this->db->prepare($query1);
        $stmt1->execute();
        $student_payments = $stmt1->fetchColumn();
        
        if ($student_payments > 0) {
            $sources[] = [
                'source' => 'Pembayaran Siswa',
                'total' => (int)$student_payments
            ];
        }
        
        // Get other income sources
        $query2 = "SELECT 
                     COALESCE(kp.nama_kategori, 'Pendapatan Lain') as source, 
                     COALESCE(SUM(p.nominal), 0) as total
                   FROM t_pendapatan p
                   LEFT JOIN m_kategori_pendapatan kp ON p.kategori_id = kp.id
                   WHERE p.tanggal >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
                   GROUP BY kp.id, kp.nama_kategori
                   HAVING total > 0
                   ORDER BY total DESC";
        
        $stmt2 = $this->db->prepare($query2);
        $stmt2->execute();
        $other_sources = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($other_sources as $source) {
            $sources[] = [
                'source' => $source['source'],
                'total' => (int)$source['total']
            ];
        }
        
        return $sources;
    }

    private function getStudentStatusDistribution() {
        $query = "SELECT status, COUNT(*) as total 
                  FROM m_siswa 
                  GROUP BY status";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ensure numeric values
        foreach ($results as &$result) {
            $result['total'] = (int)$result['total'];
        }
        
        return $results;
    }

    private function getRecentTransactions() {
        $query = "SELECT 'Pembayaran' as type, 
                         COALESCE(ps.no_kuitansi, CONCAT('PAY-', ps.id)) as reference, 
                         ps.tanggal_bayar as date, 
                         ps.total_bayar as amount,
                         CONCAT('Pembayaran dari ', s.nama_lengkap) as description, 
                         'success' as status
                  FROM t_pembayaran_siswa ps
                  JOIN m_siswa s ON ps.siswa_id = s.id
                  WHERE ps.tanggal_bayar >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                  
                  UNION ALL
                  
                  SELECT 'Pendapatan' as type, 
                         COALESCE(pd.no_bukti, CONCAT('INC-', pd.id)) as reference,
                         pd.tanggal as date, 
                         pd.nominal as amount,
                         COALESCE(pd.keterangan, 'Pendapatan') as description, 
                         'success' as status
                  FROM t_pendapatan pd
                  WHERE pd.tanggal >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                  
                  UNION ALL
                  
                  SELECT 'Pengeluaran' as type, 
                         COALESCE(pg.no_bukti, CONCAT('EXP-', pg.id)) as reference,
                         pg.tanggal as date, 
                         pg.nominal as amount,
                         COALESCE(pg.keterangan, 'Pengeluaran') as description, 
                         'danger' as status
                  FROM t_pengeluaran pg
                  WHERE pg.tanggal >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                  
                  ORDER BY date DESC
                  LIMIT 10";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ensure numeric values and clean data
        foreach ($results as &$result) {
            $result['amount'] = (int)$result['amount'];
            $result['description'] = htmlspecialchars($result['description']);
        }
        
        return $results;
    }

    private function getOutstandingPayments() {
        // Revised calculation based on assign_pembayaran_siswa and batas_waktu
        $query = "SELECT 
                    s.nama_lengkap,
                    s.nis,
                    COALESCE(k.nama_kelas, 'Tidak Ada Kelas') as nama_kelas,
                    SUM(aps.nominal_yang_harus_dibayar - aps.nominal_yang_sudah_dibayar) as outstanding,
                    COUNT(aps.id) as jumlah_tagihan
                  FROM t_assign_pembayaran_siswa aps
                  JOIN m_siswa s ON aps.siswa_id = s.id
                  LEFT JOIN m_kelas k ON s.kelas_id = k.id
                  JOIN m_data_pembayaran dp ON aps.data_pembayaran_id = dp.id
                  WHERE s.status = 'aktif' 
                    AND aps.status_pembayaran IN ('belum_bayar', 'sebagian')
                    AND dp.batas_waktu < CURRENT_DATE
                    AND (aps.nominal_yang_harus_dibayar - aps.nominal_yang_sudah_dibayar) > 0
                  GROUP BY s.id
                  ORDER BY outstanding DESC
                  LIMIT 10";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ensure numeric values and clean data
        foreach ($results as &$result) {
            $result['outstanding'] = (int)$result['outstanding'];
            $result['jumlah_tagihan'] = (int)$result['jumlah_tagihan'];
            $result['nama_lengkap'] = htmlspecialchars($result['nama_lengkap']);
            $result['nis'] = htmlspecialchars($result['nis']);
            $result['nama_kelas'] = htmlspecialchars($result['nama_kelas']);
        }
        
        return $results;
    }

    private function getYearlyComparison() {
        $current_year = date('Y');
        $previous_year = $current_year - 1;
        
        $comparison = [];
        
        foreach ([$previous_year, $current_year] as $year) {
            // Get income for the year
            $income_query = "SELECT COALESCE(SUM(nominal), 0) as total
                            FROM t_pendapatan 
                            WHERE YEAR(tanggal) = :year";
            
            $stmt = $this->db->prepare($income_query);
            $stmt->execute([':year' => $year]);
            $income = $stmt->fetchColumn();
            
            // Get student payments for the year
            $payment_query = "SELECT COALESCE(SUM(total_bayar), 0) as total
                             FROM t_pembayaran_siswa 
                             WHERE YEAR(tanggal_bayar) = :year";
            
            $stmt = $this->db->prepare($payment_query);
            $stmt->execute([':year' => $year]);
            $payments = $stmt->fetchColumn();
            
            // Get expenses for the year
            $expense_query = "SELECT COALESCE(SUM(nominal), 0) as total
                             FROM t_pengeluaran 
                             WHERE YEAR(tanggal) = :year";
            
            $stmt = $this->db->prepare($expense_query);
            $stmt->execute([':year' => $year]);
            $expenses = $stmt->fetchColumn();
            
            $comparison[] = [
                'year' => (string)$year,
                'total_income' => (int)($income + $payments),
                'total_expense' => (int)$expenses
            ];
        }
        
        return $comparison;
    }
}
