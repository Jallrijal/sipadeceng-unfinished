<!-- 
Author: Rijal Imamul Haq Syamsu Alam
Lisensi Kepada: Pengadilan Tinggi Agama Makassar
-->

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar User</h5>
        <div>
            <button class="btn btn-secondary me-2" onclick="loadUsers()" title="Refresh Data">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
            <div class="btn-group me-2">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-gear me-1"></i><span class="d-none d-md-inline">Pengaturan</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo baseUrl('user/add'); ?>">
                            <i class="bi bi-person-plus me-2"></i>Tambah User
                        </a></li>
                    <li><a class="dropdown-item" href="#" onclick="showImportModal()">
                            <i class="bi bi-file-earmark-arrow-up me-2"></i>Import CSV
                        </a></li>
                    <li><a class="dropdown-item" href="#" onclick="showSyncModal()">
                            <i class="bi bi-diagram-3 me-2"></i>Sinkronisasi Atasan
                        </a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Info Section -->
        <div class="alert alert-info mb-3">
            <h6><i class="bi bi-info-circle me-2"></i>Informasi Status User:</h6>
            <div class="row">
                <div class="col-md-3">
                    <span class="badge bg-success">Aktif</span> - User dapat login dan menggunakan sistem
                </div>
                <div class="col-md-3">
                    <span class="badge bg-warning">Diubah</span> - Data user telah dimodifikasi
                </div>
                <div class="col-md-3">
                    <span class="badge bg-danger">Non-Aktif</span> - User tidak dapat login (gunakan Edit untuk mengubah
                    status)
                </div>
                <div class="col-md-3">
                    <span class="text-muted"><i class="bi bi-exclamation-triangle me-1"></i>Tombol Hapus = Hapus
                        Permanen</span>
                </div>
            </div>
        </div>
        <!-- Search and Filter Section -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari user...">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="filterUnit">
                    <option value="">Semua Unit Kerja</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="filterType">
                    <option value="">Semua Tipe</option>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="filterUserStatus">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="modified">Diubah</option>
                    <option value="deleted">Non-Aktif</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                    <i class="bi bi-x-circle"></i> Clear
                </button>
            </div>
        </div>

        <!-- Results Info -->
        <div class="row mb-2">
            <div class="col-md-12">
                <small class="text-muted" id="resultsInfo">
                    Menampilkan <span id="totalResults">0</span> user
                </small>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped" id="usersTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Jabatan</th>
                        <th>Unit Kerja</th>
                        <th>Atasan</th>
                        <th>Tipe</th>
                        <th>Status User</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Jabatan</th>
                    <th>Unit Kerja</th>
                    <th>Atasan</th>
                    <th>Tipe</th>
                    <th>Status User</th>
                    <th>Aksi</th>
                </tr>
                <!-- Data akan dimuat via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Sinkronisasi Atasan -->
<div class="modal fade" id="syncAtasanModal" tabindex="-1" aria-labelledby="syncAtasanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning bg-opacity-10">
                <h5 class="modal-title" id="syncAtasanModalLabel">
                    <i class="bi bi-diagram-3 me-2 text-warning"></i>Sinkronisasi Atasan Otomatis
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Cara Kerja:</strong> Sistem akan menentukan atasan langsung untuk setiap pegawai secara
                    otomatis berdasarkan jabatan mereka, sesuai dengan hierarki organisasi yang sudah dikonfigurasi.
                </div>
                <p class="fw-semibold mb-2">Pilih Mode Sinkronisasi:</p>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="syncMode" id="modeOnlyNull" value="only_null"
                        checked>
                    <label class="form-check-label" for="modeOnlyNull">
                        <i class="bi bi-person-x me-1 text-danger"></i>
                        <strong>Hanya yang belum ada atasan</strong>
                        <div class="text-muted small">Aman – tidak mengubah user yang atasannya sudah terpasang</div>
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="syncMode" id="modeAll" value="all">
                    <label class="form-check-label" for="modeAll">
                        <i class="bi bi-arrow-repeat me-1 text-primary"></i>
                        <strong>Semua pegawai (override atasan lama)</strong>
                        <div class="text-muted small">Akan mengganti atasan yang sudah ada dengan hasil deteksi otomatis
                        </div>
                    </label>
                </div>
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Pastikan data jabatan pegawai sudah diisi dengan benar sebelum menjalankan sinkronisasi.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="btnRunSync" onclick="runSyncAtasan()">
                    <i class="bi bi-play-circle me-2"></i>Jalankan Sinkronisasi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hasil Sinkronisasi Atasan -->
