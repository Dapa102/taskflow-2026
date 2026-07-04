# TaskFlow — Product Requirement Document (PRD)

**Sistem Manajemen Tugas Kolaboratif Berbasis Web**

---

# 1. Pendahuluan

## 1.1 Latar Belakang Produk

Pengelolaan tugas merupakan salah satu proses penting dalam operasional organisasi karena berkaitan dengan koordinasi, distribusi pekerjaan, pemantauan progres, hingga evaluasi hasil kerja. Efektivitas proses tersebut sangat dipengaruhi oleh ketersediaan sistem yang mampu mendukung kolaborasi antaranggota organisasi secara terstruktur. Namun, pada banyak organisasi, proses pengelolaan tugas masih dilakukan melalui aplikasi pesan instan, surat elektronik, maupun komunikasi secara langsung. Cara tersebut menyebabkan informasi tugas, berkas hasil pekerjaan, serta riwayat komunikasi tersebar pada berbagai media sehingga sulit untuk dikelola secara terpusat.

Kondisi tersebut menimbulkan berbagai permasalahan, seperti kesulitan dalam memantau perkembangan tugas, rendahnya transparansi terhadap status pekerjaan, serta tidak tersedianya dokumentasi yang memadai mengenai proses peninjauan dan revisi. Selain itu, organisasi juga mengalami kendala dalam mengevaluasi kinerja karena perubahan status tugas, pihak yang melakukan perubahan, serta waktu perubahan tidak terdokumentasi secara sistematis. Akibatnya, proses pengambilan keputusan menjadi kurang efektif dan tingkat akuntabilitas organisasi menurun.

Permasalahan lainnya adalah proses distribusi tugas kepada *Project Manager* yang belum mempertimbangkan kapasitas kerja secara objektif. Hal tersebut berpotensi menyebabkan ketidakseimbangan beban kerja antar-*Project Manager*. Di sisi lain, belum tersedianya mekanisme pengalihan tanggung jawab ketika *Project Manager* berhalangan dapat menghambat penyelesaian tugas dan mengurangi efektivitas operasional organisasi.

TaskFlow dikembangkan sebagai sistem manajemen tugas kolaboratif berbasis web yang dirancang untuk mengintegrasikan seluruh proses pengelolaan tugas ke dalam satu platform. Sistem ini mendukung proses pembuatan tugas oleh Super Admin, distribusi tugas kepada *Project Manager*, penugasan kepada Anggota, peninjauan hasil pekerjaan, pengelolaan revisi, proses arbitrase, hingga persetujuan akhir sesuai dengan alur bisnis organisasi. Selain itu, TaskFlow menyediakan fitur pemantauan beban kerja, pengelolaan akun pengguna, pelaporan kinerja, pencatatan riwayat perubahan status tugas, serta notifikasi internal yang mendukung koordinasi antarpengguna. Dengan adanya sistem ini, proses pengelolaan tugas diharapkan menjadi lebih efektif, transparan, terdokumentasi, dan akuntabel.

## 1.2 Definisi Produk

TaskFlow merupakan sistem manajemen tugas kolaboratif berbasis web yang dikembangkan untuk membantu organisasi dalam mengelola seluruh proses distribusi, pelaksanaan, peninjauan, revisi, hingga penyelesaian tugas secara terintegrasi. Sistem ini menerapkan mekanisme pengelolaan hak akses berdasarkan peran (*Role-Based Access Control*) yang terdiri atas Super Admin, *Project Manager*, dan Anggota. Setiap pengguna memperoleh akses terhadap fitur sesuai dengan tanggung jawab dan kewenangannya dalam organisasi.

TaskFlow mendukung proses bisnis yang dimulai dari pembuatan tugas oleh Super Admin, penunjukan *Project Manager* berdasarkan beban kerja, pendistribusian tugas kepada Anggota, peninjauan hasil pekerjaan, pengelolaan revisi, proses arbitrase apabila diperlukan, hingga pemberian persetujuan akhir terhadap tugas yang telah diselesaikan. Seluruh aktivitas yang terjadi selama proses tersebut dicatat secara otomatis sebagai rekam jejak sehingga setiap perubahan dapat ditelusuri dengan mudah.

Selain mendukung pengelolaan tugas, TaskFlow menyediakan fasilitas pemantauan beban kerja *Project Manager*, pelaporan kinerja, pengelolaan akun pengguna, serta notifikasi internal untuk mendukung komunikasi antarperan. Melalui fitur-fitur tersebut, organisasi memperoleh sarana yang mampu meningkatkan efektivitas koordinasi, transparansi proses kerja, dan kualitas pengambilan keputusan berdasarkan data yang terdokumentasi.

## 1.3 Tujuan Dokumen

*Product Requirement Document* (PRD) ini disusun sebagai acuan dalam proses analisis, perancangan, pengembangan, pengujian, dan implementasi sistem TaskFlow. Dokumen ini mendefinisikan kebutuhan produk secara rinci sehingga seluruh pemangku kepentingan memiliki pemahaman yang sama mengenai tujuan pengembangan, ruang lingkup sistem, kebutuhan pengguna, serta fitur yang akan diimplementasikan.

PRD ini memuat tujuan produk, karakteristik pengguna, *user story*, *product backlog*, struktur navigasi, alur sistem, spesifikasi fitur, serta persyaratan nonfungsional yang menjadi pedoman dalam proses pengembangan perangkat lunak. Selain itu, dokumen ini berfungsi sebagai referensi dalam proses validasi dan pengujian sistem untuk memastikan bahwa seluruh fitur yang dikembangkan telah memenuhi kebutuhan bisnis sebagaimana didefinisikan dalam *Business Requirement Document* (BRD).

## 1.4 Target Pengguna

### Super Admin

Super Admin merupakan pengguna dengan tingkat hak akses tertinggi pada sistem. Peran ini bertanggung jawab membuat tugas baru, menunjuk *Project Manager*, memantau beban kerja, memberikan persetujuan akhir, melaksanakan arbitrase, mengelola akun pengguna, memindahkan tanggung jawab tugas apabila diperlukan, serta mengevaluasi kinerja *Project Manager* berdasarkan laporan yang dihasilkan sistem.

### *Project Manager*

*Project Manager* merupakan pengguna yang bertanggung jawab mengelola pelaksanaan tugas pada tingkat tim. Peran ini menerima penugasan dari Super Admin, mendistribusikan tugas kepada Anggota, melakukan peninjauan terhadap hasil pekerjaan, memberikan persetujuan atau catatan revisi, serta memantau beban kerja anggota untuk menjaga keseimbangan distribusi tugas dalam tim.

### Anggota

Anggota merupakan pengguna yang bertugas melaksanakan pekerjaan sesuai dengan penugasan yang diberikan oleh *Project Manager*. Pengguna dapat melihat tugas yang diterima, mengunggah hasil pekerjaan, menerima catatan revisi, melakukan perbaikan apabila diperlukan, serta mengunggah kembali hasil perbaikan hingga tugas memperoleh persetujuan sesuai dengan alur kerja yang telah ditetapkan.

# 2. Tujuan Produk

## 2.1 Tujuan Pengembangan Produk

TaskFlow dikembangkan untuk menyediakan sistem manajemen tugas kolaboratif yang mampu mendukung proses pengelolaan tugas secara terintegrasi mulai dari tahap pembuatan, distribusi, pelaksanaan, peninjauan, revisi, hingga penyelesaian tugas. Sistem ini dirancang untuk meningkatkan efektivitas koordinasi antar pengguna, mempercepat proses pengambilan keputusan, serta menyediakan dokumentasi aktivitas yang lengkap sebagai dasar evaluasi dan pengendalian pekerjaan.

Pengembangan TaskFlow diharapkan mampu mengatasi berbagai permasalahan dalam pengelolaan tugas yang masih dilakukan secara manual atau menggunakan berbagai media komunikasi yang terpisah. Dengan memusatkan seluruh proses ke dalam satu sistem, organisasi dapat meningkatkan efisiensi operasional, transparansi proses kerja, serta akuntabilitas setiap pengguna dalam melaksanakan tanggung jawabnya.

## 2.2 Tujuan Pengguna

TaskFlow dikembangkan untuk memenuhi kebutuhan masing-masing pengguna sesuai dengan peran dan tanggung jawabnya dalam organisasi.

### Super Admin

Bagi Super Admin, TaskFlow bertujuan menyediakan sarana untuk mengelola tugas secara menyeluruh, mulai dari pembuatan tugas, penunjukan *Project Manager*, pemantauan perkembangan tugas, pemberian persetujuan akhir, pelaksanaan arbitrase, pengelolaan akun pengguna, hingga evaluasi kinerja *Project Manager*. Dengan adanya sistem ini, Super Admin dapat melakukan pengambilan keputusan berdasarkan informasi yang terdokumentasi secara lengkap dan akurat.

