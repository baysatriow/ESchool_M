<?php

class Employee extends BaseModel {
    protected $table_name = "m_pegawai";
    
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

    public function getPayrollHistory($employee_id) {
        $query = "SELECT p.*, u.nama_lengkap as processed_by
                  FROM t_penggajian p
                  LEFT JOIN m_users u ON p.user_id = u.id
                  WHERE p.pegawai_id = :employee_id
                  ORDER BY p.periode_gaji DESC
                  LIMIT 12";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':employee_id', $employee_id);
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
