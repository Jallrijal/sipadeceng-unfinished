<!-- 
Author: Rijal Imamul Haq Syamsu Alam
Lisensi Kepada: Pengadilan Tinggi Agama Makassar
-->

<?php require_once APPROOT . '/views/layouts/main.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Snapshot User</h3>
                    <div class="card-tools">
                        <a href="<?= BASEURL ?>/user/snapshots" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Data Snapshot -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Data Snapshot</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%"><strong>Nama:</strong></td>
                                            <td><?= htmlspecialchars($data['snapshot']['nama']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Username:</strong></td>
                                            <td><?= htmlspecialchars($data['snapshot']['username']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>NIP:</strong></td>
                                            <td><?= htmlspecialchars($data['snapshot']['nip']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jabatan:</strong></td>
                                            <td><?= htmlspecialchars($data['snapshot']['jabatan']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Golongan:</strong></td>
                                            <td><?= htmlspecialchars($data['snapshot']['golongan']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Unit Kerja:</strong></td>
                                            <td><?php require_once __DIR__ . '/../../helpers/satker_helper.php'; echo htmlspecialchars(get_nama_satker($data['snapshot']['unit_kerja'])); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Masuk:</strong></td>
                                            <td><?= formatTanggal($data['snapshot']['tanggal_masuk']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tipe Perubahan:</strong></td>
                                            <td>
                                                <?php if ($data['snapshot']['snapshot_type'] === 'deleted'): ?>
                                                    <span class="badge bg-danger">Dihapus</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Diubah</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Snapshot:</strong></td>
                                            <td><?= formatTanggal($data['snapshot']['snapshot_date']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Alasan:</strong></td>
                                            <td><?= htmlspecialchars($data['snapshot']['reason'] ?? '-') ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Data User Aktif (jika ada) -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Data User Aktif</h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($data['activeUser']): ?>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="30%"><strong>Nama:</strong></td>
                                                <td><?= htmlspecialchars($data['activeUser']['nama']) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Username:</strong></td>
                                                <td><?= htmlspecialchars($data['activeUser']['username']) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>NIP:</strong></td>
                                                <td><?= htmlspecialchars($data['activeUser']['nip']) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Jabatan:</strong></td>
                                                <td><?= htmlspecialchars($data['activeUser']['jabatan']) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Golongan:</strong></td>
                                                <td><?= htmlspecialchars($data['activeUser']['golongan']) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Unit Kerja:</strong></td>
                                                <td><?php require_once __DIR__ . '/../../helpers/satker_helper.php'; echo htmlspecialchars(get_nama_satker($data['activeUser']['unit_kerja'])); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>
                                                    <?php if ($data['activeUser']['is_deleted']): ?>
                                                        <span class="badge bg-danger">Dihapus</span>
                                                    <?php elseif ($data['activeUser']['is_modified']): ?>
                                                        <span class="badge bg-warning">Diubah</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">Aktif</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        </table>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            User tidak ditemukan atau telah dihapus secara permanen.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Cuti -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Riwayat Pengajuan Cuti</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($data['leaveHistory'])): ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            Tidak ada riwayat pengajuan cuti untuk user ini.
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nomor Surat</th>
                                                        <th>Jenis Cuti</th>
                                                        <th>Tanggal</th>
                                                        <th>Status</th>
                                                        <th>Status User</th>
                                                        <th>Dokumen</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($data['leaveHistory'] as $index => $leave): ?>
                                                        <tr>
                                                            <td><?= $index + 1 ?></td>
                                                            <td><?= htmlspecialchars($leave['nomor_surat']) ?></td>
                                                            <td><?= htmlspecialchars($leave['nama_cuti']) ?></td>
                                                            <td>
                                                                <?= formatTanggal($leave['tanggal_mulai']) ?> - 
                                                                <?= formatTanggal($leave['tanggal_selesai']) ?>
                                                                <br>
                                                                <small class="text-muted">(<?= $leave['jumlah_hari'] ?> hari)</small>
                                                            </td>
                                                            <td><?= $leave['status_badge'] ?></td>
                                                            <td><?= $leave['user_status_badge'] ?></td>
                                                            <td>
                                                                <?php if ($leave['has_final_doc']): ?>
                                                                    <span class="badge bg-success">Tersedia</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Tidak Ada</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 