<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Daftar Tanda Tangan User</h5>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Jabatan</th>
                    <!-- <th>Unit Kerja</th> -->        
                    <th>Tipe</th>
                    <th>Preview</th>
                    <th>File</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($signatures as $sig): ?>
                <tr>
                    <td><?php echo htmlspecialchars($sig['nama']); ?></td>
                    <td><?php echo htmlspecialchars($sig['nip']); ?></td>
                    <td><?php echo htmlspecialchars($sig['jabatan']); ?></td>
                    <!-- <td><?php require_once __DIR__ . '/../../helpers/satker_helper.php'; echo htmlspecialchars(get_nama_satker($sig['unit_kerja'])); ?></td>  -->
                    <td><?php echo htmlspecialchars($sig['signature_type']); ?></td>
                    <td>
                        <img src="<?php echo getSignatureUrl($sig['signature_file']); ?>" alt="Tanda Tangan" style="max-width:120px;max-height:60px;border:1px solid #ccc;">
                    </td>
                    <td>
                        <a href="<?php echo getSignatureUrl($sig['signature_file']); ?>" target="_blank">Download</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div> 