# TaskFlow — Business Requirement Document (BRD)

**Sistem Manajemen Tugas Kolaboratif**

---

# 1. Ringkasan Eksekutif

Dokumen Business Requirement Document (BRD) ini menguraikan kebutuhan bisnis dan spesifikasi sistem untuk pengembangan aplikasi TaskFlow. Aplikasi ini merupakan platform berbasis web yang dirancang untuk mengoptimalkan proses distribusi, pengerjaan, dan peninjauan tugas secara terstruktur dalam organisasi yang menerapkan hierarki tiga tingkat, yaitu Super Admin, Project Manager, dan Anggota.

Sistem ini dikembangkan untuk mempermudah setiap peran dalam menjalankan tanggung jawab masing-masing. Alur kerja dimulai dari pembuatan tugas oleh Super Admin, distribusi kepada Project Manager, penugasan oleh Project Manager kepada Anggota, hingga pengerjaan dan penyerahan hasil oleh Anggota. Seluruh rangkaian ini dilengkapi dengan mekanisme peninjauan, revisi, dan persetujuan akhir yang terdokumentasi secara menyeluruh. Selain menjaga kelancaran operasional tim, sistem ini juga mendukung Super Admin dalam memantau kinerja Project Manager, mengelola akun pengguna, serta memastikan akuntabilitas setiap tugas melalui rekam jejak riwayat yang lengkap. Melalui implementasi sistem ini, proses pengelolaan tugas yang sebelumnya berjalan manual melalui WhatsApp, email, atau percakapan langsung dapat dialihkan ke dalam satu platform terintegrasi demi meningkatkan transparansi, efisiensi, dan akuntabilitas organisasi.

# 2. Latar Belakang dan Justifikasi Bisnis

## 2.1 Konteks

TaskFlow adalah sistem manajemen tugas kolaboratif yang melayani kebutuhan organisasi dalam mendistribusikan pekerjaan dari pimpinan (Super Admin) hingga ke pelaksana (Anggota) melalui jalur hierarki yang transparan. Dalam struktur organisasi ini, terdapat tiga peran utama: Super Admin sebagai pengelola platform dan pembuat tugas, Project Manager sebagai pemimpin tim, dan Anggota sebagai pelaksana tugas. Saat ini, proses distribusi tugas masih mengandalkan WhatsApp, email, atau percakapan langsung. Akibatnya, pengelolaan tugas, dokumentasi berkas hasil kerja, dan pembaruan status pengerjaan masih bersifat manual dan belum terarsip dengan baik.

## 2.2 Permasalahan

1.  Data tugas dan berkas hasil kerja tersebar di berbagai percakapan WhatsApp serta email, sehingga menyulitkan proses pelacakan.

2.  Super Admin mengalami kesulitan dalam memantau status tugas yang sedang berjalan secara langsung, sehingga harus melakukan konfirmasi berulang kali.

3.  Sistem lama tidak memiliki catatan riwayat yang mencatat pihak yang mengubah status, waktu perubahan, serta alasan perubahan, sehingga tingkat akuntabilitas operasional menjadi rendah.

4.  Proses peninjauan dan revisi antara Project Manager dan Anggota tidak terdokumentasi, yang sering kali memicu pengulangan pekerjaan yang tidak perlu.

5.  Organisasi belum menyediakan media terpusat untuk mengelola tugas, anggota tim, serta aktivitas operasional harian.

6.  Project Manager sering menghadapi beban kerja berlebih karena ketiadaan informasi mengenai kapasitas kerja sebelum penugasan dilakukan.

7.  Organisasi tidak memiliki mekanisme pengalihan otomatis jika Project Manager berhalangan hadir karena cuti atau sakit, sehingga alur penyelesaian tugas sering terhenti.

## 2.3 Solusi yang Diusulkan

Perusahaan akan membangun sistem aplikasi berbasis web untuk mendukung proses distribusi dan pengerjaan tugas secara terintegrasi dengan fitur sebagai berikut:

1.  Sistem menyediakan fitur pendaftaran dan akses masuk bagi pengguna yang dibagi ke dalam tiga tingkat peran (Super Admin, Project Manager, Anggota).

2.  Super Admin dapat membuat tugas dan mendistribusikannya langsung kepada Project Manager.

3.  Super Admin dapat menunjuk Project Manager dengan mempertimbangkan indikator beban kerja yang terkini.

4.  Project Manager dapat membagikan penugasan kerja kepada Anggota di dalam timnya.

5.  Anggota dapat mengunggah berkas hasil kerja dengan batasan ukuran maksimal 10 MB dan format yang ditentukan.

6.  Sistem memfasilitasi proses peninjauan dan revisi tugas antara Project Manager dan Anggota dengan batasan maksimal 3 kali revisi.

7.  Sistem melakukan eskalasi otomatis ke Super Admin apabila Project Manager tidak memberikan respons peninjauan dalam kurun waktu 2 x 24 jam.

8.  Sistem menyediakan fitur arbitrase oleh Super Admin jika proses revisi telah mencapai batas maksimal namun belum mendapatkan persetujuan.

9.  Super Admin memiliki kewenangan untuk memberikan persetujuan akhir.

10. Sistem melakukan pencatatan rekam jejak secara menyeluruh untuk setiap perubahan status tugas.

