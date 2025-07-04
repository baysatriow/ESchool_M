<?php

require_once 'BaseModel.php';

class DataPembayaran extends BaseModel {
    protected $table_name = "m_data_pembayaran";
    
    public function __construct($db) {
        parent::__construct($db);
    }
    
    public function isExists($nama_pembayaran, $excludeId = null) {
        $nama_pembayaran_cleaned = str_replace(' ', '', $nama_pembayaran);

        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE REPLACE(nama_pembayaran, ' ', '') = :nama_pembayaran_cleaned";

        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nama_pembayaran_cleaned', $nama_pembayaran_cleaned);

        if ($excludeId) {
            $stmt->bindValue(':exclude_id', $excludeId);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] > 0;
    }

    public function getAll() {
        $query = "SELECT dp.*, jp.nama_pembayaran as jenis_nama, jp.tipe, jp.kode_pembayaran, ta.tahun_ajaran 
                  FROM " . $this->table_name . " dp
                  LEFT JOIN m_jenis_pembayaran jp ON dp.jenis_pembayaran_id = jp.id
                  LEFT JOIN m_tahun_ajaran ta ON dp.tahun_ajaran_id = ta.id
                  ORDER BY dp.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT dp.*, jp.nama_pembayaran as jenis_nama, jp.tipe, jp.kode_pembayaran, ta.tahun_ajaran 
                  FROM " . $this->table_name . " dp
                  LEFT JOIN m_jenis_pembayaran jp ON dp.jenis_pembayaran_id = jp.id
                  LEFT JOIN m_tahun_ajaran ta ON dp.tahun_ajaran_id = ta.id
                  WHERE dp.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        try {
            $this->conn->beginTransaction();
            
            $query = "INSERT INTO " . $this->table_name . " 
                      (jenis_pembayaran_id, tahun_ajaran_id, nama_pembayaran, nominal, 
                       bulan_pembayaran, tahun_pembayaran, keterangan, batas_waktu, 
                       dapat_dicicil, maksimal_cicilan) 
                      VALUES (:jenis_pembayaran_id, :tahun_ajaran_id, :nama_pembayaran, :nominal, 
                              :bulan_pembayaran, :tahun_pembayaran, :keterangan, :batas_waktu, 
                              :dapat_dicicil, :maksimal_cicilan)";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([
                ':jenis_pembayaran_id' => $data['jenis_pembayaran_id'],
                ':tahun_ajaran_id' => $data['tahun_ajaran_id'],
                ':nama_pembayaran' => $data['nama_pembayaran'],
                ':nominal' => $data['nominal'],
                ':bulan_pembayaran' => $data['bulan_pembayaran'] ?? null,
                ':tahun_pembayaran' => $data['tahun_pembayaran'] ?? null,
                ':keterangan' => $data['keterangan'] ?? null,
                ':batas_waktu' => $data['batas_waktu'] ?? null,
                ':dapat_dicicil' => $data['dapat_dicicil'] ?? 0,
                ':maksimal_cicilan' => $data['maksimal_cicilan'] ?? 1
            ]);
            
