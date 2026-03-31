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
		* {
			font-family: 'Poppins', 'Roboto', 'Inter', sans-serif;
		}
		body {
			background: #f4f6fa;
			margin: 0;
			padding: 0;
		}
		.navbar {
			background: rgba(255,255,255,0.95)!important;
			box-shadow: 0 2px 12px rgba(44,62,80,0.06);
		}
		.navbar.active {
			background: rgba(255,255,255,1)!important;
			box-shadow: 0 4px 20px rgba(0,0,0,0.1);
		}
		.navbar-brand {
			font-weight: 700;
			font-size: 1.3rem;
			display: flex;
			align-items: center;
		}
		.navbar-brand img {
			height: 40px;
			margin-right: 10px;
		}
		.nav-link {
			font-weight: 500;
			color: #333!important;
			margin: 0 8px;
			transition: color 0.3s;
		}
		.nav-link:hover {
			color: #2563eb!important;
		}
		.dropdown-menu {
			background: #fff;
			border: 1px solid #e0e0e0;
			border-radius: 8px;
			box-shadow: 0 4px 12px rgba(0,0,0,0.1);
		}
		.dropdown-item {
			padding: 10px 15px;
			font-weight: 500;
			color: #333;
		}
		.dropdown-item:hover {
			background: #e3f0ff;
			color: #2563eb;
		}
		.nav-login {
			background: #2563eb;
			color: #fff!important;
			border-radius: 6px;
			padding: 8px 20px!important;
			transition: background 0.2s;
		}
		.nav-login:hover {
			background: #1e40af!important;
		}

		/* Top Beranda Section */
		.top-beranda {
			background-color: #fff;
			padding: 80px 0;
			min-height: 70vh;
			display: flex;
			align-items: center;
		}
		.title-top-beranda-1 {
			font-size: 2.5rem;
			font-weight: 700;
			color: #1e293b;
			margin-bottom: 10px;
		}
		.title-top-beranda-2 {
			font-size: 2rem;
			font-weight: 600;
			color: #2563eb;
			margin-bottom: 20px;
		}
		.top-beranda .text-justify {
			font-size: 1.1rem;
			color: #475569;
			line-height: 1.6;
		}
		.tombol-top-beranda {
			display: inline-block;
			background: #2563eb;
			color: #fff;
			padding: 14px 36px;
			border-radius: 8px;
			text-decoration: none;
			font-weight: 600;
			font-size: 1.1em;
			transition: background 0.2s;
		}
		.tombol-top-beranda:hover {
			background: #1e40af;
			color: #fff;
		}

		/* Alur Pengajuan */
		.title-alur-pengajuan {
			font-size: 2.5rem;
			font-weight: 700;
			color: #1e293b;
			text-align: center;
			margin-bottom: 40px;
		}
		.alur-icon {
			font-size: 1.8rem;
			font-weight: 700;
			color: #fff;
			background: #2563eb;
			width: 60px;
			height: 60px;
			display: flex;
			align-items: center;
			justify-content: center;
			border-radius: 50%;
		}
		.alur-title {
			font-weight: 600;
			font-size: 1.1rem;
			color: #1e293b;
			margin-top: 15px;
		}
		.alur-deskripsi {
			font-size: 0.95rem;
			color: #555;
			line-height: 1.5;
			margin-top: 8px;
		}

		/* Pedoman Section */
		.section-pedoman-ecuti {
			background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
			color: #fff;
			padding: 60px 0;
		}
		.pedoman-ecuti-title {
			font-size: 2.2rem;
			font-weight: 700;
			text-align: center;
			margin-bottom: 30px;
		}
		.btn-download-pedoman {
			display: inline-block;
			background: #fff;
			color: #2563eb;
			padding: 12px 30px;
			border-radius: 6px;
			text-decoration: none;
			font-weight: 600;
			transition: all 0.2s;
		}
		.btn-download-pedoman:hover {
			background: #f0f0f0;
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(0,0,0,0.2);
		}

		/* Siapa Saja Section */
		.siapa-main-title {
			font-size: 2.5rem;
			font-weight: 700;
			color: #1e293b;
			line-height: 1.1;
		}
		.siapa-title {
			font-size: 2.2rem;
			font-weight: 700;
			color: #1e293b;
			margin-bottom: 30px;
		}
		.siapa-sub-title {
			font-size: 1.3rem;
			font-weight: 600;
			color: #2563eb;
			margin-top: 25px;
			margin-bottom: 10px;
		}

		/* Hubungi Section */
		#hubungi {
			background: #fff;
			padding: 80px 0;
		}
		.hubungi-title {
			font-size: 2.5rem;
			font-weight: 700;
			color: #1e293b;
			text-align: center;
			margin-bottom: 50px;
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
			background: #f8f9fa;
			padding: 60px 0;
		}
		.footer-left-title {
			font-size: 1.3rem;
			font-weight: 700;
			color: #1e293b;
		}
		.footer-left-content {
			font-size: 0.95rem;
			color: #666;
			line-height: 1.6;
		}
		.section-footer-bottom {
			background: #232336;
			color: #e0e6ed;
		}
		.footer-copyright {
			text-align: center;
			font-size: 0.95rem;
		}

		/* Responsive */
		@media (max-width: 768px) {
			.title-top-beranda-1 { font-size: 1.8rem; }
			.title-top-beranda-2 { font-size: 1.4rem; }
			.siapa-main-title { font-size: 1.8rem; }
			.whatsapp { width: 50px; height: 50px; bottom: 20px; right: 20px; }
			.my-whatsapp { font-size: 1.3rem; }
		}
	</style>
