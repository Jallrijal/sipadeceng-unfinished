-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2026 at 12:11 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sipadeceng`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama_admin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`) VALUES
(1, 'Admin'),
(2, 'Alif Cukurukuk');

-- --------------------------------------------------------

--
-- Table structure for table `admin_activities`
--

CREATE TABLE `admin_activities` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `activity_type` enum('send_final_blanko','upload_supporting_document') NOT NULL,
  `leave_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `alasan_cuti`
--

CREATE TABLE `alasan_cuti` (
  `id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `alasan` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `alasan_cuti`
--

INSERT INTO `alasan_cuti` (`id`, `leave_type_id`, `alasan`, `deskripsi`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Acara Keluarga', 'Acara keluarga seperti pernikahan, khitanan, atau ulang tahun', 1, '2025-07-17 02:40:22', '2025-07-21 06:50:21'),
(2, 1, 'Liburan', 'Liburan', 1, '2025-07-17 02:40:22', '2025-07-21 06:51:05'),
(4, 1, 'Istirahat Tahunan', 'Istirahat tahunan untuk refreshing', 1, '2025-07-17 02:40:22', '2025-07-17 02:40:22'),
(6, 1, 'Lainnya', 'Alasan lain yang tidak tercantum', 1, '2025-07-17 02:40:22', '2025-07-17 02:40:22'),
(7, 2, 'Istirahat Panjang Setelah Masa Kerja', 'Istirahat panjang setelah masa kerja tertentu', 1, '2025-07-17 02:40:42', '2025-07-17 02:40:42'),
(8, 2, 'Perjalanan Ibadah (Umroh/Haji)', 'Melakukan ibadah umroh atau haji', 1, '2025-07-17 02:40:42', '2025-07-17 02:40:42'),
(9, 2, 'Studi Lanjut', 'Mengikuti pendidikan atau pelatihan lanjutan', 1, '2025-07-17 02:40:42', '2025-07-17 02:40:42'),
(10, 2, 'Lainnya', 'Alasan lain yang tidak tercantum', 1, '2025-07-17 02:40:42', '2025-07-17 02:40:42'),
(11, 3, 'Sakit', 'Sakit umum yang memerlukan istirahat', 1, '2025-07-17 02:41:05', '2025-07-21 06:52:46'),
(12, 3, 'Rawat Jalan', 'Sakit yang memerlukan perawatan', 1, '2025-07-17 02:41:05', '2025-07-21 06:53:37'),
(13, 3, 'Rawat Inap', 'Menjalani perawatan inap', 1, '2025-07-17 02:41:05', '2025-07-21 06:54:17'),
(15, 3, 'Lainnya', 'Alasan lain yang tidak tercantum', 1, '2025-07-17 02:41:05', '2025-07-17 02:41:05'),
(16, 4, 'Melahirkan Anak Pertama', 'Melahirkan anak pertama', 1, '2025-07-17 02:41:05', '2025-07-17 02:41:05'),
(17, 4, 'Melahirkan Anak Kedua', 'Melahirkan anak kedua', 1, '2025-07-17 02:41:05', '2025-07-17 02:41:05'),
(18, 4, 'Melahirkan Anak Ketiga', 'Melahirkan anak ketiga', 1, '2025-07-17 02:41:05', '2025-07-17 02:41:05'),
(19, 4, 'Lainnya', 'Alasan lain yang tidak tercantum', 1, '2025-07-17 02:41:05', '2025-07-17 02:41:05'),
(20, 5, 'Keluarga Sakit Kritis', 'Keluarga sakit kritis yang memerlukan pendampingan', 1, '2025-07-17 02:41:05', '2025-07-17 02:41:05'),
(21, 5, 'Keluarga Meninggal', 'Keluarga meninggal dunia', 1, '2025-07-17 02:41:05', '2025-07-17 02:41:05'),
(22, 5, 'Urusan Keluarga Mendesak', 'Urusan keluarga yang mendesak dan tidak dapat ditunda', 1, '2025-07-17 02:41:05', '2025-07-17 02:41:05'),
(23, 5, 'Acara Keluarga Penting', 'Acara keluarga yang sangat penting', 1, '2025-07-17 02:41:05', '2025-07-17 02:44:49'),
(24, 5, 'Urusan Pribadi Mendesak', 'Urusan pribadi yang mendesak', 1, '2025-07-17 02:41:05', '2025-07-22 02:53:18'),
(25, 5, 'Lainnya', 'Alasan lain yang tidak tercantum', 1, '2025-07-17 02:41:05', '2025-07-17 02:41:05'),
(26, 6, 'Studi Lanjut', 'Mengikuti pendidikan atau pelatihan', 1, '2025-07-17 02:41:05', '2025-07-17 02:41:05'),
(27, 6, 'Urusan Pribadi', 'rusan pribadi yang tidak dapat ditunda', 1, '2025-07-17 02:41:05', '2025-07-17 02:41:05'),
(28, 6, 'Perjalanan Pribadi', 'Perjalanan pribadi yang mendesak', 1, '2025-07-17 02:41:05', '2025-07-17 02:41:05'),
(29, 6, 'Lainnya', 'Alasan lain yang tidak tercantum', 1, '2025-07-17 02:41:05', '2025-07-17 02:41:05');

-- --------------------------------------------------------

--
-- Table structure for table `approval_logs`
--

CREATE TABLE `approval_logs` (
  `id` int(11) NOT NULL,
  `leave_request_id` int(11) NOT NULL,
  `step` int(11) NOT NULL COMMENT '1-6: workflow step',
  `action` varchar(50) NOT NULL COMMENT 'approve, reject, change, postpone',
  `catatan` text DEFAULT NULL,
  `approved_by_atasan_id` int(11) DEFAULT NULL COMMENT 'FK: atasan.id_atasan',
  `approved_by_admin_id` int(11) DEFAULT NULL COMMENT 'FK: admin_approvers.id',
  `operator_user_id` int(11) DEFAULT NULL COMMENT 'FK: users.id - user yang melakukan action',
  `logged_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atasan`
--

CREATE TABLE `atasan` (
  `id_atasan` int(11) NOT NULL,
  `nama_atasan` varchar(100) NOT NULL,
  `NIP` varchar(20) NOT NULL,
  `jabatan` varchar(255) DEFAULT NULL,
  `role` enum('kasubbag','kabag','sekretaris','ketua') DEFAULT NULL COMMENT 'NULL = atasan langsung biasa tanpa role tambahan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `atasan`
--

INSERT INTO `atasan` (`id_atasan`, `nama_atasan`, `NIP`, `jabatan`, `role`) VALUES
(1, 'Dr. Drs. Khaeril  R, M.H.', '195912311986031038', 'Ketua', 'ketua'),
(2, 'Drs. Muhammad Alwi, M.H.', '195901311990031001', 'Wakil Ketua', 'ketua'),
(3, 'Dr Hasanuddin, S.H., M.H.', '196410041994031004', 'Panitera', NULL),
(4, 'Dr. Abdul Mutalip, S.Ag., S.H., M.H.', '197011021997031001', 'Sekretaris', 'sekretaris'),
(5, 'Dr. Muhammad Busyaeri, S.H., M.H.', '197803091998031002', 'Kepala Bagian Perencanaan dan Kepegawaian', 'kabag'),
(6, 'Drs. Muhammad Amin, M.A.', '196907162003121003', 'Kepala Bagian Umum dan Keuangan', NULL),
(7, 'Nurbaya, S.Ag., M.H.I.', '197211101998022002', 'Panitera Muda Hukum', NULL),
(8, 'Hasbi, S.H., M.H.', '196512081993031007', 'Panitera Muda Banding', NULL),
(9, 'Nailah Yahya, S.Ag., M.Ag.', '197504072006042001', 'Kepala Subbagian, Subbagian Rencana Program dan Anggaran', NULL),
(10, 'Darias, S.Kom.', '198109082011011007', 'Kepala Subbagian, Subbagian Kepegawaian dan Teknologi Informasi', 'kasubbag'),
(11, 'Muhammad Silmi, S.Kom.', '198409252009121004', 'Kepala Subbagian, Subbagian Tata Usaha dan Rumah Tangga', NULL),
(12, 'Nur Azizah Zainal, S.E.', '198009252005022001', 'Kepala Subbagian, Subbagian Keuangan dan Pelaporan', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `document_signatures`
--

CREATE TABLE `document_signatures` (
  `id` int(11) NOT NULL,
  `leave_request_id` int(11) NOT NULL,
  `signature_placeholder_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `signature_file` varchar(255) NOT NULL,
  `signature_type` enum('user','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kuota_cuti_alasan_penting`
--

CREATE TABLE `kuota_cuti_alasan_penting` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL DEFAULT 5 COMMENT 'ID untuk cuti alasan penting',
  `tahun` int(11) NOT NULL,
  `kuota_tahunan` int(11) DEFAULT NULL COMMENT 'Kuota cuti alasan penting per tahun (menggunakan max_days dari leave_types)',
  `catatan` text DEFAULT NULL COMMENT 'Catatan tambahan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kuota_cuti_alasan_penting`
--

INSERT INTO `kuota_cuti_alasan_penting` (`id`, `user_id`, `leave_type_id`, `tahun`, `kuota_tahunan`, `catatan`, `created_at`, `updated_at`) VALUES
(198, 149, 5, 2026, 30, NULL, '2026-04-06 09:28:07', '2026-04-06 09:28:07');

-- --------------------------------------------------------

--
-- Table structure for table `kuota_cuti_besar`
--

CREATE TABLE `kuota_cuti_besar` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL DEFAULT 2 COMMENT 'ID untuk cuti besar',
  `kuota_total` int(11) DEFAULT NULL COMMENT 'Total kuota dalam hari (menggunakan max_days dari leave_types)',
  `sisa_kuota` int(11) DEFAULT NULL COMMENT 'Sisa kuota dalam hari',
  `tanggal_berhak` date DEFAULT NULL COMMENT 'Tanggal mulai berhak cuti besar (setelah 6 tahun)',
  `status` enum('belum_berhak','berhak','digunakan','habis') DEFAULT 'belum_berhak',
  `catatan` text DEFAULT NULL COMMENT 'Catatan tambahan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kuota_cuti_besar`
--

INSERT INTO `kuota_cuti_besar` (`id`, `user_id`, `leave_type_id`, `kuota_total`, `sisa_kuota`, `tanggal_berhak`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
(4, 3, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:28', '2025-07-07 05:31:28'),
(5, 4, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:28', '2025-07-07 05:31:28'),
(6, 5, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(7, 6, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(8, 7, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(9, 8, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(10, 9, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(11, 10, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(12, 11, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(13, 12, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(14, 13, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(15, 14, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(16, 15, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(17, 16, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(18, 17, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(19, 18, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(20, 19, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(21, 20, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(22, 21, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:31', '2025-07-07 05:31:31'),
(23, 22, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:31', '2025-07-07 05:31:31'),
(24, 23, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:31', '2025-07-07 05:31:31'),
(25, 24, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:31', '2025-07-07 05:31:31'),
(26, 25, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-07-07 05:31:31', '2025-07-07 05:31:31'),
(53, 27, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:15', '2025-08-19 05:17:15'),
(54, 28, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:15', '2025-08-19 05:17:15'),
(55, 29, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:15', '2025-08-19 05:17:15'),
(56, 30, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:15', '2025-08-19 05:17:15'),
(57, 31, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(58, 32, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(59, 33, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(60, 34, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(61, 35, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(62, 36, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(63, 37, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(64, 38, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(65, 39, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(66, 40, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(67, 41, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(68, 42, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(69, 43, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(70, 44, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(71, 45, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(72, 46, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(73, 47, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(74, 48, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(75, 49, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(76, 50, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(77, 51, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(78, 52, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(79, 53, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(80, 54, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(81, 55, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(82, 56, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(83, 57, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(84, 58, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(85, 59, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(86, 60, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(87, 61, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(88, 62, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(89, 63, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(90, 64, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(91, 65, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(92, 66, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(93, 67, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(94, 68, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(95, 69, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(96, 70, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(97, 71, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(98, 72, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(99, 73, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(100, 74, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(101, 75, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(102, 76, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(103, 77, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(104, 78, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(105, 79, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(106, 80, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(107, 81, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(108, 82, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(109, 83, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(110, 84, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(111, 85, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(112, 86, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(113, 87, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(114, 88, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(115, 89, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(116, 90, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(117, 91, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(118, 92, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(119, 93, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(120, 94, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(121, 95, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(122, 96, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(123, 97, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(124, 98, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(125, 99, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(126, 100, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(127, 101, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(128, 102, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(129, 103, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(130, 104, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(131, 105, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(132, 106, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(133, 107, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(134, 108, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(135, 109, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(136, 110, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(137, 111, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(138, 112, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(139, 113, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(140, 114, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(141, 115, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(142, 116, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(143, 117, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(144, 118, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(145, 119, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(146, 120, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(147, 121, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(148, 122, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(149, 123, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(150, 124, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(151, 125, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(152, 126, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(153, 127, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(154, 128, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(155, 129, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(156, 130, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(157, 131, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(158, 132, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(159, 133, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(160, 134, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(161, 135, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(162, 136, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(163, 137, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(164, 138, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(165, 139, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(166, 140, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(167, 141, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:30', '2025-08-19 05:17:30'),
(168, 142, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-19 05:17:30', '2025-08-19 05:17:30'),
(169, 143, 2, 90, 90, NULL, 'belum_berhak', NULL, '2025-08-20 00:20:33', '2025-08-20 00:20:33'),
(170, 146, 2, 90, 90, NULL, 'belum_berhak', NULL, '2026-01-16 09:57:30', '2026-01-16 09:57:30'),
(172, 149, 2, 90, 90, NULL, 'belum_berhak', NULL, '2026-04-06 09:28:07', '2026-04-06 09:28:07');

-- --------------------------------------------------------

--
-- Table structure for table `kuota_cuti_luar_tanggungan`
--

CREATE TABLE `kuota_cuti_luar_tanggungan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL DEFAULT 6 COMMENT 'ID untuk cuti luar tanggungan',
  `tahun` int(11) NOT NULL,
  `kuota_tahunan` int(11) DEFAULT NULL COMMENT 'Kuota cuti luar tanggungan per tahun (menggunakan max_days dari leave_types)',
  `sisa_kuota` int(11) DEFAULT NULL COMMENT 'Sisa kuota cuti luar tanggungan',
  `catatan` text DEFAULT NULL COMMENT 'Catatan tambahan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kuota_cuti_luar_tanggungan`
--

INSERT INTO `kuota_cuti_luar_tanggungan` (`id`, `user_id`, `leave_type_id`, `tahun`, `kuota_tahunan`, `sisa_kuota`, `catatan`, `created_at`, `updated_at`) VALUES
(4, 3, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:28', '2025-07-07 05:31:28'),
(5, 4, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:28', '2025-07-07 05:31:28'),
(6, 5, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(7, 6, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(8, 7, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(9, 8, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(10, 9, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(11, 10, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(12, 11, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(13, 12, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(14, 13, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(15, 14, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(16, 15, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(17, 16, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(18, 17, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(19, 18, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(20, 19, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(21, 20, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(22, 21, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:31', '2025-07-07 05:31:31'),
(23, 22, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:31', '2025-07-07 05:31:31'),
(24, 23, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:31', '2025-07-07 05:31:31'),
(25, 24, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:31', '2025-07-07 05:31:31'),
(26, 25, 6, 2025, 365, 365, NULL, '2025-07-07 05:31:31', '2025-07-07 05:31:31'),
(79, 27, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:15', '2025-08-19 05:17:15'),
(80, 28, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:15', '2025-08-19 05:17:15'),
(81, 29, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:15', '2025-08-19 05:17:15'),
(82, 30, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:15', '2025-08-19 05:17:15'),
(83, 31, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(84, 32, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(85, 33, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(86, 34, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(87, 35, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(88, 36, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(89, 37, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(90, 38, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(91, 39, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(92, 40, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(93, 41, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(94, 42, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(95, 43, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(96, 44, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(97, 45, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(98, 46, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(99, 47, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(100, 48, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(101, 49, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(102, 50, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(103, 51, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(104, 52, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(105, 53, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(106, 54, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(107, 55, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(108, 56, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(109, 57, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(110, 58, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(111, 59, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(112, 60, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(113, 61, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(114, 62, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(115, 63, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(116, 64, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(117, 65, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(118, 66, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(119, 67, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(120, 68, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(121, 69, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(122, 70, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(123, 71, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(124, 72, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(125, 73, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(126, 74, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(127, 75, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(128, 76, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(129, 77, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(130, 78, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(131, 79, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(132, 80, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(133, 81, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(134, 82, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(135, 83, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(136, 84, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(137, 85, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(138, 86, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(139, 87, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(140, 88, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(141, 89, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(142, 90, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(143, 91, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(144, 92, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(145, 93, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(146, 94, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(147, 95, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(148, 96, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(149, 97, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(150, 98, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(151, 99, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(152, 100, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(153, 101, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(154, 102, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(155, 103, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(156, 104, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(157, 105, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(158, 106, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(159, 107, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(160, 108, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(161, 109, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(162, 110, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(163, 111, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(164, 112, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(165, 113, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(166, 114, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(167, 115, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(168, 116, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(169, 117, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(170, 118, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(171, 119, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(172, 120, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(173, 121, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(174, 122, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(175, 123, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(176, 124, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(177, 125, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(178, 126, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(179, 127, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(180, 128, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(181, 129, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(182, 130, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(183, 131, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(184, 132, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(185, 133, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(186, 134, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(187, 135, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(188, 136, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(189, 137, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(190, 138, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(191, 139, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(192, 140, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(193, 141, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:30', '2025-08-19 05:17:30'),
(194, 142, 6, 2025, 365, 365, NULL, '2025-08-19 05:17:30', '2025-08-19 05:17:30'),
(195, 143, 6, 2025, 365, 365, NULL, '2025-08-20 00:20:33', '2025-08-20 00:20:33'),
(196, 146, 6, 2026, 365, 365, NULL, '2026-01-16 09:57:30', '2026-01-16 09:57:30'),
(197, 143, 6, 2026, 365, 365, NULL, '2026-03-22 09:01:32', '2026-03-22 09:01:32'),
(199, 149, 6, 2026, 365, 365, NULL, '2026-04-06 09:28:07', '2026-04-06 09:28:07');

-- --------------------------------------------------------

--
-- Table structure for table `kuota_cuti_melahirkan`
--

CREATE TABLE `kuota_cuti_melahirkan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL DEFAULT 4 COMMENT 'ID untuk cuti melahirkan',
  `kuota_total` int(11) DEFAULT NULL COMMENT 'Total kuota dalam hari (menggunakan max_days dari leave_types)',
  `jumlah_pengambilan` int(11) NOT NULL DEFAULT 0,
  `sisa_pengambilan` int(11) NOT NULL DEFAULT 1 COMMENT 'Sisa kesempatan mengambil cuti melahirkan',
  `status` enum('tersedia','digunakan','habis') DEFAULT 'tersedia',
  `tanggal_penggunaan` date DEFAULT NULL COMMENT 'Tanggal mulai menggunakan cuti melahirkan',
  `catatan` text DEFAULT NULL COMMENT 'Catatan tambahan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kuota_cuti_melahirkan`
--

INSERT INTO `kuota_cuti_melahirkan` (`id`, `user_id`, `leave_type_id`, `kuota_total`, `jumlah_pengambilan`, `sisa_pengambilan`, `status`, `tanggal_penggunaan`, `catatan`, `created_at`, `updated_at`) VALUES
(4, 3, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:28', '2025-07-07 05:31:28'),
(5, 4, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:28', '2025-07-07 05:31:28'),
(6, 5, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(7, 6, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(8, 7, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(9, 8, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(10, 9, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(11, 10, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(12, 11, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(13, 12, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:29', '2025-07-07 05:31:29'),
(14, 13, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(15, 14, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(16, 15, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(17, 16, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(18, 17, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(19, 18, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(20, 19, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(21, 20, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:30', '2025-07-07 05:31:30'),
(22, 21, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:31', '2025-07-07 05:31:31'),
(23, 22, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:31', '2025-07-07 05:31:31'),
(24, 23, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:31', '2025-07-07 05:31:31'),
(25, 24, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:31', '2025-07-07 05:31:31'),
(26, 25, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-07-07 05:31:31', '2025-07-07 05:31:31'),
(53, 27, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:15', '2025-08-19 05:17:15'),
(54, 28, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:15', '2025-08-19 05:17:15'),
(55, 29, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:15', '2025-08-19 05:17:15'),
(56, 30, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:15', '2025-08-19 05:17:15'),
(57, 31, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(58, 32, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(59, 33, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(60, 34, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(61, 35, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(62, 36, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(63, 37, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(64, 38, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(65, 39, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:16', '2025-08-19 05:17:16'),
(66, 40, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(67, 41, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(68, 42, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(69, 43, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(70, 44, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(71, 45, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(72, 46, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(73, 47, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:17', '2025-08-19 05:17:17'),
(74, 48, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(75, 49, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(76, 50, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(77, 51, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(78, 52, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(79, 53, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(80, 54, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(81, 55, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:18', '2025-08-19 05:17:18'),
(82, 56, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(83, 57, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(84, 58, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(85, 59, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(86, 60, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(87, 61, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(88, 62, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:19', '2025-08-19 05:17:19'),
(89, 63, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(90, 64, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(91, 65, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(92, 66, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(93, 67, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(94, 68, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(95, 69, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(96, 70, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(97, 71, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:20', '2025-08-19 05:17:20'),
(98, 72, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(99, 73, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(100, 74, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(101, 75, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(102, 76, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(103, 77, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(104, 78, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:21', '2025-08-19 05:17:21'),
(105, 79, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(106, 80, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(107, 81, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(108, 82, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(109, 83, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(110, 84, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(111, 85, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:22', '2025-08-19 05:17:22'),
(112, 86, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(113, 87, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(114, 88, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(115, 89, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(116, 90, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(117, 91, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(118, 92, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(119, 93, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:23', '2025-08-19 05:17:23'),
(120, 94, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(121, 95, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(122, 96, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(123, 97, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(124, 98, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(125, 99, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(126, 100, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(127, 101, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:24', '2025-08-19 05:17:24'),
(128, 102, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(129, 103, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(130, 104, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(131, 105, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(132, 106, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(133, 107, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(134, 108, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(135, 109, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:25', '2025-08-19 05:17:25'),
(136, 110, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(137, 111, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(138, 112, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(139, 113, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(140, 114, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(141, 115, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(142, 116, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(143, 117, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:26', '2025-08-19 05:17:26'),
(144, 118, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(145, 119, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(146, 120, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(147, 121, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(148, 122, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(149, 123, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(150, 124, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:27', '2025-08-19 05:17:27'),
(151, 125, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(152, 126, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(153, 127, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(154, 128, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(155, 129, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(156, 130, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(157, 131, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(158, 132, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:28', '2025-08-19 05:17:28'),
(159, 133, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(160, 134, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(161, 135, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(162, 136, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(163, 137, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(164, 138, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(165, 139, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(166, 140, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:29', '2025-08-19 05:17:29'),
(167, 141, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:30', '2025-08-19 05:17:30'),
(168, 142, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-19 05:17:30', '2025-08-19 05:17:30'),
(169, 143, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2025-08-20 00:20:33', '2025-08-20 00:20:33'),
(170, 146, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2026-01-16 09:57:30', '2026-01-16 09:57:30'),
(172, 149, 4, 90, 0, 3, 'tersedia', NULL, NULL, '2026-04-06 09:28:07', '2026-04-06 09:28:07');

-- --------------------------------------------------------

--
-- Table structure for table `kuota_cuti_sakit`
--

CREATE TABLE `kuota_cuti_sakit` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL DEFAULT 3 COMMENT 'ID untuk cuti sakit',
  `tahun` int(11) NOT NULL,
  `kuota_tahunan` int(11) DEFAULT NULL COMMENT 'Kuota cuti sakit per tahun (menggunakan max_days dari leave_types)',
  `sisa_kuota` int(11) DEFAULT NULL COMMENT 'Sisa kuota cuti sakit',
  `catatan` text DEFAULT NULL COMMENT 'Catatan tambahan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kuota_cuti_sakit`
--

INSERT INTO `kuota_cuti_sakit` (`id`, `user_id`, `leave_type_id`, `tahun`, `kuota_tahunan`, `sisa_kuota`, `catatan`, `created_at`, `updated_at`) VALUES
(196, 146, 3, 2026, 14, 14, NULL, '2026-01-16 09:57:30', '2026-01-16 09:57:30'),
(197, 3, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(198, 4, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(199, 5, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(200, 6, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(201, 7, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(202, 8, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(203, 9, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(204, 10, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(205, 11, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(206, 12, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(207, 13, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(208, 14, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(209, 15, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(210, 16, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(211, 17, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(212, 18, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(213, 19, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(214, 20, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(215, 21, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(216, 22, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(217, 23, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(218, 24, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(219, 25, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(220, 27, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(221, 28, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(222, 29, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(223, 30, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(224, 31, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(225, 32, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(226, 33, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(227, 34, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(228, 35, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(229, 36, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(230, 37, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(231, 38, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(232, 39, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(233, 40, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(234, 41, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(235, 42, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(236, 43, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(237, 44, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(238, 46, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(239, 47, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(240, 48, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(241, 49, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(242, 50, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(243, 51, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(244, 52, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(245, 53, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(246, 54, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(247, 55, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(248, 56, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(249, 57, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(250, 58, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(251, 59, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(252, 60, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(253, 61, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(254, 62, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(255, 63, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(256, 64, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(257, 65, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(258, 66, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(259, 67, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(260, 68, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(261, 69, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(262, 70, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(263, 71, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(264, 72, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(265, 73, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(266, 74, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(267, 75, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(268, 76, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(269, 77, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(270, 78, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(271, 79, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(272, 80, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(273, 81, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(274, 82, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(275, 83, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(276, 84, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(277, 85, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(278, 86, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(279, 87, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(280, 88, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(281, 89, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(282, 90, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(283, 91, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(284, 92, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(285, 93, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(286, 94, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(287, 95, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(288, 96, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(289, 97, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(290, 98, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(291, 99, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(292, 100, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(293, 101, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(294, 102, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(295, 103, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(296, 104, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(297, 105, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(298, 108, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(299, 109, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(300, 110, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(301, 111, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(302, 112, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(303, 113, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(304, 114, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(305, 115, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(306, 116, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(307, 117, 3, 2026, 14, 12, NULL, '2026-02-03 01:45:50', '2026-04-06 13:06:48'),
(308, 118, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(309, 119, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(310, 120, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(311, 121, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(312, 122, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(313, 123, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(314, 124, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(315, 125, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(316, 126, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(317, 127, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(318, 128, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(319, 129, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(320, 130, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(321, 131, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(322, 132, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(323, 133, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(324, 134, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(325, 135, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(326, 136, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(327, 137, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(328, 138, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(329, 139, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(330, 140, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(331, 141, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(332, 142, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-02-03 01:45:50'),
(333, 143, 3, 2026, 14, 14, NULL, '2026-02-03 01:45:50', '2026-04-06 13:12:12'),
(335, 149, 3, 2026, 14, 14, NULL, '2026-04-06 09:28:07', '2026-04-06 09:28:07');

-- --------------------------------------------------------

--
-- Table structure for table `leave_balances`
--

CREATE TABLE `leave_balances` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tahun` year(4) NOT NULL,
  `kuota_tahunan` int(11) DEFAULT 12,
  `sisa_kuota` int(11) DEFAULT 12
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_balances`
--

INSERT INTO `leave_balances` (`id`, `user_id`, `tahun`, `kuota_tahunan`, `sisa_kuota`) VALUES
(12, 3, '2025', 12, 6),
(15, 4, '2025', 12, 6),
(18, 5, '2025', 12, 6),
(21, 6, '2025', 12, 6),
(24, 7, '2025', 12, 6),
(27, 8, '2025', 12, 6),
(30, 9, '2025', 12, 6),
(33, 10, '2025', 12, 6),
(36, 11, '2025', 12, 6),
(39, 12, '2025', 12, 6),
(42, 13, '2025', 12, 6),
(45, 14, '2025', 12, 6),
(48, 15, '2025', 12, 6),
(51, 16, '2025', 12, 6),
(54, 17, '2025', 12, 6),
(57, 18, '2025', 12, 6),
(60, 19, '2025', 12, 6),
(63, 20, '2025', 12, 6),
(66, 21, '2025', 12, 6),
(69, 22, '2025', 12, 6),
(72, 23, '2025', 12, 6),
(75, 24, '2025', 12, 6),
(78, 25, '2025', 12, 6),
(159, 27, '2025', 12, 6),
(162, 28, '2025', 12, 6),
(165, 29, '2025', 12, 6),
(168, 30, '2025', 12, 6),
(171, 31, '2025', 12, 6),
(174, 32, '2025', 12, 6),
(177, 33, '2025', 12, 6),
(180, 34, '2025', 12, 6),
(183, 35, '2025', 12, 6),
(186, 36, '2025', 12, 6),
(189, 37, '2025', 12, 6),
(192, 38, '2025', 12, 6),
(195, 39, '2025', 12, 6),
(198, 40, '2025', 12, 6),
(201, 41, '2025', 12, 6),
(204, 42, '2025', 12, 6),
(207, 43, '2025', 12, 6),
(210, 44, '2025', 12, 6),
(213, 45, '2025', 12, 6),
(216, 46, '2025', 12, 6),
(219, 47, '2025', 12, 6),
(222, 48, '2025', 12, 6),
(225, 49, '2025', 12, 6),
(228, 50, '2025', 12, 6),
(231, 51, '2025', 12, 6),
(234, 52, '2025', 12, 6),
(237, 53, '2025', 12, 6),
(240, 54, '2025', 12, 6),
(243, 55, '2025', 12, 6),
(246, 56, '2025', 12, 6),
(249, 57, '2025', 12, 6),
(252, 58, '2025', 12, 6),
(255, 59, '2025', 12, 6),
(258, 60, '2025', 12, 6),
(261, 61, '2025', 12, 6),
(264, 62, '2025', 12, 6),
(267, 63, '2025', 12, 6),
(270, 64, '2025', 12, 6),
(273, 65, '2025', 12, 6),
(276, 66, '2025', 12, 6),
(279, 67, '2025', 12, 6),
(282, 68, '2025', 12, 6),
(285, 69, '2025', 12, 6),
(288, 70, '2025', 12, 6),
(291, 71, '2025', 12, 6),
(294, 72, '2025', 12, 6),
(297, 73, '2025', 12, 6),
(300, 74, '2025', 12, 6),
(303, 75, '2025', 12, 6),
(306, 76, '2025', 12, 6),
(309, 77, '2025', 12, 6),
(312, 78, '2025', 12, 6),
(315, 79, '2025', 12, 6),
(318, 80, '2025', 12, 6),
(321, 81, '2025', 12, 6),
(324, 82, '2025', 12, 6),
(327, 83, '2025', 12, 6),
(330, 84, '2025', 12, 6),
(333, 85, '2025', 12, 6),
(336, 86, '2025', 12, 6),
(339, 87, '2025', 12, 6),
(342, 88, '2025', 12, 6),
(345, 89, '2025', 12, 6),
(348, 90, '2025', 12, 6),
(351, 91, '2025', 12, 6),
(354, 92, '2025', 12, 6),
(357, 93, '2025', 12, 6),
(360, 94, '2025', 12, 6),
(363, 95, '2025', 12, 6),
(366, 96, '2025', 12, 6),
(369, 97, '2025', 12, 6),
(372, 98, '2025', 12, 6),
(375, 99, '2025', 12, 6),
(378, 100, '2025', 12, 6),
(381, 101, '2025', 12, 6),
(384, 102, '2025', 12, 6),
(387, 103, '2025', 12, 6),
(390, 104, '2025', 12, 6),
(393, 105, '2025', 12, 6),
(396, 106, '2025', 12, 6),
(399, 107, '2025', 12, 6),
(402, 108, '2025', 12, 6),
(405, 109, '2025', 12, 6),
(408, 110, '2025', 12, 6),
(411, 111, '2025', 12, 6),
(414, 112, '2025', 12, 6),
(417, 113, '2025', 12, 6),
(420, 114, '2025', 12, 6),
(423, 115, '2025', 12, 6),
(426, 116, '2025', 12, 6),
(429, 117, '2025', 12, 4),
(432, 118, '2025', 12, 6),
(435, 119, '2025', 12, 6),
(438, 120, '2025', 12, 6),
(441, 121, '2025', 12, 6),
(444, 122, '2025', 12, 6),
(447, 123, '2025', 12, 6),
(450, 124, '2025', 12, 6),
(453, 125, '2025', 12, 6),
(456, 126, '2025', 12, 6),
(459, 127, '2025', 12, 6),
(462, 128, '2025', 12, 6),
(465, 129, '2025', 12, 6),
(468, 130, '2025', 12, 6),
(471, 131, '2025', 12, 6),
(474, 132, '2025', 12, 6),
(477, 133, '2025', 12, 6),
(480, 134, '2025', 12, 6),
(483, 135, '2025', 12, 6),
(486, 136, '2025', 12, 6),
(489, 137, '2025', 12, 6),
(492, 138, '2025', 12, 6),
(495, 139, '2025', 12, 6),
(498, 140, '2025', 12, 6),
(501, 141, '2025', 12, 6),
(504, 142, '2025', 12, 6),
(507, 143, '2025', 6, 5),
(511, 3, '2024', 12, 0),
(513, 4, '2024', 12, 0),
(515, 5, '2024', 12, 0),
(517, 6, '2024', 12, 0),
(519, 7, '2024', 12, 0),
(521, 8, '2024', 12, 0),
(523, 9, '2024', 12, 0),
(525, 10, '2024', 12, 0),
(527, 11, '2024', 12, 0),
(529, 12, '2024', 12, 0),
(531, 13, '2024', 12, 0),
(533, 14, '2024', 12, 0),
(535, 15, '2024', 12, 0),
(537, 16, '2024', 12, 0),
(539, 17, '2024', 12, 0),
(541, 18, '2024', 12, 0),
(543, 19, '2024', 12, 0),
(545, 20, '2024', 12, 0),
(547, 21, '2024', 12, 0),
(549, 22, '2024', 12, 0),
(551, 23, '2024', 12, 0),
(553, 24, '2024', 12, 0),
(555, 25, '2024', 12, 0),
(559, 27, '2024', 12, 0),
(561, 28, '2024', 12, 0),
(563, 29, '2024', 12, 0),
(565, 30, '2024', 12, 0),
(567, 31, '2024', 12, 0),
(569, 32, '2024', 12, 0),
(571, 33, '2024', 12, 0),
(573, 34, '2024', 12, 0),
(575, 35, '2024', 12, 0),
(577, 36, '2024', 12, 0),
(579, 37, '2024', 12, 0),
(581, 38, '2024', 12, 0),
(583, 39, '2024', 12, 0),
(585, 40, '2024', 12, 0),
(587, 41, '2024', 12, 0),
(589, 42, '2024', 12, 0),
(591, 43, '2024', 12, 0),
(593, 44, '2024', 12, 0),
(595, 45, '2024', 12, 0),
(597, 46, '2024', 12, 0),
(599, 47, '2024', 12, 0),
(601, 48, '2024', 12, 0),
(603, 49, '2024', 12, 0),
(605, 50, '2024', 12, 0),
(607, 51, '2024', 12, 0),
(609, 52, '2024', 12, 0),
(611, 53, '2024', 12, 0),
(613, 54, '2024', 12, 0),
(615, 55, '2024', 12, 0),
(617, 56, '2024', 12, 0),
(619, 57, '2024', 12, 0),
(621, 58, '2024', 12, 0),
(623, 59, '2024', 12, 0),
(625, 60, '2024', 12, 0),
(627, 61, '2024', 12, 0),
(629, 62, '2024', 12, 0),
(631, 63, '2024', 12, 0),
(633, 64, '2024', 12, 0),
(635, 65, '2024', 12, 0),
(637, 66, '2024', 12, 0),
(639, 67, '2024', 12, 0),
(641, 68, '2024', 12, 0),
(643, 69, '2024', 12, 0),
(645, 70, '2024', 12, 0),
(647, 71, '2024', 12, 0),
(649, 72, '2024', 12, 0),
(651, 73, '2024', 12, 0),
(653, 74, '2024', 12, 0),
(655, 75, '2024', 12, 0),
(657, 76, '2024', 12, 0),
(659, 77, '2024', 12, 0),
(661, 78, '2024', 12, 0),
(663, 79, '2024', 12, 0),
(665, 80, '2024', 12, 0),
(667, 81, '2024', 12, 0),
(669, 82, '2024', 12, 0),
(671, 83, '2024', 12, 0),
(673, 84, '2024', 12, 0),
(675, 85, '2024', 12, 0),
(677, 86, '2024', 12, 0),
(679, 87, '2024', 12, 0),
(681, 88, '2024', 12, 0),
(683, 89, '2024', 12, 0),
(685, 90, '2024', 12, 0),
(687, 91, '2024', 12, 0),
(689, 92, '2024', 12, 0),
(691, 93, '2024', 12, 0),
(693, 94, '2024', 12, 0),
(695, 95, '2024', 12, 0),
(697, 96, '2024', 12, 0),
(699, 97, '2024', 12, 0),
(701, 98, '2024', 12, 0),
(703, 99, '2024', 12, 0),
(705, 100, '2024', 12, 0),
(707, 101, '2024', 12, 0),
(709, 102, '2024', 12, 0),
(711, 103, '2024', 12, 0),
(713, 104, '2024', 12, 0),
(715, 105, '2024', 12, 0),
(717, 107, '2024', 12, 0),
(719, 108, '2024', 12, 0),
(721, 109, '2024', 12, 0),
(723, 110, '2024', 12, 0),
(725, 111, '2024', 12, 0),
(727, 112, '2024', 12, 0),
(729, 113, '2024', 12, 0),
(731, 114, '2024', 12, 0),
(733, 115, '2024', 12, 0),
(735, 116, '2024', 12, 0),
(737, 117, '2024', 12, 0),
(739, 118, '2024', 12, 0),
(741, 119, '2024', 12, 0),
(743, 120, '2024', 12, 0),
(745, 121, '2024', 12, 0),
(747, 122, '2024', 12, 0),
(749, 123, '2024', 12, 0),
(751, 124, '2024', 12, 0),
(753, 125, '2024', 12, 0),
(755, 126, '2024', 12, 0),
(757, 127, '2024', 12, 0),
(759, 128, '2024', 12, 0),
(761, 129, '2024', 12, 0),
(763, 130, '2024', 12, 0),
(765, 131, '2024', 12, 0),
(767, 132, '2024', 12, 0),
(769, 133, '2024', 12, 0),
(771, 134, '2024', 12, 0),
(773, 135, '2024', 12, 0),
(775, 136, '2024', 12, 0),
(777, 137, '2024', 12, 0),
(779, 138, '2024', 12, 0),
(781, 139, '2024', 12, 0),
(783, 140, '2024', 12, 0),
(785, 141, '2024', 12, 0),
(787, 142, '2024', 12, 0),
(796, 143, '2024', 0, 0),
(931, 3, '2026', 12, 12),
(933, 4, '2026', 12, 12),
(935, 5, '2026', 12, 12),
(937, 6, '2026', 12, 12),
(939, 7, '2026', 12, 12),
(941, 8, '2026', 12, 12),
(943, 9, '2026', 12, 12),
(945, 10, '2026', 12, 12),
(947, 11, '2026', 12, 12),
(949, 12, '2026', 12, 12),
(951, 13, '2026', 12, 12),
(953, 14, '2026', 12, 12),
(955, 15, '2026', 12, 12),
(957, 16, '2026', 12, 12),
(959, 17, '2026', 12, 12),
(961, 18, '2026', 12, 12),
(963, 19, '2026', 12, 12),
(965, 20, '2026', 12, 12),
(967, 21, '2026', 12, 12),
(969, 22, '2026', 12, 12),
(971, 23, '2026', 12, 12),
(973, 24, '2026', 12, 12),
(975, 25, '2026', 12, 12),
(979, 27, '2026', 12, 12),
(981, 28, '2026', 12, 12),
(983, 29, '2026', 12, 12),
(985, 30, '2026', 12, 12),
(987, 31, '2026', 12, 12),
(989, 32, '2026', 12, 12),
(991, 33, '2026', 12, 12),
(993, 34, '2026', 12, 12),
(995, 35, '2026', 12, 12),
(997, 36, '2026', 12, 12),
(999, 37, '2026', 12, 12),
(1001, 38, '2026', 12, 12),
(1003, 39, '2026', 12, 12),
(1005, 40, '2026', 12, 12),
(1007, 41, '2026', 12, 12),
(1009, 42, '2026', 12, 12),
(1011, 43, '2026', 12, 12),
(1013, 44, '2026', 12, 12),
(1015, 45, '2026', 12, 12),
(1017, 46, '2026', 12, 12),
(1019, 47, '2026', 12, 12),
(1021, 48, '2026', 12, 12),
(1023, 49, '2026', 12, 12),
(1025, 50, '2026', 12, 12),
(1027, 51, '2026', 12, 12),
(1029, 52, '2026', 12, 12),
(1031, 53, '2026', 12, 12),
(1033, 54, '2026', 12, 12),
(1035, 55, '2026', 12, 12),
(1037, 56, '2026', 12, 12),
(1039, 57, '2026', 12, 12),
(1041, 58, '2026', 12, 12),
(1043, 59, '2026', 12, 12),
(1045, 60, '2026', 12, 12),
(1047, 61, '2026', 12, 12),
(1049, 62, '2026', 12, 12),
(1051, 63, '2026', 12, 12),
(1053, 64, '2026', 12, 12),
(1055, 65, '2026', 12, 12),
(1057, 66, '2026', 12, 12),
(1059, 67, '2026', 12, 12),
(1061, 68, '2026', 12, 12),
(1063, 69, '2026', 12, 12),
(1065, 70, '2026', 12, 12),
(1067, 71, '2026', 12, 12),
(1069, 72, '2026', 12, 12),
(1071, 73, '2026', 12, 12),
(1073, 74, '2026', 12, 12),
(1075, 75, '2026', 12, 12),
(1077, 76, '2026', 12, 12),
(1079, 77, '2026', 12, 12),
(1081, 78, '2026', 12, 12),
(1083, 79, '2026', 12, 12),
(1085, 80, '2026', 12, 12),
(1087, 81, '2026', 12, 12),
(1089, 82, '2026', 12, 12),
(1091, 83, '2026', 12, 12),
(1093, 84, '2026', 12, 12),
(1095, 85, '2026', 12, 12),
(1097, 86, '2026', 12, 12),
(1099, 87, '2026', 12, 12),
(1101, 88, '2026', 12, 12),
(1103, 89, '2026', 12, 12),
(1105, 90, '2026', 12, 12),
(1107, 91, '2026', 12, 12),
(1109, 92, '2026', 12, 12),
(1111, 93, '2026', 12, 12),
(1113, 94, '2026', 12, 12),
(1115, 95, '2026', 12, 12),
(1117, 96, '2026', 12, 12),
(1119, 97, '2026', 12, 12),
(1121, 98, '2026', 12, 12),
(1123, 99, '2026', 12, 12),
(1125, 100, '2026', 12, 12),
(1127, 101, '2026', 12, 12),
(1129, 102, '2026', 12, 12),
(1131, 103, '2026', 12, 12),
(1133, 104, '2026', 12, 12),
(1135, 105, '2026', 12, 12),
(1137, 107, '2026', 12, 12),
(1139, 108, '2026', 12, 12),
(1141, 109, '2026', 12, 12),
(1143, 110, '2026', 12, 12),
(1145, 111, '2026', 12, 12),
(1147, 112, '2026', 12, 12),
(1149, 113, '2026', 12, 12),
(1151, 114, '2026', 12, 12),
(1153, 115, '2026', 12, 12),
(1155, 116, '2026', 12, 12),
(1157, 117, '2026', 12, 12),
(1159, 118, '2026', 12, 12),
(1161, 119, '2026', 12, 12),
(1163, 120, '2026', 12, 12),
(1165, 121, '2026', 12, 12),
(1167, 122, '2026', 12, 12),
(1169, 123, '2026', 12, 12),
(1171, 124, '2026', 12, 12),
(1173, 125, '2026', 12, 12),
(1175, 126, '2026', 12, 12),
(1177, 127, '2026', 12, 12),
(1179, 128, '2026', 12, 12),
(1181, 129, '2026', 12, 12),
(1183, 130, '2026', 12, 12),
(1185, 131, '2026', 12, 12),
(1187, 132, '2026', 12, 12),
(1189, 133, '2026', 12, 12),
(1191, 134, '2026', 12, 12),
(1193, 135, '2026', 12, 12),
(1195, 136, '2026', 12, 12),
(1197, 137, '2026', 12, 12),
(1199, 138, '2026', 12, 12),
(1201, 139, '2026', 12, 12),
(1203, 140, '2026', 12, 12),
(1205, 141, '2026', 12, 12),
(1207, 142, '2026', 12, 12),
(1209, 143, '2026', 12, 12),
(1492, 106, '2024', 12, 0),
(1494, 146, '2024', 12, 0),
(1495, 146, '2025', 6, 6),
(1502, 106, '2026', 12, 12),
(1506, 1, '2026', 12, 12),
(1507, 1, '2024', 12, 12),
(1508, 1, '2025', 12, 12),
(1515, 146, '2026', 12, 12),
(1645, 149, '2024', 0, 0),
(1646, 149, '2025', 6, 6),
(1647, 149, '2026', 12, 12);

-- --------------------------------------------------------

--
-- Table structure for table `leave_documents`
--

CREATE TABLE `leave_documents` (
  `id` int(11) NOT NULL,
  `leave_request_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `document_type` enum('generated','user_signed','admin_signed') NOT NULL,
  `status` enum('draft','active','final') DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `upload_date` datetime DEFAULT NULL COMMENT 'Tanggal upload dokumen',
  `sent_date` datetime DEFAULT NULL COMMENT 'Tanggal pengiriman dokumen'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `atasan_id` int(11) NOT NULL,
  `kasubbag_id` int(11) DEFAULT NULL COMMENT 'ID atasan dengan role=kasubbag yang melakukan approval level 2',
  `kabag_approver_id` int(11) DEFAULT NULL COMMENT 'ID atasan dengan role=kabag yang melakukan approval level 3',
  `sekretaris_approver_id` int(11) DEFAULT NULL COMMENT 'ID atasan dengan role=sekretaris yang melakukan approval level 4',
  `ketua_approver_id` int(11) DEFAULT NULL COMMENT 'ID atasan dengan role=ketua yang dipilih untuk approval final',
  `admin_blankofinal_sender` int(11) DEFAULT NULL COMMENT 'ID Admin yang Mengirim Blanko Final untuk Pengajuan Cuti',
  `user_snapshot_id` int(11) DEFAULT NULL,
  `leave_type_id` int(11) NOT NULL,
  `nomor_surat` varchar(50) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `jumlah_hari` int(11) NOT NULL,
  `alasan` text NOT NULL,
  `alamat_cuti` text DEFAULT NULL,
  `telepon_cuti` varchar(20) DEFAULT NULL,
  `dokumen_pendukung` varchar(255) DEFAULT NULL,
  `catatan_cuti` text DEFAULT NULL,
  `status` enum('draft','pending','pending_kasubbag','pending_kabag','pending_sekretaris','awaiting_pimpinan','pending_admin_upload','approved','rejected','changed','postponed') NOT NULL DEFAULT 'draft',
  `is_completed` tinyint(1) DEFAULT 0,
  `approved_by` int(11) DEFAULT NULL,
  `atasan_approval_date` datetime DEFAULT NULL,
  `kasubbag_approval_date` datetime DEFAULT NULL COMMENT 'Tanggal approval dari kasubbag',
  `kabag_approval_date` datetime DEFAULT NULL COMMENT 'Tanggal approval dari kabag',
  `sekretaris_approval_date` datetime DEFAULT NULL COMMENT 'Tanggal approval dari sekretaris',
  `ketua_approval_date` datetime DEFAULT NULL COMMENT 'Tanggal approval dari ketua',
  `atasan_catatan` text DEFAULT NULL,
  `kasubbag_catatan` text DEFAULT NULL COMMENT 'Catatan dari kasubbag',
  `kabag_catatan` text DEFAULT NULL COMMENT 'Catatan dari kabag',
  `sekretaris_catatan` text DEFAULT NULL COMMENT 'Catatan dari sekretaris',
  `ketua_catatan` text DEFAULT NULL COMMENT 'Catatan dari ketua',
  `approval_date` datetime DEFAULT NULL,
  `catatan_approval` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `blanko_uploaded` tinyint(1) DEFAULT 0 COMMENT 'Status upload blanko yang ditandatangani user',
  `blanko_upload_date` datetime DEFAULT NULL COMMENT 'Tanggal upload blanko user',
  `final_blanko_sent` tinyint(1) DEFAULT 0 COMMENT 'Status pengiriman blanko final ke user',
  `final_blanko_sent_date` datetime DEFAULT NULL COMMENT 'Tanggal pengiriman blanko final',
  `quota_deducted` tinyint(1) DEFAULT 0 COMMENT 'Status pemotongan kuota cuti',
  `jumlah_hari_ditangguhkan` int(11) DEFAULT 0 COMMENT 'Jumlah hari cuti yang ditangguhkan (postponed) oleh admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` int(11) NOT NULL,
  `nama_cuti` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `max_days` int(11) DEFAULT 0,
  `is_akumulatif` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `nama_cuti`, `deskripsi`, `max_days`, `is_akumulatif`) VALUES
(1, 'Cuti Tahunan', 'Cuti tahunan yang menjadi hak pegawai', 12, 1),
(2, 'Cuti Besar', 'Cuti besar setelah masa kerja tertentu', 90, 1),
(3, 'Cuti Sakit', 'Cuti karena sakit dengan surat keterangan dokter', 14, 1),
(4, 'Cuti Melahirkan', 'Cuti melahirkan bagi pegawai wanita', 90, 0),
(5, 'Cuti Karena Alasan Penting', 'Cuti karena alasan penting yang tidak dapat ditunda', 30, 0),
(6, 'Cuti di Luar Tanggungan Negara', 'Cuti di luar tanggungan negara', 365, 1);

-- --------------------------------------------------------

--
-- Table structure for table `migration_logs`
--

CREATE TABLE `migration_logs` (
  `id` int(11) NOT NULL,
  `migration_name` varchar(100) NOT NULL,
  `executed_at` datetime DEFAULT current_timestamp(),
  `status` enum('success','failed') DEFAULT 'success',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migration_logs`
--

INSERT INTO `migration_logs` (`id`, `migration_name`, `executed_at`, `status`, `notes`) VALUES
(1, 'v2_workflow_migration', '2026-02-22 09:14:06', 'success', 'V2 Workflow tables and columns created');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','danger') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `related_leave_id` int(11) DEFAULT NULL COMMENT 'ID pengajuan cuti terkait'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `satker`
--

CREATE TABLE `satker` (
  `id_satker` int(11) NOT NULL,
  `nama_satker` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `satker`
--

INSERT INTO `satker` (`id_satker`, `nama_satker`) VALUES
(1, 'Pengadilan Tinggi Agama Makassar'),
(2, 'Pengadilan Agama Makassar'),
(3, 'Pengadilan Agama Sengkang'),
(4, 'Pengadilan Agama Watampone'),
(5, 'Pengadilan Agama Watansoppeng'),
(6, 'Pengadilan Agama Pinrang'),
(7, 'Pengadilan Agama Sungguminasa'),
(8, 'Pengadilan Agama Maros'),
(9, 'Pengadilan Agama Bulukumba'),
(10, 'Pengadilan Agama Palopo'),
(11, 'Pengadilan Agama Sidenreng Rappang'),
(12, 'Pengadilan Agama Jeneponto'),
(13, 'Pengadilan Agama Barru'),
(14, 'Pengadilan Agama Parepare'),
(15, 'Pengadilan Agama Belopa'),
(16, 'Pengadilan Agama Sinjai'),
(17, 'Pengadilan Agama Makale'),
(18, 'Pengadilan Agama Pangkajene'),
(19, 'Pengadilan Agama Takalar'),
(20, 'Pengadilan Agama Selayar'),
(21, 'Pengadilan Agama Bantaeng'),
(22, 'Pengadilan Agama Enrekang'),
(23, 'Pengadilan Agama Masamba'),
(24, 'Pengadilan Agama Malili');

-- --------------------------------------------------------

--
-- Table structure for table `signature_placeholders`
--

CREATE TABLE `signature_placeholders` (
  `id` int(11) NOT NULL,
  `placeholder_key` varchar(50) NOT NULL,
  `placeholder_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `signature_placeholders`
--

INSERT INTO `signature_placeholders` (`id`, `placeholder_key`, `placeholder_name`, `description`, `section_name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ttd_user', 'Tanda Tangan User', 'Tanda tangan pemohon cuti', 'VI. Alamat Selama Menjalankan Cuti', 1, '2025-07-08 05:54:03', '2025-07-08 05:54:03'),
(2, 'ttd_admin', 'Tanda Tangan Admin', 'Tanda tangan atasan langsung dan pejabat berwenang', 'VII. Pertimbangan Atasan Langsung & VIII. Keputusan Pejabat Berwenang', 1, '2025-07-08 05:54:03', '2025-07-08 05:54:03'),
(3, 'paraf', 'Paraf Petugas Cuti', 'Paraf petugas cuti pada tabel V', 'V. Catatan Cuti', 1, '2025-07-15 00:59:29', '2025-07-15 00:59:29');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL COMMENT 'Nama aksi yang dilakukan',
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Detail aksi dalam format JSON' CHECK (json_valid(`details`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`id`, `action`, `details`, `created_at`) VALUES
(1, 'system_init', '{\"message\": \"System logs table created\", \"version\": \"1.0\"}', '2025-07-30 00:46:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nip` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `jabatan` varchar(100) NOT NULL,
  `golongan` varchar(10) DEFAULT NULL,
  `unit_kerja` int(11) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `atasan` int(11) DEFAULT NULL,
  `user_type` enum('pegawai','atasan','admin') NOT NULL DEFAULT 'pegawai',
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_modified` tinyint(1) DEFAULT 0,
  `last_modified_at` timestamp NULL DEFAULT NULL,
  `failed_login_attempts` int(11) NOT NULL DEFAULT 0,
  `lock_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama`, `nip`, `email`, `jabatan`, `golongan`, `unit_kerja`, `tanggal_masuk`, `atasan`, `user_type`, `is_deleted`, `deleted_at`, `is_modified`, `last_modified_at`, `failed_login_attempts`, `lock_until`, `created_at`, `updated_at`) VALUES
(1, 'admin_pta', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', '-', NULL, 'Admin', '-', 1, '1986-03-01', NULL, 'admin', 0, NULL, 0, NULL, 0, NULL, '2025-06-05 02:30:25', '2026-04-06 14:00:19'),
(3, 'pa_makassar', '$2y$10$iogrdZxHCKZ660ZbJtZINO7zisFbZucrvGB5BH.8zCBi1H.11zOo.', 'Dr. Hj. Hasnaya H. Abd. Rasyid, M.H.', '196712121993032006', NULL, 'Ketua Pengadilan Agama', 'IV/d', 2, '1993-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:28', '2026-03-11 05:55:48'),
(4, 'pa_sengkang', '$2y$10$01LKaFiir2WztHlr8zFYR.LVLKHpIUsOy3XTayjThJZVkOy6kwPba', 'Dra. Heriyah, S.H., M.H.', '196712311993032018', NULL, 'Ketua Pengadilan Agama', 'IV/d', 3, '1993-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:28', '2026-03-11 05:55:48'),
(5, 'pa_watampone', '$2y$10$lRhVQFe7J0/89adUmi6BvurQtIL/oSyhRn4br3xMCNsiwhhPdQt7W', 'Dra. Hj. Nurlinah. K, S.H., M.H.', '196712311994032020', NULL, 'Ketua Pengadilan Agama', 'IV/d', 4, '1994-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:29', '2026-03-11 05:55:48'),
(6, 'pa_watansoppeng', '$2y$10$J5HKnwlA.FDe0kEtkQtv/ekfamwKKWhQsL2iKRXblxwmqgVjzFqla', 'Drs. H. Mursidin, M.H.', '196612311994031059', NULL, 'Ketua Pengadilan Agama', 'IV/d', 5, '1994-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:29', '2026-03-11 05:55:48'),
(7, 'pa_pinrang', '$2y$10$SQ8xty8mgdixcxg60Lg4bOMefsCR/YiO22qsyapHp5/bZADBkXnQG', 'Hadrawati, S.Ag., M.HI.', '197301311998022003', NULL, 'Ketua Pengadilan Agama', 'IV/c', 6, '1998-02-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:29', '2026-04-06 14:05:02'),
(8, 'pa_sungguminasa', '$2y$10$k8mGnsiok2Qyv42ey3r9Lel1R15iAr.c3ZpM16QiFtvhp736BXvoK', 'Abdul Rahman Salam, S.Ag., M.H.', '197302121999031001', NULL, 'Ketua Pengadilan Agama', 'IV/c', 7, '1999-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:29', '2026-03-11 05:55:48'),
(9, 'pa_maros', '$2y$10$tc632MgIdtjHOPYE/lrABOMcCldF4FeDrBpE5xyXR5OjL8h2aogQa', 'A. Muh Yusri Patawari, S.H.I., M.H.', '198001262007041001', NULL, 'Ketua Pengadilan Agama', 'IV/b', 8, '2007-04-01', 1, 'pegawai', 0, NULL, 0, '2025-07-31 00:56:08', 0, NULL, '2025-07-07 05:31:29', '2026-04-06 14:04:21'),
(10, 'pa_bulukumba', '$2y$10$ZqQgiaYE9abSIypzqF0BZuJOvE0VWxE50hgMjSASC951EM0qI94Qa', 'Laila Syahidan, S.Ag.M.H.', '197410172006042002', NULL, 'Ketua Pengadilan Agama', 'IV/b', 9, '2006-04-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:29', '2026-03-11 05:55:48'),
(11, 'pa_palopo', '$2y$10$SnitJICYCMZqxOGnsugYFuwVBDTGWD/BWCoaiIgHe.AJRj2.c7nae', 'Tommi, S.H.I.', '197905172006041005', NULL, 'Ketua Pengadilan Agama', 'IV/b', 10, '2006-04-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:29', '2026-03-11 05:55:48'),
(12, 'pa_sidrap', '$2y$10$S/AZ0GK13xLtoNLbfRudCeMPJogHGkhvQ7W4H0rPUbicUkbx0OyCO', 'Andi Muhammad Yusuf Bakri, S.H.I., M.H.', '197908062005021001', NULL, 'Ketua Pengadilan Agama', 'IV/b', 11, '2005-02-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:29', '2026-03-11 05:55:48'),
(13, 'pa_jeneponto', '$2y$10$3MXHRjqznU6IsmGXNnri9uQpNoVJbqZsNYpNXCi6E69RqMZlyoKlm', 'Fadilah, S.Ag., M.H.', '197408212002122001', NULL, 'Ketua Pengadilan Agama', 'IV/b', 12, '2002-12-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:30', '2026-03-11 05:55:48'),
(14, 'pa_barru', '$2y$10$ELgXUG31fiVJi.3xqUwxxeK6Ro/EW5BtSQ4h76Mb2OCW5cxA9XCZu', 'Maryam Fadhilah Hamdan, S.H.I.', '197805042002122003', NULL, 'Ketua Pengadilan Agama', 'IV/b', 13, '2002-12-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:30', '2026-03-11 05:55:48'),
(15, 'pa_parepare', '$2y$10$XQBfh90Ta8Z1X1X/aEIEouPD6yt.H3mAF8o23lPg8FzupdpVxZ/M2', 'Muhammad Natsir, S.H.I.', '197806252006041002', NULL, 'Ketua Pengadilan Agama', 'IV/b', 14, '2006-04-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:30', '2026-03-11 05:55:48'),
(16, 'pa_belopa', '$2y$10$p/rBoPR8lbeCW6/4i4EZDO8khUGOEGoGR1BEPqTTs36qiZCJtpbxW', 'Irham Riad, S.H.I., M.H.', '197912292006041002', NULL, 'Ketua Pengadilan Agama', 'IV/b', 15, '2006-04-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:30', '2026-03-11 05:55:48'),
(17, 'pa_sinjai', '$2y$10$c1r9sb76MqKjViXWyEyOx.sENPQRFTpUUcQ5ekXxaDFNhl.O4xumK', 'Rokiah Binti Mustaring, S.H.I., M.H.', '198011252009122002', NULL, 'Ketua Pengadilan Agama', 'IV/a', 16, '2009-12-01', 1, 'pegawai', 0, NULL, 0, '2025-07-31 00:56:08', 0, NULL, '2025-07-07 05:31:30', '2026-04-06 14:05:28'),
(18, 'pa_makale', '$2y$10$eW.UlX9NN4uugJnq7rxp9eRQ0m/grubdl.DlvvZz69dvDLe13e9Oe', 'Dr. Mushlih, S.H.I., M.H.', '198004132008051001', NULL, 'Ketua Pengadilan Agama', 'IV/a', 17, '2008-05-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:30', '2026-03-11 05:55:48'),
(19, 'pa_pangkajene', '$2y$10$JTwo9xC/FEgckGlNw7jd0ea/IybV6wECMne3T9dHfY06IKIK/FGn6', 'Dr. Wildana Arsyad, S.H.I., M.H.I.', '198312112007042001', NULL, 'Ketua Pengadilan Agama', 'IV/a', 18, '2007-04-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:30', '2026-03-11 05:55:48'),
(20, 'pa_takalar', '$2y$10$3DQki8OcpoVrmEECwwXK/./K6d9R9eR9d15NFBaKykNT3N2QTqVB2', 'Hapsah, S.Ag., M.H.', '197706302007042001', NULL, 'Ketua Pengadilan Agama', 'IV/a', 19, '2007-04-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:30', '2026-03-11 05:55:48'),
(21, 'pa_selayar', '$2y$10$4lzeg.XOG907Xiiif3F.5OMZPunMwaFeEKv.2YJUgzImUUPEO2aiG', 'Rusni, S.H.I., M.H.', '197906012007042001', NULL, 'Ketua Pengadilan Agama', 'IV/a', 20, '2007-04-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:31', '2026-03-11 05:55:48'),
(22, 'pa_bantaeng', '$2y$10$mpbuDrys3c8zoJPZtVx8auk.2mP1DNLEzAHm192NGLH17aNTs.9rq', 'Amirullah Arsyad, S.H.I.,M.H.', '198207072007041001', NULL, 'Ketua Pengadilan Agama', 'IV/a', 21, '2007-04-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:31', '2026-03-11 05:55:48'),
(23, 'pa_enrekang', '$2y$10$w4B7vrwgrRqltG38aNNbAu2PE10HnvFmytFMybs5L66sFvud1laTq', 'Dr Amin Bahroni, S.H.I., M.H.', '197705152007041001', NULL, 'Ketua Pengadilan Agama', 'IV/a', 22, '2007-04-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:31', '2026-03-11 05:55:48'),
(24, 'pa_masamba', '$2y$10$eB/qYTeED8AdCmfB58M5cOZmfXQ/h8TA3J6z7zJbwRCsihpVyfmjO', 'Nirwana, S.H.I., M.H.', '198212012008052001', NULL, 'Ketua Pengadilan Agama', 'IV/a', 23, '2008-05-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:31', '2026-03-11 05:55:48'),
(25, 'pa_malili', '$2y$10$b1jDQnGMeUMCO28w2tNOke5p/psg9soX2Eg9MqjzCIFqAXxhuTtZy', 'Rajiman, S.H.I., M.H.', '198210102007041001', NULL, 'Ketua Pengadilan Agama', 'IV/a', 24, '2007-04-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-07-07 05:31:31', '2026-03-11 05:55:48'),
(27, '195901311990031001', '$2y$10$VHfHOB1Wu7L9usGO622kiOye7RjN/ulx8AklLY8Ja5boAQkvBICxC', 'Drs. Muhammad Alwi, M.H.', '195901311990031001', NULL, 'Wakil Ketua', 'IV/e', 1, '1990-03-01', 1, 'atasan', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:15', '2026-03-11 06:44:13'),
(28, '196012311987031054', '$2y$10$bL6To7pg2XhjCfmqJ6FeeeDb8oatemHxIUXvB1kY/SpnxDPPGJ.kG', 'Drs. Iskandar, S.H.', '196012311987031054', NULL, 'Hakim Tinggi', 'IV/e', 1, '1987-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:15', '2026-03-11 05:16:19'),
(29, '196303101992032008', '$2y$10$z0mW4pNfO4jWXqXr6ZbSJuq6O6lFSQ9hhHDCm0YHr1gjf9dX6iVyy', 'Dra. Dzakiyyah, M.H.', '196303101992032008', NULL, 'Hakim Tinggi', 'IV/e', 1, '1992-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:15', '2026-03-11 05:16:19'),
(30, '196504201993031002', '$2y$10$MOyvYFeA8ThwZpARZL90X.9s5mhspW8LfoOi5ngJa1.Ug0EDwzea2', 'Drs Samarul Falah, M.H.', '196504201993031002', NULL, 'Hakim Tinggi', 'IV/e', 1, '1993-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:15', '2026-03-11 05:16:19'),
(31, '196305051990032005', '$2y$10$UqekPVdPZxltx0fOvEQNlekmULMr8ah4w0OcNm9RNH5wttyQAUCd6', 'Dra. St. Aminah, M.H.', '196305051990032005', NULL, 'Hakim Tinggi', 'IV/e', 1, '1990-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:15', '2026-03-11 05:16:19'),
(32, '195901081987032002', '$2y$10$w14Q1MRSbk25krEg7fFu4ufrefabkhduQuiltYzszGoPgv6ovGgz6', 'Dra. Nurcaya Hi Mufti, M.H.', '195901081987032002', NULL, 'Hakim Tinggi', 'IV/e', 1, '1987-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:16', '2026-03-11 05:16:19'),
(33, '196406241990032002', '$2y$10$Cdx2YTaIYHDzb16sqttSd.JZVeT2J5FMdkxcVIzKNe/8grj8wyhSa', 'Nuraeni. S, S.H., M.H.', '196406241990032002', NULL, 'Hakim Tinggi', 'IV/e', 1, '1990-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:16', '2026-03-11 05:16:19'),
(34, '196012311989032012', '$2y$10$vhf7Vvqsc3Hu5FrLy3KCZuxEJ8dOV3V5qTI/4GhGvgFBVtQ43VMOi', 'Dra. St. Mawaidah, S.H., M.H.', '196012311989032012', NULL, 'Hakim Tinggi', 'IV/e', 1, '1989-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:16', '2026-03-11 05:16:19'),
(35, '196303171991031003', '$2y$10$xH5/LLmNFBKK5kY8iaaZkuNZqJSqJwwAjOKtbvQSqjUPF1YSf26Fi', 'Drs. Syahidal', '196303171991031003', NULL, 'Hakim Tinggi', 'IV/e', 1, '1991-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:16', '2026-03-11 05:16:19'),
(36, '195907311982031001', '$2y$10$TAoGggcxCs0h0Woj/5vD1OEFdT27NK9rNTDChMOXIKLFlJfhke67K', 'Drs. M. Anas Malik, S.H., M.H.', '195907311982031001', NULL, 'Hakim Tinggi', 'IV/e', 1, '1982-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:16', '2026-03-11 05:16:19'),
(37, '195812311984032004', '$2y$10$gEMnh/eYT58yS0vPseuv8.0J33Jd4MjsGfoHYSzGBpaAkRFSBPGfG', 'Dra. Kamariah, S.H., M.H.', '195812311984032004', NULL, 'Hakim Tinggi', 'IV/e', 1, '1984-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:16', '2026-03-11 05:16:19'),
(38, '196012311983031049', '$2y$10$xce94gS.geh9lIjsH0p.FO4JJ.n5DlcVstjqGJsNzNEpWTHGMXa/a', 'Drs Mahmud, S.H., M.H.', '196012311983031049', NULL, 'Hakim Tinggi', 'IV/e', 1, '1983-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:16', '2026-03-11 05:16:19'),
(39, '195912311988031025', '$2y$10$Lmuq9U3mLVoqFgjSrIAvfuIzWeI4P4Ur8FMgvtAt4f02C8SfTmXgi', 'Drs. Hasbi, M.H.', '195912311988031025', NULL, 'Hakim Tinggi', 'IV/e', 1, '1988-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:16', '2026-03-11 05:16:19'),
(40, '196303171992032002', '$2y$10$lyYTFmJqGNpEt3fEX.8YBO3n8Fl/4dYTmNbOVEs7B/IU9OFcDloNO', 'Dra. Martina Budiana Mulya, M.H.', '196303171992032002', NULL, 'Hakim Tinggi', 'IV/d', 1, '1992-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:17', '2026-03-11 05:16:19'),
(41, '196408201993032002', '$2y$10$7.RRoNQJNFydMcuEbAg7De2xkKdZRI5Wq0oRqQMdiXqbhU5G07y2.', 'Dra. Fatmah Abujahja', '196408201993032002', NULL, 'Hakim Tinggi', 'IV/d', 1, '1993-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:17', '2026-03-11 05:16:19'),
(42, '196812291994031005', '$2y$10$BTBj4Pky149Rme5GfZkqWudbQG56EDNAo/cWJwfz.t1bpslVCE.yy', 'Drs Gunawan, M.H.', '196812291994031005', NULL, 'Hakim Tinggi', 'IV/d', 1, '1994-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:17', '2026-03-11 05:16:19'),
(43, '196410041994031004', '$2y$10$nKWL/M9LagsNBqFfYGv9..SLZ6xzzsq4/ltYEEGE4BoezQsVsv.My', 'Dr Hasanuddin, S.H., M.H.', '196410041994031004', NULL, 'Panitera', 'IV/c', 1, '1994-03-01', 1, 'atasan', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:17', '2026-03-11 05:16:19'),
(44, '197011021997031001', '$2y$10$ahM0pWljctGFnWs9NjEyluh2TYGa7WVXXcekbFTO3AlyRkhloZiT.', 'Dr. Abdul Mutalip, S.Ag., S.H., M.H.', '197011021997031001', NULL, 'Sekretaris', 'IV/c', 1, '1997-03-01', 1, 'atasan', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:17', '2026-04-06 13:47:47'),
(45, '197803091998031002', '$2y$10$rq306H9kJbrdPBXJb8HX7u.upVU61998uvux1Zsr18/n2CvmJESaS', 'Dr. Muhammad Busyaeri, S.H., M.H.', '197803091998031002', NULL, 'Kepala Bagian Perencanaan dan Kepegawaian', 'IV/b', 1, '1998-03-01', 4, 'atasan', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:17', '2026-04-06 13:58:46'),
(46, '196907162003121003', '$2y$10$su1lEwQipDX/P.pshMxukuDEAzoDM.Hjqdqoxg/qomnCb95EWIoou', 'Drs. Muhammad Amin, M.A.', '196907162003121003', NULL, 'Kepala Bagian Umum dan Keuangan', 'IV/b', 1, '2003-12-01', 4, 'atasan', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:17', '2026-03-11 06:38:02'),
(47, '197211101998022002', '$2y$10$Nhy47eJX2nJj4IJ9bcy1huAUs7C2UGDc2pUKSwtzzNmhEwMGNGiNO', 'Nurbaya, S.Ag., M.H.I.', '197211101998022002', NULL, 'Panitera Muda Hukum', 'IV/a', 1, '1998-02-01', 3, 'atasan', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:17', '2026-03-11 05:16:19'),
(48, '196512081993031007', '$2y$10$Bmfv9H3CjlJiupgogVFMOOZW6pKABnsB0g/0/mGEyTzRQM8IHgdJG', 'Hasbi, S.H., M.H.', '196512081993031007', NULL, 'Panitera Muda Banding', 'IV/a', 1, '1993-03-01', 3, 'atasan', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:18', '2026-03-11 05:16:19'),
(49, '196703111992031004', '$2y$10$S6lwFwCkKEfhcPGiT9IV2Os77T/r.3lhrqZcKXHZRtgWf24YKa3pa', 'Drs. Abdul Samad, M.H.', '196703111992031004', NULL, 'Hakim Yustisial', 'IV/d', 1, '1992-03-01', 1, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:18', '2026-03-11 05:55:48'),
(50, '196308221994031003', '$2y$10$/sZ70NCPtjFcwpe8WD.zU.d6eaYzO3qpIS1iu.Uzo7zQOoTb6P/Du', 'Sudirman, S.H.', '196308221994031003', NULL, 'Panitera Pengganti', 'IV/b', 1, '1994-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:18', '2026-03-11 05:16:19'),
(51, '197504122000031002', '$2y$10$866fo8GDp/hhT/ixfxlbCOloHt0WUeS5TWO6XHOZG7ZF1G/DcKaD6', 'Arifin, S.Ag., M.H.', '197504122000031002', NULL, 'Panitera Pengganti', 'IV/b', 1, '2000-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:18', '2026-03-11 05:16:19'),
(52, '197505171999031003', '$2y$10$MJcM0XGLK8Gci7.f432LTui6ZB98M7zZnLH1mKTEYBJsqD26hNaPG', 'Patahuddin Azis, S.Ag.', '197505171999031003', NULL, 'Panitera Pengganti', 'IV/b', 1, '1999-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:18', '2026-03-11 05:16:19'),
(53, '197311102001121006', '$2y$10$25SNmgPN97UZWF5orFM8JOHECr.pvRouZ0x7ZZj//gmhMhfXyrSzm', 'Asir Pasimbong Alo, S.Ag., M.H.', '197311102001121006', NULL, 'Panitera Pengganti', 'IV/b', 1, '2001-12-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:18', '2026-03-11 05:16:19'),
(54, '197007142001122002', '$2y$10$Pi.p6GdcR99B07/4xXdVJ.ZT5cdjjjjn.x5Pl4fX2lmE9kimB8BhS', 'Khaerawati Abdullah, S.Ag., S.H., M.H.', '197007142001122002', NULL, 'Panitera Pengganti', 'IV/a', 1, '2001-12-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:18', '2026-03-11 05:16:19'),
(55, '196410101993032002', '$2y$10$NvNh00wo5vI8M4k4nAkI2OR3Akc3FEmPgD8pVdudc00oVGsN3jz2K', 'Hartinah, S.H., M.H.', '196410101993032002', NULL, 'Panitera Pengganti', 'IV/a', 1, '1993-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:18', '2026-03-11 05:16:19'),
(56, '196412011988011001', '$2y$10$eJDR7cqYAc9BDejMqxi7kO3qJ/8PvOAbYZCjibydP4CtVxAIZUs/K', 'Haerul Ahmad, S.H., M.H.', '196412011988011001', NULL, 'Panitera Pengganti', 'IV/a', 1, '1988-01-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:19', '2026-03-11 05:16:19'),
(57, '197404252000121001', '$2y$10$oEuWBdd0x.NSjH05M3HI8.MvXBRBN4jcMPth6I0qZp0.iSpvuAxL2', 'Taufiq Hasyim, S.Ag., M.H.', '197404252000121001', NULL, 'Panitera Pengganti', 'IV/a', 1, '2000-12-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:19', '2026-03-11 05:16:19'),
(58, '196310111991032002', '$2y$10$aAxqBWrFEuqEudiCcdEhWegEUB0CHGqgh4TWimhsNuWTt8/E07cBu', 'Dra. Hunaena, M.H.', '196310111991032002', NULL, 'Panitera Pengganti', 'IV/a', 1, '1991-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:19', '2026-03-11 05:16:19'),
(59, '196802021997032002', '$2y$10$PSb3LQhQlPNteGB59NWBIOrX4SjeBDIflJzFAAElw5SG.ZI5Gx33O', 'Dra. Musafirah, M.H.', '196802021997032002', NULL, 'Panitera Pengganti', 'IV/a', 1, '1997-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:19', '2026-03-11 05:16:19'),
(60, '196801241998021001', '$2y$10$1I/HNcGBkvb.zFxVPb9Uf.V92WIHOsovQjbV89MidITkXdtkKjosy', 'Muh. Rais Naim, S.H., S.Ag.', '196801241998021001', NULL, 'Panitera Pengganti', 'IV/a', 1, '1998-02-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:19', '2026-03-11 05:16:19'),
(61, '196612311990021002', '$2y$10$RPyN3025HUGJJPS7/2i0PeKvWcbwrYNZz61x/JXIQM1DssAceTp0C', 'Husain, S.H., M.H.', '196612311990021002', NULL, 'Panitera Pengganti', 'IV/a', 1, '1990-02-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:19', '2026-03-11 05:16:19'),
(62, '196701081994021001', '$2y$10$awNiWTtR4.CCS7iUH/I5OuYSHKlt6Qu0LLiLNd8KRdc0rDPC3Rvvm', 'Muhammadiah, S.H., M.H.', '196701081994021001', NULL, 'Panitera Pengganti', 'IV/a', 1, '1994-02-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:19', '2026-03-11 05:16:19'),
(63, '196612311993031044', '$2y$10$dpTQTao9gLBw4jkotNayXO3YQq5XkRYPfDY0NB1X28Aci3xXnFkC6', 'Drs. Amir, M.H.', '196612311993031044', NULL, 'Panitera Pengganti', 'IV/a', 1, '1993-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2026-03-11 05:16:19'),
(64, '196812151997031001', '$2y$10$QCrsBcY58N2wptDrxfhl..o4nJS94ddB/9pXqs0iq2gDWfSZRdfpq', 'Drs. Tawakkal, M.H.', '196812151997031001', NULL, 'Panitera Pengganti', 'IV/a', 1, '1997-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2026-03-11 05:16:19'),
(65, '197203051995031002', '$2y$10$xyxHpMcTOKCHBr1UyOVxe.nAa8fwTMU1Tqv/q0RHmbC7/pa5LmfPe', 'Akyadi, S.IP., S.H.I., M.H.', '197203051995031002', NULL, 'Panitera Pengganti', 'IV/a', 1, '1995-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2026-03-11 05:16:19'),
(66, '196908232000032007', '$2y$10$D3s5dZePrkTZE4HAkFrQa.hQI7Q4o.NKafjXsv22wR/U6E1d3XlUG', 'Fatimah A. D., S.H., M.H.', '196908232000032007', NULL, 'Panitera Pengganti', 'IV/a', 1, '2000-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2026-03-11 05:16:19'),
(67, '196512311992031066', '$2y$10$5ky.rf2NpcHqF0pzlcz/pe1MdXx4TWIpiHOCjgau2fgsAwfRG5a0a', 'Drs. Hamzah Appas, S.H., M.H.', '196512311992031066', NULL, 'Panitera Pengganti', 'IV/a', 1, '1992-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2026-03-11 05:16:19'),
(68, '196412311994031050', '$2y$10$iOae46HAAexjW78ayMtame3PwUSynYxbmG4wWYpXahWJFBjhFflKS', 'Drs. M. Idris, S.H., M.H.', '196412311994031050', NULL, 'Panitera Pengganti', 'IV/a', 1, '1994-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2026-03-11 05:16:19'),
(69, '198105252006041005', '$2y$10$w9qXcjboRPQ.MLWSe9XTyuUftNrc.XKZF8HJbnUNW.ES/MBSSPIti', 'Muhammad Iqbal Yunus, S.H.I., M.H.', '198105252006041005', NULL, 'Panitera Pengganti', 'IV/a', 1, '2006-04-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2026-03-11 05:16:19'),
(70, '196608071994012001', '$2y$10$90tuomiI.KXUd6ulXchd.OjfOGIOmXEZj3II7fX4omVF/vvKlJtlu', 'Dra. Hasna Mohammad Tang', '196608071994012001', NULL, 'Panitera Pengganti', 'III/d', 1, '1994-01-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2026-03-11 05:16:19'),
(71, '197303122001122003', '$2y$10$ozqoDm62F/9RDlgstqaW8OpcqVZzKpMQq2wHkYvlhw3KRXgEwkiKa', 'Nurul Jamaliah, S.Ag.', '197303122001122003', NULL, 'Panitera Pengganti', 'III/d', 1, '2001-12-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2026-03-11 05:16:19'),
(72, '196308251992032004', '$2y$10$oXDpfjo5/uZOBzOzWpf41O0HG8tomEW9snI87aYexjjmcq2oYJkv.', 'Dra. Muzdalifah, S.H.', '196308251992032004', NULL, 'Panitera Pengganti', 'III/d', 1, '1992-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:21', '2026-03-11 05:16:19'),
(73, '196806041990032005', '$2y$10$MVjiBTv3abgFUtzRpzbWleKjyeucZiBNnKJOLCW1RlR4GAMieAg1e', 'Mukarramah Saleh, S.H.', '196806041990032005', NULL, 'Panitera Pengganti', 'III/d', 1, '1990-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:21', '2026-03-11 05:16:19'),
(74, '196603151997032001', '$2y$10$73hnWTqKtmyv3/By8BdVOeDapXsyjcpAOg2bjEgro7dJBFfvpwgdW', 'Dra. Haisah, S.H.', '196603151997032001', NULL, 'Panitera Pengganti', 'III/d', 1, '1997-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:21', '2026-03-11 05:16:19'),
(75, '196507142003021001', '$2y$10$excCHXIKiSl5TSK7cjeKpuGJpBWF3h2n8HbXVJqEv7HHDwpl99DeS', 'Ibrahim, S.H.', '196507142003021001', NULL, 'Panitera Pengganti', 'III/d', 1, '2003-02-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:21', '2026-03-11 05:16:19'),
(76, '197411091994031002', '$2y$10$0XMDWkfGMGYCxxEpHLoB0eRevLYvADZ8Clgez5rVKOfQJhB6LW0Fm', 'Muh. Sabir, S.H', '197411091994031002', NULL, 'Panitera Pengganti', 'III/d', 1, '1994-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:21', '2026-03-11 05:16:19'),
(77, '196912311994031024', '$2y$10$/D.LKKZ2cD6zZlMd.eF2duFdupA5IScaTkY4gurZdjhcXvgBgp4fe', 'Drs., Syamsuddin', '196912311994031024', NULL, 'Panitera Pengganti', 'III/d', 1, '1994-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:21', '2026-03-11 05:16:19'),
(78, '197009121992031004', '$2y$10$PdeEuOdCBZB6ZzeE6IL4l.Nc57mWXdFufqRLzIwmKIyy1rSpdOpAW', 'Bintang, S.H.', '197009121992031004', NULL, 'Panitera Pengganti', 'III/d', 1, '1992-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:21', '2026-03-11 05:16:19'),
(79, '197010241997032001', '$2y$10$jmKb6r.SIHmYa0uPeeRIWuInw4l4QAdxwFw5ritTxuwbr4c4haWx.', 'Dra. Suherlina', '197010241997032001', NULL, 'Panitera Pengganti', 'III/d', 1, '1997-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:22', '2026-03-11 05:16:19'),
(80, '196704121994032002', '$2y$10$CBBfbIOWGb3QHnuv0t929uauITrIF9ep/tEkO3NAblAC7MwsYFifu', 'Dra. Rosmini', '196704121994032002', NULL, 'Panitera Pengganti', 'III/d', 1, '1994-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:22', '2026-03-11 05:16:19'),
(81, '197309151994021002', '$2y$10$Rk.erHOjacXCzfUe3/hLruO0qA87M2eTdFlJk73p42BzgiymBnyJi', 'Sulfian P, S.Ag.', '197309151994021002', NULL, 'Panitera Pengganti', 'III/d', 1, '1994-02-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:22', '2026-03-11 05:16:19'),
(82, '197311052001121001', '$2y$10$pZYQ4wI4s642dTaMzu3yiuLMLTfkmGLqtuLrWrys0zqPnLqxJr2AK', 'Andi Suardi, S. Ag.', '197311052001121001', NULL, 'Panitera Pengganti', 'III/d', 1, '2001-12-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:22', '2026-03-11 05:16:19'),
(83, '197012211994031005', '$2y$10$t8NLj.EHrLNhl0XUyDtlQeb9FryRkvettjkWqpT10IuAchSi44n4m', 'Salahuddin Saleh, S.H.', '197012211994031005', NULL, 'Panitera Pengganti', 'III/d', 1, '1994-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:22', '2026-03-11 05:16:19'),
(84, '196701261994021003', '$2y$10$7oNt/WZcjqocS/J0XHn2/.DiuJmNOd0CFag/VcuTv7UWs9krX1owa', 'Ibrahim Thoai, S.H.', '196701261994021003', NULL, 'Panitera Pengganti', 'III/d', 1, '1994-02-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:22', '2026-03-11 05:16:19'),
(85, '196512081995032001', '$2y$10$rh9nyQd3xrUDxMjHjIcySeGRrwoxqu0CykvMrw8KOSpA2EFwS8PkO', 'Dra. Wahda', '196512081995032001', NULL, 'Panitera Pengganti', 'III/d', 1, '1995-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:22', '2026-03-11 05:16:19'),
(86, '196910051999032002', '$2y$10$7X3Hv6ZENQcgSQNl/B2T3.pNi1vi3IuYa7qGFhwbi1kT8Q/kawfMC', 'Annisa, S.H.', '196910051999032002', NULL, 'Panitera Pengganti', 'III/d', 1, '1999-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:22', '2026-03-11 05:16:19'),
(87, '196709231989032001', '$2y$10$rsI40/D0blLw21i99.pTK.BK1qflqCys9BjjdiNq.GDcnLpfSQXx6', 'Rahmawati, S.Ag.', '196709231989032001', NULL, 'Panitera Pengganti', 'III/d', 1, '1989-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:23', '2026-03-11 05:16:19'),
(88, '196911301993031003', '$2y$10$IkA/kDN.2PeSGiE/vplU.ejCK/CYxWUri8m7dS8hdzjE9n1tEjT6W', 'Abdul Rahman, S.H.', '196911301993031003', NULL, 'Panitera Pengganti', 'III/d', 1, '1993-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:23', '2026-03-11 05:16:19'),
(89, '197112242002122001', '$2y$10$tbUO5vx4F1El4nna36pTXe62WOB4xHJNhUeJWZIhPsFQkRRAaEN2G', 'Andi Tenri, S.Ag.', '197112242002122001', NULL, 'Panitera Pengganti', 'III/d', 1, '2002-12-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:23', '2026-03-11 05:16:19'),
(90, '196705151994032006', '$2y$10$ELUHG6QCWZl9Anl5TPh7SONbtEGZoP/lrczB2Yak3ZxRptgD4nD2.', 'Dra. St. Syahribulan', '196705151994032006', NULL, 'Panitera Pengganti', 'III/d', 1, '1994-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:23', '2026-03-11 05:16:19'),
(91, '196708011992031003', '$2y$10$UKFJLRLsY/tbfagYBQKJnug2/FIG/D8FMnvEsNz2oNk0vlMUhPehy', 'Hayad Jusa, S.Ag.', '196708011992031003', NULL, 'Panitera Pengganti', 'III/d', 1, '1992-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:23', '2026-03-11 05:16:19'),
(92, '196408081994021001', '$2y$10$6zNKMKD2Elh2jR1FiKruXuaTzhH8DHI2wTf.V3bb7bS1zL1EeEccq', 'Drs. Istambul', '196408081994021001', NULL, 'Panitera Pengganti', 'III/d', 1, '1994-02-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:23', '2026-03-11 05:16:19'),
(93, '196509091994022001', '$2y$10$.Z4SrikW88QR19MVqCrlq.nBunIRka8jGGVbO6I7sPiYql9lEhHG.', 'Haryati, S.H.', '196509091994022001', NULL, 'Panitera Pengganti', 'III/d', 1, '1994-02-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:23', '2026-03-11 05:16:19'),
(94, '196604221994021001', '$2y$10$dtGJMDqWvkFdk7CQMmub2OMGSyO3DDZBof.V0UCS.t1EUqAkcuPP2', 'Aris, S.H.', '196604221994021001', NULL, 'Panitera Pengganti', 'III/d', 1, '1994-02-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:24', '2026-03-11 05:16:19'),
(95, '196312312001122005', '$2y$10$efge57SroUnXdaYGkKyy7u535GBbQ/jL2jF8uW.8LCTXVijhNf7wa', 'Dra. St. Kasmiah', '196312312001122005', NULL, 'Panitera Pengganti', 'III/d', 1, '2001-12-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:24', '2026-03-11 05:16:19'),
(96, '196602101994022001', '$2y$10$.35T7jvNZBD4vdyVvJdbNeNyo1FCwxqhr/lHqU3v7LaCGHrU6PzK6', 'Dra. Jasrawati', '196602101994022001', NULL, 'Panitera Pengganti', 'III/d', 1, '1994-02-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:24', '2026-03-11 05:16:19'),
(97, '196612081995032001', '$2y$10$BB3qML8Z4ZTd9my1VkAJAu/.lprdCntR0ov3OPkVycbjVaU0EHH2C', 'Dra. Sehati', '196612081995032001', NULL, 'Panitera Pengganti', 'III/d', 1, '1995-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:24', '2026-03-11 05:16:19'),
(98, '196701111993032003', '$2y$10$OnLbnJoaL6s6jtpm9sSAg.vnfWPm9L4g4/SgO.wJR0WaWWXR1JDBW', 'Dra. Fitriani', '196701111993032003', NULL, 'Panitera Pengganti', 'III/d', 1, '1993-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:24', '2026-03-11 05:16:19'),
(99, '196612311987031017', '$2y$10$9Jzw9mLZmyVl98E9jMlEH.OGyAm/mT36Y9.1aLyfL1z88Sn878ivK', 'A. Napi, S.Ag.', '196612311987031017', NULL, 'Panitera Pengganti', 'III/d', 1, '1987-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:24', '2026-03-11 05:16:19'),
(100, '196712311994012002', '$2y$10$.hYDwL/gWPQYXwmTvmqtB.R4y5QaoD1XARYuWO820BtYvaiKevMQm', 'Dra Nursyaya', '196712311994012002', NULL, 'Panitera Pengganti', 'III/d', 1, '1994-01-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:24', '2026-03-11 05:16:19'),
(101, '196812081994021002', '$2y$10$HeihR9ewCpKb3aqzEDL3Z.UundJKc0ofjQeos6h.CSYVZ7qeLw6Zu', 'Andi M. Zulkarnain Chalid, S.H.', '196812081994021002', NULL, 'Panitera Pengganti', 'III/d', 1, '1994-02-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:24', '2026-03-11 05:16:19'),
(102, '197103051998032002', '$2y$10$LUF69SxwtShXbLO/TGEnauvgfk4LRDkGDO.yNJHpd56qqQHvwY8YS', 'Nur Intang, S.Ag.', '197103051998032002', NULL, 'Panitera Pengganti', 'III/d', 1, '1998-03-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:25', '2026-03-11 05:16:19'),
(103, '197112201992022001', '$2y$10$xRperXqFi62UYXlmsb15wea2KSO4S8iciZKeKD3U4ujFXpfgUqVNe', 'Nur Qalbi Patawari, S.Ag.', '197112201992022001', NULL, 'Panitera Pengganti', 'III/d', 1, '1992-02-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:25', '2026-03-11 05:16:19'),
(104, '196810012014082003', '$2y$10$O2uS3iBb7AipwCAn6XO5LubDo8RvfTfVq1JY/X76zzzfKX044CATW', 'Dra. Munirah', '196810012014082003', NULL, 'Panitera Pengganti', 'III/c', 1, '2014-08-01', 3, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:25', '2026-03-11 05:16:19'),
(105, '197504072006042001', '$2y$10$n6DumEQAXqIDCvsG/mF94O0vPLfmRgNixk6ULZWJr1xSeuO6qyb7e', 'Nailah Yahya, S.Ag., M.Ag.', '197504072006042001', NULL, 'Kepala Subbagian, Subbagian Rencana Program dan Anggaran', 'IV/a', 1, '2006-04-01', 5, 'atasan', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:25', '2026-03-11 05:16:19'),
(106, '198508012011011010', '$2y$10$ySl1r5ilaWx4MvnF1fdmXu8v/kBDGQ8PZ7yiQ9ZZq1iOkjh44MNWi', 'Verry Setya Widyatama, S.Kom.', '198508012011011010', NULL, 'Kepala Subbagian, Subbagian Kepegawaian dan Teknologi Informasi', 'III/d', 1, '2011-01-01', NULL, 'atasan', 1, '2026-03-02 04:07:40', 0, NULL, 0, NULL, '2025-08-19 05:17:25', '2026-03-02 04:07:40'),
(107, '198409252009121004', '$2y$10$z8VZ9EvcrvA1dnWCvlDNpOk7vzWAi4Yi.OYKdamalkYOpNLGxaXvq', 'Muhammad Silmi, S.Kom.', '198409252009121004', NULL, 'Kepala Subbagian, Subbagian Tata Usaha dan Rumah Tangga', 'III/d', 1, '2009-12-01', 6, 'atasan', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:25', '2026-04-06 14:05:51'),
(108, '198009252005022001', '$2y$10$n1pu0c3x6v9vpAXGONJHyuHhKFGU76fujMAQkRZS6u3tsu5PrN1Qe', 'Nur Azizah Zainal, S.E.', '198009252005022001', NULL, 'Kepala Subbagian, Subbagian Keuangan dan Pelaporan', 'III/d', 1, '2005-02-01', 6, 'atasan', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:25', '2026-03-11 05:16:19'),
(109, '197103101998032011', '$2y$10$OA2WoRqw9nAuT4P4GYFaMujkobSl3j09yNsf2xukzUWFvBgL9waC2', 'Anniswaty Hafid, S.Sos.,M.M.', '197103101998032011', NULL, 'Pustakawan Ahli Madya, Sekretaris', 'IV/c', 1, '1998-03-01', 4, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:25', '2026-03-11 05:16:19'),
(110, '198204022006042004', '$2y$10$YGuz6/7Ohr4oVBLPBPHgqOwKdJ7ZqEdeHHarti7Cjp56KAb9Qtt..', 'Karmawati, S.Pd., M.Pd.', '198204022006042004', NULL, 'Klerek - Analis Perkara Peradilan, Panitera Muda Hukum', 'IV/a', 1, '2006-04-01', 7, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:26', '2026-03-11 05:16:19'),
(111, '198207072006041017', '$2y$10$oYK3g8qDlxeEmBZuhmuLA.kws1T4rHoMFxQlVnUUar/nrTRECDs/2', 'Latuo, S.S., M.S.i.', '198207072006041017', NULL, 'Klerek - Penata Keprotokolan, Subbagian Tata Usaha dan Rumah Tangga', 'IV/a', 1, '2006-04-01', 11, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:26', '2026-03-11 05:16:19'),
(112, '197203162006042002', '$2y$10$nVA8SR71hKH4DqgzZqp1EOVe1QOdT1d7NfVe5HqH/E22r2fVgfW7W', 'Andi Iswaty Batariola, S.H.', '197203162006042002', NULL, 'Klerek - Analis Perkara Peradilan, Panitera Muda Banding', 'III/d', 1, '2006-04-01', 8, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:26', '2026-03-11 05:16:19'),
(113, '197911202009122001', '$2y$10$8.AZ/YXGOlk1zFFmNhVsO.P8vMkZEw2f7lyzT8iqg.F8Jr5v9d6Ly', 'Suhartini, S.Kom., S.H.', '197911202009122001', NULL, 'Klerek - Analis Perkara Peradilan, Panitera Muda Banding', 'III/d', 1, '2009-12-01', 8, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:26', '2026-03-11 05:16:19'),
(114, '198508092010012027', '$2y$10$8HtIdZ4/sK68ws8VdjZw9eZBy5TdS8SeZcIjIfC1hFdVTK.kWkhju', 'Nur Haerani, S.H.', '198508092010012027', NULL, 'Klerek - Analis Perkara Peradilan, Panitera Muda Banding', 'III/d', 1, '2010-01-01', 8, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:26', '2026-03-11 05:16:19'),
(115, '198712152011012020', '$2y$10$l514yV711xHgiFulzSeraeadu26usOelNMgBUizYqTg2D/ov9ubhe', 'Mawar Putri, S.E.M.Si.,AK', '198712152011012020', NULL, 'Pranata Keuangan APBN Penyelia, Sekretaris', 'III/d', 1, '2011-01-01', 4, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:26', '2026-03-11 05:16:19'),
(116, '198210152009121001', '$2y$10$EZ4GfsDe7RSyuWpvHG0WIeC0sxD8yd1kT0zzQQl3tQNM8F9KG633O', 'A. Maradona, S.H.I.', '198210152009121001', NULL, 'Analis Pengelolaan Keuangan APBN Ahli Muda, Sekretaris', 'III/d', 1, '2009-12-01', 4, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:26', '2026-03-11 05:16:19'),
(117, '198109082011011007', '$2y$10$FLclN0nrtQgBpNSB60x4YuAHW9tqOgztwZMcl.dYh3a25CoWMAoYK', 'Darias, S.Kom.', '198109082011011007', NULL, 'Kepala Subbagian, Subbagian Kepegawaian dan Teknologi Informasi', 'III/d', 1, '2011-01-01', 5, 'atasan', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:26', '2026-04-06 13:59:24'),
(118, '198501142015042001', '$2y$10$OH1LAm3gRwDGpoDJAnbEou03YM4lmXYu11mP.TxySGiidXdhwohtG', 'Nur Hikmah, S.H.', '198501142015042001', NULL, 'Klerek - Analis Perkara Peradilan, Panitera Muda Banding', 'III/c', 1, '2015-04-01', 8, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:27', '2026-03-11 05:16:19'),
(119, '197612122011012005', '$2y$10$AuFKdy8aUxVoCBsK9xfoeOIMVQmUWu7hxKbqSNvDEn7gb7dcNa0rO', 'Husnaeni, S.H.I., M.H.', '197612122011012005', NULL, 'Analis Sumber Daya Manusia Aparatur Ahli Muda, Sekretaris', 'III/c', 1, '2011-01-01', 4, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:27', '2026-03-11 05:16:19'),
(120, '198412132008052001', '$2y$10$fpdIaqd5js8eZH51VEoLGevH4aixUu.kTLOo4/QTr4JmCWSMF6pHW', 'Nur Rahma Baharuddin, S.SI.', '198412132008052001', NULL, 'Analis Sumber Daya Manusia Aparatur Ahli Muda, Subbagian Kepegawaian dan Teknologi Informasi', 'III/c', 1, '2008-05-01', 10, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:27', '2026-03-11 05:16:19'),
(121, '199006032012122003', '$2y$10$gidlJwUTpxhHC8SbMuIcwOzPA./UQGUMS8WW4yWiFiGr25Pwj/oVq', 'Syahruni Syamsu Umar, S.H.', '199006032012122003', NULL, 'Analis Sumber Daya Manusia Aparatur Ahli Muda, Subbagian Kepegawaian dan Teknologi Informasi', 'III/c', 1, '2012-12-01', 10, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:27', '2026-03-11 05:16:19'),
(122, '198909032011012006', '$2y$10$xw7A.P9CQw/ZHC.wQVuwmO3SWd4wNVdEHK7pE.z114iMmc48.X2NW', 'Dewi Sartika, S.Ft., M.Fis.', '198909032011012006', NULL, 'Operator - Penata Layanan Operasional, Subbagian Kepegawaian dan Teknologi Informasi', 'III/c', 1, '2011-01-01', 10, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:27', '2026-03-11 05:16:19'),
(123, '197612132014082001', '$2y$10$SHEmDBjnsXc7EwqDvEwodecPq7ZChVVIo5mHSfez2wIaw5YtNuDZq', 'Kamaliah, S.H.', '197612132014082001', NULL, 'Operator - Penata Layanan Operasional, Subbagian Tata Usaha dan Rumah Tangga', 'III/c', 1, '2014-08-01', 11, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:27', '2026-03-11 05:16:19'),
(124, '199704292020122008', '$2y$10$3P0CsEcABdjmgFg6S16Uje/jZgjGILyhyzTQNt5ALVCnQmMgtxN5W', 'Rifdah Fausiah Ashari, S.T.', '199704292020122008', NULL, 'Pranata Komputer Ahli Pertama, Bagian Perencanaan dan Kepegawaian', 'III/b', 1, '2020-12-01', 5, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:27', '2026-03-11 05:16:19'),
(125, '199401182019031002', '$2y$10$NU8ixQ4vM5bfAdOB/snerOoaLqPu1sQsayyw3ni.FCGgXPwaO65U.', 'Isnawan Abdhi, S.H.', '199401182019031002', NULL, 'Klerek - Analis Perkara Peradilan, Panitera Muda Hukum', 'III/b', 1, '2019-03-01', 7, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:27', '2026-03-11 05:16:19'),
(126, '198507012019031003', '$2y$10$5F5VnR6IWib96HAbB5IpI.r4dtg.Zfd1IhepK89czxV7Hshkw2TlK', 'Jamaluddin, S.E.', '198507012019031003', NULL, 'Analis Pengelolaan Keuangan APBN Ahli Pertama, Sekretaris', 'III/b', 1, '2019-03-01', 4, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:28', '2026-03-11 05:16:19'),
(127, '199612232020122003', '$2y$10$jxuRedMSB2KG.53IYWZ6JOvPy58VWUKwTiFuTyiOg4c4JwX7pArxK', 'Gebi Ajeng Harun, S.A.P', '199612232020122003', NULL, 'Operator - Penata Layanan Operasional, Subbagian Kepegawaian dan Teknologi Informasi', 'III/b', 1, '2020-12-01', 10, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:28', '2026-03-11 05:16:19'),
(128, '198812212015031001', '$2y$10$ibOZC/Z838lznyjLAHzyOu6WkEKRj0jew84O2lnvub5uqANJGx4le', 'Dadang Soenandar Hamzah, S.E.,M.H.', '198812212015031001', NULL, 'Operator - Penata Layanan Operasional, Subbagian Keuangan dan Pelaporan', 'III/b', 1, '2015-03-01', 12, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:28', '2026-03-11 05:16:19'),
(129, '199508152024051001', '$2y$10$aFCDFBzpkxJetU3ICwa9vu84xjQwbXVM0ncWJ3K4LTrwjP4CrfOz6', 'Muhammad Muaz Ikhsan, S.H.', '199508152024051001', NULL, 'Klerek - Analis Perkara Peradilan, Panitera Muda Hukum', 'III/a', 1, '2024-05-01', 7, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:28', '2026-03-11 05:16:19'),
(130, '199703282024051001', '$2y$10$f46tGroI/yV2PS/bYYPapuWVH75gJKhxLfA801KHHnSCjYzu4tIjK', 'Am Naufal Maulana, S.H.', '199703282024051001', NULL, 'Klerek - Analis Perkara Peradilan, Panitera Muda Banding', 'III/a', 1, '2024-05-01', 8, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:28', '2026-03-11 05:16:19'),
(131, '199805212020122004', '$2y$10$lVFMFWaCE4CeUrPWo5I8AOXFRrcuK7Xh9YqjwnqFctRy5xAR/C4Wm', 'Rezki Amalia, S.Tr.A.B.', '199805212020122004', NULL, 'Operator - Penata Layanan Operasional, Subbagian Rencana Program dan Anggaran', 'III/a', 1, '2020-12-01', 9, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:28', '2026-03-11 05:16:19'),
(132, '199204252022032007', '$2y$10$0xR9Kg3DMEmFyof9W/14Mu4zWCeD06VbUDWPn8MQdvuJKN6VoiPVe', 'Nadia Hamkan Bugis, S.Tr.A.B.', '199204252022032007', NULL, 'Operator - Penata Layanan Operasional, Subbagian Rencana Program dan Anggaran', 'III/a', 1, '2022-03-01', 9, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:28', '2026-03-11 05:16:19'),
(133, '199210032022031006', '$2y$10$OT/KhRnZGcof0jDFhbDXMO4EQHYiAAfJot27gSTq6pYMgrZBM3c4e', 'Aditya Permana, S.E.', '199210032022031006', NULL, 'Klerek - Penelaah Teknis Kebijakan, Subbagian Rencana Program dan Anggaran', 'III/a', 1, '2022-03-01', 9, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:29', '2026-03-11 05:16:19'),
(134, '200104152025062017', '$2y$10$F8cSkd/rEAFbSJsv37r4TuNXEKID9WdAdJ8Vu67a1rl7uZBQdVrNC', 'NURUL ANNIZA BASRI, S.Kom.', '200104152025062017', NULL, 'Penata Kelola Sistem dan Teknologi Informasi, Subbagian Kepegawaian dan Teknologi Informasi', 'III/a', 1, '2025-06-01', 10, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:29', '2026-03-11 05:16:19'),
(135, '200103022025062011', '$2y$10$vcZNnycJUNlgQWFueKYjsuoPWNrymXO7mT7upOAAIh.cL8DPp6chO', 'IFTINAN ADHASARI PRAMESTHY, S.I.Kom.', '200103022025062011', NULL, 'Klerek - Penata Keprotokolan, Subbagian Tata Usaha dan Rumah Tangga', 'III/a', 1, '2025-06-01', 11, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:29', '2026-03-11 05:16:19'),
(136, '200208042025062009', '$2y$10$3xrCICZxSROWcsX83fQ4h..RHVddwClWRIedYwLDBKAcMo0DGuHx6', 'SITI DATRI CAHYATI, S.T.', '200208042025062009', NULL, 'Teknisi Sarana dan Prasarana, Subbagian Tata Usaha dan Rumah Tangga', 'III/a', 1, '2025-06-01', 11, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:29', '2026-03-11 05:16:19'),
(137, '199111202022032009', '$2y$10$LheEQF.8eQKFEVTAPVMwoOTF4UbdsNG67tMpHQKMeGtZhqe7vitsO', 'Nur Achfiah Budhi Artha, S.ST.', '199111202022032009', NULL, 'Operator - Penata Layanan Operasional, Subbagian Keuangan dan Pelaporan', 'III/a', 1, '2022-03-01', 12, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:29', '2026-03-11 05:16:19'),
(138, '199505082022032019', '$2y$10$YE4uQnZdu.damNZbPhNrhOqREJuufNmGC9ygM6kE6WjCgtc1FKiC2', 'Watik, A.Md.', '199505082022032019', NULL, 'Klerek - Pengolah Data dan Informasi, Subbagian Kepegawaian dan Teknologi Informasi', 'II/c', 1, '2022-03-01', 10, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:29', '2026-03-11 05:16:19'),
(139, '199509092022032015', '$2y$10$WigZMYSucp2qNj3rZPCdXeappKpTG5R.TIWJIGAD2h1O/t.oqIYxW', 'Sylvana Praditarani, A.Md', '199509092022032015', NULL, 'Klerek - Pengolah Data dan Informasi, Subbagian Tata Usaha dan Rumah Tangga', 'II/c', 1, '2022-03-01', 11, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:29', '2026-03-11 05:16:19'),
(140, '199908062022031005', '$2y$10$2tQlFqEiy2xCX8QbbQa8T.QbTGTStVYxc2sUzZazATGFounU4IBOe', 'Sinang Mahatma Dhewa, A.Md.Bns', '199908062022031005', NULL, 'Klerek - Pengolah Data dan Informasi, Subbagian Keuangan dan Pelaporan', 'II/c', 1, '2022-03-01', 12, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:29', '2026-04-06 03:05:33'),
(141, '198011152023211008', '$2y$10$wfKKqaP1FRB3xpTARfRQ4eantFPD2a2CLHStJJWg1dkhzfKmoTZ7S', 'Ahmad Ridha, S.E.', '198011152023211008', NULL, 'Arsiparis Ahli Pertama, Sekretaris', 'IX', 1, '1900-01-01', 4, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:30', '2026-03-11 05:16:19'),
(142, '199609212023212031', '$2y$10$FiLxPcjQQC8OuHkI37DrwOBA6M6kbat98n.Wa/UNNL2i5YuL.3c2a', 'Satriani Har, S.M.', '199609212023212031', NULL, 'Perencana Ahli Pertama, Sekretaris', 'IX', 1, '1900-01-01', 4, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-19 05:17:30', '2026-03-11 05:16:19'),
(143, 'tester', '$2y$10$vJqv49x499/M/DnbtSZEIuHO3oSyejzEwUHjdpdqM1M9h89M2XlWy', 'Alif Qadri', '200305282024082008', NULL, 'Akun Test, Subbagian Kepegawaian dan Teknologi Informasi', 'III/b', 1, '2024-08-01', 10, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2025-08-20 00:20:33', '2026-04-06 13:59:40'),
(146, '195912311986031038', '$2y$10$pSjrug.DXCGrzvJ0Vsfr4OnyAEycLdbcsNl3hZyP.zpPNbka6Xc2O', 'Dr. Drs. Khaeril  R, M.H.', '195912311986031038', NULL, 'Ketua', 'IV/e', 1, '1986-03-01', NULL, 'atasan', 0, NULL, 0, NULL, 0, NULL, '2026-01-16 09:57:30', '2026-04-06 13:48:08'),
(149, 'fikri', '$2y$10$BrmoiKV9E3nsx/cy.zsOluNz3L32rzHK4voXyjZpi8ZrnSkXjpLVi', 'Fikri', '200505052024041001', NULL, 'Akun Test, Panitera Muda Hukum', 'III/d', 1, '2024-04-01', 7, 'pegawai', 0, NULL, 0, NULL, 0, NULL, '2026-04-06 09:28:07', '2026-04-06 09:28:07');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `after_insert_users_admin` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    IF NEW.user_type = 'admin' THEN
        INSERT INTO admin (nama_admin)
        VALUES (NEW.nama);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_users_admin` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
    IF NEW.user_type = 'admin' AND OLD.user_type <> 'admin' THEN
        INSERT INTO admin (nama_admin)
        VALUES (NEW.nama);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `users_insert_atasan` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    IF NEW.user_type = 'atasan' THEN
        INSERT INTO atasan (nama_atasan, NIP, jabatan)
        VALUES (NEW.nama, NEW.nip, NEW.jabatan);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `users_update_from_atasan` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
    IF OLD.user_type = 'atasan' AND (NEW.user_type = 'pimpinan' OR NEW.user_type = 'pegawai') THEN
        DELETE FROM atasan WHERE NIP = OLD.nip;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `users_update_to_atasan` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
    IF NEW.user_type = 'atasan' AND OLD.user_type != 'atasan' THEN
        IF NOT EXISTS (SELECT 1 FROM atasan WHERE NIP = NEW.nip) THEN
            INSERT INTO atasan (nama_atasan, NIP, jabatan)
            VALUES (NEW.nama, NEW.nip, NEW.jabatan);
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users_backup_20251208`
--

CREATE TABLE `users_backup_20251208` (
  `id` int(11) NOT NULL DEFAULT 0,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `golongan` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_masuk` date NOT NULL,
  `atasan` int(11) DEFAULT NULL,
  `user_type` enum('admin','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_modified` tinyint(1) DEFAULT 0,
  `last_modified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `unit_kerja` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_backup_20251208`
--

INSERT INTO `users_backup_20251208` (`id`, `username`, `password`, `nama`, `nip`, `jabatan`, `golongan`, `tanggal_masuk`, `atasan`, `user_type`, `is_deleted`, `deleted_at`, `is_modified`, `last_modified_at`, `created_at`, `updated_at`, `unit_kerja`) VALUES
(1, 'admin_pta', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', '-', 'Admin', '-', '1986-03-01', NULL, '', 0, NULL, 0, NULL, '2025-06-05 02:30:25', '2025-12-08 10:49:59', 1),
(3, 'pa_makassar', '$2y$10$iogrdZxHCKZ660ZbJtZINO7zisFbZucrvGB5BH.8zCBi1H.11zOo.', 'Dr. Hj. Hasnaya H. Abd. Rasyid, M.H.', '196712121993032006', 'Ketua Pengadilan Agama', 'IV/d', '1993-03-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:28', '2025-12-08 10:49:59', 2),
(4, 'pa_sengkang', '$2y$10$01LKaFiir2WztHlr8zFYR.LVLKHpIUsOy3XTayjThJZVkOy6kwPba', 'Dra. Heriyah, S.H., M.H.', '196712311993032018', 'Ketua Pengadilan Agama', 'IV/d', '1993-03-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:28', '2025-12-08 10:49:59', 3),
(5, 'pa_watampone', '$2y$10$lRhVQFe7J0/89adUmi6BvurQtIL/oSyhRn4br3xMCNsiwhhPdQt7W', 'Dra. Hj. Nurlinah. K, S.H., M.H.', '196712311994032020', 'Ketua Pengadilan Agama', 'IV/d', '1994-03-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:29', '2025-12-08 10:49:59', 4),
(6, 'pa_watansoppeng', '$2y$10$J5HKnwlA.FDe0kEtkQtv/ekfamwKKWhQsL2iKRXblxwmqgVjzFqla', 'Drs. H. Mursidin, M.H.', '196612311994031059', 'Ketua Pengadilan Agama', 'IV/d', '1994-03-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:29', '2025-12-08 10:49:59', 5),
(7, 'pa_pinrang', '$2y$10$SQ8xty8mgdixcxg60Lg4bOMefsCR/YiO22qsyapHp5/bZADBkXnQG', 'Hadrawati, S.Ag., M.HI.', '197301311998022003', 'Ketua Pengadilan Agama', 'IV/c', '1998-02-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:29', '2025-12-08 10:49:59', 6),
(8, 'pa_sungguminasa', '$2y$10$k8mGnsiok2Qyv42ey3r9Lel1R15iAr.c3ZpM16QiFtvhp736BXvoK', 'Abdul Rahman Salam, S.Ag., M.H.', '197302121999031001', 'Ketua Pengadilan Agama', 'IV/c', '1999-03-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:29', '2025-12-08 10:49:59', 7),
(9, 'pa_maros', '$2y$10$tc632MgIdtjHOPYE/lrABOMcCldF4FeDrBpE5xyXR5OjL8h2aogQa', 'A. Muh Yusri Patawari, S.H.I., M.H.', '198001262007041001', 'Ketua Pengadilan Agama', 'IV/b', '2007-04-01', 1, '', 0, NULL, 0, '2025-07-31 00:56:08', '2025-07-07 05:31:29', '2025-12-08 10:49:59', 8),
(10, 'pa_bulukumba', '$2y$10$ZqQgiaYE9abSIypzqF0BZuJOvE0VWxE50hgMjSASC951EM0qI94Qa', 'Laila Syahidan, S.Ag.M.H.', '197410172006042002', 'Ketua Pengadilan Agama', 'IV/b', '2006-04-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:29', '2025-12-08 10:49:59', 9),
(11, 'pa_palopo', '$2y$10$SnitJICYCMZqxOGnsugYFuwVBDTGWD/BWCoaiIgHe.AJRj2.c7nae', 'Tommi, S.H.I.', '197905172006041005', 'Ketua Pengadilan Agama', 'IV/b', '2006-04-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:29', '2025-12-08 10:49:59', 10),
(12, 'pa_sidrap', '$2y$10$S/AZ0GK13xLtoNLbfRudCeMPJogHGkhvQ7W4H0rPUbicUkbx0OyCO', 'Andi Muhammad Yusuf Bakri, S.H.I., M.H.', '197908062005021001', 'Ketua Pengadilan Agama', 'IV/b', '2005-02-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:29', '2025-12-08 10:49:59', 11),
(13, 'pa_jeneponto', '$2y$10$3MXHRjqznU6IsmGXNnri9uQpNoVJbqZsNYpNXCi6E69RqMZlyoKlm', 'Fadilah, S.Ag., M.H.', '197408212002122001', 'Ketua Pengadilan Agama', 'IV/b', '2002-12-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:30', '2025-12-08 10:49:59', 12),
(14, 'pa_barru', '$2y$10$ELgXUG31fiVJi.3xqUwxxeK6Ro/EW5BtSQ4h76Mb2OCW5cxA9XCZu', 'Maryam Fadhilah Hamdan, S.H.I.', '197805042002122003', 'Ketua Pengadilan Agama', 'IV/b', '2002-12-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:30', '2025-12-08 10:49:59', 13),
(15, 'pa_parepare', '$2y$10$XQBfh90Ta8Z1X1X/aEIEouPD6yt.H3mAF8o23lPg8FzupdpVxZ/M2', 'Muhammad Natsir, S.H.I.', '197806252006041002', 'Ketua Pengadilan Agama', 'IV/b', '2006-04-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:30', '2025-12-08 10:49:59', 14),
(16, 'pa_belopa', '$2y$10$p/rBoPR8lbeCW6/4i4EZDO8khUGOEGoGR1BEPqTTs36qiZCJtpbxW', 'Irham Riad, S.H.I., M.H.', '197912292006041002', 'Ketua Pengadilan Agama', 'IV/b', '2006-04-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:30', '2025-12-08 10:49:59', 15),
(17, 'pa_sinjai', '$2y$10$c1r9sb76MqKjViXWyEyOx.sENPQRFTpUUcQ5ekXxaDFNhl.O4xumK', 'Rokiah Binti Mustaring, S.H.I., M.H.', '198011252009122002', 'Ketua Pengadilan Agama', 'IV/a', '2009-12-01', 1, '', 0, NULL, 0, '2025-07-31 00:56:08', '2025-07-07 05:31:30', '2025-12-08 10:49:59', 16),
(18, 'pa_makale', '$2y$10$eW.UlX9NN4uugJnq7rxp9eRQ0m/grubdl.DlvvZz69dvDLe13e9Oe', 'Dr. Mushlih, S.H.I., M.H.', '198004132008051001', 'Ketua Pengadilan Agama', 'IV/a', '2008-05-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:30', '2025-12-08 10:49:59', 17),
(19, 'pa_pangkajene', '$2y$10$JTwo9xC/FEgckGlNw7jd0ea/IybV6wECMne3T9dHfY06IKIK/FGn6', 'Dr. Wildana Arsyad, S.H.I., M.H.I.', '198312112007042001', 'Ketua Pengadilan Agama', 'IV/a', '2007-04-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:30', '2025-12-08 10:49:59', 18),
(20, 'pa_takalar', '$2y$10$3DQki8OcpoVrmEECwwXK/./K6d9R9eR9d15NFBaKykNT3N2QTqVB2', 'Hapsah, S.Ag., M.H.', '197706302007042001', 'Ketua Pengadilan Agama', 'IV/a', '2007-04-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:30', '2025-12-08 10:49:59', 19),
(21, 'pa_selayar', '$2y$10$4lzeg.XOG907Xiiif3F.5OMZPunMwaFeEKv.2YJUgzImUUPEO2aiG', 'Rusni, S.H.I., M.H.', '197906012007042001', 'Ketua Pengadilan Agama', 'IV/a', '2007-04-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:31', '2025-12-08 10:49:59', 20),
(22, 'pa_bantaeng', '$2y$10$mpbuDrys3c8zoJPZtVx8auk.2mP1DNLEzAHm192NGLH17aNTs.9rq', 'Amirullah Arsyad, S.H.I.,M.H.', '198207072007041001', 'Ketua Pengadilan Agama', 'IV/a', '2007-04-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:31', '2025-12-08 10:49:59', 21),
(23, 'pa_enrekang', '$2y$10$w4B7vrwgrRqltG38aNNbAu2PE10HnvFmytFMybs5L66sFvud1laTq', 'Dr Amin Bahroni, S.H.I., M.H.', '197705152007041001', 'Ketua Pengadilan Agama', 'IV/a', '2007-04-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:31', '2025-12-08 10:49:59', 22),
(24, 'pa_masamba', '$2y$10$eB/qYTeED8AdCmfB58M5cOZmfXQ/h8TA3J6z7zJbwRCsihpVyfmjO', 'Nirwana, S.H.I., M.H.', '198212012008052001', 'Ketua Pengadilan Agama', 'IV/a', '2008-05-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:31', '2025-12-08 10:49:59', 23),
(25, 'pa_malili', '$2y$10$b1jDQnGMeUMCO28w2tNOke5p/psg9soX2Eg9MqjzCIFqAXxhuTtZy', 'Rajiman, S.H.I., M.H.', '198210102007041001', 'Ketua Pengadilan Agama', 'IV/a', '2007-04-01', 1, '', 0, NULL, 0, NULL, '2025-07-07 05:31:31', '2025-12-08 10:49:59', 24),
(26, '195912311986031038', '$2y$10$meeVtD/RvvBIeQ4eOBE0qu7IbCAvRhtwN4xNLYgvLWwmV1mxw4KXW', 'Dr. Drs. Khaeril  R, M.H.', '195912311986031038', 'Ketua', 'IV/e', '1986-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:15', '2025-12-08 10:49:59', 1),
(27, '195901311990031001', '$2y$10$VHfHOB1Wu7L9usGO622kiOye7RjN/ulx8AklLY8Ja5boAQkvBICxC', 'Drs. Muhammad Alwi, M.H.', '195901311990031001', 'Wakil Ketua', 'IV/e', '1990-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:15', '2025-12-08 10:49:59', 1),
(28, '196012311987031054', '$2y$10$bL6To7pg2XhjCfmqJ6FeeeDb8oatemHxIUXvB1kY/SpnxDPPGJ.kG', 'Drs. Iskandar, S.H.', '196012311987031054', 'Hakim Tinggi', 'IV/e', '1987-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:15', '2025-12-08 10:49:59', 1),
(29, '196303101992032008', '$2y$10$z0mW4pNfO4jWXqXr6ZbSJuq6O6lFSQ9hhHDCm0YHr1gjf9dX6iVyy', 'Dra. Dzakiyyah, M.H.', '196303101992032008', 'Hakim Tinggi', 'IV/e', '1992-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:15', '2025-12-08 10:49:59', 1),
(30, '196504201993031002', '$2y$10$MOyvYFeA8ThwZpARZL90X.9s5mhspW8LfoOi5ngJa1.Ug0EDwzea2', 'Drs Samarul Falah, M.H.', '196504201993031002', 'Hakim Tinggi', 'IV/e', '1993-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:15', '2025-12-08 10:49:59', 1),
(31, '196305051990032005', '$2y$10$UqekPVdPZxltx0fOvEQNlekmULMr8ah4w0OcNm9RNH5wttyQAUCd6', 'Dra. St. Aminah, M.H.', '196305051990032005', 'Hakim Tinggi', 'IV/e', '1990-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:15', '2025-12-08 10:49:59', 1),
(32, '195901081987032002', '$2y$10$w14Q1MRSbk25krEg7fFu4ufrefabkhduQuiltYzszGoPgv6ovGgz6', 'Dra. Nurcaya Hi Mufti, M.H.', '195901081987032002', 'Hakim Tinggi', 'IV/e', '1987-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:16', '2025-12-08 10:49:59', 1),
(33, '196406241990032002', '$2y$10$Cdx2YTaIYHDzb16sqttSd.JZVeT2J5FMdkxcVIzKNe/8grj8wyhSa', 'Nuraeni. S, S.H., M.H.', '196406241990032002', 'Hakim Tinggi', 'IV/e', '1990-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:16', '2025-12-08 10:49:59', 1),
(34, '196012311989032012', '$2y$10$vhf7Vvqsc3Hu5FrLy3KCZuxEJ8dOV3V5qTI/4GhGvgFBVtQ43VMOi', 'Dra. St. Mawaidah, S.H., M.H.', '196012311989032012', 'Hakim Tinggi', 'IV/e', '1989-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:16', '2025-12-08 10:49:59', 1),
(35, '196303171991031003', '$2y$10$xH5/LLmNFBKK5kY8iaaZkuNZqJSqJwwAjOKtbvQSqjUPF1YSf26Fi', 'Drs. Syahidal', '196303171991031003', 'Hakim Tinggi', 'IV/e', '1991-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:16', '2025-12-08 10:49:59', 1),
(36, '195907311982031001', '$2y$10$TAoGggcxCs0h0Woj/5vD1OEFdT27NK9rNTDChMOXIKLFlJfhke67K', 'Drs. M. Anas Malik, S.H., M.H.', '195907311982031001', 'Hakim Tinggi', 'IV/e', '1982-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:16', '2025-12-08 10:49:59', 1),
(37, '195812311984032004', '$2y$10$gEMnh/eYT58yS0vPseuv8.0J33Jd4MjsGfoHYSzGBpaAkRFSBPGfG', 'Dra. Kamariah, S.H., M.H.', '195812311984032004', 'Hakim Tinggi', 'IV/e', '1984-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:16', '2025-12-08 10:49:59', 1),
(38, '196012311983031049', '$2y$10$xce94gS.geh9lIjsH0p.FO4JJ.n5DlcVstjqGJsNzNEpWTHGMXa/a', 'Drs Mahmud, S.H., M.H.', '196012311983031049', 'Hakim Tinggi', 'IV/e', '1983-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:16', '2025-12-08 10:49:59', 1),
(39, '195912311988031025', '$2y$10$Lmuq9U3mLVoqFgjSrIAvfuIzWeI4P4Ur8FMgvtAt4f02C8SfTmXgi', 'Drs. Hasbi, M.H.', '195912311988031025', 'Hakim Tinggi', 'IV/e', '1988-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:16', '2025-12-08 10:49:59', 1),
(40, '196303171992032002', '$2y$10$lyYTFmJqGNpEt3fEX.8YBO3n8Fl/4dYTmNbOVEs7B/IU9OFcDloNO', 'Dra. Martina Budiana Mulya, M.H.', '196303171992032002', 'Hakim Tinggi', 'IV/d', '1992-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:17', '2025-12-08 10:49:59', 1),
(41, '196408201993032002', '$2y$10$7.RRoNQJNFydMcuEbAg7De2xkKdZRI5Wq0oRqQMdiXqbhU5G07y2.', 'Dra. Fatmah Abujahja', '196408201993032002', 'Hakim Tinggi', 'IV/d', '1993-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:17', '2025-12-08 10:49:59', 1),
(42, '196812291994031005', '$2y$10$BTBj4Pky149Rme5GfZkqWudbQG56EDNAo/cWJwfz.t1bpslVCE.yy', 'Drs Gunawan, M.H.', '196812291994031005', 'Hakim Tinggi', 'IV/d', '1994-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:17', '2025-12-08 10:49:59', 1),
(43, '196410041994031004', '$2y$10$nKWL/M9LagsNBqFfYGv9..SLZ6xzzsq4/ltYEEGE4BoezQsVsv.My', 'Dr Hasanuddin, S.H., M.H.', '196410041994031004', 'Panitera', 'IV/c', '1994-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:17', '2025-12-08 10:49:59', 1),
(44, '197011021997031001', '$2y$10$ahM0pWljctGFnWs9NjEyluh2TYGa7WVXXcekbFTO3AlyRkhloZiT.', 'Dr. Abdul Mutalip, S.Ag., S.H., M.H.', '197011021997031001', 'Sekretaris', 'IV/c', '1997-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:17', '2025-12-08 10:49:59', 1),
(45, '197803091998031002', '$2y$10$rq306H9kJbrdPBXJb8HX7u.upVU61998uvux1Zsr18/n2CvmJESaS', 'Dr. Muhammad Busyaeri, S.H., M.H.', '197803091998031002', 'Kepala Bagian Perencanaan dan Kepegawaian', 'IV/b', '1998-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:17', '2025-12-08 10:49:59', 1),
(46, '196907162003121003', '$2y$10$su1lEwQipDX/P.pshMxukuDEAzoDM.Hjqdqoxg/qomnCb95EWIoou', 'Drs. Muhammad Amin, M.A.', '196907162003121003', 'Kepala Bagian Umum dan Keuangan', 'IV/b', '2003-12-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:17', '2025-12-08 10:49:59', 1),
(47, '197211101998022002', '$2y$10$Nhy47eJX2nJj4IJ9bcy1huAUs7C2UGDc2pUKSwtzzNmhEwMGNGiNO', 'Nurbaya, S.Ag., M.H.I.', '197211101998022002', 'Panitera Muda Hukum', 'IV/a', '1998-02-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:17', '2025-12-08 10:49:59', 1),
(48, '196512081993031007', '$2y$10$Bmfv9H3CjlJiupgogVFMOOZW6pKABnsB0g/0/mGEyTzRQM8IHgdJG', 'Hasbi, S.H., M.H.', '196512081993031007', 'Panitera Muda Banding', 'IV/a', '1993-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:18', '2025-12-08 10:49:59', 1),
(49, '196703111992031004', '$2y$10$S6lwFwCkKEfhcPGiT9IV2Os77T/r.3lhrqZcKXHZRtgWf24YKa3pa', 'Drs. Abdul Samad, M.H.', '196703111992031004', 'Hakim Yustisial,', 'IV/d', '1992-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:18', '2025-12-08 10:49:59', 1),
(50, '196308221994031003', '$2y$10$/sZ70NCPtjFcwpe8WD.zU.d6eaYzO3qpIS1iu.Uzo7zQOoTb6P/Du', 'Sudirman, S.H.', '196308221994031003', 'Panitera Pengganti', 'IV/b', '1994-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:18', '2025-12-08 10:49:59', 1),
(51, '197504122000031002', '$2y$10$866fo8GDp/hhT/ixfxlbCOloHt0WUeS5TWO6XHOZG7ZF1G/DcKaD6', 'Arifin, S.Ag., M.H.', '197504122000031002', 'Panitera Pengganti', 'IV/b', '2000-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:18', '2025-12-08 10:49:59', 1),
(52, '197505171999031003', '$2y$10$MJcM0XGLK8Gci7.f432LTui6ZB98M7zZnLH1mKTEYBJsqD26hNaPG', 'Patahuddin Azis, S.Ag.', '197505171999031003', 'Panitera Pengganti', 'IV/b', '1999-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:18', '2025-12-08 10:49:59', 1),
(53, '197311102001121006', '$2y$10$25SNmgPN97UZWF5orFM8JOHECr.pvRouZ0x7ZZj//gmhMhfXyrSzm', 'Asir Pasimbong Alo, S.Ag., M.H.', '197311102001121006', 'Panitera Pengganti', 'IV/b', '2001-12-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:18', '2025-12-08 10:49:59', 1),
(54, '197007142001122002', '$2y$10$Pi.p6GdcR99B07/4xXdVJ.ZT5cdjjjjn.x5Pl4fX2lmE9kimB8BhS', 'Khaerawati Abdullah, S.Ag., S.H., M.H.', '197007142001122002', 'Panitera Pengganti', 'IV/a', '2001-12-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:18', '2025-12-08 10:49:59', 1),
(55, '196410101993032002', '$2y$10$NvNh00wo5vI8M4k4nAkI2OR3Akc3FEmPgD8pVdudc00oVGsN3jz2K', 'Hartinah, S.H., M.H.', '196410101993032002', 'Panitera Pengganti', 'IV/a', '1993-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:18', '2025-12-08 10:49:59', 1),
(56, '196412011988011001', '$2y$10$eJDR7cqYAc9BDejMqxi7kO3qJ/8PvOAbYZCjibydP4CtVxAIZUs/K', 'Haerul Ahmad, S.H., M.H.', '196412011988011001', 'Panitera Pengganti', 'IV/a', '1988-01-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:19', '2025-12-08 10:49:59', 1),
(57, '197404252000121001', '$2y$10$oEuWBdd0x.NSjH05M3HI8.MvXBRBN4jcMPth6I0qZp0.iSpvuAxL2', 'Taufiq Hasyim, S.Ag., M.H.', '197404252000121001', 'Panitera Pengganti', 'IV/a', '2000-12-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:19', '2025-12-08 10:49:59', 1),
(58, '196310111991032002', '$2y$10$aAxqBWrFEuqEudiCcdEhWegEUB0CHGqgh4TWimhsNuWTt8/E07cBu', 'Dra. Hunaena, M.H.', '196310111991032002', 'Panitera Pengganti', 'IV/a', '1991-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:19', '2025-12-08 10:49:59', 1),
(59, '196802021997032002', '$2y$10$PSb3LQhQlPNteGB59NWBIOrX4SjeBDIflJzFAAElw5SG.ZI5Gx33O', 'Dra. Musafirah, M.H.', '196802021997032002', 'Panitera Pengganti', 'IV/a', '1997-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:19', '2025-12-08 10:49:59', 1),
(60, '196801241998021001', '$2y$10$1I/HNcGBkvb.zFxVPb9Uf.V92WIHOsovQjbV89MidITkXdtkKjosy', 'Muh. Rais Naim, S.H., S.Ag.', '196801241998021001', 'Panitera Pengganti', 'IV/a', '1998-02-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:19', '2025-12-08 10:49:59', 1),
(61, '196612311990021002', '$2y$10$RPyN3025HUGJJPS7/2i0PeKvWcbwrYNZz61x/JXIQM1DssAceTp0C', 'Husain, S.H., M.H.', '196612311990021002', 'Panitera Pengganti', 'IV/a', '1990-02-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:19', '2025-12-08 10:49:59', 1),
(62, '196701081994021001', '$2y$10$awNiWTtR4.CCS7iUH/I5OuYSHKlt6Qu0LLiLNd8KRdc0rDPC3Rvvm', 'Muhammadiah, S.H., M.H.', '196701081994021001', 'Panitera Pengganti', 'IV/a', '1994-02-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:19', '2025-12-08 10:49:59', 1),
(63, '196612311993031044', '$2y$10$dpTQTao9gLBw4jkotNayXO3YQq5XkRYPfDY0NB1X28Aci3xXnFkC6', 'Drs. Amir, M.H.', '196612311993031044', 'Panitera Pengganti', 'IV/a', '1993-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2025-12-08 10:49:59', 1),
(64, '196812151997031001', '$2y$10$QCrsBcY58N2wptDrxfhl..o4nJS94ddB/9pXqs0iq2gDWfSZRdfpq', 'Drs. Tawakkal, M.H.', '196812151997031001', 'Panitera Pengganti', 'IV/a', '1997-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2025-12-08 10:49:59', 1),
(65, '197203051995031002', '$2y$10$xyxHpMcTOKCHBr1UyOVxe.nAa8fwTMU1Tqv/q0RHmbC7/pa5LmfPe', 'Akyadi, S.IP., S.H.I., M.H.', '197203051995031002', 'Panitera Pengganti', 'IV/a', '1995-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2025-12-08 10:49:59', 1),
(66, '196908232000032007', '$2y$10$D3s5dZePrkTZE4HAkFrQa.hQI7Q4o.NKafjXsv22wR/U6E1d3XlUG', 'Fatimah A. D., S.H., M.H.', '196908232000032007', 'Panitera Pengganti', 'IV/a', '2000-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2025-12-08 10:49:59', 1),
(67, '196512311992031066', '$2y$10$5ky.rf2NpcHqF0pzlcz/pe1MdXx4TWIpiHOCjgau2fgsAwfRG5a0a', 'Drs. Hamzah Appas, S.H., M.H.', '196512311992031066', 'Panitera Pengganti', 'IV/a', '1992-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2025-12-08 10:49:59', 1),
(68, '196412311994031050', '$2y$10$iOae46HAAexjW78ayMtame3PwUSynYxbmG4wWYpXahWJFBjhFflKS', 'Drs. M. Idris, S.H., M.H.', '196412311994031050', 'Panitera Pengganti', 'IV/a', '1994-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2025-12-08 10:49:59', 1),
(69, '198105252006041005', '$2y$10$w9qXcjboRPQ.MLWSe9XTyuUftNrc.XKZF8HJbnUNW.ES/MBSSPIti', 'Muhammad Iqbal Yunus, S.H.I., M.H.', '198105252006041005', 'Panitera Pengganti', 'IV/a', '2006-04-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2025-12-08 10:49:59', 1),
(70, '196608071994012001', '$2y$10$90tuomiI.KXUd6ulXchd.OjfOGIOmXEZj3II7fX4omVF/vvKlJtlu', 'Dra. Hasna Mohammad Tang', '196608071994012001', 'Panitera Pengganti', 'III/d', '1994-01-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2025-12-08 10:49:59', 1),
(71, '197303122001122003', '$2y$10$ozqoDm62F/9RDlgstqaW8OpcqVZzKpMQq2wHkYvlhw3KRXgEwkiKa', 'Nurul Jamaliah, S.Ag.', '197303122001122003', 'Panitera Pengganti', 'III/d', '2001-12-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:20', '2025-12-08 10:49:59', 1),
(72, '196308251992032004', '$2y$10$oXDpfjo5/uZOBzOzWpf41O0HG8tomEW9snI87aYexjjmcq2oYJkv.', 'Dra. Muzdalifah, S.H.', '196308251992032004', 'Panitera Pengganti', 'III/d', '1992-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:21', '2025-12-08 10:49:59', 1),
(73, '196806041990032005', '$2y$10$MVjiBTv3abgFUtzRpzbWleKjyeucZiBNnKJOLCW1RlR4GAMieAg1e', 'Mukarramah Saleh, S.H.', '196806041990032005', 'Panitera Pengganti', 'III/d', '1990-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:21', '2025-12-08 10:49:59', 1),
(74, '196603151997032001', '$2y$10$73hnWTqKtmyv3/By8BdVOeDapXsyjcpAOg2bjEgro7dJBFfvpwgdW', 'Dra. Haisah, S.H.', '196603151997032001', 'Panitera Pengganti', 'III/d', '1997-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:21', '2025-12-08 10:49:59', 1),
(75, '196507142003021001', '$2y$10$excCHXIKiSl5TSK7cjeKpuGJpBWF3h2n8HbXVJqEv7HHDwpl99DeS', 'Ibrahim, S.H.', '196507142003021001', 'Panitera Pengganti', 'III/d', '2003-02-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:21', '2025-12-08 10:49:59', 1),
(76, '197411091994031002', '$2y$10$0XMDWkfGMGYCxxEpHLoB0eRevLYvADZ8Clgez5rVKOfQJhB6LW0Fm', 'Muh. Sabir, S.H', '197411091994031002', 'Panitera Pengganti', 'III/d', '1994-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:21', '2025-12-08 10:49:59', 1),
(77, '196912311994031024', '$2y$10$/D.LKKZ2cD6zZlMd.eF2duFdupA5IScaTkY4gurZdjhcXvgBgp4fe', 'Drs., Syamsuddin', '196912311994031024', 'Panitera Pengganti', 'III/d', '1994-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:21', '2025-12-08 10:49:59', 1),
(78, '197009121992031004', '$2y$10$PdeEuOdCBZB6ZzeE6IL4l.Nc57mWXdFufqRLzIwmKIyy1rSpdOpAW', 'Bintang, S.H.', '197009121992031004', 'Panitera Pengganti', 'III/d', '1992-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:21', '2025-12-08 10:49:59', 1),
(79, '197010241997032001', '$2y$10$jmKb6r.SIHmYa0uPeeRIWuInw4l4QAdxwFw5ritTxuwbr4c4haWx.', 'Dra. Suherlina', '197010241997032001', 'Panitera Pengganti', 'III/d', '1997-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:22', '2025-12-08 10:49:59', 1),
(80, '196704121994032002', '$2y$10$CBBfbIOWGb3QHnuv0t929uauITrIF9ep/tEkO3NAblAC7MwsYFifu', 'Dra. Rosmini', '196704121994032002', 'Panitera Pengganti', 'III/d', '1994-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:22', '2025-12-08 10:49:59', 1),
(81, '197309151994021002', '$2y$10$Rk.erHOjacXCzfUe3/hLruO0qA87M2eTdFlJk73p42BzgiymBnyJi', 'Sulfian P, S.Ag.', '197309151994021002', 'Panitera Pengganti', 'III/d', '1994-02-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:22', '2025-12-08 10:49:59', 1),
(82, '197311052001121001', '$2y$10$pZYQ4wI4s642dTaMzu3yiuLMLTfkmGLqtuLrWrys0zqPnLqxJr2AK', 'Andi Suardi, S. Ag.', '197311052001121001', 'Panitera Pengganti', 'III/d', '2001-12-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:22', '2025-12-08 10:49:59', 1),
(83, '197012211994031005', '$2y$10$t8NLj.EHrLNhl0XUyDtlQeb9FryRkvettjkWqpT10IuAchSi44n4m', 'Salahuddin Saleh, S.H.', '197012211994031005', 'Panitera Pengganti', 'III/d', '1994-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:22', '2025-12-08 10:49:59', 1),
(84, '196701261994021003', '$2y$10$7oNt/WZcjqocS/J0XHn2/.DiuJmNOd0CFag/VcuTv7UWs9krX1owa', 'Ibrahim Thoai, S.H.', '196701261994021003', 'Panitera Pengganti', 'III/d', '1994-02-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:22', '2025-12-08 10:49:59', 1),
(85, '196512081995032001', '$2y$10$rh9nyQd3xrUDxMjHjIcySeGRrwoxqu0CykvMrw8KOSpA2EFwS8PkO', 'Dra. Wahda', '196512081995032001', 'Panitera Pengganti', 'III/d', '1995-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:22', '2025-12-08 10:49:59', 1),
(86, '196910051999032002', '$2y$10$7X3Hv6ZENQcgSQNl/B2T3.pNi1vi3IuYa7qGFhwbi1kT8Q/kawfMC', 'Annisa, S.H.', '196910051999032002', 'Panitera Pengganti', 'III/d', '1999-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:22', '2025-12-08 10:49:59', 1),
(87, '196709231989032001', '$2y$10$rsI40/D0blLw21i99.pTK.BK1qflqCys9BjjdiNq.GDcnLpfSQXx6', 'Rahmawati, S.Ag.', '196709231989032001', 'Panitera Pengganti', 'III/d', '1989-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:23', '2025-12-08 10:49:59', 1),
(88, '196911301993031003', '$2y$10$IkA/kDN.2PeSGiE/vplU.ejCK/CYxWUri8m7dS8hdzjE9n1tEjT6W', 'Abdul Rahman, S.H.', '196911301993031003', 'Panitera Pengganti', 'III/d', '1993-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:23', '2025-12-08 10:49:59', 1),
(89, '197112242002122001', '$2y$10$tbUO5vx4F1El4nna36pTXe62WOB4xHJNhUeJWZIhPsFQkRRAaEN2G', 'Andi Tenri, S.Ag.', '197112242002122001', 'Panitera Pengganti', 'III/d', '2002-12-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:23', '2025-12-08 10:49:59', 1),
(90, '196705151994032006', '$2y$10$ELUHG6QCWZl9Anl5TPh7SONbtEGZoP/lrczB2Yak3ZxRptgD4nD2.', 'Dra. St. Syahribulan', '196705151994032006', 'Panitera Pengganti', 'III/d', '1994-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:23', '2025-12-08 10:49:59', 1),
(91, '196708011992031003', '$2y$10$UKFJLRLsY/tbfagYBQKJnug2/FIG/D8FMnvEsNz2oNk0vlMUhPehy', 'Hayad Jusa, S.Ag.', '196708011992031003', 'Panitera Pengganti', 'III/d', '1992-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:23', '2025-12-08 10:49:59', 1),
(92, '196408081994021001', '$2y$10$6zNKMKD2Elh2jR1FiKruXuaTzhH8DHI2wTf.V3bb7bS1zL1EeEccq', 'Drs. Istambul', '196408081994021001', 'Panitera Pengganti', 'III/d', '1994-02-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:23', '2025-12-08 10:49:59', 1),
(93, '196509091994022001', '$2y$10$.Z4SrikW88QR19MVqCrlq.nBunIRka8jGGVbO6I7sPiYql9lEhHG.', 'Haryati, S.H.', '196509091994022001', 'Panitera Pengganti', 'III/d', '1994-02-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:23', '2025-12-08 10:49:59', 1),
(94, '196604221994021001', '$2y$10$dtGJMDqWvkFdk7CQMmub2OMGSyO3DDZBof.V0UCS.t1EUqAkcuPP2', 'Aris, S.H.', '196604221994021001', 'Panitera Pengganti', 'III/d', '1994-02-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:24', '2025-12-08 10:49:59', 1),
(95, '196312312001122005', '$2y$10$efge57SroUnXdaYGkKyy7u535GBbQ/jL2jF8uW.8LCTXVijhNf7wa', 'Dra. St. Kasmiah', '196312312001122005', 'Panitera Pengganti', 'III/d', '2001-12-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:24', '2025-12-08 10:49:59', 1),
(96, '196602101994022001', '$2y$10$.35T7jvNZBD4vdyVvJdbNeNyo1FCwxqhr/lHqU3v7LaCGHrU6PzK6', 'Dra. Jasrawati', '196602101994022001', 'Panitera Pengganti', 'III/d', '1994-02-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:24', '2025-12-08 10:49:59', 1),
(97, '196612081995032001', '$2y$10$BB3qML8Z4ZTd9my1VkAJAu/.lprdCntR0ov3OPkVycbjVaU0EHH2C', 'Dra. Sehati', '196612081995032001', 'Panitera Pengganti', 'III/d', '1995-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:24', '2025-12-08 10:49:59', 1),
(98, '196701111993032003', '$2y$10$OnLbnJoaL6s6jtpm9sSAg.vnfWPm9L4g4/SgO.wJR0WaWWXR1JDBW', 'Dra. Fitriani', '196701111993032003', 'Panitera Pengganti', 'III/d', '1993-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:24', '2025-12-08 10:49:59', 1),
(99, '196612311987031017', '$2y$10$9Jzw9mLZmyVl98E9jMlEH.OGyAm/mT36Y9.1aLyfL1z88Sn878ivK', 'A. Napi, S.Ag.', '196612311987031017', 'Panitera Pengganti', 'III/d', '1987-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:24', '2025-12-08 10:49:59', 1),
(100, '196712311994012002', '$2y$10$.hYDwL/gWPQYXwmTvmqtB.R4y5QaoD1XARYuWO820BtYvaiKevMQm', 'Dra Nursyaya', '196712311994012002', 'Panitera Pengganti', 'III/d', '1994-01-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:24', '2025-12-08 10:49:59', 1),
(101, '196812081994021002', '$2y$10$HeihR9ewCpKb3aqzEDL3Z.UundJKc0ofjQeos6h.CSYVZ7qeLw6Zu', 'Andi M. Zulkarnain Chalid, S.H.', '196812081994021002', 'Panitera Pengganti', 'III/d', '1994-02-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:24', '2025-12-08 10:49:59', 1),
(102, '197103051998032002', '$2y$10$LUF69SxwtShXbLO/TGEnauvgfk4LRDkGDO.yNJHpd56qqQHvwY8YS', 'Nur Intang, S.Ag.', '197103051998032002', 'Panitera Pengganti', 'III/d', '1998-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:25', '2025-12-08 10:49:59', 1),
(103, '197112201992022001', '$2y$10$xRperXqFi62UYXlmsb15wea2KSO4S8iciZKeKD3U4ujFXpfgUqVNe', 'Nur Qalbi Patawari, S.Ag.', '197112201992022001', 'Panitera Pengganti', 'III/d', '1992-02-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:25', '2025-12-08 10:49:59', 1),
(104, '196810012014082003', '$2y$10$O2uS3iBb7AipwCAn6XO5LubDo8RvfTfVq1JY/X76zzzfKX044CATW', 'Dra. Munirah', '196810012014082003', 'Panitera Pengganti', 'III/c', '2014-08-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:25', '2025-12-08 10:49:59', 1),
(105, '197504072006042001', '$2y$10$n6DumEQAXqIDCvsG/mF94O0vPLfmRgNixk6ULZWJr1xSeuO6qyb7e', 'Nailah Yahya, S.Ag., M.Ag.', '197504072006042001', 'Kepala Subbagian, Subbagian Rencana Program dan Anggaran', 'IV/a', '2006-04-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:25', '2025-12-08 10:49:59', 1),
(106, '198508012011011010', '$2y$10$ySl1r5ilaWx4MvnF1fdmXu8v/kBDGQ8PZ7yiQ9ZZq1iOkjh44MNWi', 'Verry Setya Widyatama, S.Kom.', '198508012011011010', 'Kepala Subbagian, Subbagian Kepegawaian dan Teknologi Informasi', 'III/d', '2011-01-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:25', '2025-12-08 10:49:59', 1),
(107, '198409252009121004', '$2y$10$z8VZ9EvcrvA1dnWCvlDNpOk7vzWAi4Yi.OYKdamalkYOpNLGxaXvq', 'Muhammad Silmi, S.Kom.', '198409252009121004', 'Kepala Subbagian, Subbagian Tata Usaha dan Rumah Tangga', 'III/d', '2009-12-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:25', '2025-12-08 10:49:59', 1),
(108, '198009252005022001', '$2y$10$n1pu0c3x6v9vpAXGONJHyuHhKFGU76fujMAQkRZS6u3tsu5PrN1Qe', 'Nur Azizah Zainal, S.E.', '198009252005022001', 'Kepala Subbagian, Subbagian Keuangan dan Pelaporan', 'III/d', '2005-02-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:25', '2025-12-08 10:49:59', 1),
(109, '197103101998032011', '$2y$10$OA2WoRqw9nAuT4P4GYFaMujkobSl3j09yNsf2xukzUWFvBgL9waC2', 'Anniswaty Hafid, S.Sos.,M.M.', '197103101998032011', 'Pustakawan Ahli Madya, Sekretaris', 'IV/c', '1998-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:25', '2025-12-08 10:49:59', 1),
(110, '198204022006042004', '$2y$10$YGuz6/7Ohr4oVBLPBPHgqOwKdJ7ZqEdeHHarti7Cjp56KAb9Qtt..', 'Karmawati, S.Pd., M.Pd.', '198204022006042004', 'Klerek - Analis Perkara Peradilan, Panitera Muda Hukum', 'IV/a', '2006-04-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:26', '2025-12-08 10:49:59', 1),
(111, '198207072006041017', '$2y$10$oYK3g8qDlxeEmBZuhmuLA.kws1T4rHoMFxQlVnUUar/nrTRECDs/2', 'Latuo, S.S., M.S.i.', '198207072006041017', 'Klerek - Penata Keprotokolan, Subbagian Tata Usaha dan Rumah Tangga', 'IV/a', '2006-04-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:26', '2025-12-08 10:49:59', 1),
(112, '197203162006042002', '$2y$10$nVA8SR71hKH4DqgzZqp1EOVe1QOdT1d7NfVe5HqH/E22r2fVgfW7W', 'Andi Iswaty Batariola, S.H.', '197203162006042002', 'Klerek - Analis Perkara Peradilan, Panitera Muda Banding', 'III/d', '2006-04-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:26', '2025-12-08 10:49:59', 1),
(113, '197911202009122001', '$2y$10$8.AZ/YXGOlk1zFFmNhVsO.P8vMkZEw2f7lyzT8iqg.F8Jr5v9d6Ly', 'Suhartini, S.Kom., S.H.', '197911202009122001', 'Klerek - Analis Perkara Peradilan, Panitera Muda Banding', 'III/d', '2009-12-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:26', '2025-12-08 10:49:59', 1),
(114, '198508092010012027', '$2y$10$8HtIdZ4/sK68ws8VdjZw9eZBy5TdS8SeZcIjIfC1hFdVTK.kWkhju', 'Nur Haerani, S.H.', '198508092010012027', 'Klerek - Analis Perkara Peradilan, Panitera Muda Banding', 'III/d', '2010-01-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:26', '2025-12-08 10:49:59', 1),
(115, '198712152011012020', '$2y$10$l514yV711xHgiFulzSeraeadu26usOelNMgBUizYqTg2D/ov9ubhe', 'Mawar Putri, S.E.M.Si.,AK', '198712152011012020', 'Pranata Keuangan APBN Penyelia, Sekretaris', 'III/d', '2011-01-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:26', '2025-12-08 10:49:59', 1),
(116, '198210152009121001', '$2y$10$EZ4GfsDe7RSyuWpvHG0WIeC0sxD8yd1kT0zzQQl3tQNM8F9KG633O', 'A. Maradona, S.H.I.', '198210152009121001', 'Analis Pengelolaan Keuangan APBN Ahli Muda, Sekretaris', 'III/d', '2009-12-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:26', '2025-12-08 10:49:59', 1),
(117, '198109082011011007', '$2y$10$FLclN0nrtQgBpNSB60x4YuAHW9tqOgztwZMcl.dYh3a25CoWMAoYK', 'Darias, S.Kom.', '198109082011011007', 'Operator - Penata Layanan Operasional, Subbagian Kepegawaian dan Teknologi Informasi', 'III/d', '2011-01-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:26', '2025-12-08 10:49:59', 1),
(118, '198501142015042001', '$2y$10$OH1LAm3gRwDGpoDJAnbEou03YM4lmXYu11mP.TxySGiidXdhwohtG', 'Nur Hikmah, S.H.', '198501142015042001', 'Klerek - Analis Perkara Peradilan, Panitera Muda Banding', 'III/c', '2015-04-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:27', '2025-12-08 10:49:59', 1),
(119, '197612122011012005', '$2y$10$AuFKdy8aUxVoCBsK9xfoeOIMVQmUWu7hxKbqSNvDEn7gb7dcNa0rO', 'Husnaeni, S.H.I., M.H.', '197612122011012005', 'Analis Sumber Daya Manusia Aparatur Ahli Muda, Sekretaris', 'III/c', '2011-01-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:27', '2025-12-08 10:49:59', 1),
(120, '198412132008052001', '$2y$10$fpdIaqd5js8eZH51VEoLGevH4aixUu.kTLOo4/QTr4JmCWSMF6pHW', 'Nur Rahma Baharuddin, S.SI.', '198412132008052001', 'Analis Sumber Daya Manusia Aparatur Ahli Muda, Subbagian Kepegawaian dan Teknologi Informasi', 'III/c', '2008-05-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:27', '2025-12-08 10:49:59', 1),
(121, '199006032012122003', '$2y$10$gidlJwUTpxhHC8SbMuIcwOzPA./UQGUMS8WW4yWiFiGr25Pwj/oVq', 'Syahruni Syamsu Umar, S.H.', '199006032012122003', 'Analis Sumber Daya Manusia Aparatur Ahli Muda, Subbagian Kepegawaian dan Teknologi Informasi', 'III/c', '2012-12-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:27', '2025-12-08 10:49:59', 1),
(122, '198909032011012006', '$2y$10$xw7A.P9CQw/ZHC.wQVuwmO3SWd4wNVdEHK7pE.z114iMmc48.X2NW', 'Dewi Sartika, S.Ft., M.Fis.', '198909032011012006', 'Operator - Penata Layanan Operasional, Subbagian Kepegawaian dan Teknologi Informasi', 'III/c', '2011-01-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:27', '2025-12-08 10:49:59', 1),
(123, '197612132014082001', '$2y$10$SHEmDBjnsXc7EwqDvEwodecPq7ZChVVIo5mHSfez2wIaw5YtNuDZq', 'Kamaliah, S.H.', '197612132014082001', 'Operator - Penata Layanan Operasional, Subbagian Tata Usaha dan Rumah Tangga', 'III/c', '2014-08-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:27', '2025-12-08 10:49:59', 1),
(124, '199704292020122008', '$2y$10$3P0CsEcABdjmgFg6S16Uje/jZgjGILyhyzTQNt5ALVCnQmMgtxN5W', 'Rifdah Fausiah Ashari, S.T.', '199704292020122008', 'Pranata Komputer Ahli Pertama, Bagian Perencanaan dan Kepegawaian', 'III/b', '2020-12-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:27', '2025-12-08 10:49:59', 1),
(125, '199401182019031002', '$2y$10$NU8ixQ4vM5bfAdOB/snerOoaLqPu1sQsayyw3ni.FCGgXPwaO65U.', 'Isnawan Abdhi, S.H.', '199401182019031002', 'Klerek - Analis Perkara Peradilan, Panitera Muda Hukum', 'III/b', '2019-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:27', '2025-12-08 10:49:59', 1),
(126, '198507012019031003', '$2y$10$5F5VnR6IWib96HAbB5IpI.r4dtg.Zfd1IhepK89czxV7Hshkw2TlK', 'Jamaluddin, S.E.', '198507012019031003', 'Analis Pengelolaan Keuangan APBN Ahli Pertama, Sekretaris', 'III/b', '2019-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:28', '2025-12-08 10:49:59', 1),
(127, '199612232020122003', '$2y$10$jxuRedMSB2KG.53IYWZ6JOvPy58VWUKwTiFuTyiOg4c4JwX7pArxK', 'Gebi Ajeng Harun, S.A.P', '199612232020122003', 'Operator - Penata Layanan Operasional, Subbagian Kepegawaian dan Teknologi Informasi', 'III/b', '2020-12-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:28', '2025-12-08 10:49:59', 1),
(128, '198812212015031001', '$2y$10$ibOZC/Z838lznyjLAHzyOu6WkEKRj0jew84O2lnvub5uqANJGx4le', 'Dadang Soenandar Hamzah, S.E.,M.H.', '198812212015031001', 'Operator - Penata Layanan Operasional, Subbagian Keuangan dan Pelaporan', 'III/b', '2015-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:28', '2025-12-08 10:49:59', 1),
(129, '199508152024051001', '$2y$10$aFCDFBzpkxJetU3ICwa9vu84xjQwbXVM0ncWJ3K4LTrwjP4CrfOz6', 'Muhammad Muaz Ikhsan, S.H.', '199508152024051001', 'Klerek - Analis Perkara Peradilan, Panitera Muda Hukum', 'III/a', '2024-05-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:28', '2025-12-08 10:49:59', 1),
(130, '199703282024051001', '$2y$10$f46tGroI/yV2PS/bYYPapuWVH75gJKhxLfA801KHHnSCjYzu4tIjK', 'Am Naufal Maulana, S.H.', '199703282024051001', 'Klerek - Analis Perkara Peradilan, Panitera Muda Banding', 'III/a', '2024-05-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:28', '2025-12-08 10:49:59', 1),
(131, '199805212020122004', '$2y$10$lVFMFWaCE4CeUrPWo5I8AOXFRrcuK7Xh9YqjwnqFctRy5xAR/C4Wm', 'Rezki Amalia, S.Tr.A.B.', '199805212020122004', 'Operator - Penata Layanan Operasional, Subbagian Rencana Program dan Anggaran', 'III/a', '2020-12-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:28', '2025-12-08 10:49:59', 1),
(132, '199204252022032007', '$2y$10$0xR9Kg3DMEmFyof9W/14Mu4zWCeD06VbUDWPn8MQdvuJKN6VoiPVe', 'Nadia Hamkan Bugis, S.Tr.A.B.', '199204252022032007', 'Operator - Penata Layanan Operasional, Subbagian Rencana Program dan Anggaran', 'III/a', '2022-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:28', '2025-12-08 10:49:59', 1),
(133, '199210032022031006', '$2y$10$OT/KhRnZGcof0jDFhbDXMO4EQHYiAAfJot27gSTq6pYMgrZBM3c4e', 'Aditya Permana, S.E.', '199210032022031006', 'Klerek - Penelaah Teknis Kebijakan, Subbagian Rencana Program dan Anggaran', 'III/a', '2022-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:29', '2025-12-08 10:49:59', 1),
(134, '200104152025062017', '$2y$10$F8cSkd/rEAFbSJsv37r4TuNXEKID9WdAdJ8Vu67a1rl7uZBQdVrNC', 'NURUL ANNIZA BASRI, S.Kom.', '200104152025062017', 'Penata Kelola Sistem dan Teknologi Informasi, Subbagian Kepegawaian dan Teknologi Informasi', 'III/a', '2025-06-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:29', '2025-12-08 10:49:59', 1),
(135, '200103022025062011', '$2y$10$vcZNnycJUNlgQWFueKYjsuoPWNrymXO7mT7upOAAIh.cL8DPp6chO', 'IFTINAN ADHASARI PRAMESTHY, S.I.Kom.', '200103022025062011', 'Klerek - Penata Keprotokolan, Subbagian Tata Usaha dan Rumah Tangga', 'III/a', '2025-06-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:29', '2025-12-08 10:49:59', 1),
(136, '200208042025062009', '$2y$10$3xrCICZxSROWcsX83fQ4h..RHVddwClWRIedYwLDBKAcMo0DGuHx6', 'SITI DATRI CAHYATI, S.T.', '200208042025062009', 'Teknisi Sarana dan Prasarana, Subbagian Tata Usaha dan Rumah Tangga', 'III/a', '2025-06-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:29', '2025-12-08 10:49:59', 1),
(137, '199111202022032009', '$2y$10$LheEQF.8eQKFEVTAPVMwoOTF4UbdsNG67tMpHQKMeGtZhqe7vitsO', 'Nur Achfiah Budhi Artha, S.ST.', '199111202022032009', 'Operator - Penata Layanan Operasional, Subbagian Keuangan dan Pelaporan', 'III/a', '2022-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:29', '2025-12-08 10:49:59', 1),
(138, '199505082022032019', '$2y$10$YE4uQnZdu.damNZbPhNrhOqREJuufNmGC9ygM6kE6WjCgtc1FKiC2', 'Watik, A.Md.', '199505082022032019', 'Klerek - Pengolah Data dan Informasi, Subbagian Kepegawaian dan Teknologi Informasi', 'II/c', '2022-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:29', '2025-12-08 10:49:59', 1),
(139, '199509092022032015', '$2y$10$WigZMYSucp2qNj3rZPCdXeappKpTG5R.TIWJIGAD2h1O/t.oqIYxW', 'Sylvana Praditarani, A.Md', '199509092022032015', 'Klerek - Pengolah Data dan Informasi, Subbagian Tata Usaha dan Rumah Tangga', 'II/c', '2022-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:29', '2025-12-08 10:49:59', 1),
(140, '199908062022031005', '$2y$10$2tQlFqEiy2xCX8QbbQa8T.QbTGTStVYxc2sUzZazATGFounU4IBOe', 'Sinang Mahatma Dhewa, A.Md.Bns', '199908062022031005', 'Klerek - Pengolah Data dan Informasi, Subbagian Keuangan dan Pelaporan', 'II/c', '2022-03-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:29', '2025-12-08 10:49:59', 1),
(141, '198011152023211008', '$2y$10$wfKKqaP1FRB3xpTARfRQ4eantFPD2a2CLHStJJWg1dkhzfKmoTZ7S', 'Ahmad Ridha, S.E.', '198011152023211008', 'Arsiparis Ahli Pertama, Sekretaris', 'IX', '1900-01-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:30', '2025-12-08 10:49:59', 1),
(142, '199609212023212031', '$2y$10$FiLxPcjQQC8OuHkI37DrwOBA6M6kbat98n.Wa/UNNL2i5YuL.3c2a', 'Satriani Har, S.M.', '199609212023212031', 'Perencana Ahli Pertama, Sekretaris', 'IX', '1900-01-01', NULL, '', 0, NULL, 0, NULL, '2025-08-19 05:17:30', '2025-12-08 10:49:59', 1),
(143, 'tester', '$2y$10$vJqv49x499/M/DnbtSZEIuHO3oSyejzEwUHjdpdqM1M9h89M2XlWy', 'Alif Cukurukuk', '200305282024081002', 'Akun Test', 'III/b', '2024-08-01', 10, '', 0, NULL, 0, NULL, '2025-08-20 00:20:33', '2025-12-08 10:49:59', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_signatures`
--

CREATE TABLE `user_signatures` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `signature_type` enum('user','admin','paraf','paraf_kasubbag','paraf_kabag','paraf_sekretaris') NOT NULL DEFAULT 'user' COMMENT 'Tipe tanda tangan: user (pegawai), admin (approver), paraf (petugas cuti), paraf_kasubbag (atasan cuti - kasubbag), paraf_kabag (atasan cuti - kabag), paraf_sekretaris (atasan cuti - sekretaris)',
  `signature_file` varchar(255) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_signatures`
--

INSERT INTO `user_signatures` (`id`, `user_id`, `signature_type`, `signature_file`, `file_size`, `file_type`, `is_active`, `created_at`, `updated_at`) VALUES
(24, 117, 'paraf_kasubbag', 'parafAtasan/img-parafAtsanCutiKasubbag_117_20260226144725.png', 8409, 'image/png', 1, '2026-02-26 06:47:25', '2026-02-26 06:47:25'),
(25, 45, 'paraf_kabag', 'parafAtasan/img-parafAtsanCutiKabag_45_20260226161900.png', 125455, 'image/png', 1, '2026-02-26 08:19:00', '2026-02-26 08:19:00'),
(29, 44, 'paraf_sekretaris', 'parafAtasan/img-parafAtsanCutiSekretaris_44_20260228143852.png', 2167, 'image/png', 1, '2026-02-28 06:38:52', '2026-02-28 06:38:52'),
(30, 146, 'user', 'signature_user_146_20260302105347.png', 99155, 'image/png', 1, '2026-03-02 02:53:47', '2026-03-02 02:53:47');

-- --------------------------------------------------------

--
-- Table structure for table `user_snapshots`
--

CREATE TABLE `user_snapshots` (
  `id` int(11) NOT NULL,
  `original_user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nip` varchar(20) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `golongan` varchar(10) DEFAULT NULL,
  `tanggal_masuk` date NOT NULL,
  `unit_kerja` int(11) NOT NULL,
  `atasan` int(11) DEFAULT NULL,
  `user_type` enum('pegawai','atasan','admin') NOT NULL,
  `snapshot_type` enum('modified','deleted') NOT NULL DEFAULT 'modified',
  `snapshot_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `reason` text DEFAULT NULL COMMENT 'Alasan snapshot dibuat'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_snapshots`
--

INSERT INTO `user_snapshots` (`id`, `original_user_id`, `username`, `nama`, `nip`, `jabatan`, `golongan`, `tanggal_masuk`, `unit_kerja`, `atasan`, `user_type`, `snapshot_type`, `snapshot_date`, `reason`) VALUES
(56, 143, 'tester', 'Alif Cukurukuk', '200305282024081002', 'Akun Test', 'III/b', '2024-08-01', 1, NULL, '', 'modified', '2025-08-20 02:47:11', 'User data modified'),
(60, 143, 'tester', 'Alif Cukurukuk', '200305282024081002', 'Akun Test', 'III/b', '2024-08-01', 1, NULL, '', 'modified', '2026-01-02 12:54:08', 'User data modified'),
(66, 143, 'tester', 'Alif Cukurukuk', '200305282024081002', 'Akun Test', 'III/b', '2024-08-01', 1, NULL, '', 'modified', '2026-01-03 09:18:56', 'User data modified'),
(67, 146, '195912311986031038', 'Dr. Drs. Khaeril  R, M.H.', '195912311986031038', 'Ketua', 'IV/e', '1986-03-01', 1, NULL, '', 'modified', '2026-01-16 09:59:21', 'User data modified'),
(68, 107, '198409252009121004', 'Muhammad Silmi, S.Kom.', '198409252009121004', 'Kepala Subbagian, Subbagian Tata Usaha dan Rumah Tangga', 'III/d', '2009-12-01', 1, NULL, '', 'modified', '2026-01-16 10:26:36', 'User data modified'),
(69, 146, '195912311986031038', 'Dr. Drs. Khaeril  R, M.H.', '195912311986031038', 'Ketua', 'IV/e', '1986-03-01', 1, NULL, '', 'modified', '2026-01-16 10:27:17', 'User data modified'),
(70, 146, '195912311986031038', 'Dr. Drs. Khaeril  R, M.H.', '195912311986031038', 'Ketua', 'IV/e', '1986-03-01', 1, NULL, '', 'modified', '2026-01-16 10:27:34', 'User data modified'),
(71, 146, '195912311986031038', 'Dr. Drs. Khaeril  R, M.H.', '195912311986031038', 'Ketua', 'IV/e', '1986-03-01', 1, NULL, '', 'modified', '2026-01-16 10:27:50', 'User data modified'),
(72, 45, '197803091998031002', 'Dr. Muhammad Busyaeri, S.H., M.H.', '197803091998031002', 'Kepala Bagian Perencanaan dan Kepegawaian', 'IV/b', '1998-03-01', 1, NULL, '', 'modified', '2026-01-16 10:29:42', 'User data modified'),
(74, 143, 'tester', 'Alif Cukurukuk', '200305282024081002', 'Akun Test', 'III/b', '2024-08-01', 1, NULL, '', 'modified', '2026-01-16 10:38:24', 'User data modified'),
(75, 143, 'tester', 'Alif Cukurukuk', '200305282024081002', 'Akun Test', 'III/b', '2024-08-01', 1, NULL, '', 'modified', '2026-01-16 10:39:22', 'User data modified'),
(77, 143, 'tester', 'Alif Cukurukuk', '200305282024081002', 'Akun Test', 'III/b', '2024-08-01', 1, NULL, '', 'modified', '2026-01-16 10:41:48', 'User data modified'),
(79, 44, '197011021997031001', 'Dr. Abdul Mutalip, S.Ag., S.H., M.H.', '197011021997031001', 'Sekretaris', 'IV/c', '1997-03-01', 1, NULL, '', 'modified', '2026-02-16 06:03:59', 'User data modified'),
(80, 117, '198109082011011007', 'Darias, S.Kom.', '198109082011011007', 'Operator - Penata Layanan Operasional, Subbagian Kepegawaian dan Teknologi Informasi', 'III/d', '2011-01-01', 1, NULL, '', 'modified', '2026-02-16 06:14:30', 'User data modified'),
(81, 27, '195901311990031001', 'Drs. Muhammad Alwi, M.H.', '195901311990031001', 'Wakil Ketua', 'IV/e', '1990-03-01', 1, NULL, 'pegawai', 'modified', '2026-02-23 08:45:06', 'User data modified'),
(82, 117, '198109082011011007', 'Darias, S.Kom.', '198109082011011007', 'Operator - Penata Layanan Operasional, Subbagian Kepegawaian dan Teknologi Informasi', 'III/d', '2011-01-01', 1, NULL, 'atasan', 'modified', '2026-02-23 08:49:25', 'User data modified'),
(83, 106, '198508012011011010', 'Verry Setya Widyatama, S.Kom.', '198508012011011010', 'Kepala Subbagian, Subbagian Kepegawaian dan Teknologi Informasi', 'III/d', '2011-01-01', 1, NULL, 'atasan', 'modified', '2026-03-02 04:07:40', 'User data modified'),
(84, 140, '199908062022031005', 'Sinang Mahatma Dhewa, A.Md.Bns', '199908062022031005', 'Klerek - Pengolah Data dan Informasi, Subbagian Keuangan dan Pelaporan', 'II/c', '2022-03-01', 1, NULL, 'pegawai', 'modified', '2026-03-03 02:02:10', 'User data modified'),
(85, 140, '199908062022031005', 'Sinang Mahatma Dhewa, A.Md.Bns', '199908062022031005', 'Klerek - Pengolah Data dan Informasi, Subbagian Keuangan dan Pelaporan', 'II/c', '2022-03-01', 1, NULL, 'pegawai', 'modified', '2026-03-03 02:04:25', 'User data modified'),
(88, 143, 'tester', 'Alif Cukurukuk', '200305282024081002', 'Akun Test', 'III/b', '2024-08-01', 1, NULL, 'pegawai', 'modified', '2026-03-05 02:53:55', 'User data modified'),
(89, 3, 'pa_makassar', 'Dr. Hj. Hasnaya H. Abd. Rasyid, M.H.', '196712121993032006', 'Ketua Pengadilan Agama', 'IV/d', '1993-03-01', 2, NULL, 'pegawai', 'modified', '2026-03-05 02:58:21', 'User data modified'),
(90, 3, 'pa_makassar', 'Dr. Hj. Hasnaya H. Abd. Rasyid, M.H.', '196712121993032006', 'Ketua Pengadilan Agama', 'IV/d', '1993-03-01', 2, NULL, 'admin', 'modified', '2026-03-05 03:01:35', 'User data modified'),
(91, 3, 'pa_makassar', 'Dr. Hj. Hasnaya H. Abd. Rasyid, M.H.', '196712121993032006', 'Ketua Pengadilan Agama', 'IV/d', '1993-03-01', 2, NULL, 'pegawai', 'modified', '2026-03-05 03:01:44', 'User data modified'),
(92, 3, 'pa_makassar', 'Dr. Hj. Hasnaya H. Abd. Rasyid, M.H.', '196712121993032006', 'Ketua Pengadilan Agama', 'IV/d', '1993-03-01', 2, NULL, 'admin', 'modified', '2026-03-05 03:02:01', 'User data modified'),
(93, 143, 'tester', 'Alif Cukurukuk', '200305282024081002', 'Akun Test', 'III/b', '2024-08-01', 1, NULL, 'admin', 'modified', '2026-03-05 03:32:51', 'User data modified'),
(109, 48, '196512081993031007', 'Hasbi, S.H., M.H.', '196512081993031007', 'Panitera Muda Banding', 'IV/a', '1993-03-01', 1, NULL, 'pegawai', 'modified', '2026-03-09 07:15:16', 'User data modified'),
(110, 47, '197211101998022002', 'Nurbaya, S.Ag., M.H.I.', '197211101998022002', 'Panitera Muda Hukum', 'IV/a', '1998-02-01', 1, NULL, 'pegawai', 'modified', '2026-03-09 07:15:26', 'User data modified'),
(111, 143, 'tester', 'Alif Cukurukuk', '200305282024081002', 'Akun Test', 'III/b', '2024-08-01', 1, NULL, 'pegawai', 'modified', '2026-03-18 23:58:23', 'User data modified'),
(112, 143, 'tester', 'Alif Cukurukuk', '200305282024081002', 'Akun Test', 'III/b', '2024-08-01', 1, NULL, 'pegawai', 'modified', '2026-03-18 23:58:56', 'User data modified'),
(113, 143, 'tester', 'Alif Cukurukuk', '200305282024081005', 'Akun Test', 'III/b', '2024-08-01', 1, NULL, 'pegawai', 'modified', '2026-03-18 23:59:41', 'User data modified'),
(114, 143, 'tester', 'Alif Cukurukuk', '200305282024081008', 'Akun Test', 'III/b', '2024-08-01', 1, NULL, 'pegawai', 'modified', '2026-03-18 23:59:58', 'User data modified'),
(115, 143, 'tester', 'Alif Cukurukuk', '200305282024081008', 'Akun Test', 'III/b', '2024-08-01', 1, NULL, 'pegawai', 'modified', '2026-03-19 00:05:40', 'User data modified'),
(116, 143, 'tester', 'Alif Cukurukuk', '200305282024081008', 'Akun Test, Subbagian Kepegawaian dan Teknologi Informasi', 'III/b', '2024-08-01', 1, 10, 'pegawai', 'modified', '2026-03-22 09:01:04', 'User data modified'),
(117, 143, 'tester', 'Alif Cukurukuk', '200305282024081008', 'Akun Test, Subbagian Kepegawaian dan Teknologi Informasi', 'III/b', '2024-08-01', 1, 10, 'pegawai', 'modified', '2026-03-23 02:16:25', 'User data modified'),
(118, 143, 'tester', 'Alif Cukurukuk', '200305282024081008', 'Akun Test, Subbagian Kepegawaian dan Teknologi Informasi', 'III/b', '2024-08-01', 1, 10, 'pegawai', 'modified', '2026-03-23 02:16:44', 'User data modified'),
(119, 143, 'tester', 'Alif Cukurukuk', '200305282024081008', 'Akun Test', 'III/b', '2024-08-01', 1, NULL, 'pegawai', 'modified', '2026-03-23 02:16:55', 'User data modified'),
(120, 143, 'tester', 'Alif Cukurukuk', '200305282024081008', 'Akun Test', 'III/b', '2024-08-01', 1, NULL, 'pegawai', 'modified', '2026-03-23 02:17:11', 'User data modified'),
(121, 143, 'tester', 'Alif Cukurukuk', '200305282024081008', 'Akun Test, Hakim Tinggi', 'III/b', '2024-08-01', 1, NULL, 'pegawai', 'modified', '2026-03-23 02:17:26', 'User data modified'),
(122, 143, 'tester', 'Alif Cukurukuk', '200305282024081008', 'Hakim Tinggi', 'III/b', '2024-08-01', 1, 1, 'pegawai', 'modified', '2026-03-27 10:04:56', 'User data modified'),
(123, 143, 'tester', 'Alif Cukurukuk', '200305282024081008', 'Akun Test,', 'III/b', '2024-08-01', 1, NULL, 'pegawai', 'modified', '2026-03-27 10:05:35', 'User data modified'),
(124, 143, 'tester', 'Alif Cukurukuk', '200305282024081008', 'Akun Test, Subbagian Kepegawaian dan Teknologi Informasi', 'III/b', '2024-08-01', 1, 10, 'pegawai', 'modified', '2026-03-28 09:53:51', 'User data modified'),
(125, 143, 'tester', 'Alif Cukurukuk', '200305282024081008', 'Akun Test, Subbagian Kepegawaian dan Teknologi Informasi', 'III/b', '2024-08-01', 1, NULL, 'admin', 'modified', '2026-04-05 02:53:38', 'User data modified'),
(126, 143, 'tester', 'Alif Cukurukuk', '200305282024081008', 'Hakim Tinggi', 'III/b', '2024-08-01', 1, 1, 'pegawai', 'modified', '2026-04-06 02:17:38', 'User data modified'),
(127, 143, 'tester', 'Alif Cukurukuk', '200305282024082008', 'Hakim Tinggi', 'III/b', '2024-08-01', 1, 1, 'pegawai', 'modified', '2026-04-06 07:49:55', 'User data modified'),
(131, 143, 'tester', 'Alif Cukurukuk', '200305282024082008', 'Akun Test, Subbagian Kepegawaian dan Teknologi Informasi', 'III/b', '2024-08-01', 1, 10, 'pegawai', 'modified', '2026-04-06 13:10:40', 'User data modified');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_leave_history_complete`
-- (See below for the actual view)
--
CREATE TABLE `v_leave_history_complete` (
`id` int(11)
,`user_id` int(11)
,`user_snapshot_id` int(11)
,`leave_type_id` int(11)
,`nomor_surat` varchar(50)
,`tanggal_mulai` date
,`tanggal_selesai` date
,`jumlah_hari` int(11)
,`alasan` text
,`alamat_cuti` text
,`telepon_cuti` varchar(20)
,`dokumen_pendukung` varchar(255)
,`catatan_cuti` text
,`status` enum('draft','pending','pending_kasubbag','pending_kabag','pending_sekretaris','awaiting_pimpinan','pending_admin_upload','approved','rejected','changed','postponed')
,`is_completed` tinyint(1)
,`approved_by` int(11)
,`approval_date` datetime
,`catatan_approval` text
,`created_at` timestamp
,`updated_at` timestamp
,`blanko_uploaded` tinyint(1)
,`blanko_upload_date` datetime
,`final_blanko_sent` tinyint(1)
,`final_blanko_sent_date` datetime
,`quota_deducted` tinyint(1)
,`jumlah_hari_ditangguhkan` int(11)
,`nama_cuti` varchar(50)
,`nama` varchar(100)
,`unit_kerja` int(11)
,`nip` varchar(20)
,`jabatan` varchar(100)
,`golongan` varchar(10)
,`user_status` varchar(8)
,`snapshot_type` enum('modified','deleted')
,`snapshot_date` timestamp
);

-- --------------------------------------------------------

--
-- Structure for view `v_leave_history_complete`
--
DROP TABLE IF EXISTS `v_leave_history_complete`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_leave_history_complete`  AS SELECT `lr`.`id` AS `id`, `lr`.`user_id` AS `user_id`, `lr`.`user_snapshot_id` AS `user_snapshot_id`, `lr`.`leave_type_id` AS `leave_type_id`, `lr`.`nomor_surat` AS `nomor_surat`, `lr`.`tanggal_mulai` AS `tanggal_mulai`, `lr`.`tanggal_selesai` AS `tanggal_selesai`, `lr`.`jumlah_hari` AS `jumlah_hari`, `lr`.`alasan` AS `alasan`, `lr`.`alamat_cuti` AS `alamat_cuti`, `lr`.`telepon_cuti` AS `telepon_cuti`, `lr`.`dokumen_pendukung` AS `dokumen_pendukung`, `lr`.`catatan_cuti` AS `catatan_cuti`, `lr`.`status` AS `status`, `lr`.`is_completed` AS `is_completed`, `lr`.`approved_by` AS `approved_by`, `lr`.`approval_date` AS `approval_date`, `lr`.`catatan_approval` AS `catatan_approval`, `lr`.`created_at` AS `created_at`, `lr`.`updated_at` AS `updated_at`, `lr`.`blanko_uploaded` AS `blanko_uploaded`, `lr`.`blanko_upload_date` AS `blanko_upload_date`, `lr`.`final_blanko_sent` AS `final_blanko_sent`, `lr`.`final_blanko_sent_date` AS `final_blanko_sent_date`, `lr`.`quota_deducted` AS `quota_deducted`, `lr`.`jumlah_hari_ditangguhkan` AS `jumlah_hari_ditangguhkan`, `lt`.`nama_cuti` AS `nama_cuti`, CASE WHEN `us`.`id` is not null THEN `us`.`nama` WHEN `u`.`id` is not null THEN `u`.`nama` ELSE 'Unknown' END AS `nama`, CASE WHEN `us`.`id` is not null THEN `us`.`unit_kerja` WHEN `u`.`id` is not null THEN `u`.`unit_kerja` ELSE NULL END AS `unit_kerja`, CASE WHEN `us`.`id` is not null THEN `us`.`nip` WHEN `u`.`id` is not null THEN `u`.`nip` ELSE 'Unknown' END AS `nip`, CASE WHEN `us`.`id` is not null THEN `us`.`jabatan` WHEN `u`.`id` is not null THEN `u`.`jabatan` ELSE 'Unknown' END AS `jabatan`, CASE WHEN `us`.`id` is not null THEN `us`.`golongan` WHEN `u`.`id` is not null THEN `u`.`golongan` ELSE 'Unknown' END AS `golongan`, CASE WHEN `us`.`id` is not null THEN 'snapshot' WHEN `u`.`id` is not null THEN 'active' ELSE 'unknown' END AS `user_status`, `us`.`snapshot_type` AS `snapshot_type`, `us`.`snapshot_date` AS `snapshot_date` FROM (((`leave_requests` `lr` join `leave_types` `lt` on(`lr`.`leave_type_id` = `lt`.`id`)) left join `users` `u` on(`lr`.`user_id` = `u`.`id` and `u`.`is_deleted` = 0 and `lr`.`user_snapshot_id` is null)) left join `user_snapshots` `us` on(`lr`.`user_snapshot_id` = `us`.`id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `admin_activities`
--
ALTER TABLE `admin_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `leave_id` (`leave_id`);

--
-- Indexes for table `alasan_cuti`
--
ALTER TABLE `alasan_cuti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_type_id` (`leave_type_id`);

--
-- Indexes for table `approval_logs`
--
ALTER TABLE `approval_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_request_id` (`leave_request_id`),
  ADD KEY `step` (`step`);

--
-- Indexes for table `atasan`
--
ALTER TABLE `atasan`
  ADD PRIMARY KEY (`id_atasan`);

--
-- Indexes for table `document_signatures`
--
ALTER TABLE `document_signatures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_request_id` (`leave_request_id`),
  ADD KEY `signature_placeholder_id` (`signature_placeholder_id`);

--
-- Indexes for table `kuota_cuti_alasan_penting`
--
ALTER TABLE `kuota_cuti_alasan_penting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_type_id` (`leave_type_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `kuota_cuti_besar`
--
ALTER TABLE `kuota_cuti_besar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `leave_type_id` (`leave_type_id`);

--
-- Indexes for table `kuota_cuti_luar_tanggungan`
--
ALTER TABLE `kuota_cuti_luar_tanggungan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_type_id` (`leave_type_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `kuota_cuti_melahirkan`
--
ALTER TABLE `kuota_cuti_melahirkan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_type_id` (`leave_type_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `kuota_cuti_sakit`
--
ALTER TABLE `kuota_cuti_sakit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_type_id` (`leave_type_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `leave_balances`
--
ALTER TABLE `leave_balances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `leave_documents`
--
ALTER TABLE `leave_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_documents_ibfk_1` (`leave_request_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_type_id` (`leave_type_id`),
  ADD KEY `user_snapshot_id` (`user_snapshot_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_leave_requests_atasan_id` (`atasan_id`),
  ADD KEY `idx_kasubbag_id` (`kasubbag_id`),
  ADD KEY `idx_status_kasubbag` (`status`,`kasubbag_id`),
  ADD KEY `admin_blanko_sender` (`admin_blankofinal_sender`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migration_logs`
--
ALTER TABLE `migration_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `notifications_ibfk_2` (`related_leave_id`);

--
-- Indexes for table `satker`
--
ALTER TABLE `satker`
  ADD PRIMARY KEY (`id_satker`);

--
-- Indexes for table `signature_placeholders`
--
ALTER TABLE `signature_placeholders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `atasan` (`atasan`),
  ADD KEY `satker` (`unit_kerja`);

--
-- Indexes for table `user_signatures`
--
ALTER TABLE `user_signatures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_snapshots`
--
ALTER TABLE `user_snapshots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `original_user_id` (`original_user_id`),
  ADD KEY `atasan` (`atasan`),
  ADD KEY `unit_kerja` (`unit_kerja`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `admin_activities`
--
ALTER TABLE `admin_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `alasan_cuti`
--
ALTER TABLE `alasan_cuti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `approval_logs`
--
ALTER TABLE `approval_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `atasan`
--
ALTER TABLE `atasan`
  MODIFY `id_atasan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `document_signatures`
--
ALTER TABLE `document_signatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kuota_cuti_alasan_penting`
--
ALTER TABLE `kuota_cuti_alasan_penting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT for table `kuota_cuti_besar`
--
ALTER TABLE `kuota_cuti_besar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;

--
-- AUTO_INCREMENT for table `kuota_cuti_luar_tanggungan`
--
ALTER TABLE `kuota_cuti_luar_tanggungan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `kuota_cuti_melahirkan`
--
ALTER TABLE `kuota_cuti_melahirkan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;

--
-- AUTO_INCREMENT for table `kuota_cuti_sakit`
--
ALTER TABLE `kuota_cuti_sakit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=336;

--
-- AUTO_INCREMENT for table `leave_balances`
--
ALTER TABLE `leave_balances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1648;

--
-- AUTO_INCREMENT for table `leave_documents`
--
ALTER TABLE `leave_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=467;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migration_logs`
--
ALTER TABLE `migration_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `satker`
--
ALTER TABLE `satker`
  MODIFY `id_satker` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `signature_placeholders`
--
ALTER TABLE `signature_placeholders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT for table `user_signatures`
--
ALTER TABLE `user_signatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `user_snapshots`
--
ALTER TABLE `user_snapshots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_activities`
--
ALTER TABLE `admin_activities`
  ADD CONSTRAINT `admin_activities_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_activities_ibfk_2` FOREIGN KEY (`leave_id`) REFERENCES `leave_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `alasan_cuti`
--
ALTER TABLE `alasan_cuti`
  ADD CONSTRAINT `alasan_cuti_ibfk_1` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`);

--
-- Constraints for table `approval_logs`
--
ALTER TABLE `approval_logs`
  ADD CONSTRAINT `approval_logs_ibfk_1` FOREIGN KEY (`leave_request_id`) REFERENCES `leave_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `document_signatures`
--
ALTER TABLE `document_signatures`
  ADD CONSTRAINT `document_signatures_ibfk_1` FOREIGN KEY (`leave_request_id`) REFERENCES `leave_requests` (`id`),
  ADD CONSTRAINT `document_signatures_ibfk_2` FOREIGN KEY (`signature_placeholder_id`) REFERENCES `signature_placeholders` (`id`);

--
-- Constraints for table `kuota_cuti_alasan_penting`
--
ALTER TABLE `kuota_cuti_alasan_penting`
  ADD CONSTRAINT `kuota_cuti_alasan_penting_ibfk_1` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`),
  ADD CONSTRAINT `kuota_cuti_alasan_penting_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kuota_cuti_besar`
--
ALTER TABLE `kuota_cuti_besar`
  ADD CONSTRAINT `kuota_cuti_besar_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kuota_cuti_besar_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`);

--
-- Constraints for table `kuota_cuti_luar_tanggungan`
--
ALTER TABLE `kuota_cuti_luar_tanggungan`
  ADD CONSTRAINT `kuota_cuti_luar_tanggungan_ibfk_1` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`),
  ADD CONSTRAINT `kuota_cuti_luar_tanggungan_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kuota_cuti_melahirkan`
--
ALTER TABLE `kuota_cuti_melahirkan`
  ADD CONSTRAINT `kuota_cuti_melahirkan_ibfk_1` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`),
  ADD CONSTRAINT `kuota_cuti_melahirkan_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kuota_cuti_sakit`
--
ALTER TABLE `kuota_cuti_sakit`
  ADD CONSTRAINT `kuota_cuti_sakit_ibfk_1` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`),
  ADD CONSTRAINT `kuota_cuti_sakit_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_balances`
--
ALTER TABLE `leave_balances`
  ADD CONSTRAINT `leave_balances_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_documents`
--
ALTER TABLE `leave_documents`
  ADD CONSTRAINT `leave_documents_ibfk_1` FOREIGN KEY (`leave_request_id`) REFERENCES `leave_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `admin_blanko_sender` FOREIGN KEY (`admin_blankofinal_sender`) REFERENCES `admin` (`id_admin`),
  ADD CONSTRAINT `fk_leave_requests_atasan` FOREIGN KEY (`atasan_id`) REFERENCES `atasan` (`id_atasan`),
  ADD CONSTRAINT `leave_requests_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`),
  ADD CONSTRAINT `leave_requests_ibfk_3` FOREIGN KEY (`user_snapshot_id`) REFERENCES `user_snapshots` (`id`),
  ADD CONSTRAINT `leave_requests_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`related_leave_id`) REFERENCES `leave_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `atasan` FOREIGN KEY (`atasan`) REFERENCES `atasan` (`id_atasan`),
  ADD CONSTRAINT `satker` FOREIGN KEY (`unit_kerja`) REFERENCES `satker` (`id_satker`);

--
-- Constraints for table `user_signatures`
--
ALTER TABLE `user_signatures`
  ADD CONSTRAINT `user_signatures_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_snapshots`
--
ALTER TABLE `user_snapshots`
  ADD CONSTRAINT `user_snapshots_ibfk_1` FOREIGN KEY (`original_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_snapshots_ibfk_2` FOREIGN KEY (`atasan`) REFERENCES `atasan` (`id_atasan`),
  ADD CONSTRAINT `user_snapshots_ibfk_3` FOREIGN KEY (`unit_kerja`) REFERENCES `satker` (`id_satker`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
