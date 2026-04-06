<?php
require_once dirname(dirname(__DIR__)) . '/helpers/signature_helper.php';
// Inject role flags for JS
$isAtasan = function_exists('isAtasan') && isAtasan();
$isPimpinan = function_exists('isAdmin') && isAdmin();
// Role flag: kasubbag (used to show pending_kasubbag to kasubbag atasan)
$isKasubbag = function_exists('isKasubbag') && isKasubbag();
// Get atasan role for other roles (kabag, sekretaris)
$atasanRole = null;
if ($isAtasan) {
    $atasanRole = isset($_SESSION['atasan_role']) ? $_SESSION['atasan_role'] : null;
}
$isKabag = ($isAtasan && $atasanRole === 'kabag');
$isSekretaris = ($isAtasan && $atasanRole === 'sekretaris');
// khusus flag untuk atasan ketua (final approver)
$isKetua = function_exists('isKetua') && isKetua();
// Flag untuk role khusus yang perlu melihat semua pengajuan bawahan
$isSpecialRole = $isKasubbag || $isKabag || $isSekretaris || $isKetua;
?>
<script>
    window.IS_ATASAN = <?php echo $isAtasan ? 'true' : 'false'; ?>;
    window.IS_PIMPINAN = <?php echo $isPimpinan ? 'true' : 'false'; ?>;
    window.IS_ADMIN = window.IS_PIMPINAN;
    window.IS_KASUBBAG = <?php echo $isKasubbag ? 'true' : 'false'; ?>;
    window.IS_KABAG = <?php echo $isKabag ? 'true' : 'false'; ?>;
    window.IS_SEKRETARIS = <?php echo $isSekretaris ? 'true' : 'false'; ?>;
    window.IS_KETUA = <?php echo $isKetua ? 'true' : 'false'; ?>;
    window.IS_SPECIAL_ROLE = <?php echo $isSpecialRole ? 'true' : 'false'; ?>;
</script>
<!-- Debug Info (remove in production) -->
<div id="debugInfo" class="alert alert-info d-none" role="alert">
    <strong>Debug Info:</strong> <span id="debugMessage"></span>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
            <h5 class="card-title mb-0">Daftar Pengajuan Cuti</h5>
            <div>
                <select class="form-select form-select-sm" id="statusFilter" style="min-width: 150px;">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu Atasan</option>
                    <option value="pending_kasubbag">Menunggu Kasubbag</option>
                    <option value="pending_kabag">Menunggu Kabag</option>
                    <option value="pending_sekretaris">Menunggu Sekretaris</option>
                    <option value="pending_admin_upload">Menunggu Dokumen</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                    <option value="changed">Perlu Perubahan</option>
                    <option value="postponed">Ditangguhkan</option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover" id="persetujuanTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Nama</th>
                        <th>Unit Kerja</th>
                        <th>Jenis Cuti</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat data...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal untuk Detail Cuti -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pengajuan Cuti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Approval -->
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvalTitle">Proses Pengajuan Cuti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="approvalForm">
                    <input type="hidden" id="leaveId" name="leave_id">
                    <input type="hidden" id="approvalAction" name="action">
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan <span class="text-danger" id="catatanRequired">*</span></label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Masukkan catatan persetujuan/penolakan"></textarea>
                    </div>
                    <!-- Input jumlah hari ditangguhkan, hanya tampil jika aksi tangguhkan -->
                    <div class="mb-3 d-none" id="jumlahHariDitangguhkanGroup">
                        <label for="jumlahHariDitangguhkan" class="form-label">Jumlah Hari Ditangguhkan <span class="text-danger">*</span></label>
                        <input type="number" min="1" class="form-control" id="jumlahHariDitangguhkan" name="jumlah_hari_ditangguhkan" placeholder="Masukkan jumlah hari yang ditangguhkan">
                        <div class="form-text">Hari yang ditangguhkan akan ditambahkan ke kuota tahun berikutnya.</div>
                    </div>
                    <!-- Forward routing untuk Kasubbag -->
                    <div class="mb-3 d-none" id="forwardRoutingGroup">
                        <label for="forwardToRole" class="form-label">Teruskan ke <span class="text-danger">*</span></label>
                        <div class="btn-group d-flex" role="group">
                            <input type="radio" class="btn-check" name="forward_to_role" id="forwardToKabag" value="kabag">
                            <label class="btn btn-outline-primary flex-fill" for="forwardToKabag">
                                Kepala Bagian (Kabag)
                            </label>
                            <input type="radio" class="btn-check" name="forward_to_role" id="forwardToSekretaris" value="sekretaris">
                            <label class="btn btn-outline-primary flex-fill" for="forwardToSekretaris">
                                Sekretaris
                            </label>
                        </div>
                        <div class="form-text mt-2">Pilih atasan untuk meneruskan pengajuan cuti ini.</div>
                    </div>
                    <!-- Pilih Pimpinan (Ketua/Wakil/PLH) untuk Sekretaris -->
                    <div class="mb-3 d-none" id="pimpinanSelectGroup">
                        <label for="pimpinanSelect" class="form-label">Pilih Pimpinan (Ketua) <span class="text-danger">*</span></label>
                        <select class="form-select" id="pimpinanSelect" name="forward_to_ketua_id">
                            <option value="">-- Pilih Pimpinan --</option>
                        </select>
                        <div class="form-text mt-2">Pilih Pimpinan yang akan menyetujui pengajuan final.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submitApproval">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

