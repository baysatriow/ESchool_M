<?php

require_once 'controllers/BaseController.php';
require_once 'models/Income.php';
require_once 'helpers/FileUpload.php';

class IncomeController extends BaseController {
    
    public function index() {
        $income = new Income($this->db);
        $incomes = $income->getIncomesWithCategory();
        $categories = $income->getIncomeCategories();
        
        $data = [
            'page_title' => 'Data Pendapatan',
            'incomes' => $incomes,
            'categories' => $categories
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
                'no_bukti' => !empty($_POST['no_bukti']) ? $_POST['no_bukti'] : null, // Will be auto-generated if empty
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
                'no_bukti' => $_POST['no_bukti'],
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
}
