# Implementation Plan MVP — TaskFlow

Dokumen ini menjadi acuan implementasi MVP TaskFlow berdasarkan `docs/BRD.md` dan `docs/PRD.md`.

Fokus MVP: membangun sistem manajemen tugas kolaboratif berbasis `Workspace`, `Project`, dan `Task` dengan tiga role utama: `Super Admin`, `Project Manager`, dan `Member`.

---

## 1. Prinsip Implementasi MVP

- Dahulukan alur bisnis utama: Super Admin membuat workspace, menunjuk Project Manager, Project Manager membuat project dan task, Member mengerjakan task.
- Gunakan Role-Based Access Control (RBAC) untuk membatasi akses sesuai role.
- Semua task harus berada dalam project, dan semua project harus berada dalam workspace.
- Setiap workspace hanya memiliki satu Project Manager aktif.
- Setiap task hanya ditugaskan kepada satu Member.
- Setiap perubahan status task harus tercatat di riwayat aktivitas.
- Fitur di luar MVP dicatat di `docs/nex_update.md`.

---

## 2. Scope MVP

### 2.1 Role MVP

- Super Admin
- Project Manager
- Member

### 2.2 Modul MVP

- Autentikasi dan manajemen akun dasar
- Manajemen user
- Manajemen workspace
- Penunjukan Project Manager
- Manajemen anggota workspace
- Manajemen project
- Manajemen task
- Update status task
- Upload lampiran task
- Riwayat perubahan status task
- Dashboard dasar per role
- Laporan dasar
- Notifikasi internal dasar

### 2.3 Status Task MVP

Status task mengikuti BRD dan PRD:

- `To Do`: task dibuat dan siap dikerjakan.
- `In Progress`: Member sedang mengerjakan task.
- `Review`: Member selesai mengerjakan dan menunggu validasi Project Manager.
- `Done`: Project Manager menyatakan task selesai.
- `Cancelled`: task dibatalkan oleh Project Manager.

### 2.4 Di Luar MVP

- Arbitrase
- Approval final Super Admin setelah PM approval
- Eskalasi otomatis 2x24 jam
- Deputy Project Manager
- WhatsApp Fonnte API
- Email SMTP
- Gantt Chart
- Kalender project
- Sprint dan backlog internal
- Subtask
- SSO
- Integrasi GitHub, GitLab, Jira
- Aplikasi mobile

Detail fitur lanjutan disimpan di `docs/nex_update.md`.

---

## 3. Sprint 0 — Fondasi Sistem

### Tujuan

Menyiapkan struktur dasar aplikasi agar semua sprint berikutnya punya fondasi teknis yang stabil.

### Scope

- Setup Laravel 12.
- Setup database MariaDB atau MySQL.
- Setup autentikasi email dan password.
- Setup role user: `super_admin`, `project_manager`, `member`.
- Setup middleware RBAC.
- Setup layout dashboard dasar.
- Setup struktur navigasi berdasarkan role.
- Setup migration awal.

### Database Awal

- `users`
- `workspaces`
- `workspace_members`
- `projects`
- `tasks`
- `task_status_histories`
- `notifications`

### Acceptance Criteria

- User dapat login dan logout.
- User diarahkan ke dashboard sesuai role.
- User tidak bisa mengakses halaman di luar role.
- Database utama tersedia melalui migration.
- Seeder role dan user awal tersedia.

---

## 4. Sprint 1 — Super Admin: User dan Workspace

### Tujuan

Menyediakan fungsi administrasi inti untuk Super Admin.

### Scope

- Super Admin dashboard dasar.
- CRUD user.
- Set role user sebagai Project Manager atau Member.
- Aktivasi dan nonaktif akun user.
- CRUD workspace.
- Menunjuk Project Manager pada workspace.
- Mengganti Project Manager pada workspace.
- Melihat daftar workspace.

### Data Workspace

- Nama workspace
- Deskripsi workspace
- Project Manager aktif
- Status aktif workspace

### Acceptance Criteria

- Super Admin dapat membuat, mengubah, dan menghapus workspace.
- Super Admin dapat menunjuk satu Project Manager untuk satu workspace.
- Super Admin dapat mengganti Project Manager.
- Super Admin dapat menambah, mengubah, menghapus, mengaktifkan, dan menonaktifkan user.
- Project Manager hanya melihat workspace yang ditugaskan kepadanya.

---

## 5. Sprint 2 — Project Manager: Project dan Anggota Workspace

### Tujuan

Menyediakan fungsi operasional awal untuk Project Manager pada workspace yang ditugaskan.

### Scope

