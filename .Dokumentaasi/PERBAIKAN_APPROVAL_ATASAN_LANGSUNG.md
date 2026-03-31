# Perbaikan Masalah Approval Level 1 untuk Kabag, Sekretaris, dan Ketua

## Masalah yang Diperbaiki
**Saat status cuti = "pending", pengajuan cuti harus di-approve oleh atasan langsung (approval level 1).** 
Ketika kabag (kepala bagian), sekretaris, atau ketua menjadi atasan langsung, pengajuan cuti pegawai mereka tidak muncul di halaman approval mereka, sehingga mereka tidak dapat melakukan approval level 1.

## Root Cause
Di `LeaveController::getHistory()` dan `Leave::getHistory()`, filter untuk kabag, sekretaris, dan ketua HANYA mencari pengajuan dengan status:
- Kabag: `status = 'pending_kabag' AND kabag_approver_id = kabag_id` (level 3 approval)
- Sekretaris: `status = 'pending_sekretaris' AND sekretaris_approver_id = sekretaris_id` (level 4 approval)
- Ketua: `status = 'awaiting_pimpinan' AND ketua_approver_id = ketua_id` (level 5 approval)

Padahal jika mereka juga atasan langsung, pengajuan akan memiliki:
- `status = 'pending'` (level 1)
- `atasan_id = kabag/sekretaris/ketua_id`

Filter yang ada TIDAK mencakup kasus ini, sehingga pengajuan tidak muncul.

## Solusi yang Diterapkan

### 1. Update `app/controllers/LeaveController.php` - method `getHistory()`
**Sebelum:**
```php
} else if ($atasanData['role'] === 'kabag') {
    // Kabag sees:
    // 1. Pending_kabag leaves where they are kabag approver
    $filters['kabag_approver_id'] = $atasanData['id_atasan'];
    $filters['is_kabag_viewer'] = true;
} else if ($atasanData['role'] === 'sekretaris') {
    // Sekretaris sees:
    // 1. Pending_sekretaris leaves where they are sekretaris approver
    // 2. Items dapat berasal dari Kabag (routed) atau langsung dari atasan
    $filters['sekretaris_approver_id'] = $atasanData['id_atasan'];
    $filters['is_sekretaris_viewer'] = true;
} else if ($atasanData['role'] === 'ketua') {
    // Ketua sees semua pengajuan yang berada di bawah tanggung jawabnya
    // baik sebagai atasan langsung maupun ketua approver. tidak dibatasi status
    $filters['ketua_approver_id'] = $atasanData['id_atasan'];
    $filters['is_ketua_viewer'] = true;
}
```

**Sesudah:**
```php
} else if ($atasanData['role'] === 'kabag') {
    // Kabag sees:
    // 1. Pending leaves where they are direct atasan (approval level 1)
    // 2. Pending_kabag leaves where they are kabag approver (level 3)
    $filters['atasan_id'] = $atasanData['id_atasan'];
    $filters['kabag_approver_id'] = $atasanData['id_atasan'];
    $filters['is_kabag_viewer'] = true;
} else if ($atasanData['role'] === 'sekretaris') {
    // Sekretaris sees:
    // 1. Pending leaves where they are direct atasan (approval level 1)
    // 2. Pending_sekretaris leaves where they are sekretaris approver (level 4)
    $filters['atasan_id'] = $atasanData['id_atasan'];
    $filters['sekretaris_approver_id'] = $atasanData['id_atasan'];
    $filters['is_sekretaris_viewer'] = true;
} else if ($atasanData['role'] === 'ketua') {
    // Ketua sees:
    // 1. Pending leaves where they are direct atasan (approval level 1)
    // 2. Awaiting_pimpinan leaves where they are ketua approver (level 5)
    $filters['atasan_id'] = $atasanData['id_atasan'];
    $filters['ketua_approver_id'] = $atasanData['id_atasan'];
    $filters['is_ketua_viewer'] = true;
}
```

