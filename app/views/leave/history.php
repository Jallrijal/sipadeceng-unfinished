<div class="card">
    <div class="card-body">
        <!-- Status Info -->
        <!-- <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle me-2"></i>Alur Pengajuan Cuti:</h6>
                    <div class="row">
                        <div class="col-md-2">
                            <span class="badge bg-secondary">Draft</span> - Belum upload blanko
                        </div>
                        <div class="col-md-2">
                            <span class="badge bg-warning text-dark">Menunggu</span> - Blanko diupload, menunggu admin
                        </div>
                        <div class="col-md-2">
                            <span class="badge bg-success">Disetujui</span> - Admin setujui, blanko final tersedia
                        </div>
                        <div class="col-md-2">
                            <span class="badge bg-danger">Ditolak</span> - Admin tolak, blanko final tersedia
                        </div>
                        <div class="col-md-2">
                            <span class="badge bg-primary">Selesai</span> - Blanko final diupload admin
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Filter Section -->
        <div class="filter-section mb-4">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <!--<option value="draft">Draft</option>-->
                        <option value="pending">Menunggu</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                        <!--<option value="completed">Selesai</option>-->
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label">Tahun</label>
                    <select class="form-select" id="filterYear">
                        <option value="">Semua Tahun</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label">Jenis Cuti</label>
                    <select class="form-select" id="filterType">
                        <option value="">Semua Jenis</option>
                    </select>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 d-flex justify-content-end">
                    <button class="btn btn-primary" id="btnFilter">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover" id="historyTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Nama Pengaju</th>
                        <th>Jenis Cuti</th>
                        <th>Periode</th>
                        <th>Jumlah Hari</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pengajuan Cuti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .filter-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
    }

    .timeline-badge {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    .timeline-badge.pending {
        background-color: #ffc107;
    }

    .timeline-badge.approved {
        background-color: #28a745;
    }

    .timeline-badge.rejected {
        background-color: #dc3545;
    }

    /* Tambahan agar tabel di HP lebih memanjang dan tidak terlalu mepet kiri */
    @media (max-width: 576px) {

        /* Fix double click issue on mobile */
        .table-hover tbody tr:hover {
            background: transparent;
        }

        .table-responsive {
            width: 100%;
            margin-left: 0;
            margin-right: 0;
            overflow-x: auto;
        }

        .table {
            font-size: 0.97rem;
            margin-bottom: 0;
        }

        /* Perkecil search field di mobile */
        #historyTable_filter {
            margin-bottom: 10px;
        }

        #historyTable_filter input {
            max-width: 150px !important;
            font-size: 0.85rem !important;
        }

        #historyTable_filter label {
            font-size: 0.85rem;
        }
    }

    /* memperbaiki masalah double click di mobile */
    @media (max-width: 768px) {
        .modal-open {
            overflow: auto !important;
            padding-right: 0 !important;
        }

        .modal-backdrop {
            display: none !important;
        }

        .modal {
            background-color: rgba(0, 0, 0, 0.5) !important;
        }
    }

    /* Fix detail modal content alignment on mobile - make it left-aligned */
    #detailContent * {
        text-align: left !important;
    }

    #detailContent {
        direction: ltr !important;
        text-align: left !important;
    }

    #detailContent .row {
        display: block !important;
    }

    #detailContent .col-md-6 {
        display: block !important;
        width: 100% !important;
        margin-bottom: 20px !important;
        text-align: left !important;
    }

    #detailContent table {
        width: 100% !important;
    }

    #detailContent table tr {
        display: table-row !important;
    }

    #detailContent table td {
        display: table-cell !important;
        text-align: left !important;
        padding: 0.5rem !important;
        justify-content: flex-start !important;
        align-items: flex-start !important;
        width: auto !important;
        flex-basis: auto !important;
    }

    #detailContent table td:before {
        display: none !important;
    }

    #detailContent h6 {
        text-align: left !important;
        margin-top: 15px !important;
    }

    #detailContent p {
        text-align: left !important;
        margin: 0.5rem 0 !important;
    }

    #detailContent .badge,
    #detailContent .alert {
        text-align: left !important;
    }

    #detailContent div {
        text-align: left !important;
    }