</head>
<body>
	<!-- Navigation -->
	<nav class="navbar navbar-expand-lg sticky-top shadow">
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
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							Informasi
						</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="<?php echo baseUrl('informasi/tentang'); ?>">Tentang SIPADECENG</a></li>
							<li><a class="dropdown-item" href="<?php echo baseUrl('informasi/alur'); ?>">Alur Pengajuan</a></li>
							<li><a class="dropdown-item" href="<?php echo baseUrl('informasi/dasar-hukum'); ?>">Dasar Hukum</a></li>
							<li><a class="dropdown-item" href="<?php echo baseUrl('informasi/panduan'); ?>">Panduan Penggunaan</a></li>
						</ul>
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
		<div class="container">
			<div class="row align-items-center">
				<div class="col-12 col-md-6">
					<div class="title-top-beranda-1">SELAMAT DATANG</div>
					<div class="title-top-beranda-2">di Aplikasi SIPADECENG</div>
					<div class="text-justify">
						SIPADECENG (Sistem Pengelolaan Administrasi Cuti Elektronik) adalah aplikasi pengelolaan cuti elektronik yang mempermudah proses pengajuan, persetujuan, dan monitoring cuti pegawai di lingkungan Pengadilan Tinggi Agama Makassar dengan sistem yang transparan, aman, dan terintegrasi.
					</div>
					<div style="margin-top: 30px;">
						<a href="<?php echo baseUrl('auth/login'); ?>" class="tombol-top-beranda"><i class="fas fa-sign-in-alt"></i> LOGIN</a>
					</div>
				</div>
				<div class="col-md-6 d-none d-md-block text-center">
					<img src="<?php echo baseUrl('public/images/sipadeceng3.png'); ?>" alt="SIPADECENG" width="85%"> <!-- style="animation: float 3s ease-in-out infinite;" -->
				</div>
			</div>
		</div>
	</section>

	<!-- Alur Pengajuan Cuti -->
	<section style="background-color: #F7F8F9;" class="py-5">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="title-alur-pengajuan">Alur Pengajuan Cuti</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12 col-sm-12 col-md-6 col-lg-3 mb-4">
					<div class="card shadow rounded h-100" style="border: none;">
						<div class="card-body">
							<div class="row">
								<div class="col-12 d-flex justify-content-center mb-3">
									<div class="alur-icon" data-aos="zoom-in">01</div>
								</div>
								<div class="col-12">
									<div class="alur-title text-center">Pemohon</div>
									<div class="alur-deskripsi text-center">
										Pemohon masuk ke Aplikasi SIPADECENG dan mengisi formulir cuti sesuai dengan jenis cuti yang dimohonkan
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-12 col-sm-12 col-md-6 col-lg-3 mb-4">
					<div class="card shadow rounded h-100" style="border: none;">
						<div class="card-body">
							<div class="row">
								<div class="col-12 d-flex justify-content-center mb-3">
									<div class="alur-icon" data-aos="zoom-in" data-aos-delay="100">02</div>
								</div>
								<div class="col-12">
									<div class="alur-title text-center">Operator</div>
									<div class="alur-deskripsi text-center">
										Operator memverifikasi kelengkapan dokumen, menentukan pejabat yang sesuai, dan meneruskan permohonan ke atasan
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-12 col-sm-12 col-md-6 col-lg-3 mb-4">
					<div class="card shadow rounded h-100" style="border: none;">
						<div class="card-body">
							<div class="row">
								<div class="col-12 d-flex justify-content-center mb-3">
									<div class="alur-icon" data-aos="zoom-in" data-aos-delay="200">03</div>
								</div>
								<div class="col-12">
									<div class="alur-title text-center">Atasan Langsung</div>
									<div class="alur-deskripsi text-center">
										Atasan langsung menerima permohonan, meninjau ketersediaan pegawai, dan memberikan keputusan persetujuan atau penolakan
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-12 col-sm-12 col-md-6 col-lg-3 mb-4">
					<div class="card shadow rounded h-100" style="border: none;">
						<div class="card-body">
							<div class="row">
								<div class="col-12 d-flex justify-content-center mb-3">
									<div class="alur-icon" data-aos="zoom-in" data-aos-delay="300">04</div>
								</div>
								<div class="col-12">
									<div class="alur-title text-center">Pejabat Berwenang</div>
									<div class="alur-deskripsi text-center">
										Pejabat Berwenang memberikan persetujuan akhir dan permohonan cuti dinyatakan selesai atau ditolak
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Pedoman / Manual Book -->
	<section class="section-pedoman-ecuti py-5">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="pedoman-ecuti-title">PANDUAN PENGGUNAAN SIPADECENG</div>
					<div class="text-center">
						<a href="<?php echo baseUrl('informasi/panduan'); ?>" class="btn-download-pedoman">
							<i class="fas fa-download"></i> UNDUH PANDUAN
						</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Siapa Saja Section -->
	<section class="py-5" style="background: #fff;">
		<div class="container">
			<div class="row">
				<div class="col-md-5 d-flex align-items-center">
					<div class="d-none d-lg-block w-100">
						<div class="siapa-main-title">
							<div>Siapa saja</div>
							<div>yang menggunakan</div>
							<div>SIPADECENG?</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-12 col-lg-7">
					<div class="row d-block d-lg-none">
						<div class="col-md-12">
							<div class="siapa-title">Siapa saja yang menggunakan SIPADECENG?</div>
						</div>
					</div>

					<div class="siapa-sub-title"><i class="fas fa-user"></i> Pegawai sebagai Pemohon</div>
					<p>
						Pegawai memiliki akun dengan username dan password untuk mengajukan permohonan cuti sesuai jenis cuti yang diperlukan dan kebutuhan personal mereka.
					</p>

					<div class="siapa-sub-title"><i class="fas fa-cogs"></i> Pegawai sebagai Operator</div>
					<p>
						Pegawai yang ditunjuk melalui Surat Tugas resmi berperan sebagai operator yang "mengatur lalu lintas" permohonan cuti, memverifikasi, dan meneruskan ke atasan yang sesuai.
					</p>

					<div class="siapa-sub-title"><i class="fas fa-user-tie"></i> Pegawai sebagai Atasan Langsung</div>
					<p>
						Atasan langsung pemohon wajib mengetahui dan memberikan keputusan atas permohonan cuti dengan mempertimbangkan beban kerja dan ketersediaan pegawai.
					</p>

					<div class="siapa-sub-title"><i class="fas fa-certificate"></i> Pegawai sebagai Pejabat Berwenang</div>
					<p>
						Pejabat Berwenang memberikan persetujuan akhir terhadap permohonan cuti dengan mempertimbangkan jalannya roda organisasi dan kepentingan layanan publik.
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- WhatsApp Button -->
	<a href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', '(0411) 452653'); ?>?text=Assalamu%20alaikum%20wr%20wb.%20Saya%20ingin%20bertanya%20tentang%20SIPADECENG" target="_blank" class="whatsapp" title="Hubungi kami via WhatsApp">
		<i class="fab fa-whatsapp my-whatsapp"></i>
	</a>

	<!-- FAQ Section -->
	<section id="faq" class="py-5" style="background: #1e293b;">
		<div class="container">
			<div class="row justify-content-center mb-5">
				<div class="col-12 text-center">
					<h2 style="font-size: 2.5rem; font-weight: 700; color: #fff; margin-bottom: 10px;">FAQ</h2>
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-lg-8">
					<div class="accordion" id="faqAccordion">
						<div class="accordion-item mb-3 rounded-3 shadow-sm border-0">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" style="background: #fff; color: #1e293b; font-weight: 600; font-size: 1.1rem;">
									<i class="fas fa-chevron-right me-2"></i> Apa itu SIPADECENG?
								</button>
							</h2>
							<div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
								<div class="accordion-body" style="background: #fff;">
									SIPADECENG adalah aplikasi web berbasis elektronik untuk pengelolaan pengajuan cuti pegawai di lingkungan Pengadilan Tinggi Agama Makassar dengan proses yang transparan, aman, dan terintegrasi.
								</div>
							</div>
						</div>

						<div class="accordion-item mb-3 rounded-3 shadow-sm border-0">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" style="background: #fff; color: #1e293b; font-weight: 600; font-size: 1.1rem;">
									<i class="fas fa-chevron-right me-2"></i> Siapa saja yang dapat mengakses SIPADECENG?
								</button>
							</h2>
							<div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
								<div class="accordion-body" style="background: #fff;">
									SIPADECENG dapat diakses oleh seluruh pegawai, atasan langsung, operator, dan Pejabat Berwenang di lingkungan Pengadilan Tinggi Agama Makassar yang memiliki akun terdaftar.
								</div>
							</div>
						</div>

						<div class="accordion-item mb-3 rounded-3 shadow-sm border-0">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" style="background: #fff; color: #1e293b; font-weight: 600; font-size: 1.1rem;">
									<i class="fas fa-chevron-right me-2"></i> Apakah data di SIPADECENG aman?
								</button>
							</h2>
							<div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
								<div class="accordion-body" style="background: #fff;">
									Ya, data Anda dilindungi dengan standar keamanan tinggi, enkripsi data, dan hanya dapat diakses oleh pihak yang berwenang sesuai dengan role masing-masing.
								</div>
							</div>
						</div>

						<div class="accordion-item mb-3 rounded-3 shadow-sm border-0">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" style="background: #fff; color: #1e293b; font-weight: 600; font-size: 1.1rem;">
									<i class="fas fa-chevron-right me-2"></i> Apakah laporan cuti bisa diekspor?
								</button>
							</h2>
							<div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
								<div class="accordion-body" style="background: #fff;">
									Ya, laporan rekap cuti dapat diekspor ke format PDF dan Excel secara otomatis melalui fitur ekspor yang tersedia di sistem untuk kebutuhan administrasi dan pelaporan.
								</div>
							</div>
						</div>

						<div class="accordion-item rounded-3 shadow-sm border-0">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" style="background: #fff; color: #1e293b; font-weight: 600; font-size: 1.1rem;">
									<i class="fas fa-chevron-right me-2"></i> Bagaimana jika permohonan cuti saya ditolak?
								</button>
							</h2>
							<div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
								<div class="accordion-body" style="background: #fff;">
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
	<section id="hubungi" class="py-5">
		<div class="container">
			<h2 class="hubungi-title">Hubungi Kami</h2>
			<div class="row justify-content-center align-items-center">
				<div class="col-lg-6 mb-4 mb-lg-0">
					<div class="mb-3" style="font-size: 1.13rem; color: #444;">
						<p><b>Butuh bantuan atau konsultasi terkait penggunaan SIPADECENG?</b></p>
						<p>Silakan hubungi kami melalui kontak di bawah ini atau kunjungi langsung kantor kami. Tim kami siap membantu Anda terkait pengajuan, monitoring, maupun kendala teknis pada aplikasi.</p>
					</div>
					<div style="color: #666; font-size: 1.08rem;">
						<div class="mb-3">
							<strong><i class="fas fa-envelope text-primary"></i> Email:</strong><br>
							admin@pta-makassar.go.id
						</div>
						<div class="mb-3">
							<strong><i class="fas fa-phone text-primary"></i> Telepon:</strong><br>
							(0411) 452653
						</div>
						<div class="mb-3">
							<strong><i class="fas fa-map-marker-alt text-primary"></i> Alamat:</strong><br>
							Jln. A.P. Pettarani No.66, Makassar 90142
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="ratio ratio-16x9 rounded-4 shadow-sm overflow-hidden">
						<iframe title="mapsPTA" src="https://www.google.com/maps?q=-5.1521,119.4326&hl=id&z=16&output=embed" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Footer Top -->
	<section class="section-footer-top py-5">
		<div class="container">
			<div class="row">
				<div class="col-md-4 mb-4">
					<div class="footer-left-title mb-2"><i class="fas fa-laptop"></i> SIPADECENG</div>
					<div class="footer-left-content">
						SIPADECENG (Sistem Pengelolaan Administrasi Cuti Elektronik) adalah aplikasi web yang mempermudah proses pengajuan, persetujuan, dan monitoring cuti pegawai di lingkungan Pengadilan Tinggi Agama Makassar.
					</div>
				</div>
				<div class="col-md-4 mb-4">
					<div class="footer-left-title mb-2"><i class="fas fa-phone-alt"></i> KONTAK KAMI</div>
					<div class="footer-left-content mb-2">
						<strong>Pengadilan Tinggi Agama Makassar</strong>
					</div>
					<div class="d-flex footer-left-content mb-2">
						<div class="me-2"><i class="fas fa-map-marker-alt text-primary"></i></div>
						<div>Jln. A.P. Pettarani No.66, Makassar</div>
					</div>
					<div class="d-flex footer-left-content mb-2">
						<div class="me-2"><i class="fas fa-phone text-primary"></i></div>
						<div>(0411) 452653</div>
					</div>
					<div class="d-flex footer-left-content">
						<div class="me-2"><i class="fas fa-envelope text-primary"></i></div>
						<div>admin@pta-makassar.go.id</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="footer-left-title mb-2"><i class="fas fa-building"></i> INSTITUSI</div>
					<div class="footer-left-content mb-3">
						SIPADECENG merupakan aplikasi yang dikembangkan oleh dan untuk Pengadilan Tinggi Agama Makassar guna mendukung tata kelola cuti yang transparan, efisien, dan akuntabel.
					</div>
					<div>
						<a href="<?php echo baseUrl(''); ?>" style="color: #2563eb; text-decoration: none; font-weight: 600;">
							<i class="fas fa-globe"></i> www.pta-makassar.go.id
						</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Footer Bottom -->
	<section class="section-footer-bottom py-3">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="footer-copyright">
						© <script>document.write(new Date().getFullYear());</script> SIPADECENG - Pengadilan Tinggi Agama Makassar. Hak Cipta Dilindungi.
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

		// Smooth Scroll
		$(function() {
			$('a[href*="#"]').on('click', function(e) {
				if (this.hash !== '') {
					e.preventDefault();
					var target = $(this.hash);
					if (target.length) {
						$('html, body').animate({
							scrollTop: target.offset().top - 80
						}, 800);
					}
				}
			});
		});

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