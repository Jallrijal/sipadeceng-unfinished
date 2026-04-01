/**
 * SIPADECENG - Landing Page JavaScript
 * File: public/js/auth/welcome.js
 */

$(function() {
    // Navbar Active Class on Scroll
    $(window).on('scroll', function() {
        var scrollPos = $(window).scrollTop();
        
        // Atur Navbar styling (shadow/active)
        if (scrollPos > 150) {
            $('.navbar').addClass('active');
        } else {
            $('.navbar').removeClass('active');
        }
        
        // Custom ScrollSpy: Update active class pada nav-link saat di scroll
        var offset = 200; // toleransi scroll spy
        $('.nav-link').each(function() {
            var currLink = $(this);
            var refElementId = currLink.attr("href");
            
            // Validasi link untuk href yang berupa anchor (#)
            if (refElementId && refElementId.startsWith("#") && refElementId.length > 1) {
                var refElement = $(refElementId);
                // Pastikan target elemen ada di halaman
                if (refElement.length) {
                    var elementTop = refElement.offset().top - offset;
                    var elementBottom = elementTop + refElement.outerHeight() + 50;
                    
                    if (scrollPos >= elementTop && scrollPos < elementBottom) {
                        $('.nav-link').removeClass("active");
                        currLink.addClass("active");
                    }
                }
            }
        });
    });

    // Handle Nav-Link Clicks (Scroll to target & close mobile menu)
    $('.nav-link').on('click', function() {
        // Abaikan jika tombol login
        if (!$(this).hasClass('nav-login')) {
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
        }
        
        // Tutup menu collapse d mobile jika sedang terbuka
        var navbarCollapse = $('.navbar-collapse');
        if (navbarCollapse.hasClass('show')) {
            navbarCollapse.collapse('hide');
        }
    });
});

// Initialize AOS Scroll Animation
AOS.init({
    easing: 'ease-out-back',
    duration: 1000,
    once: false
});

// Interactive Hubungi Section Accordion Logic
$(function() {
    $('.hubungi-item').on('click', function() {
        if (!$(this).hasClass('active')) {
            // Remove active from all items
            $('.hubungi-item').removeClass('active');
            // Add active to clicked item
            $(this).addClass('active');
        }
    });
});
