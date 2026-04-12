<!-- 
Author: Rijal Imamul Haq Syamsu Alam
Lisensi Kepada: Pengadilan Tinggi Agama Makassar
-->

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><?php echo $page_title; ?></h5>
                </div>
                <div class="card-body">
                    <form id="atasanForm">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <input type="hidden" name="id" value="<?php echo $atasan ? $atasan['id_atasan'] : ''; ?>">
                        
                        <?php if ($action == 'add'): ?>
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Informasi:</strong> Semua field yang ditandai dengan <span class="text-danger">*</span> adalah wajib diisi.
                        </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="nama_atasan" class="form-label">Nama Atasan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_atasan" name="nama_atasan" value="<?php echo $atasan ? $atasan['nama_atasan'] : ''; ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="NIP" class="form-label">NIP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="NIP" name="NIP" value="<?php echo $atasan ? $atasan['NIP'] : ''; ?>" required>
                                <small class="text-muted">NIP harus minimal 14 digit</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="jabatan" name="jabatan" value="<?php echo $atasan ? $atasan['jabatan'] : ''; ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-control" id="role" name="role">
                                    <option value="" <?php echo (!$atasan || empty($atasan['role'])) ? 'selected' : ''; ?>>-- Tidak ada / Normal --</option>
                                    <option value="kasubbag" <?php echo ($atasan && $atasan['role'] === 'kasubbag') ? 'selected' : ''; ?>>Kasubbag</option>
                                    <option value="kabag" <?php echo ($atasan && $atasan['role'] === 'kabag') ? 'selected' : ''; ?>>Kabag</option>
                                    <option value="sekretaris" <?php echo ($atasan && $atasan['role'] === 'sekretaris') ? 'selected' : ''; ?>>Sekretaris</option>
                                    <option value="ketua" <?php echo ($atasan && $atasan['role'] === 'ketua') ? 'selected' : ''; ?>>Ketua</option>
                                </select>
                                <small class="text-muted">(opsional) Memodifikasi peran atasan untuk alur persetujuan</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="<?php echo baseUrl('atasan/index'); ?>" class="btn btn-secondary">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#atasanForm').on('submit', function(e) {
        e.preventDefault();
        
        // Show loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Sedang menyimpan data atasan',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.post(baseUrl('atasan/save'), $(this).serialize(), function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    showConfirmButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = baseUrl('atasan/index');
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: response.message
                });
            }
        }, 'json');
    });
    
    // NIP validation
    $('#NIP').on('input', function() {
        const nip = $(this).val();
        if (nip.length > 0 && nip.length < 14) {
            $(this).addClass('is-invalid');
            $(this).removeClass('is-valid');
        } else if (nip.length >= 14) {
            $(this).removeClass('is-invalid');
            $(this).addClass('is-valid');
        } else {
            $(this).removeClass('is-invalid is-valid');
        }
    });
});
</script>
