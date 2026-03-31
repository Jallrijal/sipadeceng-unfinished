# Perbaikan Logika Username yang Lebih Cerdas

## Masalah yang Ditemukan

Saat perpindahan unit kerja, username masih berubah menjadi suffix angka padahal seharusnya tetap sama jika sudah sesuai dengan unit kerja baru.

**Contoh:**
- User dengan username `pa_tidung` dipindahkan ke "Pengadilan Agama Tidung"
- Seharusnya username tetap `pa_tidung`
- Tapi malah berubah menjadi `pa_tidung1`

## Solusi yang Diterapkan

### 1. Pemisahan Fungsi Username Generation

**Fungsi Baru: `generateBaseUsername()`**
```php
private function generateBaseUsername($satker) {
    // Generate username dasar tanpa suffix
    // Contoh: "Pengadilan Agama Tidung" → "pa_tidung"
    return $username;
}
```

**Fungsi Existing: `generateUsername()`**
```php
private function generateUsername($satker, $excludeUserId = null) {
    // Generate username dengan suffix jika diperlukan
    $username = $this->generateBaseUsername($satker);
    
    // Tambah suffix jika sudah ada
    while ($this->isUsernameExists($username, $excludeUserId)) {
        $username = $baseUsername . $counter;
        $counter++;
    }
    return $username;
}
```

### 2. Logika Update Username yang Lebih Cerdas

**Sebelum:**
```php
// Update username jika unit kerja berubah DAN username berbeda
if ($oldUnitKerja !== $satker && $oldUsername !== $newUsername) {
    $updateData['username'] = $newUsername;
}
```

**Sesudah:**
```php
// Update username jika unit kerja berubah
if ($oldUnitKerja !== $satker) {
    // Jika username lama sudah sesuai dengan unit kerja baru, jangan ubah
    $idealUsername = $this->generateBaseUsername($satker);
    if ($oldUsername === $idealUsername) {
        // Username sudah sesuai, tidak perlu update
        $newUsername = $oldUsername;
    } else {
        // Username perlu diupdate
        $updateData['username'] = $newUsername;
    }
}
```

### 3. Pesan Log yang Lebih Informatif

**Sebelum:**
```php
$action = "dipindahkan dari {$oldUnitKerja} ke {$satker} (username: {$oldUsername} → {$newUsername})";
```

**Sesudah:**
```php
$idealUsername = $this->generateBaseUsername($satker);
if ($oldUsername === $idealUsername) {
    $action = "dipindahkan dari {$oldUnitKerja} ke {$satker} (username tetap: {$oldUsername})";
} elseif ($oldUsername !== $newUsername) {
    $action = "dipindahkan dari {$oldUnitKerja} ke {$satker} (username: {$oldUsername} → {$newUsername})";
}
```

## Skenario Testing

### Skenario 1: Username Sudah Sesuai
**Data Existing:**
- Username: `pa_tidung`, Nama: Alip, Unit: Pengadilan Agama Tamamaung

**Import CSV:**
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Alip;200406132019111000;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Tidung
```

**Expected Result:**
- Username tetap: `pa_tidung` (tidak berubah)
- Log: "dipindahkan dari Pengadilan Agama Tamamaung ke Pengadilan Agama Tidung (username tetap: pa_tidung)"

### Skenario 2: Username Perlu Berubah
**Data Existing:**
- Username: `pa_tamamaung`, Nama: Fizaky, Unit: Pengadilan Agama Toddopuli

**Import CSV:**
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Fizaky;200402212020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Tamamaung
```

**Expected Result:**
- Username berubah: `pa_tamamaung` → `pa_tamamaung` (tetap sama karena sudah sesuai)
- Log: "dipindahkan dari Pengadilan Agama Toddopuli ke Pengadilan Agama Tamamaung (username tetap: pa_tamamaung)"

### Skenario 3: Username Konflik
**Data Existing:**
- Username: `pa_pinrang`, Nama: Rijal, Unit: Pengadilan Agama Pinrang
- Username: `pa_sengkang`, Nama: Alif, Unit: Pengadilan Agama Sengkang

**Import CSV:**
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
```

**Expected Result:**
- Username berubah: `pa_pinrang` → `pa_sengkang1` (karena `pa_sengkang` sudah ada)
- Log: "dipindahkan dari Pengadilan Agama Pinrang ke Pengadilan Agama Sengkang (username: pa_pinrang → pa_sengkang1)"

## Keuntungan Perbaikan

### 1. **Username Tetap Konsisten**
- Jika username sudah sesuai dengan unit kerja baru, tidak akan berubah
- Menghindari perubahan yang tidak perlu

### 2. **Logika yang Lebih Cerdas**
- Sistem mengenali apakah username perlu diubah atau tidak
- Hanya mengubah username jika benar-benar diperlukan

### 3. **User Experience yang Lebih Baik**
- User tidak perlu mengingat username baru yang tidak perlu
- Login tetap menggunakan username yang familiar

### 4. **Pesan Log yang Informatif**
- Menunjukkan apakah username tetap atau berubah
- Memudahkan tracking perubahan

## Testing

### File Testing: `test_username_debug.csv`
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Alip Ba Ta;200406132019111000;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Tidung
Fizaky;200402212020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Tamamaung
Mamat;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Toddopuli
```

### Expected Results:
1. **Alip Ba Ta**: Username tetap `pa_tidung` (jika sudah sesuai)
2. **Fizaky**: Username tetap `pa_tamamaung` (jika sudah sesuai)
3. **Mamat**: Username tetap `pa_toddopuli` (jika sudah sesuai)

## Verifikasi

### 1. Cek Username Tidak Berubah Tidak Perlu
```sql
-- Cek apakah ada user yang username berubah padahal sudah sesuai
SELECT id, username, nama, unit_kerja 
FROM users 
WHERE username LIKE '%1' OR username LIKE '%2'
ORDER BY username;
```

### 2. Cek Log Import
- Pesan log menunjukkan "username tetap" jika tidak berubah
- Pesan log menunjukkan perubahan username jika berubah

## Kesimpulan

Perbaikan ini mengatasi masalah username yang berubah tidak perlu dengan:
- ✅ Pemisahan fungsi generate username dasar dan dengan suffix
- ✅ Logika cerdas untuk menentukan apakah username perlu diubah
- ✅ Username tetap konsisten jika sudah sesuai dengan unit kerja
- ✅ Pesan log yang lebih informatif
- ✅ User experience yang lebih baik

Sekarang saat perpindahan unit kerja, username akan tetap sama jika sudah sesuai dengan unit kerja baru! 