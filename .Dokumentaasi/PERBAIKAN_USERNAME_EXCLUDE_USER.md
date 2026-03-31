# Perbaikan Username Saat Perpindahan Unit Kerja

## Masalah yang Ditemukan

Saat perpindahan unit kerja, username berubah menjadi suffix angka yang tidak diinginkan:

**Contoh:**
- User dengan username `pa_sengkang` dipindahkan ke unit kerja "Pengadilan Agama Sengkang"
- Seharusnya username tetap `pa_sengkang`
- Tapi malah berubah menjadi `pa_sengkang1`

**Penyebab:** Sistem tidak mengecualikan user yang sedang diupdate dari pengecekan duplikasi username.

## Solusi yang Diterapkan

### 1. Perbaikan Fungsi `generateUsername()`

**Sebelum:**
```php
private function generateUsername($satker) {
    // Generate username
    $username = 'pa_' . $lokasi;
    
    // Cek duplikasi (termasuk user yang sedang diupdate)
    while ($this->isUsernameExists($username)) {
        $username = $baseUsername . $counter;
        $counter++;
    }
    return $username;
}
```

**Sesudah:**
```php
private function generateUsername($satker, $excludeUserId = null) {
    // Generate username
    $username = 'pa_' . $lokasi;
    
    // Cek duplikasi (exclude user yang sedang diupdate)
    while ($this->isUsernameExists($username, $excludeUserId)) {
        $username = $baseUsername . $counter;
        $counter++;
    }
    return $username;
}
```

### 2. Perbaikan Fungsi `isUsernameExists()`

**Sebelum:**
```php
private function isUsernameExists($username) {
    $sql = "SELECT COUNT(*) as count FROM users WHERE username = ?";
    $result = $this->db()->fetch($sql, [$username]);
    return $result['count'] > 0;
}
```

**Sesudah:**
```php
private function isUsernameExists($username, $excludeUserId = null) {
    if ($excludeUserId) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = ? AND id != ?";
        $result = $this->db()->fetch($sql, [$username, $excludeUserId]);
    } else {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = ?";
        $result = $this->db()->fetch($sql, [$username]);
    }
    return $result['count'] > 0;
}
```

### 3. Perbaikan Fungsi `updateExistingUser()`

**Sebelum:**
```php
// Generate username baru berdasarkan unit kerja baru
$newUsername = $this->generateUsername($satker);
```

**Sesudah:**
```php
// Generate username baru berdasarkan unit kerja baru (exclude user yang sedang diupdate)
$newUsername = $this->generateUsername($satker, $userId);
```

## Skenario Testing

### Skenario 1: Perpindahan Unit Kerja (Username Tetap Sama)
**Data Existing:**
- User ID: 1, Username: `pa_sengkang`, Nama: Rijal, Unit: Pengadilan Agama Pinrang

**Import CSV:**
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
```

**Expected Result:**
- Username tetap: `pa_sengkang` (tidak berubah menjadi `pa_sengkang1`)
- Unit kerja berubah: Pinrang → Sengkang
- Log: "dipindahkan dari Pengadilan Agama Pinrang ke Pengadilan Agama Sengkang"

### Skenario 2: Perpindahan Unit Kerja (Username Berubah)
**Data Existing:**
- User ID: 1, Username: `pa_pinrang`, Nama: Rijal, Unit: Pengadilan Agama Pinrang
- User ID: 2, Username: `pa_sengkang`, Nama: Alif, Unit: Pengadilan Agama Sengkang

**Import CSV:**
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
```

**Expected Result:**
- Username berubah: `pa_pinrang` → `pa_sengkang1` (karena `pa_sengkang` sudah ada)
- Unit kerja berubah: Pinrang → Sengkang
- Log: "dipindahkan dari Pengadilan Agama Pinrang ke Pengadilan Agama Sengkang (username: pa_pinrang → pa_sengkang1)"

### Skenario 3: User Baru (Username Sudah Ada)
**Data Existing:**
- Username: `pa_toddopuli` sudah ada

**Import CSV:**
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Budi;200501152021081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Toddopuli
```

**Expected Result:**
- Username yang dibuat: `pa_toddopuli1`
- User baru dibuat dengan username unik

## Keuntungan Perbaikan

### 1. **Username Tetap Konsisten**
- Saat perpindahan unit kerja, username tidak berubah jika sudah sesuai
- Menghindari perubahan username yang tidak perlu

### 2. **Logika yang Lebih Cerdas**
- Sistem mengenali bahwa user yang sedang diupdate tidak dianggap sebagai duplikasi
- Username hanya berubah jika benar-benar diperlukan

### 3. **User Experience yang Lebih Baik**
- User tidak perlu mengingat username baru yang tidak perlu
- Login tetap menggunakan username yang familiar

### 4. **Data Integrity**
- Username tetap konsisten dengan unit kerja
- Tidak ada perubahan yang membingungkan

## Testing

### File Testing: `test_username_consistency.csv`
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
Alif;200406222020091001;Ketua Pengadilan Agama;IV/d;Pengadilan Agama Maros
Fikri;200402212020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Pinrang
```

### Expected Results:
1. **Rijal**: Username tetap `pa_sengkang` (jika sudah ada), atau berubah dari `pa_pinrang` → `pa_sengkang`
2. **Alif**: Username tetap `pa_maros` (jika sudah ada), atau berubah dari `pa_sengkang` → `pa_maros`
3. **Fikri**: Username tetap `pa_pinrang` (jika sudah ada), atau berubah dari `pa_maros` → `pa_pinrang`

## Verifikasi

### 1. Cek Username Tidak Berubah Tidak Perlu
```sql
-- Cek apakah ada user yang username berubah padahal unit kerja sama
SELECT id, username, nama, unit_kerja 
FROM users 
WHERE username LIKE '%1' OR username LIKE '%2'
ORDER BY username;
```

### 2. Cek Konsistensi Username dengan Unit Kerja
```sql
-- Cek apakah username sesuai dengan unit kerja
SELECT username, unit_kerja,
       CASE 
           WHEN username LIKE 'pa_%' AND unit_kerja LIKE '%Pengadilan Agama%' THEN 'OK'
           WHEN username LIKE 'pta_%' AND unit_kerja LIKE '%Pengadilan Tinggi Agama%' THEN 'OK'
           ELSE 'MISMATCH'
       END as status
FROM users
WHERE user_type = 'user'
ORDER BY username;
```

### 3. Cek Log Import
- Username tidak berubah jika sudah sesuai dengan unit kerja
- Pesan log yang akurat tentang perubahan username

## Kesimpulan

Perbaikan ini mengatasi masalah username yang berubah tidak perlu dengan:
- ✅ Mengecualikan user yang sedang diupdate dari pengecekan duplikasi
- ✅ Username tetap konsisten saat perpindahan unit kerja
- ✅ Hanya mengubah username jika benar-benar diperlukan
- ✅ Mempertahankan user experience yang baik
- ✅ Menjaga data integrity

Sekarang saat perpindahan unit kerja, username akan tetap sama jika sudah sesuai dengan unit kerja baru! 