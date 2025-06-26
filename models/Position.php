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
    
    public function canDelete($id) {
        $query = "SELECT COUNT(*) as count FROM m_pegawai WHERE jabatan_id = ? AND status = 'aktif'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] == 0;
    }
}
