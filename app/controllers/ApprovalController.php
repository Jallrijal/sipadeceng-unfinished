<?php
class ApprovalController extends Controller {
    private $leaveModel;
    private $leaveBalanceModel;
    private $notificationModel;
    
    public function __construct() {
        // Make sure Database class is available
        if (!class_exists('Database')) {
            require_once 'app/core/Database.php';
        }
        
        $this->leaveModel = $this->model('Leave');
        $this->leaveBalanceModel = $this->model('LeaveBalance');
        $this->notificationModel = $this->model('Notification');
    }
    
    public function index() {
        requireLogin();

        if (!isAtasan() && !isAdmin()) {
            $this->redirect('dashboard');
        }

        $data = [
            'title' => 'Daftar Pengajuan Cuti',
            'page_title' => 'Daftar Pengajuan Cuti',
        ];

        $this->view('approval/index', $data);
    }
    
    public function process() {
        // Allow both atasan and pimpinan to process approvals
        requireLogin();

        $action = $_POST['action'];
        $leaveId = cleanInput($_POST['leave_id']);
        $catatan = cleanInput($_POST['catatan']);
        $jumlahHariDitangguhkan = isset($_POST['jumlah_hari_ditangguhkan']) ? intval($_POST['jumlah_hari_ditangguhkan']) : 0;
        // New parameter for kasubbag forward routing
        $forwardToRole = isset($_POST['forward_to_role']) ? cleanInput($_POST['forward_to_role']) : null;
        
        // Reject any attempt by admin (pimpinan) to directly process approvals
        if (isAdmin()) {
            $this->jsonResponse(['success' => false, 'message' => 'Akses tidak diizinkan. Final approval hanya dapat dilakukan oleh atasan dengan role ketua.']);
        }
        if (in_array($action, ['reject_leave', 'change_leave', 'postpone_leave']) && empty($catatan)) {
            $this->jsonResponse(['success' => false, 'message' => 'Catatan wajib diisi untuk aksi ini!']);
        }
        
        $leave = $this->leaveModel->find($leaveId);
        $userModel = $this->model('User');
        $userData = $userModel->find($leave['user_id']);
        // Hanya untuk Pengadilan Tinggi Agama Makassar (unit_kerja = 1)
        $isPTAMks = ($userData && isset($userData['unit_kerja']) && (int)$userData['unit_kerja'] === 1);
        
        // Load kasubbag workflow helper
        require_once dirname(__DIR__) . '/helpers/kasubbag_workflow_helper.php';
        
        // Determine what approval levels this user can process
        $isKasubbagRole = isKasubbag();
        
        // For atasan users, lookup their id_atasan based on NIP
        $atasanData = null;
        if (isAtasan()) {
            $atasanData = $this->db()->fetch(
                "SELECT a.id_atasan, a.role FROM atasan a WHERE a.NIP = ? LIMIT 1",
                [$_SESSION['nip']]
            );
        }
        
        // For atasan users, allow processing when status is 'pending' (level 1 approval)
        // Also allow 'pending_kasubbag' if they are kasubbag
        // Also allow 'pending_kabag' if they are kabag, and 'pending_sekretaris' if they are sekretaris
        $allowedStatusesForAtasan = ['pending'];
        if ($isKasubbagRole && $atasanData) {
            $allowedStatusesForAtasan[] = 'pending_kasubbag';
        }
        // Check if user is kabag
        $isKabagRole = ($atasanData && $atasanData['role'] === 'kabag');
        if ($isKabagRole) {
            $allowedStatusesForAtasan[] = 'pending_kabag';
        }
        // Check if user is sekretaris
        $isSekretarisRole = ($atasanData && $atasanData['role'] === 'sekretaris');
        if ($isSekretarisRole) {
            $allowedStatusesForAtasan[] = 'pending_sekretaris';
        }
        // Ketika atasan bertindak sebagai pimpinan final (role ketua), izinkan status awaiting_pimpinan
        $isKetuaRole = ($atasanData && $atasanData['role'] === 'ketua');
        if ($isKetuaRole) {
            $allowedStatusesForAtasan[] = 'awaiting_pimpinan';
        }
        
        // For pimpinan, expect status 'awaiting_pimpinan' (set by kasubbag or atasan) or legacy 'pending'
        $allowedStatusesForPimpinan = ['awaiting_pimpinan', 'pending'];

        // compute whether this atasan is authorized as the selected ketua
        $userAtasanId = $atasanData ? $atasanData['id_atasan'] : null;
        $isLevelFiveApprovalKetuaCheck = false;
        if ($isKetuaRole && $leave && $leave['status'] === 'awaiting_pimpinan') {
            $isLevelFiveApprovalKetuaCheck = (
                (isset($leave['ketua_approver_id']) && $leave['ketua_approver_id'] == $userAtasanId) ||
                ($leave['atasan_id'] == $userAtasanId)
            );
        }

        if (isAtasan()) {
            if (!$leave || !in_array($leave['status'], $allowedStatusesForAtasan)) {
                $this->jsonResponse(['success' => false, 'message' => 'Pengajuan tidak ditemukan atau sudah diproses atau tidak sesuai level approval Anda!']);
            }
            // Verify this atasan has right to approve this leave
            $isDirectAtasan = ($leave['atasan_id'] == $userAtasanId);
            $isKasubbagApprover = ($isKasubbagRole && $leave['kasubbag_id'] == $userAtasanId && $leave['status'] === 'pending_kasubbag');
            $isKabagApprover = ($isKabagRole && $leave['kabag_approver_id'] == $userAtasanId && $leave['status'] === 'pending_kabag');
            $isSekretarisApprover = ($isSekretarisRole && $leave['sekretaris_approver_id'] == $userAtasanId && $leave['status'] === 'pending_sekretaris');
            
            if (!$isDirectAtasan && !$isKasubbagApprover && !$isKabagApprover && !$isSekretarisApprover && !$isLevelFiveApprovalKetuaCheck) {
                $this->jsonResponse(['success' => false, 'message' => 'Anda tidak memiliki akses untuk memproses pengajuan ini!']);
            }
        } else {
            // Only atasan users may process approvals now
            $this->jsonResponse(['success' => false, 'message' => 'Akses tidak diizinkan untuk aksi ini']);
        }

        // Pastikan blanko user sudah diupload, kecuali untuk atasan/admin atau Pengadilan Tinggi Agama Makassar dan status draft
        // Atasan dapat merekomendasikan cuti ke pimpinan tanpa harus menunggu blanko diupload oleh user
        // Admin/pimpinan dapat mengapprove awaiting_pimpinan tanpa harus blanko diupload oleh user
        if (!$leave['blanko_uploaded'] && !isAtasan() && !isAdmin() && !($isPTAMks && $leave['status'] === 'draft')) {
            $this->jsonResponse(['success' => false, 'message' => 'Pengajuan belum lengkap. User belum mengupload blanko yang ditandatangani.']);
        }
        
        $this->db()->beginTransaction();
        
        try {
            if (isAtasan()) {
                $userAtasanId = $atasanData['id_atasan'];
                // Determine which approval level this atasan is doing
                $isLevelOneApproval = ($leave['status'] === 'pending' && $leave['atasan_id'] == $userAtasanId);
                $isLevelTwoApproval = ($leave['status'] === 'pending_kasubbag' && $leave['kasubbag_id'] == $userAtasanId);
                $isLevelThreeApprovalKabag = ($leave['status'] === 'pending_kabag' && $leave['kabag_approver_id'] == $userAtasanId);
                $isLevelFourApprovalSekretaris = ($leave['status'] === 'pending_sekretaris' && $leave['sekretaris_approver_id'] == $userAtasanId);
                // Level 5: Pimpinan final approval — only a ketua atasan selected for this leave
                $isLevelFiveApprovalKetua = ($isKetuaRole && $leave['status'] === 'awaiting_pimpinan' && (
                    (isset($leave['ketua_approver_id']) && $leave['ketua_approver_id'] == $userAtasanId) ||
                    ($leave['atasan_id'] == $userAtasanId)
                )); 
                
                // Validate allowed actions based on approval level
                // Level 1 and 5: all actions (approve, reject, change, postpone)
                // Level 2, 3, 4: only approve and reject
                if (in_array($action, ['change_leave', 'postpone_leave'])) {
                    if ($isLevelTwoApproval || $isLevelThreeApprovalKabag || $isLevelFourApprovalSekretaris) {
                        $this->db()->rollback();
                        $this->jsonResponse(['success' => false, 'message' => 'Aksi ini tidak diizinkan untuk level approval Anda.']);
                    }
                }
                
                // Actions performed by atasan: approve -> forward to next level, change, postpone, reject
                if ($action == 'approve_leave') {
                    if ($isLevelOneApproval) {
                        // Level 1: Direct atasan approval
                        // Determine if kasubbag level is needed
                        $nextApprover = getNextApproverAfterAtasan($leave['atasan_id'], $leave['user_id'], $this->db());
                        
                        if ($nextApprover['requires_kasubbag']) {
                            // Forward to kasubbag level 2
                            $sql = "UPDATE leave_requests SET status = 'pending_kasubbag', kasubbag_id = ?, atasan_approval_date = NOW(), atasan_catatan = ? WHERE id = ?";
                            $this->db()->execute($sql, [$nextApprover['kasubbag_id'], $catatan, $leaveId]);
                            $message = 'Pengajuan cuti direkomendasikan ke Kasubbag.';
                            
                            // Get kasubbag user_id for notification
                            $kasubbagUser = $this->db()->fetch(
                                "SELECT u.id FROM users u JOIN atasan a ON u.nip = a.NIP WHERE a.id_atasan = ? AND u.user_type = 'atasan' LIMIT 1",
                                [$nextApprover['kasubbag_id']]
                            );
                            if ($kasubbagUser) {
                                $this->notificationModel->sendNotification(
                                    $kasubbagUser['id'],
                                    "Pengajuan cuti dari " . $userData['nama'] . " direkomendasikan oleh atasan " . $_SESSION['nama'] . ". Silakan proses untuk level persetujuan kasubbag.",
                                    'info',
                                    $leaveId
                                );
                            }
                        } else {
                            // Direct forward to pimpinan (current atasan is kasubbag or no kasubbag needed)
                            $sql = "UPDATE leave_requests SET status = 'awaiting_pimpinan', atasan_approval_date = NOW(), atasan_catatan = CONCAT(IFNULL(atasan_catatan, ''), ?), kasubbag_approval_date = NOW(), kasubbag_catatan = ? WHERE id = ?";
                            $note = "[Rekomendasi Atasan: " . $_SESSION['nama'] . "]\n";
                            $this->db()->execute($sql, [$note, $catatan, $leaveId]);
                            $message = 'Pengajuan cuti direkomendasikan ke pimpinan.';
                            
                            // Notify admins/pimpinan
                            $this->notificationModel->notifyAdmins(
                                "Pengajuan cuti dari " . $userData['nama'] . " direkomendasikan oleh atasan/kasubbag " . $_SESSION['nama'] . ". Silakan proses.",
                                'info',
                                $leaveId
                            );
                        }
                    } else if ($isLevelTwoApproval) {
                        // Level 2: Kasubbag approval - forward to Kabag or Sekretaris based on choice
                        if (!$forwardToRole || !in_array($forwardToRole, ['kabag', 'sekretaris'])) {
                            $this->db()->rollback();
                            $this->jsonResponse(['success' => false, 'message' => 'Pilih routing untuk meneruskan pengajuan (ke Kabag atau Sekretaris)!']);
                        }
                        
                        // Find the target approver (Kabag or Sekretaris)
                        $approverRole = $forwardToRole;
                        $targetApprover = $this->db()->fetch(
                            "SELECT id_atasan, nama_atasan FROM atasan WHERE role = ? LIMIT 1",
                            [$approverRole]
                        );
                        
                        if (!$targetApprover) {
                            $this->db()->rollback();
                            $this->jsonResponse(['success' => false, 'message' => 'Target atasan (' . $approverRole . ') tidak ditemukan.']);
                        }
                        
                        // Set status based on forward_to_role
                        $newStatus = ($forwardToRole === 'kabag') ? 'pending_kabag' : 'pending_sekretaris';
                        $approverIdColumn = ($forwardToRole === 'kabag') ? 'kabag_approver_id' : 'sekretaris_approver_id';
                        
                        // Update leave request with new status and forward info
                        $updateSql = "UPDATE leave_requests SET status = ?, " . $approverIdColumn . " = ?, kasubbag_approval_date = NOW(), kasubbag_catatan = ? WHERE id = ?";
                        $this->db()->execute($updateSql, [$newStatus, $targetApprover['id_atasan'], $catatan, $leaveId]);
                        
                        $message = 'Pengajuan cuti diteruskan ke ' . ucfirst($forwardToRole) . '.';
                        
                        // Get target approver user for notification
                        $targetApproverUser = $this->db()->fetch(
                            "SELECT u.id FROM users u JOIN atasan a ON u.nip = a.NIP WHERE a.id_atasan = ? AND u.user_type = 'atasan' LIMIT 1",
                            [$targetApprover['id_atasan']]
                        );
                        if ($targetApproverUser) {
                            $this->notificationModel->sendNotification(
                                $targetApproverUser['id'],
                                "Pengajuan cuti dari " . $userData['nama'] . " telah disetujui oleh Kasubbag " . $_SESSION['nama'] . ". Silakan proses untuk level persetujuan " . $forwardToRole . ".",
                                'info',
                                $leaveId
                            );
                        }
                    } else if ($isLevelThreeApprovalKabag) {
                        // Level 3: Kabag approval - forward to Sekretaris
                        // Find the sekretaris approver
                        $sekretarisApprover = $this->db()->fetch(
                            "SELECT id_atasan, nama_atasan FROM atasan WHERE role = 'sekretaris' LIMIT 1",
                            []
                        );
                        
                        if (!$sekretarisApprover) {
                            $this->db()->rollback();
                            $this->jsonResponse(['success' => false, 'message' => 'Atasan Sekretaris tidak ditemukan.']);
                        }
                        
                        // Update leave request to forward to sekretaris
                        $this->db()->execute(
                            "UPDATE leave_requests SET status = 'pending_sekretaris', sekretaris_approver_id = ?, kabag_approval_date = NOW(), kabag_catatan = ? WHERE id = ?",
                            [$sekretarisApprover['id_atasan'], $catatan, $leaveId]
                        );
                        
                        $message = 'Pengajuan cuti diteruskan ke Sekretaris setelah persetujuan Kabag.';
                        
                        // Get sekretaris user for notification
                        $sekretarisUser = $this->db()->fetch(
                            "SELECT u.id FROM users u JOIN atasan a ON u.nip = a.NIP WHERE a.id_atasan = ? AND u.user_type = 'atasan' LIMIT 1",
                            [$sekretarisApprover['id_atasan']]
                        );
                        if ($sekretarisUser) {
                            $this->notificationModel->sendNotification(
                                $sekretarisUser['id'],
                                "Pengajuan cuti dari " . $userData['nama'] . " telah disetujui oleh Kabag " . $_SESSION['nama'] . ". Silakan proses untuk level persetujuan Sekretaris.",
                                'info',
                                $leaveId
                            );
                        }
                    } else if ($isLevelFourApprovalSekretaris) {
                        // Level 4: Sekretaris approval - forward to selected Pimpinan (Ketua/Wakil/PLH)
                        $forwardKetuaId = isset($_POST['forward_to_ketua_id']) ? intval($_POST['forward_to_ketua_id']) : 0;

                        if ($forwardKetuaId) {
                            // Save selected ketua approver on leave request (new column ketua_approver_id)
                            $this->db()->execute(
                                "UPDATE leave_requests SET status = 'awaiting_pimpinan', sekretaris_approval_date = NOW(), sekretaris_catatan = ?, ketua_approver_id = ? WHERE id = ?",
                                [$catatan, $forwardKetuaId, $leaveId]
                            );

                            $message = 'Pengajuan cuti diteruskan ke Pimpinan yang dipilih setelah persetujuan Sekretaris.';

                            // Notify the chosen ketua user (find user by atasan.id_atasan)
                            $ketuaUser = $this->db()->fetch(
                                "SELECT u.id FROM users u JOIN atasan a ON u.nip = a.NIP WHERE a.id_atasan = ? AND u.user_type = 'atasan' LIMIT 1",
                                [$forwardKetuaId]
                            );
                            if ($ketuaUser) {
                                $this->notificationModel->sendNotification(
                                    $ketuaUser['id'],
                                    "Pengajuan cuti dari " . $userData['nama'] . " telah diteruskan oleh Sekretaris " . $_SESSION['nama'] . ". Silakan proses persetujuan final.",
                                    'info',
                                    $leaveId
                                );
                            } else {
                                // Fallback: notify all admins if no specific atasan user mapping found
                                $this->notificationModel->notifyAdmins(
                                    "Pengajuan cuti dari " . $userData['nama'] . " telah disetujui oleh Sekretaris " . $_SESSION['nama'] . ". Silakan proses untuk persetujuan final.",
                                    'info',
                                    $leaveId
                                );
                            }
                        } else {
                            // No specific ketua chosen: fallback to previous behavior (notify admins)
                            $this->db()->execute(
                                "UPDATE leave_requests SET status = 'awaiting_pimpinan', sekretaris_approval_date = NOW(), sekretaris_catatan = ? WHERE id = ?",
                                [$catatan, $leaveId]
                            );

                            $message = 'Pengajuan cuti diteruskan ke Pimpinan setelah persetujuan Sekretaris.';
                            $this->notificationModel->notifyAdmins(
                                "Pengajuan cuti dari " . $userData['nama'] . " telah disetujui oleh Sekretaris " . $_SESSION['nama'] . ". Silakan proses untuk persetujuan final.",
                                'info',
                                $leaveId
                            );
                        }
                        // Setelah sekretaris approve, generate ulang dokumen (generated) agar placeholder p3 terisi
                        try {
                            require_once dirname(__DIR__) . '/helpers/document_helper.php';
                            // Ambil data leave terbaru dan user
                            $leave = $this->leaveModel->find($leaveId);
                            $userDataForDoc = $userModel->find($leave['user_id']);
                            $isAfterApprove = ($leave['status'] === 'approved');
                            $result = generateLeaveDocument($leave, $userDataForDoc, 'blanko_cuti_template.docx', false, $isAfterApprove);
                            if ($result['success']) {
                                $documentModel = $this->model('DocumentModel');
                                $existingDoc = $documentModel->getLatestByLeaveId($leaveId, 'generated');
                                if ($existingDoc) {
                                    $oldPath = dirname(dirname(__DIR__)) . '/public/uploads/documents/temp/' . $existingDoc['filename'];
                                    if (file_exists($oldPath)) { @unlink($oldPath); }
                                    $documentModel->update($existingDoc['id'], [
                                        'filename' => $result['filename'],
                                        'status' => $leave['status'],
                                        'created_at' => date('Y-m-d H:i:s')
                                    ]);
                                } else {
                                    $documentModel->createDocument([
                                        'leave_request_id' => $leaveId,
                                        'filename' => $result['filename'],
                                        'document_type' => 'generated',
                                        'status' => $leave['status'],
                                        'created_by' => $_SESSION['user_id']
                                    ]);
                                }
                            }
                        } catch (Exception $e) {
                            error_log('[ApprovalController] Gagal generate dokumen setelah sekretaris approve: ' . $e->getMessage());
                        }
                    }
                    
                    // If this is final approval by Pimpinan (ketua), process approval now and generate final doc
                    if ($isLevelFiveApprovalKetua) {
                        // Final approval by selected ketua
                        $this->leaveModel->approveByAtasan($leaveId, $userAtasanId, $catatan);
                        $message = 'Pengajuan cuti berhasil disetujui.';
                        // Notify user
                        $this->notificationModel->sendNotification(
                            $leave['user_id'],
                            "Pengajuan cuti Anda telah disetujui oleh " . $_SESSION['nama'] . ". blanko cuti anda sedang diproses oleh staf kepegawaian.",
                            'success',
                            $leaveId
                        );
                        
                        // === Generate ulang dokumen (generated) setelah ketua approval ===
                        // Placeholder admin akan terisi dengan data ketua
                        try {
                            require_once dirname(__DIR__) . '/helpers/document_helper.php';
                            // Ambil data leave terbaru dengan status yang sudah diupdate ke 'approved'
                            $leaveForRegenerate = $this->leaveModel->find($leaveId);
                            $userDataForRegenerate = $userModel->find($leaveForRegenerate['user_id']);
                            
                            // Generate dokumen dengan status 'approved' (placeholder admin akan terisi)
                            $resultGenerated = generateLeaveDocument(
                                $leaveForRegenerate, 
                                $userDataForRegenerate, 
                                'blanko_cuti_template.docx', 
                                false,  // includeAdminSignature = false (hanya untuk generated, bukan final)
                                true    // isAfterApprove = true (agar placeholder admin terisi dengan data ketua)
                            );
                            
                            if ($resultGenerated['success']) {
                                $documentModel = $this->model('DocumentModel');
                                $existingDoc = $documentModel->getLatestByLeaveId($leaveId, 'generated');
                                
                                if ($existingDoc) {
                                    // Update existing generated document
                                    $oldPath = dirname(dirname(__DIR__)) . '/public/uploads/documents/temp/' . $existingDoc['filename'];
                                    if (file_exists($oldPath)) { @unlink($oldPath); }
                                    $documentModel->update($existingDoc['id'], [
                                        'filename' => $resultGenerated['filename'],
                                        'status' => $leaveForRegenerate['status'],
                                        'created_at' => date('Y-m-d H:i:s')
                                    ]);
                                } else {
                                    // Create new generated document
                                    $documentModel->createDocument([
                                        'leave_request_id' => $leaveId,
                                        'filename' => $resultGenerated['filename'],
                                        'document_type' => 'generated',
                                        'status' => $leaveForRegenerate['status'],
                                        'created_by' => $_SESSION['user_id']
                                    ]);
                                }
                                error_log('[ApprovalController] Dokumen generated berhasil di-regenerate setelah ketua approval');
                            } else {
                                error_log('[ApprovalController] Gagal generate dokumen generated setelah ketua approval: ' . $resultGenerated['message']);
                            }
                        } catch (Exception $e) {
                            error_log('[ApprovalController] Exception saat generate dokumen setelah ketua approval: ' . $e->getMessage());
                        }
                        // Continue to generate final document below since status is now 'approved'
                    }

                    // Generate document hanya untuk level 1, level 2 atau kabag approval (level 3)
                    if ($isLevelOneApproval || $isLevelTwoApproval || $isLevelThreeApprovalKabag) {
                        require_once dirname(__DIR__) . '/helpers/document_helper.php';
                        // Ambil data leave terbaru untuk mendapatkan status yang sudah diupdate
                        $leave = $this->leaveModel->find($leaveId);
                        $leaveDataWithStatus = $leave;
                        
                        $currentUserData = $userModel->find($_SESSION['user_id']);
                        // generate blanko again using original user data; the helper now ignores any approver fields
$result = generateLeaveDocument($leaveDataWithStatus, $userData, 'blanko_cuti_template.docx', false, false, false);
                        
                        if ($result['success']) {
                            $documentModel = $this->model('DocumentModel');
                            $existingDoc = $documentModel->getLatestByLeaveId($leaveId, 'generated');
                            
                            if ($existingDoc) {
                                $oldPath = dirname(dirname(__DIR__)) . '/public/uploads/documents/temp/' . $existingDoc['filename'];
                                if (file_exists($oldPath)) {
                                    @unlink($oldPath);
                                }
                                $documentModel->update($existingDoc['id'], [
                                    'filename' => $result['filename'],
                                    'status' => $leaveDataWithStatus['status'],
                                    'created_at' => date('Y-m-d H:i:s')
                                ]);
                            } else {
                                $documentModel->createDocument([
                                    'leave_request_id' => $leaveId,
                                    'filename' => $result['filename'],
                                    'document_type' => 'generated',
                                    'status' => $leaveDataWithStatus['status'],
                                    'created_by' => $_SESSION['user_id']
                                ]);
                            }
                        }
                    }
                    
                    $this->db()->commit();
                    $this->jsonResponse(['success' => true, 'message' => $message]);
                    return;
                } else if ($action == 'change_leave') {
                    if ($isLevelOneApproval) {
                        $this->leaveModel->change($leaveId, $_SESSION['user_id'], $catatan);
                    } else if ($isLevelTwoApproval) {
                        // Kasubbag requesting changes - update kasubbag notes
                        $this->db()->execute(
                            "UPDATE leave_requests SET status = 'changed', kasubbag_approval_date = NOW(), kasubbag_catatan = ? WHERE id = ?",
                            [$catatan, $leaveId]
                        );
                    } else if ($isLevelThreeApprovalKabag) {
                        // Kabag requesting changes
                        $this->db()->execute(
                            "UPDATE leave_requests SET status = 'changed', kabag_approval_date = NOW(), kabag_catatan = ? WHERE id = ?",
                            [$catatan, $leaveId]
                        );
                    } else if ($isLevelFourApprovalSekretaris) {
                        // Sekretaris requesting changes
                        $this->db()->execute(
                            "UPDATE leave_requests SET status = 'changed', sekretaris_approval_date = NOW(), sekretaris_catatan = ? WHERE id = ?",
                            [$catatan, $leaveId]
                        );
                    }
                    $message = 'Pengajuan cuti perlu perubahan.';
                    $this->notificationModel->sendNotification(
                        $leave['user_id'],
                        "Pengajuan cuti Anda perlu perubahan dari " . $_SESSION['nama'] . ". Catatan: " . $catatan,
                        'warning',
                        $leaveId
                    );
                    $this->db()->commit();
                    $this->jsonResponse(['success' => true, 'message' => $message]);
                    return;
                } else if ($action == 'postpone_leave') {
                    if ($isLevelOneApproval) {
                        $this->leaveModel->postpone($leaveId, $_SESSION['user_id'], $catatan, $jumlahHariDitangguhkan);
                    } else if ($isLevelTwoApproval) {
                        // Kasubbag postponing - update kasubbag notes
                        $this->db()->execute(
                            "UPDATE leave_requests SET status = 'postponed', kasubbag_approval_date = NOW(), kasubbag_catatan = ?, jumlah_hari_ditangguhkan = ? WHERE id = ?",
                            [$catatan, $jumlahHariDitangguhkan, $leaveId]
                        );
                    } else if ($isLevelThreeApprovalKabag) {
                        // Kabag postponing
                        $this->db()->execute(
                            "UPDATE leave_requests SET status = 'postponed', kabag_approval_date = NOW(), kabag_catatan = ?, jumlah_hari_ditangguhkan = ? WHERE id = ?",
                            [$catatan, $jumlahHariDitangguhkan, $leaveId]
                        );
                    } else if ($isLevelFourApprovalSekretaris) {
                        // Sekretaris postponing
                        $this->db()->execute(
                            "UPDATE leave_requests SET status = 'postponed', sekretaris_approval_date = NOW(), sekretaris_catatan = ?, jumlah_hari_ditangguhkan = ? WHERE id = ?",
                            [$catatan, $jumlahHariDitangguhkan, $leaveId]
                        );
                    }
                    $message = 'Pengajuan cuti ditangguhkan.';
                    $this->notificationModel->sendNotification(
                        $leave['user_id'],
                        "Pengajuan cuti Anda ditangguhkan oleh " . $_SESSION['nama'] . ". Catatan: " . $catatan,
                        'warning',
                        $leaveId
                    );
                    $this->db()->commit();
                    $this->jsonResponse(['success' => true, 'message' => $message]);
                    return;
                } else {
                    // REJECT
                    if ($isLevelOneApproval) {
                        $this->leaveModel->reject($leaveId, $_SESSION['user_id'], $catatan);
                    } else if ($isLevelTwoApproval) {
                        // Kasubbag rejecting
                        $this->db()->execute(
                            "UPDATE leave_requests SET status = 'rejected', kasubbag_approval_date = NOW(), kasubbag_catatan = ? WHERE id = ?",
                            [$catatan, $leaveId]
                        );
                    } else if ($isLevelThreeApprovalKabag) {
                        // Kabag rejecting
                        $this->db()->execute(
                            "UPDATE leave_requests SET status = 'rejected', kabag_approval_date = NOW(), kabag_catatan = ? WHERE id = ?",
                            [$catatan, $leaveId]
                        );
                    } else if ($isLevelFourApprovalSekretaris) {
                        // Sekretaris rejecting
                        $this->db()->execute(
                            "UPDATE leave_requests SET status = 'rejected', sekretaris_approval_date = NOW(), sekretaris_catatan = ? WHERE id = ?",
                            [$catatan, $leaveId]
                        );
                    }
                    $message = 'Pengajuan cuti berhasil ditolak.';
                    $this->notificationModel->sendNotification(
                        $leave['user_id'],
                        "Pengajuan cuti Anda telah ditolak oleh " . $_SESSION['nama'] . ". Catatan: " . $catatan,
                        'warning',
                        $leaveId
                    );
                    $this->db()->commit();
                    $this->jsonResponse(['success' => true, 'message' => $message]);
                    return;
                }
            } else {
                // Sanity: this branch should never be reached because admins are blocked earlier
                $this->db()->rollback();
                $this->jsonResponse(['success' => false, 'message' => 'Akses tidak diizinkan untuk pimpinan (admin).']);
            }
            
            // Ambil data leave terbaru setelah update
            $leave = $this->leaveModel->find($leaveId);
            
            // Ambil data user
            $userData = $userModel->find($leave['user_id']);
            
            // Jika status sekarang adalah 'approved' (pimpinan menyetujui), generate dokumen untuk preview
            // is_completed masih tetap 0, akan diset menjadi 1 ketika admin mengupload dokumen final
            if ($leave['status'] == 'approved') {
                // Generate dokumen final otomatis (dengan dua tanda tangan) untuk preview/reference
                require_once dirname(__DIR__) . '/helpers/document_helper.php';
                $result = generateLeaveDocument($leave, $userData, 'blanko_cuti_template.docx', true);

                if ($result['success']) {
                    // === Pindahkan file dari temp ke signed ===
                    $tempPath = dirname(dirname(__DIR__)) . '/public/uploads/documents/temp/' . $result['filename'];
                    $signedPath = dirname(dirname(__DIR__)) . '/public/uploads/documents/signed/' . $result['filename'];
                    if (file_exists($tempPath)) {
                        copy($tempPath, $signedPath);
                    }
                    // Simpan ke leave_documents
                    $documentModel = $this->model('DocumentModel');
                    $documentModel->createDocument([
                        'leave_request_id' => $leaveId,
                        'filename' => $result['filename'],
                        'document_type' => 'admin_signed',
                        'status' => 'final',
                        'created_by' => $_SESSION['user_id'],
                        'upload_date' => date('Y-m-d H:i:s')
                    ]);

                    // Kirim notifikasi approval ke user bahwa sudah disetujui dan menunggu upload dokumen final
                    $this->notificationModel->sendNotification(
                        $leave['user_id'],
                        "Pengajuan cuti Anda telah disetujui oleh " . $_SESSION['nama'] . ". Silakan download blanko final untuk ditandatangani dan diupload kembali.",
                        'success',
                        $leaveId
                    );
                } else {
                    throw new Exception('Gagal generate blanko final: ' . $result['message']);
                }
            } else {
                // Jika status bukan approved (mis. rejected/changed/postponed), generate dokumen final dan kirim notifikasi sesuai status
                require_once dirname(__DIR__) . '/helpers/document_helper.php';
                $result = generateLeaveDocument($leave, $userData, 'blanko_cuti_template.docx', true);

                if ($result['success']) {
                    $tempPath = dirname(dirname(__DIR__)) . '/public/uploads/documents/temp/' . $result['filename'];
                    $signedPath = dirname(dirname(__DIR__)) . '/public/uploads/documents/signed/' . $result['filename'];
                    if (file_exists($tempPath)) {
                        copy($tempPath, $signedPath);
                    }
                    $documentModel = $this->model('DocumentModel');
                    $documentModel->createDocument([
                        'leave_request_id' => $leaveId,
                        'filename' => $result['filename'],
                        'document_type' => 'admin_signed',
                        'status' => 'final',
                        'created_by' => $_SESSION['user_id'],
                        'upload_date' => date('Y-m-d H:i:s')
                    ]);

                    if (in_array($leave['status'], ['rejected', 'changed', 'postponed'])) {
                        $notifMsg = '';
                        if ($leave['status'] == 'rejected') {
                            $notifMsg = "Pengajuan cuti Anda telah ditolak oleh " . $_SESSION['nama'] . ". Catatan: " . $leave['catatan_approval'] . ". Dokumen final telah tersedia untuk diunduh.";
                        } else if ($leave['status'] == 'changed') {
                            $notifMsg = "Pengajuan cuti Anda perlu perubahan oleh " . $_SESSION['nama'] . ". Catatan: " . $leave['catatan_approval'] . ". Dokumen final telah tersedia untuk diunduh.";
                        } else if ($leave['status'] == 'postponed') {
                            $notifMsg = "Pengajuan cuti Anda ditangguhkan oleh " . $_SESSION['nama'] . ". Catatan: " . $leave['catatan_approval'] . ". Dokumen final telah tersedia untuk diunduh.";
                        }
                        $this->notificationModel->sendNotification(
                            $leave['user_id'],
                            $notifMsg,
                            'warning',
                            $leaveId
                        );
                    }
                } else {
                    throw new Exception('Gagal generate blanko final: ' . $result['message']);
                }
            }
            
            // DEBUG: Log status sebelum commit
            error_log("=== APPROVAL PROCESS DEBUG ===");
            error_log("Action: " . $action);
            error_log("LeaveId: " . $leaveId);
            $leaveCheck = $this->leaveModel->find($leaveId);
            error_log("Status sebelum commit: " . ($leaveCheck ? $leaveCheck['status'] : 'NULL'));
            if (isAdmin()) {
                error_log("Admin approver ID: " . $adminApproverId);
            }
            error_log("=== END APPROVAL PROCESS DEBUG ===");
            
            $this->db()->commit();
            $this->jsonResponse(['success' => true, 'message' => $message . ' Blanko final berhasil dibuat dan notifikasi dikirim ke user.']);
        } catch (Exception $e) {
            $this->db()->rollback();
            $this->jsonResponse(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
    
    // Method baru untuk mengirim notifikasi setelah upload dokumen final
    public function sendFinalNotification() {
        requireAdmin();
        
        $leaveId = cleanInput($_POST['leave_id']);
        $leave = $this->leaveModel->find($leaveId);
        
        if (!$leave || !in_array($leave['status'], ['approved', 'rejected'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Pengajuan tidak valid untuk dikirim notifikasi']);
        }
        
        // Cek apakah dokumen final sudah diupload
        $documentModel = $this->model('DocumentModel');
        $finalDoc = $documentModel->getLatestByLeaveId($leaveId, 'admin_signed');
        
        if (!$finalDoc) {
            $this->jsonResponse(['success' => false, 'message' => 'Dokumen final belum diupload']);
        }
        
        try {
            if ($leave['status'] == 'approved') {
                // Kirim notifikasi approval
                $this->notificationModel->sendNotification(
                    $leave['user_id'],
                    "Pengajuan cuti Anda telah disetujui oleh " . $_SESSION['nama'] . ". Dokumen final telah tersedia untuk diunduh.",
                    'success'
                );
            } else {
                // Kirim notifikasi rejection
                $this->notificationModel->sendNotification(
                    $leave['user_id'],
                    "Pengajuan cuti Anda telah ditolak oleh " . $_SESSION['nama'] . ". Catatan: " . $leave['catatan_approval'] . ". Dokumen final telah tersedia untuk diunduh.",
                    'warning'
                );
            }
            
            $this->jsonResponse(['success' => true, 'message' => 'Notifikasi berhasil dikirim ke user']);
            
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Gagal mengirim notifikasi: ' . $e->getMessage()]);
        }
    }
    
    public function getDetail() {
        requireLogin();
        if (!isAtasan() && !isAdmin()) {
            $this->jsonResponse(['success' => false, 'message' => 'Akses ditolak']);
        }

        $leaveId = cleanInput($_POST['leave_id']);
        $leave = $this->leaveModel->find($leaveId);
        
        if (!$leave) {
            $this->jsonResponse(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
        
        // Get additional info
    $sql = "SELECT lr.*, lt.nama_cuti, u.nama, u.nip, u.jabatan, u.golongan, u.unit_kerja,
        au.nama as approved_by_name
        FROM leave_requests lr
        JOIN leave_types lt ON lr.leave_type_id = lt.id
        JOIN users u ON lr.user_id = u.id
        LEFT JOIN users au ON lr.approved_by = au.id AND au.is_deleted = 0
        WHERE lr.id = ?";
        
        $data = $this->db()->fetch($sql, [$leaveId]);
        
        // Format dates
        $data['tanggal_mulai_formatted'] = formatTanggal($data['tanggal_mulai']);
        $data['tanggal_selesai_formatted'] = formatTanggal($data['tanggal_selesai']);
        $data['status_badge'] = getStatusBadge($data['status']);
        
        // Get user's leave balance
        $tahun = date('Y', strtotime($data['tanggal_mulai']));
        $data['sisa_kuota'] = $this->leaveBalanceModel->getTotalBalance($data['user_id'], $tahun);
        
        // Check for documents
        $documentModel = $this->model('DocumentModel');
        $signedDoc = $documentModel->getLatestByLeaveId($leaveId, 'user_signed');
        $finalDoc = $documentModel->getLatestByLeaveId($leaveId, 'admin_signed');
        
        $data['has_signed_doc'] = !empty($signedDoc);
        $data['has_final_doc'] = !empty($finalDoc);
        $data['blanko_uploaded'] = $leave['blanko_uploaded'];
        $data['final_blanko_sent'] = $leave['final_blanko_sent'];
        $data['quota_deducted'] = $leave['quota_deducted'];
        
        // Add document info if available
        if ($signedDoc) {
            $data['signed_doc_filename'] = $signedDoc['filename'];
            $data['signed_doc_upload_date'] = formatTanggal($signedDoc['upload_date']);
        }
        if ($finalDoc) {
            $data['final_doc_filename'] = $finalDoc['filename'];
            $data['final_doc_upload_date'] = formatTanggal($finalDoc['upload_date']);
        }
        // include generated document info for use by sekretaris and others
        $generatedDoc = $documentModel->getLatestByLeaveId($leaveId, 'generated');
        $data['has_generated_doc'] = !empty($generatedDoc);
        if ($generatedDoc) {
            $data['generated_doc_filename'] = $generatedDoc['filename'];
        }
        // compute permission flag for this viewer
        require_once dirname(__DIR__) . '/helpers/general_helper.php';
        $data['can_download_generated'] = canDownloadGeneratedDocRow($data);
        // Tambahan: kirim dokumen pendukung jika ada
        $data['dokumen_pendukung'] = $leave['dokumen_pendukung'] ?? null;
        
        // Tambahan: info tentang siapa yang approve sebelum sampai ke sekretaris
        $data['last_approver_info'] = null;
        $data['last_approver_source'] = null; // 'kasubbag' atau 'kabag'
        if ($data['status'] === 'pending_sekretaris') {
            if (!empty($data['kabag_approver_id'])) {
                // Approval dari Kabag
                $kabagSql = "SELECT id_atasan, nama_atasan, jabatan FROM atasan WHERE id_atasan = ? LIMIT 1";
                $kabagData = $this->db()->fetch($kabagSql, [$data['kabag_approver_id']]);
                if ($kabagData) {
                    $data['last_approver_info'] = $kabagData['nama_atasan'] . ' (' . $kabagData['jabatan'] . ')';
                    $data['last_approver_source'] = 'kabag';
                }
            } else if (!empty($data['kasubbag_id'])) {
                // Approval langsung dari Kasubbag
                $kasubbagSql = "SELECT id_atasan, nama_atasan, jabatan FROM atasan WHERE id_atasan = ? LIMIT 1";
                $kasubbagData = $this->db()->fetch($kasubbagSql, [$data['kasubbag_id']]);
                if ($kasubbagData) {
                    $data['last_approver_info'] = $kasubbagData['nama_atasan'] . ' (' . $kasubbagData['jabatan'] . ')';
                    $data['last_approver_source'] = 'kasubbag';
                }
            }
        }
        
    // Tambahan: info khusus PTA Makassar dan status draft
    $data['is_pta_makassar'] = (isset($data['unit_kerja']) && (int)$data['unit_kerja'] === 1);
    $data['status_draft'] = ($data['status'] === 'draft');
    $this->jsonResponse(['success' => true, 'data' => $data, 'is_admin' => (function_exists('isAdmin') && isAdmin())]);
    }

    /**
     * Return list of atasan with role=ketua (Ketua, Wakil, PLH) for Sekretaris to choose
     */
    public function getPimpinanList() {
        requireLogin();
        if (!isAtasan()) {
            $this->jsonResponse(['success' => false, 'message' => 'Akses ditolak']);
        }

        try {
            $rows = $this->db()->fetchAll(
                "SELECT id_atasan, nama_atasan, NIP, jabatan FROM atasan WHERE role = 'ketua' OR jabatan LIKE '%Ketua%' ORDER BY FIELD(role, 'ketua') DESC, nama_atasan ASC"
            );

            $this->jsonResponse(['success' => true, 'data' => $rows]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Gagal mengambil daftar pimpinan: ' . $e->getMessage()]);
        }
    }
    
    public function quota() {
        requireAdmin();
        
        $data = [
            'title' => 'Kuota Cuti Pegawai',
            'page_title' => 'Kuota Cuti Pegawai'
        ];
        
        $this->view('approval/quota', $data);
    }
    
    public function getUsersWithBalance() {
        requireAdmin();
        
        $tahun = isset($_POST['tahun']) ? intval($_POST['tahun']) : date('Y');
        $leaveTypeId = isset($_POST['leave_type_id']) ? intval($_POST['leave_type_id']) : 1;
        try {
            $users = $this->leaveBalanceModel->getUsersWithBalance($tahun, $leaveTypeId);
            $this->jsonResponse(['success' => true, 'data' => $users]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function upload($leaveId = null) {
        requireAdmin();
        
        if (!$leaveId) {
            $this->redirect('approval');
        }
        
        $leave = $this->leaveModel->find($leaveId);
        
        // Validasi akses dan status - sekarang bisa untuk approved, rejected, changed, atau postponed
        if (!$leave || !in_array($leave['status'], ['approved', 'rejected', 'changed', 'postponed'])) {
            $_SESSION['error'] = 'Pengajuan tidak ditemukan atau belum diproses';
            $this->redirect('approval');
        }
        
        // Get additional info
    $sql = "SELECT lr.*, lt.nama_cuti, u.nama, u.nip, u.jabatan, u.unit_kerja
        FROM leave_requests lr
        JOIN leave_types lt ON lr.leave_type_id = lt.id
        JOIN users u ON lr.user_id = u.id
        WHERE lr.id = ?";
        
        $leaveData = $this->db()->fetch($sql, [$leaveId]);
        
        $data = [
            'title' => 'Upload Dokumen Final',
            'page_title' => 'Upload Dokumen Final',
            'leaveId' => $leaveId,
            'leaveData' => $leaveData
        ];
        
        $this->view('approval/upload', $data);
    }

    /**
     * Endpoint untuk menjalankan pengelolaan kuota otomatis secara manual
     * Hanya untuk testing dan admin
     */
    public function runQuotaManagement() {
        requireAdmin();
        try {
            // Jalankan pengelolaan kuota menggunakan model LeaveBalance
            $targetYear = isset($_POST['year']) ? intval($_POST['year']) : null;
                $result = $this->leaveBalanceModel->runQuotaManagement($targetYear);

                if (is_array($result) && isset($result['success']) && $result['success']) {
                    $response = [
                        'success' => true,
                        'message' => 'Pengelolaan kuota otomatis selesai',
                        'year' => $result['year'],
                        'processed' => $result['processed'],
                        'created' => $result['created'],
                        'updated' => $result['updated']
                    ];
                    if (!empty($result['backup_file'])) $response['backup_file'] = $result['backup_file'];
                    if (!empty($result['log_file'])) $response['log_file'] = $result['log_file'];
                    $this->jsonResponse($response);
                } else {
                    $msg = is_array($result) && isset($result['message']) ? $result['message'] : 'Pengelolaan kuota otomatis gagal. Cek log aplikasi.';
                    $resp = ['success' => false, 'message' => $msg];
                    if (is_array($result) && !empty($result['backup_file'])) $resp['backup_file'] = $result['backup_file'];
                    if (is_array($result) && !empty($result['log_file'])) $resp['log_file'] = $result['log_file'];
                    $this->jsonResponse($resp);
                }
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Mendapatkan data kuota 3 tahun terakhir untuk user tertentu
     */
    public function getThreeYearQuota() {
        requireAdmin();
        
        $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        
        if (!$userId) {
            $this->jsonResponse(['success' => false, 'message' => 'User ID tidak valid']);
        }
        
        try {
            $quotaData = $this->leaveBalanceModel->getTotalQuotaLastThreeYears($userId);
            $this->jsonResponse(['success' => true, 'data' => $quotaData]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Mendapatkan daftar user dengan kuota 3 tahun terakhir
     */
    public function getUsersWithThreeYearQuota() {
        requireAdmin();
        
        try {
            $users = $this->leaveBalanceModel->getUsersWithThreeYearQuota();
            $this->jsonResponse(['success' => true, 'data' => $users]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Endpoint test untuk debugging
     */
    public function testQuotaHelper() {
        requireAdmin();
        
        try {
            // Include database config first
            require_once dirname(__DIR__) . '/../config/database.php';
            
            // Test database connection
            $db = Database::getInstance();
            
            // Test helper include
            $helperPath = dirname(__DIR__) . '/helpers/quota_scheduler_helper_fixed.php';
            if (!file_exists($helperPath)) {
                throw new Exception('Helper file tidak ditemukan: ' . $helperPath);
            }
            
            require_once $helperPath;
            
            // Test function exists
            if (!function_exists('runAnnualQuotaManagement')) {
                throw new Exception('Function runAnnualQuotaManagement tidak ditemukan');
            }
            
            // Test simple database query
            $testQuery = $db->fetchAll("SELECT COUNT(*) as total FROM users WHERE user_type IN ('pegawai', 'atasan')");
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Test endpoint berhasil diakses',
                'timestamp' => date('Y-m-d H:i:s'),
                'current_year' => date('Y'),
                'helper_path' => $helperPath,
                'function_exists' => function_exists('runAnnualQuotaManagement'),
                'user_count' => $testQuery[0]['total']
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Test endpoint untuk pengelolaan kuota cuti sakit
     */
    public function testQuotaSakitHelper() {
        requireAdmin();
        try {
            $db = Database::getInstance();
            // Simple test: count distinct tahun in kuota_cuti_sakit and total pegawai
            $countYears = $db->fetchAll("SELECT tahun, COUNT(*) as total FROM kuota_cuti_sakit GROUP BY tahun ORDER BY tahun DESC");
            $userCount = $db->fetchAll("SELECT COUNT(*) as total FROM users WHERE user_type IN ('pegawai', 'atasan') AND is_deleted = 0");

            $this->jsonResponse([
                'success' => true,
                'message' => 'Test endpoint kuota cuti sakit berhasil diakses',
                'timestamp' => date('Y-m-d H:i:s'),
                'current_year' => date('Y'),
                'years' => $countYears,
                'user_count' => $userCount[0]['total']
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Endpoint untuk menjalankan pengelolaan kuota cuti sakit secara manual
     */
    public function runQuotaSakitManagement() {
        requireAdmin();
        try {
            $targetYear = isset($_POST['year']) ? intval($_POST['year']) : null;
            $kuotaSakitModel = $this->model('KuotaCutiSakit');
            $result = $kuotaSakitModel->runSickQuotaManagement($targetYear);

            if (is_array($result) && isset($result['success']) && $result['success']) {
                $response = [
                    'success' => true,
                    'message' => 'Pengelolaan kuota cuti sakit selesai',
                    'year' => $result['year'],
                    'deleted_year' => $result['deleted_year'],
                    'processed' => $result['processed'],
                    'created' => $result['created'],
                    'updated' => $result['updated'],
                    'deleted' => $result['deleted']
                ];
                if (!empty($result['backup_file'])) $response['backup_file'] = $result['backup_file'];
                if (!empty($result['log_file'])) $response['log_file'] = $result['log_file'];
                $this->jsonResponse($response);
            } else {
                $msg = is_array($result) && isset($result['message']) ? $result['message'] : 'Pengelolaan kuota cuti sakit gagal. Cek log aplikasi.';
                $resp = ['success' => false, 'message' => $msg];
                if (is_array($result) && !empty($result['backup_file'])) $resp['backup_file'] = $result['backup_file'];
                if (is_array($result) && !empty($result['log_file'])) $resp['log_file'] = $result['log_file'];
                $this->jsonResponse($resp);
            }
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    public function uploadAdminDocument() {
        requireLogin();
        if (!isAdmin()) {
            $this->jsonResponse(['success' => false, 'message' => 'Akses ditolak']);
        }

        $leaveId = cleanInput($_POST['leave_id']);
        $leave = $this->leaveModel->find($leaveId);

        if (!$leave) {
            $this->jsonResponse(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        if ($leave['status'] !== 'pending_admin_upload') {
            $this->jsonResponse(['success' => false, 'message' => 'Status pengajuan tidak sesuai']);
        }

        // Handle file upload
        if (!isset($_FILES['dokumen_pendukung']) || $_FILES['dokumen_pendukung']['error'] != 0) {
            $this->jsonResponse(['success' => false, 'message' => 'Dokumen pendukung wajib diupload']);
        }

        $uploadResult = uploadDokumen($_FILES['dokumen_pendukung']);
        if (!$uploadResult['success']) {
            $this->jsonResponse(['success' => false, 'message' => $uploadResult['message']]);
        }

        // Update leave request
        $this->db()->beginTransaction();
        try {
            // Update dokumen pendukung dan status
            $this->leaveModel->update($leaveId, [
                'dokumen_pendukung' => $uploadResult['filename'],
                'status' => 'pending'
            ]);

            // Log admin activity
            $adminActivityModel = $this->model('AdminActivity');
            $adminActivityModel->logActivity($_SESSION['user_id'], 'upload_supporting_document', $leaveId);

            // Kirim notifikasi ke user
            $notificationModel = $this->model('Notification');
            $notificationModel->sendNotification(
                $leave['user_id'],
                "Dokumen pendukung untuk pengajuan cuti {$leave['nomor_surat']} telah diupload admin. Pengajuan Anda sekarang menunggu approval atasan.",
                'info',
                $leaveId
            );

            $this->db()->commit();
            $this->jsonResponse(['success' => true, 'message' => 'Dokumen berhasil diupload dan pengajuan dikirim ke atasan']);
        } catch (Exception $e) {
            $this->db()->rollback();
            $this->jsonResponse(['success' => false, 'message' => 'Gagal memproses upload: ' . $e->getMessage()]);
        }
    }

    public function continueProcess() {
        requireLogin();
        
        if (!isAdmin()) {
            $this->jsonResponse(['success' => false, 'message' => 'Akses tidak diizinkan.']);
        }
        
        $leaveId = cleanInput($_POST['leave_id']);
        
        if (!$leaveId) {
            $this->jsonResponse(['success' => false, 'message' => 'ID cuti tidak valid.']);
        }
        
        $db = Database::getInstance();
        $leave = $this->leaveModel->find($leaveId);
        if (!$leave) {
            $this->jsonResponse(['success' => false, 'message' => 'Data cuti tidak ditemukan.']);
        }
        
        if ($leave['status'] !== 'approved') {
            $this->jsonResponse(['success' => false, 'message' => 'Status cuti harus approved untuk melanjutkan proses.']);
        }
        
        if (!empty($leave['admin_blankofinal_sender'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Proses sudah dilanjutkan sebelumnya.']);
        }
        
        // VALIDASI: Check apakah admin yang akan melanjutkan sudah upload paraf
        $adminParaf = $db->fetch(
            "SELECT * FROM user_signatures WHERE user_id = ? AND signature_type = 'paraf' AND is_active = 1",
            [$_SESSION['user_id']]
        );
        
        if (!$adminParaf) {
            $this->jsonResponse([
                'success' => false, 
                'message' => 'Anda belum upload paraf di halaman Manajemen Paraf (Signature > Paraf Petugas Cuti). Silakan upload paraf terlebih dahulu sebelum melanjutkan proses pengajuan cuti.'
            ]);
        }
        
        error_log("=== CONTINUE PROCESS START ===");
        error_log("Admin User ID: " . $_SESSION['user_id']);
        error_log("Admin Paraf File: " . $adminParaf['signature_file']);
        error_log("Leave ID: " . $leaveId);
        
        // Update admin_blankofinal_sender dengan user_id admin yang melanjutkan proses
        $updateSql = "UPDATE leave_requests SET admin_blankofinal_sender = ? WHERE id = ?";
        $result = $db->execute($updateSql, [$_SESSION['user_id'], $leaveId]);
        
        error_log("Database Update Result: " . ($result ? 'SUCCESS' : 'FAILED'));
        
        if (!$result) {
            $this->jsonResponse(['success' => false, 'message' => 'Gagal memperbarui data cuti.']);
        }
        
        // Refresh data leave dari database untuk mendapatkan admin_blankofinal_sender yang baru diupdate
        $leave = $this->leaveModel->find($leaveId);
        
        error_log("Leave ID: " . $leaveId);
        error_log("Leave status: " . ($leave['status'] ?? 'NULL'));
        error_log("Leave admin_blankofinal_sender (after update): " . ($leave['admin_blankofinal_sender'] ?? 'NULL'));
        error_log("Leave user_id: " . ($leave['user_id'] ?? 'NULL'));
        
        if (!$leave['admin_blankofinal_sender']) {
            error_log("ERROR: admin_blankofinal_sender masih NULL setelah update!");
            $this->jsonResponse(['success' => false, 'message' => 'Gagal set admin_blankofinal_sender di database.']);
        }
        
        // Generate ulang blanko cuti
        require_once dirname(__DIR__) . '/helpers/document_helper.php';
        require_once dirname(__DIR__) . '/models/User.php';
        
        $userModel = new User();
        $userData = $userModel->find($leave['user_id']);
        
        if (!$userData) {
            $this->jsonResponse(['success' => false, 'message' => 'Data user tidak ditemukan.']);
        }
        
        error_log("Calling generateLeaveDocument:");
        error_log("  - Leave Status: " . $leave['status']);
        error_log("  - Admin_blankofinal_sender: " . $leave['admin_blankofinal_sender']);
        error_log("  - Parameter isAfterApprove: true");
        error_log("  - Expect placeholder paraf akan diisi dengan paraf admin ID: " . $leave['admin_blankofinal_sender']);
        
        try {
            // Generate document dengan admin_blankofinal_sender yang sudah diisi
            // Parameter: ($leaveData, $userData, $templateFile, $includeAdminSignature, $isAfterApprove, $isAwaitingPimpinan)
            $generateResult = generateLeaveDocument($leave, $userData, 'blanko_cuti_template.docx', false, true, false);
            error_log("generateLeaveDocument execution: SUCCESS");
            error_log("Generate result: " . json_encode($generateResult));
            
            // Simpan hasil generate ke database
            if ($generateResult && isset($generateResult['success']) && $generateResult['success']) {
                $documentModel = $this->model('DocumentModel');
                $existingDoc = $documentModel->getLatestByLeaveId($leaveId, 'generated');
                
                if ($existingDoc) {
                    // Update dokumen yang sudah ada
                    $oldPath = dirname(dirname(__DIR__)) . '/public/uploads/documents/temp/' . $existingDoc['filename'];
                    if (file_exists($oldPath)) { 
                        @unlink($oldPath); 
                    }
                    $documentModel->update($existingDoc['id'], [
                        'filename' => $generateResult['filename'],
                        'status' => $leave['status'],
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    error_log("Document updated in database: " . $existingDoc['id']);
                } else {
                    // Buat dokumen baru
                    $documentModel->createDocument([
                        'leave_request_id' => $leaveId,
                        'filename' => $generateResult['filename'],
                        'document_type' => 'generated',
                        'status' => $leave['status'],
                        'created_by' => $_SESSION['user_id']
                    ]);
                    error_log("New document created in database");
                }
            } else {
                error_log("Generate result invalid or not successful");
            }
        } catch (Exception $e) {
            error_log("generateLeaveDocument execution: FAILED - " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Gagal generate ulang blanko cuti: ' . $e->getMessage()]);
        }
        
        error_log("=== CONTINUE PROCESS END ===");
        
        $this->jsonResponse(['success' => true, 'message' => 'Proses pengajuan cuti berhasil dilanjutkan.']);
    }

}
