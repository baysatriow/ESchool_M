<?php

require_once 'models/Class.php';

class ClassController extends BaseController {
    
    public function index() {
        $class = new ClassModel($this->db);
        $classes = $class->getClassesWithStudentCount();
        
        $data = [
            'page_title' => 'Data Kelas',
            'classes' => $classes,
            'additional_css' => 1,
            'additional_js' => 1
        ];
        
        $this->view('classes/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $class = new ClassModel($this->db);
            
            $data = [
                'nama_kelas' => $_POST['nama_kelas'],
                'tingkat' => $_POST['tingkat'],
                'kapasitas' => $_POST['kapasitas'] ?? 30
            ];
            
            try {
                if ($class->isExists($data['nama_kelas'])) {
                    $this->redirect('classes', 'Nama kelas ' . $data['nama_kelas'] . ' sudah ada! Silakan gunakan nama kelas yang berbeda.', 'error');
                    return;
                }
                
                $result = $class->create($data);
                if ($result) {
                    $this->redirect('classes', 'Data kelas berhasil ditambahkan!', 'success');
                } else {
                    $this->redirect('classes', 'Gagal menambahkan data kelas!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('classes', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $class = new ClassModel($this->db);
            
            $id = $_POST['id'];
            $data = [
                'nama_kelas' => $_POST['nama_kelas'],
                'tingkat' => $_POST['tingkat'],
                'kapasitas' => $_POST['kapasitas']
            ];
            
            try {
                if ($class->isExists($data['nama_kelas'], $id)) {
                    $this->redirect('classes', 'Nama kelas ' . $data['nama_kelas'] . ' sudah ada! Silakan gunakan nama kelas yang berbeda.', 'error');
                    return;
                }
                $result = $class->update($id, $data);
                if ($result) {
                    $this->redirect('classes', 'Data kelas berhasil diperbarui!', 'success');
                } else {
                    $this->redirect('classes', 'Gagal memperbarui data kelas!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('classes', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $class = new ClassModel($this->db);
            $id = $_POST['id'];
            
            try {
                $result = $class->delete($id);
                if ($result) {
                    $this->redirect('classes', 'Data kelas berhasil dihapus!', 'success');
                } else {
                    $this->redirect('classes', 'Gagal menghapus data kelas!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('classes', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
}
