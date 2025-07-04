<?php
require_once 'controllers/BaseController.php';
require_once 'models/IncomeCategory.php';

class IncomeCategoryController extends BaseController {
    
    public function index() {
        $incomeCategory = new IncomeCategory($this->db);
        $categories = $incomeCategory->getIncomeCategoriesWithStats();
        
        $data = [
            'page_title' => 'Kategori Pendapatan',
            'categories' => $categories,
            'additional_css' => 1,
            'additional_js' => 1
        ];
        
        $this->view('income-categories/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $incomeCategory = new IncomeCategory($this->db);
            
            $data = [
                'nama_kategori' => trim($_POST['nama_kategori']),
                'keterangan' => trim($_POST['keterangan'] ?? '')
            ];
            
            // Validation
            if (empty($data['nama_kategori'])) {
                $this->redirect('income-categories', 'Nama kategori tidak boleh kosong!', 'error');
                return;
            }
            
            try {
                if ($incomeCategory->isExists($data['nama_kategori'])) {
                    $this->redirect('income-categories', 'Nama Kategori ' . $data['nama_kategori'] . ' sudah ada! Silakan gunakan nama kategori yang berbeda.', 'error');
                    return;
                }
                $result = $incomeCategory->create($data);
                if ($result) {
                    $this->redirect('income-categories', 'Kategori pendapatan berhasil ditambahkan!', 'success');
                } else {
                    $this->redirect('income-categories', 'Gagal menambahkan kategori pendapatan!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('income-categories', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $incomeCategory = new IncomeCategory($this->db);
            
            $id = $_POST['id'];
            $data = [
                'nama_kategori' => trim($_POST['nama_kategori']),
                'keterangan' => trim($_POST['keterangan'] ?? '')
            ];
            
            // Validation
            if (empty($data['nama_kategori'])) {
                $this->redirect('income-categories', 'Nama kategori tidak boleh kosong!', 'error');
                return;
            }
            
            try {
                if ($incomeCategory->isExists($data['nama_kategori'], $id)) {
                    $this->redirect('income-categories', 'Nama Kategori ' . $data['nama_kategori'] . ' sudah ada! Silakan gunakan nama kategori yang berbeda.', 'error');
                    return;
                }
                
                $result = $incomeCategory->update($id, $data);
                if ($result) {
                    $this->redirect('income-categories', 'Kategori pendapatan berhasil diperbarui!', 'success');
                } else {
                    $this->redirect('income-categories', 'Gagal memperbarui kategori pendapatan!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('income-categories', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $incomeCategory = new IncomeCategory($this->db);
            $id = $_POST['id'];
            
            try {
                // Check if category is being used
                if ($incomeCategory->isUsed($id)) {
                    $this->redirect('income-categories', 'Kategori tidak dapat dihapus karena masih digunakan!', 'error');
                    return;
                }
                
                $result = $incomeCategory->delete($id);
                if ($result) {
                    $this->redirect('income-categories', 'Kategori pendapatan berhasil dihapus!', 'success');
                } else {
                    $this->redirect('income-categories', 'Gagal menghapus kategori pendapatan!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('income-categories', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
}
