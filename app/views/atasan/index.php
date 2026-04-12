<!-- 
Author: Rijal Imamul Haq Syamsu Alam
Lisensi Kepada: Pengadilan Tinggi Agama Makassar
-->

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Atasan</h5>
                    <div>
                        <button class="btn btn-secondary me-2" onclick="loadAtasan()" title="Refresh Data">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Section -->
                    <div class="row mb-3 justify-content-end">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Cari atasan...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                <i class="bi bi-x-circle"></i> Clear
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Data Atasan -->
                    <div class="table-responsive">
                        <table class="table table-striped" id="atasanTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Atasan</th>
                                    <th>NIP</th>
                                    <th>Jabatan</th>
                                <th>Role</th>
                                    <th>Jumlah User</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Total Data Pengguna -->
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <small class="text-muted" id="resultsInfo">
                                Menampilkan <span id="totalResults">0</span> atasan
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 576px) {
        /* Fix double click issue on mobile */
        .table-striped tbody tr:hover { background: transparent; }
    }
</style>

<script>
$(document).ready(function() {
    // Load atasan data
    loadAtasan();

    // Search functionality
    $('#searchInput').on('input', filterAtasan);

    function loadAtasan() {
        // Show loading state
        const refreshBtn = $('.btn-secondary i');
        refreshBtn.removeClass('bi-arrow-clockwise').addClass('bi-arrow-clockwise spin');
        
        $.post(baseUrl('atasan/getAtasanList'), function(response) {
            if (response.success) {
                // Store original data for filtering
                originalAtasan = response.data;
                const tbody = $('#atasanTable tbody');
                tbody.empty();
                response.data.forEach((atasan, index) => {
                    const row = `
                        <tr>
                            <td data-label="No">${index + 1}</td>
                            <td data-label="Nama Atasan">${atasan.nama_atasan}</td>
                            <td data-label="NIP">${atasan.NIP}</td>
                            <td data-label="Jabatan">${atasan.jabatan || '-'}</td>
                            <td data-label="Role">${atasan.role || '-'}</td>
                            <td data-label="Jumlah User">
                                <span class="badge bg-${atasan.user_count > 0 ? 'info' : 'secondary'}">${atasan.user_count} user</span>
                            </td>
                            <td data-label="Aksi">
                                <a href="${baseUrl('atasan/detail/' + atasan.id_atasan)}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="${baseUrl('atasan/edit/' + atasan.id_atasan)}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteAtasan(${atasan.id_atasan})" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
                $('#totalResults').text(response.data.length);
            }
            // Reset loading state
            refreshBtn.removeClass('bi-arrow-clockwise spin').addClass('bi-arrow-clockwise');
        }, 'json');
    }

    // Store original data for filtering
    let originalAtasan = [];

    function filterAtasan() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        
        const tbody = $('#atasanTable tbody');
        tbody.empty();
        
        let filteredAtasan = originalAtasan.filter(atasan => {
            const matchesSearch = atasan.nama_atasan.toLowerCase().includes(searchTerm) || 
                                 atasan.NIP.toLowerCase().includes(searchTerm) ||
                                 (atasan.jabatan && atasan.jabatan.toLowerCase().includes(searchTerm)) ||
                                 (atasan.role && atasan.role.toLowerCase().includes(searchTerm));
            return matchesSearch;
        });
        filteredAtasan.forEach((atasan, index) => {
            const row = `
                <tr>
                    <td data-label="No">${index + 1}</td>
                    <td data-label="Nama Atasan">${atasan.nama_atasan}</td>
                    <td data-label="NIP">${atasan.NIP}</td>
                    <td data-label="Jabatan">${atasan.jabatan || '-'}</td>
                    <td data-label="Role">${atasan.role || '-'}</td>
                    <td data-label="Jumlah User">
                        <span class="badge bg-${atasan.user_count > 0 ? 'info' : 'secondary'}">${atasan.user_count} user</span>
                    </td>
                    <td data-label="Aksi">
                        <a href="${baseUrl('atasan/detail/' + atasan.id_atasan)}" class="btn btn-sm btn-info" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="${baseUrl('atasan/edit/' + atasan.id_atasan)}" class="btn btn-sm btn-warning" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="deleteAtasan(${atasan.id_atasan})" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
        $('#totalResults').text(filteredAtasan.length);
    }

    window.deleteAtasan = function(id) {
        Swal.fire({
            title: 'Hapus Atasan?',
            text: "Atasan akan dihapus dari database. Pastikan tidak ada user yang menggunakan atasan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(baseUrl('atasan/delete/' + id), function(response) {
                    if (response.success) {
                        Swal.fire('Terhapus!', 'Atasan berhasil dihapus.', 'success');
                        loadAtasan();
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                }, 'json');
            }
        });
    }

    window.clearFilters = function() {
        $('#searchInput').val('');
        filterAtasan();
    }
});
</script>
