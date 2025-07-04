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
            'additional_css' => 1,
            'additional_js' => 1
        ];
        $this->view('positions/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $position = new Position($this->db);
            $nama_jabatan = trim($_POST['nama_jabatan']);
            $data = [
                'nama_jabatan' => trim($_POST['nama_jabatan']),
                'keterangan' => trim($_POST['keterangan']) ?: null
            ];
            
            try {
                if ($position->isExists($nama_jabatan)) {
                    $this->redirect('positions', 'Nama jabatan ' . $nama_jabatan . ' sudah ada! Silakan gunakan nama jabatan yang berbeda.', 'error');
                    return;
                }

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
            $nama_jabatan = trim($_POST['nama_jabatan']);
            $id = $_POST['id'];
            $data = [
                'nama_jabatan' => trim($_POST['nama_jabatan']),
                'keterangan' => trim($_POST['keterangan']) ?: null
            ];
            
            try {
                if ($position->isExists($nama_jabatan, $id)) {
                    $this->redirect('positions', 'Nama jabatan ' . $nama_jabatan . ' sudah ada! Silakan gunakan nama jabatan yang berbeda.', 'error');
                    return;
                }

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
