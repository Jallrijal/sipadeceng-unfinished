<?php
/**
 * SIPADECENG - Landing Page Entry Point
 *
 * File ini adalah entry point yang di-render oleh AuthController::welcome().
 * Seluruh konten halaman dipecah menjadi partial terpisah di folder landingpage/.
 *
 * Struktur:
 *   landingpage/index.php     → Master template (head + includes + scripts)
 *   landingpage/_navbar.php   → Navigasi bar floating pill
 *   landingpage/_hero.php     → Hero / Beranda section
 *   landingpage/_tentang.php  → Tentang SIPADECENG (4 feature cards)
 *   landingpage/_alur.php     → Alur Pengajuan Cuti (4 step cards)
 *   landingpage/_pengguna.php → Siapa saja yang menggunakan SIPADECENG
 *   landingpage/_faq.php      → FAQ accordion modern
 *   landingpage/_hubungi.php  → Hubungi Kami + Google Maps
 *   landingpage/_footer.php   → Footer Top + Footer Bottom
 *   landingpage/_whatsapp.php → Tombol WhatsApp floating
 *
 * CSS : public/css/auth/welcome.css
 * JS  : public/js/auth/welcome.js
 */
require_once __DIR__ . '/landingpage/index.php';