**Perubahan:** Tambahkan `$filters['atasan_id']` untuk kabag, sekretaris, dan ketua agar mereka bisa melihat pengajuan level 1 ketika mereka adalah atasan langsung.

---

### 2. Update `app/models/Leave.php` - method `getHistory()`
**Sebelum:**
```php
// Handle Kabag viewer filter
if ($isKabagViewer && isset($filters['kabag_approver_id'])) {
    $conditions[] = "lr.kabag_approver_id = ? AND lr.status = 'pending_kabag'";
    $params[] = $filters['kabag_approver_id'];
}

// Handle Sekretaris viewer filter
if ($isSekretarisViewer && isset($filters['sekretaris_approver_id'])) {
    $conditions[] = "lr.sekretaris_approver_id = ? AND lr.status = 'pending_sekretaris' AND (lr.kabag_approval_date IS NOT NULL OR lr.kabag_approver_id IS NULL)";
    $params[] = $filters['sekretaris_approver_id'];
}

// Handle Ketua viewer filter
$isKetuaViewer = isset($filters['is_ketua_viewer']) && $filters['is_ketua_viewer'] === true;
if ($isKetuaViewer && isset($filters['ketua_approver_id'])) {
    $allowedStatuses = ['awaiting_pimpinan', 'approved', 'rejected', 'changed', 'postponed'];
    $statusPlaceholders = implode(',', array_fill(0, count($allowedStatuses), '?'));
    $conditions[] = "((lr.ketua_approver_id = ? OR lr.atasan_id = ?) AND lr.status IN ({$statusPlaceholders}))";
    $params[] = $filters['ketua_approver_id'];
    $params[] = $filters['ketua_approver_id'];
    $params = array_merge($params, $allowedStatuses);
}
```
**Sesudah:**
```php
// Handle Kabag viewer filter - dapat melihat approval level 1 (sebagai atasan langsung) dan level 3 (sebagai kabag approver)
if ($isKabagViewer && isset($filters['kabag_approver_id'])) {
    $conditions[] = "((lr.atasan_id = ? AND lr.status = 'pending') OR (lr.kabag_approver_id = ? AND lr.status = 'pending_kabag'))";
    $params[] = $filters['kabag_approver_id'];
    $params[] = $filters['kabag_approver_id'];
}

// Handle Sekretaris viewer filter - dapat melihat approval level 1 (sebagai atasan langsung) dan level 4 (sebagai sekretaris approver)
if ($isSekretarisViewer && isset($filters['sekretaris_approver_id'])) {
    $conditions[] = "((lr.atasan_id = ? AND lr.status = 'pending') OR (lr.sekretaris_approver_id = ? AND lr.status = 'pending_sekretaris' AND (lr.kabag_approval_date IS NOT NULL OR lr.kabag_approver_id IS NULL)))";
    $params[] = $filters['sekretaris_approver_id'];
    $params[] = $filters['sekretaris_approver_id'];
}

// Handle Ketua viewer filter - dapat melihat approval level 1 (sebagai atasan langsung) dan level 5 (sebagai ketua approver)
$isKetuaViewer = isset($filters['is_ketua_viewer']) && $filters['is_ketua_viewer'] === true;
if ($isKetuaViewer && isset($filters['ketua_approver_id'])) {
    $allowedStatuses = ['awaiting_pimpinan', 'approved', 'rejected', 'changed', 'postponed'];
    $statusPlaceholders = implode(',', array_fill(0, count($allowedStatuses), '?'));
    $conditions[] = "((lr.atasan_id = ? AND lr.status = 'pending') OR (lr.ketua_approver_id = ? AND lr.status IN ({$statusPlaceholders})))";
    $params[] = $filters['ketua_approver_id'];
    $params[] = $filters['ketua_approver_id'];
    $params = array_merge($params, $allowedStatuses);
}
```

