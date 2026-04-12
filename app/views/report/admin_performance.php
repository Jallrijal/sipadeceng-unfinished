<!-- 
Author: Rijal Imamul Haq Syamsu Alam
Lisensi Kepada: Pengadilan Tinggi Agama Makassar
-->

<style>
.table-responsive {
    max-height: 600px;
    overflow-y: auto;
}
</style>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-graph-up me-2"></i>Laporan Kinerja Admin - <?php echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?>
                </h6>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="changeMonth()">
                        <i class="bi bi-calendar-event me-1"></i>Pilih Bulan
                    </button>
                    <?php $downloadUrl = function_exists('baseUrl') ? baseUrl('report/downloadAdminPerformanceExcel?month=' . $month . '&year=' . $year) : '/report/downloadAdminPerformanceExcel?month=' . $month . '&year=' . $year; ?>
                    <a href="<?php echo $downloadUrl; ?>" 
                       class="btn btn-sm btn-success">
                        <i class="bi bi-download me-2"></i>Download Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($activities)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">Tidak ada aktivitas</h5>
                        <p class="text-muted">Belum ada aktivitas admin untuk bulan ini.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Tanggal</th>
                                    <th width="25%">Aktivitas</th>
                                    
                                    <th width="20%">Pegawai</th>
                                    <th width="15%">Jenis Cuti</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($activities as $activity): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($activity['created_at'])); ?></td>
                                    <td>
                                        <?php if ($activity['activity_type'] == 'send_final_blanko'): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-send me-1"></i>Kirim Blanko Cuti Final
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-info">
                                                <i class="bi bi-upload me-1"></i>Upload Dokumen Pendukung
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td><?php echo htmlspecialchars($activity['pegawai_nama']); ?></td>
                                    <td><?php echo htmlspecialchars($activity['jenis_cuti']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="text-primary"><?php echo count(array_filter($activities, function($a) { return $a['activity_type'] == 'send_final_blanko'; })); ?></h5>
                                        <small class="text-muted">Blanko Final Dikirim</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="text-info"><?php echo count(array_filter($activities, function($a) { return $a['activity_type'] == 'upload_supporting_document'; })); ?></h5>
                                        <small class="text-muted">Dokumen Pendukung Diupload</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk pilih bulan -->
<div class="modal fade" id="monthPickerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Bulan dan Tahun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="monthForm">
                    <div class="row">
                        <div class="col-6">
                            <label for="selectMonth" class="form-label">Bulan</label>
                            <select class="form-select" id="selectMonth" name="month">
                                <?php 
                                if (isset($months_by_year[$year]) && !empty($months_by_year[$year])):
                                    foreach ($months_by_year[$year] as $m): 
                                ?>
                                    <option value="<?php echo $m; ?>" <?php echo ($m == $month) ? 'selected' : ''; ?>>
                                        <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                                    </option>
                                <?php 
                                    endforeach;
                                else:
                                ?>
                                    <option value="">Tidak ada data</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="selectYear" class="form-label">Tahun</label>
                            <select class="form-select" id="selectYear" name="year" onchange="updateMonths()">
                                <?php if (isset($available_years) && !empty($available_years)): ?>
                                    <?php foreach ($available_years as $y): ?>
                                        <option value="<?php echo $y; ?>" <?php echo ($y == $year) ? 'selected' : ''; ?>>
                                            <?php echo $y; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">Tidak ada data</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="applyFilter()">Terapkan</button>
            </div>
        </div>
    </div>
</div>

<script>
// Data months by year dari server (PHP)
const monthsByYearData = <?php echo json_encode(isset($months_by_year) ? $months_by_year : []); ?>;

function changeMonth() {
    $('#monthPickerModal').modal('show');
}

function updateMonths() {
    const selectedYear = $('#selectYear').val();
    const monthSelect = $('#selectMonth');
    const months = monthsByYearData[selectedYear] || [];
    
    monthSelect.empty();
    
    if (months.length === 0) {
        monthSelect.append('<option value="">Tidak ada data</option>');
    } else {
        const monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        
        months.forEach(function(m) {
            monthSelect.append(`<option value="${m}">${monthNames[m]}</option>`);
        });
    }
}

function applyFilter() {
    const month = $('#selectMonth').val();
    const year = $('#selectYear').val();
    
    if (!month || !year) {
        alert('Pilih bulan dan tahun terlebih dahulu');
        return;
    }
    
    const basePath = (typeof BASE_URL !== 'undefined') ? BASE_URL : '<?php echo function_exists('baseUrl') ? baseUrl('') : ''; ?>';
    window.location.href = `${basePath}/report/adminPerformanceReport?month=${month}&year=${year}`;
}
</script>