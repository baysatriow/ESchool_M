<?php

require_once 'models/Expense.php';
require_once 'models/ExpenseCategory.php';

class ExpenseController extends BaseController {
    
    public function index() {
        $expense = new Expense($this->db);
        $expenses = $expense->getExpensesWithCategory();
        
        $data = [
            'page_title' => 'Data Pengeluaran',
            'expenses' => $expenses,
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
        
        $this->view('expenses/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $expense = new Expense($this->db);
            
            $data = [
                'kategori_id' => $_POST['kategori_id'],
                'user_id' => Session::get('user_id'),
                'tanggal' => $_POST['tanggal'],
                'no_bukti' => $_POST['no_bukti'],
                'keterangan' => $_POST['keterangan'],
                'nominal' => $_POST['nominal']
            ];
            
            try {
                $result = $expense->createExpenseWithMutation($data);
                if ($result) {
                    $this->redirect('expenses', 'Data pengeluaran berhasil ditambahkan!', 'success');
                } else {
                    $this->redirect('expenses', 'Gagal menambahkan data pengeluaran!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('expenses', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $expense = new Expense($this->db);
            
            $id = $_POST['id'];
            $data = [
                'kategori_id' => $_POST['kategori_id'],
                'tanggal' => $_POST['tanggal'],
                'no_bukti' => $_POST['no_bukti'],
                'keterangan' => $_POST['keterangan'],
                'nominal' => $_POST['nominal']
            ];
            
            try {
                $result = $expense->update($id, $data);
                if ($result) {
                    $this->redirect('expenses', 'Data pengeluaran berhasil diperbarui!', 'success');
                } else {
                    $this->redirect('expenses', 'Gagal memperbarui data pengeluaran!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('expenses', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $expense = new Expense($this->db);
            $id = $_POST['id'];
            
            try {
                $result = $expense->delete($id);
                if ($result) {
                    $this->redirect('expenses', 'Data pengeluaran berhasil dihapus!', 'success');
                } else {
                    $this->redirect('expenses', 'Gagal menghapus data pengeluaran!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('expenses', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
}
