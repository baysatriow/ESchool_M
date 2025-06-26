<?php

class Student extends BaseModel {
    protected $table_name = "m_siswa";
    
    public function getStudentsWithClass() {
        $query = "SELECT s.*, k.nama_kelas 
                  FROM " . $this->table_name . " s 
                  LEFT JOIN m_kelas k ON s.kelas_id = k.id 
                  ORDER BY s.nama_lengkap";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getStudentWithDetails($id) {
        $query = "SELECT s.*, k.nama_kelas, k.tingkat
                  FROM " . $this->table_name . " s 
                  LEFT JOIN m_kelas k ON s.kelas_id = k.id 
                  WHERE s.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getPaymentHistory($student_id) {
        $query = "SELECT p.*, u.nama_lengkap as kasir
                  FROM t_pembayaran_siswa p
                  LEFT JOIN m_users u ON p.user_id = u.id
                  WHERE p.siswa_id = :student_id
                  ORDER BY p.tanggal_bayar DESC
                  LIMIT 10";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':student_id', $student_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateStatus($id, $new_status, $user_id, $keterangan = '') {
        // Get current status
        $current = $this->findById($id);
        if (!$current) return false;
        
        // Update student status
        $result = $this->update($id, ['status' => $new_status]);
        
        if ($result) {
            // Log status change
            $log_query = "INSERT INTO log_status_siswa 
                         (siswa_id, updated_by_user_id, status_sebelum, status_sesudah, tanggal_perubahan, keterangan) 
                         VALUES (:siswa_id, :user_id, :status_sebelum, :status_sesudah, NOW(), :keterangan)";
            
            $stmt = $this->conn->prepare($log_query);
            $stmt->bindValue(':siswa_id', $id);
            $stmt->bindValue(':user_id', $user_id);
            $stmt->bindValue(':status_sebelum', $current['status']);
            $stmt->bindValue(':status_sesudah', $new_status);
            $stmt->bindValue(':keterangan', $keterangan);
            
            $stmt->execute();
        }
        
        return $result;
    }
    
    public function getStatusHistory($student_id = null) {
        $query = "SELECT l.*, s.nama_lengkap as nama_siswa, u.nama_lengkap as updated_by 
                  FROM log_status_siswa l 
                  LEFT JOIN m_siswa s ON l.siswa_id = s.id 
                  LEFT JOIN m_users u ON l.updated_by_user_id = u.id";
        
        if ($student_id) {
            $query .= " WHERE l.siswa_id = :student_id";
        }
        
        $query .= " ORDER BY l.tanggal_perubahan DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($student_id) {
            $stmt->bindValue(':student_id', $student_id);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (kelas_id, nis, nama_lengkap, jenis_kelamin, tahun_masuk, status, nama_wali, no_hp_wali) 
                  VALUES (:kelas_id, :nis, :nama_lengkap, :jenis_kelamin, :tahun_masuk, :status, :nama_wali, :no_hp_wali)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':kelas_id', $data['kelas_id']);
        $stmt->bindValue(':nis', $data['nis']);
        $stmt->bindValue(':nama_lengkap', $data['nama_lengkap']);
        $stmt->bindValue(':jenis_kelamin', $data['jenis_kelamin']);
        $stmt->bindValue(':tahun_masuk', $data['tahun_masuk']);
        $stmt->bindValue(':status', $data['status'] ?? 'aktif');
        $stmt->bindValue(':nama_wali', $data['nama_wali'] ?? null);
        $stmt->bindValue(':no_hp_wali', $data['no_hp_wali'] ?? null);
        
        return $stmt->execute();
    }
    
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET kelas_id = :kelas_id, 
                      nis = :nis, 
                      nama_lengkap = :nama_lengkap, 
                      jenis_kelamin = :jenis_kelamin,
                      tahun_masuk = :tahun_masuk, 
                      status = :status,
                      nama_wali = :nama_wali,
                      no_hp_wali = :no_hp_wali,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':kelas_id', $data['kelas_id']);
        $stmt->bindValue(':nis', $data['nis']);
        $stmt->bindValue(':nama_lengkap', $data['nama_lengkap']);
        $stmt->bindValue(':jenis_kelamin', $data['jenis_kelamin']);
        $stmt->bindValue(':tahun_masuk', $data['tahun_masuk']);
        $stmt->bindValue(':status', $data['status']);
        $stmt->bindValue(':nama_wali', $data['nama_wali'] ?? null);
        $stmt->bindValue(':no_hp_wali', $data['no_hp_wali'] ?? null);
        
        return $stmt->execute();
    }
}
