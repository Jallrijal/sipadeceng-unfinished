<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-3 flex-header-mobile">
            <h5 class="card-title mb-0">Kuota Cuti Pegawai</h5>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" id="jenisCuti" style="width: 180px;">
                    <option value="1">Cuti Tahunan</option>
                    <option value="2">Cuti Besar</option>
                    <option value="3">Cuti Sakit</option>
                    <option value="4">Cuti Melahirkan</option>
                    <option value="5">Cuti Alasan Penting</option>
                    <option value="6">Cuti Luar Tanggungan</option>
                </select>
                <div id="tahunKuotaWrapper" style="width: 100px;">
                    <?php $__currentYear = (int) date('Y'); ?>
                    <select class="form-select form-select-sm" id="tahunKuota">
                        <?php for ($__i = 0; $__i < 3; $__i++):
                            $__y = $__currentYear - $__i; ?>
                            <option value="<?php echo $__y; ?>"><?php echo $__y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="button" class="btn btn-warning btn-sm" id="runQuotaManagement"
                    title="Jalankan Pengelolaan Kuota Otomatis" style="display: none;">
                    <i class="bi bi-gear"></i> Reset Quota
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="runQuotaSakit"
                    title="Jalankan Pengelolaan Kuota Cuti Sakit" style="display: none; margin-left:6px;">
                    <i class="bi bi-heart-pulse"></i> Reset Quota Sakit
                </button>
            </div>
        </div>

        <div class="alert alert-info alert-dismissible fade show" id="infoBadge" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Info:</strong>
            <div class="mb-2">
                <strong>Sistem akan memotong kuota cuti dengan prioritas:</strong><br>
                <span class="badge bg-secondary">2 tahun lalu</span> →
                <span class="badge bg-secondary">1 tahun lalu</span> →
                <span class="badge bg-secondary">Tahun ini</span>
            </div>
            <div>
                <strong>Kuota Tahunan Akumulatif:</strong> Maksimal 18 hari dalam 3 tahun (0 + 6 + 12 hari)
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="kuotaTable">
                <thead>
                    <tr id="kuotaTableHeader">
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Unit Kerja</th>
                        <th class="th-kuota">Total Kuota</th>
                        <th class="th-terpakai">Terpakai</th>
                        <th class="th-sisa">Total Sisa</th>
                        <th class="th-detail">Detail Sisa</th>
                        <th class="th-sisa-pengambilan">Sisa Pengambilan</th>
                        <th class="th-progress">Progress</th>
                        <th class="th-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- User History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Cuti - <span id="userNameHistory"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="historyTable">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis Cuti</th>
                                <th>Periode</th>
                                <th>Jumlah Hari</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Kuota Modal -->
