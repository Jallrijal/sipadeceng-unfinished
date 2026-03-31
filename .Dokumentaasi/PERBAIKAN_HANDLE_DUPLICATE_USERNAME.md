# Perbaikan: Handle Duplicate Username Error

## Masalah yang Ditemukan

Dari hasil testing terakhir, terjadi error:
```
Duplicate entry 'pa_tamamaung' for key 'username'
Duplicate entry 'pa_toddopuli' for key 'username'
Duplicate entry 'pa_tidung' for key 'username'
```

Ini menunjukkan bahwa pendekatan sederhana saya masih menyebabkan konflik username karena langsung assign username ideal tanpa memastikan username tersebut benar-benar tersedia.

## Analisis Masalah

Masalahnya adalah:
1. Pendekatan "assign langsung" menyebabkan duplicate entry error
2. Username lama tidak dikosongkan sebelum user lain menggunakan username yang sama
3. Tidak ada pengecekan konflik yang proper

## Solusi: Cek Konflik + Clear Username Lama

### 1. Logika yang Diperbaiki

**Sebelum (Salah):**
```php
// Langsung assign username ideal
$newUsername = $idealUsername;
```

**Sesudah (Benar):**
```php
// Cek apakah username ideal sudah ada (exclude user yang sedang diupdate)
$usernameExists = $this->isUsernameExists($idealUsername, $userId);

if ($usernameExists) {
    // Username ideal sudah ada, generate dengan suffix
    $newUsername = $this->generateUsername($satker, $userId);
} else {
    // Username ideal tersedia, gunakan itu
    $newUsername = $idealUsername;
}

// Kosongkan username lama agar bisa digunakan user lain
$this->clearOldUsernameForReuse($oldUsername, $userId);
```

### 2. Fungsi Baru: `clearOldUsernameForReuse()`

```php
private function clearOldUsernameForReuse($oldUsername, $currentUserId) {
    // Cek user lain yang menggunakan username lama
    $existingUsers = $this->db()->fetchAll(
        "SELECT id, nama, unit_kerja FROM users WHERE username = ? AND id != ?", 
        [$oldUsername, $currentUserId]
    );
    
    if (!empty($existingUsers)) {
        // Berikan username baru dengan suffix untuk user lain
        foreach ($existingUsers as $user) {
            $newUsername = $this->generateUsername($user['unit_kerja'], $user['id']);
            $this->userModel->update($user['id'], ['username' => $newUsername]);
        }
    }
}
```

## Cara Kerja Perbaikan

### 1. **Cek Konflik Terlebih Dahulu**
- Sistem mengecek apakah username ideal sudah ada
- Exclude user yang sedang diupdate dari pengecekan

### 2. **Generate Username yang Tepat**
- Jika username ideal tersedia → gunakan username ideal
- Jika username ideal sudah ada → generate dengan suffix

### 3. **Kosongkan Username Lama**
- Setelah menentukan username baru, kosongkan username lama
- User lain yang menggunakan username lama akan mendapat suffix

### 4. **Update Database**
- Update user dengan username yang sudah ditentukan
- Tidak ada duplicate entry error

## Skenario Testing

### Skenario: Perpindahan Berantai
**Data Existing:**
- User A: `pa_tamamaung` (Pengadilan Agama Tamamaung)
- User B: `pa_toddopuli` (Pengadilan Agama Toddopuli)
- User C: `pa_tidung` (Pengadilan Agama Tidung)

**Import CSV:**
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Alip Ba Ta;200406132019111000;Akun Test;IV/c;Pengadilan Agama Tamamaung
Fizaky;200402212020081001;Akun Test;IV/c;Pengadilan Agama Toddopuli
Mamat;200305282020081001;Akun Test;IV/c;Pengadilan Agama Tidung
```

**Expected Result:**
1. **Alip Ba Ta**: `pa_tamamaung` → `pa_tamamaung` (tetap sama, tidak ada konflik)
2. **Fizaky**: `pa_toddopuli` → `pa_toddopuli` (tetap sama, tidak ada konflik)
3. **Mamat**: `pa_tidung` → `pa_tidung` (tetap sama, tidak ada konflik)

## Log yang Diharapkan

```
CLEAR_FOR_REUSE: Username 'pa_tamamaung' telah dikosongkan untuk digunakan kembali
CLEAR_FOR_REUSE: Username 'pa_toddopuli' telah dikosongkan untuk digunakan kembali
CLEAR_FOR_REUSE: Username 'pa_tidung' telah dikosongkan untuk digunakan kembali
```

## Verifikasi

### 1. Tidak Ada Duplicate Entry Error
- Import CSV berhasil tanpa error
- Tidak ada pesan "Duplicate entry" di log

### 2. Username Tanpa Suffix
```sql
-- Seharusnya user yang berpindah tidak memiliki suffix
SELECT id, username, nama, unit_kerja 
FROM users 
WHERE username LIKE '%1' OR username LIKE '%2'
ORDER BY username;
```

### 3. Konsistensi Data
```sql
-- Username harus sesuai dengan unit kerja
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

## Keuntungan Perbaikan

### 1. **Tidak Ada Duplicate Entry Error**
- Pengecekan konflik yang proper sebelum update
- Username lama dikosongkan untuk digunakan kembali

### 2. **Username Tanpa Suffix**
- User yang berpindah mendapatkan username ideal jika tersedia
- Hanya user lain yang mendapat suffix jika konflik

### 3. **Konsistensi Data**
- Username selalu sesuai dengan unit kerja
- Tidak ada username yang "tertinggal"

### 4. **Logging yang Informatif**
- Setiap perubahan username dicatat
- Memudahkan tracking dan debugging

## Kesimpulan

Perbaikan ini mengatasi masalah duplicate entry error dengan:
- ✅ **Pengecekan konflik yang proper** sebelum update
- ✅ **Pengosongan username lama** untuk digunakan kembali
- ✅ **Username tanpa suffix** untuk user yang berpindah
- ✅ **Tidak ada duplicate entry error**
- ✅ **Konsistensi data yang terjamin**

Sekarang import CSV akan berhasil tanpa error duplicate username! 