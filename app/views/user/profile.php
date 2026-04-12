<!-- 
Author: Rijal Imamul Haq Syamsu Alam
Lisensi Kepada: Pengadilan Tinggi Agama Makassar
-->

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-person-circle me-2"></i>Informasi Profile</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td data-label="Nama"><strong>Nama</strong></td>
                        <td data-label="Nama">: <?php echo $_SESSION['nama']; ?></td>
                    </tr>
                    <tr>
                        <td data-label="NIP"><strong>NIP</strong></td>
                        <td data-label="NIP">: <?php echo $_SESSION['nip']; ?></td>
                    </tr>
                    <tr>
                        <td data-label="Jabatan"><strong>Jabatan</strong></td>
                        <td data-label="Jabatan">: <?php echo $_SESSION['jabatan']; ?></td>
                    </tr>
                    <tr>
                        <td data-label="Golongan"><strong>Golongan</strong></td>
                        <td data-label="Golongan">: <?php echo $_SESSION['golongan']; ?></td>
                    </tr>
                    <tr>
                        <td data-label="Unit Kerja"><strong>Unit Kerja</strong></td>
                        <td data-label="Unit Kerja">: <?php require_once __DIR__ . '/../../helpers/satker_helper.php'; echo get_nama_satker($_SESSION['unit_kerja']); ?></td>
                    </tr>
                    <tr>
                        <td data-label="Username"><strong>Username</strong></td>
                        <td data-label="Username">: <?php echo $_SESSION['username']; ?></td>
                    </tr>
                    <tr>
                        <td data-label="Role"><strong>Role</strong></td>
                        <td data-label="Role">: <?php echo $_SESSION['user_type'] == 'admin' ? 'Admin' : (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'atasan' ? 'Atasan' : 'Pegawai'); ?></td>
                    </tr>
                    <tr>
                        <td data-label="Masa Kerja"><strong>Masa Kerja</strong></td>
                        <td data-label="Masa Kerja">: <?php echo hitungMasaKerja($_SESSION['tanggal_masuk']); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Keamanan Akun</h6>
            </div>
            <div class="card-body">
                <form id="changePasswordForm">
                    <div class="mb-3">
                        <label for="old_password" class="form-label">Password Lama</label>
                        <input type="password" class="form-control" id="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="new_password" required>
                        <small class="text-muted">Minimal 6 karakter</small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-key me-2"></i>Ubah Password
                    </button>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <p class="text-muted mb-2">Login Terakhir</p>
                    <small><?php echo date('d F Y H:i'); ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quota Information -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Informasi Kuota Cuti</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tahun</th>
                                <th>Kuota Tahunan</th>
                                <th>Terpakai</th>
                                <th>Sisa</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody id="quotaTableBody">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    /* Data profile rata kiri dan tetap table normal */
    .card-body table.table-borderless td {
        text-align: left !important;
        vertical-align: top !important;
        display: table-cell !important;
        width: auto !important;
        justify-content: flex-start !important;
        align-items: flex-start !important;
        padding: 0.5rem 0.75rem !important;
        border: none !important;
        font-size: 1rem !important;
    }
    .card-body table.table-borderless, .card-body table.table-borderless tr, .card-body table.table-borderless td {
        display: table !important;
    }
    /* Tabel kuota cuti: thead di kiri, tbody data di kanan */
    .table-responsive {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
        border: 1px solid #dee2e6 !important;
        box-sizing: border-box !important;
    }

    .table-responsive .table.table-bordered {
        margin: 0 !important;
        display: flex !important;
        flex-direction: row !important;
        border: none !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    .table-responsive .table.table-bordered thead {
        display: flex !important;
        flex-direction: row !important;
        flex-shrink: 0 !important;
        background-color: var(--bs-card-cap-bg);
        width: 40% !important;
        max-width: 150px !important;
        min-width: 0 !important;
    }

    .table-responsive .table.table-bordered tbody {
        display: flex !important;
        flex-direction: row !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        flex-grow: 1 !important;
        width: 60% !important;
        min-width: 0 !important;
    }

    .table-responsive .table.table-bordered th,
    .table-responsive .table.table-bordered td {
        display: block !important;
        width: 100% !important;
        padding: 0.5rem 0.75rem !important;
        font-size: 0.95rem !important;
        border: none !important;
        border-bottom: 1px solid #dee2e6 !important;
        border-right: 1px solid #dee2e6 !important;
        box-sizing: border-box !important;
        white-space: nowrap !important;
        min-height: 45px !important;
    }

    .table-responsive .table.table-bordered tr th:last-child {
        border-bottom: none !important;
    }

    .table-responsive .table.table-bordered tr:last-child td {
        border-bottom: none !important;
    }

    .table-responsive .table.table-bordered tr {
        display: flex !important;
        flex-direction: column !important;
        flex: 1 1 auto !important;
        min-width: 110px !important;
    }
}
</style>

