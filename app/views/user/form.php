<style>
/* Hilangkan ikon show/hide password default browser pada input password (khusus Chrome, Edge, Safari) */
input[type="password"]::-ms-reveal,
input[type="password"]::-ms-clear {
    display: none;
}
input[type="password"]::-webkit-credentials-auto-fill-button,
input[type="password"]::-webkit-input-password-reveal-button {
    display: none !important;
}
input[type="password"]::-webkit-input-clear-button {
    display: none !important;
}
input[type="password"]::-webkit-input-password-reveal {
    display: none !important;
}
input[type="password"]::-o-clear-button {
    display: none !important;
}
input[type="password"]::-o-reveal-button {
    display: none !important;
}
.nav-tabs .nav-link {
    color: #495057;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-bottom: none;
}
.nav-tabs .nav-link:hover {
    border-color: #dee2e6;
    background-color: #e9ecef;
}
.nav-tabs .nav-link.active {
    color: #fff;
    background-color: #1b5e20;
    border-color: #dee2e6 #dee2e6 #fff;
}
.tab-content {
    border: 1px solid #dee2e6;
    border-top: none;
    padding: 20px;
}
.tab-content .tab-pane {
    display: none;
}
.tab-content .tab-pane.active {
    display: block;
}
</style>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><?php echo $page_title; ?></h5>
    </div>
    <div class="card-body">
        <form id="userForm">
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <input type="hidden" name="id" value="<?php echo $user ? $user['id'] : ''; ?>">

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-3" id="userFormTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-data-user" data-bs-toggle="tab" data-bs-target="#content-data-user" 
                        type="button" role="tab" aria-controls="content-data-user" aria-selected="true">
                    <i class="bi bi-person-fill me-2"></i>Data User
                </button>
            </li>
            <?php if (($action == 'edit' && $user && ($user['user_type'] == 'pegawai' || $user['user_type'] == 'atasan')) || ($action == 'add')): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-kuota-cuti" data-bs-toggle="tab" data-bs-target="#content-kuota-cuti" 
                        type="button" role="tab" aria-controls="content-kuota-cuti" aria-selected="false">
                    <i class="bi bi-calendar-check me-2"></i>Kuota Cuti
                </button>
            </li>
            <?php endif; ?>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="userFormTabContent">
            <!-- Tab 1: Data User -->
            <div class="tab-pane fade active show" id="content-data-user" role="tabpanel" aria-labelledby="tab-data-user">
            
            <?php if ($action == 'add'): ?>
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Informasi:</strong> Semua field yang ditandai dengan <span class="text-danger">*</span> adalah wajib diisi.
                Tanggal masuk akan otomatis diisi berdasarkan NIP yang dimasukkan.
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo $user ? $user['username'] : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group align-items-center">
                        <input type="password" class="form-control" id="password" name="password" <?php echo $action == 'add' ? 'required' : ''; ?> autocomplete="new-password">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1" aria-label="Show/Hide Password">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    <?php if ($action == 'edit'): ?>
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $user ? $user['nama'] : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nip" class="form-label">NIP <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nip" name="nip" value="<?php echo $user ? $user['nip'] : ''; ?>" required>
                    <?php if ($action == 'add'): ?>
                        <small class="text-muted">Tanggal masuk akan otomatis diisi berdasarkan NIP (digit 9-14)</small>
                        <div class="mt-1">
                            <small id="tanggalMasukInfo" class="text-info">
                                <i class="bi bi-calendar-event me-1"></i>
                                Tanggal masuk: <span id="tanggalMasukPreview">-</span>
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="jabatan" name="jabatan" value="<?php echo $user ? $user['jabatan'] : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="golongan" class="form-label">Golongan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="golongan" name="golongan" value="<?php echo $user ? $user['golongan'] : ''; ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="unit_kerja" class="form-label">Unit Kerja <span class="text-danger">*</span></label>
                    <?php 
                        require_once __DIR__ . '/../../models/Satker.php';
                        $satkerModel = new Satker();
                        $satkerList = $satkerModel->getAllSatker();
                    ?>
                    <select class="form-control" id="unit_kerja" name="unit_kerja" required>
                        <option value="">Pilih Unit Kerja</option>
                        <?php foreach ($satkerList as $satker): ?>
                            <option value="<?php echo $satker['id_satker']; ?>" <?php echo ($user && $user['unit_kerja'] == $satker['id_satker']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($satker['nama_satker']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="atasan" class="form-label">Atasan</label>
                    <select class="form-control" id="atasan" name="atasan">
                        <option value="">Pilih Atasan</option>
                        <?php if (isset($atasanList) && is_array($atasanList)): ?>
                            <?php foreach ($atasanList as $atasan): ?>
                                <option value="<?php echo $atasan['id_atasan']; ?>" 
                                    <?php echo ($user && $user['atasan'] == $atasan['id_atasan']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($atasan['nama_atasan']); ?> (<?php echo $atasan['NIP']; ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="user_type" class="form-label">Tipe User <span class="text-danger">*</span></label>
                    <select class="form-control" id="user_type" name="user_type" required>
                        <option value="">Pilih Tipe User</option>
                        <option value="admin" <?php echo ($user && $user['user_type'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="pegawai" <?php echo ($user && $user['user_type'] == 'pegawai') ? 'selected' : ''; ?>>Pegawai</option>
                        <option value="atasan" <?php echo ($user && $user['user_type'] == 'atasan') ? 'selected' : ''; ?>>Atasan</option>
                    </select>
                </div>
            </div>
            
            <?php if ($action == 'edit'): ?>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="user_status" class="form-label">Status Akun <span class="text-danger">*</span></label>
                    <select class="form-control" id="user_status" name="user_status" required>
                        <option value="active" <?php echo ($user && !$user['is_deleted']) ? 'selected' : ''; ?>>Aktif</option>
                        <option value="inactive" <?php echo ($user && $user['is_deleted']) ? 'selected' : ''; ?>>Non-Aktif</option>
                    </select>
                    <small class="text-muted">Status "Non-Aktif" akan mencegah user untuk login ke sistem</small>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?php echo baseUrl('user/manage'); ?>" class="btn btn-secondary">Batal</a>
                </div>
            </div>
            </div><!-- End content-data-user tab pane -->

            <!-- Tab 2: Kuota Cuti -->
            <div class="tab-pane fade" id="content-kuota-cuti" role="tabpanel" aria-labelledby="tab-kuota-cuti">
                <?php if ($action == 'add'): ?>
                <!-- Section Kuota Cuti untuk User Baru -->
                <div class="card" id="quotaSectionNew">
                <div class="card-header">
                    <h6 class="mb-0">Kuota Cuti Awal</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Informasi:</strong> Kuota cuti akan otomatis dibuat dengan nilai default: <?php echo date('Y') - 2; ?>: 0, <?php echo date('Y') - 1; ?>: 6, <?php echo date('Y'); ?>: 12
                            </div>
                        </div>
                    </div>
                    <div class="row" id="newQuotaInputs">
                        <div class="col-md-4 mb-3">
                            <div class="card quota-card">
                                <div class="card-header">
                                    <h6 class="mb-0">Tahun <?php echo date('Y') - 2; ?></h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <label class="form-label">Kuota Tahunan</label>
                                        <input type="text" class="form-control" value="0" readonly>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Sisa Kuota</label>
                                        <input type="text" class="form-control" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card quota-card">
                                <div class="card-header">
                                    <h6 class="mb-0">Tahun <?php echo date('Y') - 1; ?></h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <label class="form-label">Kuota Tahunan</label>
                                        <input type="text" class="form-control" value="6" readonly>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Sisa Kuota</label>
                                        <input type="text" class="form-control" value="6" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card quota-card">
                                <div class="card-header">
                                    <h6 class="mb-0">Tahun <?php echo date('Y'); ?></h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <label class="form-label">Kuota Tahunan</label>
                                        <input type="text" class="form-control" value="12" readonly>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Sisa Kuota</label>
                                        <input type="text" class="form-control" value="12" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Ringkasan Kuota Awal</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h4 class="text-primary">18</h4>
                                                <small class="text-muted">Total Kuota Tahunan</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h4 class="text-success">18</h4>
                                                <small class="text-muted">Total Sisa Kuota</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h4 class="text-warning">0</h4>
                                                <small class="text-muted">Total Terpakai</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($action == 'edit' && $user && ($user['user_type'] == 'pegawai' || $user['user_type'] == 'atasan')): ?>
            <!-- Section Kuota Cuti -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Kuota Cuti</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Informasi:</strong> Pilih jenis kuota cuti yang ingin diedit. Kuota cuti tahunan dapat diatur untuk 3 tahun (<?php echo date('Y') - 2; ?>, <?php echo date('Y') - 1; ?>, dan <?php echo date('Y'); ?>). 
                                Sisa kuota tidak boleh melebihi kuota tahunan.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pilihan Jenis Kuota -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Pilih Jenis Kuota Cuti:</label>
                            <select class="form-select" id="jenisKuotaSelect" onchange="loadKuotaByType()">
                                <option value="">Pilih jenis kuota...</option>
                                <option value="tahunan">Cuti Tahunan (Akumulatif 3 tahun)</option>
                                <option value="besar">Cuti Besar (Akumulatif - setelah 6 tahun)</option>
                                <option value="sakit">Cuti Sakit (Per tahun)</option>
                                <option value="melahirkan">Cuti Melahirkan</option>
                                <option value="alasan_penting">Cuti Alasan Penting (Info)</option>
                                <option value="luar_tanggungan">Cuti Luar Tanggungan (Per tahun)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="showQuotaHistory(<?php echo $user['id']; ?>)">
                                <i class="bi bi-clock-history me-1"></i>Lihat Riwayat Kuota
                            </button>
                        </div>
                    </div>
                    
                    <!-- Container untuk kuota tahunan -->
                    <div id="quotaTahunanContainer" style="display: none;">
                        <div class="row" id="quotaSection">
                            <!-- Data kuota tahunan akan dimuat via AJAX -->
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Ringkasan Kuota Tahunan</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <h4 class="text-primary" id="totalKuota">0</h4>
                                                    <small class="text-muted">Total Kuota Tahunan</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <h4 class="text-success" id="totalSisa">0</h4>
                                                    <small class="text-muted">Total Sisa Kuota</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <h4 class="text-warning" id="totalTerpakai">0</h4>
                                                    <small class="text-muted">Total Terpakai</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary" id="btnUpdateAllQuota">
                                    <i class="bi bi-save me-1"></i>Update Kuota Tahunan
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Container untuk kuota cuti besar -->
                    <div id="quotaBesarContainer" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Kuota Cuti Besar</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Total Kuota (hari)</label>
                                            <input type="number" class="form-control" id="kuotaBesarTotal" min="0" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Sisa Kuota (hari)</label>
                                            <input type="number" class="form-control" id="kuotaBesarSisa" min="0">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" id="kuotaBesarStatus">
                                                <option value="belum_berhak">Belum Berhak</option>
                                                <option value="berhak">Berhak</option>
                                                <option value="digunakan">Digunakan</option>
                                                <option value="habis">Habis</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Berhak</label>
                                            <input type="date" class="form-control" id="kuotaBesarTanggalBerhak">
                                        </div>
                                        <button type="button" class="btn btn-primary" onclick="updateKuotaBesar()">
                                            <i class="bi bi-save me-1"></i>Update Kuota Besar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Container untuk kuota cuti melahirkan -->
                    <div id="quotaMelahirkanContainer" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Kuota Cuti Melahirkan</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Total Kuota (hari)</label>
                                            <input type="number" class="form-control" id="kuotaMelahirkanTotal" min="0" value="90" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" id="kuotaMelahirkanStatus">
                                                <option value="tersedia">Tersedia</option>
                                                <option value="digunakan">Digunakan</option>
                                                <option value="habis">Habis</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Penggunaan</label>
                                            <input type="date" class="form-control" id="kuotaMelahirkanTanggalPenggunaan">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Jumlah Pengambilan Cuti Melahirkan</label>
                                            <input type="number" class="form-control" id="jumlahPengambilanMelahirkan" name="jumlah_pengambilan_melahirkan" min="0" max="3" value="<?php echo isset($kuotaMelahirkan['jumlah_pengambilan']) ? $kuotaMelahirkan['jumlah_pengambilan'] : 0; ?>">
                                            <small class="text-muted">Maksimal 3 kali selama masa kerja</small>
                                        </div>
                                        <button type="button" class="btn btn-primary" onclick="updateKuotaMelahirkan()">
                                            <i class="bi bi-save me-1"></i>Update Status Melahirkan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Container untuk info cuti alasan penting (tidak ada kuota akumulatif) -->
                    <div id="quotaAlasanPentingContainer" style="display: none;">
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Cuti Alasan Penting – Tidak Ada Kuota Akumulatif</strong>
                            <hr class="my-2">
                            <p class="mb-1">Cuti karena alasan penting <strong>tidak berpatokan pada kuota</strong> yang tersimpan di database.</p>
                            <p class="mb-1">Batas yang berlaku adalah <strong>per sekali pengajuan</strong>:</p>
                            <ul class="mb-0">
                                <li><strong>Pegawai biasa:</strong> maksimal <strong>10 hari</strong> per 1 kali pengajuan</li>
                                <li><strong>Hakim Tinggi:</strong> maksimal <strong>30 hari</strong> per 1 kali pengajuan</li>
                            </ul>
                            <p class="mt-2 mb-0 text-muted"><small>Tidak diperlukan pengaturan kuota untuk jenis cuti ini.</small></p>
                        </div>
                    </div>

                    <!-- Container untuk kuota tahunan (sakit, luar tanggungan) -->
                    <div id="quotaTahunanLainContainer" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0" id="quotaTahunanLainTitle">Kuota Cuti</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info" id="quotaTahunanLainInfo" style="display:none;">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>Informasi:</strong> <span id="quotaTahunanLainInfoText"></span>
                                        </div>
                                        <div class="mb-3" id="quotaTahunanLainTahunWrapper" style="display:none;">
                                            <label class="form-label">Tahun</label>
                                            <select class="form-select" id="quotaTahunanLainTahun">
                                                <option value="<?php echo date('Y') - 2; ?>"><?php echo date('Y') - 2; ?></option>
                                                <option value="<?php echo date('Y') - 1; ?>"><?php echo date('Y') - 1; ?></option>
                                                <option value="<?php echo date('Y'); ?>" selected><?php echo date('Y'); ?></option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Kuota Tahunan (hari)</label>
                                            <input type="number" class="form-control" id="quotaTahunanLainKuota" min="0" value="14">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Sisa Kuota (hari)</label>
                                            <input type="number" class="form-control" id="quotaTahunanLainSisa" min="0">
                                            <div class="invalid-feedback" id="sisaKuotaInvalidMsg" style="display:none;">Sisa kuota tidak boleh lebih dari kuota tahunan</div>
                                        </div>
                                        <button type="button" class="btn btn-primary" id="btnUpdateKuotaTahunanLain" onclick="updateKuotaTahunanLain()">
                                            <i class="bi bi-save me-1"></i>Update Kuota
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            </div><!-- End content-kuota-cuti tab pane -->
        </div><!-- End tab-content -->
        </form>
    </div>
</div>

<style>
.input-group.align-items-center { align-items: center; }
.input-group .invalid-feedback {
    position: absolute;
    left: 0;
    top: 100%;
    z-index: 2;
    width: 100%;
    margin-top: 0.1rem;
    font-size: 0.95em;
}
.input-group { position: relative; }
.quota-card {
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.quota-card:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.quota-card .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.new-quota-input:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.form-control.is-invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='m5.8 4.6 2.4 2.4m0-2.4L5.8 7'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-control.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-select.is-invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='m5.8 4.6 2.4 2.4m0-2.4L5.8 7'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-select.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Styling untuk tanggal masuk info */
#tanggalMasukInfo {
    font-size: 0.875em;
    font-weight: 500;
}

#tanggalMasukInfo.text-info {
    color: #0dcaf0 !important;
}

#tanggalMasukInfo.text-success {
    color: #198754 !important;
}

#tanggalMasukInfo.text-danger {
    color: #dc3545 !important;
}

/* Required field indicator */
.text-danger {
    color: #dc3545 !important;
}

/* Form validation animation */
.form-control.is-invalid,
.form-select.is-invalid {
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}
</style>

<script>
$(document).ready(function() {
    // Show/hide password toggle, dan hapus ikon silang merah jika ada
    $('#togglePassword').on('click', function() {
        const passwordInput = $('#password');
        const icon = $('#togglePasswordIcon');
        const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);
        icon.toggleClass('bi-eye bi-eye-slash');
    });
    // Hilangkan ikon silang merah pada input password jika ada (bawaan browser)
    $('#password').on('input focus', function() {
        // Hilangkan tombol clear bawaan browser (IE/Edge lama), dan hilangkan outline merah jika valid
        this.style.backgroundImage = 'none';
    });

    // Initialize Bootstrap tabs functionality
    const userFormTabs = document.getElementById('userFormTabs');
    if (userFormTabs) {
        userFormTabs.addEventListener('shown.bs.tab', function (event) {
            const targetTab = event.target.getAttribute('data-bs-target');
            console.log('Tab switched to:', targetTab);
            
            // Load quota data when switching to quota tab
            if (targetTab === '#content-kuota-cuti') {
                <?php if ($action == 'edit' && $user): ?>
                const userId = <?php echo $user['id']; ?>;
                loadKuotaByType();
                <?php endif; ?>
            }
        });
    }

    <?php if ($action == 'edit' && $user && $user['user_type'] == 'user'): ?>
    // Load quota data for edit mode
    loadUserQuota(<?php echo $user['id']; ?>);
    
    // Set default selection to kuota tahunan
    $('#jenisKuotaSelect').val('tahunan');
    $('#quotaTahunanContainer').show();
    <?php endif; ?>

    <?php if ($action == 'add'): ?>
    // Kuota cuti akan otomatis dibuat dengan nilai default: <?php echo date('Y') - 2; ?>: 0, <?php echo date('Y') - 1; ?>: 6, <?php echo date('Y'); ?>: 12
    
    // Show/hide quota tab based on user type
    $('#user_type').on('change', function() {
        // Remove invalid class when user selects a type
        $(this).removeClass('is-invalid');
        
        const quotaTab = document.getElementById('tab-kuota-cuti');
        const quotaSectionNew = $('#quotaSectionNew');
        
        if ($(this).val() === 'pegawai') {
            // Show quota tab if it exists
            if (quotaTab) {
                quotaTab.style.display = 'block';
            }
            quotaSectionNew.show();
        } else {
            // Hide quota tab
            if (quotaTab) {
                quotaTab.style.display = 'none';
                // Switch to data-user tab if quota tab was active
                if (document.getElementById('content-kuota-cuti').classList.contains('active')) {
                    const dataUserTab = new bootstrap.Tab(document.getElementById('tab-data-user'));
                    dataUserTab.show();
                }
            }
            quotaSectionNew.hide();
        }
    });
    
    // Initially show/hide based on current selection
    if ($('#user_type').val() === 'pegawai') {
        $('#quotaSectionNew').show();
        const quotaTab = document.getElementById('tab-kuota-cuti');
        if (quotaTab) {
            quotaTab.style.display = 'block';
        }
    } else {
        $('#quotaSectionNew').hide();
        const quotaTab = document.getElementById('tab-kuota-cuti');
        if (quotaTab) {
            quotaTab.style.display = 'none';
        }
    }
    
    // Handle NIP input for tanggal masuk preview
    $('#nip').on('input', function() {
        const nip = $(this).val();
        const tanggalMasukInfo = $('#tanggalMasukInfo');
        const tanggalMasukPreview = $('#tanggalMasukPreview');
        
        // Remove invalid class when user starts typing
        $(this).removeClass('is-invalid');
        
        // Reset to default state
        tanggalMasukInfo.removeClass('text-info text-success text-danger').addClass('text-info');
        tanggalMasukPreview.text('-');
        
        if (nip.length >= 14) {
            const tanggalMasuk = extractTanggalMasukFromNIP(nip);
            if (tanggalMasuk !== '1900-01-01') {
                const formattedDate = formatTanggalMasukFromNIP(nip);
                tanggalMasukPreview.text(formattedDate);
                tanggalMasukInfo.removeClass('text-info text-danger').addClass('text-success');
            } else {
                tanggalMasukPreview.text('NIP tidak valid');
                tanggalMasukInfo.removeClass('text-info text-success').addClass('text-danger');
                $(this).addClass('is-invalid');
            }
        } else if (nip.length > 0) {
            tanggalMasukPreview.text('NIP terlalu pendek');
            tanggalMasukInfo.removeClass('text-info text-success').addClass('text-danger');
            $(this).addClass('is-invalid');
        }
    });
    
    // Trigger initial preview if NIP already has value
    if ($('#nip').val().length > 0) {
        $('#nip').trigger('input');
    }
    <?php elseif ($action == 'edit'): ?>
    // For edit action, load quota data when switching to quota tab
    // This is handled in the tab switch event listener below
    <?php endif; ?>

    $('#userForm').submit(function(e) {
        e.preventDefault();
        // Validasi form untuk memastikan semua field required terisi
        let isValid = true;
        const requiredFields = ['username', 'nama', 'nip', 'jabatan', 'golongan', 'unit_kerja', 'user_type'];
        requiredFields.forEach(field => {
            const value = $(`#${field}`).val().trim();
            if (!value) {
                $(`#${field}`).addClass('is-invalid');
                if (!$(`#${field}`).next('.invalid-feedback').length) {
                    const fieldName = $(`#${field}`).prev('label').text().replace(' *', '');
                    $(`#${field}`).after(`<div class="invalid-feedback">Field ${fieldName} wajib diisi</div>`);
                }
                isValid = false;
            } else {
                $(`#${field}`).removeClass('is-invalid');
                $(`#${field}`).next('.invalid-feedback').remove();
            }
        });
        const userType = $('#user_type').val();
        if (!userType) {
            $('#user_type').addClass('is-invalid');
            if (!$('#user_type').next('.invalid-feedback').length) {
                $('#user_type').after('<div class="invalid-feedback">Pilih tipe user</div>');
            }
            isValid = false;
        } else {
            $('#user_type').removeClass('is-invalid');
            $('#user_type').next('.invalid-feedback').remove();
        }
        if ($('input[name="action"]').val() === 'add') {
            const password = $('#password').val().trim();
            if (!password) {
                $('#password').addClass('is-invalid');
                if (!$('#password').next('.invalid-feedback').length) {
                    $('#password').after('<div class="invalid-feedback">Password wajib diisi</div>');
                }
                isValid = false;
            } else if (password.length < 6) {
                $('#password').addClass('is-invalid');
                if (!$('#password').next('.invalid-feedback').length) {
                    $('#password').after('<div class="invalid-feedback">Password minimal 6 karakter</div>');
                }
                isValid = false;
            } else {
                $('#password').removeClass('is-invalid');
                $('#password').next('.invalid-feedback').remove();
            }
        }
        const nip = $('#nip').val().trim();
        if (nip.length < 14) {
            $('#nip').addClass('is-invalid');
            if (!$('#nip').next('.invalid-feedback').length) {
                $('#nip').after('<div class="invalid-feedback">NIP harus minimal 14 digit</div>');
            }
            isValid = false;
        }
        if (nip.length >= 14) {
            const tanggalMasuk = extractTanggalMasukFromNIP(nip);
            if (tanggalMasuk === '1900-01-01') {
                $('#nip').addClass('is-invalid');
                if (!$('#nip').next('.invalid-feedback').length) {
                    $('#nip').after('<div class="invalid-feedback">NIP tidak valid. Pastikan digit 9-14 berisi tahun dan bulan yang valid</div>');
                }
                isValid = false;
            }
        }
        if (!isValid) {
            let errorMessage = 'Mohon lengkapi semua field yang wajib diisi';
            if ($('#password').hasClass('is-invalid')) {
                const passwordError = $('#password').next('.invalid-feedback').text();
                if (passwordError.includes('6 karakter')) {
                    errorMessage = 'Password minimal 6 karakter';
                } else if (passwordError.includes('wajib diisi')) {
                    errorMessage = 'Password wajib diisi';
                }
            }
            else if ($('#nip').hasClass('is-invalid')) {
                const nipError = $('#nip').next('.invalid-feedback').text();
                if (nipError.includes('14 digit')) {
                    errorMessage = 'NIP harus minimal 14 digit';
                } else if (nipError.includes('tidak valid')) {
                    errorMessage = 'NIP tidak valid. Pastikan digit 9-14 berisi tahun dan bulan yang valid';
                } else if (nipError.includes('hanya boleh berisi angka')) {
                    errorMessage = 'NIP hanya boleh berisi angka';
                }
            }
            else if ($('#username').hasClass('is-invalid')) {
                const usernameError = $('#username').next('.invalid-feedback').text();
                if (usernameError.includes('Username hanya boleh')) {
                    errorMessage = 'Username hanya boleh berisi huruf, angka, dan underscore';
                }
            }
            else if ($('#user_type').hasClass('is-invalid')) {
                errorMessage = 'Pilih tipe user';
            }
            Swal.fire('Error!', errorMessage, 'error');
            return;
        }

        // Jika jenis kuota tahunan aktif, simpan juga data kuota tahunan sebelum submit utama
        if ($('#jenisKuotaSelect').val() === 'tahunan' && $('#quotaTahunanContainer').is(':visible')) {
            const userId = <?php echo $user ? $user['id'] : 0; ?>;
            let dataKuota = [];
            let validQuota = true;
            let errorMsg = '';
            $('#quotaSection .quota-card').each(function() {
                const tahun = $(this).find('input[data-field="kuota_tahunan"]').data('tahun');
                const kuotaTahunan = $(this).find('input[data-field="kuota_tahunan"]').val();
                const sisaKuota = $(this).find('input[data-field="sisa_kuota"]').val();
                if (!kuotaTahunan || !sisaKuota) {
                    validQuota = false;
                    errorMsg = 'Semua field kuota harus diisi';
                    return false;
                }
                if (parseInt(kuotaTahunan) < 0 || parseInt(sisaKuota) < 0) {
                    validQuota = false;
                    errorMsg = 'Kuota tidak boleh negatif';
                    return false;
                }
                if (parseInt(sisaKuota) > parseInt(kuotaTahunan)) {
                    validQuota = false;
                    errorMsg = 'Sisa kuota tidak boleh lebih dari kuota tahunan';
                    return false;
                }
                dataKuota.push({
                    tahun: tahun,
                    kuota_tahunan: kuotaTahunan,
                    sisa_kuota: sisaKuota
                });
            });
            if (!validQuota) {
                Swal.fire('Error!', errorMsg, 'error');
                return;
            }
            // Simpan kuota tahunan dulu, lalu submit utama jika sukses
            $.post(baseUrl('user/updateQuotaAll'), {
                user_id: userId,
                data_kuota: dataKuota
            }, function(response) {
                if (response.success) {
                    submitUserFormUtama();
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            }, 'json').fail(function() {
                Swal.fire('Error!', 'Terjadi kesalahan saat mengupdate kuota', 'error');
            });
        } else {
            submitUserFormUtama();
        }

        function submitUserFormUtama() {
            $.post(baseUrl('user/save'), $('#userForm').serialize(), function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonColor: '#1b5e20'
                    }).then(() => {
                        window.location.href = baseUrl('user/manage');
                    });
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            }, 'json').fail(function(xhr, status, error) {
                console.error('AJAX Error:', status, error, xhr.responseText);
                Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan data', 'error');
            });
        }
    });
    
    // Remove invalid class when user starts typing
    $('input, select').on('input change', function() {
        const fieldId = $(this).attr('id');
        const value = $(this).val().trim();
        
        // Special handling for password field
        if (fieldId === 'password') {
            if (value.length >= 6) {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        } else {
            // For other fields
            if (value) {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        }
    });

    // Real-time validation for username
    $('#username').on('blur', function() {
        const username = $(this).val().trim();
        if (username && username.length >= 3) {
            // Check username availability (you can implement AJAX call here if needed)
            // For now, we'll just do basic validation
            if (!/^[a-zA-Z0-9_]+$/.test(username)) {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">Username hanya boleh berisi huruf, angka, dan underscore</div>');
                }
            }
        }
    });
    
    // Real-time validation for NIP format
    $('#nip').on('blur', function() {
        const nip = $(this).val().trim();
        if (nip && !/^\d+$/.test(nip)) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">NIP hanya boleh berisi angka</div>');
            }
        }
    });

    // Real-time validation for password
    $('#password').on('input', function() {
        const password = $(this).val();
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
        
        if (password.length > 0 && password.length < 6) {
            $(this).addClass('is-invalid');
            $(this).after('<div class="invalid-feedback">Password minimal 6 karakter</div>');
        }
    });

    function loadUserQuota(userId) {
        $.post(baseUrl('user/getUserQuota'), { user_id: userId }, function(response) {
            if (response.success) {
                const data = response.data;
                let html = '';
                data.forEach(function(item) {
                    html += renderQuotaTahunanInput(userId, item.tahun, item.kuota_tahunan, item.sisa_kuota);
                });
                $('#quotaSection').html(html);
            }
        }, 'json');
    }

    // JavaScript functions for NIP date extraction
    function extractTanggalMasukFromNIP(nip) {
        if (nip.length < 14) {
            return '1900-01-01';
        }
        
        const tahun = nip.substring(8, 12); // digit 9-12
        const bulan = nip.substring(12, 14); // digit 13-14
        
        // Validasi tahun dan bulan
        if (!isNumeric(tahun) || !isNumeric(bulan)) {
            return '1900-01-01';
        }
        
        if (parseInt(bulan) < 1 || parseInt(bulan) > 12) {
            return '1900-01-01';
        }
        
        return `${tahun}-${bulan.padStart(2, '0')}-01`;
    }
    
    function formatTanggalMasukFromNIP(nip) {
        const tanggal = extractTanggalMasukFromNIP(nip);
        if (tanggal === '1900-01-01') {
            return 'NIP tidak valid';
        }
        
        const bulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        const date = new Date(tanggal);
        const bulanNum = date.getMonth();
        const tahun = date.getFullYear();
        
        return `${bulan[bulanNum]} ${tahun}`;
    }
    
    function isNumeric(str) {
        return !isNaN(str) && !isNaN(parseFloat(str));
    }

    // Handle quota update
    $(document).on('click', '.update-quota', function() {
        const userId = $(this).data('user-id');
        const tahun = $(this).data('tahun');
        const kuotaTahunan = $(`input[data-user-id="${userId}"][data-tahun="${tahun}"][data-field="kuota_tahunan"]`).val();
        const sisaKuota = $(`input[data-user-id="${userId}"][data-tahun="${tahun}"][data-field="sisa_kuota"]`).val();
        
        // Validasi input
        if (!kuotaTahunan || !sisaKuota) {
            Swal.fire('Error!', 'Semua field kuota harus diisi', 'error');
            return;
        }
        
        if (parseInt(kuotaTahunan) < 0 || parseInt(sisaKuota) < 0) {
            Swal.fire('Error!', 'Kuota tidak boleh negatif', 'error');
            return;
        }
        
        if (parseInt(sisaKuota) > parseInt(kuotaTahunan)) {
            Swal.fire('Error!', 'Sisa kuota tidak boleh lebih dari kuota tahunan', 'error');
            return;
        }

        // Konfirmasi sebelum update
        Swal.fire({
            title: 'Update Kuota?',
            text: `Apakah Anda yakin ingin mengupdate kuota tahun ${tahun}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1b5e20',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Update!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Disable button dan show loading
                const button = $(this);
                const originalText = button.text();
                button.prop('disabled', true).text('Updating...');
                
                $.post(baseUrl('user/updateQuota'), {
                    user_id: userId,
                    tahun: tahun,
                    kuota_tahunan: kuotaTahunan,
                    sisa_kuota: sisaKuota
                }, function(response) {
                    // Re-enable button
                    button.prop('disabled', false).text(originalText);
                    
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: `Kuota tahun ${tahun} berhasil diupdate`,
                            icon: 'success',
                            confirmButtonColor: '#1b5e20'
                        });
                        // Reload quota data to update summary
                        loadUserQuota(userId);
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                }, 'json').fail(function() {
                    // Re-enable button on error
                    button.prop('disabled', false).text(originalText);
                    Swal.fire('Error!', 'Terjadi kesalahan saat mengupdate kuota', 'error');
                });
            }
        });
    });

    // Real-time validation for quota inputs
    $(document).on('input', '.quota-input', function() {
        const userId = $(this).data('user-id');
        const tahun = $(this).data('tahun');
        const field = $(this).data('field');
        
        const kuotaTahunan = $(`input[data-user-id="${userId}"][data-tahun="${tahun}"][data-field="kuota_tahunan"]`).val();
        const sisaKuota = $(`input[data-user-id="${userId}"][data-tahun="${tahun}"][data-field="sisa_kuota"]`).val();
        
        if (kuotaTahunan && sisaKuota) {
            if (parseInt(sisaKuota) > parseInt(kuotaTahunan)) {
                $(this).addClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
                $(this).after('<div class="invalid-feedback">Sisa kuota tidak boleh lebih dari kuota tahunan</div>');
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        }
    });

    window.showQuotaHistory = function(userId) {
        $.post(baseUrl('user/getQuotaHistory'), { user_id: userId }, function(response) {
            if (response.success) {
                const user = response.data.user;
                const history = response.data.history;
                
                let historyHtml = '';
                if (history.length > 0) {
                    history.forEach(item => {
                        const persentase = item.persentase_terpakai;
                        const badgeClass = persentase > 80 ? 'danger' : persentase > 50 ? 'warning' : 'success';
                        
                        historyHtml += `
                            <tr>
                                <td>${item.tahun}</td>
                                <td>${item.kuota_tahunan} hari</td>
                                <td>${item.sisa_kuota} hari</td>
                                <td>${item.kuota_tahunan - item.sisa_kuota} hari</td>
                                <td><span class="badge bg-${badgeClass}">${persentase}%</span></td>
                            </tr>
                        `;
                    });
                } else {
                    historyHtml = '<tr><td colspan="5" class="text-center">Belum ada data kuota</td></tr>';
                }
                
                Swal.fire({
                    title: `Riwayat Kuota - ${user.nama}`,
                    html: `
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tahun</th>
                                        <th>Kuota Tahunan</th>
                                        <th>Sisa Kuota</th>
                                        <th>Kuota Terpakai</th>
                                        <th>Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${historyHtml}
                                </tbody>
                            </table>
                        </div>
                    `,
                    width: '600px',
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#1b5e20'
                });
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        }, 'json');
    }

    // Fungsi untuk memuat kuota berdasarkan jenis yang dipilih
    window.loadKuotaByType = function() {
        const jenisKuota = $('#jenisKuotaSelect').val();
        const userId = <?php echo $user ? $user['id'] : 0; ?>;
        
        // Sembunyikan semua container terlebih dahulu
        $('#quotaTahunanContainer, #quotaBesarContainer, #quotaMelahirkanContainer, #quotaTahunanLainContainer, #quotaAlasanPentingContainer').hide();
        
        if (!jenisKuota) {
            return;
        }
        
        switch (jenisKuota) {
            case 'tahunan':
                $('#quotaTahunanContainer').show();
                loadUserQuota(userId);
                break;
            case 'besar':
                $('#quotaBesarContainer').show();
                loadKuotaBesar(userId);
                break;
            case 'melahirkan':
                $('#quotaMelahirkanContainer').show();
                loadKuotaMelahirkan(userId);
                break;
            case 'sakit':
                $('#quotaTahunanLainContainer').show();
                $('#quotaTahunanLainTahunWrapper').hide();
                $('#quotaTahunanLainKuota').prop('readonly', true);
                updateQuotaTahunanLainTitle(jenisKuota);
                loadKuotaTahunanLain(userId, jenisKuota);
                break;
            case 'alasan_penting':
                // Cuti alasan penting tidak memiliki kuota akumulatif - tampilkan info saja
                $('#quotaAlasanPentingContainer').show();
                break;
            case 'luar_tanggungan':
                $('#quotaTahunanLainContainer').show();
                $('#quotaTahunanLainTahunWrapper').hide();
                $('#quotaTahunanLainKuota').prop('readonly', true).val(365);
                updateQuotaTahunanLainTitle(jenisKuota);
                loadKuotaTahunanLain(userId, jenisKuota);
                break;
        }
    }
    
    // Fungsi untuk memuat kuota cuti besar
    window.loadKuotaBesar = function(userId) {
        $.post(baseUrl('user/getKuotaBesar'), { user_id: userId }, function(response) {
            if (response.success) {
                const data = response.data;
                $('#kuotaBesarTotal').val(data.kuota_total || 0).prop('readonly', true);
                $('#kuotaBesarSisa').val(data.sisa_kuota || 0);
                $('#kuotaBesarStatus').val(data.status || 'belum_berhak');
                $('#kuotaBesarTanggalBerhak').val(data.tanggal_berhak || '');
            } else {
                $('#kuotaBesarTotal').val(0).prop('readonly', true);
                $('#kuotaBesarSisa').val(0);
                $('#kuotaBesarStatus').val('belum_berhak');
                $('#kuotaBesarTanggalBerhak').val('');
            }
        }, 'json');
    }
    
    // Fungsi untuk memuat kuota cuti melahirkan
    window.loadKuotaMelahirkan = function(userId) {
        $.post(baseUrl('user/getKuotaMelahirkan'), { user_id: userId }, function(response) {
            if (response.success) {
                const data = response.data;
                $('#kuotaMelahirkanTotal').val(90).prop('readonly', true);
                $('#kuotaMelahirkanStatus').val(data.status || 'tersedia');
                $('#kuotaMelahirkanTanggalPenggunaan').val(data.tanggal_penggunaan || '');
            } else {
                $('#kuotaMelahirkanTotal').val(90).prop('readonly', true);
                $('#kuotaMelahirkanStatus').val('tersedia');
                $('#kuotaMelahirkanTanggalPenggunaan').val('');
            }
        }, 'json');
    }
    
    // Fungsi untuk memuat kuota tahunan lainnya
    window.loadKuotaTahunanLain = function(userId, jenisKuota) {
        const tahun = $('#quotaTahunanLainTahun').val();
        $.post(baseUrl('user/getKuotaTahunanLain'), { 
            user_id: userId, 
            jenis_kuota: jenisKuota,
            tahun: tahun 
        }, function(response) {
            if (response.success) {
                const data = response.data;
                let kuota = data.kuota_tahunan;
                
                // Set default berdasarkan jenis kuota
                if (jenisKuota === 'sakit') {
                    if (!kuota || kuota == 0) kuota = 14;
                } else {
                    if (!kuota || kuota == 0) kuota = 14;
                }
                
                $('#quotaTahunanLainKuota').val(kuota);
                $('#quotaTahunanLainSisa').val(data.sisa_kuota || 0);
            } else {
                $('#quotaTahunanLainKuota').val(14);
                $('#quotaTahunanLainSisa').val(0);
            }
            // Reset validasi
            $('#quotaTahunanLainSisa').removeClass('is-invalid');
            $('#sisaKuotaInvalidMsg').hide();
            $('#btnUpdateKuotaTahunanLain').prop('disabled', false);
        }, 'json');
    }
    
    // Fungsi untuk update judul kuota tahunan lainnya
    window.updateQuotaTahunanLainTitle = function(jenisKuota) {
        const titles = {
            'sakit': 'Kuota Cuti Sakit',
            'luar_tanggungan': 'Kuota Cuti Luar Tanggungan'
        };
        $('#quotaTahunanLainTitle').text(titles[jenisKuota] || 'Kuota Cuti');
        // Sembunyikan info box (tidak ada info khusus untuk sakit/luar tanggungan)
        $('#quotaTahunanLainInfo').hide();
    }
    
    // Fungsi untuk update kuota cuti besar
    window.updateKuotaBesar = function() {
        const userId = <?php echo $user ? $user['id'] : 0; ?>;
        const data = {
            user_id: userId,
            kuota_total: $('#kuotaBesarTotal').val(),
            sisa_kuota: $('#kuotaBesarSisa').val(),
            status: $('#kuotaBesarStatus').val(),
            tanggal_berhak: $('#kuotaBesarTanggalBerhak').val()
        };
        
        $.post(baseUrl('user/updateKuotaBesar'), data, function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Kuota cuti besar berhasil diupdate',
                    icon: 'success',
                    confirmButtonColor: '#1b5e20'
                });
            } else {
                Swal.fire('Gagal!', response.message, 'error');
            }
        }, 'json');
    }
    
    // Fungsi untuk update kuota cuti melahirkan
    window.updateKuotaMelahirkan = function() {
        const userId = <?php echo $user ? $user['id'] : 0; ?>;
        const data = {
            user_id: userId,
            kuota_total: 90,
            sisa_kuota: 90,
            status: $('#kuotaMelahirkanStatus').val(),
            tanggal_penggunaan: $('#kuotaMelahirkanTanggalPenggunaan').val(),
            jumlah_pengambilan_melahirkan: $('#jumlahPengambilanMelahirkan').val()
        };
        
        $.post(baseUrl('user/updateKuotaMelahirkan'), data, function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Status cuti melahirkan berhasil diupdate',
                    icon: 'success',
                    confirmButtonColor: '#1b5e20'
                });
            } else {
                Swal.fire('Gagal!', response.message, 'error');
            }
        }, 'json');
    }
    
    // Fungsi untuk update kuota tahunan lainnya
    window.updateKuotaTahunanLain = function() {
        const userId = <?php echo $user ? $user['id'] : 0; ?>;
        const jenisKuota = $('#jenisKuotaSelect').val();
        
        // Untuk alasan penting, tidak ada kuota akumulatif
        if (jenisKuota === 'alasan_penting') {
            Swal.fire({
                icon: 'info',
                title: 'Tidak Diperlukan',
                text: 'Cuti Alasan Penting tidak memiliki kuota akumulatif. Tidak ada yang perlu diupdate.',
                confirmButtonColor: '#1b5e20'
            });
            return;
        }
        
        let kuotaTahunan = $('#quotaTahunanLainKuota').val();
        
        const data = {
            user_id: userId,
            jenis_kuota: jenisKuota,
            tahun: $('#quotaTahunanLainTahun').val(),
            kuota_tahunan: kuotaTahunan,
            sisa_kuota: $('#quotaTahunanLainSisa').val()
        };
        
        $.post(baseUrl('user/updateKuotaTahunanLain'), data, function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Kuota berhasil diupdate',
                    icon: 'success',
                    confirmButtonColor: '#1b5e20'
                });
            } else {
                Swal.fire('Gagal!', response.message, 'error');
            }
        }, 'json');
    }
    
    // Event handler untuk perubahan tahun pada kuota tahunan lainnya
    $('#quotaTahunanLainTahun').on('change', function() {
        const userId = <?php echo $user ? $user['id'] : 0; ?>;
        const jenisKuota = $('#jenisKuotaSelect').val();
        if (jenisKuota && ['sakit', 'alasan_penting', 'luar_tanggungan'].includes(jenisKuota)) {
            loadKuotaTahunanLain(userId, jenisKuota);
        }
    });

    // Validasi real-time untuk sisa kuota cuti sakit
    $('#quotaTahunanLainSisa').on('input', function() {
        const sisa = parseInt($(this).val(), 10);
        const kuota = parseInt($('#quotaTahunanLainKuota').val(), 10);
        if (sisa > kuota) {
            $(this).addClass('is-invalid');
            $('#sisaKuotaInvalidMsg').show();
            $('#btnUpdateKuotaTahunanLain').prop('disabled', true);
        } else {
            $(this).removeClass('is-invalid');
            $('#sisaKuotaInvalidMsg').hide();
            $('#btnUpdateKuotaTahunanLain').prop('disabled', false);
        }
    });

    // Modifikasi render input kuota tahunan pada quota tahunan agar readonly
    function renderQuotaTahunanInput(userId, tahun, kuotaTahunan, sisaKuota) {
        return `
            <div class="col-md-4 mb-3">
                <div class="card quota-card">
                    <div class="card-header">
                        <strong>${tahun == new Date().getFullYear() ? 'Tahun Ini' : (tahun == new Date().getFullYear() - 1 ? 'Tahun Lalu' : '2 Tahun Lalu')} (${tahun})</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label">Kuota Tahunan</label>
                            <input type="number" class="form-control quota-input" data-user-id="${userId}" data-tahun="${tahun}" data-field="kuota_tahunan" value="${kuotaTahunan}" readonly>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Sisa Kuota</label>
                            <input type="number" class="form-control quota-input" data-user-id="${userId}" data-tahun="${tahun}" data-field="sisa_kuota" value="${sisaKuota}" min="0">
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Sembunyikan tombol 'Lihat Riwayat Kuota' jika bukan cuti tahunan
    // Tambahkan satu tombol Update Kuota di bawah quotaSection
    $(document).on('click', '#btnUpdateAllQuota', function() {
        const userId = <?php echo $user ? $user['id'] : 0; ?>;
        let dataKuota = [];
        let valid = true;
        let errorMsg = '';
        $('#quotaSection .quota-card').each(function() {
            const tahun = $(this).find('input[data-field="kuota_tahunan"]').data('tahun');
            const kuotaTahunan = $(this).find('input[data-field="kuota_tahunan"]').val();
            const sisaKuota = $(this).find('input[data-field="sisa_kuota"]').val();
            if (!kuotaTahunan || !sisaKuota) {
                valid = false;
                errorMsg = 'Semua field kuota harus diisi';
                return false;
            }
            if (parseInt(kuotaTahunan) < 0 || parseInt(sisaKuota) < 0) {
                valid = false;
                errorMsg = 'Kuota tidak boleh negatif';
                return false;
            }
            if (parseInt(sisaKuota) > parseInt(kuotaTahunan)) {
                valid = false;
                errorMsg = 'Sisa kuota tidak boleh lebih dari kuota tahunan';
                return false;
            }
            dataKuota.push({
                tahun: tahun,
                kuota_tahunan: kuotaTahunan,
                sisa_kuota: sisaKuota
            });
        });
        if (!valid) {
            Swal.fire('Error!', errorMsg, 'error');
            return;
        }
        Swal.fire({
            title: 'Update Kuota?',
            text: 'Apakah Anda yakin ingin mengupdate seluruh kuota tahunan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1b5e20',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Update!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const button = $('#btnUpdateAllQuota');
                const originalText = button.text();
                button.prop('disabled', true).text('Updating...');
                $.post(baseUrl('user/updateQuotaAll'), {
                    user_id: userId,
                    data_kuota: dataKuota
                }, function(response) {
                    button.prop('disabled', false).text(originalText);
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Seluruh kuota tahunan berhasil diupdate',
                            icon: 'success',
                            confirmButtonColor: '#1b5e20'
                        });
                        loadUserQuota(userId);
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                }, 'json').fail(function() {
                    button.prop('disabled', false).text(originalText);
                    Swal.fire('Error!', 'Terjadi kesalahan saat mengupdate kuota', 'error');
                });
            }
        });
    });

    // ===== Auto-detect direct superior based on position (jabatan) =====
    function updateAutoSuperior() {
        const jabatan = $('#jabatan').val().trim();
        const userType = $('#user_type').val();
        const autoSuperiorInfo = $('#autoSuperiorInfo');

        // Hide all alerts first
        autoSuperiorInfo.find('.alert').addClass('d-none');

        if (!jabatan || userType !== 'pegawai') {
            if (userType === 'atasan' || userType === 'admin') {
                autoSuperiorInfo.find('#nonEmployeeAlert').removeClass('d-none');
            }
            return;
        }

        // Show loading state
        autoSuperiorInfo.find('.alert-info').addClass('d-none');

        // Fetch auto superior from server
        $.post(baseUrl('user/getAutoDirectSuperior'), {
            jabatan: jabatan,
            user_type: userType
        }, function(response) {
            if (response.success && response.atasan_id) {
                // Update the select field with the auto-detected superior
                $('#atasan').val(response.atasan_id);

                // Display the auto superior information
                $('#autoSuperiorName').text(response.atasan_nama);
                $('#autoSuperiorNip').text(response.atasan_nip);
                $('#autoSuperiorJabatan').text(response.atasan_jabatan);

                autoSuperiorInfo.find('.alert-info').removeClass('d-none');
            } else if (!response.atasan_id && userType === 'pegawai') {
                // No suitable superior found
                autoSuperiorInfo.find('#noAutoSuperiorAlert').removeClass('d-none');
                $('#atasan').val('');
            } else {
                // For non-employee types
                autoSuperiorInfo.find('#nonEmployeeAlert').removeClass('d-none');
            }
        }, 'json').fail(function() {
            console.error('Failed to fetch auto superior');
            autoSuperiorInfo.find('#noAutoSuperiorAlert').removeClass('d-none');
        });
    }

    // Event listeners for auto-detect
    $('#jabatan').on('change blur', function() {
        updateAutoSuperior();
    });

    $('#user_type').on('change', function() {
        updateAutoSuperior();
    });

    // Trigger on page load if editing
    if ('<?php echo $action; ?>' === 'edit' && $('#jabatan').val().trim()) {
        updateAutoSuperior();
    }

    $('#jenisKuotaSelect').on('change', function() {
        if ($(this).val() === 'tahunan') {
            $("button[onclick^='showQuotaHistory']").show();
        } else {
            $("button[onclick^='showQuotaHistory']").hide();
        }
    });
    // Inisialisasi tampilan tombol saat load
    if ($('#jenisKuotaSelect').val() === 'tahunan') {
        $("button[onclick^='showQuotaHistory']").show();
    } else {
        $("button[onclick^='showQuotaHistory']").hide();
    }

    // Validasi jumlah hari cuti melahirkan sebelum update
    $(document).on('input', '#jumlahPengambilanMelahirkan', function() {
        const maxMelahirkan = 3; // Maksimal pengambilan cuti melahirkan
        let val = parseInt($(this).val(), 10);
        if (val > maxMelahirkan) {
            $(this).val(maxMelahirkan);
            Swal.fire('Error!', 'Jumlah pengambilan cuti melahirkan tidak boleh lebih dari 3 kali selama masa kerja.', 'error');
        } else if (val < 0 || isNaN(val)) {
            $(this).val(0);
        }
    });

    // Validasi jumlah hari cuti melahirkan (jumlah hari per pengambilan tidak boleh lebih dari 90)
    $(document).on('click', 'button[onclick="updateKuotaMelahirkan()"]', function(e) {
        const maxHariMelahirkan = 90;
        // Jika ada field jumlah hari cuti melahirkan, validasi di sini (misal jika nanti ada input jumlah hari)
        // Jika hanya jumlah pengambilan, validasi sudah di atas
        // Jika ingin validasi field lain, tambahkan di sini
        // Saat ini, validasi jumlah pengambilan sudah cukup
    });
});
</script>
