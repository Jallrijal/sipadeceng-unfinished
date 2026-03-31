# Perubahan Fitur Perpindahan Unit Kerja V2

## Ringkasan Perubahan

Fitur perpindahan unit kerja telah diperbarui dari V1 ke V2 dengan logika yang berbeda sesuai requirement baru.

## Perubahan Utama

### V1 (Sebelumnya):
- Data nama, NIP, jabatan, dan golongan berpindah ke unit kerja baru
- Username berubah mengikuti unit kerja baru
- Kuota cuti ikut berpindah

### V2 (Sekarang):
- **Ketika ada data satker (unit_kerja) yang sama, jangan buat akun baru, tapi timpa data lama dengan data baru**
- **Data nama, NIP, jabatan, dan golongan yang berpindah ke username dan unit kerja yang sudah ada**
- **Username tetap mengikuti unit kerja yang sudah ada**
- Kuota cuti tetap terjaga

## File yang Dimodifikasi

### 1. Controller
- `app/controllers/UserController.php`
  - ✅ Fungsi `processCSVRow()` - Ditambah logika deteksi user dengan unit kerja dan jabatan yang sama
  - ✅ Fungsi `overwriteExistingUser()` - Fungsi baru untuk timpa data user
  - ✅ Fungsi `logDataOverwrite()` - Fungsi baru untuk log penimpaan data

### 2. Dokumentasi
- ✅ `FITUR_PERPINDAHAN_UNIT_KERJA_V2.md` - Dokumentasi lengkap fitur V2
- ✅ `TESTING_PERPINDAHAN_UNIT_KERJA_V2.md` - Panduan testing V2
- ✅ `contoh_perpindahan_unit_kerja_v2.csv` - File contoh untuk testing V2
- ✅ `README_FITUR_PERPINDAHAN.md` - Diperbarui untuk V2

## Contoh Skenario

### Data Awal:
1. Username: pa_pinrang, Nama: Rijal, NIP: 200305282020081001, Unit Kerja: Pengadilan Agama Pinrang
2. Username: pa_sengkang, Nama: Alif, NIP: 200406222020091001, Unit Kerja: Pengadilan Agama Sengkang
3. Username: pa_maros, Nama: Fikri, NIP: 200402212020081001, Unit Kerja: Pengadilan Agama Maros

### Import CSV Baru:
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Fikri;200402212020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Pinrang
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
Alif;200406222020091001;Ketua Pengadilan Agama;IV/d;Pengadilan Agama Maros
```

### Hasil V2:
1. **pa_pinrang**: Nama berubah dari Rijal → Fikri, NIP berubah dari 200305282020081001 → 200402212020081001
2. **pa_sengkang**: Nama berubah dari Alif → Rijal, NIP berubah dari 200406222020091001 → 200305282020081001
3. **pa_maros**: Nama berubah dari Fikri → Alif, NIP berubah dari 200402212020081001 → 200406222020091001

**Username tetap sama, hanya data nama, NIP, jabatan, dan golongan yang berubah.**

## Keunggulan V2

1. **Tidak Duplikasi Username** - Username tetap mengikuti unit kerja yang sudah ada
2. **Data Terjaga** - Semua kuota cuti tetap terjaga
3. **Masa Kerja Akurat** - Tanggal masuk tetap mengikuti digit 9-14 NIP
4. **Fleksibel** - Bisa mengupdate sebagian atau seluruh data user
5. **Audit Trail** - Log lengkap untuk setiap penimpaan data

## Cara Testing

1. Siapkan data awal sesuai contoh
2. Import CSV dengan data yang berbeda
3. Verifikasi hasil penimpaan data
4. Cek username tetap sama
5. Cek kuota cuti tetap terjaga

Lihat file `TESTING_PERPINDAHAN_UNIT_KERJA_V2.md` untuk panduan testing lengkap.

## Status Implementasi

✅ **Selesai** - Fitur perpindahan unit kerja V2 telah berhasil diimplementasikan dan siap digunakan. 