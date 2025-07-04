<?php

class Employee extends BaseModel {
    protected $table_name = "m_pegawai";
    
    public function isExists($niy, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE nip = :nip";
        
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nip', $niy);
        
        if ($excludeId) {
            $stmt->bindValue(':exclude_id', $excludeId);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    public function getEmployeesWithDetails() {
        $query = "SELECT p.*, j.nama_jabatan, u.username 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN m_jabatan j ON p.jabatan_id = j.id 
                  LEFT JOIN m_users u ON p.user_id = u.id 
                  ORDER BY p.nama_lengkap";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmployeeWithFullDetails($id) {
        $query = "SELECT p.*, j.nama_jabatan, j.keterangan as jabatan_keterangan, u.username, u.role
                  FROM " . $this->table_name . " p 
                  LEFT JOIN m_jabatan j ON p.jabatan_id = j.id 
                  LEFT JOIN m_users u ON p.user_id = u.id 
                  WHERE p.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAttendanceHistory($employee_id, $days = 30) {
        $query = "SELECT * FROM t_presensi_pegawai 
                  WHERE pegawai_id = :employee_id 
                  AND tanggal >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                  ORDER BY tanggal DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':employee_id', $employee_id);
        $stmt->bindValue(':days', $days);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateSalary($id, $gaji_pokok) {
        return $this->update($id, ['gaji_pokok' => $gaji_pokok]);
    }

    public function getEmployeeWithSalary($id) {
        $query = "SELECT p.*, j.nama_jabatan, u.username 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN m_jabatan j ON p.jabatan_id = j.id 
                  LEFT JOIN m_users u ON p.user_id = u.id 
                  WHERE p.id = :id";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
