<?php

require_once 'models/Class.php';

class ClassController extends BaseController {
    
    public function index() {
        $class = new ClassModel($this->db);
        $classes = $class->getClassesWithStudentCount();
        
        $data = [
            'page_title' => 'Data Kelas',
            'classes' => $classes,
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
