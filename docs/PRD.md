# PRODUCT REQUIREMENT DOCUMENT (PRD)
# TASKFLOW
Sistem Manajemen Kolaborasi Tugas

# 1. PENDAHULUAN
## 1.1 Latar Belakang Produk
TaskFlow merupakan aplikasi berbasis web yang dikembangkan untuk membantu organisasi dalam mengelola pekerjaan secara terstruktur melalui konsep Workspace, Project, dan Task. Aplikasi ini dirancang untuk memudahkan proses koordinasi, pembagian tugas, pemantauan progres pekerjaan, serta pelaporan aktivitas dalam satu platform yang terintegrasi.

Pada banyak organisasi, proses pengelolaan pekerjaan masih dilakukan melalui berbagai media komunikasi seperti WhatsApp, email, maupun spreadsheet. Kondisi tersebut menyebabkan informasi pekerjaan tersebar di berbagai tempat sehingga menyulitkan proses pemantauan, dokumentasi, dan evaluasi.

TaskFlow hadir sebagai solusi dengan menyediakan sistem manajemen tugas yang memiliki pembagian hak akses berdasarkan peran pengguna. Super Admin bertanggung jawab mengelola workspace dan pengguna, Project Manager mengelola project serta task pada workspace yang menjadi tanggung jawabnya, sedangkan Member bertugas menyelesaikan task yang diberikan. Dengan mekanisme tersebut, seluruh proses bisnis dapat berjalan secara lebih terstruktur, transparan, dan terdokumentasi.
## 1.2 TUJUAN DOKUMEN
TaskFlow merupakan sistem manajemen tugas kolaboratif berbasis web yang dikembangkan untuk membantu organisasi dalam mengelola seluruh proses distribusi, pelaksanaan, peninjauan, revisi, hingga penyelesaian tugas secara terintegrasi. Sistem ini menerapkan mekanisme pengelolaan hak akses berdasarkan peran (Role-Based Access Control) yang terdiri atas Super Admin, Project Manager, dan Anggota. Setiap pengguna memperoleh akses terhadap fitur sesuai dengan tanggung jawab dan kewenangannya dalam organisasi.
TaskFlow mendukung proses bisnis yang dimulai dari pembuatan tugas oleh Super Admin, penunjukan Project Manager berdasarkan beban kerja, pendistribusian tugas kepada Anggota, peninjauan hasil pekerjaan, pengelolaan revisi, proses arbitrase apabila diperlukan, hingga pemberian persetujuan akhir terhadap tugas yang telah diselesaikan. Seluruh aktivitas yang terjadi selama proses tersebut dicatat secara otomatis sebagai rekam jejak sehingga setiap perubahan dapat ditelusuri dengan mudah.
Selain mendukung pengelolaan tugas, TaskFlow menyediakan fasilitas pemantauan beban kerja Project Manager, pelaporan kinerja, pengelolaan akun pengguna, serta notifikasi internal untuk mendukung komunikasi antarperan. Melalui fitur-fitur tersebut, organisasi memperoleh sarana yang mampu meningkatkan efektivitas koordinasi, transparansi proses kerja, dan kualitas pengambilan keputusan berdasarkan data yang terdokumentasi.
## 1.3 Tujuan Produk
Pengembangan TaskFlow bertujuan untuk:

Menyediakan platform terpusat untuk mengelola workspace, project, dan task. 
Mempermudah Super Admin dalam mengelola workspace dan pengguna. 
Membantu Project Manager dalam mengelola project, task, dan anggota workspace. 
Membantu Member memperoleh informasi tugas secara jelas dan terstruktur. 
Meningkatkan efisiensi koordinasi dan kolaborasi antar anggota tim. 
Menyediakan laporan perkembangan project dan task secara real-time. 
Menyediakan riwayat aktivitas sebagai media monitoring dan evaluasi.
## 1.4 Target Pengguna

Super Admin
Mengelola workspace. 
Menunjuk Project Manager. 
Mengelola pengguna. 
Melihat seluruh project. 
Melihat seluruh task. 
Melihat laporan dan statistik sistem.Project Manager

Project Manager
Mengelola project. 
Mengelola anggota workspace. 
Membuat, mengubah, dan menghapus task. 
Menugaskan Member. 
Memantau progres project. 
Melihat laporan workspace.

Member
Melihat daftar task. 
Melihat detail task. 
Memperbarui status task. 
Mengunggah lampiran hasil pekerjaan. 
Mengelola profil akun.

