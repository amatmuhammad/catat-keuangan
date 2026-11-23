# Product Requirements Document (PRD) & Arsitektur Sistem

**Nama Proyek:** Sistem Pencatatan Keuangan & Arus Kas (Cashflow Tracker)

**Tech Stack:** Laravel 13, MySQL/PostgreSQL, PWA (Progressive Web App), Web Push Notification API

## 1. Product Requirements Document (PRD)

### 1.1. Informasi Umum Produk

* **Nama Produk:** Sistem Pencatatan Keuangan & Arus Kas (Cashflow Tracker)

* **Platform Target:** Web Application berbasis Progressive Web App (PWA) yang dapat di-install ke layar utama (*Home Screen*) smartphone dengan tampilan layar penuh (*standalone/WebView experience*) maupun diakses melalui browser desktop.

* **Tujuan Utama:** Menyediakan platform pencatatan transaksi pemasukan dan pengeluaran yang cepat, mudah dipasang langsung dari browser pasca-login layaknya aplikasi APK, tanpa hambatan autentikasi berulang, serta memberikan pemantauan arus kas secara *real-time* melalui notifikasi perangkat.

### 1.2. Kebutuhan Fungsional Utama

| Kode | Fitur | Deskripsi Fungsional | Kriteria Penerimaan (*Acceptance Criteria*) | 
| ----- | ----- | ----- | ----- | 
| **FR-01** | **Autentikasi Sekali Jalan (*Persistent Login*)** | Pengguna hanya perlu memasukkan kredensial (Email/Username & Password) satu kali pada perangkat baru. | Sesi tetap aktif meski aplikasi/browser ditutup total atau perangkat di-restart, kecuali pengguna menekan tombol *Logout* secara manual. | 
| **FR-02** | **In-App Install Prompt (Pasca-Login)** | Setelah pengguna berhasil melakukan login pertama kali dan masuk ke halaman utama (Dashboard), sistem mendeteksi apakah aplikasi sudah terinstal di perangkat. Jika belum, sistem memunculkan *banner* atau *modal* ajakan instalasi langsung di halaman. | *Banner/modal* dengan tombol **"Pasang Aplikasi"** muncul otomatis setelah login jika dibuka via browser. *Banner* tidak muncul jika aplikasi sudah dibuka dari ikon Home Screen (mode *standalone*). | 
| **FR-03** | **Mode Tampilan Full-Screen (WebView Experience)** | Aplikasi yang telah dipasang ke Home Screen berjalan dalam jendela terpisah layaknya aplikasi APK tanpa antarmuka browser (tanpa URL bar, tombol *back/forward* browser, maupun tab). | Ketika ikon aplikasi di layar utama HP diketuk, aplikasi terbuka penuh layar (*standalone*) dengan *splash screen* dan langsung memuat halaman Dashboard tanpa meminta login ulang. | 
| **FR-04** | **Pencatatan Arus Kas** | Pengguna dapat mencatat transaksi **Pemasukan** dan **Pengeluaran** beserta nominal, kategori, tanggal, dan catatan tambahan. | Saldo utama langsung bertambah atau berkurang secara akurat sesuai jenis transaksi yang disimpan. | 
| **FR-05** | **Manajemen Kategori & Akun/Dompet** | Pengelompokan sumber dana (misal: Kas Tunai, Rekening Bank, E-Wallet) dan kategori transaksi (misal: Operasional, Penjualan, Makan). | Pengguna dapat memfilter riwayat transaksi dan melihat sisa saldo berdasarkan masing-masing dompet. | 
| **FR-06** | **Notifikasi Perubahan Arus Kas** | Sistem otomatis mengirimkan notifikasi ke perangkat pengguna setiap kali ada transaksi masuk atau keluar yang tercatat (baik oleh pengguna sendiri maupun akun lain dalam satu ruang kerja). | Notifikasi muncul di bar notifikasi perangkat (walaupun aplikasi sedang ditutup) dalam hitungan detik setelah data masuk ke database. | 
| **FR-07** | **Dashboard & Laporan Ringkas** | Menampilkan total saldo saat ini, perbandingan pemasukan vs pengeluaran bulan berjalan, dan daftar transaksi terakhir. | Data grafik dan ringkasan angka terbarui secara otomatis setelah transaksi ditambahkan, diubah, atau dihapus. | 

