<!-- 
Author: Rijal Imamul Haq Syamsu Alam
Lisensi Kepada: Pengadilan Tinggi Agama Makassar
-->

<style>
/* reduce horizontal gap between the two report cards */
.report-cards-row > [class*="col-"] { padding-left: 6px; padding-right: 6px; }
.report-cards-row .card { margin: 0; }

/* Responsive Preview Laporan Table */
@media (max-width: 767.98px) {
    /* Responsive Data Tables */
    #reportPreview .table-bordered {
        display: block;
        width: 100%;
        border: none;
    }
    #reportPreview .table-bordered thead {
        display: none;
    }
    #reportPreview .table-bordered tbody,
    #reportPreview .table-bordered tr,
    #reportPreview .table-bordered td {
        display: block;
        width: 100%;
    }
    #reportPreview .table-bordered tr {
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
    #reportPreview .table-bordered td {
        display: block;
        text-align: right;
        border: none;
        border-bottom: 1px solid #dee2e6;
        padding: 0.75rem;
        overflow-wrap: break-word;
        word-wrap: break-word;
        white-space: normal;
    }
    #reportPreview .table-bordered td:last-child {
        border-bottom: none;
    }
    #reportPreview .table-bordered td::before {
        content: attr(data-label);
        float: left;
        font-weight: bold;
        text-align: left;
        padding-right: 15px;
        max-width: 50%;
    }
    #reportPreview .table-bordered td::after {
        content: "";
        display: table;
        clear: both;
    }

    /* Responsive Informasi Pegawai Table */
    #reportPreview .table-sm,
    #reportPreview .table-sm tbody,
    #reportPreview .table-sm tr {
        display: block;
        width: 100%;
    }
    #reportPreview .table-sm tr {
        display: flex;
        align-items: flex-start;
        border-bottom: 1px solid #dee2e6;
        padding: 0.5rem 0;
        margin-bottom: 0;
    }
    #reportPreview .table-sm tr:last-child {
        border-bottom: none;
    }
    #reportPreview .table-sm td {
        display: block;
        padding: 0;
        border: none;
        word-wrap: break-word;
        overflow-wrap: break-word;
        white-space: normal;
        text-align: left;
    }
    #reportPreview .table-sm td:first-child {
        width: 40% !important; /* Override hardcoded HTML width */
        flex-shrink: 0;
    }
    #reportPreview .table-sm td:last-child {
        width: 60%;
    }
}
</style>

<div class="row gx-0 mb-4 report-cards-row">
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <div>
                    <i class="bi bi-file-earmark-text text-success" style="font-size: 2.5rem;"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <h6 class="mb-1">Laporan Per-Pegawai</h6>
                    <p class="text-muted small mb-0">Rekapitulasi cuti masing-masing pegawai</p>
                </div>
                <button class="btn btn-success btn-sm" onclick="laporanPerorang()">
                    <i class="bi bi-download me-1"></i>Generate
                </button>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <div>
                    <i class="bi bi-file-earmark-bar-graph text-primary" style="font-size: 2.5rem;"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <h6 class="mb-1">Laporan Bulanan</h6>
                    <p class="text-muted small mb-0">Rekapitulasi cuti per bulan</p>
                </div>
                <button class="btn btn-primary btn-sm" onclick="laporanBulanan()">
                    <i class="bi bi-download me-1"></i>Generate
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Preview Laporan</h6>
    </div>
    <div class="card-body">
        <div id="reportPreview" class="text-center text-muted p-5">
            <i class="bi bi-file-text" style="font-size: 4rem;"></i>
            <p class="mt-3">Pilih jenis laporan untuk melihat preview</p>
        </div>
    </div>
    <button id="btnDownloadExcel" class="btn btn-success d-none mx-auto mb-3" style="width: fit-content; font-size: 0.75rem; padding: 0.25rem 0.5rem;" onclick="downloadExcelLaporan()">
        <i class="bi bi-file-earmark-excel me-1"></i>Download Excel
    </button>
</div>

<script>
// Holds current download context after preview is rendered
var currentDownloadParams = null;

