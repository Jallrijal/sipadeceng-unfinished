<?php
require_once dirname(dirname(__DIR__)) . '/helpers/signature_helper.php';
$hasSignature = getUserSignature($_SESSION['user_id'], 'user');
if (!$hasSignature):
?>
<div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    Anda belum mengunggah gambar tanda tangan. Silakan unggah tanda tangan Anda <a href="<?= baseUrl('signature') ?>" class="alert-link">di sini</a>.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Welcome Banner -->
<div class="welcome-banner">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h3>Selamat Datang, <?php echo $_SESSION['nama']; ?>!</h3>
            <p class="mb-0">Kelola pengajuan cuti Anda dengan mudah melalui sistem ini</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="<?php echo baseUrl('leave/form'); ?>" class="btn btn-light mt-3 mt-md-0 btn-ajukan-tablet">
                <i class="bi bi-plus-circle me-2"></i>Ajukan Cuti Baru
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body">
                <h6 class="text-white-50">Total Pengajuan</h6>
                <h2 class="mb-0" id="totalPengajuan">0</h2>
                <i class="bi bi-file-earmark-text stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-warning text-white">
            <div class="card-body">
                <h6 class="text-white-50">Menunggu Persetujuan</h6>
                <h2 class="mb-0" id="pendingCount">0</h2>
                <i class="bi bi-hourglass-split stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-success text-white">
            <div class="card-body">
                <h6 class="text-white-50">Selesai</h6>
                <h2 class="mb-0" id="completedCount">0</h2>
                <i class="bi bi-check-circle stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-info text-white">
            <div class="card-body">
                <h6 class="text-white-50">Menunggu Dokumen</h6>
                <h2 class="mb-0" id="waitingDocCount">0</h2>
                <i class="bi bi-file-earmark-text stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-secondary text-white">
            <div class="card-body">
                <h6 class="text-white-50">Disetujui</h6>
                <h2 class="mb-0" id="approvedCount">0</h2>
                <i class="bi bi-check2 stat-icon"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quota Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-check me-2"></i>Kuota Cuti Tersedia
                </h5>
            </div>
            <div class="card-body">
                <div id="quotaContainer">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data kuota cuti...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities & Quick Guide -->
<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Aktivitas Terbaru</h6>
                <a href="<?php echo baseUrl('leave'); ?>" class="text-decoration-none">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="timeline" id="recentActivities">
                    <!-- Activities will be loaded here -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Panduan Cepat</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="<?php echo baseUrl('leave/form'); ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-1-circle text-primary me-2"></i>
                        Ajukan cuti baru
                    </a>
                    <a href="<?php echo baseUrl('leave'); ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-2-circle text-primary me-2"></i>
                        Cek status pengajuan
                    </a>
                    <!--<a href="<?php echo baseUrl('leave/documents'); ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-3-circle text-primary me-2"></i>
                        Download formulir
                    </a>-->
                </div>
                
                <div class="alert alert-info mt-3" role="alert">
                    <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Info</h6>
                    <small>Pengajuan cuti harus dilakukan minimal 3 hari kerja sebelum tanggal cuti yang diinginkan.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .welcome-banner {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
        position: relative;
    }
    
    .quota-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .quota-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        position: relative;
    }
    
    .quota-circle .number {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--primary-color);
    }
    
    .quota-circle .label {
        position: absolute;
        bottom: -25px;
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 9px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -21px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--primary-color);
        border: 2px solid white;
        box-shadow: 0 0 0 3px #f8f9fa;
    }
    
    .stat-icon {
        font-size: 3rem;
        opacity: 0.3;
        position: absolute;
        right: 20px;
        top: 20px;
    }
    
    /* Tombol Ajukan Cuti di tablet (600px-1024px) pojok kanan atas dalam kotak hijau */
    @media (min-width: 600px) and (max-width: 1024px) {
        .welcome-banner .row {
            display: block !important;
        }
        .welcome-banner .btn-ajukan-tablet {
            position: absolute;
            top: 20px;
            right: 30px;
            z-index: 2;
            margin-top: 0 !important;
        }
        .welcome-banner .col-md-4.text-md-end {
            position: static !important;
            text-align: right !important;
        }
    }
