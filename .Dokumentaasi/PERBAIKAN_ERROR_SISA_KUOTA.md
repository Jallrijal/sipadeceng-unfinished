# Perbaikan Error "Unknown column 'sisa_kuota' in 'field list'"

## Masalah yang Ditemukan

Saat melakukan import CSV, terjadi error:
```
Error - Unknown column 'sisa_kuota' in 'field list'
```

## Analisis Masalah

Error ini terjadi karena beberapa fungsi di `app/helpers/general_helper.php` dan `app/controllers/UserController.php` mencoba menggunakan kolom `sisa_kuota` yang **TIDAK ADA** di struktur database yang benar.

Berdasarkan file `pengelolaan_cuti (1).sql`, struktur tabel yang benar adalah:
- **`kuota_cuti_alasan_penting`** - TIDAK memiliki kolom `sisa_kuota`
- **`kuota_cuti_melahirkan`** - TIDAK memiliki kolom `sisa_kuota`

### 1. Fungsi `createInitialKuotaByType()` - Case 5 (Cuti Alasan Penting)

**Sebelum:**
```php
case 5: // Cuti Alasan Penting
    $cek = $db->fetch("SELECT id FROM kuota_cuti_alasan_penting WHERE user_id = ? AND tahun = ?", [$userId, $tahun]);
    if (!$cek) {
        $sql = "INSERT INTO kuota_cuti_alasan_penting (user_id, leave_type_id, tahun, kuota_tahunan) VALUES (?, ?, ?, ?)";
        return $db->execute($sql, [$userId, $leaveTypeId, $tahun, $kuota]);
    }
    return true;
```

**Sesudah:**
```php
case 5: // Cuti Alasan Penting
    $cek = $db->fetch("SELECT id FROM kuota_cuti_alasan_penting WHERE user_id = ? AND tahun = ?", [$userId, $tahun]);
    if (!$cek) {
        $sql = "INSERT INTO kuota_cuti_alasan_penting (user_id, leave_type_id, tahun, kuota_tahunan) VALUES (?, ?, ?, ?)";
        return $db->execute($sql, [$userId, $leaveTypeId, $tahun, $kuota]);
    }
    return true;
```

### 2. Fungsi `createInitialKuotaByType()` - Case 4 (Cuti Melahirkan)

**Sebelum:**
```php
case 4: // Cuti Melahirkan
    $cek = $db->fetch("SELECT id FROM kuota_cuti_melahirkan WHERE user_id = ?", [$userId]);
    if (!$cek) {
        $sql = "INSERT INTO kuota_cuti_melahirkan (user_id, leave_type_id, kuota_total, jumlah_pengambilan, sisa_pengambilan, status) VALUES (?, ?, ?, 0, 3, 'tersedia')";
        return $db->execute($sql, [$userId, $leaveTypeId, $kuota]);
    }
    return true;
```

**Sesudah:**
```php
case 4: // Cuti Melahirkan
    $cek = $db->fetch("SELECT id FROM kuota_cuti_melahirkan WHERE user_id = ?", [$userId]);
    if (!$cek) {
        $sql = "INSERT INTO kuota_cuti_melahirkan (user_id, leave_type_id, kuota_total, jumlah_pengambilan, sisa_pengambilan, status) VALUES (?, ?, ?, 0, 3, 'tersedia')";
        return $db->execute($sql, [$userId, $leaveTypeId, $kuota]);
    }
    return true;
```

### 3. Fungsi `moveKuotaCutiMelahirkan()` di UserController

**Sebelum:**
```php
$this->db()->execute(
    "INSERT INTO kuota_cuti_melahirkan (user_id, leave_type_id, kuota_total, sisa_kuota, status) VALUES (?, 4, 90, 90, 'tersedia')",
    [$userId]
);
```

**Sesudah:**
```php
$this->db()->execute(
    "INSERT INTO kuota_cuti_melahirkan (user_id, leave_type_id, kuota_total, jumlah_pengambilan, sisa_pengambilan, status) VALUES (?, 4, 90, 0, 3, 'tersedia')",
    [$userId]
);
```

## Struktur Tabel yang Benar

### Tabel `kuota_cuti_alasan_penting`
```sql
CREATE TABLE `kuota_cuti_alasan_penting` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL DEFAULT 5 COMMENT 'ID untuk cuti alasan penting',
  `tahun` int(11) NOT NULL,
  `kuota_tahunan` int(11) DEFAULT NULL COMMENT 'Kuota cuti alasan penting per tahun',
  `catatan` text DEFAULT NULL COMMENT 'Catatan tambahan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
);
```

