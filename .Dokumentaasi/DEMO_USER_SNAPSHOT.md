# Demo Penggunaan Fitur User Snapshot

## Langkah-langkah Demo

### 1. Login sebagai Admin
- Buka aplikasi sistem cuti
- Login dengan akun admin
- Pastikan Anda memiliki akses ke menu manajemen user

### 2. Melihat Riwayat Cuti dengan Status User
1. Buka menu "Riwayat & Status Pengajuan Cuti"
2. Perhatikan kolom baru "Status User" di tabel
3. Badge akan menampilkan:
   - 🟢 **Aktif** - User masih aktif
   - 🟡 **Diubah** - Data user telah diubah  
   - 🔴 **Dihapus** - User telah dihapus

### 3. Test Tracking Perubahan Data User
1. Buka menu "Manajemen User"
2. Pilih user yang ingin diubah
3. Ubah data penting seperti:
   - Nama
   - NIP
   - Jabatan
   - Unit Kerja
   - Golongan
4. Simpan perubahan
5. Kembali ke halaman riwayat cuti
6. Cari pengajuan cuti dari user tersebut
7. Perhatikan badge status user berubah menjadi "Diubah"

### 4. Test Soft Delete User
1. Buka menu "Manajemen User"
2. Pilih user yang ingin dihapus
3. Klik tombol "Hapus"
4. Konfirmasi penghapusan
5. Kembali ke halaman riwayat cuti
6. Cari pengajuan cuti dari user tersebut
7. Perhatikan badge status user berubah menjadi "Dihapus"

### 5. Melihat Daftar Snapshot
1. Buka menu "Riwayat Perubahan User" (di sidebar admin)
2. Atau akses langsung: `/user/snapshots`
3. Lihat daftar semua snapshot yang telah dibuat
4. Filter berdasarkan tipe perubahan (Diubah/Dihapus)
5. Cari snapshot berdasarkan nama atau NIP

### 6. Melihat Detail Snapshot
1. Klik tombol "Detail" pada salah satu snapshot
2. Lihat perbandingan data:
   - Data snapshot (sebelum perubahan)
   - Data user aktif (jika masih ada)
   - Riwayat pengajuan cuti user tersebut

### 7. Filter Berdasarkan Status User
1. Di halaman riwayat cuti, gunakan filter "Status User"
2. Pilih "Aktif" - hanya menampilkan user yang masih aktif
3. Pilih "Diubah/Dihapus" - hanya menampilkan user yang telah berubah
4. Kombinasikan dengan filter lain (status, tahun, jenis cuti)

## Contoh Skenario

### Skenario 1: User Diubah Jabatan
1. User "John Doe" memiliki riwayat cuti tahun 2024
2. Admin mengubah jabatan John dari "Staff" menjadi "Supervisor"
3. Sistem otomatis membuat snapshot data John sebelum perubahan
4. Di riwayat cuti, pengajuan John tahun 2024 akan menampilkan badge "Diubah"
5. Admin dapat melihat data John sebelum dan sesudah perubahan

### Skenario 2: User Dihapus
1. User "Jane Smith" memiliki riwayat cuti tahun 2023-2024
2. Admin menghapus akun Jane
3. Sistem membuat snapshot data Jane sebelum dihapus
4. Di riwayat cuti, semua pengajuan Jane akan menampilkan badge "Dihapus"
5. Admin tetap dapat melihat riwayat lengkap Jane termasuk data pribadinya

### Skenario 3: Audit Trail
1. Admin ingin melihat semua perubahan user dalam periode tertentu
2. Buka halaman "Riwayat Perubahan User"
3. Filter berdasarkan tanggal snapshot
4. Lihat statistik perubahan (berapa user diubah, berapa yang dihapus)
5. Export data untuk keperluan audit

## Fitur Tambahan

### Export Data
- Riwayat cuti dapat di-export ke Excel
- Kolom "Status User" akan ikut ter-export
- Memudahkan analisis data untuk audit

### Pencarian
- Cari snapshot berdasarkan nama, NIP, atau username
- Filter berdasarkan tipe perubahan
- Tampilan yang responsif

### Statistik
- Lihat statistik perubahan user
- Grafik berdasarkan tanggal
- Analisis tren perubahan

## Keuntungan Fitur

1. **Data Integrity**: Tidak ada data yang hilang
2. **Compliance**: Memenuhi kebutuhan audit
3. **Transparency**: Tracking perubahan yang jelas
4. **User Experience**: Interface yang intuitif
5. **Performance**: Query yang efisien dengan view

## Troubleshooting

### Jika data tidak muncul:
1. Pastikan migrasi database berhasil dijalankan
2. Cek apakah view `v_leave_history_complete` ada
3. Verifikasi data di tabel `user_snapshots`

### Jika performa lambat:
1. Tambahkan index pada kolom yang sering diquery
2. Pertimbangkan pagination untuk data besar
3. Monitor query execution time

### Jika ada error:
1. Cek log error aplikasi
2. Pastikan permission database user
3. Verifikasi struktur tabel dan view 