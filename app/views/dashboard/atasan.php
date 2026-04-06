<?php $hide_charts = isset($hide_charts) ? $hide_charts : false; ?>

<?php
require_once dirname(dirname(__DIR__)) . '/helpers/signature_helper.php';
$hasSignature = getUserSignature($_SESSION['user_id'], 'user');
if (!$hasSignature):
?>
<div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    Anda belum mengunggah gambar tanda tangan. Silakan unggah tanda tangan Anda <a href="<?= baseUrl('signature') ?>" class="alert-link">di sini</a>.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-2 mb-3">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body">
                <h6 class="text-white-50">Total Pegawai</h6>
                <h2 class="mb-0" id="totalUsers">0</h2>
                <i class="bi bi-people stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card stat-card bg-warning text-white">
            <div class="card-body">
                <h6 class="text-white-50">Menunggu Persetujuan</h6>
                <h2 class="mb-0" id="pendingCount">0</h2>
                <i class="bi bi-hourglass-split stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card stat-card bg-info text-white">
            <div class="card-body">
                <h6 class="text-white-50">Menunggu Dokumen</h6>
                <h2 class="mb-0" id="waitingDocCount">0</h2>
                <i class="bi bi-file-earmark-text stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card stat-card bg-success text-white">
            <div class="card-body">
                <h6 class="text-white-50">Selesai</h6>
                <h2 class="mb-0" id="completedCount">0</h2>
                <i class="bi bi-check-circle stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card stat-card bg-secondary text-white">
            <div class="card-body">
                <h6 class="text-white-50">Disetujui</h6>
                <h2 class="mb-0" id="approvedCount">0</h2>
                <i class="bi bi-check2 stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card stat-card bg-danger text-white">
            <div class="card-body">
                <h6 class="text-white-50">Ditolak</h6>
                <h2 class="mb-0" id="rejectedCount">0</h2>
                <i class="bi bi-x-circle stat-icon"></i>
            </div>
        </div>
    </div>
</div>

<?php if (!$hide_charts): ?>
    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-8 mb-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Trend Pengajuan Cuti (6 Bulan Terakhir)</h6>
                </div>
                <div class="card-body">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Jenis Cuti</h6>
                </div>
                <div class="card-body">
                    <canvas id="leaveTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- My Recent Activities -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Pengajuan Cuti Saya</h6>
        <a href="<?= baseUrl('leave/history') ?>" class="btn btn-sm btn-link text-primary p-0">Lihat Semua</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="myRecentActivitiesTable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis Cuti</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0">Aktivitas Terbaru (Pegawai)</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="recentActivitiesTable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Unit Kerja</th>
                        <th>Jenis Cuti</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .stat-card {
        height: 120px;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }

    .stat-card .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 1rem;
        position: relative;
        z-index: 2;
    }

    .stat-card h6 {
        font-size: 0.875rem;
        line-height: 1.2;
        margin-bottom: 0.5rem;
        min-height: 2.4rem;
        display: flex;
        align-items: center;
        max-width: 70%;
        word-wrap: break-word;
    }

    .stat-card h2 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0;
        position: relative;
        z-index: 3;
    }

    .stat-icon {
        font-size: 3.5rem;
        opacity: 0.2;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 1;
    }

    .chart-container {
        position: relative;
        height: 300px;
        margin-top: 20px;
    }
</style>

<!-- Mobile-only styles to make Recent Activities fill the card-body without gaps -->
<style>
    @media (max-width: 768px) {
        .card .card-body {
            padding: 0.5rem 0.75rem;
        }

        .table-responsive {
            padding: 0;
            margin: 0;
            border: none;
            overflow-x: hidden;
        }

        #myRecentActivitiesTable,
        #recentActivitiesTable {
            width: 100%;
            margin-bottom: 0;
        }

        #myRecentActivitiesTable thead,
        #recentActivitiesTable thead {
            display: none;
        }

        #myRecentActivitiesTable tbody,
        #recentActivitiesTable tbody {
            display: block;
        }

        #myRecentActivitiesTable tbody tr,
        #recentActivitiesTable tbody tr {
            display: block;
            border: 0;
            padding: 0.5rem 1rem;
        }

        #myRecentActivitiesTable tbody tr+tr,
        #recentActivitiesTable tbody tr+tr {
            border-top: 1px solid rgba(0, 0, 0, 0.08);
        }

        #myRecentActivitiesTable td,
        #recentActivitiesTable td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.4rem 0.5rem;
            /* Increased horizontal padding */
            white-space: normal;
            border: none;
            gap: 1rem;
            text-align: right;
            /* width: 100%; removed to prevent horizontal overflow */
        }

        /* Style the label */
        #myRecentActivitiesTable td::before,
        #recentActivitiesTable td::before {
            content: attr(data-label);
            font-weight: 600;
            text-align: left;
            flex-shrink: 0;
        }

        .table-hover tbody tr:hover {
            background: transparent;
        }
    }
