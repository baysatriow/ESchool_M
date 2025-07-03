<?php

require_once 'controllers/BaseController.php';
require_once 'models/Income.php';
require_once 'helpers/FileUpload.php';
require_once 'helpers/ExcelExporter.php';

class IncomeController extends BaseController {
    
    public function index() {
        // Set default dates to current month if not provided
        $start_date = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
        $end_date = $_GET['end_date'] ?? date('Y-m-t');     // Last day of current month
        $kategori_id = $_GET['kategori_id'] ?? '';
        $export = $_GET['export'] ?? null;
        
        $income = new Income($this->db);
        
        // Build filters array
        $filters = [];
        if (!empty($start_date)) {
            $filters['tanggal_dari'] = $start_date;
        }
        if (!empty($end_date)) {
            $filters['tanggal_sampai'] = $end_date;
        }
        if (!empty($kategori_id)) {
            $filters['kategori_id'] = $kategori_id;
        }
        
        // If export is requested
        if ($export === 'excel') {
            $this->exportIncomesToExcel($filters);
            return;
        }
        
        // Get filtered incomes
        $incomes = $income->getIncomesWithCategoryFiltered($filters);
        $categories = $income->getIncomeCategories();
        
        // Get summary statistics with filters
        $summary = $income->getIncomeSummaryCards($filters);
        
        $data = [
            'page_title' => 'Data Pendapatan',
            'incomes' => $incomes,
            'categories' => $categories,
            'summary' => $summary,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'selected_kategori' => $kategori_id,
            'additional_css' => [
                'assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
                'assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
                'assets/libs/select2/css/select2.min.css',
                'assets/libs/select2/css/select2-bootstrap4.min.css'
            ],
            'additional_js' => [
                'assets/libs/datatables.net/js/jquery.dataTables.min.js',
                'assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js',
                'assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js',
                'assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js',
                'assets/libs/select2/js/select2.min.js'
            ]
        ];
        
        $this->view('income/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $income = new Income($this->db);
            
            $data = [
                'kategori_id' => $_POST['kategori_id'],
                'user_id' => Session::get('user_id'),
                'tanggal' => $_POST['tanggal'],
                'keterangan' => $_POST['keterangan'],
                'nominal' => $_POST['nominal']
            ];
            
            // Handle file upload
            if (isset($_FILES['bukti_foto']) && $_FILES['bukti_foto']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = FileUpload::uploadImage($_FILES['bukti_foto'], 'income');
                
                if ($uploadResult['success']) {
                    $data['bukti_foto'] = $uploadResult['filename'];
                } else {
                    Session::setFlash('error', 'Error upload foto: ' . $uploadResult['message']);
                    $this->redirect('income');
                    return;
                }
            }
            
            try {
                $result = $income->createIncomeWithMutation($data);
                if ($result) {
                    Session::setFlash('success', 'Data pendapatan berhasil ditambahkan!');
                } else {
                    Session::setFlash('error', 'Gagal menambahkan data pendapatan!');
                    // Delete uploaded file if database insert failed
                    if (isset($data['bukti_foto'])) {
                        FileUpload::deleteFile('income', $data['bukti_foto']);
                    }
                }
            } catch (Exception $e) {
                Session::setFlash('error', 'Error: ' . $e->getMessage());
                // Delete uploaded file if error occurred
                if (isset($data['bukti_foto'])) {
                    FileUpload::deleteFile('income', $data['bukti_foto']);
                }
            }
            
            $this->redirect('income');
        }
    }
    
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $income = new Income($this->db);
            $id = $_POST['id'];
            
            // Get existing data
            $existingData = $income->getById($id);
            if (!$existingData) {
                Session::setFlash('error', 'Data pendapatan tidak ditemukan!');
                $this->redirect('income');
                return;
            }
            
            $data = [
                'kategori_id' => $_POST['kategori_id'],
                'tanggal' => $_POST['tanggal'],
                'keterangan' => $_POST['keterangan'],
                'nominal' => $_POST['nominal']
            ];
            
