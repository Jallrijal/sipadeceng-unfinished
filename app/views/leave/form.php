<div class="card">
    <div class="card-body">
        <!-- Form Header -->
        <div class="form-header">
            <h3><i class="bi bi-file-earmark-text me-2"></i>FORMULIR PERMINTAAN DAN PEMBERIAN CUTI</h3>
            <p class="text-muted mb-0">Pengadilan Tinggi Agama Makassar</p>
        </div>
        
        <!-- Quota Alert -->
        <div id="quotaAlert" class="quota-alert"></div>
        
        <!-- Form -->
        <form id="cutiForm" enctype="multipart/form-data">
            <!-- Section 1: Data Pemohon -->
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-person-badge me-2"></i>I. DATA PEMOHON
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" value="<?php echo $_SESSION['nama']; ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIP</label>
                        <input type="text" class="form-control" value="<?php echo $_SESSION['nip']; ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jabatan</label>
                        <input type="text" class="form-control" value="<?php echo $_SESSION['jabatan']; ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Golongan</label>
                        <input type="text" class="form-control" value="<?php echo $_SESSION['golongan']; ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit Kerja</label>
                        <input type="text" class="form-control" value="<?php require_once __DIR__ . '/../../helpers/satker_helper.php'; echo get_nama_satker($_SESSION['unit_kerja']); ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Masa Kerja</label>
                        <input type="text" class="form-control" value="<?php echo hitungMasaKerja($_SESSION['tanggal_masuk']); ?>" readonly>
                    </div>
                </div>
            </div>
            
            <!-- Section 2: Jenis Cuti -->
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-card-list me-2"></i>II. JENIS CUTI YANG DIAMBIL
                </div>
                <div class="mb-3">
                    <label for="leave_type_id" class="form-label">Pilih Jenis Cuti</label>
                    <select class="form-select" id="leave_type_id" name="leave_type_id" required>
                        <option value="">-- Pilih Jenis Cuti --</option>
                    </select>
                    <small id="infoHakMelahirkan" class="text-muted d-block mt-1"></small>
                </div>
            </div>
            
            <!-- Section 3: Alasan Cuti -->
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-chat-left-text me-2"></i>III. ALASAN CUTI
                </div>
                <div class="mb-3">
                    <select class="form-select" id="alasan" name="alasan" required>
                        <option value="">-- Pilih Alasan Cuti --</option>
                    </select>
                    <input type="text" class="form-control mt-2 d-none" id="alasan_lainnya" name="alasan_lainnya" placeholder="Masukkan alasan cuti...">
                    <div class="invalid-feedback" id="alasan_lainnya_feedback">
                        Alasan cuti wajib diisi jika memilih 'Lainnya'.
                    </div>
                </div>
            </div>
            
            <!-- Section 4: Lamanya Cuti -->
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-calendar-range me-2"></i>IV. LAMANYA CUTI
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="text" class="form-control" id="tanggal_mulai" name="tanggal_mulai" 
                               placeholder="Pilih tanggal" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="text" class="form-control" id="tanggal_selesai" name="tanggal_selesai" 
                               placeholder="Pilih tanggal" required>
                    </div>
                </div>
                <div class="date-info">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Jumlah Hari Kerja:</strong> <span id="jumlahHari">0</span> hari
                        </div>
                        <div class="col-md-4" id="sisaKuotaWrapper">
                            <strong>Sisa Cuti:</strong> <span id="sisaKuota">-</span> hari
                        </div>
                        <div class="col-md-4" id="sisaSetelahWrapper">
                            <strong>Setelah Cuti:</strong> <span id="sisaSetelah">-</span> hari
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section 5: Catatan Cuti -->
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-journal-text me-2"></i>V. CATATAN CUTI
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan Cuti <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="catatan_cuti" name="catatan_cuti" rows="3" 
                              placeholder="Catatan cuti akan diisi secara otomatis (wajib diisi jika bukan cuti tahunan)" required readonly><?php echo isset($catatan_cuti) ? $catatan_cuti : ''; ?></textarea>
                    <div class="invalid-feedback">
                        Catatan cuti wajib diisi
                    </div>
                </div>
            </div>
            
            <!-- Section 6: Alamat Selama Cuti -->
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-geo-alt me-2"></i>VI. ALAMAT SELAMA MENJALANKAN CUTI
                </div>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alamat_cuti" name="alamat_cuti" rows="2" 
                                placeholder="Alamat lengkap selama cuti (wajib diisi)" required></textarea>
                        <div class="invalid-feedback">
                            Alamat selama cuti wajib diisi
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">No. Telepon <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="telepon_cuti" name="telepon_cuti" 
                            placeholder="08xx-xxxx-xxxx" required>
                        <div class="invalid-feedback">
                            Nomor telepon wajib diisi
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section 6: Dokumen Pendukung -->
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-paperclip me-2"></i>VI. DOKUMEN PENDUKUNG <!-- asterisk removed as upload is no longer mandatory -->
                </div>
                <div class="info-box mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    <small id="dokumenInfo"><strong>Opsional:</strong> Upload dokumen pendukung jika diperlukan (Surat Keterangan Dokter untuk cuti sakit, dll). 
                    Format: PDF. Max: 5MB</small>
                </div>
                <div class="file-upload-wrapper">
                    <input type="file" id="dokumen_pendukung" name="dokumen_pendukung" 
                        accept=".pdf">
                    <label for="dokumen_pendukung" class="file-upload-label">
                        <i class="bi bi-cloud-upload me-2"></i>
                        <span id="fileName">Klik untuk upload dokumen PDF</span>
                    </label>
                    <div class="mt-2" id="dokumenActions" style="display:none;">
                        <button type="button" class="btn btn-sm btn-warning me-2" id="gantiDokumenBtn"><i class="bi bi-arrow-repeat me-1"></i>Ganti Dokumen</button>
                        <button type="button" class="btn btn-sm btn-danger" id="hapusDokumenBtn"><i class="bi bi-trash me-1"></i>Hapus Dokumen</button>
                    </div>
                </div>
            </div>
            
            <!-- Submit Buttons -->
            <div class="text-center mt-4">
                <a href="<?php echo baseUrl('dashboard'); ?>" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left me-2"></i>Batal
                </a>
                <button type="submit" id="submitLeaveBtn" class="btn btn-primary">
                    <i class="bi bi-send me-2"></i>Ajukan Cuti
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<style>
    .form-control.is-invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + .75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(.375em + .1875rem) center;
    background-size: calc(.75em + .375rem) calc(.75em + .375rem);
    }

    .form-control.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 .25rem rgba(220,53,69,.25);
    }

    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: .25rem;
        font-size: .875em;
        color: #dc3545;
    }

    .form-control.is-invalid ~ .invalid-feedback {
        display: block;
    }

    /* Asterisk merah untuk field wajib */
    .form-label .text-danger {
        font-weight: bold;
    }

    .form-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .form-header h3 {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .form-section {
        margin-bottom: 30px;
    }
    
    .section-title {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 1.1rem;
        font-weight: 500;
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
    }
    
    .leave-type-card {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .leave-type-card:hover {
        border-color: var(--primary-color);
        background-color: #f8f9fa;
    }
    
    .leave-type-card.selected {
        border-color: var(--primary-color);
        background-color: rgba(27, 94, 32, 0.1);
    }
    
    .leave-type-card input[type="radio"] {
        display: none;
    }
    
    .info-box {
        background: #e8f5e9;
        border-left: 4px solid var(--primary-color);
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .info-box i {
        color: var(--primary-color);
    }
    
    .date-info {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-top: 10px;
    }
    
    .file-upload-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
        cursor: pointer;
        width: 100%;
    }
    
    .file-upload-wrapper input[type=file] {
        position: absolute;
        left: -9999px;
    }
    
    .file-upload-label {
        display: block;
        padding: 10px 15px;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 5px;
        text-align: center;
        transition: all 0.3s;
    }
    
    .file-upload-wrapper:hover .file-upload-label {
        background: #e9ecef;
        border-color: var(--primary-color);
    }
    
    .quota-alert {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 1050;
    }
    
    /* Loading overlay */
    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }
    
    .loading-overlay.show {
        display: flex;
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
$(document).ready(function() {
    let leaveTypes = [];
    let currentBalance = 0;
    window.cutiSakitQuota = 14; // Default kuota cuti sakit
    window.jumlahPengambilanMelahirkan = 0;
    // NIP current user (untuk validasi jenis kelamin pada cuti melahirkan)
    window.currentUserNip = '<?php echo isset($_SESSION["nip"]) ? addslashes($_SESSION["nip"]) : ""; ?>';
    
    // Initialize
    loadLeaveTypes();
    loadLeaveBalance();
    loadJumlahPengambilanMelahirkan();
    
    // Initialize date pickers
    const dateConfig = {
        locale: "id",
        dateFormat: "Y-m-d",
        minDate: "today",
        disable: [
            function(date) {
                // Disable weekends
                return (date.getDay() === 0 || date.getDay() === 6);
            }
        ]
    };
    
    const startDatePicker = flatpickr("#tanggal_mulai", {
        ...dateConfig,
        onChange: function(selectedDates, dateStr) {
            endDatePicker.set('minDate', dateStr);
            calculateDays();
        }
    });
    
    const endDatePicker = flatpickr("#tanggal_selesai", {
        ...dateConfig,
        onChange: function(selectedDates, dateStr) {
            calculateDays();
        }
    });
    
    // Load leave types
    function loadLeaveTypes() {
        $.post(baseUrl('leave/getTypes'), function(response) {
            if (response.success) {
                leaveTypes = response.data;
                let html = '<option value="">-- Pilih Jenis Cuti --</option>';
                response.data.forEach(function(type) {
                    html += `<option value="${type.id}">${type.nama_cuti}${type.deskripsi ? ' - ' + type.deskripsi : ''}</option>`;
                });
                $('#leave_type_id').html(html);
            }
        }, 'json');
    }
    
    // Load leave balance
    function loadLeaveBalance() {
        $.post(baseUrl('user/getStatistics'), function(response) {
            if (response.success) {
                currentBalance = response.data.sisa_cuti;
                $('#sisaKuota').text(currentBalance);
                
                // Show alert if quota is low
                if (currentBalance <= 3) {
                    $('#quotaAlert').html(`
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Sisa kuota cuti tahunan Anda tinggal <strong>${currentBalance} hari</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);
                }
            }
        }, 'json');
        
        // Load cuti sakit quota separately
        loadCutiSakitQuota();
    }
    
    // Load cuti sakit quota
    function loadCutiSakitQuota() {
        $.post(baseUrl('user/getLeaveQuotas'), function(response) {
            if (response.success) {
                // Find cuti sakit data (id=3)
                const cutiSakit = response.data.find(item => item.id == 3);
                if (cutiSakit) {
                    window.cutiSakitQuota = cutiSakit.sisa_kuota;
                    console.log('Cuti sakit quota loaded:', window.cutiSakitQuota);
                }
            }
        }, 'json');
    }
    
    // Load jumlah pengambilan cuti melahirkan
    function loadJumlahPengambilanMelahirkan() {
        $.post(baseUrl('user/getKuotaMelahirkan'), { user_id: window.currentUserId }, function(response) {
            if (response.success && response.data) {
                window.jumlahPengambilanMelahirkan = response.data.jumlah_pengambilan || 0;
                $('#infoHakMelahirkan').text('Sisa hak pengambilan: ' + (3 - window.jumlahPengambilanMelahirkan) + ' dari 3 kali');
                if (window.jumlahPengambilanMelahirkan >= 3) {
                    $('#ajukanCutiMelahirkanBtn').prop('disabled', true);
                    $('#infoHakMelahirkan').addClass('text-danger');
                } else {
                    $('#ajukanCutiMelahirkanBtn').prop('disabled', false);
                    $('#infoHakMelahirkan').removeClass('text-danger');
                }
            }
        }, 'json');
    }
    
    // Load alasan cuti dinamis
    function loadAlasanCuti(leaveTypeId) {
        if (!leaveTypeId) {
            $('#alasan').html('<option value="">-- Pilih Alasan Cuti --</option>');
            return;
        }
        $.post(baseUrl('leave/getAlasanCuti'), { leave_type_id: leaveTypeId }, function(response) {
            if (response.success) {
                let html = '<option value="">-- Pilih Alasan Cuti --</option>';
                let lainnyaOption = '';
                response.data.forEach(function(item) {
                    if (item.alasan === 'Lainnya') {
                        lainnyaOption = `<option value="Lainnya">Lainnya</option>`;
                    } else {
                        html += `<option value="${item.alasan}">${item.alasan}</option>`;
                    }
                });
                html += lainnyaOption; // Tambahkan 'Lainnya' di paling bawah
                $('#alasan').html(html);
            } else {
                $('#alasan').html('<option value="">-- Pilih Alasan Cuti --</option>');
            }
        }, 'json');
    }

    // Update readonly/placeholder state for Catatan Cuti based on selected jenis cuti
    function updateCatatanFieldState() {
        const typeId = $('#leave_type_id').val();
        if (typeId == 1) {
            // For Cuti Tahunan, field is readonly and filled automatically
            $('#catatan_cuti').attr('readonly', true).attr('placeholder', 'Catatan cuti akan diisi secara otomatis').removeClass('is-invalid');
            // Trigger automatic generation if empty
            setTimeout(updateCatatanCutiOtomatis, 100);
        } else {
            // For non-Cuti Tahunan, make field editable and required for manual input
            // NOTE: removed .focus() to avoid auto-scrolling to this field on page load
            $('#catatan_cuti').removeAttr('readonly').attr('placeholder', 'Silakan isi catatan cuti (wajib diisi)').removeClass('is-invalid');
        }
    }

    // Update dokumen pendukung state based on selected jenis cuti
    function updateDokumenFieldState() {
        const typeId = $('#leave_type_id').val();
        if (typeId == 3 || typeId == 5) { // Cuti Sakit atau Cuti Alasan Penting
            $('#dokumenRequired').show();
            $('#dokumenInfo').html('<strong>Opsional:</strong> Upload dokumen pendukung jika tersedia (Surat Keterangan Dokter untuk cuti sakit, atau dokumen pendukung lainnya untuk cuti alasan penting). Jika tidak ada, admin akan mengupload nanti. Format: PDF. Max: 5MB');
            $('#fileName').text('Klik untuk upload dokumen PDF');
            // Dokumen tidak wajib, admin akan upload jika diperlukan
        } else {
            $('#dokumenRequired').hide();
            $('#dokumenInfo').html('<strong>Opsional:</strong> Upload dokumen pendukung jika diperlukan. Format: PDF. Max: 5MB');
            $('#fileName').text('Klik untuk upload dokumen PDF');
            $('#dokumen_pendukung').removeAttr('required');
        }
    }

    // Trigger load alasan cuti saat jenis cuti berubah
    $('#leave_type_id').on('change', function() {
        const typeId = $(this).val();
        loadAlasanCuti(typeId);
        // Check and display quota sections
        if (typeId == 1) { // Cuti Tahunan
            $('#sisaKuotaWrapper').show();
            $('#sisaSetelahWrapper').show();
        } else {
            $('#sisaKuotaWrapper').hide();
            $('#sisaSetelahWrapper').hide();
        }

        // If user selects Cuti Melahirkan, validate NIP (digit 15) on client-side and disable submit if not allowed
        if (typeId == 4) {
            const nipDigits = (window.currentUserNip || '').toString().replace(/\D/g, '');
            if (nipDigits.length < 15) {
                $('#submitLeaveBtn').prop('disabled', true);
                $('#infoHakMelahirkan').text('Tidak dapat menentukan jenis kelamin dari NIP. Silakan perbarui data NIP di profil.').addClass('text-danger');
            } else {
                const digit15 = nipDigits.charAt(14);
                if (digit15 === '1') {
                    $('#submitLeaveBtn').prop('disabled', true);
                    $('#infoHakMelahirkan').text('Cuti melahirkan tidak tersedia untuk pegawai laki-laki.').addClass('text-danger');
                } else {
                    $('#submitLeaveBtn').prop('disabled', false);
                    $('#infoHakMelahirkan').removeClass('text-danger');
                    // refresh melahirkan kuota info
                    loadJumlahPengambilanMelahirkan();
                }
            }
        } else {
            // enable submit for other types
            $('#submitLeaveBtn').prop('disabled', false);
            $('#infoHakMelahirkan').removeClass('text-danger');
        }

        // Jika bukan Cuti Tahunan, kosongkan catatan agar diisi manual
        if (typeId != 1) {
            $('#catatan_cuti').val('');
        }
        // Update state (readonly/placeholder) for catatan cuti
        updateCatatanFieldState();
        // Update dokumen pendukung state
        updateDokumenFieldState();
        // Update catatan cuti otomatis / cek auto-fill
        setTimeout(function() {
            updateCatatanCutiOtomatis();
            checkAndFillCatatanCuti();
        }, 100);
    });
    
    // Tampilkan input alasan manual jika pilih 'Lainnya'
    $('#alasan').on('change', function() {
        if ($(this).val() === 'Lainnya') {
            $('#alasan_lainnya').removeClass('d-none').attr('required', true);
        } else {
            $('#alasan_lainnya').addClass('d-none').val('').removeAttr('required').removeClass('is-invalid');
        }
    });

    // Fungsi hitung hari kerja dan update tampilan
    window.calculateDays = function() {
        const startDate = $('#tanggal_mulai').val();
        const endDate = $('#tanggal_selesai').val();
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            let businessDays = 0;
            const currentDate = new Date(start);
            while (currentDate <= end) {
                const dayOfWeek = currentDate.getDay();
                if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                    businessDays++;
                }
                currentDate.setDate(currentDate.getDate() + 1);
            }
            $('#jumlahHari').text(businessDays);
            // Calculate remaining quota for annual leave or cuti sakit
            const leaveTypeId = $('#leave_type_id').val();
            if (leaveTypeId == 1) { // Cuti Tahunan
                const remaining = currentBalance - businessDays;
                $('#sisaSetelah').text(remaining);
                if (remaining < 0) {
                    $('#sisaSetelah').css('color', 'red');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Kuota Tidak Mencukupi',
                        text: 'Jumlah hari cuti melebihi sisa kuota Anda!',
                        confirmButtonColor: '#1b5e20'
                    });
                } else {
                    $('#sisaSetelah').css('color', 'inherit');
                }
            } else if (leaveTypeId == 3) { // Cuti Sakit
                const remaining = window.cutiSakitQuota - businessDays;
                $('#sisaSetelah').text(remaining);
                if (remaining < 0) {
                    $('#sisaSetelah').css('color', 'red');
                } else {
                    $('#sisaSetelah').css('color', 'inherit');
                }
            }
            checkAndFillCatatanCuti();
        }
    }

    // Validasi sebelum submit
    $('#cutiForm').submit(function(e) {
        e.preventDefault();
        const leaveTypeId = $('#leave_type_id').val(); // <-- pastikan dideklarasikan
        const alasan = $('#alasan').val();
        const alasanLainnya = $('#alasan_lainnya').val();
        const startDate = $('#tanggal_mulai').val();
        const endDate = $('#tanggal_selesai').val();
        const alamatCuti = $('#alamat_cuti').val();
        const teleponCuti = $('#telepon_cuti').val();
        const catatanCuti = $('#catatan_cuti').val();
        // Validasi alasan cuti
        if (alasan === 'Lainnya' && (!alasanLainnya || alasanLainnya.trim() === '')) {
            $('#alasan_lainnya').addClass('is-invalid');
            $('#alasan_lainnya_feedback').show();
            Swal.fire({
                icon: 'error',
                title: 'Alasan Cuti Wajib Diisi',
                text: 'Silakan isi alasan cuti pada kolom yang tersedia!',
                confirmButtonColor: '#1b5e20'
            });
            return false;
        } else {
            $('#alasan_lainnya').removeClass('is-invalid');
            $('#alasan_lainnya_feedback').hide();
        }
        // Pastikan alasan yang dikirim adalah alasan_lainnya jika dipilih
        if (alasan === 'Lainnya') {
            // Ganti value alasan sebelum submit
            $('#alasan').append(`<option value="${alasanLainnya}" selected>${alasanLainnya}</option>`);
            $('#alasan').val(alasanLainnya);
        }
        // Validasi semua field wajib
        if (!leaveTypeId || !startDate || !endDate || !alasan || !alamatCuti || !teleponCuti || !catatanCuti || catatanCuti.trim() === '') {
            Swal.fire({
                icon: 'error',
                title: 'Form Belum Lengkap',
                text: 'Harap lengkapi semua field yang wajib diisi termasuk alamat, nomor telepon, dan catatan cuti!',
                confirmButtonColor: '#1b5e20'
            });
            if (!alamatCuti) $('#alamat_cuti').addClass('is-invalid');
            if (!teleponCuti) $('#telepon_cuti').addClass('is-invalid');
            if (!catatanCuti || catatanCuti.trim() === '') $('#catatan_cuti').addClass('is-invalid');
            return;
        }
        // Validasi format nomor telepon
        const phoneRegex = /^[0-9\-\+\(\)\ ]+$/;
        if (!phoneRegex.test(teleponCuti) || teleponCuti.length < 10) {
            Swal.fire({
                icon: 'error',
                title: 'Format Nomor Telepon Salah',
                text: 'Nomor telepon harus berupa angka dan minimal 10 digit!',
                confirmButtonColor: '#1b5e20'
            });
            $('#telepon_cuti').addClass('is-invalid');
            return;
        }
        // Validasi cuti tahunan (default)
        if (leaveTypeId == 1) {
            if (jumlahHari > sisaKuota) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kuota Tidak Mencukupi',
                    text: 'Sisa kuota cuti tahunan Anda tidak mencukupi!',
                    confirmButtonColor: '#1b5e20'
                });
                return;
            }
        }
        
        // Validasi maksimal hari untuk Cuti Melahirkan (id=4) + validasi NIP (jenis kelamin)
        if (leaveTypeId == 4) {
            // Validasi jenis kelamin via NIP digit ke-15
            const nipRaw = (window.currentUserNip || '').toString();
            const nipDigits = nipRaw.replace(/\D/g, '');
            if (nipDigits.length < 15) {
                Swal.fire({
                    icon: 'error',
                    title: 'NIP Tidak Lengkap',
                    text: 'Tidak dapat menentukan jenis kelamin dari NIP Anda. Silakan perbarui data NIP di profil atau hubungi admin.',
                    confirmButtonColor: '#d33'
                });
                return;
            }
            const digit15 = nipDigits.charAt(14);
            if (digit15 === '1') {
                // Laki-laki -> tolak pengajuan cuti melahirkan
                Swal.fire({
                    icon: 'error',
                    title: 'Pengajuan Ditolak',
                    text: 'Cuti melahirkan hanya diperuntukkan untuk pegawai perempuan.',
                    confirmButtonColor: '#d33'
                });
                return;
            }

            // Cari max_days cuti melahirkan dari leaveTypes
            let maxMelahirkan = 90;
            if (Array.isArray(leaveTypes)) {
                const melahirkan = leaveTypes.find(t => t.id == 4);
                if (melahirkan && melahirkan.max_days) {
                    maxMelahirkan = parseInt(melahirkan.max_days);
                }
            }
            if (jumlahHari > maxMelahirkan) {
                Swal.fire({
                    icon: 'error',
                    title: 'Jumlah Hari Melebihi Batas',
                    text: `Cuti Melahirkan maksimal ${maxMelahirkan} hari!`,
                    confirmButtonColor: '#1b5e20'
                });
                return;
            }
            if (window.jumlahPengambilanMelahirkan >= 3) {
                Swal.fire({
                    icon: 'error',
                    title: 'Hak Cuti Melahirkan Habis',
                    text: 'Anda sudah menggunakan seluruh hak cuti melahirkan (3 kali).',
                    confirmButtonColor: '#1b5e20'
                });
                return;
            }
        }
        

        // Validasi cuti sakit: jika melebihi sisa kuota, tampilkan konfirmasi terlebih dahulu, lalu peringatan
        if (leaveTypeId == 3 && jumlahHari > window.cutiSakitQuota) {
            // Hitung berapa hari yang kurang
            const hariKurang = jumlahHari - window.cutiSakitQuota;
            
            // Isi catatan cuti otomatis
            const catatanOtomatis = `Kuota cuti sakit kurang ${hariKurang} hari dan dikenakan pemotongan gaji sebesar 5% per kuota yang kurang`;
            $('#catatan_cuti').val(catatanOtomatis);
            
            // 1. Konfirmasi pengajuan dulu
            Swal.fire({
                title: 'Konfirmasi Pengajuan',
                html: `
                    <div class="text-start">
                        <p>Apakah Anda yakin ingin mengajukan cuti dengan detail berikut?</p>
                        <ul>
                            <li>Jenis Cuti: <strong>${leaveTypes.find(t => t.id == leaveTypeId)?.nama_cuti || ''}</strong></li>
                            <li>Periode: <strong>${startDate} s/d ${endDate}</strong></li>
                            <li>Jumlah Hari: <strong>${$('#jumlahHari').text()} hari</strong></li>
                            <li>Alamat Cuti: <strong>${alamatCuti}</strong></li>
                            <li>Telepon: <strong>${teleponCuti}</strong></li>
                            <li>Catatan: <strong>${catatanOtomatis}</strong></li>
                        </ul>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1b5e20',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Ajukan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // 2. Setelah setuju, tampilkan peringatan kuota cuti sakit
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan Kuota Cuti Sakit',
                        text: `Kuota cuti sakit Anda tidak mencukupi!\n\nSisa kuota: ${window.cutiSakitQuota} hari\nDibutuhkan: ${jumlahHari} hari\nKurang: ${hariKurang} hari\n\nCatatan cuti telah diisi otomatis dengan informasi pemotongan gaji.`,
                        showCancelButton: true,
                        confirmButtonText: 'Lanjutkan',
                        cancelButtonText: 'Batalkan',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d'
                    }).then((result2) => {
                        if (result2.isConfirmed) {
                            submitForm();
                        }
                    });
                }
            });
            return;
        }
        
        // Validasi dokumen pendukung untuk cuti sakit dan cuti alasan penting
        // Supporting document is optional for all leave types; no validation needed here
        // if ((leaveTypeId == 3 || leaveTypeId == 5) && !$('#dokumen_pendukung')[0].files[0]) {
        //     Swal.fire({
        //         icon: 'error',
        //         title: 'Dokumen Pendukung Wajib',
        //         text: 'Untuk jenis cuti ini, dokumen pendukung wajib diupload!',
        //         confirmButtonColor: '#1b5e20'
        //     });
        //     return;
        // }
        
        // Khusus untuk cuti sakit dan cuti alasan penting tanpa dokumen
        if ((leaveTypeId == 3 || leaveTypeId == 5) && !$('#dokumen_pendukung')[0].files[0]) {
            const alasan = $('#alasan').val();
            const alasanLainnya = $('#alasan_lainnya').val();
            const alasanFinal = (alasan === 'Lainnya') ? alasanLainnya : alasan;
            Swal.fire({
                title: 'Konfirmasi Pengajuan',
                html: `
                    <div class="text-start">
                        <p>Pengajuan cuti ini memerlukan dokumen pendukung yang akan diupload oleh admin setelah Anda mengajukan.</p>
                        <ul>
                            <li>Jenis Cuti: <strong>${leaveTypes.find(t => t.id == leaveTypeId)?.nama_cuti || ''}</strong></li>
                            <li>Periode: <strong>${startDate} s/d ${endDate}</strong></li>
                            <li>Jumlah Hari: <strong>${$('#jumlahHari').text()} hari</strong></li>
                            <li>Alasan Cuti: <strong>${alasanFinal}</strong></li>
                            <li>Alamat Cuti: <strong>${alamatCuti}</strong></li>
                            <li>Telepon: <strong>${teleponCuti}</strong></li>
                            <li>Catatan: <strong>${catatanCuti}</strong></li>
                        </ul>
                        <p>Apakah Anda yakin ingin melanjutkan pengajuan?</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1b5e20',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Ajukan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitForm();
                }
            });
            return;
        }
        
        // Konfirmasi pengajuan (default)
        confirmAndSubmit();
    });

    // Konfirmasi pengajuan cuti (untuk semua jenis cuti)
    function confirmAndSubmit() {
        const leaveTypeId = $('#leave_type_id').val();
        const startDate = $('#tanggal_mulai').val();
        const endDate = $('#tanggal_selesai').val();
        const alamatCuti = $('#alamat_cuti').val();
        const teleponCuti = $('#telepon_cuti').val();
        const catatanCuti = $('#catatan_cuti').val();
        const alasan = $('#alasan').val();
        const alasanLainnya = $('#alasan_lainnya').val();
        const alasanFinal = (alasan === 'Lainnya') ? alasanLainnya : alasan;
        // Ambil nama file dokumen pendukung jika ada
        const dokumenFile = $('#dokumen_pendukung')[0].files[0];
        const dokumenName = dokumenFile ? dokumenFile.name : null;
        Swal.fire({
            title: 'Konfirmasi Pengajuan',
            html: `
                <div class="text-start">
                    <p>Apakah Anda yakin ingin mengajukan cuti dengan detail berikut?</p>
                    <ul>
                        <li>Jenis Cuti: <strong>${leaveTypes.find(t => t.id == leaveTypeId)?.nama_cuti || ''}</strong></li>
                        <li>Periode: <strong>${startDate} s/d ${endDate}</strong></li>
                        <li>Jumlah Hari: <strong>${$('#jumlahHari').text()} hari</strong></li>
                        <li>Alasan Cuti: <strong>${alasanFinal}</strong></li>
                        <li>Alamat Cuti: <strong>${alamatCuti}</strong></li>
                        <li>Telepon: <strong>${teleponCuti}</strong></li>
                        <li>Catatan: <strong>${catatanCuti}</strong></li>
                        ${dokumenName ? `<li>Dokumen Pendukung: <strong>${dokumenName}</strong></li>` : ''}
                    </ul>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1b5e20',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Ajukan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm();
            }
        });
    }

    // Submit form
    function submitForm() {
        $('#loadingOverlay').addClass('show');
        const formData = new FormData($('#cutiForm')[0]);
        formData.append('action', 'submit_leave');
        // When submitting from confirmation, request server to save as 'pending'
        formData.append('status', 'pending');
        formData.append('leave_type_id', $('#leave_type_id').val());
        formData.append('tanggal_mulai', $('#tanggal_mulai').val());
        formData.append('tanggal_selesai', $('#tanggal_selesai').val());
        formData.append('alasan', $('#alasan').val());
        formData.append('alamat_cuti', $('#alamat_cuti').val());
        formData.append('telepon_cuti', $('#telepon_cuti').val());
        formData.append('catatan_cuti', $('#catatan_cuti').val());
        $.ajax({
            url: baseUrl('leave/submit'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                $('#loadingOverlay').removeClass('show');
                if (response.success) {
                    // Jika cuti sakit dan sisa kuota minus, tampilkan alert khusus
                    if (response.leave_type_id == 3 && response.sisa_kuota_sakit !== undefined && response.sisa_kuota_sakit < 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Sisa Kuota Sakit Minus',
                            text: 'Sisa kuota cuti sakit Anda sudah minus. Anda akan dikenakan potongan bonus gaji.',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = baseUrl('leave/draft/' + response.leave_id);
                        });
                        return;
                    }
                    

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        confirmButtonColor: '#1b5e20',
                        confirmButtonText: 'Lanjutkan',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = baseUrl('leave/draft/' + response.leave_id);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message,
                        confirmButtonColor: '#1b5e20'
                    });
                }
            },
            error: function() {
                $('#loadingOverlay').removeClass('show');
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem. Silakan coba lagi.',
                    confirmButtonColor: '#1b5e20'
                });
            }
        });
    }

    // Remove invalid class when user types
    $('#alamat_cuti, #telepon_cuti').on('input', function() {
        $(this).removeClass('is-invalid');
    });
    
    // Update catatan cuti otomatis berdasarkan jenis cuti dan tanggal
    function updateCatatanCutiOtomatis() {
        const leaveTypeId = $('#leave_type_id').val();
        const tanggal = $('#tanggal_mulai').val();
        
        // Catatan otomatis hanya untuk Cuti Tahunan (id=1)
        if (leaveTypeId != 1 || !tanggal) {
            return;
        }
        
        // AJAX call untuk generate catatan cuti otomatis
        $.ajax({
            url: baseUrl('leave/generateCatatan'),
            type: 'POST',
            data: {
                leave_type_id: leaveTypeId,
                tanggal: tanggal
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    $('#catatan_cuti').val(response.data.catatan);
                }
            },
            error: function() {
                // Jika error, biarkan catatan tetap ada
                console.log('Error updating catatan cuti otomatis');
            }
        });
    }
    
    // Auto-fill catatan cuti untuk cuti sakit dan cuti alasan penting
    function checkAndFillCatatanCuti() {
        const leaveTypeId = $('#leave_type_id').val();
        const jumlahHari = parseInt($('#jumlahHari').text()) || 0;
        
        // Cuti Sakit (id=3)
        if (leaveTypeId == 3 && window.cutiSakitQuota !== undefined) {
            // Ambil jabatan user dari input readonly
            const jabatanUser = ($('input[readonly][value][class="form-control"]').filter(function(){return $(this).prev('label').text().toLowerCase().includes('jabatan');}).val() || '').toLowerCase();
            if (jabatanUser.includes('ketua') || jabatanUser.includes('hakim')) {
                // Jika jabatan mengandung 'ketua' atau 'hakim', kosongkan catatan cuti
                $('#catatan_cuti').val('');
                return;
            }
            if (jumlahHari > window.cutiSakitQuota) {
                const hariKurang = jumlahHari - window.cutiSakitQuota;
                const catatanOtomatis = `Kuota cuti sakit kurang ${hariKurang} hari dan dikenakan pemotongan gaji sebesar 5% per kuota yang kurang`;
                // Selalu update jika catatan cuti adalah auto-fill atau kosong
                const currentCatatan = $('#catatan_cuti').val();
                if (!currentCatatan || currentCatatan.includes('Kuota cuti sakit kurang')) {
                    $('#catatan_cuti').val(catatanOtomatis);
                }
            } else {
                // Jika jumlah hari sudah tidak melebihi kuota, hapus auto-fill jika ada
                const currentCatatan = $('#catatan_cuti').val();
                if (currentCatatan && currentCatatan.includes('Kuota cuti sakit kurang')) {
                    $('#catatan_cuti').val('');
                }
            }
        }
        
        // Cuti Alasan Penting (id=5) - Tidak ada lagi isi otomatis catatan cuti
        // Validasi maksimal 30 hari akan dilakukan saat submit
        
        // Untuk jenis cuti lain, biarkan kosong agar user input manual
        // Tidak perlu mengisi default untuk jenis cuti selain cuti sakit
    }
    
    // Panggil fungsi ketika jenis cuti berubah atau jumlah hari berubah
    // This event listener is now handled by the new $('#leave_type_id').on('change', ...)
    
    // Panggil fungsi ketika tanggal berubah (jumlah hari akan berubah)
    $('#tanggal_mulai, #tanggal_selesai').on('change', function() {
        setTimeout(function() {
            updateCatatanCutiOtomatis();
            checkAndFillCatatanCuti();
        }, 100);
    });

    // Pastikan perhitungan hari kerja tetap berjalan jika tanggal diubah manual
    $('#tanggal_mulai, #tanggal_selesai').on('change', function() {
        setTimeout(calculateDays, 100);
    });

    // Event untuk menampilkan nama file yang dipilih pada dokumen pendukung
    $('#dokumen_pendukung').on('change', function() {
        const file = this.files[0];
        if (file) {
            // Validasi tipe file
            if (file.type !== 'application/pdf') {
                Swal.fire({
                    icon: 'error',
                    title: 'Format File Tidak Didukung',
                    text: 'Hanya file PDF yang diperbolehkan untuk dokumen pendukung!',
                    confirmButtonColor: '#1b5e20'
                });
                // Reset input file
                $(this).val('');
                $('#fileName').text('Klik untuk upload dokumen PDF');
                $('#dokumenActions').hide();
                return;
            }
            
            // Validasi ukuran file (5MB)
            const maxSize = 5 * 1024 * 1024; // 5MB dalam bytes
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran File Terlalu Besar',
                    text: 'Ukuran file maksimal 5MB!',
                    confirmButtonColor: '#1b5e20'
                });
                // Reset input file
                $(this).val('');
                $('#fileName').text('Klik untuk upload dokumen PDF');
                $('#dokumenActions').hide();
                return;
            }
            
            $('#fileName').text(file.name);
            $('#dokumenActions').show();
        } else {
            $('#fileName').text('Klik untuk upload dokumen PDF');
            $('#dokumenActions').hide();
        }
    });

    // Event tombol ganti dokumen
    $('#gantiDokumenBtn').on('click', function() {
        // Reset input file
        $('#dokumen_pendukung').val('');
        $('#fileName').text('Klik untuk upload dokumen PDF');
        $('#dokumenActions').hide();
        $('#dokumen_pendukung').click();
    });

    // Event tombol hapus dokumen
    $('#hapusDokumenBtn').on('click', function() {
        $('#dokumen_pendukung').val('');
        $('#fileName').text('Klik untuk upload dokumen PDF');
        $('#dokumenActions').hide();
        // Jika ada leaveId (draft sudah ada), panggil backend untuk hapus file
        if (window.currentLeaveId) {
            $.post(baseUrl('leave/deleteSupportingDoc'), { leave_id: window.currentLeaveId }, function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        confirmButtonColor: '#1b5e20'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message,
                        confirmButtonColor: '#1b5e20'
                    });
                }
            }, 'json');
        }
    });

    // Show/hide sisa kuota & sisa setelah cuti sesuai jenis cuti
    function updateKuotaDisplay() {
        const typeId = $('#leave_type_id').val();
        if (typeId == 1) { // Cuti Tahunan
            $('#sisaKuotaWrapper').show();
            $('#sisaSetelahWrapper').show();
            $('#sisaKuota').text(currentBalance);
        } else if (typeId == 3) { // Cuti Sakit
            $('#sisaKuotaWrapper').show();
            $('#sisaSetelahWrapper').show();
            $('#sisaKuota').text(window.cutiSakitQuota);
        } else {
            $('#sisaKuotaWrapper').hide();
            $('#sisaSetelahWrapper').hide();
        }
    }
    // Panggil saat jenis cuti berubah
    $('#leave_type_id').on('change', function() {
        updateKuotaDisplay();
    });

    // Inisialisasi tampilan awal
    updateKuotaDisplay();
    // Set initial state for catatan cuti (readonly or editable)
    updateCatatanFieldState();

    // Pantau perubahan jumlah hari menggunakan MutationObserver
    const jumlahHariNode = document.getElementById('jumlahHari');
    if (jumlahHariNode) {
        const observer = new MutationObserver(function(mutationsList, observer) {
            checkAndFillCatatanCuti();
        });
        observer.observe(jumlahHariNode, { childList: true, characterData: true, subtree: true });
    }
});
</script>