# Alur Pengajuan Cuti Baru

## Overview
Sistem pengajuan cuti telah diperbarui dengan alur yang lebih terstruktur dan aman. Alur baru ini memastikan bahwa setiap tahap pengajuan cuti memiliki bukti fisik berupa blanko yang ditandatangani.

## Alur Lengkap

### 1. User Login
- User melakukan login ke sistem
- Sistem memverifikasi kredensial user

### 2. User Masuk ke Page Pengajuan Cuti
- User mengakses halaman formulir pengajuan cuti
- Sistem menampilkan form pengajuan cuti

### 3. User Memilih dan Mengisi Data
- User memilih jenis cuti
- User mengisi periode cuti (tanggal mulai dan selesai)
- User mengisi alasan cuti
- User mengisi alamat dan nomor telepon selama cuti
- User mengisi catatan cuti (opsional)
- User mengupload dokumen pendukung (opsional)

### 4. User Konfirmasi dan Kirim Pengajuan
- User mengkonfirmasi data yang telah diisi
- Sistem memvalidasi data
- Sistem menyimpan pengajuan dengan status "draft"
- Sistem generate blanko cuti dengan tanda tangan user otomatis

### 5. User Download Blanko
- User download blanko yang telah digenerate sistem
- Blanko sudah termasuk tanda tangan user di placeholder `${ttd_user}`
- User cetak blanko dan tandatangani secara manual

### 6. User Upload Blanko yang Ditandatangani
- User scan blanko yang telah ditandatangani
- User upload blanko ke sistem
- Sistem memverifikasi file upload
- Sistem mengubah status menjadi "pending"
- Sistem mengirim notifikasi ke admin

### 7. Admin Menerima Notifikasi
- Admin menerima notifikasi pengajuan cuti baru
- Admin dapat melihat pengajuan di halaman persetujuan
- **PENTING**: Pengajuan hanya muncul jika blanko sudah diupload

### 8. Admin Login
- Admin melakukan login ke sistem
- Sistem memverifikasi kredensial admin

### 9. Admin Masuk ke Page Persetujuan Cuti
- Admin mengakses halaman persetujuan cuti
- Admin melihat daftar pengajuan yang sudah diupload blanko

### 10. Admin Proses Pengajuan
- Admin memilih pengajuan untuk diproses
- Admin memilih "Setuju" atau "Tolak"
- Jika setuju: catatan opsional
- Jika tolak: catatan wajib
- Sistem generate blanko final dengan dua tanda tangan

### 11. Admin Download dan Upload Blanko Final
- Admin download blanko final yang telah digenerate
- Admin cetak dan tandatangani blanko final
- Admin scan dan upload blanko final ke sistem
- Sistem mengubah status menjadi "completed"

### 12. Pemotongan Kuota Cuti
- **PENTING**: Kuota cuti hanya dipotong setelah blanko final diupload
- Sistem memotong kuota cuti untuk jenis cuti yang akumulatif
- Sistem menandai kuota sudah dipotong

### 13. User Menerima Notifikasi
- User menerima notifikasi bahwa pengajuan telah diproses
- User dapat download blanko final sebagai bukti

### 14. User Download Blanko Final
- User download blanko final yang telah ditandatangani user dan admin
- Blanko final berfungsi sebagai bukti persetujuan/penolakan

## Perubahan Database

### Tabel `leave_requests`
- `blanko_uploaded` (TINYINT): Status upload blanko user
- `blanko_upload_date` (DATETIME): Tanggal upload blanko user
- `final_blanko_sent` (TINYINT): Status pengiriman blanko final
- `final_blanko_sent_date` (DATETIME): Tanggal pengiriman blanko final
- `quota_deducted` (TINYINT): Status pemotongan kuota cuti

### Tabel `leave_documents`
- `upload_date` (DATETIME): Tanggal upload dokumen
- `sent_date` (DATETIME): Tanggal pengiriman dokumen

### Tabel `notifications`
- `related_leave_id` (INT): ID pengajuan cuti terkait

## Status Pengajuan

### Draft
- Pengajuan baru dibuat
- User belum upload blanko
- User dapat edit pengajuan

### Pending
- User sudah upload blanko
- Menunggu admin memproses
- User tidak dapat edit lagi

### Approved/Rejected
- Admin sudah memproses
- Blanko final sudah digenerate
- Menunggu admin upload blanko final

### Completed
- Blanko final sudah diupload admin
- Kuota cuti sudah dipotong (jika disetujui)
- Proses selesai

## Keunggulan Alur Baru

1. **Keamanan**: Setiap tahap memiliki bukti fisik
2. **Transparansi**: Status pengajuan jelas dan terstruktur
3. **Kontrol**: Admin hanya dapat memproses setelah blanko diupload
4. **Audit Trail**: Semua aktivitas tercatat dengan timestamp
5. **Fleksibilitas**: User dapat download ulang blanko jika diperlukan

## File yang Diperbarui

### Backend
- `app/controllers/LeaveController.php`
- `app/controllers/ApprovalController.php`
- `app/models/Leave.php`
- `app/models/Notification.php`
- `app/helpers/workflow_helper.php`

### Frontend
- `app/views/leave/form.php`
- `app/views/leave/draft.php`
- `app/views/leave/history.php`
- `app/views/approval/index.php`

### Database
- `database/update_leave_workflow.sql`

## Cara Menjalankan Update

1. Jalankan SQL update:
```sql
source database/update_leave_workflow.sql;
```

2. Pastikan semua file telah diperbarui

3. Test alur pengajuan cuti baru

## Catatan Penting

- Alur lama masih dapat diakses untuk data yang sudah ada
- Data lama akan tetap berfungsi normal
- Semua pengajuan baru akan mengikuti alur baru
- Backup database sebelum menjalankan update 