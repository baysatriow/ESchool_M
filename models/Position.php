<?php

class Position extends BaseModel {
    protected $table_name = "m_jabatan";
    
    public function getPositionsWithEmployeeCount() {
        $query = "SELECT j.*, COUNT(p.id) as jumlah_pegawai 
                  FROM " . $this->table_name . " j 
                  LEFT JOIN m_pegawai p ON j.id = p.jabatan_id AND p.status = 'aktif'
                  GROUP BY j.id 
                  ORDER BY j.nama_jabatan";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function isExists($position, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE nama_jabatan = :nama_jabatan";
        
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nama_jabatan', $position);
        
        if ($excludeId) {
            $stmt->bindValue(':exclude_id', $excludeId);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    public function canDelete($id) {
        $query = "SELECT COUNT(*) as count FROM m_pegawai WHERE jabatan_id = ? AND status = 'aktif'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] == 0;
    }
}