11. Sistem menyediakan kotak masuk notifikasi internal di dalam aplikasi sebagai solusi cadangan jika notifikasi eksternal mengalami kegagalan.

12. Sistem menampilkan halaman pemantauan kinerja dan beban kerja Project Manager sebagai bahan evaluasi objektif.

13. Sistem mendukung pengiriman notifikasi WhatsApp dan email secara otomatis.

# 3. Tujuan Bisnis

1.  Menyediakan platform distribusi tugas yang terstruktur, transparan, serta mudah dioperasikan.

2.  Membantu pengelolaan tugas dan penyimpanan berkas hasil kerja secara terpusat serta terdokumentasi.

3.  Mempermudah setiap pemegang peran dalam memantau status tugas sesuai dengan porsi tanggung jawabnya.

4.  Meningkatkan akuntabilitas operasional melalui pencatatan rekam jejak pada setiap perubahan status tugas.

5.  Mendorong efisiensi operasional organisasi melalui tata kelola data dan alur kerja yang lebih rapi.

6.  Membantu Super Admin dalam memantau aktivitas serta kinerja Project Manager secara objektif.

7.  Mencegah terjadinya kemacetan proses dalam peninjauan tugas melalui penerapan batas revisi dan jalur arbitrase.

8.  Memastikan kelancaran alur tugas organisasi meskipun terdapat keterbatasan ketersediaan dari Project Manager maupun Super Admin.

# 4. Ruang Lingkup

## 4.1 Dashboard Super Admin

- Membuat tugas baru dengan mengisi parameter judul, deskripsi, tingkat prioritas, tenggat waktu, serta menyertakan rekomendasi Project Manager (opsional).

- Mengirimkan tugas yang telah dibuat kepada Project Manager.

- Melihat semua tugas yang telah dibuat dengan fitur penyaring status.

- Memantau beban kerja setiap Project Manager, yang mencakup jumlah tugas aktif, tugas menunggu peninjauan, dan tugas yang terlambat, sebelum melakukan penunjukan.

- Menunjuk Project Manager untuk setiap tugas, baik dengan menyetujui rekomendasi maupun memilih secara mandiri.

- Mengakses daftar tugas yang masuk ke tahap arbitrase akibat revisi yang telah mencapai batas maksimal.

- Mengambil keputusan arbitrase, baik berupa persetujuan tugas maupun instruksi revisi tambahan.

- Memindahkan tanggung jawab tugas dari satu Project Manager ke Project Manager lain jika Project Manager utama berhalangan atau tidak memberikan respons.

- Menunjuk pengganti Project Manager untuk menggantikan peran Project Manager utama sementara waktu.

- Memberikan persetujuan akhir untuk tugas yang telah disetujui Project Manager.

- Mengelola akun pengguna, termasuk melakukan pengaktifan atau penangguhan akun.

- Memantau kinerja Project Manager melalui metrik total tugas, tugas selesai, tugas terlambat, dan tingkat penyelesaian.

- Memantau daftar keseluruhan tugas yang pernah dibuat beserta status terkininya.

- Melihat riwayat perubahan status untuk setiap tugas terkait.

- Memantau penghitung revisi apabila tugas tersebut sedang dalam proses perbaikan.

## 4.2 Dashboard Project Manager

- Menerima penugasan dari Super Admin.

- Membagikan dan menunjuk Anggota tim untuk mengerjakan tugas tersebut.

- Melihat beban kerja tim berdasarkan jumlah tugas yang sedang ditangani oleh masing-masing anggota.

- Melakukan peninjauan terhadap hasil kerja Anggota dengan memeriksa berkas yang diunggah.

- Memberikan persetujuan yang akan mengubah status tugas menjadi menunggu persetujuan Super Admin.

- Melakukan penolakan disertai catatan perbaikan yang akan mengubah status tugas menjadi tahap revisi.

- Memantau penghitung revisi serta menerima peringatan dini saat jumlah revisi mendekati batas maksimal.

- Memantau perkembangan pengerjaan tugas dari seluruh anggota timnya.

## 4.3 Dashboard Anggota

- Melihat daftar tugas yang dialokasikan kepada dirinya.

- Mengerjakan tugas dan mengunggah berkas hasil kerja dengan kapasitas maksimal 10 MB.

- Menerima catatan revisi dari Project Manager dan mengunggah kembali berkas yang telah diperbaiki.

- Melihat nilai penghitung revisi serta sisa batas maksimal revisi yang tersedia.

## 4.4 Pengelolaan Tugas

- Penyimpanan data karakteristik tugas, meliputi judul, deskripsi, prioritas, tenggat waktu, dan berkas.

- Pengelolaan status tugas yang terbagi ke dalam 9 status spesifik, termasuk pembatalan.

- Penerapan batas maksimal revisi sebanyak 3 kali dan penghitungan revisi secara otomatis oleh sistem.

- Mekanisme eskalasi otomatis ke Super Admin jika Project Manager tidak merespons dalam waktu 2 x 24 jam.

- Proses arbitrase oleh Super Admin ketika batas perbaikan telah habis.

- Pencatatan riwayat perubahan status secara permanen.

## 4.5 Pelaporan Dasar

- Ringkasan statistik jumlah tugas berdasarkan status.

- Laporan riwayat perubahan status tugas.

