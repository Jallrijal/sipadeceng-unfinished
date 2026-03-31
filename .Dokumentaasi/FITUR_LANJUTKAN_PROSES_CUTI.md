# Fitur: Lanjutkan Proses Pengajuan Cuti dengan Paraf Admin

## Deskripsi Fitur

Fitur ini memungkinkan Admin/Pimpinan untuk melanjutkan proses pengajuan cuti yang sudah disetujui. Ketika tombol "Lanjutkan Proses Pengajuan Cuti" ditekan, sistem akan:

1. **Generate ulang blanko cuti** dengan isi yang sama seperti sebelumnya
2. **Isi placeholder ${paraf}** dengan gambar paraf milik admin yang sedang melanjutkan proses
3. **Simpan blanko baru** ke database sebagai dokumen generated terbaru

### Alur Proses

```
Admin disetujui cuti (status=approved)
         ↓
Admin menekan tombol "Lanjutkan Proses"
         ↓
Sistem validate admin sudah upload paraf
         ↓
Update admin_blankofinal_sender = user_id admin
         ↓
Generate ulang blanko cuti dengan placeholder ${paraf} diisi
         ↓
Simpan dokumen baru ke database
         ↓
Proses selesai, blanko siap untuk tahap berikutnya
```

---

## Prerequisites

### 1. Admin Harus Upload Paraf Terlebih Dahulu

Sebelum dapat melanjutkan proses pengajuan cuti, admin **HARUS** sudah upload paraf di halaman:

**Menu:** Signature > **Paraf Petugas Cuti**
- **URL:** `/signature/paraf` (atau klik menu Signature → Paraf Petugas Cuti)
- **Spesifikasi File:**
  - Format: PNG, JPG, JPEG, atau GIF
  - Ukuran maksimal: 1 MB
  - Rekomendasi: Gunakan file dengan format landscape/landscape untuk hasil yang optimal

**Langkah Upload Paraf:**

1. Masuk ke halaman: **Signature → Paraf Petugas Cuti**
2. Jika sudah ada paraf sebelumnya, akan ditampilkan preview
3. Klik tombol **"Upload/Update Paraf"** dan pilih file gambar paraf
4. Klik tombol **"Upload"** atau **"Update"** untuk menyimpan
5. Tunggu sampai success message muncul

**Catatan:** 
- Paraf disimpan di folder: `public/uploads/signatures/img-parafUser{user_id}_{timestamp}.{ext}`
- Hanya 1 paraf yang dapat aktif per admin
- Jika upload paraf baru, paraf lama otomatis diganti

### 2. Status Pengajuan Cuti harus 'approved'

Tombol "Lanjutkan Proses" hanya muncul ketika:
- Status pengajuan = **"Disetujui"** (approved)
- `admin_blankofinal_sender` masih kosong (belum pernah di-lanjutkan sebelumnya)
- `is_completed` = 0

---

## Panduan Penggunaan

### Step-by-Step: Melanjutkan Proses Pengajuan Cuti

#### Step 1: Pastikan Paraf Sudah Diupload

1. Login sebagai Admin
2. Pergi ke **Signature → Paraf Petugas Cuti**
3. Verifikasi bahwa paraf sudah ter-upload dan terlihat previewnya
4. Jika belum, upload paraf terlebih dahulu

#### Step 2: Buka Halaman Daftar Pengajuan Cuti

1. Pergi ke **Approval → Daftar Pengajuan Cuti**
2. Tab/dropdown status akan menampilkan daftar pengajuan

#### Step 3: Cari Pengajuan dengan Status 'Disetujui'

1. Filter status: Pilih **"Disetujui"** dari dropdown status
2. Sistem akan menampilkan daftar pengajuan dengan status approved
3. Cari pengajuan yang ingin di-lanjutkan prosesnya

#### Step 4: Klik Tombol "Lanjutkan"

Tombol "Lanjutkan Proses Pengajuan Cuti" akan muncul di kolom Aksi jika:
- Status = "Disetujui" ✓
- admin_blankofinal_sender kosong ✓
- is_completed = 0 ✓

Klik tombol **"Lanjutkan"** (warna hijau dengan icon play)

#### Step 5: Konfirmasi

1. Dialog konfirmasi akan muncul dengan pertanyaan: "Apakah Anda yakin ingin melanjutkan proses pengajuan cuti ini?"
2. Klik **"Ya, Lanjutkan"** untuk melanjutkan
3. Klik **"Batal"** untuk membatalkan operasi