function downloadExcelLaporan() {
    if (!currentDownloadParams) return;
    const p = currentDownloadParams;
    let url = '';
    if (p.type === 'perorang') {
        url = baseUrl('report/downloadPerorang') + '?user_id=' + encodeURIComponent(p.user_id);
    } else if (p.type === 'bulanan') {
        url = baseUrl('report/downloadBulanan') + '?month=' + encodeURIComponent(p.month) + '&year=' + encodeURIComponent(p.year);
    }
    if (url) {
        window.location.href = url;
    }
}

// Report generation functions
function laporanBulanan() {
    console.log('=== LAPORAN BULANAN: Starting ===');
    
    // Fetch available years and months from leave_requests
    $.post(baseUrl('report/getAvailableMonthsYear'), {}, function(response) {
        console.log('Response:', response);
        
        if (!response.success) {
            Swal.fire('Error', 'Gagal memuat data tahun/bulan', 'error');
            return;
        }

        const years = response.years || [];
        const monthsByYear = response.months_by_year || {};

        if (years.length === 0) {
            Swal.fire('Data tidak ditemukan', 'Tidak ada data cuti pada database.', 'info');
            return;
        }

        const yearOptions = years.map(y => `<option value="${y}">${y}</option>`).join('');

        Swal.fire({
            title: 'Pilih Bulan dan Tahun',
            html: `
                <div class="text-start">
                    <label class="form-label mb-2"><strong>Pilih Bulan:</strong></label>
                    <select class="form-select mb-3" id="monthSelect"></select>
                    
                    <label class="form-label mb-2"><strong>Pilih Tahun:</strong></label>
                    <select class="form-select" id="yearSelect">${yearOptions}</select>
                </div>
            `,
            confirmButtonText: 'Generate',
            confirmButtonColor: '#1b5e20',
            showCancelButton: true,
            cancelButtonText: 'Batal',
            didOpen: () => {
                // Populate months for the initially selected year
                const populateMonths = (y) => {
                    const months = monthsByYear[y] || [];
                    if (months.length === 0) {
                        $('#monthSelect').html('<option value="">-- Tidak ada bulan --</option>');
                        return;
                    }
                    const opts = months.map(m => {
                        const mm = String(m).padStart(2, '0');
                        return `<option value="${mm}">${getMonthName(mm)}</option>`;
                    }).join('');
                    $('#monthSelect').html(opts);
                };

                const $year = $('#yearSelect');
                $year.off('change').on('change', function() {
                    populateMonths($(this).val());
                });

                // Initial populate
                populateMonths($year.val());
            },
            preConfirm: () => {
                const month = $('#monthSelect').val();
                const year = $('#yearSelect').val();
                
                if (!month || !year) {
                    Swal.showValidationMessage('Silakan pilih bulan dan tahun terlebih dahulu');
                    return false;
                }
                
                return { month, year };
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                const { month, year } = result.value;
                console.log('Fetching data for month:', month, 'year:', year);
                
                // Show loading
                Swal.fire({
                    title: 'Memuat data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Fetch monthly leave report
                $.post(baseUrl('report/getMonthlyLeaveReport'), 
                    { month: month, year: year }, 
                    function(reportResponse) {
                        console.log('Monthly report response:', reportResponse);
                        
                        if (!reportResponse.success) {
                            console.error('ERROR: Report fetch failed:', reportResponse);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: reportResponse.message || 'Gagal memuat data laporan'
                            });
                            return;
                        }
                        
                        const leaveData = reportResponse.data || [];
                        console.log('Leave data count:', leaveData.length);
                        
                        // Build table rows
                        let tableRows = '';
                        if (leaveData.length === 0) {
                            tableRows = '<tr><td colspan="5" class="text-center text-muted">Tidak ada data cuti untuk periode ini</td></tr>';
                        } else {
                            tableRows = leaveData.map(leave => {
                                const sisaKuotaHtml = (leave.sisa_kuota !== null && leave.sisa_kuota !== undefined)
                                    ? `<strong class="text-success">${leave.sisa_kuota} hari</strong>`
                                    : `<span class="text-muted">-</span>`;
                                return `
                                    <tr>
                                        <td data-label="Nama Pegawai">${leave.nama || '-'}</td>
                                        <td data-label="Jenis Cuti">${leave.jenis_cuti || '-'}</td>
                                        <td data-label="Tanggal Mulai">${leave.tanggal_mulai || '-'}</td>
                                        <td data-label="Tanggal Selesai">${leave.tanggal_selesai || '-'}</td>
                                        <td data-label="Sisa Kuota">${sisaKuotaHtml}</td>
                                    </tr>
                                `;
                            }).join('');
                        }
                        
                        // Build report HTML
                        const reportHtml = `
                            <div class="text-start">
                                <h5 class="mb-4">Laporan Bulanan - ${getMonthName(month)} ${year}</h5>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Pegawai</th>
                                                <th>Jenis Cuti</th>
                                                <th>Tanggal Mulai</th>
                                                <th>Tanggal Selesai</th>
                                                <th>Sisa Kuota</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${tableRows}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                        
                        console.log('Displaying report in preview area');
                        $('#reportPreview').html(reportHtml);
                        // Show download button for bulanan
                        currentDownloadParams = { type: 'bulanan', month: month, year: year };
                        $('#btnDownloadExcel').removeClass('d-none');
                        Swal.close();
                        console.log('=== LAPORAN BULANAN: Completed successfully ===');
                        
                    }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX ERROR: Request failed');
                        console.error('Status:', textStatus);
                        console.error('Error:', errorThrown);
                        console.error('Response:', jqXHR.responseText);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data dari server',
                            footer: `<small>Status: ${jqXHR.status} - ${textStatus}</small>`
                        });
                    });
            }
        });
    }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX ERROR: Fetch months/years failed');
        console.error('Status:', textStatus);
        console.error('Error:', errorThrown);
        
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal memuat data dari server',
            footer: `<small>Status: ${jqXHR.status} - ${textStatus}</small>`
        });
    });
}

function laporanPerorang() {
    console.log('=== LAPORAN PERORANG: Starting ===');
    
    // Fetch employee list from server
    const url = baseUrl('report/getEmployeeList');
    console.log('Step 1: Fetching employee list from:', url);
    
    $.post(url, {}, function(response) {
        console.log('Step 2: Employee list response received:', response);
        
        if (!response.success) {
            console.error('ERROR: Employee list fetch failed:', response);
            Swal.fire('Error', 'Gagal memuat data pegawai', 'error');
            return;
        }

        const employees = response.data || [];
        console.log('Step 3: Number of employees:', employees.length);
        
        if (employees.length === 0) {
            console.warn('WARNING: No employees found in database');
            Swal.fire('Data tidak ditemukan', 'Tidak ada data pegawai pada database.', 'info');
            return;
        }

        // Create employee options for datalist
        const employeeOptions = employees.map(emp => 
            `<option data-id="${emp.id}" value="${emp.nama} ${emp.nip ? '(' + emp.nip + ')' : ''}"></option>`
        ).join('');
        console.log('Step 4: Employee options created');

        Swal.fire({
            title: 'Pilih Pegawai',
            html: `
                <input class="form-control" list="employeeDatalist" id="employeeInput" placeholder="Ketik nama atau NIP pegawai..." autocomplete="off">
                <datalist id="employeeDatalist">
                    ${employeeOptions}
                </datalist>
            `,
            confirmButtonText: 'Generate',
            confirmButtonColor: '#1b5e20',
            showCancelButton: true,
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const inputValue = $('#employeeInput').val();
                let userId = null;
                $('#employeeDatalist option').each(function() {
                    if ($(this).attr('value') === inputValue) {
                        userId = $(this).attr('data-id');
                    }
                });
                
                console.log('Step 5: User selected employee ID:', userId);
                if (!userId) {
                    console.warn('WARNING: No valid employee selected');
                    Swal.showValidationMessage('Silakan pilih pegawai yang valid dari daftar');
                    return false;
                }
                return userId;
            }
        }).then((result) => {
            console.log('Step 6: SweetAlert result:', result);
            
            if (result.isConfirmed && result.value) {
                const userId = result.value;
                console.log('Step 7: Confirmed, fetching report for user_id:', userId);
                
                // Show loading
                Swal.fire({
                    title: 'Memuat data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Fetch employee leave report
                const reportUrl = baseUrl('report/getEmployeeLeaveReport');
                const postData = { user_id: userId };
                console.log('Step 8: Sending POST request to:', reportUrl);
                console.log('Step 8: POST data:', postData);
                
                $.post(reportUrl, postData, function(reportResponse) {
                    console.log('Step 9: Report response received:', reportResponse);
                    
                    if (!reportResponse.success) {
                        console.error('ERROR: Report fetch failed:', reportResponse);
                        console.error('ERROR message:', reportResponse.message);
                        console.error('ERROR details:', reportResponse.error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: reportResponse.message || 'Gagal memuat data laporan',
                            footer: reportResponse.error ? `<small>Detail: ${reportResponse.error}</small>` : ''
                        });
                        return;
                    }
                    
                    console.log('Step 10: Report data:', reportResponse.data);
                    const data = reportResponse.data;
                    const leaveRequests = data.leave_requests || [];
                    console.log('Step 11: Leave requests count:', leaveRequests.length);
                    
                    // Build leave periods table
                    let leavePeriodRows = '';
                    if (leaveRequests.length === 0) {
                        leavePeriodRows = '<tr><td colspan="4" class="text-center">Tidak ada riwayat cuti</td></tr>';
                    } else {
                        leavePeriodRows = leaveRequests.map(leave => {
                            const statusBadge = leave.status === 'approved' ? 
                                '<span class="badge bg-success">Disetujui</span>' :
                                leave.status === 'rejected' ?
                                '<span class="badge bg-danger">Ditolak</span>' :
                                '<span class="badge bg-warning">Pending</span>';
                            
                            return `
                                <tr>
                                    <td data-label="Jenis Cuti">${leave.nama_cuti || '-'}</td>
                                    <td data-label="Tanggal Mulai">${leave.tanggal_mulai || '-'}</td>
                                    <td data-label="Tanggal Selesai">${leave.tanggal_selesai || '-'}</td>
                                    <td data-label="Status">${statusBadge}</td>
                                </tr>
                            `;
                        }).join('');
                    }
                    
                    console.log('Step 12: Building HTML report');
                    
                    // Display report in preview area
                    const reportHtml = `
                        <div class="text-start">
                            <h5>Laporan Cuti Pegawai</h5>
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h6 class="card-title">Informasi Pegawai</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td width="150"><strong>Nama</strong></td>
                                            <td>: ${data.nama}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>NIP</strong></td>
                                            <td>: ${data.nip || '-'}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Kuota Tahunan</strong></td>
                                            <td>: ${data.kuota_tahunan} hari</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cuti Terpakai</strong></td>
                                            <td>: ${data.cuti_terpakai} hari</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Sisa Kuota</strong></td>
                                            <td>: <strong class="text-success">${data.sisa_kuota} hari</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h6 class="card-title">Riwayat Periode Cuti</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Jenis Cuti</th>
                                                    <th>Tanggal Mulai</th>
                                                    <th>Tanggal Selesai</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${leavePeriodRows}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    console.log('Step 13: Displaying report in preview area');
                    $('#reportPreview').html(reportHtml);
                    // Show download button for perorang
                    currentDownloadParams = { type: 'perorang', user_id: userId };
                    $('#btnDownloadExcel').removeClass('d-none');
                    Swal.close();
                    console.log('=== LAPORAN PERORANG: Completed successfully ===');
                    
                }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX ERROR: Request failed');
                    console.error('Status:', textStatus);
                    console.error('Error:', errorThrown);
                    console.error('Response:', jqXHR.responseText);
                    console.error('Status Code:', jqXHR.status);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat data dari server',
                        footer: `<small>Status: ${jqXHR.status} - ${textStatus}</small>`
                    });
                });
            }
        });
    }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX ERROR: Employee list request failed');
        console.error('Status:', textStatus);
        console.error('Error:', errorThrown);
        console.error('Response:', jqXHR.responseText);
        console.error('Status Code:', jqXHR.status);
        
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal memuat data dari server',
            footer: `<small>Status: ${jqXHR.status} - ${textStatus}</small>`
        });
    });
}