<style>
    .filter-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    #persetujuanTable_wrapper {
        margin-top: 10px;
    }
    
    .dataTables_length, .dataTables_filter {
        margin-bottom: 10px;
    }

    @media (min-width: 577px) and (max-width: 1024px) {
        .table-responsive {
            overflow-x: auto !important;
            width: 100vw !important;
            max-width: 100vw !important;
            min-width: 100vw !important;
            margin-left: -16px !important;
            margin-right: -16px !important;
            box-sizing: border-box !important;
            padding: 0 !important;
        }
        #persetujuanTable {
            min-width: 1100px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        #persetujuanTable th, #persetujuanTable td {
            white-space: nowrap !important;
            padding: 0.5rem 0.75rem !important;
            text-align: left !important;
            font-size: 1rem !important;
        }
    }
    /* Mobile-only: make DataTables search and length match #statusFilter width */
    @media (max-width: 576px) {
        /* Fix double click issue on mobile */
        .table-hover tbody tr:hover { background: transparent; }

        /* Match status filter width (use same min and explicit width for consistency) */
        #statusFilter {
            min-width: 150px;
            width: 150px;
        }

        /* Target DataTables controls inside the table wrapper and force widths */
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length {
            display: inline-block !important;
            vertical-align: middle !important;
            min-width: 150px !important;
            width: 150px !important;
            box-sizing: border-box !important;
        }

        /* Ensure labels wrap and inputs/selects fill the container */
        .dataTables_wrapper .dataTables_filter label,
        .dataTables_wrapper .dataTables_length label {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
        }

        .dataTables_wrapper .dataTables_filter input[type="search"] {
            width: 100% !important;
            min-width: 0 !important;
        }

        .dataTables_wrapper .dataTables_length select {
            width: 100% !important;
            min-width: 0 !important;
        }

        /* Small tweak to avoid overflow when many controls present */
        .dataTables_wrapper .dataTables_filter + .dataTables_length,
        .dataTables_wrapper .dataTables_length + .dataTables_filter {
            margin-left: .5rem !important;
        }
    }

    /* Memperbaiki masalah double click di mobile */
    @media (max-width: 768px) {
        .modal-open {
            overflow: auto !important;
            padding-right: 0 !important;
        }
        .modal-backdrop {
            display: none !important;
        }
        .modal {
            background-color: rgba(0, 0, 0, 0.5) !important;
        }
        
        /* Fix detail modal content alignment on mobile - make it left-aligned */
        #detailContent * {
            text-align: left !important;
        }
        
        #detailContent {
            direction: ltr !important;
            text-align: left !important;
        }
        
        #detailContent .row {
            display: block !important;
        }
        
        #detailContent .col-md-6 {
            display: block !important;
            width: 100% !important;
            margin-bottom: 20px !important;
            text-align: left !important;
        }
        
        #detailContent table {
            width: 100% !important;
        }
        
        #detailContent table tr {
            display: table-row !important;
        }
        
        #detailContent table td {
            display: table-cell !important;
            text-align: left !important;
            padding: 0.5rem !important;
            justify-content: flex-start !important;
            align-items: flex-start !important;
            width: auto !important;
            flex-basis: auto !important;
        }
        
        #detailContent table td:before {
            display: none !important;
        }
        
        #detailContent h6 {
            text-align: left !important;
            margin-top: 15px !important;
        }
        
        #detailContent p {
            text-align: left !important;
            margin: 0.5rem 0 !important;
        }
        
        #detailContent .badge,
        #detailContent .alert {
            text-align: left !important;
        }
        
        #detailContent div {
            text-align: left !important;
        }
    }
</style>

<script>
// Ensure baseUrl is available
if (typeof baseUrl === 'undefined') {
    function baseUrl(url = '') {
        const base = window.BASE_URL || '';
        return base + url;
    }
}

// Variabel global untuk tracking update
let isUpdating = false;

$(document).ready(function() {
    initializeApprovalPage();
});

function initializeApprovalPage() {
    console.log('Initializing approval page...');
    console.log('Is Special Role:', window.IS_SPECIAL_ROLE);
    
    // Load initial data
    loadPersetujuanData();
    
    // Filter change
    $('#statusFilter').change(function() {
        console.log('Status filter changed to:', $(this).val());
        loadPersetujuanData($(this).val());
    });
    
    // Submit approval
    $('#submitApproval').off('click').on('click', function() {
        const action = $('#approvalAction').val();
        const catatan = $('#catatan').val();
        const leaveId = $('#leaveId').val();
        let jumlahHariDitangguhkan = 0;
        if (['reject_leave','change_leave','postpone_leave'].includes(action) && !catatan) {
            Swal.fire('Error', 'Catatan wajib diisi untuk aksi ini!', 'error');
            return;
        }
        if (action === 'postpone_leave') {
            jumlahHariDitangguhkan = parseInt($('#jumlahHariDitangguhkan').val(), 10) || 0;
            if (jumlahHariDitangguhkan < 1) {
                Swal.fire('Error', 'Jumlah hari ditangguhkan wajib diisi dan minimal 1!', 'error');
                return;
            }
        }
        // Validasi forward_to_role untuk kasubbag approval
        let forwardToRole = null;
        if (action === 'approve_leave' && !$('#forwardRoutingGroup').hasClass('d-none')) {
            forwardToRole = $('input[name="forward_to_role"]:checked').val();
            if (!forwardToRole) {
                Swal.fire('Error', 'Pilih atasan untuk meneruskan pengajuan (Kabag atau Sekretaris)!', 'error');
                return;
            }
        }
        console.log('Submitting approval:', { action, catatan, leaveId, jumlahHariDitangguhkan, forwardToRole });
        // Store the leave ID for later use
        const currentLeaveId = leaveId;
        // Disable submit button and show loading
        const $submitBtn = $('#submitApproval');
        const originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
        const postData = {
            action: action,
            leave_id: leaveId,
            catatan: catatan,
            jumlah_hari_ditangguhkan: jumlahHariDitangguhkan
        };
        if (forwardToRole) {
            postData.forward_to_role = forwardToRole;
        }
        // If sekretaris selected a specific ketua, include it
        const forwardToKetuaId = $('#pimpinanSelect').length ? $('#pimpinanSelect').val() : null;
        if (forwardToKetuaId) {
            postData.forward_to_ketua_id = forwardToKetuaId;
        }
        $.post(baseUrl('approval/process'), postData, function(response) {
            console.log('Approval response:', response);
            
            // Re-enable button
            $submitBtn.prop('disabled', false).text(originalText);
            
            if (response.success) {
                // Close modal immediately
                $('#approvalModal').modal('hide');
                $('#approvalForm')[0].reset();
                
                // Show success message with timer
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
                
                // Update the row immediately without reloading all data
                setTimeout(function() {
                    updateRowAfterApproval(currentLeaveId, action);
                }, 100);
                
                // Fallback: reload data setelah 3 detik untuk memastikan konsistensi
                setTimeout(function() {
                    console.log('Reloading data for consistency');
                    loadPersetujuanData($('#statusFilter').val());
                }, 3000);
            } else {
                Swal.fire('Error', response.message || 'Terjadi kesalahan', 'error');
            }
        }, 'json').fail(function(xhr, status, error) {
            console.error('AJAX Error:', error);
            console.error('Response:', xhr.responseText);
            
            // Re-enable button on error
            $submitBtn.prop('disabled', false).text(originalText);
            
            Swal.fire('Error', 'Terjadi kesalahan saat memproses data', 'error');
        });
    });
}