<script>
$(document).ready(function() {
    loadQuotaInfo();
    
    // Handle change password
    $('#changePasswordForm').submit(function(e) {
        e.preventDefault();
        
        const formData = {
            old_password: $('#old_password').val(),
            new_password: $('#new_password').val(),
            confirm_password: $('#confirm_password').val()
        };
        
        $.post(baseUrl('auth/changePassword'), formData, function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                    confirmButtonColor: '#1b5e20'
                });
                $('#changePasswordForm')[0].reset();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.message,
                    confirmButtonColor: '#1b5e20'
                });
            }
        }, 'json');
    });
    
    function loadQuotaInfo() {
        const currentYear = new Date().getFullYear();
        const years = [currentYear - 2, currentYear - 1, currentYear];
        const tbody = $('#quotaTableBody');
        tbody.empty();
        
        let totalQuota = 0;
        let totalUsed = 0;
        let results = [];
        let processed = 0;
        
        years.forEach(function(year, idx) {
            $.post(baseUrl('leave/getBalance'), {
                tahun: year
            }, function(response) {
                if (response.success) {
                    const data = response.data;
                    const used = data.kuota_tahunan - data.sisa_kuota;
                    const percentage = (used / data.kuota_tahunan * 100).toFixed(1);
                    results[idx] = {
                        year: year,
                        kuota: data.kuota_tahunan,
                        used: used,
                        sisa: data.sisa_kuota,
                        percentage: percentage
                    };
                    totalQuota += data.kuota_tahunan;
                    totalUsed += used;
                } else {
                    results[idx] = {
                        year: year,
                        kuota: 0,
                        used: 0,
                        sisa: 0,
                        percentage: 0
                    };
                }
                processed++;
                if (processed === years.length) {
                    // Render setelah semua data diterima
                    results.forEach(function(item) {
                        const progressClass = item.percentage > 80 ? 'danger' : 
                                              item.percentage > 50 ? 'warning' : 'success';
                        tbody.append(`
                            <tr>
                                <td data-label="Tahun">${item.year}</td>
                                <td data-label="Kuota Tahunan">${item.kuota} hari</td>
                                <td data-label="Terpakai">${item.used} hari</td>
                                <td data-label="Sisa"><strong>${item.sisa} hari</strong></td>
                                <td data-label="Persentase">
                                    <div class="progress">
                                        <div class="progress-bar bg-${progressClass}" 
                                             role="progressbar" 
                                             style="width: ${item.percentage}%">
                                            ${item.percentage}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `);
                    });
                    const totalPercentage = (totalUsed / totalQuota * 100).toFixed(1);
                    tbody.append(`
                        <tr class="table-secondary">
                            <td><strong>TOTAL</strong></td>
                            <td><strong>${totalQuota} hari</strong></td>
                            <td><strong>${totalUsed} hari</strong></td>
                            <td><strong>${totalQuota - totalUsed} hari</strong></td>
                            <td><strong>${totalPercentage}%</strong></td>
                        </tr>
                    `);
                }
            }, 'json');
        });
    }
});
</script>