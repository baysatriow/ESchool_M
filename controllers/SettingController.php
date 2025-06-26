<?php

require_once 'models/AppSetting.php';

class SettingController extends BaseController {
    
    public function schoolIdentity() {
        $this->appSettings();
    }
    
    public function appSettings() {
        $appSetting = new AppSetting($this->db);
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nama_sekolah' => $_POST['nama_sekolah'],
                'npsn' => $_POST['npsn'],
                'alamat' => $_POST['alamat'],
                'no_telepon' => $_POST['no_telepon'],
                'email' => $_POST['email'],
                'kepala_sekolah' => $_POST['kepala_sekolah'],
                'bendahara' => $_POST['bendahara']
            ];
            
            try {
                $result = $appSetting->updateSettings($data);
                if ($result) {
                    $this->redirect('app-settings', 'Pengaturan berhasil disimpan!', 'success');
                } else {
                    $this->redirect('app-settings', 'Gagal menyimpan pengaturan!', 'error');
                }
            } catch (Exception $e) {
                $this->redirect('app-settings', 'Error: ' . $e->getMessage(), 'error');
            }
        }
        
        $settings = $appSetting->getSettings();
        
        $data = [
            'page_title' => 'Pengaturan Aplikasi',
            'settings' => $settings
        ];
        
        $this->view('settings/app-settings', $data);
    }
}
