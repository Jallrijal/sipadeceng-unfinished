<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="icon" href="<?php echo baseUrl('public/images/sipadeceng.ico'); ?>">
	<title>SIPADECENG - Sistem Pengelolaan Cuti Elektronik PTA Makassar</title>
	<meta property="og:image" content="<?php echo baseUrl('public/images/sipadeceng.png'); ?>">
	<meta property="og:image:width" content="400">
	<meta property="og:image:height" content="240">
	<meta property="og:description" content="SIPADECENG - Sistem Pengelolaan Administrasi Cuti Elektronik Pengadilan Tinggi Agama Makassar">
	<meta property="og:url" content="<?php echo baseUrl(''); ?>">

	<!-- BOOTSTRAP 5 -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<!-- GOOGLE FONT -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<!-- ++ Poppins -->
	<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
	<!-- ++ Roboto -->
	<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
	<!-- ++ Inter -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
	<!-- STYLE AOS SCROLL ANIMATION -->
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.2.0/aos.css">

	<style>
		html {
			scroll-behavior: smooth;
			scroll-padding-top: 130px; /* Offset for fixed navbar */
		}
		* {
			font-family: 'Poppins', 'Roboto', 'Inter', sans-serif;
		}
		body {
			background: #f4f6fa;
			margin: 0;
			padding: 0;
		}
		/* NAVBAR NEW MODERN DESIGN */
		/* NAVBAR NEW COOL FLOATING UI */
		.navbar {
			background: transparent !important;
			padding: 24px 0;
			z-index: 1030;
			border: none;
		}
		
		.navbar .container {
			background: rgba(255, 255, 255, 0.92);
			backdrop-filter: blur(20px);
			-webkit-backdrop-filter: blur(20px);
			border: 1px solid rgba(255, 255, 255, 0.6);
			box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05), 0 5px 15px rgba(0, 0, 0, 0.03);
			border-radius: 100px; /* Pill shape */
			padding: 8px 24px;
		}

		/* No scroll animations */
		.navbar.active {
			background: transparent !important;
			padding: 24px 0;
			box-shadow: none;
			border: none;
		}

		.navbar-brand {
			font-weight: 800;
			font-size: 1.25rem;
			display: flex;
			align-items: center;
			letter-spacing: -0.5px;
		}

		.navbar-brand img {
			height: 38px;
			margin-right: 12px;
		}

		.nav-link {
			font-weight: 600;
			font-size: 0.95rem;
			color: #64748b !important;
			margin: 0 14px;
			transition: color 0.2s ease;
			position: relative;
		}

		.nav-link:hover, .nav-link.active {
			color: #0f172a !important; /* Cool dark tone */
		}

		.nav-link.active::after {
			content: '';
			position: absolute;
			bottom: -4px;
			left: 50%;
			transform: translateX(-50%);
			width: 6px;
			height: 6px;
			background-color: #0f172a;
			border-radius: 50%;
		}

		.dropdown-menu {
			background: #ffffff;
			border: none;
			border-radius: 16px;
			box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0,0,0,0.03);
			padding: 10px 0;
			margin-top: 20px;
		}

		.dropdown-item {
			padding: 10px 24px;
			font-weight: 500;
			font-size: 0.95rem;
			color: #475569;
			transition: background-color 0.2s ease, color 0.2s ease;
		}

		.dropdown-item:hover {
			background: #f8fafc;
			color: #0f172a;
		}

		.nav-login {
			background: #0f172a; /* Sleek dark button */
			color: #fff !important;
			border-radius: 100px;
			padding: 10px 28px !important;
			font-weight: 600;
			margin-left: 15px;
			transition: background-color 0.2s ease;
			box-shadow: 0 4px 15px rgba(15, 23, 42, 0.2);
			display: inline-block;
		}

		.nav-login:hover {
			background: #1e293b !important;
		}

		/* Mobile adjustments for the pill shape */
		@media (max-width: 991.98px) {
			.navbar .container {
				border-radius: 20px;
				padding: 12px 20px;
				position: relative;
			}
			.navbar-collapse {
				background: rgba(255, 255, 255, 0.98);
				border-radius: 16px;
				padding: 20px;
				margin-top: 15px;
				box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
				position: absolute;
				top: 100%;
				left: 0;
				right: 0;
				z-index: 1050;
			}
			.nav-login {
				margin-left: 0;
				margin-top: 10px;
				width: 100%;
				text-align: center;
			}
		}

		/* Enhanced Top Beranda Section (Hero) */
		.top-beranda {
			position: relative;
			background-color: #ffffff;
			background-image: radial-gradient(circle at top right, rgba(37, 99, 235, 0.05) 0%, transparent 40%),
							  radial-gradient(circle at bottom left, rgba(37, 99, 235, 0.03) 0%, transparent 40%);
			padding: 160px 0 80px;
			min-height: 100vh;
			display: flex;
			align-items: center;
			overflow: hidden;
		}
		.hero-glow-1 {
			position: absolute;
			top: -100px;
			right: -100px;
			width: 400px;
			height: 400px;
			background: radial-gradient(circle, rgba(37,99,235,0.1) 0%, rgba(255,255,255,0) 70%);
			border-radius: 50%;
			z-index: 0;
		}
		.hero-content-wrapper {
			position: relative;
			z-index: 2;
		}
		.title-top-beranda-1 {
			font-size: 3.2rem;
			font-weight: 800;
			color: #0f172a;
			line-height: 1.1;
			margin-bottom: 5px;
			letter-spacing: -1px;
		}
		.title-top-beranda-2 {
			font-size: 3.2rem;
			font-weight: 800;
			background: linear-gradient(135deg, #2563eb, #3b82f6, #06b6d4);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			margin-bottom: 25px;
			letter-spacing: -1px;
		}
		.top-beranda .text-justify {
			font-size: 1.15rem;
			color: #475569;
			line-height: 1.7;
			margin-bottom: 40px;
			max-width: 95%;
		}
		.tombol-top-beranda {
			display: inline-flex;
			align-items: center;
			background: #2563eb;
			color: #ffffff;
			padding: 15px 40px;
			border-radius: 100px;
			text-decoration: none;
			font-weight: 600;
			font-size: 1.1rem;
			transition: all 0.3s ease;
			box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
		}
		.tombol-top-beranda:hover {
			background: #1d4ed8;
			color: #ffffff;
			transform: translateY(-3px);
			box-shadow: 0 15px 35px rgba(37, 99, 235, 0.4);
		}
		.tombol-top-beranda i {
			margin-right: 10px;
			font-size: 1.2rem;
		}
		.hero-img-wrapper {
			position: relative;
			z-index: 2;
		}
		.hero-img-wrapper img {
			filter: drop-shadow(0 20px 40px rgba(0,0,0,0.08));
			transform: scale(1.05);
		}

		/* New Tentang Section */
		.section-tentang {
			padding: 100px 0;
			background-color: #ffffff;
			position: relative;
			border-top: 1px solid rgba(0,0,0,0.03);
			border-bottom: 1px solid rgba(0,0,0,0.03);
		}
		.tentang-badge {
			display: inline-block;
			background: rgba(37, 99, 235, 0.1);
			color: #2563eb;
			padding: 8px 16px;
			border-radius: 50px;
			font-weight: 700;
			font-size: 0.85rem;
			letter-spacing: 1px;
			margin-bottom: 20px;
			text-transform: uppercase;
		}
		.tentang-title {
			font-size: 2.5rem;
			font-weight: 800;
			color: #0f172a;
			margin-bottom: 25px;
			letter-spacing: -0.5px;
			line-height: 1.3;
		}
		.tentang-desc {
			font-size: 1.1rem;
			color: #475569;
			line-height: 1.8;
			margin-bottom: 30px;
		}
		.tentang-card {
			background: #f8fafc;
			border-radius: 20px;
			padding: 25px;
			border: 1px solid rgba(0,0,0,0.03);
			height: 100%;
			transition: all 0.3s ease;
		}
		.tentang-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 15px 30px rgba(0,0,0,0.05);
		}
		.tentang-icon {
			width: 50px;
			height: 50px;
			background: #ffffff;
			border-radius: 14px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 1.5rem;
			color: #2563eb;
			box-shadow: 0 10px 20px rgba(0,0,0,0.04);
			margin-bottom: 20px;
		}
		.tentang-card-title {
			font-weight: 700;
			font-size: 1.15rem;
			color: #0f172a;
			margin-bottom: 10px;
		}
		.tentang-card-desc {
			color: #64748b;
			line-height: 1.6;
			font-size: 0.95rem;
		}

		/* Alur Pengajuan Modern */
		.section-alur {
			padding: 100px 0;
			background-color: #f8fafc;
			position: relative;
		}
		.title-alur-pengajuan {
			font-size: 2.5rem;
			font-weight: 800;
			color: #0f172a;
			text-align: center;
			margin-bottom: 15px;
			letter-spacing: -0.5px;
		}
		.subtitle-alur {
			text-align: center;
			color: #64748b;
			font-size: 1.1rem;
			margin-bottom: 60px;
		}
		.alur-card {
			background: #ffffff;
			border-radius: 20px;
			padding: 40px 25px;
			height: 100%;
			border: 1px solid rgba(0,0,0,0.03);
			transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
			position: relative;
			z-index: 1;
			text-align: center;
			/* Box shadow modern */
			box-shadow: 0 10px 30px rgba(0,0,0,0.02);
		}
		.alur-card:hover {
			box-shadow: 0 20px 40px rgba(37, 99, 235, 0.08); 
			border-color: rgba(37, 99, 235, 0.1);
		}
		.alur-step-number {
			position: absolute;
			top: -20px;
			left: 50%;
			transform: translateX(-50%);
			width: 40px;
			height: 40px;
			background: linear-gradient(135deg, #2563eb, #3b82f6);
			color: white;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: 700;
			font-size: 1.1rem;
			border: 4px solid #f8fafc;
			box-shadow: 0 5px 15px rgba(37,99,235,0.2);
			z-index: 2;
		}
		.alur-icon {
			width: 80px;
			height: 80px;
			background: rgba(37, 99, 235, 0.05);
			color: #2563eb;
			border-radius: 24px;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			font-size: 2.2rem;
			margin-bottom: 25px;
			transition: all 0.3s ease;
		}
		.alur-card:hover .alur-icon {
			background: #2563eb;
			color: #ffffff;
		}
		.alur-card:hover .alur-icon i {
			animation: iconHoverAnim 1s ease infinite;
		}
		.alur-title {
			font-weight: 700;
			font-size: 1.25rem;
			color: #0f172a;
			margin-bottom: 12px;
		}
		.alur-deskripsi {
			font-size: 0.95rem;
			color: #64748b;
			line-height: 1.6;
		}

		/* Animasi Alur Hover */
		@keyframes iconHoverAnim {
			0%, 100% { transform: translateY(0); }
			50% { transform: translateY(-8px); }
		}

		.tombol-download-panduan {
			display: inline-flex;
			align-items: center;
			background: rgba(37, 99, 235, 0.1);
			color: #2563eb;
			padding: 15px 40px;
			border-radius: 100px;
			text-decoration: none;
			font-weight: 600;
			font-size: 1.1rem;
			transition: all 0.3s ease;
		}
		.tombol-download-panduan:hover {
			background: rgba(37, 99, 235, 0.2);
			color: #1d4ed8;
			transform: translateY(-3px);
		}
		.tombol-download-panduan i {
			margin-right: 10px;
			font-size: 1.2rem;
		}

		/* Siapa Saja Section Modern */
		.section-siapa {
			padding: 100px 0;
			background-color: #ffffff;
			overflow: hidden;
		}
		.siapa-main-title {
			font-size: 3rem;
			font-weight: 800;
			color: #0f172a;
			line-height: 1.2;
			letter-spacing: -1px;
		}
		.siapa-desc {
			font-size: 1.1rem;
			color: #64748b;
			line-height: 1.8;
			max-width: 90%;
		}
		.siapa-card-horizontal {
			display: flex;
			align-items: flex-start;
			background: #f8fafc;
			border: 1px solid rgba(0,0,0,0.03);
			border-radius: 20px;
			padding: 30px;
			transition: all 0.3s ease;
			box-shadow: 0 4px 15px rgba(0,0,0,0.02);
			position: relative;
			overflow: hidden;
		}
		.siapa-card-horizontal:hover {
			transform: translateX(10px);
			box-shadow: 0 15px 30px rgba(37, 99, 235, 0.08);
			background: #ffffff;
			border-color: rgba(37,99,235,0.1);
		}
		.siapa-card-horizontal::before {
			content: '';
			position: absolute;
			left: 0;
			top: 0;
			bottom: 0;
			width: 4px;
			background: #2563eb;
			opacity: 0;
			transition: opacity 0.3s ease;
		}
		.siapa-card-horizontal:hover::before {
			opacity: 1;
		}
		.siapa-card-horizontal .icon-box {
			flex-shrink: 0;
			width: 65px;
			height: 65px;
			background: rgba(37, 99, 235, 0.08);
			color: #2563eb;
			border-radius: 16px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 1.8rem;
			margin-right: 25px;
			transition: all 0.3s ease;
		}
		.siapa-card-horizontal:hover .icon-box {
			background: #2563eb;
			color: #ffffff;
			transform: scale(1.05) rotate(5deg);
		}
		.siapa-card-horizontal .text-box h4 {
			font-weight: 700;
			font-size: 1.25rem;
			color: #0f172a;
			margin-bottom: 8px;
			margin-top: 5px;
		}
		.siapa-card-horizontal .text-box p {
			margin: 0;
			color: #64748b;
			font-size: 0.95rem;
			line-height: 1.6;
		}

		/* Hubungi Kami Modern */
		.section-hubungi {
			padding: 100px 0;
			background-color: #ffffff;
			position: relative;
		}
		.hubungi-badge {
			display: inline-block;
			background: rgba(37, 99, 235, 0.1);
			color: #2563eb;
			padding: 8px 16px;
			border-radius: 50px;
			font-weight: 700;
			font-size: 0.85rem;
			letter-spacing: 1px;
			text-transform: uppercase;
			text-align: center;
			margin: 0 auto 20px auto;
			display: block;
			width: max-content;
		}
		.hubungi-main-title {
			font-size: 2.5rem;
			font-weight: 800;
			color: #0f172a;
			text-align: center;
			margin-bottom: 15px;
			letter-spacing: -0.5px;
		}
		.hubungi-subtitle {
			text-align: center;
			color: #64748b;
			font-size: 1.1rem;
			max-width: 650px;
			margin: 0 auto 50px auto;
			line-height: 1.6;
		}
		.contact-info-wrapper {
			padding-right: 20px;
		}
		.contact-card {
			display: flex;
			align-items: center;
			background: #f8fafc;
			padding: 25px;
			border-radius: 20px;
			border: 1px solid rgba(0,0,0,0.03);
			transition: all 0.3s ease;
		}
		.contact-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 15px 30px rgba(37, 99, 235, 0.08) !important;
			background: #ffffff;
			border-color: rgba(37, 99, 235, 0.1);
		}
		.contact-icon {
			width: 60px;
			height: 60px;
			background: rgba(37, 99, 235, 0.1);
			color: #2563eb;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 1.5rem;
			margin-right: 20px;
			flex-shrink: 0;
			transition: all 0.3s ease;
		}
		.contact-card:hover .contact-icon {
			background: #2563eb;
			color: #ffffff;
		}
		.contact-details h4 {
			font-size: 1.1rem;
			font-weight: 700;
			color: #0f172a;
			margin-bottom: 5px;
		}
		.contact-details p {
			margin: 0;
			color: #64748b;
			font-size: 0.95rem;
			line-height: 1.5;
		}
		.contact-details p a {
			color: #64748b;
			text-decoration: none;
			transition: color 0.2s ease;
		}
		.contact-details p a:hover {
			color: #2563eb;
		}
		.map-wrapper {
			border: 8px solid #ffffff;
			box-shadow: 0 15px 35px rgba(0,0,0,0.06) !important;
		}

		/* WhatsApp Button */
		.whatsapp {
			position: fixed;
			bottom: 30px;
			right: 30px;
			width: 60px;
			height: 60px;
			background: #25d366;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			z-index: 999;
			text-decoration: none;
			box-shadow: 0 4px 12px rgba(0,0,0,0.2);
			transition: all 0.3s;
		}
		.whatsapp:hover {
			background: #20ba5a;
			transform: scale(1.1);
		}
		.my-whatsapp {
			color: #fff;
			font-size: 1.8rem;
		}

		/* Footer */
		.section-footer-top {
			background: #f8fafc;
			padding: 80px 0 60px 0;
			border-top: 1px solid rgba(0,0,0,0.03);
		}
		.footer-title {
			font-size: 1.15rem;
			font-weight: 800;
			color: #0f172a;
			letter-spacing: 0.5px;
			position: relative;
			padding-bottom: 12px;
		}
		.footer-title::after {
			content: '';
			position: absolute;
			left: 0;
			bottom: 0;
			width: 40px;
			height: 3px;
			background: #2563eb;
			border-radius: 2px;
		}
		.footer-text {
			font-size: 0.95rem;
			color: #64748b;
			line-height: 1.7;
		}
		.contact-item-footer {
			transition: transform 0.2s ease;
		}
		.contact-item-footer:hover {
			transform: translateX(8px);
		}
		.contact-icon-footer {
			width: 40px;
			height: 40px;
			background: #ffffff;
			border: 1px solid rgba(37, 99, 235, 0.1);
			color: #2563eb;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 1.1rem;
			transition: all 0.3s ease;
			box-shadow: 0 4px 10px rgba(0,0,0,0.02);
			flex-shrink: 0;
		}
		.contact-item-footer:hover .contact-icon-footer {
			background: #2563eb;
			color: #ffffff;
			border-color: #2563eb;
			box-shadow: 0 8px 20px rgba(37, 99, 235, 0.2);
		}
		.hover-primary-text {
			transition: color 0.2s ease;
		}
		.hover-primary-text:hover {
			color: #2563eb !important;
		}
		.social-icon {
			width: 40px;
			height: 40px;
			border-radius: 50%;
			background: #ffffff;
			border: 1px solid rgba(0,0,0,0.05);
			display: flex;
			align-items: center;
			justify-content: center;
			color: #64748b;
			box-shadow: 0 4px 10px rgba(0,0,0,0.02);
			transition: all 0.3s ease;
			text-decoration: none;
		}
		.social-icon:hover {
			background: #2563eb;
			color: #ffffff;
			border-color: #2563eb;
			transform: translateY(-3px);
			box-shadow: 0 8px 15px rgba(37,99,235,0.2);
		}
		.btn-kunjungi {
			background-color: transparent;
			color: #2563eb;
			border: 2px solid #2563eb;
			font-weight: 600;
			padding: 10px 25px;
			border-radius: 100px;
			display: inline-flex;
			align-items: center;
			text-decoration: none;
			transition: all 0.3s ease;
		}
		.btn-kunjungi:hover {
			background-color: #2563eb;
			color: #ffffff;
			transform: translateY(-2px);
			box-shadow: 0 8px 20px rgba(37, 99, 235, 0.2);
		}
		.section-footer-bottom {
			background: #0f172a;
			color: #94a3b8;
			padding: 24px 0;
		}
		.footer-copyright {
			font-size: 0.95rem;
		}

		/* FAQ Section Modern */
		.section-faq {
			padding: 100px 0;
			background-color: #f8fafc;
			position: relative;
		}
		.faq-badge {
			display: inline-block;
			background: rgba(37, 99, 235, 0.1);
			color: #2563eb;
			padding: 8px 16px;
			border-radius: 50px;
			font-weight: 700;
			font-size: 0.85rem;
			letter-spacing: 1px;
			margin-bottom: 20px;
			text-transform: uppercase;
		}
		.faq-main-title {
			font-size: 3rem;
			font-weight: 800;
			color: #0f172a;
			line-height: 1.2;
			letter-spacing: -1px;
		}
		.faq-desc {
			font-size: 1.1rem;
			color: #64748b;
			line-height: 1.8;
			max-width: 95%;
		}
		.btn-tanya-kami {
			display: inline-flex;
			align-items: center;
			background: #ffffff;
			color: #0f172a;
			font-weight: 600;
			padding: 12px 28px;
			border-radius: 100px;
			text-decoration: none;
			box-shadow: 0 4px 15px rgba(0,0,0,0.05);
			transition: all 0.3s ease;
			border: 1px solid rgba(0,0,0,0.05);
		}
		.btn-tanya-kami:hover {
			background: #0f172a;
			color: #ffffff;
			transform: translateY(-3px);
			box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
		}
		
		/* Modern Accordion */
		.modern-accordion .accordion-item {
			background: #ffffff;
			border: 1px solid rgba(0,0,0,0.04);
			border-radius: 16px !important;
			margin-bottom: 16px;
			overflow: hidden;
			box-shadow: 0 4px 15px rgba(0,0,0,0.02);
			transition: all 0.3s ease;
		}
		.modern-accordion .accordion-item:hover {
			box-shadow: 0 10px 25px rgba(0,0,0,0.04);
			border-color: rgba(37, 99, 235, 0.1);
		}
		.modern-accordion .accordion-button {
			background: #ffffff;
			color: #1e293b;
			font-weight: 600;
			font-size: 1.1rem;
			padding: 20px 24px;
			box-shadow: none !important;
			border-radius: 16px !important;
		}
		.modern-accordion .accordion-button:not(.collapsed) {
			color: #2563eb;
			background: #ffffff;
			box-shadow: none;
		}
		.modern-accordion .accordion-button::after {
			background-image: none;
			content: '\f067'; /* FontAwesome plus icon */
			font-family: 'Font Awesome 6 Free';
			font-weight: 900;
			font-size: 1rem;
			color: #64748b;
			transition: all 0.3s ease;
		}
		.modern-accordion .accordion-button:not(.collapsed)::after {
			background-image: none;
			content: '\f068'; /* FontAwesome minus icon */
			color: #2563eb;
			transform: rotate(180deg);
		}
		.modern-accordion .accordion-body {
			padding: 0 24px 24px 24px;
			color: #475569;
			line-height: 1.7;
			font-size: 1rem;
			background: #ffffff;
		}

		/* Responsive */
		@media (max-width: 768px) {
			.title-top-beranda-1 { font-size: 1.8rem; }
			.title-top-beranda-2 { font-size: 1.4rem; }
			.siapa-main-title { font-size: 2rem; line-height: 1.3; }
			.siapa-desc { max-width: 100%; font-size: 1rem; }
			.siapa-card-horizontal { padding: 20px; flex-direction: column; }
			.siapa-card-horizontal .icon-box { margin-bottom: 15px; margin-right: 0; }
			.faq-main-title { font-size: 2rem; }
			.faq-desc { max-width: 100%; font-size: 1rem; }
			.whatsapp { width: 50px; height: 50px; bottom: 20px; right: 20px; }
			.my-whatsapp { font-size: 1.3rem; }
			.hubungi-main-title { font-size: 2rem; }
			.contact-info-wrapper { padding-right: 0; }
		}
	</style>
