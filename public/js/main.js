// Main JavaScript file for Sistem Cuti

// Global notification handler
function loadNotifications() {
    $.post(baseUrl('user/getNotifications'), function (response) {
        if (response.success) {
            const count = response.notifications.length;
            if (count > 0) {
                $('#notifCount').text(count).show();

                const notifHtml = response.notifications.map(notif => `
                    <div class="notification-item" data-id="${notif.id}">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">${notif.created_at}</small>
                            <span class="badge bg-${notif.type}">${notif.type}</span>
                        </div>
                        <p class="mb-0 small">${notif.message}</p>
                    </div>
                `).join('');

                $('#notificationList').html(notifHtml);
            } else {
                $('#notifCount').hide();
                $('#notificationList').html('<div class="text-center p-3 text-muted"><small>Tidak ada notifikasi baru</small></div>');
            }
        }
    }, 'json');
}

// Mark notifications as read
$(document).on('show.bs.dropdown', '#notificationDropdown', function () {
    $.post(baseUrl('user/markAllNotificationsRead'), function (response) {
        if (response.success) {
            $('#notifCount').hide();
        }
    }, 'json');
});

// Initialize on document ready
$(document).ready(function () {
    // Load notifications if logged in
    if ($('#notificationDropdown').length > 0) {
        loadNotifications();

        // Refresh notifications every 30 seconds
        setInterval(loadNotifications, 30000);
    }

    // Custom sidebar toggle with proper backdrop handling
    $('#sidebarToggle').off('click.sidebar').off('click');

    $(document).on('click.sidebar', '#sidebarToggle', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $button = $(this);
        var target = $button.data('bs-target');
        var isOpen = $button.data('sidebar-open') || false;

        if (target) {
            var offcanvasEl = document.querySelector(target);
            if (offcanvasEl) {
                var bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                if (!bsOffcanvas) {
                    bsOffcanvas = new bootstrap.Offcanvas(offcanvasEl, {
                        backdrop: false,
                        scroll: false
                    });
                }

                if (isOpen) {
                    bsOffcanvas.hide();
                } else {
                    bsOffcanvas.show();
                }
            }
        }
    });

    // Custom backdrop management
    $(document).on('shown.bs.offcanvas', '.offcanvas', function () {
        $('#sidebarToggle').data('sidebar-open', true);
        // Hide navbar on mobile
        if (window.innerWidth < 768) {
            $('.top-navbar').addClass('d-none');
        }

        // Add custom backdrop
        if (!$('.custom-backdrop').length) {
            $('body').append('<div class="custom-backdrop fade show" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.5); z-index: 1040;"></div>');
        }
    });

    $(document).on('hidden.bs.offcanvas', '.offcanvas', function () {
        $('#sidebarToggle').data('sidebar-open', false);
        // Show navbar
        $('.top-navbar').removeClass('d-none');

        // Remove custom backdrop
        $('.custom-backdrop').remove();
    });

    // Close offcanvas when clicking custom backdrop
    $(document).on('click', '.custom-backdrop', function () {
        $('.offcanvas.show').each(function () {
            var bsOffcanvas = bootstrap.Offcanvas.getInstance(this);
            if (bsOffcanvas) {
                bsOffcanvas.hide();
            }
        });
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });


    // ---------------- Session timeout / auto logout ---------------- 🔒
    // Active when layout includes #sessionTimeoutModal
    if ($('#sessionTimeoutModal').length > 0) {
        var sessionRemaining = null; // seconds
        var sessionWarning = 60; // default
        var sessionTimeout = 20 * 60; // default
        var keepaliveThrottle = 30; // default seconds
        var lastPing = 0;
        var countdownInterval = null;
        var modalEl = document.getElementById('sessionTimeoutModal');
        var bootstrapModal = modalEl ? new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false }) : null;

        function handleSessionExpired() {
            // Close modal if open and redirect to logout which will destroy server session
            if (bootstrapModal) {
                try { bootstrapModal.hide(); } catch (e) { }
            }
            // Force logout and go to login with expired flag
            window.location.href = baseUrl('auth/logout?expired=1');
        }

        function showWarning() {
            if (bootstrapModal) {
                $('#session-countdown').text(sessionRemaining);
                bootstrapModal.show();
            }
        }

        function hideWarning() {
            if (bootstrapModal) {
                bootstrapModal.hide();
            }
        }

        function updateCountdownDisplay() {
            if ($('#session-countdown').length) {
                $('#session-countdown').text(sessionRemaining);
            }
        }

        function startTimer() {
            if (countdownInterval) clearInterval(countdownInterval);
            countdownInterval = setInterval(function () {
                sessionRemaining = Math.max(0, sessionRemaining - 1);
                updateCountdownDisplay();

                if (sessionRemaining <= 0) {
                    clearInterval(countdownInterval);
                    handleSessionExpired();
                } else if (sessionRemaining <= sessionWarning) {
                    showWarning();
                }
            }, 1000);
        }

        function fetchSessionStatus() {
            $.get(baseUrl('auth/sessionStatus'), function (resp) {
                if (resp && resp.success) {
                    sessionRemaining = resp.remaining;
                    sessionWarning = resp.warning || sessionWarning;
                    sessionTimeout = resp.timeout || sessionTimeout;
                    keepaliveThrottle = resp.throttle || keepaliveThrottle;
                    startTimer();
                } else {
                    // If not logged in or session already expired
                    handleSessionExpired();
                }
            }, 'json').fail(function (xhr) {
                console.error('sessionStatus failed', xhr);
            });
        }

        function sendKeepalive() {
            lastPing = Math.floor(Date.now() / 1000);
            $.post(baseUrl('auth/keepalive'), {}, function (resp) {
                if (resp && resp.success) {
                    // Reset remaining from server
                    sessionRemaining = resp.remaining || sessionTimeout;
                    updateCountdownDisplay();
                } else {
                    // Could be expired
                    handleSessionExpired();
                }
            }, 'json').fail(function (xhr) {
                console.error('keepalive failed', xhr);
            });
        }

        // Throttled activity handler
        var activityTimer = null;
        function onUserActivity() {
            // Update local remaining
            sessionRemaining = sessionTimeout;
            updateCountdownDisplay();

            // Throttle keepalive calls to at most once every keepaliveThrottle seconds
            var now = Math.floor(Date.now() / 1000);
            if (now - lastPing > (keepaliveThrottle || 30)) {
                sendKeepalive();
            }
        }

        // Bind user activity events
        ['click', 'scroll', 'keydown', 'mousemove', 'touchstart'].forEach(function (evt) {
            document.addEventListener(evt, function () {
                if (activityTimer) clearTimeout(activityTimer);
                // Debounce quick repeated events
                activityTimer = setTimeout(onUserActivity, 250);
            }, { passive: true });
        });

        // Handle modal buttons
        $(document).on('click', '#keepAliveBtn', function (e) {
            e.preventDefault();
            sendKeepalive();
            hideWarning();
        });

        $(document).on('click', '#logoutNowBtn', function (e) {
            e.preventDefault();
            // Use existing logout flow
            window.location.href = baseUrl('auth/logout');
        });

        // Intercept AJAX completions to check for session_expired response
        $(document).ajaxComplete(function (event, xhr) {
            // If server returned JSON with message session_expired or success false with message
            var json = null;
            try { json = xhr.responseJSON || (xhr.responseText ? JSON.parse(xhr.responseText) : null); } catch (e) { json = null; }
            if (json) {
                if (json.message && json.message === 'session_expired') {
                    handleSessionExpired();
                }
                // If server provided remaining time, use it
                if (json.remaining !== undefined) {
                    sessionRemaining = json.remaining;
                }
            }
        });

        // Initial fetch to sync timer with server
        fetchSessionStatus();
    }
});