### 2a. Additional logic for special viewers
Dalam blok awal yang menangani `atasan_id` umum, tambahkan pengecualian agar klausa
`lr.atasan_id = ?` tidak ditambahkan jika pengguna adalah kabag/sekretaris/ketua.
Hal ini mencegah kondisi AND ganda yang menimpa OR di atas dan memblokir
pengajuan berstatus *pending_kabag* / *pending_sekretaris* ketika atasan langsungnya
bukan mereka.

---

### Catatan Tambahan
- Bug ini muncul khusus ketika:
  1. atasan langsung = kabag
  2. kasubbag menyetujui
  3. kabag kembali melakukan approval level 3
  4. status berubah ke `pending_sekretaris`, sekretaris_approver_id terisi
  5. namun `Leave::getHistory` sebelumnya mem-filter dengan `atasan_id = sekre_id`
     sehingga baris tersebut hilang dari daftar sekretaris.
- Perbaikan memastikan tabel approval sekretaris sekarang menampilkan permohonan
  dengan status `pending_sekretaris` terlepas dari `atasan_id` asal.

**Sesudah:**
```php
// Handle Kabag viewer filter - dapat melihat approval level 1 (sebagai atasan langsung) dan level 3 (sebagai kabag approver)
if ($isKabagViewer && isset($filters['kabag_approver_id'])) {
    $conditions[] = "((lr.atasan_id = ? AND lr.status = 'pending') OR (lr.kabag_approver_id = ? AND lr.status = 'pending_kabag'))";
    $params[] = $filters['kabag_approver_id'];
    $params[] = $filters['kabag_approver_id'];
}

// Handle Sekretaris viewer filter - dapat melihat approval level 1 (sebagai atasan langsung) dan level 4 (sebagai sekretaris approver)
if ($isSekretarisViewer && isset($filters['sekretaris_approver_id'])) {
    $conditions[] = "((lr.atasan_id = ? AND lr.status = 'pending') OR (lr.sekretaris_approver_id = ? AND lr.status = 'pending_sekretaris' AND (lr.kabag_approval_date IS NOT NULL OR lr.kabag_approver_id IS NULL)))";
    $params[] = $filters['sekretaris_approver_id'];
    $params[] = $filters['sekretaris_approver_id'];
}

// Handle Ketua viewer filter - dapat melihat approval level 1 (sebagai atasan langsung) dan level 5 (sebagai ketua approver)
$isKetuaViewer = isset($filters['is_ketua_viewer']) && $filters['is_ketua_viewer'] === true;
if ($isKetuaViewer && isset($filters['ketua_approver_id'])) {
    $allowedStatuses = ['awaiting_pimpinan', 'approved', 'rejected', 'changed', 'postponed'];
    $statusPlaceholders = implode(',', array_fill(0, count($allowedStatuses), '?'));
    $conditions[] = "((lr.atasan_id = ? AND lr.status = 'pending') OR (lr.ketua_approver_id = ? AND lr.status IN ({$statusPlaceholders})))";
    $params[] = $filters['ketua_approver_id'];
    $params[] = $filters['ketua_approver_id'];
    $params = array_merge($params, $allowedStatuses);
}
```

**Perubahan:** Update query SQL untuk handle OR condition antara:
- Level 1 approval: `(lr.atasan_id = ??? AND lr.status = 'pending')`
- Level khusus approval: status pending_kabag/pending_sekretaris/awaiting_pimpinan

---

### 3. Update `app/views/approval/index.php` - JavaScript kondisi tombol aksi
**Perubahan:** Logika sudah benar di view - tidak perlu perubahan signifikan. Kondisi sudah mengecek `window.IS_ATASAN` untuk semua role dan menampilkan tombol aksi untuk status 'pending'.

---

### 4. Update `app/helpers/auth_helper.php` - Tambah helper functions
**Sebelum:**
```php
function isKasubbag() {
    if (!isAtasan()) {
        return false;
    }
    return isset($_SESSION['atasan_role']) && $_SESSION['atasan_role'] === 'kasubbag';
}

function isKetua() {
    if (!isAtasan()) {
        return false;
    }
    return isset($_SESSION['atasan_role']) && $_SESSION['atasan_role'] === 'ketua';
}
```

