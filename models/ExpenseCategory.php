<?php

class ExpenseCategory extends BaseModel {
    protected $table_name = "m_kategori_pengeluaran";
    
    public function getCategoriesWithTotal() {
        $query = "SELECT kp.*, 
                         COALESCE(SUM(p.nominal), 0) as total_pengeluaran,
                         COUNT(p.id) as jumlah_transaksi
                  FROM " . $this->table_name . " kp 
                  LEFT JOIN t_pengeluaran p ON kp.id = p.kategori_id
                  GROUP BY kp.id 
                  ORDER BY kp.nama_kategori";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
