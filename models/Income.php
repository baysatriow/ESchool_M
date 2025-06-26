<?php

require_once 'models/BaseModel.php';
require_once 'helpers/FileUpload.php';

class Income extends BaseModel {
    protected $table_name = "t_pendapatan";
    
    public function generateReceiptNumber() {
        // Format: SDITXXXXXXPDPT where X is random number/letter
        $prefix = 'SDIT-';
        $suffix = '-PDPT';
        $randomPart = '';
        
        // Generate 6 random characters (numbers and letters)
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0; $i < 6; $i++) {
            $randomPart .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        $receiptNumber = $prefix . $randomPart . $suffix;
        
        // Check if receipt number already exists
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE no_bukti = :no_bukti";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':no_bukti', $receiptNumber);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If exists, generate new one recursively
        if ($result['count'] > 0) {
            return $this->generateReceiptNumber();
        }
        
        return $receiptNumber;
    }
    
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createIncomeWithMutation($income_data) {
        try {
            $this->conn->beginTransaction();
            
            // Always auto-generate receipt number (tidak bisa diubah user)
            $income_data['no_bukti'] = $this->generateReceiptNumber();
            
            // Create income record
            $income_id = $this->createAndGetId($income_data);
            
            // Create cash mutation record
            $mutation_data = [
                'user_id' => $income_data['user_id'],
                'tanggal' => $income_data['tanggal'] . ' ' . date('H:i:s'),
                'kode_transaksi' => $income_data['no_bukti'],
                'sumber_transaksi_id' => $income_id,
                'tipe_sumber' => 'PENDAPATAN',
                'keterangan' => $income_data['keterangan'],
                'debit' => $income_data['nominal'],
                'kredit' => 0
            ];
            
            $mutation_query = "INSERT INTO t_kas_mutasi 
                             (user_id, tanggal, kode_transaksi, sumber_transaksi_id, tipe_sumber, keterangan, debit, kredit) 
                             VALUES (:user_id, :tanggal, :kode_transaksi, :sumber_transaksi_id, :tipe_sumber, :keterangan, :debit, :kredit)";
            
            $stmt = $this->conn->prepare($mutation_query);
            foreach ($mutation_data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->execute();
            
            $this->conn->commit();
            return $income_id;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    public function updateIncomeWithMutation($id, $income_data) {
        try {
            $this->conn->beginTransaction();
            
            // Get old data for comparison
            $oldData = $this->getById($id);
            if (!$oldData) {
                throw new Exception("Income record not found");
            }
            
            // No_bukti tidak boleh diubah, gunakan yang lama
            $income_data['no_bukti'] = $oldData['no_bukti'];
            
            // Update income record
            $result = $this->update($id, $income_data);
            if (!$result) {
                throw new Exception("Failed to update income record");
            }
            
            // Update cash mutation record
            $mutation_update_query = "UPDATE t_kas_mutasi 
                                    SET tanggal = :tanggal,
                                        keterangan = :keterangan,
                                        debit = :debit
                                    WHERE sumber_transaksi_id = :sumber_transaksi_id 
                                    AND tipe_sumber = 'PENDAPATAN'";
            
            $stmt = $this->conn->prepare($mutation_update_query);
            $stmt->bindValue(':tanggal', $income_data['tanggal'] . ' ' . date('H:i:s'));
            $stmt->bindValue(':keterangan', $income_data['keterangan']);
            $stmt->bindValue(':debit', $income_data['nominal']);
            $stmt->bindValue(':sumber_transaksi_id', $id);
            $stmt->execute();
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    public function deleteIncomeWithMutation($id) {
        try {
            $this->conn->beginTransaction();
            
            // Get income data before deletion
            $incomeData = $this->getById($id);
            if (!$incomeData) {
                throw new Exception("Income record not found");
            }
            
            // Delete cash mutation record first
            $mutation_delete_query = "DELETE FROM t_kas_mutasi 
                                    WHERE sumber_transaksi_id = :sumber_transaksi_id 
                                    AND tipe_sumber = 'PENDAPATAN'";
            
            $stmt = $this->conn->prepare($mutation_delete_query);
            $stmt->bindValue(':sumber_transaksi_id', $id);
            $stmt->execute();
            
            // Delete income record
            $result = $this->delete($id);
            if (!$result) {
                throw new Exception("Failed to delete income record");
            }
            
            // Delete associated file if exists
            if (!empty($incomeData['bukti_foto'])) {
                FileUpload::deleteFile('income', $incomeData['bukti_foto']);
            }
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    public function getIncomesWithCategory() {
        $query = "SELECT i.*, kp.nama_kategori, u.nama_lengkap as created_by
                  FROM " . $this->table_name . " i
                  LEFT JOIN m_kategori_pendapatan kp ON i.kategori_id = kp.id
                  LEFT JOIN m_users u ON i.user_id = u.id
                  ORDER BY i.tanggal DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIncomeDetail($id) {
        $query = "SELECT i.*, kp.nama_kategori, u.nama_lengkap as created_by
                  FROM " . $this->table_name . " i
                  LEFT JOIN m_kategori_pendapatan kp ON i.kategori_id = kp.id
                  LEFT JOIN m_users u ON i.user_id = u.id
                  WHERE i.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getIncomeCategories() {
        $query = "SELECT * FROM m_kategori_pendapatan ORDER BY nama_kategori ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
