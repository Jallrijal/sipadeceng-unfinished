# Panduan Testing Fitur Perpindahan Unit Kerja

## Persiapan Testing

### 1. Data Awal
Pastikan ada beberapa user dengan unit kerja yang berbeda di database:

```sql
-- Contoh data awal untuk testing
INSERT INTO users (username, password, nama, nip, jabatan, golongan, unit_kerja, user_type, tanggal_masuk) VALUES
('pa_pinrang', '$2y$10$...', 'Rijal', '200305282020081001', 'Ketua Pengadilan Agama', 'IV/c', 'Pengadilan Agama Pinrang', 'user', '2020-08-01'),
('pa_sengkang', '$2y$10$...', 'Alif', '200406222020091001', 'Ketua Pengadilan Agama', 'IV/d', 'Pengadilan Agama Sengkang', 'user', '2020-09-01'),
('pa_maros', '$2y$10$...', 'Fikri', '200402212020081001', 'Ketua Pengadilan Agama', 'IV/c', 'Pengadilan Agama Maros', 'user', '2020-08-01');
```

### 2. File CSV untuk Testing
Gunakan file `contoh_perpindahan_unit_kerja.csv` yang sudah dibuat:

```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
Alif;200406222020091001;Ketua Pengadilan Agama;IV/d;Pengadilan Agama Maros
Fikri;200402212020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Pinrang
```

## Langkah Testing

### 1. Login sebagai Admin
- Buka aplikasi dan login sebagai admin
- Pastikan memiliki akses ke menu User Management

### 2. Akses Menu Import CSV
- Klik menu "User Management"
- Klik tombol "Tambah User" → "Import CSV"
- Modal import CSV akan muncul

### 3. Upload File CSV
- Pilih file `contoh_perpindahan_unit_kerja.csv`
- Klik tombol "Import Data"
- Tunggu proses import selesai

### 4. Verifikasi Hasil

#### a. Cek Data User
```sql
SELECT nama, nip, unit_kerja FROM users WHERE nip IN (
    '200305282020081001',
    '200406222020091001', 
    '200402212020081001'
) ORDER BY nama;
```

**Hasil yang diharapkan:**
- Rijal: Pengadilan Agama Sengkang (dari Pinrang)
- Alif: Pengadilan Agama Maros (dari Sengkang)
- Fikri: Pengadilan Agama Pinrang (dari Maros)

#### b. Cek Kuota Cuti Tahunan
```sql
SELECT u.nama, u.unit_kerja, lb.tahun, lb.sisa_kuota 
FROM users u 
JOIN leave_balances lb ON u.id = lb.user_id 
WHERE u.nip IN ('200305282020081001', '200406222020091001', '200402212020081001')
ORDER BY u.nama, lb.tahun;
```

#### c. Cek Kuota Cuti Sakit
```sql
SELECT u.nama, u.unit_kerja, kcs.tahun, kcs.sisa_kuota 
FROM users u 
JOIN kuota_cuti_sakit kcs ON u.id = kcs.user_id 
WHERE u.nip IN ('200305282020081001', '200406222020091001', '200402212020081001')
ORDER BY u.nama, kcs.tahun;
```

#### d. Cek Kuota Cuti Besar
```sql
SELECT u.nama, u.unit_kerja, kcb.sisa_kuota 
FROM users u 
JOIN kuota_cuti_besar kcb ON u.id = kcb.user_id 
WHERE u.nip IN ('200305282020081001', '200406222020091001', '200402212020081001')
ORDER BY u.nama;
```

### 5. Cek Log Perpindahan
```bash
# Cek file log
cat logs/unit_kerja_transfer.log
```

**Contoh output log:**
```
[2025-07-29 10:30:15] User Rijal (NIP: 200305282020081001) dipindahkan dari Pengadilan Agama Pinrang ke Pengadilan Agama Sengkang oleh admin Admin_Name
[2025-07-29 10:30:16] User Alif (NIP: 200406222020091001) dipindahkan dari Pengadilan Agama Sengkang ke Pengadilan Agama Maros oleh admin Admin_Name
[2025-07-29 10:30:17] User Fikri (NIP: 200402212020081001) dipindahkan dari Pengadilan Agama Maros ke Pengadilan Agama Pinrang oleh admin Admin_Name
```

## Test Case yang Harus Dilakukan

### 1. Test Case: Perpindahan Unit Kerja
- **Input**: CSV dengan NIP yang sudah ada dan unit kerja berbeda
- **Expected**: User dipindahkan ke unit kerja baru, semua kuota ikut berpindah
- **Status**: ✅

### 2. Test Case: Update Data Tanpa Perpindahan
- **Input**: CSV dengan NIP yang sudah ada dan unit kerja sama
- **Expected**: Data user diupdate, tidak ada perpindahan
- **Status**: ✅

### 3. Test Case: Timpa Data Existing (Unit Kerja + Jabatan Sama)
- **Input**: CSV dengan NIP baru tapi unit kerja dan jabatan sama dengan user existing
- **Expected**: Data user existing ditimpa, tidak membuat akun baru
- **Status**: ✅

### 4. Test Case: User Baru (Unit Kerja atau Jabatan Berbeda)
- **Input**: CSV dengan NIP baru dan unit kerja/jabatan berbeda
- **Expected**: User baru dibuat dengan kuota default
- **Status**: ✅

### 5. Test Case: Validasi NIP
- **Input**: CSV dengan NIP tidak valid
- **Expected**: Error message, data tidak diproses
- **Status**: ✅

### 6. Test Case: Data Kosong
- **Input**: CSV dengan kolom wajib kosong
- **Expected**: Error message, data tidak diproses
- **Status**: ✅

## Troubleshooting

### 1. Error "Username sudah ada"
- **Penyebab**: Username yang di-generate sudah digunakan
- **Solusi**: Hapus user lama atau gunakan unit kerja yang berbeda

### 2. Error "NIP sudah terdaftar"
- **Penyebab**: NIP sudah ada di database
- **Solusi**: Fitur ini seharusnya mengupdate data existing, bukan error

### 3. Kuota tidak berpindah
- **Penyebab**: Fungsi moveUserQuotaToNewUnit tidak berjalan
- **Solusi**: Cek log error dan pastikan tabel kuota ada

### 4. Log tidak tercatat
- **Penyebab**: Direktori logs tidak ada atau permission error
- **Solusi**: Buat direktori logs dan pastikan permission write

## Catatan Penting
- Backup database sebelum testing
- Test di environment development terlebih dahulu
- Pastikan semua tabel kuota cuti sudah dibuat
- Cek permission file dan direktori untuk logging 