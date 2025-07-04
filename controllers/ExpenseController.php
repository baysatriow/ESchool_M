<?php

require_once 'controllers/BaseController.php';
require_once 'models/Expense.php';
require_once 'helpers/FileUpload.php';
require_once 'helpers/ExcelExporter.php';

class ExpenseController extends BaseController {
    
    public function index() {
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-t');
        $kategori_id = $_GET['kategori_id'] ?? '';
        $export = $_GET['export'] ?? null;
        
        $expense = new Expense($this->db);
        
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
            $this->exportExpensesToExcel($filters);
            return;
        }
        
        // Get filtered expenses
        $expenses = $expense->getExpensesWithCategoryFiltered($filters);
        $categories = $expense->getExpenseCategories();
        
        // Get summary statistics with filters
        $summary = $expense->getExpenseSummaryCards($filters);
        
        $data = [
            'page_title' => 'Data Pengeluaran',
            'expenses' => $expenses,
            'categories' => $categories,
            'summary' => $summary,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'selected_kategori' => $kategori_id,
            'additional_css' => 1,
            'additional_js' => 1
        ];
        
        $this->view('expenses/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $expense = new Expense($this->db);
            
            $data = [
                'kategori_id' => $_POST['kategori_id'],
                'user_id' => Session::get('user_id'),
                'tanggal' => $_POST['tanggal'],
                'keterangan' => $_POST['keterangan'],
                'nominal' => $_POST['nominal']
            ];
            
            // Handle file upload
            if (isset($_FILES['bukti_foto']) && $_FILES['bukti_foto']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = FileUpload::uploadImage($_FILES['bukti_foto'], 'expense');
                
                if ($uploadResult['success']) {
                    $data['bukti_foto'] = $uploadResult['filename'];
                } else {
                    Session::setFlash('error', 'Error upload foto: ' . $uploadResult['message']);
                    $this->redirect('expenses');
                    return;
                }
            }
            
            try {
                $result = $expense->createExpenseWithMutation($data);
                if ($result) {
                    Session::setFlash('success', 'Data pengeluaran berhasil ditambahkan!');
                } else {
                    Session::setFlash('error', 'Gagal menambahkan data pengeluaran!');
                }
            } catch (Exception $e) {
                Session::setFlash('error', 'Error: ' . $e->getMessage());
            }
            
            $this->redirect('expenses');
        }
    }
    
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $expense = new Expense($this->db);
            $id = $_POST['id'];
            
            // Get existing data
            $existingData = $expense->getById($id);
            if (!$existingData) {
                Session::setFlash('error', 'Data pengeluaran tidak ditemukan!');
                $this->redirect('expenses');
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
                $uploadResult = FileUpload::uploadImage($_FILES['bukti_foto'], 'expense');
                
                if ($uploadResult['success']) {
                    $oldPhoto = $existingData['bukti_foto']; // Store old photo for deletion
                    $data['bukti_foto'] = $uploadResult['filename'];
                } else {
                    Session::setFlash('error', 'Error upload foto: ' . $uploadResult['message']);
                    $this->redirect('expenses');
                    return;
                }
            }
            
            try {
                $result = $expense->updateExpenseWithMutation($id, $data);
                if ($result) {
                    Session::setFlash('success', 'Data pengeluaran berhasil diperbarui!');
                    // Delete old photo if new photo was uploaded
                    if ($oldPhoto && isset($data['bukti_foto'])) {
                        FileUpload::deleteFile('expense', $oldPhoto);
                    }
                } else {
                    Session::setFlash('error', 'Gagal memperbarui data pengeluaran!');
                }
            } catch (Exception $e) {
                Session::setFlash('error', 'Error: ' . $e->getMessage());
                // Delete new photo if error occurred
                if (isset($data['bukti_foto'])) {
                    FileUpload::deleteFile('expense', $data['bukti_foto']);
                }
            }
            
            $this->redirect('expenses');
        }
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $expense = new Expense($this->db);
            $id = $_POST['id'];
            
            try {
                $result = $expense->deleteExpenseWithMutation($id);
                if ($result) {
                    Session::setFlash('success', 'Data pengeluaran berhasil dihapus!');
                } else {
                    Session::setFlash('error', 'Gagal menghapus data pengeluaran!');
                }
            } catch (Exception $e) {
                Session::setFlash('error', 'Error: ' . $e->getMessage());
            }
            
            $this->redirect('expenses');
        }
    }
    
    public function detail($id) {
        $expense = new Expense($this->db);
        $expenseData = $expense->getExpenseDetail($id);
        if ($expenseData) {
            $data = [
                'page_title' => 'Detail Data Pengeluaran',
                'expense' => $expenseData
            ];
            $this->view('expenses/detail', $data);
        } else {
            Session::setFlash('error', 'Data pengeluaran tidak ditemukan!');
            $this->redirect('expenses');
        }
    }
    
    public function receipt() {
        try {
            // Validasi parameter
            $expense_id = filter_input(INPUT_GET, 'expense_id', FILTER_VALIDATE_INT);
            
            if (!$expense_id) {
                $this->redirect('expenses', 'ID Pengeluaran tidak valid', 'error');
                return;
            }
            
            // Get expense data
            $expense = new Expense($this->db);
            $expenseData = $expense->getExpenseDetail($expense_id);
            
            if (!$expenseData) {
                $this->redirect('expenses', 'Data pengeluaran tidak ditemukan', 'error');
                return;
            }
            
            // Get school settings
            $schoolQuery = "SELECT * FROM app_pengaturan LIMIT 1";
            $schoolStmt = $this->db->prepare($schoolQuery);
            $schoolStmt->execute();
            $schoolInfo = $schoolStmt->fetch(PDO::FETCH_ASSOC);
            
            // Load receipt view
            $this->view('expenses/receipt', [
                'expense' => $expenseData,
                'school' => $schoolInfo
            ]);
            
        } catch (PDOException $e) {
            error_log('Database Error in receipt(): ' . $e->getMessage());
            $this->redirect('expenses', 'Terjadi kesalahan database', 'error');
        } catch (Exception $e) {
            error_log('Error in receipt(): ' . $e->getMessage());
            $this->redirect('expenses', 'Terjadi kesalahan: ' . $e->getMessage(), 'error');
        }
    }
    
    private function exportExpensesToExcel($filters = []) {
        $exporter = new ExcelExporter();
        $expense = new Expense($this->db);
        
        // Get filtered expense data
        $expenses = $expense->getExpensesWithCategoryFiltered($filters);
        $summary = $expense->getExpenseSummaryCards($filters);
        
        // Prepare data for export
        $export_data = [];
        
        // Add header
        $export_data[] = ['LAPORAN PENGELUARAN SEKOLAH'];
        
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
        $export_data[] = ['RINGKASAN PENGELUARAN'];
        $export_data[] = ['Total Transaksi', $summary['total_transaksi'] ?? 0];
        $export_data[] = ['Total Pengeluaran', 'Rp ' . number_format($summary['total_pengeluaran'] ?? 0, 0, ',', '.')];
        $export_data[] = ['Rata-rata Pengeluaran', 'Rp ' . number_format($summary['rata_rata_pengeluaran'] ?? 0, 0, ',', '.')];
        $export_data[] = ['Pengeluaran Bulan Ini', 'Rp ' . number_format($summary['pengeluaran_bulan_ini'] ?? 0, 0, ',', '.')];
        $export_data[] = [''];
        
        // Detail pengeluaran
        if (!empty($expenses)) {
            $export_data[] = ['DETAIL PENGELUARAN'];
            $export_data[] = ['No', 'Tanggal', 'No. Bukti', 'Kategori', 'Keterangan', 'Nominal', 'Dibuat Oleh', 'Waktu Input'];
            
            $no = 1;
            foreach ($expenses as $row) {
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
            
            $export_data[] = ['', '', '', '', 'TOTAL PENGELUARAN', $summary['total_pengeluaran'] ?? 0, '', ''];
        } else {
            $export_data[] = ['TIDAK ADA DATA PENGELUARAN'];
        }
        
        // Generate filename
        $filename = 'Laporan_Pengeluaran';
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