## 1.4 Gambaran umum produk
TaskFlow menggunakan struktur pengelolaan pekerjaan yang terdiri atas tiga entitas utama, yaitu Workspace, Project, dan Task. Workspace berfungsi sebagai ruang kerja yang dibuat oleh Super Admin dan dikelola oleh seorang Project Manager. Di dalam setiap workspace terdapat satu atau lebih project yang berisi kumpulan task. Setiap task ditugaskan kepada seorang Member untuk dikerjakan dan dipantau progresnya oleh Project Manager.

Alur kerja utama pada aplikasi adalah sebagai berikut:

Super Admin membuat workspace. 
Super Admin menunjuk Project Manager. 
Project Manager menambahkan Member ke dalam workspace. 
Project Manager membuat project. 
Project Manager membuat dan menugaskan task kepada Member. 
Member mengerjakan task dan memperbarui statusnya. 
Project Manager melakukan monitoring serta menyelesaikan task yang telah selesai. 
Super Admin memantau laporan dan perkembangan seluruh workspace.
# 2. TUJUAN PRODUK
## 2.1 Tujuan Pengembangan Produk
TaskFlow dikembangkan sebagai aplikasi manajemen tugas berbasis web yang bertujuan mendukung proses pengelolaan pekerjaan secara terstruktur melalui konsep Workspace, Project, dan Task. Sistem ini dirancang untuk memfasilitasi proses perencanaan, pembagian tugas, pelaksanaan pekerjaan, pemantauan progres, hingga penyelesaian tugas dalam satu platform yang terintegrasi.

Pengembangan TaskFlow diharapkan mampu mengatasi berbagai kendala dalam pengelolaan pekerjaan yang masih dilakukan menggunakan berbagai media komunikasi yang terpisah. Dengan memusatkan seluruh aktivitas ke dalam satu sistem, organisasi dapat meningkatkan efisiensi operasional, transparansi proses kerja, kolaborasi antaranggota tim, serta akuntabilitas setiap pengguna dalam menjalankan tanggung jawabnya.



## 2.2 Tujuan Pengguna
TaskFlow dikembangkan untuk memenuhi kebutuhan setiap pengguna sesuai dengan peran dan tanggung jawabnya dalam organisasi.

Super Admin
Bagi Super Admin, TaskFlow bertujuan menyediakan sarana untuk mengelola sistem secara menyeluruh melalui pengelolaan workspace, penunjukan Project Manager, pengelolaan akun pengguna, serta pemantauan aktivitas seluruh workspace. Selain itu, sistem menyediakan dashboard dan laporan yang membantu Super Admin melakukan monitoring terhadap perkembangan project dan task pada setiap workspace sehingga pengambilan keputusan dapat dilakukan berdasarkan informasi yang terdokumentasi dengan baik.

Project Manager
Bagi Project Manager, TaskFlow bertujuan membantu proses pengelolaan operasional pada workspace yang menjadi tanggung jawabnya. Sistem menyediakan fasilitas untuk membuat project, mengelola anggota workspace, membuat dan mendistribusikan task kepada Member, memantau progres pekerjaan, serta melakukan validasi terhadap hasil pekerjaan sebelum task dinyatakan selesai. Dengan demikian, Project Manager dapat mengelola pekerjaan secara lebih efektif dan menjaga ketercapaian target project.

Member
Bagi Member, TaskFlow bertujuan menyediakan media yang memudahkan pelaksanaan pekerjaan melalui penyajian informasi task yang terstruktur, pembaruan status pekerjaan, serta pengunggahan lampiran hasil pekerjaan apabila diperlukan. Sistem juga memungkinkan Member memantau perkembangan task sehingga proses penyelesaian pekerjaan menjadi lebih terarah dan terdokumentasi.

## 2.3 Tujuan Organisasi
Dari perspektif organisasi, pengembangan TaskFlow bertujuan meningkatkan kualitas pengelolaan pekerjaan melalui digitalisasi proses bisnis. Sistem ini diharapkan mampu mengurangi ketergantungan terhadap media komunikasi yang tidak terintegrasi, meningkatkan transparansi proses kerja, mempercepat koordinasi antarunit kerja, serta menyediakan dokumentasi aktivitas yang lengkap.

Selain itu, TaskFlow membantu organisasi dalam mengelola workspace, project, dan task secara terpusat sehingga proses monitoring dan evaluasi dapat dilakukan dengan lebih efektif. Laporan yang dihasilkan sistem dapat dimanfaatkan sebagai dasar dalam pengambilan keputusan, pengukuran kinerja, serta perencanaan pekerjaan di masa mendatang.


