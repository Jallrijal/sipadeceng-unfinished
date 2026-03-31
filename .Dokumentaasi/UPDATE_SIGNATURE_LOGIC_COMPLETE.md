# Update Logika Kolom Tanda Tangan User dan Admin

## Ringkasan Perubahan
Update ini memperbarui logika penanganan kolom tanda tangan user (pemohon) dan admin pada blanko cuti untuk menangani dua skenario berbeda berdasarkan ketersediaan image tanda tangan.

## Perubahan Logika

### Tanda Tangan User (Pemohon)

#### Skenario 1: User Memiliki Image Tanda Tangan
Ketika user memiliki image tanda tangan yang tersimpan di sistem:

- **Placeholder `${ttd_user}`**: Menampilkan image tanda tangan user dengan ukuran 120px × 60px
- **Placeholder `${nama_pemohon}`**: String kosong
- **Placeholder `${nip_pemohon}`**: String kosong

#### Skenario 2: User Tidak Memiliki Image Tanda Tangan
Ketika user tidak memiliki image tanda tangan:

- **Placeholder `${ttd_user}`**: String kosong
- **Placeholder `${nama_pemohon}`**: Data nama user dari database
- **Placeholder `${nip_pemohon}`**: "NIP. " + data NIP user (contoh: "NIP. 200204192020091001")

### Tanda Tangan Admin

#### Skenario 1: Admin Memiliki Image Tanda Tangan
Ketika admin memiliki image tanda tangan yang tersimpan di sistem:

- **Placeholder `${ttd_admin}`**: Menampilkan image tanda tangan admin dengan ukuran 180px × 90px
- **Placeholder `${jabatan_admin}`**: String kosong
- **Placeholder `${nama_admin}`**: String kosong  
- **Placeholder `${nip_admin}`**: String kosong

#### Skenario 2: Admin Tidak Memiliki Image Tanda Tangan
Ketika admin tidak memiliki image tanda tangan:

- **Placeholder `${ttd_admin}`**: String kosong
- **Placeholder `${jabatan_admin}`**: Data jabatan admin dari database
- **Placeholder `${nama_admin}`**: Data nama admin dari database
- **Placeholder `${nip_admin}`**: "NIP. " + data NIP admin (contoh: "NIP. 200204192020091001")

## Implementasi Kode

### File yang Diubah
- **File**: `app/helpers/document_helper.php`
- **Fungsi**: `generateLeaveDocument()`
- **Lokasi**: Bagian penanganan tanda tangan user dan admin

### Kode Tanda Tangan User
```php
// Tanda tangan user (pemohon)
$ttdUser = getUserSignature($userData['id'], 'user');
if ($ttdUser && file_exists(getSignatureFilePath($ttdUser['signature_file']))) {
    // User memiliki image tanda tangan: tampilkan image saja, placeholder text kosong
    $templateProcessor->setImageValue('ttd_user', [
        'path' => getSignatureFilePath($ttdUser['signature_file']),
        'width' => 120,
        'height' => 60,
        'ratio' => true
    ]);
    // Placeholder text kosong karena sudah ada image
    $templateProcessor->setValue('nama_pemohon', '');
    $templateProcessor->setValue('nip_pemohon', '');
} else {
    // User tidak memiliki image tanda tangan: tampilkan data nama dan NIP
    $templateProcessor->setValue('ttd_user', '');
    $templateProcessor->setValue('nama_pemohon', $userData['nama']);
    // Tambahkan "NIP. " sebelum angka NIP
    $templateProcessor->setValue('nip_pemohon', 'NIP. ' . $userData['nip']);
}
```

### Kode Tanda Tangan Admin
```php
// Cek apakah admin memiliki image tanda tangan
if (!empty($adminApprover['signature_file']) && file_exists(getSignatureFilePath($adminApprover['signature_file']))) {
    // Admin memiliki image tanda tangan: tampilkan image saja, placeholder text kosong
    $templateProcessor->setImageValue('ttd_admin', [
        'path' => getSignatureFilePath($adminApprover['signature_file']),
        'width' => 180,
        'height' => 90,
        'ratio' => true
    ]);
    // Placeholder text kosong karena sudah ada image
    $templateProcessor->setValue('jabatan_admin', '');
    $templateProcessor->setValue('nama_admin', '');
    $templateProcessor->setValue('nip_admin', '');
} else {
    // Admin tidak memiliki image tanda tangan: tampilkan data jabatan, nama, dan NIP
    $templateProcessor->setValue('ttd_admin', '');
    $templateProcessor->setValue('jabatan_admin', $adminApprover['jabatan']);
    $templateProcessor->setValue('nama_admin', $adminApprover['nama']);
    // Tambahkan "NIP. " sebelum angka NIP
    $templateProcessor->setValue('nip_admin', 'NIP. ' . $adminApprover['nip']);
}
```

## Template Word

### Placeholder yang Diperlukan

#### Kolom Tanda Tangan User (Pemohon)
- `${ttd_user}` - untuk image tanda tangan
- `${nama_pemohon}` - untuk nama user (text)
- `${nip_pemohon}` - untuk NIP user (text dengan format "NIP. ")

