<?php

class FileUpload {
    
    private static $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif'];
    private static $maxFileSize = 5 * 1024 * 1024; // 5MB
    
    public static function uploadImage($file, $directory, $allowedTypes = null) {
        if ($allowedTypes === null) {
            $allowedTypes = self::$allowedImageTypes;
        }
        
        // Check if file was uploaded
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false, 
                'message' => 'No file uploaded or upload error occurred'
            ];
        }
        
        // Validate file size
        if ($file['size'] > self::$maxFileSize) {
            return [
                'success' => false, 
                'message' => 'File size too large. Maximum size is 5MB'
            ];
        }
        
        // Validate file type
        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);
        
        if (!in_array($extension, $allowedTypes)) {
            return [
                'success' => false, 
                'message' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes)
            ];
        }
        
        // Create upload directory if not exists
        $uploadDir = 'uploads/' . trim($directory, '/');
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return [
                    'success' => false, 
                    'message' => 'Failed to create upload directory'
                ];
            }
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . '/' . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true, 
                'filename' => $filename,
                'filepath' => $filepath
            ];
        } else {
            return [
                'success' => false, 
                'message' => 'Failed to move uploaded file'
            ];
        }
    }
    
    public static function deleteFile($directory, $filename) {
        if (empty($filename)) {
            return true;
        }
        
        $filepath = 'uploads/' . trim($directory, '/') . '/' . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        
        return true;
    }
    
    public static function getFileUrl($directory, $filename) {
        if (empty($filename)) {
            return null;
        }
        
        return 'uploads/' . trim($directory, '/') . '/' . $filename;
    }
}
