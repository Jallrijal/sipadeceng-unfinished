<?php
// Akses kontrol - hanya admin yang bisa akses
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    ?>
    <div class="alert alert-danger" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Akses Ditolak</strong><br>
        Fitur Manajemen Paraf Petugas Cuti hanya dapat diakses oleh Administrator.
    </div>
    <?php
    return;
}
?>
<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Manajemen Paraf Petugas Cuti</h5>
        <div class="mb-3">
            <label class="form-label">Paraf Saat Ini:</label><br>
            <?php if ($paraf): ?>
                <div class="row">
                    <div class="col-md-6">
                        <div style="border:1px solid #ccc; background:#f8f9fa; padding:15px; border-radius:8px; text-align:center;">
                            <h6 class="mb-2">Preview Paraf</h6>
                            <img src="<?php echo getSignatureUrl($paraf['signature_file']); ?>"
                                 alt="Paraf Petugas Cuti"
                                 style="max-width:120px; max-height:60px; display:block; margin:0 auto; border:1px solid #ddd; background:white; padding:5px;">
                            <div class="mt-2">
                                <small class="text-muted">
                                    Ukuran tampilan di blanko: 60x30 pixel
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="border:1px solid #ccc; background:#f8f9fa; padding:15px; border-radius:8px;">
                            <h6 class="mb-2">Informasi File</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td><strong>Nama File:</strong></td>
                                    <td><?php echo htmlspecialchars($paraf['signature_file']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Ukuran:</strong></td>
                                    <td><?php echo number_format($paraf['file_size'] / 1024, 1); ?> KB</td>
                                </tr>
                                <tr>
                                    <td><strong>Tipe:</strong></td>
                                    <td><?php echo strtoupper(pathinfo($paraf['signature_file'], PATHINFO_EXTENSION)); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Upload:</strong></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($paraf['created_at'])); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteParaf()">
                        <i class="bi bi-trash"></i> Hapus Paraf
                    </button>
                    <a href="<?php echo getSignatureUrl($paraf['signature_file']); ?>" target="_blank" class="btn btn-info btn-sm ms-2">
                        <i class="bi bi-eye"></i> Lihat Full Size
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm ms-2" onclick="showParafModal('<?php echo getSignatureUrl($paraf['signature_file']); ?>', '<?php echo htmlspecialchars($paraf['signature_file']); ?>')">
                        <i class="bi bi-zoom-in"></i> Preview Detail
                    </button>
                </div>
            <?php else: ?>
                <div style="border:2px dashed #ccc; background:#f8f9fa; padding:30px; border-radius:8px; text-align:center;">
                    <i class="bi bi-image" style="font-size:3rem; color:#ccc;"></i>
                    <p class="text-muted mt-2 mb-0">Belum ada paraf yang diupload</p>
                </div>
            <?php endif; ?>
        </div>
        <form id="parafUploadForm" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="paraf_file" class="form-label">Upload/Update Paraf (PNG/JPG, max 1MB)</label>
                <input type="file" class="form-control" id="paraf_file" name="paraf_file" accept=".png,.jpg,.jpeg,.gif" required>
                <div class="form-text">
                    <strong>Catatan:</strong> Paraf ini akan digunakan pada kolom paraf petugas cuti di tabel V pada blanko cuti. 
                    Ukuran gambar akan disesuaikan secara otomatis agar proporsional dengan kolom paraf.
                </div>
            </div>
            
            <!-- Preview file yang dipilih -->
            <div id="filePreview" class="mb-3" style="display:none;">
                <label class="form-label">Preview File yang Dipilih:</label>
                <div class="row">
                    <div class="col-md-6">
                        <div style="border:1px solid #ccc; background:#f8f9fa; padding:15px; border-radius:8px; text-align:center;">
                            <h6 class="mb-2">Preview</h6>
                            <img id="previewImage" src="" alt="Preview" style="max-width:120px; max-height:60px; display:block; margin:0 auto; border:1px solid #ddd; background:white; padding:5px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="border:1px solid #ccc; background:#f8f9fa; padding:15px; border-radius:8px;">
                            <h6 class="mb-2">Informasi File</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td><strong>Nama:</strong></td>
                                    <td id="fileName"></td>
                                </tr>
                                <tr>
                                    <td><strong>Ukuran:</strong></td>
                                    <td id="fileSize"></td>
                                </tr>
                                <tr>
                                    <td><strong>Tipe:</strong></td>
                                    <td id="fileType"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Upload/Update Paraf</button>
        </form>
        <div class="mt-3">
            <small class="text-muted">
                <strong>Fitur Paraf Petugas Cuti:</strong><br>
                • Paraf akan otomatis muncul di kolom paraf petugas cuti pada blanko cuti<br>
                • Jika belum mengunggah paraf, kolom paraf akan kosong<br>
                • Ukuran paraf akan disesuaikan secara otomatis agar proporsional<br>
                • Format yang didukung: PNG, JPG, JPEG, GIF (maksimal 1MB)
            </small>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Preview file yang dipilih
