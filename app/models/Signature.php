<?php

class Signature extends BaseModel {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Mendapatkan tanda tangan user berdasarkan user_id dan tipe
     */
    public function getUserSignature($userId, $signatureType = 'user') {
        return $this->db->fetch(
            "SELECT * FROM user_signatures WHERE user_id = ? AND signature_type = ? AND is_active = 1",
            [$userId, $signatureType]
        );
    }
    
    /**
     * Menyimpan tanda tangan user
     */
    public function saveUserSignature($userId, $signatureType, $signatureFile, $fileSize, $fileType) {
        // Cek apakah sudah ada tanda tangan untuk user ini
        $existing = $this->getUserSignature($userId, $signatureType);
        
        if ($existing) {
            // Update tanda tangan yang ada
            return $this->db->execute(
                "UPDATE user_signatures SET 
                signature_file = ?, 
                file_size = ?, 
                file_type = ?, 
                updated_at = NOW() 
                WHERE user_id = ? AND signature_type = ?",
                [$signatureFile, $fileSize, $fileType, $userId, $signatureType]
            );
        } else {
            // Insert tanda tangan baru
            return $this->db->execute(
                "INSERT INTO user_signatures (user_id, signature_type, signature_file, file_size, file_type) 
                VALUES (?, ?, ?, ?, ?)",
                [$userId, $signatureType, $signatureFile, $fileSize, $fileType]
            );
        }
    }
    
    /**
     * Mendapatkan semua placeholder tanda tangan
     */
    public function getSignaturePlaceholders() {
        return $this->db->fetchAll(
            "SELECT * FROM signature_placeholders WHERE is_active = 1 ORDER BY id"
        );
    }
    
    /**
     * Mendapatkan placeholder berdasarkan key
     */
    public function getPlaceholderByKey($key) {
        return $this->db->fetch(
            "SELECT * FROM signature_placeholders WHERE placeholder_key = ? AND is_active = 1",
            [$key]
        );
    }
    
    /**
     * Menyimpan tanda tangan untuk dokumen tertentu
     */
    public function saveDocumentSignature($leaveRequestId, $placeholderId, $userId, $signatureFile, $signatureType) {
        return $this->db->execute(
            "INSERT INTO document_signatures (leave_request_id, signature_placeholder_id, user_id, signature_file, signature_type) 
            VALUES (?, ?, ?, ?, ?)",
            [$leaveRequestId, $placeholderId, $userId, $signatureFile, $signatureType]
        );
    }
    
    /**
     * Mendapatkan tanda tangan untuk dokumen tertentu
     */
    public function getDocumentSignatures($leaveRequestId) {
        return $this->db->fetchAll(
            "SELECT ds.*, sp.placeholder_key, sp.placeholder_name, u.nama as user_name
            FROM document_signatures ds
            JOIN signature_placeholders sp ON ds.signature_placeholder_id = sp.id
            JOIN users u ON ds.user_id = u.id
            WHERE ds.leave_request_id = ?
            ORDER BY sp.id",
            [$leaveRequestId]
        );
    }
    
    /**
     * Mendapatkan tanda tangan berdasarkan placeholder untuk dokumen tertentu
     */
    public function getDocumentSignatureByPlaceholder($leaveRequestId, $placeholderKey) {
        return $this->db->fetch(
            "SELECT ds.*, sp.placeholder_key, sp.placeholder_name, u.nama as user_name
            FROM document_signatures ds
            JOIN signature_placeholders sp ON ds.signature_placeholder_id = sp.id
            JOIN users u ON ds.user_id = u.id
            WHERE ds.leave_request_id = ? AND sp.placeholder_key = ?",
            [$leaveRequestId, $placeholderKey]
        );
    }
    
    /**
     * Menghapus tanda tangan user
     */
    public function deleteUserSignature($userId, $signatureType) {
        return $this->db->execute(
            "DELETE FROM user_signatures WHERE user_id = ? AND signature_type = ?",
            [$userId, $signatureType]
        );
    }
    
    /**
     * Mendapatkan semua tanda tangan user (untuk admin)
     */
    public function getAllUserSignatures() {
        return $this->db->fetchAll(
            "SELECT us.*, u.nama, u.nip, u.jabatan, u.unit_kerja
            FROM user_signatures us
            JOIN users u ON us.user_id = u.id
            WHERE us.is_active = 1
            ORDER BY u.nama, us.signature_type"
        );
    }
    
    /**
     * Mendapatkan statistik tanda tangan
     */
    public function getSignatureStats() {
        $stats = [];
        
        // Total user dengan tanda tangan
        $stats['total_users_with_signature'] = $this->db->fetch(
            "SELECT COUNT(DISTINCT user_id) as total FROM user_signatures WHERE is_active = 1"
        )['total'];
        
        // Total tanda tangan user
        $stats['total_user_signatures'] = $this->db->fetch(
            "SELECT COUNT(*) as total FROM user_signatures WHERE signature_type = 'user' AND is_active = 1"
        )['total'];
        
        // Total tanda tangan admin
        $stats['total_admin_signatures'] = $this->db->fetch(
            "SELECT COUNT(*) as total FROM user_signatures WHERE signature_type = 'admin' AND is_active = 1"
        )['total'];
        
        return $stats;
    }
}