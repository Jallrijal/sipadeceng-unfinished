# Testing Checklist: Placeholder Admin untuk Ketua Approval

**Tanggal Test**: [Tanggal Tes]
**Tested By**: [Nama Tester]

## Pre-requisites
- [ ] Setup database dengan tabel atasan yang memiliki role='ketua'
- [ ] Setup user dengan user_type='atasan' dan NIP yang sesuai dengan data atasan
- [ ] Setup signature untuk ketua di tabel user_signatures
- [ ] Setup template blanko_cuti_template.docx dengan placeholder `${jabatan_admin}`, `${ttd_admin}`, `${nama_admin}`, `${nip_admin}`

## Test Case 1: Pengajuan Cuti Baru (Draft)

### Setup
- [ ] Login sebagai user biasa
- [ ] Siapkan data pengajuan cuti

### Execution
- [ ] Click "Ajukan Cuti"
- [ ] Isi form pengajuan cuti
- [ ] Click "Buat Draft"
- [ ] Download dokumen draft

### Expected Result
- [ ] Dokumen draft berhasil di-generate
- [ ] Placeholder `${jabatan_admin}` kosong
- [ ] Placeholder `${ttd_admin}` kosong
- [ ] Placeholder `${nama_admin}` kosong
- [ ] Placeholder `${nip_admin}` kosong
- [ ] Kolom VII (atasan) terisi dengan data atasan langsung atau kosong (sesuai atasan_id)

## Test Case 2: Approval oleh Atasan Level 1 (pending -> pending_kasubbag)

### Setup
- [ ] Ada pengajuan cuti dengan status draft
- [ ] User atasan sudah ter-setup di system
- [ ] Atasan tersebut memiliki role='kasubbag' atau role=null (regular atasan)

### Execution
- [ ] Login sebagai atasan
- [ ] Lihat detail pengajuan cuti
- [ ] Click "Approve" / "Terima"
- [ ] Confirm approval
- [ ] Download dokumen yang di-generate

### Expected Result
- [ ] Status berubah ke pending_kasubbag atau awaiting_pimpinan (sesuai routing)
- [ ] Dokumen ter-generate dengan placeholder admin kosong
- [ ] Kolom VII terisi dengan data atasan

## Test Case 3: Approval oleh Kasubbag (pending_kasubbag -> pending_kabag)

### Setup
- [ ] Ada pengajuan cuti dengan status pending_kasubbag
- [ ] Kasubbag sudah ter-setup

### Execution
- [ ] Login sebagai kasubbag
- [ ] Forward ke Kabag
- [ ] Confirm
- [ ] Download dokumen yang di-generate

### Expected Result
- [ ] Status berubah ke pending_kabag
- [ ] Placeholder p1 (paraf kasubbag) terisi dengan image paraf kasubbag (jika ada)
- [ ] Placeholder admin tetap kosong

## Test Case 4: Approval oleh Kabag (pending_kabag -> pending_sekretaris)

### Setup
- [ ] Ada pengajuan cuti dengan status pending_kabag
- [ ] Kabag sudah ter-setup

### Execution
- [ ] Login sebagai kabag
- [ ] Forward ke Sekretaris
- [ ] Confirm
- [ ] Download dokumen yang di-generate

### Expected Result
- [ ] Status berubah ke pending_sekretaris
- [ ] Placeholder p2 (paraf kabag) terisi dengan image paraf kabag (jika ada)
- [ ] Placeholder admin tetap kosong

## Test Case 5: Approval oleh Sekretaris (pending_sekretaris -> awaiting_pimpinan)

### Setup
- [ ] Ada pengajuan cuti dengan status pending_sekretaris
- [ ] Sekretaris sudah ter-setup
- [ ] Ada beberapa ketua yang bisa dipilih

### Execution
- [ ] Login sebagai sekretaris
- [ ] Forward ke Pimpinan (pilih salah satu ketua)
- [ ] Confirm
- [ ] Download dokumen yang di-generate (generated doc sebelum approval final)

### Expected Result
- [ ] Status berubah ke awaiting_pimpinan
- [ ] ketua_approver_id tersimpan di database
- [ ] Placeholder p3 (paraf sekretaris) terisi dengan image paraf sekretaris (jika ada)
- [ ] Placeholder admin tetap kosong (karena status belum approved)

## Test Case 6: Approval Final oleh Ketua (awaiting_pimpinan -> approved) ⭐ CRITICAL

### Setup
- [ ] Ada pengajuan cuti dengan status awaiting_pimpinan
- [ ] ketua_approver_id sudah ter-set ke id ketua yang ter-pilih
- [ ] Ketua memiliki signature di tabel user_signatures dengan signature_type='user'
- [ ] Data ketua ada di tabel atasan

### Execution
- [ ] Login sebagai ketua (atasan dengan role='ketua')
- [ ] Lihat detail pengajuan cuti yang ditujukan ke dia
- [ ] Click "Approve" / "Terima"
- [ ] Confirm approval
- [ ] **PENTING**: Download dokumen final (generated doc setelah approval)

### Expected Result - **MAIN VERIFICATION POINT**
- [ ] Status berubah ke approved
- [ ] Dokumen final di-generate dengan placeholder admin **TERISI DATA KETUA**:
  - [ ] `${jabatan_admin}` = jabatan ketua (dari tabel atasan)
  - [ ] `${ttd_admin}` = image signature ketua (dari user_signatures) atau kosong jika tidak ada
  - [ ] `${nama_admin}` = nama_atasan ketua (dari tabel atasan)
  - [ ] `${nip_admin}` = NIP ketua dengan format "NIP. xxxxxxxx" (dari tabel atasan)
- [ ] Kolom VII (atasan) juga terisi dengan data ketua (khusus untuk user dengan atasan_id=1)
- [ ] Notification dikirim ke user

