<?php

class IncomeCategory extends BaseModel {
    protected $table_name = "m_kategori_pendapatan";
    
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
}
