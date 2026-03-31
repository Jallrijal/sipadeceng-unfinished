# Instalasi Fitur Paraf Petugas Cuti

## Langkah 1: Jalankan SQL
Buka phpMyAdmin atau MySQL client dan jalankan query berikut:

```sql
INSERT INTO signature_placeholders (placeholder_key, placeholder_name, description, section_name, is_active, created_at, updated_at) 
VALUES ('paraf', 'Paraf Petugas Cuti', 'Paraf petugas cuti pada tabel V', 'V. Catatan Cuti', 1, NOW(), NOW());
```

## Langkah 2: Pastikan Folder Upload Ada
Buat folder upload jika belum ada:
```bash
mkdir -p public/uploads/signatures/
chmod 755 public/uploads/signatures/
```

## Langkah 3: Tambahkan Placeholder di Template Word
1. Buka file `templates/blanko_cuti_template.docx`
2. Tambahkan placeholder `${paraf}` di kolom paraf petugas cuti pada tabel V
3. Simpan template

## Langkah 4: Test Fitur
1. Login sebagai user (bukan admin)
2. Klik menu "Paraf Petugas Cuti" di sidebar
3. Upload file gambar paraf (PNG/JPG/GIF, max 1MB)
4. Test generate blanko cuti untuk melihat paraf otomatis

## Langkah 5: Verifikasi Instalasi
Jalankan file test untuk memverifikasi instalasi:
```
http://localhost/sistem-cuti/test_paraf_feature.php
```

## Fitur yang Tersedia
- ✅ Upload paraf petugas cuti
- ✅ Preview paraf yang sudah diupload
- ✅ Hapus paraf
- ✅ Paraf otomatis muncul di blanko cuti
- ✅ Ukuran paraf disesuaikan otomatis (60x30 pixel)
- ✅ Format file: PNG, JPG, JPEG, GIF (max 1MB)
- ✅ Nama file unik: `img-parafUser{userId}_{timestamp}.{extension}`

## Troubleshooting
- Jika paraf tidak muncul: cek apakah placeholder `${paraf}` sudah ditambahkan di template
- Jika upload gagal: cek permission folder `public/uploads/signatures/`
- Jika halaman tidak bisa diakses: pastikan login sebagai user (bukan admin)

## Catatan
- Fitur ini hanya untuk user, admin tidak perlu mengunggah paraf
- Jika user belum memiliki paraf, kolom paraf akan kosong
- Paraf akan otomatis muncul di semua blanko cuti user yang bersangkutan 