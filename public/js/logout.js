// Logout functionality with SweetAlert confirmation
$(document).ready(function() {
    // Logout confirmation function
    function confirmLogout() {
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: 'Apakah Anda yakin ingin keluar dari sistem?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Logout!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Logging out...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Redirect to logout URL
                window.location.href = baseUrl('auth/logout');
            }
        });
    }

    // Handle logout links in sidebar and navbar
    $(document).on('click', 'a[href*="auth/logout"], .logout-link', function(e) {
        e.preventDefault();
        confirmLogout();
    });
    
    // Handle logout buttons if any
    $(document).on('click', '.logout-btn, [data-action="logout"]', function(e) {
        e.preventDefault();
        confirmLogout();
    });
}); 