## 2.4 Indikator Keberhasilan Produk
Keberhasilan pengembangan TaskFlow diukur berdasarkan kemampuan sistem dalam memenuhi kebutuhan pengguna dan mendukung proses bisnis organisasi. Indikator keberhasilan produk ditetapkan sebagai berikut.
# 3. PERSONA PENGGUNA
## 3.1 Pendahuluan
Persona pengguna merupakan representasi karakteristik pengguna yang menjadi sasaran utama pengembangan aplikasi TaskFlow. Penyusunan persona bertujuan untuk memberikan gambaran mengenai profil, kebutuhan, tujuan, serta kendala yang dihadapi oleh setiap pengguna dalam menjalankan aktivitasnya. Informasi tersebut menjadi dasar dalam perancangan fitur, antarmuka pengguna, serta pengalaman pengguna sehingga sistem yang dikembangkan mampu mendukung proses bisnis organisasi secara efektif.

TaskFlow memiliki tiga kelompok pengguna utama, yaitu Super Admin, Project Manager, dan Member. Masing-masing memiliki hak akses, tanggung jawab, serta kebutuhan yang berbeda sesuai dengan perannya dalam pengelolaan workspace, project, dan task.
## 3.2 Persona Super Admin
### Deskripsi
Super Admin merupakan pengguna dengan hak akses tertinggi pada sistem. Peran ini bertanggung jawab mengelola workspace, menunjuk Project Manager, mengelola akun pengguna, serta memantau seluruh aktivitas yang berlangsung pada setiap workspace. Super Admin tidak terlibat secara langsung dalam operasional project maupun pengerjaan task, melainkan berfokus pada administrasi sistem dan monitoring.

Kebutuhan
Membuat workspace. 
Mengelola workspace. 
Menunjuk Project Manager. 
Mengelola akun pengguna. 
Melihat laporan seluruh workspace. 
Memantau perkembangan project dan task. 
Melihat statistik sistem.
### Permasalahan
Sulit memantau perkembangan banyak workspace secara bersamaan. 
Sulit memperoleh laporan aktivitas yang terpusat. 
Membutuhkan informasi perkembangan project secara real-time.
### Harapan
Super Admin menginginkan sistem yang mampu menyediakan dashboard terpusat, laporan yang informatif, serta memudahkan pengelolaan workspace dan pengguna.
## 3.3 Persona Project Manager
### Deskripsi
Project Manager bertanggung jawab mengelola seluruh aktivitas operasional pada workspace yang telah ditugaskan oleh Super Admin. Project Manager membuat project, mengelola anggota workspace, membuat task, mendistribusikan task kepada Member, serta memantau progres pekerjaan hingga project selesai.
### Kebutuhan
Menerima tugas dari Super Admin.
Melihat daftar anggota tim.
Menugaskan anggota.
Meninjau hasil pekerjaan.
Memberikan catatan revisi.
Menyetujui hasil pekerjaan.
Memantau beban kerja anggota.
### Permasalahan
Sulit mengetahui distribusi pekerjaan anggota.
Peninjauan hasil masih dilakukan melalui berbagai media komunikasi.
Catatan revisi tidak terdokumentasi dengan baik.
Sulit mengetahui perkembangan seluruh tugas dalam tim.
### Harapan
Project Manager menginginkan sistem yang mampu membantu koordinasi tim, mempercepat proses peninjauan hasil pekerjaan, serta mempermudah pemantauan progres tugas.
## 3.4 Persona Anggota
### Deskripsi
Anggota merupakan pengguna yang bertugas melaksanakan pekerjaan sesuai dengan instruksi dari Project Manager. Pengguna dapat melihat informasi tugas, mengunggah hasil pekerjaan, menerima catatan revisi, serta mengirimkan kembali hasil perbaikan melalui sistem.
### Kebutuhan
Melihat daftar tugas.
Melihat detail tugas.
Mengunggah hasil pekerjaan.
Melihat catatan revisi.
Mengunggah hasil perbaikan.
Melihat status penyelesaian tugas.
### Permasalahan
Informasi tugas tersebar pada berbagai media komunikasi.
Sulit mengetahui versi berkas yang harus diperbaiki.
Riwayat revisi tidak terdokumentasi.
Tidak mengetahui perkembangan status tugas secara langsung.
### Harapan
Anggota menginginkan sistem yang menyediakan informasi tugas secara jelas, mempermudah pengiriman hasil pekerjaan, serta memungkinkan pemantauan status tugas secara langsung.
## 3.5 Ringkasan Persona Pengguna

