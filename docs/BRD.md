# BUSINESS REQUIREMENT DOCUMENT (BRD)
# TaskFlow: Sistem Manajemen Tugas Kolaboratif 


## 1. Ringkasan Eksekutif

Business Requirement Document (BRD) ini disusun sebagai acuan dalam pengembangan TaskFlow, yaitu aplikasi berbasis web yang dirancang untuk mendukung pengelolaan tugas secara kolaboratif dalam suatu organisasi atau perusahaan. Sistem ini menerapkan tiga peran utama, yaitu Super Admin, Project Manager, dan Member, dengan pembagian hak akses dan tanggung jawab yang jelas sesuai struktur organisasi.

TaskFlow dikembangkan untuk mengatasi permasalahan pengelolaan tugas yang masih dilakukan melalui media komunikasi seperti WhatsApp, email, atau percakapan langsung, sehingga sering menimbulkan kesulitan dalam pelacakan pekerjaan, pemantauan progres, dan dokumentasi aktivitas. Melalui sistem ini, seluruh proses pengelolaan tugas dilakukan secara terpusat mulai dari pembentukan workspace, pengelolaan project, pembuatan task, penugasan anggota, hingga pemantauan penyelesaian pekerjaan. 

Dalam proses bisnis yang diusulkan, Super Admin berperan sebagai pengelola sistem yang bertanggung jawab membuat dan mengelola workspace, menunjuk Project Manager, mengelola akun pengguna, serta memantau aktivitas seluruh workspace melalui dashboard dan laporan. Setelah memperoleh akses ke workspace, Project Manager bertanggung jawab mengelola operasional pekerjaan dengan membuat project, menyusun task, mengatur anggota workspace, serta mendistribusikan task kepada Member. Selanjutnya, Member bertugas mengerjakan task yang telah diberikan, memperbarui status pengerjaan, dan mengunggah lampiran apabila diperlukan. 

Dengan penerapan sistem ini, organisasi diharapkan memperoleh proses pengelolaan tugas yang lebih terstruktur, transparan, terdokumentasi, dan mudah dipantau. Selain meningkatkan efisiensi koordinasi antaranggota tim, TaskFlow juga menyediakan informasi progres pekerjaan secara real-time sehingga membantu proses pengambilan keputusan oleh pihak manajemen.


## 2. Latar Belakang dan Justifikasi Bisnis
### 2.1 Konteks
TaskFlow merupakan aplikasi berbasis web yang dirancang untuk membantu organisasi atau perusahaan dalam mengelola pekerjaan secara terstruktur melalui konsep workspace, project, dan task. Sistem ini mendukung kolaborasi antara tiga peran utama, yaitu Super Admin, Project Manager, dan Member, dengan pembagian tanggung jawab yang jelas sesuai kebutuhan operasional.

Dalam implementasinya, Super Admin bertanggung jawab mengelola sistem secara keseluruhan, termasuk membuat workspace, menunjuk Project Manager, mengelola akun pengguna, serta memantau aktivitas seluruh workspace. Project Manager bertanggung jawab mengelola operasional pada workspace yang telah ditugaskan dengan membuat project, menyusun task, mengatur anggota workspace, serta memantau progres pekerjaan. Member bertugas melaksanakan task yang diberikan oleh Project Manager dan memperbarui status penyelesaiannya. 

Saat ini, banyak organisasi masih mengelola pekerjaan menggunakan media komunikasi seperti WhatsApp, email, maupun percakapan langsung. Metode tersebut sering kali menyebabkan informasi pekerjaan tersebar di berbagai platform, sehingga menyulitkan proses koordinasi, pemantauan progres, serta penyimpanan dokumentasi pekerjaan. Selain itu, tidak adanya sistem yang terintegrasi menyebabkan proses pelaporan dan evaluasi kinerja menjadi kurang efektif.

TaskFlow dikembangkan sebagai solusi untuk menyediakan platform terpusat yang mampu mengelola seluruh aktivitas pekerjaan mulai dari pembentukan workspace hingga penyelesaian task, sehingga proses kolaborasi menjadi lebih terorganisasi, transparan, dan terdokumentasi.

2.2 Permasalahan
Berdasarkan kondisi yang ada, beberapa permasalahan yang dihadapi organisasi antara lain:
Pengelolaan tugas masih dilakukan melalui berbagai media komunikasi sehingga informasi pekerjaan tidak terpusat. 
Sulit memantau perkembangan pekerjaan secara real-time karena tidak tersedia sistem yang menunjukkan status setiap task. 
Tidak terdapat pembagian tanggung jawab yang jelas antara administrator sistem, pengelola proyek, dan pelaksana tugas. 
Riwayat perubahan status pekerjaan tidak terdokumentasi dengan baik sehingga menyulitkan proses evaluasi. 
Distribusi pekerjaan kepada anggota tim masih dilakukan secara manual sehingga berpotensi menimbulkan keterlambatan maupun ketidakseimbangan beban kerja. 
Organisasi belum memiliki media terintegrasi untuk mengelola workspace, project, task, dan anggota dalam satu sistem. 
Proses penyusunan laporan perkembangan pekerjaan masih dilakukan secara manual sehingga membutuhkan waktu lebih lama.
Organisasi tidak memiliki mekanisme pengalihan otomatis jika Project Manager berhalangan hadir karena cuti atau sakit, sehingga alur penyelesaian tugas sering terhenti.

