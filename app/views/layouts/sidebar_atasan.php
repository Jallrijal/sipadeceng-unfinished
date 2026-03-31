<!-- Sidebar Atasan Offcanvas -->
<nav class="d-xl-block d-none sidebar" id="sidebar">
    <div class="sidebar-header p-3">
        <h4><img src="<?php echo baseUrl('public/images/sipadeceng.png'); ?>" alt="Logo SIPADECENG" style="max-width: 50px; height: auto; margin-right: 10px;"> SIPADECENG</h4>
        <p class="mb-0"><?php echo $_SESSION['nama']; ?></p>
        <small><?php echo $_SESSION['jabatan']; ?></small>
    </div>
    <div class="sidebar-menu px-2">
        <a href="<?php echo baseUrl('dashboard/atasan'); ?>" class="<?php echo $this->isActive('dashboard'); ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="<?php echo baseUrl('leave/form'); ?>" class="<?php echo $this->isActive('leave/form'); ?>">
            <i class="bi bi-plus-circle"></i> Ajukan Cuti
        </a>
    <hr class="my-1 mx-1" style="border-color: #003366;">
        <a href="<?php echo baseUrl('approval/index'); ?>" class="<?php echo $this->isActive('approval'); ?>">
            <i class="bi bi-check-square"></i> Persetujuan Cuti
        </a>
        <a href="<?php echo baseUrl('leave/history'); ?>" class="<?php echo $this->isActive('leave/history'); ?>">
            <i class="bi bi-clock-history"></i> Riwayat Cuti
        </a>
        <!-- <a href="<?php echo baseUrl('signature/parafAtasanCuti'); ?>" class="<?php echo $this->isActive('signature'); ?>">
            <i class="bi bi-pen-fill"></i> Paraf Atasan Cuti
        </a> -->
    <hr class="my-1 mx-1" style="border-color: #003366;">
        <a href="<?php echo baseUrl('user/profile'); ?>" class="<?php echo $this->isActive('user/profile'); ?>">
            <i class="bi bi-gear"></i> Pengaturan
        </a>
        <a href="<?php echo baseUrl('auth/logout'); ?>" class="logout-link">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</nav>

<!-- Offcanvas Sidebar for Mobile -->
<div class="d-xl-none">
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebarAtasan" aria-labelledby="offcanvasSidebarAtasanLabel">
        <div class="offcanvas-header" style="background-color: #001f4d; color: #fff;">
            <h5 class="offcanvas-title" id="offcanvasSidebarAtasanLabel">Atasan Panel</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="sidebar-header mb-3">
                <p class="mb-0 fw-semibold" style="color: #fff;"><?php echo $_SESSION['nama']; ?></p>
                <small style="color: #b0b8c1;"><?php echo $_SESSION['jabatan']; ?></small>
            </div>
            <div class="sidebar-menu">
                <a href="<?php echo baseUrl('dashboard/atasan'); ?>" class="<?php echo $this->isActive('dashboard'); ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="<?php echo baseUrl('leave/form'); ?>" class="<?php echo $this->isActive('leave/form'); ?>">
                    <i class="bi bi-plus-circle"></i> Ajukan Cuti
                </a>
                <hr class="my-3 mx-3" style="border-color: #003366;">
                <a href="<?php echo baseUrl('approval/index'); ?>" class="<?php echo $this->isActive('approval'); ?>">
                    <i class="bi bi-check-square"></i> Persetujuan Cuti
                </a>
                <a href="<?php echo baseUrl('leave/history'); ?>" class="<?php echo $this->isActive('leave/history'); ?>">
                    <i class="bi bi-clock-history"></i> Riwayat Cuti
                </a>
                <!-- <a href="<?php echo baseUrl('signature/parafAtasanCuti'); ?>" class="<?php echo $this->isActive('signature'); ?>">
                    <i class="bi bi-pen-fill"></i> Paraf Khusus Cuti
                </a>
                <a href="<?php echo baseUrl('signature'); ?>" class="<?php echo $this->isActive('signature'); ?>">
                    <i class="bi bi-pencil"></i> Tanda Tangan Digital
                </a> -->
                <hr class="my-3 mx-3" style="border-color: #003366;">
                <a href="<?php echo baseUrl('user/profile'); ?>" class="<?php echo $this->isActive('user/profile'); ?>">
                    <i class="bi bi-gear"></i> Pengaturan
                </a>
                <a href="<?php echo baseUrl('auth/logout'); ?>" class="logout-link">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Reuse same styling as admin/user sidebars */
#sidebar {
    background: #001f4d !important;
    color: #fff !important;
    border-right: 1px solid #003366 !important;
    min-height: 100vh;
    height: 100vh;
}
#sidebar *,
#sidebar .sidebar-header,
#sidebar .sidebar-menu,
#sidebar .sidebar-menu a,
#sidebar .sidebar-header h4,
#sidebar .sidebar-header small {
    background: transparent !important;
    color: #fff !important;
    border: none !important;
}
#sidebar .sidebar-menu a.active,
#sidebar .sidebar-menu a:hover {
    background: #003366 !important;
    color: #fff !important;
}
#sidebar hr {
    border-color: #003366 !important;
}

/* Mobile Offcanvas */
#offcanvasSidebarAtasan {
    background: #001f4d !important;
    color: #fff !important;
    border-right: 1px solid #003366 !important;
    min-height: 100vh;
    height: 100vh;
}
#offcanvasSidebarAtasan *,
#offcanvasSidebarAtasan .offcanvas-header,
#offcanvasSidebarAtasan .sidebar-header,
#offcanvasSidebarAtasan .sidebar-menu,
#offcanvasSidebarAtasan .sidebar-menu a,
#offcanvasSidebarAtasan .sidebar-header h4,
#offcanvasSidebarAtasan .sidebar-header small {
    background: transparent !important;
    color: #fff !important;
    border: none !important;
}
#offcanvasSidebarAtasan .sidebar-menu a.active,
#offcanvasSidebarAtasan .sidebar-menu a:hover {
    background: #003366 !important;
    color: #fff !important;
}
#offcanvasSidebarAtasan hr {
    border-color: #003366 !important;
}
</style>
