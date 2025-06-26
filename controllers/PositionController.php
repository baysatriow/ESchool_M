<?php

require_once 'models/BaseModel.php';
require_once 'models/Position.php';

class PositionController extends BaseController {
    
    public function index() {
        $position = new Position($this->db);
        $positions = $position->getPositionsWithEmployeeCount();
        
        $data = [
            'page_title' => 'Data Jabatan',
            'positions' => $positions,
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
        $this->view('positions/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $position = new Position($this->db);
            
            $data = [
                'nama_jabatan' => trim($_POST['nama_jabatan']),
                'keterangan' => trim($_POST['keterangan']) ?: null
            ];
            
            try {
                $result = $position->create($data);
                if ($result) {
                    $this->redirect('positions', 'Data jabatan berhasil ditambahkan!', 'success');
                } else {
                    $this->redirect('positions', 'Gagal menambahkan data jabatan!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('positions', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $position = new Position($this->db);
            
            $id = $_POST['id'];
            $data = [
                'nama_jabatan' => trim($_POST['nama_jabatan']),
                'keterangan' => trim($_POST['keterangan']) ?: null
            ];
            
            try {
                $result = $position->update($id, $data);
                if ($result) {
                    $this->redirect('positions', 'Data jabatan berhasil diperbarui!', 'success');
                } else {
                    $this->redirect('positions', 'Gagal memperbarui data jabatan!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('positions', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $position = new Position($this->db);
            $id = $_POST['id'];
            
            try {
                // Check if position can be deleted
                if (!$position->canDelete($id)) {
                    $this->redirect('positions', 'Tidak dapat menghapus jabatan yang masih memiliki pegawai aktif!', 'error');
                    return;
                }
                
                $result = $position->delete($id);
                if ($result) {
                    $this->redirect('positions', 'Data jabatan berhasil dihapus!', 'success');
                } else {
                    $this->redirect('positions', 'Gagal menghapus data jabatan!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('positions', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
}