<div class="modal fade" id="detailKuotaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Kuota Cuti - <span id="userNameDetail"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailKuotaContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
    .detail-tooltip {
        cursor: help;
        text-decoration: underline;
        text-decoration-style: dotted;
    }

    .quota-details {
        font-size: 0.8rem;
        color: #6c757d;
    }

    @media (max-width: 940px) {

        /* Fix double click issue on mobile */
        .table-hover tbody tr:hover {
            background: transparent;
        }

        .d-flex.justify-content-between.mb-3.flex-header-mobile {
            display: flex !important;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
            font-size: 0.95rem !important;
            padding: 8px 8px !important;
        }

        .d-flex.justify-content-between.mb-3.flex-header-mobile h5 {
            font-size: 0.98rem !important;
            margin-bottom: 0 !important;
            margin-right: 8px;
            font-weight: 600;
        }

        .d-flex.justify-content-between.mb-3.flex-header-mobile select,
        .d-flex.justify-content-between.mb-3.flex-header-mobile .form-select {
            font-size: 0.92rem !important;
            padding: 2px 8px !important;
            height: 28px !important;
            min-width: 70px !important;
            max-width: 100px !important;
            border-radius: 5px;
            line-height: 1.2 !important;
            box-sizing: border-box !important;
        }

        /* DataTables length & search ke kanan sejajar */
        div.dataTables_wrapper div.dataTables_length,
        div.dataTables_wrapper div.dataTables_filter {
            float: right !important;
            display: inline-block !important;
            margin: 0 0 8px 8px !important;
        }

        div.dataTables_wrapper div.dataTables_length label,
        div.dataTables_wrapper div.dataTables_filter label {
            font-size: 0.95rem !important;
        }

        /* Make DataTables length (select) and search input match the `jenisCuti` select on mobile */
        div.dataTables_wrapper div.dataTables_length select,
        div.dataTables_wrapper div.dataTables_length select.form-select,
        div.dataTables_wrapper div.dataTables_filter input[type="search"] {
            font-size: 0.92rem !important;
            padding: 2px 8px !important;
            height: 28px !important;
            min-width: 70px !important;
            max-width: 100px !important;
            width: 100px !important;
            border-radius: 5px !important;
            line-height: 1.2 !important;
            box-sizing: border-box !important;
        }

        .table-responsive {
            overflow-x: auto !important;
        }

        #kuotaTable {
            width: 100% !important;
        }

        #kuotaTable th,
        #kuotaTable td {
            padding: 0.35rem 0.5rem !important;
            border: 1px solid #dee2e6 !important;
            text-align: left !important;
            white-space: normal !important;
            font-size: 0.95rem !important;
            word-break: break-word !important;
        }
    }

    /* Fix modal untuk mobile (width <= 768px) */
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
            padding: 0 !important;
        }

        #historyModal.show,
        #detailKuotaModal.show {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
        }

        #historyModal .modal-dialog,
        #detailKuotaModal .modal-dialog {
            margin: 0 !important;
            max-width: 95% !important;
            width: 95% !important;
            max-height: 85vh !important;
            height: auto !important;
            padding: 0 !important;
        }

        #historyModal .modal-content,
        #detailKuotaModal .modal-content {
            max-height: 85vh !important;
            height: auto !important;
            width: 100% !important;
            border-radius: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            border: none !important;
        }

        #historyModal .modal-header,
        #detailKuotaModal .modal-header {
            flex-shrink: 0 !important;
            padding: 0.5rem !important;
            border-bottom: 1px solid #dee2e6 !important;
        }

        #historyModal .modal-header .modal-title,
        #detailKuotaModal .modal-header .modal-title {
            font-size: 0.9rem !important;
        }

        #historyModal .modal-body,
        #detailKuotaModal .modal-body {
            flex: 1 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: 0 !important;
            max-height: calc(85vh - 50px) !important;
        }

        /* Hapus styling dari table-responsive untuk mobile */
        #historyModal .table-responsive,
        #detailKuotaModal .table-responsive {
            display: block !important;
            width: 100% !important;
            overflow: visible !important;
        }

        /* History Table */
        #historyTable {
            font-size: 0.75rem !important;
            margin: 0 !important;
            display: table !important;
            width: 100% !important;
            border-collapse: collapse !important;
        }

        #historyTable thead {
            display: table-header-group !important;
            background-color: #f8f9fa !important;
        }

        #historyTable thead tr {
            display: table-row !important;
        }

        #historyTable thead th {
            display: table-cell !important;
            padding: 0.35rem 0.25rem !important;
            border: 1px solid #dee2e6 !important;
            background-color: #f8f9fa !important;
            font-weight: 600 !important;
            text-align: center !important;
            font-size: 0.7rem !important;
            vertical-align: middle !important;
        }

        #historyTable thead th:nth-child(1) {
            width: 12% !important;
        }

        #historyTable thead th:nth-child(2) {
            width: 30% !important;
        }

        #historyTable tbody {
            display: table-row-group !important;
        }

        #historyTable tbody tr {
            display: table-row !important;
        }

        #historyTable tbody td {
            display: table-cell !important;
            padding: 0.3rem 0.25rem !important;
            border: 1px solid #dee2e6 !important;
            text-align: center !important;
            font-size: 0.75rem !important;
            vertical-align: middle !important;
            word-break: break-word !important;
        }

        #historyTable tbody td:nth-child(1) {
            width: 12% !important;
        }

        #historyTable tbody td:nth-child(2) {
            width: 30% !important;
        }

        /* Detail Kuota Table */
        #detailKuotaContent table {
            font-size: 0.75rem !important;
            margin: 0 !important;
            display: table !important;
            width: 100% !important;
            border-collapse: collapse !important;
        }

        #detailKuotaContent table thead {
            display: table-header-group !important;
            background-color: #f8f9fa !important;
        }

        #detailKuotaContent table thead tr {
            display: table-row !important;
        }

        #detailKuotaContent table thead th {
            display: table-cell !important;
            padding: 0.35rem 0.25rem !important;
            border: 1px solid #dee2e6 !important;
            background-color: #f8f9fa !important;
            font-weight: 600 !important;
            text-align: center !important;
            font-size: 0.7rem !important;
            vertical-align: middle !important;
        }

        #detailKuotaContent table tbody {
            display: table-row-group !important;
        }

        #detailKuotaContent table tbody tr {
            display: table-row !important;
        }

        #detailKuotaContent table tbody td {
            display: table-cell !important;
            padding: 0.3rem 0.25rem !important;
            border: 1px solid #dee2e6 !important;
            text-align: center !important;
            font-size: 0.75rem !important;
            vertical-align: middle !important;
            word-break: break-word !important;
        }
    }
