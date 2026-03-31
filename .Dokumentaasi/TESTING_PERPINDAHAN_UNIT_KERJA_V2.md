# Panduan Testing Fitur Perpindahan Unit Kerja V2

## Persiapan Testing

### 1. Backup Database
```sql
-- Backup tabel users dan tabel kuota cuti
mysqldump -u root -p sistem_cuti users leave_balances kuota_cuti_sakit kuota_cuti_besar kuota_cuti_melahirkan kuota_cuti_alasan_penting kuota_cuti_luar_tanggungan > backup_sebelum_testing.sql
```

### 2. Siapkan Data Awal
```sql
-- Hapus data testing yang ada
DELETE FROM users WHERE username IN ('pa_pinrang', 'pa_sengkang', 'pa_maros');

-- Insert data awal untuk testing
INSERT INTO users (username, password, nama, nip, jabatan, golongan, unit_kerja, user_type, tanggal_masuk) VALUES
('pa_pinrang', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rijal', '200305282020081001', 'Ketua Pengadilan Agama', 'IV/c', 'Pengadilan Agama Pinrang', 'user', '2020-08-01'),
('pa_sengkang', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alif', '200406222020091001', 'Ketua Pengadilan Agama', 'IV/d', 'Pengadilan Agama Sengkang', 'user', '2020-09-01'),
('pa_maros', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fikri', '200402212020081001', 'Ketua Pengadilan Agama', 'IV/c', 'Pengadilan Agama Maros', 'user', '2020-08-01');

-- Ambil user_id untuk insert kuota
SET @user1_id = (SELECT id FROM users WHERE username = 'pa_pinrang');
SET @user2_id = (SELECT id FROM users WHERE username = 'pa_sengkang');
SET @user3_id = (SELECT id FROM users WHERE username = 'pa_maros');

-- Insert kuota cuti tahunan
INSERT INTO leave_balances (user_id, year, total_quota, used_quota, remaining_quota) VALUES
(@user1_id, 2025, 12, 2, 10),
(@user2_id, 2025, 12, 1, 11),
(@user3_id, 2025, 12, 3, 9);

-- Insert kuota cuti sakit
INSERT INTO kuota_cuti_sakit (user_id, tahun, kuota_total, kuota_terpakai, kuota_sisa) VALUES
(@user1_id, 2025, 12, 1, 11),
(@user2_id, 2025, 12, 2, 10),
(@user3_id, 2025, 12, 0, 12);
```

## Test Case 1: Penimpaan Data dengan Unit Kerja dan Jabatan Sama

### Skenario:
- Data awal: 3 user dengan unit kerja dan jabatan berbeda
- Import CSV: 3 user dengan unit kerja dan jabatan yang sama dengan data awal
- Expected: Data nama, NIP, dan golongan tertimpa, username tetap sama

### Langkah Testing:
1. Login sebagai admin
2. Akses menu User Management → Tambah User → Import CSV
3. Upload file `contoh_perpindahan_unit_kerja_v2.csv`
4. Klik Import

### File CSV untuk Testing:
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Fikri;200402212020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Pinrang
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
Alif;200406222020091001;Ketua Pengadilan Agama;IV/d;Pengadilan Agama Maros
```

### Expected Result:
1. **pa_pinrang**: Nama berubah dari Rijal → Fikri, NIP berubah dari 200305282020081001 → 200402212020081001
2. **pa_sengkang**: Nama berubah dari Alif → Rijal, NIP berubah dari 200406222020091001 → 200305282020081001
3. **pa_maros**: Nama berubah dari Fikri → Alif, NIP berubah dari 200402212020081001 → 200406222020091001

### Verifikasi:
```sql
-- Cek data user setelah import
SELECT username, nama, nip, jabatan, golongan, unit_kerja FROM users WHERE username IN ('pa_pinrang', 'pa_sengkang', 'pa_maros');

