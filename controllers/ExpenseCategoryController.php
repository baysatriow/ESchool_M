<?php

require_once 'models/BaseModel.php';
require_once 'models/ExpenseCategory.php';

class ExpenseCategoryController extends BaseController {
    
    public function index() {
        $expenseCategory = new ExpenseCategory($this->db);
        $expenseCategories = $expenseCategory->getCategoriesWithTotal();
        
        $data = [
            'page_title' => 'Kategori Pengeluaran',
            'expense_categories' => $expenseCategories,
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
        
        $this->view('expense-categories/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $expenseCategory = new ExpenseCategory($this->db);
            
            $data = [
                'nama_kategori' => $_POST['nama_kategori'],
                'keterangan' => $_POST['keterangan'] ?? ''
            ];
            
            try {
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
                'nama_kategori' => $_POST['nama_kategori'],
                'keterangan' => $_POST['keterangan']
            ];
            
            try {
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