# 4. USER STORY
## 4.1 Pendahuluan
User Story merupakan deskripsi singkat mengenai kebutuhan pengguna terhadap sistem berdasarkan sudut pandang masing-masing peran. Penyusunan User Story bertujuan untuk menggambarkan fungsi yang diharapkan oleh pengguna beserta manfaat yang diperoleh ketika fungsi tersebut tersedia. Setiap User Story menjadi dasar dalam penyusunan Product Backlog serta proses pengembangan fitur pada aplikasi TaskFlow.
TaskFlow memiliki tiga kelompok pengguna utama, yaitu Super Admin, Project Manager, dan Member. Oleh karena itu, User Story dikelompokkan berdasarkan masing-masing peran agar kebutuhan setiap pengguna dapat diidentifikasi secara jelas.

4.2 User Story Super Admin




# 4.3 User Story Project Manager
# 4.4 User Story Member

# 4.5 Prioritas Pengembangan
Prioritas Tinggi
Fitur yang wajib tersedia agar proses bisnis utama aplikasi dapat berjalan dengan baik.
Login dan autentikasi 
Manajemen Workspace 
Manajemen Project 
Manajemen Task 
Manajemen Anggota Workspace 
Penugasan Task 
Update Status Task 
Monitoring Project 

Prioritas Sedang
Fitur pendukung yang meningkatkan efektivitas penggunaan aplikasi.
Dashboard statistik 
Laporan 
Notifikasi 
Manajemen profil pengguna

# 5. PRODUCT BACKLOG
## 5.1 Pendahuluan
Product Backlog merupakan daftar kebutuhan produk yang akan dikembangkan pada aplikasi TaskFlow. Penyusunan Product Backlog dilakukan berdasarkan User Story, kebutuhan pengguna, serta Business Requirement Document (BRD) yang telah disusun sebelumnya. Setiap backlog merepresentasikan fitur atau modul utama yang akan diimplementasikan selama proses pengembangan.
Selain menjadi acuan dalam proses pengembangan, Product Backlog juga digunakan untuk menentukan prioritas pengerjaan, perencanaan iterasi, serta memastikan keterlacakan antara kebutuhan pengguna dengan implementasi sistem.
## 5.2 Daftar Product Backlog

## 5.3 Prioritas Pengembangan
Prioritas pengembangan ditentukan berdasarkan tingkat kepentingan setiap fitur terhadap proses bisnis utama TaskFlow.
## 5.4 Perencanaan Iterasi
### Iterasi 1
Fokus pada pembangunan fondasi sistem dan autentikasi pengguna.
Backlog yang dikembangkan:
PB-01 Manajemen Workspace 
PB-02 Penugasan Project Manager 
PB-03 Manajemen Pengguna
### Iterasi 2
Fokus pada pengelolaan project dan anggota workspace.
Backlog yang dikembangkan:
PB-05 Manajemen Project 
PB-06 Manajemen Anggota Workspace
### Iterasi 3
Fokus pada pengelolaan task dan distribusi pekerjaan.
Backlog yang dikembangkan:
PB-07 Manajemen Task 
PB-08 Penugasan Task 
PB-11 Pelaksanaan Task
### Iterasi 4
Fokus pada monitoring serta validasi pekerjaan.
Backlog yang dikembangkan:
PB-09 Monitoring Project 
PB-10 Validasi Task 
PB-12 Pembaruan Status Task 
PB-13 Lampiran Tas
### Iterasi 5
Fokus pada penyempurnaan aplikasi.
Backlog yang dikembangkan:
PB-04 Monitoring Sistem 
PB-14 Notifikasi

## 5.5 Minimum Viable Product (MVP)
Minimum Viable Product (MVP) merupakan kumpulan fitur minimum yang harus tersedia agar aplikasi TaskFlow dapat digunakan sesuai kebutuhan utama organisasi.

