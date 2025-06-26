<?php

class Expense extends BaseModel {
    protected $table_name = "t_pengeluaran";
    
    public function createExpenseWithMutation($expense_data) {
        try {
            $this->conn->beginTransaction();
            
            // Create expense
            $expense_id = $this->createAndGetId($expense_data);
            
            // Create cash mutation record
            $mutation_data = [
                'user_id' => $expense_data['user_id'],
                'tanggal' => $expense_data['tanggal'] . ' ' . date('H:i:s'),
                'kode_transaksi' => $expense_data['no_bukti'] ?? 'EXP-' . $expense_id,
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
    
    public function getExpensesWithCategory() {
        $query = "SELECT e.*, k.nama_kategori, u.nama_lengkap as created_by
                  FROM " . $this->table_name . " e
                  LEFT JOIN m_kategori_pengeluaran k ON e.kategori_id = k.id
                  LEFT JOIN m_users u ON e.user_id = u.id
                  ORDER BY e.tanggal DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
