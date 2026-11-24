<p align="center">
  <img src="public/assets/images/Saku%20Kas%20Wallet%20Finance%20Logo.png" alt="Saku Kas" width="220">
</p>

<h1 align="center">Saku Kas — Cashflow Tracker</h1>

<p align="center">
  Aplikasi pencatatan keuangan dan arus kas berbasis web yang bisa dipasang seperti aplikasi mobile (PWA),
  dengan saldo bersama untuk kolaborasi keluarga/tim dan saldo pribadi untuk setiap anggota.
</p>

<p align="center">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white">
  <img alt="Bootstrap" src="https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white">
  <img alt="PWA" src="https://img.shields.io/badge/PWA-ready-5A0FC8">
</p>

---

## Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Cara Kerja Inti](#cara-kerja-inti)
- [Model Data](#model-data)
- [Struktur Proyek](#struktur-proyek)
- [Menjalankan di Lokal](#menjalankan-di-lokal)
- [Konfigurasi Penting](#konfigurasi-penting)
- [Pengujian](#pengujian)
- [Checklist Deployment](#checklist-deployment)
- [Catatan & Batasan](#catatan--batasan)

---

## Tentang Proyek

**Saku Kas** (nama aplikasi default: `Catat Keuangan`) adalah aplikasi *cashflow tracker* untuk mencatat pemasukan
dan pengeluaran harian. Fokusnya ada di tiga hal:

1. **Cepat dipakai** — antarmuka mobile-first, navigasi bawah, dan tombol tambah transaksi yang selalu terjangkau.
2. **Bisa dikolaborasikan** — satu akun bisa dipakai beberapa anggota (maks. **5 orang**), dengan pemisahan jelas
   antara **saldo bersama** dan **saldo pribadi**.
3. **Terasa seperti aplikasi native** — dapat di-install ke *home screen* (PWA mode `standalone`) dan mengirim
   notifikasi ke perangkat setiap kali arus kas berubah.

Cocok untuk pencatatan keuangan rumah tangga, kas kecil usaha, atau tim kecil yang butuh transparansi arus kas.

---

## Fitur Utama

### Autentikasi & Sesi
- Login dan registrasi dengan email + password.
- **Sesi persisten**: `SESSION_LIFETIME=525600` (1 tahun) dan opsi *remember me* selalu aktif, sehingga pengguna
  tidak perlu login ulang setiap membuka aplikasi.

### Dashboard
- Ringkasan **total saldo bersama** dan **total saldo pribadi**.
- Pemasukan vs pengeluaran bulan berjalan, lengkap dengan perbandingan terhadap bulan sebelumnya.
- Grafik arus kas 7 hari terakhir.
- Daftar 5 transaksi terakhir.
- Ringkasan diperbarui otomatis lewat endpoint `/live/summary` (polling).

### Transaksi
- CRUD pemasukan dan pengeluaran (nominal, dompet, kategori, tanggal, deskripsi).
- Filter berdasarkan jenis, dompet, kategori, rentang tanggal, dan kata kunci deskripsi.
- Ringkasan total pemasukan/pengeluaran mengikuti filter yang aktif.
- Pagination 15 transaksi per halaman.

### Dompet & Kategori
- **Dompet bersama** (*shared*) untuk kas utama akun.
- **Dompet pribadi** otomatis dibuat untuk setiap anggota ("Saldo Pribadi").
- Kategori terpisah untuk pemasukan dan pengeluaran, dengan ikon Tabler.
- Transaksi tidak bisa dipindahkan antara dompet bersama dan dompet pribadi (menjaga integritas saldo).

### Kolaborasi Akun
- Satu pengguna dapat menjadi anggota beberapa akun dan berpindah akun aktif.
- Peran **owner** dan **member**; hanya owner yang dapat menambah/menghapus anggota.
- Undang anggota lain melalui email pengguna yang sudah terdaftar.
- Batas maksimal 5 anggota per akun.
- **Log aktivitas** akun (pembuatan akun, transaksi, perubahan anggota, dll.), dengan pemisahan aktivitas
  bersama vs pribadi.

### Kalender
- Tampilan bulanan berisi transaksi per tanggal, dengan total pemasukan/pengeluaran harian.

### Notifikasi Web Push
- Notifikasi dikirim saat transaksi dibuat, diubah, atau dihapus.
- Transaksi **bersama** dikirim ke seluruh anggota akun; transaksi **pribadi** hanya ke pemilik dompet.
- Pengiriman dilakukan langsung saat transaksi disimpan agar notifikasi terasa instan. Jika layanan push gagal,
  kegagalan dicatat ke log dan **tidak** menggagalkan pencatatan transaksi.

### PWA
- `manifest.json` dengan `display: standalone` dan `start_url: /dashboard?source=pwa`.
- Service worker (`public/sw.js`) untuk caching aset dan menerima push notification.
- Banner/modal "Pasang Aplikasi" setelah login, plus panduan khusus iOS (Safari).
- Mendukung mode gelap/terang.

---

## Tech Stack

| Lapisan | Teknologi |
| --- | --- |
| Backend | PHP 8.3, Laravel 13 |
| Database | MySQL (default `.env.example`), SQLite in-memory untuk test |
| Frontend | Blade, Bootstrap 5 (tema Dasher), CSS kustom `public/assets/css/cashflow.css` |
| Build tool | Vite 8 + Tailwind 4 (dipakai halaman default; UI aplikasi memakai aset statis di `public/assets`) |
| PWA | Web App Manifest, Service Worker, Web Push (VAPID) |
| Push | `laravel-notification-channels/webpush` |
| Testing | PHPUnit 12 |
| Formatter | Laravel Pint |

---

## Cara Kerja Inti

### 1. Multi-akun & konteks akun aktif

Setiap request terautentikasi melewati middleware `ResolveCurrentAccount`:

- Menentukan akun aktif pengguna (`users.current_account_id`) atau membuat akun pribadi bila belum ada.
- Membagikan `$currentAccount` dan `$userAccounts` ke seluruh view.
- Semua data transaksi, dompet, kategori, dan log aktivitas selalu dibatasi oleh `account_id`.

### 2. Saldo bersama vs saldo pribadi

- `wallets.is_shared = true` → dompet bersama, terlihat oleh semua anggota akun.
- `wallets.is_shared = false` → dompet pribadi, hanya terlihat oleh pemiliknya.
- Dashboard menampilkan dua angka saldo terpisah agar dana pribadi tidak tercampur dengan kas bersama.

### 3. Pencatatan saldo yang atomik

`App\Services\CashflowService` menangani pembuatan, perubahan, dan penghapusan transaksi di dalam
`DB::transaction()` dengan `lockForUpdate()`. Setiap perubahan transaksi langsung menyesuaikan saldo dompet,
sehingga tidak ada selisih saat dua transaksi disimpan bersamaan.

### 4. Notifikasi event-driven

`TransactionObserver` memicu `CashflowUpdated` setiap kali transaksi berubah. Listener
`SendCashflowPushNotification` memilih penerima (semua anggota untuk dompet bersama, hanya pemilik untuk dompet
pribadi), lalu mengirim `CashflowChangedNotification` ke subscription perangkat yang tersimpan di tabel
`push_subscriptions`.

### 5. PWA

`public/sw.js` melakukan caching aset statis dan menangani event `push` serta `notificationclick`.
`public/assets/js/pwa-installer.js` mencegat `beforeinstallprompt`, menampilkan ajakan instalasi, dan mendeteksi
mode `standalone` agar banner tidak muncul lagi setelah aplikasi terpasang.

---

## Model Data

| Tabel | Keterangan |
| --- | --- |
| `users` | Pengguna, termasuk `current_account_id` untuk akun aktif |
| `accounts` | Akun/ruang kerja keuangan, memiliki `owner_id` |
| `account_user` | Pivot keanggotaan dengan `role` (`owner`/`member`) dan `joined_at` |
| `wallets` | Dompet dengan `balance`, `is_shared`, dan `account_id` |
| `categories` | Kategori pemasukan/pengeluaran per akun (`type`, `icon`) |
| `transactions` | Transaksi (`type`, `amount`, `transaction_date`, `wallet_id`, `category_id`, `user_id`) |
| `activity_logs` | Jejak aktivitas akun (`action`, `description`, `subject`, `properties`, `is_shared`) |
| `push_subscriptions` | Endpoint Web Push perangkat (dikelola package webpush) |
| `sessions`, `cache`, `jobs` | Tabel framework (sesi database, cache, antrean) |

Relasi ringkas:

```text
User ──< account_user >── Account ──< Wallet ──< Transaction >── Category
 │                           │                                  │
 │                           ├──< Category                      │
 │                           └──< ActivityLog                   │
 └──< push_subscriptions                                       User
```

---

## Struktur Proyek

```text
app/
├── Events/CashflowUpdated.php              # Event perubahan arus kas
├── Http/
│   ├── Controllers/                        # Auth, Dashboard, Transaksi, Dompet, Kategori, Akun, Kalender, Push
│   ├── Middleware/ResolveCurrentAccount.php # Konteks akun aktif
│   └── Requests/StoreTransactionRequest.php # Validasi transaksi
├── Listeners/
│   ├── SendCashflowPushNotification.php    # Kirim push ke penerima yang tepat
│   └── LogFailedWebPush.php                # Catat kegagalan pengiriman push
├── Models/                                 # User, Account, Wallet, Category, Transaction, ActivityLog
├── Notifications/CashflowChangedNotification.php
├── Observers/TransactionObserver.php       # created/updated/deleted → event
├── Services/
│   ├── CashflowService.php                 # Kalkulasi saldo atomik
│   └── AccountService.php                  # Pembuatan akun, anggota, aktivasi akun
├── Providers/AppServiceProvider.php        # Registrasi observer & paginator
└── Http/Controllers/Auth/                  # Login & registrasi

database/
├── migrations/                             # Skema termasuk accounts, is_shared, push_subscriptions
├── factories/
└── seeders/
    ├── DatabaseSeeder.php                  # Akun demo + data awal
    └── DefaultCategorySeeder.php           # Dompet "Kas Utama" & 7 kategori default

public/
├── assets/css/theme.css                    # Tema Bootstrap (Dasher)
├── assets/css/cashflow.css                 # Gaya kustom aplikasi
├── assets/js/pwa-installer.js              # Install prompt & permission notifikasi
├── manifest.json                           # Konfigurasi PWA
├── sw.js                                   # Service worker (cache + push)
└── icons/                                  # Ikon 192/512 & maskable

resources/views/
├── layouts/app.blade.php                   # Shell aplikasi (sidebar, topbar, FAB)
├── partials/                               # Sidebar, topbar, head, script, mobile nav
├── dashboard/ transactions/ wallets/ categories/ calendar/ account/ auth/
└── components/

routes/web.php                              # Seluruh rute aplikasi
tests/Feature/                              # Test akun, cashflow, PWA, push, filter, kalender
```

---

## Menjalankan di Lokal

### Prasyarat

- PHP 8.3+ dengan ekstensi umum Laravel (dan `gd` bila ingin memproses gambar)
- Composer 2
- Node.js 20+ dan npm
- MySQL 8+ (atau sesuaikan `DB_CONNECTION` ke `sqlite` untuk percobaan cepat)

### Langkah instalasi

```bash
git clone <url-repository> saku-kas
cd saku-kas

composer install
cp .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database di `.env`, lalu:

```bash
php artisan migrate --seed
php artisan webpush:vapid
```

Perintah `webpush:vapid` menghasilkan pasangan kunci VAPID. Salin nilainya ke `.env`:

```dotenv
VAPID_SUBJECT=mailto:admin@example.com
VAPID_PUBLIC_KEY=...
VAPID_PRIVATE_KEY=...
```

Jalankan aplikasi:

```bash
composer run dev
```

Perintah tersebut menjalankan proses development Laravel sesuai konfigurasi `php artisan dev` (server, antrean, dan Vite). Alternatif manual:

```bash
php artisan serve
npm install
npm run dev
```

Buka `http://localhost:8000`.

### Akun demo

`php artisan migrate --seed` membuat akun berikut:

| Email | Password |
| --- | --- |
| `test@example.com` | `password` |

Seeder juga membuat dompet **Kas Utama** dan kategori default (Gaji, Penjualan, Bonus, Operasional, Konsumsi,
Transportasi, Tagihan).

> **Catatan aset frontend:** UI utama memuat CSS/JS langsung dari `public/assets` (bukan bundel Vite), sehingga
> perubahan pada `public/assets/css` atau `public/assets/js` langsung terlihat. `npm run build` hanya diperlukan
> untuk aset Vite (mis. halaman `welcome`).

---

## Konfigurasi Penting

| Variabel | Fungsi |
| --- | --- |
| `APP_NAME` | Nama aplikasi yang tampil di UI |
| `SESSION_LIFETIME` | Lama sesi (default 525600 menit ≈ 1 tahun) |
| `SESSION_ENCRYPT` | Enkripsi cookie sesi (default `true`) |
| `QUEUE_CONNECTION` | Default `database`; notifikasi push saat ini dikirim sinkron |
| `VAPID_SUBJECT`, `VAPID_PUBLIC_KEY`, `VAPID_PRIVATE_KEY` | Kunci Web Push |
| `DB_CONNECTION` | `mysql` (default) atau `sqlite` |

Service worker dan Web Push **hanya berjalan pada HTTPS** (atau `localhost` saat development).

Jika memakai ngrok, `AppServiceProvider` otomatis memaksa skema HTTPS untuk host yang mengandung `ngrok`.

---

## Pengujian

```bash
php artisan test --compact
```

Test berjalan dengan SQLite in-memory, queue `sync`, dan session `array` (lihat `phpunit.xml`). Cakupan test
meliputi autentikasi, akun/anggota, transaksi & filter, dompet, kategori, kalender, endpoint push subscription,
serta perilaku PWA.

Untuk menjalankan satu file atau satu test:

```bash
php artisan test --compact tests/Feature/CashflowTest.php
php artisan test --compact --filter=testName
```

Format kode PHP:

```bash
vendor/bin/pint --dirty
```

---

## Checklist Deployment

1. Set `.env` produksi: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://...`, kredensial DB, dan kunci VAPID.
2. Aktifkan **HTTPS** (wajib untuk service worker, install prompt, dan Web Push).
3. Jalankan migrasi: `php artisan migrate --force`.
4. Optimalkan cache: `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`.
5. Jalankan queue worker hanya jika pengiriman notifikasi dipindahkan ke antrean (mis.
   `php artisan queue:work --tries=3` via Supervisor). Saat ini notifikasi dikirim langsung, jadi worker tidak wajib.
6. Naikkan versi cache service worker (`CACHE_NAME` di `public/sw.js`) setiap deploy perubahan aset agar klien
   mengambil versi terbaru.

---

## Catatan & Batasan

- Anggota akun ditambahkan berdasarkan **email pengguna yang sudah terdaftar**; belum ada undangan via email.
- Batas anggota akun adalah **5 orang** (`Account::MAX_MEMBERS`).
- Peran saat ini hanya `owner` dan `member`; belum ada peran granular per fitur.
- Notifikasi push bergantung pada dukungan browser. Di iOS, Web Push memerlukan iOS 16.4+ dan aplikasi sudah
  dipasang ke *home screen*.
- Gambar logo di `public/assets/images` masih berukuran besar; kompresi/optimalisasi dapat dilakukan bila
  performa muat halaman menjadi perhatian.

---

Proyek internal — tidak dipublikasikan sebagai package. Silakan sesuaikan bagian lisensi bila akan dirilis
sebagai open source.