/**
 * Helper function to translate status code to display text
 */
function getStatusDisplay(statusCode) {
    const statusMap = {
        'pending': 'Menunggu Atasan',
        'pending_kasubbag': 'Menunggu Kasubbag',
        'pending_kabag': 'Menunggu Kabag',
        'pending_sekretaris': 'Menunggu Sekretaris',
        'pending_admin_upload': 'Menunggu Dokumen',
        'awaiting_pimpinan': 'Menunggu Pimpinan',
        'approved': 'Disetujui',
        'rejected': 'Ditolak',
        'changed': 'Perlu Perubahan',
        'postponed': 'Ditangguhkan'
    };
    return statusMap[statusCode] || statusCode;
}

/**
 * Helper function to render table rows untuk kabag dan non-kabag
 */


function loadPersetujuanData(status = '') {
    console.log('Loading persetujuan data with status:', status);
    console.log('Using URL:', baseUrl('leave/getHistory'));
    
    $.post(baseUrl('leave/getHistory'), {
        status: status
    }, function(response) {
        console.log('Response received:', response);
        
        if (response.success) {
            // Update kasubbag/ketua flags from server (session-aware) so UI logic can rely on them
            if (typeof response.is_kasubbag !== 'undefined') {
                window.IS_KASUBBAG = response.is_kasubbag;
                console.log('Updated window.IS_KASUBBAG from server:', window.IS_KASUBBAG);
            }
            if (typeof response.is_ketua !== 'undefined') {
                window.IS_KETUA = response.is_ketua;
                console.log('Updated window.IS_KETUA from server:', window.IS_KETUA);
            }
            if (typeof response.is_admin !== 'undefined') {
                window.IS_PIMPINAN = response.is_admin;
                console.log('Updated window.IS_PIMPINAN (admin) from server:', window.IS_PIMPINAN);
            }
            const tbody = $('#persetujuanTable tbody');
            tbody.empty();
            
            if (!response.data || response.data.length === 0) {
                tbody.append('<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>');
                return;
            }
            
            response.data.forEach(function(item, index) {
                // Kolom aksi: tombol detail + aksi role-specific
                let actions = `
                    <button class="btn btn-sm btn-info me-1" onclick="viewDetail(${item.id})" title="Lihat Detail">
                        <i class="bi bi-eye"></i>
                    </button>
                `;

                // Tambahkan tombol download blanko yang digenerate sistem jika flag dari server menyatakan boleh
                if (item.has_generated_doc && item.can_download_generated) {
                    const genHref = baseUrl('leave/downloadGeneratedDoc?id=' + item.id);
                    console.log('Rendering generated-download button for leave', item.id, 'href=', genHref, 'has_generated_doc=', item.has_generated_doc, 'filename=', item.generated_doc_filename, 'can_download_generated=', item.can_download_generated);
                    actions += `
                        <a href="${genHref}" 
                           class="btn btn-sm btn-primary btn-action btn-download-generated me-1" title="Download Blanko" target="_blank" rel="noopener">
                            <i class="bi bi-file-earmark-arrow-down"></i>
                        </a>
                    `;
                }

                // Role-based quick actions
                if (typeof window.IS_ATASAN !== 'undefined' && window.IS_ATASAN) {
                    // Atasan (including kasubbag, kabag, sekretaris, ketua) dapat memproses pengajuan tertentu
                    // Sebagai atasan langsung (level 1 approval): status 'pending'
                    // Sebagai kasubbag (level 2): status 'pending_kasubbag'
                    if ((item.status === 'pending' || (item.status === 'pending_kasubbag' && window.IS_KASUBBAG)) && (item.blanko_uploaded || item.has_generated_doc || window.IS_ATASAN)) {
                        actions += `
                            <button class="btn btn-sm btn-primary me-1" onclick="approveLeave(${item.id})" title="Setujui">
                                <i class="bi bi-arrow-up-right-circle"></i>
                            </button>
                        `;
                        // Tambah tombol tolak cepat
                        actions += `
                            <button class="btn btn-sm btn-danger me-1" onclick="rejectLeave(${item.id})" title="Tolak">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        `;
                        // Untuk level 1 (atasan langsung), tambahkan change dan postpone
                        if (item.status === 'pending') {
                            actions += `
                                <button class="btn btn-sm btn-warning me-1" onclick="changeLeave(${item.id})" title="Perlu Perubahan">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-secondary me-1" onclick="postponeLeave(${item.id})" title="Tangguhkan">
                                    <i class="bi bi-pause"></i>
                                </button>
                            `;
                        }
                    }
                    // Kabag dapat memproses pengajuan dengan status pending_kabag (level 3) - hanya approve dan reject
                    else if (typeof window.IS_KABAG !== 'undefined' && window.IS_KABAG && item.status === 'pending_kabag') {
                        actions += `
                            <button class="btn btn-sm btn-primary me-1" onclick="approveLeave(${item.id})" title="Teruskan ke Sekretaris">
                                <i class="bi bi-arrow-up-right-circle"></i>
                            </button>
                        `;
                        actions += `
                            <button class="btn btn-sm btn-danger me-1" onclick="rejectLeave(${item.id})" title="Tolak">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        `;
                    }
                    // Ketua dapat memproses pengajuan yang berstatus awaiting_pimpinan (level 5)
                    else if (typeof window.IS_KETUA !== 'undefined' && window.IS_KETUA && item.status === 'awaiting_pimpinan') {
                        actions += `
                            <button class="btn btn-sm btn-success me-1" onclick="approveLeave(${item.id})" title="Setujui">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        `;
                        actions += `
                            <button class="btn btn-sm btn-danger me-1" onclick="rejectLeave(${item.id})" title="Tolak">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        `;
                        actions += `
                            <button class="btn btn-sm btn-warning me-1" onclick="changeLeave(${item.id})" title="Perlu Perubahan">
                                <i class="bi bi-pencil"></i>
                            </button>
                        `;
                        actions += `
                            <button class="btn btn-sm btn-secondary me-1" onclick="postponeLeave(${item.id})" title="Tangguhkan">
                                <i class="bi bi-pause"></i>
                            </button>
                        `;
                    }
                    // Sekretaris dapat memproses pengajuan dengan status pending_sekretaris (level 4) - hanya approve dan reject
                    else if (typeof window.IS_SEKRETARIS !== 'undefined' && window.IS_SEKRETARIS && item.status === 'pending_sekretaris') {
                        actions += `
                            <button class="btn btn-sm btn-primary me-1" onclick="approveLeave(${item.id})" title="Teruskan ke Pimpinan">
                                <i class="bi bi-arrow-up-right-circle"></i>
                            </button>
                        `;
                        actions += `
                            <button class="btn btn-sm btn-danger me-1" onclick="rejectLeave(${item.id})" title="Tolak">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        `;
                    }
                }

                // Special handling for admin when status is 'pending_admin_upload'
                if (typeof window.IS_PIMPINAN !== 'undefined' && window.IS_PIMPINAN && item.status === 'pending_admin_upload') {
                    actions += `
                        <button class="btn btn-sm btn-warning me-1" onclick="uploadAdminDocument(${item.id})" title="Upload Dokumen Pendukung">
                            <i class="bi bi-upload"></i> Upload Dokumen
                        </button>
                    `;
                }

                // Special handling for admin when status is 'approved'
                if (typeof window.IS_PIMPINAN !== 'undefined' && window.IS_PIMPINAN && item.status === 'approved') {
                    if (!item.admin_blankofinal_sender && (!item.is_completed || item.is_completed == 0)) {
                        // Hide other actions, show only "Lanjutkan Proses Pengajuan Cuti" button
                        actions = `
                            <button class="btn btn-sm btn-info me-1" onclick="viewDetail(${item.id})" title="Lihat Detail">
                                <i class="bi bi-eye"></i> Detail
                            </button>
                            <button class="btn btn-sm btn-success me-1" onclick="continueProcess(${item.id})" title="Lanjutkan Proses Pengajuan Cuti">
                                <i class="bi bi-play-circle"></i> Lanjutkan
                            </button>
                        `;
                    } else {
                        // admin_blankofinal_sender exists or is_completed=1, show normal actions
                        // Continue to add download/upload buttons below
                        if (["approved","rejected","changed","postponed"].includes(item.status) && (!item.is_completed || item.is_completed == 0)) {
                            // Tombol download blanko final jika sudah ada dokumen final
                            if (item.has_final_doc) {
                                actions += `
                                    <a href="${baseUrl('leave/downloadFinalDoc?id=' + item.id)}" 
                                       class="btn btn-sm btn-success btn-action me-1" title="Download Blanko Final">
                                        <i class="bi bi-file-earmark-check"></i>
                                    </a>
                                `;
                            }
                            // Tombol upload blanko final
                            actions += `
                                <a href="${baseUrl('approval/upload/' + item.id)}" 
                                   class="btn btn-sm btn-warning btn-action me-1" title="Upload Blanko Final">
                                    <i class="bi bi-upload"></i>
                                </a>
                            `;
                        }
                    }
                } else {
                    // For non-admin or other statuses, show upload buttons if applicable
                    if (["approved","rejected","changed","postponed"].includes(item.status) && (!item.is_completed || item.is_completed == 0)) {
                        // Tombol download blanko final jika sudah ada dokumen final
                        if (item.has_final_doc) {
                            actions += `
                                <a href="${baseUrl('leave/downloadFinalDoc?id=' + item.id)}" 
                                   class="btn btn-sm btn-success btn-action me-1" title="Download Blanko Final">
                                    <i class="bi bi-file-earmark-check"></i>
                                </a>
                            `;
                        }
                        // Tombol upload blanko final
                        actions += `
                            <a href="${baseUrl('approval/upload/' + item.id)}" 
                               class="btn btn-sm btn-warning btn-action me-1" title="Upload Blanko Final">
                                <i class="bi bi-upload"></i>
                            </a>
                        `;
                    }
                }
                // Ubah tampilan status
                let status_display = '';
                if (item.is_completed && item.is_completed == 1) {
                    status_display += '<span class="badge bg-primary me-1">Selesai</span>';
                }
                // Tentukan warna badge berdasarkan status
                let badgeColor = 'bg-secondary';
                switch(item.status) {
                    case 'pending':
                    case 'pending_kasubbag':
                    case 'pending_kabag':
                    case 'pending_sekretaris':
                    case 'awaiting_pimpinan':
                    case 'pending_admin_upload':
                        badgeColor = 'bg-warning';
                        break;
                    case 'approved':
                        badgeColor = 'bg-success';
                        break;
                    case 'rejected':
                        badgeColor = 'bg-danger';
                        break;
                    case 'changed':
                        badgeColor = 'bg-info';
                        break;
                    case 'postponed':
                        badgeColor = 'bg-secondary';
                        break;
                }
                status_display += '<span class="badge ' + badgeColor + '">' + getStatusDisplay(item.status) + '</span>';
                // Tampilkan jumlah hari ditangguhkan jika ada
                let postponedInfo = '';
                if (item.jumlah_hari_ditangguhkan && parseInt(item.jumlah_hari_ditangguhkan) > 0) {
                    postponedInfo = `<div class=\"small text-warning\">Ditangguhkan: ${item.jumlah_hari_ditangguhkan} hari</div>`;
                }
                
                let actionBtns = actions.replace(/ me-1/g, '').trim();
                let btnCount = (actionBtns.match(/<button/g) || []).length + (actionBtns.match(/<a/g) || []).length;
                let gridCols = btnCount > 4 ? 3 : 2;
                let actionContainer = `<div style="display: grid; grid-template-columns: repeat(${gridCols}, 1fr); gap: 0.25rem;">${actionBtns}</div>`;

                tbody.append(`
                    <tr data-leave-id="${item.id}">
                        <td data-label="No">${index + 1}</td>
                        <td data-label="Tanggal Pengajuan">${item.created_at_formatted || item.created_at || '-'}</td>
                        <td data-label="Nama">${item.nama || '-'}</td>
                        <td data-label="Unit Kerja">${item.nama_satker ? item.nama_satker : (item.unit_kerja || '-')}</td>
                        <td data-label="Jenis Cuti">${item.nama_cuti || '-'}</td>
                        <td data-label="Periode">${item.tanggal_mulai_formatted || item.tanggal_mulai || '-'} - ${item.tanggal_selesai_formatted || item.tanggal_selesai || '-'}</td>
                        <td data-label="Status">${status_display} ${postponedInfo}</td>
                        <td data-label="Aksi">${actionContainer}</td>
                    </tr>
                `);
            });
            
            // Initialize DataTable
            if ($.fn.DataTable.isDataTable('#persetujuanTable')) {
                $('#persetujuanTable').DataTable().destroy();
            }
            
            setTimeout(function() {
                $('#persetujuanTable').DataTable({
                language: {
                    "sEmptyTable": "Tidak ada data",
                    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                    "sInfoFiltered": "(disaring dari _MAX_ total data)",
                    "sInfoPostFix": "",
                    "sInfoThousands": ".",
                    "sLengthMenu": "Tampilkan _MENU_ data",
                    "sLoadingRecords": "Memuat...",
                    "sProcessing": "Sedang memproses...",
                    "sSearch": "Cari:",
                    "sZeroRecords": "Tidak ditemukan data yang sesuai",
                    "oPaginate": {
                        "sFirst": "Pertama",
                        "sLast": "Terakhir",
                        "sNext": "Selanjutnya",
                        "sPrevious": "Sebelumnya"
                    }
                },
                order: [[0, 'asc']],
                pageLength: 10
            });
        }, 100);
            
        } else {
            console.error('Failed to load data:', response.message);
            $('#persetujuanTable tbody').html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data: ' + (response.message || 'Unknown error') + '</td></tr>');
        }
    }, 'json').fail(function(xhr, status, error) {
        console.error('AJAX Error:', error);
        console.error('Status:', status);
        console.error('Response:', xhr.responseText);
        
        let errorMessage = 'Error: Gagal menghubungi server';
        
        // Try to parse JSON response
        try {
            const jsonResponse = JSON.parse(xhr.responseText);
            if (jsonResponse.message) {
                errorMessage = jsonResponse.message;
            }
        } catch (e) {
            // If not JSON, check if it's HTML error
            if (xhr.responseText.includes('<!DOCTYPE') || xhr.responseText.includes('<br />')) {
                errorMessage = 'Server error: PHP error detected. Check console.';
            }
        }
        
        $('#persetujuanTable tbody').html('<tr><td colspan="8" class="text-center text-danger">' + errorMessage + '</td></tr>');
    });
}

