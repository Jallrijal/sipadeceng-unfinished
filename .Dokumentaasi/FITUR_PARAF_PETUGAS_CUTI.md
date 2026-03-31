# Fitur Paraf Petugas Cuti

## Overview
Fitur ini menambahkan kemampuan untuk user mengunggah gambar paraf yang akan otomatis muncul di kolom paraf petugas cuti pada tabel V di blanko cuti. Fitur ini hanya tersedia untuk user, admin tidak perlu mengunggah paraf.

## Fitur yang Ditambahkan

### 1. Halaman Unggah Paraf
- **URL**: `/signature/paraf`
- **Akses**: Hanya untuk user (bukan admin)
- **Fungsi**: Upload, preview, dan hapus paraf petugas cuti

### 2. Format File Paraf
- **Format yang didukung**: PNG, JPG, JPEG, GIF
- **Ukuran maksimal**: 1MB
- **Nama file**: `img-parafUser{userId}_{timestamp}.{extension}`
- **Ukuran tampilan**: 60x30 pixel (disesuaikan secara otomatis)

### 3. Placeholder Template
- **Placeholder**: `${paraf}`
- **Lokasi**: Tabel V - Catatan Cuti
- **Perilaku**: 
  - Jika user memiliki paraf: menampilkan gambar paraf
  - Jika user belum memiliki paraf: kolom kosong

## File yang Dimodifikasi

### 1. Database
- **File**: `database/add_paraf_placeholder.sql`
- **Perubahan**: Menambahkan placeholder 'paraf' ke tabel `signature_placeholders`

### 2. Controller
- **File**: `app/controllers/SignatureController.php`
- **Method yang ditambahkan**:
  - `paraf()` - Halaman manajemen paraf
  - `uploadParaf()` - Upload file paraf
  - `previewParaf()` - Preview paraf
  - `deleteParaf()` - Hapus paraf

### 3. Helper
- **File**: `app/helpers/signature_helper.php`
- **Fungsi yang ditambahkan**:
  - `uploadParafFile()` - Upload dan validasi file paraf
  - `getUserParaf()` - Mendapatkan paraf user
  - `getUserParafUrl()` - Mendapatkan URL paraf
  - `getUserParafPath()` - Mendapatkan path file paraf

### 4. Document Helper
- **File**: `app/helpers/document_helper.php`
- **Perubahan**: Menambahkan logika untuk menangani placeholder `${paraf}`

### 5. View
- **File**: `app/views/user/paraf_manage.php` (baru)
- **Fungsi**: Interface untuk mengelola paraf

### 6. Sidebar
- **File**: `app/views/layouts/sidebar_user.php`
- **Perubahan**: Menambahkan link "Paraf Petugas Cuti"

## Instalasi

### 1. Jalankan SQL
```sql
INSERT INTO signature_placeholders (placeholder_key, placeholder_name, description, section_name, is_active, created_at, updated_at) 
VALUES ('paraf', 'Paraf Petugas Cuti', 'Paraf petugas cuti pada tabel V', 'V. Catatan Cuti', 1, NOW(), NOW());
```

### 2. Pastikan Folder Upload Ada
```bash
mkdir -p public/uploads/signatures/
chmod 755 public/uploads/signatures/
```

## Cara Penggunaan

### 1. User Mengunggah Paraf
1. Login sebagai user
2. Klik menu "Paraf Petugas Cuti" di sidebar
3. Upload file gambar paraf (PNG/JPG/GIF, max 1MB)
4. Paraf akan otomatis disimpan dengan format `img-parafUser{userId}_{timestamp}.{extension}`

### 2. Paraf Otomatis di Blanko
1. User mengajukan cuti
2. Sistem generate blanko cuti
3. Placeholder `${paraf}` otomatis diganti dengan:
   - Gambar paraf user (jika sudah diupload)
   - String kosong (jika belum diupload)

### 3. Preview dan Hapus Paraf
- User dapat melihat preview paraf yang sudah diupload
- User dapat menghapus paraf yang sudah ada
- User dapat mengupload ulang paraf baru

## Struktur Database

### Tabel `user_signatures`
```sql
CREATE TABLE `user_signatures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `signature_type` enum('user','admin','paraf') NOT NULL DEFAULT 'user',
  `signature_file` varchar(255) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_signature` (`user_id`, `signature_type`)
);
```

### Tabel `signature_placeholders`
```sql
INSERT INTO signature_placeholders (placeholder_key, placeholder_name, description, section_name, is_active, created_at, updated_at) 
VALUES ('paraf', 'Paraf Petugas Cuti', 'Paraf petugas cuti pada tabel V', 'V. Catatan Cuti', 1, NOW(), NOW());
```

## Keamanan

### 1. Validasi File
- Format file: PNG, JPG, JPEG, GIF
- Ukuran maksimal: 1MB
- Validasi tipe MIME

### 2. Akses Kontrol
- Hanya user yang bisa mengakses fitur paraf
- Admin tidak bisa mengakses halaman paraf
- Validasi session untuk setiap request

### 3. Nama File Unik
- Format: `img-parafUser{userId}_{timestamp}.{extension}`
- Mencegah konflik nama file
- Mudah diidentifikasi berdasarkan user

## Template Word

### Placeholder yang Ditambahkan
- **Placeholder**: `${paraf}`
- **Lokasi**: Tabel V - Catatan Cuti
- **Ukuran**: 60x30 pixel (disesuaikan otomatis)

### Cara Menambahkan ke Template
1. Buka file `templates/blanko_cuti_template.docx`
2. Tambahkan placeholder `${paraf}` di kolom paraf petugas cuti
3. Simpan template

## Testing

### 1. Test Upload Paraf
1. Login sebagai user
2. Akses `/signature/paraf`
3. Upload file gambar
4. Verifikasi file tersimpan dengan format yang benar

### 2. Test Generate Blanko
1. User upload paraf
2. User ajukan cuti
3. Download blanko cuti
4. Verifikasi paraf muncul di kolom yang benar

### 3. Test Tanpa Paraf
1. User tanpa paraf ajukan cuti
2. Download blanko cuti
3. Verifikasi kolom paraf kosong

## Troubleshooting

### 1. Paraf Tidak Muncul
- Cek apakah file paraf tersimpan di `public/uploads/signatures/`
- Cek apakah placeholder `${paraf}` ada di template
- Cek log error PHP

### 2. Upload Gagal
- Cek permission folder `public/uploads/signatures/`
- Cek ukuran file (max 1MB)
- Cek format file (PNG/JPG/GIF)

### 3. Halaman Tidak Bisa Diakses
- Cek apakah user sudah login
- Cek apakah user bukan admin
- Cek apakah route sudah benar

## Catatan Penting

1. **Ukuran Paraf**: Paraf akan ditampilkan dengan ukuran 60x30 pixel untuk menjaga proporsi dengan kolom paraf
2. **Format File**: Gunakan format PNG untuk hasil terbaik
3. **Backup**: Selalu backup template sebelum menambahkan placeholder
4. **Testing**: Test fitur dengan berbagai ukuran dan format file
5. **Maintenance**: Bersihkan file paraf yang tidak terpakai secara berkala 