<?php

class PaymentType extends BaseModel {
    protected $table_name = "m_jenis_pembayaran";
    
    public function getPaymentTypesWithTotal() {
        $query = "SELECT jp.*, 
                         COALESCE(SUM(dp.nominal_bayar), 0) as total_pembayaran,
                         COUNT(DISTINCT dp.pembayaran_id) as jumlah_transaksi
                  FROM " . $this->table_name . " jp 
                  LEFT JOIN t_detail_pembayaran_siswa dp ON jp.id = dp.jenis_pembayaran_id
                  GROUP BY jp.id 
                  ORDER BY jp.nama_pembayaran";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