function viewDetail(id) {
    $.post(baseUrl('approval/getDetail'), {
        leave_id: id
    }, function(response) {
        if (response.success) {
            const data = response.data;
            // If getDetail doesn't include has_generated_doc, infer it from the list row
            const $row = $(`#persetujuanTable tbody tr[data-leave-id="${id}"]`);
            const hasGenerated = !!(data.has_generated_doc || ($row.length && $row.find('a.btn-download-generated').length > 0));
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi Pengajuan</h6>
                        <table class="table table-sm">
                            <tr><td>Nama</td><td>: ${data.nama}</td></tr>
                            <tr><td>NIP</td><td>: ${data.nip}</td></tr>
                            <tr><td>Jabatan</td><td>: ${data.jabatan}</td></tr>
                            <tr><td>Unit Kerja</td><td>: ${data.nama_satker ? data.nama_satker : data.unit_kerja}</td></tr>
                            <tr><td>Jenis Cuti</td><td>: ${data.nama_cuti}</td></tr>
                            <tr><td>Tanggal</td><td>: ${data.tanggal_mulai_formatted} - ${data.tanggal_selesai_formatted}</td></tr>
                            <tr><td>Jumlah Hari</td><td>: ${data.jumlah_hari} hari</td></tr>
                            <tr><td>Status</td><td>: 
                                ${(data.is_completed && data.is_completed == 1) ? '<span class="badge bg-primary me-1">Selesai</span>' : ''}
                                ${data.status_badge}
                            </td></tr>
                            ${data.status === 'postponed' && data.jumlah_hari_ditangguhkan && parseInt(data.jumlah_hari_ditangguhkan) > 0 ? `<tr><td>Jumlah Hari Ditangguhkan</td><td>: <span class='text-warning fw-bold'>${data.jumlah_hari_ditangguhkan} hari</span></td></tr>` : ''}
                            ${data.approved_by_name ? `<tr><td>Disetujui Oleh</td><td>: ${data.approved_by_name}</td></tr>` : ''}
                            ${data.catatan_approval ? `<tr><td>Catatan</td><td>: ${data.catatan_approval}</td></tr>` : ''}
                            ${(window.IS_SEKRETARIS && data.status === 'pending_sekretaris' && data.last_approver_info) ? `<tr><td>Direkomendasikan Oleh</td><td>: <span class='badge ${data.last_approver_source === "kabag" ? "bg-info" : "bg-warning"} text-dark'>${data.last_approver_info}</span>${data.last_approver_source === "kabag" ? '<br><small class="text-muted"></small>' : '<br><small class="text-muted"></small>'}</td></tr>` : ''}
                        </table>
                        
                        <h6 class="mt-3">Alasan Cuti</h6>
                        <p class="text-muted">${data.alasan || '-'}</p>
                        
                        ${data.alamat_cuti ? `
                            <h6>Alamat Selama Cuti</h6>
                            <p class="text-muted">${data.alamat_cuti}</p>
                        ` : ''}
                        
                        ${data.telepon_cuti ? `
                            <h6>Telepon Selama Cuti</h6>
                            <p class="text-muted">${data.telepon_cuti}</p>
                        ` : ''}
                    </div>
                    <div class="col-md-6">
                        <h6>Status Blanko</h6>
                        <div class="mb-2">
                            <span class="badge ${(data.blanko_uploaded || data.has_generated_doc) ? 'bg-success' : 'bg-warning'} me-2">
                                <i class="bi ${(data.blanko_uploaded || data.has_generated_doc) ? 'bi-check-circle' : 'bi-clock'}"></i>
                                Blanko Pegawai
                            </span>\n                            ${(hasGenerated || data.can_download_generated) && data.has_generated_doc ? `
                                <div class="mt-2 text-muted small d-flex flex-column align-items-start gap-1">
                                    <span>Blanko (diberikan oleh sistem)</span>
                                    <a href="${baseUrl('leave/downloadGeneratedDoc?id=' + data.id)}" class="btn btn-sm btn-outline-primary btn-download-generated-modal" target="_blank" rel="noopener">
                                        <i class="bi bi-file-earmark-arrow-down"></i> Download Blanko
                                    </a>
                                </div>
                                ${data.blanko_uploaded ? `
                                    <div class="mt-2 d-flex flex-column align-items-start gap-1">
                                        <span class="text-muted small">Signed upload: ${data.signed_doc_upload_date || '-'}</span>
                                        <a href="${baseUrl('leave/downloadSignedDoc?id=' + data.id)}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i> Download Signed
                                        </a>
                                    </div>
                                ` : ''}
                            ` : (
                                data.blanko_uploaded ? 
                                `<div class="mt-2 d-flex flex-column align-items-start gap-1">
                                    <span class="text-muted small">Upload: ${data.signed_doc_upload_date || '-'}</span>
                                    <a href="${baseUrl('leave/downloadSignedDoc?id=' + data.id)}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                </div>` : 
                                '<div class="mt-1"><span class="text-muted small">Belum diupload</span></div>'
                            )}
                        </div>
                        <div class="mb-2 mt-3">
                            <span class="badge ${data.has_final_doc ? 'bg-success' : 'bg-secondary'} me-2">
                                <i class="bi ${data.has_final_doc ? 'bi-check-circle' : 'bi-x-circle'}"></i>
                                Blanko Final
                            </span>
                            ${data.has_final_doc ? 
                                `<div class="mt-2 d-flex flex-column align-items-start gap-1">
                                    <span class="text-muted small">Upload: ${data.final_doc_upload_date || '-'}</span>
                                    <a href="${baseUrl('leave/downloadFinalDoc?id=' + data.id)}" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                </div>` : 
                                '<div class="mt-1"><span class="text-muted small">Belum dibuat</span></div>'
                            }
                        </div>
                        
                        ${(data.status === 'pending' && !data.blanko_uploaded && !data.has_generated_doc && !window.IS_ATASAN) ? `
                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Peringatan:</strong> User belum mengupload blanko yang ditandatangani. 
                                Pengajuan tidak dapat diproses sampai blanko diupload.
                            </div>
                        ` : ''}

                        ${(data.status === 'pending' || data.status === 'pending_kasubbag' || data.status === 'awaiting_pimpinan' || (data.status_draft && data.is_pta_makassar)) ? `
                            <div class="d-flex flex-column align-items-start gap-2 mt-3">
                                ${((data.status === 'awaiting_pimpinan' && window.IS_KETUA) || ((data.status === 'pending' || (data.status === 'pending_kasubbag' && window.IS_KASUBBAG)) && (data.blanko_uploaded || data.has_generated_doc || window.IS_ATASAN)) || (data.status_draft && data.is_pta_makassar)) ? `
                                            ${window.IS_ATASAN ? `<button class="btn btn-primary btn-sm" onclick="approveLeave(${data.id})">
                                                <i class="bi bi-arrow-up-right-circle"></i> Setujui
                                            </button>` : `<button class="btn btn-success btn-sm" onclick="approveLeave(${data.id})">
                                                <i class="bi bi-check"></i> Setujui
                                            </button>`}
                                            <button class="btn btn-danger btn-sm" onclick="rejectLeave(${data.id})">
                                                <i class="bi bi-x"></i> Tolak
                                            </button>
                                        ` : ((data.status === 'pending' || data.status === 'pending_kasubbag') && !window.IS_ATASAN ? `
                                        ` : '')}
                            </div>
                        ` : (['approved','rejected','changed','postponed'].includes(data.status)) ? `
                            ${((['approved','rejected','changed','postponed'].includes(data.status)) && (!data.is_completed || data.is_completed == 0)) ? `
                                <div class="d-flex flex-column align-items-start gap-2 mt-3">
                                    <a href="${baseUrl('approval/upload/' + data.id)}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-upload"></i> Upload Blanko Final
                                    </a>
                                </div>
                            ` : ''}
                        ` : ''
                        }
                        ${(data.status === 'pending') ? `
                            <div class="d-flex flex-column align-items-start gap-2 mt-2">
                                <button class="btn btn-warning btn-sm text-white" onclick="changeLeave(${data.id})"><i class="bi bi-pencil"></i> Perlu Perubahan</button>
                                <button class="btn btn-secondary btn-sm" onclick="postponeLeave(${data.id})"><i class="bi bi-pause"></i> Tangguhkan</button>
                            </div>
                        ` : ''}
                        ${(data.status === 'pending_admin_upload' && window.IS_ADMIN) ? `
                            <div class="mt-3">
                                <button class="btn btn-info btn-sm" onclick="uploadAdminDocument(${data.id})">
                                    <i class="bi bi-upload"></i> Upload Dokumen Pendukung
                                </button>
                            </div>
                        ` : ''}
                    </div>
                </div>
                                <div class="col-12 mt-3">
                                        ${data.dokumen_pendukung ? (() => {
                                            const ext = data.dokumen_pendukung.split('.').pop().toLowerCase();
                                            let previewBtn = '';
                                            if (ext === 'pdf') {
                                                previewBtn = `<button class=\"btn btn-secondary btn-sm ms-2\" onclick=\"previewDokumenPendukung('${encodeURIComponent(data.dokumen_pendukung)}')\"><i class=\"bi bi-eye\"></i> Preview</button>`;
                                            }
                                            return `
                                                <div class=\"alert alert-info\">
                                                    <i class=\"bi bi-paperclip me-2\"></i>
                                                    <strong>Dokumen Pendukung:</strong>
                                                    ${previewBtn}
                                                </div>
                                                <div id=\"previewDokumenPendukungModal\" class=\"modal fade\" tabindex=\"-1\">
                                                    <div class=\"modal-dialog modal-xl\">
                                                        <div class=\"modal-content\">
                                                            <div class=\"modal-header\">
                                                                <h5 class=\"modal-title\">Preview Dokumen Pendukung</h5>
                                                                <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\"></button>
                                                            </div>
                                                            <div class=\"modal-body\" id=\"previewDokumenPendukungBody\" style=\"min-height:60vh;\">
                                                                <!-- Preview content will be loaded here -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
                                        })() : ''}
                                </div>
            `;
            
            $('#detailContent').html(content);
            $('#detailModal').modal('show');
            console.log('Detail modal shown for leave id=', data.id, 'has_generated_doc=', hasGenerated, 'generated_doc_filename=', data.generated_doc_filename);
            // Agar preview PDF tidak terdownload, pastikan controller PHP (leave/downloadDocument) mengirim header Content-Disposition: inline untuk file PDF
            // Contoh di controller:
            // if ($ext === 'pdf') { header('Content-Disposition: inline; filename="' . $filename . '"'); }
            window.previewDokumenPendukung = function(file) {
                const url = baseUrl('leave/downloadDocument?file=') + decodeURIComponent(file);
                let ext = file.split('.').pop().toLowerCase();
                if (ext !== 'pdf') {
                    Swal.fire('Preview hanya tersedia untuk file PDF.');
                    return;
                }
                let previewHtml = `<embed src="${url}" type="application/pdf" width="100%" height="600px" style="border:none;" />`;
                $('#previewDokumenPendukungBody').html(previewHtml);
                $('#previewDokumenPendukungModal').modal('show');
            }
        } else {
            Swal.fire('Error', response.message || 'Gagal memuat detail', 'error');
        }
    }, 'json').fail(function(xhr, status, error) {
        console.error('AJAX Error:', error);
        Swal.fire('Error', 'Gagal menghubungi server', 'error');
    });
}

function approveLeave(id) {
    $('#leaveId').val(id);
    $('#approvalAction').val('approve_leave');
    // Reset forward routing UI
    $('#forwardRoutingGroup').addClass('d-none');
    $('#forwardToKabag').prop('checked', false);
    $('#forwardToSekretaris').prop('checked', false);
    
    // Jika current user adalah atasan, ubah teks dan style menjadi Rekomendasikan kecuali
    // saat ketua melakukan final approval (status awaiting_pimpinan)
    if (typeof window.IS_ATASAN !== 'undefined' && window.IS_ATASAN) {
        $('#approvalTitle').text('Rekomendasikan Pengajuan Cuti ke Pimpinan');
        $('#catatanRequired').hide();
        $('#submitApproval').removeClass('btn-success btn-danger btn-warning btn-secondary').addClass('btn-primary').text('Setujui');
        
        // Ambil data leave dari table untuk cek status
        const $row = $(`#persetujuanTable tbody tr[data-leave-id="${id}"]`);
        if ($row.length > 0) {
            // Get leave data via AJAX to check status dan role
            $.post(baseUrl('approval/getDetail'), {
                leave_id: id
            }, function(response) {
                if (response.success) {
                    const leaveData = response.data;
                    // Jika status adalah 'awaiting_pimpinan' dan current atasan adalah ketua, treat as final approval
                    if (leaveData.status === 'awaiting_pimpinan' && typeof window.IS_KETUA !== 'undefined' && window.IS_KETUA) {
                        $('#approvalTitle').text('Persetujuan Final oleh Ketua');
                        $('#submitApproval').removeClass('btn-primary').addClass('btn-success').text('Setujui');
                        // no forwarding options
                    }
                    // Jika status adalah 'pending_kasubbag' dan user adalah kasubbag, tampilkan forward routing
                    else if (leaveData.status === 'pending_kasubbag' && typeof window.IS_KASUBBAG !== 'undefined' && window.IS_KASUBBAG) {
                        $('#approvalTitle').text('Teruskan Pengajuan Cuti Kasubbag');
                        $('#forwardRoutingGroup').removeClass('d-none');
                    }
                    // Kabag approval - forward to sekretaris
                    else if (leaveData.status === 'pending_kabag' && typeof window.IS_KABAG !== 'undefined' && window.IS_KABAG) {
                        $('#approvalTitle').text('Teruskan Pengajuan Cuti ke Sekretaris');
                        $('#submitApproval').text('Teruskan ke Sekretaris');
                    }
                    // Sekretaris approval - forward to pimpinan
                    else if (leaveData.status === 'pending_sekretaris' && typeof window.IS_SEKRETARIS !== 'undefined' && window.IS_SEKRETARIS) {
                        $('#approvalTitle').text('Teruskan Pengajuan Cuti ke Pimpinan');
                        $('#submitApproval').text('Teruskan ke Pimpinan');
                        // Show pimpinan selector and populate list via AJAX
                        $('#pimpinanSelectGroup').removeClass('d-none');
                        // Clear existing options
                        $('#pimpinanSelect').html('<option value="">-- Pilih Pimpinan --</option>');
                        $.post(baseUrl('approval/getPimpinanList'), {}, function(resp) {
                            if (resp.success && resp.data) {
                                resp.data.forEach(function(r) {
                                    $('#pimpinanSelect').append('<option value="' + r.id_atasan + '">' + r.nama_atasan + ' (' + (r.jabatan || '') + ')</option>');
                                });
                            } else {
                                console.warn('Gagal memuat daftar pimpinan:', resp.message);
                            }
                        }, 'json').fail(function() {
                            console.warn('Gagal menghubungi server untuk daftar pimpinan');
                        });
                    }
                }
            }, 'json');
        }
    } else {
        $('#approvalTitle').text('Setujui Pengajuan Cuti');
        $('#catatanRequired').hide();
        $('#submitApproval').removeClass('btn-primary btn-danger btn-warning btn-secondary').addClass('btn-success').text('Setujui');
    }
    $('#approvalModal').modal('show');
}

function rejectLeave(id) {
    $('#leaveId').val(id);
    $('#approvalAction').val('reject_leave');
    $('#approvalTitle').text('Tolak Pengajuan Cuti');
    $('#catatanRequired').show();
    $('#submitApproval').removeClass('btn-success').addClass('btn-danger').text('Tolak');
    $('#approvalModal').modal('show');
}

// Tambah tombol aksi baru di detail pengajuan (change & postpone)
function changeLeave(id) {
    $('#leaveId').val(id);
    $('#approvalAction').val('change_leave');
    $('#approvalTitle').text('Perlu Perubahan Pengajuan Cuti');
    $('#catatanRequired').show();
    $('#submitApproval').removeClass('btn-success btn-danger btn-secondary text-white').addClass('btn-warning text-white').text('Perlu Perubahan');
    $('#approvalModal').modal('show');
}

function postponeLeave(id) {
    $('#leaveId').val(id);
    $('#approvalAction').val('postpone_leave');
    $('#approvalTitle').text('Tangguhkan Pengajuan Cuti');
    $('#catatanRequired').show();
    $('#submitApproval').removeClass('btn-success btn-danger btn-warning text-white').addClass('btn-secondary text-white').text('Tangguhkan');
    // Ambil jumlah hari cuti dari baris tabel
    let jumlahHari = 0;
    const $row = $(`#persetujuanTable tbody tr[data-leave-id="${id}"]`);
    if ($row.length > 0) {
        // Ambil dari kolom ke-7 ("Periode" ada di kolom ke-6, "Status" ke-7, "Aksi" ke-8)
        // Kita ambil jumlah hari dari data response jika tersedia
        // Atau bisa juga dari detail jika sudah pernah di-load
        // Lebih aman: ambil dari detail API
        $.post(baseUrl('approval/getDetail'), { leave_id: id }, function(response) {
            if (response.success && response.data && response.data.jumlah_hari) {
                jumlahHari = parseInt(response.data.jumlah_hari, 10) || 0;
                $('#jumlahHariDitangguhkan').attr('max', jumlahHari);
                $('#jumlahHariDitangguhkan').attr('placeholder', 'Maksimal ' + jumlahHari + ' hari');
            } else {
                $('#jumlahHariDitangguhkan').removeAttr('max');
                $('#jumlahHariDitangguhkan').attr('placeholder', 'Masukkan jumlah hari yang ditangguhkan');
            }
        }, 'json');
    } else {
        $('#jumlahHariDitangguhkan').removeAttr('max');
        $('#jumlahHariDitangguhkan').attr('placeholder', 'Masukkan jumlah hari yang ditangguhkan');
    }
    // Tampilkan input jumlah hari ditangguhkan
    $('#jumlahHariDitangguhkanGroup').removeClass('d-none');
    $('#jumlahHariDitangguhkan').val('');
    $('#approvalModal').modal('show');
}

function continueProcess(id) {
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin melanjutkan proses pengajuan cuti ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1b5e20',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.post(baseUrl('approval/continueProcess'), {
                leave_id: id
            }, function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        confirmButtonColor: '#1b5e20'
                    }).then(() => {
                        // Reload data
                        loadPersetujuanData();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message,
                        confirmButtonColor: '#1b5e20'
                    });
                }
            }, 'json').fail(function(xhr, status, error) {
                console.error('AJAX Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal menghubungi server',
                    confirmButtonColor: '#1b5e20'
                });
            });
        }
    });
}

