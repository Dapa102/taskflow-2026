# BUSINESS REQUIREMENT DOCUMENT (BRD) - REVISI FINAL
## Collaborative Task Management System (Pure Team Edition)

---

**Versi:** 7.0 (Review Workflow + Sidebar for All Roles)
**Tanggal:** 21 Juni 2026
**Status:** Final untuk Stakeholder

---

## 1. Ringkasan Eksekutif

Dokumen ini menguraikan persyaratan untuk pengembangan aplikasi kolaborasi tim untuk tim kecil (3-10 orang). Sistem **sepenuhnya berbasis tim**; setiap tugas wajib terikat pada Workspace dan melalui alur review bertingkat.

Tiga pilar utama:
1. **Super Admin** — membuat tugas, menunjuk Project Manager, dan memberikan persetujuan final.
2. **Project Manager** — mengelola tugas tim, meninjau hasil kerja anggota, dan meneruskan ke admin.
3. **Anggota** — mengerjakan tugas, mengunggah hasil, dan menerima revisi.

---

## 2. Latar Belakang dan Justifikasi Bisnis

- **Konteks:** Tim kecil butuh alur kerja terstruktur: Super Admin sebagai pengawas, PM sebagai koordinator, anggota sebagai pelaksana.
- **Permasalahan:** Tools kolaborasi tidak memiliki alur review bertingkat dan visibilitas penuh bagi pemilik platform.
- **Solusi:** Platform dengan alur tugas Super Admin → PM → Anggota dengan sistem review, upload, dan persetujuan di setiap level.

---

## 3. Tujuan Bisnis

- Alur tugas terstruktur dengan review 3 level (Admin → PM → Anggota).
- Visibilitas penuh bagi Super Admin terhadap semua tugas.
- Akuntabilitas setiap role dalam siklus tugas.
- Sidebar navigasi khusus per role untuk akses cepat.

---

## 4. Ruang Lingkup

**A. Modul Super Admin:**
- Membuat tugas dan menunjuk PM.
- Melihat semua tugas di semua tim.
- Persetujuan final tugas yang sudah direview PM.
- Manajemen akun pengguna.

**B. Modul Project Manager:**
- Mengelola workspace dan anggota tim.
- Menerima tugas dari Super Admin.
- Menugaskan tugas ke anggota tim.
- Mereview hasil kerja anggota (approve/reject).
- Meneruskan tugas yang approve ke Super Admin.

**C. Modul Anggota:**
- Melihat tugas yang ditugaskan.
- Mengerjakan dan mengunggah hasil (file).
- Menerima revisi dari PM.
- Status tugas: todo, on_progress, pending_pm, pending_admin, revision, done.

---

## 5. Stakeholders dan Pengguna

| Stakeholder | Peran |
|-------------|-------|
| **Super Admin** | Membuat tugas global, final approval, manajemen akun. |
| **Project Manager** | Mengelola tim, menugaskan & mereview tugas anggota. |
| **Anggota** | Mengerjakan tugas, upload hasil, menerima revisi. |

---

## 6. Persyaratan Fungsional

| Kode | Kebutuhan | Prioritas |
|------|-----------|-----------|
| F-01 | Super Admin dapat membuat tugas dan menunjuk PM. | Wajib |
| F-02 | Saat memilih PM, muncul tim yang dipimpin. | Wajib |
| F-03 | PM dapat menugaskan tugas ke anggota tim. | Wajib |
| F-04 | Anggota dapat mengerjakan dan mengupload file. | Wajib |
| F-05 | PM dapat approve tugas anggota → pending admin. | Wajib |
| F-06 | PM dapat reject tugas anggota → revision. | Wajib |
| F-07 | Super Admin dapat final approve → done. | Wajib |
| F-08 | Setiap role memiliki sidebar dengan daftar tugas. | Wajib |
| F-09 | Label Project Manager dan Anggota tampil di semua tempat. | Wajib |
| F-10 | Nama tim tampil di samping nama pengguna. | Wajib |

---

## 7. Alur Proses Bisnis

1. **Super Admin** buka Daftar Tugas → klik "+ Buat Tugas Baru" → pilih PM → tampil tim PM → simpan.
2. **PM** lihat tugas baru → klik "Assign" → pilih anggota → anggota kerja.
3. **Anggota** kerja → klik "Selesai & Upload" → upload file → status `pending_pm`.
4. **PM** review → **Approve** (→ `pending_admin`) atau **Revisi** + catatan (→ `revision`).
5. **Super Admin** lihat tugas `pending_admin` → klik "Selesai" → `done`.
6. Jika revisi, anggota perbaiki dan upload ulang.

---

## 8. Teknologi

- **Framework:** Laravel Fullstack (Blade + Livewire).
- **Database:** MariaDB.
- **Frontend:** Tailwind CSS, Alpine.js.
- **Autentikasi:** Session-based (Laravel Breeze).
- **Layout:** Sidebar per role (admin, pm, member).

---

## 9. Kriteria Penerimaan

| No | Kriteria | Status |
|----|----------|--------|
| 1 | Super Admin buat tugas, pilih PM, lihat tim PM | ✅ |
| 2 | PM assign tugas ke anggota | ✅ |
| 3 | Anggota upload file + submit | ✅ |
| 4 | PM approve → pending_admin | ✅ |
| 5 | PM reject + catatan → revision | ✅ |
| 6 | Super Admin final approve → done | ✅ |
| 7 | Sidebar daftar tugas per role | ✅ |
| 8 | Label (Project Manager) / (Anggota) + nama tim | ✅ |
