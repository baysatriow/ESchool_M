<?php
require_once 'models/BaseModel.php';

class IncomeCategory extends BaseModel {
    protected $table_name = "m_kategori_pendapatan";
    
    public function __construct($db) {
        parent::__construct($db);
    }
    
    public function getIncomeCategoriesWithStats() {
        $query = "SELECT kp.*, 
                         COALESCE(COUNT(p.id), 0) as total_transaksi,
                         COALESCE(SUM(p.nominal), 0) as total_nominal
                  FROM " . $this->table_name . " kp 
                  LEFT JOIN t_pendapatan p ON kp.id = p.kategori_id
                  GROUP BY kp.id 
                  ORDER BY kp.nama_kategori";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function isUsed($id) {
        $query = "SELECT COUNT(*) as count FROM t_pendapatan WHERE kategori_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }
}