- Laporan kinerja Project Manager, meliputi total tugas, tugas selesai, tugas terlambat, dan tingkat penyelesaian.

- Laporan analisis beban kerja Project Manager, meliputi tugas aktif, tugas menunggu peninjauan, dan tugas terlambat.

- Laporan rekapitulasi tugas yang masuk ke dalam tahap arbitrase.

## 4.6 Ruang Lingkup yang Tidak Termasuk

- Pengembangan aplikasi bergerak berbasis Android atau iOS secara langsung.

- Integrasi dengan sistem pembayaran daring.

- Sistem notifikasi langsung menggunakan saluran khusus. Notifikasi dikirimkan secara tidak langsung melalui WhatsApp, email, dan kotak masuk internal.

- Pengelolaan proyek dengan banyak ruang kerja untuk satu Project Manager. Pada versi produk minimum, satu Project Manager hanya terikat pada satu ruang kerja.

- Fitur percakapan langsung atau ruang diskusi langsung antar pengguna di dalam sistem.

# 5. Pemangku Kepentingan dan Pengguna

Super Admin:

Membuat instruksi tugas, mendistribusikan tugas kepada Project Manager, memberikan persetujuan akhir, melaksanakan keputusan arbitrase, mengalihkan tugas antar-Project Manager, mengelola akun pengguna, dan mengevaluasi kinerja Project Manager.

Project Manager:

Menerima tugas dari Super Admin, menunjuk Anggota pelaksana, melakukan peninjauan hasil kerja (menyetujui atau menolak dengan catatan), memantau kapasitas tim, dan memastikan tugas selesai sesuai target.

Anggota:

Melaksanakan tugas teknis, mengunggah berkas hasil kerja, merespons dan memperbaiki catatan revisi dari Project Manager, serta menyelesaikan pekerjaan tepat waktu.

# 6. Persyaratan Fungsional

## 6.1 Situs Web Pengguna (Semua Peran)

- Sistem harus menyediakan fitur pendaftaran akun dengan pilihan peran yang sesuai.

- Sistem harus menyediakan fitur masuk dan keluar yang aman.

- Sistem harus menampilkan halaman utama yang dinamis, disesuaikan dengan peran masing-masing melalui bilah sisi navigasi.

- Sistem harus menampilkan daftar tugas yang menjadi tanggung jawab langsung pengguna tersebut.

- Sistem harus menampilkan informasi detail tugas secara lengkap.

- Sistem harus menyediakan kotak masuk notifikasi internal yang ditandai dengan ikon lonceng pada antarmuka pengguna.

## 6.2 Manajemen Tugas

- Sistem harus mampu membuat nomor atau kode tugas secara otomatis.

- Sistem harus menjamin keamanan penyimpanan data tugas serta berkas lampirannya.

- Sistem harus mengatur perubahan status tugas berdasarkan alur bisnis yang telah ditetapkan.

- Sistem harus mencatat setiap riwayat perubahan status tugas ke dalam rekam jejak dengan merekam data pelaku, waktu modifikasi, status asal, status tujuan, serta catatan pendukung.

Status Tugas (9 Status):

Status draft:

Tugas baru berhasil dibuat oleh Super Admin dan menunggu didistribusikan. Peran yang bertindak: Super Admin.

Status assigned_pm:

Super Admin telah menunjuk Project Manager penanggung jawab. Peran yang bertindak: Super Admin.

Status assigned_member:

Project Manager telah mengalokasikan tugas kepada Anggota tim. Peran yang bertindak: Project Manager.

Status pending_pm:

Anggota telah mengunggah hasil kerja dan menunggu proses peninjauan dari Project Manager. Peran yang bertindak: Anggota.

Status revision:

Anggota melakukan perbaikan tugas berdasarkan catatan peninjauan dan mengirimkannya kembali. Peran yang bertindak: Anggota.

Status pending_arbitration:

Akumulasi revisi telah mencapai batas maksimal 3 kali sehingga menunggu keputusan Super Admin. Peran yang bertindak: Super Admin.

Status pending_admin:

Project Manager menyetujui hasil kerja dan meneruskan tugas ke Super Admin untuk persetujuan akhir. Peran yang bertindak: Super Admin.

Status done:

Super Admin memberikan persetujuan akhir dan tugas dinyatakan selesai. Peran yang bertindak: Super Admin.

Status cancelled:

Tugas dibatalkan dan dihentikan dari alur kerja sebelum mencapai tahap selesai. Peran yang bertindak: Super Admin.

## 6.3 Dashboard Super Admin

Kode F-01:

Super Admin dapat membuat formulir tugas baru (judul, deskripsi, prioritas, tenggat waktu). Prioritas: Wajib.

Kode F-02:

Super Admin dapat menyertakan rekomendasi Project Manager pilihan sewaktu menyusun tugas (opsional). Prioritas: Wajib.

Kode F-03:

Super Admin dapat mengirimkan draf tugas secara langsung kepada Project Manager. Prioritas: Wajib.

Kode F-04:

Super Admin dapat melihat daftar seluruh tugas yang pernah dibuat beserta status terbarunya. Prioritas: Wajib.

Kode F-05:

Super Admin dapat melacak riwayat perubahan status pada tugas terkait. Prioritas: Wajib.

