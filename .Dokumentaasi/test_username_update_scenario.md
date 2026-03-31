# Testing Skenario Update Username - Perpindahan Unit Kerja

## Skenario yang Diinginkan

### Data Lama di Database:
```sql
-- User 1
INSERT INTO users (username, password, nama, nip, jabatan, golongan, unit_kerja, user_type, tanggal_masuk) VALUES
('pa_pinrang', '$2y$10$...', 'Rijal', '200305282020081001', 'Ketua Pengadilan Agama', 'IV/c', 'Pengadilan Agama Pinrang', 'user', '2020-08-01');

-- User 2  
INSERT INTO users (username, password, nama, nip, jabatan, golongan, unit_kerja, user_type, tanggal_masuk) VALUES
('pa_sengkang', '$2y$10$...', 'Alif', '200406222020091001', 'Ketua Pengadilan Agama', 'IV/d', 'Pengadilan Agama Sengkang', 'user', '2020-08-01');

-- User 3
INSERT INTO users (username, password, nama, nip, jabatan, golongan, unit_kerja, user_type, tanggal_masuk) VALUES
('pa_maros', '$2y$10$...', 'Fikri', '200402212020081001', 'Ketua Pengadilan Agama', 'IV/c', 'Pengadilan Agama Maros', 'user', '2020-08-01');
```

### File CSV untuk Import:
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
Alif;200406222020091001;Ketua Pengadilan Agama;IV/d;Pengadilan Agama Maros
Fikri;200402212020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Pinrang
```

### Expected Result:
1. **Rijal**: dipindahkan dari Pinrang ke Sengkang, username: `pa_pinrang` → `pa_sengkang`
2. **Alif**: dipindahkan dari Sengkang ke Maros, username: `pa_sengkang` → `pa_maros`
3. **Fikri**: dipindahkan dari Maros ke Pinrang, username: `pa_maros` → `pa_pinrang`

## Langkah Testing

### 1. Persiapan Database
```sql
-- Backup data existing
CREATE TABLE users_backup AS SELECT * FROM users;

-- Hapus data testing jika ada
DELETE FROM users WHERE nip IN ('200305282020081001', '200406222020091001', '200402212020081001');

-- Hapus kuota cuti terkait
DELETE FROM leave_balances WHERE user_id IN (SELECT id FROM users WHERE nip IN ('200305282020081001', '200406222020091001', '200402212020081001'));
DELETE FROM kuota_cuti_sakit WHERE user_id IN (SELECT id FROM users WHERE nip IN ('200305282020081001', '200406222020091001', '200402212020081001'));
DELETE FROM kuota_cuti_besar WHERE user_id IN (SELECT id FROM users WHERE nip IN ('200305282020081001', '200406222020091001', '200402212020081001'));
DELETE FROM kuota_cuti_melahirkan WHERE user_id IN (SELECT id FROM users WHERE nip IN ('200305282020081001', '200406222020091001', '200402212020081001'));
DELETE FROM kuota_cuti_alasan_penting WHERE user_id IN (SELECT id FROM users WHERE nip IN ('200305282020081001', '200406222020091001', '200402212020081001'));
DELETE FROM kuota_cuti_luar_tanggungan WHERE user_id IN (SELECT id FROM users WHERE nip IN ('200305282020081001', '200406222020091001', '200402212020081001'));
```

### 2. Insert Data Testing
```sql
-- Insert data untuk testing
INSERT INTO users (username, password, nama, nip, jabatan, golongan, unit_kerja, user_type, tanggal_masuk) VALUES
('pa_pinrang', '$2y$10$...', 'Rijal', '200305282020081001', 'Ketua Pengadilan Agama', 'IV/c', 'Pengadilan Agama Pinrang', 'user', '2020-08-01'),
('pa_sengkang', '$2y$10$...', 'Alif', '200406222020091001', 'Ketua Pengadilan Agama', 'IV/d', 'Pengadilan Agama Sengkang', 'user', '2020-08-01'),
('pa_maros', '$2y$10$...', 'Fikri', '200402212020081001', 'Ketua Pengadilan Agama', 'IV/c', 'Pengadilan Agama Maros', 'user', '2020-08-01');
```

### 3. Import CSV
- Upload file `contoh_perpindahan_unit_kerja_v2.csv`
- Cek hasil import
- Verifikasi pesan sukses

### 4. Verifikasi Hasil

#### 4.1 Cek Data User
```sql
SELECT id, username, nama, nip, jabatan, golongan, unit_kerja 
FROM users 
WHERE nip IN ('200305282020081001', '200406222020091001', '200402212020081001')
ORDER BY nama;
```

**Expected Result:**
```
id | username     | nama  | nip                | jabatan                | golongan | unit_kerja
---|--------------|-------|-------------------|------------------------|----------|------------------------
X  | pa_sengkang  | Rijal | 200305282020081001| Ketua Pengadilan Agama | IV/c     | Pengadilan Agama Sengkang
Y  | pa_maros     | Alif  | 200406222020091001| Ketua Pengadilan Agama | IV/d     | Pengadilan Agama Maros
Z  | pa_pinrang   | Fikri | 200402212020081001| Ketua Pengadilan Agama | IV/c     | Pengadilan Agama Pinrang
```

#### 4.2 Cek Kuota Cuti Tetap Terjaga
```sql
-- Cek leave_balances
SELECT u.nama, u.username, lb.tahun, lb.sisa_kuota
FROM users u
JOIN leave_balances lb ON u.id = lb.user_id
WHERE u.nip IN ('200305282020081001', '200406222020091001', '200402212020081001')
ORDER BY u.nama, lb.tahun;