### 2.3 Solusi yang Diusulkan
Untuk mengatasi permasalahan tersebut, akan dikembangkan aplikasi TaskFlow berbasis web dengan fitur-fitur sebagai berikut:
Sistem menyediakan autentikasi pengguna berdasarkan tiga peran, yaitu Super Admin, Project Manager, dan Member. 
Super Admin dapat membuat, mengubah, dan menghapus workspace. 
Super Admin dapat menunjuk Project Manager untuk mengelola workspace tertentu. 
Super Admin dapat mengelola akun pengguna serta memantau seluruh aktivitas workspace. 
Project Manager dapat membuat dan mengelola project pada workspace yang menjadi tanggung jawabnya. 
Project Manager dapat mengelola anggota workspace. 
Project Manager dapat membuat, mengubah, dan menghapus task. 
Project Manager dapat menetapkan anggota sebagai penanggung jawab setiap task. 
Member dapat melihat daftar task yang diberikan kepadanya beserta detail pekerjaan. 
Member dapat memperbarui status task sesuai progres pengerjaan. 
Member dapat mengunggah lampiran sebagai bukti penyelesaian pekerjaan apabila diperlukan. 
Sistem menyediakan dashboard dan laporan yang menampilkan perkembangan project dan task secara real-time. 
Sistem mencatat seluruh aktivitas penting sebagai riwayat yang dapat digunakan untuk proses monitoring dan evaluasi.

## 3. Tujuan Bisnis
Pengembangan aplikasi TaskFlow bertujuan untuk menyediakan sistem manajemen tugas berbasis web yang mampu mendukung proses kolaborasi dalam organisasi secara terstruktur, terintegrasi, dan mudah dipantau. Sistem ini dirancang untuk meningkatkan efisiensi pengelolaan pekerjaan mulai dari pembentukan workspace, pengelolaan project, hingga penyelesaian task oleh setiap anggota tim sesuai dengan peran dan tanggung jawabnya.
Secara rinci, tujuan pengembangan TaskFlow adalah sebagai berikut:

Menyediakan platform terpusat untuk mengelola workspace, project, dan task dalam satu sistem. 
Mempermudah Super Admin dalam mengelola workspace, pengguna, serta melakukan pemantauan terhadap seluruh aktivitas organisasi. 
Membantu Project Manager dalam mengelola project, mendistribusikan task kepada Member, serta memantau perkembangan pekerjaan secara real-time. 
Membantu Member memperoleh informasi task yang jelas, terstruktur, dan mudah dipantau selama proses pengerjaan. 
Meningkatkan transparansi proses kerja melalui pencatatan status task dan riwayat aktivitas yang terdokumentasi. 
Mempermudah proses monitoring dan evaluasi kinerja berdasarkan data progres project dan task yang tersedia pada sistem. 
Mengurangi penggunaan media komunikasi yang terpisah dalam proses distribusi dan pelaporan pekerjaan. 
Meningkatkan efisiensi koordinasi antar pengguna melalui sistem yang terintegrasi dalam satu platform. 
Menyediakan laporan perkembangan pekerjaan sebagai dasar pengambilan keputusan oleh pihak manajemen. 
Membangun sistem manajemen tugas yang mudah dikembangkan untuk mendukung kebutuhan organisasi di masa mendatang.

## 4. Ruang Lingkup
Ruang lingkup pengembangan aplikasi TaskFlow meliputi seluruh fitur utama yang mendukung proses pengelolaan workspace, project, task, anggota tim, serta pelaporan. Sistem dibangun berbasis web dengan pembagian hak akses berdasarkan tiga peran pengguna, yaitu Super Admin, Project Manager, dan Member.
### 4.1 Dashboard Super Admin
Super Admin merupakan administrator utama yang bertanggung jawab terhadap pengelolaan sistem secara keseluruhan. Pada dashboard ini, Super Admin memiliki akses untuk mengelola workspace, pengguna, serta memantau seluruh aktivitas yang berlangsung di dalam sistem.
Fitur yang tersedia meliputi:
Melihat ringkasan informasi sistem berupa jumlah workspace, project, task, Project Manager, dan Member. 
Membuat workspace baru. 
Mengubah informasi workspace. 
Menghapus workspace. 
Menunjuk atau mengganti Project Manager pada setiap workspace. 
Mengelola akun pengguna. 
Mengaktifkan atau menonaktifkan akun pengguna. 
Melihat seluruh project pada setiap workspace. 
Melihat seluruh task beserta statusnya. 
Melihat laporan perkembangan seluruh workspace. 
Melihat statistik penyelesaian task. 
Melihat profil pengguna. 
Mengubah password akun.
Memberikan persetujuan akhir untuk tugas yang telah disetujui Project Manager.
Mengelola akun pengguna, termasuk melakukan pengaktifan atau penangguhan akun.
Memantau kinerja Project Manager melalui metrik total tugas, tugas selesai, tugas terlambat, dan tingkat penyelesaian.
Memantau daftar keseluruhan tugas yang pernah dibuat beserta status terkininya.
Melihat riwayat perubahan status untuk setiap tugas terkait.
Memantau penghitung revisi apabila tugas tersebut sedang dalam proses perbaikan.

