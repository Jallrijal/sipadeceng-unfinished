/**
 * darkmode.js
 * ===========
 * Script untuk toggle dark mode.
 * Dipanggil di main.php sebelum </body>
 */

(function () {
    'use strict';

    var toggle = document.getElementById('darkModeToggle');
    var icon   = document.getElementById('darkModeIcon');

    // ── Injected <style> untuk override DataTables + Bootstrap warna teks ──
    var DARK_STYLE_ID = 'darkmode-datatable-override';

    var DARK_CSS = [
        /* Semua td/th di dalam tabel DataTables */
        'body.dark-mode table.dataTable tbody tr td,',
        'body.dark-mode table.dataTable tbody tr th {',
        '  background-color: #161b22 !important;',
        '  color: #c9d1d9 !important;',
        '  border-color: #21262d !important;',
        '}',

        'body.dark-mode table.dataTable thead > tr > th,',
        'body.dark-mode table.dataTable thead > tr > td {',
        '  background-color: #1c2128 !important;',
        '  color: #e1e4e8 !important;',
        '  border-color: #30363d !important;',
        '}',

        /* Hover row */
        'body.dark-mode table.dataTable tbody tr:hover > td,',
        'body.dark-mode table.dataTable tbody tr:hover > th {',
        '  background-color: #1c2128 !important;',
        '  color: #c9d1d9 !important;',
        '}',

        /* Striped odd rows */
        'body.dark-mode table.dataTable.table-striped > tbody > tr:nth-of-type(odd) > td,',
        'body.dark-mode table.dataTable.table-striped > tbody > tr:nth-of-type(odd) > th {',
        '  background-color: rgba(255,255,255,0.04) !important;',
        '  color: #c9d1d9 !important;',
        '}',

        /* DataTables wrapper teks (label "Tampilkan", "Cari:", info, dsb.) */
        'body.dark-mode .dataTables_wrapper,',
        'body.dark-mode .dataTables_wrapper label,',
        'body.dark-mode .dataTables_wrapper .dataTables_length label,',
        'body.dark-mode .dataTables_wrapper .dataTables_filter label,',
        'body.dark-mode .dataTables_wrapper .dataTables_info {',
        '  color: #c9d1d9 !important;',
        '}',

        /* Input cari & select length */
        'body.dark-mode .dataTables_wrapper .dataTables_filter input,',
        'body.dark-mode .dataTables_wrapper .dataTables_length select {',
        '  background-color: #0d1117 !important;',
        '  border-color: #30363d !important;',
        '  color: #c9d1d9 !important;',
        '}',

        /* Pagination links */
        'body.dark-mode .dataTables_wrapper .dataTables_paginate .page-link {',
        '  background-color: #161b22 !important;',
        '  border-color: #30363d !important;',
        '  color: #c9d1d9 !important;',
        '}',
        'body.dark-mode .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {',
        '  background-color: #58a6ff !important;',
        '  border-color: #58a6ff !important;',
        '  color: #fff !important;',
        '}',
        'body.dark-mode .dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link {',
        '  background-color: #1c2128 !important;',
        '  border-color: #30363d !important;',
        '  color: #484f58 !important;',
        '}',

        /* Bootstrap 5 table variables override */
        'body.dark-mode .table {',
        '  --bs-table-color: #c9d1d9 !important;',
        '  --bs-table-bg: #161b22 !important;',
        '  --bs-table-border-color: #21262d !important;',
        '  --bs-table-hover-bg: #1c2128 !important;',
        '  --bs-table-hover-color: #c9d1d9 !important;',
        '  --bs-table-striped-bg: rgba(255,255,255,0.04) !important;',
        '  --bs-table-striped-color: #c9d1d9 !important;',
        '  color: #c9d1d9 !important;',
        '}',

        /* td/th umum (bukan hanya dataTable) */
        'body.dark-mode .table td,',
        'body.dark-mode .table th {',
        '  color: #c9d1d9 !important;',
        '}',
        'body.dark-mode .table thead td,',
        'body.dark-mode .table thead th {',
        '  background-color: #1c2128 !important;',
        '  color: #e1e4e8 !important;',
        '}',
        'body.dark-mode .table tbody td,',
        'body.dark-mode .table tbody th {',
        '  background-color: #161b22 !important;',
        '  color: #c9d1d9 !important;',
        '}',
    ].join('\n');

    function injectDarkStyles() {
        if (!document.getElementById(DARK_STYLE_ID)) {
            var s = document.createElement('style');
            s.id   = DARK_STYLE_ID;
            s.type = 'text/css';
            s.textContent = DARK_CSS;
            document.head.appendChild(s);
        }
    }

    function removeDarkStyles() {
        var s = document.getElementById(DARK_STYLE_ID);
        if (s) { s.parentNode.removeChild(s); }
    }

    // Fungsi untuk apply dark mode
    function applyDarkMode(isDark) {
        if (isDark) {
            document.body.classList.add('dark-mode');
            injectDarkStyles();
            if (icon) {
                icon.classList.remove('bi-moon-fill');
                icon.classList.add('bi-sun-fill');
            }
            if (toggle) {
                toggle.classList.remove('text-dark');
                toggle.classList.add('text-light');
            }
        } else {
            document.body.classList.remove('dark-mode');
            removeDarkStyles();
            if (icon) {
                icon.classList.remove('bi-sun-fill');
                icon.classList.add('bi-moon-fill');
            }
            if (toggle) {
                toggle.classList.remove('text-light');
                toggle.classList.add('text-dark');
            }
        }
    }

    // Cek preferensi tersimpan saat load
    var savedMode = localStorage.getItem('darkMode');
    if (savedMode === 'true') {
        applyDarkMode(true);
    }

    // Event listener untuk tombol toggle
    if (toggle) {
        toggle.addEventListener('click', function () {
            var isDark = !document.body.classList.contains('dark-mode');
            applyDarkMode(isDark);
            localStorage.setItem('darkMode', isDark.toString());
        });
    }
})();