// Sembunyikan input jika modal ditutup
$('#approvalModal').on('hidden.bs.modal', function() {
    $('#jumlahHariDitangguhkanGroup').addClass('d-none');
    $('#jumlahHariDitangguhkan').val('');
    $('#pimpinanSelectGroup').addClass('d-none');
    $('#pimpinanSelect').val('');
});
// Delegated click handler for modal-based download links (preview in modal)
$(document).on('click', 'a.btn-download-generated-modal', function(e) {
    const href = $(this).attr('href');
    console.log('Generated download (modal) click intercepted, href=', href);
    // prevent immediate navigation so we can probe
    e.preventDefault();
    // Do a HEAD request to inspect final URL and content-type
    fetch(href, { method: 'HEAD', credentials: 'same-origin' }).then(function(res) {
        console.log('HEAD response for', href, 'status=', res.status, 'url=', res.url, 'content-type=', res.headers.get('content-type'));
        // If final url appears to be a redirect to /leave without download, log a warning
        try {
            const finalUrl = new URL(res.url, window.location.origin).pathname;
            if (finalUrl && finalUrl.indexOf('/leave') !== -1 && finalUrl.indexOf('downloadGeneratedDoc') === -1) {
                console.warn('HEAD indicates redirect to', res.url, '- likely file missing or controller redirecting.');
            }
        } catch (err) {
            console.warn('Error parsing HEAD response URL', err);
        }
        // proceed to open the link in a new tab anyway (to allow download or show redirect)
        window.open(href, '_blank');
    }).catch(function(err) {
        console.error('Error during HEAD for', href, err);
        // fallback: open tab
        window.open(href, '_blank');
    });
});