### *Project Manager*

Bagi *Project Manager*, TaskFlow bertujuan membantu proses pengelolaan pelaksanaan tugas pada tingkat tim. Sistem menyediakan fasilitas untuk menerima tugas dari Super Admin, mendistribusikan tugas kepada Anggota, melakukan peninjauan hasil pekerjaan, memberikan persetujuan atau catatan revisi, serta memantau distribusi beban kerja anggota. Dengan demikian, *Project Manager* dapat mengelola pekerjaan secara lebih efektif dan menjaga keseimbangan beban kerja dalam tim.

### Anggota

Bagi Anggota, TaskFlow bertujuan menyediakan media yang memudahkan pelaksanaan tugas melalui penyampaian informasi tugas yang terstruktur, pengunggahan hasil pekerjaan, penerimaan catatan revisi, serta pengiriman hasil perbaikan. Sistem juga memungkinkan Anggota memantau perkembangan status tugas sehingga proses penyelesaian pekerjaan menjadi lebih terarah dan terdokumentasi.

## 2.3 Tujuan Organisasi

Dari perspektif organisasi, pengembangan TaskFlow bertujuan untuk meningkatkan kualitas pengelolaan tugas melalui digitalisasi proses bisnis. Sistem ini diharapkan mampu mengurangi ketergantungan terhadap media komunikasi yang tidak terintegrasi, meningkatkan transparansi proses kerja, serta mempercepat koordinasi antarunit kerja.

Selain itu, TaskFlow mendukung organisasi dalam mendistribusikan tugas secara lebih merata berdasarkan informasi beban kerja *Project Manager*, menyediakan mekanisme pengalihan tanggung jawab apabila diperlukan, serta menghasilkan laporan yang dapat digunakan sebagai dasar evaluasi kinerja dan pengambilan keputusan. Dengan demikian, organisasi memperoleh sistem yang mampu mendukung peningkatan produktivitas sekaligus menjaga keberlangsungan proses operasional.

## 2.4 Indikator Keberhasilan Produk

Keberhasilan pengembangan TaskFlow diukur berdasarkan kemampuan sistem dalam memenuhi kebutuhan pengguna dan mendukung proses bisnis organisasi. Indikator keberhasilan produk ditetapkan sebagai berikut.

| **No.** | **Indikator Keberhasilan**                                                                                        |
|---------|-------------------------------------------------------------------------------------------------------------------|
| 1       | Super Admin dapat membuat dan mengelola tugas melalui sistem.                                                     |
| 2       | Super Admin dapat menunjuk *Project Manager* berdasarkan informasi beban kerja.                                   |
| 3       | *Project Manager* dapat menerima dan mendistribusikan tugas kepada Anggota.                                       |
| 4       | Anggota dapat mengunggah hasil pekerjaan melalui sistem.                                                          |
| 5       | *Project Manager* dapat melakukan peninjauan hasil pekerjaan serta memberikan persetujuan atau catatan revisi.    |
| 6       | Sistem mampu mendukung proses revisi sesuai dengan aturan bisnis yang ditetapkan.                                 |
| 7       | Super Admin dapat melakukan persetujuan akhir maupun arbitrase terhadap tugas yang memerlukan keputusan lanjutan. |
| 8       | Sistem mencatat seluruh perubahan status tugas sebagai rekam jejak aktivitas.                                     |
| 9       | Sistem menyediakan informasi beban kerja dan laporan kinerja *Project Manager*.                                   |
| 10      | Seluruh fitur utama dapat berjalan sesuai dengan kebutuhan pengguna dan proses bisnis organisasi.                 |

# 3. Persona Pengguna

## 3.1 Pendahuluan

Persona pengguna merupakan representasi karakteristik pengguna yang menjadi sasaran utama pengembangan sistem TaskFlow. Penyusunan persona bertujuan untuk memberikan gambaran mengenai profil, kebutuhan, tujuan, serta kendala yang dihadapi oleh masing-masing pengguna selama menjalankan aktivitasnya. Informasi tersebut menjadi dasar dalam perancangan fitur, antarmuka pengguna, serta pengalaman pengguna sehingga sistem yang dikembangkan mampu memenuhi kebutuhan operasional organisasi.

TaskFlow memiliki tiga kelompok pengguna utama, yaitu Super Admin, *Project Manager*, dan Anggota. Setiap pengguna memiliki hak akses, tanggung jawab, dan kebutuhan yang berbeda sesuai dengan perannya dalam proses pengelolaan tugas.

## 3.2 Persona Super Admin

| **Atribut**                  | **Deskripsi**                                              |
|------------------------------|------------------------------------------------------------|
| Nama Persona                 | Super Admin                                                |
| Peran                        | Administrator Sistem                                       |
| Usia                         | 25–50 tahun                                                |
| Tingkat Penggunaan Teknologi | Tinggi                                                     |
| Perangkat yang Digunakan     | Laptop dan *smartphone*                                    |
| Tujuan                       | Mengelola seluruh proses manajemen tugas dalam organisasi. |

### Deskripsi

Super Admin merupakan pengguna dengan hak akses tertinggi pada sistem. Pengguna bertanggung jawab terhadap pengelolaan tugas secara menyeluruh, mulai dari pembuatan tugas, penunjukan *Project Manager*, pengelolaan akun pengguna, pelaksanaan arbitrase, hingga pemberian persetujuan akhir terhadap tugas yang telah selesai dikerjakan.

### Kebutuhan

- Membuat tugas baru.

- Melihat perkembangan seluruh tugas.

- Mengetahui beban kerja setiap *Project Manager*.

- Menunjuk *Project Manager* yang sesuai.

- Memberikan persetujuan akhir.

- Melakukan arbitrase apabila diperlukan.

- Mengelola akun pengguna.

- Memantau kinerja *Project Manager*.

### Permasalahan

- Sulit memantau perkembangan seluruh tugas secara bersamaan.

- Distribusi tugas belum mempertimbangkan kapasitas kerja *Project Manager*.

- Evaluasi kinerja masih dilakukan secara manual.

- Riwayat perubahan status belum terdokumentasi dengan baik.

### Harapan

Super Admin menginginkan sistem yang mampu memberikan informasi secara *real-time*, membantu proses pengambilan keputusan, serta menyediakan dokumentasi aktivitas yang lengkap.

## 3.3 Persona *Project Manager*

| **Atribut**                  | **Deskripsi**                                 |
|------------------------------|-----------------------------------------------|
| Nama Persona                 | *Project Manager*                             |
| Peran                        | Koordinator Tim                               |
| Usia                         | 23–45 tahun                                   |
| Tingkat Penggunaan Teknologi | Menengah hingga Tinggi                        |
| Perangkat yang Digunakan     | Laptop dan *smartphone*                       |
| Tujuan                       | Mengelola pelaksanaan tugas pada tingkat tim. |

### Deskripsi

*Project Manager* bertanggung jawab menerima tugas dari Super Admin, mendistribusikan tugas kepada Anggota, melakukan peninjauan terhadap hasil pekerjaan, memberikan persetujuan atau catatan revisi, serta memastikan seluruh pekerjaan dapat diselesaikan sesuai dengan target yang telah ditentukan.

### Kebutuhan

- Menerima tugas dari Super Admin.

- Melihat daftar anggota tim.

- Menugaskan anggota.

- Meninjau hasil pekerjaan.

- Memberikan catatan revisi.

- Menyetujui hasil pekerjaan.

- Memantau beban kerja anggota.

### Permasalahan

- Sulit mengetahui distribusi pekerjaan anggota.

- Peninjauan hasil masih dilakukan melalui berbagai media komunikasi.

- Catatan revisi tidak terdokumentasi dengan baik.

- Sulit mengetahui perkembangan seluruh tugas dalam tim.

### Harapan

*Project Manager* menginginkan sistem yang mampu membantu koordinasi tim, mempercepat proses peninjauan hasil pekerjaan, serta mempermudah pemantauan progres tugas.

## 3.4 Persona Anggota

| **Atribut**                  | **Deskripsi**                                               |
|------------------------------|-------------------------------------------------------------|
| Nama Persona                 | Anggota                                                     |
| Peran                        | Pelaksana Tugas                                             |
| Usia                         | 18–35 tahun                                                 |
| Tingkat Penggunaan Teknologi | Menengah                                                    |
| Perangkat yang Digunakan     | Laptop dan *smartphone*                                     |
| Tujuan                       | Menyelesaikan tugas sesuai dengan penugasan yang diberikan. |

### Deskripsi

