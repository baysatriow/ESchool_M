<?php

class Payroll extends BaseModel {
    protected $table_name = "t_penggajian";
    
    public function createPayrollWithDetails($payroll_data, $components) {
        try {
            $this->conn->beginTransaction();
            
            // Create payroll
            $payroll_id = $this->createAndGetId($payroll_data);
            
            // Create payroll details
            foreach ($components as $component) {
                $component['penggajian_id'] = $payroll_id;
                $detail_query = "INSERT INTO t_detail_penggajian 
                               (penggajian_id, komponen_id, nama_komponen_saat_transaksi, nominal) 
                               VALUES (:penggajian_id, :komponen_id, :nama_komponen_saat_transaksi, :nominal)";
                
                $stmt = $this->conn->prepare($detail_query);
                foreach ($component as $key => $value) {
                    $stmt->bindValue(':' . $key, $value);
                }
                $stmt->execute();
            }
            
            // Create cash mutation record
            $mutation_data = [
                'user_id' => $payroll_data['user_id'],
                'tanggal' => $payroll_data['tanggal_pembayaran'] . ' ' . date('H:i:s'),
                'kode_transaksi' => 'GAJI-' . $payroll_id,
                'sumber_transaksi_id' => $payroll_id,
                'tipe_sumber' => 'GAJI',
                'keterangan' => 'Pembayaran gaji periode ' . $payroll_data['periode_gaji'],
                'debit' => 0,
                'kredit' => $payroll_data['gaji_bersih']
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
            return $payroll_id;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    public function getPayrollsWithEmployee() {
        $query = "SELECT p.*, pg.nama_lengkap, pg.nip, j.nama_jabatan, u.nama_lengkap as created_by
                  FROM " . $this->table_name . " p
                  LEFT JOIN m_pegawai pg ON p.pegawai_id = pg.id
                  LEFT JOIN m_jabatan j ON pg.jabatan_id = j.id
                  LEFT JOIN m_users u ON p.user_id = u.id
                  ORDER BY p.periode_gaji DESC, pg.nama_lengkap";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
