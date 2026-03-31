<?php
/**
 * Workflow Helper for Kasubbag-based Multi-Level Approval
 * 
 * Implements the leave approval workflow:
 * Level 1: Direct atasan (any role)
 * Level 2: Kasubbag atasan (if direct atasan doesn't have kasubbag role)
 * Level 3: Admin/Pimpinan (final approval)
 */

/**
 * Determine next approver for a leave request after atasan approval
 * 
 * @param int $atasanId ID of the direct atasan
 * @param int $userId ID of the user who submitted leave
 * @param db $db Database instance
 * @return array ['requires_kasubbag' => bool, 'kasubbag_id' => int|null, 'kasubbag_name' => string|null]
 */
function getNextApproverAfterAtasan($atasanId, $userId, $db) {
    // Get the direct atasan's role
    $atasan = $db->fetch("SELECT role, nama_atasan FROM atasan WHERE id_atasan = ?", [$atasanId]);
    
    if (!$atasan) {
        return ['requires_kasubbag' => false, 'kasubbag_id' => null, 'kasubbag_name' => null];
    }
    
    // If direct atasan has kasubbag role, they still need to approve as kasubbag after direct approval
    if ($atasan['role'] === 'kasubbag') {
        return ['requires_kasubbag' => true, 'kasubbag_id' => $atasanId, 'kasubbag_name' => $atasan['nama_atasan']];
    }
    
    // Get user's unit_kerja to find kasubbag atasan
    $user = $db->fetch("SELECT unit_kerja FROM users WHERE id = ?", [$userId]);
    if (!$user) {
        return ['requires_kasubbag' => false, 'kasubbag_id' => null, 'kasubbag_name' => null];
    }
    
    // Find kasubbag atasan in the same unit
    // A kasubbag can be a direct atasan of pegawai, or an atasan of other atasans
    $kasubbag = $db->fetch(
        "SELECT a.id_atasan, a.nama_atasan FROM atasan a 
         WHERE a.role = 'kasubbag' 
         ORDER BY a.id_atasan LIMIT 1",
        []
    );
    
    if ($kasubbag) {
        return [
            'requires_kasubbag' => true,
            'kasubbag_id' => $kasubbag['id_atasan'],
            'kasubbag_name' => $kasubbag['nama_atasan']
        ];
    }
    
    return ['requires_kasubbag' => false, 'kasubbag_id' => null, 'kasubbag_name' => null];
}

/**
 * Get leaves pending kasubbag approval for a kasubbag atasan
 * 
 * @param int $kasubbagId ID of kasubbag atasan
 * @param db $db Database instance
 * @return array Leaves pending kasubbag approval
 */
function getLeavesForKasubbagApproval($kasubbagId, $db) {
    $sql = "SELECT lr.*, 
                lt.nama_cuti,
                u.nama, u.nip, u.jabatan, u.golongan, u.unit_kerja,
                a.nama_atasan as atasan_nama
            FROM leave_requests lr
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            JOIN users u ON lr.user_id = u.id
            JOIN atasan a ON lr.atasan_id = a.id_atasan
            WHERE lr.status = 'pending_kasubbag' 
            AND lr.kasubbag_id = ?
            ORDER BY lr.created_at DESC";
    
    return $db->fetchAll($sql, [$kasubbagId]);
}

/**
 * Get leaves that can be approved by current user based on their role
 * 
 * For atasans: returns leaves in 'pending' status that are under them as direct atasan
 * For kasubbag atasans: also includes 'pending_kasubbag' status leaves
 * For admin: includes everything ready for final approval
 * 
 * @param int $userId ID of current user (atasan)
 * @param string $userType Type of user ('atasan', 'admin')
 * @param string $atasanRole Role of atasan if applicable
 * @param db $db Database instance
 * @return array Leaves available for approval
 */
