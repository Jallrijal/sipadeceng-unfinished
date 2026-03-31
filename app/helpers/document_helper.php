<?php
use PhpOffice\PhpWord\TemplateProcessor;

require_once dirname(__DIR__) . '/helpers/kota_helper.php';
function generateLeaveDocument($leaveData, $userData, $templateFile = 'blanko_cuti_template.docx', $includeAdminSignature = false, $isAfterApprove = false, $isAwaitingPimpinan = false) {
    try {
        // Path ke template
        $templatePath = dirname(dirname(__DIR__)) . '/templates/' . $templateFile;
        
        // Debug: cek path
        error_log("Template path: " . $templatePath);
        
        if (!file_exists($templatePath)) {
            throw new Exception("Template file tidak ditemukan: " . $templatePath);
        }
        
        // Cek apakah class TemplateProcessor ada
        if (!class_exists('PhpOffice\PhpWord\TemplateProcessor')) {
            throw new Exception("PHPWord TemplateProcessor class tidak ditemukan. Pastikan composer autoload sudah di-load.");
        }
        
        // Load template
        $templateProcessor = new TemplateProcessor($templatePath);
        
        // Data pegawai
        $templateProcessor->setValue('nama', $userData['nama']);
        $templateProcessor->setValue('nip', $userData['nip']);
        $templateProcessor->setValue('jabatan', $userData['jabatan']);
        $templateProcessor->setValue('golongan', $userData['golongan']);
        // Ambil nama satker dari id_satker jika yang disimpan adalah id
        $unitKerja = isset($userData['unit_kerja']) ? $userData['unit_kerja'] : null;
        if (is_numeric($unitKerja)) {
            $namaSatker = getNamaSatkerById($unitKerja);
        } else {
            $namaSatker = $unitKerja;
        }
        $templateProcessor->setValue('unit_kerja', $namaSatker);
        $templateProcessor->setValue('masa_kerja', hitungMasaKerja($userData['tanggal_masuk']));
        
        // Tanggal surat dengan nama kota (hari ini)
        $bulanIndonesia = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
    // Extract nama kota dari nama satker
    $namaKota = getKotaFromNamaSatker($namaSatker);
    $tanggalSurat = $namaKota . ", " . date("d") . " " . $bulanIndonesia[date("n")] . " " . date("Y");
    $templateProcessor->setValue('tanggal_surat', $tanggalSurat);
        
        // Jenis cuti (checkbox)
        $jenisMap = [
            1 => 'tahunan', 2 => 'besar', 3 => 'sakit', 
            4 => 'melahirkan', 5 => 'alasan_penting', 6 => 'luar_tanggungan'
        ];
        
        foreach ($jenisMap as $id => $key) {
            $checkValue = ($leaveData['leave_type_id'] == $id) ? '☑' : '☐';
            $templateProcessor->setValue('check_' . $key, $checkValue);
        }
        
        // Data cuti
        $templateProcessor->setValue('alasan_cuti', $leaveData['alasan']);
        $templateProcessor->setValue('jumlah_hari', $leaveData['jumlah_hari']);
        $templateProcessor->setValue('tanggal_mulai', formatTanggal($leaveData['tanggal_mulai']));
        $templateProcessor->setValue('tanggal_selesai', formatTanggal($leaveData['tanggal_selesai']));
        $templateProcessor->setValue('alamat_cuti', $leaveData['alamat_cuti'] ?: '-');
        $templateProcessor->setValue('telepon_cuti', $leaveData['telepon_cuti'] ?: '-');
        
        // Debug: log data catatan_cuti
        error_log("Catatan cuti data: " . ($leaveData['catatan_cuti'] ?? 'NULL'));
        $templateProcessor->setValue('catatan_cuti', $leaveData['catatan_cuti'] ?? '-');
        
        // Catatan cuti untuk 3 tahun terakhir
        $tahunSekarang = date('Y');
        $db = Database::getInstance();

        // Hitung total cuti yang akan diambil berdasarkan tahun cuti
        $tahunCuti = date('Y', strtotime($leaveData['tanggal_mulai']));
        $jumlahHariCuti = $leaveData['jumlah_hari'];

        for ($i = 0; $i < 3; $i++) {
            $tahun = $tahunSekarang - $i;
            $balance = $db->fetch(
                "SELECT * FROM leave_balances WHERE user_id = ? AND tahun = ?",
                [$userData['id'], $tahun]
            );
            
            $templateProcessor->setValue('tahun_' . ($i + 1), $tahun);
            $sisaKuota = $balance ? $balance['sisa_kuota'] : '12';
            $templateProcessor->setValue('sisa_' . ($i + 1), $sisaKuota);
            
            // Generate keterangan berdasarkan apakah tahun ini akan dipotong
            $keterangan = '';
            
            // Hanya untuk cuti tahunan (leave_type_id = 1)
            if ($leaveData['leave_type_id'] == 1) {
                // Cek apakah tahun ini akan dipotong berdasarkan tahun cuti
                if ($tahun <= $tahunCuti) {
                    // Simulasi pemotongan kuota berdasarkan prioritas
                    $sisaKuotaAwal = $sisaKuota;
                    $cutiDipotong = 0;
                    
                    // Hitung berapa yang akan dipotong dari tahun ini
                    if ($i == 2 && $tahunCuti >= $tahun) { // 2 tahun lalu
                        $cutiDipotong = min($jumlahHariCuti, $sisaKuotaAwal);
                    } elseif ($i == 1 && $tahunCuti >= $tahun) { // 1 tahun lalu
                        // Cek sisa dari 2 tahun lalu
                        $balance2TahunLalu = $db->fetch(
                            "SELECT * FROM leave_balances WHERE user_id = ? AND tahun = ?",
                            [$userData['id'], $tahunSekarang - 2]
                        );
                        $sisa2TahunLalu = $balance2TahunLalu ? $balance2TahunLalu['sisa_kuota'] : 0;
                        
                        if ($jumlahHariCuti > $sisa2TahunLalu) {
                            $cutiDipotong = min($jumlahHariCuti - $sisa2TahunLalu, $sisaKuotaAwal);
                        }
                    } elseif ($i == 0 && $tahunCuti >= $tahun) { // Tahun ini
                        // Cek sisa dari 2 tahun sebelumnya
                        $totalSisaSebelumnya = 0;
                        for ($j = 1; $j <= 2; $j++) {
                            $balanceSebelumnya = $db->fetch(
                                "SELECT * FROM leave_balances WHERE user_id = ? AND tahun = ?",
                                [$userData['id'], $tahunSekarang - $j]
                            );
                            $totalSisaSebelumnya += $balanceSebelumnya ? $balanceSebelumnya['sisa_kuota'] : 0;
                        }
                        
                        if ($jumlahHariCuti > $totalSisaSebelumnya) {
                            $cutiDipotong = min($jumlahHariCuti - $totalSisaSebelumnya, $sisaKuotaAwal);
                        }
                    }
                    
                    if ($cutiDipotong > 0) {
                        $sisaSetelahDipotong = $sisaKuotaAwal - $cutiDipotong;
                        $keterangan = "Diambil {$cutiDipotong}, sisa {$sisaSetelahDipotong}";
                    }
                    // Jika tidak ada pemotongan, biarkan kosong
                }
            }
            // Untuk jenis cuti selain cuti tahunan, biarkan kosong
            
            $templateProcessor->setValue('keterangan_' . ($i + 1), $keterangan);
        }

        // === Integrasi Tanda Tangan Dinamis ===
        require_once dirname(__DIR__) . '/helpers/signature_helper.php';
        // Tanda tangan user (pemohon)
        $ttdUser = getUserSignature($userData['id'], 'user');
        if ($ttdUser && file_exists(getSignatureFilePath($ttdUser['signature_file']))) {
            // User memiliki image tanda tangan: tampilkan image + data nama dan NIP
            $templateProcessor->setImageValue('ttd_user', [
                'path' => getSignatureFilePath($ttdUser['signature_file']),
                'width' => 120,
                'height' => 60,
                'ratio' => true
            ]);
            // Isi dengan data pemohon
            $templateProcessor->setValue('nama_pemohon', $userData['nama']);
            $templateProcessor->setValue('nip_pemohon', 'NIP. ' . $userData['nip']);
        } else {
            // User tidak memiliki image tanda tangan: tampilkan data nama dan NIP
            $templateProcessor->setValue('ttd_user', '');
            $templateProcessor->setValue('nama_pemohon', $userData['nama']);
            // Tambahkan "NIP. " sebelum angka NIP
            $templateProcessor->setValue('nip_pemohon', 'NIP. ' . $userData['nip']);
        }
        
        // Paraf petugas cuti (dari admin, bukan user)
        // Syarat: status cuti adalah approved/rejected/changed/postponed AND admin_blankofinal_sender tidak NULL
        $parafAdminId = null;
        $statusRequiresParaf = in_array($leaveData['status'], ['approved', 'rejected', 'changed', 'postponed']);
        $hasAdminSender = isset($leaveData['admin_blankofinal_sender']) && !empty($leaveData['admin_blankofinal_sender']);
        
        // Debug: log status paraf
        error_log("=== PLACEHOLDER PARAF START ===");
        error_log("Leave Status: {$leaveData['status']}");
        error_log("StatusRequiresParaf: " . ($statusRequiresParaf ? 'TRUE' : 'FALSE'));
        error_log("AdminBlankofinalSender: " . ($leaveData['admin_blankofinal_sender'] ?? 'NULL'));
        error_log("HasAdminSender: " . ($hasAdminSender ? 'TRUE' : 'FALSE'));
        
        if ($statusRequiresParaf && $hasAdminSender) {
            // Ambil paraf admin berdasarkan admin_blankofinal_sender
            $parafAdminId = intval($leaveData['admin_blankofinal_sender']);
            error_log("ParafAdminId: $parafAdminId");
            
            // Coba ambil data paraf dari user_signatures directly
            $parafAdmin = $db->fetch(
                "SELECT * FROM user_signatures WHERE user_id = ? AND signature_type = 'paraf' AND is_active = 1",
                [$parafAdminId]
            );
            
            error_log("Direct DB Query Result: " . ($parafAdmin ? 'FOUND' : 'NOT FOUND'));
            if ($parafAdmin) {
                error_log("Paraf Signature File: {$parafAdmin['signature_file']}");
                error_log("Paraf User ID: {$parafAdmin['user_id']}");
                error_log("Paraf Signature Type: {$parafAdmin['signature_type']}");
                error_log("Paraf Is Active: {$parafAdmin['is_active']}");
            }
            
            // Jika tidak ditemukan, coba dengan getUserSignature helper
            if (!$parafAdmin) {
                error_log("Trying getUserSignature helper...");
                $parafAdmin = getUserSignature($parafAdminId, 'paraf');
            }
            
            if ($parafAdmin) {
                $filePath = getSignatureFilePath($parafAdmin['signature_file']);
                error_log("Paraf File Path: $filePath");
                error_log("File Exists: " . (file_exists($filePath) ? 'YES' : 'NO'));
                
                if (file_exists($filePath)) {
                    $templateProcessor->setImageValue('paraf', [
                        'path' => $filePath,
                        'width' => 120,
                        'height' => 60,
                        'ratio' => true
                    ]);
                    error_log("✓ Placeholder ${paraf} successfully set with admin paraf image");
                } else {
                    error_log("✗ File tidak ditemukan di: $filePath");
                    $templateProcessor->setValue('paraf', '');
                }
            } else {
                // Admin belum upload paraf
                error_log("✗ Paraf admin not found in database (user_id=$parafAdminId)");
                error_log("  Admin perlu upload paraf di halaman paraf_manage.php terlebih dahulu");
                $templateProcessor->setValue('paraf', '');
            }
        } else {
            // Syarat tidak terpenuhi
            if (!$statusRequiresParaf) {
                error_log("✗ Status '{$leaveData['status']}' tidak memerlukan placeholder paraf");
            } else {
                error_log("✗ admin_blankofinal_sender belum di-set");
            }
            $templateProcessor->setValue('paraf', '');
        }
        error_log("=== PLACEHOLDER PARAF END ===");
        
        // === Kolom VII. Pertimbangan Atasan Langsung ===
        require_once dirname(__DIR__) . '/models/Atasan.php';
        $atasanModel = new Atasan();

        // Supervisor information should always come from the original atasan_id stored
        // on the leave request. using approver-related fields caused kasubbag/kabag
        // to overwrite the placeholders when they generated a new blanko.
        $atasanId = $leaveData['atasan_id'] ?? null;
        $isAtasanSatu = ($atasanId == 1);
        $atasanData = null;
        if ($atasanId) {
            $atasanData = $atasanModel->getAtasanById($atasanId);
        }

        $isFinalStatus = in_array($leaveData['status'], ['approved', 'rejected', 'changed', 'postponed']);
        $shouldUseAdminForAtasan = ($isAtasanSatu && $isFinalStatus);

        // Populate name/NIP/jabatan from the original supervisor (or fallbacks).
        if (!$shouldUseAdminForAtasan) {
            if ($atasanData) {
                $templateProcessor->setValue('nama_atasan', $atasanData['nama_atasan']);
                $templateProcessor->setValue('nip_atasan', 'NIP. ' . $atasanData['NIP']);
                $templateProcessor->setValue('jabatan_atasan', $atasanData['jabatan']);
            } else {
                $templateProcessor->setValue('nama_atasan', '________________________');
                $templateProcessor->setValue('nip_atasan', '________________________');
                $templateProcessor->setValue('jabatan_atasan', '________________________');
            }
        }

        $statusRequiresAtasanSignature = in_array($leaveData['status'], [
            'pending_kasubbag',
            'pending_kabag',
            'pending_sekretaris',
            'awaiting_pimpinan',
            'approved',
            'rejected',
            'changed',
            'postponed'
        ]);

        error_log("=== Kolom VII START ===");
        error_log("atasanId=" . ($atasanId ?? 'null') . ", isAtasanSatu=" . ($isAtasanSatu ? 'true' : 'false'));
        error_log("Status: " . $leaveData['status'] . ", statusRequiresAtasanSignature=" . ($statusRequiresAtasanSignature ? 'true' : 'false'));

        $shouldShowAtasanSignature = $statusRequiresAtasanSignature;

        if (!$shouldUseAdminForAtasan) {
            if ($atasanData && $shouldShowAtasanSignature) {
                // attempt to find corresponding user account for signature lookup
                $atasanUserId = null;
                $row = $db->fetch(
                    "SELECT id FROM users WHERE nip = ? AND user_type = 'atasan' AND is_deleted = 0 LIMIT 1",
                    [$atasanData['NIP']]
                );
                if ($row) {
                    $atasanUserId = $row['id'];
                } else {
                    $row = $db->fetch(
                        "SELECT id FROM users WHERE nama = ? AND user_type = 'atasan' AND is_deleted = 0 LIMIT 1",
                        [$atasanData['nama_atasan']]
                    );
                    if ($row) {
                        $atasanUserId = $row['id'];
                    }
                }

                if ($atasanUserId) {
                    $atasanSignature = getUserSignature($atasanUserId, 'user');
                    if ($atasanSignature && file_exists(getSignatureFilePath($atasanSignature['signature_file']))) {
                        $templateProcessor->setImageValue('ttd_atasan', [
                            'path' => getSignatureFilePath($atasanSignature['signature_file']),
                            'width' => 180,
                            'height' => 90,
                            'ratio' => true
                        ]);
                    } else {
                        $templateProcessor->setValue('ttd_atasan', '');
                    }
                } else {
                    $templateProcessor->setValue('ttd_atasan', '');
                }
            } else {
                $templateProcessor->setValue('ttd_atasan', '');
            }
        }

        error_log("=== Kolom VII END ===");

        // === Placeholder P1: Paraf Kasubbag ===
        // Placeholder ${p1} diisi dengan image paraf kasubbag ketika draft sudah disetujui oleh kasubbag
        // Sebelum kasubbag approval, placeholder dikosongkan
        error_log("=== Placeholder P1 START ===");
        
        // Cek apakah status sudah passed kasubbag approval
        $isAfterKasubbagApproval = in_array($leaveData['status'], [
            'pending_kabag', 'pending_sekretaris', 'awaiting_pimpinan', 
            'approved', 'rejected', 'changed', 'postponed'
        ]);
        
        error_log("Status: " . $leaveData['status'] . ", isAfterKasubbagApproval=" . ($isAfterKasubbagApproval ? 'true' : 'false'));
        
        if ($isAfterKasubbagApproval && isset($leaveData['kasubbag_id']) && !empty($leaveData['kasubbag_id'])) {
            error_log("Kasubbag ID: " . $leaveData['kasubbag_id']);
            
            // Cari kasubbag user berdasarkan kasubbag_id dari atasan table
            $kasubbagAtasan = $db->fetch(
                "SELECT a.id_atasan, a.NIP, a.nama_atasan FROM atasan a WHERE a.id_atasan = ?",
                [$leaveData['kasubbag_id']]
            );
            
            if ($kasubbagAtasan) {
                error_log("Kasubbag Atasan found: " . $kasubbagAtasan['nama_atasan']);
                
                // Cari kasubbag user_id dari users table berdasarkan NIP
                $kasubbagUser = $db->fetch(
                    "SELECT u.id FROM users u WHERE u.nip = ? AND u.user_type = 'atasan' AND u.is_deleted = 0 LIMIT 1",
                    [$kasubbagAtasan['NIP']]
                );
                
                if ($kasubbagUser) {
                    error_log("Kasubbag User ID: " . $kasubbagUser['id']);
                    
                    // Cari paraf kasubbag dari user_signatures table
                    $kasubbagParaf = $db->fetch(
                        "SELECT signature_file FROM user_signatures WHERE user_id = ? AND signature_type = 'paraf_kasubbag' AND is_active = 1",
                        [$kasubbagUser['id']]
                    );
                    
                    if ($kasubbagParaf && !empty($kasubbagParaf['signature_file'])) {
                        $kasubbagParafPath = getSignatureFilePath($kasubbagParaf['signature_file']);
                        error_log("Kasubbag Paraf Path: " . $kasubbagParafPath . ", exists=" . (file_exists($kasubbagParafPath) ? 'true' : 'false'));
                        
                        if (file_exists($kasubbagParafPath)) {
                            // Gunakan ukuran kecil sesuai ukuran placeholder yang kecil
                            $templateProcessor->setImageValue('p1', [
                                'path' => $kasubbagParafPath,
                                'width' => 40,
                                'height' => 20,
                                'ratio' => true
                            ]);
                            error_log("Placeholder p1 diisi dengan image paraf kasubbag");
                        } else {
                            $templateProcessor->setValue('p1', ' ');
                            error_log("File paraf kasubbag tidak ditemukan, placeholder dikosongkan");
                        }
                    } else {
                        $templateProcessor->setValue('p1', ' ');
                        error_log("Paraf kasubbag tidak ada di database, placeholder dikosongkan");
                    }
                } else {
                    $templateProcessor->setValue('p1', ' ');
                    error_log("Kasubbag user tidak ditemukan di users table, placeholder dikosongkan");
                }
            } else {
                $templateProcessor->setValue('p1', ' ');
                error_log("Kasubbag atasan tidak ditemukan, placeholder dikosongkan");
            }
        } else {
            // Status masih pending_kasubbag atau lebih awal, kosongkan placeholder
            $templateProcessor->setValue('p1', ' ');
            error_log("Status belum passed kasubbag approval atau kasubbag_id kosong, placeholder dikosongkan");
        }
        
        error_log("=== Placeholder P1 END ===");

        // === Placeholder P2: Paraf Kabag ===
        // Placeholder ${p2} diisi dengan image paraf kabag setelah kabag approve
        // (status berubah dari pending_kabag ke pending_sekretaris atau lainnya).
        // Sebelum kabag approval atau jika belum upload paraf, kosongkan.
        error_log("=== Placeholder P2 START ===");

        // Cek apakah status sudah passed kabag approval
        $isAfterKabagApproval = in_array($leaveData['status'], [
            'pending_sekretaris', 'awaiting_pimpinan', 
            'approved', 'rejected', 'changed', 'postponed'
        ]);
        error_log("Status: " . $leaveData['status'] . ", isAfterKabagApproval=" . ($isAfterKabagApproval ? 'true' : 'false'));

        if ($isAfterKabagApproval && isset($leaveData['kabag_approver_id']) && !empty($leaveData['kabag_approver_id'])) {
            error_log("Kabag approver ID: " . $leaveData['kabag_approver_id']);

            // Cari kabag atasan berdasarkan kabag_approver_id dari atasan table
            $kabagAtasan = $db->fetch(
                "SELECT a.id_atasan, a.NIP, a.nama_atasan FROM atasan a WHERE a.id_atasan = ?",
                [$leaveData['kabag_approver_id']]
            );

            if ($kabagAtasan) {
                error_log("Kabag atasan found: " . $kabagAtasan['nama_atasan']);

                // Cari kabag user_id dari users table berdasarkan NIP
                $kabagUser = $db->fetch(
                    "SELECT u.id FROM users u WHERE u.nip = ? AND u.user_type = 'atasan' AND u.is_deleted = 0 LIMIT 1",
                    [$kabagAtasan['NIP']]
                );

                if ($kabagUser) {
                    error_log("Kabag User ID: " . $kabagUser['id']);

                    // Dapatkan path paraf kabag dari helper (akan mengikuti ukuran placeholder)
                    $kabagParafPath = getParafAtasanCutiPath($kabagUser['id'], 'kabag');
                    error_log("Kabag Paraf Path: " . $kabagParafPath . ", exists=" . (file_exists($kabagParafPath) ? 'true' : 'false'));

                    if ($kabagParafPath && file_exists($kabagParafPath)) {
                        // ukuran kecil sesuai placeholder
                        $templateProcessor->setImageValue('p2', [
                            'path' => $kabagParafPath,
                            'width' => 40,
                            'height' => 20,
                            'ratio' => true
                        ]);
                        error_log("Placeholder p2 diisi dengan image paraf kabag");
                    } else {
                        $templateProcessor->setValue('p2', ' ');
                        error_log("File paraf kabag tidak ditemukan, placeholder dikosongkan");
                    }
                } else {
                    $templateProcessor->setValue('p2', ' ');
                    error_log("Kabag user tidak ditemukan di users table, placeholder dikosongkan");
                }
            } else {
                $templateProcessor->setValue('p2', ' ');
                error_log("Kabag atasan tidak ditemukan, placeholder dikosongkan");
            }
        } else {
            // Status masih pending_kabag atau kabag_approver_id kosong, kosongkan placeholder
            $templateProcessor->setValue('p2', ' ');
            error_log("Status belum passed kabag approval atau kabag_approver_id kosong, placeholder dikosongkan");
        }
        error_log("=== Placeholder P2 END ===");

        // === Placeholder P3: Paraf Sekretaris ===
        // Placeholder ${p3} diisi dengan image paraf sekretaris setelah sekretaris approve
        // (status berubah dari pending_sekretaris ke awaiting_pimpinan atau lainnya).
        // Sebelum sekretaris approval atau jika belum upload paraf, kosongkan.
        error_log("=== Placeholder P3 START ===");

        // Cek apakah status sudah passed sekretaris approval
        $isAfterSekretarisApproval = in_array($leaveData['status'], [
            'awaiting_pimpinan', 
            'approved', 'rejected', 'changed', 'postponed'
        ]);
        error_log("Status: " . $leaveData['status'] . ", isAfterSekretarisApproval=" . ($isAfterSekretarisApproval ? 'true' : 'false'));

        if ($isAfterSekretarisApproval && isset($leaveData['sekretaris_approver_id']) && !empty($leaveData['sekretaris_approver_id'])) {
            error_log("Sekretaris approver ID: " . $leaveData['sekretaris_approver_id']);

            // Cari sekretaris atasan berdasarkan sekretaris_approver_id dari atasan table
            $sekretarisAtasan = $db->fetch(
                "SELECT a.id_atasan, a.NIP, a.nama_atasan FROM atasan a WHERE a.id_atasan = ?",
                [$leaveData['sekretaris_approver_id']]
            );

            if ($sekretarisAtasan) {
                error_log("Sekretaris atasan found: " . $sekretarisAtasan['nama_atasan']);

                // Cari sekretaris user_id dari users table berdasarkan NIP
                $sekretarisUser = $db->fetch(
                    "SELECT u.id FROM users u WHERE u.nip = ? AND u.user_type = 'atasan' AND u.is_deleted = 0 LIMIT 1",
                    [$sekretarisAtasan['NIP']]
                );

                if ($sekretarisUser) {
                    error_log("Sekretaris User ID: " . $sekretarisUser['id']);

                    // Dapatkan path paraf sekretaris dari helper (akan mengikuti ukuran placeholder)
                    $sekretarisParafPath = getParafAtasanCutiPath($sekretarisUser['id'], 'sekretaris');
                    error_log("Sekretaris Paraf Path: " . $sekretarisParafPath . ", exists=" . (file_exists($sekretarisParafPath) ? 'true' : 'false'));

                    if ($sekretarisParafPath && file_exists($sekretarisParafPath)) {
                        // ukuran kecil sesuai placeholder
                        $templateProcessor->setImageValue('p3', [
                            'path' => $sekretarisParafPath,
                            'width' => 40,
                            'height' => 20,
                            'ratio' => true
                        ]);
                        error_log("Placeholder p3 diisi dengan image paraf sekretaris");
                    } else {
                        $templateProcessor->setValue('p3', ' ');
                        error_log("File paraf sekretaris tidak ditemukan, placeholder dikosongkan");
                    }
                } else {
                    $templateProcessor->setValue('p3', ' ');
                    error_log("Sekretaris user tidak ditemukan di users table, placeholder dikosongkan");
                }
            } else {
                $templateProcessor->setValue('p3', ' ');
                error_log("Sekretaris atasan tidak ditemukan, placeholder dikosongkan");
            }
        } else {
            // Status masih pending_sekretaris atau sekretaris_approver_id kosong, kosongkan placeholder
            $templateProcessor->setValue('p3', ' ');
            error_log("Status belum passed sekretaris approval atau sekretaris_approver_id kosong, placeholder dikosongkan");
        }
        // === Placeholder P4: Paraf Admin ===
        // Placeholder ${p4} diisi dengan image paraf admin ketika admin melanjutkan proses pengajuan cuti
        // (status approved dan admin_blankofinal_sender tidak NULL)
        error_log("=== Placeholder P4 START ===");
        
        // Cek apakah status sudah approved dan admin_blankofinal_sender ada
        $isAfterAdminContinue = ($leaveData['status'] === 'approved' && isset($leaveData['admin_blankofinal_sender']) && !empty($leaveData['admin_blankofinal_sender']));
        
        error_log("Status: " . $leaveData['status'] . ", admin_blankofinal_sender=" . ($leaveData['admin_blankofinal_sender'] ?? 'null') . ", isAfterAdminContinue=" . ($isAfterAdminContinue ? 'true' : 'false'));
        
        if ($isAfterAdminContinue) {
            error_log("Admin blankofinal sender ID: " . $leaveData['admin_blankofinal_sender']);
            
            // Cari admin user berdasarkan admin_blankofinal_sender dari users table
            $adminUser = $db->fetch(
                "SELECT u.id FROM users u WHERE u.id = ? AND u.user_type = 'admin' AND u.is_deleted = 0 LIMIT 1",
                [$leaveData['admin_blankofinal_sender']]
            );
            
            if ($adminUser) {
                error_log("Admin User ID: " . $adminUser['id']);
                
                // Cari paraf admin dari user_signatures table
                $adminParaf = $db->fetch(
                    "SELECT signature_file FROM user_signatures WHERE user_id = ? AND signature_type = 'paraf' AND is_active = 1",
                    [$adminUser['id']]
                );
                
                if ($adminParaf && !empty($adminParaf['signature_file'])) {
                    $adminParafPath = getSignatureFilePath($adminParaf['signature_file']);
                    error_log("Admin Paraf Path: " . $adminParafPath . ", exists=" . (file_exists($adminParafPath) ? 'true' : 'false'));
                    
                    if (file_exists($adminParafPath)) {
                        // Gunakan ukuran kecil sesuai ukuran placeholder yang kecil
                        $templateProcessor->setImageValue('p4', [
                            'path' => $adminParafPath,
                            'width' => 40,
                            'height' => 20,
                            'ratio' => true
                        ]);
                        error_log("Placeholder p4 diisi dengan image paraf admin");
                    } else {
                        $templateProcessor->setValue('p4', ' ');
                        error_log("File paraf admin tidak ditemukan, placeholder dikosongkan");
                    }
                } else {
                    $templateProcessor->setValue('p4', ' ');
                    error_log("Paraf admin tidak ada di database, placeholder dikosongkan");
                }
            } else {
                $templateProcessor->setValue('p4', ' ');
                error_log("Admin user tidak ditemukan di users table, placeholder dikosongkan");
            }
        } else {
            // Status belum approved atau admin_blankofinal_sender kosong, kosongkan placeholder
            $templateProcessor->setValue('p4', ' ');
            error_log("Status belum approved atau admin_blankofinal_sender kosong, placeholder dikosongkan");
        }
        
        error_log("=== Placeholder P4 END ===");

        // (DIPINDAHKAN KE BAWAH SETELAH ADMIN)
        // === Kolom VIII. Pejabat Berwenang (admin or ketua) ===
        $adminHasImage = false;
        $adminSignatureFile = '';
        $adminJabatan = $adminNama = $adminNip = '';
        
        // Cek apakah status sudah approval final oleh ketua
        $isApprovalFinalByKetua = in_array($leaveData['status'], [
            'awaiting_pimpinan', 'approved', 'rejected', 'changed', 'postponed'
        ]) && isset($leaveData['ketua_approver_id']) && !empty($leaveData['ketua_approver_id']);
        
        if ($isApprovalFinalByKetua) {
            // Gunakan data ketua (atasan dengan role=ketua) untuk placeholder admin
            error_log("=== Admin Placeholder: Menggunakan data Ketua START ===");
            error_log("Ketua Approver ID: " . $leaveData['ketua_approver_id']);
            
            // Ambil data ketua dari tabel atasan
            $ketuaAtasan = $db->fetch(
                "SELECT a.id_atasan, a.NIP, a.nama_atasan, a.jabatan FROM atasan a WHERE a.id_atasan = ?",
                [$leaveData['ketua_approver_id']]
            );
            
            if ($ketuaAtasan) {
                error_log("Ketua data found: " . $ketuaAtasan['nama_atasan']);
                
                // Cari ketua user_id dari users table berdasarkan NIP
                $ketuaUser = $db->fetch(
                    "SELECT u.id FROM users u WHERE u.nip = ? AND u.user_type = 'atasan' AND u.is_deleted = 0 LIMIT 1",
                    [$ketuaAtasan['NIP']]
                );
                
                if ($ketuaUser) {
                    error_log("Ketua User ID: " . $ketuaUser['id']);
                    
                    // Cari signature ketua dari user_signatures table
                    $ketuaSignature = $db->fetch(
                        "SELECT signature_file FROM user_signatures WHERE user_id = ? AND signature_type = 'user' AND is_active = 1",
                        [$ketuaUser['id']]
                    );
                    
                    if ($ketuaSignature && !empty($ketuaSignature['signature_file'])) {
                        $ketuaSignaturePath = getSignatureFilePath($ketuaSignature['signature_file']);
                        error_log("Ketua Signature Path: " . $ketuaSignaturePath . ", exists=" . (file_exists($ketuaSignaturePath) ? 'true' : 'false'));
                        
                        if (file_exists($ketuaSignaturePath)) {
                            $adminHasImage = true;
                            $adminSignatureFile = $ketuaSignaturePath;
                            $templateProcessor->setImageValue('ttd_admin', [
                                'path' => $ketuaSignaturePath,
                                'width' => 180,
                                'height' => 90,
                                'ratio' => true
                            ]);
                            $adminJabatan = $adminNama = $adminNip = '';
                            error_log("Placeholder ttd_admin diisi dengan image signature ketua");
                        } else {
                            $adminHasImage = false;
                            $adminJabatan = $ketuaAtasan['jabatan'];
                            $adminNama = $ketuaAtasan['nama_atasan'];
                            $adminNip = 'NIP. ' . $ketuaAtasan['NIP'];
                            $templateProcessor->setValue('ttd_admin', '');
                            error_log("File signature ketua tidak ditemukan, placeholder ttd_admin dikosongkan");
                        }
                    } else {
                        $adminHasImage = false;
                        $adminJabatan = $ketuaAtasan['jabatan'];
                        $adminNama = $ketuaAtasan['nama_atasan'];
                        $adminNip = 'NIP. ' . $ketuaAtasan['NIP'];
                        $templateProcessor->setValue('ttd_admin', '');
                        error_log("Signature ketua tidak ada di database, placeholder ttd_admin dikosongkan");
                    }
                } else {
                    $adminHasImage = false;
                    $adminJabatan = $ketuaAtasan['jabatan'];
                    $adminNama = $ketuaAtasan['nama_atasan'];
                    $adminNip = 'NIP. ' . $ketuaAtasan['NIP'];
                    $templateProcessor->setValue('ttd_admin', '');
                    error_log("Ketua user tidak ditemukan di users table, menggunakan data dari atasan");
                }
            } else {
                $adminHasImage = false;
                $adminJabatan = $adminNama = $adminNip = '________________________';
                $templateProcessor->setValue('ttd_admin', '');
                error_log("Ketua atasan tidak ditemukan, placeholder dikosongkan");
            }
            error_log("=== Admin Placeholder: Menggunakan data Ketua END ===");
        } else {
            $adminHasImage = false;
            $adminJabatan = $adminNama = $adminNip = '________________________';
            $templateProcessor->setValue('ttd_admin', '');
        }
        $templateProcessor->setValue('jabatan_admin', $adminJabatan);
        $templateProcessor->setValue('nama_admin', $adminNama);
        $templateProcessor->setValue('nip_admin', $adminNip);

        // Jika user adalah AtasanSatu dan berstatus final approval,
        // copy seluruh data admin ke atasan agar tampil konsisten dan identik.
        if (isset($shouldUseAdminForAtasan) && $shouldUseAdminForAtasan) {
            if ($adminHasImage && !empty($adminSignatureFile)) {
                $templateProcessor->setImageValue('ttd_atasan', [
                    'path' => $adminSignatureFile,
                    'width' => 180,
                    'height' => 90,
                    'ratio' => true
                ]);
            } else {
                $templateProcessor->setValue('ttd_atasan', '');
            }
            $templateProcessor->setValue('jabatan_atasan', $adminJabatan);
            $templateProcessor->setValue('nama_atasan', $adminNama);
            $templateProcessor->setValue('nip_atasan', $adminNip);
        }
        // Placeholder untuk pejabat (akan diisi manual)
        $templateProcessor->setValue('nama_pejabat', '________________________');
        $templateProcessor->setValue('nip_pejabat', '________________________');
        
        // Checkbox keputusan berdasarkan status approval
        $isApproved = ($leaveData['status'] == 'approved');
        $isRejected = ($leaveData['status'] == 'rejected');
        $isChanged = ($leaveData['status'] == 'changed');
        $isPostponed = ($leaveData['status'] == 'postponed');

        // Placeholder untuk checkbox keputusan (hanya satu yang tercentang)
        $cekDisetujui = $isApproved ? '☑' : '';
        $cekDitolak = $isRejected ? '☑' : '';
        $cekPerubahan = $isChanged ? '☑' : '';
        $cekDitangguhkan = $isPostponed ? '☑' : '';

        // Set placeholder untuk checkbox keputusan (atasan dan pejabat sama)
        $templateProcessor->setValue('cek_disetujui', $cekDisetujui);
        $templateProcessor->setValue('cek_ditolak', $cekDitolak);
        $templateProcessor->setValue('cek_perubahan', $cekPerubahan);
        $templateProcessor->setValue('cek_ditangguhkan', $cekDitangguhkan);

        $templateProcessor->setValue('check_disetujui_pejabat', $cekDisetujui);
        $templateProcessor->setValue('check_perubahan_pejabat', $cekPerubahan);
        $templateProcessor->setValue('check_ditangguhkan_pejabat', $cekDitangguhkan);
        $templateProcessor->setValue('check_tidak_disetujui_pejabat', $cekDitolak);

        $templateProcessor->setValue('check_disetujui_atasan', $cekDisetujui);
        $templateProcessor->setValue('check_perubahan_atasan', $cekPerubahan);
        $templateProcessor->setValue('check_ditangguhkan_atasan', $cekDitangguhkan);
        $templateProcessor->setValue('check_tidak_disetujui_atasan', $cekDitolak);
        
        // Alasan admin (catatan approval)
        $alasanAdmin = '';
        if (isset($leaveData['catatan_approval']) && !empty(trim($leaveData['catatan_approval']))) {
            $alasanAdmin = 'Alasan: ' . $leaveData['catatan_approval'];
        } else {
            $alasanAdmin = '';
        }
        $templateProcessor->setValue('alasan_admin', $alasanAdmin);
        
        // === Placeholder Alasan Atasan ===
        // Diisi setelah atasan melakukan approval (status berubah dari pending ke status lainnya)
        // Sebelum approval atasan, placeholder dikosongkan
        error_log("=== Alasan Atasan START ===");
        
        // Cek apakah status sudah passed atasan approval
        // Status awal: 'pending', status setelah approval: 'pending_kasubbag', 'pending_kabag', dll
        $isAfterAtasanApproval = !in_array($leaveData['status'], ['pending', 'draft']);
        
        error_log("Status: " . $leaveData['status'] . ", isAfterAtasanApproval=" . ($isAfterAtasanApproval ? 'true' : 'false'));
        
        $alasanAtasan = '';
        if ($isAfterAtasanApproval && isset($leaveData['atasan_catatan']) && !empty(trim($leaveData['atasan_catatan']))) {
            $alasanAtasan = 'Alasan Atasan: ' . $leaveData['atasan_catatan'];
        } else {
            $alasanAtasan = '';
        }
        $templateProcessor->setValue('alasan_atasan', $alasanAtasan);
        
        error_log("=== Alasan Atasan END ===");
        
        // Generate filename
        $filename = 'Formulir_Cuti_' . date('YmdHis') . '.docx';
        $outputPath = dirname(dirname(__DIR__)) . '/public/uploads/documents/temp/' . $filename;
        
        // Buat folder jika belum ada
        $outputDir = dirname($outputPath);
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        
        // Simpan dokumen
        $templateProcessor->saveAs($outputPath);
        
        return [
            'success' => true,
            'filename' => $filename,
            'path' => $outputPath
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error generating document: ' . $e->getMessage()
        ];
    }
}

