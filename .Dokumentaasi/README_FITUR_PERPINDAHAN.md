# Fitur Perpindahan Unit Kerja V2 - Sistem Cuti

## Ringkasan Implementasi

Fitur perpindahan unit kerja V2 telah berhasil diimplementasikan dalam sistem cuti. Fitur ini memungkinkan admin untuk mengupdate data user yang sudah ada saat melakukan import CSV, termasuk perpindahan unit kerja dengan logika yang berbeda dari versi sebelumnya.

## Perubahan Logika dari V1 ke V2

### V1 (Sebelumnya):
- Data nama, NIP, jabatan, dan golongan berpindah ke unit kerja baru
- Username berubah mengikuti unit kerja baru
- Kuota cuti ikut berpindah

### V2 (Sekarang):
- **Ketika ada data satker (unit_kerja) yang sama, jangan buat akun baru, tapi timpa data lama dengan data baru**
- **Data nama, NIP, jabatan, dan golongan yang berpindah ke username dan unit kerja yang sudah ada**
- **Username tetap mengikuti unit kerja yang sudah ada**
- Kuota cuti tetap terjaga

## Fitur yang Diimplementasikan

### ✅ 1. Deteksi User Existing
- Sistem mengecek NIP yang sudah ada di database
- Jika NIP ditemukan, data akan diupdate (tidak membuat akun baru)
- Jika NIP tidak ditemukan, sistem mengecek apakah ada user dengan unit kerja dan jabatan yang sama
- **Jika ada user dengan unit kerja dan jabatan yang sama, data akan ditimpa**
- Jika tidak ada, akan dibuat user baru

### ✅ 2. Overwrite Data User
- **Data lama ditimpa dengan data baru**
- Nama, NIP, jabatan, dan golongan dapat diupdate
- Tanggal masuk tetap mengikuti digit 9-14 NIP
- **Username tetap mengikuti unit kerja yang sudah ada**

### ✅ 3. Preservasi Kuota Cuti
- Semua jenis kuota cuti tetap terjaga
- Tidak ada perpindahan kuota karena username tetap sama
- Data kuota tetap terkait dengan user_id yang sama

### ✅ 4. Logging System
- Mencatat semua penimpaan data
- Log disimpan dalam file `logs/unit_kerja_transfer.log`
- Mencatat admin yang melakukan perubahan
- Timestamp untuk audit trail

### ✅ 5. UI/UX Improvements
- Modal import CSV yang informatif
- Penjelasan fitur perpindahan unit kerja
- Alert success untuk fitur baru
- Pesan yang jelas saat perpindahan berhasil

## File yang Dimodifikasi

### 1. Controller
- `app/controllers/UserController.php`
  - Fungsi `processCSVRow()` - Deteksi user existing dengan logika baru
  - Fungsi `overwriteExistingUser()` - Timpa data user yang sudah ada
  - Fungsi `updateExistingUser()` - Update data user (untuk NIP yang sama)
  - Fungsi `createNewUser()` - Buat user baru

### 2. View
- `app/views/user/manage.php`
  - Modal import CSV dengan informasi fitur baru
  - Alert success untuk fitur perpindahan unit kerja

### 3. Dokumentasi
- `FITUR_PERPINDAHAN_UNIT_KERJA_V2.md` - Dokumentasi lengkap fitur V2
- `TESTING_PERPINDAHAN_UNIT_KERJA_V2.md` - Panduan testing V2
- `contoh_perpindahan_unit_kerja_v2.csv` - File contoh untuk testing V2

### 4. Logging
- `logs/unit_kerja_transfer.log` - File log perpindahan
- `.gitignore` - Mengabaikan file log dari git

## Cara Penggunaan

### 1. Login sebagai Admin
```
Username: admin
Password: password
```

### 2. Akses Menu Import
- Menu: User Management
- Klik: Tambah User → Import CSV