## Test Case 7: Approval Final Rejection (awaiting_pimpinan -> rejected)

### Setup
- [ ] Ada pengajuan cuti dengan status awaiting_pimpinan
- [ ] Ketua ter-setup

### Execution
- [ ] Login sebagai ketua
- [ ] Click "Reject" / "Tolak"
- [ ] Isi catatan penolakan
- [ ] Confirm
- [ ] Download dokumen yang di-generate

### Expected Result
- [ ] Status berubah ke rejected
- [ ] Placeholder admin **TERISI DATA KETUA** (sama seperti approval)
- [ ] Checkbox `${cek_ditolak}` sudah ter-check

## Test Case 8: Approval Final Change (awaiting_pimpinan -> changed)

### Setup
- [ ] Ada pengajuan cuti dengan status awaiting_pimpinan

### Execution
- [ ] Login sebagai ketua
- [ ] Click "Ubah" / "Diminta Perubahan"
- [ ] Isi catatan perubahan yang dibutuhkan
- [ ] Confirm
- [ ] Download dokumen

### Expected Result
- [ ] Status berubah ke changed
- [ ] Placeholder admin **TERISI DATA KETUA**
- [ ] Checkbox `${cek_perubahan}` sudah ter-check

## Test Case 9: Approval Final Postpone (awaiting_pimpinan -> postponed)

### Setup
- [ ] Ada pengajuan cuti dengan status awaiting_pimpinan

### Execution
- [ ] Login sebagai ketua
- [ ] Click "Tunda" / "Ditangguhkan"
- [ ] Isi catatan penangguhan dan jumlah hari yang ditangguhkan
- [ ] Confirm
- [ ] Download dokumen

### Expected Result
- [ ] Status berubah ke postponed
- [ ] Placeholder admin **TERISI DATA KETUA**
- [ ] Checkbox `${cek_ditangguhkan}` sudah ter-check
- [ ] jumlah_hari_ditangguhkan tersimpan

## Test Case 10: User dengan atasan_id = 1 (Atasan Khusus)

### Setup
- [ ] Ada user dengan atasan_id = 1
- [ ] Pengajuan cuti dari user ini sedang dalam proses approval

### Execution
- [ ] Setup approval chain sampai level final approval oleh ketua
- [ ] Ketua melakukan approval
- [ ] Download dokumen final

### Expected Result
- [ ] Kolom VII (nama_atasan, nip_atasan, jabatan_atasan) **TERISI DATA KETUA** (karena ketua_approver_id ada dan status appropriate)
- [ ] Placeholder admin juga terisi data ketua
- [ ] Kedua area placeholder menunjukkan data ketua yang sama

## Test Case 11: Ketua Tanpa Signature

### Setup
- [ ] Ketua tidak memiliki record di user_signatures dengan signature_type='user'

### Execution
- [ ] Lakukan approval final
- [ ] Download dokumen

### Expected Result
- [ ] `${ttd_admin}` kosong (tidak ada image)
- [ ] `${jabatan_admin}` = jabatan ketua ✓
- [ ] `${nama_admin}` = nama_atasan ketua ✓
- [ ] `${nip_admin}` = NIP ketua ✓
- [ ] Dokumen tetap valid dan readable

## Test Case 12: Error Handling - Ketua Data Tidak Ditemukan

### Setup
- [ ] ketua_approver_id tunjuk ke id yang tidak ada di tabel atasan

### Execution
- [ ] Lakukan approval final
- [ ] Check error log
- [ ] Download dokumen

### Expected Result
- [ ] Error ter-log di error_log
- [ ] Placeholder admin diisi dengan garis kosong `________________________`
- [ ] Dokumen tetap ter-generate (tidak error)

## Test Case 13: Error Handling - Ketua User Tidak Ditemukan

### Setup
- [ ] Data ketua ada di tabel atasan
- [ ] Tapi user dengan NIP ketua tidak ada atau statusnya deleted

### Execution
- [ ] Lakukan approval final
- [ ] Check error log
- [ ] Download dokumen

### Expected Result
- [ ] Error ter-log di error_log
- [ ] Placeholder tetap diisi dengan data dari tabel atasan (fallback)
- [ ] Dokumen tetap valid

## Test Case 14: Database Check - ketua_approver_id

### Execution
```sql
SELECT id, status, ketua_approver_id 
FROM leave_requests 
WHERE ketua_approver_id IS NOT NULL 
LIMIT 5;
```

### Expected Result
- [ ] ketua_approver_id berisi id_atasan dari ketua
- [ ] Baris dengan status = 'awaiting_pimpinan' memiliki ketua_approver_id (sebelum approval)
- [ ] Baris dengan status = 'approved/rejected/changed/postponed' memiliki ketua_approver_id (setelah approval)

## Test Case 15: Backward Compatibility - Legacy Admin Approver

### Setup
- [ ] Ada metadata atau test data dengan admin_approver_id (legacy)
- [ ] Test jika ada fallback ke admin_approver masih berjalan

### Execution
- [ ] Check hasil approval yang menggunakan old flow (jika masih ada)

### Expected Result
- [ ] Jika tidak ada ketua_approver_id, sistem fallback ke admin_approver (jika ada)
- [ ] Legacy flow tetap berjalan

## Summary

**Total Test Cases**: 15
**Passed**: ___/15
**Failed**: ___/15

### Critical Test Cases (MUST PASS)
- [ ] Test Case 6: Approval Final oleh Ketua (placeholder admin terisi data ketua)
- [ ] Test Case 10: User dengan atasan_id=1 (kolom VII terisi data ketua)

### Notes
```
[Tuliskan catatan tambahan tentang hasil testing]


```

