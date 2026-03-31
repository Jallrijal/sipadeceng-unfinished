# Update Template Blanko Cuti: Kolom Tanda Tangan Admin

## Ringkasan Perubahan
Pada update ini, dilakukan perubahan pada file template dokumen Word `blanko_cuti_template.docx` dengan **menghapus tiga placeholder** berikut dari kolom tanda tangan admin:

- `${jabatan_admin}`
- `${nama_admin}`
- `${nip_admin}`

Sehingga, di kolom tanda tangan admin kini hanya tersisa placeholder `${ttd_admin}` saja. Hal ini dilakukan karena pada gambar tanda tangan admin sudah terdapat informasi jabatan, nama, dan NIP.

## Langkah Perubahan
1. Buka file `templates/blanko_cuti_template.docx` menggunakan Microsoft Word atau aplikasi sejenis.
2. Cari kolom tanda tangan admin.
3. Hapus teks/placeholder `${jabatan_admin}`, `${nama_admin}`, dan `${nip_admin}`.
4. Pastikan hanya `${ttd_admin}` yang tersisa di kolom tersebut.
5. Simpan file template.

## Catatan Penting
- **Kode backend (PHP)** masih menyediakan data untuk `${jabatan_admin}`, `${nama_admin}`, dan `${nip_admin}`. Jika di kemudian hari ingin mengembalikan placeholder tersebut, cukup tambahkan kembali placeholder di template Word tanpa perlu mengubah kode backend, database, atau frontend.
- Tidak ada perubahan pada kode program, database, maupun frontend terkait update ini.

## Cara Mengembalikan Placeholder
Jika ingin mengembalikan placeholder yang dihapus:
1. Buka kembali file `blanko_cuti_template.docx`.
2. Tambahkan placeholder `${jabatan_admin}`, `${nama_admin}`, dan `${nip_admin}` di posisi yang diinginkan pada kolom tanda tangan admin.
3. Simpan file template.
4. Sistem akan otomatis mengisi placeholder tersebut sesuai data admin pada dokumen yang digenerate berikutnya.

## Update Ukuran Tanda Tangan Admin
Pada update ini, ukuran image tanda tangan admin pada placeholder `${ttd_admin}` diperbesar agar lebih memenuhi kolom tanda tangan, sehingga tampak lebih jelas dan proporsional.

- **Ukuran baru:**
  - width: 180 px
  - height: 90 px
- **Ukuran sebelumnya:**
  - width: 120 px
  - height: 60 px

Perubahan ini hanya mempengaruhi tampilan gambar tanda tangan admin pada dokumen Word yang dihasilkan.

### Cara Mengembalikan Ukuran ke Semula
Jika ingin mengembalikan ukuran tanda tangan admin ke ukuran semula (misal jika placeholder `${jabatan_admin}`, `${nama_admin}`, dan `${nip_admin}` ingin dikembalikan sehingga butuh ruang di bawah tanda tangan):

1. Buka file `app/helpers/document_helper.php`.
2. Cari bagian berikut:
   ```php
   $templateProcessor->setImageValue('ttd_admin', [
       'path' => getSignatureFilePath($adminApprover['signature_file']),
       'width' => 180, // sebelumnya 120
       'height' => 90, // sebelumnya 60
       'ratio' => true
   ]);
   ```
3. Ubah nilai `width` menjadi `120` dan `height` menjadi `60`.
4. Simpan file.
5. Tambahkan kembali placeholder yang diinginkan pada template Word jika perlu.

## Update Logika Kolom Tanda Tangan Admin (Versi Terbaru)

### Perubahan Logika
Pada update terbaru ini, logika kolom tanda tangan admin telah diperbarui untuk menangani dua skenario:

#### 1. Admin Memiliki Image Tanda Tangan
- **Placeholder `${ttd_admin}`**: Menampilkan image tanda tangan admin
- **Placeholder `${jabatan_admin}`**: String kosong
- **Placeholder `${nama_admin}`**: String kosong  
- **Placeholder `${nip_admin}`**: String kosong
- **Ukuran Image**: 180px × 90px (memenuhi kolom)

#### 2. Admin Tidak Memiliki Image Tanda Tangan
- **Placeholder `${ttd_admin}`**: String kosong
- **Placeholder `${jabatan_admin}`**: Data jabatan admin
- **Placeholder `${nama_admin}`**: Data nama admin
- **Placeholder `${nip_admin}`**: "NIP. " + data NIP admin (contoh: "NIP. 200204192020091001")

### Implementasi Kode
Perubahan dilakukan pada file `app/helpers/document_helper.php` pada bagian penanganan tanda tangan admin:

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

### Template Word
Untuk menggunakan fitur ini, template Word `blanko_cuti_template.docx` harus memiliki placeholder berikut di kolom tanda tangan admin:
- `${ttd_admin}` - untuk image tanda tangan
- `${jabatan_admin}` - untuk jabatan admin (text)
- `${nama_admin}` - untuk nama admin (text)
- `${nip_admin}` - untuk NIP admin (text dengan format "NIP. ")

---
_Dokumentasi ini dibuat untuk memudahkan rollback atau penyesuaian format blanko di masa mendatang._ 
_Dokumentasi ini diperbarui untuk mencatat perubahan ukuran tanda tangan admin pada blanko cuti._
_Dokumentasi ini diperbarui untuk mencatat perubahan logika kolom tanda tangan admin yang baru._ 