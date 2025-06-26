<?php

require_once 'models/BaseModel.php';
require_once 'models/IncomeCategory.php';

class IncomeCategoryController extends BaseController {
    
    public function index() {
        $incomeCategory = new IncomeCategory($this->db);
        $categories = $incomeCategory->getIncomeCategoriesWithStats();
        
        $data = [
            'page_title' => 'Kategori Pendapatan',
            'categories' => $categories,
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
        
        $this->view('income-categories/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $incomeCategory = new IncomeCategory($this->db);
            
            $data = [
                'nama_kategori' => $_POST['nama_kategori'],
                'keterangan' => $_POST['keterangan'] ?? ''
            ];
            
            try {
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
                'nama_kategori' => $_POST['nama_kategori'],
                'keterangan' => $_POST['keterangan']
            ];
            
            try {
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
                $query = "SELECT COUNT(*) as count FROM t_pendapatan WHERE kategori_id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result['count'] > 0) {
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