</style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function () {
        let dataTable;
        let leaveData = [];
        let leaveTypes = [];

        // Initialize
        loadFilters();
        loadHistoryData();

        // Load filters
        function loadFilters() {
            // Load years
            $.post(baseUrl('user/getYears'), function (response) {
                if (response.success) {
                    let yearOptions = '<option value="">Semua Tahun</option>';
                    response.data.forEach(year => {
                        yearOptions += `<option value="${year}">${year}</option>`;
                    });
                    $('#filterYear').html(yearOptions);
                }
            }, 'json');

            // Load leave types
            $.post(baseUrl('leave/getTypes'), function (response) {
                if (response.success) {
                    leaveTypes = response.data;
                    let typeOptions = '<option value="">Semua Jenis</option>';
                    response.data.forEach(type => {
                        typeOptions += `<option value="${type.id}">${type.nama_cuti}</option>`;
                    });
                    $('#filterType').html(typeOptions);
                }
            }, 'json');
        }

        function loadHistoryData(filters = {}) {
            filters.only_self = 1;
            $.post(baseUrl('leave/getHistory'), filters, function (response) {
                if (response.success) {
                    leaveData = response.data;
                    populateTable(response.data);
                }
            }, 'json');
        }

        // Populate table
        function populateTable(data) {
            if (dataTable) {
                dataTable.destroy();
            }

            const tbody = $('#historyTable tbody');
            tbody.empty();

            data.forEach((item, index) => {
                let actions = `
                <button class="btn btn-sm btn-info btn-action" onclick="viewDetail(${item.id})">
                    <i class="bi bi-eye"></i>
                </button>
            `;
                // Tampilkan tombol download blanko final hanya jika selesai dan status utama salah satu dari 4 keputusan
                if (item.is_completed == 1 && ['approved', 'rejected', 'changed', 'postponed'].includes(item.status) && item.has_final_doc) {
                    actions += `
                    <a href="${baseUrl('leave/downloadFinalDoc?id=' + item.id)}" 
                       class="btn btn-sm btn-success btn-action" title="Download Blanko Final">
                        <i class="bi bi-file-earmark-check"></i>
                    </a>
                `;
                }
                // Hapus tombol download dokumen final pada status lain (approved, rejected, changed, postponed, dsb)
                if (item.status === 'draft') {
                    actions += `
                    <a href="${baseUrl('leave/downloadGeneratedDoc?id=' + item.id)}" 
                       class="btn btn-sm btn-outline-primary btn-action" title="Download Blanko">
                        <i class="bi bi-download"></i>
                    </a>
                    <a href="${baseUrl('leave/draft/' + item.id)}" 
                       class="btn btn-sm btn-primary btn-action" title="Upload Dokumen">
                        <i class="bi bi-upload"></i>
                    </a>
                    <button class="btn btn-sm btn-danger btn-action" onclick="deleteDraft(${item.id})" 
                            title="Hapus Draft">
                        <i class="bi bi-trash"></i>
                    </button>
                `;
                } else if (['completed', 'approved', 'rejected'].includes(item.status)) {
                    // Tidak ada tombol download dokumen final, hanya badge status jika perlu
                    // (Jika ingin badge tambahan, tambahkan di sini)
                } else if (item.status === 'pending' || item.status === 'pending_admin_upload') {
                    actions += `
                    <a href="${baseUrl('leave/downloadGeneratedDoc?id=' + item.id)}" 
                    class="btn btn-sm btn-primary btn-action" title="Download Dokumen" target="_blank" rel="noopener">
                        <i class="bi bi-file-earmark-arrow-down"></i>
                    </a>
                    <button class="btn btn-sm btn-danger btn-action" onclick="cancelLeave(${item.id})">
                        <i class="bi bi-x-circle"></i>
                    </button>
                `;
                }

                // Ubah tampilan status
                let status_display = '';
                if (item.is_completed && item.is_completed == 1) {
                    status_display += '<span class="badge bg-primary me-1">Selesai</span>';
                }
                status_display += item.status_badge;
                tbody.append(`
                <tr>
                    <td data-label="No">${index + 1}</td>
                    <td data-label="Tanggal Pengajuan">${item.created_at_formatted}</td>
                    <td data-label="Nama Pengaju">${item.nama}</td>
                    <td data-label="Jenis Cuti">${item.nama_cuti}</td>
                    <td data-label="Periode">${item.tanggal_mulai_formatted} - ${item.tanggal_selesai_formatted}</td>
                    <td data-label="Jumlah Hari">${item.jumlah_hari} hari</td>
                    <td data-label="Status">${status_display}</td>
                    <td data-label="Aksi">${actions}</td>
                </tr>
            `);
            });

            // Initialize DataTable
            dataTable = $('#historyTable').DataTable({
                language: {
                    "sEmptyTable": "Tidak ada data",
                    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                    "sInfoFiltered": "(disaring dari _MAX_ total data)",
                    "sInfoPostFix": "",
                    "sInfoThousands": ".",
                    "sLengthMenu": "Tampilkan _MENU_ data",
                    "sLoadingRecords": "Memuat...",
                    "sProcessing": "Sedang memproses...",
                    "sSearch": "Cari:",
                    "sZeroRecords": "Tidak ditemukan data yang sesuai",
                    "oPaginate": {
                        "sFirst": "Pertama",
                        "sLast": "Terakhir",
                        "sNext": "Selanjutnya",
                        "sPrevious": "Sebelumnya"
                    }
                },
                order: [[0, 'asc']],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="bi bi-file-earmark-excel me-2"></i>Export Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'Riwayat Cuti - <?php echo $_SESSION['nama']; ?>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    }
                ]
            });
        }

        // Filter button click
        $('#btnFilter').click(function () {
            const filters = {
                status: $('#filterStatus').val(),
                tahun: $('#filterYear').val(),
                leave_type_id: $('#filterType').val()
            };

            loadHistoryData(filters);
        });

        // View detail function
        window.viewDetail = function (id) {
            const leave = leaveData.find(item => item.id == id);
            if (!leave) return;

            // Tampilkan jumlah_hari_ditangguhkan jika status postponed, apapun nilainya
            let jumlahDitangguhkan = '';
            if (
                leave.status === 'postponed' &&
                typeof leave.jumlah_hari_ditangguhkan !== 'undefined' &&
                leave.jumlah_hari_ditangguhkan !== null &&
                leave.jumlah_hari_ditangguhkan !== '' &&
                !isNaN(parseInt(leave.jumlah_hari_ditangguhkan))
            ) {
                jumlahDitangguhkan = parseInt(leave.jumlah_hari_ditangguhkan);
            }

            const content = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">Data Pemohon</h6>
                    <table class="table table-sm detail-table">
                        <tr>
                            <td width="150"><strong>Nama</strong></td>
                            <td>: ${leave.nama}</td>
                        </tr>
                        <tr>
                            <td><strong>NIP</strong></td>
                            <td>: ${leave.nip}</td>
                        </tr>
                        <tr>
                            <td><strong>Jabatan</strong></td>
                            <td>: ${leave.jabatan}</td>
                        </tr>
                        <tr>
                            <td><strong>Unit Kerja</strong></td>
                            <td>: ${leave.nama_satker ? leave.nama_satker : leave.unit_kerja}</td>
                        </tr>
                        <tr>
                            <td><strong>Jenis Cuti</strong></td>
                            <td>: ${leave.nama_cuti}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal</strong></td>
                            <td>: ${leave.tanggal_mulai_formatted} - ${leave.tanggal_selesai_formatted}</td>
                        </tr>
                        <tr>
                            <td><strong>Jumlah Hari</strong></td>
                            <td>: ${leave.jumlah_hari} hari</td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>: ${leave.status_badge}</td>
                        </tr>
                        ${(leave.status === 'postponed') ? `<tr><td><strong>Jumlah Hari Ditangguhkan</strong></td><td>: <span class='text-warning fw-bold'>${typeof leave.jumlah_hari_ditangguhkan !== 'undefined' && leave.jumlah_hari_ditangguhkan !== null && leave.jumlah_hari_ditangguhkan !== '' ? leave.jumlah_hari_ditangguhkan : 0} hari</span></td></tr>` : ''}
                        ${leave.approved_by_name ? `<tr><td><strong>Diproses Oleh</strong></td><td>: ${leave.approved_by_name}</td></tr>` : ''}
                        ${leave.approval_date ? `<tr><td><strong>Tanggal Proses</strong></td><td>: ${leave.approval_date ? formatDate(leave.approval_date) : '-'}</td></tr>` : ''}
                        ${leave.catatan_approval ? `<tr><td><strong>Catatan</strong></td><td>: ${leave.catatan_approval}</td></tr>` : ''}
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">Informasi Lainnya</h6>
                    <table class="table table-sm detail-table">
                        <tr>
                            <td width="150"><strong>Alasan Cuti</strong></td>
                            <td>: ${leave.alasan}</td>
                        </tr>
                        <tr>
                            <td><strong>Alamat Cuti</strong></td>
                            <td>: ${leave.alamat_cuti || '-'}</td>
                        </tr>
                        <tr>
                            <td><strong>Telepon</strong></td>
                            <td>: ${leave.telepon_cuti || '-'}</td>
                        </tr>
                    </table>
                    ${leave.dokumen_pendukung ? `
                        <div class="mt-3">
                            <a href="${baseUrl('leave/downloadDocument?file=' + leave.dokumen_pendukung)}" 
                               class="btn btn-primary btn-sm">
                                <i class="bi bi-download me-2"></i>Download Dokumen Pendukung
                            </a>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;

            $('#detailContent').html(content);
            $('#detailModal').modal('show');
        };

        // Cancel leave function
        window.cancelLeave = function (id) {
            Swal.fire({
                title: 'Batalkan Pengajuan?',
                text: 'Apakah Anda yakin ingin membatalkan pengajuan cuti ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(baseUrl('leave/cancel'), {
                        leave_id: id
                    }, function (response) {
                        if (response.success) {
                            Swal.fire(
                                'Dibatalkan!',
                                response.message,
                                'success'
                            );
                            loadHistoryData();
                        } else {
                            Swal.fire(
                                'Gagal!',
                                response.message,
                                'error'
                            );
                        }
                    }, 'json');
                }
            });
        };

        // Delete draft function
        window.deleteDraft = function (id) {
            Swal.fire({
                title: 'Hapus Draft?',
                text: 'Draft yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(baseUrl('leave/deleteDraft'), {
                        leave_id: id
                    }, function (response) {
                        if (response.success) {
                            Swal.fire('Terhapus!', 'Draft berhasil dihapus.', 'success');
                            loadHistoryData();
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    }, 'json');
                }
            });
        };

        // Helper function to format date
        function formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }
    });
</script>