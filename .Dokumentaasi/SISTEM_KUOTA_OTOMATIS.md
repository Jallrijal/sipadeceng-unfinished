# Sistem Kuota Otomatis untuk User Baru

## Ringkasan
Sistem sudah dirancang untuk **otomatis menggunakan nilai `max_days` dari tabel `leave_types`** ketika membuat kuota cuti untuk user baru, baik yang ditambahkan secara manual maupun melalui import CSV.

## Cara Kerja Sistem

### 1. Fungsi Utama: `getKuotaFromLeaveType($leaveTypeId)`
```php
function getKuotaFromLeaveType($leaveTypeId) {
    $db = Database::getInstance();
    $sql = "SELECT max_days FROM leave_types WHERE id = ?";
    $result = $db->fetch($sql, [$leaveTypeId]);
    return $result ? $result['max_days'] : 0;
}
```

**Fungsi ini:**
- Mengambil nilai `max_days` dari tabel `leave_types` berdasarkan `leave_type_id`
- Untuk cuti alasan penting (`id=5`), akan mengambil nilai `max_days` yang sudah diupdate menjadi 30

### 2. Fungsi Pembuat Kuota: `createInitialKuotaByType($userId, $leaveTypeId)`
```php
function createInitialKuotaByType($userId, $leaveTypeId) {
    $db = Database::getInstance();
    $kuota = getKuotaFromLeaveType($leaveTypeId); // Mengambil dari max_days
    if ($kuota <= 0) return false;
    $tahun = date('Y');
    
    switch ($leaveTypeId) {
        case 5: // Cuti Alasan Penting
            $cek = $db->fetch("SELECT id FROM kuota_cuti_alasan_penting WHERE user_id = ? AND tahun = ?", [$userId, $tahun]);
            if (!$cek) {
                $sql = "INSERT INTO kuota_cuti_alasan_penting (user_id, leave_type_id, tahun, kuota_tahunan) VALUES (?, ?, ?, ?)";
                return $db->execute($sql, [$userId, $leaveTypeId, $tahun, $kuota]);
            }
            return true;
        // ... kasus lainnya
    }
}
```

### 3. Fungsi Pembuat Semua Kuota: `createAllInitialQuota($userId)`
```php
function createAllInitialQuota($userId) {
    createInitialKuotaByType($userId, 2); // Cuti Besar
    createInitialKuotaByType($userId, 3); // Cuti Sakit
    createInitialKuotaByType($userId, 4); // Cuti Melahirkan
    createInitialKuotaByType($userId, 5); // Cuti Alasan Penting ← Menggunakan max_days=30
    createInitialKuotaByType($userId, 6); // Cuti Luar Tanggungan
}
```

## Kapan Fungsi Ini Dipanggil

### 1. Penambahan User Manual
**File:** `app/controllers/UserController.php`
```php
public function save() {
    // ... validasi dan pembuatan user
    
    if ($action == 'add') {
        $userId = $this->userModel->create($data);
        
        // Jika user adalah user biasa, buat kuota cuti awal
        if ($data['user_type'] == 'user' && $userId) {
            $this->createInitialQuota($userId); // ← Memanggil createAllInitialQuota
        }
    }
}
```

### 2. Import CSV
**File:** `app/controllers/UserController.php`
```php
private function processCSVRow($data, $headerMapping, $rowNumber) {
    // ... validasi dan pembuatan user
    
    $userId = $this->userModel->create([
        'username' => $username,
        'password' => $hashedPassword,
        'nama' => $nama,
        'nip' => $nip,
        'jabatan' => $jabatan,
        'golongan' => $golongan,
        'unit_kerja' => $satker,
        'user_type' => 'user',
        'tanggal_masuk' => $tanggalMasuk
    ]);
    
    if ($userId) {
        $this->createInitialQuota($userId); // ← Memanggil createAllInitialQuota
        // ...
    }
}
```

## Alur Kerja Lengkap

1. **User baru ditambahkan** (manual atau CSV)
2. **`createInitialQuota($userId)` dipanggil**
3. **`createAllInitialQuota($userId)` dipanggil**
4. **`createInitialKuotaByType($userId, 5)` dipanggil** untuk cuti alasan penting
5. **`getKuotaFromLeaveType(5)` dipanggil** untuk mengambil `max_days` dari database
6. **Query:** `SELECT max_days FROM leave_types WHERE id = 5`
7. **Hasil:** `max_days = 30` (setelah diupdate)
8. **Insert ke database:** `kuota_tahunan = 30`

## Verifikasi

### 1. Cek Database
```sql
-- Cek nilai max_days untuk cuti alasan penting
SELECT id, nama_cuti, max_days FROM leave_types WHERE id = 5;

-- Cek kuota user baru
SELECT user_id, tahun, kuota_tahunan 
FROM kuota_cuti_alasan_penting 
WHERE user_id = [USER_ID_BARU];
```

### 2. Test Penambahan User Baru
1. Tambah user baru secara manual
2. Import user baru melalui CSV
3. Cek tabel `kuota_cuti_alasan_penting`
4. Pastikan `kuota_tahunan = 30`

## Keuntungan Sistem Ini

✅ **Otomatis**: Tidak perlu mengubah kode setiap kali ada perubahan kuota  
✅ **Konsisten**: Semua user baru akan menggunakan nilai yang sama  
✅ **Fleksibel**: Mudah diubah hanya dengan mengupdate tabel `leave_types`  
✅ **Terpusat**: Satu sumber kebenaran untuk kuota cuti  

## Status
✅ **SUDAH BERFUNGSI** - Sistem sudah otomatis menggunakan `max_days` dari database untuk user baru. 