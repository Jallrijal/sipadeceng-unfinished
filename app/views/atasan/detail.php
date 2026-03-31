<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Atasan</h5>
                    <div>
                        <a href="<?php echo baseUrl('atasan/edit/' . $atasan['id_atasan']); ?>" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </a>
                        <a href="<?php echo baseUrl('atasan/index'); ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informasi Atasan</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Nama Atasan:</strong></td>
                                    <td><?php echo htmlspecialchars($atasan['nama_atasan']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>NIP:</strong></td>
                                    <td><?php echo htmlspecialchars($atasan['NIP']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Jabatan:</strong></td>
                                    <td><?php echo htmlspecialchars($atasan['jabatan'] ?? '-'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Role:</strong></td>
                                    <td><?php echo htmlspecialchars($atasan['role'] ?? '-'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Jumlah User:</strong></td>
                                    <td>
                                        <span class="badge bg-<?php echo count($users) > 0 ? 'info' : 'secondary'; ?>">
                                            <?php echo count($users); ?> user
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6>Daftar User yang Menggunakan Atasan Ini</h6>
                            <?php if (empty($users)): ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Belum ada user yang menggunakan atasan ini.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>NIP</th>
                                                <th>Jabatan</th>
                                                <th>Unit Kerja</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($users as $index => $user): ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo htmlspecialchars($user['nama']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['nip']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['jabatan']); ?></td>
                                                    <td><?php require_once __DIR__ . '/../../helpers/satker_helper.php'; echo htmlspecialchars(get_nama_satker($user['unit_kerja'])); ?></td>
                                                    <td>
                                                        <?php if ($user['is_deleted']): ?>
                                                            <span class="badge bg-danger">Non-Aktif</span>
                                                        <?php elseif ($user['is_modified']): ?>
                                                            <span class="badge bg-warning">Diubah</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success">Aktif</span>
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