            $oldPhoto = null;
            
            // Handle file upload
            if (isset($_FILES['bukti_foto']) && $_FILES['bukti_foto']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = FileUpload::uploadImage($_FILES['bukti_foto'], 'income');
                
                if ($uploadResult['success']) {
                    $oldPhoto = $existingData['bukti_foto']; // Store old photo for deletion
                    $data['bukti_foto'] = $uploadResult['filename'];
                } else {
                    Session::setFlash('error', 'Error upload foto: ' . $uploadResult['message']);
                    $this->redirect('income');
                    return;
                }
            }
            
            try {
                $result = $income->updateIncomeWithMutation($id, $data);
                if ($result) {
                    // Delete old photo if new photo was uploaded
                    if ($oldPhoto && isset($data['bukti_foto'])) {
                        FileUpload::deleteFile('income', $oldPhoto);
                    }
                    Session::setFlash('success', 'Data pendapatan berhasil diperbarui!');
                } else {
                    // Delete new photo if database update failed
                    if (isset($data['bukti_foto'])) {
                        FileUpload::deleteFile('income', $data['bukti_foto']);
                    }
                    Session::setFlash('error', 'Gagal memperbarui data pendapatan!');
                }
            } catch (Exception $e) {
                // Delete new photo if error occurred
                if (isset($data['bukti_foto'])) {
                    FileUpload::deleteFile('income', $data['bukti_foto']);
                }
                Session::setFlash('error', 'Error: ' . $e->getMessage());
            }
            