#### Step 6: Tunggu Proses Generate Blanko

1. Sistem akan menampilkan loading animation: "Memproses... Mohon tunggu sebentar"
2. Proses usually memakan waktu 3-10 detik tergantung ukuran file

#### Step 7: Sukses atau Error

**Jika SUKSES:**
- Dialog sukses akan muncul dengan message: 
  > "Proses pengajuan cuti berhasil dilanjutkan. Blanko cuti telah di-generate ulang dengan paraf admin."
- Klik **"OK"** dan halaman akan otomatis refresh
- Tombol "Lanjutkan" akan hilang (diganti dengan tombol Download/Upload)

**Jika ERROR:**
- Dialog error akan muncul dengan message yang menjelaskan masalahnya
- Baca error message dengan seksama untuk troubleshooting

---

## Troubleshooting

### Error: "Anda belum upload paraf..."

**Masalah:** 
```
Anda belum upload paraf di halaman Manajemen Paraf (Signature > Paraf Petugas Cuti). 
Silakan upload paraf terlebih dahulu sebelum melanjutkan proses pengajuan cuti.
```

**Solusi:**
1. Pergi ke halaman: **Signature → Paraf Petugas Cuti**
2. Upload file gambar paraf (PNG/JPG, max 1MB)
3. Tunggu sampai success message muncul
4. Kembali ke halaman Approval dan coba "Lanjutkan" lagi

---

### Error: "Proses sudah dilanjutkan sebelumnya"

**Masalah:** 
```
Proses sudah dilanjutkan sebelumnya.
```

**Penjelasan:**
Pengajuan cuti ini sudah pernah di-lanjutkan prosesnya oleh admin sebelumnya. Tombol "Lanjutkan" tidak akan muncul lagi.

**Solusi:**
- Hal ini normal dan sesuai rancangan sistem
- Jika perlu generate ulang blanko dengan paraf yang berbeda, hubungi administrator sistem

---

### Error: "Status cuti harus approved untuk melanjutkan proses"

**Masalah:** 
```
Status cuti harus approved untuk melanjutkan proses.
```

**Penjelasan:**
Status pengajuan cuti tidak sedang dalam status "Disetujui". Tombol "Lanjutkan" hanya muncul untuk status "Disetujui" saja.

**Solusi:**
- Pastikan pengajuan sudah disetujui oleh atasan/pimpinan lebih dahulu
- Tunggu sampai status berubah menjadi "Disetujui"

---

### Placeholder ${paraf} Masih Kosong di Blanko

**Masalah:**
Setelah melanjutkan proses, blanko di-generate tapi placeholder ${paraf} masih kosong (tidak ada gambar paraf).

**Kemungkinan Penyebab:**
1. Paraf belum di-upload atau sudah expired
2. File paraf tidak ditemukan di filesystem
3. Ada error saat reading file paraf

**Solusi:**
1. **Re-upload Paraf:**
   - Pergi ke: **Signature → Paraf Petugas Cuti**
   - Hapus paraf lama (klik tombol "Hapus Paraf")
   - Upload paraf baru
   - Pastikan preview muncul dengan benar

2. **Check File Sistem:**
   - Cek folder: `public/uploads/signatures/`
   - Verifikasi bahwa file `img-parafUser{user_id}_*.png` ada
   - Jika tidak ada, ada issue dengan upload process

3. **Cek Database:**
   - Buka database
   - Query: `SELECT * FROM user_signatures WHERE user_id = {admin_id} AND signature_type = 'paraf'`
   - Pastikan record ada dan `is_active = 1`

3. **Cek Error Log:**
   - Buka file logs: `logs/` folder
   - Cari log dengan pattern "PLACEHOLDER PARAF" untuk debugging detail
   - Perhatikan error message yang spesifik

4. **Jika Masih Error:**
   - Hubungi System Administrator
   - Sertakan message dari error log saat reporting

---

## Technical Details

### Database Schema

**Tabel: `user_signatures`**

