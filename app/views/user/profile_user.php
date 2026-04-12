<!-- 
Author: Rijal Imamul Haq Syamsu Alam
Lisensi Kepada: Pengadilan Tinggi Agama Makassar
-->

<!-- Navigation Tabs -->
<div class="row mb-4">
    <div class="col-12">
        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profil-tab" data-bs-toggle="tab" data-bs-target="#profil-content"
                    type="button" role="tab" aria-controls="profil-content" aria-selected="true">
                    <i class="bi bi-person-circle me-2"></i>Informasi Profil
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="keamanan-tab" data-bs-toggle="tab" data-bs-target="#keamanan-content"
                    type="button" role="tab" aria-controls="keamanan-content" aria-selected="false">
                    <i class="bi bi-shield-lock me-2"></i>Keamanan Akun
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="kuota-tab" data-bs-toggle="tab" data-bs-target="#kuota-content"
                    type="button" role="tab" aria-controls="kuota-content" aria-selected="false">
                    <i class="bi bi-calendar-check me-2"></i>Informasi Kuota Cuti
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email-content"
                    type="button" role="tab" aria-controls="email-content" aria-selected="false">
                    <i class="bi bi-envelope me-2"></i>Kelola Email
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="signature-tab" data-bs-toggle="tab" data-bs-target="#signature-content"
                    type="button" role="tab" aria-controls="signature-content" aria-selected="false">
                    <i class="bi bi-pen me-2"></i>Tanda Tangan
                </button>
            </li>

        </ul>
    </div>
</div>