-- Cek kuota cuti tetap terjaga
SELECT u.username, lb.year, lb.remaining_quota 
FROM users u 
JOIN leave_balances lb ON u.id = lb.user_id 
WHERE u.username IN ('pa_pinrang', 'pa_sengkang', 'pa_maros');
```

## Test Case 2: Update Data dengan NIP yang Sama

### Skenario:
- Data awal: User dengan NIP tertentu
- Import CSV: User dengan NIP yang sama tapi data lain berbeda
- Expected: Data diupdate, tidak ada penimpaan

### Langkah Testing:
1. Siapkan data awal dengan NIP yang sudah ada
2. Import CSV dengan NIP yang sama tapi nama/jabatan berbeda
3. Verifikasi hasil

### Expected Result:
- Data user diupdate sesuai CSV
- Tidak ada penimpaan data
- Username bisa berubah jika unit kerja berubah

## Test Case 3: User Baru

### Skenario:
- Import CSV dengan data yang benar-benar baru
- Expected: User baru dibuat

### Langkah Testing:
1. Import CSV dengan unit kerja dan jabatan yang belum ada
2. Verifikasi user baru dibuat

### Expected Result:
- User baru dibuat dengan username sesuai unit kerja
- Kuota cuti awal dibuat otomatis

## Test Case 4: Validasi Data

### Skenario:
- Import CSV dengan data kosong atau tidak valid
- Expected: Error message yang jelas

### Test Cases:
1. Nama kosong
2. NIP kosong
3. Jabatan kosong
4. SATKER kosong
5. NIP tidak valid (bukan angka)

### Expected Result:
- Error message yang informatif
- Tidak ada data yang tersimpan

## Test Case 5: Logging System

### Skenario:
- Import CSV yang menyebabkan penimpaan data
- Expected: Log tercatat dengan detail

### Verifikasi:
```bash
# Cek file log
tail -f logs/unit_kerja_transfer.log
```

### Expected Result:
- Log mencatat setiap penimpaan data
- Informasi lengkap: user_id, nama, NIP, unit kerja lama dan baru
- Timestamp dan admin yang melakukan

## Test Case 6: Kuota Cuti Terjaga

### Skenario:
- Import CSV yang menyebabkan penimpaan data
- Expected: Kuota cuti tetap terjaga

### Verifikasi:
```sql
-- Cek semua jenis kuota cuti
SELECT 
    u.username,
    u.nama,
    lb.remaining_quota as cuti_tahunan,
    kcs.kuota_sisa as cuti_sakit
FROM users u
LEFT JOIN leave_balances lb ON u.id = lb.user_id AND lb.year = 2025
LEFT JOIN kuota_cuti_sakit kcs ON u.id = kcs.user_id AND kcs.tahun = 2025
WHERE u.username IN ('pa_pinrang', 'pa_sengkang', 'pa_maros');
```

### Expected Result:
- Semua kuota cuti tetap terjaga
- Tidak ada data kuota yang hilang

## Test Case 7: Masa Kerja Akurat

### Skenario:
- Import CSV dengan NIP yang berbeda
- Expected: Tanggal masuk diupdate sesuai digit 9-14 NIP

### Verifikasi:
```sql
-- Cek tanggal masuk
SELECT username, nama, nip, tanggal_masuk FROM users WHERE username IN ('pa_pinrang', 'pa_sengkang', 'pa_maros');
```

### Expected Result:
- Tanggal masuk sesuai digit 9-14 NIP
- Format: YYYY-MM-01

## Checklist Testing

### ✅ Test Case 1: Penimpaan Data
- [ ] Data nama tertimpa dengan benar
- [ ] Data NIP tertimpa dengan benar
- [ ] Data jabatan tertimpa dengan benar
- [ ] Data golongan tertimpa dengan benar
- [ ] Username tetap sama
- [ ] Unit kerja tetap sama

### ✅ Test Case 2: Update Data NIP Sama
- [ ] Data diupdate dengan benar
- [ ] Tidak ada penimpaan
- [ ] Username bisa berubah jika unit kerja berubah

### ✅ Test Case 3: User Baru
- [ ] User baru dibuat dengan benar
- [ ] Username sesuai unit kerja
- [ ] Kuota cuti awal dibuat

### ✅ Test Case 4: Validasi Data
- [ ] Error message untuk data kosong
- [ ] Error message untuk NIP tidak valid
- [ ] Tidak ada data tersimpan jika error

### ✅ Test Case 5: Logging System
- [ ] Log tercatat untuk penimpaan data
- [ ] Informasi lengkap dalam log
- [ ] Timestamp dan admin tercatat

### ✅ Test Case 6: Kuota Cuti Terjaga
- [ ] Kuota cuti tahunan terjaga
- [ ] Kuota cuti sakit terjaga
- [ ] Kuota cuti besar terjaga
- [ ] Kuota cuti melahirkan terjaga
- [ ] Kuota cuti alasan penting terjaga
- [ ] Kuota cuti luar tanggungan terjaga

### ✅ Test Case 7: Masa Kerja Akurat
- [ ] Tanggal masuk sesuai digit 9-14 NIP
- [ ] Format tanggal benar (YYYY-MM-01)

## Troubleshooting

### Masalah: Data tidak tertimpa
**Solusi:**
- Pastikan unit kerja dan jabatan sama persis (termasuk spasi)
- Cek apakah ada karakter khusus di CSV
- Verifikasi encoding file CSV

### Masalah: Username berubah
**Solusi:**
- Fitur V2 seharusnya mempertahankan username
- Cek apakah ada bug di logika `overwriteExistingUser()`

### Masalah: Kuota cuti hilang
**Solusi:**
- Cek apakah user_id tetap sama
- Verifikasi foreign key constraints
- Cek log error database

### Masalah: Log tidak tercatat
**Solusi:**
- Pastikan direktori `logs/` ada
- Cek permission write pada direktori
- Verifikasi fungsi `logUnitKerjaTransfer()`

## Restore Database

Setelah testing selesai, restore database jika diperlukan:

```bash
mysql -u root -p sistem_cuti < backup_sebelum_testing.sql
```

## Kesimpulan

Setelah semua test case berhasil, fitur perpindahan unit kerja V2 siap digunakan di production environment. 