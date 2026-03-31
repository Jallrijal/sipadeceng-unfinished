<?php

/**
 * Helper untuk mengelola tanda tangan dinamis
 */

/**
 * Upload dan validasi file tanda tangan
 */
function uploadSignatureFile($file, $userId, $signatureType = 'user') {
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    // Validasi file
    if (!in_array($file['type'], $allowedTypes)) {
        return [
            'success' => false, 
            'message' => 'Format file tidak diizinkan. Hanya JPG, PNG, atau GIF.'
        ];
    }
    
    if ($file['size'] > $maxSize) {
        return [
            'success' => false, 
            'message' => 'Ukuran file terlalu besar (max 2MB).'
        ];
    }
    
    // Generate filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'signature_' . $signatureType . '_' . $userId . '_' . date('YmdHis') . '.' . $extension;
    
    // Buat direktori jika belum ada
    $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/signatures/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $uploadPath = $uploadDir . $filename;
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return [
            'success' => true,
            'filename' => $filename,
            'path' => $uploadPath,
            'size' => $file['size'],
            'type' => $file['type']
        ];
    }
    
    return [
        'success' => false, 
        'message' => 'Gagal mengupload file tanda tangan.'
    ];
}

/**
 * Upload dan validasi file paraf
 */
function uploadParafFile($file, $userId) {
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $maxSize = 1 * 1024 * 1024; // 1MB (lebih kecil karena paraf)
    
    // Validasi file
    if (!in_array($file['type'], $allowedTypes)) {
        return [
            'success' => false, 
            'message' => 'Format file tidak diizinkan. Hanya JPG, PNG, atau GIF.'
        ];
    }
    
    if ($file['size'] > $maxSize) {
        return [
            'success' => false, 
            'message' => 'Ukuran file terlalu besar (max 1MB).'
        ];
    }
    
    // Generate filename dengan format img-parafUser{userId}
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'img-parafUser' . $userId . '_' . date('YmdHis') . '.' . $extension;
    
    // Buat direktori jika belum ada
    $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/signatures/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $uploadPath = $uploadDir . $filename;
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return [
            'success' => true,
            'filename' => $filename,
            'path' => $uploadPath,
            'size' => $file['size'],
            'type' => $file['type']
        ];
    }
    
    return [
        'success' => false, 
        'message' => 'Gagal mengupload file paraf.'
    ];
}

/**
 * Mendapatkan path file tanda tangan
 */
function getSignatureFilePath($filename) {
    return dirname(dirname(__DIR__)) . '/public/uploads/signatures/' . $filename;
}

/**
 * Mendapatkan URL tanda tangan
 */
function getSignatureUrl($filename) {
    return baseUrl('uploads/signatures/' . $filename);
}

/**
 * Menghapus file tanda tangan
 */
function deleteSignatureFile($filename) {
    $filePath = getSignatureFilePath($filename);
    if (file_exists($filePath)) {
        return unlink($filePath);
    }
    return false;
}

/**
 * Mendapatkan tanda tangan untuk user tertentu
 */
function getUserSignature($userId, $signatureType = 'user') {
    $signatureModel = new Signature();
    return $signatureModel->getUserSignature($userId, $signatureType);
}

/**
 * Mendapatkan URL tanda tangan untuk user tertentu
 */
function getUserSignatureUrl($userId, $signatureType = 'user') {
    $signature = getUserSignature($userId, $signatureType);
    if ($signature) {
        return getSignatureUrl($signature['signature_file']);
    }
    return null;
}

/**
 * Mendapatkan path file tanda tangan untuk user tertentu
 */
function getUserSignaturePath($userId, $signatureType = 'user') {
    $signature = getUserSignature($userId, $signatureType);
    if ($signature) {
        return getSignatureFilePath($signature['signature_file']);
    }
    return null;
}

/**
 * Menyimpan tanda tangan user
 */
function saveUserSignature($userId, $signatureType, $signatureFile, $fileSize, $fileType) {
    $signatureModel = new Signature();
    return $signatureModel->saveUserSignature($userId, $signatureType, $signatureFile, $fileSize, $fileType);
}

/**
 * Mendapatkan semua placeholder tanda tangan
 */
function getSignaturePlaceholders() {
    $signatureModel = new Signature();
    return $signatureModel->getSignaturePlaceholders();
}

/**
 * Mendapatkan placeholder berdasarkan key
 */
function getPlaceholderByKey($key) {
    $signatureModel = new Signature();
    return $signatureModel->getPlaceholderByKey($key);
}

/**
 * Mendapatkan tanda tangan untuk dokumen tertentu
 */
function getDocumentSignatures($leaveRequestId) {
    $signatureModel = new Signature();
    return $signatureModel->getDocumentSignatures($leaveRequestId);
}

/**
 * Mendapatkan tanda tangan berdasarkan placeholder untuk dokumen tertentu
 */
function getDocumentSignatureByPlaceholder($leaveRequestId, $placeholderKey) {
    $signatureModel = new Signature();
    return $signatureModel->getDocumentSignatureByPlaceholder($leaveRequestId, $placeholderKey);
}

/**
 * Menyimpan tanda tangan untuk dokumen tertentu
 */
function saveDocumentSignature($leaveRequestId, $placeholderId, $userId, $signatureFile, $signatureType) {
    $signatureModel = new Signature();
    return $signatureModel->saveDocumentSignature($leaveRequestId, $placeholderId, $userId, $signatureFile, $signatureType);
}

/**
 * Menghapus tanda tangan user
 */