</style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const labelMap = {
        1: {
            kuota: 'Total Kuota',
            terpakai: 'Terpakai',
            sisa: 'Total Sisa',
            detail: 'Detail Sisa',
            info: true
        },
        2: {
            kuota: 'Kuota Cuti Besar',
            terpakai: 'Terpakai',
            sisa: 'Sisa Cuti Besar',
            detail: 'Detail Sisa',
            info: false
        },
        3: {
            kuota: 'Kuota Cuti Sakit',
            terpakai: 'Terpakai',
            sisa: 'Sisa Cuti Sakit',
            detail: 'Detail Sisa',
            info: false
        },
        4: {
            kuota: 'Maksimal Hari Cuti',
            terpakai: 'Terpakai',
            sisa: 'Sisa Kuota Cuti',
            detail: 'Detail Sisa',
            info: false
        },
        5: {
            kuota: 'Maksimal Hari Cuti',
            terpakai: 'Terpakai',
            sisa: 'Sisa Kuota Cuti',
            detail: 'Detail Sisa',
            info: false
        },
        6: {
            kuota: 'Kuota Luar Tanggungan',
            terpakai: 'Terpakai',
            sisa: 'Sisa Kuota Cuti',
            detail: 'Detail Sisa',
            info: false
        }
    };

    $(document).ready(function () {
        loadKuotaData();

        $('#tahunKuota, #jenisCuti').change(function () {
            loadKuotaData($('#tahunKuota').val(), $('#jenisCuti').val());
        });
        // Ganti label kolom saat jenis cuti berubah
        $('#jenisCuti').change(function () {
            updateTableHeader();
            updateInfoBadge();
            toggleYearSelector();
            toggleSisaPengambilanColumn();
            toggleKolomMelahirkan();
            toggleAutoQuotaButton();
        });
        updateTableHeader();
        updateInfoBadge();
        toggleYearSelector();
        toggleSisaPengambilanColumn();
        toggleKolomMelahirkan();
        toggleAutoQuotaButton(); // Pastikan tombol Reset Quota ditampilkan jika Cuti Tahunan dipilih
    });

    function updateTableHeader() {
        const jenis = parseInt($('#jenisCuti').val());
        const map = labelMap[jenis];
        $('#kuotaTableHeader .th-kuota').text(map.kuota);
        $('#kuotaTableHeader .th-terpakai').text(map.terpakai);
        $('#kuotaTableHeader .th-sisa').text(map.sisa);
        $('#kuotaTableHeader .th-detail').text(map.detail);
        if (jenis === 4) {
            $('#kuotaTableHeader .th-sisa-pengambilan').show();
        } else {
            $('#kuotaTableHeader .th-sisa-pengambilan').hide();
        }
    }
    function updateInfoBadge() {
        const jenis = parseInt($('#jenisCuti').val());
        if (labelMap[jenis].info) {
            $('#infoBadge').show();
        } else {
            $('#infoBadge').hide();
        }
    }

    function toggleYearSelector() {
        const jenis = parseInt($('#jenisCuti').val());
        if (jenis === 1) { // Cuti Tahunan
            $('#tahunKuotaWrapper').show();
        } else {
            $('#tahunKuotaWrapper').hide();
        }
    }

    function toggleSisaPengambilanColumn() {
        const jenis = parseInt($('#jenisCuti').val());
        if (jenis === 4) {
            $('.th-sisa-pengambilan, td[data-label="Sisa Pengambilan"]').show();
        } else {
            $('.th-sisa-pengambilan, td[data-label="Sisa Pengambilan"]').hide();
        }
    }

    function toggleKolomMelahirkan() {
        const jenis = parseInt($('#jenisCuti').val());
        if (jenis === 4 || jenis === 5) {
            $('.th-terpakai, .th-sisa, .th-detail, .th-progress, .th-aksi, td[data-label="Terpakai"], td[data-label="Total Sisa"], td[data-label="Detail Sisa"], td[data-label="Progress"], td[data-label="Aksi"]').hide();
        } else {
            $('.th-terpakai, .th-sisa, .th-detail, .th-progress, .th-aksi, td[data-label="Terpakai"], td[data-label="Total Sisa"], td[data-label="Detail Sisa"], td[data-label="Progress"], td[data-label="Aksi"]').show();
        }
    }

    function toggleAutoQuotaButton() {
        const jenis = parseInt($('#jenisCuti').val());
        if (jenis === 1) { // Cuti Tahunan
            $('#runQuotaManagement').show();
            $('#runQuotaSakit').hide();
        } else if (jenis === 3) { // Cuti Sakit
            $('#runQuotaManagement').hide();
            $('#runQuotaSakit').show();
        } else {
            $('#runQuotaManagement').hide();
            $('#runQuotaSakit').hide();
        }
    }

    function loadKuotaData(tahun = <?php echo date('Y'); ?>, jenisCuti = 1) {
        $.post(baseUrl('approval/getUsersWithBalance'), {
            tahun: tahun,
            leave_type_id: jenisCuti
        }, function (response) {
            if (response.success) {
                console.log('DATA:', response.data);
                // Destroy DataTable sebelum render ulang
                if ($.fn.DataTable.isDataTable('#kuotaTable')) {
                    $('#kuotaTable').DataTable().destroy();
                }
                const tbody = $('#kuotaTable tbody');
                tbody.empty();
                response.data.forEach(function (item, index) {
                    let sisaPengambilanTd = '';
                    if (jenisCuti == 4) {
                        sisaPengambilanTd = `<td data-label="Sisa Pengambilan" class="text-center">${item.sisa_pengambilan ?? '-'}</td>`;
                    } else {
                        sisaPengambilanTd = `<td data-label="Sisa Pengambilan" class="text-center"></td>`;
                    }
                    let terpakaiTd = '';
                    let sisaKuotaTd = '';
                    let detailSisaTd = '';
                    let progressTd = '';
                    let aksiTd = '';
                    if (jenisCuti == 4 || jenisCuti == 5) {
                        terpakaiTd = `<td data-label="Terpakai" class="text-center" style="display:none"></td>`;
                        sisaKuotaTd = `<td data-label="Total Sisa" class="text-center" style="display:none"></td>`;
                        detailSisaTd = `<td data-label="Detail Sisa" style="display:none"></td>`;
                        progressTd = `<td data-label="Progress" style="display:none"></td>`;
                        aksiTd = `<td data-label="Aksi" style="display:none"></td>`;
                    } else {
                        terpakaiTd = `<td data-label="Terpakai" class="text-center">${item.cuti_terpakai}</td>`;
                        sisaKuotaTd = `<td data-label="Total Sisa" class="text-center"><strong>${item.sisa_kuota}</strong></td>`;
                        detailSisaTd = `<td data-label="Detail Sisa"><a href="#" class="detail-tooltip" onclick="showDetailKuota(${item.id}, '${item.nama}', '${item.detail_sisa}', ${jenisCuti}, ${tahun})"><small>${item.detail_sisa}</small></a></td>`;
                        progressTd = `<td data-label="Progress">\n<div class=\"progress\">\n<div class=\"progress-bar bg-${item.persentase_terpakai > 80 ? 'danger' : item.persentase_terpakai > 50 ? 'warning' : 'success'}\" role=\"progressbar\" style=\"width: ${item.persentase_terpakai}%\">${item.persentase_terpakai}%</div>\n</div>\n</td>`;
                        aksiTd = `<td data-label="Aksi">\n<button class="btn btn-sm btn-info" onclick="viewUserHistory(${item.id}, '${item.nama}')\">\n<i class="bi bi-clock-history"></i>\n</button>\n</td>`;
                    }
                    tbody.append(`
                    <tr>
                        <td data-label="No">${index + 1}</td>
                        <td data-label="Nama">${item.nama}</td>
                        <td data-label="NIP">${item.nip}</td>
                        <td data-label="Unit Kerja">${item.nama_satker ? item.nama_satker : item.unit_kerja}</td>
                        <td data-label="Total Kuota" class="text-center">${item.kuota_tahunan}</td>
                        ${terpakaiTd}
                        ${sisaKuotaTd}
                        ${detailSisaTd}
                        ${sisaPengambilanTd}
                        ${progressTd}
                        ${aksiTd}
                    </tr>
                `);
                });
                // Inisialisasi ulang DataTable
                $('#kuotaTable').DataTable({
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
                    pageLength: 25
                });
                toggleSisaPengambilanColumn();
                toggleKolomMelahirkan();
            }
        }, 'json');
    }

    function showDetailKuota(userId, userName, detail, jenisCuti, tahun) {
        $('#userNameDetail').text(userName);
        const details = detail.split(', ');
        let content = '<table class="table table-sm">';
        content += '<thead><tr><th>Tahun</th><th>Sisa Kuota</th><th>Status</th></tr></thead><tbody>';
        if (jenisCuti == 1) {
            details.forEach(function (item) {
                const [tahun, sisa] = item.split(': ');
                const sisaInt = parseInt(sisa);
                const badgeClass = sisaInt === 0 ? 'danger' : sisaInt <= 3 ? 'warning' : 'success';
                content += `
                <tr>
                    <td>${tahun}</td>
                    <td class="text-center">${sisa} hari</td>
                    <td class="text-center">
                        <span class="badge bg-${badgeClass}">
                            ${sisaInt === 0 ? 'Habis' : sisaInt <= 3 ? 'Sedikit' : 'Tersedia'}
                        </span>
                    </td>
                </tr>
            `;
            });
        } else if (jenisCuti == 3) {
            // Jika tidak ada data (mis. server mengembalikan 'Tidak ada data'), tampilkan baris penanda
            if (details.length === 1 && !details[0].includes(': ')) {
                content += `<tr><td colspan="3" class="text-center text-muted">${details[0]}</td></tr>`;
            } else {
                details.forEach(function (item) {
                    if (!item.includes(': ')) return; // abaikan entry yang tidak valid
                    const [tahun, sisa] = item.split(': ');
                    const sisaInt = parseInt(sisa);
                    let badgeClass = 'success';
                    let statusText = 'Tersedia';
                    if (sisaInt < 0) {
                        badgeClass = 'danger';
                        statusText = 'Minus';
                    } else if (sisaInt === 0) {
                        badgeClass = 'danger';
                        statusText = 'Habis';
                    } else if (sisaInt <= 3) {
                        badgeClass = 'warning';
                        statusText = 'Sedikit';
                    }
                    content += `
                    <tr>
                        <td>${tahun}</td>
                        <td class="text-center">${sisa} hari</td>
                        <td class="text-center">
                            <span class="badge bg-${badgeClass}">
                                ${statusText}
                            </span>
                        </td>
                    </tr>
                `;
                });
            }
        } else {
            // Hanya tampilkan tahun yang dipilih
            const found = details.find(x => x.startsWith(tahun + ':'));
            let sisa = '0';
            if (found) sisa = found.split(': ')[1];
            const sisaInt = parseInt(sisa);
            const badgeClass = sisaInt === 0 ? 'danger' : sisaInt <= 3 ? 'warning' : 'success';
            content += `
            <tr>
                <td>${tahun}</td>
                <td class="text-center">${sisa} hari</td>
                <td class="text-center">
                    <span class="badge bg-${badgeClass}">
                        ${sisaInt === 0 ? 'Habis' : sisaInt <= 3 ? 'Sedikit' : 'Tersedia'}
                    </span>
                </td>
            </tr>
        `;
        }
        content += '</tbody></table>';
        $('#detailKuotaContent').html(content);
        $('#detailKuotaModal').modal('show');
    }

    function viewUserHistory(userId, userName) {
        $('#userNameHistory').text(userName);

        $.post(baseUrl('leave/getHistory'), {
            user_id: userId
        }, function (response) {
            if (response.success) {
                const tbody = $('#historyTable tbody');
                tbody.empty();

                response.data.forEach(function (item) {
                    tbody.append(`
                    <tr>
                        <td>${item.created_at_formatted}</td>
                        <td>${item.nama_cuti}</td>
                        <td>${item.tanggal_mulai_formatted} - ${item.tanggal_selesai_formatted}</td>
                        <td>${item.jumlah_hari}</td>
                        <td>${item.status_badge}</td>
                    </tr>
                `);
                });

                if (response.data.length === 0) {
                    tbody.append('<tr><td colspan="5" class="text-center">Belum ada riwayat cuti</td></tr>');
                }

                $('#historyModal').modal('show');
            }
        }, 'json');
    }

    // Fungsi untuk menjalankan pengelolaan kuota otomatis
    $(document).ready(function () {
        // Test endpoint terlebih dahulu
        $('#runQuotaManagement').click(function () {
            console.log('Button clicked, starting test...');

            // Test helper terlebih dahulu
            $.ajax({
                url: baseUrl('approval/testQuotaHelper'),
                type: 'POST',
                dataType: 'json',
                timeout: 10000,
                success: function (response) {
                    console.log('Test response:', response);

                    if (response.success) {
                        // Jika test berhasil, jalankan proses utama
                        Swal.fire({
                            title: 'Konfirmasi Pengelolaan Kuota',
                            html: `
                            <p>Apakah Anda yakin ingin menjalankan pengelolaan kuota otomatis?</p>
                            <p><strong>Proses ini akan:</strong></p>
                            <ul style="text-align: left;">
                                <li>Menghapus kuota 3 tahun lalu dan ke atasnya</li>
                                <li>Menyimpan kuota 2 tahun lalu dengan 0 hari</li>
                                <li>Menyimpan kuota 1 tahun lalu sesuai sisa (maksimal 6 hari)</li>
                                <li>Membuat kuota tahun baru dengan 12 hari</li>
                            </ul>
                            <p><em>Proses ini hanya dapat dijalankan sekali per hari.</em></p>
                        `,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, Jalankan!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#runQuotaManagement').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Processing...');

                                // Tampilkan loading
                                Swal.fire({
                                    title: 'Menjalankan Pengelolaan Kuota...',
                                    text: 'Mohon tunggu sebentar',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                $.ajax({
                                    url: baseUrl('approval/runQuotaManagement'),
                                    type: 'POST',
                                    dataType: 'json',
                                    timeout: 30000,
                                    success: function (response) {
                                        if (response.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil',
                                                text: 'Pengelolaan kuota otomatis berhasil dilakukan!'
                                            }).then(() => {
                                                location.reload();
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Gagal',
                                                text: 'Error: ' + response.message
                                            });
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('AJAX Error:', status, error);
                                        console.error('Response:', xhr.responseText);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: 'Terjadi kesalahan saat menjalankan proses otomatis. Cek console untuk detail.'
                                        });
                                    },
                                    complete: function () {
                                        $('#runQuotaManagement').prop('disabled', false).html('<i class="bi bi-gear"></i> Reset Quota');
                                    }
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Test Gagal',
                            html: `
                            <p>Test gagal: ${response.message}</p>
                            <p><strong>File:</strong> ${response.file}</p>
                            <p><strong>Line:</strong> ${response.line}</p>
                        `
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Test AJAX Error:', status, error);
                    console.error('Test Response:', xhr.responseText);
                    console.error('Status Code:', xhr.status);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        html: `
                        <p>Terjadi kesalahan saat test. Cek console untuk detail.</p>
                        <p><strong>Status:</strong> ${status}</p>
                        <p><strong>Error:</strong> ${error}</p>
                    `
                    });
                }
            });
        });

        // Handler untuk Reset Quota Cuti Sakit
        $('#runQuotaSakit').click(function () {
            console.log('Reset Quota Sakit clicked, starting test...');
            $.ajax({
                url: baseUrl('approval/testQuotaSakitHelper'),
                type: 'POST',
                dataType: 'json',
                timeout: 10000,
                success: function (response) {
                    console.log('Test Sakit response:', response);
                    if (response.success) {
                        const currentYear = new Date().getFullYear();
                        Swal.fire({
                            title: 'Konfirmasi Pengelolaan Kuota Cuti Sakit',
                            html: `
                            <p>Apakah Anda yakin ingin menjalankan pengelolaan kuota cuti sakit?</p>
                            <p><strong>Proses ini akan:</strong></p>
                            <ul style="text-align: left;">
                                <li>Menghapus data kuota cuti sakit setahun sebelumnya</li>
                                <li>Menambahkan data kuota cuti sakit tahun ini untuk semua pegawai dengan <strong>sisa_kuota = 14</strong></li>
                            </ul>
                            <p><em>Proses ini hanya dapat dijalankan sekali per hari.</em></p>
                        `,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, Jalankan!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#runQuotaSakit').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Processing...');
                                Swal.fire({
                                    title: 'Menjalankan Pengelolaan Kuota Cuti Sakit...',
                                    text: 'Mohon tunggu sebentar',
                                    allowOutsideClick: false,
                                    didOpen: () => { Swal.showLoading(); }
                                });

                                $.ajax({
                                    url: baseUrl('approval/runQuotaSakitManagement'),
                                    type: 'POST',
                                    dataType: 'json',
                                    data: { year: currentYear },
                                    timeout: 30000,
                                    success: function (response) {
                                        if (response.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil',
                                                text: 'Pengelolaan kuota cuti sakit berhasil dilakukan!'
                                            }).then(() => { location.reload(); });
                                        } else {
                                            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Error: ' + response.message });
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('AJAX Error (sakit):', status, error);
                                        console.error('Response:', xhr.responseText);
                                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat menjalankan proses otomatis. Cek console untuk detail.' });
                                    },
                                    complete: function () {
                                        $('#runQuotaSakit').prop('disabled', false).html('<i class="bi bi-heart-pulse"></i> Reset Quota Sakit');
                                    }
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error', title: 'Test Gagal', html: `
                        <p>Test gagal: ${response.message}</p>
                        <p><strong>File:</strong> ${response.file}</p>
                        <p><strong>Line:</strong> ${response.line}</p>
                    `});
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Test AJAX Error (sakit):', status, error);
                    console.error('Test Response:', xhr.responseText);
                    console.error('Status Code:', xhr.status);
                    Swal.fire({
                        icon: 'error', title: 'Gagal', html: `
                    <p>Terjadi kesalahan saat test. Cek console untuk detail.</p>
                    <p><strong>Status:</strong> ${status}</p>
                    <p><strong>Error:</strong> ${error}</p>
                `});
                }
            });
        });

    });
</script>