            if ($result) {
                $id = $this->conn->lastInsertId();
                $this->conn->commit();
                return $id;
            } else {
                $this->conn->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error creating data pembayaran: " . $e->getMessage());
            return false;
        }
    }
    
    public function update($id, $data) {
        try {
            $this->conn->beginTransaction();
            
            $query = "UPDATE " . $this->table_name . " SET 
                      jenis_pembayaran_id = :jenis_pembayaran_id, 
                      tahun_ajaran_id = :tahun_ajaran_id, 
                      nama_pembayaran = :nama_pembayaran, 
                      nominal = :nominal, 
                      bulan_pembayaran = :bulan_pembayaran, 
                      tahun_pembayaran = :tahun_pembayaran, 
                      keterangan = :keterangan,
                      batas_waktu = :batas_waktu,
                      dapat_dicicil = :dapat_dicicil,
                      maksimal_cicilan = :maksimal_cicilan
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([
                ':id' => $id,
                ':jenis_pembayaran_id' => $data['jenis_pembayaran_id'],
                ':tahun_ajaran_id' => $data['tahun_ajaran_id'],
                ':nama_pembayaran' => $data['nama_pembayaran'],
                ':nominal' => $data['nominal'],
                ':bulan_pembayaran' => $data['bulan_pembayaran'] ?? null,
                ':tahun_pembayaran' => $data['tahun_pembayaran'] ?? null,
                ':keterangan' => $data['keterangan'] ?? null,
                ':batas_waktu' => $data['batas_waktu'] ?? null,
                ':dapat_dicicil' => $data['dapat_dicicil'] ?? 0,
                ':maksimal_cicilan' => $data['maksimal_cicilan'] ?? 1
            ]);
            
            if ($result) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error updating data pembayaran: " . $e->getMessage());
            return false;
        }
    }
    
    public function delete($id) {
        try {
            $this->conn->beginTransaction();
            
            // Check if there are assignments
            $checkQuery = "SELECT COUNT(*) FROM t_assign_pembayaran_siswa WHERE data_pembayaran_id = :id";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindValue(':id', $id);
            $checkStmt->execute();
            $assignmentCount = $checkStmt->fetchColumn();
            
            if ($assignmentCount > 0) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Tidak dapat menghapus data pembayaran yang sudah di-assign ke siswa'];
            }
            
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $result = $stmt->execute();
            
            if ($result) {
                $this->conn->commit();
                return ['success' => true, 'message' => 'Data pembayaran berhasil dihapus'];
            } else {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Gagal menghapus data pembayaran'];
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error deleting data pembayaran: " . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat menghapus data'];
        }
    }
    
    public function generateMonthlyPayments($jenis_pembayaran_id, $tahun_ajaran_id, $nominal, $nama_base, $batas_waktu_tanggal = null, $dapat_dicicil = 0, $maksimal_cicilan = 1) {
    try {
        $this->conn->beginTransaction();
        
        // Get academic year data
        $academicYearQuery = "SELECT * FROM m_tahun_ajaran WHERE id = :id";
        $academicYearStmt = $this->conn->prepare($academicYearQuery);
        $academicYearStmt->bindValue(':id', $tahun_ajaran_id);
        $academicYearStmt->execute();
        $tahunAjaranData = $academicYearStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$tahunAjaranData) {
            throw new Exception("Tahun ajaran tidak ditemukan");
        }
        
        $bulanMulai = $tahunAjaranData['bulan_mulai'] ?? 7;
        $bulanSelesai = $tahunAjaranData['bulan_selesai'] ?? 6;
        
        // Parse tahun ajaran (format: 2024/2025)
        $tahunParts = explode('/', $tahunAjaranData['tahun_ajaran']);
        if (count($tahunParts) != 2) {
            throw new Exception("Format tahun ajaran tidak valid");
        }
        
        $tahunMulai = (int)$tahunParts[0];
        $tahunSelesai = (int)$tahunParts[1];
        
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $generatedIds = [];
        $currentMonth = $bulanMulai;
        $currentYear = $tahunMulai;
        $monthCount = 0;
        $maxMonths = 12;
        
        // Extract day from batas_waktu_tanggal if provided
        $dueDateDay = null;
        if ($batas_waktu_tanggal) {
            $dueDateDay = date('d', strtotime($batas_waktu_tanggal));
        }
        
        while ($monthCount < $maxMonths) {
            $monthName = $months[$currentMonth];
            $namaPembayaran = $nama_base . " " . $monthName . " " . $currentYear;
            $bulanPembayaran = sprintf("%04d-%02d", $currentYear, $currentMonth);
            
            // Calculate due date
            if ($dueDateDay) {
                // Use the selected day for each month
                $lastDayOfMonth = date('t', strtotime($bulanPembayaran . '-01'));
                $actualDay = min($dueDateDay, $lastDayOfMonth); // Handle months with fewer days
                $batasWaktuBulan = sprintf("%04d-%02d-%02d", $currentYear, $currentMonth, $actualDay);
            } else {
                // Default to end of month
                $batasWaktuBulan = date('Y-m-t', strtotime($bulanPembayaran . '-01'));
            }
            
            // Insert directly without calling create() to avoid nested transactions
            $insertQuery = "INSERT INTO " . $this->table_name . " 
                          (jenis_pembayaran_id, tahun_ajaran_id, nama_pembayaran, nominal, 
                           bulan_pembayaran, tahun_pembayaran, keterangan, batas_waktu, 
                           dapat_dicicil, maksimal_cicilan) 
                          VALUES (:jenis_pembayaran_id, :tahun_ajaran_id, :nama_pembayaran, :nominal, 
                                  :bulan_pembayaran, :tahun_pembayaran, :keterangan, :batas_waktu, 
                                  :dapat_dicicil, :maksimal_cicilan)";
            
            $insertStmt = $this->conn->prepare($insertQuery);
            $result = $insertStmt->execute([
                ':jenis_pembayaran_id' => $jenis_pembayaran_id,
                ':tahun_ajaran_id' => $tahun_ajaran_id,
                ':nama_pembayaran' => $namaPembayaran,
                ':nominal' => $nominal,
                ':bulan_pembayaran' => $bulanPembayaran,
                ':tahun_pembayaran' => $currentYear,
                ':keterangan' => "Pembayaran bulanan untuk " . $monthName . " " . $currentYear,
                ':batas_waktu' => $batasWaktuBulan,
                ':dapat_dicicil' => $dapat_dicicil,
                ':maksimal_cicilan' => $maksimal_cicilan
            ]);
            
            if ($result) {
                $generatedIds[] = $this->conn->lastInsertId();
            }
            
            // Check if we've reached the end month and year
            if ($currentMonth == $bulanSelesai && $currentYear == $tahunSelesai) {
                break;
            }
            
            // Move to next month
            $currentMonth++;
            if ($currentMonth > 12) {
                $currentMonth = 1;
                $currentYear++;
            }
            
            $monthCount++;
        }
        
        $this->conn->commit();
        return ['success' => true, 'generated_ids' => $generatedIds, 'count' => count($generatedIds)];
        
    } catch (Exception $e) {
        $this->conn->rollBack();
        error_log("Error generating monthly payments: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
}
