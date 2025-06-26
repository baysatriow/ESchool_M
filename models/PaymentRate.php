<?php

class PaymentRate extends BaseModel {
    protected $table_name = "m_tarif_pembayaran";
    
    public function getRatesByClassAndYear($kelas_id, $tahun_ajaran_id) {
        $query = "SELECT tp.*, jp.nama_pembayaran, jp.kode_pembayaran
                  FROM " . $this->table_name . " tp
                  LEFT JOIN m_jenis_pembayaran jp ON tp.jenis_pembayaran_id = jp.id
                  WHERE tp.kelas_id = :kelas_id AND tp.tahun_ajaran_id = :tahun_ajaran_id
                  ORDER BY jp.nama_pembayaran";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':kelas_id', $kelas_id);
        $stmt->bindValue(':tahun_ajaran_id', $tahun_ajaran_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getRatesWithDetails() {
        $query = "SELECT tp.*, jp.nama_pembayaran, k.nama_kelas, ta.tahun_ajaran
                  FROM " . $this->table_name . " tp
                  LEFT JOIN m_jenis_pembayaran jp ON tp.jenis_pembayaran_id = jp.id
                  LEFT JOIN m_kelas k ON tp.kelas_id = k.id
                  LEFT JOIN m_tahun_ajaran ta ON tp.tahun_ajaran_id = ta.id
                  ORDER BY ta.tahun_ajaran DESC, k.tingkat, jp.nama_pembayaran";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
