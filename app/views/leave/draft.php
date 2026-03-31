<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Upload Dokumen Cuti yang Telah Ditandatangani</h5>
        
        <div class="alert alert-info" role="alert">
            <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Alur Pengajuan Cuti Baru:</h6>
            <ol class="mb-0">
                <li><strong>Download blanko</strong> yang telah dibuat sistem (sudah termasuk tanda tangan Anda)</li>
                <li><strong>Cetak dan tandatangani</strong> blanko tersebut</li>
                <li><strong>Scan blanko</strong> yang telah ditandatangani (format PDF)</li>
                <li><strong>Upload blanko</strong> yang telah ditandatangani</li>
                <li><strong>Admin akan memproses</strong> pengajuan setelah blanko diupload</li>
                <li><strong>Download blanko final</strong> yang telah ditandatangani admin</li>
            </ol>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <i class="bi bi-file-earmark-word text-primary" style="font-size: 3rem;"></i>
                        <h6 class="mt-3">1. Download Blanko</h6>
                        <p class="text-muted">Download blanko cuti yang telah dibuat sistem (sudah termasuk tanda tangan Anda)</p>
                        <a href="<?php echo baseUrl('leave/downloadGeneratedDoc?id=' . $leaveId); ?>" 
                           class="btn btn-primary">
                            <i class="bi bi-download me-2"></i>Download Blanko
                        </a>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Anda dapat download dokumen ini berkali-kali jika diperlukan
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <i class="bi bi-cloud-upload text-success" style="font-size: 3rem;"></i>
                        <h6 class="mt-3">2. Upload Blanko Tertandatangan</h6>
                        <p class="text-muted">Upload blanko yang telah Anda tandatangani</p>
                        
                        <form id="uploadForm" enctype="multipart/form-data">
                            <input type="hidden" name="leave_id" value="<?php echo $leaveId; ?>">
                            <div class="mb-3">
                                <input type="file" class="form-control" id="signed_document" 
                                       name="signed_document" accept=".pdf" required>
                                <small class="text-muted">Format: PDF (Max: 10MB)</small>
                            </div>
                            <button type="submit" class="btn btn-primary" style="background-color: #001f4d; border-color: #001f4d;">
                                <i class="bi bi-upload me-2"></i>Upload Blanko
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-warning" role="alert">
                    <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Penting!</h6>
                    <ul class="mb-0">
                        <li>Pastikan blanko telah ditandatangani dengan benar sebelum diupload</li>
                        <li>Hanya file PDF yang diperbolehkan untuk upload</li>
                        <li>Ukuran file maksimal 10MB</li>
                        <li>Setelah blanko diupload, pengajuan akan dikirim ke admin untuk diproses</li>
                        <li>Anda tidak dapat mengubah pengajuan setelah blanko diupload</li>
                        <li>Admin akan membuat blanko final yang telah ditandatangani setelah memproses pengajuan</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <hr class="my-4">
        
        <div class="text-center">
            <a href="<?php echo baseUrl('leave/history'); ?>" class="btn btn-secondary me-2">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Draft
            </a>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Validasi file saat dipilih
    $('#signed_document').change(function() {
        const file = this.files[0];
        if (file) {
            const allowedTypes = ['application/pdf'];
            const maxSize = 10 * 1024 * 1024; // 10MB
            
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format File Tidak Didukung',
                    text: 'Hanya file PDF yang diperbolehkan. Silakan pilih file PDF.',
                    confirmButtonColor: '#1b5e20'
                });
                this.value = ''; // Reset file input
                return;
            }
            
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran File Terlalu Besar',
                    text: 'Ukuran file maksimal 10MB. Silakan pilih file yang lebih kecil.',
                    confirmButtonColor: '#1b5e20'
                });
                this.value = ''; // Reset file input
                return;
            }
            
            // Tampilkan info file yang dipilih
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            $(this).next('small').html(`File dipilih: ${file.name} (${fileSizeMB} MB)`);
        }
    });
    
    $('#uploadForm').submit(function(e) {
        e.preventDefault();
        
        const fileInput = $('#signed_document')[0];
        if (!fileInput.files[0]) {
            Swal.fire('Error', 'Pilih file terlebih dahulu', 'error');
            return;
        }
        
        // Validasi format file
        const file = fileInput.files[0];
        const allowedTypes = ['application/pdf'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Format File Tidak Didukung',
                text: 'Hanya file PDF yang diperbolehkan. Silakan pilih file PDF.',
                confirmButtonColor: '#1b5e20'
            });
            return;
        }
        
        // Validasi ukuran file (10MB)
        const maxSize = 10 * 1024 * 1024; // 10MB dalam bytes
        if (file.size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran File Terlalu Besar',
                text: 'Ukuran file maksimal 10MB. Silakan pilih file yang lebih kecil.',
                confirmButtonColor: '#1b5e20'
            });
            return;
        }
        
        const formData = new FormData(this);
        
        Swal.fire({
            title: 'Konfirmasi Upload Blanko',
            html: `
                <div class="text-start">
                    <p>Apakah Anda yakin blanko sudah ditandatangani dengan benar?</p>
                    <div class="alert alert-warning">
                        <small><i class="bi bi-exclamation-triangle me-1"></i>
                        Setelah blanko diupload, pengajuan akan dikirim ke admin dan Anda tidak dapat mengubahnya lagi.
                        </small>
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1b5e20',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Upload Blanko!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                uploadDocument(formData);
            }
        });
    });
    
    function uploadDocument(formData) {
        $.ajax({
            url: baseUrl('leave/uploadSignedDoc'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                Swal.fire({
                    title: 'Uploading...',
                    html: 'Mohon tunggu',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        confirmButtonColor: '#1b5e20'
                    }).then(() => {
                        window.location.href = baseUrl('leave');
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
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem',
                    confirmButtonColor: '#1b5e20'
                });
            }
        });
    }
});
</script>