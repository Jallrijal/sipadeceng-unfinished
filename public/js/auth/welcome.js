/**
 * SIPADECENG - Landing Page JavaScript
 * File: public/js/auth/welcome.js
 */

$(function() {
    // Navbar Active Class on Scroll
    $(window).on('scroll', function() {
        if ($(window).scrollTop() > 150) {
            $('.navbar').addClass('active');
        } else {
            $('.navbar').removeClass('active');
        }
    });
});

// Initialize AOS Scroll Animation
AOS.init({
    easing: 'ease-out-back',
    duration: 1000,
    once: true
});
