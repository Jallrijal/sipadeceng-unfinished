<?php require_once APPROOT . '/views/layouts/main.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Perubahan User</h3>
                    <div class="card-tools">
                        <a href="<?= BASEURL ?>/user/snapshots/stats" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar"></i> Statistik
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="<?= BASEURL ?>/user/snapshots" class="form-inline">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Cari nama/NIP..." 
                                           value="<?= $data['filters']['search'] ?? '' ?>">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group" role="group">
                                <a href="<?= BASEURL ?>/user/snapshots" class="btn btn-outline-secondary">Semua</a>
                                <a href="<?= BASEURL ?>/user/snapshots?type=modified" class="btn btn-outline-warning">Diubah</a>
                                <a href="<?= BASEURL ?>/user/snapshots?type=deleted" class="btn btn-outline-danger">Dihapus</a>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Snapshots -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>NIP</th>
                                    <th>Unit Kerja</th>
                                    <th>Jabatan</th>
                                    <th>Tipe Perubahan</th>
                                    <th>Tanggal Snapshot</th>
                                    <th>Alasan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($data['snapshots'])): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">Tidak ada data snapshot</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($data['snapshots'] as $index => $snapshot): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($snapshot['nama']) ?></strong>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars($snapshot['username']) ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($snapshot['nip']) ?></td>
                                            <td><?php require_once __DIR__ . '/../../helpers/satker_helper.php'; echo htmlspecialchars(get_nama_satker($snapshot['unit_kerja'])); ?></td>
                                            <td><?= htmlspecialchars($snapshot['jabatan']) ?></td>
                                            <td>
                                                <?php if ($snapshot['snapshot_type'] === 'deleted'): ?>
                                                    <span class="badge bg-danger">Dihapus</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Diubah</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= formatTanggal($snapshot['snapshot_date']) ?></td>
                                            <td>
                                                <?php if ($snapshot['reason']): ?>
                                                    <span class="text-muted"><?= htmlspecialchars($snapshot['reason']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= BASEURL ?>/user/snapshots/detail/<?= $snapshot['id'] ?>" 
                                                   class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 