<?php

class AcademicYear extends BaseModel {
    protected $table_name = "m_tahun_ajaran";
    
    public function getActiveYear() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'aktif' LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function setActiveYear($id) {
        // Deactivate all years
        $this->conn->prepare("UPDATE " . $this->table_name . " SET status = 'tidak_aktif'")->execute();
        
        // Activate selected year
        return $this->update($id, ['status' => 'aktif']);
    }
    
    public function getMonthName($monthNumber) {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return isset($months[$monthNumber]) ? $months[$monthNumber] : '';
    }
    
    public function getAcademicYearPeriod($tahunAjaran, $bulanMulai, $bulanSelesai) {
        $years = explode('/', $tahunAjaran);
        $startYear = $years[0];
        $endYear = isset($years[1]) ? $years[1] : $startYear + 1;
        
        $startMonth = $this->getMonthName($bulanMulai);
        $endMonth = $this->getMonthName($bulanSelesai);
        
        return $startMonth . ' ' . $startYear . ' - ' . $endMonth . ' ' . $endYear;
    }
}
