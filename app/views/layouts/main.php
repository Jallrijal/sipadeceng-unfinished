<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($title) ? $title : 'Sipadeceng'; ?> - SIPADECENG</title>
    <link rel="icon" href="<?php echo baseUrl('public/images/sipadeceng.ico'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo baseUrl('public/css/style.css' ); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('public/css/mobile-responsive.css' ); ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- BARIS INI DIPINDAHKAN -->
    <?php if (isset($extra_css )): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?> 
    <link rel="stylesheet" href="<?php echo baseUrl('public/css/darkmode.css'); ?>">
    <!-- Dark mode DataTables override — harus setelah CDN DataTables CSS -->
    <style id="darkmode-dt-head">
        body.dark-mode table.dataTable tbody > tr > td,
        body.dark-mode table.dataTable tbody > tr > th {
            background-color: #161b22 !important;
            color: #c9d1d9 !important;
            border-color: #21262d !important;
        }
        body.dark-mode table.dataTable thead > tr > th,
        body.dark-mode table.dataTable thead > tr > td {
            background-color: #1c2128 !important;
            color: #e1e4e8 !important;
            border-color: #30363d !important;
        }
        body.dark-mode table.dataTable tbody > tr:hover > td,
        body.dark-mode table.dataTable tbody > tr:hover > th {
            background-color: #1c2128 !important;
            color: #c9d1d9 !important;
        }
        body.dark-mode table.dataTable.table-striped > tbody > tr:nth-child(odd) > td,
        body.dark-mode table.dataTable.table-striped > tbody > tr:nth-child(odd) > th {
            background-color: rgba(255,255,255,0.04) !important;
            color: #c9d1d9 !important;
        }
        body.dark-mode .dataTables_wrapper,
        body.dark-mode .dataTables_wrapper label,
        body.dark-mode .dataTables_wrapper .dataTables_length label,
        body.dark-mode .dataTables_wrapper .dataTables_filter label,
        body.dark-mode .dataTables_wrapper .dataTables_info {
            color: #c9d1d9 !important;
        }
        body.dark-mode .dataTables_wrapper .dataTables_filter input,
        body.dark-mode .dataTables_wrapper .dataTables_length select {
            background-color: #0d1117 !important;
            border-color: #30363d !important;
            color: #c9d1d9 !important;
        }
        body.dark-mode .dataTables_wrapper .dataTables_paginate .page-link {
            background-color: #161b22 !important;
            border-color: #30363d !important;
            color: #c9d1d9 !important;
        }
        body.dark-mode .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
            background-color: #58a6ff !important;
            border-color: #58a6ff !important;
            color: #fff !important;
        }
        body.dark-mode .dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link {
            background-color: #1c2128 !important;
            border-color: #30363d !important;
            color: #484f58 !important;
        }
        /* Bootstrap 5 table CSS variables */
        body.dark-mode .table {
            --bs-table-color: #c9d1d9;
            --bs-table-bg: #161b22;
            --bs-table-border-color: #21262d;
            --bs-table-hover-bg: #1c2128;
            --bs-table-hover-color: #c9d1d9;
            --bs-table-striped-bg: rgba(255,255,255,0.04);
            --bs-table-striped-color: #c9d1d9;
            color: #c9d1d9 !important;
        }
        body.dark-mode .table > :not(caption) > * > * {
            background-color: #161b22 !important;
            color: #c9d1d9 !important;
            border-color: #21262d !important;
        }
        body.dark-mode .table > thead > * > * {
            background-color: #1c2128 !important;
            color: #e1e4e8 !important;
        }
    </style>
</head>
<body>
    <?php 
    if (isLoggedIn()) {
        if (function_exists('isAtasan') && isAtasan()) {
            $this->partial('layouts/sidebar_atasan');
        } elseif (function_exists('isPimpinan') && isPimpinan()) {
            $this->partial('layouts/sidebar_admin');
        } else {
            $this->partial('layouts/sidebar_user');
        }
    }
    ?>
    
    <div class="main-content">
        <?php 
        if (isLoggedIn()) {
            $this->partial('layouts/navbar', ['page_title' => $page_title ?? 'Dashboard']);
        }
        ?>
        
        <div class="page-content">
            <?php echo $content; ?>
        </div>
    </div>

    <!-- Session timeout warning modal -->
    <?php if (isLoggedIn()): ?>
    <div class="modal fade" id="sessionTimeoutModal" tabindex="-1" aria-labelledby="sessionTimeoutModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="sessionTimeoutModalLabel">Peringatan: Sesi Akan Berakhir</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Anda tidak melakukan aktivitas. Sesi akan otomatis logout dalam <strong><span id="session-countdown">60</span></strong> detik.</p>
            <p class="small text-muted">Jika ingin tetap masuk, tekan <strong>Tetap Login</strong>.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="logoutNowBtn">Logout Sekarang</button>
            <button type="button" class="btn btn-primary" id="keepAliveBtn">Tetap Login</button>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Base URL helper for JavaScript
        const BASE_URL = '<?php echo baseUrl( ); ?>';
        
        function baseUrl(url = '') {
            return BASE_URL + url;
        }
    </script>
    <script src="<?php echo baseUrl('public/js/main.js'); ?>"></script>
    <script src="<?php echo baseUrl('public/js/logout.js'); ?>"></script>
    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    <script src="<?php echo baseUrl('public/js/darkmode.js'); ?>"></script>
</body>
</html>
