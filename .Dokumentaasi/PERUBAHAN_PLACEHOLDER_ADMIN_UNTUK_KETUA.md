# Perubahan Penempatan Placeholder Admin untuk Approval Final Ketua

**Tanggal**: Maret 2, 2026
**Status**: Selesai

## Ringkasan

Sebelumnya, placeholder `${jabatan_admin}`, `${ttd_admin}`, `${nama_admin}`, dan `${nip_admin}` diisi setelah admin (user_type=admin) melakukan approval final.

Sekarang, approval final dilakukan oleh atasan dengan role=ketua, dan placeholder tersebut harus diisi dengan data ketua dari tabel `atasan`.

## Perubahan yang Dilakukan

### 1. File: `app/helpers/document_helper.php`

**Lokasi**: Bagian "Kolom VIII. Pejabat Berwenang (admin or ketua)" (baris ~480-615)

**Perubahan yang dilakukan**:
- Menambahkan logika untuk mengecek apakah approval final dilakukan oleh ketua (`$isApprovalFinalByKetua`)
- Jika ya, mengambil data ketua dari tabel `atasan` menggunakan `id_atasan = ketua_approver_id`
- Mencari user ketua dari tabel `users` berdasarkan NIP
- Mengambil signature ketua dari tabel `user_signatures` dengan `signature_type = 'user'`
- Mengisi placeholder dengan data ketua:
  - `${jabatan_admin}` = kolom "jabatan" dari tabel "atasan"
  - `${ttd_admin}` = image dari kolom "signature_file" tabel "user_signature" (diisi image jika ada, kosong jika tidak)
  - `${nama_admin}` = kolom "nama_atasan" dari tabel "atasan"
  - `${nip_admin}` = kolom "NIP" dari tabel "atasan"

**Mekanisme pemanggilan**:
- Placeholder terisi ketika status adalah salah satu dari: `'awaiting_pimpinan'`, `'approved'`, `'rejected'`, `'changed'`, `'postponed'`
- Placeholder kosong ketika belum ada `ketua_approver_id` atau status belum sampai level approval final
- Jika tidak ada data ketua atau signature, placeholder diisi dengan nama/NIP dari atasan, atau garis kosong jika tidak ada data sama sekali

**Kondisi dan Logika**:
```php
$isApprovalFinalByKetua = in_array($leaveData['status'], [
    'awaiting_pimpinan', 'approved', 'rejected', 'changed', 'postponed'
]) && isset($leaveData['ketua_approver_id']) && !empty($leaveData['ketua_approver_id']);

if ($isApprovalFinalByKetua) {
    // Gunakan data ketua
    // 1. Ambil dari tabel atasan
    // 2. Cari user_id dari tabel users
    // 3. Ambil signature dari tabel user_signatures
    // 4. Fill placeholder dengan data ketua
} elseif ($includeAdminSignature) {
    // Gunakan data admin approver (backward compatibility)
} else {
    // Kosongkan placeholder
}
```

### 2. File: `app/controllers/LeaveController.php`

**Lokasi**: Method `regenerateDocument()` (baris ~720-750)

**Perubahan yang dilakukan**:
- Menambahkan `ketua_approver_id` ke dalam array `$leaveData` saat memanggil `generateLeaveDocument()`

**Line tambahan (di array $leaveData, baris ~745)**:
```php
'ketua_approver_id' => isset($fullData['ketua_approver_id']) ? $fullData['ketua_approver_id'] : null,
```

**Alasan**: Agar ketua_approver_id tersedia saat dokumen di-generate, sehingga logika di document_helper.php dapat mengakses nilai tersebut.

## Alur Eksekusi

### Saat User Biasa Mengajukan Cuti (Status: draft)
1. `generateLeaveDocument()` dipanggil dengan `$includeAdminSignature = false`
2. Placeholder admin tidak ter-isi (kosong)
3. Placeholder atasan diisi dengan data dari tabel `atasan` menggunakan `atasan_id`

### Saat Ketua Melakukan Approval Final (Status: approved/rejected/changed/postponed)
1. Di ApprovalController, status diubah menjadi `'approved'` (atau status lain)
2. `generateLeaveDocument()` dipanggil dengan `$includeAdminSignature = true`
3. Di document_helper.php:
   - Mengecek `$isApprovalFinalByKetua` = true (karena status = approved dan ketua_approver_id tidak kosong)
   - Mengambil data ketua dari tabel `atasan`
   - Mengisi variable `$adminJabatan`, `$adminNama`, `$adminNip`, `$adminSignatureFile` dengan data ketua
   - Placeholder diisi dengan data ketua
4. Untuk user dengan atasan_id == 1 (atasan_satu), placeholder kolom VII (nama_atasan, nip_atasan, jabatan_atasan) juga menggunakan data ketua (dari variable `$adminJabatan`, dll)

## Database Queries Baru

### Query untuk mengambil data ketua:
```sql
SELECT a.id_atasan, a.NIP, a.nama_atasan, a.jabatan 
FROM atasan a 
WHERE a.id_atasan = ?
```

### Query untuk mencari user ketua:
```sql
SELECT u.id 
FROM users u 
WHERE u.nip = ? AND u.user_type = 'atasan' AND u.is_deleted = 0 LIMIT 1
```

### Query untuk mengambil signature ketua:
```sql
SELECT signature_file 
FROM user_signatures 
WHERE user_id = ? AND signature_type = 'user' AND is_active = 1
```

## Testing

Untuk memverifikasi perubahan:

1. **Buat pengajuan cuti** dengan user biasa
   - Verifikasi: placeholder admin kosong di dokumen draft

2. **Lakukan approval final** dengan ketua
   - Verifikasi: placeholder admin terisi dengan data ketua:
     - `${jabatan_admin}` = jabatan ketua
     - `${ttd_admin}` = signature ketua (jika ada)
     - `${nama_admin}` = nama_atasan ketua
     - `${nip_admin}` = NIP ketua

3. **Test dengan user atasan_id == 1**:
   - Verifikasi: kolom VII (nama_atasan, dll) juga menggunakan data ketua setelah approval final

## Kompatibilitas Backward

Perubahan ini tetap kompatibel dengan legacy flow:
- Jika ada approval menggunakan admin_approver (yang lama), sistem tetap menggunakan data admin
- Kondisional check memastikan data ketua digunakan hanya saat `ketua_approver_id` ada dan status tepat
- Jika ketua tidak memiliki signature, sistem fallback menggunakan data nama/NIP dari tabel atasan

## Error Handling

Sistem memiliki error handling untuk beberapa skenario:
1. **Ketua atasan tidak ditemukan**: placeholder diisi dengan garis kosong
2. **Ketua user tidak ditemukan di tabel users**: menggunakan data dari tabel atasan
3. **Signature ketua tidak ditemukan**: placeholder ttd_admin dikosongkan, nama/NIP tetap diisi
4. **File signature tidak ditemukan di disk**: placeholder ttd_admin dikosongkan, nama/NIP tetap diisi

Semua kondisi di-log ke error log untuk tracking.

## Notes Teknis

- `ketua_approver_id` disimpan di database saat sekretaris forward ke ketua (ApprovalController, baris 275)
- Query `SELECT lr.*` di LeaveController sudah meng-include `ketua_approver_id` (backward compatible)
- Helper function `getSignatureFilePath()` digunakan untuk mendapatkan path lengkap dari nama file signature