### 4.2 Dashboard Project Manager
Project Manager bertanggung jawab mengelola seluruh aktivitas operasional pada workspace yang menjadi tanggung jawabnya.

Fitur yang tersedia meliputi:

Melihat dashboard workspace. 
Melihat ringkasan jumlah project. 
Melihat jumlah task berdasarkan status. 
Membuat project. 
Mengubah project. 
Menghapus project. 
Melihat detail project. 
Mengelola anggota workspace. 
Menambahkan Member ke dalam workspace. 
Menghapus Member dari workspace. 
Melihat daftar anggota workspace. 
Membuat task. 
Mengubah task. 
Menghapus task. 
Menetapkan Member sebagai penanggung jawab task. 
Mengubah status task. 
Memantau progres project. 
Melihat laporan workspace. 
Melihat profil pengguna. 
Mengubah password akun.

### 4.3 Dashboard Member
Member merupakan pengguna yang bertugas menyelesaikan task yang diberikan oleh Project Manager.

Fitur yang tersedia meliputi:
Melihat dashboard pribadi. 
Melihat daftar task yang diberikan. 
Melihat detail task. 
Memperbarui status task. 
Mengunggah lampiran hasil pekerjaan (opsional). 
Melihat riwayat task. 
Melihat profil pengguna. 
Mengubah password akun.

### 4.4 Pengelolaan Workspace
Sistem menyediakan fitur pengelolaan workspace sebagai wadah utama dalam pelaksanaan pekerjaan.

Fitur yang tersedia meliputi:

Pembuatan workspace. 
Perubahan informasi workspace. 
Penghapusan workspace. 
Penunjukan Project Manager. 
Pengelolaan anggota workspace. 
Menampilkan daftar workspace.

### 4.5 Pengelolaan Project
Setiap workspace dapat memiliki satu atau lebih project yang dikelola oleh Project Manager.

Fitur yang tersedia meliputi:
Pembuatan project. 
Perubahan informasi project. 
Penghapusan project. 
Penentuan deadline project. 
Pemantauan progres project. 
Menampilkan daftar project.

### 4.6 Pengelolaan Task
Task merupakan unit pekerjaan yang berada di dalam suatu project.

Fitur yang tersedia meliputi:

Pembuatan task. 
Perubahan task. 
Penghapusan task. 
Penentuan prioritas task. 
Penentuan deadline task. 
Penugasan Member. 
Perubahan status task. 
Penyimpanan lampiran. 
Penyimpanan riwayat perubahan status.

### 4.7 Pelaporan
Sistem menyediakan fitur pelaporan untuk membantu proses monitoring dan evaluasi.

Laporan yang tersedia meliputi:

Ringkasan jumlah workspace. 
Ringkasan jumlah project. 
Ringkasan jumlah task. 
Statistik task berdasarkan status. 
Statistik penyelesaian project. 
Statistik penyelesaian task setiap Member. 
Statistik aktivitas setiap Project Manager.

### 4.8 Notifikasi
Sistem menyediakan notifikasi internal agar setiap pengguna memperoleh informasi mengenai aktivitas yang berkaitan dengan pekerjaannya.

Notifikasi meliputi:

Penunjukan Project Manager pada workspace. 
Penambahan Member ke workspace. 
Penugasan task. 
Perubahan status task. 
Deadline task yang akan berakhir. 
Penyelesaian task.

### 4.8 Ruang Lingkup yang Tidak Termasuk
Pengembangan versi pertama (Minimum Viable Product/MVP) tidak mencakup fitur-fitur berikut:

Aplikasi mobile Android maupun iOS. 
Integrasi dengan sistem pembayaran. 
Fitur percakapan (chat) antar pengguna. 
Fitur panggilan suara maupun video. 
Integrasi dengan layanan penyimpanan cloud pihak ketiga. 
Gantt Chart dan Timeline Project. 
Kalender proyek. 
Pengelolaan sprint dan backlog. 
Subtask atau task bertingkat. 
Integrasi Single Sign-On (SSO). 
Integrasi dengan GitHub, GitLab, atau Jira. 
Multi-tenant untuk satu pengguna dengan hak akses lintas organisasi.