```sql
CREATE TABLE user_signatures (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    signature_type VARCHAR(50) NOT NULL,  -- 'user', 'paraf', dll
    signature_file VARCHAR(255) NOT NULL,
    file_size INT,
    file_type VARCHAR(50),
    is_active TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

**Tabel: `leave_requests` (relevant fields)**

```sql
ALTER TABLE leave_requests ADD COLUMN (
    admin_blankofinal_sender INT,           -- user_id dari admin yang melanjutkan proses
    is_completed TINYINT DEFAULT 0
);
```

### File-File yang Terlibat

| File | Fungsi |
|------|--------|
| `app/controllers/ApprovalController.php` | Logika `continueProcess()` - handle request dari tombol "Lanjutkan" |
| `app/helpers/document_helper.php` | Fungsi `generateLeaveDocument()` - generate blanko dengan placeholder paraf |
| `app/helpers/signature_helper.php` | Helper functions untuk signature/paraf |
| `app/models/Signature.php` | Model untuk query user_signatures table |
| `app/views/approval/index.php` | View yang menampilkan tombol "Lanjutkan" dan logika JS |
| `app/views/user/paraf_manage.php` | View untuk upload paraf |
| `public/uploads/signatures/` | Folder tempat menyimpan gambar paraf |

### Logging Points

Debugging logs dapat ditemukan di file logs dengan pattern:

**Di document_helper.php:**
```
=== PLACEHOLDER PARAF START ===
Leave Status: {status}
StatusRequiresParaf: {TRUE/FALSE}
AdminBlankofinalSender: {user_id atau NULL}
HasAdminSender: {TRUE/FALSE}
ParafAdminId: {user_id}
Direct DB Query Result: {FOUND/NOT FOUND}
Paraf File Path: {path}
File Exists: {YES/NO}
✓ Placeholder ${paraf} successfully set...  // SUCCESS
atau
✗ File tidak ditemukan di: {path}  // ERROR
=== PLACEHOLDER PARAF END ===
```

**Di continueProcess():**
```
=== CONTINUE PROCESS START ===
Admin User ID: {user_id}
Admin Paraf File: {signature_file}
Leave ID: {leave_id}
Database Update Result: {SUCCESS/FAILED}
Leave admin_blankofinal_sender (after update): {user_id atau NULL}
generateLeaveDocument execution: {SUCCESS/FAILED}
=== CONTINUE PROCESS END ===
```

---

## FAQ (Frequently Asked Questions)

**Q: Bisa tidak saya lanjutkan proses dengan paraf admin lain?**

**A:** Tidak. Paraf yang digunakan adalah paraf dari admin yang sedang login dan menekan tombol "Lanjutkan Proses". Satu admin = satu paraf.

---

**Q: Berapa kali saya bisa klik "Lanjutkan Proses" untuk pengajuan yang sama?**

**A:** Hanya **SEKALI**. Setelah klik, `admin_blankofinal_sender` akan ter-set dan tidak bisa diubah. Tombol "Lanjutkan" tidak akan muncul lagi.

---

**Q: Jika paraf di-update, apakah blanko yang sudah di-generate juga ikut update?**

**A:** Tidak. Blanko yang sudah di-generate menggunakan paraf pada waktu tersebut. Jika admin update paraf, hanya blanko yang di-generate setelah update yang akan menggunakan paraf baru.

---

**Q: Di mana hasil blanko yang sudah di-generate?**

**A:** Tersimpan di database dengan status `document_type = 'generated'`. Admin dapat download dari halaman Approval dengan klik icon download.

---

## Support & Reporting

Jika menemukan masalah atau bug:

1. Catat **error message** yang muncul
2. Catat **user_id admin** yang mengalami masalah
3. Catat **leave_id/ID pengajuan cuti** yang bermasalah
4. Check file logs untuk debugging detail
5. Hubungi System Administrator dengan informasi di atas

---

## Changelog

### Version 1.0 (2026-03-05)

**Added:**
- ✨ Fitur tombol "Lanjutkan Proses Pengajuan Cuti" di halaman Approval
- ✨ Validasi bahwa admin sudah upload paraf sebelum lanjut
- ✨ Auto-fill placeholder ${paraf} dengan paraf admin yang melanjutkan
- ✨ Logging detail untuk debugging
- 📝 Dokumentasi lengkap fitur

**Fixed:**
- 🔧 Improved error handling di continueProcess()
- 🔧 Better database query untuk mencari paraf dengan fallback
- 🔧 Comprehensive logging untuk troubleshooting

---

*Dokumentasi ini terakhir diupdate: **2026-03-05***

*Untuk informasi lebih lanjut, lihat file-file yang terlibat di folder `app/`*