<!-- Tab Content -->
<div class="tab-content" id="profileTabContent">
    <!-- Informasi Profil Tab -->
    <div class="tab-pane fade show active" id="profil-content" role="tabpanel" aria-labelledby="profil-tab">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-person-circle me-2"></i>Informasi Profil</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="200"><strong>Nama</strong></td>
                                <td>: <?php echo $_SESSION['nama']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>NIP</strong></td>
                                <td>: <?php echo $_SESSION['nip']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Jabatan</strong></td>
                                <td>: <?php echo $_SESSION['jabatan']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Golongan</strong></td>
                                <td>: <?php echo $_SESSION['golongan']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Unit Kerja</strong></td>
                                <td>:
                                    <?php require_once __DIR__ . '/../../helpers/satker_helper.php';
                                    echo get_nama_satker($_SESSION['unit_kerja']); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Username</strong></td>
                                <td>: <?php echo $_SESSION['username']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Role</strong></td>
                                <td>:
                                    <?php echo $_SESSION['user_type'] == 'admin' ? 'Pimpinan' : (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'atasan' ? 'Atasan' : 'Pegawai'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Masa Kerja</strong></td>
                                <td>: <?php echo hitungMasaKerja($_SESSION['tanggal_masuk']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Keamanan Akun Tab -->
    <div class="tab-pane fade" id="keamanan-content" role="tabpanel" aria-labelledby="keamanan-tab">
        <div class="row">
            <div class="col-12">
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
    </div>

    <!-- Informasi Kuota Cuti Tab -->
    <div class="tab-pane fade" id="kuota-content" role="tabpanel" aria-labelledby="kuota-tab">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Informasi Kuota Cuti</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 row">
                            <div class="col-md-6 col-sm-12">
                                <label for="leaveTypeSelect" class="form-label fw-bold">Pilih Jenis Cuti</label>
                                <select class="form-select" id="leaveTypeSelect">
                                    <!-- Options will be loaded via JS -->
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead id="quotaTableHeader">
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
    </div>

    <!-- Kelola Email Tab -->
    <div class="tab-pane fade" id="email-content" role="tabpanel" aria-labelledby="email-tab">
        <div class="row">
            <div class="col-12">
                <?php include __DIR__ . '/email_management.php'; ?>
            </div>
        </div>
    </div>

    <!-- Tanda Tangan Digital Tab -->
    <div class="tab-pane fade" id="signature-content" role="tabpanel" aria-labelledby="signature-tab">
        <div class="row">
            <div class="col-12">
                <div id="signatureManagementDiv">
                    <!-- Loading spinner -->
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<style>
    /* Tab styling */
    .nav-tabs {
        border-bottom: 2px solid #dee2e6;
    }

    .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .nav-link:hover {
        color: #1b5e20;
        border-bottom-color: #1b5e20;
    }

    .nav-link.active {
        color: #fff;
        background-color: #1b5e20;
        border-bottom-color: #1b5e20;
        border-radius: 5px 5px 0 0;
    }

    .tab-content {
        padding: 20px 0;
    }

    @media (max-width: 768px) {
        .nav-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            --webkit-overflow-scrolling: touch;
        }

        .nav-link {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            white-space: nowrap;
        }
    }

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

        .card-body table.table-borderless,
        .card-body table.table-borderless tr,
        .card-body table.table-borderless td {
            display: table !important;
        }

        /* Tabel kuota cuti: thead di kiri, tbody data di kanan */
        .tab-pane .table-responsive {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
            padding-bottom: 0 !important;
            border: 1px solid #dee2e6 !important;
            box-sizing: border-box !important;
        }

        .tab-pane .table-responsive .table.table-bordered {
            margin: 0 !important;
            display: flex !important;
            flex-direction: row !important;
            border: none !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        .tab-pane .table-responsive .table.table-bordered thead {
            display: flex !important;
            flex-direction: row !important;
            flex-shrink: 0 !important;
            background-color: var(--bs-card-cap-bg);
            width: 40% !important;
            max-width: 150px !important;
            min-width: 0 !important;
        }

        .tab-pane .table-responsive .table.table-bordered tbody {
            display: flex !important;
            flex-direction: row !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            flex-grow: 1 !important;
            width: 60% !important;
            min-width: 0 !important;
        }

        .tab-pane .table-responsive .table.table-bordered th,
        .tab-pane .table-responsive .table.table-bordered td {
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

        .tab-pane .table-responsive .table.table-bordered tr th:last-child {
            border-bottom: none !important;
        }

        .tab-pane .table-responsive .table.table-bordered tr td:last-child {
            border-bottom: none !important;
        }

        .tab-pane .table-responsive .table.table-bordered tr {
            display: flex !important;
            flex-direction: column !important;
            flex: 1 1 auto !important;
            min-width: 110px !important;
        }
    }

    @media (max-width: 576px) {
        .nav-link {
            font-size: 0.8rem;
            padding: 0.4rem 0.6rem;
        }

        .nav-link i {
            display: none;
        }
    }
</style>

<script>
    $(document).ready(function () {
        let quotaLoaded = false;
        let signatureLoaded = false;

        // Handle change password
        $('#changePasswordForm').submit(function (e) {
            e.preventDefault();

            const formData = {
                old_password: $('#old_password').val(),
                new_password: $('#new_password').val(),
                confirm_password: $('#confirm_password').val()
            };

            $.post(baseUrl('auth/changePassword'), formData, function (response) {
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

        let allQuotas = [];

        // Tab switching events
        $('#kuota-tab').on('click', function () {
            if (!quotaLoaded) {
                loadAllQuotas();
                quotaLoaded = true;
            }
        });

        $('#signature-tab').on('click', function () {
            if (!signatureLoaded) {
                loadSignatureManagement();
                signatureLoaded = true;
            }
        });



        function loadAllQuotas() {
            $.get(baseUrl('user/getLeaveQuotas'), function (response) {
                if (response.success) {
                    allQuotas = response.data;
                    const select = $('#leaveTypeSelect');
                    select.empty();
                    allQuotas.forEach(function (quota) {
                        select.append(`<option value="${quota.id}">${quota.nama_cuti}</option>`);
                    });
                    // render default
                    select.trigger('change');
                }
            }, 'json');
        }

        $('#leaveTypeSelect').on('change', function () {
            const leaveTypeId = parseInt($(this).val());
            const thead = $('#quotaTableHeader');
            const tbody = $('#quotaTableBody');
            
            tbody.empty();
            if (leaveTypeId === 1) {
                // Cuti Tahunan
                thead.html(`
                    <tr>
                        <th>Tahun</th>
                        <th>Kuota Tahunan</th>
                        <th>Terpakai</th>
                        <th>Sisa</th>
                        <th>Persentase</th>
                    </tr>
                `);
                loadQuotaInfo();
            } else {
                const quota = allQuotas.find(q => q.id === leaveTypeId);
                if (!quota) return;
                
                if (quota.is_akumulatif === 0) {
                    if (leaveTypeId === 4 && quota.kesempatan_sisa !== undefined) {
                        // Cuti Melahirkan dg Batas Kesempatan
                        const totalKesempatan = 3;
                        const terpakai = totalKesempatan - quota.kesempatan_sisa;
                        const percentage = (terpakai / totalKesempatan * 100).toFixed(1);
                        const progressClass = percentage > 80 ? 'danger' : percentage > 50 ? 'warning' : 'success';
                        
                        thead.html(`
                            <tr>
                                <th>Jenis Cuti</th>
                                <th>Total Kesempatan</th>
                                <th>Terpakai</th>
                                <th>Sisa Kesempatan</th>
                                <th>Persentase</th>
                            </tr>
                        `);
                        tbody.html(`
                            <tr>
                                <td>${quota.nama_cuti}<br><small class="text-muted">${quota.max_days} hari per pengajuan</small></td>
                                <td>${totalKesempatan} kali</td>
                                <td>${terpakai} kali</td>
                                <td><strong>${quota.kesempatan_sisa} kali</strong></td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-${progressClass}" 
                                             role="progressbar" 
                                             style="width: ${percentage}%">
                                            ${percentage}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `);
                    } else {
                        // Cuti Alasan Penting dll
                        thead.html(`
                            <tr>
                                <th>Jenis Cuti</th>
                                <th>Max Per Pengajuan</th>
                                <th>Status Kuota</th>
                                <th>Keterangan</th>
                            </tr>
                        `);
                        tbody.html(`
                            <tr>
                                <td><strong>${quota.nama_cuti}</strong></td>
                                <td>${quota.max_days} hari</td>
                                <td><span class="badge bg-success">Tersedia</span></td>
                                <td><small class="text-muted">${quota.keterangan}</small></td>
                            </tr>
                        `);
                    }
                } else {
                    // Akumulatif (Cuti Besar, Cuti Sakit, Luar Tanggungan)
                    const totalKuota = quota.max_days;
                    const terpakai = totalKuota > 0 ? (totalKuota - quota.sisa_kuota) : 0;
                    const percentage = totalKuota > 0 ? (terpakai / totalKuota * 100).toFixed(1) : 0;
                    const progressClass = percentage > 80 ? 'danger' : percentage > 50 ? 'warning' : 'success';
                    
                    thead.html(`
                        <tr>
                            <th>Jenis Cuti</th>
                            <th>Total Kuota</th>
                            <th>Terpakai</th>
                            <th>Sisa</th>
                            <th>Persentase</th>
                        </tr>
                    `);
                    tbody.html(`
                        <tr>
                            <td>${quota.nama_cuti}<br><small class="text-muted">${quota.keterangan}</small></td>
                            <td>${totalKuota} hari</td>
                            <td>${terpakai} hari</td>
                            <td><strong>${quota.sisa_kuota} hari</strong></td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar bg-${progressClass}" 
                                         role="progressbar" 
                                         style="width: ${percentage}%">
                                        ${percentage}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `);
                }
            }
        });

        function loadQuotaInfo() {
            const currentYear = new Date().getFullYear();
            const years = [currentYear - 2, currentYear - 1, currentYear];
            const tbody = $('#quotaTableBody');
            // tbody.empty(); called in change event

            let totalQuota = 0;
            let totalUsed = 0;
            let results = [];
            let processed = 0;

            years.forEach(function (year, idx) {
                $.post(baseUrl('leave/getBalance'), {
                    tahun: year
                }, function (response) {
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
                        results.forEach(function (item) {
                            const progressClass = item.percentage > 80 ? 'danger' :
                                item.percentage > 50 ? 'warning' : 'success';
                            tbody.append(`
                            <tr>
                                <td>${item.year}</td>
                                <td>${item.kuota} hari</td>
                                <td>${item.used} hari</td>
                                <td><strong>${item.sisa} hari</strong></td>
                                <td>
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

        function loadSignatureManagement() {
            const container = $('#signatureManagementDiv');

            // Load signature management page via AJAX
            $.get(baseUrl('user/signatureManagement'), function (data) {
                container.html(data);
            }).fail(function () {
                container.html(`
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>Gagal memuat halaman manajemen tanda tangan.
                </div>
            `);
            });
        }
    });

</script>