- Project Manager dashboard dasar.
- Melihat daftar workspace yang dikelola.
- Melihat detail workspace.
- Menambahkan Member ke workspace.
- Menghapus Member dari workspace.
- Melihat daftar Member workspace.
- CRUD project dalam workspace.
- Menentukan deadline project.
- Melihat daftar project pada workspace.

### Data Project

- Nama project
- Deskripsi project
- Deadline project
- Workspace asal

### Acceptance Criteria

- Project Manager hanya dapat mengelola workspace yang ditugaskan.
- Project Manager dapat menambahkan Member ke workspace.
- Project Manager dapat menghapus Member dari workspace.
- Project Manager dapat membuat, mengubah, dan menghapus project.
- Project harus selalu terhubung ke satu workspace.

---

## 6. Sprint 3 — Project Manager: Task Management

### Tujuan

Menyediakan fitur pembuatan dan penugasan task sebagai inti TaskFlow.

### Scope

- CRUD task dalam project.
- Assign task ke satu Member workspace.
- Menentukan prioritas task.
- Menentukan deadline task.
- Status awal task: `To Do`.
- Melihat detail task.
- Melihat daftar task per project.
- Menyimpan riwayat saat task dibuat dan diubah.
- Membuat notifikasi internal saat task ditugaskan.

### Data Task

- Judul
- Deskripsi
- Project
- Assigned Member
- Prioritas
- Deadline
- Status
- Lampiran opsional

### Acceptance Criteria

- Project Manager dapat membuat task dalam project.
- Project Manager dapat mengubah dan menghapus task.
- Project Manager dapat menugaskan task ke satu Member.
- Member yang dipilih harus berasal dari workspace project.
- Task baru otomatis berstatus `To Do`.
- Riwayat aktivitas tercatat saat task dibuat, diubah, dan ditugaskan.

---

## 7. Sprint 4 — Member: Pelaksanaan Task

### Tujuan

Menyediakan workflow utama bagi Member untuk mengerjakan task.

### Scope

- Member dashboard dasar.
- Melihat daftar task pribadi.
- Melihat detail task.
- Mengubah status dari `To Do` ke `In Progress`.
- Mengubah status dari `In Progress` ke `Review`.
- Upload lampiran hasil pekerjaan.
- Melihat riwayat status task.
- Notifikasi internal ke Project Manager saat task masuk `Review`.

### Aturan Upload MVP

- Maksimum ukuran file: 10 MB.
- Format awal: `pdf`, `doc`, `docx`, `zip`, `xlsx`, `xls`, `jpg`, `jpeg`, `png`.

### Acceptance Criteria

- Member hanya melihat task yang ditugaskan kepadanya.
- Member dapat melihat detail task.
- Member dapat mengubah status task sesuai alur.
- Member dapat upload lampiran hasil pekerjaan.
- Setiap perubahan status tercatat di `task_status_histories`.

---

## 8. Sprint 5 — Project Manager: Review dan Penyelesaian Task

### Tujuan

Menyediakan validasi hasil kerja oleh Project Manager.

### Scope

- Project Manager melihat task berstatus `Review`.
- Project Manager membuka lampiran hasil kerja.
- Project Manager menyetujui task menjadi `Done`.
- Project Manager mengembalikan task ke `In Progress` dengan catatan revisi sederhana.
- Project Manager membatalkan task menjadi `Cancelled`.
- Riwayat status dan catatan tersimpan.
- Notifikasi internal dikirim ke Member saat task disetujui, dikembalikan, atau dibatalkan.

### Acceptance Criteria

- Project Manager dapat melihat task yang menunggu review.
- Project Manager dapat mengubah status `Review` menjadi `Done`.
- Project Manager dapat mengembalikan task ke `In Progress` dengan catatan.
- Project Manager dapat membatalkan task sebelum `Done`.
- Task `Done` tidak dapat diedit oleh Member.

---

## 9. Sprint 6 — Dashboard, Monitoring, dan Laporan Dasar

### Tujuan

Menyediakan monitoring dasar sesuai kebutuhan BRD dan PRD.

### Scope Super Admin

- Melihat jumlah workspace.
- Melihat jumlah project.
- Melihat jumlah task.
- Melihat jumlah Project Manager.
- Melihat jumlah Member.
- Melihat statistik task berdasarkan status.
- Melihat daftar seluruh workspace, project, dan task.

### Scope Project Manager

- Melihat ringkasan workspace.
- Melihat jumlah project.
- Melihat jumlah task berdasarkan status.
- Melihat task mendekati deadline.
- Melihat progress project berdasarkan task `Done`.

