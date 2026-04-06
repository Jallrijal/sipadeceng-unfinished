<?php require_once dirname(dirname(__DIR__)) . '/helpers/signature_helper.php'; ?>

<!-- Navigation Tabs -->
<div class="row mb-4">
    <div class="col-12">
        <ul class="nav nav-tabs" id="adminProfileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profil-tab" data-bs-toggle="tab" data-bs-target="#profil-content" type="button" role="tab" aria-controls="profil-content" aria-selected="true">
                    <i class="bi bi-person-circle me-2"></i>Informasi Profile
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="keamanan-tab" data-bs-toggle="tab" data-bs-target="#keamanan-content" type="button" role="tab" aria-controls="keamanan-content" aria-selected="false">
                    <i class="bi bi-shield-lock me-2"></i>Keamanan Akun
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="paraf-tab" data-bs-toggle="tab" data-bs-target="#paraf-content" type="button" role="tab" aria-controls="paraf-content" aria-selected="false">
                    <i class="bi bi-pencil me-2"></i>Paraf Petugas Cuti
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="kinerja-tab" data-bs-toggle="tab" data-bs-target="#kinerja-content" type="button" role="tab" aria-controls="kinerja-content" aria-selected="false">
                    <i class="bi bi-graph-up me-2"></i>Laporan Kinerja Admin
                </button>
            </li>
        </ul>
    </div>
</div>

<!-- Tab Content -->
<div class="tab-content" id="adminProfileTabContent">
    <!-- Profil -->
    <div class="tab-pane fade show active" id="profil-content" role="tabpanel" aria-labelledby="profil-tab">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-person-circle me-2"></i>Informasi Profile</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr><td width="200"><strong>Nama</strong></td><td>: <?php echo $_SESSION['nama']; ?></td></tr>
                            <tr><td><strong>Jabatan</strong></td><td>: <?php echo $_SESSION['jabatan']; ?></td></tr>
                            <tr><td><strong>Unit Kerja</strong></td><td>: <?php require_once __DIR__ . '/../../helpers/satker_helper.php'; echo get_nama_satker($_SESSION['unit_kerja']); ?></td></tr>
                            <tr><td><strong>Username</strong></td><td>: <?php echo $_SESSION['username']; ?></td></tr>
                            <tr><td><strong>Role</strong></td><td>: <?php echo $_SESSION['user_type'] == 'admin' ? 'Admin' : (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'atasan' ? 'Atasan' : 'Pegawai'); ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Keamanan Akun (dipisah menjadi tab tersendiri) -->
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

    <!-- Paraf Petugas Cuti Tab -->
    <div class="tab-pane fade" id="paraf-content" role="tabpanel" aria-labelledby="paraf-tab">
        <div class="row">
            <div class="col-12">
                <div id="parafManagementDiv">
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

    <!-- Laporan Kinerja Admin Tab -->
    <div class="tab-pane fade" id="kinerja-content" role="tabpanel" aria-labelledby="kinerja-tab">
        <div class="row">
            <div class="col-12">
                <div id="kinerjaDiv">
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

<script>
$(document).ready(function() {
    let parafLoaded = false;
    let kinerjaLoaded = false;

    // Password change handler
    $(document).on('submit', '#changePasswordForm', function(e) {
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

    // Load paraf management when tab is clicked
    $('#paraf-tab').on('click', function() {
        if (!parafLoaded) {
            loadParafManagement();
            parafLoaded = true;
        }
    });

    // Load kinerja report when tab is clicked
    $('#kinerja-tab').on('click', function() {
        if (!kinerjaLoaded) {
            loadKinerjaReport();
            kinerjaLoaded = true;
        }
    });

    function loadParafManagement() {
        const container = $('#parafManagementDiv');
        
        // Load paraf management page via AJAX
        $.get(baseUrl('user/parafManagement'), function(data) {
            container.html(data);
        }).fail(function() {
            container.html(`
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>Gagal memuat halaman manajemen paraf petugas cuti.
                </div>
            `);
        });
    }

    function loadKinerjaReport() {
        const container = $('#kinerjaDiv');
        
        // Load kinerja report page via AJAX
        $.get(baseUrl('report/adminPerformanceReport'), function(data) {
            container.html(data);
        }).fail(function() {
            container.html(`
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>Gagal memuat laporan kinerja admin.
                </div>
            `);
        });
    }
});
</script>

<style>
/* Tab styles reused from profile_user */
.nav-tabs { border-bottom: 2px solid #dee2e6; }
.nav-link { color: #6c757d; border: none; border-bottom: 3px solid transparent; transition: all 0.3s ease; font-weight: 500; }
.nav-link:hover { color: #1b5e20; border-bottom-color: #1b5e20; }
.nav-link.active { color: #fff; background-color: #1b5e20; border-bottom-color: #1b5e20; border-radius: 5px 5px 0 0; }
.tab-content { padding: 20px 0; }
@media (max-width: 768px) { .nav-tabs { flex-wrap: nowrap; overflow-x: auto; --webkit-overflow-scrolling: touch; } .nav-link { font-size: 0.9rem; padding: 0.5rem 1rem; white-space: nowrap; } }
@media (max-width: 576px) {
    .card-body table.table-borderless td { text-align: left !important; vertical-align: top !important; display: table-cell !important; width: auto !important; padding: 0.5rem 0.75rem !important; border: none !important; font-size: 1rem !important; }
    .card-body table.table-borderless, .card-body table.table-borderless tr, .card-body table.table-borderless td { display: table !important; }
}

@media (min-width: 577px) and (max-width: 768px) {
    .card-body table.table-borderless td { text-align: left !important; vertical-align: top !important; display: table-cell !important; width: auto !important; padding: 0.5rem 0.75rem !important; border: none !important; font-size: 1rem !important; }
    .card-body table.table-borderless, .card-body table.table-borderless tr, .card-body table.table-borderless td { display: table !important; }
}
</style>