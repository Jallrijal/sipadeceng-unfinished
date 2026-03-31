# Solusi Pemisahan Tanda Tangan Kolom VII & VIII pada Blanko Template

## 1. Identifikasi Peran Atasan
- Setiap user memiliki atasan, dan atasan bisa saja seorang admin (merangkap dua peran).
- Perlu logika untuk mengecek apakah atasan user juga merupakan admin (cek di tabel `admin_approvers`).

## 2. Proses Tanda Tangan Kolom VII (Atasan)
- **Jika atasan adalah admin:**  
  - Proses tanda tangan kolom VII dan VIII dilakukan sekaligus oleh admin.
  - Data (image tanda tangan/nama, NIP, jabatan) diambil dari tabel `admin_approvers`.
- **Jika atasan bukan admin:**  
  - Kolom VII diisi manual oleh atasan (bisa upload image atau manual).
  - Data (nama, NIP, jabatan) diambil dari tabel `atasan`.
  - Setelah atasan menandatangani, user mengirim blanko ke admin untuk kolom VIII.

## 3. Proses Tanda Tangan Kolom VIII (Admin)
- **Selalu diisi oleh admin** setelah keputusan diambil.
- Data diambil dari tabel `admin_approvers`.

## 4. Alur Sistem (Pseudocode/Flow)
1. User mengajukan cuti → sistem cek atasan user.
2. Cek apakah atasan user juga admin:
   - **Ya:**  
     - Admin login, sistem tampilkan dua kolom (VII & VIII) untuk diisi sekaligus.
     - Data diambil dari `admin_approvers`.
   - **Tidak:**  
     - User/atasan login, sistem hanya tampilkan kolom VII untuk diisi atasan.
     - Setelah kolom VII diisi, user kirim ke admin untuk kolom VIII.
     - Data kolom VII dari `atasan`, kolom VIII dari `admin_approvers`.

## 5. Implementasi di Kode
- Tambahkan logika di controller (misal di `LeaveController` atau `DocumentModel`) untuk:
  - Mengecek peran atasan (query ke `admin_approvers` dan `atasan`).
  - Menentukan siapa yang harus menandatangani kolom VII dan VIII.
  - Menampilkan/mengaktifkan form tanda tangan sesuai peran.
- Pastikan view (template blanko) bisa menerima dua sumber data berbeda untuk kolom VII dan VIII.

## 6. Saran Teknis
- Buat fungsi helper, misal: `isAtasanAdmin($userId)` untuk cek apakah atasan user adalah admin.
- Pastikan proses upload/signature manual bisa dilakukan untuk kedua peran.
- Simpan status penandatanganan (misal: `signed_by_atasan`, `signed_by_admin`) agar alur approval jelas.

---

**Contoh logika pengecekan di PHP:**
```php
function isAtasanAdmin($atasanId) {
    // Cek di tabel admin_approvers
    $admin = AdminApprover::findByUserId($atasanId);
    return $admin !== null;
}
```
**Di controller:**
```php
if (isAtasanAdmin($atasanId)) {
    // Tampilkan form tanda tangan untuk admin di kolom VII & VIII
} else {
    // Tampilkan form tanda tangan untuk atasan di kolom VII
    // Setelah itu, kirim ke admin untuk kolom VIII
}
```

---

Jika ingin implementasi kode lebih detail (controller, model, atau view), sebutkan file/fungsi yang ingin diubah.
