<?php

require_once 'models/BaseModel.php';

class Expense extends BaseModel {
    protected $table_name = "t_pengeluaran";
    
    public function __construct($db) {
        parent::__construct($db);
    }
    
    public function generateReceiptNumber() {
        // Format: PGLR/YYYYMMDD/XXXX where XXXX is sequential number
        $prefix = 'PGLR/';
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
    
    public function createExpenseWithMutation($expense_data) {
        try {
            $this->conn->beginTransaction();
            
            // Always auto-generate receipt number
            $expense_data['no_bukti'] = $this->generateReceiptNumber();
            
            // Create expense record
            $result = $this->create($expense_data);
            if (!$result) {
                throw new Exception("Failed to create expense record");
            }
            
            $expense_id = $this->conn->lastInsertId();
            
            // Create cash mutation record
            $mutation_data = [
                'user_id' => $expense_data['user_id'],
                'tanggal' => $expense_data['tanggal'] . ' ' . date('H:i:s'),
                'kode_transaksi' => $expense_data['no_bukti'],
                'sumber_transaksi_id' => $expense_id,
                'tipe_sumber' => 'PENGELUARAN',
                'keterangan' => $expense_data['keterangan'],
                'debit' => 0,
                'kredit' => $expense_data['nominal']
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
            return $expense_id;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    public function updateExpenseWithMutation($id, $expense_data) {
        try {
            $this->conn->beginTransaction();
            
            // Get old data for comparison
            $oldData = $this->getById($id);
            if (!$oldData) {
                throw new Exception("Expense record not found");
            }
            
            // No_bukti tidak boleh diubah, gunakan yang lama
            $expense_data['no_bukti'] = $oldData['no_bukti'];
            
            // Update expense record
            $result = $this->update($id, $expense_data);
            if (!$result) {
                throw new Exception("Failed to update expense record");
            }
            
            // Update cash mutation record
            $mutation_update_query = "UPDATE t_kas_mutasi 
                                    SET tanggal = :tanggal,
                                        keterangan = :keterangan,
                                        kredit = :kredit
                                    WHERE sumber_transaksi_id = :sumber_transaksi_id 
                                    AND tipe_sumber = 'PENGELUARAN'";
            
            $stmt = $this->conn->prepare($mutation_update_query);
            $stmt->bindValue(':tanggal', $expense_data['tanggal'] . ' ' . date('H:i:s'));
            $stmt->bindValue(':keterangan', $expense_data['keterangan']);
            $stmt->bindValue(':kredit', $expense_data['nominal']);
            $stmt->bindValue(':sumber_transaksi_id', $id);
            $stmt->execute();
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    public function deleteExpenseWithMutation($id) {
        try {
            $this->conn->beginTransaction();
            
            // Get expense data before deletion
            $expenseData = $this->getById($id);
            if (!$expenseData) {
                throw new Exception("Expense record not found");
            }
            
            // Delete cash mutation record first
            $mutation_delete_query = "DELETE FROM t_kas_mutasi 
                                    WHERE sumber_transaksi_id = :sumber_transaksi_id 
                                    AND tipe_sumber = 'PENGELUARAN'";
            
            $stmt = $this->conn->prepare($mutation_delete_query);
            $stmt->bindValue(':sumber_transaksi_id', $id);
            $stmt->execute();
            
            // Delete expense record
            $result = $this->delete($id);
            if (!$result) {
                throw new Exception("Failed to delete expense record");
            }
            
            // Delete associated file if exists
            if (!empty($expenseData['bukti_foto'])) {
                FileUpload::deleteFile('expense', $expenseData['bukti_foto']);
            }
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    public function getExpensesWithCategory() {
        $query = "SELECT e.*, kp.nama_kategori, u.nama_lengkap as created_by
                  FROM " . $this->table_name . " e
                  LEFT JOIN m_kategori_pengeluaran kp ON e.kategori_id = kp.id
                  LEFT JOIN m_users u ON e.user_id = u.id
                  ORDER BY e.tanggal DESC, e.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getExpensesWithCategoryFiltered($filters = []) {
        $query = "SELECT e.*, kp.nama_kategori, u.nama_lengkap as created_by
                  FROM " . $this->table_name . " e
                  LEFT JOIN m_kategori_pengeluaran kp ON e.kategori_id = kp.id
                  LEFT JOIN m_users u ON e.user_id = u.id
                  WHERE 1=1";
        
        $params = [];
        
        // Apply filters
        if (!empty($filters['tanggal_dari'])) {
            $query .= " AND e.tanggal >= :tanggal_dari";
            $params[':tanggal_dari'] = $filters['tanggal_dari'];
        }
        
        if (!empty($filters['tanggal_sampai'])) {
            $query .= " AND e.tanggal <= :tanggal_sampai";
            $params[':tanggal_sampai'] = $filters['tanggal_sampai'];
        }
        
        if (!empty($filters['kategori_id'])) {
            $query .= " AND e.kategori_id = :kategori_id";
            $params[':kategori_id'] = $filters['kategori_id'];
        }
        
        $query .= " ORDER BY e.tanggal DESC, e.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getExpenseDetail($id) {
        $query = "SELECT e.*, kp.nama_kategori, u.nama_lengkap as created_by,
                         DATE_FORMAT(e.tanggal, '%d %M %Y') as tanggal_formatted,
                         DATE_FORMAT(e.created_at, '%d %M %Y %H:%i:%s') as waktu_transaksi
                  FROM " . $this->table_name . " e
                  LEFT JOIN m_kategori_pengeluaran kp ON e.kategori_id = kp.id
                  LEFT JOIN m_users u ON e.user_id = u.id
                  WHERE e.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getExpenseCategories() {
        $query = "SELECT * FROM m_kategori_pengeluaran ORDER BY nama_kategori ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getExpenseSummaryCards($filters = []) {
        // Get total expenses and transactions
        $totalQuery = "SELECT 
                        COUNT(*) as total_transaksi,
                        COALESCE(SUM(nominal), 0) as total_pengeluaran,
                        COALESCE(AVG(nominal), 0) as rata_rata_pengeluaran
                       FROM " . $this->table_name . " 
                       WHERE 1=1";
        
        // Get this month's expenses
        $monthQuery = "SELECT 
                        COUNT(*) as transaksi_bulan_ini,
                        COALESCE(SUM(nominal), 0) as pengeluaran_bulan_ini
                       FROM " . $this->table_name . " 
                       WHERE MONTH(tanggal) = MONTH(CURRENT_DATE()) 
                       AND YEAR(tanggal) = YEAR(CURRENT_DATE())";
        
        // Get total income from income table
        $incomeQuery = "SELECT COALESCE(SUM(nominal), 0) as total_pendapatan 
                       FROM t_pendapatan";
        
        // Get total payments from student payments
        $paymentQuery = "SELECT COALESCE(SUM(total_bayar), 0) as total_pembayaran_siswa 
                        FROM t_pembayaran_siswa";
        
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
        
        // Execute queries
        $totalStmt = $this->conn->prepare($totalQuery);
        $totalStmt->execute($params);
        $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
        
        $monthStmt = $this->conn->prepare($monthQuery);
        $monthStmt->execute();
        $monthResult = $monthStmt->fetch(PDO::FETCH_ASSOC);
        
        $incomeStmt = $this->conn->prepare($incomeQuery);
        $incomeStmt->execute();
        $incomeResult = $incomeStmt->fetch(PDO::FETCH_ASSOC);
        
        $paymentStmt = $this->conn->prepare($paymentQuery);
        $paymentStmt->execute();
        $paymentResult = $paymentStmt->fetch(PDO::FETCH_ASSOC);
        
        // Calculate total balance
        $totalIncome = ($incomeResult['total_pendapatan'] ?? 0) + ($paymentResult['total_pembayaran_siswa'] ?? 0);
        $totalExpense = $totalResult['total_pengeluaran'] ?? 0;
        $saldoTersisa = $totalIncome - $totalExpense;
        
        return [
            'total_transaksi' => $totalResult['total_transaksi'] ?? 0,
            'total_pengeluaran' => $totalResult['total_pengeluaran'] ?? 0,
            'rata_rata_pengeluaran' => $totalResult['rata_rata_pengeluaran'] ?? 0,
            'transaksi_bulan_ini' => $monthResult['transaksi_bulan_ini'] ?? 0,
            'pengeluaran_bulan_ini' => $monthResult['pengeluaran_bulan_ini'] ?? 0,
            'total_pendapatan' => $incomeResult['total_pendapatan'] ?? 0,
            'total_pembayaran_siswa' => $paymentResult['total_pembayaran_siswa'] ?? 0,
            'total_pemasukan' => $totalIncome,
            'saldo_tersisa' => $saldoTersisa
        ];
    }
}