## 5. Pemangku Kepentingan dan Pengguna
Aplikasi TaskFlow melibatkan beberapa pemangku kepentingan yang memiliki peran dan tanggung jawab berbeda dalam mendukung proses pengelolaan pekerjaan. Pembagian hak akses dilakukan berdasarkan kebutuhan operasional agar setiap pengguna hanya dapat mengakses fitur yang sesuai dengan tanggung jawabnya.

### 5.1 Super Admin
Super Admin merupakan administrator utama yang memiliki kewenangan penuh terhadap pengelolaan sistem. Peran ini bertanggung jawab dalam mengelola workspace, menunjuk Project Manager, mengelola akun pengguna, serta memantau seluruh aktivitas yang terjadi pada setiap workspace. Super Admin tidak terlibat secara langsung dalam pelaksanaan project maupun pengerjaan task, melainkan berfokus pada pengelolaan sistem dan monitoring secara menyeluruh.

Tanggung jawab Super Admin meliputi:

Mengelola workspace. 
Menunjuk atau mengganti Project Manager. 
Mengelola akun pengguna. 
Mengaktifkan dan menonaktifkan akun. 
Melihat seluruh project. 
Melihat seluruh task. 
Memantau laporan seluruh workspace. 
Melihat statistik aktivitas sistem.

### 5.2 Project Manager
Project Manager merupakan pengguna yang bertanggung jawab mengelola operasional pada workspace yang telah ditugaskan oleh Super Admin. Project Manager memiliki kewenangan untuk mengelola project, membuat task, menentukan penanggung jawab task, serta memantau progres penyelesaian pekerjaan anggota tim.

Tanggung jawab Project Manager meliputi:

Mengelola project. 
Mengelola anggota workspace. 
Membuat task. 
Mengubah task. 
Menghapus task. 
Menugaskan task kepada Member. 
Memantau progres project. 
Melihat laporan workspace.

### 5.3 Member
Member merupakan pengguna yang bertanggung jawab melaksanakan task yang diberikan oleh Project Manager. Member hanya memiliki akses terhadap task yang menjadi tanggung jawabnya dan tidak memiliki hak untuk mengelola project maupun workspace.

Tanggung jawab Member meliputi:

Melihat daftar task. 
Melihat detail task. 
Memperbarui status task. 
Mengunggah lampiran hasil pekerjaan (opsional). 
Menyelesaikan task sesuai target yang ditentukan.

### 5.4 Hubungan Antar Peran
Hubungan antar pengguna dalam sistem TaskFlow bersifat hierarkis sehingga setiap peran memiliki kewenangan yang berbeda.










Super Admin
      │
      ├── Mengelola Workspace
      ├── Mengelola User
      └── Menunjuk Project Manager
                  │
                  ▼
          Project Manager
                  │
                  ├── Mengelola Project
                  ├── Mengelola Task
                  ├── Mengelola Member
                  └── Monitoring Progress
                           │
                           ▼
                        Member
                           │
                           ├── Melihat Task
                           ├── Mengubah Status
                           └── Upload Lampiran

Hubungan tersebut menunjukkan bahwa Super Admin berperan sebagai administrator sistem, sedangkan Project Manager bertindak sebagai pengelola operasional pada workspace yang ditugaskan. Member menjadi pelaksana pekerjaan yang berfokus pada penyelesaian task sesuai penugasan dari Project Manager.

## 6. Persyaratan Fungsional
Persyaratan fungsional menjelaskan seluruh fungsi yang harus disediakan oleh sistem agar proses bisnis dapat berjalan sesuai dengan kebutuhan pengguna. Setiap fungsi dirancang berdasarkan hak akses masing-masing peran, yaitu Super Admin, Project Manager, dan Member.
### 6.1 Autentikasi dan Manajemen Akun
Sistem harus menyediakan mekanisme autentikasi yang aman untuk seluruh pengguna. Setelah berhasil masuk, sistem akan menampilkan dashboard sesuai dengan peran pengguna. Selain itu, setiap pengguna dapat mengelola informasi akun pribadinya.

Fungsi yang harus tersedia meliputi:

Login menggunakan email dan password. 
Logout dari sistem. 
Dashboard yang menyesuaikan hak akses pengguna. 
Melihat dan mengubah profil pengguna. 
Mengubah password akun. 
Menampilkan notifikasi internal pada aplikasi.

### 6.2 Manajemen Workspace
Workspace merupakan ruang kerja utama yang dikelola oleh Super Admin.

Sistem harus menyediakan fungsi sebagai berikut:

