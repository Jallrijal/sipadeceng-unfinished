# SISTEM KUOTA CUTI OTOMATIS

## Deskripsi
Sistem ini telah diperbarui dengan fitur pengelolaan kuota cuti otomatis yang berjalan setiap pergantian tahun (1 Januari). Fitur ini khusus untuk kuota cuti tahunan dengan sistem akumulatif maksimal 18 hari dalam 3 tahun.

## Logika Kuota Tahunan

### Contoh: 1 Januari 2026

1. **Hapus kuota lama**: Hapus kuota tahun 2023 ke atas (2023, 2022, 2021, dst.)
2. **Simpan kuota 2 tahun lalu**: Tahun 2024 dengan kuota 0 hari
3. **Simpan kuota 1 tahun lalu**: Tahun 2025 dengan kuota sesuai sisa:
   - Jika sisa ≥ 6 hari → simpan 6 hari
   - Jika sisa < 6 hari → simpan sesuai sisa (misal: sisa 4 hari → simpan 4 hari)
4. **Buat kuota tahun baru**: Tahun 2026 dengan kuota 12 hari
5. **Total maksimal**: 18 hari (0 + 6 + 12 = 18 hari)

## File yang Ditambahkan/Dimodifikasi

### 1. Helper Files
- `app/helpers/quota_scheduler_helper.php` - Helper untuk mengelola kuota otomatis

### 2. Cron Job
- `cron/annual_quota_management.php` - Script untuk menjalankan proses otomatis

### 3. Database
- `database/create_system_logs_table.sql` - Tabel untuk mencatat log aktivitas

### 4. Model
- `app/models/LeaveBalance.php` - Ditambahkan method untuk kuota 3 tahun

### 5. Controller
- `app/controllers/ApprovalController.php` - Ditambahkan endpoint untuk Reset Quota

### 6. View
- `app/views/approval/quota.php` - Ditambahkan tombol Reset Quota dan info

## Cara Setup

### 1. Jalankan SQL untuk membuat tabel system_logs
```sql
-- Jalankan file: database/create_system_logs_table.sql
```

### 2. Setup Cron Job
Tambahkan ke crontab server:
```bash
# Jalankan setiap 1 Januari jam 00:01
1 0 1 1 * /usr/bin/php /path/to/sistem-cuti/cron/annual_quota_management.php
```

### 3. Pastikan folder logs dapat ditulis
```bash
chmod 755 logs/
```

## Cara Penggunaan

### 1. Otomatis (Cron Job)
- Sistem akan berjalan otomatis setiap 1 Januari jam 00:01
- Log akan disimpan di `logs/quota_management_YYYY-MM-DD.log`

### 2. Manual (Admin Panel)
- Login sebagai admin
- Buka menu "Kuota Cuti"
- Klik tombol "Reset Quota" (kuning dengan icon gear)
- Konfirmasi dan tunggu proses selesai

### 3. Monitoring
- Cek log file di folder `logs/`
- Cek tabel `system_logs` di database
- Monitor di halaman admin quota

## Fitur Keamanan

1. **Anti Duplikasi**: Proses hanya dapat dijalankan sekali per hari
2. **Transaction**: Semua operasi database menggunakan transaction
3. **Logging**: Semua aktivitas dicatat dengan detail
4. **Error Handling**: Error handling yang komprehensif
5. **Validation**: Validasi input dan status sebelum eksekusi

## Struktur Data

### Tabel leave_balances
```sql
CREATE TABLE leave_balances (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    leave_type_id INT NOT NULL DEFAULT 1,
    tahun INT NOT NULL,
    kuota_tahunan INT NOT NULL,
    sisa_kuota INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Tabel system_logs
```sql
CREATE TABLE system_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    action VARCHAR(100) NOT NULL,
    details JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Testing

### 1. Test Manual
- Login admin
- Klik tombol "Reset Quota"
- Cek hasil di database dan log

### 2. Test Cron Job
```bash
# Test script secara manual
php cron/annual_quota_management.php
```

### 3. Test Logika
- Cek perhitungan kuota 3 tahun
- Verifikasi maksimal 18 hari
- Test berbagai skenario sisa kuota

## Troubleshooting

### 1. Cron Job Tidak Berjalan
- Cek permission file
- Cek path PHP
- Cek crontab syntax
- Cek log system

### 2. Error Database
- Cek koneksi database
- Cek permission tabel
- Cek log error

### 3. Proses Gagal
- Cek log file
- Cek tabel system_logs
- Restart proses manual jika perlu

## Catatan Penting

