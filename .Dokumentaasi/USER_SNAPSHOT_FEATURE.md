# Fitur User Snapshot - Sistem Pengelolaan Cuti

## Deskripsi
Fitur ini memungkinkan sistem untuk menyimpan dan menampilkan data pengajuan cuti bagi akun yang telah diedit datanya atau dihapus. Admin tetap dapat melihat seluruh riwayat cuti user termasuk akun user yang mengalami perubahan atau telah terhapus.

## Fitur Utama

### 1. Tracking Perubahan Data User
- Sistem otomatis membuat snapshot saat data user diubah
- Menyimpan data user sebelum perubahan dilakukan
- Tracking perubahan pada field penting: nama, NIP, jabatan, unit kerja, golongan

### 2. Soft Delete User
- User tidak benar-benar dihapus dari database
- Data user disimpan dalam snapshot sebelum dihapus
- Admin dapat melihat riwayat user yang telah dihapus

### 3. Riwayat Cuti Lengkap
- Menampilkan riwayat cuti untuk user aktif dan yang telah diubah/dihapus
- Badge status user untuk membedakan user aktif, diubah, atau dihapus
- Filter berdasarkan status user

### 4. Manajemen Snapshot
- Halaman khusus untuk melihat daftar snapshot
- Detail snapshot dengan perbandingan data
- Statistik perubahan user

## Struktur Database

### Tabel Baru

#### 1. `user_snapshots`
```sql
CREATE TABLE `user_snapshots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `original_user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nip` varchar(20) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `golongan` varchar(10) DEFAULT NULL,
  `tanggal_masuk` date NOT NULL,
  `unit_kerja` varchar(100) NOT NULL,
  `user_type` enum('admin','user') NOT NULL DEFAULT 'user',
  `snapshot_type` enum('modified','deleted') NOT NULL DEFAULT 'modified',
  `snapshot_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `reason` text DEFAULT NULL COMMENT 'Alasan snapshot dibuat',
  PRIMARY KEY (`id`)
);
```

### Kolom Baru di Tabel Existing

#### 1. Tabel `users`
- `is_deleted` tinyint(1) DEFAULT 0
- `deleted_at` timestamp NULL DEFAULT NULL
- `is_modified` tinyint(1) DEFAULT 0
- `last_modified_at` timestamp NULL DEFAULT NULL

#### 2. Tabel `leave_requests`
- `user_snapshot_id` int(11) DEFAULT NULL

### View Baru

#### 1. `v_user_data`
View untuk menggabungkan data user aktif dan snapshot

#### 2. `v_leave_history_complete`
View untuk riwayat cuti lengkap dengan status user

## Cara Penggunaan

### 1. Menjalankan Migrasi
```bash
php run_migration.php
```

### 2. Akses Fitur Snapshot
- Login sebagai admin
- Akses menu "Riwayat Perubahan User" di sidebar admin
- Atau langsung ke URL: `/user/snapshots`

### 3. Melihat Riwayat Cuti dengan Status User
- Di halaman riwayat cuti, akan ada kolom "Status User"
- Badge akan menampilkan:
  - 🟢 **Aktif** - User masih aktif
  - 🟡 **Diubah** - Data user telah diubah
  - 🔴 **Dihapus** - User telah dihapus

### 4. Filter Berdasarkan Status User
- Gunakan filter "Status User" di halaman riwayat cuti
- Pilih "Aktif" untuk user yang masih aktif
- Pilih "Diubah/Dihapus" untuk user yang telah berubah

## File yang Ditambahkan/Dimodifikasi

### File Baru
- `app/controllers/UserSnapshotController.php`
- `app/views/user/snapshots.php`
- `app/views/user/snapshot_detail.php`
- `database/add_user_snapshot_support.sql`
- `run_migration.php`
- `USER_SNAPSHOT_FEATURE.md`

### File yang Dimodifikasi
- `app/models/Leave.php` - Menambahkan dukungan view dan status user
- `app/models/User.php` - Menambahkan soft delete dan tracking perubahan
- `app/models/UserSnapshot.php` - Menambahkan method baru
- `app/controllers/LeaveController.php` - Menambahkan filter status user
- `app/views/leave/history.php` - Menambahkan kolom status user

## Keuntungan

### 1. Data Integrity
- Tidak ada data yang hilang saat user dihapus
- Riwayat lengkap tetap terjaga
- Audit trail yang jelas

### 2. Compliance
- Memenuhi kebutuhan audit
- Data historis tetap tersedia
- Tracking perubahan yang transparan

### 3. User Experience
- Admin dapat melihat riwayat lengkap
- Filter yang fleksibel
- Interface yang intuitif

## Catatan Penting

1. **Backup Database**: Selalu backup database sebelum menjalankan migrasi
2. **Testing**: Test fitur di environment development terlebih dahulu
3. **Performance**: View dapat mempengaruhi performa query, monitor penggunaan
4. **Storage**: Snapshot akan menambah ukuran database, pertimbangkan cleanup policy

## Troubleshooting

### Error saat migrasi
- Pastikan database connection berfungsi
- Cek permission database user
- Pastikan tidak ada constraint yang konflik

### Data tidak muncul
- Cek apakah view berhasil dibuat
- Verifikasi data di tabel `user_snapshots`
- Cek log error aplikasi

### Performa lambat
- Tambahkan index pada kolom yang sering diquery
- Pertimbangkan pagination untuk data besar
- Monitor query execution time 