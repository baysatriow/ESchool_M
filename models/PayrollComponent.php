<?php

class PayrollComponent extends BaseModel {
    protected $table_name = "m_komponen_gaji";
    
    public function getComponentsByType($type = null) {
        $query = "SELECT * FROM " . $this->table_name;
        if ($type) {
            $query .= " WHERE tipe = :type";
        }
        $query .= " ORDER BY nama_komponen";
        
        $stmt = $this->conn->prepare($query);
        if ($type) {
            $stmt->bindValue(':type', $type);
        }
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
