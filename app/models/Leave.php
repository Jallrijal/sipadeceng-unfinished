<?php
class Leave extends Model {
    protected $table = 'leave_requests';
    
    /**
     * Create a new leave request and automatically populate kasubbag_id if atasan is a kasubbag
     * @param array $data
     * @return int Leave request ID
     */
    public function create($data) {
        // Call parent create method to insert the leave request
        $leaveId = parent::create($data);
        
        if (!$leaveId || empty($data['atasan_id'])) {
            return $leaveId;
        }
        
        // Auto-populate kasubbag_id based on atasan's role
        try {
            $atasanId = $data['atasan_id'];
            
            // Check if the atasan has role='kasubbag'
            $atasanRow = $this->db->fetch(
                "SELECT id_atasan, role FROM atasan WHERE id_atasan = ? LIMIT 1",
                [$atasanId]
            );
            
            if ($atasanRow && isset($atasanRow['role']) && $atasanRow['role'] === 'kasubbag') {
                // Atasan is a kasubbag, so set kasubbag_id = atasan_id
                // This triggers the dual-level approval workflow
                $this->db->query(
                    "UPDATE leave_requests SET kasubbag_id = ? WHERE id = ?",
                    [$atasanId, $leaveId]
                );
                error_log("[Leave::create] Auto-populated kasubbag_id=$atasanId for leave_id=$leaveId (atasan is kasubbag)");
            }
        } catch (Exception $e) {
            error_log("[Leave::create] Error setting kasubbag_id: " . $e->getMessage());
            // Don't fail the entire creation if kasubbag_id population fails
        }
        
        return $leaveId;
    }
    