Anggota merupakan pengguna yang bertugas melaksanakan pekerjaan sesuai dengan instruksi dari *Project Manager*. Pengguna dapat melihat informasi tugas, mengunggah hasil pekerjaan, menerima catatan revisi, serta mengirimkan kembali hasil perbaikan melalui sistem.

### Kebutuhan

- Melihat daftar tugas.

- Melihat detail tugas.

- Mengunggah hasil pekerjaan.

- Melihat catatan revisi.

- Mengunggah hasil perbaikan.

- Melihat status penyelesaian tugas.

### Permasalahan

- Informasi tugas tersebar pada berbagai media komunikasi.

- Sulit mengetahui versi berkas yang harus diperbaiki.

- Riwayat revisi tidak terdokumentasi.

- Tidak mengetahui perkembangan status tugas secara langsung.


### Harapan

Anggota menginginkan sistem yang menyediakan informasi tugas secara jelas, mempermudah pengiriman hasil pekerjaan, serta memungkinkan pemantauan status tugas secara langsung.

## 3.5 Ringkasan Persona Pengguna

| **Persona**       | **Peran**            | **Tujuan Utama**                             | **Fitur yang Paling Sering Digunakan**                                                                            |
|-------------------|----------------------|----------------------------------------------|-------------------------------------------------------------------------------------------------------------------|
| Super Admin       | Administrator Sistem | Mengelola seluruh proses manajemen tugas     | Pengelolaan tugas, pengelolaan *Project Manager*, persetujuan akhir, arbitrase, pengelolaan akun, laporan kinerja |
| *Project Manager* | Koordinator Tim      | Mengelola pelaksanaan tugas pada tingkat tim | Penugasan anggota, peninjauan hasil kerja, monitoring tim                                                         |
| Anggota           | Pelaksana Tugas      | Menyelesaikan tugas yang diberikan           | Tugas Saya, unggah hasil pekerjaan, revisi, unggah hasil perbaikan                                                |

# 4. User Story

## 4.1 Pendahuluan

*User Story* merupakan deskripsi singkat mengenai kebutuhan pengguna terhadap sistem dari sudut pandang masing-masing peran. Penyusunan *User Story* bertujuan untuk menggambarkan fungsi yang diharapkan oleh pengguna beserta manfaat yang diperoleh ketika fungsi tersebut tersedia. Setiap *User Story* pada dokumen ini menjadi dasar dalam penyusunan *Product Backlog* dan pengembangan fitur pada sistem TaskFlow.

TaskFlow memiliki tiga kelompok pengguna utama, yaitu Super Admin, *Project Manager*, dan Anggota. Oleh karena itu, *User Story* dikelompokkan berdasarkan peran masing-masing pengguna agar kebutuhan setiap aktor dapat diidentifikasi secara jelas.

## 4.2 User Story Super Admin

| **ID**   | ***User Story***                                                                                                                                                 | **Prioritas** |
|----------|------------------------------------------------------------------------------------------------------------------------------------------------------------------|---------------|
| US-SA-01 | Sebagai **Super Admin**, saya ingin membuat tugas baru agar pekerjaan dapat didistribusikan kepada *Project Manager*.                                            | Tinggi        |
| US-SA-02 | Sebagai **Super Admin**, saya ingin melihat daftar seluruh tugas agar dapat memantau perkembangan setiap tugas.                                                  | Tinggi        |
| US-SA-03 | Sebagai **Super Admin**, saya ingin melihat beban kerja setiap *Project Manager* agar distribusi tugas dilakukan secara seimbang.                                | Tinggi        |
| US-SA-04 | Sebagai **Super Admin**, saya ingin menunjuk *Project Manager* berdasarkan beban kerja agar setiap tugas memiliki penanggung jawab yang sesuai.                  | Tinggi        |
| US-SA-05 | Sebagai **Super Admin**, saya ingin memberikan persetujuan akhir terhadap tugas yang telah disetujui oleh *Project Manager* agar tugas dapat dinyatakan selesai. | Tinggi        |
| US-SA-06 | Sebagai **Super Admin**, saya ingin melakukan arbitrase terhadap tugas yang mengalami kebuntuan agar proses penyelesaian tetap dapat dilanjutkan.                | Tinggi        |
| US-SA-07 | Sebagai **Super Admin**, saya ingin memindahkan tugas kepada *Project Manager* lain apabila diperlukan agar pekerjaan tidak terhambat.                           | Sedang        |
| US-SA-08 | Sebagai **Super Admin**, saya ingin menunjuk pengganti *Project Manager* apabila penanggung jawab sebelumnya berhalangan agar proses bisnis tetap berjalan.      | Sedang        |
| US-SA-09 | Sebagai **Super Admin**, saya ingin mengelola akun pengguna agar keamanan dan hak akses sistem tetap terjaga.                                                    | Tinggi        |
| US-SA-10 | Sebagai **Super Admin**, saya ingin melihat laporan kinerja *Project Manager* agar dapat melakukan evaluasi berdasarkan data.                                    | Sedang        |

## 4.3 User Story Project Manager

| **ID**   | ***User Story***                                                                                                                                             | **Prioritas** |
|----------|--------------------------------------------------------------------------------------------------------------------------------------------------------------|---------------|
| US-PM-01 | Sebagai **Project Manager**, saya ingin menerima tugas dari Super Admin agar dapat mengelola pelaksanaan pekerjaan.                                          | Tinggi        |
| US-PM-02 | Sebagai **Project Manager**, saya ingin melihat daftar anggota agar dapat menentukan pelaksana tugas yang sesuai.                                            | Tinggi        |
| US-PM-03 | Sebagai **Project Manager**, saya ingin mendistribusikan tugas kepada anggota agar pekerjaan dapat dilaksanakan sesuai pembagian tugas.                      | Tinggi        |
| US-PM-04 | Sebagai **Project Manager**, saya ingin meninjau hasil pekerjaan anggota agar dapat memastikan kualitas pekerjaan sebelum diteruskan kepada Super Admin.     | Tinggi        |
| US-PM-05 | Sebagai **Project Manager**, saya ingin memberikan catatan revisi apabila hasil pekerjaan belum sesuai agar anggota mengetahui bagian yang harus diperbaiki. | Tinggi        |
| US-PM-06 | Sebagai **Project Manager**, saya ingin menyetujui hasil pekerjaan yang telah memenuhi ketentuan agar dapat diteruskan kepada Super Admin.                   | Tinggi        |
| US-PM-07 | Sebagai **Project Manager**, saya ingin memantau beban kerja anggota agar distribusi tugas tetap seimbang.                                                   | Sedang        |

## 4.4 User Story Anggota

| **ID**   | ***User Story***                                                                                                                   | **Prioritas** |
|----------|------------------------------------------------------------------------------------------------------------------------------------|---------------|
| US-AG-01 | Sebagai **Anggota**, saya ingin melihat daftar tugas yang diberikan kepada saya agar mengetahui pekerjaan yang harus diselesaikan. | Tinggi        |
| US-AG-02 | Sebagai **Anggota**, saya ingin melihat detail tugas agar memahami kebutuhan pekerjaan yang harus dilakukan.                       | Tinggi        |
| US-AG-03 | Sebagai **Anggota**, saya ingin mengunggah hasil pekerjaan agar *Project Manager* dapat melakukan peninjauan.                      | Tinggi        |
| US-AG-04 | Sebagai **Anggota**, saya ingin menerima catatan revisi agar mengetahui bagian yang harus diperbaiki.                              | Tinggi        |
| US-AG-05 | Sebagai **Anggota**, saya ingin mengunggah kembali hasil perbaikan agar tugas dapat ditinjau ulang oleh *Project Manager*.         | Tinggi        |
| US-AG-06 | Sebagai **Anggota**, saya ingin melihat status tugas agar mengetahui perkembangan proses penyelesaian pekerjaan.                   |               |


# 5. Product Backlog

## 5.1 Pendahuluan

*Product Backlog* merupakan daftar kebutuhan produk yang akan dikembangkan pada sistem TaskFlow. Penyusunan *Product Backlog* dilakukan berdasarkan *User Story*, kebutuhan pengguna, serta persyaratan fungsional yang telah didefinisikan pada *Business Requirement Document* (BRD). Setiap *backlog item* merepresentasikan fitur atau modul utama yang akan diimplementasikan selama proses pengembangan sistem.

Selain menjadi dasar penyusunan rencana pengembangan, *Product Backlog* juga berfungsi sebagai acuan dalam menentukan prioritas pengerjaan, pembagian iterasi, serta keterlacakan antara kebutuhan bisnis dan implementasi sistem.

## 5.2 Daftar *Product Backlog*