### 1.3. Kebutuhan Non-Fungsional

* **Keamanan Sesi:** Walaupun sesi dibuat persisten, token autentikasi harus dienkripsi dan memiliki mekanisme pencabutan akses (*revoke token*) jika perangkat hilang.

* **Kecepatan Akses:** Waktu muat halaman utama di bawah 1,5 detik menggunakan *caching* aset statis pada Service Worker sehingga transisi layar terasa mulus seperti aplikasi *native*.

* **Responsivitas & UI Mobile-First:** Antarmuka dioptimalkan khusus untuk layar *mobile* tanpa *horizontal scroll*, dilengkapi bilah navigasi bawah (*bottom navigation bar*) dan tombol aksi utama (*Floating Action Button*) yang mudah dijangkau ibu jari.

## 2. Arsitektur Sistem (Laravel 13)

Untuk mewujudkan perilaku seperti aplikasi *native mobile* / APK di atas ekosistem web Laravel 13, arsitektur dibagi menjadi empat lapisan utama berikut:

### 2.1. Arsitektur Sesi Persisten (Login Satu Kali)

Standar sesi web biasa akan kedaluwarsa dalam beberapa jam. Agar pengguna tidak perlu login berulang kali seperti aplikasi mobile, arsitektur menerapkan kombinasi berikut:

1. **Extended Lifetime & Remember Token:** Konfigurasi `SESSION_LIFETIME` pada file `.env` Laravel diatur ke durasi sangat panjang (misal: `525600` menit / 1 tahun) yang dipadukan dengan cookie `remember_web` terenkripsi dan opsi `remember` diaktifkan secara *default* saat proses autentikasi.

2. **PWA Standalone Storage:** Saat pengguna menginstal web sebagai PWA di Android/iOS, sistem operasi mengisolasi penyimpanan cookie dan *Local Storage* khusus untuk aplikasi tersebut sehingga tidak ikut terhapus saat pengguna membersihkan riwayat browser biasa.

3. **Token Device Binding (Opsional untuk SPA):** Menggunakan **Laravel Sanctum** jika antarmuka depan menggunakan pendekatan SPA (Vue/React/Livewire) untuk menyimpan token perangkat secara permanen hingga pengguna melakukan *logout*.

### 2.2. Arsitektur Instalasi Pasca-Login & Tampilan Standalone (WebView-Like)

Untuk mewujudkan alur dari **Login** $\rightarrow$ **Pop-up Install** $\rightarrow$ **Ikon Aplikasi Layar Penuh**, sistem menggunakan kombinasi komponen PWA berikut pada Blade Template Laravel 13:

1. **Web App Manifest (`public/manifest.json`):**

   * **`display: "standalone"`**: Menghilangkan seluruh elemen UI browser (bilah alamat URL dan menu browser), sehingga tampilan murni 100% halaman aplikasi layaknya WebView/APK.

   * **`start_url: "/dashboard?source=pwa"`**: Memastikan saat ikon di HP diketuk, sistem langsung mengarahkan ke Dashboard (memanfaatkan sesi login persisten) serta menandai bahwa akses berasal dari aplikasi terpasang.

   * **`icons` & `theme_color`**: Menyediakan ikon aplikasi beresolusi `192x192` px dan `512x512` px beserta warna tema bilah status (*status bar*) HP.