Fitur yang termasuk dalam MVP adalah:
PB-01 Manajemen Workspace 
PB-02 Penugasan Project Manager 
PB-03 Manajemen Pengguna 
PB-05 Manajemen Project 
PB-06 Manajemen Anggota Workspace 
PB-07 Manajemen Task 
PB-08 Penugasan Task 
PB-09 Monitoring Project 
PB-11 Pelaksanaan Task 
PB-12 Pembaruan Status Task 
PB-13 Lampiran Task
## 5.6 Keterlacakan Kebutuhan (Requirements Traceability)
Untuk memastikan setiap kebutuhan pengguna diimplementasikan ke dalam sistem, setiap Product Backlog dipetakan terhadap User Story yang menjadi dasar penyusunannya.
# 6. SITEMAP
## 6.1 Pendahuluan
Sitemap merupakan representasi struktur navigasi aplikasi TaskFlow yang menunjukkan hubungan antarhalaman berdasarkan hak akses setiap pengguna. Penyusunan sitemap bertujuan untuk memberikan gambaran mengenai alur navigasi aplikasi sehingga memudahkan proses perancangan antarmuka, implementasi sistem, serta pengalaman pengguna. TaskFlow menerapkan Role-Based Access Control (RBAC), sehingga setiap pengguna hanya dapat mengakses menu sesuai dengan peran yang dimiliki.
## 6.2 Sitemap Sistem
Secara umum, struktur navigasi TaskFlow diawali dengan proses autentikasi pengguna. Setelah pengguna berhasil melakukan login, sistem akan menampilkan dashboard sesuai dengan peran yang dimiliki. Dari halaman dashboard tersebut, pengguna dapat mengakses berbagai menu sesuai dengan hak aksesnya.

Struktur navigasi sistem dibagi menjadi tiga kelompok utama, yaitu:
Super Admin 
Project Manager 
Member 

Pembagian tersebut bertujuan menjaga keamanan sistem, mempermudah pengelolaan hak akses, serta memastikan setiap pengguna hanya dapat mengakses fitur yang menjadi tanggung jawabnya.
## 6.3 Diagram Sitemap
Diagram sitemap menggambarkan hubungan hierarkis antarhalaman pada sistem TaskFlow. Diagram ini menjadi acuan dalam proses perancangan antarmuka (User Interface) dan implementasi navigasi sistem.


Gambar 6.1 Diagram Sitemap TaskFlow
Keterangan:
Super Admin memiliki akses terhadap modul administrasi sistem, pengelolaan workspace, pengguna, serta monitoring keseluruhan aktivitas. 
Project Manager memiliki akses terhadap modul pengelolaan project, task, anggota workspace, dan laporan workspace. 
Member memiliki akses terhadap modul pelaksanaan task, pembaruan status pekerjaan, serta pengunggahan lampiran.6.4 Struktur Navigasi Super Admin
## 6.5 Struktur Navigasi Super Admin
## 6.6 Struktur Navigasi Project Manager
## 6.7 Struktur Navigasi Member
## 6.8 Hubungan Sitemap dengan Product Backlog

# 7. ALUR SISTEM
## 7.1 Pendahuluan
Alur sistem menjelaskan urutan proses yang dilakukan oleh setiap pengguna dalam menggunakan aplikasi TaskFlow. Penyusunan alur sistem bertujuan untuk memberikan gambaran mengenai interaksi antara pengguna dengan sistem, mulai dari proses autentikasi hingga penyelesaian task. Alur ini menjadi acuan dalam perancangan Activity Diagram, Sequence Diagram, serta implementasi fitur pada aplikasi.

TaskFlow menerapkan mekanisme Role-Based Access Control (RBAC) sehingga setiap pengguna hanya dapat mengakses fungsi yang sesuai dengan hak akses dan tanggung jawabnya.
## 7.2 Alur Umum Sistem
Proses penggunaan TaskFlow dimulai ketika pengguna melakukan login ke dalam sistem menggunakan akun yang telah terdaftar. Setelah proses autentikasi berhasil, sistem akan menampilkan dashboard sesuai dengan peran pengguna. Super Admin bertanggung jawab membuat workspace, menunjuk Project Manager, serta mengelola akun pengguna. Setelah memperoleh penugasan, Project Manager mengelola workspace dengan membuat project, menambahkan Member, serta membuat task yang akan dikerjakan oleh Member. Member kemudian melihat task yang telah diberikan, mengerjakan pekerjaan sesuai deskripsi task, memperbarui status pekerjaan, serta mengunggah lampiran apabila diperlukan. Selanjutnya Project Manager memantau perkembangan task dan melakukan validasi sebelum task dinyatakan selesai. Seluruh aktivitas pengguna dicatat oleh sistem sebagai riwayat aktivitas dan ditampilkan pada dashboard maupun laporan.
## 7.3 Alur Kerja Super Admin
Alur kerja Super Admin terdiri atas beberapa tahapan sebagai berikut.

Login ke dalam sistem. 
Membuat workspace baru. 
Menunjuk Project Manager pada workspace. 
Mengelola akun pengguna. 
Memantau perkembangan workspace. 
Melihat laporan dan statistik sistem. 
Logout dari sistem.
## 7.4 Alur Kerja Project Manager
Setelah ditunjuk oleh Super Admin, Project Manager mengelola operasional workspace melalui tahapan berikut.