Kode F-06:

Super Admin dapat melihat indikator penghitung revisi pada tugas yang sedang diperbaiki. Prioritas: Wajib.

Kode F-07:

Super Admin dapat memantau menu semua tugas yang menampilkan seluruh tugas dengan filter status. Prioritas: Wajib.

Kode F-08:

Super Admin dapat menganalisis beban kerja harian setiap Project Manager sebelum menetapkan penunjukan tugas. Prioritas: Wajib.

Kode F-09:

Super Admin dapat menetapkan Project Manager untuk memimpin tugas (menyetujui rekomendasi atau memilih PM lain). Prioritas: Wajib.

Kode F-10:

Super Admin dapat memberikan persetujuan akhir pada tugas yang berstatus pending_admin. Prioritas: Wajib.

Kode F-11:

Super Admin dapat mengakses daftar tugas yang dialihkan ke menu arbitrase (pending_arbitration). Prioritas: Wajib.

Kode F-12:

Super Admin dapat menetapkan keputusan arbitrase (mengubah status menjadi pending_admin atau mengembalikannya ke revision). Prioritas: Wajib.

Kode F-13:

Super Admin dapat melakukan pemindahan tanggung jawab tugas antar-Project Manager. Prioritas: Wajib.

Kode F-14:

Super Admin dapat menunjuk pengganti Project Manager untuk menggantikan posisi Project Manager utama yang sedang berhalangan. Prioritas: Wajib.

Kode F-15:

Super Admin dapat melakukan pengelolaan akun pengguna seperti proses aktivasi atau penangguhan. Prioritas: Wajib.

Kode F-16:

Super Admin dapat menganalisis laporan kinerja Project Manager secara berkala. Prioritas: Wajib.

## 6.4 Dashboard Project Manager

Kode F-17:

Project Manager dapat menerima pemberitahuan alokasi tugas baru dari Super Admin. Prioritas: Wajib.

Kode F-18:

Project Manager dapat mendelegasikan tugas tersebut kepada Anggota di dalam kelompok kerjanya. Prioritas: Wajib.

Kode F-19:

Project Manager dapat memantau persebaran kapasitas beban kerja dari seluruh anggota timnya. Prioritas: Wajib.

Kode F-20:

Project Manager dapat melakukan peninjauan mendalam terhadap berkas hasil kerja yang diserahkan Anggota. Prioritas: Wajib.

Kode F-21:

Project Manager dapat mengeklik tombol persetujuan untuk mengubah status ke pending_admin. Prioritas: Wajib.

Kode F-22:

Project Manager dapat mengeklik tombol penolakan dan wajib menyertakan catatan untuk mengubah status ke revision. Prioritas: Wajib.

Kode F-23:

Project Manager dapat memantau angka penghitung revisi untuk mengantisipasi batas maksimal penolakan. Prioritas: Wajib.

Kode F-24:

Project Manager dapat memantau seluruh perkembangan pengerjaan tugas di dalam timnya. Prioritas: Wajib.

## 6.5 Dashboard Anggota

Kode F-25:

Anggota dapat melihat bagian khusus berisi daftar tugas yang didelegasikan kepadanya. Prioritas: Wajib.

Kode F-26:

Anggota dapat mengunggah berkas lampiran hasil pengerjaan dengan ketentuan ukuran berkas maksimal 10 MB. Prioritas: Wajib.

Kode F-27:

Anggota dapat mengirimkan hasil pekerjaan kepada Project Manager sehingga status berubah menjadi pending_pm. Prioritas: Wajib.

Kode F-28:

Anggota dapat membaca umpan balik berupa catatan perbaikan dari Project Manager jika tugas ditolak. Prioritas: Wajib.

Kode F-29:

Anggota dapat mengunggah kembali dokumen perbaikan yang secara otomatis mengembalikan status ke pending_pm. Prioritas: Wajib.

Kode F-30:

Anggota dapat melihat jumlah kuota revisi yang telah terpakai melalui penghitung revisi. Prioritas: Wajib.

## 6.6 Pelaporan

Kode F-31:

Sistem harus mampu menampilkan laporan rekam jejak yang lengkap per nomor tugas. Prioritas: Wajib.

Kode F-32:

Sistem harus menyusun laporan berkala kinerja Project Manager bagi Super Admin. Prioritas: Wajib.

Kode F-33:

Sistem harus menyusun tampilan data beban kerja Project Manager berdasarkan status tugas aktif dan tenggat waktu. Prioritas: Wajib.

Kode F-34:

Sistem harus menyediakan laporan rekapitulasi data tugas yang sempat masuk ke tahap arbitrase. Prioritas: Wajib.

## 6.7 Aturan Bisnis Tambahan

BR-01 (Batas Maksimal Revisi):

Setiap instruksi tugas dibatasi hanya dapat melalui proses revisi maksimal sebanyak 3 kali. Jika Project Manager melakukan penolakan untuk yang keempat kalinya, sistem secara otomatis akan mengunci interaksi mereka dan menaikkan status tugas tersebut ke tingkat Super Admin dengan status pending_arbitration.

BR-02 (Eskalasi Kelalaian PM):

