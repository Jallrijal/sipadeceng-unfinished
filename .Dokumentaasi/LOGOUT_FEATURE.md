# Fitur Logout dengan SweetAlert Konfirmasi

## Deskripsi
Fitur logout dengan konfirmasi menggunakan SweetAlert2 telah ditambahkan ke sistem cuti. Fitur ini memberikan pengalaman pengguna yang lebih baik dengan menampilkan dialog konfirmasi sebelum logout.

## Fitur yang Ditambahkan

### 1. Konfirmasi Logout
- Dialog konfirmasi dengan SweetAlert2
- Pesan konfirmasi dalam bahasa Indonesia
- Tombol "Ya, Logout!" dan "Batal"
- Loading state saat proses logout

### 2. Lokasi Tombol Logout
- Sidebar Admin (desktop dan mobile)
- Sidebar User (desktop dan mobile)
- Dropdown menu di navbar

### 3. Styling
- Konsisten dengan tema aplikasi (warna hijau)
- Hover effect pada tombol logout
- Responsive design

## File yang Dimodifikasi

### JavaScript
- `public/js/main.js` - Menghapus kode logout lama
- `public/js/logout.js` - File baru untuk fungsi logout
- `app/views/auth/login.php` - Menambahkan SweetAlert untuk pesan login

### CSS
- `public/css/style.css` - Menambahkan styling untuk logout dan SweetAlert

### PHP Views
- `app/views/layouts/main.php` - Menambahkan include logout.js
- `app/views/layouts/sidebar_admin.php` - Menambahkan class logout-link
- `app/views/layouts/sidebar_user.php` - Menambahkan class logout-link
- `app/views/layouts/navbar.php` - Menambahkan class logout-link

## Cara Kerja

1. **Event Handler**: Semua link logout dengan class `logout-link` atau href yang mengandung `auth/logout` akan ditangkap
2. **Konfirmasi**: SweetAlert menampilkan dialog konfirmasi
3. **Loading State**: Jika user mengkonfirmasi, tampilkan loading state
4. **Redirect**: Redirect ke halaman logout

## Konfigurasi SweetAlert

```javascript
Swal.fire({
    title: 'Konfirmasi Logout',
    text: 'Apakah Anda yakin ingin keluar dari sistem?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Ya, Logout!',
    cancelButtonText: 'Batal',
    reverseButtons: true,
    allowOutsideClick: false,
    allowEscapeKey: false
});
```

## Dependencies
- SweetAlert2 v11 (sudah diinclude di layout utama)
- jQuery 3.6.0
- Bootstrap 5.3.2

## Testing
Untuk menguji fitur logout:
1. Login ke sistem
2. Klik tombol logout di sidebar atau navbar
3. Konfirmasi dialog yang muncul
4. Pastikan redirect ke halaman login berhasil

## Catatan
- Fitur ini kompatibel dengan semua browser modern
- Responsive design untuk mobile dan desktop
- Menggunakan event delegation untuk performa yang lebih baik
- Konsisten dengan tema aplikasi yang ada 