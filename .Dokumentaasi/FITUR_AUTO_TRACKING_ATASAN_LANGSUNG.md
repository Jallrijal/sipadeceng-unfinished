# Fitur Auto Tracking Atasan Langsung Otomatis

## Deskripsi Fitur

Fitur ini mengotomatisasi penentuan atasan langsung untuk setiap pegawai berdasarkan jabatan mereka. Sistem akan secara otomatis:

1. Menentukan atasan langsung ketika pegawai dibuat
2. Memperbarui atasan langsung ketika jabatan pegawai berubah
3. Menampilkan preview atasan yang akan ditentukan di form untuk konfirmasi admin

## Aturan Penentuan Atasan Langsung

### 1. Atasan Struktural (Fixed)

Untuk jabatan struktural yang telah ditentukan, atasan ditentukan berdasarkan hierarki berikut:

- **Ketua** → Tidak punya atasan (NULL)
- **Wakil Ketua, Panitera, Sekretaris, Hakim Tinggi, Hakim Yustisial** → Atasan: Ketua
- **Kepala Bagian Perencanaan dan Kepegawaian** → Atasan: Sekretaris
- **Kepala Bagian Umum dan Keuangan** → Atasan: Sekretaris
- **Panitera Muda Hukum** → Atasan: Panitera
- **Panitera Muda Banding** → Atasan: Panitera
- **Panitera Pengganti** → Atasan: Panitera
- **Kepala Subbagian, Subbagian Rencana Program dan Anggaran** → Atasan: Kepala Bagian Perencanaan dan Kepegawaian
- **Kepala Subbagian, Subbagian Kepegawaian dan Teknologi Informasi** → Atasan: Kepala Bagian Perencanaan dan Kepegawaian
- **Kepala Subbagian, Subbagian Tata Usaha dan Rumah Tangga** → Atasan: Kepala Bagian Umum dan Keuangan
- **Kepala Subbagian, Subbagian Keuangan dan Pelaporan** → Atasan: Kepala Bagian Umum dan Keuangan

### 2. Pegawai Non-Struktural

Untuk pegawai yang bukan struktur tetap, atasan ditentukan dari unit kerja yang tertera di belakang koma dalam jabatan mereka.

**Format Jabatan Non-Struktural:**
```
{Nama Jabatan}, {Unit Kerja}
```

**Contoh:**
- `Pustakawan Ahli Madya, Sekretaris` → Atasan: Sekretaris
- `Klerek - Analis Perkara Peradilan, Panitera Muda Hukum` → Atasan: Panitera Muda Hukum
- `Pranata Komputer Ahli Pertama, Bagian Perencanaan dan Kepegawaian` → Atasan: Kepala Bagian Perencanaan dan Kepegawaian
- `Operator - Penata Layanan Operasional, Subbagian Rencana Program dan Anggaran` → Atasan: Kepala Subbagian, Subbagian Rencana Program dan Anggaran

### 3. Atasan dan Admin

- **Tipe User: Atasan** → Atasan ditentukan otomatis berdasarkan jabatan mereka (sesuai hierarki struktural)
- **Tipe User: Admin** → Tidak punya atasan (NULL)

## File yang Dimodifikasi/Ditambahkan

### File Baru

1. **`app/helpers/direct_superior_helper.php`**
   - Helper untuk menentukan atasan langsung otomatis
   - Function utama: `getAutomaticDirectSuperior($jabatan)`
   - Function pendukung: `getStructuralSuperior()`, `extractUnitKerjaFromJabatan()`, `getSuperiorByUnitKerja()`, dll.

### File yang Dimodifikasi

1. **`index.php`**
   - Menambahkan: `require_once 'app/helpers/direct_superior_helper.php';`

2. **`app/controllers/UserController.php`**
   - Update method `save()`: Menambahkan logika auto-detect atasan untuk pegawai (user_type = 'pegawai')
   - Tambah method `getAutoDirectSuperior()`: API endpoint untuk preview atasan otomatis

3. **`app/views/user/form.php`**
   - Menambahkan UI untuk preview atasan otomatis
   - Menambahkan JavaScript untuk trigger auto-detection saat jabatan atau user_type berubah

## Cara Kerja

### Saat Membuat Pegawai Baru (Create)

1. Admin membuka form tambah user
2. Admin memilih tipe user "Pegawai"
3. Admin mengisi jabatan
4. Saat form di-submit:
   - System memanggil `getAutomaticDirectSuperior($jabatan)`
   - Atasan otomatis ditentukan dan diisi ke field dengan `update($userId, ['atasan' => $autoSuperiorId])`
   - Pegawai tersimpan dengan atasan yang sudah ditentukan

### Saat Edit Pegawai (Update)

1. Admin membuka form edit user
2. Jika user adalah pegawai dan jabatan berubah:
   - System otomatis menghitung atasan baru
   - Atasan diperbaharui tanpa konfirmasi tambahan

### Preview Atasan di Form