2. **Event Interception (`beforeinstallprompt`) Pasca-Login:**

   * **Tangkap Event:** Saat halaman dashboard dimuat setelah login berhasil, skrip JavaScript mencegat event `beforeinstallprompt` dari browser dan menyimpannya ke dalam variabel *state* (`deferredPrompt`).

   * **Tampilkan UI Kustom:** Sistem langsung memunculkan komponen *floating banner* atau *modal* di halaman dengan tombol **"Pasang Aplikasi Sekarang"**.

   * **Eksekusi Instalasi:** Ketika pengguna menekan tombol tersebut, skrip memanggil `deferredPrompt.prompt()` untuk memicu dialog instalasi resmi milik sistem operasi HP. Setelah terpasang, *banner* otomatis disembunyikan.

   * **Deteksi Mode Terpasang:** Menggunakan pengecekan `window.matchMedia('(display-mode: standalone)').matches` untuk memastikan *banner* instalasi tidak pernah muncul lagi saat pengguna membuka aplikasi dari ikon Home Screen.

   * **Panduan Khusus iOS (Safari):** Untuk perangkat iPhone/iPad yang belum mendukung `beforeinstallprompt` otomatis, skrip mendeteksi *user-agent* iOS dan menampilkan instruksi visual singkat (*"Ketuk ikon Bagikan/Share, lalu pilih Tambah ke Layar Utama"*).

3. **Service Worker (`public/sw.js`):**

   * Melakukan *caching* aset statis (*CSS, JS, font, ikon*) agar aplikasi terbuka instan tanpa layar memuat putih saat ikon diketuk.

   * Bertugas sebagai *background listener* untuk menangkap pesan *Push Notification* meskipun aplikasi sedang tidak aktif di layar.

### 2.3. Arsitektur Notifikasi Arus Kas (Event-Driven + Web Push)

Agar notifikasi tetap masuk ke HP meskipun aplikasi sedang ditutup total, sistem menggunakan protokol **Web Push (VAPID)** yang terintegrasi dengan **Laravel Events & Queues**:

1. **Pendaftaran Perangkat:** Saat pertama kali berhasil login (atau setelah menekan tombol izinkan notifikasi), browser/PWA meminta izin notifikasi kepada pengguna. Setelah diizinkan, perangkat menghasilkan *Push Subscription Endpoint* dan menyimpannya ke tabel `push_subscriptions` di database melalui package `laravel-notification-channels/webpush`.

2. **Pemicu Perubahan (Eloquent Observer):** Setiap kali model `Transaction` mengalami perubahan (`created`, `updated`, atau `deleted`), **TransactionObserver** otomatis memicu event perubahan arus kas.

3. **Pemrosesan Latar Belakang (Queue Worker):** Listener menangkap event tersebut dan mengirimkannya ke antrean (*Queue* menggunakan Redis/Database) agar proses penyimpanan transaksi di layar pengguna tetap instan tanpa jeda.

4. **Pengiriman ke Perangkat:** Queue Worker mengeksekusi kelas notifikasi Laravel (`CashflowChangedNotification`) yang meneruskan pesan ke server Push (Google FCM / Apple Push Service), yang kemudian memunculkan *banner* notifikasi langsung di layar HP pengguna.

### 2.4. Rancangan Struktur Database (ERD)

```
+------------------+       +---------------------+       +----------------------+
|      users       |       |       wallets       |       |     transactions     |
+------------------+       +---------------------+       +----------------------+
| id (PK)          |---<   | id (PK)             |---<   | id (PK)              |
| name             |       | user_id (FK)        |       | wallet_id (FK)       |
| email            |       | name                |       | category_id (FK)     |
| password         |       | balance (15,2)      |       | user_id (FK)         |
| remember_token   |       | timestamps          |       | type (income/expense)|
| timestamps       |       +---------------------+       | amount (15,2)        |
+------------------+                                     | description          |
         |                                               | transaction_date     |
         |                 +---------------------+       | timestamps           |
         +----------------<|  push_subscriptions |       +----------------------+
         |                 +---------------------+                  ^
         |                 | id (PK)             |                  |
         |                 | subscribable_id     |       +----------------------+
         |                 | endpoint            |       |      categories      |
         |                 | public_key          |       +----------------------+
         |                 | auth_token          |---<   | id (PK)              |
         |                 | timestamps          |       | user_id (FK)         |
         |                 +---------------------+       | name                 |
         +----------------------------------------------<| type (income/expense)|
                                                         | icon                 |
                                                         | timestamps           |
                                                         +----------------------+

```