function getLeavesForApproval($userId, $userType, $atasanRole, $db) {
    if ($userType !== 'atasan' && $userType !== 'admin') {
        return [];
    }
    
    if ($userType === 'admin') {
        // Admin sees awaiting_pimpinan status
        $sql = "SELECT lr.*, 
                    lt.nama_cuti,
                    u.nama, u.nip, u.jabatan, u.golongan, u.unit_kerja,
                    a.nama_atasan as atasan_nama
                FROM leave_requests lr
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                JOIN users u ON lr.user_id = u.id
                JOIN atasan a ON lr.atasan_id = a.id_atasan
                WHERE lr.status IN ('awaiting_pimpinan', 'pending')
                ORDER BY lr.created_at DESC";
        return $db->fetchAll($sql, []);
    }
    
    // For atasans
    if ($atasanRole === 'kasubbag') {
        // Kasubbag sees: pending leaves from direct subordinates + pending_kasubbag from others
        $sql = "SELECT lr.*, 
                    lt.nama_cuti,
                    u.nama, u.nip, u.jabatan, u.golongan, u.unit_kerja,
                    a.nama_atasan as atasan_nama,
                    CASE 
                        WHEN lr.status = 'pending' AND lr.atasan_id = ? THEN 'Persetujuan Level 1'
                        WHEN lr.status = 'pending_kasubbag' AND lr.kasubbag_id = ? THEN 'Persetujuan Level 2'
                        ELSE 'Pending'
                    END as approval_level
                FROM leave_requests lr
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                JOIN users u ON lr.user_id = u.id
                JOIN atasan a ON lr.atasan_id = a.id_atasan
                WHERE (
                    (lr.status = 'pending' AND lr.atasan_id = ?)
                    OR
                    (lr.status = 'pending_kasubbag' AND lr.kasubbag_id = ?)
                )
                ORDER BY lr.created_at DESC";
        return $db->fetchAll($sql, [$userId, $userId, $userId, $userId]);
    } else {
        // Regular atasan sees only pending leaves from direct subordinates
        $sql = "SELECT lr.*, 
                    lt.nama_cuti,
                    u.nama, u.nip, u.jabatan, u.golongan, u.unit_kerja,
                    a.nama_atasan as atasan_nama
                FROM leave_requests lr
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                JOIN users u ON lr.user_id = u.id
                JOIN atasan a ON lr.atasan_id = a.id_atasan
                WHERE lr.status = 'pending' AND lr.atasan_id = ?
                ORDER BY lr.created_at DESC";
        return $db->fetchAll($sql, [$userId]);
    }
}

/**
 * Check if a leave is ready for a specific approval level
 * 
 * @param array $leave Leave request data
 * @param string $approvalLevel 'atasan', 'kasubbag', or 'pimpinan'
 * @param int $userId Current user ID
 * @param string $userRole Current user's role (for atasans)
 * @return bool
 */
function isLeaveReadyForApproval($leave, $approvalLevel, $userId, $userRole = null) {
    switch ($approvalLevel) {
        case 'atasan':
            // Ready if status is 'pending' and user is the direct atasan
            return $leave['status'] === 'pending' && $leave['atasan_id'] == $userId;
            
        case 'kasubbag':
            // Ready if status is 'pending_kasubbag' and user is the kasubbag
            return $leave['status'] === 'pending_kasubbag' && $leave['kasubbag_id'] == $userId;
            
        case 'pimpinan':
            // Ready if status is 'awaiting_pimpinan'
            return $leave['status'] === 'awaiting_pimpinan';
            
        default:
            return false;
    }
}

/**
 * Get status badge label for kasubbag workflow
 * 
 * @param string $status
 * @return string
 */
function getApprovalsStatusLabel($status) {
    $labels = [
        'draft' => 'Draft',
        'pending' => 'Menunggu Persetujuan Atasan',
        'pending_kasubbag' => 'Menunggu Persetujuan Kasubbag',
        'awaiting_pimpinan' => 'Menunggu Persetujuan Pimpinan',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'changed' => 'Perlu Perubahan',
        'postponed' => 'Ditangguhkan'
    ];
    
    return $labels[$status] ?? $status;
}