Jika tugas yang berstatus pending_pm tidak mendapatkan tindakan peninjauan atau respons dari Project Manager dalam kurun waktu 2 x 24 jam, sistem akan memicu peringatan eskalasi otomatis kepada Super Admin. Super Admin memiliki hak penuh untuk memindahkan tugas tersebut ke Project Manager lain atau mengambil alih keputusan secara langsung.

BR-03 (Hak Rekomendasi Super Admin):

Super Admin diberikan hak opsional untuk merekomendasikan nama Project Manager tertentu saat membuat draf tugas. Keputusan final penunjukan Project Manager tetap berada di bawah wewenang mutlak Super Admin.

BR-04 (Delegasi Pengganti PM):

Super Admin berwenang menetapkan seorang pengganti Project Manager apabila Project Manager utama dinyatakan tidak aktif (akibat sakit, cuti, atau mutasi kerja). Pengganti tersebut akan mewarisi seluruh hak akses peninjauan serta persetujuan tugas.

BR-05 (Validasi Beban Kerja):

Sistem mewajibkan Super Admin untuk membuka dan memeriksa bagan beban kerja Project Manager terlebih dahulu sebelum menugaskan suatu pekerjaan baru guna menghindari ketimpangan produktivitas.

BR-06 (Mekanisme Kotak Masuk Internal):

Di samping mengandalkan WhatsApp dan email, sistem wajib menyimpan salinan notifikasi ke dalam basis data internal (inbox berikon lonceng) sebagai perlindungan jika pihak ketiga mengalami gangguan.

BR-07 (Kontinuitas Revisi saat Mutasi):

Apabila terjadi pemindahan tanggung jawab tugas ke Project Manager lain atau pengambilalihan oleh pengganti Project Manager, penghitung revisi tidak diatur ulang, melainkan dilanjutkan. Hal ini menjamin batas maksimal 3 kali revisi tetap berlaku.

BR-08 (Mekanisme Pembatalan Tugas):

Super Admin memiliki wewenang untuk membatalkan tugas kapan saja sebelum tugas tersebut mencapai status selesai (done). Tugas yang dibatalkan akan berubah statusnya menjadi cancelled dan dihentikan dari sisa alur kerja yang ada.

# 7. Persyaratan Non-Fungsional (Kualitatif)

Keamanan Data:

Perlindungan data tugas, dokumen kerja, dan riwayat aktivitas dikendalikan secara ketat melalui pengendalian akses berdasarkan peran. Untuk menjaga keutuhan data, Super Admin hanya diberikan hak baca tanpa kewenangan mengubah konten tugas yang sedang berjalan.

Reliabilitas:

Sistem menjamin ketersediaan data dan penyimpanan berkas secara aman. Kebijakan pencadangan data pada basis data wajib dijalankan secara otomatis setiap hari.

Kemudahan Penggunaan:

Rancangan antarmuka pengguna dioptimalkan dengan pendekatan minimalis menggunakan navigasi samping yang disesuaikan per peran untuk mengurangi kebutuhan pelatihan intensif bagi pengguna baru.

Kinerja Sistem:

Sistem harus mampu memuat serta menampilkan tampilan data tugas, status, dan riwayat rekam jejak dengan kecepatan waktu respons kurang dari 3 detik untuk kapasitas hingga 1.000 tugas aktif.

Pemeliharaan:

Pengelolaan data pengguna, perubahan parameter status, dan penyesuaian konfigurasi dasar harus dapat dilakukan langsung oleh Super Admin melalui panel administrasi tanpa perlu mengubah kode program.

Kompatibilitas:

Sistem harus responsif dan dapat menyesuaikan diri saat diakses melalui peramban modern seperti Chrome, Firefox, Edge, atau Safari, baik pada perangkat komputer meja maupun telepon pintar.

Rekam Jejak:

Seluruh perubahan status tugas dipastikan tersimpan secara permanen pada tabel catatan sistem, tidak dapat diubah, dan mencantumkan identitas pelaku secara lengkap.

# 8. Arsitektur Tingkat Tinggi

1.  Komponen Belakang (Back-end): Laravel 12 (berjalan pada PHP 8.2 ke atas)

2.  Panel Administrasi (Admin UI): Filament (khusus untuk Super Admin)

3.  Sistem Manajemen Basis Data (Database): MariaDB atau MySQL

4.  Komponen Depan (Front-end): Blade, Tailwind CSS, Alpine.js, Livewire

5.  Peladen Web (Web Server): Nginx

6.  Teknologi Wadah (Containerization): Docker

7.  Manajemen Repositori (Version Control): Git dan GitHub

8.  Alamat Protokol Lokal (URL Lokal): <https://taskflow.test>

9.  Sistem Notifikasi: WhatsApp (Fonnte API), Email (SMTP), dan Kotak Masuk Internal berbasis Basis Data

# 9. Model Data (Ringkas)

### Tabel `users`

```
id, name, email, password, nomor_whatsapp,
role (super_admin, pm, member), is_active,
created_at, updated_at
```

### Tabel `workspaces`

```
id, pm_id (user_id), name, description,
created_at, updated_at
```

### Tabel `workspace_members`

```
id, workspace_id, user_id, joined_at
```

### Tabel `tasks`

