# Deskripsi Umum — TaskFlow

## Tentang Proyek

**TaskFlow** adalah sistem manajemen tugas kolaboratif berbasis web yang dirancang untuk organisasi dengan struktur hierarki. Sistem ini memfasilitasi distribusi, pengerjaan, dan peninjauan tugas secara terstruktur melalui 4 level peran: **Atasan, Super Admin, Project Manager, dan Anggota**.

Setiap tugas mengalir dari pembuat (Atasan) ke pelaksana (Anggota) melalui jalur yang jelas — dengan checkpoint review di setiap level — sehingga akuntabilitas dan audit trail terjamin.

---

## Tujuan

- Menyediakan alur distribusi tugas yang terstruktur dan terdokumentasi.
- Memisahkan tanggung jawab antara pembuat tugas, distributor, pelaksana, dan pengulas.
- Memberikan visibilitas penuh kepada Atasan terhadap status tugas di setiap tahap.
- Memastikan setiap perubahan status tercatat (siapa, kapan, dari status apa ke status apa).

---

## Peran & Tanggung Jawab

| Peran | Tanggung Jawab |
|-------|---------------|
| **Atasan** | Membuat tugas dan mengirimkannya ke Super Admin. Memantau status tugas dari awal hingga selesai. |
| **Super Admin** | Menerima tugas dari Atasan (Global Tasks), menunjuk Project Manager, memberikan persetujuan akhir, dan mengelola akun pengguna. |
| **Project Manager** | Menerima tugas dari Super Admin, menugaskan ke anggota tim, serta mereview dan menyetujui/menolak hasil kerja anggota. |
| **Anggota** | Mengerjakan tugas yang ditugaskan, mengunggah file hasil kerja, dan merespons catatan revisi dari Project Manager. |

---

## Alur Kerja

```
Atasan → buat tugas → Super Admin → assign PM → PM → assign anggota → Anggota
                                                                          |
                                                                   upload & submit
                                                                          |
                                                                    PM review
                                                                    /        \
                                                               approve      reject + catatan
                                                                 |                |
                                                          Super Admin        Anggota revisi
                                                          final approve           |
                                                                |           upload ulang
                                                              SELESAI       (kembali ke PM review)
```

**Status Tugas:**
`draft` → `assigned_pm` → `assigned_member` → `pending_pm` → `pending_admin` → `done`
(cabang revisi: `pending_pm` → `revision` → `pending_pm`, looping hingga disetujui)

---

## Teknologi

| Layer | Teknologi |
|-------|-----------|
| Backend | PHP 8.2+, Laravel 11 |
| Frontend | Blade, Tailwind CSS 3.4, Alpine.js 3.0, Livewire 3 |
| Database | MariaDB 10.6 (InnoDB) |
| Autentikasi | Laravel Breeze (session-based) |
| Build Tool | Vite |
| Deployment | Docker Compose (PHP-FPM + Nginx + MariaDB) |
| Notifikasi | WhatsApp via Fonnte API, Email via SMTP |

---

## Fitur Utama

- **Manajemen Tugas Hierarki** — distribusi tugas 4-level dengan alur yang terdefinisi.
- **Sidebar per Role** — setiap peran mendapatkan antarmuka navigasi yang berbeda sesuai tanggung jawabnya.
- **Review & Revisi** — PM dapat menyetujui atau menolak hasil kerja anggota dengan catatan revisi.
- **Upload File** — anggota mengunggah file hasil kerja (max 10 MB, format: pdf, doc, docx, zip, xlsx, jpg, png).
- **PM Performance** — Super Admin dapat memantau metrik kinerja setiap PM (total tugas, selesai, overdue, completion rate).
- **Notifikasi** — pemberitahuan otomatis via WhatsApp dan Email saat status tugas berubah (asinkron).
- **Audit Trail** — setiap perubahan status tugas tercatat beserta pelakunya.
- **Manajemen Pengguna** — Super Admin dapat mengaktifkan/menonaktifkan akun pengguna.

---

## Struktur Pengguna Demo

| Peran | Email | Password |
|-------|-------|----------|
| Atasan | atasan@test.com | password |
| Super Admin | admin@admin.com | password |
| Project Manager | pm1@test.com | password |
| Anggota | member1@test.com | password |
| Anggota | member2@test.com | password |

---

## Keamanan

- **RBAC (Role-Based Access Control)** — setiap route dilindungi middleware `CheckRole`.
- **CheckActive Middleware** — akun nonaktif tidak bisa mengakses sistem.
- **TaskPolicy** — otorisasi granular per aksi (view, approve, assign, dll).
- Session-based auth, CSRF aktif, Blade escaping.
