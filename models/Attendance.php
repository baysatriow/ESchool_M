<?php

class Attendance extends BaseModel {
    protected $table_name = "t_presensi_pegawai";
    
    public function getAttendanceWithEmployee($date = null) {
        $whereClause = $date ? "WHERE DATE(pp.tanggal) = :date" : "WHERE DATE(pp.tanggal) = CURDATE()";
        
        $query = "SELECT pp.*, p.nama_lengkap, p.nip, j.nama_jabatan
                  FROM " . $this->table_name . " pp
                  LEFT JOIN m_pegawai p ON pp.pegawai_id = p.id
                  LEFT JOIN m_jabatan j ON p.jabatan_id = j.id
                  {$whereClause}
                  ORDER BY pp.jam_masuk";
        
        $stmt = $this->conn->prepare($query);
        if ($date) {
            $stmt->bindValue(':date', $date);
        }
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function checkTodayAttendance($pegawai_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE pegawai_id = :pegawai_id AND DATE(tanggal) = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pegawai_id', $pegawai_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function clockIn($pegawai_id) {
        $existing = $this->checkTodayAttendance($pegawai_id);
        if ($existing) {
            return false;
        }
        
        $data = [
            'pegawai_id' => $pegawai_id,
            'tanggal' => date('Y-m-d H:i:s'),
            'jam_masuk' => date('H:i:s'),
            'status_kehadiran' => 'hadir'
        ];
        
        return $this->create($data);
    }
    
    public function clockOut($pegawai_id) {
        $existing = $this->checkTodayAttendance($pegawai_id);
        if (!$existing || $existing['jam_pulang']) {
            return false;
        }
        
        return $this->update($existing['id'], ['jam_pulang' => date('H:i:s')]);
    }
}