</style>

<script>
$(document).ready(function() {
    // Load statistics
    loadStatistics();
    
    // Load leave quotas
    loadLeaveQuotas();
    
    // Load recent activities
    loadRecentActivities();
    
    function loadStatistics() {
        $.post(baseUrl('user/getStatistics'), function(response) {
            if (response.success) {
                const data = response.data;
                
                // Update statistics cards
                $('#totalPengajuan').text(data.total_pengajuan || 0);
                $('#pendingCount').text(data.pending || 0);
                $('#approvedCount').text(data.approved || 0);
                $('#rejectedCount').text(data.rejected || 0);
                $('#completedCount').text(data.completed || 0);
                $('#menungguDokumen').text(data.menunggu_dokumen || 0);
                
                // Update annual leave quota
                $('#sisaCuti').text(data.sisa_cuti || 0);
                
                // Show alert if quota is low
                if (data.sisa_cuti <= 3) {
                    $('#quotaAlert').html(`
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Sisa kuota cuti tahunan Anda tinggal <strong>${data.sisa_cuti} hari</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);
                }
            }
        }, 'json');
    }
    
    function loadLeaveQuotas() {
        $.post(baseUrl('user/getLeaveQuotas'), function(response) {
            if (response.success) {
                const quotas = response.data;
                let html = '<div class="row">';
                
                quotas.forEach(function(quota) {
                    let badgeClass = quota.is_akumulatif ? 'success' : 'info';
                    let badgeText = quota.is_akumulatif ? (quota.id == 3 ? 'Pertahun' : 'Akumulatif') : 'Tidak Akumulatif';
                    let iconClass = quota.is_akumulatif ? 'bi-arrow-repeat' : 'bi-calendar-x';
                    let cardClass = `card h-100 border-${badgeClass}`;
                    let headerClass = `card-header bg-${badgeClass} bg-opacity-10 border-${badgeClass}`;
                    let numberClass = `text-${badgeClass} mb-0`;
                    // Jika cuti sakit dan kuota minus, beri warna merah
                    if (quota.id == 3 && quota.kuota_tersedia < 0) {
                        cardClass = 'card h-100 border-danger';
                        headerClass = 'card-header bg-danger bg-opacity-10 border-danger';
                        numberClass = 'text-danger mb-0';
                    }
                    html += `
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="${cardClass}">
                                <div class="${headerClass}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">${quota.nama_cuti}</h6>
                                        <span class="badge bg-${badgeClass}">
                                            <i class="bi ${iconClass} me-1"></i>${badgeText}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <h3 class="${numberClass}">${quota.kuota_tersedia}</h3>
                                        <small class="text-muted">hari tersedia</small>
                                    </div>
                                    <p class="card-text small text-muted mb-2">
                                        <i class="bi bi-info-circle me-1"></i>${quota.keterangan}
                                    </p>
                                    ${quota.deskripsi ? `<p class="card-text small">${quota.deskripsi}</p>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                $('#quotaContainer').html(html);
            } else {
                $('#quotaContainer').html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Gagal memuat data kuota cuti
                    </div>
                `);
            }
        }, 'json');
    }
    
    function loadRecentActivities() {
        $.post(baseUrl('user/getRecentActivities'), function(response) {
            if (response.success && response.data.length > 0) {
                let html = '';
                response.data.forEach(function(item) {
                    html += `
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">${item.nama_cuti}</h6>
                                    <p class="mb-1 text-muted">${item.tanggal_mulai} - ${item.tanggal_selesai} (${item.jumlah_hari} hari)</p>
                                    <p class="mb-0">${item.status_badge}</p>
                                </div>
                                <small class="text-muted">${item.created_at_formatted}</small>
                            </div>
                        </div>
                    `;
                });
                $('#recentActivities').html(html);
            } else {
                $('#recentActivities').html('<p class="text-muted text-center">Belum ada aktivitas</p>');
            }
        }, 'json');
    }
});
</script>
