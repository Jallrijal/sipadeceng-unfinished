# Perubahan Cuti Alasan Penting

## Ringkasan Perubahan
Perubahan telah dilakukan pada sistem cuti untuk cuti karena alasan penting sesuai permintaan:

1. **Maksimal hari cuti diubah dari 10 hari menjadi 30 hari**
2. **Fitur isi otomatis catatan cuti dihilangkan**
3. **Alert validasi ditambahkan untuk mencegah pengajuan cuti lebih dari 30 hari**

## Detail Perubahan

### 1. Database Changes
- File: `database/update_alasan_penting_30_days.sql`
- Mengubah `max_days` di tabel `leave_types` dari 10 menjadi 30 hari
- Mengupdate `kuota_tahunan` di tabel `kuota_cuti_alasan_penting` dari 10 menjadi 30 hari

### 2. Frontend Changes (app/views/leave/form.php)
- **Menghilangkan fitur isi otomatis catatan cuti** untuk cuti alasan penting
- **Menambahkan validasi alert** yang mencegah user mengajukan cuti lebih dari 30 hari
- Alert akan menampilkan pesan error dan mencegah pengajuan cuti

### 3. Backend Changes

#### app/controllers/LeaveController.php
- Validasi backend tetap menggunakan 30 hari sebagai batas maksimal
- Tidak ada perubahan pada logika validasi

#### app/controllers/UserController.php
- Mengubah informasi kuota dari "Maksimal 10 hari" menjadi "Maksimal 30 hari"
- Mengubah default kuota dari 10 menjadi 30 hari

#### app/helpers/general_helper.php
- Fungsi `validateKuotaCuti()` menggunakan 30 hari sebagai batas maksimal
- Fungsi `getSisaKuotaByType()` tetap mengembalikan 30 hari

#### app/models/LeaveBalance.php
- Informasi kuota cuti alasan penting tetap menampilkan 30 hari
- Tidak ada perubahan pada logika perhitungan

## Cara Menjalankan Perubahan

### 1. Update Database
Jalankan script SQL berikut di database:
```sql
-- Update max_days untuk cuti alasan penting dari 10 menjadi 30 hari
UPDATE leave_types SET max_days = 30 WHERE id = 5 AND nama_cuti = 'Cuti Karena Alasan Penting';

-- Update kuota cuti alasan penting untuk semua user dari 10 menjadi 30 hari
UPDATE kuota_cuti_alasan_penting SET kuota_tahunan = 30 WHERE leave_type_id = 5;
```

### 2. Deploy Code Changes
Semua perubahan kode sudah dilakukan dan siap untuk di-deploy.

## Testing

### Test Case 1: Pengajuan Cuti ≤ 30 Hari
- User mengajukan cuti alasan penting 15 hari
- Sistem harus menerima pengajuan tanpa alert
- Catatan cuti tidak diisi otomatis

### Test Case 2: Pengajuan Cuti > 30 Hari
- User mengajukan cuti alasan penting 35 hari
- Sistem harus menampilkan alert error
- Pengajuan cuti harus ditolak
- Pesan: "Cuti alasan penting maksimal 30 hari per sekali mengajukan"

### Test Case 3: Informasi Kuota
- Halaman informasi kuota harus menampilkan "Maksimal 30 hari per sekali mengajukan"
- Tidak ada lagi referensi ke 10 hari

## Status
✅ **SELESAI** - Semua perubahan telah diimplementasikan sesuai permintaan. 