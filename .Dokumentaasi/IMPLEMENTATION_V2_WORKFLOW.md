# IMPLEMENTASI WORKFLOW APPROVAL V2 - PANDUAN LENGKAP
**Tanggal:** 22 Februari 2026  
**Status:** Siap Implementasi  
**Database:** sipadeceng (MySQL/MariaDB)  

---

## 📋 DAFTAR ISI

1. [Ringkasan Perubahan](#1-ringkasan-perubahan)
2. [Alur Workflow V2](#2-alur-workflow-v2)
3. [Database Migration](#3-database-migration)
4. [File-File Baru & Modifikasi](#4-file-file-baru--modifikasi)
5. [Implementasi Step-by-Step](#5-implementasi-step-by-step)
6. [Contoh Skenario Workflow](#6-contoh-skenario-workflow)
7. [Panduan Testing](#7-panduan-testing)
8. [Troubleshooting](#8-troubleshooting)

---

## 1. RINGKASAN PERUBAHAN

### Workflow Lama (2 Step)
```
Pegawai → Atasan Langsung → Pimpinan (Final) → Admin Blanko
```

### Workflow Baru (2-5 Step, Dinamis dengan Routing)
```
Pegawai → Atasan Langsung → Kasubbag → [Via Kabag OR Direct → Sekretaris] → Ketua → Admin Blanko
           (Step 1)        (Step 2)      (Step 3 or Skip)               (Step 4)   (Step 5)
```

### Key Points Perubahan:
1. **Penambahan Step Kasubbag (Step 2)** - Kasubbag memilih routing approval
2. **Kondisional Step 3** - Jika routing "via_kabag", lanjut ke Kabag. Jika "direct_sekretaris", skip ke Step 4
3. **Atasan dengan Role Ganda** - Jika atasan langsung adalah juga Kasubbag, dia approve 2x
4. **Dynamic Routing** - Kasubbag memilih: `via_kabag` atau `direct_sekretaris`
5. **Approval Logs** - Setiap approval dicatat di tabel `approval_logs`
6. **Status Baru** - `pending_atasan`, `pending_kasubbag`, `pending_kabag`, `pending_sekretaris`, `pending_ketua`

---

## 2. ALUR WORKFLOW V2

### Flow Chart Lengkap

```
┌─────────────────────────────────────────────────────────────────────┐
│                                                                     │
│  Pegawai Submit Cuti (Step: draft → pending_atasan)                │
│             ↓                                                       │
│  Atasan Langsung Approve/Reject                                    │
│  ├─→ [Reject]  → Status: rejected (SELESAI)                       │
│  └─→ [Approve] → Status: pending_kasubbag (Step 2)                │
│             ↓                                                       │
│  Kasubbag Approve + Pilih Routing                                  │
│  ├─→ [Reject]  → Status: rejected (SELESAI)                       │
│  ├─→ [Approve + via_kabag]     → Status: pending_kabag (Step 3)  │
│  │                                                                  │
│  │       Kabag Approve/Reject                                      │
│  │       ├─→ [Reject]  → Status: rejected (SELESAI)              │
│  │       └─→ [Approve] → Status: pending_sekretaris (Step 4) ────┐│
│  │                                                                  ││
│  └─→ [Approve + direct_sekretaris] → Status: pending_sekretaris ──┤│
│                                      (Step 4)                      ││
│                                                                    ││
│  Sekretaris Approve + Pilih Ketua                                 ││
│  ├─→ [Reject]  → Status: rejected (SELESAI)                      ││
│  └─→ [Approve + to_ketua/wakil/plh] → Status: pending_ketua ─────┘│
│             ↓                                                       │
│  Ketua Approve (FINAL)                                             │
│  ├─→ [Reject]  → Status: rejected (SELESAI)                       │
│  └─→ [Approve] → Status: approved (Step 5 = Admin Blanko)        │
│             ↓                                                       │
│  Admin Kepegawaian Process Blanko                                  │
│  └─→ Upload Blanko + Kurangi Kuota → Status: selesai              │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### Tabel Status untuk Setiap Step

| Step | Status Pengajuan | Menunggu Dari | Action |
|------|-----------------|---------------|---------|
| 1 | draft | Initial | Atasan submit pertama kali |
| 1 | pending_atasan | Atasan Langsung | Atasan approve/reject/change |
| 2 | pending_kasubbag | Kasubbag | Kasubbag approve (+ routing) / reject |
| 3* | pending_kabag | Kabag (jika routing=via_kabag) | Kabag approve/reject |
| 4 | pending_sekretaris | Sekretaris | Sekretaris approve (+ pilih ketua) / reject |
| 5 | pending_ketua | Ketua/Wakil/PLH | Ketua approve (FINAL) / reject |
| 6 | approved | Admin Kepeg | Admin process blanko & kurangi kuota |
| 6 | rejected | - | Pengajuan ditolak (end state) |

*Step 3 (Kabag) adalah conditional - hanya jika routing=via_kabag

---

## 3. DATABASE MIGRATION

### 3.1 Backup Database SEBELUM MIGRASI

```bash
# Di command line / terminal
mysqldump -u root -p sipadeceng > backup_sipadeceng_v2_2026_02_22.sql
```

### 3.2 Migration Script - Jalankan di phpMyAdmin atau MySQL CLI

```sql
-- ============================================================================
-- V2 WORKFLOW MIGRATION - Run this script to enable V2 workflow
-- ============================================================================

-- ============================================================================
-- STEP 1: Modify leave_requests table - add workflow tracking columns
-- ============================================================================

ALTER TABLE `leave_requests` 
ADD COLUMN `current_step` INT(11) DEFAULT 1 COMMENT 'Current workflow step: 1-6' AFTER `status`,
ADD COLUMN `routing_path` VARCHAR(30) DEFAULT NULL COMMENT 'via_kabag or direct_sekretaris' AFTER `current_step`,
ADD COLUMN `routing_ketua` VARCHAR(30) DEFAULT NULL COMMENT 'to_ketua, to_wakil, to_plh' AFTER `routing_path`,
ADD COLUMN `rejected_by_role` VARCHAR(30) DEFAULT NULL COMMENT 'Role yang menolak' AFTER `routing_ketua`,
ADD COLUMN `rejected_at_step` INT(11) DEFAULT NULL COMMENT 'Step berapa ditolak' AFTER `rejected_by_role`;

-- Add approval tracking per role
ALTER TABLE `leave_requests`
ADD COLUMN `atasan_approver_id` INT(11) DEFAULT NULL COMMENT 'FK: atasan.id_atasan' AFTER `approved_by`,
ADD COLUMN `atasan_approval_date` DATETIME DEFAULT NULL AFTER `atasan_approver_id`,
ADD COLUMN `atasan_catatan` TEXT DEFAULT NULL AFTER `atasan_approval_date`,
ADD COLUMN `kasubbag_approver_id` INT(11) DEFAULT NULL COMMENT 'FK: atasan.id_atasan' AFTER `atasan_catatan`,
ADD COLUMN `kasubbag_approval_date` DATETIME DEFAULT NULL AFTER `kasubbag_approver_id`,
ADD COLUMN `kasubbag_catatan` TEXT DEFAULT NULL AFTER `kasubbag_approval_date`,
ADD COLUMN `kabag_approver_id` INT(11) DEFAULT NULL COMMENT 'FK: atasan.id_atasan' AFTER `kasubbag_catatan`,
ADD COLUMN `kabag_approval_date` DATETIME DEFAULT NULL AFTER `kabag_approver_id`,
ADD COLUMN `kabag_catatan` TEXT DEFAULT NULL AFTER `kabag_approval_date`,
ADD COLUMN `sekretaris_approver_id` INT(11) DEFAULT NULL COMMENT 'FK: atasan.id_atasan' AFTER `kabag_catatan`,
ADD COLUMN `sekretaris_approval_date` DATETIME DEFAULT NULL AFTER `sekretaris_approver_id`,
ADD COLUMN `sekretaris_catatan` TEXT DEFAULT NULL AFTER `sekretaris_approval_date`,
ADD COLUMN `ketua_approver_id` INT(11) DEFAULT NULL COMMENT 'FK: admin_approvers.id' AFTER `sekretaris_catatan`,
ADD COLUMN `ketua_approval_date` DATETIME DEFAULT NULL AFTER `ketua_approver_id`,
ADD COLUMN `ketua_catatan` TEXT DEFAULT NULL AFTER `ketua_approval_date`,
ADD COLUMN `admin_kepeg_user_id` INT(11) DEFAULT NULL COMMENT 'User ID staff kepegawaian' AFTER `ketua_catatan`;

-- ============================================================================
-- STEP 2: Create approval_logs table
-- ============================================================================

CREATE TABLE IF NOT EXISTS `approval_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `leave_request_id` INT(11) NOT NULL,
  `step` INT(11) NOT NULL COMMENT '1-6: workflow step',
  `action` VARCHAR(50) NOT NULL COMMENT 'approve, reject, change, postpone',
  `catatan` TEXT,
  `approved_by_atasan_id` INT(11) DEFAULT NULL COMMENT 'FK: atasan.id_atasan',
  `approved_by_admin_id` INT(11) DEFAULT NULL COMMENT 'FK: admin_approvers.id',
  `operator_user_id` INT(11) DEFAULT NULL COMMENT 'FK: users.id - user yang melakukan action',
  `logged_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  
  KEY `leave_request_id` (`leave_request_id`),
  KEY `step` (`step`),
  CONSTRAINT `approval_logs_ibfk_1` FOREIGN KEY (`leave_request_id`) REFERENCES `leave_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- STEP 3: Add role column to atasan table
-- ============================================================================

ALTER TABLE `atasan`
ADD COLUMN `role` ENUM('kasubbag', 'kabag', 'sekretaris', 'ketua') DEFAULT NULL 
COMMENT 'NULL=atasan biasa, or special role in workflow' AFTER `jabatan`;

-- Update existing atasan records with proper roles (SESUAIKAN DENGAN DATA REAL)
UPDATE `atasan` SET `role` = 'kasubbag' WHERE `nama_atasan` LIKE '%Kasubbag%' OR `id_atasan` = 1;
UPDATE `atasan` SET `role` = 'kabag' WHERE `nama_atasan` LIKE '%Kabag%' OR `id_atasan` = 6;
UPDATE `atasan` SET `role` = 'ketua' WHERE `nama_atasan` LIKE '%Ketua%' OR `id_atasan` = 5;

-- INSERT Sekretaris if not exists (CUSTOMIZE)
INSERT INTO `atasan` (`nama_atasan`, `NIP`, `jabatan`, `role`) 
SELECT 'Sekretaris', 'NIP_SEKRETARIS', 'Sekretaris', 'sekretaris'
WHERE NOT EXISTS (SELECT 1 FROM `atasan` WHERE `role` = 'sekretaris');

-- ============================================================================
-- STEP 4: Update existing leave_requests to populate current_step & status
-- ============================================================================

-- Draft requests
UPDATE `leave_requests` SET `current_step` = 1 WHERE `status` = 'draft';

-- Pending requests -> pending_atasan (menunggu atasan)
UPDATE `leave_requests` SET 
  `current_step` = 1, 
  `status` = 'pending_atasan' 
WHERE `status` = 'pending';

-- Awaiting pimpinan -> pending_kasubbag (menunggu kasubbag)
UPDATE `leave_requests` SET 
  `current_step` = 2, 
  `status` = 'pending_kasubbag' 
WHERE `status` = 'awaiting_pimpinan';

-- Already approved
UPDATE `leave_requests` SET `current_step` = 99 WHERE `status` = 'approved';

-- Rejected, changed, postponed status tetap sama
UPDATE `leave_requests` SET `current_step` = 999 WHERE `status` IN ('rejected', 'changed', 'postponed');

-- ============================================================================
-- STEP 5: Create approval_logs from existing approvals (optional, for audit trail)
-- ============================================================================

-- Log dari atasan approvals (estimate dari catatan_approval)
INSERT INTO `approval_logs` (`leave_request_id`, `step`, `action`, `catatan`, `logged_at`) 
SELECT `id`, 1, 'approve', SUBSTRING(`catatan_approval`, 1, 500), `created_at`
FROM `leave_requests`
WHERE `status` IN ('pending_kasubbag', 'pending_kabag', 'pending_sekretaris', 'pending_ketua', 'approved');

-- ============================================================================
-- STEP 6 (OPTIONAL): Create migration status table for tracking
-- ============================================================================

CREATE TABLE IF NOT EXISTS `migration_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `migration_name` VARCHAR(100) NOT NULL,
  `executed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('success', 'failed') DEFAULT 'success',
  `notes` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migration_logs` (`migration_name`, `status`, `notes`)
VALUES ('v2_workflow_migration', 'success', 'V2 Workflow tables and columns created');

-- ============================================================================
-- VERIFICATION: Check migration success
-- ============================================================================

-- Check columns added
SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'leave_requests' AND TABLE_SCHEMA = 'sipadeceng' 
ORDER BY ORDINAL_POSITION DESC LIMIT 10;

-- Check approval_logs table
SELECT * FROM `approval_logs` LIMIT 1;

-- Check atasan roles
SELECT id_atasan, nama_atasan, role FROM `atasan` WHERE role IS NOT NULL;

```

---

## 4. FILE-FILE BARU & MODIFIKASI

### 📁 File Baru yang Dibuat

| File | Tujuan |
|------|--------|
| `app/helpers/workflow_engine.php` | Core workflow engine dengan semua logika routing & step handling |

### 📝 File yang Dimodifikasi

| File | Perubahan |
|------|-----------|
| `app/controllers/ApprovalController.php` | Tambah method: `processV2()`, `showKasubbagApprovalForm()`, `getAtasanIdFromSession()` |
| `app/helpers/workflow_helper.php` | Tambah V2 helper functions untuk status badge, step labels, timeline formatting |
| `app/views/approval/index.php` | TBD: Update form untuk menampilkan routing options saat kasubbag approve |

### 📄 File Reference (Dari MIGRATION_GUIDE_V2.md)

- Lihat `MIGRATION_GUIDE_V2.md` untuk detail lengkap database schema
- Lihat bagian "4. WORKFLOW ENGINE (PHP)" untuk dokumentasi function

---

## 5. IMPLEMENTASI STEP-BY-STEP

### Phase 1: Database Migration (15 menit)

```
1. Backup database
   mysqldump -u root -p sipadeceng > backup_sipadeceng_v2_2026_02_22.sql
   
2. Jalankan migration script di phpMyAdmin → SQL:
   - Copy script dari section 3.2 di file ini
   - Paste ke phpMyAdmin SQL tab
   - Execute
   
3. Verify migrasi berhasil:
   - Check kolom baru di leave_requests table
   - Check approval_logs table tercipta
   - Check atasan.role column terisi
```

### Phase 2: Code Deployment (10 menit)

```
1. Copy file baru:
   - app/helpers/workflow_engine.php (sudah dibuat)
   
2. Update ApprovalController.php:
   - Method processV2() sudah ditambahkan
   - Method showKasubbagApprovalForm() sudah ditambahkan
   - Method getAtasanIdFromSession() sudah ditambahkan
   
3. Update workflow_helper.php:
   - V2 helper functions sudah ditambahkan
```

### Phase 3: Testing & Validation (30 menit)

```
1. Test workflow dengan skenario di section 6
2. Verify approval logs terekan dengan benar
3. Test routing: via_kabag dan direct_sekretaris
4. Test atasan yang juga kasubbag (dual approval)
5. Test notifications terkirim ke proper approver
```

### Phase 4: UI Updates (Optional - untuk UX yang lebih baik)

```
1. Update approval/index.php form untuk routing UI
2. Tambah timeline display di detail modal
3. Update status badges sesuai V2 workflow
```

---

## 6. CONTOH SKENARIO WORKFLOW

### Skenario 1: Atasan Biasa (Tidak Punya Role)

```
Pegawai: Ihsan (atasan: Alif Qadri - role: NULL)
Pengajuan: Cuti 5 hari, Jan 2026

FLOW:
1. Ihsan submit cuti
   → Status: pending_atasan, current_step: 1

2. Alif Qadri (Atasan Langsung) approve
   → Call: processAtasanLangsungApproval(..., 'approve', ...)
   → Status: pending_kasubbag, current_step: 2
   → Notifikasi ke Kasubbag

3. Verry (Kasubbag, role=kasubbag) approve + pilih routing
   → Call: processKasubbagApproval(..., 'approve', 'direct_sekretaris')
   → Status: pending_sekretaris, current_step: 4 (skip Kabag)
   → Notifikasi ke Sekretaris

4. Sekretaris approve + pilih ketua
   → Status: pending_ketua, current_step: 5
   → Notifikasi ke Ketua

5. Ketua approve (FINAL)
   → Status: approved, current_step: 99
   → Notifikasi ke Admin & Ihsan

6. Admin upload blanko & kurangi kuota
   → Status: selesai
   → Kuota Ihsan berkurang 5 hari
```

### Skenario 2: Atasan Sekaligus Kasubbag (Dual Role)

```
Pegawai: Budi (atasan: Verry Setya Widyatama - role: kasubbag)
Pengajuan: Cuti 3 hari, Jan 2026

FLOW:
1. Budi submit cuti
   → Status: pending_atasan, current_step: 1

2. Verry (Atasan + Kasubbag) approve sebagai Atasan
   → Call: processAtasanLangsungApproval(..., 'approve', ...)
   → Status: pending_kasubbag, current_step: 2
   → Notifikasi ke Verry (sebagai Kasubbag, atau auto-display form kasubbag?)

3. Verry (Kasubbag) approve + pilih routing
   → Call: processKasubbagApproval(..., 'approve', 'via_kabag')
   → Status: pending_kabag, current_step: 3 (pilih via Kabag)
   → Notifikasi ke Kabag

4. Kabag approve
   → Status: pending_sekretaris, current_step: 4
   → Notifikasi ke Sekretaris

5. [Continue same as Skenario 1...]

CATATAN:
- Verry approve 2 kali (step 1 & step 2)
- UI bisa auto-detect kalau sudah approve step 1, tampilkan form kasubbag untuk pilih routing
```

### Skenario 3: Atasan Sekaligus Kasubbag + Kabag (Triple Role - Edge Case)

```
Jika ada atasan yang punya dua role (misal: kasubbag AND kabag):
- Step 1: Approve sebagai Atasan Langsung
- Step 2: Approve sebagai Kasubbag (pilih routing)
- Step 3: Approve sebagai Kabag (jika routing via_kabag)

IMPLEMENTASI:
Lihat logic di processAtasanLangsungApproval() → ada check `isAtasanAlsoKasubbag()`
```

### Skenario 3b: Atasan Sekaligus Kabag (Dual Role)

```
Pegawai: Andi (atasan: Budi - role: kabag)
Pengajuan: Cuti 2 hari, Jan 2026

FLOW:
1. Andi submit cuti
   → Status: pending_atasan, current_step: 1

2. Budi (Atasan + Kabag) approve sebagai Atasan Langsung
   → Call: processAtasanLangsungApproval(..., 'approve', ...)
   → Status: pending_kasubbag, current_step: 2
   → Notifikasi ke Kasubbag

3. Kasubbag approve + pilih routing
   → Call: processKasubbagApproval(..., 'approve', 'via_kabag')
   → Status: pending_kabag, current_step: 3
   → Notifikasi ke Budi (sebagai Kabag)

4. Budi (Kabag) approve sebagai Kabag
   → Call: processKabagApproval(..., 'approve', ...)
   → Status: pending_sekretaris, current_step: 4
   → Notifikasi ke Sekretaris

5. Sekretaris approve + pilih ketua
   → Status: pending_ketua, current_step: 5
   → Notifikasi ke Ketua

6. Ketua approve (FINAL)
   → Status: approved, current_step: 99
   → Notifikasi ke Admin & Andi

CATATAN:
- Budi approve 2 kali (step 1 sebagai Atasan Langsung, step 3 sebagai Kabag)
- Step 2 (Kasubbag) harus approve dulu sebelum Budi bisa approve sebagai Kabag
- Jika kasubbag pilih routing 'direct_sekretaris', Budi tidak akan approve lagi (tidak ada step 3 Kabag)
- UI perlu detect kalau user punya multiple roles dan tampilkan form yang sesuai
```

### Skenario 3c: Atasan Sekaligus Sekretaris (Dual Role)

```
Pegawai: Chandra (atasan: Doni - role: sekretaris)
Pengajuan: Cuti 4 hari, Jan 2026

FLOW:
1. Chandra submit cuti
   → Status: pending_atasan, current_step: 1

2. Doni (Atasan + Sekretaris) approve sebagai Atasan Langsung
   → Call: processAtasanLangsungApproval(..., 'approve', ...)
   → Status: pending_kasubbag, current_step: 2
   → Notifikasi ke Kasubbag

3. Kasubbag approve + pilih routing
   → Call: processKasubbagApproval(..., 'approve', 'direct_sekretaris')
   → Status: pending_sekretaris, current_step: 4 (skip Kabag)
   → Notifikasi ke Doni (sebagai Sekretaris)

4. Doni (Sekretaris) approve + pilih ketua
   → Call: processSekretarisApproval(..., 'approve', 'to_ketua', ...)
   → Status: pending_ketua, current_step: 5
   → Notifikasi ke Ketua

5. Ketua approve (FINAL)
   → Status: approved, current_step: 99
   → Notifikasi ke Admin & Chandra

CATATAN:
- Doni approve 2 kali (step 1 sebagai Atasan Langsung, step 4 sebagai Sekretaris)
- Jika kasubbag pilih routing 'via_kabag', ada step 3 (Kabag) yang harus dihandle oleh Kabag lain (bukan Doni)
- Doni tidak akan approve step 3 Kabag meskipun punya role sekretaris, karena role sekretaris hanya handle step 4
- UI perlu detect kalau user punya multiple roles dan tampilkan form yang sesuai per step
```

### Skenario 4: Penolakan di Step Kasubbag

```
Pegawai: Ihsan
Status: pending_kasubbag

Kasubbag REJECT:
→ Call: processKasubbagApproval(..., 'reject', 'Alasan penolakan...')
→ Status: rejected, rejected_by_role: 'kasubbag', rejected_at_step: 2
→ Notifikasi: "Pengajuan ditolak oleh Kasubbag"
→ END WORKFLOW
```

### Skenario 5: Penolakan di Step Kabag

```
Pegawai: Ihsan (pengajuan pending_kabag setelah Kasubbag approve dengan routing via_kabag)
Status: pending_kabag, current_step: 3

Kabag REJECT:
→ Call: processKabagApproval(..., 'reject', 'Alasan penolakan...')
→ Status: rejected, rejected_by_role: 'kabag', rejected_at_step: 3
→ kasubbag_catatan: Alasan penolakan dari Kabag
→ Notifikasi: "Pengajuan ditolak oleh Kabag"
→ END WORKFLOW

BEDANYA dengan penolakan Kasubbag:
- Step: 2 (Kasubbag) → 3 (Kabag)
- Rejected at step: 2 → 3
- Role: kasubbag → kabag
- Database column: kasubbag_approver_id, kasubbag_approval_date, kasubbag_catatan → kabag_approver_id, kabag_approval_date, kabag_catatan
```

### Skenario 6: Penolakan di Step Sekretaris

```
Pegawai: Ihsan (pengajuan pending_sekretaris setelah Kabag atau Kasubbag approve)
Status: pending_sekretaris, current_step: 4

Sekretaris REJECT:
→ Call: processSekretarisApproval(..., 'reject', 'Alasan penolakan...')
→ Status: rejected, rejected_by_role: 'sekretaris', rejected_at_step: 4
→ Notifikasi: "Pengajuan ditolak oleh Sekretaris"
→ END WORKFLOW

BEDANYA dengan penolakan di step sebelumnya:
- Step: 4 (Sekretaris)
- Rejected at step: 4
- Role: sekretaris
- Database column: sekretaris_approver_id, sekretaris_approval_date, sekretaris_catatan
- Ini adalah step terakhir sebelum Ketua
```

### Skenario 7: Penolakan di Step Ketua (Final)

```
Pegawai: Ihsan (pengajuan pending_ketua setelah Sekretaris approve)
Status: pending_ketua, current_step: 5

Ketua REJECT:
→ Call: processKetuaApproval(..., 'reject', 'Alasan penolakan...')
→ Status: rejected, rejected_by_role: 'ketua', rejected_at_step: 5
→ Notifikasi: "Pengajuan ditolak oleh Ketua"
→ END WORKFLOW

CATATAN (Final Rejection):
- Ini adalah penolakan paling akhir sebelum Admin
- Jika sampai tahap ini dan ditolak, pegawai harus submit pengajuan ulang
- Database column: ketua_approver_id, ketua_approval_date, ketua_catatan
```

---

## 7. PANDUAN TESTING

### Test Case 1: Basic Approval Flow

```
Setup:
- Pegawai: Test User
- Atasan: Test Atasan (role: NULL)
- Kasubbag: Test Kasubbag (role: kasubbag)
- Kabag: Test Kabag (role: kabag)
- Sekretaris: Test Sekretaris (role: sekretaris)
- Ketua: Test Ketua (admin_approvers.id)

Steps:
1. Login as Test User → Submit cuti
2. Verify: Status = pending_atasan, current_step = 1
3. Login as Test Atasan → Approve
4. Verify: Status = pending_kasubbag, current_step = 2
5. Login as Test Kasubbag → Approve (routing: direct_sekretaris)
6. Verify: Status = pending_sekretaris, current_step = 4 (skipped step 3)
7. Login as Test Sekretaris → Approve (pilih ketua)
8. Verify: Status = pending_ketua, current_step = 5
9. Login as Test Ketua → Approve
10. Verify: Status = approved
11. Check approval_logs: 5 entries (1 approve dari each role)
```

### Test Case 2: Dual Role Approval

```
Setup:
- Pegawai: Test User 2
- Atasan: Test Kasubbag (jadi atasan langsung yang juga kasubbag)
- Kasubbag: (same person = Test Kasubbag)

Steps:
1. Login as Test User 2 → Submit cuti
2. Status = pending_atasan, current_step = 1
3. Login as Test Kasubbag → Approve (Step 1)
4. Status = pending_kasubbag, current_step = 2
5. Login as Test Kasubbag AGAIN → Approve (Step 2, dengan routing)
6. Status = pending_kabag or pending_sekretaris (depending on routing)
7. Verify approval_logs: berbeda approval_by_atasan_id with kasubbag_approver_id
```

### Test Case 3: Rejection at Each Step

```
Test at each step:
- Step 1 (Atasan): Reject → rejected, rejected_at_step=1
- Step 2 (Kasubbag): Reject → rejected, rejected_at_step=2
- Step 3 (Kabag): Reject → rejected, rejected_at_step=3
- Step 4 (Sekretaris): Reject → rejected, rejected_at_step=4
- Step 5 (Ketua): Reject → rejected, rejected_at_step=5
```

### Test Case 4: Routing Variations

```
Test routing at Kasubbag (step 2):
1. Approve + routing=via_kabag → next_step=3 (status=pending_kabag)
2. Approve + routing=direct_sekretaris → next_step=4 (status=pending_sekretaris, skip kabag)
```

### Test Case 5: Notification Flow

```
Verify notifications are sent to:
- Step 1 approve → Notify Kasubbag
- Step 2 approve → Notify next approver (Kabag or Sekretaris)
- Step 3 approve → Notify Sekretaris
- Step 4 approve → Notify Ketua
- Step 5 approve → Notify Admin & Pegawai
```

---

## 8. TROUBLESHOOTING

### Issue 1: Status = pending_atasan but tidak ada data di approval_logs

**Penyebab:** Data lama belum dimigrasikan dengan approval_logs

**Solusi:**
```sql
-- Insert approval logs dari leave_requests yang sudah punya atasan_approver_id
INSERT INTO `approval_logs` 
(`leave_request_id`, `step`, `action`, `catatan`, `approved_by_atasan_id`, `logged_at`)
SELECT `id`, 1, 'approve', `atasan_catatan`, `atasan_approver_id`, `atasan_approval_date`
FROM `leave_requests`
WHERE `atasan_approver_id` IS NOT NULL AND `status` != 'draft';
```

### Issue 2: processV2() method not found

**Penyebab:** ApprovalController belum di-update

**Solusi:** Pastikan file `app/controllers/ApprovalController.php` sudah di-update dengan method `processV2()`, `showKasubbagApprovalForm()`, `getAtasanIdFromSession()`

### Issue 3: Table approval_logs doesn't exist

**Penyebab:** Migration database belum dijalankan

**Solusi:** Jalankan migration script dari section 3.2

### Issue 4: Atasan tidak bisa approve (Akses ditolak)

**Penyebab:** User bukan atasan (session user_type != 'atasan')

**Cek:**
```sql
SELECT id, nama, user_type FROM users WHERE id = <login_user_id>;
-- Pastikan user_type = 'atasan'
```

### Issue 5: Routing option tidak muncul saat kasubbag approve

**Penyebab:** Form UI belum di-update untuk menampilkan routing radio buttons

**Solusi:** Update `app/views/approval/index.php` atau buat form baru untuk kasubbag approval

Referensi: Lihat function `showKasubbagApprovalForm()` di ApprovalController untuk DOM form template

### Issue 6: Notifications tidak terkirim

**Penyebab:** Notification model atau query tidak benar

**Cek:**
```sql
SELECT * FROM notifications WHERE leave_request_id = <leave_id> ORDER BY created_at DESC;
```

**Verifikasi:** Function `insertApprovalLog()` dan notifications dimulai di workflow_engine.php line ~200+

---

## 📞 QUICK REFERENCE

### Key Functions Location

**workflow_engine.php:**
- `getWorkflowSteps()` - Define semua step
- `getAtasanRole($db, $atasan_id)` - Get atasan info & role
- `processAtasanLangsungApproval(...)` - Handle step 1
- `processKasubbagApproval(...)` - Handle step 2 (dengan routing)
- `getApprovalTimeline($db, $leave_id)` - Get audit trail

**ApprovalController.php:**
- `processV2()` - Main handler untuk V2 workflow
- `showKasubbagApprovalForm()` - Form untuk kasubbag routing

**workflow_helper.php:**
- `getV2StatusBadge($status, $currentStep)` - Status display
- `getStepLabelFromStatus($status)` - Step label
- `isCurrentUserApprover(...)` - Check permission

### Database Queries

**Get pending requests untuk specific step:**
```sql
-- Menunggu atasan
SELECT * FROM leave_requests WHERE status = 'pending_atasan' AND current_step = 1;

-- Menunggu kasubbag
SELECT * FROM leave_requests WHERE status = 'pending_kasubbag' AND current_step = 2;

-- Menunggu kabag
SELECT * FROM leave_requests WHERE status = 'pending_kabag' AND current_step = 3;
```

**Get approval timeline:**
```sql
SELECT al.*, a.nama_atasan as approver
FROM approval_logs al
LEFT JOIN atasan a ON al.approved_by_atasan_id = a.id_atasan
WHERE al.leave_request_id = <leave_id>
ORDER BY al.logged_at ASC;
```

---

## ✅ TODO CHECKLIST

- [x] Create workflow_engine.php dengan core functions
- [x] Update ApprovalController dengan processV2() method
- [x] Update workflow_helper.php dengan V2 functions
- [ ] ⚠️ **MANUAL**: Update approval form UI untuk routing & timeline
- [ ] ⚠️ **MANUAL**: Run database migration script
- [ ] ⚠️ **MANUAL**: Configure atasan roles di database (kasubbag, kabag, sekretaris, ketua)
- [ ] ⚠️ **MANUAL**: Test semua scenario di section 7
- [ ] ⚠️ **MANUAL**: Update navigation/dashboard untuk menampilkan pending requests per role

---

## 📄 REFERENSI DOKUMEN

- `MIGRATION_GUIDE_V2.md` - Detail lengkap database schema & workflow logic
- `APPROVAL_CONTROLLER.php` - Implementation code untuk approval handlers
- `workflow_engine.php` - Core workflow engine functions
- `workflow_helper.php` - Helper functions untuk V2 workflow

---

**Last Updated:** 22 Februari 2026  
**Version:** 2.0 (V2 Workflow dengan Routing Dinamis)  
**Status:** ✅ Ready for Implementation
