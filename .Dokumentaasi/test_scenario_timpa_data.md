# Skenario Testing - Fitur Timpa Data

## Skenario 1: Timpa Data dengan Unit Kerja + Jabatan Sama

### Data Existing di Database:
```sql
INSERT INTO users (username, password, nama, nip, jabatan, golongan, unit_kerja, user_type, tanggal_masuk) VALUES
('pa_sengkang', '$2y$10$...', 'Rijal Lama', '200305282020081001', 'Ketua Pengadilan Agama', 'IV/c', 'Pengadilan Agama Sengkang', 'user', '2020-08-01');
```

### File CSV untuk Import:
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Alif Baru;200406222020091001;Ketua Pengadilan Agama;IV/d;Pengadilan Agama Sengkang
```

### Expected Result:
- ✅ Data Rijal Lama ditimpa dengan data Alif Baru
- ✅ Username tetap: `pa_sengkang`
- ✅ NIP berubah dari `200305282020081001` ke `200406222020091001`
- ✅ Nama berubah dari "Rijal Lama" ke "Alif Baru"
- ✅ Golongan berubah dari "IV/c" ke "IV/d"
- ✅ Tidak ada user baru yang dibuat
- ✅ Kuota cuti tetap terjaga

## Skenario 2: Update NIP Existing

### Data Existing di Database:
```sql
INSERT INTO users (username, password, nama, nip, jabatan, golongan, unit_kerja, user_type, tanggal_masuk) VALUES
('pa_pinrang', '$2y$10$...', 'Rijal', '200305282020081001', 'Ketua Pengadilan Agama', 'IV/c', 'Pengadilan Agama Pinrang', 'user', '2020-08-01');
```

### File CSV untuk Import:
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
```

### Expected Result:
- ✅ Data Rijal diupdate
- ✅ Unit kerja berubah dari "Pengadilan Agama Pinrang" ke "Pengadilan Agama Sengkang"
- ✅ Username tetap: `pa_pinrang` (tidak berubah)
- ✅ Kuota cuti ikut berpindah ke unit kerja baru
- ✅ Log mencatat perpindahan unit kerja

## Skenario 3: Create New User (Unit Kerja Berbeda)

### Data Existing di Database:
```sql
INSERT INTO users (username, password, nama, nip, jabatan, golongan, unit_kerja, user_type, tanggal_masuk) VALUES
('pa_sengkang', '$2y$10$...', 'Rijal', '200305282020081001', 'Ketua Pengadilan Agama', 'IV/c', 'Pengadilan Agama Sengkang', 'user', '2020-08-01');
```

### File CSV untuk Import:
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Fikri;200402212020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Maros
```

### Expected Result:
- ✅ User baru Fikri dibuat
- ✅ Username: `pa_maros`
- ✅ NIP: `200402212020081001`
- ✅ Unit kerja: "Pengadilan Agama Maros"
- ✅ Kuota cuti default dibuat untuk Fikri

## Skenario 4: Create New User (Jabatan Berbeda)

### Data Existing di Database:
```sql
INSERT INTO users (username, password, nama, nip, jabatan, golongan, unit_kerja, user_type, tanggal_masuk) VALUES
('pa_sengkang', '$2y$10$...', 'Rijal', '200305282020081001', 'Ketua Pengadilan Agama', 'IV/c', 'Pengadilan Agama Sengkang', 'user', '2020-08-01');
```

### File CSV untuk Import:
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Alif;200406222020091001;Wakil Ketua Pengadilan Agama;IV/d;Pengadilan Agama Sengkang
```

### Expected Result:
- ✅ User baru Alif dibuat
- ✅ Username: `pa_sengkang1` (karena username pa_sengkang sudah ada)
- ✅ NIP: `200406222020091001`
- ✅ Jabatan: "Wakil Ketua Pengadilan Agama" (berbeda)
- ✅ Unit kerja: "Pengadilan Agama Sengkang" (sama)
- ✅ Kuota cuti default dibuat untuk Alif

## Langkah Testing

### 1. Persiapan Database
```sql
-- Backup data existing
CREATE TABLE users_backup AS SELECT * FROM users;

-- Hapus data testing jika ada
DELETE FROM users WHERE nip IN ('200305282020081001', '200406222020091001', '200402212020081001');
```

### 2. Insert Data Testing
```sql
-- Insert data untuk testing
INSERT INTO users (username, password, nama, nip, jabatan, golongan, unit_kerja, user_type, tanggal_masuk) VALUES
('pa_sengkang', '$2y$10$...', 'Rijal Lama', '200305282020081001', 'Ketua Pengadilan Agama', 'IV/c', 'Pengadilan Agama Sengkang', 'user', '2020-08-01');
```

### 3. Import CSV
- Upload file CSV sesuai skenario
- Cek hasil import
- Verifikasi data di database

### 4. Verifikasi Hasil
```sql
-- Cek data user
SELECT id, username, nama, nip, jabatan, golongan, unit_kerja FROM users WHERE nip IN ('200305282020081001', '200406222020091001', '200402212020081001');

-- Cek kuota cuti
SELECT u.nama, lb.tahun, lb.sisa_kuota FROM users u JOIN leave_balances lb ON u.id = lb.user_id WHERE u.nip IN ('200305282020081001', '200406222020091001', '200402212020081001');
```

### 5. Cek Log
```bash
# Cek file log
tail -f logs/unit_kerja_transfer.log
```

## Expected Output Log

### Skenario 1 (Timpa Data):
```
[2025-07-29 10:30:15] User Alif Baru (NIP: 200406222020091001) dipindahkan dari Pengadilan Agama Sengkang ke Pengadilan Agama Sengkang oleh admin Admin_Name
```

### Skenario 2 (Update NIP):
```
[2025-07-29 10:30:16] User Rijal (NIP: 200305282020081001) dipindahkan dari Pengadilan Agama Pinrang ke Pengadilan Agama Sengkang oleh admin Admin_Name
```

### Skenario 3 & 4 (Create New):
```
# Tidak ada log perpindahan karena user baru
```

## Troubleshooting

### Masalah: Username Duplikasi
**Gejala:** Error "Username sudah ada"
**Solusi:** Sistem akan otomatis menambahkan suffix angka (pa_sengkang1, pa_sengkang2, dst)

### Masalah: NIP Duplikasi
**Gejala:** Error "NIP sudah terdaftar"
**Solusi:** Pastikan NIP di CSV berbeda dengan yang ada di database

### Masalah: Kuota Tidak Berpindah
**Gejala:** Kuota cuti tidak ikut berpindah
**Solusi:** Cek log error dan pastikan tabel kuota cuti ada

### Masalah: Log Tidak Tercatat
**Gejala:** File log kosong
**Solusi:** Pastikan direktori logs ada dan permission write 