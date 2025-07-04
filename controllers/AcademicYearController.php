<?php
class AcademicYearController extends BaseController {
    
    public function index() {
        $academicYear = new AcademicYear($this->db);
        $academicYears = $academicYear->read();
        
        $data = [
            'page_title' => 'Tahun Ajaran',
            'academic_years' => $academicYears,
            'additional_css' => 1,
            'additional_js' => 1
        ];
        
        $this->view('academic-years/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $academicYear = new AcademicYear($this->db);
            
            // Ambil data dari form
            $tahunMulai = $_POST['tahun_mulai'];
            $tahunSelesai = $_POST['tahun_selesai'];
            $tahunAjaran = $tahunMulai . '/' . $tahunSelesai;
            
            $data = [
                'tahun_ajaran' => $tahunAjaran,
                'bulan_mulai' => (int)$_POST['bulan_mulai'],
                'bulan_selesai' => (int)$_POST['bulan_selesai'],
                'status' => $_POST['status'] ?? 'tidak_aktif'
            ];
            
            // Validate months
            if ($data['bulan_mulai'] < 1 || $data['bulan_mulai'] > 12 ||
                $data['bulan_selesai'] < 1 || $data['bulan_selesai'] > 12) {
                $this->redirect('academic-years', 'Bulan tidak valid! Pilih bulan 1-12.', 'error');
                return;
            }
            
            // Validate tahun
            if ($tahunMulai >= $tahunSelesai) {
                $this->redirect('academic-years', 'Tahun selesai harus lebih besar dari tahun mulai!', 'error');
                return;
            }
            
            try {
                // Cek apakah tahun ajaran sudah ada
                if ($academicYear->isExists($tahunAjaran)) {
                    $this->redirect('academic-years', 'Tahun ajaran ' . $tahunAjaran . ' sudah ada! Silakan gunakan tahun ajaran yang berbeda.', 'error');
                    return;
                }
                
                // If setting as active, deactivate others first
                if ($data['status'] === 'aktif') {
                    $this->db->prepare("UPDATE m_tahun_ajaran SET status = 'tidak_aktif'")->execute();
                }
                
                $result = $academicYear->create($data);
                if ($result) {
                    $this->redirect('academic-years', 'Tahun ajaran berhasil ditambahkan!', 'success');
                } else {
                    $this->redirect('academic-years', 'Gagal menambahkan tahun ajaran!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('academic-years', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $academicYear = new AcademicYear($this->db);
            
            $id = $_POST['id'];
            $tahunMulai = $_POST['tahun_mulai'];
            $tahunSelesai = $_POST['tahun_selesai'];
            $tahunAjaran = $tahunMulai . '/' . $tahunSelesai;
            
            $data = [
                'tahun_ajaran' => $tahunAjaran,
                'bulan_mulai' => (int)$_POST['bulan_mulai'],
                'bulan_selesai' => (int)$_POST['bulan_selesai'],
                'status' => $_POST['status']
            ];
            
            // Validate months
            if ($data['bulan_mulai'] < 1 || $data['bulan_mulai'] > 12 ||
                $data['bulan_selesai'] < 1 || $data['bulan_selesai'] > 12) {
                $this->redirect('academic-years', 'Bulan tidak valid! Pilih bulan 1-12.', 'error');
                return;
            }
            
            // Validate tahun
            if ($tahunMulai >= $tahunSelesai) {
                $this->redirect('academic-years', 'Tahun selesai harus lebih besar dari tahun mulai!', 'error');
                return;
            }
            
            try {
                // Cek apakah tahun ajaran sudah ada (kecuali untuk data yang sedang diedit)
                if ($academicYear->isExists($tahunAjaran, $id)) {
                    $this->redirect('academic-years', 'Tahun ajaran ' . $tahunAjaran . ' sudah ada! Silakan gunakan tahun ajaran yang berbeda.', 'error');
                    return;
                }
                
                // If setting as active, deactivate others first
                if ($data['status'] === 'aktif') {
                    $this->db->prepare("UPDATE m_tahun_ajaran SET status = 'tidak_aktif'")->execute();
                }
                
                $result = $academicYear->update($id, $data);
                if ($result) {
                    $this->redirect('academic-years', 'Tahun ajaran berhasil diperbarui!', 'success');
                } else {
                    $this->redirect('academic-years', 'Gagal memperbarui tahun ajaran!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('academic-years', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $academicYear = new AcademicYear($this->db);
            $id = $_POST['id'];
            
            try {
                $result = $academicYear->delete($id);
                if ($result) {
                    $this->redirect('academic-years', 'Tahun ajaran berhasil dihapus!', 'success');
                } else {
                    $this->redirect('academic-years', 'Gagal menghapus tahun ajaran!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('academic-years', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
}
?>