Login ke dalam sistem. 
Memilih workspace yang menjadi tanggung jawabnya. 
Membuat project baru. 
Menambahkan Member ke dalam workspace. 
Membuat task. 
Menentukan Member sebagai penanggung jawab task. 
Memantau progres task. 
Memvalidasi hasil pekerjaan. 
Menyelesaikan task. 
Melihat laporan workspace. 
Logout dari sistem.
## 7.5 Alur Kerja Member
Member menjalankan aktivitas sebagai pelaksana task melalui tahapan berikut.
Login ke dalam sistem. 

Melihat daftar task yang ditugaskan. 
Membuka detail task. 
Mengubah status task sesuai progres pekerjaan. 
Mengunggah lampiran hasil pekerjaan apabila diperlukan. 
Menunggu validasi dari Project Manager. 
Logout dari sistem.
## 7.6 Status Task

## 7.7 Diagram Alur Sistem

## 7.8 Hubungan Alur Sistem dengan Product Backlog

# 8. SPESIFIKASI FITUR
## 8.1 Pendahuluan
Spesifikasi fitur menjelaskan kebutuhan fungsional setiap modul pada aplikasi TaskFlow. Setiap fitur dirancang berdasarkan kebutuhan pengguna yang telah diidentifikasi pada Business Requirement Document (BRD), User Story, dan Product Backlog. Spesifikasi ini menjadi acuan dalam proses pengembangan, pengujian, serta implementasi sistem.

## 8.2 Fitur Super Admin
### 8.2.1 Dashboard
Deskripsi
Dashboard Super Admin menampilkan ringkasan aktivitas sistem secara keseluruhan.

Aktor
Super Admin

Fungsi
Melihat jumlah workspace. 
Melihat jumlah project. 
Melihat jumlah task. 
Melihat statistik task berdasarkan status. 
Melihat notifikasi terbaru. 

Hasil yang Diharapkan
Dashboard menampilkan informasi sistem secara real-time.

### 8.2.2 Manajemen Workspace
Deskripsi
Super Admin dapat mengelola seluruh workspace pada sistem.

Aktor
Super Admin

Fungsi
Menambah workspace. 
Mengubah workspace. 
Menghapus workspace. 
Melihat daftar workspace. 

Data yang Dikelola
Nama Workspace 
Deskripsi 
Project Manager 

### 8.2.3 Penugasan Project Manager
Deskripsi
Super Admin menunjuk Project Manager untuk mengelola suatu workspace.

Aktor
Super Admin

Fungsi
Memilih Project Manager. 
Mengganti Project Manager. 
Melihat informasi Project Manager. 

### 8.2.4 Manajemen Pengguna
Deskripsi
Super Admin mengelola seluruh akun pengguna.

Fungsi
Tambah pengguna. 
Edit pengguna. 
Hapus pengguna. 
Aktivasi akun. 
Nonaktifkan akun. 

### 8.2.5 Monitoring
Fungsi
Melihat seluruh project. 
Melihat seluruh task. 
Melihat laporan. 
Melihat statistik. 

## 8.3 Fitur Project Manager
### 8.3.1 Dashboard
Fungsi
Melihat progress project. 
Melihat jumlah task. 
Melihat task yang mendekati deadline. 
Melihat notifikasi. 

### 8.3.2 Manajemen Project
Fungsi
Membuat project. 
Mengubah project. 
Menghapus project. 
Melihat detail project. 

Data
Nama Project 
Deskripsi 
Deadline 

### 8.3.3 Manajemen Member
Fungsi
Menambah Member. 
Menghapus Member. 
Melihat daftar Member. 

### 8.3.4 Manajemen Task
Fungsi
Membuat task. 
Mengubah task. 
Menghapus task. 
Menentukan prioritas. 
Menentukan deadline. 
Menentukan Member.

Data
Judul 
Deskripsi 
Prioritas 
Deadline 
Status 
Lampiran 

### 8.3.5 Monitoring Project
Fungsi
Melihat progress project. 
Melihat progress task. 
Memvalidasi task. 
Mengubah status menjadi Done. 

## 8.4 Fitur Member
### 8.4.1 Dashboard
Fungsi
Melihat task aktif. 
Melihat task selesai. 
Melihat deadline. 
Melihat notifikasi. 

### 8.4.2 Task Saya
Fungsi
Melihat daftar task. 
Melihat detail task. 
Update status. 
Upload lampiran. 
### 8.4.3 Profil
Fungsi
Mengubah foto profil. 
Mengubah informasi akun. 
Mengubah password. 