**Sesudah:**
```php
function isKasubbag() {
    if (!isAtasan()) {
        return false;
    }
    return isset($_SESSION['atasan_role']) && $_SESSION['atasan_role'] === 'kasubbag';
}

function isKabag() {
    if (!isAtasan()) {
        return false;
    }
    return isset($_SESSION['atasan_role']) && $_SESSION['atasan_role'] === 'kabag';
}

function isSekretaris() {
    if (!isAtasan()) {
        return false;
    }
    return isset($_SESSION['atasan_role']) && $_SESSION['atasan_role'] === 'sekretaris';
}

function isKetua() {
    if (!isAtasan()) {
        return false;
    }
    return isset($_SESSION['atasan_role']) && $_SESSION['atasan_role'] === 'ketua';
}
```

**Perubahan:** Tambahkan helper functions `isKabag()` dan `isSekretaris()` agar bisa digunakan di controller dan view.

---

## Flow Approval Level 1 Setelah Perbaikan

### Untuk Atasan Biasa (role = null/regular)
1. User mengajukan cuti → status = 'pending', atasan_id = atasan user
2. Atasan melihat pengajuan di approval list (status = 'pending')
3. Atasan approve → forward ke kasubbag atau langsung ke pimpinan

### Untuk Kabag (role = 'kabag') yang juga Atasan Langsung
1. User mengajukan cuti → status = 'pending', atasan_id = kabag_id
2. **Kabag melihat pengajuan di approval list** (status = 'pending' ✓ SEBELUMNYA TIDAK MUNCUL)
3. Kabag approve → forward ke kasubbag atau langsung ke pimpinan
4. Jika kemudian ada level 3 approval kabag, pengajuan ada di status 'pending_kabag'

### Untuk Sekretaris (role = 'sekretaris') yang juga Atasan Langsung
1. User mengajukan cuti → status = 'pending', atasan_id = sekretaris_id
2. **Sekretaris melihat pengajuan di approval list** (status = 'pending' ✓ SEBELUMNYA TIDAK MUNCUL)
3. Sekretaris approve → forward ke kasubbag atau langsung ke pimpinan
4. Jika kemudian ada level 4 approval sekretaris, pengajuan ada di status 'pending_sekretaris'

### Untuk Ketua (role = 'ketua') yang juga Atasan Langsung
1. User mengajukan cuti → status = 'pending', atasan_id = ketua_id
2. **Ketua melihat pengajuan di approval list** (status = 'pending' ✓ SEBELUMNYA TIDAK MUNCUL)
3. Ketua approve → forward ke kasubbag atau langsung ke pimpinan
4. Jika kemudian ada level 5 final approval ketua, pengajuan ada di status 'awaiting_pimpinan'

---

## Testing Checklist

- [ ] Buka halaman Approval sebagai Kabag yang juga atasan langsung → pastikan pengajuan dengan status 'pending' muncul
- [ ] Buka halaman Approval sebagai Sekretaris yang juga atasan langsung → pastikan pengajuan dengan status 'pending' muncul
- [ ] Buka halaman Approval sebagai Ketua yang juga atasan langsung → pastikan pengajuan dengan status 'pending' muncul
- [ ] Coba approve pengajuan sebagai kabag/sekretaris/ketua ketika mereka adalah atasan langsung → pastikan berhasil
- [ ] Verifikasi status dan catatan approval tersimpan dengan benar di database
- [ ] Pastikan notifikasi dikirim ke level approval berikutnya

---

## File yang Diubah
1. `app/controllers/LeaveController.php`
2. `app/models/Leave.php`
3. `app/views/approval/index.php`
4. `app/helpers/auth_helper.php`

## Tanggal Perbaikan
**8 Maret 2026**