| **ID** | ***Epic***                  | ***Product Backlog***                                                                                                             | **Prioritas** | **Referensi *User Story***             |
|--------|-----------------------------|-----------------------------------------------------------------------------------------------------------------------------------|---------------|----------------------------------------|
| PB-01  | Manajemen Tugas             | Super Admin dapat membuat, melihat, mengubah, dan mengelola tugas induk.                                                          | Tinggi        | US-SA-01, US-SA-02                     |
| PB-02  | Manajemen *Project Manager* | Super Admin dapat melihat beban kerja, menunjuk *Project Manager*, memindahkan tugas, serta menunjuk pengganti *Project Manager*. | Tinggi        | US-SA-03, US-SA-04, US-SA-07, US-SA-08 |
| PB-03  | Persetujuan Tugas           | Super Admin dapat memberikan persetujuan akhir terhadap tugas yang telah selesai ditinjau.                                        | Tinggi        | US-SA-05                               |
| PB-04  | Arbitrase                   | Super Admin dapat melakukan arbitrase terhadap tugas yang mengalami kebuntuan akibat revisi.                                      | Tinggi        | US-SA-06                               |
| PB-05  | Manajemen Pengguna          | Super Admin dapat mengelola akun pengguna sesuai dengan hak akses yang dimiliki.                                                  | Tinggi        | US-SA-09                               |
| PB-06  | Monitoring Kinerja          | Super Admin dapat melihat laporan kinerja dan beban kerja *Project Manager*.                                                      | Sedang        | US-SA-10                               |
| PB-07  | Penugasan Anggota           | *Project Manager* menerima tugas dari Super Admin dan mendistribusikannya kepada anggota.                                         | Tinggi        | US-PM-01, US-PM-02, US-PM-03           |
| PB-08  | Peninjauan Hasil Kerja      | *Project Manager* dapat meninjau hasil pekerjaan anggota sebelum memberikan keputusan.                                            | Tinggi        | US-PM-04                               |
| PB-09  | Revisi Tugas                | *Project Manager* memberikan catatan revisi dan anggota melakukan perbaikan terhadap hasil pekerjaan.                             | Tinggi        | US-PM-05, US-AG-04, US-AG-05           |
| PB-10  | Monitoring Tim              | *Project Manager* memantau distribusi beban kerja anggota.                                                                        | Sedang        | US-PM-07                               |
| PB-11  | Pelaksanaan Tugas           | Anggota melihat tugas, mengerjakan tugas, dan mengunggah hasil pekerjaan.                                                         | Tinggi        | US-AG-01, US-AG-02, US-AG-03           |
| PB-12  | Monitoring Status Tugas     | Anggota dapat melihat perkembangan status tugas yang sedang dikerjakan.                                                           | Sedang        | US-AG-06                               |

## 5.3 Prioritas Pengembangan

Prioritas pengembangan ditentukan berdasarkan tingkat kepentingan setiap fitur terhadap proses bisnis utama TaskFlow.

| **Prioritas** | **Deskripsi**                                                                                   |
|---------------|-------------------------------------------------------------------------------------------------|
| Tinggi        | Fitur wajib yang harus tersedia agar proses bisnis utama dapat berjalan dengan baik.            |
| Sedang        | Fitur pendukung yang meningkatkan efektivitas penggunaan sistem dan kualitas pengelolaan tugas. |

Sebagian besar *Product Backlog* memiliki prioritas tinggi karena berkaitan langsung dengan proses bisnis utama, yaitu pengelolaan tugas, distribusi pekerjaan, peninjauan hasil, revisi, arbitrase, dan persetujuan akhir.

## 5.4 Perencanaan Iterasi

### Iterasi 1

Fokus pada pembangunan fungsi dasar sistem yang berkaitan dengan pengelolaan tugas oleh Super Admin.

**Backlog yang dikembangkan:**

- PB-01 Manajemen Tugas

- PB-02 Manajemen *Project Manager*

### Iterasi 2

Fokus pada proses penugasan dan pelaksanaan tugas.

**Backlog yang dikembangkan:**

- PB-07 Penugasan Anggota

- PB-11 Pelaksanaan Tugas

### Iterasi 3

Fokus pada proses peninjauan hasil pekerjaan dan revisi.

**Backlog yang dikembangkan:**

- PB-08 Peninjauan Hasil Kerja

- PB-09 Revisi Tugas

### Iterasi 4

Fokus pada penyelesaian tugas dan pengambilan keputusan.

**Backlog yang dikembangkan:**

- PB-03 Persetujuan Tugas

- PB-04 Arbitrase

### Iterasi 5

Fokus pada pengelolaan sistem dan monitoring.

**Backlog yang dikembangkan:**

- PB-05 Manajemen Pengguna

- PB-06 Monitoring Kinerja

- PB-10 Monitoring Tim

- PB-12 Monitoring Status Tugas


## 5.5 *Minimum Viable Product* (MVP)

*Minimum Viable Product* merupakan sekumpulan fitur utama yang harus tersedia agar TaskFlow dapat digunakan sesuai dengan kebutuhan bisnis organisasi.

Fitur yang termasuk dalam *Minimum Viable Product* meliputi:

- PB-01 Manajemen Tugas

- PB-02 Manajemen *Project Manager*

- PB-03 Persetujuan Tugas

- PB-07 Penugasan Anggota

- PB-08 Peninjauan Hasil Kerja

- PB-09 Revisi Tugas

- PB-11 Pelaksanaan Tugas

Fitur monitoring, pelaporan, dan pengelolaan akun pengguna dapat dikembangkan pada iterasi berikutnya tanpa memengaruhi proses bisnis utama sistem.

## 5.6 Keterlacakan Kebutuhan (*Requirements Traceability*)

Untuk memastikan setiap kebutuhan pengguna diimplementasikan ke dalam sistem, setiap *Product Backlog* dipetakan terhadap *User Story* yang menjadi dasar penyusunannya.

| ***User Story***                       | ***Product Backlog*** |
|----------------------------------------|-----------------------|
| US-SA-01, US-SA-02                     | PB-01                 |
| US-SA-03, US-SA-04, US-SA-07, US-SA-08 | PB-02                 |
| US-SA-05                               | PB-03                 |
| US-SA-06                               | PB-04                 |
| US-SA-09                               | PB-05                 |
| US-SA-10                               | PB-06                 |
| US-PM-01, US-PM-02, US-PM-03           | PB-07                 |
| US-PM-04                               | PB-08                 |
| US-PM-05, US-AG-04, US-AG-05           | PB-09                 |
| US-PM-07                               | PB-10                 |
| US-AG-01, US-AG-02, US-AG-03           | PB-11                 |
| US-AG-06                               | PB-12                 |

# 6. Sitemap

## 6.1 Pendahuluan

*Sitemap* merupakan representasi struktur navigasi yang menggambarkan hubungan antarhalaman dalam sistem TaskFlow. Penyusunan *sitemap* bertujuan untuk memberikan gambaran mengenai organisasi menu, hubungan antarfitur, serta alur navigasi yang akan diakses oleh setiap pengguna sesuai dengan hak akses yang dimiliki.

TaskFlow menerapkan mekanisme pengelolaan hak akses berdasarkan peran (*Role-Based Access Control*), yaitu Super Admin, *Project Manager*, dan Anggota. Setiap pengguna memiliki struktur navigasi yang berbeda sesuai dengan tanggung jawabnya dalam proses bisnis. Oleh karena itu, *sitemap* disusun berdasarkan masing-masing peran agar alur penggunaan sistem menjadi lebih jelas dan mudah dipahami.

## 6.2 Struktur Navigasi Sistem

Secara umum, struktur navigasi TaskFlow diawali dengan proses autentikasi pengguna. Setelah pengguna berhasil masuk ke dalam sistem, halaman *dashboard* akan ditampilkan sesuai dengan peran yang dimiliki. Dari halaman tersebut, pengguna dapat mengakses berbagai fitur yang berkaitan dengan tugas dan tanggung jawabnya.

Struktur navigasi sistem dibagi menjadi tiga kelompok utama sebagai berikut.

- Super Admin

- *Project Manager*

- Anggota

Pembagian tersebut bertujuan untuk menjaga keamanan sistem sekaligus memastikan bahwa setiap pengguna hanya dapat mengakses fitur sesuai dengan hak akses yang dimiliki.

## 6.3 Diagram *Sitemap*

Diagram *sitemap* menggambarkan hubungan hierarkis antarhalaman pada sistem TaskFlow. Diagram ini menjadi acuan dalam proses perancangan antarmuka (*User Interface*) dan implementasi navigasi sistem.

![image20.png](assets/prd/image20.png)

> **Gambar 6.1 Diagram Sitemap TaskFlow**

**Keterangan:**

