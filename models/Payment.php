<?php

class Payment extends BaseModel {
    protected $table_name = "t_pembayaran_siswa";
    
    public function createPaymentWithDetails($payment_data, $details) {
        try {
            $this->conn->beginTransaction();

            // Handle photo upload
            $bukti_foto = $this->uploadPhoto($payment_data['bukti_foto']);
            $payment_data['bukti_foto'] = $bukti_foto;
            
            // Create payment
            $payment_id = $this->createAndGetId($payment_data);
            
            // Create payment details
            foreach ($details as $detail) {
                $detail['pembayaran_id'] = $payment_id;
                $detail_query = "INSERT INTO t_detail_pembayaran_siswa 
                               (pembayaran_id, jenis_pembayaran_id, bulan_pembayaran, tahun_pembayaran, nominal_bayar) 
                               VALUES (:pembayaran_id, :jenis_pembayaran_id, :bulan_pembayaran, :tahun_pembayaran, :nominal_bayar)";
                
                $stmt = $this->conn->prepare($detail_query);
                foreach ($detail as $key => $value) {
                    $stmt->bindValue(':' . $key, $value);
                }
                $stmt->execute();
            }
            
            // Create cash mutation record
            $mutation_data = [
                'user_id' => $payment_data['user_id'],
                'tanggal' => $payment_data['tanggal_bayar'] . ' ' . date('H:i:s'),
                'kode_transaksi' => $payment_data['no_kuitansi'],
                'sumber_transaksi_id' => $payment_id,
                'tipe_sumber' => 'PEMBAYARAN_SISWA',
                'keterangan' => 'Pembayaran siswa - ' . $payment_data['no_kuitansi'],
                'debit' => $payment_data['total_bayar'],
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
            return $payment_id;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function getPaymentDetail($id) {
        $query = "SELECT p.*, s.nama_lengkap as nama_siswa, s.nis, k.nama_kelas, u.nama_lengkap as created_by
                  FROM " . $this->table_name . " p
                  LEFT JOIN m_siswa s ON p.siswa_id = s.id
                  LEFT JOIN m_kelas k ON s.kelas_id = k.id
                  LEFT JOIN m_users u ON p.user_id = u.id
                  WHERE p.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($payment) {
            $detail_query = "SELECT * FROM t_detail_pembayaran_siswa WHERE pembayaran_id = :pembayaran_id";
            $stmt = $this->conn->prepare($detail_query);
            $stmt->bindValue(':pembayaran_id', $id);
            $stmt->execute();
            $payment['details'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $payment;
    }
    
    public function getPaymentsWithDetails() {
        $query = "SELECT p.*, s.nama_lengkap as nama_siswa, s.nis, k.nama_kelas, u.nama_lengkap as created_by
                  FROM " . $this->table_name . " p
                  LEFT JOIN m_siswa s ON p.siswa_id = s.id
                  LEFT JOIN m_kelas k ON s.kelas_id = k.id
                  LEFT JOIN m_users u ON p.user_id = u.id
                  ORDER BY p.tanggal_bayar DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function uploadPhoto($file) {
        $target_dir = "uploads/payment_photos/";
        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($file["tmp_name"]);
        if($check === false) {
            throw new Exception("File is not an image.");
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $target_file = $target_dir . uniqid() . basename($file["name"]);
        }

        // Check file size
        if ($file["size"] > 500000) {
            throw new Exception("Sorry, your file is too large.");
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            throw new Exception("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        }

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $target_file;
        } else {
            throw new Exception("Sorry, there was an error uploading your file.");
        }
    }
}
