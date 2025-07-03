<?php

require_once 'models/BaseModel.php';
require_once 'helpers/FileUpload.php';

class Income extends BaseModel {
    protected $table_name = "t_pendapatan";
    
    public function __construct($db) {
        parent::__construct($db);
    }
    
    public function generateReceiptNumber() {
        // Format: PDPT/YYYYMMDD/XXXX where XXXX is sequential number
        $prefix = 'PDPT/';
        $date = date('Ymd');
        $suffix = '/';
        
        // Get last number for today
        $query = "SELECT no_bukti FROM " . $this->table_name . " 
                  WHERE no_bukti LIKE :pattern 
                  ORDER BY no_bukti DESC LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':pattern' => $prefix . $date . '%']);
        $lastReceipt = $stmt->fetchColumn();
        
        if ($lastReceipt) {
            // Extract the last 4 digits
            $lastNumber = intval(substr($lastReceipt, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $date . $suffix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
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
            
            // Always auto-generate receipt number
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
                  ORDER BY i.tanggal DESC, i.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getIncomesWithCategoryFiltered($filters = []) {
        $query = "SELECT i.*, kp.nama_kategori, u.nama_lengkap as created_by
                  FROM " . $this->table_name . " i
                  LEFT JOIN m_kategori_pendapatan kp ON i.kategori_id = kp.id
                  LEFT JOIN m_users u ON i.user_id = u.id
                  WHERE 1=1";
        
        $params = [];
        
        // Apply filters
        if (!empty($filters['tanggal_dari'])) {
            $query .= " AND i.tanggal >= :tanggal_dari";
            $params[':tanggal_dari'] = $filters['tanggal_dari'];
        }
        
        if (!empty($filters['tanggal_sampai'])) {
            $query .= " AND i.tanggal <= :tanggal_sampai";
            $params[':tanggal_sampai'] = $filters['tanggal_sampai'];
        }
        
        if (!empty($filters['kategori_id'])) {
            $query .= " AND i.kategori_id = :kategori_id";
            $params[':kategori_id'] = $filters['kategori_id'];
        }
        
        $query .= " ORDER BY i.tanggal DESC, i.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getIncomeDetail($id) {
        $query = "SELECT i.*, kp.nama_kategori, u.nama_lengkap as created_by,
                         DATE_FORMAT(i.tanggal, '%d %M %Y') as tanggal_formatted,
                         DATE_FORMAT(i.created_at, '%d %M %Y %H:%i:%s') as waktu_transaksi
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
    
    public function getIncomeSummary($filters = []) {
        $query = "SELECT 
                    COUNT(*) as total_transaksi,
                    SUM(nominal) as total_nominal,
                    AVG(nominal) as rata_rata_nominal
                  FROM " . $this->table_name . " i
                  WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['tanggal_dari'])) {
            $query .= " AND i.tanggal >= :tanggal_dari";
            $params[':tanggal_dari'] = $filters['tanggal_dari'];
        }
        
        if (!empty($filters['tanggal_sampai'])) {
            $query .= " AND i.tanggal <= :tanggal_sampai";
            $params[':tanggal_sampai'] = $filters['tanggal_sampai'];
        }
        
        if (!empty($filters['kategori_id'])) {
            $query .= " AND i.kategori_id = :kategori_id";
            $params[':kategori_id'] = $filters['kategori_id'];
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getIncomeSummaryCards($filters = []) {
        // Get total income and transactions
        $totalQuery = "SELECT 
                        COUNT(*) as total_transaksi,
                        COALESCE(SUM(nominal), 0) as total_pendapatan,
                        COALESCE(AVG(nominal), 0) as rata_rata_pendapatan
                       FROM " . $this->table_name . " 
                       WHERE 1=1";
        
        // Get this month's income
        $monthQuery = "SELECT 
                        COUNT(*) as transaksi_bulan_ini,
                        COALESCE(SUM(nominal), 0) as pendapatan_bulan_ini
                       FROM " . $this->table_name . " 
                       WHERE MONTH(tanggal) = MONTH(CURRENT_DATE()) 
                       AND YEAR(tanggal) = YEAR(CURRENT_DATE())";
        
        $params = [];
        
        // Apply filters if provided
        if (!empty($filters['tanggal_dari'])) {
            $totalQuery .= " AND tanggal >= :tanggal_dari";
            $params[':tanggal_dari'] = $filters['tanggal_dari'];
        }
        
        if (!empty($filters['tanggal_sampai'])) {
            $totalQuery .= " AND tanggal <= :tanggal_sampai";
            $params[':tanggal_sampai'] = $filters['tanggal_sampai'];
        }
        
        if (!empty($filters['kategori_id'])) {
            $totalQuery .= " AND kategori_id = :kategori_id";
            $params[':kategori_id'] = $filters['kategori_id'];
        }
        
        // Execute total query
        $totalStmt = $this->conn->prepare($totalQuery);
        $totalStmt->execute($params);
        $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
        
        // Execute month query
        $monthStmt = $this->conn->prepare($monthQuery);
        $monthStmt->execute();
        $monthResult = $monthStmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'total_transaksi' => $totalResult['total_transaksi'] ?? 0,
            'total_pendapatan' => $totalResult['total_pendapatan'] ?? 0,
            'rata_rata_pendapatan' => $totalResult['rata_rata_pendapatan'] ?? 0,
            'transaksi_bulan_ini' => $monthResult['transaksi_bulan_ini'] ?? 0,
            'pendapatan_bulan_ini' => $monthResult['pendapatan_bulan_ini'] ?? 0
        ];
    }
}