function processUploadedDocument($file, $leaveId, $userId, $type) {
    $allowedTypes = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
                     'application/msword', 'application/pdf'];
    
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Format file tidak diizinkan. Hanya DOCX, DOC, atau PDF.'];
    }
    
    if ($file['size'] > 10485760) { // 10MB
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (max 10MB).'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'DOC_' . $type . '_' . $leaveId . '_' . date('YmdHis') . '.' . $extension;
    $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/documents/signed/';
    
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $uploadPath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return [
            'success' => true,
            'filename' => $filename,
            'path' => $uploadPath
        ];
    }
    
    return ['success' => false, 'message' => 'Gagal mengupload file.'];
}


function extractKotaFromUnitKerja($unitKerja) {
    // Extract the city name from the unit_kerja string.
    // This regex tries to capture the city name after "Pengadilan Agama" or as the first word.
    if (preg_match("/(?:Pengadilan Agama\s+)?([A-Za-z]+(?:\s[A-Za-z]+)*)/", $unitKerja, $matches)) {
        return trim($matches[1]);
    }
    return $unitKerja; // Fallback to original if no match
}


/**
 * Retrieve the latest generated document record for a given leave request.
 *
 * @param int $leaveId
 * @return array|null
 */
function getLatestGeneratedDocument($leaveId) {
    require_once dirname(__DIR__) . '/models/DocumentModel.php';
    $model = new DocumentModel();
    return $model->getLatestByLeaveId($leaveId, 'generated');
}

/**
 * Check whether a generated document file for the given leave exists on disk.
 *
 * @param int $leaveId
 * @return bool
 */
function hasGeneratedDocumentFile($leaveId) {
    $doc = getLatestGeneratedDocument($leaveId);
    if (!$doc || empty($doc['filename'])) {
        return false;
    }
    $baseDir = dirname(dirname(__DIR__)) . '/public/uploads/documents/';
    $paths = [
        $baseDir . 'temp/' . $doc['filename'],
        $baseDir . 'generated/' . $doc['filename'],
        $baseDir . $doc['filename']
    ];
    foreach ($paths as $p) {
        if (file_exists($p)) {
            return true;
        }
    }
    return false;
}