-- Cek kuota cuti sakit
SELECT u.nama, u.username, kcs.tahun, kcs.sisa_kuota
FROM users u
JOIN kuota_cuti_sakit kcs ON u.id = kcs.user_id
WHERE u.nip IN ('200305282020081001', '200406222020091001', '200402212020081001')
ORDER BY u.nama, kcs.tahun;
```

**Expected Result:**
- Semua user memiliki kuota cuti lengkap
- Kuota cuti tetap terjaga setelah perpindahan

### 5. Cek Log Perpindahan
```bash
# Cek file log
tail -f logs/unit_kerja_transfer.log
```

**Expected Log:**
```
[2025-07-29 11:00:15] User Rijal (NIP: 200305282020081001) dipindahkan dari Pengadilan Agama Pinrang ke Pengadilan Agama Sengkang oleh admin Admin_Name
[2025-07-29 11:00:16] User Alif (NIP: 200406222020091001) dipindahkan dari Pengadilan Agama Sengkang ke Pengadilan Agama Maros oleh admin Admin_Name
[2025-07-29 11:00:17] User Fikri (NIP: 200402212020081001) dipindahkan dari Pengadilan Agama Maros ke Pengadilan Agama Pinrang oleh admin Admin_Name
```

## Testing Skenario Tambahan

### Skenario 1: User Baru (NIP Belum Ada)
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Budi;200501152021081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Makassar
```

**Expected Result:**
- User baru Budi dibuat
- Username: `pa_makassar`
- Kuota cuti default dibuat

### Skenario 2: Update Data Tanpa Perpindahan
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
```

**Expected Result:**
- Data Rijal diupdate
- Username tetap `pa_sengkang` (tidak berubah)
- Tidak ada perpindahan unit kerja

### Skenario 3: Update Jabatan
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Rijal;200305282020081001;Wakil Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
```

**Expected Result:**
- Jabatan Rijal diupdate
- Username tetap `pa_sengkang`
- Unit kerja tetap sama

## Verifikasi Keamanan

### 1. Password Tidak Berubah
```sql
-- Cek password tetap sama
SELECT username, password FROM users WHERE nip IN ('200305282020081001', '200406222020091001', '200402212020081001');
```

### 2. Riwayat Cuti Tetap Terjaga
```sql
-- Cek riwayat cuti tidak hilang
SELECT u.nama, lr.jenis_cuti, lr.tanggal_mulai, lr.tanggal_selesai
FROM users u
JOIN leave_requests lr ON u.id = lr.user_id
WHERE u.nip IN ('200305282020081001', '200406222020091001', '200402212020081001')
ORDER BY u.nama, lr.tanggal_mulai;
```

### 3. Dokumen Tetap Terjaga
```sql
-- Cek dokumen tidak hilang
SELECT u.nama, ld.filename
FROM users u
JOIN leave_documents ld ON u.id = ld.created_by
WHERE u.nip IN ('200305282020081001', '200406222020091001', '200402212020081001')
ORDER BY u.nama;
```

## Troubleshooting

### Masalah: Username Duplikasi
**Gejala:** Error "Username sudah ada"
**Solusi:** Sistem akan otomatis menambahkan suffix angka (pa_sengkang1, pa_sengkang2, dst)

### Masalah: Kuota Tidak Berpindah
**Gejala:** Kuota cuti tidak ikut berpindah
**Solusi:** Cek log error dan pastikan tabel kuota cuti ada

### Masalah: Username Tidak Update
**Gejala:** Username tetap lama setelah perpindahan
**Solusi:** Pastikan unit kerja benar-benar berubah di CSV

## Kesimpulan

Setelah testing, sistem akan:
- ✅ Mengupdate username sesuai unit kerja baru
- ✅ Memindahkan semua kuota cuti
- ✅ Mencatat log perpindahan
- ✅ Menjaga data lain (password, riwayat, dokumen)
- ✅ Menangani user baru dengan benar 