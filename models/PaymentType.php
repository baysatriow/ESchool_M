<?php

require_once 'BaseModel.php';

class PaymentType extends BaseModel {
    protected $table_name = "m_jenis_pembayaran";
    
    public function __construct($db) {
        parent::__construct($db);
    }
    
    public function isExists($kode_pembayaran, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE kode_pembayaran = :kode_pembayaran";
        
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':kode_pembayaran', $kode_pembayaran);
        
        if ($excludeId) {
            $stmt->bindValue(':exclude_id', $excludeId);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }


    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (kode_pembayaran, nama_pembayaran, tipe, keterangan) 
                  VALUES (:kode_pembayaran, :nama_pembayaran, :tipe, :keterangan)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':kode_pembayaran', $data['kode_pembayaran']);
        $stmt->bindValue(':nama_pembayaran', $data['nama_pembayaran']);
        $stmt->bindValue(':tipe', $data['tipe']);
        $stmt->bindValue(':keterangan', $data['keterangan']);
        
        return $stmt->execute();
    }
    
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET kode_pembayaran = :kode_pembayaran, 
                      nama_pembayaran = :nama_pembayaran, 
                      tipe = :tipe, 
                      keterangan = :keterangan 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':kode_pembayaran', $data['kode_pembayaran']);
        $stmt->bindValue(':nama_pembayaran', $data['nama_pembayaran']);
        $stmt->bindValue(':tipe', $data['tipe']);
        $stmt->bindValue(':keterangan', $data['keterangan']);
        
        return $stmt->execute();
    }
    
    public function delete($id) {
        // Check if payment type is being used
        $checkQuery = "SELECT COUNT(*) FROM m_data_pembayaran WHERE jenis_pembayaran_id = :id";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindValue(':id', $id);
        $checkStmt->execute();
        
        if ($checkStmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Jenis pembayaran tidak dapat dihapus karena sedang digunakan'];
        }
        
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Jenis pembayaran berhasil dihapus'];
        } else {
            return ['success' => false, 'message' => 'Gagal menghapus jenis pembayaran'];
        }
    }
    
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nama_pembayaran";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPaymentTypesWithUsage() {
        $query = "SELECT jp.*, 
                         COUNT(DISTINCT dp.id) as jumlah_data_pembayaran,
                         COUNT(DISTINCT aps.id) as jumlah_assignment
                  FROM " . $this->table_name . " jp 
                  LEFT JOIN m_data_pembayaran dp ON jp.id = dp.jenis_pembayaran_id
                  LEFT JOIN t_assign_pembayaran_siswa aps ON dp.id = aps.data_pembayaran_id
                  GROUP BY jp.id 
                  ORDER BY jp.nama_pembayaran";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