Membuat workspace baru. 
Mengubah informasi workspace. 
Menghapus workspace. 
Menampilkan daftar workspace. 
Menunjuk Project Manager pada setiap workspace. 
Mengganti Project Manager apabila diperlukan. 
Menampilkan informasi Project Manager yang bertanggung jawab pada setiap workspace.

### 6.3 Manajemen Pengguna
Sistem harus menyediakan fasilitas untuk mengelola akun pengguna sesuai dengan hak akses yang dimiliki.

Fungsi yang harus tersedia meliputi:

Menambahkan akun pengguna. 
Mengubah data pengguna. 
Menghapus akun pengguna. 
Mengaktifkan atau menonaktifkan akun. 
Menentukan peran pengguna sebagai Project Manager atau Member. 
Melihat daftar seluruh pengguna.

### 6.4 Manajemen Project
Project merupakan kumpulan pekerjaan yang berada di dalam suatu workspace dan dikelola oleh Project Manager.

Sistem harus menyediakan fungsi sebagai berikut:

Membuat project. 
Mengubah informasi project. 
Menghapus project. 
Menentukan deskripsi project. 
Menentukan deadline project. 
Melihat daftar project pada workspace. 
Menampilkan progres penyelesaian project berdasarkan task yang telah diselesaikan.

### 6.5 Manajemen Anggota Workspace
Project Manager bertanggung jawab mengelola anggota yang berada di dalam workspace.

Sistem harus menyediakan fungsi sebagai berikut:

Menambahkan Member ke dalam workspace. 
Menghapus Member dari workspace. 
Melihat daftar Member pada workspace. 
Melihat informasi setiap Member.

6.6 Manajemen Task

Task merupakan pekerjaan yang berada di dalam suatu project dan menjadi tanggung jawab Project Manager.

Sistem harus menyediakan fungsi sebagai berikut:

Membuat task baru. 
Mengubah informasi task. 
Menghapus task. 
Menentukan project tujuan. 
Menentukan prioritas task. 
Menentukan deadline task. 
Menentukan Member sebagai penanggung jawab task. 
Menampilkan daftar task berdasarkan project. 
Menampilkan detail task. 
Menyimpan lampiran task. 
Menyimpan riwayat perubahan task.

### 6.7 Status Task
Setiap task memiliki status yang menggambarkan perkembangan pekerjaan.

Status yang digunakan dalam sistem terdiri dari:

To Do
Task telah dibuat oleh Project Manager dan siap untuk dikerjakan oleh Member.
In Progress
Member sedang mengerjakan task yang telah diberikan.

Review
Member telah menyelesaikan pekerjaan dan mengubah status task menjadi Review agar dapat diperiksa oleh Project Manager.

Done
Project Manager menyatakan task telah selesai dan sesuai dengan kebutuhan project.

Cancelled
Task dibatalkan oleh Project Manager sehingga tidak lagi menjadi bagian dari proses pekerjaan.

### 6.8 Dashboard Super Admin
Super Admin memiliki fungsi sebagai administrator sistem.

Fungsi yang tersedia meliputi:

Melihat dashboard sistem. 
Mengelola workspace. 
Mengelola akun pengguna. 
Menunjuk Project Manager. 
Melihat seluruh project. 
Melihat seluruh task. 
Melihat laporan seluruh workspace. 
Melihat statistik penyelesaian task. 
Melihat statistik project. 
Mengelola profil akun.

### 6.9 Dashboard Project Manager
Project Manager memiliki fungsi untuk mengelola operasional pada workspace yang menjadi tanggung jawabnya.

Fungsi yang tersedia meliputi:

Melihat dashboard workspace. 
Mengelola project. 
Mengelola anggota workspace. 
Membuat task. 
Mengubah task. 
Menghapus task. 
Menentukan Member yang bertanggung jawab. 
Memantau progres project. 
Memantau penyelesaian task. 
Melihat laporan workspace. 
Mengelola profil akun.

### 6.10 Dashboard Member
Member memiliki fungsi sebagai pelaksana pekerjaan.

Fungsi yang tersedia meliputi:

Melihat dashboard pribadi. 
Melihat daftar task. 
Melihat detail task. 
Mengubah status task. 
Mengunggah lampiran hasil pekerjaan. 
Melihat riwayat task. 
Mengelola profil akun.

### 6.11 Pelaporan
Sistem harus menyediakan laporan yang dapat digunakan untuk proses monitoring dan evaluasi.

Laporan yang tersedia meliputi:

Jumlah workspace. 
Jumlah project. 
Jumlah task. 
Statistik task berdasarkan status. 
Statistik progres project. 
Statistik penyelesaian task setiap Member. 
Statistik aktivitas Project Manager. 
Rekapitulasi penyelesaian task pada setiap workspace.

### 6.12 Notifikasi
Sistem menyediakan notifikasi internal agar setiap pengguna memperoleh informasi mengenai aktivitas yang berkaitan dengan pekerjaannya.

