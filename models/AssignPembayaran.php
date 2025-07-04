<?php

require_once 'BaseModel.php';

class AssignPembayaran extends BaseModel {
    protected $table_name = "t_assign_pembayaran_siswa";
    
    public function __construct($db) {
        parent::__construct($db);
    }
    
    public function getAssignmentsByPaymentId($data_pembayaran_id) {
        $query = "SELECT aps.*, s.nama_lengkap, s.nis, k.nama_kelas 
                  FROM " . $this->table_name . " aps
                  LEFT JOIN m_siswa s ON aps.siswa_id = s.id
                  LEFT JOIN m_kelas k ON s.kelas_id = k.id
                  WHERE aps.data_pembayaran_id = :data_pembayaran_id
                  ORDER BY k.nama_kelas, s.nama_lengkap";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':data_pembayaran_id', $data_pembayaran_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getStudentsForAssignment($tahun_ajaran_id, $kelas_id = null) {
        $query = "SELECT s.*, k.nama_kelas 
                  FROM m_siswa s
                  LEFT JOIN m_kelas k ON s.kelas_id = k.id
                  WHERE s.status IN ('aktif', 'naik_kelas')";
        
        $params = [];
        
        if ($kelas_id) {
            $query .= " AND s.kelas_id = :kelas_id";
            $params[':kelas_id'] = $kelas_id;
        }
        
        $query .= " ORDER BY k.nama_kelas, s.nama_lengkap";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function assignToStudents($data_pembayaran_id, $student_ids, $jumlah_cicilan = 1) {
        try {
            $this->conn->beginTransaction();
            
            // Get payment data
            $dataPembayaran = new DataPembayaran($this->conn);
            $paymentData = $dataPembayaran->getById($data_pembayaran_id);
            
            if (!$paymentData) {
                throw new Exception("Data pembayaran tidak ditemukan");
            }
            
            // Use nominal from data_pembayaran
            $nominal = $paymentData['nominal'];
            $assignedCount = 0;
            
            foreach ($student_ids as $siswa_id) {
                // Check if already assigned
                $checkQuery = "SELECT id FROM " . $this->table_name . " WHERE data_pembayaran_id = :data_pembayaran_id AND siswa_id = :siswa_id";
                $checkStmt = $this->conn->prepare($checkQuery);
                $checkStmt->execute([':data_pembayaran_id' => $data_pembayaran_id, ':siswa_id' => $siswa_id]);
                
                if (!$checkStmt->fetch()) {
                    $insertQuery = "INSERT INTO " . $this->table_name . " 
                                   (data_pembayaran_id, siswa_id, nominal_yang_harus_dibayar, jumlah_cicilan) 
                                   VALUES (:data_pembayaran_id, :siswa_id, :nominal_yang_harus_dibayar, :jumlah_cicilan)";
                    $insertStmt = $this->conn->prepare($insertQuery);
                    $insertStmt->execute([
                        ':data_pembayaran_id' => $data_pembayaran_id, 
                        ':siswa_id' => $siswa_id, 
                        ':nominal_yang_harus_dibayar' => $nominal,
                        ':jumlah_cicilan' => $jumlah_cicilan
                    ]);
                    
                    $assignId = $this->conn->lastInsertId();
                    
                    // Create installment records if payment can be paid in installments
                    if ($paymentData['dapat_dicicil'] && $jumlah_cicilan > 1) {
                        $this->createInstallmentRecords($assignId, $nominal, $jumlah_cicilan);
                    }
                    
                    $assignedCount++;
                }
            }
            
            $this->conn->commit();
            return ['success' => true, 'assigned_count' => $assignedCount];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error assigning payments: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function createInstallmentRecords($assign_pembayaran_id, $total_nominal, $jumlah_cicilan) {
        $nominal_per_cicilan = ceil($total_nominal / $jumlah_cicilan);
        
        for ($i = 1; $i <= $jumlah_cicilan; $i++) {
            // For the last installment, adjust the amount to match the total exactly
            if ($i == $jumlah_cicilan) {
                $nominal_cicilan = $total_nominal - (($jumlah_cicilan - 1) * $nominal_per_cicilan);
            } else {
                $nominal_cicilan = $nominal_per_cicilan;
            }
            
            $query = "INSERT INTO t_cicilan_pembayaran 
                      (assign_pembayaran_id, cicilan_ke, nominal_cicilan) 
                      VALUES (:assign_pembayaran_id, :cicilan_ke, :nominal_cicilan)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':assign_pembayaran_id' => $assign_pembayaran_id,
                ':cicilan_ke' => $i,
                ':nominal_cicilan' => $nominal_cicilan
            ]);
        }
    }
    
    public function removeAssignment($data_pembayaran_id, $siswa_id) {
        try {
            $this->conn->beginTransaction();
            
            // Check if payment has been made
            $checkQuery = "SELECT nominal_yang_sudah_dibayar FROM " . $this->table_name . " 
                          WHERE data_pembayaran_id = :data_pembayaran_id AND siswa_id = :siswa_id";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute([':data_pembayaran_id' => $data_pembayaran_id, ':siswa_id' => $siswa_id]);
            $assignment = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($assignment && $assignment['nominal_yang_sudah_dibayar'] > 0) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Tidak dapat menghapus assignment yang sudah ada pembayaran'];
            }
            
            // Delete installment records first
            $deleteInstallmentQuery = "DELETE FROM t_cicilan_pembayaran 
                                      WHERE assign_pembayaran_id IN (
                                          SELECT id FROM " . $this->table_name . " 
                                          WHERE data_pembayaran_id = :data_pembayaran_id AND siswa_id = :siswa_id
                                      )";
            $deleteInstallmentStmt = $this->conn->prepare($deleteInstallmentQuery);
            $deleteInstallmentStmt->execute([':data_pembayaran_id' => $data_pembayaran_id, ':siswa_id' => $siswa_id]);
            
            $deleteQuery = "DELETE FROM " . $this->table_name . " WHERE data_pembayaran_id = :data_pembayaran_id AND siswa_id = :siswa_id";
            $deleteStmt = $this->conn->prepare($deleteQuery);
            $result = $deleteStmt->execute([':data_pembayaran_id' => $data_pembayaran_id, ':siswa_id' => $siswa_id]);
            
            if ($result) {
                $this->conn->commit();
                return ['success' => true, 'message' => 'Assignment berhasil dihapus'];
            } else {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Gagal menghapus assignment'];
            }
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error removing assignment: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function getPaymentSummary($data_pembayaran_id) {
        $query = "SELECT 
                    COUNT(*) as total_siswa,
                    SUM(nominal_yang_harus_dibayar) as total_nominal,
                    SUM(nominal_yang_sudah_dibayar) as total_terbayar,
                    SUM(CASE WHEN status_pembayaran = 'sudah_bayar' THEN 1 ELSE 0 END) as siswa_lunas,
                    SUM(CASE WHEN status_pembayaran = 'belum_bayar' THEN 1 ELSE 0 END) as siswa_belum_bayar,
                    SUM(CASE WHEN status_pembayaran = 'sebagian' THEN 1 ELSE 0 END) as siswa_sebagian
                  FROM " . $this->table_name . " 
                  WHERE data_pembayaran_id = :data_pembayaran_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':data_pembayaran_id', $data_pembayaran_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