</style>

<?php if (!$hide_charts): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script>
        // Wait for jQuery to be loaded
        document.addEventListener('DOMContentLoaded', function () {
            // Check if jQuery is loaded
            if (typeof jQuery === 'undefined') {
                console.error('jQuery is not loaded!');
                return;
            }

            // Use jQuery with $ alias
            (function ($) {
                let charts = {};

                // Load dashboard data
                loadDashboardData();

                function loadDashboardData() {
                    // Load statistics
                    $.post(baseUrl('user/getStatistics'), function (response) {
                        if (response.success) {
                            $('#totalUsers').text(response.data.total_users || 0);
                            $('#pendingCount').text(response.data.pending || 0);
                            $('#waitingDocCount').text(response.data.menunggu_dokumen || 0);
                            $('#completedCount').text(response.data.completed || 0);
                            $('#approvedCount').text(response.data.approved || 0);
                            $('#rejectedCount').text(response.data.rejected || 0);
                        }
                    }, 'json').fail(function (xhr, status, error) {
                        console.error('Error loading statistics:', error);
                        console.error('Response:', xhr.responseText);
                    });

                    // Load charts data
                    $.post(baseUrl('report/getStatistics'), function (response) {
                        if (response.success) {
                            // Destroy existing charts if they exist
                            if (charts.trend) {
                                charts.trend.destroy();
                            }
                            if (charts.leaveType) {
                                charts.leaveType.destroy();
                            }

                            // Trend chart
                            const ctx1 = document.getElementById('trendChart');
                            if (ctx1 && ctx1.getContext && response.monthly_stats && response.monthly_stats.length > 0) {
                                charts.trend = new Chart(ctx1.getContext('2d'), {
                                    type: 'line',
                                    data: {
                                        labels: response.monthly_stats.map(item => item.month),
                                        datasets: [{
                                            label: 'Pengajuan Cuti',
                                            data: response.monthly_stats.map(item => item.total),
                                            borderColor: 'rgb(27, 94, 32)',
                                            backgroundColor: 'rgba(27, 94, 32, 0.1)',
                                            tension: 0.4
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                ticks: {
                                                    stepSize: 1
                                                }
                                            }
                                        }
                                    }
                                });
                            }

                            // Leave type chart
                            const ctx2 = document.getElementById('leaveTypeChart');
                            if (ctx2 && ctx2.getContext && response.leave_type_stats && response.leave_type_stats.length > 0) {
                                charts.leaveType = new Chart(ctx2.getContext('2d'), {
                                    type: 'doughnut',
                                    data: {
                                        labels: response.leave_type_stats.map(item => item.nama_cuti),
                                        datasets: [{
                                            data: response.leave_type_stats.map(item => item.total),
                                            backgroundColor: [
                                                '#1b5e20',
                                                '#FFA500',
                                                '#000080',
                                                '#FFC0CB',
                                                '#8B4513',
                                                '#800080'
                                            ]
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                position: 'bottom'
                                            }
                                        }
                                    }
                                });
                            }
                        }
                    }, 'json').fail(function (xhr, status, error) {
                        console.error('Error loading chart statistics:', error);
                        console.error('Response:', xhr.responseText);
                    });

                    // Load my recent activities
                    $.post(baseUrl('user/getMyRecentActivities'), function (response) {
                        if (response.success && response.data && response.data.length > 0) {
                            const tbody = $('#myRecentActivitiesTable tbody');
                            tbody.empty();

                            response.data.forEach(function (item) {
                                tbody.append(`
                            <tr>
                                <td data-label="Tanggal">${item.created_at_formatted}</td>
                                <td data-label="Jenis Cuti">${item.nama_cuti}</td>
                                <td data-label="Mulai">${item.tanggal_mulai}</td>
                                <td data-label="Selesai">${item.tanggal_selesai}</td>
                                <td data-label="Status">${item.status_badge}</td>
                            </tr>
                        `);
                            });
                        } else {
                            $('#myRecentActivitiesTable tbody').html('<tr><td colspan="5" class="text-center">Tidak ada pengajuan cuti saya</td></tr>');
                        }
                    }, 'json').fail(function (xhr, status, error) {
                        console.error('Error loading my recent activities:', error);
                        $('#myRecentActivitiesTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
                    });

                    // Load recent activities
                    $.post(baseUrl('user/getRecentActivities'), function (response) {
                        if (response.success && response.data && response.data.length > 0) {
                            const tbody = $('#recentActivitiesTable tbody');
                            tbody.empty();

                            response.data.forEach(function (item) {
                                tbody.append(`
                            <tr>
                                <td data-label="Tanggal">${item.created_at_formatted}</td>
                                <td data-label="Nama">${item.nama}</td>
                                <td data-label="Unit Kerja">${item.nama_satker && item.nama_satker !== '' ? item.nama_satker : (item.unit_kerja || '-')}</td>
                                <td data-label="Jenis Cuti">${item.nama_cuti}</td>
                                <td data-label="Status">${item.status_badge}</td>
                            </tr>
                        `);
                            });
                        } else {
                            $('#recentActivitiesTable tbody').html('<tr><td colspan="5" class="text-center">Tidak ada aktivitas terbaru</td></tr>');
                        }
                    }, 'json').fail(function (xhr, status, error) {
                        console.error('Error loading recent activities:', error);
                        console.error('Response:', xhr.responseText);
                        $('#recentActivitiesTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
                    });
                }
            })(jQuery);
        });
    </script>
<?php else: ?>
    <script>
        // Minimal JS for atasan: load statistics and recent activities (no charts)
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof jQuery === 'undefined') {
                console.error('jQuery is not loaded!');
                return;
            }

            (function ($) {
                // Load statistics
                $.post(baseUrl('user/getStatistics'), function (response) {
                    if (response.success) {
                        $('#totalUsers').text(response.data.total_users || 0);
                        $('#pendingCount').text(response.data.pending || 0);
                        $('#waitingDocCount').text(response.data.menunggu_dokumen || 0);
                        $('#completedCount').text(response.data.completed || 0);
                        $('#approvedCount').text(response.data.approved || 0);
                        $('#rejectedCount').text(response.data.rejected || 0);
                    }
                }, 'json').fail(function (xhr, status, error) {
                    console.error('Error loading statistics (atasan):', error);
                    console.error('Response:', xhr.responseText);
                });

                // Load my recent activities
                $.post(baseUrl('user/getMyRecentActivities'), function (response) {
                    if (response.success && response.data && response.data.length > 0) {
                        const tbody = $('#myRecentActivitiesTable tbody');
                        tbody.empty();

                        response.data.forEach(function (item) {
                            tbody.append(`
                        <tr>
                            <td data-label="Tanggal">${item.created_at_formatted}</td>
                            <td data-label="Jenis Cuti">${item.nama_cuti}</td>
                            <td data-label="Mulai">${item.tanggal_mulai}</td>
                            <td data-label="Selesai">${item.tanggal_selesai}</td>
                            <td data-label="Status">${item.status_badge}</td>
                        </tr>
                    `);
                        });
                    } else {
                        $('#myRecentActivitiesTable tbody').html('<tr><td colspan="5" class="text-center">Tidak ada pengajuan cuti saya</td></tr>');
                    }
                }, 'json').fail(function (xhr, status, error) {
                    console.error('Error loading my recent activities (atasan):', error);
                    $('#myRecentActivitiesTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
                });

                // Load recent activities filtered for atasan via backend
                $.post(baseUrl('user/getRecentActivities'), function (response) {
                    if (response.success && response.data && response.data.length > 0) {
                        const tbody = $('#recentActivitiesTable tbody');
                        tbody.empty();

                        response.data.forEach(function (item) {
                            tbody.append(`
                        <tr>
                            <td data-label="Tanggal">${item.created_at_formatted}</td>
                            <td data-label="Nama">${item.nama}</td>
                            <td data-label="Unit Kerja">${item.nama_satker && item.nama_satker !== '' ? item.nama_satker : (item.unit_kerja || '-')}</td>
                            <td data-label="Jenis Cuti">${item.nama_cuti}</td>
                            <td data-label="Status">${item.status_badge}</td>
                        </tr>
                    `);
                        });
                    } else {
                        $('#recentActivitiesTable tbody').html('<tr><td colspan="5" class="text-center">Tidak ada aktivitas terbaru</td></tr>');
                    }
                }, 'json').fail(function (xhr, status, error) {
                    console.error('Error loading recent activities (atasan):', error);
                    console.error('Response:', xhr.responseText);
                    $('#recentActivitiesTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
                });
            })(jQuery);
        });
    </script>
<?php endif; ?>