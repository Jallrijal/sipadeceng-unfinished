<div class="card">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="bi bi-envelope me-2"></i>Kelola Email</h6>
    </div>
    <div class="card-body">
        <!-- Current Email Display -->
        <div class="mb-4">
            <label class="form-label text-muted small">Email Anda Saat Ini</label>
            <?php 
                require_once __DIR__ . '/../../core/Database.php';
                $db = Database::getInstance();
                $user = $db->fetch("SELECT email FROM users WHERE id = ?", [$_SESSION['user_id']]);
                $current_email = $user['email'] ?? null;
            ?>
            <div class="d-flex align-items-center gap-2 p-3 bg-light rounded border-start border-4 border-info">
                <strong class="flex-grow-1"><?php echo $current_email ? htmlspecialchars($current_email) : '<span class="text-muted">Tidak ada email tersimpan</span>'; ?></strong>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="editEmailBtn" aria-label="Ubah email">
                    <i class="bi bi-pencil"></i>
                </button>
            </div>
        </div>

        <!-- Email Edit Form -->
        <form id="emailManagementForm" class="mt-3" style="display: none;">
            <div class="mb-3">
                <label for="newEmail" class="form-label fw-5">Email</label>
                <input type="email" class="form-control" id="newEmail" name="email" placeholder="nama@example.com" required>
            </div>
            <div class="d-flex gap-2 pt-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Simpan
                </button>
                <button type="button" class="btn btn-outline-secondary" id="cancelEditBtn">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </button>
                <?php if ($current_email): ?>
                    <button type="button" class="btn btn-outline-danger" id="clearEmailBtn">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                <?php endif; ?>
            </div>
        </form>

        <!-- Info Alert -->
        <div class="alert alert-sm alert-info mt-4 d-flex gap-2" role="alert">
            <i class="bi bi-info-circle flex-shrink-0"></i>
            <small>Email digunakan untuk notifikasi sistem penting. Pastikan email aktif dan dapat menerima pesan.</small>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Email form submission
        $('#emailManagementForm').on('submit', function (e) {
            e.preventDefault();

            const newEmail = $.trim($('#newEmail').val());
            const confirmEmail = $.trim($('#confirmEmail').val());

            // Validation
            if (!newEmail) {
                showEmailAlert('danger', 'Email tidak boleh kosong');
                return;
            }

            if (newEmail !== confirmEmail) {
                showEmailAlert('danger', 'Email tidak cocok. Silakan periksa kembali.');
                return;
            }

            if (!isValidEmail(newEmail)) {
                showEmailAlert('danger', 'Format email tidak valid');
                return;
            }

            // Submit
            $.ajax({
                url: baseUrl('user/updateUserEmail'),
                type: 'POST',
                data: { email: newEmail },
                success: function (response) {
                    if (response.success) {
                        showEmailAlert('success', response.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showEmailAlert('danger', response.message);
                    }
                },
                error: function () {
                    showEmailAlert('danger', 'Terjadi kesalahan. Silakan coba lagi.');
                }
            });
        });

        // Edit / show form
        $('#editEmailBtn').on('click', function () {
            const currentEmail = '<?php echo $current_email ? addslashes($current_email) : ''; ?>';
            $('#newEmail').val(currentEmail);
            $('#emailManagementForm').slideDown();
            $('#newEmail').focus();
        });

        $('#cancelEditBtn').on('click', function () {
            $('#emailManagementForm').slideUp();
            $('#newEmail').val('');
        });

        // Delete email button
        $('#clearEmailBtn').on('click', function () {
            if (confirm('Yakin ingin menghapus email?')) {
                $.ajax({
                    url: baseUrl('user/deleteUserEmail'),
                    type: 'POST',
                    success: function (response) {
                        if (response.success) {
                            showEmailAlert('success', response.message);
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showEmailAlert('danger', response.message);
                        }
                    },
                    error: function () {
                        showEmailAlert('danger', 'Terjadi kesalahan. Silakan coba lagi.');
                    }
                });
            }
        });

        // Helpers
        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function showEmailAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show d-flex gap-2" role="alert">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} flex-shrink-0"></i>
                    <div>${message}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $(alertHtml).insertBefore('#emailManagementForm').delay(5000).fadeOut(function () {
                $(this).remove();
            });
        }
    });
</script>