Notifikasi diberikan ketika terjadi:
Penunjukan Project Manager pada workspace. 
Penambahan Member ke dalam workspace. 
Penugasan task kepada Member. 
Perubahan status task. 
Deadline task yang akan segera berakhir. 
Penyelesaian task.

### 6.13 Ketentuan Sistem
Untuk mendukung proses bisnis, sistem menerapkan beberapa ketentuan sebagai berikut:

Setiap workspace hanya memiliki satu Project Manager aktif dalam satu waktu. 
Seorang Project Manager dapat mengelola lebih dari satu workspace. 
Setiap project berada pada satu workspace. 
Setiap task harus berada pada satu project. 
Setiap task hanya dapat ditugaskan kepada satu Member. 
Setiap perubahan status task disimpan sebagai riwayat aktivitas. 
Hanya Project Manager yang dapat membuat, mengubah, dan menghapus task. 
Hanya Super Admin yang dapat membuat workspace dan menunjuk Project Manager. 
Member hanya dapat melihat dan memperbarui task yang menjadi tanggung jawabnya. 
Sistem mencatat waktu pembuatan, perubahan, dan penyelesaian setiap task untuk keperluan monitoring dan pelaporan.

## 7. Persyaratan Non-Fungsional (Kualitatif)
### 7.1 Keamanan
Sistem harus menerapkan mekanisme keamanan yang mampu melindungi data pengguna serta aktivitas yang terjadi di dalam aplikasi.

Ketentuan keamanan yang diterapkan meliputi:

Setiap pengguna hanya dapat mengakses fitur sesuai dengan perannya. 
Password pengguna disimpan dalam bentuk terenkripsi menggunakan algoritma hashing. 
Setiap pengguna wajib melakukan autentikasi sebelum mengakses sistem. 
Seluruh aktivitas penting dicatat sebagai riwayat sistem. 
Sistem membatasi akses terhadap data workspace berdasarkan hak akses pengguna.


### 7.2 Kinerja
Sistem harus mampu memberikan performa yang baik selama digunakan.

Ketentuan kinerja meliputi:

Waktu respon halaman tidak melebihi 3 detik pada kondisi penggunaan normal. 
Sistem mampu menangani banyak project dan task tanpa menurunkan performa secara signifikan. 
Dashboard mampu menampilkan data secara cepat meskipun jumlah data terus bertambah.

### 7.3 Keandalan
Sistem harus mampu menjaga ketersediaan data selama proses operasional.

Ketentuan keandalan meliputi:

Data project dan task harus tersimpan secara konsisten. 
Sistem mampu menangani kegagalan penyimpanan tanpa menyebabkan kehilangan data. 
Backup basis data dilakukan secara berkala. 
Sistem mampu melakukan pemulihan data apabila terjadi gangguan.

### 7.4 Kemudahan Penggunaan
Antarmuka sistem harus dirancang agar mudah digunakan oleh seluruh pengguna.

Ketentuan yang diterapkan meliputi:

Tampilan antarmuka sederhana dan konsisten. 
Navigasi disesuaikan berdasarkan peran pengguna. 
Informasi penting mudah ditemukan. 
Pengguna baru dapat memahami penggunaan sistem tanpa memerlukan pelatihan yang kompleks.

### 7.5 Kompabilitas
Sistem harus dapat dijalankan pada berbagai perangkat dan peramban modern.

Sistem mendukung penggunaan pada:
Google Chrome 
Mozilla Firefox 
Microsoft Edge 
Safari 

Selain itu, aplikasi dapat diakses melalui komputer maupun perangkat seluler menggunakan browser.

### 7.6 Skalabilitas
Sistem dirancang agar mudah dikembangkan apabila kebutuhan organisasi meningkat.

Pengembangan yang dapat dilakukan di masa mendatang antara lain:

Penambahan modul baru. 
Penambahan role pengguna. 
Integrasi dengan layanan pihak ketiga. 
Pengembangan aplikasi mobile. 
Penambahan fitur notifikasi eksternal.

### 7.7 Maintability
Sistem harus mudah dipelihara dan dikembangkan.

Ketentuan maintainability meliputi:

Struktur kode mengikuti standar framework Laravel. 
Pengelolaan data dilakukan melalui panel administrasi. 
Perubahan konfigurasi dasar tidak memerlukan perubahan langsung pada kode program. 
Dokumentasi sistem tersedia untuk mempermudah proses pengembangan.

### 7.8 Audit dan Riwayat Aktivitas
Sistem harus mampu menyimpan riwayat aktivitas pengguna sebagai bentuk audit.

Riwayat aktivitas mencakup:

Pembuatan workspace. 
Perubahan data workspace. 
Pembuatan project. 
Perubahan project. 
Pembuatan task. 
Perubahan status task. 
Penugasan Member. 
Perubahan data pengguna. 
## 8. Arsitektur Tingkat Tinggi
Komponen Belakang (Back-end): Laravel 12 (berjalan pada PHP 8.2 ke atas)
Panel Administrasi (Admin UI): Filament (khusus untuk Super Admin)
Sistem Manajemen Basis Data (Database): MariaDB atau MySQL
Komponen Depan (Front-end): Blade, Tailwind CSS, Alpine.js, Livewire
Peladen Web (Web Server): Nginx
Teknologi Wadah (Containerization): Docker
Manajemen Repositori (Version Control): Git dan GitHub
Alamat Protokol Lokal (URL Lokal): https://taskflow.test
Sistem Notifikasi: WhatsApp (Fonnte API), Email (SMTP), dan Kotak Masuk Internal berbasis Basis Data

## 9. Model Data (Ringkas)
Model data TaskFlow dirancang untuk mendukung proses pengelolaan workspace, project, task, pengguna, serta pencatatan aktivitas sistem. Setiap entitas saling berhubungan sesuai dengan alur bisnis yang telah ditetapkan sehingga mampu mendukung proses monitoring dan pelaporan secara terintegrasi.

Tabel Users
Menyimpan informasi seluruh pengguna yang dapat mengakses sistem.


Tabel Workspaces
Menyimpan informasi workspace yang dibuat oleh Super Admin.

Tabel Workspace Members
Menyimpan hubungan antara workspace dengan Member.


Tabel Projects
Menyimpan informasi project pada setiap workspace.


Tabel Tasks
Menyimpan informasi task pada setiap project.


Tabel Task Histories
Menyimpan riwayat perubahan status task.


Tabel Notifications
Menyimpan notifikasi internal aplikasi.


Hubungan Antar Entitas
Hubungan antar tabel dalam sistem TaskFlow adalah sebagai berikut:
Satu Workspace dikelola oleh satu Project Manager. 
Satu Workspace dapat memiliki banyak Member. 
Satu Workspace dapat memiliki banyak Project. 
Satu Project dapat memiliki banyak Task. 
Satu Task hanya dapat ditugaskan kepada satu Member. 
Setiap Task memiliki banyak Riwayat Aktivitas. 
Setiap User dapat menerima banyak Notifikasi.


## 10. Alur Proses Bisnis
Alur proses bisnis pada aplikasi TaskFlow menggambarkan tahapan pengelolaan pekerjaan mulai dari pembentukan workspace oleh Super Admin hingga penyelesaian task oleh Member. Setiap tahapan melibatkan peran pengguna yang berbeda sesuai dengan hak akses dan tanggung jawabnya.



Tahapan Proses Bisnis

Login ke Sistem
Seluruh pengguna melakukan autentikasi menggunakan email dan password. Setelah berhasil login, sistem menampilkan dashboard sesuai dengan peran pengguna.

Super Admin Membuat Workspace
Super Admin membuat workspace baru dengan mengisi informasi dasar berupa nama workspace dan deskripsi. Workspace menjadi ruang kerja utama yang akan digunakan dalam pengelolaan project dan task.

Super Admin Menunjuk Project Manager
Setelah workspace berhasil dibuat, Super Admin menunjuk seorang Project Manager untuk mengelola workspace tersebut. Project Manager hanya memiliki akses terhadap workspace yang telah ditugaskan kepadanya.

Project Manager Mengelola Workspace
Project Manager masuk ke dalam workspace yang telah ditugaskan, kemudian melakukan pengelolaan anggota dengan menambahkan Member yang akan terlibat dalam pekerjaan.

Project Manager Membuat Project
Project Manager membuat satu atau lebih project sesuai kebutuhan pekerjaan. Setiap project memiliki nama, deskripsi, dan deadline yang menjadi acuan penyelesaian pekerjaan.

Project Manager Membuat Task
Pada setiap project, Project Manager membuat task dengan menentukan judul, deskripsi, prioritas, deadline, serta Member yang bertanggung jawab terhadap task tersebut. Setelah task dibuat, sistem mengirimkan notifikasi kepada Member yang ditugaskan.

Member Mengerjakan Task
Member melihat daftar task yang diberikan melalui dashboard pribadi. Selanjutnya Member mulai mengerjakan task dan memperbarui status pekerjaan sesuai progres yang dicapai.

Project Manager Melakukan Monitoring
Project Manager memantau perkembangan setiap task pada project yang dikelolanya. Monitoring dilakukan berdasarkan status task, progres project, serta deadline yang telah ditentukan. Apabila task telah memenuhi kebutuhan project, Project Manager mengubah status task menjadi Done.


Super Admin Melakukan Monitoring
Super Admin memantau aktivitas seluruh workspace melalui dashboard dan laporan yang tersedia. Informasi yang dapat dipantau meliputi jumlah workspace, project, task, serta perkembangan penyelesaian pekerjaan pada setiap workspace.