// Helper function for AJAX error handling
function handleAjaxError(xhr, status, error) {
    console.error('AJAX Error:', status, error);
    console.error('Response:', xhr.responseText);

    let errorMessage = 'Terjadi kesalahan sistem. Silakan coba lagi.';

    // Check if response is HTML (likely PHP error)
    if (xhr.responseText && (xhr.responseText.includes('<!DOCTYPE') || xhr.responseText.includes('<br />') || xhr.responseText.includes('<b>'))) {
        console.error('PHP Error detected in response');
        errorMessage = 'Terjadi kesalahan pada server. Silakan periksa console untuk detail.';
    } else {
        // Try to parse JSON response
        try {
            const response = JSON.parse(xhr.responseText);
            if (response.message) {
                errorMessage = response.message;
            }
        } catch (e) {
            // If JSON parsing fails, use default message
            console.error('Failed to parse JSON response:', e);
        }
    }

    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: errorMessage,
        confirmButtonColor: '#1b5e20'
    });
}

// Setup AJAX defaults
$.ajaxSetup({
    error: handleAjaxError
});

// Mobile sidebar overlay and close functionality
$(document).ready(function () {
    // Create overlay if not exists
    if ($('#sidebarOverlay').length === 0) {
        $('body').append('<div class="sidebar-overlay" id="sidebarOverlay"></div>');
    }

    // Close sidebar when clicking overlay
    $('#sidebarOverlay').off('click').on('click', function () {
        $('#sidebar').removeClass('active');
        $(this).removeClass('active');
        $('body').removeClass('sidebar-open');
    });

    // Close sidebar on window resize if open
    $(window).off('resize.sidebar').on('resize.sidebar', function () {
        if ($(window).width() > 991) {
            $('#sidebar').removeClass('active');
            $('#sidebarOverlay').removeClass('active');
            $('body').removeClass('sidebar-open');
        }
    });
});

// Prevent body scroll when sidebar is open on mobile
$(document).on('DOMContentLoaded', function () {
    const style = document.createElement('style');
    style.textContent = `
        body.sidebar-open {
            overflow: hidden;
            position: fixed;
            width: 100%;
        }
    `;
    document.head.appendChild(style);

    // Ensure sidebar toggle works after DOM is ready
    setTimeout(function () {
        // Remove any existing event handlers
        $(document).off('click.sidebar');

        // Add event handler with namespace using delegation
        $(document).on('click.sidebar', '#sidebarToggle', function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            console.log('Sidebar toggle clicked (DOM ready)');

            if (window.innerWidth <= 768) {
                // Custom mobile sidebar fallback
                $('#sidebar').toggleClass('active');
                $('#sidebarOverlay').toggleClass('active');
                $('body').toggleClass('sidebar-open');
                console.log('Mobile sidebar toggled (Custom)');
            } else {
                // Use Bootstrap Offcanvas for 769px and above (up to 1200px where d-xl-none hides button)
                var target = $(this).data('bs-target');
                if (target) {
                    var offcanvasEl = document.querySelector(target);
                    if (offcanvasEl) {
                        var bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
                        bsOffcanvas.toggle();
                        console.log('Sidebar toggled (Offcanvas)');
                    }
                }
            }

            return false;
        });
    }, 100);
});