#### Kolom Tanda Tangan Admin
- `${ttd_admin}` - untuk image tanda tangan
- `${jabatan_admin}` - untuk jabatan admin (text)
- `${nama_admin}` - untuk nama admin (text)
- `${nip_admin}` - untuk NIP admin (text dengan format "NIP. ")

### Posisi Placeholder
Placeholder harus ditempatkan di kolom tanda tangan sesuai dengan layout yang diinginkan:
- Image placeholder di bagian atas kolom
- Text placeholder di bagian bawah kolom

## Alur Kerja

### 1. Generate Dokumen
1. Sistem mengambil data user dan admin
2. Sistem mengecek ketersediaan image tanda tangan
3. Jika ada image tanda tangan:
   - Set image ke placeholder image
   - Kosongkan placeholder text
4. Jika tidak ada image tanda tangan:
   - Kosongkan placeholder image
   - Isi placeholder text dengan data
   - Tambahkan "NIP. " sebelum NIP

### 2. Hasil Dokumen
- **Dengan Image**: Kolom tanda tangan berisi image tanda tangan
- **Tanpa Image**: Kolom tanda tangan berisi text data

## Kompatibilitas

### Database
- Tidak ada perubahan struktur database
- Menggunakan tabel yang sudah ada
- Menggunakan field yang sudah ada

### Frontend
- Tidak ada perubahan pada frontend
- Perubahan hanya pada backend document generation

### Template
- Template Word harus memiliki semua placeholder yang diperlukan
- Jika placeholder tidak ada, akan diabaikan tanpa error

## Testing

### Test Case 1: User dan Admin dengan Image Tanda Tangan
1. Pastikan user dan admin memiliki image tanda njj
2. Generate dokumen cuti
3. Verifikasi bahwa kolom tanda tangan hanya menampilkan image
4. Verifikasi bahwa placeholder text kosong

### Test Case 2: User dan Admin tanpa Image Tanda Tangan
1. Pastikan user dan admin tidak memiliki image tanda tangan
2. Generate dokumen cuti
3. Verifikasi bahwa kolom tanda tangan menampilkan text data
4. Verifikasi format NIP: "NIP. [angka NIP]"

### Test Case 3: Kombinasi (User dengan Image, Admin tanpa Image)
1. User memiliki image tanda tangan, admin tidak
2. Generate dokumen cuti
3. Verifikasi bahwa kolom user menampilkan image
4. Verifikasi bahwa kolom admin menampilkan text

## Konsistensi Logika

Kedua logika (user dan admin) konsisten dalam hal:

### Format NIP
- Semua NIP ditampilkan dengan format "NIP. [angka NIP]"
- Konsisten untuk user dan admin

### Kondisi Image vs Text
- Jika ada image: tampilkan image, kosongkan text
- Jika tidak ada image: kosongkan image, tampilkan text

### Ukuran Image
- User: 120px × 60px
- Admin: 180px × 90px

## Perbaikan Bug

### Bug yang Ditemukan
Pada versi sebelumnya, ketika user memiliki image tanda tangan, sistem masih menampilkan data text `${nama_pemohon}` dan `${nip_pemohon}` padahal seharusnya kosong.

### Penyebab Bug
Data pemohon diisi terlebih dahulu sebelum logika tanda tangan dinamis, sehingga data text tetap muncul meskipun user memiliki image tanda tangan.

### Solusi
Memindahkan pengisian data pemohon ke dalam logika kondisional, sehingga:
- Jika user memiliki image: data text dikosongkan
- Jika user tidak memiliki image: data text diisi dengan data user

### Kode yang Diperbaiki
```php
// SEBELUM (Bug):
// Data pemohon
$templateProcessor->setValue('nama_pemohon', $userData['nama']);
$templateProcessor->setValue('nip_pemohon', $userData['nip']);

// Logika tanda tangan dinamis...

// SESUDAH (Fixed):
// Logika tanda tangan dinamis langsung tanpa pengisian awal
$ttdUser = getUserSignature($userData['id'], 'user');
if ($ttdUser && file_exists(getSignatureFilePath($ttdUser['signature_file']))) {
    // User memiliki image: kosongkan text
    $templateProcessor->setValue('nama_pemohon', '');
    $templateProcessor->setValue('nip_pemohon', '');
} else {
    // User tidak memiliki image: isi text
    $templateProcessor->setValue('nama_pemohon', $userData['nama']);
    $templateProcessor->setValue('nip_pemohon', 'NIP. ' . $userData['nip']);
}
```

## Rollback
Jika ingin mengembalikan ke logika sebelumnya:

1. Buka file `app/helpers/document_helper.php`
2. Cari bagian penanganan tanda tangan user dan admin
3. Ganti dengan kode lama yang selalu menampilkan data text
4. Simpan file

---
_Dokumentasi ini dibuat untuk mencatat perubahan logika kolom tanda tangan user dan admin pada sistem cuti._
_Dokumentasi ini diperbarui untuk mencatat perbaikan bug pada logika tanda tangan user._ 