<div class="modal fade" id="syncResultModal" tabindex="-1" aria-labelledby="syncResultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="syncResultModalLabel">
                    <i class="bi bi-diagram-3-fill me-2"></i>Hasil Sinkronisasi Atasan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Kartu Ringkasan -->
                <div class="row mb-3" id="syncStatCards">
                    <div class="col-6 col-md-3 mb-2">
                        <div class="card border-primary text-center">
                            <div class="card-body py-2">
                                <h4 class="card-title text-primary mb-0" id="syncTotalCount">0</h4>
                                <small class="text-muted">Diproses</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="card border-success text-center">
                            <div class="card-body py-2">
                                <h4 class="card-title text-success mb-0" id="syncSuccessCount">0</h4>
                                <small class="text-muted">Berhasil</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="card border-warning text-center">
                            <div class="card-body py-2">
                                <h4 class="card-title text-warning mb-0" id="syncNoMatchCount">0</h4>
                                <small class="text-muted">Tidak Cocok</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="card border-danger text-center">
                            <div class="card-body py-2">
                                <h4 class="card-title text-danger mb-0" id="syncErrorCount">0</h4>
                                <small class="text-muted">Error</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Detail Hasil -->
                <ul class="nav nav-tabs mb-3" id="syncResultTabs">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#syncTabBerhasil"> <i
                                class="bi bi-check-circle text-success me-1"></i>Berhasil <span
                                class="badge bg-success ms-1" id="tabBadgeBerhasil">0</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#syncTabTidakCocok">
                            <i class="bi bi-question-circle text-warning me-1"></i>Tidak Cocok <span
                                class="badge bg-warning ms-1" id="tabBadgeTidakCocok">0</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#syncTabError">
                            <i class="bi bi-x-circle text-danger me-1"></i>Error <span class="badge bg-danger ms-1"
                                id="tabBadgeError">0</span>
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    <!-- Tab Berhasil -->
                    <div class="tab-pane fade show active" id="syncTabBerhasil">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped" id="syncTableBerhasil">
                                <thead class="table-success">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Atasan Baru</th>
                                        <th>Jabatan Atasan</th>
                                    </tr>
                                </thead>
                                <tbody id="syncBodyBerhasil"></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Tab Tidak Cocok -->
                    <div class="tab-pane fade" id="syncTabTidakCocok">
                        <div class="alert alert-warning">
                            <i class="bi bi-lightbulb me-2"></i>
                            <strong>Tips:</strong> User-user berikut jabatannya tidak cocok dengan aturan hierarki.
                            Periksa format jabatan mereka atau pilih atasan secara manual saat edit user.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped" id="syncTableTidakCocok">
                                <thead class="table-warning">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody id="syncBodyTidakCocok"></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Tab Error -->
                    <div class="tab-pane fade" id="syncTabError">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped" id="syncTableError">
                                <thead class="table-danger">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody id="syncBodyError"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary"
                    onclick="$('#syncResultModal').modal('hide'); loadUsers();">
                    <i class="bi bi-arrow-clockwise me-2"></i>Refresh Daftar User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import CSV -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Data User dari CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="csvFile" class="form-label">Pilih File CSV</label>
                    <input type="file" class="form-control" id="csvFile" accept=".csv" required>
                    <div class="form-text">Format file harus CSV dengan encoding UTF-8. Mendukung delimiter comma (,)
                        dan semicolon (;)</div>
                </div>

                <div class="mb-3">
                    <h6>Format CSV yang diharapkan:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Kolom CSV</th>
                                    <th>Mapping Database</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>NAMA</td>
                                    <td>nama</td>
                                    <td><span class="badge bg-danger">Wajib</span></td>
                                </tr>
                                <tr>
                                    <td>NIP/NRP</td>
                                    <td>nip</td>
                                    <td><span class="badge bg-danger">Wajib</span></td>
                                </tr>
                                <tr>
                                    <td>JABATAN</td>
                                    <td>jabatan</td>
                                    <td><span class="badge bg-danger">Wajib</span></td>
                                </tr>
                                <tr>
                                    <td>GOL</td>
                                    <td>golongan</td>
                                    <td><span class="badge bg-danger">Wajib</span></td>
                                </tr>
                                <tr>
                                    <td>SATKER</td>
                                    <td>unit_kerja</td>
                                    <td><span class="badge bg-danger">Wajib</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Fitur Import:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>Deteksi Otomatis:</strong> Sistem akan mendeteksi delimiter (comma/semicolon) secara
                            otomatis</li>
                        <li><strong>Skip Header Non-Data:</strong> Baris kosong dan header non-data akan di-skip
                            otomatis</li>
                        <li><strong>Generate Username:</strong> Username di-generate otomatis dari SATKER dengan pola:
                            <ul>
                                <li><code>pa_[nama_kota]</code> untuk "Pengadilan Agama"</li>
                                <li><code>pta_[nama_kota]</code> untuk "Pengadilan Tinggi Agama"</li>
                            </ul>
                        </li>
                        <li><strong>Password Default:</strong> Semua user akan memiliki password "password" (ter-hash)
                        </li>
                        <li><strong>Validasi Duplikasi:</strong> Cek NIP dan username untuk menghindari duplikasi</li>
                        <li><strong>Kuota Cuti:</strong> Setiap user baru akan mendapat kuota cuti default</li>
                        <li><strong>Update Data Existing:</strong> Jika NIP sudah ada, data akan diupdate (termasuk
                            perpindahan unit kerja)</li>
                        <li><strong>Username Dinamis:</strong> Username akan diupdate sesuai unit kerja baru</li>
                        <li><strong>Pindah Unit Kerja:</strong> Saat user berpindah unit kerja, semua kuota cuti ikut
                            berpindah</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Contoh Format CSV:</strong><br>
                    <code>NAMA;NIP/NRP;JABATAN;GOL;SATKER</code><br>
                    <code>Dr. Drs. Khaeril R, M.H.;195912311986031038;Ketua Pengadilan Agama;IV/e;Pengadilan Tinggi Agama Makassar</code>
                </div>

                <div class="alert alert-success">
                    <i class="bi bi-arrow-repeat me-2"></i>
                    <strong>Fitur Perpindahan Unit Kerja:</strong><br>
                    <ul class="mb-0 mt-2">
                        <li>Jika NIP sudah ada di database, data akan diupdate (tidak membuat akun baru)</li>
                        <li>Username akan diupdate sesuai unit kerja baru saat perpindahan</li>
                        <li>Saat unit kerja berubah, semua kuota cuti (tahunan, sakit, besar, melahirkan, alasan
                            penting, luar tanggungan) ikut berpindah</li>
                        <li>Masa kerja tetap mengikuti digit 9-14 NIP</li>
                        <li>Data yang tidak berubah dibiarkan tanpa perubahan</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="importCSV()">
                    <i class="bi bi-upload me-2"></i>Import Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hasil Import -->
