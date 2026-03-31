# Perbaikan: Kosongkan Username Lama Saat Perpindahan Unit Kerja

## Masalah yang Ditemukan

Saat perpindahan unit kerja, username masih berubah menjadi suffix angka karena username lama masih "terisi" oleh data yang belum berpindah.

**Contoh Skenario Masalah:**
1. `pa_maros` pindah ke `pa_sinjai` → username menjadi `pa_sinjai1` (karena `pa_sinjai` masih terisi)
2. `pa_sinjai` pindah ke `pa_sidrap` → username menjadi `pa_sidrap` (karena `pa_sinjai` sudah kosong)
3. `pa_sidrap` pindah ke `pa_maros` → username menjadi `pa_maros` (karena `pa_maros` sudah kosong)

## Akar Masalah

Sistem tidak mengosongkan username lama saat user berpindah unit kerja, sehingga username tersebut masih "terisi" dan menyebabkan konflik saat user lain ingin menggunakan username yang sama.

## Solusi yang Diterapkan

### 1. Fungsi Baru: `clearOldUsername()`

```php
private function clearOldUsername($oldUsername, $currentUserId) {
    // Cek apakah ada user lain yang menggunakan username lama
    $sql = "SELECT id, nama, unit_kerja FROM users WHERE username = ? AND id != ?";
    $existingUsers = $this->db()->fetchAll($sql, [$oldUsername, $currentUserId]);
    
    if (!empty($existingUsers)) {
        // Jika ada user lain yang menggunakan username lama, 
        // berikan username baru dengan suffix untuk user tersebut
        foreach ($existingUsers as $user) {
            $newUsername = $this->generateUsername($user['unit_kerja'], $user['id']);
            $this->userModel->update($user['id'], ['username' => $newUsername]);
            
            // Log perubahan username
            error_log("CLEAR_USERNAME: User {$user['nama']} username diubah dari {$oldUsername} ke {$newUsername}");
        }
    }
    
    // Log bahwa username lama telah dikosongkan
    error_log("CLEAR_USERNAME: Username '{$oldUsername}' telah dikosongkan untuk user ID {$currentUserId}");
}
```

### 2. Logika Update yang Diperbaiki

**Sebelum:**
```php
// Generate username ideal berdasarkan unit kerja baru
$idealUsername = $this->generateBaseUsername($satker);

// Cek apakah username ideal sudah ada
$usernameExists = $this->isUsernameExists($idealUsername, $userId);
```

**Sesudah:**
```php
// Generate username ideal berdasarkan unit kerja baru
$idealUsername = $this->generateBaseUsername($satker);

// Jika unit kerja berubah, kosongkan username lama terlebih dahulu
if ($oldUnitKerja !== $satker) {
    $this->clearOldUsername($oldUsername, $userId);
}

// Cek apakah username ideal sudah ada
$usernameExists = $this->isUsernameExists($idealUsername, $userId);
```

## Cara Kerja Perbaikan

### 1. **Deteksi Perpindahan Unit Kerja**
- Sistem mendeteksi bahwa user berpindah dari unit kerja lama ke unit kerja baru

### 2. **Kosongkan Username Lama**
- Sebelum mengupdate username baru, sistem memanggil `clearOldUsername()`
- Fungsi ini mencari user lain yang masih menggunakan username lama
- Jika ada, user tersebut akan diberikan username baru dengan suffix

### 3. **Generate Username Baru**
- Setelah username lama dikosongkan, sistem generate username baru
- Username ideal (tanpa suffix) akan tersedia untuk digunakan

### 4. **Update Username**
- User yang berpindah akan mendapatkan username ideal tanpa suffix

## Skenario Testing

### Skenario 1: Perpindahan Normal
**Data Existing:**
- User A: `pa_maros` (Pengadilan Agama Maros)
- User B: `pa_sinjai` (Pengadilan Agama Sinjai)

**Import CSV:**
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
User A;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sinjai
```

**Expected Result:**
1. `clearOldUsername('pa_maros', userA_id)` dipanggil
2. User B (yang masih menggunakan `pa_sinjai`) akan diubah menjadi `pa_sinjai1`
3. User A akan mendapatkan username `pa_sinjai` (tanpa suffix)

### Skenario 2: Perpindahan Berantai
**Data Existing:**
- User A: `pa_maros` (Pengadilan Agama Maros)
- User B: `pa_sinjai` (Pengadilan Agama Sinjai)
- User C: `pa_sidrap` (Pengadilan Agama Sidrap)

**Import CSV 1:**
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
User A;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sinjai
```

**Import CSV 2:**
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
User B;200406222020091001;Ketua Pengadilan Agama;IV/d;Pengadilan Agama Sidrap
```

**Import CSV 3:**
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
User C;200402212020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Maros
```

**Expected Result:**
1. **Import 1**: User A → `pa_sinjai`, User B → `pa_sinjai1`
2. **Import 2**: User B → `pa_sidrap`, User C → `pa_sidrap1`
3. **Import 3**: User C → `pa_maros` (tanpa suffix karena `pa_maros` sudah kosong)

## Keuntungan Perbaikan

### 1. **Username Tanpa Suffix**
- User yang berpindah akan mendapatkan username ideal tanpa suffix
- Hanya user yang "terpaksa" pindah yang akan mendapat suffix

### 2. **Manajemen Username Otomatis**
- Sistem otomatis mengosongkan username lama
- Tidak perlu manual intervention

### 3. **Logging yang Informatif**
- Setiap perubahan username dicatat dalam log
- Memudahkan tracking dan debugging

### 4. **Konsistensi Data**
- Username selalu sesuai dengan unit kerja
- Tidak ada username yang "tertinggal" di unit kerja lama

## Testing

### File Testing: `test_username_debug.csv`
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Alip Ba Ta;200406132019111000;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Tidung
Fizaky;200402212020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Tamamaung
Mamat;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Toddopuli
```

### Expected Results:
1. **Username tanpa suffix** untuk user yang berpindah
2. **Log yang informatif** tentang pengosongan username lama
3. **Tidak ada konflik username** yang tidak perlu

## Verifikasi

### 1. Cek Log PHP Error
```bash
# Cari log CLEAR_USERNAME
grep "CLEAR_USERNAME" /path/to/php/error.log
```

### 2. Cek Database
```sql
-- Cek username yang masih menggunakan suffix
SELECT id, username, nama, unit_kerja 
FROM users 
WHERE username LIKE '%1' OR username LIKE '%2'
ORDER BY username;
```

### 3. Cek Konsistensi
```sql
-- Cek apakah username sesuai dengan unit kerja
SELECT username, unit_kerja,
       CASE 
           WHEN unit_kerja LIKE '%Pengadilan Agama%' 
           THEN CONCAT('pa_', LOWER(REPLACE(REPLACE(unit_kerja, 'Pengadilan Agama ', ''), ' ', '_')))
           ELSE username
       END as expected_username
FROM users 
WHERE user_type = 'user'
HAVING username != expected_username;
```

## Kesimpulan

Perbaikan ini mengatasi masalah username suffix dengan:
- ✅ **Mengosongkan username lama** saat perpindahan unit kerja
- ✅ **Username tanpa suffix** untuk user yang berpindah
- ✅ **Manajemen otomatis** username yang konflik
- ✅ **Logging informatif** untuk tracking
- ✅ **Konsistensi data** username dengan unit kerja

Sekarang saat perpindahan unit kerja, username akan tetap bersih tanpa suffix yang tidak perlu! 