- Super Admin memiliki akses terhadap seluruh modul administrasi dan pengelolaan tugas.

- *Project Manager* memiliki akses terhadap modul penugasan, peninjauan, dan monitoring tim.

- Anggota memiliki akses terhadap modul pelaksanaan tugas dan revisi.

## 6.4 Struktur Navigasi Super Admin

Super Admin merupakan pengguna dengan hak akses tertinggi pada sistem. Menu yang tersedia dirancang untuk mendukung proses pengelolaan tugas secara menyeluruh, pengelolaan pengguna, serta pengambilan keputusan terhadap proses bisnis organisasi.

| **Menu**        | **Deskripsi**                                                 |
|-----------------|---------------------------------------------------------------|
| Dashboard       | Menampilkan ringkasan aktivitas sistem.                       |
| Tugas           | Mengelola seluruh tugas yang terdapat pada sistem.            |
| Project Manager | Mengelola data, beban kerja, dan penugasan *Project Manager*. |
| Pengguna        | Mengelola akun pengguna.                                      |
| Laporan         | Menampilkan laporan kinerja, beban kerja, dan riwayat tugas.  |
| Profil          | Mengelola informasi akun pengguna.                            |

## 6.5 Struktur Navigasi *Project Manager*

*Project Manager* berperan sebagai koordinator yang mengelola pelaksanaan tugas pada tingkat tim. Struktur navigasi difokuskan pada proses distribusi pekerjaan dan peninjauan hasil kerja.

| **Menu**          | **Deskripsi**                            |
|-------------------|------------------------------------------|
| Dashboard         | Menampilkan ringkasan aktivitas tim.     |
| Tugas Masuk       | Menampilkan tugas dari Super Admin.      |
| Penugasan Anggota | Menugaskan anggota ke dalam suatu tugas. |
| Daftar Anggota    | Menampilkan daftar anggota tim.          |
| Peninjauan Hasil  | Meninjau hasil pekerjaan anggota.        |
| Monitoring Tim    | Memantau beban kerja anggota.            |
| Profil            | Mengelola informasi akun pengguna.       |

## 6.6 Struktur Navigasi Anggota

Anggota merupakan pelaksana tugas yang menerima penugasan dari *Project Manager*. Struktur navigasi dibuat lebih sederhana agar pengguna dapat berfokus pada penyelesaian tugas.

| **Menu**         | **Deskripsi**                                                 |
|------------------|---------------------------------------------------------------|
| Dashboard        | Menampilkan ringkasan tugas yang sedang dikerjakan.           |
| Tugas Saya       | Menampilkan daftar tugas yang diterima.                       |
| Detail Tugas     | Menampilkan informasi detail tugas.                           |
| Unggah Hasil     | Mengirimkan hasil pekerjaan kepada *Project Manager*.         |
| Revisi           | Melihat catatan revisi yang diberikan oleh *Project Manager*. |
| Unggah Perbaikan | Mengirimkan hasil revisi.                                     |
| Profil           | Mengelola informasi akun pengguna.                            |

## 6.7 Hubungan *Sitemap* dengan *Product Backlog*

Struktur navigasi pada TaskFlow disusun berdasarkan modul yang terdapat pada *Product Backlog*. Hubungan tersebut ditunjukkan pada Tabel 6.1.

**Tabel 6.1 Hubungan *Product Backlog* dengan Struktur Navigasi**

| **Product Backlog**               | **Menu**          |
|-----------------------------------|-------------------|
| PB-01 Manajemen Tugas             | Tugas             |
| PB-02 Manajemen *Project Manager* | Project Manager   |
| PB-03 Persetujuan Tugas           | Persetujuan Akhir |
| PB-04 Arbitrase                   | Arbitrase         |
| PB-05 Manajemen Pengguna          | Pengguna          |
| PB-06 Monitoring Kinerja          | Laporan           |
| PB-07 Penugasan Anggota           | Penugasan Anggota |
| PB-08 Peninjauan Hasil Kerja      | Peninjauan Hasil  |
| PB-09 Revisi Tugas                | Revisi            |
| PB-10 Monitoring Tim              | Monitoring Tim    |
| PB-11 Pelaksanaan Tugas           | Tugas Saya        |
| PB-12 Monitoring Status Tugas     | Dashboard         |

## 6.8 Alur Navigasi Pengguna

Proses navigasi pengguna pada TaskFlow dimulai ketika pengguna berhasil melakukan autentikasi. Selanjutnya, sistem akan mengidentifikasi peran pengguna dan menampilkan *dashboard* sesuai dengan hak akses yang dimiliki. Dari halaman tersebut, pengguna dapat mengakses seluruh menu yang berkaitan dengan tugas dan tanggung jawabnya.

Setiap aktivitas yang dilakukan akan mengarahkan pengguna ke halaman yang sesuai tanpa memberikan akses terhadap menu milik peran lain. Pendekatan ini mendukung keamanan sistem, menjaga konsistensi proses bisnis, serta mempermudah pengguna dalam mengoperasikan aplikasi.

# 7. Alur Sistem

## 7.1 Pendahuluan

Alur sistem menjelaskan proses bisnis yang terjadi pada TaskFlow mulai dari pembuatan tugas hingga penyelesaian tugas. Bagian ini bertujuan memberikan gambaran mengenai interaksi antaraktor, perubahan status tugas, serta urutan aktivitas yang dilakukan oleh setiap pengguna selama menggunakan sistem.

Penyusunan alur sistem menjadi dasar dalam proses pengembangan karena menggambarkan bagaimana setiap fitur saling berhubungan untuk mendukung proses bisnis organisasi. Selain itu, alur sistem juga menjadi acuan dalam penyusunan *Activity Diagram*, *Sequence Diagram*, implementasi sistem, serta proses pengujian.

## 7.2 Alur Umum Sistem

Secara umum, proses bisnis pada TaskFlow terdiri atas lima tahapan utama, yaitu:

1.  Pembuatan tugas oleh Super Admin.

2.  Distribusi tugas kepada *Project Manager*.

3.  Penugasan tugas kepada Anggota.

4.  Peninjauan hasil pekerjaan.

5.  Penyelesaian tugas melalui persetujuan akhir atau arbitrase.

Setiap tahapan akan menghasilkan perubahan status tugas yang dicatat secara otomatis oleh sistem sehingga seluruh aktivitas dapat ditelusuri melalui riwayat perubahan status.

## 7.3 Diagram Alur Sistem

Diagram berikut menggambarkan alur proses bisnis TaskFlow secara menyeluruh mulai dari pembuatan tugas hingga tugas dinyatakan selesai.

![image19.png](assets/prd/image19.png)

> **Gambar 7.1 Activity Diagram TaskFlow**

Diagram tersebut memperlihatkan hubungan aktivitas antara Super Admin, *Project Manager*, Anggota, dan sistem selama proses pengelolaan tugas berlangsung.

## 7.4 Alur Pengelolaan Tugas

Proses pengelolaan tugas diawali ketika Super Admin membuat tugas baru dengan mengisi informasi yang diperlukan, seperti judul tugas, deskripsi, prioritas, dan tenggat waktu. Setelah tugas dibuat, sistem memberikan status **Draft**.

Selanjutnya, Super Admin melihat informasi beban kerja seluruh *Project Manager* sebagai dasar dalam menentukan penanggung jawab tugas. Setelah *Project Manager* dipilih, sistem mengubah status tugas menjadi **Assigned to Project Manager** dan mengirimkan notifikasi kepada *Project Manager* yang bersangkutan.

## 7.5 Alur Penugasan Anggota

*Project Manager* menerima tugas yang diberikan oleh Super Admin melalui halaman **Tugas Masuk**. Selanjutnya, *Project Manager* melihat daftar anggota tim beserta beban kerja masing-masing untuk menentukan anggota yang paling sesuai.

Setelah anggota dipilih, sistem mengubah status tugas menjadi **Assigned to Member**. Anggota kemudian menerima notifikasi dan dapat melihat tugas tersebut melalui menu **Tugas Saya**.

## 7.6 Alur Pelaksanaan Tugas

Anggota membuka detail tugas, mempelajari informasi yang diberikan, kemudian melaksanakan pekerjaan sesuai dengan ketentuan yang telah ditetapkan.

Setelah pekerjaan selesai, Anggota mengunggah hasil pekerjaan melalui sistem dan mengirimkannya kepada *Project Manager*. Sistem kemudian mengubah status tugas menjadi **Pending Review** sebagai tanda bahwa tugas sedang menunggu proses peninjauan.

## 7.7 Alur Peninjauan Hasil

*Project Manager* melakukan pemeriksaan terhadap hasil pekerjaan yang dikirimkan oleh Anggota.