<div class="modal fade" id="importResultModal" tabindex="-1" aria-labelledby="importResultModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importResultModalLabel">
                    <i class="bi bi-file-earmark-check me-2"></i>
                    Hasil Import Data User
                </h5>
                <div class="ms-auto me-3">
                    <span class="badge bg-primary" id="importStatusBadge">
                        <i class="bi bi-check-circle me-1"></i>Selesai
                    </span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info" id="importSummaryAlert">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Ringkasan Import:</strong>
                            <span id="importSummary"></span>
                        </div>
                    </div>
                </div>

                <div class="row mb-3" id="importStats" style="display: none;">
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <h5 class="card-title text-success" id="newUsersCount">0</h5>
                                <p class="card-text">User Baru</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <h5 class="card-title text-warning" id="updatedUsersCount">0</h5>
                                <p class="card-text">User Diupdate</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <h5 class="card-title text-info" id="totalProcessedCount">0</h5>
                                <p class="card-text">Total Diproses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-danger">
                            <div class="card-body text-center">
                                <h5 class="card-title text-danger" id="errorCount">0</h5>
                                <p class="card-text">Error</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3" id="importNotes" style="display: none;">
                    <div class="col-md-12">
                        <div class="alert alert-light border">
                            <h6 class="alert-heading"><i class="bi bi-lightbulb me-2"></i>Catatan Penting:</h6>
                            <ul class="mb-0">
                                <li>Data yang berhasil diimport akan langsung tersedia di sistem</li>
                                <li>Untuk user baru, password default adalah "password"</li>
                                <li>Username di-generate otomatis berdasarkan unit kerja</li>
                                <li>Kuota cuti default akan diberikan untuk user baru</li>
                                <li>Perpindahan unit kerja akan memindahkan semua kuota cuti</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" id="importResultTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="success-tab" data-bs-toggle="tab"
                                    data-bs-target="#success-content" type="button" role="tab"
                                    aria-controls="success-content" aria-selected="true">
                                    <i class="bi bi-check-circle text-success me-2"></i>Berhasil
                                    <span class="badge bg-success ms-2" id="successCount">0</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="error-tab" data-bs-toggle="tab"
                                    data-bs-target="#error-content" type="button" role="tab"
                                    aria-controls="error-content" aria-selected="false">
                                    <i class="bi bi-x-circle text-danger me-2"></i>Error
                                    <span class="badge bg-danger ms-2" id="errorCount">0</span>
                                </button>
                            </li>
                            <li class="nav-item ms-auto" role="presentation">
                                <button class="btn btn-sm btn-outline-secondary" onclick="exportImportResult()"
                                    title="Export Hasil Import">
                                    <i class="bi bi-download me-1"></i>Export
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="importResultTabContent">
                            <div class="tab-pane fade show active" id="success-content" role="tabpanel"
                                aria-labelledby="success-tab">
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped table-hover" id="successTable">
                                        <thead class="table-success">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>NIP</th>
                                                <th>Jabatan</th>
                                                <th>Golongan</th>
                                                <th>Unit Kerja</th>
                                                <th>Username</th>
                                                <th>Aksi</th>
                                                <th>Detail</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data berhasil akan dimuat di sini -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="error-content" role="tabpanel" aria-labelledby="error-tab">
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped table-hover" id="errorTable">
                                        <thead class="table-danger">
                                            <tr>
                                                <th>No</th>
                                                <th>Baris</th>
                                                <th>Error</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data error akan dimuat di sini -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="me-auto">
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        Import selesai pada: <span id="importTimestamp"></span>
                    </small>
                    <br>
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Klik "Refresh Data User" untuk melihat data terbaru di tabel utama
                    </small>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="closeImportAndRefresh()">
                    <i class="bi bi-arrow-clockwise me-2"></i>Refresh Data User
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @media (min-width: 577px) and (max-width: 1024px) {
        .table-responsive {
            overflow-x: auto !important;
            width: 100vw !important;
            max-width: 100vw !important;
            min-width: 100vw !important;
            margin-left: -16px !important;
            margin-right: -16px !important;
            box-sizing: border-box !important;
            padding: 0 !important;
        }

        #usersTable {
            min-width: 900px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        #usersTable th,
        #usersTable td {
            white-space: nowrap !important;
            padding: 0.5rem 0.75rem !important;
            text-align: left !important;
            font-size: 1rem !important;
        }
    }

    @media (max-width: 576px) {

        /* Fix double click issue on mobile */
        .table-hover tbody tr:hover {
            background: transparent;
        }

        .user-header-mobile {
            display: flex !important;
            align-items: center !important;
            gap: 6px;
            margin-bottom: 12px !important;
            flex-wrap: nowrap !important;
            overflow: hidden;
        }

        .user-header-mobile .header-title {
            font-size: 1rem !important;
            margin-bottom: 0 !important;
            font-weight: 600;
            flex-shrink: 1;
            white-space: nowrap !important;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-header-mobile .btn,
        .user-header-mobile .btn-group,
        .user-header-mobile .dropdown-toggle {
            font-size: 0.92rem !important;
            padding: 2px 10px !important;
            height: 30px !important;
            min-width: 32px !important;
            border-radius: 5px !important;
            line-height: 1.2 !important;
        }

        .user-header-mobile .btn-success .tambah-user-text {
            display: inline;
        }
    }

    @media (max-width: 400px) {
        .user-header-mobile .btn-success .tambah-user-text {
            display: none !important;
        }

        .user-header-mobile .btn-success i {
            margin-right: 0 !important;
        }
    }

    /* Modal Import Result Responsive */
    @media (max-width: 768px) {
        #importResultModal .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }

        #importResultModal .table-responsive {
            font-size: 0.875rem;
        }

        #importResultModal .table th,
        #importResultModal .table td {
            padding: 0.5rem 0.25rem;
        }

        #importResultModal .nav-tabs .nav-link {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }

        /* Force table and all DataTables wrappers to 100% width */
        #usersTable_wrapper,
        #usersTable_wrapper .row,
        #usersTable_wrapper .col-sm-12,
        #usersTable,
        #usersTable tbody,
        #usersTable tr {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 100% !important;
            box-sizing: border-box !important;
            display: block !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        /* Fix Responsive Table Content for Users Table */
        #usersTable td {
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            white-space: normal !important;
            text-align: right !important;
            font-size: 0.78rem !important; /* Kembalikan ke ukuran ideal */
            padding-right: 15px !important; 
            padding-left: 10px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        #usersTable td::before {
            flex-basis: 30% !important;
            flex-shrink: 0 !important;
            text-align: left !important;
            padding-right: 5px !important;
            font-size: 0.75rem !important;
        }

        #usersTable td > div {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            text-align: right;
            max-width: 70%;
            white-space: normal !important;
            word-break: break-all !important;
        }
    }