document.getElementById('paraf_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const previewDiv = document.getElementById('filePreview');
    const previewImage = document.getElementById('previewImage');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const fileType = document.getElementById('fileType');
    
    if (file) {
        // Validasi ukuran file (1MB = 1024 * 1024 bytes)
        if (file.size > 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran File Terlalu Besar',
                text: 'Ukuran file maksimal 1MB. File Anda: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB'
            });
            this.value = '';
            previewDiv.style.display = 'none';
            return;
        }
        
        // Validasi tipe file
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Format File Tidak Didukung',
                text: 'Hanya file PNG, JPG, JPEG, atau GIF yang diizinkan.'
            });
            this.value = '';
            previewDiv.style.display = 'none';
            return;
        }
        
        // Tampilkan preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            fileName.textContent = file.name;
            fileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
            fileType.textContent = file.type.split('/')[1].toUpperCase();
            previewDiv.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        previewDiv.style.display = 'none';
    }
});

// Upload form
document.getElementById('parafUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var formData = new FormData(form);

    // Tampilkan loading
    Swal.fire({
        title: 'Mengupload Paraf...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('<?php echo baseUrl('signature/uploadParaf'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message
            });
        }
    })
    .catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Terjadi kesalahan saat upload.'
        });
    });
});

// Fungsi untuk menghapus paraf
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
            // Tampilkan loading
            Swal.fire({
                title: 'Menghapus Paraf...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Kirim request delete
            fetch('<?php echo baseUrl('signature/deleteParaf'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat menghapus paraf.'
                });
            });
        }
    });
}

// Fungsi untuk menampilkan modal preview detail
function showParafModal(imageUrl, fileName) {
    document.getElementById('modalParafImage').src = imageUrl;
    document.getElementById('modalParafSmall').src = imageUrl;
    document.getElementById('modalParafMedium').src = imageUrl;
    document.getElementById('modalFileName').textContent = fileName;
    document.getElementById('modalDownloadLink').href = imageUrl;
    
    const modal = new bootstrap.Modal(document.getElementById('parafModal'));
    modal.show();
}
</script>

<!-- Modal Preview Detail -->
<div class="modal fade" id="parafModal" tabindex="-1" aria-labelledby="parafModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="parafModalLabel">Preview Detail Paraf</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <h6>Ukuran Asli</h6>
                    <img id="modalParafImage" src="" alt="Paraf Detail" style="max-width:100%; max-height:400px; border:1px solid #ddd; background:white; padding:10px;">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Ukuran di Blanko (60x30 pixel)</h6>
                        <img id="modalParafSmall" src="" alt="Paraf Kecil" style="width:60px; height:30px; border:1px solid #ddd; background:white; padding:2px; object-fit:contain;">
                    </div>
                    <div class="col-md-6">
                        <h6>Ukuran Preview (120x60 pixel)</h6>
                        <img id="modalParafMedium" src="" alt="Paraf Sedang" style="width:120px; height:60px; border:1px solid #ddd; background:white; padding:5px; object-fit:contain;">
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted" id="modalFileName"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a id="modalDownloadLink" href="" target="_blank" class="btn btn-primary">
                    <i class="bi bi-download"></i> Download
                </a>
            </div>
        </div>
    </div>
</div> 