## 8.5 Fitur Notifikasi
Deskripsi
Sistem mengirimkan notifikasi kepada pengguna ketika terjadi aktivitas tertentu.

Pemicu Notifikasi
Workspace baru. 
Penunjukan Project Manager. 
Penambahan Member. 
Task baru. 
Perubahan status task. 
Task selesai. 
Deadline mendekat. 

Media
Notifikasi Internal 
WhatsApp (Fonnte API) 
Email (SMTP) 

## 8.6 Validasi Sistem
Sistem melakukan validasi terhadap data yang dimasukkan pengguna sebelum disimpan ke basis data.



# 9. PERSYARATAN NON-FUNGSIONAL
## 9.1 Pendahuluan
Persyaratan non-fungsional menjelaskan karakteristik kualitas yang harus dimiliki oleh aplikasi TaskFlow agar dapat digunakan secara optimal. Persyaratan ini tidak berkaitan langsung dengan fungsi utama sistem, tetapi menjadi standar yang harus dipenuhi dalam aspek keamanan, kinerja, keandalan, kemudahan penggunaan, serta kompatibilitas aplikasi.

## 9.2 Keamanan (Security)
TaskFlow harus menerapkan mekanisme keamanan untuk melindungi data pengguna serta aktivitas yang terjadi di dalam sistem.

Persyaratan keamanan meliputi:
Setiap pengguna wajib melakukan autentikasi sebelum mengakses sistem. 
Hak akses pengguna diatur menggunakan Role-Based Access Control (RBAC). 
Password pengguna disimpan menggunakan algoritma hashing. 
Sistem menggunakan proteksi terhadap Cross-Site Request Forgery (CSRF). 
Validasi input dilakukan untuk mencegah data yang tidak valid. 
Aktivitas penting pengguna dicatat dalam riwayat aktivitas sistem. 

## 9.3 Kinerja (Performance)
TaskFlow harus mampu memberikan performa yang baik dalam kondisi penggunaan normal.

Persyaratan kinerja meliputi:
Waktu respon halaman tidak melebihi 3 detik pada koneksi internet yang stabil. 
Dashboard mampu menampilkan data secara cepat meskipun jumlah project dan task terus bertambah. 
Sistem mampu menangani banyak pengguna yang mengakses aplikasi secara bersamaan tanpa menurunkan performa secara signifikan. 

## 9.4 Keandalan (Reliability)
TaskFlow harus mampu menjaga konsistensi data selama proses operasional berlangsung.

Persyaratan keandalan meliputi:
Data yang berhasil disimpan tidak mengalami kehilangan. 
Sistem mampu menangani kesalahan proses penyimpanan dengan memberikan pesan kesalahan yang jelas. 
Basis data dicadangkan secara berkala. 
Riwayat aktivitas tersimpan secara permanen selama data tidak dihapus oleh administrator. 

## 9.5 Kemudahan Penggunaan (Usability)
Antarmuka aplikasi harus mudah dipahami oleh seluruh pengguna.

Persyaratan usability meliputi:
Tampilan antarmuka sederhana dan konsisten. 
Navigasi mudah dipahami. 
Dashboard disesuaikan dengan peran pengguna. 
Informasi penting mudah ditemukan. 
Pengguna baru dapat menggunakan sistem tanpa pelatihan yang kompleks. 

## 9.6 Kompatibilitas (Compatibility)
TaskFlow harus dapat digunakan pada berbagai perangkat dan browser modern.

Browser yang didukung meliputi:
Google Chrome 
Mozilla Firefox 
Microsoft Edge 
Safari 

Aplikasi juga harus dapat diakses melalui komputer maupun perangkat seluler menggunakan browser.

## 9.7 Skalabilitas (Scalability)
TaskFlow dirancang agar mudah dikembangkan apabila kebutuhan organisasi meningkat.

Kemungkinan pengembangan di masa mendatang meliputi:
Penambahan role pengguna. 
Penambahan modul baru. 
Integrasi dengan layanan pihak ketiga. 
Pengembangan aplikasi mobile. 
Penambahan fitur kolaborasi secara real-time. 

## 9.8 Maintainability
TaskFlow harus mudah dipelihara dan dikembangkan oleh tim pengembang.

Persyaratan maintainability meliputi:
Struktur kode mengikuti standar Laravel. 
Penggunaan arsitektur modular agar mudah dikembangkan. 
Dokumentasi kode tersedia. 
Konfigurasi sistem dapat dilakukan tanpa mengubah kode utama. 