// Direct download handler for regular download buttons (no special processing)
$(document).on('click', 'a.btn-download-generated:not(.btn-download-generated-modal)', function(e) {
    const href = $(this).attr('href');
    console.log('Generated download (direct) click, href=', href);
    // Allow browser to handle download naturally - don't preventDefault
    // The browser will download file if Content-Disposition: attachment header is set
});
// Fungsi untuk memperbarui baris setelah approval/rejection
function updateRowAfterApproval(leaveId, action) {
    console.log('Updating row for leave ID:', leaveId, 'with action:', action);
    // ...existing code...
}

function uploadAdminDocument(leaveId) {
    // Create file input
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = '.pdf';
    fileInput.style.display = 'none';
    
    fileInput.onchange = function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        // Validate file type
        if (file.type !== 'application/pdf') {
            Swal.fire('Error', 'File harus berupa PDF!', 'error');
            return;
        }
        
        // Validate file size (10MB)
        if (file.size > 10485760) {
            Swal.fire('Error', 'Ukuran file maksimal 10MB!', 'error');
            return;
        }
        
        // Confirm upload
        Swal.fire({
            title: 'Konfirmasi Upload',
            text: `Upload dokumen pendukung "${file.name}" untuk pengajuan cuti ini?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1b5e20',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Upload',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Mengupload...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Create form data
                const formData = new FormData();
                formData.append('leave_id', leaveId);
                formData.append('dokumen_pendukung', file);
                
                // Upload
                $.ajax({
                    url: baseUrl('approval/uploadAdminDocument'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                confirmButtonColor: '#1b5e20'
                            }).then(() => {
                                // Reload data
                                loadPersetujuanData();
                            });
                        } else {
                            Swal.fire('Error', response.message || 'Upload gagal', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Upload error:', error);
                        Swal.fire('Error', 'Terjadi kesalahan saat upload', 'error');
                    }
                });
            }
        });
    };
    
    // Trigger file selection
    fileInput.click();
}
</script>