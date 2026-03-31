<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadows">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-bell me-2"></i>
                        Notifikasi Saya
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($notifications)): ?>
                        <div class="alert alert-info text-center" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            Tidak ada notifikasi
                        </div>
                    <?php else: ?>
                        <div class="notification-list">
                            <?php foreach ($notifications as $notif): ?>
                                <div class="notification-item border-bottom pb-3 mb-3" data-notif-id="<?php echo $notif['id']; ?>">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-bell text-primary"></i>
                                                <p class="mb-1 notification-message">
                                                    <?php echo htmlspecialchars($notif['message']); ?>
                                                </p>
                                                <?php if (!$notif['is_read']): ?>
                                                    <span class="badge bg-danger">Baru</span>
                                                <?php endif; ?>
                                            </div>
                                            <small class="text-muted d-block mt-2">
                                                <i class="bi bi-clock me-1"></i>
                                                <?php echo formatDateTimeIndonesian($notif['created_at']); ?>
                                            </small>
                                        </div>
                                        <?php if (!$notif['is_read']): ?>
                                            <button class="btn btn-sm btn-outline-primary ms-2 mark-as-read-btn" data-notif-id="<?php echo $notif['id']; ?>" title="Tandai sebagai sudah dibaca">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                Total: <strong><?php echo count($notifications); ?></strong> notifikasi
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .notification-item {
        padding: 12px 0;
        transition: background-color 0.2s ease;
    }
    
    .notification-item:hover {
        background-color: #f8f9fa;
    }
    
    .notification-message {
        font-size: 0.95rem;
        color: #333;
    }
    
    .mark-as-read-btn {
        white-space: nowrap;
    }
    
    .mark-as-read-btn:hover {
        background-color: #0d6efd;
        color: white;
    }

    /* ── Dark Mode Overrides ── */
    body.dark-mode .notification-item {
        border-bottom-color: #21262d !important;
    }

    body.dark-mode .notification-item:hover {
        background-color: #1c2128 !important;
    }

    body.dark-mode .notification-message {
        color: #c9d1d9 !important;
    }

    body.dark-mode .notification-list .text-muted,
    body.dark-mode .notification-list small.text-muted {
        color: #8b949e !important;
    }

    body.dark-mode .card-header.bg-primary {
        background-color: #1a3a5c !important;
        border-bottom-color: #21262d !important;
    }

    body.dark-mode .mark-as-read-btn {
        border-color: #58a6ff !important;
        color: #58a6ff !important;
    }

    body.dark-mode .mark-as-read-btn:hover {
        background-color: #1f6feb !important;
        border-color: #1f6feb !important;
        color: #fff !important;
    }

    /* Fix double click issue on mobile */
    @media (max-width: 576px) {
        .notification-item:hover { background-color: transparent !important; }
        body.dark-mode .notification-item:hover { background-color: transparent !important; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mark as read functionality
        document.querySelectorAll('.mark-as-read-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const notifId = this.getAttribute('data-notif-id');
                const notifItem = document.querySelector(`[data-notif-id="${notifId}"]`);
                
                fetch('<?php echo baseUrl("notification/markAsRead"); ?>/' + notifId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove badge and button
                        const badge = notifItem.querySelector('.badge');
                        if (badge) badge.remove();
                        this.remove();
                        
                        // Show success message
                        alert(data.message);
                    } else {
                        alert('Gagal menandai notifikasi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menandai notifikasi');
                });
            });
        });
    });
</script>
