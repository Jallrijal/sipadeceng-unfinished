<?php
$title = 'SOP Pengajuan Cuti';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">SOP Pengajuan Cuti</h1>
        <a href="<?php echo baseUrl('dashboard'); ?>" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Standar Operasional Prosedur (SOP) Pengajuan Cuti</h6>
        </div>
        <div class="card-body">
            <div class="timeline-container">
                <ol class="list-group list-group-numbered">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Mengisi Form Pengajuan</div>
                            Pegawai mengisi form pengajuan cuti di sistem. Pastikan untuk memilih jenis cuti, tanggal mulai dan selesai, serta melengkapi dokumen pendukung (jika diperlukan untuk jenis cuti tertentu seperti Cuti Sakit atau Cuti Alasan Penting).
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Persetujuan Atasan Langsung</div>
                            Setelah diajukan, permohonan cuti akan masuk ke Atasan Langsung untuk diverifikasi dan diberikan persetujuan (ACC).
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Verifikasi Berjenjang (Sesuai Struktur)</div>
                            Permohonan yang disetujui atasan akan diteruskan ke pejabat struktural di atasnya sesuai dengan alur birokrasi (seperti Kasubbag, Kabag, dan Sekretaris). Setiap tahapan akan melakukan verifikasi berkas dan sisa cuti.
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Persetujuan Pimpinan (Ketua)</div>
                            Setelah melewati verifikasi berjenjang, permohonan akan disetujui dan ditandatangani secara digital oleh Pimpinan yang berwenang (Ketua).
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Selesai / Cetak Surat Cuti</div>
                            Setelah disetujui sepenuhnya, status permohonan akan berubah menjadi "Selesai". Pegawai dapat mengunduh dan mencetak Surat Izin Cuti yang telah ditandatangani secara digital.
                        </div>
                    </li>
                </ol>
            </div>

            <div class="alert alert-info mt-4" role="alert">
                <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Informasi Tambahan</h6>
                <ul class="mb-0">
                    <li>Pengajuan cuti tahunan sebaiknya dilakukan minimal 3 hari kerja sebelum tanggal pelaksanaan cuti.</li>
                    <li>Cuti Sakit yang lebih dari 1 hari wajib melampirkan Surat Keterangan Dokter.</li>
                    <li>Untuk cuti tahunan, terdapat kuota maksimal pegawai yang bisa cuti pada tanggal yang sama (maksimal 30% dari total pegawai per unit kerja).</li>
                </ul>
            </div>
        </div>
    </div>
</div>
