<?php
class AcademicYear extends BaseModel {
    protected $table_name = "m_tahun_ajaran";
    
    public function __construct($db) {
        parent::__construct($db);
    }
    
    public function isExists($tahunAjaran, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE tahun_ajaran = :tahun_ajaran";
        
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':tahun_ajaran', $tahunAjaran);
        
        if ($excludeId) {
            $stmt->bindValue(':exclude_id', $excludeId);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }
    
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
    
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY tahun_ajaran DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>