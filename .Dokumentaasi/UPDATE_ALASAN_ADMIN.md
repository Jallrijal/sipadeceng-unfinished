# Update Fitur Alasan Admin pada Blanko Final

## Overview
Update ini menambahkan fitur untuk menampilkan alasan admin (catatan approval) pada blanko final yang di-generate setelah admin menyetujui atau menolak pengajuan cuti.

## Perubahan yang Diterapkan

### 1. Update Document Helper (`app/helpers/document_helper.php`)
- **File**: `app/helpers/document_helper.php`
- **Perubahan**: Menambahkan kode untuk mengisi placeholder `${alasan_admin}` dengan data `catatan_approval` dari database
- **Lokasi**: Setelah checkbox keputusan lama (sekitar baris 200)

```php
// Alasan admin (catatan approval)
$alasanAdmin = '';
if (isset($leaveData['catatan_approval']) && !empty($leaveData['catatan_approval'])) {
    $alasanAdmin = $leaveData['catatan_approval'];
} else {
    $alasanAdmin = '-';
}
$templateProcessor->setValue('alasan_admin', $alasanAdmin);
```

### 2. Update LeaveController (`app/controllers/LeaveController.php`)
- **File**: `app/controllers/LeaveController.php`
- **Perubahan**: Memastikan data `catatan_approval` tersedia saat generate dokumen di method `regenerateDocument`
- **Lokasi**: Method `regenerateDocument` (sekitar baris 420)

```php
// Prepare data for document generation
$leaveData = [
    'leave_type_id' => $fullData['leave_type_id'],
    'nomor_surat' => $fullData['nomor_surat'],
    'tanggal_mulai' => $fullData['tanggal_mulai'],
    'tanggal_selesai' => $fullData['tanggal_selesai'],
    'jumlah_hari' => $fullData['jumlah_hari'],
    'alasan' => $fullData['alasan'],
    'alamat_cuti' => $fullData['alamat_cuti'],
    'telepon_cuti' => $fullData['telepon_cuti'],
    'catatan_cuti' => $fullData['catatan_cuti'] ?? '',
    'status' => $fullData['status'],
    'approved_by' => $fullData['approved_by'],
    'catatan_approval' => $fullData['catatan_approval'] ?? ''
];
```

### 3. Update Query SQL
- **File**: `app/controllers/LeaveController.php`
- **Perubahan**: Memastikan field `catatan_approval` dan `approved_by` diambil dari database
- **Lokasi**: Method `regenerateDocument` (sekitar baris 410)

```php
// Get full leave data
$sql = "SELECT lr.*, lt.nama_cuti, u.*, lr.catatan_approval, lr.approved_by
        FROM leave_requests lr
        JOIN leave_types lt ON lr.leave_type_id = lt.id
        JOIN users u ON lr.user_id = u.id
        WHERE lr.id = ?";
```

## Template Blanko Cuti
Untuk menggunakan fitur ini, Anda perlu menambahkan placeholder `${alasan_admin}` di template blanko cuti (`templates/blanko_cuti_template.docx`) pada posisi yang diinginkan, yaitu:
- **Lokasi**: Di bawah tabel VII, di bawah checkbox disetujui, perubahan, dan ditangguhkan
- **Format**: `${alasan_admin}`
- **Posisi**: Sebelum kolom tanda tangan admin untuk menghindari tumpang tindih

## Alur Kerja

### 1. Admin Memproses Pengajuan
1. Admin login ke sistem
2. Admin masuk ke halaman persetujuan cuti
3. Admin memilih pengajuan untuk diproses
4. Admin memilih "Setuju" atau "Tolak"
5. Admin mengisi catatan/alasan (wajib untuk penolakan, opsional untuk persetujuan)
6. Sistem menyimpan `catatan_approval` ke database

### 2. Generate Blanko Final
1. Sistem otomatis generate blanko final dengan dua tanda tangan
2. Placeholder `${alasan_admin}` diganti dengan isi `catatan_approval`
3. Jika tidak ada catatan, akan ditampilkan "-"
4. Dokumen disimpan di folder `public/uploads/documents/signed/`

### 3. User Download Blanko Final
1. User menerima notifikasi bahwa pengajuan telah diproses
2. User dapat download blanko final yang sudah termasuk alasan admin
3. Alasan admin ditampilkan di posisi yang telah ditentukan di template

## Data yang Digunakan
- **Field Database**: `catatan_approval` dari tabel `leave_requests`
- **Placeholder Template**: `${alasan_admin}`
- **Default Value**: "-" (jika tidak ada catatan)

## Kompatibilitas
- **Backward Compatible**: Ya, perubahan ini tidak mempengaruhi fitur yang sudah ada
- **Database**: Tidak memerlukan perubahan struktur database
- **Template**: Memerlukan penambahan placeholder di template Word

## Testing
File test telah dibuat: `test_alasan_admin.php` untuk memverifikasi bahwa placeholder berfungsi dengan benar.

## Catatan Penting
1. Pastikan template blanko cuti sudah memiliki placeholder `${alasan_admin}` di posisi yang tepat
2. Placeholder harus ditempatkan sebelum kolom tanda tangan admin untuk menghindari tumpang tindih
3. Jika admin tidak mengisi catatan, akan ditampilkan "-" sebagai default
4. Fitur ini hanya aktif untuk dokumen final (setelah admin memproses pengajuan) 