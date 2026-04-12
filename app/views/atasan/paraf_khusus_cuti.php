<!-- 
Author: Rijal Imamul Haq Syamsu Alam
Lisensi Kepada: Pengadilan Tinggi Agama Makassar
-->

<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">
            <i class="bi bi-pen-fill"></i> Manajemen Paraf Khusus Atasan Cuti
        </h5>
        
        <div class="alert alert-info mb-4" role="alert">
            <i class="bi bi-info-circle"></i>
            <strong>Perhatian:</strong> Paraf khusus ini akan digunakan pada dokumen cuti untuk keperluan persetujuan sebagai atasan cuti. 
            Pastikan paraf sudah dalam format yang jelas dan sesuai standar.
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 bg-light">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-eye"></i> Paraf Saat Ini</h6>
                    </div>
                    <div class="card-body">
                        <?php if ($paraf): ?>
                            <div style="border:1px solid #ccc; background:#f8f9fa; padding:15px; border-radius:8px; text-align:center;">
                                <h6 class="mb-3">Preview Paraf</h6>
                                <img src="<?php echo getSignatureUrl($paraf['signature_file']); ?>"
                                     alt="Paraf Atasan Cuti"
                                     style="max-width:150px; max-height:80px; display:block; margin:0 auto; border:1px solid #ddd; background:white; padding:5px;">
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <strong>File:</strong> <?php echo htmlspecialchars($paraf['signature_file']); ?><br>
                                        <strong>Ukuran:</strong> <?php echo number_format($paraf['file_size'] / 1024, 1); ?> KB<br>
                                        <strong>Upload:</strong> <?php echo date('d M Y H:i', strtotime($paraf['created_at'])); ?>
                                    </small>
                                </div>
                            </div>
                            <div class="mt-3 d-flex gap-2 flex-column">
                                <button type="button" class="btn btn-sm btn-info" onclick="previewFullSize('<?php echo getSignatureUrl($paraf['signature_file']); ?>')">
                                    <i class="bi bi-zoom-in"></i> Preview Full Size
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteParaf()">
                                    <i class="bi bi-trash"></i> Hapus Paraf
                                </button>
                            </div>
                        <?php else: ?>
                            <div style="border:2px dashed #ccc; background:#fff; padding:30px; border-radius:8px; text-align:center;">
                                <i class="bi bi-image" style="font-size:3rem; color:#ccc;"></i>
                                <p class="text-muted mt-3 mb-0">Belum ada paraf yang diupload</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 bg-light">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-cloud-upload"></i> Upload Paraf Khusus</h6>
                    </div>
                    <div class="card-body">
                        <form id="parafAtasanUploadForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="paraf_file" class="form-label">Pilih File Paraf</label>
                                <input type="file" class="form-control" id="paraf_file" name="paraf_file" 
                                       accept=".png,.jpg,.jpeg,.gif" required>
                                <small class="form-text text-muted d-block mt-2">
                                    Format: PNG, JPG, atau GIF | Ukuran max: 1 MB
                                </small>
                            </div>

                            <!-- File Preview -->
                            <div id="filePreviewContainer" class="mb-3" style="display:none;">
                                <label class="form-label">Preview File yang Dipilih:</label>
                                <div style="border:1px solid #ddd; background:#fff; padding:10px; border-radius:6px; text-align:center;">
                                    <img id="previewImage" src="" alt="Preview" 
                                         style="max-width:120px; max-height:80px; display:block; margin:0 auto;">
                                </div>
                            </div>

                            <div class="alert alert-warning alert-sm mb-3" id="fileSizeWarning" style="display:none;">
                                <small><i class="bi bi-exclamation-triangle"></i> Ukuran file melebihi batas (max 1 MB)</small>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-upload"></i> Upload/Update Paraf
                            </button>
                        </form>

                        <div class="mt-3">
                            <small class="form-text text-secondary">
                                <strong>Tips:</strong> Pastikan paraf:
                                <ul class="mb-0 ps-3">
                                    <li>Jelas dan mudah dibaca</li>
                                    <li>Konsisten dengan gaya tanda tangan Anda</li>
                                    <li>Di-scan dalam resolusi yang baik</li>
                                </ul>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-secondary" role="alert">
                    <small>
                        <strong>Catatan:</strong> Paraf khusus atasan cuti digunakan pada dokumen cuti untuk keperluan persetujuan dan 
                        pengesahan oleh atasan cuti (Kasubbag, Kabag, atau Sekretaris). Setiap perubahan akan langsung 
                        berlaku untuk semua dokumen cuti yang baru.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for full size preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Preview Paraf - Ukuran Penuh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalPreviewImage" src="" alt="Preview" style="max-width:100%; height:auto;">
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Handle file selection preview
document.getElementById('paraf_file').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        // Check file size (1MB)
        const maxSize = 1 * 1024 * 1024;
        const fileSizeWarning = document.getElementById('fileSizeWarning');
        
        if (file.size > maxSize) {
            fileSizeWarning.style.display = 'block';
        } else {
            fileSizeWarning.style.display = 'none';
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function (event) {
            document.getElementById('previewImage').src = event.target.result;
            document.getElementById('filePreviewContainer').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// Handle form submission
document.getElementById('parafAtasanUploadForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const fileInput = document.getElementById('paraf_file');
    const file = fileInput.files[0];

    if (!file) {
        Swal.fire('Error', 'Pilih file terlebih dahulu', 'error');
        return;
    }

    // Client-side validation
    if (!['image/png', 'image/jpeg', 'image/jpg', 'image/gif'].includes(file.type)) {
        Swal.fire('Error', 'Format file harus PNG, JPG, atau GIF', 'error');
        return;
    }

    if (file.size > 1 * 1024 * 1024) {
        Swal.fire('Error', 'Ukuran file tidak boleh lebih dari 1 MB', 'error');
        return;
    }

    // Upload file
    const formData = new FormData();
    formData.append('paraf_file', file);

    Swal.fire({
        title: 'Mengupload Paraf...',
        text: 'Mohon tunggu sebentar',
        icon: 'info',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('<?php echo baseUrl("signature/uploadParafAtasanCuti"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Paraf khusus atasan cuti berhasil diupload',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error', data.message || 'Gagal mengupload paraf', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan saat mengupload paraf', 'error');
    });
});

// Delete paraf
function deleteParaf() {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus paraf ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus Paraf...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('<?php echo baseUrl("signature/deleteParafAtasanCuti"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil',
                        text: 'Paraf berhasil dihapus',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Gagal menghapus paraf', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat menghapus paraf', 'error');
            });
        }
    });
}

// Preview full size
function previewFullSize(imageUrl) {
    document.getElementById('modalPreviewImage').src = imageUrl;
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}
</script>