function showReportPreview(type, params) {
   $('#reportPreview').html('<div class="spinner-border text-primary" role="status"></div>');
   
   // Simulate report generation
   setTimeout(() => {
       let previewHtml = '<div class="text-start">';
       
       switch(type) {
           case 'monthly':
               previewHtml += `
                   <h5>Laporan Bulanan - ${getMonthName(params.month)} ${params.year}</h5>
                   <div class="table-responsive">
                       <table class="table table-bordered mt-3">
                           <thead>
                               <tr>
                                   <th>Unit Kerja</th>
                                   <th>Total Pengajuan</th>
                                   <th>Disetujui</th>
                                   <th>Ditolak</th>
                                   <th>Pending</th>
                               </tr>
                           </thead>
                           <tbody id="monthlyReportBody">
                               <tr>
                                   <td colspan="5" class="text-center">Loading data...</td>
                               </tr>
                           </tbody>
                       </table>
                   </div>
               `;
               break;
               
           case 'yearly':
               previewHtml += `
                   <h5>Laporan Tahunan - ${params.year}</h5>
                   <div class="row mt-3">
                       <div class="col-md-4">
                           <div class="card text-center">
                               <div class="card-body">
                                   <h3 class="text-primary" id="yearlyTotal">
                                       <span class="spinner-border spinner-border-sm text-primary me-2" role="status"></span>
                                   </h3>
                                   <p>Total Pengajuan</p>
                               </div>
                           </div>
                       </div>
                       <div class="col-md-4">
                           <div class="card text-center">
                               <div class="card-body">
                                   <h3 class="text-success" id="yearlyApproved">
                                       <span class="spinner-border spinner-border-sm text-success me-2" role="status"></span>
                                   </h3>
                                   <p>Disetujui</p>
                               </div>
                           </div>
                       </div>
                       <div class="col-md-4">
                           <div class="card text-center">
                               <div class="card-body">
                                   <h3 class="text-danger" id="yearlyRejected">
                                       <span class="spinner-border spinner-border-sm text-danger me-2" role="status"></span>
                                   </h3>
                                   <p>Ditolak</p>
                               </div>
                           </div>
                       </div>
                   </div>
               `;
               break;
               
           case 'unit':
               previewHtml += `
                   <h5>Laporan Unit Kerja - ${params.unit}</h5>
                   <p>Data pengajuan cuti untuk unit kerja ${params.unit}</p>
               `;
               break;
               
           case 'statistics':
               previewHtml += `
                   <h5>Statistik Penggunaan Cuti</h5>
                   <p>Analisis data cuti tahun berjalan</p>
               `;
               break;
       }
       
       previewHtml += '</div>';

       // encode params into a data attribute to avoid inline JS parsing issues
       const _encodedParams = params ? encodeURIComponent(JSON.stringify(params)) : '';
       const _downloadBtnId = 'downloadReportBtn_' + Math.random().toString(36).substr(2, 9);
       previewHtml += `
           <div class="text-center mt-4">
               <button class="btn btn-primary" id="${_downloadBtnId}" data-type="${type}" data-params="${_encodedParams}">
                   <i class="bi bi-download me-2"></i>Download Report
               </button>
           </div>
       `;

       $('#reportPreview').html(previewHtml);

       // attach click handler to the newly created button
       (function(btnId){
           const $btn = $('#' + btnId);
           $btn.off('click').on('click', function(){
               const t = $btn.data('type');
               const pRaw = $btn.data('params');
               const p = pRaw ? JSON.parse(decodeURIComponent(pRaw)) : {};
               downloadReport(t, p);
           });
       })(_downloadBtnId);

       // If yearly preview, fetch real data and populate cards
       if (type === 'yearly') {
           const year = params.year;
           console.log('Fetching yearly report for year', year);
           $.ajax({
               url: baseUrl('report/getYearlyReport'),
               method: 'POST',
               data: { year: year },
               dataType: 'json',
               timeout: 10000,
               success: function(resp) {
                   if (!resp || !resp.success) {
                       console.error('getYearlyReport returned error', resp);
                       return;
                   }

                   const data = resp.data || {};
                   const total = data.total_pengajuan ?? 0;
                   const approved = data.disetujui ?? 0;
                   const rejected = data.ditolak ?? 0;
                   
                   $('#yearlyTotal').text(total);
                   $('#yearlyApproved').text(approved);
                   $('#yearlyRejected').text(rejected);
               },
               error: function(xhr, status, err) {
                   console.error('getYearlyReport AJAX error', status, err, xhr.responseText);
               }
           });
       }

       // If monthly preview, fetch real data and populate table
       if (type === 'monthly') {
           const month = params.month;
           const year = params.year;
           const $tbody = $('#monthlyReportBody');
           if ($tbody.length) {
               $tbody.html('<tr><td colspan="5" class="text-center">Loading data...</td></tr>');
               console.log('Fetching monthly report', month, year);
               $.ajax({
                   url: baseUrl('report/getMonthlyReport'),
                   method: 'POST',
                   data: { month: month, year: year },
                   dataType: 'json',
                   timeout: 10000,
                   success: function(resp) {
                       if (!resp || !resp.success) {
                           $tbody.html(`<tr><td colspan="5" class="text-center text-danger">${(resp && resp.message) ? resp.message : 'Gagal memuat data'}</td></tr>`);
                           console.error('getMonthlyReport returned error', resp);
                           return;
                       }

                       const rows = resp.data || [];
                       if (rows.length === 0) {
                           $tbody.html('<tr><td colspan="5" class="text-center">Tidak ada data untuk periode ini</td></tr>');
                           return;
                       }

                       const trs = rows.map(r => {
                           const unit = r.unit_kerja || r['Unit Kerja'] || '(Tidak Ditetapkan)';
                           const total = r.total_pengajuan ?? r['Total Pengajuan'] ?? 0;
                           const approved = r.disetujui ?? r['Disetujui'] ?? 0;
                           const rejected = r.ditolak ?? r['Ditolak'] ?? 0;
                           const pending = r.pending ?? r['Pending'] ?? 0;
                           return `
                               <tr>
                                   <td data-label="Unit Kerja">${unit}</td>
                                   <td data-label="Total Pengajuan">${total}</td>
                                   <td data-label="Disetujui">${approved}</td>
                                   <td data-label="Ditolak">${rejected}</td>
                                   <td data-label="Pending">${pending}</td>
                               </tr>
                           `;
                       }).join('');

                       $tbody.html(trs);
                   },
                   error: function(xhr, status, err) {
                       const text = status === 'timeout' ? 'Request timeout' : 'Error koneksi ke server';
                       $tbody.html(`<tr><td colspan="5" class="text-center text-danger">${text}</td></tr>`);
                       console.error('getMonthlyReport AJAX error', status, err, xhr.responseText);
                   }
               });
           }
       }
   }, 1000);
}