Apabila hasil pekerjaan telah memenuhi ketentuan, *Project Manager* memberikan persetujuan sehingga status tugas berubah menjadi **Pending Final Approval** dan diteruskan kepada Super Admin.

Sebaliknya, apabila hasil pekerjaan belum memenuhi ketentuan, *Project Manager* memberikan catatan revisi yang berisi informasi mengenai bagian yang perlu diperbaiki. Sistem kemudian mengubah status tugas menjadi **Revision Required** dan mengirimkan notifikasi kepada Anggota.

## 7.8 Alur Revisi dan Arbitrase

Setelah menerima catatan revisi, Anggota melakukan perbaikan terhadap hasil pekerjaan sesuai dengan masukan yang diberikan oleh *Project Manager*. Hasil perbaikan kemudian diunggah kembali melalui sistem untuk dilakukan peninjauan ulang.

Sistem mencatat jumlah revisi yang telah dilakukan. Apabila jumlah revisi telah mencapai batas maksimum dan hasil pekerjaan masih belum memenuhi ketentuan, status tugas berubah menjadi **Pending Arbitration**.

Pada tahap ini, Super Admin meninjau riwayat revisi beserta hasil pekerjaan yang telah dikirimkan. Berdasarkan hasil peninjauan tersebut, Super Admin dapat memberikan keputusan berupa persetujuan atau meminta proses revisi kembali sesuai dengan kondisi yang dihadapi.

## 7.9 Alur Persetujuan Akhir

Setelah tugas memperoleh persetujuan dari *Project Manager*, Super Admin melakukan pemeriksaan akhir terhadap hasil pekerjaan.

Apabila seluruh persyaratan telah dipenuhi, Super Admin memberikan persetujuan akhir sehingga status tugas berubah menjadi **Done**. Sistem kemudian mengarsipkan seluruh informasi tugas beserta riwayat aktivitas sebagai dokumentasi yang dapat digunakan untuk keperluan pelaporan dan evaluasi.

## 7.10 Perubahan Status Tugas

Perubahan status tugas digunakan sebagai indikator perkembangan proses bisnis pada sistem. Setiap perubahan status dilakukan secara otomatis berdasarkan aktivitas pengguna.

| **Status**                      | **Deskripsi**                                   |
|---------------------------------|-------------------------------------------------|
| **Draft**                       | Tugas baru dibuat oleh Super Admin.             |
| **Assigned to Project Manager** | Tugas telah diberikan kepada *Project Manager*. |
| **Assigned to Member**          | Tugas telah didistribusikan kepada Anggota.     |
| **Pending Review**              | Menunggu peninjauan oleh *Project Manager*.     |
| **Revision Required**           | Menunggu perbaikan dari Anggota.                |
| **Pending Arbitration**         | Menunggu keputusan arbitrase dari Super Admin.  |
| **Pending Final Approval**      | Menunggu persetujuan akhir dari Super Admin.    |
| **Done**                        | Tugas telah selesai.                            |
| **Cancelled**                   | Tugas dibatalkan.                               |

## 7.11 Ringkasan Alur Sistem

Tabel berikut menjelaskan ringkasan aktivitas setiap aktor dalam proses bisnis TaskFlow.

| **Aktor**           | **Aktivitas Utama**                                                                                                                     |
|---------------------|-----------------------------------------------------------------------------------------------------------------------------------------|
| **Super Admin**     | Membuat tugas, menunjuk *Project Manager*, melakukan arbitrase, memberikan persetujuan akhir, mengelola pengguna, dan memantau kinerja. |
| **Project Manager** | Menerima tugas, menugaskan anggota, meninjau hasil pekerjaan, memberikan revisi atau persetujuan, serta memantau beban kerja tim.       |
| **Anggota**         | Mengerjakan tugas, mengunggah hasil pekerjaan, menerima revisi, memperbaiki pekerjaan, dan mengunggah hasil revisi.                     |

# 8. Wireframe dan Mockup

## 8.1 Pendahuluan

Wireframe merupakan rancangan awal antarmuka (*low-fidelity*) yang digunakan untuk menggambarkan struktur halaman, tata letak komponen, serta alur interaksi pengguna sebelum dilakukan proses desain visual (*mockup*). Wireframe berfungsi sebagai acuan dalam proses pengembangan antarmuka agar setiap halaman memiliki struktur yang konsisten dan sesuai dengan kebutuhan pengguna.

Pada TaskFlow, wireframe disusun berdasarkan kebutuhan setiap aktor, yaitu Super Admin, *Project Manager*, dan Anggota. Setiap rancangan menampilkan komponen utama yang akan digunakan pada proses implementasi tanpa memperhatikan aspek visual seperti warna, ikon, maupun tipografi.

## 8.2 Wireframe Halaman Login
### Tujuan
Merancang halaman autentikasi yang digunakan oleh seluruh pengguna untuk mengakses sistem TaskFlow.

### Aktor
- Super Admin

- *Project Manager*

- Anggota

### Deskripsi
Halaman *login* merupakan halaman pertama yang diakses pengguna sebelum memasuki sistem. Pengguna diminta memasukkan alamat surat elektronik dan kata sandi yang telah terdaftar. Sistem akan memverifikasi data tersebut dan mengarahkan pengguna ke *dashboard* sesuai dengan perannya.

### Komponen Antarmuka
| **Komponen**     | **Fungsi**                                      |
|------------------|-------------------------------------------------|
| Logo TaskFlow    | Identitas sistem                                |
| Kolom Email      | Memasukkan alamat surat elektronik              |
| Kolom Kata Sandi | Memasukkan kata sandi                           |
| Tombol Masuk     | Mengirim data autentikasi                       |
| Pesan Kesalahan  | Menampilkan informasi apabila autentikasi gagal |

### Alur Penggunaan
1.  Pengguna membuka halaman *login*.

2.  Pengguna memasukkan alamat surat elektronik.

3.  Pengguna memasukkan kata sandi.

4.  Pengguna menekan tombol **Masuk**.

5.  Sistem melakukan autentikasi.

6.  Pengguna diarahkan ke *dashboard* sesuai dengan hak akses.

![image15.png](assets/prd/image15.png)

**Gambar 8.1 Wireframe Halaman Login**

## 8.3 Wireframe Dashboard Super Admin
### Tujuan
Merancang halaman utama Super Admin sebagai pusat pengelolaan sistem.

### Aktor
- Super Admin

### Deskripsi
Dashboard Super Admin menampilkan ringkasan informasi mengenai jumlah tugas, tugas yang sedang berjalan, tugas yang menunggu persetujuan, informasi beban kerja *Project Manager*, serta menu utama sistem.

### Komponen Antarmuka
| **Komponen**         | **Fungsi**                             |
|----------------------|----------------------------------------|
| Sidebar              | Navigasi menu                          |
| Navbar               | Informasi pengguna                     |
| Ringkasan Statistik  | Menampilkan jumlah tugas               |
| Grafik Kinerja       | Menampilkan performa *Project Manager* |
| Daftar Tugas Terbaru | Menampilkan aktivitas terbaru          |

### Alur Penggunaan
1.  Super Admin berhasil masuk ke sistem.

2.  Dashboard ditampilkan.

3.  Super Admin memilih menu yang diinginkan.

![image14.png](assets/prd/image14.png)

**Gambar 8.2 Wireframe Dashboard Super Admin**

## 8.4 Wireframe Daftar Tugas
### Tujuan
Merancang halaman yang menampilkan seluruh tugas pada sistem.

### Aktor
- Super Admin

### Deskripsi
Halaman ini digunakan untuk melihat seluruh tugas, melakukan pencarian, penyaringan data, serta membuka detail tugas.

### Komponen Antarmuka
- Tabel tugas

- Pencarian

- Filter status

- Filter prioritas

- Tombol Tambah Tugas

- Tombol Detail

- Pagination

![image22.png](assets/prd/image22.png)

**Gambar 8.3 Wireframe Daftar Tugas**

## 8.5 Wireframe Tambah Tugas
Menjelaskan formulir pembuatan tugas.

![image12.png](assets/prd/image12.png)

**Gambar 8.4 Wireframe Tambah Tugas**

## 8.6 Wireframe Detail Tugas
Menampilkan seluruh informasi tugas.

![image11.png](assets/prd/image11.png)

**Gambar 8.5 Wireframe Detail Tugas**

## 8.7 Wireframe Penunjukan *Project Manager*
Menampilkan daftar *Project Manager* beserta beban kerja sebagai dasar penunjukan.

![image13.png](assets/prd/image13.png)

**Gambar 8.6 Wireframe Penunjukan Project Manager**

## 8.8 Wireframe Persetujuan Akhir
Halaman untuk melakukan persetujuan akhir terhadap tugas.

