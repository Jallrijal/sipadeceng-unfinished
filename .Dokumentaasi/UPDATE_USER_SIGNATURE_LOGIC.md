# Update Logika Kolom Tanda Tangan User

## Ringkasan Perubahan
Update ini memperbarui logika penanganan kolom tanda tangan user (pemohon) pada blanko cuti untuk menangani dua skenario berbeda berdasarkan ketersediaan image tanda tangan user, sama seperti yang telah diterapkan untuk admin.

## Perubahan Logika

### Skenario 1: User Memiliki Image Tanda Tangan
Ketika user memiliki image tanda tangan yang tersimpan di sistem:

- **Placeholder `${ttd_user}`**: Menampilkan image tanda tangan user dengan ukuran 120px × 60px
- **Placeholder `${nama_pemohon}`**: String kosong
- **Placeholder `${nip_pemohon}`**: String kosong

### Skenario 2: User Tidak Memiliki Image Tanda Tangan
Ketika user tidak memiliki image tanda tangan:

- **Placeholder `${ttd_user}`**: String kosong
- **Placeholder `${nama_pemohon}`**: Data nama user dari database
- **Placeholder `${nip_pemohon}`**: "NIP. " + data NIP user (contoh: "NIP. 200204192020091001")

## Implementasi Kode

### File yang Diubah
- **File**: `app/helpers/document_helper.php`
- **Fungsi**: `generateLeaveDocument()`
- **Lokasi**: Bagian penanganan tanda tangan user (sekitar baris 140-155)

### Kode yang Diperbarui
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

## Template Word

### Placeholder yang Diperlukan
Template Word `blanko_cuti_template.docx` harus memiliki placeholder berikut di kolom tanda tangan user (pemohon):

- `${ttd_user}` - untuk image tanda tangan
- `${nama_pemohon}` - untuk nama user (text)
- `${nip_pemohon}` - untuk NIP user (text dengan format "NIP. ")

### Posisi Placeholder
Placeholder harus ditempatkan di kolom tanda tangan user sesuai dengan layout yang diinginkan:
- `${ttd_user}` di bagian atas kolom untuk image
- `${nama_pemohon}`, `${nip_pemohon}` di bagian bawah kolom untuk text

## Alur Kerja

### 1. Generate Dokumen
1. Sistem mengambil data user dari `$userData`
2. Sistem mengecek apakah user memiliki tanda tangan yang valid melalui `getUserSignature()`
3. Jika ada image tanda tangan:
   - Set image ke placeholder `${ttd_user}`
   - Kosongkan placeholder text lainnya
4. Jika tidak ada image tanda tangan:
   - Kosongkan placeholder `${ttd_user}`
   - Isi placeholder text dengan data user
   - Tambahkan "NIP. " sebelum NIP

### 2. Hasil Dokumen
- **Dengan Image**: Kolom tanda tangan berisi image tanda tangan user
- **Tanpa Image**: Kolom tanda tangan berisi text nama dan NIP user

## Kompatibilitas

### Database
- Tidak ada perubahan struktur database
- Menggunakan tabel `user_signatures` yang sudah ada
- Menggunakan field `nama`, `nip` dari data user

### Frontend
- Tidak ada perubahan pada frontend
- Perubahan hanya pada backend document generation

### Template
- Template Word harus memiliki semua placeholder yang diperlukan
- Jika placeholder tidak ada, akan diabaikan tanpa error

## Testing

### Test Case 1: User dengan Image Tanda Tangan
1. Pastikan user memiliki image tanda tangan di database
2. Generate dokumen cuti
3. Verifikasi bahwa kolom tanda tangan user hanya menampilkan image
4. Verifikasi bahwa placeholder text kosong

### Test Case 2: User tanpa Image Tanda Tangan
1. Pastikan user tidak memiliki image tanda tangan
2. Generate dokumen cuti
3. Verifikasi bahwa kolom tanda tangan user menampilkan text nama dan NIP
4. Verifikasi format NIP: "NIP. [angka NIP]"

### Test Case 3: User Tidak Ditemukan
1. Set `userData` ke nilai yang tidak valid
2. Generate dokumen cuti
3. Verifikasi bahwa sistem menangani error dengan baik

## Konsistensi dengan Admin Signature

Logika ini konsisten dengan logika tanda tangan admin yang telah diimplementasikan sebelumnya:

### Admin Signature Logic
- **Dengan Image**: `${ttd_admin}` = image, `${jabatan_admin}` = kosong, `${nama_admin}` = kosong, `${nip_admin}` = kosong
- **Tanpa Image**: `${ttd_admin}` = kosong, `${jabatan_admin}` = data, `${nama_admin}` = data, `${nip_admin}` = "NIP. " + data

### User Signature Logic
- **Dengan Image**: `${ttd_user}` = image, `${nama_pemohon}` = kosong, `${nip_pemohon}` = kosong
- **Tanpa Image**: `${ttd_user}` = kosong, `${nama_pemohon}` = data, `${nip_pemohon}` = "NIP. " + data

## Rollback
Jika ingin mengembalikan ke logika sebelumnya:

1. Buka file `app/helpers/document_helper.php`
2. Cari bagian penanganan tanda tangan user
3. Ganti dengan kode lama yang selalu menampilkan data text user
4. Simpan file

---
_Dokumentasi ini dibuat untuk mencatat perubahan logika kolom tanda tangan user pada sistem cuti._ 