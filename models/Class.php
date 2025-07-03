<?php
require_once 'BaseModel.php';

class ClassModel extends BaseModel {
    protected $table_name = 'm_kelas';
    
    public function __construct($db) {
        parent::__construct($db);
    }
    
    public function isExists($nama_kelas, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE nama_kelas = :nama_kelas";
        
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nama_kelas', $nama_kelas);
        
        if ($excludeId) {
            $stmt->bindValue(':exclude_id', $excludeId);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    public function getClassesWithStudentCount() {
        $query = "SELECT k.*, COUNT(s.id) as jumlah_siswa 
                  FROM m_kelas k 
                  LEFT JOIN m_siswa s ON k.id = s.kelas_id 
                  GROUP BY k.id 
                  ORDER BY k.tingkat ASC, k.nama_kelas ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getActiveClasses() {
        $query = "SELECT * FROM m_kelas ORDER BY tingkat ASC, nama_kelas ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findAll($orderBy = 'tingkat', $orderDirection = 'ASC') {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY {$orderBy} {$orderDirection}, nama_kelas ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY tingkat, nama_kelas";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
