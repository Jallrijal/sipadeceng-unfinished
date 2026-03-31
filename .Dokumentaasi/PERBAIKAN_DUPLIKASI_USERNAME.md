# Perbaikan Masalah Duplikasi Username

## Masalah yang Ditemukan

Error yang muncul saat import CSV:
```
Baris 3: Error - Duplicate entry 'pa_toddopuli' for key 'username'
Baris 4: Error - Duplicate entry 'pa_tidung' for key 'username'
```

**Penyebab:** Fungsi `generateUsername()` tidak menangani duplikasi username yang sudah ada di database.

## Solusi yang Diterapkan

### 1. Perbaikan Fungsi `generateUsername()`

**Sebelum:**
```php
private function generateUsername($satker) {
    // Generate username dari satker
    $username = 'pa_' . $lokasi;
    return $username; // Bisa duplikasi!
}
```

**Sesudah:**
```php
private function generateUsername($satker) {
    // Generate username dari satker
    $username = 'pa_' . $lokasi;
    
    // Cek apakah username sudah ada, jika ya tambahkan suffix angka
    $baseUsername = $username;
    $counter = 1;
    while ($this->isUsernameExists($username)) {
        $username = $baseUsername . $counter;
        $counter++;
    }
    
    return $username;
}

private function isUsernameExists($username) {
    $sql = "SELECT COUNT(*) as count FROM users WHERE username = ?";
    $result = $this->db()->fetch($sql, [$username]);
    return $result['count'] > 0;
}
```

### 2. Perbaikan Logika Update Username

**Sebelum:**
```php
// Update username jika unit kerja berubah
if ($oldUnitKerja !== $satker) {
    $updateData['username'] = $newUsername;
}
```

**Sesudah:**
```php
// Update username jika unit kerja berubah DAN username berbeda
if ($oldUnitKerja !== $satker && $oldUsername !== $newUsername) {
    $updateData['username'] = $newUsername;
}
```

### 3. Perbaikan Pesan Log

**Sebelum:**
```php
$action = "dipindahkan dari {$oldUnitKerja} ke {$satker} (username: {$oldUsername} → {$newUsername})";
```

**Sesudah:**
```php
if ($oldUsername !== $newUsername) {
    $action = "dipindahkan dari {$oldUnitKerja} ke {$satker} (username: {$oldUsername} → {$newUsername})";
} else {
    $action = "dipindahkan dari {$oldUnitKerja} ke {$satker}";
}
```

## Skenario Testing

### Skenario 1: Username Sudah Ada
**Data Existing:**
- Username: `pa_toddopuli` sudah ada di database

**Import CSV:**
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Budi;200501152021081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Toddopuli
```

**Expected Result:**
- Username yang dibuat: `pa_toddopuli1`
- Tidak ada error duplikasi

### Skenario 2: Multiple Username Duplikasi
**Data Existing:**
- Username: `pa_tidung` sudah ada
- Username: `pa_tidung1` sudah ada

**Import CSV:**
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Sari;200502202021081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Tidung
```

**Expected Result:**
- Username yang dibuat: `pa_tidung2`
- Tidak ada error duplikasi

### Skenario 3: Perpindahan Unit Kerja
**Data Existing:**
- Username: `pa_pinrang`, Nama: Rijal, Unit: Pengadilan Agama Pinrang

**Import CSV:**
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
```

**Expected Result:**
- Username berubah: `pa_pinrang` → `pa_sengkang`
- Jika `pa_sengkang` sudah ada: `pa_sengkang1`

## Keuntungan Perbaikan

### 1. **Menghindari Error Duplikasi**
- Sistem otomatis menambahkan suffix angka
- Tidak ada lagi error "Duplicate entry for key 'username'"

### 2. **Username Tetap Unik**
- Setiap user memiliki username yang unik
- Tidak ada konflik saat login

### 3. **Logika yang Lebih Cerdas**
- Username hanya diupdate jika benar-benar berbeda
- Pesan log yang lebih akurat

### 4. **Backward Compatibility**
- Tidak mengubah data existing
- Tetap mendukung semua skenario import

## Testing

### File Testing: `test_import_fix.csv`
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
Alif;200406222020091001;Ketua Pengadilan Agama;IV/d;Pengadilan Agama Maros
Fikri;200402212020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Pinrang
Budi;200501152021081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Toddopuli
Sari;200502202021081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Tidung
```

### Expected Results:
1. **Rijal**: Update data existing, username: `pa_pinrang` → `pa_sengkang`
2. **Alif**: Update data existing, username: `pa_sengkang` → `pa_maros`
3. **Fikri**: Update data existing, username: `pa_maros` → `pa_pinrang`
4. **Budi**: User baru, username: `pa_toddopuli1` (jika `pa_toddopuli` sudah ada)
5. **Sari**: User baru, username: `pa_tidung1` (jika `pa_tidung` sudah ada)

## Verifikasi

### 1. Cek Username Unik
```sql
SELECT username, COUNT(*) as count 
FROM users 
GROUP BY username 
HAVING count > 1;
```
**Expected:** Tidak ada hasil (semua username unik)

### 2. Cek Data Import
```sql
SELECT username, nama, nip, unit_kerja 
FROM users 
WHERE nip IN ('200305282020081001', '200406222020091001', '200402212020081001', '200501152021081001', '200502202021081001')
ORDER BY nama;
```

### 3. Cek Log Import
- Tidak ada error duplikasi username
- Pesan sukses untuk semua baris
- Log perpindahan unit kerja tercatat dengan benar

## Kesimpulan

Perbaikan ini mengatasi masalah duplikasi username dengan:
- ✅ Menambahkan pengecekan duplikasi di `generateUsername()`
- ✅ Otomatis menambahkan suffix angka jika username sudah ada
- ✅ Memperbaiki logika update username
- ✅ Menghasilkan pesan log yang lebih akurat
- ✅ Mempertahankan backward compatibility

Sekarang import CSV akan berhasil tanpa error duplikasi username! 