```
id, created_by (user_id super_admin), assigned_pm (user_id),
assigned_member (user_id), workspace_id, title, description,
priority (low/medium/high), deadline,
status (draft/assigned_pm/assigned_member/pending_pm/revision/
        pending_arbitration/pending_admin/done/cancelled),
file_path, file_original_name, review_notes,
revision_counter (integer, default 0),
max_revision_limit (integer, default 3),
created_at, updated_at
```

### Tabel `task_status_histories`

```
id, task_id, from_status, to_status,
changed_by (user_id), notes, created_at
```

### Tabel `notifications`

```
id, user_id, task_id, type (whatsapp/email/inbox),
subject, message, status (pending/sent/failed/read),
sent_at, created_at
```

# 10. Alur Proses Bisnis

Berikut merupakan tata urutan logis dari siklus distribusi, pelaksanaan, peninjauan, hingga penyelesaian tugas di dalam sistem TaskFlow:

1.  Super Admin masuk ke dalam sistem, menyusun data tugas baru (judul, deskripsi, tingkat prioritas, tenggat waktu), serta dapat menyertakan rekomendasi nama Project Manager (opsional).

2.  Super Admin mengirimkan tugas tersebut, yang secara otomatis mengubah status tugas menjadi draft.

3.  Super Admin mengakses halaman utama semua tugas, memeriksa rincian pekerjaan, dan menganalisis laporan beban kerja harian dari masing-masing Project Manager yang tersedia.

4.  Super Admin menunjuk Project Manager penanggung jawab (bisa menyetujui rekomendasi ataupun mengalokasikannya ke Project Manager lain), sehingga status bergerak menjadi assigned_pm.

5.  Project Manager menerima pemberitahuan tugas, memilih Anggota di dalam lingkup kerja kelompoknya, lalu mendelegasikan tugas tersebut ke Anggota tertentu. Status berubah menjadi assigned_member.

6.  Anggota membaca instruksi pada menu tugasnya, mulai melakukan pengerjaan teknis, mengunggah dokumen hasil kerja, lalu mengirimkan pekerjaan kepada atasannya. Status bergeser menjadi pending_pm.

7.  Project Manager melakukan proses peninjauan berkas kerja Anggota dengan opsi keputusan sebagai berikut:

    1.  Pilihan Setuju: Mengubah status tugas menjadi pending_admin untuk antrean persetujuan akhir oleh Super Admin.

    2.  Pilihan Tolak: Project Manager wajib mengisi catatan perbaikan, yang akan mengubah status tugas kembali menjadi revision, serta memicu sistem untuk menambah nilai penghitung revisi sebanyak 1 poin.

    3.  Kelalaian Tindakan: Apabila Project Manager tidak merespons dan tidak memberikan keputusan dalam tempo 2 x 24 jam, sistem akan mengirimkan peringatan eskalasi kepada Super Admin. Super Admin memiliki hak penuh untuk memindahkan tugas ke Project Manager baru atau mengambil alih keputusan.

8.  Anggota menelaah masukan revisi dari Project Manager, melakukan perbaikan, lalu mengunggah kembali berkas yang baru. Tindakan ini mengembalikan status tugas menjadi pending_pm.

9.  Ketentuan Batas Kritis: Jika proses penolakan oleh Project Manager mencapai batas 3 kali dan Project Manager kembali memilih opsi Tolak pada putaran berikutnya, sistem akan mengunci proses interaksi mereka dan secara otomatis mengubah status tugas tersebut menjadi pending_arbitration ke tingkat Super Admin.

10. Super Admin melakukan arbitrase dengan meninjau riwayat perbaikan terdahulu, dan menetapkan keputusan akhir sebagai berikut:

    1.  Arbitrase Menyetujui: Tugas dianggap sah dan selesai, status diubah menjadi pending_admin lalu diteruskan ke done.

    2.  Arbitrase Menolak: Tugas dinilai masih kurang, status dikembalikan ke revision disertai instruksi khusus langsung dari Super Admin kepada Anggota.

11. Super Admin membuka daftar antrean pending_admin, melakukan verifikasi akhir, dan memberikan persetujuan akhir yang mengubah status mutlak menjadi done.

12. Tugas secara resmi dinyatakan selesai dan diarsipkan. Rekam jejak menyimpan seluruh data kronologi pergerakan status dari awal hingga akhir siklus.

13. Layanan notifikasi (WhatsApp, email, dan kotak masuk aplikasi) dikirimkan secara otomatis kepada para aktor terkait di setiap perubahan status tugas.

# 11. Teknologi

1.  Komponen Belakang (Back-end): Laravel 12 (berjalan pada PHP 8.2 ke atas)

2.  Panel Administrasi (Admin UI): Filament (khusus untuk Super Admin)

3.  Sistem Manajemen Basis Data (Database): MariaDB atau MySQL

4.  Komponen Depan (Front-end): Blade, Tailwind CSS, Alpine.js, Livewire

5.  Peladen Web (Web Server): Nginx

6.  Teknologi Wadah (Containerization): Docker Compose (kombinasi lingkungan PHP-FPM, Nginx, dan MariaDB)

7.  Manajemen Repositori (Version Control): Git dan GitHub

8.  Lingkungan Pengembangan (Development Environment): WSL (Windows Subsystem for Linux) dan Visual Studio Code

9.  Sistem Notifikasi: WhatsApp melalui Fonnte API, Email melalui SMTP, dan Kotak Masuk Internal melalui tabel Basis Data