![image16.png](assets/prd/image16.png)

**Gambar 8.7 Wireframe Persetujuan Akhir**

## 8.9 Wireframe Arbitrase
Halaman untuk menyelesaikan konflik revisi.

![image10.png](assets/prd/image10.png)

**Gambar 8.8 Wireframe Arbitrase**

## 8.10 Wireframe Kelola Pengguna
Halaman pengelolaan akun pengguna.

![image18.png](assets/prd/image18.png)

**Gambar 8.9 Wireframe Kelola Pengguna**

## 8.11 Wireframe Monitoring Kinerja
Menampilkan grafik dan laporan performa *Project Manager*.

![image5.png](assets/prd/image5.png)

**Gambar 8.10 Wireframe Monitoring Kinerja**

## 8.12 Wireframe Dashboard *Project Manager*
Halaman utama *Project Manager*.

![image1.png](assets/prd/image1.png)

**Gambar 8.11 Wireframe Dashboard Project Manager**

## 8.13 Wireframe Penugasan Anggota
Halaman untuk memilih anggota yang akan menerima tugas.

![image17.png](assets/prd/image17.png)

**Gambar 8.12 Wireframe Penugasan Anggota**

## 8.14 Wireframe Peninjauan Hasil
Halaman untuk melihat hasil pekerjaan anggota dan memberikan keputusan.

![image8.png](assets/prd/image8.png)

**Gambar 8.13 Wireframe Peninjauan Hasil**

## 8.15 Wireframe Monitoring Tim
Halaman yang menampilkan distribusi tugas anggota.

![image21.png](assets/prd/image21.png)

**Gambar 8.14 Wireframe Monitoring Tim**

## 8.16 Wireframe Dashboard Anggota
Halaman utama Anggota.

![image9.png](assets/prd/image9.png)

**Gambar 8.15 Wireframe Dashboard Anggota**

## 8.17 Wireframe Tugas Saya
Halaman yang menampilkan seluruh tugas milik Anggota.

![image2.png](assets/prd/image2.png)

**Gambar 8.16 Wireframe Tugas Saya**

## 8.18 Wireframe Detail Tugas
Halaman yang menampilkan informasi lengkap tugas.

![image6.png](assets/prd/image6.png)

**Gambar 8.17 Wireframe Detail Tugas Anggota**

## 8.19 Wireframe Unggah Hasil
Halaman untuk mengunggah hasil pekerjaan.

![image4.png](assets/prd/image4.png)

**Gambar 8.18 Wireframe Unggah Hasil**

## 8.20 Wireframe Revisi
Halaman untuk melihat catatan revisi.

![image3.png](assets/prd/image3.png)

**Gambar 8.19 Wireframe Revisi**

## 8.21 Wireframe Unggah Perbaikan
Halaman untuk mengunggah hasil perbaikan setelah revisi.

![image7.png](assets/prd/image7.png)

**Gambar 8.20 Wireframe Unggah Perbaikan**

# 9. Minimum Viable Product (MVP) dan Roadmap Pengembangan

Bab ini menjelaskan fitur minimum yang harus tersedia pada versi awal sistem serta rencana pengembangan fitur pada versi berikutnya. Penyusunan MVP bertujuan untuk memastikan bahwa proses bisnis utama TaskFlow dapat berjalan secara optimal sebelum dilakukan pengembangan fitur tambahan pada versi selanjutnya.

## 9.1 Minimum Viable Product (MVP)
*Minimum Viable Product* (MVP) merupakan versi awal sistem yang berisi fitur-fitur inti yang wajib tersedia agar sistem TaskFlow dapat digunakan oleh Super Admin, *Project Manager*, dan Anggota sesuai dengan kebutuhan proses bisnis organisasi.




### Fitur MVP

| **No.** | **Fitur**                   | **Keterangan**                                                       |
|---------|-----------------------------|----------------------------------------------------------------------|
| 1       | Login                       | Autentikasi pengguna berdasarkan peran.                              |
| 2       | Dashboard Super Admin       | Menampilkan ringkasan aktivitas sistem dan statistik tugas.          |
| 3       | Manajemen Tugas             | Super Admin membuat, mengubah, melihat, dan menghapus tugas.         |
| 4       | Manajemen *Project Manager* | Menunjuk *Project Manager* berdasarkan beban kerja.                  |
| 5       | Dashboard *Project Manager* | Menampilkan ringkasan tugas dan aktivitas tim.                       |
| 6       | Penugasan Anggota           | *Project Manager* menugaskan anggota pada suatu tugas.               |
| 7       | Dashboard Anggota           | Menampilkan tugas yang diterima oleh anggota.                        |
| 8       | Pelaksanaan Tugas           | Anggota mengunggah hasil pekerjaan.                                  |
| 9       | Peninjauan Hasil            | *Project Manager* meninjau hasil pekerjaan dan memberikan keputusan. |
| 10      | Revisi Tugas                | Anggota menerima revisi dan mengunggah hasil perbaikan.              |
| 11      | Persetujuan Akhir           | Super Admin memberikan persetujuan akhir terhadap tugas.             |
| 12      | Arbitrase                   | Super Admin menyelesaikan konflik revisi apabila diperlukan.         |
| 13      | Notifikasi Email            | Sistem mengirim pemberitahuan perubahan status tugas melalui SMTP.   |

### Tujuan MVP

Versi MVP bertujuan untuk memastikan seluruh proses utama manajemen tugas dapat berjalan secara digital, mulai dari pembuatan tugas oleh Super Admin, pendistribusian kepada *Project Manager*, penugasan kepada Anggota, pelaksanaan pekerjaan, proses peninjauan dan revisi, hingga persetujuan akhir serta arbitrase melalui sistem TaskFlow.

## 9.2 Roadmap Pengembangan
Roadmap pengembangan digunakan sebagai acuan dalam pengembangan fitur pada versi berikutnya setelah seluruh fitur MVP berhasil diimplementasikan.

### Versi 1.0 (MVP)
Fokus pada digitalisasi proses manajemen tugas kolaboratif.

#### Fitur
- Login

- Dashboard Super Admin

- Manajemen Tugas

- Manajemen *Project Manager*

- Dashboard *Project Manager*

- Penugasan Anggota

- Dashboard Anggota

- Pelaksanaan Tugas

- Peninjauan Hasil

- Revisi Tugas

- Persetujuan Akhir

- Arbitrase

- Notifikasi Email

### Versi 1.1
Fokus pada peningkatan komunikasi, monitoring, dan pelaporan.

#### Fitur
- Riwayat Aktivitas Tugas

- Dashboard Monitoring Kinerja

- Laporan Kinerja *Project Manager*

- Laporan Produktivitas Anggota

- Filter dan Pencarian Lanjutan

- Ekspor Laporan PDF

- Ekspor Laporan Excel

### Versi 1.2
Fokus pada peningkatan otomatisasi dan integrasi sistem.

#### Fitur
- Notifikasi WhatsApp

- Kalender Tugas

- Dashboard Analitik Lanjutan

- REST API

- Integrasi Penyimpanan Cloud

- Mode Gelap (*Dark Mode*)

- Aplikasi Mobile

## 9.3 Metrik Keberhasilan Produk
Keberhasilan sistem diukur berdasarkan indikator yang dapat dievaluasi setelah implementasi.

| **No.** | **Indikator**                         | **Target**                                               |
|---------|---------------------------------------|----------------------------------------------------------|
| 1       | Login berhasil                        | Pengguna dapat masuk sesuai hak akses.                   |
| 2       | Pembuatan tugas berhasil              | Data tugas tersimpan pada basis data.                    |
| 3       | Penunjukan *Project Manager* berhasil | Tugas berhasil didistribusikan kepada *Project Manager*. |
| 4       | Penugasan anggota berhasil            | Tugas diterima oleh anggota yang ditunjuk.               |
| 5       | Unggah hasil pekerjaan berhasil       | Berkas tersimpan dan dapat ditinjau.                     |
| 6       | Peninjauan hasil berjalan             | Keputusan persetujuan atau revisi tercatat pada sistem.  |
| 7       | Persetujuan akhir berhasil            | Status tugas berubah menjadi selesai (*Done*).           |
| 8       | Notifikasi email terkirim             | Pengguna menerima pemberitahuan perubahan status tugas.  |
| 9       | Dashboard berjalan                    | Statistik dan informasi tugas ditampilkan dengan benar.  |

## 9.4 Teknologi yang Digunakan
Teknologi yang digunakan dalam pengembangan sistem TaskFlow adalah sebagai berikut.

