<?php

require_once 'models/BaseModel.php';

class StudentStatus extends BaseModel {
    protected $table_name = "m_siswa";
    
    public function getStudentsWithClass($kelas_id = null) {
        $query = "SELECT s.*, k.nama_kelas, k.tingkat 
                  FROM " . $this->table_name . " s 
                  LEFT JOIN m_kelas k ON s.kelas_id = k.id";
        
        $params = [];
        if ($kelas_id) {
            $query .= " WHERE s.kelas_id = :kelas_id";
            $params[':kelas_id'] = $kelas_id;
        }
        
        $query .= " ORDER BY k.tingkat ASC, k.nama_kelas ASC, s.nama_lengkap ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
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
    
    // Update status dan kelas sekaligus dengan logging dalam satu baris (dengan transaction)
    public function updateStatusAndClass($id, $new_status, $new_class_id, $user_id, $keterangan = '') {
        $this->conn->beginTransaction();
        
        try {
            $result = $this->updateStatusAndClassWithoutTransaction($id, $new_status, $new_class_id, $user_id, $keterangan);
            
            if ($result) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollback();
                return false;
            }
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    // Update status dan kelas tanpa transaction (untuk bulk operations)
    public function updateStatusAndClassWithoutTransaction($id, $new_status, $new_class_id, $user_id, $keterangan = '') {
        // Get current data with class name
        $current = $this->getStudentWithDetails($id);
        if (!$current) return false;
        
        // Get new class name if class_id is provided
        $new_class_name = null;
        if (!empty($new_class_id)) {
            $new_class_query = "SELECT nama_kelas FROM m_kelas WHERE id = :id";
            $stmt = $this->conn->prepare($new_class_query);
            $stmt->bindValue(':id', $new_class_id);
            $stmt->execute();
            $new_class = $stmt->fetch(PDO::FETCH_ASSOC);
            $new_class_name = $new_class['nama_kelas'] ?? null;
        }
        
        // Update student status and class
        $update_data = [
            'status' => $new_status
        ];
        
        // Only update class if provided
        if (!empty($new_class_id)) {
            $update_data['kelas_id'] = $new_class_id;
        }
        
        $result = $this->update($id, $update_data);
        
        if ($result) {
            // Log perubahan dalam satu baris
            $this->logStatusAndClassChange(
                $id, 
                $user_id, 
                $current['status'], 
                $new_status,
                $current['nama_kelas'] ?? null,
                $new_class_name,
                $keterangan
            );
        }
        
        return $result;
    }
    
    // Bulk update status dan kelas (diperbaiki untuk menghindari nested transaction)
    public function bulkUpdateStatusAndClass($student_ids, $new_status, $new_class_id, $user_id, $keterangan = '') {
        if (empty($student_ids)) return false;
        
        $success_count = 0;
        $failed_students = [];
        
        $this->conn->beginTransaction();
        
        try {
            foreach ($student_ids as $student_id) {
                $result = $this->updateStatusAndClassWithoutTransaction($student_id, $new_status, $new_class_id, $user_id, $keterangan);
                if ($result) {
                    $success_count++;
                } else {
                    $failed_students[] = $student_id;
                }
            }
            
            $this->conn->commit();
            
            // Return array dengan informasi detail
            return [
                'success_count' => $success_count,
                'failed_count' => count($failed_students),
                'failed_students' => $failed_students,
                'total_processed' => count($student_ids)
            ];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    // Log perubahan status dan kelas dalam satu baris
    private function logStatusAndClassChange($siswa_id, $user_id, $status_lama, $status_baru, $kelas_lama, $kelas_baru, $keterangan) {
        $log_query = "INSERT INTO log_status_siswa 
                     (siswa_id, updated_by_user_id, status_sebelum, status_sesudah, kelas_sebelum, kelas_sesudah, tanggal_perubahan, keterangan) 
                     VALUES (:siswa_id, :user_id, :status_sebelum, :status_sesudah, :kelas_sebelum, :kelas_sesudah, NOW(), :keterangan)";
        
        $stmt = $this->conn->prepare($log_query);
        $stmt->bindValue(':siswa_id', $siswa_id);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':status_sebelum', $status_lama);
        $stmt->bindValue(':status_sesudah', $status_baru);
        $stmt->bindValue(':kelas_sebelum', $kelas_lama);
        $stmt->bindValue(':kelas_sesudah', $kelas_baru);
        $stmt->bindValue(':keterangan', $keterangan);
        
        return $stmt->execute();
    }
    
    public function getStatusHistory($student_id = null) {
        $query = "SELECT l.*, s.nama_lengkap as nama_siswa, s.nis, u.nama_lengkap as updated_by
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
    
    public function getStatusOptions() {
        return [
            'aktif' => 'Aktif',
            'lulus' => 'Lulus',
            'pindah' => 'Pindah',
            'dikeluarkan' => 'Dikeluarkan',
            'ALUMNI' => 'Alumni',
            'naik_kelas' => 'Naik Kelas'
        ];
    }
    
    // Get students count by status
    public function getStatusStatistics() {
        $query = "SELECT status, COUNT(*) as jumlah 
                  FROM " . $this->table_name . " 
                  GROUP BY status 
                  ORDER BY jumlah DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get recent changes for dashboard
    public function getRecentChanges($limit = 10) {
        $query = "SELECT l.*, s.nama_lengkap as nama_siswa, s.nis, u.nama_lengkap as updated_by
                  FROM log_status_siswa l 
                  LEFT JOIN m_siswa s ON l.siswa_id = s.id 
                  LEFT JOIN m_users u ON l.updated_by_user_id = u.id
                  ORDER BY l.tanggal_perubahan DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