</style>

<!-- Mobile-only responsive overrides for DataTables controls (users table) -->
<style>
    @media (max-width: 576px) {

        /* Target controls generated by DataTables for the users table only */
        #usersTable_wrapper .dataTables_filter,
        #usersTable_wrapper .dataTables_length {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            flex-wrap: nowrap !important;
            margin-bottom: 6px !important;
        }

        #usersTable_wrapper .dataTables_filter label,
        #usersTable_wrapper .dataTables_length label {
            font-size: 0.82rem !important;
            margin: 0 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            max-width: 40% !important;
        }

        /* Make the search input and length select compact on mobile */
        #usersTable_wrapper .dataTables_filter input[type="search"],
        #usersTable_wrapper .dataTables_length select {
            width: 64px !important;
            /* small fixed width for mobile */
            max-width: 30vw !important;
            padding: 4px 6px !important;
            font-size: 0.82rem !important;
            box-sizing: border-box !important;
            height: auto !important;
        }

        /* If DataTables injects bootstrap classes, target those variants too */
        #usersTable_wrapper .dataTables_filter input.form-control,
        #usersTable_wrapper .dataTables_length select.form-select {
            width: 64px !important;
        }
    }
</style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        // Load users data
        loadUsers();
        loadUnits();

        // Search and filter functionality
        $('#searchInput').on('input', filterUsers);
        $('#filterUnit').on('change', filterUsers);
        $('#filterType').on('change', filterUsers);
        $('#filterUserStatus').on('change', filterUsers);

        function loadUnits() {
            $.post(baseUrl('user/getAllUnits'), function (response) {
                if (response.success) {
                    const filterUnit = $('#filterUnit');
                    response.data.forEach(unit => {
                        filterUnit.append(`<option value="${unit}">${unit}</option>`);
                    });
                }
            }, 'json');
        }

        function loadUsers() {
            // Show loading state
            const refreshBtn = $('.btn-secondary i');
            refreshBtn.removeClass('bi-arrow-clockwise').addClass('bi-arrow-clockwise spin');

            $.post(baseUrl('user/getUsers'), function (response) {
                if (response.success) {
                    // Store original data for filtering
                    originalUsers = response.data;

                    // Destroy existing DataTable (if any) before re-render
                    if ($.fn.DataTable.isDataTable('#usersTable')) {
                        $('#usersTable').DataTable().destroy();
                    }

                    const tbody = $('#usersTable tbody');
                    tbody.empty();

                    response.data.forEach((user, index) => {
                        const row = `
                        <tr>
                            <td data-label="No"><div>${index + 1}</div></td>
                            <td data-label="Nama"><div>${user.nama}</div></td>
                            <td data-label="NIP"><div>${user.nip}</div></td>
                            <td data-label="Jabatan"><div>${user.jabatan}</div></td>
                            <td data-label="Unit Kerja"><div>${user.nama_satker ? user.nama_satker : user.unit_kerja}</div></td>
                            <td data-label="Atasan"><div>${user.nama_atasan ? `${user.nama_atasan}<br><small class="text-muted d-none d-md-inline">${user.nip_atasan}</small>` : '<span class="text-muted">-</span>'}</div></td>
                            <td data-label="Tipe"><div><span class="badge bg-${user.user_type === 'admin' ? 'success' : 'info'}">${user.user_type}</span></div></td>
                            <td data-label="Status User"><div>${user.user_status_badge || '<span class="badge bg-success">Aktif</span>'}</div></td>
                            <td data-label="Aksi"><div class="w-100 d-flex justify-content-end gap-1"><a href="${baseUrl('user/edit/' + user.id)}" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a><button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})" title="Hapus"><i class="bi bi-trash"></i></button></div></td>
                        </tr>
                    `;
                        tbody.append(row);
                    });

                    // Initialize DataTable for pagination/length menu
                    $('#usersTable').DataTable({
                        searching: false,
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
                            "sZeroRecords": "Tidak ditemukan data yang sesuai",
                            "oPaginate": {
                                "sFirst": "Pertama",
                                "sLast": "Terakhir",
                                "sNext": "Selanjutnya",
                                "sPrevious": "Sebelumnya"
                            }
                        },
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
                    });
                }

                // Reset loading state
                refreshBtn.removeClass('bi-arrow-clockwise spin').addClass('bi-arrow-clockwise');

                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }, 'json');
        }

        window.deleteUser = function (id) {
            Swal.fire({
                title: 'Hapus User Permanen?',
                text: "User akan dihapus secara permanen dari database. Semua data terkait (cuti, kuota, dll) akan hilang!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus permanen!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(baseUrl('user/delete'), { id: id }, function (response) {
                        if (response.success) {
                            Swal.fire('Terhapus!', 'User berhasil dihapus.', 'success');
                            loadUsers();
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    }, 'json');
                }
            });
        }

        // Store original data for filtering
        let originalUsers = [];

        function filterUsers() {
            const searchTerm = $('#searchInput').val().toLowerCase();
            const filterUnit = $('#filterUnit').val();
            const filterType = $('#filterType').val();
            const filterUserStatus = $('#filterUserStatus').val();

            // Destroy existing DataTable (if any) before re-render
            if ($.fn.DataTable.isDataTable('#usersTable')) {
                $('#usersTable').DataTable().destroy();
            }

            const tbody = $('#usersTable tbody');
            tbody.empty();

            let filteredUsers = originalUsers.filter(user => {
                const matchesSearch = user.nama.toLowerCase().includes(searchTerm) ||
                    user.nip.toLowerCase().includes(searchTerm) ||
                    user.jabatan.toLowerCase().includes(searchTerm);
                const matchesUnit = !filterUnit || user.unit_kerja === filterUnit;
                const matchesType = !filterType || user.user_type === filterType;

                // Filter berdasarkan status user
                let matchesUserStatus = true;
                if (filterUserStatus) {
                    if (filterUserStatus === 'active') {
                        matchesUserStatus = !user.is_deleted && !user.is_modified;
                    } else if (filterUserStatus === 'modified') {
                        matchesUserStatus = user.is_modified;
                    } else if (filterUserStatus === 'deleted') {
                        matchesUserStatus = user.is_deleted;
                    }
                }

                return matchesSearch && matchesUnit && matchesType && matchesUserStatus;
            });

            filteredUsers.forEach((user, index) => {
                const row = `
                <tr>
                    <td data-label="No">${index + 1}</td>
                    <td data-label="Nama">${user.nama}</td>
                    <td data-label="NIP">${user.nip}</td>
                    <td data-label="Jabatan">${user.jabatan}</td>
                    <td data-label="Unit Kerja">${user.nama_satker ? user.nama_satker : user.unit_kerja}</td>
                    <td data-label="Atasan">${user.nama_atasan ? `${user.nama_atasan}<br><small class="text-muted">${user.nip_atasan}</small>` : '<span class="text-muted">-</span>'}</td>
                    <td data-label="Tipe"><span class="badge bg-${user.user_type === 'admin' ? 'success' : 'info'}">${user.user_type}</span></td>
                    <td data-label="Status User">${user.user_status_badge || '<span class="badge bg-success">Aktif</span>'}</td>
                    <td data-label="Aksi"><a href="${baseUrl('user/edit/' + user.id)}" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a><button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})" title="Hapus"><i class="bi bi-trash"></i></button></td>
                </tr>
            `;
                tbody.append(row);
            });

            // Initialize tooltips for filtered results
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Update results count
            $('#totalResults').text(filteredUsers.length);

            // Initialize DataTable for pagination/length menu after filtering
            $('#usersTable').DataTable({
                searching: false,
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
                    "sZeroRecords": "Tidak ditemukan data yang sesuai",
                    "oPaginate": {
                        "sFirst": "Pertama",
                        "sLast": "Terakhir",
                        "sNext": "Selanjutnya",
                        "sPrevious": "Sebelumnya"
                    }
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
            });
        }

        window.clearFilters = function () {
            $('#searchInput').val('');
            $('#filterUnit').val('');
            $('#filterType').val('');
            $('#filterUserStatus').val('');
            filterUsers();
        }

        window.showImportModal = function () {
            $('#importModal').modal('show');
        }

        window.importCSV = function () {
            const fileInput = document.getElementById('csvFile');
            const file = fileInput.files[0];

            if (!file) {
                Swal.fire('Error!', 'Silakan pilih file CSV terlebih dahulu', 'error');
                return;
            }

            if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
                Swal.fire('Error!', 'File harus berformat CSV', 'error');
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Memproses File...',
                text: 'Sedang membaca dan memvalidasi data CSV',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('csv_file', file);

            $.ajax({
                url: baseUrl('user/importCSV'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        // Tutup loading SweetAlert
                        Swal.close();

                        // Tutup modal import
                        $('#importModal').modal('hide');
                        fileInput.value = '';

                        // Tampilkan modal hasil import
                        showImportResultModal(response);
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    let errorMessage = 'Terjadi kesalahan saat memproses file';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMessage, 'error');
                }
            });
        }

        window.showImportResultModal = function (response) {
            const details = response.details;
            const successCount = details.success.length;
            const errorCount = details.error.length;
            const totalProcessed = details.total_processed;

            // Get file name from the file input
            const fileInput = document.getElementById('csvFile');
            const file = fileInput.files[0];
            const fileName = file ? file.name : 'File CSV';
            const fileSize = file ? formatFileSize(file.size) : '';

            // Update ringkasan
            $('#importSummary').html(`
            File: <strong>${fileName}</strong>${fileSize ? ` (${fileSize})` : ''} | 
            Total diproses: <strong>${totalProcessed}</strong> | 
            Berhasil: <strong class="text-success">${successCount}</strong> | 
            Gagal: <strong class="text-danger">${errorCount}</strong>
        `);

            // Update badge count
            $('#successCount').text(successCount);
            $('#errorCount').text(errorCount);

            // Calculate statistics
            let newUsersCount = 0;
            let updatedUsersCount = 0;

            if (details.imported_data && details.imported_data.length > 0) {
                details.imported_data.forEach(data => {
                    if (data.action === 'Tambah Baru') {
                        newUsersCount++;
                    } else {
                        updatedUsersCount++;
                    }
                });
            }

            // Update statistics cards
            $('#newUsersCount').text(newUsersCount);
            $('#updatedUsersCount').text(updatedUsersCount);
            $('#totalProcessedCount').text(totalProcessed);
            $('#errorCount').text(errorCount);

            // Show statistics if there are successful imports
            if (successCount > 0) {
                $('#importStats').show();
            }

            // Set timestamp
            const now = new Date();
            const timestamp = now.toLocaleString('id-ID', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            $('#importTimestamp').text(timestamp);

            // Update alert color based on results
            const summaryAlert = $('#importSummaryAlert');
            summaryAlert.removeClass('alert-info alert-success alert-warning alert-danger');

            if (errorCount === 0 && successCount > 0) {
                summaryAlert.addClass('alert-success');
                summaryAlert.find('i').removeClass('bi-info-circle').addClass('bi-check-circle');
            } else if (errorCount > 0 && successCount === 0) {
                summaryAlert.addClass('alert-danger');
                summaryAlert.find('i').removeClass('bi-info-circle').addClass('bi-x-circle');
            } else if (errorCount > 0 && successCount > 0) {
                summaryAlert.addClass('alert-warning');
                summaryAlert.find('i').removeClass('bi-info-circle').addClass('bi-exclamation-triangle');
            } else {
                summaryAlert.addClass('alert-info');
                summaryAlert.find('i').removeClass('bi-check-circle bi-x-circle bi-exclamation-triangle').addClass('bi-info-circle');
            }

            // Update status badge
            const statusBadge = $('#importStatusBadge');
            if (errorCount === 0 && successCount > 0) {
                statusBadge.removeClass('bg-primary bg-warning bg-danger').addClass('bg-success');
                statusBadge.html('<i class="bi bi-check-circle me-1"></i>Berhasil');
            } else if (errorCount > 0 && successCount === 0) {
                statusBadge.removeClass('bg-primary bg-success bg-warning').addClass('bg-danger');
                statusBadge.html('<i class="bi bi-x-circle me-1"></i>Gagal');
            } else if (errorCount > 0 && successCount > 0) {
                statusBadge.removeClass('bg-primary bg-success bg-danger').addClass('bg-warning');
                statusBadge.html('<i class="bi bi-exclamation-triangle me-1"></i>Sebagian');
            } else {
                statusBadge.removeClass('bg-success bg-warning bg-danger').addClass('bg-primary');
                statusBadge.html('<i class="bi bi-info-circle me-1"></i>Selesai');
            }

            // Populate success table
            const successTbody = $('#successTable tbody');
            successTbody.empty();

            if (details.imported_data && details.imported_data.length > 0) {
                details.imported_data.forEach((data, index) => {
                    const actionBadge = data.action === 'Tambah Baru' ?
                        '<span class="badge bg-success">Tambah Baru</span>' :
                        '<span class="badge bg-warning">Update</span>';

                    let detailInfo = '';
                    if (data.action !== 'Tambah Baru' && data.old_unit_kerja) {
                        const changes = [];
                        if (data.old_unit_kerja !== data.unit_kerja) {
                            changes.push(`Unit: ${data.old_unit_kerja} &rarr; ${data.nama_satker ? data.nama_satker : data.unit_kerja}`);
                        }
                        if (data.old_jabatan && data.old_jabatan !== data.jabatan) {
                            changes.push(`Jabatan: ${data.old_jabatan} → ${data.jabatan}`);
                        }
                        if (data.old_username && data.old_username !== data.username) {
                            changes.push(`Username: ${data.old_username} → ${data.username}`);
                        }

                        if (changes.length > 0) {
                            detailInfo = `
                            <small class="text-muted">
                                <strong>Perubahan:</strong><br>
                                ${changes.join('<br>')}
                            </small>
                        `;
                        }
                    }

                    // Determine status based on action
                    let statusBadge = '';
                    if (data.action === 'Tambah Baru') {
                        statusBadge = '<span class="badge bg-success"><i class="bi bi-plus-circle me-1"></i>Baru</span>';
                    } else {
                        statusBadge = '<span class="badge bg-warning"><i class="bi bi-arrow-clockwise me-1"></i>Update</span>';
                    }

                    const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td><strong>${data.nama}</strong></td>
                        <td>${data.nip}</td>
                        <td>${data.jabatan}</td>
                        <td>${data.golongan || '-'}</td>
                        <td>${data.nama_satker ? data.nama_satker : (data.unit_kerja || '-')}</td>
                        <td><code>${data.username}</code></td>
                        <td>${actionBadge}</td>
                        <td>${detailInfo}</td>
                        <td>${statusBadge}</td>
                    </tr>
                `;
                    successTbody.append(row);
                });
            } else if (details.success && details.success.length > 0) {
                // Fallback jika tidak ada imported_data, gunakan success messages
                details.success.forEach((message, index) => {
                    const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td colspan="9">${message}</td>
                    </tr>
                `;
                    successTbody.append(row);
                });
            } else {
                // Tidak ada data berhasil
                const row = `
                <tr>
                    <td colspan="9" class="text-center text-muted">
                        <i class="bi bi-info-circle me-2"></i>
                        Tidak ada data yang berhasil diimport
                    </td>
                </tr>
            `;
                successTbody.append(row);
            }

            // Populate error table
            const errorTbody = $('#errorTable tbody');
            errorTbody.empty();

            if (details.error && details.error.length > 0) {
                details.error.forEach((error, index) => {
                    // Extract row number from error message
                    const rowMatch = error.match(/Baris (\d+):/);
                    const rowNumber = rowMatch ? rowMatch[1] : '-';
                    const errorMessage = error.replace(/^Baris \d+: /, '');

                    // Determine error type for better categorization
                    let errorType = 'Data Error';
                    let errorDescription = '';

                    if (errorMessage.includes('wajib diisi')) {
                        errorType = 'Validasi';
                        errorDescription = 'Data wajib tidak lengkap';
                    } else if (errorMessage.includes('sudah terdaftar')) {
                        errorType = 'Duplikasi';
                        errorDescription = 'Data sudah ada di sistem';
                    } else if (errorMessage.includes('NIP tidak valid')) {
                        errorType = 'Format NIP';
                        errorDescription = 'Format NIP tidak sesuai';
                    } else if (errorMessage.includes('Error -')) {
                        errorType = 'Sistem Error';
                        errorDescription = 'Kesalahan sistem';
                    }

                    const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td><span class="badge bg-secondary">${rowNumber}</span></td>
                        <td><span class="text-danger">${errorMessage}</span></td>
                        <td><span class="badge bg-light text-dark">${errorType}</span><br><small class="text-muted">${errorDescription}</small></td>
                    </tr>
                `;
                    errorTbody.append(row);
                });
            } else {
                // Tidak ada error
                const row = `
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        <i class="bi bi-check-circle me-2"></i>
                        Tidak ada error dalam proses import
                    </td>
                </tr>
            `;
                errorTbody.append(row);
            }

            // Show/hide tabs based on data
            if (successCount === 0) {
                $('#success-tab').hide();
                $('#error-tab').tab('show');
            } else if (errorCount === 0) {
                $('#error-tab').hide();
                $('#success-tab').tab('show');
            } else {
                $('#success-tab').show();
                $('#error-tab').show();
            }

            // Show modal
            $('#importResultModal').modal('show');
        }

        window.closeImportAndRefresh = function () {
            $('#importResultModal').modal('hide');
            loadUsers(); // Reload user data
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        window.exportImportResult = function () {
            // Get current import data from modal
            const successData = [];
            const errorData = [];

            // Collect success data
            $('#successTable tbody tr').each(function () {
                const cells = $(this).find('td');
                if (cells.length > 1) {
                    successData.push({
                        nama: $(cells[1]).text().trim(),
                        nip: $(cells[2]).text().trim(),
                        jabatan: $(cells[3]).text().trim(),
                        golongan: $(cells[4]).text().trim(),
                        unit_kerja: $(cells[5]).text().trim(),
                        username: $(cells[6]).text().trim(),
                        aksi: $(cells[7]).text().trim(),
                        detail: $(cells[8]).text().trim()
                    });
                }
            });

            // Collect error data
            $('#errorTable tbody tr').each(function () {
                const cells = $(this).find('td');
                if (cells.length > 1) {
                    errorData.push({
                        baris: $(cells[1]).text().trim(),
                        error: $(cells[2]).text().trim(),
                        keterangan: $(cells[3]).text().trim()
                    });
                }
            });

            // Create CSV content
            let csvContent = 'data:text/csv;charset=utf-8,';

            // Add success data
            if (successData.length > 0) {
                csvContent += 'HASIL BERHASIL\n';
                csvContent += 'No,Nama,NIP,Jabatan,Golongan,Unit Kerja,Username,Aksi,Detail\n';
                successData.forEach((row, index) => {
                    csvContent += `${index + 1},"${row.nama}","${row.nip}","${row.jabatan}","${row.golongan}","${row.unit_kerja}","${row.username}","${row.aksi}","${row.detail}"\n`;
                });
                csvContent += '\n';
            }

            // Add error data
            if (errorData.length > 0) {
                csvContent += 'HASIL ERROR\n';
                csvContent += 'No,Baris,Error,Keterangan\n';
                errorData.forEach((row, index) => {
                    csvContent += `${index + 1},"${row.baris}","${row.error}","${row.keterangan}"\n`;
                });
            }

            // Create download link
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            link.setAttribute('download', `hasil_import_${new Date().toISOString().slice(0, 10)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        window.showSyncModal = function () {
            $('#syncAtasanModal').modal('show');
        }

        window.runSyncAtasan = function () {
            const mode = $('input[name="syncMode"]:checked').val();

            // Disable button and show loading
            const btn = $('#btnRunSync');
            const originalHtml = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...');

            // Close sync modal
            $('#syncAtasanModal').modal('hide');

            Swal.fire({
                title: 'Sinkronisasi...',
                text: 'Sedang menentukan atasan otomatis untuk user',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: baseUrl('user/syncAutoAtasan'),
                type: 'POST',
                data: { mode: mode },
                dataType: 'json',
                success: function (response) {
                    Swal.close();
                    btn.prop('disabled', false).html(originalHtml);

                    if (response.success) {
                        showSyncResult(response.data);
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    Swal.close();
                    btn.prop('disabled', false).html(originalHtml);
                    Swal.fire('Error!', 'Terjadi kesalahan sistem saat sinkronisasi', 'error');
                }
            });
        }

        function showSyncResult(data) {
            // Update stats
            $('#syncTotalCount').text(data.total_diproses);
            $('#syncSuccessCount').text(data.berhasil);
            $('#syncNoMatchCount').text(data.tidak_cocok);
            $('#syncErrorCount').text(data.error);

            $('#tabBadgeBerhasil').text(data.berhasil);
            $('#tabBadgeTidakCocok').text(data.tidak_cocok);
            $('#tabBadgeError').text(data.error);

            // Populate tables
            const bodyBerhasil = $('#syncBodyBerhasil').empty();
            const bodyTidakCocok = $('#syncBodyTidakCocok').empty();
            const bodyError = $('#syncBodyError').empty();

            let rowNumBerhasil = 1;
            let rowNumTidakCocok = 1;
            let rowNumError = 1;

            if (data.results && data.results.length > 0) {
                data.results.forEach(res => {
                    if (res.status === 'berhasil') {
                        bodyBerhasil.append(`
                        <tr>
                            <td>${rowNumBerhasil++}</td>
                            <td>${res.nama}</td>
                            <td><small>${res.jabatan}</small></td>
                            <td>${res.atasan_baru}</td>
                            <td><small>${res.jabatan_atasan}</small></td>
                        </tr>
                    `);
                    } else if (res.status === 'tidak_cocok') {
                        bodyTidakCocok.append(`
                        <tr>
                            <td>${rowNumTidakCocok++}</td>
                            <td>${res.nama}</td>
                            <td><small>${res.jabatan}</small></td>
                            <td><span class="text-muted italic">${res.keterangan}</span></td>
                        </tr>
                    `);
                    } else {
                        bodyError.append(`
                        <tr>
                            <td>${rowNumError++}</td>
                            <td>${res.nama}</td>
                            <td><small>${res.jabatan}</small></td>
                            <td><span class="text-danger">${res.keterangan}</span></td>
                        </tr>
                    `);
                    }
                });
            }

            // Jika tab kosong, beri pesan
            if (rowNumBerhasil === 1) bodyBerhasil.append('<tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>');
            if (rowNumTidakCocok === 1) bodyTidakCocok.append('<tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>');
            if (rowNumError === 1) bodyError.append('<tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>');

            // Open result modal
            $('#syncResultModal').modal('show');
        }
    });
</script>