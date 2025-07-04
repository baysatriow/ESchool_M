<?php

require_once 'BaseModel.php';

class Payment extends BaseModel {
    protected $table_name = "t_pembayaran_siswa";
    
    public function __construct($db) {
        parent::__construct($db);
    }
    
    public function getStudentsWithPaymentStatus($filters = []) {
        $query = "SELECT s.*, k.nama_kelas,
                         COUNT(DISTINCT aps.id) as total_tagihan,
                         COUNT(DISTINCT CASE WHEN aps.status_pembayaran = 'sudah_bayar' THEN aps.id END) as tagihan_lunas,
                         COUNT(DISTINCT CASE WHEN aps.status_pembayaran = 'belum_bayar' THEN aps.id END) as tagihan_belum_bayar,
                         COUNT(DISTINCT CASE WHEN aps.status_pembayaran = 'sebagian' THEN aps.id END) as tagihan_sebagian,
                         COALESCE(SUM(aps.nominal_yang_harus_dibayar), 0) as total_harus_bayar,
                         COALESCE(SUM(aps.nominal_yang_sudah_dibayar), 0) as total_sudah_bayar,
                         COALESCE(SUM(aps.nominal_yang_harus_dibayar - aps.nominal_yang_sudah_dibayar), 0) as total_sisa_bayar
                  FROM m_siswa s
                  LEFT JOIN m_kelas k ON s.kelas_id = k.id
                  LEFT JOIN t_assign_pembayaran_siswa aps ON s.id = aps.siswa_id
                  LEFT JOIN m_data_pembayaran dp ON aps.data_pembayaran_id = dp.id";
        
        $params = [];
        $conditions = ["s.status IN ('aktif','lulus','pindah','dikeluarkan','ALUMNI','naik_kelas')"];
        
        if (!empty($filters['tahun_ajaran_id'])) {
            $conditions[] = "(aps.data_pembayaran_id IS NULL OR dp.tahun_ajaran_id = :tahun_ajaran_id)";
            $params[':tahun_ajaran_id'] = $filters['tahun_ajaran_id'];
        }
        
        if (!empty($filters['kelas_id'])) {
            $conditions[] = "s.kelas_id = :kelas_id";
            $params[':kelas_id'] = $filters['kelas_id'];
        }
        
        if (!empty($filters['status_pembayaran'])) {
            if ($filters['status_pembayaran'] == 'ada_tagihan') {
                $conditions[] = "aps.id IS NOT NULL";
            } elseif ($filters['status_pembayaran'] == 'tidak_ada_tagihan') {
                $conditions[] = "aps.id IS NULL";
            }
        }
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $query .= " GROUP BY s.id ORDER BY k.nama_kelas, s.nama_lengkap";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPaymentDetail($siswa_id, $tahun_ajaran_id = null) {
        // Get student info
        $studentQuery = "SELECT s.*, k.nama_kelas 
                        FROM m_siswa s 
                        LEFT JOIN m_kelas k ON s.kelas_id = k.id 
                        WHERE s.id = :siswa_id";
        $studentStmt = $this->conn->prepare($studentQuery);
        $studentStmt->execute([':siswa_id' => $siswa_id]);
        $student = $studentStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$student) return null;
        
        // Get payment assignments
        $assignQuery = "SELECT aps.*, dp.jenis_pembayaran_id, dp.nama_pembayaran, dp.nominal, dp.batas_waktu, 
                               dp.dapat_dicicil, dp.maksimal_cicilan, dp.bulan_pembayaran,
                               jp.nama_pembayaran as jenis_nama, jp.tipe, jp.kode_pembayaran,
                               ta.tahun_ajaran
                        FROM t_assign_pembayaran_siswa aps
                        JOIN m_data_pembayaran dp ON aps.data_pembayaran_id = dp.id
                        JOIN m_jenis_pembayaran jp ON dp.jenis_pembayaran_id = jp.id
                        JOIN m_tahun_ajaran ta ON dp.tahun_ajaran_id = ta.id
                        WHERE aps.siswa_id = :siswa_id";
        
        $params = [':siswa_id' => $siswa_id];
        
        if ($tahun_ajaran_id) {
            $assignQuery .= " AND dp.tahun_ajaran_id = :tahun_ajaran_id";
            $params[':tahun_ajaran_id'] = $tahun_ajaran_id;
        }
        
        $assignQuery .= " ORDER BY dp.batas_waktu ASC, dp.created_at DESC";
        
        $assignStmt = $this->conn->prepare($assignQuery);
        $assignStmt->execute($params);
        $assignments = $assignStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get payment history
        $historyQuery = "SELECT ps.*, dps.jenis_pembayaran_id, dps.bulan_pembayaran, 
                                dps.tahun_pembayaran, dps.nominal_bayar,
                                jp.nama_pembayaran as jenis_nama, jp.kode_pembayaran,
                                u.nama_lengkap as user_name
                         FROM t_pembayaran_siswa ps
                         LEFT JOIN t_detail_pembayaran_siswa dps ON ps.id = dps.pembayaran_id
                         LEFT JOIN m_jenis_pembayaran jp ON dps.jenis_pembayaran_id = jp.id
                         LEFT JOIN m_users u ON ps.user_id = u.id
                         WHERE ps.siswa_id = :siswa_id
                         ORDER BY ps.tanggal_bayar DESC, ps.created_at DESC";
        
        $historyStmt = $this->conn->prepare($historyQuery);
        $historyStmt->execute([':siswa_id' => $siswa_id]);
        $history = $historyStmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'student' => $student,
            'assignments' => $assignments,
            'history' => $history
        ];
    }
    
    public function getInstallments($assign_pembayaran_id) {
        $query = "SELECT cp.*, aps.nominal_yang_harus_dibayar, aps.jumlah_cicilan
                  FROM t_cicilan_pembayaran cp
                  JOIN t_assign_pembayaran_siswa aps ON cp.assign_pembayaran_id = aps.id
                  WHERE cp.assign_pembayaran_id = :assign_id
                  ORDER BY cp.cicilan_ke";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':assign_id' => $assign_pembayaran_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function createPayment($data) {
        try {
            $this->conn->beginTransaction();
            
            // Generate receipt number
            $no_kuitansi = $this->generateReceiptNumber();
            
            // Insert main payment record
            $paymentQuery = "INSERT INTO t_pembayaran_siswa 
                            (siswa_id, user_id, no_kuitansi, tanggal_bayar, total_bayar, metode_bayar, bukti_foto) 
                            VALUES (:siswa_id, :user_id, :no_kuitansi, :tanggal_bayar, :total_bayar, :metode_bayar, :bukti_foto)";
            
            $paymentStmt = $this->conn->prepare($paymentQuery);
            $paymentResult = $paymentStmt->execute([
                ':siswa_id' => $data['siswa_id'],
                ':user_id' => $data['user_id'],
                ':no_kuitansi' => $no_kuitansi,
                ':tanggal_bayar' => $data['tanggal_bayar'],
                ':total_bayar' => $data['total_bayar'],
                ':metode_bayar' => $data['metode_bayar'],
                ':bukti_foto' => $data['bukti_foto'] ?? null
            ]);
            
            if (!$paymentResult) {
                throw new Exception("Gagal menyimpan data pembayaran");
            }
            
            $payment_id = $this->conn->lastInsertId();
            
            // Insert payment details and update assignments
            foreach ($data['items'] as $item) {
                // Insert detail
                $detailQuery = "INSERT INTO t_detail_pembayaran_siswa 
                               (pembayaran_id, jenis_pembayaran_id, bulan_pembayaran, tahun_pembayaran, nominal_bayar) 
                               VALUES (:pembayaran_id, :jenis_pembayaran_id, :bulan_pembayaran, :tahun_pembayaran, :nominal_bayar)";
                
                $detailStmt = $this->conn->prepare($detailQuery);
                $detailStmt->execute([
                    ':pembayaran_id' => $payment_id,
                    ':jenis_pembayaran_id' => $item['jenis_pembayaran_id'],
                    ':bulan_pembayaran' => $item['bulan_pembayaran'] ?? null,
                    ':tahun_pembayaran' => $item['tahun_pembayaran'] ?? null,
                    ':nominal_bayar' => $item['nominal_bayar']
                ]);
                
                // Update assignment
                $this->updateAssignmentPayment($item['assign_pembayaran_id'], $item['nominal_bayar']);
                
                // Update installment if applicable
                if (isset($item['cicilan_ke']) && $item['cicilan_ke'] > 0) {
                    $this->updateInstallmentPayment($item['assign_pembayaran_id'], $item['cicilan_ke'], $payment_id);
                }
            }
            
            // Insert to cash flow
            $this->insertCashFlow($payment_id, $data['total_bayar'], $data['user_id'], $no_kuitansi);
            
            $this->conn->commit();
            return ['success' => true, 'payment_id' => $payment_id, 'no_kuitansi' => $no_kuitansi];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error creating payment: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function generateReceiptNumber() {
        $prefix = 'INV/';
        $date = date('Ymd');
        $endifx = '/SW';
        // Get last number for today
        $query = "SELECT no_kuitansi FROM t_pembayaran_siswa 
                  WHERE no_kuitansi LIKE :pattern 
                  ORDER BY no_kuitansi DESC LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':pattern' => $prefix . $date . '%']);
        $lastReceipt = $stmt->fetchColumn();
        
        if ($lastReceipt) {
            $lastNumber = intval(substr($lastReceipt, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT) . $endifx;
    }
    
    private function updateAssignmentPayment($assign_pembayaran_id, $nominal_bayar) {
        // Get current assignment data
        $query = "SELECT * FROM t_assign_pembayaran_siswa WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $assign_pembayaran_id]);
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$assignment) return;
        
        $new_paid = $assignment['nominal_yang_sudah_dibayar'] + $nominal_bayar;
        $status = 'belum_bayar';
        
        if ($new_paid >= $assignment['nominal_yang_harus_dibayar']) {
            $status = 'sudah_bayar';
            $tanggal_lunas = date('Y-m-d H:i:s');
        } elseif ($new_paid > 0) {
            $status = 'sebagian';
            $tanggal_lunas = null;
        }
        
        $updateQuery = "UPDATE t_assign_pembayaran_siswa 
                       SET nominal_yang_sudah_dibayar = :nominal_sudah_dibayar, 
                           status_pembayaran = :status_pembayaran,
                           tanggal_lunas = :tanggal_lunas
                       WHERE id = :id";
        
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->execute([
            ':nominal_sudah_dibayar' => $new_paid,
            ':status_pembayaran' => $status,
            ':tanggal_lunas' => $tanggal_lunas ?? null,
            ':id' => $assign_pembayaran_id
        ]);
    }
    
    private function updateInstallmentPayment($assign_pembayaran_id, $cicilan_ke, $payment_id) {
        $query = "UPDATE t_cicilan_pembayaran 
                  SET status = 'sudah_bayar', 
                      tanggal_bayar = CURRENT_DATE, 
                      pembayaran_id = :payment_id 
                  WHERE assign_pembayaran_id = :assign_id AND cicilan_ke = :cicilan_ke";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':payment_id' => $payment_id,
            ':assign_id' => $assign_pembayaran_id,
            ':cicilan_ke' => $cicilan_ke
        ]);
        
        // Update cicilan_terbayar count
        $countQuery = "SELECT COUNT(*) FROM t_cicilan_pembayaran 
                      WHERE assign_pembayaran_id = :assign_id AND status = 'sudah_bayar'";
        $countStmt = $this->conn->prepare($countQuery);
        $countStmt->execute([':assign_id' => $assign_pembayaran_id]);
        $paidCount = $countStmt->fetchColumn();
        
        $updateAssignQuery = "UPDATE t_assign_pembayaran_siswa 
                             SET cicilan_terbayar = :paid_count 
                             WHERE id = :id";
        $updateAssignStmt = $this->conn->prepare($updateAssignQuery);
        $updateAssignStmt->execute([
            ':paid_count' => $paidCount,
            ':id' => $assign_pembayaran_id
        ]);
    }
    
    private function insertCashFlow($payment_id, $total_bayar, $user_id, $no_kuitansi) {
        $query = "INSERT INTO t_kas_mutasi 
                  (user_id, tanggal, kode_transaksi, sumber_transaksi_id, tipe_sumber, keterangan, debit, kredit) 
                  VALUES (:user_id, NOW(), :kode_transaksi, :sumber_transaksi_id, 'PEMBAYARAN_SISWA', :keterangan, :debit, 0)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':user_id' => $user_id,
            ':kode_transaksi' => $no_kuitansi,
            ':sumber_transaksi_id' => $payment_id,
            ':keterangan' => 'Pembayaran siswa dengan kuitansi ' . $no_kuitansi,
            ':debit' => $total_bayar
        ]);
    }
    
    public function getPaymentSummary($filters = []) {
        $query = "SELECT 
                    COUNT(DISTINCT ps.id) as total_pembayaran,
                    SUM(ps.total_bayar) as total_nominal,
                    COUNT(DISTINCT ps.siswa_id) as total_siswa_bayar,
                    AVG(ps.total_bayar) as rata_rata_pembayaran
                  FROM t_pembayaran_siswa ps
                  LEFT JOIN t_detail_pembayaran_siswa dps ON ps.id = dps.pembayaran_id
                  LEFT JOIN m_jenis_pembayaran jp ON dps.jenis_pembayaran_id = jp.id
                  WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['tanggal_dari'])) {
            $query .= " AND ps.tanggal_bayar >= :tanggal_dari";
            $params[':tanggal_dari'] = $filters['tanggal_dari'];
        }
        
        if (!empty($filters['tanggal_sampai'])) {
            $query .= " AND ps.tanggal_bayar <= :tanggal_sampai";
            $params[':tanggal_sampai'] = $filters['tanggal_sampai'];
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
