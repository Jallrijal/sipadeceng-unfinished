# Fitur Perpindahan Unit Kerja

## Deskripsi
Fitur ini memungkinkan admin untuk mengupdate data user yang sudah ada saat melakukan import CSV, termasuk perpindahan unit kerja. Saat user berpindah unit kerja, semua data kuota cuti akan ikut berpindah ke unit kerja tujuan.

## Cara Kerja

### 1. Deteksi User Existing
- Sistem akan mengecek apakah NIP sudah ada di database
- Jika NIP ditemukan, data akan diupdate (tidak membuat akun baru)
- Jika NIP tidak ditemukan, akan dibuat user baru
- **Username akan diupdate** sesuai unit kerja baru saat perpindahan

### 2. Update Data User
Data yang akan diupdate:
- Nama
- Jabatan  
- Golongan
- Unit Kerja
- Tanggal Masuk (dari digit 9-14 NIP)

### 3. Perpindahan Kuota Cuti
Saat unit kerja berubah, semua jenis kuota cuti akan ikut berpindah:

#### a. Cuti Tahunan (leave_balances)
- Kuota tahun 2023, 2024, 2025
- Sisa kuota tetap terjaga

#### b. Cuti Sakit (kuota_cuti_sakit)
- Kuota tahun 2023, 2024, 2025
- Sisa kuota tetap terjaga

#### c. Cuti Besar (kuota_cuti_besar)
- Kuota total dan sisa kuota tetap terjaga

#### d. Cuti Melahirkan (kuota_cuti_melahirkan)
- Kuota total dan sisa kuota tetap terjaga

#### e. Cuti Alasan Penting (kuota_cuti_alasan_penting)
- Kuota tahun 2023, 2024, 2025
- Sisa kuota tetap terjaga

#### f. Cuti Luar Tanggungan (kuota_cuti_luar_tanggungan)
- Kuota tahun 2023, 2024, 2025
- Sisa kuota tetap terjaga

## Contoh Penggunaan

### Data Lama:
1. Username: pa_pinrang, Nama: Rijal, NIP: 200305282020081001, Jabatan: Ketua Pengadilan Agama, Golongan: IV/c, Unit Kerja: Pengadilan Agama Pinrang
2. Username: pa_sengkang, Nama: Alif, NIP: 200406222020091001, Jabatan: Ketua Pengadilan Agama, Golongan: IV/d, Unit Kerja: Pengadilan Agama Sengkang
3. Username: pa_maros, Nama: Fikri, NIP: 200402212020081001, Jabatan: Ketua Pengadilan Agama, Golongan: IV/c, Unit Kerja: Pengadilan Agama Maros

### Import CSV Baru:
1. Nama: Rijal, NIP: 200305282020081001, Jabatan: Ketua Pengadilan Agama, Golongan: IV/c, Unit Kerja: Pengadilan Agama Sengkang
2. Nama: Alif, NIP: 200406222020091001, Jabatan: Ketua Pengadilan Agama, Golongan: IV/d, Unit Kerja: Pengadilan Agama Maros
3. Nama: Fikri, NIP: 200402212020081001, Jabatan: Ketua Pengadilan Agama, Golongan: IV/c, Unit Kerja: Pengadilan Agama Pinrang

### Hasil:
- Rijal: dipindahkan dari Pengadilan Agama Pinrang ke Pengadilan Agama Sengkang (username: pa_pinrang → pa_sengkang)
- Alif: dipindahkan dari Pengadilan Agama Sengkang ke Pengadilan Agama Maros (username: pa_sengkang → pa_maros)
- Fikri: dipindahkan dari Pengadilan Agama Maros ke Pengadilan Agama Pinrang (username: pa_maros → pa_pinrang)
- Semua kuota cuti ikut berpindah sesuai unit kerja baru
- Username diupdate sesuai unit kerja baru

## Format CSV
```
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
Alif;200406222020091001;Ketua Pengadilan Agama;IV/d;Pengadilan Agama Maros
Fikri;200402212020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Pinrang
```

**Catatan:** Username tidak perlu ada di CSV, akan di-generate otomatis berdasarkan unit kerja

## Keunggulan
1. **Tidak Duplikasi**: Tidak membuat akun baru untuk NIP yang sudah ada
2. **Username Dinamis**: Username diupdate sesuai unit kerja baru
3. **Data Terjaga**: Semua kuota cuti tetap terjaga saat perpindahan
4. **Masa Kerja Akurat**: Tanggal masuk tetap mengikuti digit 9-14 NIP
5. **Fleksibel**: Bisa mengupdate sebagian atau seluruh data user
6. **Audit Trail**: Sistem mencatat perpindahan unit kerja dalam log

## Catatan Penting
- **Username diupdate** sesuai unit kerja baru saat perpindahan
- Password tetap menggunakan password yang sudah ada
- Semua riwayat cuti tetap terjaga
- Tidak ada data yang hilang saat perpindahan
- Username di-generate otomatis: `pa_[nama_kota]` atau `pta_[nama_kota]` 