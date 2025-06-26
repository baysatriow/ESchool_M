<?php

class AppSetting extends BaseModel {
    protected $table_name = "app_pengaturan";
    
    public function getSettings() {
        $query = "SELECT * FROM " . $this->table_name . " LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateSettings($data) {
        $settings = $this->getSettings();
        
        if ($settings) {
            return $this->update($settings['id'], $data);
        } else {
            return $this->create($data);
        }
    }
}