function deleteUserSignature($userId, $signatureType) {
    $signatureModel = new Signature();
    return $signatureModel->deleteUserSignature($userId, $signatureType);
}

/**
 * Mendapatkan semua tanda tangan user (untuk admin)
 */
function getAllUserSignatures() {
    $signatureModel = new Signature();
    return $signatureModel->getAllUserSignatures();
}

/**
 * Mendapatkan statistik tanda tangan
 */
function getSignatureStats() {
    $signatureModel = new Signature();
    return $signatureModel->getSignatureStats();
}

/**
 * Validasi format file tanda tangan
 */
function validateSignatureFile($file) {
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        return [
            'valid' => false,
            'message' => 'Format file tidak diizinkan. Hanya JPG, PNG, atau GIF.'
        ];
    }
    
    if ($file['size'] > $maxSize) {
        return [
            'valid' => false,
            'message' => 'Ukuran file terlalu besar (max 2MB).'
        ];
    }
    
    return [
        'valid' => true,
        'message' => 'File valid.'
    ];
}

/**
 * Generate preview tanda tangan
 */
function generateSignaturePreview($filename, $width = 200, $height = 100) {
    $filePath = getSignatureFilePath($filename);
    if (!file_exists($filePath)) {
        return null;
    }
    
    $imageInfo = getimagesize($filePath);
    if (!$imageInfo) {
        return null;
    }
    
    $originalWidth = $imageInfo[0];
    $originalHeight = $imageInfo[1];
    
    // Hitung rasio aspek
    $ratio = min($width / $originalWidth, $height / $originalHeight);
    $newWidth = $originalWidth * $ratio;
    $newHeight = $originalHeight * $ratio;
    
    return [
        'url' => getSignatureUrl($filename),
        'width' => $newWidth,
        'height' => $newHeight,
        'original_width' => $originalWidth,
        'original_height' => $originalHeight
    ];
}

/**
 * Mendapatkan tipe signature berdasarkan user type
 */
function getSignatureTypeByUserType($userType) {
    // Map new user_type values to existing signature_type values
    // 'admin' -> 'admin' (signature row for authority), others -> 'user'
    return $userType === 'admin' ? 'admin' : 'user';
}

/**
 * Mendapatkan placeholder key berdasarkan user type
 */
function getPlaceholderKeyByUserType($userType) {
    return $userType === 'admin' ? 'ttd_admin' : 'ttd_user';
}

/**
 * Mendapatkan paraf untuk user tertentu
 */
function getUserParaf($userId) {
    return getUserSignature($userId, 'paraf');
}

/**
 * Mendapatkan URL paraf untuk user tertentu
 */
function getUserParafUrl($userId) {
    $paraf = getUserParaf($userId);
    if ($paraf) {
        return getSignatureUrl($paraf['signature_file']);
    }
    return null;
}

/**
 * Mendapatkan path file paraf untuk user tertentu
 */
function getUserParafPath($userId) {
    $paraf = getUserParaf($userId);
    if ($paraf) {
        return getSignatureFilePath($paraf['signature_file']);
    }
    return null;
}

/**
 * Upload file paraf khusus atasan cuti (kasubbag, kabag, sekretaris).
 * File akan disimpan di subfolder `parafAtasan` di bawah uploads/signatures
 */
function uploadParafAtasanCutiFile($file, $userId, $role) {
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $maxSize = 1 * 1024 * 1024; // 1MB (lebih kecil karena paraf)
    
    // Validasi file
    if (!in_array($file['type'], $allowedTypes)) {
        return [
            'success' => false, 
            'message' => 'Format file tidak diizinkan. Hanya JPG, PNG, atau GIF.'
        ];
    }
    
    if ($file['size'] > $maxSize) {
        return [
            'success' => false, 
            'message' => 'Ukuran file terlalu besar (max 1MB).'
        ];
    }
    
    // Generate filename dengan format img-parafAtsanCuti{role}_{userId}
    // Contoh: img-parafAtsanCutiKasubbag_9_20260221120000.png
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $rolePrefix = ucfirst($role); // kasubbag -> Kasubbag
    $filename = 'img-parafAtsanCuti' . $rolePrefix . '_' . $userId . '_' . date('YmdHis') . '.' . $extension;
    
    // Buat direktori khusus paraf atasan jika belum ada
    $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/signatures/parafAtasan/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $uploadPath = $uploadDir . $filename;
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // store filename with subfolder so retrieval helpers know to look in it
        $storedName = 'parafAtasan/' . $filename;
        return [
            'success' => true,
            'filename' => $storedName,
            'path' => $uploadPath,
            'size' => $file['size'],
            'type' => $file['type']
        ];
    }
    
    return [
        'success' => false, 
        'message' => 'Gagal mengupload file paraf atasan cuti.'
    ];
}

/**
 * Mendapatkan paraf khusus atasan cuti untuk user tertentu
 */
function getParafAtasanCuti($userId, $role) {
    $signatureType = 'paraf_' . $role;
    return getUserSignature($userId, $signatureType);
}

/**
 * Mendapatkan URL paraf khusus atasan cuti untuk user tertentu
 */
function getParafAtasanCutiUrl($userId, $role) {
    $paraf = getParafAtasanCuti($userId, $role);
    if ($paraf) {
        return getSignatureUrl($paraf['signature_file']);
    }
    return null;
}

/**
 * Mendapatkan path file paraf khusus atasan cuti untuk user tertentu
 */
function getParafAtasanCutiPath($userId, $role) {
    $paraf = getParafAtasanCuti($userId, $role);
    if ($paraf) {
        return getSignatureFilePath($paraf['signature_file']);
    }
    return null;
}