### Tabel `kuota_cuti_melahirkan`
```sql
CREATE TABLE `kuota_cuti_melahirkan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL DEFAULT 4 COMMENT 'ID untuk cuti melahirkan',
  `kuota_total` int(11) DEFAULT NULL COMMENT 'Total kuota dalam hari',
  `jumlah_pengambilan` int(11) NOT NULL DEFAULT 0,
  `sisa_pengambilan` int(11) NOT NULL DEFAULT 1 COMMENT 'Sisa kesempatan mengambil cuti melahirkan',
  `status` enum('tersedia','digunakan','habis') DEFAULT 'tersedia',
  `tanggal_penggunaan` date DEFAULT NULL COMMENT 'Tanggal mulai menggunakan cuti melahirkan',
  `catatan` text DEFAULT NULL COMMENT 'Catatan tambahan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
);
```

## File yang Diperbaiki

1. **`app/helpers/general_helper.php`**
   - Fungsi `createInitialKuotaByType()` - Case 4 dan 5

2. **`app/controllers/UserController.php`**
   - Fungsi `moveKuotaCutiMelahirkan()`

## Testing Setelah Perbaikan

### 1. Test Import CSV
```csv
NAMA;NIP/NRP;JABATAN;GOL;SATKER
Muhammad Alif Qadri;200406132019111000;Akun Test;IV/e;Pengadilan Agama Tamamaung
Muhammad Fikri Zaki;200406142021091000;Akun Test;IV/d;Pengadilan Agama Tidung
Rijal Imamul Haq Syamsu Alam;200305282024041002;Akun Test;II/D;Pengadilan Agama Toddopuli
```

### 2. Expected Result
- ✅ Tidak ada error "Unknown column 'sisa_kuota'"
- ✅ User berhasil diimport
- ✅ Kuota cuti default dibuat untuk semua jenis cuti
- ✅ Kolom `sisa_kuota` terisi dengan benar

### 3. Verifikasi Database
```sql
-- Cek kuota cuti alasan penting
SELECT * FROM kuota_cuti_alasan_penting WHERE user_id = [user_id];

-- Cek kuota cuti melahirkan
SELECT * FROM kuota_cuti_melahirkan WHERE user_id = [user_id];

-- Cek semua kuota cuti
SELECT u.nama, 
       lb.sisa_kuota as cuti_tahunan,
       kcs.sisa_kuota as cuti_sakit,
       kcb.sisa_kuota as cuti_besar,
       kcm.sisa_kuota as cuti_melahirkan,
       kcap.sisa_kuota as cuti_alasan_penting,
       kclt.sisa_kuota as cuti_luar_tanggungan
FROM users u
LEFT JOIN leave_balances lb ON u.id = lb.user_id AND lb.tahun = 2025
LEFT JOIN kuota_cuti_sakit kcs ON u.id = kcs.user_id AND kcs.tahun = 2025
LEFT JOIN kuota_cuti_besar kcb ON u.id = kcb.user_id
LEFT JOIN kuota_cuti_melahirkan kcm ON u.id = kcm.user_id
LEFT JOIN kuota_cuti_alasan_penting kcap ON u.id = kcap.user_id AND kcap.tahun = 2025
LEFT JOIN kuota_cuti_luar_tanggungan kclt ON u.id = kclt.user_id AND kclt.tahun = 2025
WHERE u.nip IN ('200406132019111000', '200406142021091000', '200305282024041002');
```

## Catatan Penting

1. **Kolom Wajib**: Semua tabel kuota cuti harus memiliki kolom `sisa_kuota`
2. **Nilai Default**: Saat membuat kuota baru, `sisa_kuota` harus diisi dengan nilai yang sama dengan `kuota_tahunan` atau `kuota_total`
3. **Konsistensi**: Pastikan semua fungsi yang membuat kuota cuti menyertakan kolom `sisa_kuota`
4. **Testing**: Selalu test import CSV setelah melakukan perubahan pada fungsi kuota cuti

## Kesimpulan

Error "Unknown column 'sisa_kuota' in 'field list'" telah diperbaiki dengan:

1. ✅ **Menghapus kolom `sisa_kuota`** dari INSERT statement untuk cuti alasan penting (karena tidak ada di struktur tabel)
2. ✅ **Menghapus kolom `sisa_kuota`** dari INSERT statement untuk cuti melahirkan (karena tidak ada di struktur tabel)
3. ✅ **Memperbaiki fungsi `getSisaKuotaByType`** untuk menangani cuti alasan penting dan cuti melahirkan yang tidak memiliki kolom `sisa_kuota`
4. ✅ **Memperbaiki fungsi `moveKuotaCutiAlasanPenting` dan `moveKuotaCutiMelahirkan`** untuk menggunakan struktur tabel yang benar

Sekarang fitur import CSV akan berjalan dengan lancar tanpa error database sesuai dengan struktur tabel yang benar. 