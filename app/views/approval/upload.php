<!-- 
Author: Rijal Imamul Haq Syamsu Alam
Lisensi Kepada: Pengadilan Tinggi Agama Makassar
-->

<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Upload Dokumen Final</h5>

        <div class="alert alert-info" role="alert">
            <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Upload Dokumen Final:</h6>
            <ul class="mb-0">
                <li>Upload dokumen cuti yang telah Anda tandatangani sebagai persetujuan final</li>
                <li>Hanya file PDF yang diperbolehkan</li>
                <li>Ukuran file maksimal 3MB</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Pengajuan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nama:</strong></td>
                                    <td><?php echo $leaveData['nama']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>NIP:</strong></td>
                                    <td><?php echo $leaveData['nip']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Unit Kerja:</strong></td>
                                    <td><?php require_once __DIR__ . '/../../helpers/satker_helper.php';
                                    echo get_nama_satker($leaveData['unit_kerja']); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis Cuti:</strong></td>
                                    <td><?php echo $leaveData['nama_cuti']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal:</strong></td>
                                    <td><?php echo formatTanggal($leaveData['tanggal_mulai']); ?> -
                                        <?php echo formatTanggal($leaveData['tanggal_selesai']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td><?php echo getStatusBadge($leaveData['status']); ?></td>
                                </tr>
                                <?php if ($leaveData['status'] == 'rejected' && !empty($leaveData['catatan_approval'])): ?>
                                    <tr>
                                        <td><strong>Catatan:</strong></td>
                                        <td class="text-danger"><?php echo $leaveData['catatan_approval']; ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>

                        <hr>

                        <div class="text-center">
                            <a href="<?php echo baseUrl('leave/downloadGeneratedDoc?id=' . $leaveId); ?>"
                                class="btn btn-primary" target="_blank" rel="noopener">
                                <i class="bi bi-download me-2"></i>Download Blanko User
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="mb-3">Upload Dokumen Final</h6>

                        <?php if ($leaveData['status'] == 'approved'): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Pengajuan telah disetujui.</strong> Silakan upload dokumen yang telah ditandatangani
                                oleh pegawai, atasan langsung, dan pejabat yang berwenang.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Pengajuan telah ditolak.</strong> Silakan upload dokumen penolakan yang telah
                                ditandatangani.
                            </div>
                        <?php endif; ?>

                        <form id="uploadApprovedForm" enctype="multipart/form-data">
                            <input type="hidden" name="leave_id" value="<?php echo $leaveId; ?>">
                            <div class="mb-3">
                                <label class="form-label">Dokumen yang telah ditandatangani</label>
                                <input type="file" class="form-control" id="approved_document" name="approved_document"
                                    accept=".pdf" required>
                                <small class="text-muted">Format: PDF (Max: 3MB)</small>
                            </div>

                            <div class="alert alert-warning mb-3">
                                <small>
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    <strong>Penting:</strong> Pastikan dokumen telah ditandatangani dengan benar dan
                                    hanya dalam format PDF.
                                </small>
                            </div>

                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-upload me-2"></i>Upload Dokumen Final
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="text-center">
            <a href="<?php echo baseUrl('approval'); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Validasi file saat dipilih
        $('#approved_document').change(function () {
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

        $('#uploadApprovedForm').submit(function (e) {
            e.preventDefault();

            const fileInput = $('#approved_document')[0];
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
                    text: 'Ukuran file maksimal 3MB. Silakan pilih file yang lebih kecil.',
                    confirmButtonColor: '#1b5e20'
                });
                return;
            }

            const formData = new FormData(this);

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Upload dokumen final yang telah ditandatangani?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1b5e20',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Upload!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    uploadFinalDocument(formData);
                }
            });
        });

        function uploadFinalDocument(formData) {
            $.ajax({
                url: baseUrl('leave/uploadApprovedDoc'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    Swal.fire({
                        title: 'Uploading...',
                        html: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            confirmButtonColor: '#1b5e20'
                        }).then(() => {
                            window.location.href = baseUrl('approval');
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
                error: function () {
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