| **Komponen**                 | **Teknologi**                                 |
|------------------------------|-----------------------------------------------|
| Backend                      | Laravel 12                                    |
| Frontend                     | Blade, HTML, CSS, JavaScript                  |
| Database                     | MariaDB                                       |
| Admin Panel                  | Laravel Filament                              |
| Web Server                   | Nginx                                         |
| Containerization             | Docker                                        |
| Version Control              | Git dan GitHub                                |
| Operating System Development | Ubuntu pada Windows Subsystem for Linux (WSL) |
| IDE                          | Visual Studio Code                            |
| Mail Service                 | SMTP (Laravel Mail)                           |

### Alasan Pemilihan Teknologi

- **Laravel 12** dipilih karena menyediakan kerangka kerja yang modern, aman, dan mendukung pengembangan aplikasi berbasis arsitektur *Model-View-Controller* (MVC).

- **Laravel Filament** digunakan untuk mempercepat pembangunan panel administrasi dengan komponen yang siap digunakan.

- **MariaDB** dipilih sebagai sistem manajemen basis data karena memiliki performa yang baik, stabil, dan kompatibel dengan Laravel.

- **Docker** digunakan untuk menjaga konsistensi lingkungan pengembangan sehingga aplikasi dapat dijalankan pada berbagai perangkat dengan konfigurasi yang seragam.

- **Git dan GitHub** digunakan untuk pengelolaan versi kode sumber serta mendukung kolaborasi selama proses pengembangan.

- **Ubuntu pada WSL** digunakan sebagai lingkungan pengembangan karena memberikan kompatibilitas yang baik terhadap ekosistem Laravel dan Docker.

- **SMTP (Laravel Mail)** digunakan untuk mengirim notifikasi perubahan status tugas secara otomatis kepada pengguna melalui surat elektronik.

# 10. Acceptance Criteria

Acceptance Criteria digunakan sebagai acuan untuk menentukan apakah setiap fitur pada sistem TaskFlow telah berjalan sesuai dengan kebutuhan yang telah ditetapkan dalam *Business Requirement Document* (BRD) dan *Product Requirement Document* (PRD). Setiap kriteria memuat kondisi yang harus dipenuhi agar suatu fitur dinyatakan berhasil diimplementasikan dan siap digunakan oleh pengguna.

## 10.1 Login

| **Kriteria**                                        | **Status Keberhasilan**                                                       |
|-----------------------------------------------------|-------------------------------------------------------------------------------|
| Pengguna memasukkan email dan kata sandi yang benar | Pengguna berhasil masuk ke sistem sesuai dengan perannya.                     |
| Email tidak terdaftar                               | Sistem menampilkan pesan bahwa akun tidak ditemukan.                          |
| Kata sandi salah                                    | Sistem menampilkan pesan kesalahan autentikasi.                               |
| Akun dinonaktifkan                                  | Sistem menolak proses login dan menampilkan informasi bahwa akun tidak aktif. |

## 10.2 Dashboard Super Admin

| **Kriteria**                    | **Status Keberhasilan**            |
|---------------------------------|------------------------------------|
| Super Admin berhasil login      | Dashboard dapat diakses.           |
| Data tugas tersedia             | Ringkasan tugas ditampilkan.       |
| Data *Project Manager* tersedia | Informasi beban kerja ditampilkan. |
| Data aktivitas tersedia         | Statistik dan grafik ditampilkan.  |

## 10.3 Manajemen Tugas

| **Kriteria**                           | **Status Keberhasilan**            |
|----------------------------------------|------------------------------------|
| Super Admin mengisi seluruh data tugas | Tugas berhasil disimpan.           |
| Judul tugas kosong                     | Sistem menampilkan pesan validasi. |
| Tenggat waktu tidak valid              | Sistem menolak penyimpanan data.   |
| Data berhasil disimpan                 | Tugas muncul pada daftar tugas.    |





## 10.4 Penunjukan *Project Manager*

| **Kriteria**                          | **Status Keberhasilan**                                       |
|---------------------------------------|---------------------------------------------------------------|
| Super Admin memilih *Project Manager* | Penanggung jawab tugas berhasil ditetapkan.                   |
| Beban kerja ditampilkan               | Super Admin dapat membandingkan kapasitas kerja.              |
| Penugasan berhasil                    | Status tugas berubah menjadi **Assigned to Project Manager**. |

## 10.5 Penugasan Anggota

| **Kriteria**                      | **Status Keberhasilan**                              |
|-----------------------------------|------------------------------------------------------|
| *Project Manager* memilih anggota | Tugas berhasil diberikan kepada anggota.             |
| Anggota tersedia                  | Sistem menampilkan daftar anggota.                   |
| Penugasan berhasil                | Status tugas berubah menjadi **Assigned to Member**. |

## 10.6 Unggah Hasil Pekerjaan

| **Kriteria**                   | **Status Keberhasilan**                          |
|--------------------------------|--------------------------------------------------|
| Berkas sesuai format           | Berkas berhasil diunggah.                        |
| Ukuran berkas sesuai ketentuan | Berkas berhasil disimpan.                        |
| Format tidak didukung          | Sistem menampilkan pesan kesalahan.              |
| Unggah berhasil                | Status tugas berubah menjadi **Pending Review**. |

## 10.7 Peninjauan Hasil

| **Kriteria**                       | **Status Keberhasilan**                            |
|------------------------------------|----------------------------------------------------|
| *Project Manager* menyetujui hasil | Status berubah menjadi **Pending Final Approval**. |
| *Project Manager* meminta revisi   | Catatan revisi berhasil dikirim kepada anggota.    |
| Catatan revisi dikirim             | Status berubah menjadi **Revision Required**.      |

## 10.8 Revisi Tugas

| **Kriteria**                       | **Status Keberhasilan**                    |
|------------------------------------|--------------------------------------------|
| Anggota melihat catatan revisi     | Catatan revisi ditampilkan.                |
| Anggota mengunggah hasil perbaikan | Berkas berhasil diperbarui.                |
| Unggah berhasil                    | Status kembali menjadi **Pending Review**. |


## 10.9 Persetujuan Akhir

| **Kriteria**                 | **Status Keberhasilan**          |
|------------------------------|----------------------------------|
| Super Admin menyetujui tugas | Status berubah menjadi **Done**. |
| Persetujuan berhasil         | Riwayat aktivitas tersimpan.     |

## 10.10 Arbitrase

| **Kriteria**                     | **Status Keberhasilan**                   |
|----------------------------------|-------------------------------------------|
| Tugas masuk ke proses arbitrase  | Riwayat revisi ditampilkan.               |
| Super Admin memberikan keputusan | Status tugas diperbarui sesuai keputusan. |
| Keputusan tersimpan              | Riwayat arbitrase tercatat.               |

## 10.11 Kelola Pengguna

| **Kriteria**                       | **Status Keberhasilan**               |
|------------------------------------|---------------------------------------|
| Super Admin menambah pengguna      | Data pengguna tersimpan.              |
| Super Admin mengubah data pengguna | Perubahan berhasil disimpan.          |
| Super Admin menonaktifkan akun     | Pengguna tidak dapat masuk ke sistem. |

## 10.12 Monitoring Kinerja

| **Kriteria**                    | **Status Keberhasilan**           |
|---------------------------------|-----------------------------------|
| Data tugas tersedia             | Statistik ditampilkan.            |
| Data *Project Manager* tersedia | Laporan kinerja ditampilkan.      |
| Pengguna memilih periode        | Laporan diperbarui sesuai filter. |

## 10.13 Kriteria Penerimaan Sistem Secara Keseluruhan

Sistem TaskFlow dianggap memenuhi kebutuhan produk apabila:

1.  Pengguna dapat melakukan autentikasi sesuai dengan hak akses yang dimiliki.

2.  Super Admin dapat membuat dan mengelola tugas.

3.  Super Admin dapat menunjuk *Project Manager* berdasarkan informasi beban kerja.

4.  *Project Manager* dapat mendistribusikan tugas kepada anggota.

5.  Anggota dapat melihat tugas dan mengunggah hasil pekerjaan.

6.  *Project Manager* dapat melakukan peninjauan hasil pekerjaan serta memberikan persetujuan atau catatan revisi.

7.  Super Admin dapat melakukan persetujuan akhir maupun arbitrase terhadap tugas apabila diperlukan.

8.  Sistem dapat mengirim notifikasi email setiap terjadi perubahan status tugas.

9.  Dashboard setiap aktor menampilkan informasi sesuai dengan hak aksesnya.

10. Seluruh aktivitas pengguna tercatat pada sistem dan tersimpan di dalam basis data.

11. Seluruh fitur yang termasuk dalam *Minimum Viable Product* (MVP) berjalan sesuai dengan kebutuhan pengguna dan proses bisnis organisasi.