# 12. Asumsi

- Seluruh pengguna memiliki koneksi internet yang stabil untuk mengakses aplikasi situs web.

- Super Admin bertindak sebagai pengendali utama ekosistem platform sekaligus pemegang keputusan tertinggi.

- Setiap Project Manager memimpin satu ruang kerja dan bertanggung jawab penuh atas produktivitas anggotanya.

- Setiap Anggota terikat secara eksklusif pada satu kelompok kerja dalam satu waktu.

- Berkas dokumen yang diunggah oleh Anggota menggunakan format yang diizinkan (seperti .pdf, .doc, .docx, .zip, .xlsx, .jpg, .png) dengan ukuran tidak melampaui batas 10 MB.

- Proses peninjauan oleh Project Manager berjalan secara ideal dan tidak melampaui batas toleransi kelalaian waktu 2 x 24 jam setelah penyerahan oleh Anggota.

- Layanan pengiriman pesan informasi (WhatsApp dan email) berjalan di latar belakang secara tidak langsung sehingga tidak membebani kecepatan transaksi data utama aplikasi.

- Kuota toleransi revisi dipatok sebanyak 3 kali demi menjaga efisiensi ritme kerja.

- Project Manager dianggap berhalangan atau tidak tersedia apabila terbukti melewati batas waktu peninjauan 2 x 24 jam.

- Super Admin diposisikan sebagai pihak penengah tunggal yang objektif saat terjadi kebuntuan revisi.

# 13. Risiko dan Mitigasi

Risiko: Terjadinya kebuntuan komunikasi antara Project Manager dan Anggota dalam menyelesaikan revisi berkas kerja.

Mitigasi: Sistem menerapkan aturan pembatasan revisi maksimal 3 kali. Jika batas terlampaui, pengendalian dialihkan ke status pending_arbitration agar Super Admin dapat memberikan keputusan final.

Risiko: Project Manager lambat merespons atau tidak melakukan peninjauan karena cuti, sakit, atau beban kerja padat.

Mitigasi: Sistem mengaktifkan peringatan eskalasi otomatis berdurasi 2 x 24 jam. Super Admin diberikan hak untuk memindahkan kepemimpinan tugas ke Project Manager lain atau menunjuk pengganti Project Manager.

Risiko: Terhentinya proses penugasan akibat Super Admin sedang tidak aktif atau berhalangan.

Mitigasi: Super Admin dapat menyisipkan rekomendasi nama Project Manager saat menyusun draf untuk mempercepat proses ketika berhalangan.

Risiko: Super Admin tidak objektif atau lalai memantau kapasitas Project Manager sehingga memicu penumpukan tugas.

Mitigasi: Sistem mewajibkan Super Admin untuk mengakses tampilan beban kerja Project Manager terlebih dahulu sebelum tombol penunjukan Project Manager dapat berfungsi.

Risiko: Kegagalan pengiriman pesan informasi akibat gangguan pada penghubung pihak ketiga atau kuota pengiriman habis.

Mitigasi: Sistem menyediakan kotak masuk notifikasi internal di dalam aplikasi sebagai solusi cadangan sehingga pesan tetap terbaca saat pengguna masuk ke akun mereka.

Risiko: Super Admin kehilangan kendali terhadap tugas-tugas penting yang tertahan di fase perbaikan berkelanjutan.

Mitigasi: Halaman utama Super Admin menampilkan indikator angka penghitung revisi serta status terkini dari setiap tugas secara terbuka.

Risiko: Berkas tugas lampiran yang dikirimkan oleh Anggota mengalami kerusakan atau tidak dapat dibuka.

Mitigasi: Project Manager wajib melakukan pemeriksaan fisik dokumen sebelum menekan tombol persetujuan. Jika dokumen rusak, Project Manager dapat meminta unggah ulang menggunakan opsi Tolak beserta catatan penjelasannya.

Risiko: Hilangnya data transaksional penting akibat gangguan pada infrastruktur peladen atau basis data.

Mitigasi: Seluruh data transaksional diamankan di dalam basis data terpusat dan dijadwalkan untuk melakukan prosedur pencadangan otomatis harian.

Risiko: Terjadinya lonjakan volume tugas aktif yang berpotensi menurunkan kinerja respons aplikasi.

Mitigasi: Struktur sistem dirancang untuk mendukung pengelolaan antrean tugas secara sistematis melalui pembagian skala prioritas dan pengendalian beban kerja.

# 14. Kriteria Penerimaan

Nomor 1:

Super Admin dapat masuk ke sistem dan menyusun instruksi tugas baru disertai opsi rekomendasi Project Manager. Status: Wajib.

Nomor 2:

Super Admin dapat melihat menu semua tugas dan menetapkan Project Manager berdasarkan pertimbangan beban kerja. Status: Wajib.

Nomor 3:

Project Manager dapat mendelegasikan tugas yang diterimanya kepada Anggota tim pelaksana. Status: Wajib.

Nomor 4:

Anggota dapat mengidentifikasi tugas, mengunggah berkas lampiran, dan mengirimkannya ke tahapan berikutnya. Status: Wajib.

Nomor 5:

Project Manager dapat mengeksekusi tombol persetujuan atau penolakan hasil kerja Anggota. Status: Wajib.

Nomor 6:

