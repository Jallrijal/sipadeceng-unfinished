<!-- Top Navbar -->
<div class="top-navbar navbar-sticky-mobile d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <?php
            $offcanvas_target = '#offcanvasSidebarUser';
            $offcanvas_control = 'offcanvasSidebarUser';
            if (isAdmin()) {
                $offcanvas_target = '#offcanvasSidebarAdmin';
                $offcanvas_control = 'offcanvasSidebarAdmin';
            } elseif (function_exists('isAtasan') && isAtasan()) {
                $offcanvas_target = '#offcanvasSidebarAtasan';
                $offcanvas_control = 'offcanvasSidebarAtasan';
            }
        ?>
        <button class="btn btn-link text-dark d-xl-none d-inline-block me-2" id="sidebarToggle" type="button" data-bs-target="<?php echo $offcanvas_target; ?>" aria-controls="<?php echo $offcanvas_control; ?>">
            <i class="bi bi-list fs-4"></i>
        </button>
        <h5 class="mb-0 d-inline-block top-navbar-title"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h5>
    </div>
    <div class="d-flex align-items-center">
          <button class="btn btn-link text-dark me-2" id="darkModeToggle" title="Toggle Dark Mode">
            <i class="bi bi-moon-fill fs-5" id="darkModeIcon"></i>
          </button>
          <!-- Notifications -->
         <div class="dropdown me-3">
            <button class="btn btn-link text-dark position-relative" data-bs-toggle="dropdown" id="notificationDropdown">
                <i class="bi bi-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notifCount" style="display: none;">0</span>
            </button>
            <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                <div class="dropdown-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Notifikasi</h6>
                    <a href="<?php echo baseUrl('notification'); ?>" class="btn btn-sm btn-outline-primary" title="Lihat semua notifikasi">
                        <i class="bi bi-arrow-up-right fs-6"></i>
                    </a>
                </div>
                <div id="notificationList">
                    <div class="text-center p-3 text-muted">
                        <small>Tidak ada notifikasi baru</small>
                    </div>
                </div>
            </div>
          </div>
        <!-- User Profile -->
        <div class="dropdown">
            <button class="btn btn-link text-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle fs-5 me-2"></i>
                <span class="d-none d-md-inline"><?php echo $_SESSION['nama']; ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?php echo baseUrl('user/profile'); ?>"><i class="bi bi-person me-2"></i>Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item logout-link" href="<?php echo baseUrl('auth/logout'); ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
        </div>
    </div>
</div>

<style>
/* Kecilkan judul dan sejajarkan elemen header di HP */
@media (max-width: 576px) {
  .top-navbar {
    padding-left: 8px;
    padding-right: 8px;
  }
  .top-navbar .d-flex.align-items-center {
    gap: 0.25rem;
  }
  .top-navbar-title {
    font-size: 1rem;
    max-width: 120px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0 !important;
  }
  .top-navbar > .d-flex:first-child {
    flex: 1 1 0;
    min-width: 0;
  }
  .top-navbar > .d-flex:last-child {
    flex-shrink: 0;
  }
  .navbar-sticky-mobile {
    position: sticky !important;
    top: 0;
    z-index: 1050;
    background: #fff !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  }
}
/* === Responsive Tablet: 600px - 1024px (portrait & landscape) === */
@media (min-width: 600px) and (max-width: 1024px) {
  html, body {
    width: 100vw;
    max-width: 100vw;
    min-width: 100vw;
    overflow-x: hidden;
  }
  .top-navbar,
  .container,
  .container-fluid,
  .card,
  .row,
  .col-12,
  .col-md-3,
  .col-md-2,
  .col-md-6,
  .col-md-4,
  .table-responsive,
  .table {
    width: 100vw !important;
    max-width: 100vw !important;
    min-width: 100vw !important;
    margin: 0 !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
    box-sizing: border-box;
  }
  main, #main, .main-content {
    width: 100vw !important;
    max-width: 100vw !important;
    min-width: 100vw !important;
    margin: 0 !important;
    padding: 0 !important;
    box-sizing: border-box;
  }
}
/* Tetap responsif untuk tablet landscape dan tablet besar */
@media (min-width: 801px) and (max-width: 1024px) {
  html, body {
    width: 100vw;
    max-width: 100vw;
    overflow-x: hidden;
  }
  .top-navbar, .container, .container-fluid, .card, .row, .col-12, .col-md-3, .col-md-2, .col-md-6, .col-md-4, .table-responsive, .table {
    width: 100vw !important;
    max-width: 100vw !important;
    margin: 0 !important;
    box-sizing: border-box;
  }
}
@media (max-width: 576px), (min-width: 600px) and (max-width: 1024px) {
  .navbar-sticky-mobile {
    position: sticky !important;
    top: 0;
    z-index: 1050;
    background: #fff !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  }
}
@media (max-width: 1024px) {
  .navbar-sticky-mobile {
    position: sticky !important;
    top: 0;
    left: 0;
    right: 0;
    width: 100vw !important;
    min-width: 0 !important;
    z-index: 1050 !important;
    background: #fff !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  }
  body, html, main, #main, .main-content {
    overflow-x: visible !important;
    overflow-y: visible !important;
  }
}
</style>

<!-- Sidebar toggle functionality moved to main.js to prevent conflicts -->