<?php
require_once 'controllers/BaseController.php';
require_once 'models/Payment.php';
require_once 'models/Expense.php';
require_once 'models/Student.php';
require_once 'helpers/ExcelExporter.php';

class ReportController extends BaseController {
    
    public function financial() {
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-t');
        $export = $_GET['export'] ?? null;
        
        // Get comprehensive financial data for display
        $financial_data = $this->getFinancialData($start_date, $end_date);
        
        // If export is requested
        if ($export === 'excel') {
            $this->exportFinancialToExcel($start_date, $end_date);
            return;
        }
        
        $data = [
            'page_title' => 'Laporan Keuangan Sekolah',
            'financial_data' => $financial_data,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
        
        $this->view('reports/financial', $data);
    }
    
    public function arrears() {
        $kelas_id = $_GET['kelas_id'] ?? '';
        $export = $_GET['export'] ?? null;
        
        // Get arrears data
        $arrears_data = $this->getArrearsData($kelas_id);
        
        // Get classes for filter
        $classes = $this->getClasses();
        
        // If export is requested
        if ($export === 'excel') {
            $this->exportArrearsToExcel($arrears_data, $kelas_id);
            return;
        }
        
        $data = [
            'page_title' => 'Laporan Tunggakan Siswa',
            'arrears_data' => $arrears_data,
            'classes' => $classes,
            'selected_kelas' => $kelas_id,
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
    
    private function getFinancialData($start_date, $end_date) {
        // Get income from t_pendapatan
        $income_query = "SELECT 
                            DATE(tanggal) as tanggal,
                            'Pendapatan Lain' as kategori,
                            kp.nama_kategori as sub_kategori,
                            SUM(nominal) as nominal,
                            COUNT(*) as jumlah_transaksi
                         FROM t_pendapatan p
                         LEFT JOIN m_kategori_pendapatan kp ON p.kategori_id = kp.id
                         WHERE DATE(tanggal) BETWEEN :start_date AND :end_date
                         GROUP BY DATE(tanggal), kp.nama_kategori
                         ORDER BY tanggal DESC";
        
        $stmt = $this->db->prepare($income_query);
        $stmt->bindValue(':start_date', $start_date);
        $stmt->bindValue(':end_date', $end_date);
        $stmt->execute();
        $income_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get income from student payments
        $payment_query = "SELECT 
                             DATE(ps.tanggal_bayar) as tanggal,
                             'Pembayaran Siswa' as kategori,
                             jp.nama_pembayaran as sub_kategori,
                             SUM(ps.total_bayar) as nominal,
                             COUNT(*) as jumlah_transaksi
                          FROM t_pembayaran_siswa ps
                          LEFT JOIN t_detail_pembayaran_siswa dps ON ps.id = dps.pembayaran_id
                          LEFT JOIN m_jenis_pembayaran jp ON dps.jenis_pembayaran_id = jp.id
                          WHERE DATE(ps.tanggal_bayar) BETWEEN :start_date AND :end_date
                          GROUP BY DATE(ps.tanggal_bayar), jp.nama_pembayaran
                          ORDER BY tanggal DESC";
        
        $stmt = $this->db->prepare($payment_query);
        $stmt->bindValue(':start_date', $start_date);
        $stmt->bindValue(':end_date', $end_date);
        $stmt->execute();
        $payment_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get expenses
        $expense_query = "SELECT 
                             DATE(tanggal) as tanggal,
                             'Pengeluaran' as kategori,
                             kp.nama_kategori as sub_kategori,
                             SUM(nominal) as nominal,
                             COUNT(*) as jumlah_transaksi
                          FROM t_pengeluaran e
                          LEFT JOIN m_kategori_pengeluaran kp ON e.kategori_id = kp.id
                          WHERE DATE(tanggal) BETWEEN :start_date AND :end_date
                          GROUP BY DATE(tanggal), kp.nama_kategori
                          ORDER BY tanggal DESC";
        
        $stmt = $this->db->prepare($expense_query);
        $stmt->bindValue(':start_date', $start_date);
        $stmt->bindValue(':end_date', $end_date);
        $stmt->execute();
        $expense_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate summary
        $total_income = array_sum(array_column($income_data, 'nominal'));
        $total_payment = array_sum(array_column($payment_data, 'nominal'));
        $total_expense = array_sum(array_column($expense_data, 'nominal'));
        $total_pemasukan = $total_income + $total_payment;
        $saldo = $total_pemasukan - $total_expense;
        
        return [
            'income_data' => $income_data,
            'payment_data' => $payment_data,
            'expense_data' => $expense_data,
            'summary' => [
                'total_income' => $total_income,
                'total_payment' => $total_payment,
                'total_pemasukan' => $total_pemasukan,
                'total_expense' => $total_expense,
                'saldo' => $saldo
            ]
        ];
    }
    
    private function getArrearsData($kelas_id = '') {
        $where_clause = '';
        $params = [];
        
        if (!empty($kelas_id)) {
            $where_clause = 'AND s.kelas_id = :kelas_id';
            $params[':kelas_id'] = $kelas_id;
        }
        
        $query = "SELECT 
                     s.id as siswa_id,
                     s.nis,
                     s.nama_lengkap,
                     k.nama_kelas,
                     COUNT(aps.id) as total_tagihan,
                     SUM(CASE WHEN aps.status_pembayaran = 'sudah_bayar' THEN 1 ELSE 0 END) as sudah_bayar,
                     SUM(CASE WHEN aps.status_pembayaran = 'belum_bayar' 
                              AND dp.batas_waktu < CURDATE() THEN 1 ELSE 0 END) as tunggakan,
                     GROUP_CONCAT(
                         CASE WHEN aps.status_pembayaran = 'belum_bayar' 
                              AND dp.batas_waktu < CURDATE() 
                         THEN CONCAT(dp.nama_pembayaran, ' (', DATE_FORMAT(dp.batas_waktu, '%d/%m/%Y'), ')')
                         END SEPARATOR '; '
                     ) as detail_tunggakan,
                     SUM(CASE WHEN aps.status_pembayaran = 'belum_bayar' 
                              AND dp.batas_waktu < CURDATE() 
                         THEN aps.nominal_yang_harus_dibayar ELSE 0 END) as total_nominal_tunggakan
                  FROM m_siswa s
                  LEFT JOIN m_kelas k ON s.kelas_id = k.id
                  LEFT JOIN t_assign_pembayaran_siswa aps ON s.id = aps.siswa_id
                  LEFT JOIN m_data_pembayaran dp ON aps.data_pembayaran_id = dp.id
                  WHERE s.status = 'aktif' {$where_clause}
                  GROUP BY s.id
                  HAVING total_tagihan > 0
                  ORDER BY tunggakan DESC, s.nama_lengkap";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getClasses() {
        $query = "SELECT id, nama_kelas FROM m_kelas ORDER BY tingkat, nama_kelas";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function exportFinancialToExcel($start_date, $end_date) {
        $exporter = new ExcelExporter();
        
        // Get all detailed data from database
        $all_data = $this->getAllFinancialDataForExport($start_date, $end_date);
        
        // Prepare data for export
        $export_data = [];
        
        // Add header
        $export_data[] = ['LAPORAN KEUANGAN SEKOLAH LENGKAP'];
        $export_data[] = ['Periode: ' . date('d/m/Y', strtotime($start_date)) . ' s/d ' . date('d/m/Y', strtotime($end_date))];
        $export_data[] = ['Dicetak pada: ' . date('d/m/Y H:i:s')];
        $export_data[] = [''];
        
        // Summary section
        $export_data[] = ['RINGKASAN KEUANGAN'];
        $export_data[] = ['Total Pendapatan Lain', 'Rp ' . number_format($all_data['summary']['total_pendapatan'], 0, ',', '.')];
        $export_data[] = ['Total Pembayaran Siswa', 'Rp ' . number_format($all_data['summary']['total_pembayaran_siswa'], 0, ',', '.')];
        $export_data[] = ['Total Pemasukan', 'Rp ' . number_format($all_data['summary']['total_pemasukan'], 0, ',', '.')];
        $export_data[] = ['Total Pengeluaran', 'Rp ' . number_format($all_data['summary']['total_pengeluaran'], 0, ',', '.')];
        $export_data[] = ['Saldo Akhir', 'Rp ' . number_format($all_data['summary']['saldo_akhir'], 0, ',', '.')];
        $export_data[] = [''];
        
        // Detail Pendapatan Lain
        if (!empty($all_data['pendapatan'])) {
            $export_data[] = ['DETAIL PENDAPATAN LAIN'];
            $export_data[] = ['No', 'Tanggal', 'No. Bukti', 'Kategori', 'Keterangan', 'Nominal', 'Dibuat Oleh'];
            $no = 1;
            foreach ($all_data['pendapatan'] as $row) {
                $export_data[] = [
                    $no++,
                    date('d/m/Y', strtotime($row['tanggal'])),
                    $row['no_bukti'] ?: '-',
                    $row['kategori'],
                    $row['keterangan'],
                    $row['nominal'],
                    $row['created_by']
                ];
            }
            $export_data[] = ['', '', '', '', 'TOTAL PENDAPATAN LAIN', $all_data['summary']['total_pendapatan'], ''];
            $export_data[] = [''];
        }
        
        // Detail Pembayaran Siswa
        if (!empty($all_data['pembayaran_siswa'])) {
            $export_data[] = ['DETAIL PEMBAYARAN SISWA'];
            $export_data[] = ['No', 'Tanggal', 'No. Kuitansi', 'NIS', 'Nama Siswa', 'Kelas', 'Jenis Pembayaran', 'Bulan/Tahun', 'Nominal', 'Metode', 'Petugas'];
            $no = 1;
            foreach ($all_data['pembayaran_siswa'] as $row) {
                $bulan_tahun = '';
                if ($row['bulan_pembayaran']) {
                    $bulan_tahun = date('m/Y', strtotime($row['bulan_pembayaran'] . '-01'));
                } elseif ($row['tahun_pembayaran']) {
                    $bulan_tahun = $row['tahun_pembayaran'];
                }
                
                $export_data[] = [
                    $no++,
                    date('d/m/Y', strtotime($row['tanggal_bayar'])),
                    $row['no_kuitansi'],
                    $row['nis'],
                    $row['nama_siswa'],
                    $row['nama_kelas'],
                    $row['nama_pembayaran'],
                    $bulan_tahun,
                    $row['nominal_bayar'],
                    ucfirst($row['metode_bayar']),
                    $row['petugas']
                ];
            }
            $export_data[] = ['', '', '', '', '', '', '', 'TOTAL PEMBAYARAN SISWA', $all_data['summary']['total_pembayaran_siswa'], '', ''];
            $export_data[] = [''];
        }
        
        // Detail Pengeluaran
        if (!empty($all_data['pengeluaran'])) {
            $export_data[] = ['DETAIL PENGELUARAN'];
            $export_data[] = ['No', 'Tanggal', 'No. Bukti', 'Kategori', 'Keterangan', 'Nominal', 'Dibuat Oleh'];
            $no = 1;
            foreach ($all_data['pengeluaran'] as $row) {
                $export_data[] = [
                    $no++,
                    date('d/m/Y', strtotime($row['tanggal'])),
                    $row['no_bukti'] ?: '-',
                    $row['kategori'],
                    $row['keterangan'],
                    $row['nominal'],
                    $row['created_by']
                ];
            }
            $export_data[] = ['', '', '', '', 'TOTAL PENGELUARAN', $all_data['summary']['total_pengeluaran'], ''];
            $export_data[] = [''];
        }
        
        // Rekap Harian
        if (!empty($all_data['rekap_harian'])) {
            $export_data[] = ['REKAP HARIAN'];
            $export_data[] = ['Tanggal', 'Pendapatan Lain', 'Pembayaran Siswa', 'Total Pemasukan', 'Pengeluaran', 'Saldo Harian'];
            foreach ($all_data['rekap_harian'] as $row) {
                $saldo_harian = ($row['pendapatan_lain'] + $row['pembayaran_siswa']) - $row['pengeluaran'];
                $export_data[] = [
                    date('d/m/Y', strtotime($row['tanggal'])),
                    $row['pendapatan_lain'],
                    $row['pembayaran_siswa'],
                    $row['pendapatan_lain'] + $row['pembayaran_siswa'],
                    $row['pengeluaran'],
                    $saldo_harian
                ];
            }
        }
        
        $filename = 'Laporan_Keuangan_Lengkap_' . date('Y-m-d', strtotime($start_date)) . '_sd_' . date('Y-m-d', strtotime($end_date));
        $exporter->exportToExcel($export_data, $filename);
    }
    
    private function getAllFinancialDataForExport($start_date, $end_date) {
        // Get all income data (t_pendapatan)
        $pendapatan_query = "SELECT 
                                p.tanggal,
                                p.no_bukti,
                                kp.nama_kategori as kategori,
                                p.keterangan,
                                p.nominal,
                                u.nama_lengkap as created_by,
                                p.created_at
                             FROM t_pendapatan p
                             LEFT JOIN m_kategori_pendapatan kp ON p.kategori_id = kp.id
                             LEFT JOIN m_users u ON p.user_id = u.id
                             WHERE DATE(p.tanggal) BETWEEN :start_date AND :end_date
                             ORDER BY p.tanggal, p.created_at";
        
        $stmt = $this->db->prepare($pendapatan_query);
        $stmt->bindValue(':start_date', $start_date);
        $stmt->bindValue(':end_date', $end_date);
        $stmt->execute();
        $pendapatan_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get all student payment data
        $pembayaran_query = "SELECT 
                                ps.tanggal_bayar,
                                ps.no_kuitansi,
                                s.nis,
                                s.nama_lengkap as nama_siswa,
                                k.nama_kelas,
                                jp.nama_pembayaran,
                                dps.bulan_pembayaran,
                                dps.tahun_pembayaran,
                                dps.nominal_bayar,
                                ps.metode_bayar,
                                u.nama_lengkap as petugas,
                                ps.created_at
                             FROM t_pembayaran_siswa ps
                             LEFT JOIN m_siswa s ON ps.siswa_id = s.id
                             LEFT JOIN m_kelas k ON s.kelas_id = k.id
                             LEFT JOIN t_detail_pembayaran_siswa dps ON ps.id = dps.pembayaran_id
                             LEFT JOIN m_jenis_pembayaran jp ON dps.jenis_pembayaran_id = jp.id
                             LEFT JOIN m_users u ON ps.user_id = u.id
                             WHERE DATE(ps.tanggal_bayar) BETWEEN :start_date AND :end_date
                             ORDER BY ps.tanggal_bayar, ps.created_at, dps.id";
        
        $stmt = $this->db->prepare($pembayaran_query);
        $stmt->bindValue(':start_date', $start_date);
        $stmt->bindValue(':end_date', $end_date);
        $stmt->execute();
        $pembayaran_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get all expense data
        $pengeluaran_query = "SELECT 
                                 e.tanggal,
                                 e.no_bukti,
                                 kp.nama_kategori as kategori,
                                 e.keterangan,
                                 e.nominal,
                                 u.nama_lengkap as created_by,
                                 e.created_at
                              FROM t_pengeluaran e
                              LEFT JOIN m_kategori_pengeluaran kp ON e.kategori_id = kp.id
                              LEFT JOIN m_users u ON e.user_id = u.id
                              WHERE DATE(e.tanggal) BETWEEN :start_date AND :end_date
                              ORDER BY e.tanggal, e.created_at";
        
        $stmt = $this->db->prepare($pengeluaran_query);
        $stmt->bindValue(':start_date', $start_date);
        $stmt->bindValue(':end_date', $end_date);
        $stmt->execute();
        $pengeluaran_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get daily summary
        $rekap_query = "SELECT 
                           tanggal,
                           SUM(pendapatan_lain) as pendapatan_lain,
                           SUM(pembayaran_siswa) as pembayaran_siswa,
                           SUM(pengeluaran) as pengeluaran
                        FROM (
                            SELECT DATE(tanggal) as tanggal, SUM(nominal) as pendapatan_lain, 0 as pembayaran_siswa, 0 as pengeluaran
                            FROM t_pendapatan 
                            WHERE DATE(tanggal) BETWEEN :start_date AND :end_date
                            GROUP BY DATE(tanggal)
                            
                            UNION ALL
                            
                            SELECT DATE(tanggal_bayar) as tanggal, 0 as pendapatan_lain, SUM(total_bayar) as pembayaran_siswa, 0 as pengeluaran
                            FROM t_pembayaran_siswa 
                            WHERE DATE(tanggal_bayar) BETWEEN :start_date2 AND :end_date2
                            GROUP BY DATE(tanggal_bayar)
                            
                            UNION ALL
                            
                            SELECT DATE(tanggal) as tanggal, 0 as pendapatan_lain, 0 as pembayaran_siswa, SUM(nominal) as pengeluaran
                            FROM t_pengeluaran 
                            WHERE DATE(tanggal) BETWEEN :start_date3 AND :end_date3
                            GROUP BY DATE(tanggal)
                        ) as combined
                        GROUP BY tanggal
                        ORDER BY tanggal";
        
        $stmt = $this->db->prepare($rekap_query);
        $stmt->bindValue(':start_date', $start_date);
        $stmt->bindValue(':end_date', $end_date);
        $stmt->bindValue(':start_date2', $start_date);
        $stmt->bindValue(':end_date2', $end_date);
        $stmt->bindValue(':start_date3', $start_date);
        $stmt->bindValue(':end_date3', $end_date);
        $stmt->execute();
        $rekap_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate totals
        $total_pendapatan = array_sum(array_column($pendapatan_data, 'nominal'));
        $total_pembayaran_siswa = array_sum(array_column($pembayaran_data, 'nominal_bayar'));
        $total_pengeluaran = array_sum(array_column($pengeluaran_data, 'nominal'));
        $total_pemasukan = $total_pendapatan + $total_pembayaran_siswa;
        $saldo_akhir = $total_pemasukan - $total_pengeluaran;
        
        return [
            'pendapatan' => $pendapatan_data,
            'pembayaran_siswa' => $pembayaran_data,
            'pengeluaran' => $pengeluaran_data,
            'rekap_harian' => $rekap_data,
            'summary' => [
                'total_pendapatan' => $total_pendapatan,
                'total_pembayaran_siswa' => $total_pembayaran_siswa,
                'total_pemasukan' => $total_pemasukan,
                'total_pengeluaran' => $total_pengeluaran,
                'saldo_akhir' => $saldo_akhir
            ]
        ];
    }
    
    private function exportArrearsToExcel($arrears_data, $kelas_id) {
        $exporter = new ExcelExporter();
        
        // Get class name for filename
        $kelas_name = 'Semua_Kelas';
        if (!empty($kelas_id)) {
            $stmt = $this->db->prepare("SELECT nama_kelas FROM m_kelas WHERE id = ?");
            $stmt->execute([$kelas_id]);
            $kelas = $stmt->fetch();
            if ($kelas) {
                $kelas_name = str_replace(' ', '_', $kelas['nama_kelas']);
            }
        }
        
        // Prepare data for export
        $export_data = [];
        
        // Add header
        $export_data[] = ['LAPORAN TUNGGAKAN SISWA'];
        $export_data[] = ['Tanggal: ' . date('d/m/Y')];
        $export_data[] = ['Kelas: ' . ($kelas_name === 'Semua_Kelas' ? 'Semua Kelas' : str_replace('_', ' ', $kelas_name))];
        $export_data[] = [''];
        
        // Add table header
        $export_data[] = ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Total Tagihan', 'Sudah Bayar', 'Tunggakan', 'Total Nominal Tunggakan', 'Detail Tunggakan'];
        
        // Add data
        $no = 1;
        foreach ($arrears_data as $row) {
            $export_data[] = [
                $no++,
                "'" . $row['nis'],
                $row['nama_lengkap'],
                $row['nama_kelas'],
                $row['total_tagihan'],
                $row['sudah_bayar'],
                $row['tunggakan'],
                'Rp ' . number_format($row['total_nominal_tunggakan'], 0, ',', '.'),
                $row['detail_tunggakan'] ?: 'Tidak ada tunggakan'
            ];
        }
        
        $filename = 'Laporan_Tunggakan_' . $kelas_name . '_' . date('Y-m-d');
        $exporter->exportToExcel($export_data, $filename);
    }
    
    public function salary() {
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        
        // Get attendance data for salary calculation
        $query = "SELECT p.*, j.nama_jabatan,
                         COUNT(pp.id) as total_hadir,
                         SUM(CASE WHEN pp.status_kehadiran = 'hadir' THEN 1 ELSE 0 END) as hadir,
                         SUM(CASE WHEN pp.status_kehadiran = 'izin' THEN 1 ELSE 0 END) as izin,
                         SUM(CASE WHEN pp.status_kehadiran = 'sakit' THEN 1 ELSE 0 END) as sakit,
                         SUM(CASE WHEN pp.status_kehadiran = 'alpa' THEN 1 ELSE 0 END) as alpha
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
