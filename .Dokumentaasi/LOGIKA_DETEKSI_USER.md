# Logika Deteksi User - Fitur Import CSV

## Overview
Sistem import CSV menggunakan logika deteksi user yang cerdas untuk menghindari duplikasi dan memastikan data yang konsisten.

## Alur Logika Deteksi

### 1. Cek NIP Existing
```php
$existingUserByNIP = $this->db()->fetch("SELECT id, username, unit_kerja, jabatan FROM users WHERE nip = ?", [$nip]);
```

**Jika NIP ditemukan:**
- ✅ **UPDATE**: Data user existing akan diupdate
- Semua field (nama, jabatan, golongan, unit_kerja) akan diupdate
- **Username akan diupdate** sesuai unit kerja baru
- Tanggal masuk akan diupdate berdasarkan digit 9-14 NIP
- Password tetap tidak berubah

### 2. Buat User Baru
**Jika NIP tidak ada:**
- ✅ **CREATE**: User baru akan dibuat
- Username di-generate dari unit kerja
- Password default: "password"
- Kuota cuti default akan dibuat

## Contoh Skenario

### Skenario 1: NIP Sudah Ada
**Data Existing:**
- NIP: 200305282020081001
- Nama: Rijal Lama
- Unit Kerja: Pengadilan Agama Pinrang
- Jabatan: Ketua Pengadilan Agama

**Import CSV:**
- NIP: 200305282020081001
- Nama: Rijal Baru
- Unit Kerja: Pengadilan Agama Sengkang
- Jabatan: Ketua Pengadilan Agama

**Hasil:** ✅ UPDATE - Data Rijal diupdate dan dipindahkan ke Sengkang

### Skenario 2: User Baru
**Data Existing:**
- Tidak ada user dengan NIP yang sama

**Import CSV:**
- NIP: 200406222020091001 (NIP baru)
- Nama: Alif Baru
- Unit Kerja: Pengadilan Agama Sengkang
- Jabatan: Ketua Pengadilan Agama

**Hasil:** ✅ CREATE - User baru Alif dibuat dengan username pa_sengkang

### Skenario 3: Perpindahan Unit Kerja dengan Username Update
**Data Existing:**
- Username: pa_pinrang, Nama: Rijal, NIP: 200305282020081001
- Unit Kerja: Pengadilan Agama Pinrang

**Import CSV:**
- Nama: Rijal, NIP: 200305282020081001
- Unit Kerja: Pengadilan Agama Sengkang

**Hasil:** ✅ UPDATE - Rijal dipindahkan ke Sengkang, username: pa_pinrang → pa_sengkang

## Keuntungan Logika Ini

### 1. **Menghindari Duplikasi**
- Tidak ada user dengan NIP yang sama

### 2. **Fleksibilitas**
- Bisa mengupdate data existing berdasarkan NIP
- Bisa membuat user baru jika NIP belum ada

### 3. **Konsistensi Data**
- Username diupdate sesuai unit kerja baru
- Password tidak berubah saat update
- Kuota cuti tetap terjaga

### 4. **Audit Trail**
- Log mencatat setiap perubahan
- Bisa melacak perpindahan unit kerja
- Bisa melacak update data

## Implementasi Kode

### Fungsi processCSVRow()
```php
// 1. Cek NIP existing
$existingUserByNIP = $this->db()->fetch("SELECT id, username, unit_kerja, jabatan FROM users WHERE nip = ?", [$nip]);

if ($existingUserByNIP) {
    // Update data existing
    return $this->updateExistingUser($existingUserByNIP, $nama, $nip, $jabatan, $golongan, $satker, $rowNumber);
} else {
    // 2. Buat user baru
    return $this->createNewUser($nama, $nip, $jabatan, $golongan, $satker, $sisa_kuota, $rowNumber);
}
```

## Testing

### Test Case 1: Update NIP Existing
```csv
NAMA;NIP;JABATAN;GOL;SATKER
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
```
**Expected:** Update data Rijal yang sudah ada

### Test Case 2: Create New User
```csv
NAMA;NIP;JABATAN;GOL;SATKER
Alif;200406222020091001;Ketua Pengadilan Agama;IV/d;Pengadilan Agama Sengkang
```
**Expected:** Buat user baru Alif dengan username pa_sengkang

### Test Case 3: Update Username on Unit Transfer
```csv
NAMA;NIP;JABATAN;GOL;SATKER
Rijal;200305282020081001;Ketua Pengadilan Agama;IV/c;Pengadilan Agama Sengkang
```
**Expected:** Update data Rijal, username berubah dari pa_pinrang ke pa_sengkang

## Catatan Penting

1. **Username Generation**: Username di-generate dari unit kerja dan diupdate saat perpindahan
2. **Password**: Tidak berubah saat update data
3. **Kuota Cuti**: Tetap terjaga saat perpindahan unit kerja
4. **Logging**: Setiap perubahan dicatat dalam log
5. **Validation**: NIP dan data wajib tetap divalidasi 