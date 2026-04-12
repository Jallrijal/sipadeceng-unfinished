<!-- 
Author: Rijal Imamul Haq Syamsu Alam
Lisensi Kepada: Pengadilan Tinggi Agama Makassar
-->

<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Manajemen Tanda Tangan Digital</h5>
        <div class="mb-3">
            <label class="form-label">Tanda Tangan Saat Ini:</label><br>
            <?php if ($signature): ?>
                <div style="display:inline-block; border:1px solid #ccc; background:#f8f9fa; padding:8px; border-radius:6px; vertical-align:middle;">
                    <img src="<?php echo getSignatureUrl($signature['signature_file']); ?>"
                         alt="Tanda Tangan"
                         style="max-width:220px; max-height:100px; display:block;">
                </div>
                <button type="button" class="btn btn-danger btn-sm ms-2" onclick="deleteSignature()">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            <?php else: ?>
                <span class="text-muted">Belum ada tanda tangan.</span>
            <?php endif; ?>
        </div>
        <form id="signatureUploadForm" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="signature_file" class="form-label">Upload/Update Tanda Tangan (PNG/JPG, max 2MB)</label>
                <input type="file" class="form-control" id="signature_file" name="signature_file" accept=".png,.jpg,.jpeg,.gif" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload/Update</button>
        </form>
        <div class="mt-3">
            <small class="text-muted">Tanda tangan ini akan digunakan pada dokumen cuti Anda.</small>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Fungsi untuk menghapus tanda tangan
function deleteSignature() {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus tanda tangan ini?',
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
                title: 'Menghapus Tanda Tangan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Kirim request delete
            fetch('<?php echo baseUrl('signature/delete'); ?>', {
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
                    text: 'Terjadi kesalahan saat menghapus tanda tangan.'
                });
            });
        }
    });
}

document.getElementById('signatureUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var formData = new FormData(form);

    // Tampilkan loading
    Swal.fire({
        title: 'Mengupload Tanda Tangan...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('<?php echo baseUrl('signature/upload'); ?>', {
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
</script> 