### Scope Member

- Melihat jumlah task pribadi.
- Melihat task aktif.
- Melihat task selesai.
- Melihat deadline terdekat.

### Acceptance Criteria

- Dashboard menampilkan data sesuai role.
- Super Admin dapat melihat monitoring global.
- Project Manager dapat melihat monitoring workspace yang dikelola.
- Member dapat melihat monitoring task pribadi.
- Progress project dihitung dari jumlah task `Done` dibanding total task.

---

## 10. Sprint 7 — Notifikasi Internal dan Profil

### Tujuan

Menyediakan komunikasi dasar dalam aplikasi dan pengelolaan akun pribadi.

### Scope

- Notifikasi internal berbasis database.
- Bell icon atau menu notifikasi di dashboard.
- Mark notification as read.
- Notifikasi saat Project Manager ditunjuk.
- Notifikasi saat Member ditambahkan ke workspace.
- Notifikasi saat task ditugaskan.
- Notifikasi saat status task berubah.
- Notifikasi saat task selesai.
- Profil user.
- Ubah password.

### Acceptance Criteria

- User dapat melihat notifikasi internal miliknya.
- User dapat menandai notifikasi sebagai dibaca.
- Notifikasi dibuat otomatis pada aktivitas penting MVP.
- User dapat melihat dan mengubah profil.
- User dapat mengubah password.

---

## 11. Sprint 8 — QA, Responsiveness, dan Release MVP

### Tujuan

Menstabilkan MVP agar siap digunakan dan diuji pengguna.

### Scope

- Testing alur Super Admin.
- Testing alur Project Manager.
- Testing alur Member.
- Validasi RBAC.
- Validasi upload file.
- Validasi task history.
- Validasi dashboard dan laporan dasar.
- Responsive layout desktop dan mobile.
- Browser compatibility: Chrome, Firefox, Edge, Safari.
- Seeder data demo.
- Dokumentasi setup lokal.

### Acceptance Criteria

- Semua alur utama MVP berjalan tanpa error kritis.
- Setiap role hanya bisa mengakses fitur sesuai hak akses.
- Website responsif di desktop dan mobile.
- Waktu respon halaman utama tidak melebihi 3 detik pada kondisi normal.
- MVP siap dipakai untuk evaluasi awal organisasi.

---

## 12. Urutan Implementasi

```text
Sprint 0  ->  Sprint 1  ->  Sprint 2  ->  Sprint 3
Fondasi       Super Admin    PM Workspace   Task Management

Sprint 4  ->  Sprint 5  ->  Sprint 6  ->  Sprint 7  ->  Sprint 8
Member        Review PM      Dashboard      Notifikasi     QA Release
```

---

## 13. Traceability MVP

| Sprint | Acuan BRD | Acuan PRD | Output |
|---|---|---|---|
| Sprint 0 | BRD 6.1, 7.1, 8 | PRD 9, 11 | Auth, RBAC, struktur dasar |
| Sprint 1 | BRD 4.1, 4.4, 6.2, 6.3 | PRD 8.2 | User dan workspace |
| Sprint 2 | BRD 4.2, 4.5, 6.4, 6.5 | PRD 8.3 | Project dan anggota workspace |
| Sprint 3 | BRD 4.6, 6.6 | PRD 8.3.4 | Task management |
| Sprint 4 | BRD 4.3, 6.10 | PRD 8.4 | Member workflow |
| Sprint 5 | BRD 6.7, 6.13 | PRD 7.4, 8.3.5 | Review dan penyelesaian task |
| Sprint 6 | BRD 4.7, 6.8, 6.9, 6.11 | PRD 8.2.5, 8.3.5 | Dashboard dan laporan |
| Sprint 7 | BRD 4.8, 6.12 | PRD 8.5, 9.10 | Notifikasi internal dan profil |
| Sprint 8 | BRD 7, 14 | PRD 10 | QA dan release MVP |

---

## 14. Definition of Done MVP

- Super Admin dapat mengelola user dan workspace.
- Super Admin dapat menunjuk Project Manager untuk workspace.
- Project Manager dapat mengelola project dan anggota workspace.
- Project Manager dapat membuat dan menugaskan task.
- Member dapat melihat, mengerjakan, memperbarui status, dan upload lampiran task.
- Project Manager dapat review dan menyelesaikan task.
- Status task berjalan sesuai alur MVP.
- Riwayat perubahan status task tercatat.
- Dashboard dasar tersedia untuk semua role.
- Notifikasi internal dasar berjalan.
- Aplikasi responsif dan aman berdasarkan RBAC.