1. **Backup Database**: Selalu backup sebelum menjalankan proses
2. **Test Environment**: Test di development terlebih dahulu
3. **Timezone**: Pastikan timezone server sesuai (Asia/Jakarta)
4. **Monitoring**: Monitor log dan database secara berkala
5. **Maintenance**: Bersihkan log lama secara berkala

## INSTRUKSI KHUSUS UNTUK TAHUN 2026

### **Persiapan Sebelum 1 Januari 2026**

#### **1. Backup Database (31 Desember 2025)**
```bash
# Backup database sebelum pergantian tahun
mysqldump -u root -p pengelolaan_cuti > backup_2025_12_31.sql

# Atau dari phpMyAdmin:
# 1. Pilih database pengelolaan_cuti
# 2. Klik Export
# 3. Pilih "Custom" dan "SQL"
# 4. Download backup
```

#### **2. Verifikasi Cron Job**
```bash
# Cek apakah cron job sudah terpasang
crontab -l

# Pastikan ada entry ini:
# 1 0 1 1 * /usr/bin/php /path/to/sistem-cuti/cron/annual_quota_management.php
```

#### **3. Test Manual (31 Desember 2025)**
1. Login sebagai admin
2. Buka menu "Kuota Cuti"
3. Klik tombol "Reset Quota" untuk test
4. Verifikasi hasil di database

### **Proses Otomatis 1 Januari 2026**

#### **Yang Akan Terjadi Otomatis:**
1. **00:01 WIB** - Cron job berjalan
2. **Hapus kuota lama** - Tahun 2023 ke atas
3. **Simpan kuota 2024** - Dengan 0 hari
4. **Simpan kuota 2025** - Sesuai sisa (maksimal 6 hari)
5. **Buat kuota 2026** - Dengan 12 hari

#### **Contoh Hasil untuk User:**
```
Tahun 2024: 0 hari (selalu 0)
Tahun 2025: 6 hari (jika sisa ≥ 6) atau sesuai sisa
Tahun 2026: 12 hari (kuota baru)
Total: Maksimal 18 hari
```

### **Monitoring Setelah 1 Januari 2026**

#### **1. Cek Log File**
```bash
# Cek log proses otomatis
cat logs/quota_management_2026-01-01.log
```

#### **2. Cek Database**
```sql
-- Cek hasil proses otomatis
SELECT user_id, tahun, kuota_tahunan, sisa_kuota 
FROM leave_balances 
WHERE tahun IN (2024, 2025, 2026) 
ORDER BY user_id, tahun;

-- Cek total kuota per user
SELECT user_id, SUM(kuota_tahunan) as total_kuota
FROM leave_balances 
WHERE tahun IN (2024, 2025, 2026)
GROUP BY user_id
HAVING total_kuota > 18;
```

#### **3. Cek System Logs**
```sql
-- Cek aktivitas sistem
SELECT * FROM system_logs 
WHERE action = 'annual_quota_management' 
AND DATE(created_at) = '2026-01-01'
ORDER BY created_at DESC;
```

### **Troubleshooting Jika Gagal**

#### **1. Jika Cron Job Tidak Berjalan**
```bash
# Jalankan manual
php cron/annual_quota_management.php

# Cek error
tail -f logs/quota_management_2026-01-01.log
```

#### **2. Jika Ada Error Database**
```bash
# Restore dari backup
mysql -u root -p pengelolaan_cuti < backup_2025_12_31.sql

# Jalankan ulang proses
php cron/annual_quota_management.php
```

#### **3. Jika Perlu Manual Override**
```sql
-- Hapus data yang salah
DELETE FROM leave_balances WHERE tahun = 2026;

-- Jalankan proses manual dari admin panel
```

### **Checklist Tahun 2026**

- [ ] **31 Desember 2025**: Backup database
- [ ] **31 Desember 2025**: Test manual
- [ ] **1 Januari 2026 00:01**: Monitor cron job
- [ ] **1 Januari 2026 00:05**: Cek log file
- [ ] **1 Januari 2026 00:10**: Verifikasi database
- [ ] **1 Januari 2026 08:00**: Test di admin panel
- [ ] **1 Januari 2026 09:00**: Dokumentasikan hasil

### **Catatan Penting**

1. **Jangan restart server** pada 1 Januari 2026 sebelum proses selesai
2. **Monitor log file** secara real-time
3. **Siapkan backup** sebelum dan sesudah proses
4. **Test manual** jika otomatis gagal
5. **Dokumentasikan** semua aktivitas

---

## Support

Jika ada masalah atau pertanyaan, silakan:
1. Cek log file terlebih dahulu
2. Cek dokumentasi ini
3. Hubungi tim development

---
*Dokumentasi ini akan diupdate sesuai dengan perkembangan sistem* 