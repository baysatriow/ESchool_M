<?php
require_once '../config/Database.php';
require_once '../models/Session.php';

Session::requireLogin();

header('Content-Type: application/json');

$type = $_GET['type'] ?? '';

$database = new Database();
$db = $database->getConnection();

$response = [];

try {
    switch ($type) {
        case 'classes':
            $stmt = $db->prepare("SELECT id, nama_kelas, tingkat FROM m_kelas ORDER BY tingkat, nama_kelas");
            $stmt->execute();
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'positions':
            $stmt = $db->prepare("SELECT id, nama_jabatan FROM m_jabatan ORDER BY nama_jabatan");
            $stmt->execute();
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'payment-types':
            $stmt = $db->prepare("SELECT id, kode_pembayaran, nama_pembayaran, tipe, nominal_default FROM m_jenis_pembayaran ORDER BY nama_pembayaran");
            $stmt->execute();
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'expense-categories':
            $stmt = $db->prepare("SELECT id, nama_kategori FROM m_kategori_pengeluaran ORDER BY nama_kategori");
            $stmt->execute();
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'income-categories':
            $stmt = $db->prepare("SELECT id, nama_kategori FROM m_kategori_pendapatan ORDER BY nama_kategori");
            $stmt->execute();
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'students':
            $stmt = $db->prepare("SELECT s.id, s.nis, s.nama_lengkap, k.nama_kelas 
                                 FROM m_siswa s 
                                 LEFT JOIN m_kelas k ON s.kelas_id = k.id 
                                 WHERE s.status = 'aktif' 
                                 ORDER BY s.nama_lengkap");
            $stmt->execute();
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'employees':
            $stmt = $db->prepare("SELECT p.id, p.nip, p.nama_lengkap, j.nama_jabatan 
                                 FROM m_pegawai p 
                                 LEFT JOIN m_jabatan j ON p.jabatan_id = j.id 
                                 WHERE p.status = 'aktif' 
                                 ORDER BY p.nama_lengkap");
            $stmt->execute();
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'users':
            $stmt = $db->prepare("SELECT id, nama_lengkap, username FROM m_users ORDER BY nama_lengkap");
            $stmt->execute();
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        default:
            $response = ['error' => 'Invalid type parameter'];
            break;
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