### 2.5. Struktur Folder Proyek (Laravel 13)

Struktur direktori disesuaikan dengan arsitektur ramping **Laravel 13** yang dilengkapi lapisan *Service*, *Event-Driven Notification*, serta aset PWA:

```
cashflow-tracker/
├── app/
│   ├── Events/
│   │   └── CashflowUpdated.php                 # Event pemicu saat arus kas masuk/keluar berubah
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── LoginController.php         # Autentikasi sekali jalan (remember_me aktif permanen)
│   │   │   ├── DashboardController.php         # Ringkasan saldo, grafik & status instalasi PWA
│   │   │   ├── PushSubscriptionController.php  # Simpan/hapus endpoint Web Push perangkat
│   │   │   ├── TransactionController.php       # CRUD transaksi pemasukan & pengeluaran
│   │   │   └── WalletController.php            # Manajemen akun kas/dompet & kategori
│   │   └── Requests/
│   │       └── StoreTransactionRequest.php     # Validasi input transaksi & saldo
│   ├── Listeners/
│   │   └── SendCashflowPushNotification.php    # Listener antrean untuk mengirim notifikasi ke HP
│   ├── Models/
│   │   ├── Category.php                        # Relasi kategori pemasukan/pengeluaran
│   │   ├── Transaction.php                     # Model transaksi (terhubung ke Observer)
│   │   ├── User.php                            # Menggunakan trait HasPushSubscriptions
│   │   └── Wallet.php                          # Model saldo dompet/rekening
│   ├── Notifications/
│   │   └── CashflowChangedNotification.php     # Payload & format pesan Web Push Notification
│   ├── Observers/
│   │   └── TransactionObserver.php             # Deteksi otomatis created/updated/deleted transaksi
│   ├── Providers/
│   │   └── AppServiceProvider.php              # Registrasi TransactionObserver
│   └── Services/
│       └── CashflowService.php                 # Logika bisnis & DB::transaction kalkulasi saldo
├── bootstrap/
│   ├── app.php                                 # Konfigurasi routing, middleware & exception Laravel 13
│   └── providers.php
├── config/
│   ├── session.php                             # Konfigurasi lifetime sesi persisten (525600 menit)
│   └── webpush.php                             # Konfigurasi VAPID Public & Private Key
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2026_11_23_000001_create_wallets_table.php
│   │   ├── 2026_11_23_000002_create_categories_table.php
│   │   ├── 2026_11_23_000003_create_transactions_table.php
│   │   └── 2026_11_23_000004_create_push_subscriptions_table.php
│   └── seeders/
│       └── DefaultCategorySeeder.php           # Data awal kategori & dompet default
├── public/
│   ├── icons/                                  # Ikon aplikasi untuk Home Screen (192x192 & 512x512)
│   │   ├── icon-192x192.png
│   │   └── icon-512x512.png
│   ├── index.php
│   ├── manifest.json                           # Konfigurasi PWA (display: standalone)
│   └── sw.js                                   # Service Worker (Cache aset & listener Push Event)
├── resources/
│   ├── js/
│   │   ├── app.js
│   │   └── pwa-installer.js                    # Handler beforeinstallprompt & izin notifikasi
│   └── views/
│       ├── auth/
│       │   └── login.blade.php                 # Halaman login 1x dengan auto-remember
│       ├── components/
│       │   └── install-prompt-banner.blade.php # Modal/banner "Pasang Aplikasi" pasca-login
│       ├── layouts/
│       │   └── app.blade.php                   # Layout mobile-first (meta PWA & registrasi sw.js)
│       ├── dashboard.blade.php                 # Halaman utama (mode standalone WebView-like)
│       └── transactions/
│           └── index.blade.php                 # Riwayat & filter arus kas
├── routes/
│   ├── console.php
│   └── web.php                                 # Rute dashboard, transaksi & endpoint push subscription
├── .env                                        # Konfigurasi DB, QUEUE_CONNECTION=database, VAPID keys
└── composer.json                               # Dependensi utama & laravel-notification-channels/webpush

```

