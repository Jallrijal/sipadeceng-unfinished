<!-- 
Author: Rijal Imamul Haq Syamsu Alam
Lisensi Kepada: Pengadilan Tinggi Agama Makassar
-->

<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Dokumen dan Formulir</h5>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 3rem;"></i>
                        <h6 class="mt-3">Formulir Pengajuan Cuti</h6>
                        <p class="text-muted small">Format resmi pengajuan cuti PTA Makassar</p>
                        <a href="#" class="btn btn-primary btn-sm">
                            <i class="bi bi-download me-2"></i>Download
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-file-earmark-text text-primary" style="font-size: 3rem;"></i>
                        <h6 class="mt-3">Panduan Pengajuan Cuti</h6>
                        <p class="text-muted small">Tata cara dan ketentuan cuti pegawai</p>
                        <a href="#" class="btn btn-primary btn-sm">
                            <i class="bi bi-download me-2"></i>Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="my-4">
        
        <h6 class="mb-3">Dokumen Cuti Anda</h6>
        <div class="table-responsive">
            <table class="table table-hover" id="dokumenTable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis Dokumen</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadDokumenData();
    
    function loadDokumenData() {
        $.post(baseUrl('leave/getHistory'), {
            user_id: <?php echo $_SESSION['user_id']; ?>
        }, function(response) {
            if (response.success) {
                const tbody = $('#dokumenTable tbody');
                tbody.empty();
                
                let hasDocuments = false;
                response.data.forEach(function(item) {
                    if (item.status === 'approved' || item.dokumen_pendukung) {
                        hasDocuments = true;
                        tbody.append(`
                            <tr>
                                <td data-label="Tanggal">${item.created_at_formatted}</td>
                                <td data-label="Jenis Dokumen">Pengajuan Cuti - ${item.nama_cuti}</td>
                                <td data-label="Status">${item.status_badge}</td>
                                <td data-label="Aksi">
                                    ${item.status === 'approved' ? `
                                        <a href="${baseUrl('leave/generateBlanko?id=' + item.id)}" 
                                           class="btn btn-sm btn-success me-1" title="Download Blanko">
                                            <i class="bi bi-file-earmark-word"></i>
                                        </a>
                                    ` : ''}
                                    ${item.dokumen_pendukung ? `
                                        <a href="${baseUrl('leave/downloadDocument?file=' + item.dokumen_pendukung)}" 
                                           class="btn btn-sm btn-primary" title="Download Dokumen Pendukung">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    ` : ''}
                                </td>
                            </tr>
                        `);
                    }
                });
                
                if (!hasDocuments) {
                    tbody.append('<tr><td colspan="4" class="text-center">Belum ada dokumen</td></tr>');
                }
            }
        }, 'json');
    }
});
</script>