    public function getHistory($filters = []) {
        // Build query with necessary joins (similar to view but with direct table access for better control)
        $sql = "SELECT lr.*, 
                lt.nama_cuti,
                CASE WHEN us.id is not null THEN us.nama 
                     WHEN u.id is not null THEN u.nama 
                     ELSE 'Unknown' END AS nama,
                CASE WHEN us.id is not null THEN us.unit_kerja 
                     WHEN u.id is not null THEN u.unit_kerja 
                     ELSE 'Unknown' END AS unit_kerja,
                CASE WHEN us.id is not null THEN us.nip 
                     WHEN u.id is not null THEN u.nip 
                     ELSE 'Unknown' END AS nip,
                CASE WHEN us.id is not null THEN us.jabatan 
                     WHEN u.id is not null THEN u.jabatan 
                     ELSE 'Unknown' END AS jabatan,
                CASE WHEN us.id is not null THEN us.golongan 
                     WHEN u.id is not null THEN u.golongan 
                     ELSE 'Unknown' END AS golongan,
                CASE WHEN us.id is not null THEN 'snapshot' 
                     WHEN u.id is not null THEN 'active' 
                     ELSE 'unknown' END AS user_status,
                au.nama AS approved_by_name,
                us.snapshot_type
                FROM leave_requests lr
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                LEFT JOIN users u ON lr.user_id = u.id AND u.is_deleted = 0 AND lr.user_snapshot_id IS NULL
                LEFT JOIN user_snapshots us ON lr.user_snapshot_id = us.id
                LEFT JOIN users au ON lr.approved_by = au.id AND au.is_deleted = 0";
        
        $conditions = [];
        $params = [];
        
        // Check viewer roles -- these flags influence how we build the WHERE clause
        $isKasubbagViewer = isset($filters['is_kasubbag_viewer']) && $filters['is_kasubbag_viewer'] === true;
        $isKabagViewer = isset($filters['is_kabag_viewer']) && $filters['is_kabag_viewer'] === true;
        $isSekretarisViewer = isset($filters['is_sekretaris_viewer']) && $filters['is_sekretaris_viewer'] === true;
        $isKetuaViewer = isset($filters['is_ketua_viewer']) && $filters['is_ketua_viewer'] === true;
        
        if (isset($filters['user_id'])) {
            $conditions[] = "lr.user_id = ?";
            $params[] = $filters['user_id'];
        }
        
        // Base atasan filtering: only apply automatically if the requester is not a
        // special viewer (kabag, sekretaris, or ketua). Those roles construct their
        // own compound conditions further down and must not be restricted by a
        // standalone `atasan_id` clause.
        if (isset($filters['atasan_id']) && isset($filters['kasubbag_id'])) {
            // Handle kasubbag viewers or regular atasan with kasubbag fallback
            if ($isKasubbagViewer) {
                // Kasubbag melihat semua pengajuan dari bawahan dan pending_kasubbag
                // Juga melihat pengajuan cuti mereka sendiri jika include_own_requests diset
                if (isset($filters['include_own_requests'])) {
                    $conditions[] = "((lr.atasan_id = ?) OR (lr.kasubbag_id = ? AND lr.status = 'pending_kasubbag') OR lr.user_id = ?)";
                    $params[] = $filters['atasan_id'];
                    $params[] = $filters['kasubbag_id'];
                    $params[] = $filters['include_own_requests'];
                } else {
                    $conditions[] = "((lr.atasan_id = ?) OR (lr.kasubbag_id = ? AND lr.status = 'pending_kasubbag'))";
                    $params[] = $filters['atasan_id'];
                    $params[] = $filters['kasubbag_id'];
                }
            } else {
                $conditions[] = "lr.atasan_id = ?";
                $params[] = $filters['atasan_id'];
            }
        } elseif (isset($filters['atasan_id']) && !($isKabagViewer || $isSekretarisViewer || $isKetuaViewer)) {
            // Regular atasan without any special viewing role
            // Juga melihat pengajuan cuti mereka sendiri jika include_own_requests diset
            if (isset($filters['include_own_requests'])) {
                $conditions[] = "((lr.atasan_id = ?) OR lr.user_id = ?)";
                $params[] = $filters['atasan_id'];
                $params[] = $filters['include_own_requests'];
            } else {
                $conditions[] = "lr.atasan_id = ?";
                $params[] = $filters['atasan_id'];
            }
        }
        
        // Handle Kabag viewer filter - dapat melihat semua pengajuan dari pegawai bawahannya dan pengajuan dengan status pending_kabag
        // Kabag dapat melihat:
        // 1. Semua status dari pengajuan pegawai yang mempunyai atasan_id = kabag_approver_id (pegawai bawahannya)
        // 2. Items dengan status pending_kabag di mana kabag_approver_id = kabag_approver_id (pengajuan tertangguhkan di level kabag)
        // 3. Pengajuan cuti mereka sendiri jika include_own_requests diset (karena mereka juga adalah atasan)
        if ($isKabagViewer && isset($filters['kabag_approver_id'])) {
            if (isset($filters['include_own_requests'])) {
                $conditions[] = "((lr.atasan_id = ?) OR (lr.kabag_approver_id = ? AND lr.status = 'pending_kabag') OR lr.user_id = ?)";
                $params[] = $filters['kabag_approver_id'];
                $params[] = $filters['kabag_approver_id'];
                $params[] = $filters['include_own_requests'];
            } else {
                $conditions[] = "((lr.atasan_id = ?) OR (lr.kabag_approver_id = ? AND lr.status = 'pending_kabag'))";
                $params[] = $filters['kabag_approver_id'];
                $params[] = $filters['kabag_approver_id'];
            }
        }
        
        // Handle Sekretaris viewer filter - dapat melihat semua pengajuan dari pegawai bawahannya dan pengajuan dengan status pending_sekretaris
        // Sekretaris dapat melihat:
        // 1. Semua status dari pengajuan pegawai yang mempunyai atasan_id = sekretaris_approver_id (pegawai bawahannya)
        // 2. Items dengan status pending_sekretaris di mana sekretaris_approver_id = sekretaris_approver_id (pengajuan tertangguhkan di level sekretaris)
        // 3. Pengajuan cuti mereka sendiri jika include_own_requests diset (karena mereka juga adalah atasan)
        if ($isSekretarisViewer && isset($filters['sekretaris_approver_id'])) {
            if (isset($filters['include_own_requests'])) {
                $conditions[] = "((lr.atasan_id = ?) OR (lr.sekretaris_approver_id = ? AND lr.status = 'pending_sekretaris') OR lr.user_id = ?)";
                $params[] = $filters['sekretaris_approver_id'];
                $params[] = $filters['sekretaris_approver_id'];
                $params[] = $filters['include_own_requests'];
            } else {
                $conditions[] = "((lr.atasan_id = ?) OR (lr.sekretaris_approver_id = ? AND lr.status = 'pending_sekretaris'))";
                $params[] = $filters['sekretaris_approver_id'];
                $params[] = $filters['sekretaris_approver_id'];
            }
        }

        // Handle Ketua (pimpinan) viewer filter - dapat melihat semua pengajuan dari pegawai bawahannya dan pengajuan dengan status awaiting_pimpinan
        // Ketua dapat melihat:
        // 1. Semua status dari pengajuan pegawai yang mempunyai atasan_id = ketua_approver_id (pegawai bawahannya)
        // 2. Items dengan status awaiting_pimpinan di mana ketua_approver_id = ketua_approver_id (pengajuan tertangguhkan di level ketua)
        // 3. Pengajuan cuti mereka sendiri jika include_own_requests diset (karena mereka juga adalah atasan)
        $isKetuaViewer = isset($filters['is_ketua_viewer']) && $filters['is_ketua_viewer'] === true;
        if ($isKetuaViewer && isset($filters['ketua_approver_id'])) {
            if (isset($filters['include_own_requests'])) {
                $conditions[] = "((lr.atasan_id = ?) OR (lr.ketua_approver_id = ? AND lr.status = 'awaiting_pimpinan') OR lr.user_id = ?)";
                $params[] = $filters['ketua_approver_id'];
                $params[] = $filters['ketua_approver_id'];
                $params[] = $filters['include_own_requests'];
            } else {
                $conditions[] = "((lr.atasan_id = ?) OR (lr.ketua_approver_id = ? AND lr.status = 'awaiting_pimpinan'))";
                $params[] = $filters['ketua_approver_id'];
                $params[] = $filters['ketua_approver_id'];
            }
        }
        
        if (isset($filters['status'])) {
            $conditions[] = "lr.status = ?";
            $params[] = $filters['status'];
        }
        
        // Exclude certain statuses (untuk pimpinan yang tidak ingin melihat draft dan pending)
        if (isset($filters['exclude_status']) && is_array($filters['exclude_status'])) {
            $excludeStatuses = $filters['exclude_status'];
            $placeholders = implode(',', array_fill(0, count($excludeStatuses), '?'));
            $conditions[] = "lr.status NOT IN ({$placeholders})";
            $params = array_merge($params, $excludeStatuses);
        }
        
        // Include certain statuses (untuk menampilkan status tambahan)
        if (isset($filters['include_status']) && is_array($filters['include_status'])) {
            $includeStatuses = $filters['include_status'];
            $placeholders = implode(',', array_fill(0, count($includeStatuses), '?'));
            $conditions[] = "lr.status IN ({$placeholders})";
            $params = array_merge($params, $includeStatuses);
        }
        
        if (isset($filters['tahun'])) {
            $conditions[] = "YEAR(lr.tanggal_mulai) = ?";
            $params[] = $filters['tahun'];
        }
        
        if (isset($filters['leave_type_id'])) {
            $conditions[] = "lr.leave_type_id = ?";
            $params[] = $filters['leave_type_id'];
        }
        
        if (isset($filters['unit_kerja'])) {
            $conditions[] = "CASE WHEN us.id is not null THEN us.unit_kerja WHEN u.id is not null THEN u.unit_kerja ELSE 0 END = ?";
            $params[] = $filters['unit_kerja'];
        }
        
        if (isset($filters['user_status'])) {
            $conditions[] = "CASE WHEN us.id is not null THEN 'snapshot' WHEN u.id is not null THEN 'active' ELSE 'unknown' END = ?";
            $params[] = $filters['user_status'];
        }
        
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY lr.created_at DESC";
        
        // Debug logging for kasubbag viewers to inspect generated SQL/params
        if ($isKasubbagViewer) {
            error_log("[DEBUG][Leave::getHistory] is_kasubbag_viewer SQL: " . $sql);
            error_log("[DEBUG][Leave::getHistory] params: " . json_encode($params));
        }

        $results = $this->db->fetchAll($sql, $params);

        if ($isKasubbagViewer) {
            error_log("[DEBUG][Leave::getHistory] returned rows: " . count($results));

            // Additional diagnostics: counts for various pending_kasubbag scenarios
            try {
                $kasubbagId = $filters['kasubbag_id'] ?? null;
                $countAssigned = $this->db->fetch("SELECT COUNT(*) as c FROM leave_requests WHERE status = 'pending_kasubbag' AND kasubbag_id = ?", [$kasubbagId]);
                $countUnassigned = $this->db->fetch("SELECT COUNT(*) as c FROM leave_requests WHERE status = 'pending_kasubbag' AND kasubbag_id IS NULL", []);
                $countAll = $this->db->fetch("SELECT COUNT(*) as c FROM leave_requests WHERE status = 'pending_kasubbag'", []);
                error_log("[DEBUG][Leave::getHistory] pending_kasubbag counts - assignedToThis: " . ($countAssigned['c'] ?? 0) . ", unassigned: " . ($countUnassigned['c'] ?? 0) . ", total: " . ($countAll['c'] ?? 0));

                // Log up to 10 sample IDs per category to inspect mismatches
                $sampleAssigned = $this->db->fetchAll("SELECT id FROM leave_requests WHERE status = 'pending_kasubbag' AND kasubbag_id = ? LIMIT 10", [$kasubbagId]);
                $sampleUnassigned = $this->db->fetchAll("SELECT id FROM leave_requests WHERE status = 'pending_kasubbag' AND kasubbag_id IS NULL LIMIT 10", []);
                $idsAssigned = array_map(function($r){ return $r['id']; }, $sampleAssigned);
                $idsUnassigned = array_map(function($r){ return $r['id']; }, $sampleUnassigned);
                error_log("[DEBUG][Leave::getHistory] sample assigned IDs: " . json_encode($idsAssigned));
                error_log("[DEBUG][Leave::getHistory] sample unassigned IDs: " . json_encode($idsUnassigned));
            } catch (Exception $e) {
                error_log("[DEBUG][Leave::getHistory] diagnostic query failed: " . $e->getMessage());
            }
        }
        
        // Ambil semua mapping id_satker => nama_satker
        require_once __DIR__ . '/../models/Satker.php';
        $satkerModel = new Satker();
        $satkerList = $satkerModel->getAllSatker();
        $satkerMap = [];
        foreach ($satkerList as $satker) {
            $satkerMap[$satker['id_satker']] = $satker['nama_satker'];
        }

        // Format data dan mapping nama satker
        foreach ($results as &$row) {
            $row['status_badge'] = getStatusBadge($row['status']);
            $row['tanggal_mulai_formatted'] = formatTanggal($row['tanggal_mulai']);
            $row['tanggal_selesai_formatted'] = formatTanggal($row['tanggal_selesai']);
            $row['created_at_formatted'] = formatTanggal($row['created_at']);
            $row['user_status_badge'] = $this->getUserStatusBadge($row['user_status'], $row['snapshot_type'] ?? null);
            // Tambahan: cek dokumen final (admin_signed) dan dokumen generated (blanko yang di-generate sistem)
            $documentModel = new DocumentModel();
            $finalDoc = $documentModel->getLatestByLeaveId($row['id'], 'admin_signed');
            $row['has_final_doc'] = !empty($finalDoc);
            $generatedDoc = $documentModel->getLatestByLeaveId($row['id'], 'generated');
            $row['generated_doc_filename'] = $generatedDoc['filename'] ?? null;
            $row['has_generated_doc'] = false;
            if (!empty($row['generated_doc_filename'])) {
                $baseDir = dirname(dirname(__DIR__)) . '/public/uploads/documents/';
                $candidates = [
                    $baseDir . 'temp/' . $row['generated_doc_filename'],
                    $baseDir . 'generated/' . $row['generated_doc_filename'],
                    $baseDir . $row['generated_doc_filename']
                ];
                foreach ($candidates as $p) {
                    if (file_exists($p)) {
                        $row['has_generated_doc'] = true;
                        break;
                    }
                }
            }
            // in addition, compute permission flag if helper exists
            if (function_exists('canDownloadGeneratedDocRow')) {
                $row['can_download_generated'] = canDownloadGeneratedDocRow($row);
            } else {
                $row['can_download_generated'] = false;
            }
            // Mapping nama satker
            $row['nama_satker'] = isset($satkerMap[$row['unit_kerja']]) ? $satkerMap[$row['unit_kerja']] : $row['unit_kerja'];

            // Indicate routing source for sekretaris view
            $row['forwarded_from'] = null;
            if ($row['status'] === 'pending_sekretaris' && !empty($row['kabag_approval_date'])) {
                $row['forwarded_from'] = 'kabag';
            }
        }
        
        return $results;
    }