### 3. Upload File CSV
Format CSV yang didukung:
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
Alif;200406222020091001;Ketua Pengadilan Agama;IV/d;Pengadilan Agama Maros
```

### 4. Verifikasi Hasil
- Cek data user di database
- Cek kuota cuti tetap terjaga
- Cek log perpindahan di `logs/unit_kerja_transfer.log`

## Contoh Skenario

### Data Awal:
1. Username: pa_pinrang, Nama: Rijal, NIP: 200305282020081001, Jabatan: Ketua Pengadilan Agama, Golongan: IV/c, Unit Kerja: Pengadilan Agama Pinrang
2. Username: pa_sengkang, Nama: Alif, NIP: 200406222020091001, Jabatan: Ketua Pengadilan Agama, Golongan: IV/d, Unit Kerja: Pengadilan Agama Sengkang
3. Username: pa_maros, Nama: Fikri, NIP: 200402212020081001, Jabatan: Ketua Pengadilan Agama, Golongan: IV/c, Unit Kerja: Pengadilan Agama Maros

### Import CSV Baru:
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Fikri;200402212020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Pinrang
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
Alif;200406222020091001;Ketua Pengadilan Agama;IV/d;Pengadilan Agama Maros
```

### Hasil:
1. **pa_pinrang**: Nama berubah dari Rijal → Fikri, NIP berubah dari 200305282020081001 → 200402212020081001
2. **pa_sengkang**: Nama berubah dari Alif → Rijal, NIP berubah dari 200406222020091001 → 200305282020081001
3. **pa_maros**: Nama berubah dari Fikri → Alif, NIP berubah dari 200402212020081001 → 200406222020091001

**Username tetap sama, hanya data nama, NIP, jabatan, dan golongan yang berubah.**

## Keunggulan Fitur

### 1. **Tidak Duplikasi Username**
- Username tetap mengikuti unit kerja yang sudah ada
- Tidak ada konflik username

### 2. **Data Terjaga**
- Semua kuota cuti tetap terjaga
- Tidak ada data yang hilang

### 3. **Masa Kerja Akurat**
- Tanggal masuk tetap mengikuti digit 9-14 NIP
- Perhitungan masa kerja tetap akurat

### 4. **Fleksibel**
- Bisa mengupdate sebagian atau seluruh data user
- Mendukung berbagai format CSV

### 5. **Audit Trail**
- Log lengkap untuk setiap penimpaan data
- Mencatat admin yang melakukan perubahan

## Testing

### Test Case yang Sudah Diimplementasikan:
1. ✅ Penimpaan data user dengan unit kerja dan jabatan yang sama
2. ✅ Update data tanpa penimpaan (NIP yang sama)
3. ✅ User baru
4. ✅ Validasi NIP
5. ✅ Data kosong

### Cara Testing:
Lihat file `TESTING_PERPINDAHAN_UNIT_KERJA_V2.md` untuk panduan lengkap.

## Troubleshooting

### Masalah Umum:
1. **Data tidak tertimpa**: Pastikan unit kerja dan jabatan sama persis
2. **Username berubah**: Fitur V2 seharusnya mempertahankan username
3. **Kuota hilang**: Cek log error dan pastikan user_id tetap sama
4. **Log tidak tercatat**: Pastikan direktori logs ada dan permission write

### Solusi:
- Backup database sebelum testing
- Test di environment development terlebih dahulu
- Pastikan semua tabel kuota cuti sudah dibuat
- Cek permission file dan direktori untuk logging

## Kesimpulan

Fitur perpindahan unit kerja V2 telah berhasil diimplementasikan dengan logika yang berbeda dari V1:

✅ **Ketika terdapat data satker (unit_kerja) yang sama, tidak membuat akun baru, tapi timpa data lama dengan data baru**
✅ **Data nama, NIP, jabatan, dan golongan yang berpindah ke username dan unit kerja yang sudah ada**
✅ **Username tetap mengikuti unit kerja yang sudah ada**
✅ **Jumlah kuota cuti seluruh jenis cuti user tetap terjaga**
✅ **Masa kerja masih mengikuti digit 9-14 NIP**
✅ **Update dilakukan dengan input CSV**

Fitur ini siap digunakan dan telah dilengkapi dengan dokumentasi lengkap, panduan testing, dan sistem logging untuk audit trail. 