            $this->redirect('income');
        }
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $income = new Income($this->db);
            $id = $_POST['id'];
            
            try {
                $result = $income->deleteIncomeWithMutation($id);
                if ($result) {
                    Session::setFlash('success', 'Data pendapatan berhasil dihapus!');
                } else {
                    Session::setFlash('error', 'Gagal menghapus data pendapatan!');
                }
            } catch (Exception $e) {
                Session::setFlash('error', 'Error: ' . $e->getMessage());
            }
            
            $this->redirect('income');
        }
    }
    
    public function detail($id) {
        $income = new Income($this->db);
        $incomeData = $income->getIncomeDetail($id);
        if ($incomeData) {
            $data = [
                'page_title' => 'Detail Data Pendapatan',
                'income' => $incomeData
            ];
            $this->view('income/detail', $data);
        } else {
            Session::setFlash('error', 'Data pendapatan tidak ditemukan!');
            $this->redirect('income');
        }
    }
    
    public function receipt() {
        try {
            // Validasi parameter
            $income_id = filter_input(INPUT_GET, 'income_id', FILTER_VALIDATE_INT);
            
            if (!$income_id) {
                $this->redirect('income', 'ID Pendapatan tidak valid', 'error');
                return;
            }
            
            // Get income data
            $income = new Income($this->db);
            $incomeData = $income->getIncomeDetail($income_id);
            
            if (!$incomeData) {
                $this->redirect('income', 'Data pendapatan tidak ditemukan', 'error');
                return;
            }
            
            // Get school settings
            $schoolQuery = "SELECT * FROM app_pengaturan LIMIT 1";
            $schoolStmt = $this->db->prepare($schoolQuery);
            $schoolStmt->execute();
            $schoolInfo = $schoolStmt->fetch(PDO::FETCH_ASSOC);
            
            // Load receipt view
            $this->view('income/receipt', [
                'income' => $incomeData,
                'school' => $schoolInfo
            ]);
            
        } catch (PDOException $e) {
            error_log('Database Error in receipt(): ' . $e->getMessage());
            $this->redirect('income', 'Terjadi kesalahan database', 'error');
        } catch (Exception $e) {
            error_log('Error in receipt(): ' . $e->getMessage());
            $this->redirect('income', 'Terjadi kesalahan: ' . $e->getMessage(), 'error');
        }
    }
    
    private function exportIncomesToExcel($filters = []) {
        $exporter = new ExcelExporter();
        $income = new Income($this->db);
        
        // Get filtered income data
        $incomes = $income->getIncomesWithCategoryFiltered($filters);
        $summary = $income->getIncomeSummaryCards($filters);
        
        // Prepare data for export
        $export_data = [];
        
        // Add header
        $export_data[] = ['LAPORAN PENDAPATAN SEKOLAH'];
        
        // Add date range if filters applied
        $date_range = '';
        if (!empty($filters['tanggal_dari']) && !empty($filters['tanggal_sampai'])) {
            $date_range = 'Periode: ' . date('d/m/Y', strtotime($filters['tanggal_dari'])) . ' s/d ' . date('d/m/Y', strtotime($filters['tanggal_sampai']));
        } elseif (!empty($filters['tanggal_dari'])) {
            $date_range = 'Dari Tanggal: ' . date('d/m/Y', strtotime($filters['tanggal_dari']));
        } elseif (!empty($filters['tanggal_sampai'])) {
            $date_range = 'Sampai Tanggal: ' . date('d/m/Y', strtotime($filters['tanggal_sampai']));
        } else {
            $date_range = 'Semua Data';
        }
        
        $export_data[] = [$date_range];
        $export_data[] = ['Dicetak pada: ' . date('d/m/Y H:i:s')];
        $export_data[] = [''];
        
        // Summary section
        $export_data[] = ['RINGKASAN PENDAPATAN'];
        $export_data[] = ['Total Transaksi', $summary['total_transaksi'] ?? 0];
        $export_data[] = ['Total Pendapatan', 'Rp ' . number_format($summary['total_pendapatan'] ?? 0, 0, ',', '.')];
        $export_data[] = ['Rata-rata Pendapatan', 'Rp ' . number_format($summary['rata_rata_pendapatan'] ?? 0, 0, ',', '.')];
        $export_data[] = ['Pendapatan Bulan Ini', 'Rp ' . number_format($summary['pendapatan_bulan_ini'] ?? 0, 0, ',', '.')];
        $export_data[] = [''];
        
        // Detail pendapatan
        if (!empty($incomes)) {
            $export_data[] = ['DETAIL PENDAPATAN'];
            $export_data[] = ['No', 'Tanggal', 'No. Bukti', 'Kategori', 'Keterangan', 'Nominal', 'Dibuat Oleh', 'Waktu Input'];
            
            $no = 1;
            foreach ($incomes as $row) {
                $export_data[] = [
                    $no++,
                    date('d/m/Y', strtotime($row['tanggal'])),
                    $row['no_bukti'] ?: '-',
                    $row['nama_kategori'] ?: '-',
                    $row['keterangan'] ?: '-',
                    $row['nominal'] ?? 0,
                    $row['created_by'] ?: '-',
                    date('d/m/Y H:i:s', strtotime($row['created_at']))
                ];
            }
            
            $export_data[] = ['', '', '', '', 'TOTAL PENDAPATAN', $summary['total_pendapatan'] ?? 0, '', ''];
        } else {
            $export_data[] = ['TIDAK ADA DATA PENDAPATAN'];
        }
        
        // Generate filename
        $filename = 'Laporan_Pendapatan';
        if (!empty($filters['tanggal_dari']) && !empty($filters['tanggal_sampai'])) {
            $filename .= '_' . date('Y-m-d', strtotime($filters['tanggal_dari'])) . '_sd_' . date('Y-m-d', strtotime($filters['tanggal_sampai']));
        } elseif (!empty($filters['tanggal_dari'])) {
            $filename .= '_dari_' . date('Y-m-d', strtotime($filters['tanggal_dari']));
        } elseif (!empty($filters['tanggal_sampai'])) {
            $filename .= '_sampai_' . date('Y-m-d', strtotime($filters['tanggal_sampai']));
        } else {
            $filename .= '_' . date('Y-m-d');
        }
        
        $exporter->exportToExcel($export_data, $filename);
    }
}