function downloadReport(type, params) {
   $.post(baseUrl('report/export'), {
       type: type,
       ...params
   }, function(response) {
       if (response.success) {
           // Create CSV file
           let csvContent = '\uFEFF'; // BOM for UTF-8
           csvContent += response.headers.join(',') + '\n';
           
           response.data.forEach(row => {
               const values = Object.values(row).map(val => {
                   // Escape values containing commas or quotes
                   if (typeof val === 'string' && (val.includes(',') || val.includes('"'))) {
                       return '"' + val.replace(/"/g, '""') + '"';
                   }
                   return val;
               });
               csvContent += values.join(',') + '\n';
           });
           
           // Download file
           const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8' });
           const url = window.URL.createObjectURL(blob);
           const a = document.createElement('a');
           a.href = url;
           a.download = response.filename + '.csv';
           document.body.appendChild(a);
           a.click();
           document.body.removeChild(a);
           window.URL.revokeObjectURL(url);
           
           Swal.fire({
               icon: 'success',
               title: 'Download Berhasil',
               text: 'File telah berhasil di-download',
               confirmButtonColor: '#1b5e20'
           });
       }
   }, 'json');
}

function getMonthName(month) {
   const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
   return months[parseInt(month) - 1];
}

function getYearOptions(rangeBefore = 1, rangeAfter = 1) {
    // deprecated: year options are now fetched from server
    return '';
}
</script>