**Catatan Struktur Penting:**

* **`app/Services/CashflowService.php`**: Memisahkan logika penambahan atau pengurangan saldo dari controller menggunakan `DB::transaction()` agar perhitungan saldo tetap akurat dan tidak terjadi selisih saat pencatatan cepat.

* **`public/manifest.json` & `public/sw.js`**: Wajib berada langsung di root direktori `public/` agar cakupan (*scope*) Service Worker dapat mengontrol seluruh rute aplikasi mulai dari `/`.

* **`resources/views/components/install-prompt-banner.blade.php`**: Komponen khusus yang hanya tampil ketika pengguna mengakses aplikasi lewat browser biasa setelah login, dan otomatis tersembunyi ketika dibuka dari ikon layar utama (*standalone mode*).

### 2.6. Alur Kerja Sistem (End-to-End Data Flow)

1. **Akses Awal & Login:** Pengguna membuka URL melalui browser HP $\rightarrow$ Melakukan **Login 1 kali** $\rightarrow$ Sesi dikunci secara persisten di perangkat.

2. **Instalasi Layaknya APK:** Halaman **Dashboard** terbuka $\rightarrow$ Muncul *banner* instalasi di layar $\rightarrow$ Pengguna menekan tombol **Pasang Aplikasi** $\rightarrow$ Ikon aplikasi muncul di Home Screen HP.

3. **Penggunaan Harian (WebView Mode):** Pengguna mengetuk ikon aplikasi di Home Screen $\rightarrow$ Aplikasi terbuka layar penuh tanpa bilah browser dan langsung masuk ke Dashboard tanpa login ulang.

4. **Input Transaksi & Notifikasi:** Pengguna mencatat pemasukan Rp 500.000 $\rightarrow$ Data tersimpan di `transactions` dan saldo `wallets` diperbarui via `DB::transaction` $\rightarrow$ `TransactionObserver` mengirim job ke Queue $\rightarrow$ Notifikasi muncul di bilah notifikasi HP: *"Arus Kas Masuk: +Rp 500.000 pada Kas Utama"*.

## 3. Urutan Pengerjaan (Development Roadmap)

Berikut adalah tahapan terstruktur pengerjaan aplikasi dari inisialisasi proyek hingga siap digunakan di perangkat pengguna:

### Fase 1: Inisialisasi Proyek & Konfigurasi Sesi Persisten
1. **Instalasi Laravel 13:** Membuat proyek baru (`composer create-project laravel/laravel cashflow-tracker`) dan mengonfigurasi koneksi database (`MySQL/PostgreSQL`) serta `QUEUE_CONNECTION=database` di file `.env`.
2. **Konfigurasi Sesi Login Satu Kali:** Mengubah `SESSION_LIFETIME=525600` (1 tahun) di `.env` dan memastikan `expire_on_close => false` pada `config/session.php`.
3. **Setup Autentikasi:** Membuat halaman dan controller login (`LoginController`) dengan memaksa parameter `remember = true` pada `Auth::attempt($credentials, true)` agar cookie sesi persisten selalu tertanam di perangkat pengguna.