1. Admin memasukkan jabatan di form
2. Saat field jabatan di-blur atau user_type berubah:
   - JavaScript memanggil API endpoint `user/getAutoDirectSuperior`
   - Preview ditampilkan dalam alert box dengan detail:
     - Nama Atasan
     - NIP Atasan
     - Jabatan Atasan

## Penggunaan Helper Function

### `getAutomaticDirectSuperior($jabatan)`

Menentukan ID atasan berdasarkan jabatan.

```php
$jabatan = "Klerek - Analis Perkara Peradilan, Panitera Muda Hukum";
$atasanId = getAutomaticDirectSuperior($jabatan);
// Returns: 7 (ID dari atasan "Panitera Muda Hukum")
```

### `updateAutoDirectSuperior($userId, $jabatan)`

Update atasan untuk seorang user berdasarkan jabatan mereka.

```php
$userId = 50;
$jabatan = "Operator - Penata Layanan Operasional, Subbagian Rencana Program dan Anggaran";
updateAutoDirectSuperior($userId, $jabatan);
// User dengan ID 50 akan mendapat atasan: Kepala Subbagian, Subbagian Rencana Program dan Anggaran
```

## Testing

### Test Case 1: Pegawai dengan Jabatan Struktural

**Input:**
- Nama: Test User 1
- NIP: 199912011999031001
- Jabatan: Panitera Muda Hukum
- User Type: Pegawai

**Expected Output:**
- Atasan ID: 7
- Atasan: Nurbaya, S.Ag., M.H.I. (Panitera Muda Hukum)

### Test Case 2: Pegawai Non-Struktural dengan Unit Kerja

**Input:**
- Nama: Test User 2
- NIP: 199512151999031002
- Jabatan: Klerek - Analis Perkara Peradilan, Panitera Muda Banding
- User Type: Pegawai

**Expected Output:**
- Atasan ID: 8
- Atasan: Hasbi, S.H., M.H. (Panitera Muda Banding)

### Test Case 3: Pegawai dengan Subbagian

**Input:**
- Nama: Test User 3
- NIP: 199801151999031003
- Jabatan: Operator - Penata Layanan Operasional, Subbagian Rencana Program dan Anggaran
- User Type: Pegawai

**Expected Output:**
- Atasan ID: 9
- Atasan: Nailah Yahya, S.Ag., M.Ag. (Kepala Subbagian, Subbagian Rencana Program dan Anggaran)

### Test Case 4: Atasan User

**Input:**
- Nama: Test Atasan
- NIP: 199850011999031004
- Jabatan: Panitera
- User Type: Atasan

**Expected Output:**
- Atasan: NULL (tidak ada atasan otomatis untuk user type atasan)

### Test Case 5: Admin User

**Input:**
- Nama: Test Admin
- NIP: 199960011999031005
- Jabatan: Admin
- User Type: Admin

**Expected Output:**
- Atasan: NULL (tidak ada atasan otomatis untuk admin)

## Troubleshooting

### Atasan Tidak Ditemukan

Jika sistem menampilkan "Tidak ada atasan otomatis yang cocok untuk jabatan ini":

1. **Periksa format jabatan non-struktural**
   - Pastikan format adalah: `{Nama Jabatan}, {Unit Kerja}`
   - Contoh yang benar: `Klerek - Analis Perkara Peradilan, Panitera Muda Hukum`

2. **Verifikasi unit kerja ada di tabel atasan**
   - Buka: Admin → Kelola Atasan
   - Pastikan jabatan unit kerja sudah terdaftar

3. **Gunakan preview untuk debugging**
   - Periksa alert box yang menampilkan hasil preview
   - Informasi di preview akan menunjukkan alasan atasan tidak ditemukan

### Atasan Berubah Tidak Sesuai Harapan

1. Periksa ulang aturan penentuan atasan di bagian **Aturan Penentuan Atasan Langsung** di atas
2. Verifikasi bahwa jabatan diketik dengan benar dan dengan capitalization yang sesuai
3. Gunakan preview feature di form sebelum menyimpan untuk mengonfirmasi atasan yang akan dipilih

## Performance Notes

- Helper function menggunakan database queries yang optimal
- Preview atasan di form menggunakan AJAX yang asynchronous (tidak blocking)
- Tidak ada impact signifikan terhadap performa sistem

## Catatan Penting

1. **Override Manual**: Admin masih bisa memilih atasan secara manual jika diperlukan (meskipun untuk user tripe pegawai, atasan otomatis akan override pilihan manual saat simpan)
2. **Saat Edit**: Ketika mengedit pegawai, jika jabatan berubah, atasan otomatis akan diupdate
3. **Case Insensitive**: Matching jabatan case-insensitive untuk fleksibilitas
4. **Null Handling**: Jika tidak ada atasan yang cocok, field atasan diset ke NULL

## Kontribusi dan Pengembangan Lebih Lanjut

Untuk menambahkan/memodifikasi aturan atasan:

1. Edit file `app/helpers/direct_superior_helper.php`
2. Ubah mapping di function `getStructuralSuperior()` atau `getKepalaSubbagianSuperior()`
3. Testing mengikuti Test Case di atas
