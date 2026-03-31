# UPDATE SISTEM KUOTA CUTI OTOMATIS

## Deskripsi Update
Sistem akan diperbarui untuk mengelola kuota cuti secara otomatis saat pergantian tahun dengan fitur:
1. Membuat dan menyimpan data kuota cuti tahun selanjutnya secara otomatis
2. Menghapus data kuota cuti 3 tahun lalu dan ke atasnya
3. Mengelola kuota cuti tahunan dengan logika khusus (akumulatif maksimal 18 hari dalam 3 tahun)

## Analisis Sistem Saat Ini

### File yang Perlu Diperiksa:
1. **Model Kuota Cuti** - `app/models/Quota.php`
2. **Controller Kuota** - `app/controllers/ApprovalController.php` (untuk bagian quota)
3. **Database** - Struktur tabel quota
4. **Cron Job/Job Scheduler** - Untuk menjalankan proses otomatis

### Fitur yang Akan Ditambahkan:
1. **Auto Create Quota** - Membuat kuota untuk tahun berikutnya
2. **Auto Cleanup** - Menghapus kuota lama (3+ tahun)
3. **Scheduler** - Menjalankan proses secara otomatis
4. **Kuota Tahunan Akumulatif** - Mengelola kuota cuti tahunan dengan maksimal 18 hari dalam 3 tahun

### Logika Kuota Tahunan (Contoh: 1 Januari 2026):
1. **Hapus kuota lama**: Hapus kuota tahun 2023 ke atas (2023, 2022, 2021, dst.)
2. **Simpan kuota 2 tahun lalu**: Tahun 2024 dengan kuota 0 hari
3. **Simpan kuota 1 tahun lalu**: Tahun 2025 dengan kuota sesuai sisa:
   - Jika sisa ≥ 6 hari → simpan 6 hari
   - Jika sisa < 6 hari → simpan sesuai sisa (misal: sisa 4 hari → simpan 4 hari)
4. **Buat kuota tahun baru**: Tahun 2026 dengan kuota 12 hari
5. **Total maksimal**: 18 hari (0 + 6 + 12 = 18 hari)

## Langkah-langkah Implementasi

### 1. Analisis Struktur Database
- Periksa tabel `quota` atau tabel terkait kuota cuti
- Identifikasi field yang diperlukan untuk tahun

### 2. Modifikasi Model
- Tambahkan method untuk membuat kuota otomatis
- Tambahkan method untuk membersihkan kuota lama
- Tambahkan method untuk mendapatkan tahun saat ini
- Tambahkan method untuk mengelola kuota tahunan akumulatif
- Tambahkan method untuk menghitung sisa kuota dan menentukan kuota yang dapat dibawa ke tahun berikutnya

### 3. Modifikasi Controller
- Tambahkan endpoint untuk menjalankan proses otomatis
- Tambahkan validasi dan error handling

### 4. Implementasi Scheduler
- Buat script yang dapat dijalankan via cron job
- Atau implementasi scheduler internal

### 5. Testing
- Test pembuatan kuota otomatis
- Test penghapusan kuota lama
- Test error handling
- Test logika akumulatif kuota tahunan
- Test perhitungan sisa kuota yang dapat dibawa ke tahun berikutnya
- Test total maksimal 18 hari dalam 3 tahun

## File yang Akan Dimodifikasi:
1. `app/models/LeaveBalance.php` - Model untuk kuota cuti tahunan
2. `app/controllers/ApprovalController.php` - Controller untuk mengelola kuota
3. `app/helpers/` - Tambah helper untuk scheduler dan logika kuota
4. Script cron job baru untuk menjalankan proses otomatis
5. View quota untuk menampilkan total kuota 3 tahun

## Catatan Penting:
- Backup database sebelum implementasi
- Test di environment development terlebih dahulu
- Dokumentasikan perubahan untuk maintenance
- Pertimbangkan timezone untuk eksekusi tepat waktu
- Logika ini hanya berlaku untuk kuota cuti tahunan (leave_type_id = 1)
- Kuota cuti lain (sakit, besar, melahirkan, dll) tetap menggunakan logika yang sudah ada
- Proses ini harus dijalankan tepat pada tanggal 1 Januari setiap tahun

## Timeline:
- Analisis: 1-2 jam ✅
- Implementasi: 4-6 jam ✅
- Testing: 2-3 jam (Pending)
- Deployment: 1 jam (Pending)

## Status Implementasi:
✅ **SELESAI** - Semua fitur telah diimplementasikan:

1. ✅ Helper untuk pengelolaan kuota otomatis
2. ✅ Script cron job untuk menjalankan proses otomatis
3. ✅ Tabel system_logs untuk logging
4. ✅ Method baru di model LeaveBalance
5. ✅ Endpoint baru di ApprovalController
6. ✅ UI update di halaman quota
7. ✅ Dokumentasi lengkap

## File yang Telah Dibuat:
1. `app/helpers/quota_scheduler_helper.php` ✅
2. `cron/annual_quota_management.php` ✅
3. `database/create_system_logs_table.sql` ✅
4. `README_KUOTA_OTOMATIS.md` ✅

## File yang Telah Dimodifikasi:
1. `app/models/LeaveBalance.php` ✅
2. `app/controllers/ApprovalController.php` ✅
3. `app/views/approval/quota.php` ✅

## Langkah Selanjutnya:
1. Jalankan SQL untuk membuat tabel system_logs
2. Setup cron job di server
3. Test fitur secara manual
4. Test cron job
5. Deploy ke production

## INSTRUKSI KHUSUS UNTUK TAHUN 2026

### **Persiapan Sebelum 1 Januari 2026**
- Backup database pada 31 Desember 2025
- Verifikasi cron job sudah terpasang
- Test manual untuk memastikan sistem berfungsi

### **Proses Otomatis 1 Januari 2026**
- **00:01 WIB**: Cron job berjalan otomatis
- Hapus kuota tahun 2023 ke atas
- Simpan kuota 2024 dengan 0 hari
- Simpan kuota 2025 sesuai sisa (maksimal 6 hari)
- Buat kuota 2026 dengan 12 hari

### **Monitoring Setelah 1 Januari 2026**
- Cek log file: `logs/quota_management_2026-01-01.log`
- Verifikasi database dengan query monitoring
- Cek system_logs untuk aktivitas

### **Troubleshooting**
- Jika cron job gagal, jalankan manual
- Jika ada error, restore dari backup
- Test manual dari admin panel jika diperlukan

**Lihat file `README_KUOTA_OTOMATIS.md` untuk instruksi lengkap tahun 2026.**

---
*Implementasi selesai pada: <?php echo date('Y-m-d H:i:s'); ?>* 