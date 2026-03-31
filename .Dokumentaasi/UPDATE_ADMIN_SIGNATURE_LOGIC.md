# Update Logika Kolom Tanda Tangan Admin

## Ringkasan Perubahan
Update ini memperbarui logika penanganan kolom tanda tangan admin pada blanko cuti untuk menangani dua skenario berbeda berdasarkan ketersediaan image tanda tangan admin.

## Perubahan Logika

### Skenario 1: Admin Memiliki Image Tanda Tangan
Ketika admin memiliki image tanda tangan yang tersimpan di sistem:

- **Placeholder `${ttd_admin}`**: Menampilkan image tanda tangan admin dengan ukuran 180px × 90px
- **Placeholder `${jabatan_admin}`**: String kosong
- **Placeholder `${nama_admin}`**: String kosong  
- **Placeholder `${nip_admin}`**: String kosong

### Skenario 2: Admin Tidak Memiliki Image Tanda Tangan
Ketika admin tidak memiliki image tanda tangan:

- **Placeholder `${ttd_admin}`**: String kosong
- **Placeholder `${jabatan_admin}`**: Data jabatan admin dari database
- **Placeholder `${nama_admin}`**: Data nama admin dari database
- **Placeholder `${nip_admin}`**: "NIP. " + data NIP admin (contoh: "NIP. 200204192020091001")

## Implementasi Kode

### File yang Diubah
- **File**: `app/helpers/document_helper.php`
- **Fungsi**: `generateLeaveDocument()`
- **Lokasi**: Bagian penanganan tanda tangan admin (sekitar baris 170-200)

### Kode yang Diperbarui
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
Template Word `blanko_cuti_template.docx` harus memiliki placeholder berikut di kolom tanda tangan admin:

- `${ttd_admin}` - untuk image tanda tangan
- `${jabatan_admin}` - untuk jabatan admin (text)
- `${nama_admin}` - untuk nama admin (text)
- `${nip_admin}` - untuk NIP admin (text dengan format "NIP. ")

### Posisi Placeholder
Placeholder harus ditempatkan di kolom tanda tangan admin sesuai dengan layout yang diinginkan:
- `${ttd_admin}` di bagian atas kolom untuk image
- `${jabatan_admin}`, `${nama_admin}`, `${nip_admin}` di bagian bawah kolom untuk text

## Alur Kerja

### 1. Generate Dokumen
1. Sistem mengambil data admin approver berdasarkan `admin_approver_id`
2. Sistem mengecek apakah admin memiliki `signature_file` yang valid
3. Jika ada image tanda tangan:
   - Set image ke placeholder `${ttd_admin}`
   - Kosongkan placeholder text lainnya
4. Jika tidak ada image tanda tangan:
   - Kosongkan placeholder `${ttd_admin}`
   - Isi placeholder text dengan data admin
   - Tambahkan "NIP. " sebelum NIP

### 2. Hasil Dokumen
- **Dengan Image**: Kolom tanda tangan berisi image tanda tangan admin yang memenuhi kolom
- **Tanpa Image**: Kolom tanda tangan berisi text jabatan, nama, dan NIP admin

## Kompatibilitas

### Database
- Tidak ada perubahan struktur database
- Menggunakan field `signature_file` yang sudah ada di tabel `admin_approvers`
- Menggunakan field `jabatan`, `nama`, `nip` yang sudah ada

### Frontend
- Tidak ada perubahan pada frontend
- Perubahan hanya pada backend document generation

### Template
- Template Word harus memiliki semua placeholder yang diperlukan
- Jika placeholder tidak ada, akan diabaikan tanpa error

## Testing

### Test Case 1: Admin dengan Image Tanda Tangan
1. Pastikan admin memiliki image tanda tangan di database
2. Generate dokumen cuti
3. Verifikasi bahwa kolom tanda tangan admin hanya menampilkan image
4. Verifikasi bahwa placeholder text kosong

### Test Case 2: Admin tanpa Image Tanda Tangan
1. Pastikan admin tidak memiliki image tanda tangan
2. Generate dokumen cuti
3. Verifikasi bahwa kolom tanda tangan admin menampilkan text jabatan, nama, dan NIP
4. Verifikasi format NIP: "NIP. [angka NIP]"

### Test Case 3: Admin Tidak Ditemukan
1. Set `admin_approver_id` ke nilai yang tidak valid
2. Generate dokumen cuti
3. Verifikasi bahwa kolom tanda tangan admin menampilkan placeholder "________________________"

## Rollback
Jika ingin mengembalikan ke logika sebelumnya:

1. Buka file `app/helpers/document_helper.php`
2. Cari bagian penanganan tanda tangan admin
3. Ganti dengan kode lama yang selalu menampilkan data text admin
4. Simpan file

---
_Dokumentasi ini dibuat untuk mencatat perubahan logika kolom tanda tangan admin pada sistem cuti._ 