Penyelesaian Project
Project dinyatakan selesai apabila seluruh task yang berada di dalam project telah berstatus Done. Sistem secara otomatis memperbarui progres project berdasarkan jumlah task yang telah diselesaikan.

## 11. Teknologi
Komponen Belakang (Back-end): Laravel 12 yang berjalan pada PHP 8.2 atau versi yang lebih baru sebagai framework utama pengembangan aplikasi.
Panel Administrasi (Admin UI): Filament yang digunakan sebagai panel administrasi khusus untuk Super Admin dalam mengelola workspace, pengguna, serta monitoring sistem.
Sistem Manajemen Basis Data (Database): MariaDB atau MySQL sebagai media penyimpanan seluruh data aplikasi.
Komponen Depan (Front-end): Blade sebagai template engine, Livewire untuk membangun komponen interaktif, Tailwind CSS sebagai framework CSS, dan Alpine.js sebagai library JavaScript ringan untuk mendukung interaktivitas antarmuka.
Peladen Web (Web Server): Nginx sebagai web server yang menangani permintaan dari pengguna menuju aplikasi.
Teknologi Wadah (Containerization): Docker Compose yang digunakan untuk mengelola lingkungan pengembangan dengan mengintegrasikan layanan PHP-FPM, Nginx, dan MariaDB dalam satu konfigurasi.
Manajemen Repositori (Version Control): Git dan GitHub yang digunakan untuk mengelola versi kode sumber serta mendukung kolaborasi pengembangan.
Lingkungan Pengembangan (Development Environment): Windows Subsystem for Linux (WSL) dan Visual Studio Code sebagai lingkungan utama dalam proses pengembangan aplikasi.
Sistem Notifikasi: Sistem menyediakan notifikasi melalui tiga media, yaitu:
WhatsApp menggunakan Fonnte API. 
Email menggunakan SMTP (Simple Mail Transfer Protocol). 
Notifikasi Internal yang disimpan pada basis data dan ditampilkan melalui ikon notifikasi pada aplikasi.



## 12. Asumsi
Seluruh pengguna memiliki koneksi internet yang stabil untuk mengakses aplikasi. 
Seluruh akun pengguna telah dibuat oleh Super Admin. 
Setiap workspace hanya memiliki satu Project Manager aktif dalam satu waktu. 
Seorang Project Manager dapat mengelola lebih dari satu workspace. 
Setiap project berada pada satu workspace. 
Setiap task berada pada satu project. 
Setiap task hanya memiliki satu Member sebagai penanggung jawab utama. 
Member hanya dapat mengakses task yang menjadi tanggung jawabnya. 
Project Manager hanya dapat mengelola workspace yang telah ditugaskan kepadanya. 
Berkas lampiran yang diunggah memiliki ukuran maksimum 10 MB. 
Sistem menyimpan seluruh riwayat perubahan task sebagai aktivitas sistem. 
Progress project dihitung berdasarkan jumlah task yang telah diselesaikan.
## 13. Risiko dan Mitigasi


## 14. Kriteria Penerimaan
Sistem dinyatakan memenuhi kebutuhan pengguna apabila seluruh fungsi berikut dapat dijalankan dengan baik.

Super Admin dapat membuat, mengubah, dan menghapus workspace. 
Super Admin dapat menunjuk atau mengganti Project Manager pada setiap workspace. 
Super Admin dapat mengelola akun pengguna. 
Project Manager dapat membuat, mengubah, dan menghapus project. 
Project Manager dapat mengelola anggota workspace. 
Project Manager dapat membuat, mengubah, dan menghapus task. 
Project Manager dapat menetapkan Member sebagai penanggung jawab task. 
Member dapat melihat task yang menjadi tanggung jawabnya. 
Member dapat memperbarui status task sesuai alur proses bisnis. 
Member dapat mengunggah lampiran hasil pekerjaan. 
Project Manager dapat memantau progress project berdasarkan penyelesaian task. 
Super Admin dapat melihat laporan seluruh workspace. 
Sistem mencatat seluruh aktivitas perubahan task sebagai riwayat. 
Sistem menampilkan notifikasi internal kepada pengguna sesuai aktivitas yang terjadi. 
Dashboard menampilkan informasi sesuai hak akses masing-masing pengguna. 
Sistem dapat diakses melalui browser modern pada desktop maupun perangkat seluler.

## 15. Diagram Use Case
### 15.1 Daftar Aktor
Super Admin
Super Admin bertanggung jawab mengelola sistem secara keseluruhan, termasuk workspace, pengguna, penunjukan Project Manager, serta monitoring aktivitas seluruh workspace.

Project Manager
Project Manager bertanggung jawab mengelola project, task, anggota workspace, dan memantau progres pekerjaan pada workspace yang menjadi tanggung jawabnya.

Member
Member bertanggung jawab mengerjakan task yang diberikan oleh Project Manager serta memperbarui status pekerjaan sesuai perkembangan tugas.


