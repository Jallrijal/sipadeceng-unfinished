<?php
/**
 * Helper functions untuk alur pengajuan cuti baru
 */

/**
 * Cek apakah pengajuan cuti sudah siap untuk diproses admin
 */
function isLeaveReadyForAdmin($leaveData) {
    return $leaveData['status'] === 'pending' && $leaveData['blanko_uploaded'] == 1;
}

/**
 * Cek apakah pengajuan cuti sudah selesai (blanko final sudah diupload)
 */
function isLeaveCompleted($leaveData) {
    return $leaveData['status'] === 'completed' && $leaveData['final_blanko_sent'] == 1;
}

/**
 * Cek apakah kuota cuti sudah dipotong
 */
function isQuotaDeducted($leaveData) {
    return $leaveData['quota_deducted'] == 1;
}

/**
 * Dapatkan status badge untuk alur baru
 */
function getWorkflowStatusBadge($leaveData) {
    if ($leaveData['status'] === 'draft') {
        return '<span class="badge bg-secondary">Draft</span>';
    } elseif ($leaveData['status'] === 'pending') {
        if ($leaveData['blanko_uploaded'] == 1) {
            return '<span class="badge bg-warning text-dark">Menunggu Admin</span>';
        } else {
            return '<span class="badge bg-info">Menunggu Upload Blanko</span>';
        }
    } elseif ($leaveData['status'] === 'approved') {
        if ($leaveData['final_blanko_sent'] == 1) {
            return '<span class="badge bg-primary">Selesai</span>';
        } else {
            return '<span class="badge bg-success">Disetujui</span>';
        }
    } elseif ($leaveData['status'] === 'rejected') {
        if ($leaveData['final_blanko_sent'] == 1) {
            return '<span class="badge bg-primary">Selesai</span>';
        } else {
            return '<span class="badge bg-danger">Ditolak</span>';
        }
    } elseif ($leaveData['status'] === 'completed') {
        return '<span class="badge bg-primary">Selesai</span>';
    }
    
    return '<span class="badge bg-secondary">Unknown</span>';
}

/**
 * Dapatkan deskripsi status untuk alur baru
 */
function getWorkflowStatusDescription($leaveData) {
    if ($leaveData['status'] === 'draft') {
        return 'Pengajuan dalam status draft. Silakan download blanko dan upload setelah ditandatangani.';
    } elseif ($leaveData['status'] === 'pending') {
        if ($leaveData['blanko_uploaded'] == 1) {
            return 'Blanko telah diupload dan menunggu persetujuan admin.';
        } else {
            return 'Pengajuan menunggu upload blanko yang ditandatangani.';
        }
    } elseif ($leaveData['status'] === 'approved') {
        if ($leaveData['final_blanko_sent'] == 1) {
            return 'Pengajuan telah disetujui dan blanko final tersedia untuk diunduh.';
        } else {
            return 'Pengajuan telah disetujui. Admin akan mengupload blanko final.';
        }
    } elseif ($leaveData['status'] === 'rejected') {
        if ($leaveData['final_blanko_sent'] == 1) {
            return 'Pengajuan ditolak dan blanko final tersedia untuk diunduh.';
        } else {
            return 'Pengajuan ditolak. Admin akan mengupload blanko final.';
        }
    } elseif ($leaveData['status'] === 'completed') {
        return 'Proses pengajuan cuti telah selesai.';
    }
    
    return 'Status tidak diketahui.';
}

/**
 * Dapatkan langkah selanjutnya untuk user
 */
function getNextStepForUser($leaveData) {
    if ($leaveData['status'] === 'draft') {
        return 'Download blanko, tandatangani, dan upload kembali ke sistem.';
    } elseif ($leaveData['status'] === 'pending') {
        if ($leaveData['blanko_uploaded'] == 1) {
            return 'Menunggu admin memproses pengajuan.';
        } else {
            return 'Upload blanko yang telah ditandatangani.';
        }
    } elseif ($leaveData['status'] === 'approved') {
        if ($leaveData['final_blanko_sent'] == 1) {
            return 'Download blanko final yang telah ditandatangani admin.';
        } else {
            return 'Menunggu admin mengupload blanko final.';
        }
    } elseif ($leaveData['status'] === 'rejected') {
        if ($leaveData['final_blanko_sent'] == 1) {
            return 'Download blanko final yang telah ditandatangani admin.';
        } else {
            return 'Menunggu admin mengupload blanko final.';
        }
    } elseif ($leaveData['status'] === 'completed') {
        return 'Proses selesai.';
    }
    
    return 'Tidak ada langkah selanjutnya.';
}

/**
 * Dapatkan langkah selanjutnya untuk admin
 */
function getNextStepForAdmin($leaveData) {
    if ($leaveData['status'] === 'pending') {
        if ($leaveData['blanko_uploaded'] == 1) {
            return 'Proses pengajuan (setujui/tolak) dan generate blanko final.';
        } else {
            return 'Menunggu user mengupload blanko yang ditandatangani.';
        }
    } elseif ($leaveData['status'] === 'approved' || $leaveData['status'] === 'rejected') {
        if ($leaveData['final_blanko_sent'] == 1) {
            return 'Blanko final telah diupload. Proses selesai.';
        } else {
            return 'Upload blanko final yang telah ditandatangani.';
        }
    }
    
    return 'Tidak ada langkah selanjutnya.';
}

/**
 * Cek apakah pengajuan dapat diproses oleh admin
 */
function canAdminProcess($leaveData) {
    return $leaveData['status'] === 'pending' && $leaveData['blanko_uploaded'] == 1;
}

/**
 * Cek apakah blanko final dapat diupload
 */
function canUploadFinalBlanko($leaveData) {
    return in_array($leaveData['status'], ['approved', 'rejected']) && $leaveData['final_blanko_sent'] == 0;
}

/**
 * Cek apakah kuota dapat dipotong
 * Syarat: status=approved AND is_completed=1 AND quota_deducted=0
 */
function canDeductQuota($leaveData) {
    return $leaveData['status'] === 'approved' && 
           $leaveData['is_completed'] == 1 && 
           $leaveData['quota_deducted'] == 0;
} 