## 9.9 Ketersediaan (Availability)
Sistem diharapkan memiliki tingkat ketersediaan yang tinggi agar dapat digunakan oleh pengguna kapan pun diperlukan.

Target ketersediaan sistem meliputi:
Sistem dapat diakses selama 24 jam setiap hari, kecuali saat proses pemeliharaan. 
Proses pemeliharaan dilakukan di luar jam operasional apabila memungkinkan. 
Pengguna memperoleh informasi apabila sistem sedang mengalami pemeliharaan. 

## 9.10 Notifikasi
TaskFlow menyediakan mekanisme notifikasi untuk membantu pengguna memperoleh informasi mengenai aktivitas yang terjadi pada sistem.

Notifikasi dikirim melalui:
Notifikasi Internal berbasis database. 
WhatsApp menggunakan Fonnte API. 
Email menggunakan SMTP. 

Notifikasi diberikan ketika terjadi:
Penugasan Project Manager. 
Penambahan Member ke workspace. 
Pembuatan task baru. 
Perubahan status task. 
Task mendekati deadline. 
Task dinyatakan selesai. 
## 9.11 Ringkasan Persyaratan Non-Fungsional


# 10. ACCEPTANCE CRITERIA
## 10.1 Pendahuluan
Acceptance Criteria merupakan sekumpulan kriteria yang harus dipenuhi agar suatu fitur dinyatakan berhasil diimplementasikan. Kriteria ini digunakan sebagai acuan dalam proses pengujian sistem (testing), validasi kebutuhan pengguna, serta memastikan bahwa setiap fitur telah berjalan sesuai dengan Product Backlog dan Business Requirement Document (BRD).
## 10.2 Acceptance Criteria Super Admin


## 10.3 Acceptance Criteria Project Manager


## 10.4 Acceptance Criteria Member


## 10.5 Acceptance Criteria Sistem


## 10.6 Ringkasan Acceptance Criteria


## 10.7 Status Pengujian
Acceptance Criteria nantinya akan digunakan sebagai dasar dalam proses pengujian aplikasi. Setiap fitur akan diuji menggunakan skenario pengujian yang sesuai dan diberikan status sebagai berikut.


# 11. TEKNOLOGI
## 11.1 Pendahuluan
Bab ini menjelaskan teknologi yang digunakan dalam proses pengembangan aplikasi TaskFlow. Pemilihan teknologi didasarkan pada kebutuhan sistem yang memerlukan performa yang baik, kemudahan pengembangan, keamanan, serta kemudahan pemeliharaan di masa mendatang. Teknologi yang digunakan mencakup framework backend, antarmuka pengguna, basis data, layanan notifikasi, hingga lingkungan pengembangan.

## 11.2 Teknologi Pengembangan
Teknologi yang digunakan pada aplikasi TaskFlow dijelaskan pada tabel berikut.


## 11.3 Teknologi Notifikasi
TaskFlow menyediakan mekanisme notifikasi melalui beberapa media komunikasi untuk memastikan setiap pengguna memperoleh informasi mengenai aktivitas yang berkaitan dengan pekerjaannya.

Teknologi yang digunakan meliputi:


Notifikasi dikirim ketika terjadi aktivitas berikut.
Penunjukan Project Manager. 
Penambahan Member ke dalam workspace. 
Penugasan task kepada Member. 
Perubahan status task. 
Task mendekati deadline. 
Task dinyatakan selesai.

## 11.5 Lingkungan Pengembangan
Selama proses pengembangan, aplikasi TaskFlow dibangun menggunakan lingkungan pengembangan sebagai berikut.


## 11.6 Alasan Pemilihan Teknologi
Teknologi yang digunakan dipilih berdasarkan beberapa pertimbangan berikut.
Laravel menyediakan struktur pengembangan yang terorganisir serta mendukung implementasi fitur secara cepat. 
Filament mempermudah pembangunan panel administrasi untuk Super Admin. 
Livewire memungkinkan pembuatan antarmuka interaktif tanpa memerlukan framework JavaScript yang kompleks. 
Tailwind CSS mendukung pengembangan antarmuka yang responsif dan konsisten. 
Docker Compose mempermudah proses deployment serta menjaga konsistensi lingkungan pengembangan. 
MariaDB atau MySQL merupakan sistem manajemen basis data yang stabil dan mudah diintegrasikan dengan Laravel. 
Fonnte API dan SMTP memungkinkan sistem mengirimkan notifikasi melalui WhatsApp dan email secara otomatis. 

11.7 Ringkasan Teknologi