Sistem secara otomatis menambahkan akumulasi poin pada penghitung revisi setiap kali Project Manager memilih opsi Tolak. Status: Wajib.

Nomor 7:

Sistem otomatis mengunci tugas dan menaikkan status menjadi pending_arbitration ke akun Super Admin jika revisi menyentuh batas 3 kali. Status: Wajib.

Nomor 8:

Super Admin dapat mengeksekusi keputusan arbitrase pada menu tugas berstatus pending_arbitration. Status: Wajib.

Nomor 9:

Sistem otomatis mengirimkan peringatan eskalasi kepada Super Admin jika Project Manager membiarkan status pending_pm selama 2 x 24 jam. Status: Wajib.

Nomor 10:

Super Admin memiliki fungsi untuk memindahkan kepemimpinan tugas dari satu Project Manager ke Project Manager lainnya. Status: Wajib.

Nomor 11:

Super Admin dapat menunjuk seorang pengganti Project Manager untuk mengambil alih hak akses Project Manager utama sementara waktu. Status: Wajib.

Nomor 12:

Super Admin dapat memberikan keputusan persetujuan akhir untuk menyelesaikan tugas berstatus pending_admin. Status: Wajib.

Nomor 13:

Setiap perubahan status tugas tercatat secara permanen ke dalam basis data rekam jejak. Status: Wajib.

Nomor 14:

Super Admin dapat memantau data kinerja Project Manager melalui indikator total tugas, selesai, terlambat, dan tingkat penyelesaian. Status: Wajib.

Nomor 15:

Super Admin dapat mengakses grafik beban kerja Project Manager yang merinci akumulasi tugas aktif, menunggu peninjauan, dan tugas terlambat. Status: Wajib.

Nomor 16:

Super Admin dapat mengelola hak operasional akun pengguna seperti fitur aktivasi atau penangguhan akun. Status: Wajib.

Nomor 17:

Setiap pengguna dilengkapi antarmuka kotak masuk notifikasi internal sebagai pengaman jika pesan eksternal gagal dikirim. Status: Wajib.

Nomor 18:

Sistem secara otomatis memicu pengiriman notifikasi melalui WhatsApp dan email di setiap perubahan status tugas. Status: Wajib.

Nomor 19:

Sistem mampu menampilkan halaman ringkasan atau statistik aktivitas tugas beserta statusnya secara informatif. Status: Wajib.

# 15. Diagram Use Case

## 15.1 Daftar Aktor

1.  Super Admin: Pengendali platform, pembuat tugas, penentu penugasan Project Manager, penengah, pemberi persetujuan akhir, pemutus pembatalan tugas, dan pengelola akun.

2.  Project Manager: Pengelola tim, pembagi tugas kepada Anggota, penilai hasil kerja (setuju/tolak).

3.  Anggota: Pelaksana teknis, pengunggah dokumen hasil, dan perespon revisi.

1\. SEMUA AKTOR — Masuk ke Sistem

Super Admin, Project Manager, dan Anggota masuk menggunakan email dan password.

2\. SUPER ADMIN — Membuat dan Mendistribusikan Tugas

- Membuat Tugas Baru → mengisi judul, deskripsi, prioritas, *deadline*.

- Melihat Beban Kerja PM (wajib) → mengecek kapasitas PM sebelum menunjuk.

- Menunjuk Project Manager → tugas diberi status assigned_pm.

- (Opsional) Memindahkan tugas atau menunjuk pengganti PM jika PM berhalangan.

3\. PROJECT MANAGER — Menugaskan ke Anggota

- Menerima tugas dari Super Admin.

- Menugaskan Anggota → memilih anggota tim dan memberikan tugas → status assigned_member.

4\. ANGGOTA — Mengerjakan dan Menyerahkan

- Melihat tugas yang ditugaskan.

- Mengerjakan & Mengunggah Berkas → *upload* file hasil kerja → status pending_pm.

5\. PROJECT MANAGER — Meninjau Hasil Kerja

- Meninjau Hasil Kerja → melihat berkas yang diunggah.

  - Jika Setuju → status menjadi pending_admin (menunggu Super Admin).

  - Jika Tolak → wajib mengisi catatan revisi → status revision dan *counter* revisi +1.

- Kelalaian: Jika PM tidak merespon dalam 2×24 jam → eskalasi ke Super Admin.

6\. ANGGOTA — Menindaklanjuti Revisi (jika ada)

- Menerima catatan revisi dari PM.

- Memperbaiki & Mengunggah Ulang → status kembali pending_pm.

7\. BATAS KRITIS — Arbitrase (jika revisi mencapai 3 kali)

- Jika PM menolak hingga 3 kali, tugas otomatis naik ke Super Admin dengan status pending_arbitration.

8\. SUPER ADMIN — Arbitrase dan Persetujuan Akhir

- Melakukan Arbitrase → meninjau riwayat revisi.

  - Jika Menyetujui → status pending_admin → lalu done.

  - Jika Menolak → status revision dengan catatan langsung dari Super Admin ke Anggota.

- Memberikan Persetujuan Akhir untuk tugas pending_admin → status done.

9\. SEMUA AKTOR — Keluar dari Sistem

Setelah selesai, semua aktor dapat *logout*.

![image1.png](assets/brd/image1.png)