### Fase 2: Pembuatan Database, Model & Logika Inti Arus Kas
1. **Database Migration:** Membuat dan menjalankan migrasi untuk tabel `wallets`, `categories`, dan `transactions` (`php artisan migrate`).
2. **Eloquent Model & Relasi:** Mengatur relasi antar model (`User`, `Wallet`, `Category`, `Transaction`) beserta *casting* tipe data nominal (`decimal:2`).
3. **Service Layer (`CashflowService`):** Membangun logika transaksi menggunakan `DB::transaction()` untuk memastikan setiap pencatatan, perubahan, atau penghapusan transaksi secara atomik memperbarui kolom `balance` pada tabel `wallets`.
4. **Seeder Data Awal:** Membuat `DefaultCategorySeeder` agar pengguna baru otomatis memiliki dompet default (misal: *Kas Tunai*) dan kategori standar (*Penjualan, Gaji, Operasional, Konsumsi*).

### Fase 3: Antarmuka Mobile-First & Dashboard
1. **Layout Utama (`app.blade.php`):** Membangun kerangka antarmuka responsif bergaya aplikasi mobile (dilengkapi *bottom navigation bar* dan tombol tambah transaksi cepat).
2. **Halaman Dashboard:** Menampilkan total saldo keseluruhan, rincian saldo per dompet, ringkasan pemasukan vs pengeluaran bulan berjalan, dan 5 transaksi terakhir.
3. **Halaman Manajemen Transaksi & Dompet:** Membuat form input pemasukan/pengeluaran yang ringkas serta halaman riwayat transaksi dengan filter tanggal dan kategori.

### Fase 4: Transformasi PWA (Install Prompt Pasca-Login & Mode Standalone)
1. **Aset Ikon & Web Manifest:** Menyiapkan ikon aplikasi (`192x192` dan `512x512`) lalu membuat file `public/manifest.json` dengan pengaturan `"display": "standalone"` dan `"start_url": "/dashboard?source=pwa"`.
2. **Service Worker (`public/sw.js`):** Membuat skrip *caching* aset statis agar aplikasi terbuka instan layaknya aplikasi *native*.
3. **In-App Install Prompt (`pwa-installer.js`):** Membuat komponen *banner/modal* `"Pasang Aplikasi"` di halaman Dashboard yang mencegat event `beforeinstallprompt` tepat setelah pengguna login, serta menyembunyikannya otomatis jika aplikasi sudah berjalan dalam mode `standalone`.

### Fase 5: Implementasi Web Push Notification Arus Kas
1. **Instalasi Package WebPush:** Memasang `laravel-notification-channels/webpush`, menjalankan migrasi tabel `push_subscriptions`, dan men-generate kunci VAPID (`php artisan webpush:vapid`).
2. **Registrasi Endpoint Perangkat:** Menambahkan skrip JavaScript pada aplikasi untuk meminta izin notifikasi (*Notification.requestPermission()*) setelah login dan mengirimkan *Push Subscription* ke `PushSubscriptionController`.
3. **Observer & Queue Notification:** Membuat `TransactionObserver`, event `CashflowUpdated`, dan `CashflowChangedNotification` sehingga setiap perubahan data transaksi langsung memicu notifikasi ke HP melalui antrean latar belakang (*Queue Worker*).

### Fase 6: Pengujian (QA) & Deployment Produksi
1. **Pengujian End-to-End di Perangkat Asli:**
   * Menguji alur Login 1x $\rightarrow$ muncul *banner* Install $\rightarrow$ ikon terpasang di Home Screen HP $\rightarrow$ aplikasi ditutup dan dibuka ulang dari ikon (memastikan tidak meminta login ulang dan tampilan bersih tanpa bilah browser).
   * Menguji pengiriman notifikasi arus kas masuk/keluar saat aplikasi dalam kondisi tertutup (*background/killed*).
2. **Konfigurasi Server & SSL (HTTPS):** Melakukan *deploy* ke server produksi dengan sertifikat **SSL/HTTPS aktif** (syarat wajib agar Service Worker, PWA Install, dan Web Push berjalan di browser).
3. **Aktivasi Queue Worker:** Menjalankan `php artisan queue:work` menggunakan **Supervisor** di server agar pengiriman notifikasi arus kas berjalan otomatis 24/7 tanpa henti.