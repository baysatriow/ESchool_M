<?php
require_once 'controllers/BaseController.php';
require_once 'models/ExpenseCategory.php';

class ExpenseCategoryController extends BaseController {
    
    public function index() {
        $expenseCategory = new ExpenseCategory($this->db);
        $categories = $expenseCategory->getExpenseCategoriesWithStats();
        
        $data = [
            'page_title' => 'Kategori Pengeluaran',
            'categories' => $categories,
            'additional_css' => 1,
            'additional_js' => 1
        ];
        
        $this->view('expense-categories/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $expenseCategory = new ExpenseCategory($this->db);
            
            $data = [
                'nama_kategori' => trim($_POST['nama_kategori']),
                'keterangan' => trim($_POST['keterangan'] ?? '')
            ];
            
            // Validation
            if (empty($data['nama_kategori'])) {
                $this->redirect('expense-categories', 'Nama kategori tidak boleh kosong!', 'error');
                return;
            }
            
            try {
                if ($expenseCategory->isExists($data['nama_kategori'])) {
                    $this->redirect('expense-categories', 'Nama Kategori ' . $data['nama_kategori'] . ' sudah ada! Silakan gunakan nama kategori yang berbeda.', 'error');
                    return;
                }
                $result = $expenseCategory->create($data);
                if ($result) {
                    $this->redirect('expense-categories', 'Kategori pengeluaran berhasil ditambahkan!', 'success');
                } else {
                    $this->redirect('expense-categories', 'Gagal menambahkan kategori pengeluaran!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('expense-categories', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $expenseCategory = new ExpenseCategory($this->db);
            
            $id = $_POST['id'];
            $data = [
                'nama_kategori' => trim($_POST['nama_kategori']),
                'keterangan' => trim($_POST['keterangan'] ?? '')
            ];
            
            // Validation
            if (empty($data['nama_kategori'])) {
                $this->redirect('expense-categories', 'Nama kategori tidak boleh kosong!', 'error');
                return;
            }
            
            try {
                if ($expenseCategory->isExists($data['nama_kategori'], $id)) {
                    $this->redirect('expense-categories', 'Nama Kategori ' . $data['nama_kategori'] . ' sudah ada! Silakan gunakan nama kategori yang berbeda.', 'error');
                    return;
                }
                $result = $expenseCategory->update($id, $data);
                if ($result) {
                    $this->redirect('expense-categories', 'Kategori pengeluaran berhasil diperbarui!', 'success');
                } else {
                    $this->redirect('expense-categories', 'Gagal memperbarui kategori pengeluaran!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('expense-categories', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $expenseCategory = new ExpenseCategory($this->db);
            $id = $_POST['id'];
            
            try {
                // Check if category is being used
                if ($expenseCategory->isUsed($id)) {
                    $this->redirect('expense-categories', 'Kategori tidak dapat dihapus karena masih digunakan!', 'error');
                    return;
                }
                
                $result = $expenseCategory->delete($id);
                if ($result) {
                    $this->redirect('expense-categories', 'Kategori pengeluaran berhasil dihapus!', 'success');
                } else {
                    $this->redirect('expense-categories', 'Gagal menghapus kategori pengeluaran!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('expense-categories', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
}