</head>
<body data-bs-spy="scroll" data-bs-target="#mainNavbar" data-bs-offset="150" tabindex="0">
	<!-- Navigation -->
	<nav class="navbar navbar-expand-lg fixed-top" id="mainNavbar">
		<div class="container">
			<a class="navbar-brand" href="<?php echo baseUrl(''); ?>">
				<img src="<?php echo baseUrl('public/images/sipadeceng.png'); ?>" alt="Logo SIPADECENG">
				<span style="color: #2563eb;">SIPADECENG</span>
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav ms-auto">
					<li class="nav-item">
						<a class="nav-link" href="#beranda">Beranda</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#definisi">Tentang</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#alur">Alur</a>
					</li>


					<li class="nav-item">
						<a class="nav-link" href="#faq">FAQ</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#hubungi">Kontak</a>
					</li>
					<li class="nav-item">
						<a class="nav-link nav-login" href="<?php echo baseUrl('auth/login'); ?>">LOGIN</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<!-- Top Beranda / Hero Section -->
	<section class="top-beranda" id="beranda">
		<div class="hero-glow-1"></div>
		<div class="container hero-content-wrapper">
			<div class="row align-items-center">
				<div class="col-12 col-md-6">
					<div class="title-top-beranda-1">SELAMAT DATANG</div>
					<div class="title-top-beranda-2">di Aplikasi SIPADECENG</div>
					<div class="text-justify">
						Sistem Pengelolaan Administrasi Cuti Elektronik yang modern, cepat, dan transparan. Dirancang khusus untuk mewujudkan pelayanan kepegawaian yang unggul di lingkungan Pengadilan Tinggi Agama Makassar.
					</div>
					<div style="margin-top: 30px; display: flex; gap: 15px; flex-wrap: wrap;">
						<a href="<?php echo baseUrl('auth/login'); ?>" class="tombol-top-beranda"><i class="fas fa-sign-in-alt"></i> LOGIN SISTEM</a>
						<a href="<?php echo baseUrl('informasi/panduan'); ?>" class="tombol-download-panduan"><i class="fas fa-download"></i> UNDUH PANDUAN</a>
					</div>
				</div>
				<div class="col-md-6 d-none d-md-block text-center hero-img-wrapper">
					<img src="<?php echo baseUrl('public/images/sipadeceng3.png'); ?>" alt="SIPADECENG" width="85%">
				</div>
			</div>
		</div>
	</section>

	<!-- Section Tentang -->
	<section class="section-tentang" id="definisi">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-5 mb-5 mb-lg-0">
					<div class="tentang-badge">Tentang SIPADECENG</div>
					<h2 class="tentang-title">Inovasi Layanan Cuti Digital Terpadu</h2>
					<p class="tentang-desc">
						<strong>SIPADECENG</strong> (Sistem Pengelolaan Administrasi Cuti Elektronik) adalah aplikasi berbasis web yang mefasilitasi seluruh rangkaian pengelolaan cuti pegawai secara elektronik. Dari tahap pengajuan, verifikasi operasional, persetujuan atasan, hingga penetapan akhir, semuanya dilakukan dalam satu platform yang terintegrasi penuh.
					</p>
					<p class="tentang-desc">
						Transformasi digital ini memangkas antrean birokrasi manual, meminimalisir penggunaan kertas (paperless), serta memastikan riwayat cuti setiap pegawai terekam jelas dan transparan.
					</p>
				</div>
				<div class="col-lg-6 offset-lg-1">
					<div class="row g-4">
						<div class="col-md-6">
							<div class="tentang-card">
								<div class="tentang-icon"><i class="fas fa-bolt"></i></div>
								<div class="tentang-card-title">Cepat & Responsif</div>
								<div class="tentang-card-desc">Proses birokrasi permohonan menjadi jauh lebih cepat dengan notifikasi dan alur kerja real-time.</div>
							</div>
						</div>
						<div class="col-md-6 mt-md-4">
							<div class="tentang-card">
								<div class="tentang-icon"><i class="fas fa-search"></i></div>
								<div class="tentang-card-title">Transparan</div>
								<div class="tentang-card-desc">Pemohon dapat terus melacak dan mengetahui secara pasti di mana berkas permohonannya berada.</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="tentang-card">
								<div class="tentang-icon"><i class="fas fa-shield-alt"></i></div>
								<div class="tentang-card-title">Aman & Terintegrasi</div>
								<div class="tentang-card-desc">Sistem terpusat dengan jaminan keamanan data dan hak akses bertingkat para pejabat terkait.</div>
							</div>
						</div>
						<div class="col-md-6 mt-md-4">
							<div class="tentang-card">
								<div class="tentang-icon"><i class="fas fa-leaf"></i></div>
								<div class="tentang-card-title">Ramah Lingkungan</div>
								<div class="tentang-card-desc">Mendukung gerakan go green dengan mengurangi penggunaan tumpukan berkas cetak secara drastis.</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Alur Pengajuan Cuti Modern -->
	<section id="alur" class="section-alur">
		<div class="container">
			<div class="row" data-aos="fade-up">
				<div class="col-md-12">
					<h2 class="title-alur-pengajuan">Alur Pengajuan Cuti</h2>
					<p class="subtitle-alur">Proses mudah, cepat, dan transparan dalam 4 langkah</p>
				</div>
			</div>
			
			<div class="row position-relative mt-4">
				<div class="col-12 col-md-6 col-lg-3 mb-5 mb-lg-0">
					<div class="alur-card" data-aos="fade-up" data-aos-delay="100">
						<div class="alur-step-number">1</div>
						<div class="alur-icon">
							<i class="fas fa-file-signature"></i>
						</div>
						<h3 class="alur-title">Pemohon</h3>
						<p class="alur-deskripsi">
							Pemohon masuk ke Aplikasi SIPADECENG dan mengisi formulir cuti sesuai dengan jenis cuti yang dimohonkan.
						</p>
					</div>
				</div>

				<div class="col-12 col-md-6 col-lg-3 mb-5 mb-lg-0">
					<div class="alur-card" data-aos="fade-up" data-aos-delay="200">
						<div class="alur-step-number">2</div>
						<div class="alur-icon">
							<i class="fas fa-clipboard-check"></i>
						</div>
						<h3 class="alur-title">Operator</h3>
						<p class="alur-deskripsi">
							Operator memverifikasi kelengkapan dokumen, menentukan pejabat yang sesuai, dan meneruskan permohonan ke atasan.
						</p>
					</div>
				</div>

				<div class="col-12 col-md-6 col-lg-3 mb-5 mb-lg-0">
					<div class="alur-card" data-aos="fade-up" data-aos-delay="300">
						<div class="alur-step-number">3</div>
						<div class="alur-icon">
							<i class="fas fa-user-check"></i>
						</div>
						<h3 class="alur-title">Atasan Langsung</h3>
						<p class="alur-deskripsi">
							Atasan langsung menerima permohonan, meninjau ketersediaan pegawai, dan memberikan persetujuan atau penolakan.
						</p>
					</div>
				</div>

				<div class="col-12 col-md-6 col-lg-3 mb-5 mb-lg-0">
					<div class="alur-card" data-aos="fade-up" data-aos-delay="400">
						<div class="alur-step-number">4</div>
						<div class="alur-icon">
							<i class="fas fa-stamp"></i>
						</div>
						<h3 class="alur-title">Pejabat Berwenang</h3>
						<p class="alur-deskripsi">
							Pejabat Berwenang memberikan persetujuan akhir dan permohonan cuti dinyatakan selesai atau ditolak.
						</p>
					</div>
				</div>
			</div>
		</div>
	</section>



	<!-- Siapa Saja Section Modern -->
	<section id="pengguna" class="section-siapa">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-5 mb-5 mb-lg-0" data-aos="fade-right">
					<h2 class="siapa-main-title">
						Siapa saja yang menggunakan<br>
						<span style="color: #2563eb;">SIPADECENG?</span>
					</h2>
					<p class="siapa-desc mt-4">
						Aplikasi ini dirancang untuk memfasilitasi berbagai peran dalam struktur birokrasi, memastikan alur pengajuan cuti berjalan lancar, transparan, dan akuntabel.
					</p>
				</div>
				<div class="col-lg-7">
					<div class="d-flex flex-column gap-3">
						<div class="siapa-card-horizontal" data-aos="fade-left" data-aos-delay="100">
							<div class="icon-box">
								<i class="fas fa-user mb-0"></i>
							</div>
							<div class="text-box">
								<h4>Pegawai sebagai Pemohon</h4>
								<p>Pegawai memiliki akun dengan username dan password untuk mengajukan permohonan cuti sesuai jenis cuti yang diperlukan dan kebutuhan personal mereka.</p>
							</div>
						</div>
						
						<div class="siapa-card-horizontal" data-aos="fade-left" data-aos-delay="200">
							<div class="icon-box">
								<i class="fas fa-cogs mb-0"></i>
							</div>
							<div class="text-box">
								<h4>Pegawai sebagai Operator</h4>
								<p>Pegawai yang ditunjuk melalui Surat Tugas resmi berperan sebagai operator yang "mengatur lalu lintas" permohonan cuti, memverifikasi, dan meneruskan ke atasan yang sesuai.</p>
							</div>
						</div>
						
						<div class="siapa-card-horizontal" data-aos="fade-left" data-aos-delay="300">
							<div class="icon-box">
								<i class="fas fa-user-tie mb-0"></i>
							</div>
							<div class="text-box">
								<h4>Pegawai sebagai Atasan Langsung</h4>
								<p>Atasan langsung pemohon wajib mengetahui dan memberikan keputusan atas permohonan cuti dengan mempertimbangkan beban kerja dan ketersediaan pegawai.</p>
							</div>
						</div>
						
						<div class="siapa-card-horizontal" data-aos="fade-left" data-aos-delay="400">
							<div class="icon-box">
								<i class="fas fa-certificate mb-0"></i>
							</div>
							<div class="text-box">
								<h4>Pegawai sebagai Pejabat Berwenang</h4>
								<p>Pejabat Berwenang memberikan persetujuan akhir terhadap permohonan cuti dengan mempertimbangkan jalannya roda organisasi dan kepentingan layanan publik.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- WhatsApp Button -->
	<a href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', '(0411) 452653'); ?>?text=Assalamu%20alaikum%20wr%20wb.%20Saya%20ingin%20bertanya%20tentang%20SIPADECENG" target="_blank" class="whatsapp" title="Hubungi kami via WhatsApp">
		<i class="fab fa-whatsapp my-whatsapp"></i>
	</a>

	<!-- FAQ Section Modern -->
	<section id="faq" class="section-faq">
		<div class="container">
			<div class="row">
				<div class="col-lg-5 mb-5 mb-lg-0" data-aos="fade-right">
					<div class="faq-badge">Bantuan</div>
					<h2 class="faq-main-title">Pertanyaan yang Sering<br>Diajukan (FAQ)</h2>
					<p class="faq-desc mt-4">
						Temukan jawaban atas pertanyaan umum seputar penggunaan aplikasi SIPADECENG. Jika Anda memiliki pertanyaan lain, jangan ragu untuk menghubungi tim dukungan kami.
					</p>
					<div class="mt-4">
						<a href="#hubungi" class="btn-tanya-kami">
							Tanya Kami <i class="fas fa-arrow-right ms-2"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-7" data-aos="fade-left">
					<div class="accordion modern-accordion" id="faqAccordion">
						<!-- Item 1 -->
						<div class="accordion-item">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
									Apa itu SIPADECENG?
								</button>
							</h2>
							<div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
								<div class="accordion-body">
									SIPADECENG adalah aplikasi web berbasis elektronik untuk pengelolaan pengajuan cuti pegawai di lingkungan Pengadilan Tinggi Agama Makassar dengan proses yang transparan, aman, dan terintegrasi.
								</div>
							</div>
						</div>

						<!-- Item 2 -->
						<div class="accordion-item">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
									Siapa saja yang dapat mengakses SIPADECENG?
								</button>
							</h2>
							<div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
								<div class="accordion-body">
									SIPADECENG dapat diakses oleh seluruh pegawai, atasan langsung, operator, dan Pejabat Berwenang di lingkungan Pengadilan Tinggi Agama Makassar yang memiliki akun terdaftar.
								</div>
							</div>
						</div>

						<!-- Item 3 -->
						<div class="accordion-item">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
									Apakah data di SIPADECENG aman?
								</button>
							</h2>
							<div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
								<div class="accordion-body">
									Ya, data Anda dilindungi dengan standar keamanan tinggi, enkripsi data, dan hanya dapat diakses oleh pihak yang berwenang sesuai dengan role masing-masing.
								</div>
							</div>
						</div>

						<!-- Item 4 -->
						<div class="accordion-item">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
									Apakah laporan cuti bisa diekspor?
								</button>
							</h2>
							<div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
								<div class="accordion-body">
									Ya, laporan rekap cuti dapat diekspor ke format PDF dan Excel secara otomatis melalui fitur ekspor yang tersedia di sistem untuk kebutuhan administrasi dan pelaporan.
								</div>
							</div>
						</div>

						<!-- Item 5 -->
						<div class="accordion-item">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
									Bagaimana jika permohonan cuti saya ditolak?
								</button>
							</h2>
							<div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
								<div class="accordion-body">
									Jika permohonan ditolak, Anda akan mendapatkan notifikasi beserta alasan penolakan. Anda dapat mengajukan permohonan kembali dengan menyesuaikan tanggal atau jenis cuti yang dimohonkan.
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Hubungi Kami Section -->
	<section id="hubungi" class="section-hubungi position-relative">
		<div class="container">
			<div class="row" data-aos="fade-up">
				<div class="col-md-12">
					<div class="hubungi-badge">Kontak</div>
					<h2 class="hubungi-main-title">Hubungi Kami</h2>
					<p class="hubungi-subtitle">Butuh bantuan atau konsultasi terkait penggunaan SIPADECENG? Tim kami siap membantu Anda terkait pengajuan, monitoring, maupun kendala teknis.</p>
				</div>
			</div>

			<div class="row align-items-center mt-4">
				<div class="col-lg-5 mb-5 mb-lg-0" data-aos="fade-right">
					<div class="contact-info-wrapper">
						<div class="contact-card border-0 mb-4 shadow-sm">
							<div class="contact-icon">
								<i class="fas fa-envelope"></i>
							</div>
							<div class="contact-details">
								<h4>Email</h4>
								<p><a href="mailto:admin@pta-makassar.go.id">admin@pta-makassar.go.id</a></p>
							</div>
						</div>
						
						<div class="contact-card border-0 mb-4 shadow-sm">
							<div class="contact-icon">
								<i class="fas fa-phone-alt"></i>
							</div>
							<div class="contact-details">
								<h4>Telepon</h4>
								<p><a href="tel:0411452653">(0411) 452653</a></p>
							</div>
						</div>

						<div class="contact-card border-0 shadow-sm">
							<div class="contact-icon">
								<i class="fas fa-map-marker-alt"></i>
							</div>
							<div class="contact-details">
								<h4>Alamat</h4>
								<p>Jln. A.P. Pettarani No.66,<br>Makassar 90142</p>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-7" data-aos="fade-left">
					<div class="map-wrapper rounded-4 overflow-hidden">
						<iframe title="mapsPTA" src="https://www.google.com/maps?q=-5.1521,119.4326&hl=id&z=16&output=embed" width="100%" height="400" style="border:0; display:block;" allowfullscreen="" loading="lazy"></iframe>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Footer Top -->
	<section class="section-footer-top">
		<div class="container" data-aos="fade-up">
			<div class="row">
				<div class="col-lg-4 mb-5 mb-lg-0">
					<div class="mb-4">
						<i class="fas fa-laptop text-primary" style="font-size: 1.5rem;"></i> 
						<span class="fw-bold text-dark ms-2" style="font-size: 1.4rem; letter-spacing: -0.5px;">SIPADECENG</span>
					</div>
					<div class="footer-text mb-4 pe-lg-4">
						Sistem Pengelolaan Administrasi Cuti Elektronik yang modern, transparan, dan terintegrasi untuk mempermudah proses cuti pegawai.
					</div>
					<div class="d-flex gap-3">
						<a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
						<a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
						<a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
					</div>
				</div>
                
				<div class="col-lg-4 mb-5 mb-lg-0">
					<h4 class="footer-title mb-4">KONTAK KAMI</h4>
					<div class="d-flex flex-column gap-3 mt-4">
						<div class="contact-item-footer d-flex">
							<div class="contact-icon-footer me-3">
								<i class="fas fa-building"></i>
							</div>
							<div class="footer-text">
								<strong class="d-block text-dark mb-1">Pengadilan Tinggi Agama</strong>
								Jln. A.P. Pettarani No.66, Makassar 90142
							</div>
						</div>
						<div class="contact-item-footer d-flex">
							<div class="contact-icon-footer me-3">
								<i class="fas fa-phone-alt"></i>
							</div>
							<div class="footer-text mt-2">
								<a href="tel:0411452653" class="text-decoration-none text-muted hover-primary-text">(0411) 452653</a>
							</div>
						</div>
						<div class="contact-item-footer d-flex">
							<div class="contact-icon-footer me-3">
								<i class="fas fa-envelope"></i>
							</div>
							<div class="footer-text mt-2">
								<a href="mailto:admin@pta-makassar.go.id" class="text-decoration-none text-muted hover-primary-text">admin@pta-makassar.go.id</a>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-4">
					<h4 class="footer-title mb-4">INSTITUSI</h4>
					<div class="footer-text mb-4 pe-lg-3 mt-4">
						Dikembangkan oleh dan untuk Pengadilan Tinggi Agama Makassar guna mendukung tata kelola birokrasi dan layanan publik yang unggul.
					</div>
					<a href="<?php echo baseUrl(''); ?>" class="btn-kunjungi">
						<i class="fas fa-globe me-2"></i> Kunjungi Website
					</a>
				</div>
			</div>
		</div>
	</section>

	<!-- Footer Bottom -->
	<section class="section-footer-bottom">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<div class="footer-copyright">
						© <script>document.write(new Date().getFullYear());</script> <strong>SIPADECENG</strong> - Pengadilan Tinggi Agama Makassar. Hak Cipta Dilindungi.
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- BOOTSTRAP 5 JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<!-- AOS Scroll Animation -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.2.0/aos.js"></script>

	<script>
		// Navbar Active on Scroll
		$(function() {
			$(window).on('scroll', function() {
				if ($(window).scrollTop() > 150) {
					$('.navbar').addClass('active');
				} else {
					$('.navbar').removeClass('active');
				}
			});
		});

		// Smooth scroll is natively handled by CSS 'scroll-behavior: smooth'
		// in modern browsers and compensated by 'scroll-padding-top'.


		// Initialize AOS
		AOS.init({
			easing: 'ease-out-back',
			duration: 1000,
			once: true
		});

		// Float Animation
		const style = document.createElement('style');
		style.textContent = `
			@keyframes float {
				0%, 100% { transform: translateY(0px); }
				50% { transform: translateY(-20px); }
			}
		`;
		document.head.appendChild(style);
	</script>
</body>
</html>