    /**
     * Ambil events untuk kalender (rentang tanggal) — mengembalikan rows ringkas yang dibutuhkan oleh UI
     * @param string|null $start YYYY-MM-DD
     * @param string|null $end YYYY-MM-DD
     * @param array $filters role-based filters (user_id, atasan_id, exclude_status, unit_kerja)
     */
    public function getEvents($start = null, $end = null, $filters = []) {
        $sql = "SELECT lr.id, lr.tanggal_mulai, lr.tanggal_selesai, lr.jumlah_hari, lr.status, lt.nama_cuti,
                CASE WHEN us.id is not null THEN us.nama WHEN u.id is not null THEN u.nama ELSE 'Unknown' END AS nama,
                CASE WHEN us.id is not null THEN us.unit_kerja WHEN u.id is not null THEN u.unit_kerja ELSE 'Unknown' END AS unit_kerja
                FROM leave_requests lr
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                LEFT JOIN users u ON lr.user_id = u.id AND u.is_deleted = 0 AND lr.user_snapshot_id IS NULL
                LEFT JOIN user_snapshots us ON lr.user_snapshot_id = us.id";

        $conditions = [];
        $params = [];

        if ($start && $end) {
            // overlap condition: start <= endDate AND end >= startDate
            $conditions[] = "(lr.tanggal_mulai <= ? AND lr.tanggal_selesai >= ?)";
            $params[] = $end;
            $params[] = $start;
        } else if (isset($filters['tahun'])) {
            $conditions[] = "YEAR(lr.tanggal_mulai) = ?";
            $params[] = $filters['tahun'];
        }

        if (isset($filters['user_id'])) {
            $conditions[] = "lr.user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (isset($filters['atasan_id'])) {
            $conditions[] = "lr.atasan_id = ?";
            $params[] = $filters['atasan_id'];
        }

        if (isset($filters['exclude_status']) && is_array($filters['exclude_status'])) {
            $placeholders = implode(',', array_fill(0, count($filters['exclude_status']), '?'));
            $conditions[] = "lr.status NOT IN ({$placeholders})";
            $params = array_merge($params, $filters['exclude_status']);
        }

        if (isset($filters['unit_kerja'])) {
            $conditions[] = "CASE WHEN us.id is not null THEN us.unit_kerja WHEN u.id is not null THEN u.unit_kerja ELSE 0 END = ?";
            $params[] = $filters['unit_kerja'];
        }

        // additional filters for calendar view
        if (isset($filters['status'])) {
            $conditions[] = "lr.status = ?";
            $params[] = $filters['status'];
        }
        if (isset($filters['is_completed'])) {
            $conditions[] = "lr.is_completed = ?";
            $params[] = $filters['is_completed'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY lr.tanggal_mulai ASC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Mendapatkan badge untuk status user
     */
    private function getUserStatusBadge($userStatus, $snapshotType = null) {
        switch ($userStatus) {
            case 'active':
                return '<span class="badge bg-success">Aktif</span>';
            case 'snapshot':
                if ($snapshotType === 'deleted') {
                    return '<span class="badge bg-danger">Dihapus</span>';
                } else {
                    return '<span class="badge bg-warning">Diubah</span>';
                }
            default:
                return '<span class="badge bg-secondary">Tidak Diketahui</span>';
        }
    }
    
    public function getPending() {
        return $this->getHistory(['status' => 'pending']);
    }
    
    public function getPendingForAdmin() {
        // Hanya ambil pengajuan yang sudah diupload blanko user
        $sql = "SELECT * FROM v_leave_history_complete 
                WHERE status = 'pending' AND blanko_uploaded = 1
                ORDER BY blanko_upload_date ASC";
        
        $results = $this->db->fetchAll($sql);
        
        // Format data
        foreach ($results as &$row) {
            $row['status_badge'] = getStatusBadge($row['status']);
            $row['tanggal_mulai_formatted'] = formatTanggal($row['tanggal_mulai']);
            $row['tanggal_selesai_formatted'] = formatTanggal($row['tanggal_selesai']);
            $row['created_at_formatted'] = formatTanggal($row['created_at']);
            $row['blanko_upload_date_formatted'] = formatTanggal($row['blanko_upload_date']);
            $row['user_status_badge'] = $this->getUserStatusBadge($row['user_status'], $row['snapshot_type'] ?? null);
        }
        
        return $results;
    }

    /**
     * Approve by an atasan (ketua) instead of admin template approver
     * Stores atasan_approver_id or ketua_approver_id depending on schema
     */
    public function approveByAtasan($id, $atasanId, $catatan = '') {
        $data = [
            'status' => 'approved',
            'approval_date' => date('Y-m-d H:i:s'),
            'catatan_approval' => $catatan
        ];
        // Prefer ketua_approver_id if exists in table, else use atasan_approver_id
        $columns = $this->db->fetchAll("SHOW COLUMNS FROM {$this->table}");
        $hasKetuaCol = false;
        foreach ($columns as $col) {
            if ($col['Field'] === 'ketua_approver_id') { $hasKetuaCol = true; break; }
        }
        if ($hasKetuaCol) {
            $data['ketua_approver_id'] = $atasanId;
        } else {
            $data['atasan_approver_id'] = $atasanId;
        }

        return $this->update($id, $data);
    }
    
    public function reject($id, $adminApproverId, $catatan) {
        $data = [
            'status' => 'rejected',
            'approved_by' => $adminApproverId,
            'approval_date' => date('Y-m-d H:i:s'),
            'catatan_approval' => $catatan
        ];
        
        return $this->update($id, $data);
    }

    public function rejectByAtasan($id, $atasanId, $catatan) {
        $data = [
            'status' => 'rejected',
            'approval_date' => date('Y-m-d H:i:s'),
            'catatan_approval' => $catatan
        ];
        $columns = $this->db->fetchAll("SHOW COLUMNS FROM {$this->table}");
        $hasKetuaCol = false;
        foreach ($columns as $col) {
            if ($col['Field'] === 'ketua_approver_id') { $hasKetuaCol = true; break; }
        }
        if ($hasKetuaCol) {
            $data['ketua_approver_id'] = $atasanId;
        } else {
            $data['atasan_approver_id'] = $atasanId;
        }
        return $this->update($id, $data);
    }
    
    public function change($id, $adminApproverId, $catatan) {
        $data = [
            'status' => 'changed',
            'approved_by' => $adminApproverId,
            'approval_date' => date('Y-m-d H:i:s'),
            'catatan_approval' => $catatan
        ];
        return $this->update($id, $data);
    }

    public function changeByAtasan($id, $atasanId, $catatan) {
        $data = [
            'status' => 'changed',
            'approval_date' => date('Y-m-d H:i:s'),
            'catatan_approval' => $catatan
        ];
        $columns = $this->db->fetchAll("SHOW COLUMNS FROM {$this->table}");
        $hasKetuaCol = false;
        foreach ($columns as $col) {
            if ($col['Field'] === 'ketua_approver_id') { $hasKetuaCol = true; break; }
        }
        if ($hasKetuaCol) {
            $data['ketua_approver_id'] = $atasanId;
        } else {
            $data['atasan_approver_id'] = $atasanId;
        }
        return $this->update($id, $data);
    }

    /**
     * Menangguhkan cuti (postponed) dan menyimpan jumlah hari yang ditangguhkan
     * @param int $id ID pengajuan cuti
     * @param int $adminApproverId ID admin yang menangguhkan
     * @param string $catatan Catatan penangguhan
     * @param int $jumlahHariDitangguhkan Jumlah hari yang ditangguhkan
     * @return bool
     */
    public function postpone($id, $adminApproverId, $catatan, $jumlahHariDitangguhkan = 0) {
        $data = [
            'status' => 'postponed',
            'approved_by' => $adminApproverId,
            'approval_date' => date('Y-m-d H:i:s'),
            'catatan_approval' => $catatan,
            'jumlah_hari_ditangguhkan' => $jumlahHariDitangguhkan // Jumlah hari cuti yang ditangguhkan
        ];
        return $this->update($id, $data);
    }

    public function postponeByAtasan($id, $atasanId, $catatan, $jumlahHariDitangguhkan = 0) {
        $data = [
            'status' => 'postponed',
            'approval_date' => date('Y-m-d H:i:s'),
            'catatan_approval' => $catatan,
            'jumlah_hari_ditangguhkan' => $jumlahHariDitangguhkan
        ];
        $columns = $this->db->fetchAll("SHOW COLUMNS FROM {$this->table}");
        $hasKetuaCol = false;
        foreach ($columns as $col) {
            if ($col['Field'] === 'ketua_approver_id') { $hasKetuaCol = true; break; }
        }
        if ($hasKetuaCol) {
            $data['ketua_approver_id'] = $atasanId;
        } else {
            $data['atasan_approver_id'] = $atasanId;
        }
        return $this->update($id, $data);
    }
    
    public function markBlankoUploaded($id) {
        $data = [
            'blanko_uploaded' => 1,
            'blanko_upload_date' => date('Y-m-d H:i:s'),
            'status' => 'pending'
        ];
        
        return $this->update($id, $data);
    }
    
    public function markFinalBlankoSent($id) {
        $data = [
            'final_blanko_sent' => 1,
            'final_blanko_sent_date' => date('Y-m-d H:i:s'),
            'is_completed' => 1
        ];
        return $this->update($id, $data);
    }
    
    public function markQuotaDeducted($id) {
        $data = [
            'quota_deducted' => 1
        ];
        
        return $this->update($id, $data);
    }
    
    public function getRecentActivities($limit = 10, $atasanId = null) {
        // Jika diberikan atasanId, gunakan getHistory dengan filter atasan_id lalu ambil $limit pertama
        if ($atasanId !== null) {
            $all = $this->getHistory(['atasan_id' => $atasanId]);
            $results = array_slice($all, 0, $limit);
        } else {
            $sql = "SELECT id, nama_cuti, nama, unit_kerja, status, created_at, user_status, snapshot_type, tanggal_mulai, tanggal_selesai, jumlah_hari
                    FROM v_leave_history_complete
                    ORDER BY created_at DESC
                    LIMIT ?";
            $results = $this->db->fetchAll($sql, [$limit]);
        }

        // Map unit_kerja to nama_satker
        require_once __DIR__ . '/Satker.php';
        $satkerModel = new Satker();
        $satkerList = $satkerModel->getAllSatker();
        $satkerMap = [];
        foreach ($satkerList as $satker) {
            $satkerMap[$satker['id_satker']] = $satker['nama_satker'];
        }

        foreach ($results as &$row) {
            $row['nama_satker'] = isset($satkerMap[$row['unit_kerja']]) ? $satkerMap[$row['unit_kerja']] : $row['unit_kerja'];
            // Fallback jika tanggal/jumlah_hari null
            $row['tanggal_mulai'] = $row['tanggal_mulai'] ?? '-';
            $row['tanggal_selesai'] = $row['tanggal_selesai'] ?? '-';
            $row['jumlah_hari'] = $row['jumlah_hari'] ?? '-';
        }

        return $results;
    }
    
    /**
     * Check whether a leave request has a generated document file on disk.
     *
     * @param int $leaveId
     * @return bool
     */
    public function hasGeneratedDoc($leaveId) {
        require_once __DIR__ . '/DocumentModel.php';
        $documentModel = new DocumentModel();
        $generated = $documentModel->getLatestByLeaveId($leaveId, 'generated');
        if (empty($generated) || empty($generated['filename'])) {
            return false;
        }
        $baseDir = dirname(dirname(__DIR__)) . '/public/uploads/documents/';
        $candidates = [
            $baseDir . 'temp/' . $generated['filename'],
            $baseDir . 'generated/' . $generated['filename'],
            $baseDir . $generated['filename']
        ];
        foreach ($candidates as $p) {
            if (file_exists($p)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve the latest generated document record for a leave request.
     *
     * @param int $leaveId
     * @return array|null
     */
    public function getLatestGeneratedDoc($leaveId) {
        require_once __DIR__ . '/DocumentModel.php';
        $documentModel = new DocumentModel();
        return $documentModel->getLatestByLeaveId($leaveId, 'generated');
    }

    public function getPendingCount() {
        $sql = "SELECT COUNT(*) as count FROM leave_requests 
                WHERE status = 'pending' AND blanko_uploaded = 1";
        $result = $this->db->fetch($sql);
        return $result['count'];
    }
    
    public function getDraftCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM leave_requests 
                WHERE user_id = ? AND status = 'draft'";
        $result = $this->db->fetch($sql, [$userId]);
        return $result['count'];
    }

    /**
     * Cari leave request berdasarkan nama file dokumen pendukung
     */
    public function findByDokumenPendukung($filename) {
        $sql = "SELECT * FROM leave_requests WHERE dokumen_pendukung = ? LIMIT 1";
        return $this->db->fetch($sql, [$filename]);
    }
}