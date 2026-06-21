# Fitur Lanjutan — Saran Pengembangan

## Prioritas Tinggi

### 1. Notifikasi WhatsApp (Fonnte)
Trigger notifikasi otomatis ke WhatsApp saat:
- Atasan buat tugas → notif ke Super Admin
- Super Admin assign PM → notif ke PM
- PM assign anggota → notif ke anggota

**File trigger:**
- `app/Livewire/Atasan/CreateTask.php` — setelah `save()`
- `app/Livewire/Admin/TaskOversight.php` — setelah `assignToPm()`
- `app/Livewire/Pm/PmDashboard.php` — setelah `assignToMember()`

**Config:** `config/fonnte.php`, `.env` (`FONNTE_TOKEN`).

### 2. Notifikasi Email
Form compose email sudah ada (Admin → PM, PM → Member). Tinggal integrasi SMTP di `.env`.

### 3. Dashboard Atasan — Grafik & Chart
Tambahkan chart interaktif (via Chart.js atau Alpine):
- Bar chart: tugas per bulan
- Pie chart: breakdown status
- Line chart: deadline trend

### 4. Filter & Pencarian Global
- Search by title, assignee, workspace, status, priority, deadline range
- Filter multi-kriteria simultan

---

## Prioritas Menengah

### 5. Export Data
Export tabel tugas ke:
- CSV
- Excel (Laravel Excel / maatwebsite)
- PDF (DomPDF / Barryvdh)

Bonus: export per role (Atasan export tugasnya sendiri, Admin export semua).

### 6. Activity Log / Riwayat
Catat setiap perubahan status tugas (Spatie Activitylog sudah terinstall):
- Siapa, kapan, dari status apa ke status apa
- Tampilkan di modal detail tugas sebagai timeline

### 7. Notifikasi In-App (Bell Icon)
Bell icon di header sidebar:
- Notifikasi saat ada tugas baru / perubahan status / revisi
- Badge jumlah notifikasi belum dibaca
- Klik → mark as read
- Bisa pake Livewire + Alpine + local storage

### 8. Reminder Deadline Otomatis
Daily cron job kirim notifikasi (WhatsApp/Email):
- H-1 deadline: "Tugas {title} deadline besok!"
- Lewat deadline: "Tugas {title} terlambat!"
- Cron: `php artisan schedule:run`

---

## Prioritas Rendah

### 9. Dark Mode
Toggle dark mode per user. Simpan preferensi di DB atau local storage.
Tailwind udah support `dark:` prefix.

### 10. Multi Bahasa (i18n)
Bikin file translasi `id.json` / `en.json`. Pasang locale switcher di sidebar.
Saat ini label masih campuran Indonesia-Inggris.

### 11. Invite Member via Email
Form invite: input email + pilih team → kirim email undangan + link register.
Di `Team` model sudah ada kolom `invite_code`.

### 12. Mobile Responsive
Sidebar jadi hamburger menu di layar kecil. Tabel jadi card view di mobile.
Tailwind responsive utilities udah siap tinggal dipakai.

### 13. Manajemen Workspace
CRUD workspace dari dashboard admin:
- Buat/Edit/Hapus workspace
- Assign PM ke workspace
- Atur anggota workspace

### 14. Manajemen Tim
CRUD team dari dashboard PM:
- Buat tim, invite anggota
- Atur role anggota (admin/member)
- Hapus anggota

---

## Catatan Implementasi

**Notifikasi multi-channel:**
```php
// Contoh struktur Notification class
class TaskAssignedNotification extends Notification
{
    public function via($notifiable)
    {
        return ['database', 'whatsapp', 'mail'];
    }
}
```

**Cron job reminder:**
```php
// app/Console/Kernel.php
$schedule->call(function () {
    // Kirim reminder untuk tugas deadline besok
})->dailyAt('08:00');
```

**Activity log:**
```php
// Udah terinstall, tinggal pake
activity()->performedOn($task)
    ->causedBy($user)
    ->withProperties(['old' => $oldStatus, 'new' => $newStatus])
    ->log('status_updated');
```
