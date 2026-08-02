# Smart School Information System

Fondasi aplikasi sekolah modern berbasis Laravel 12. Phase 1 mencakup autentikasi, RBAC, profil sekolah, settings, navigasi database, dashboard admin, layout publik, upload tervalidasi, dan test. Homepage awal Phase 2 juga sudah tersedia sebagai landasan CMS.

## Pratinjau aplikasi

Seluruh gambar berikut merupakan tangkapan layar halaman penuh dalam mode desktop dan mobile.

| Halaman | Desktop | Mobile |
| --- | --- | --- |
| Beranda | <img src="docs/screenshots/01-home-desktop.png" alt="Beranda mode desktop" width="520"> | <img src="docs/screenshots/01-home-mobile.png" alt="Beranda mode mobile" width="220"> |
| Berita | <img src="docs/screenshots/02-news-desktop.png" alt="Berita mode desktop" width="520"> | <img src="docs/screenshots/02-news-mobile.png" alt="Berita mode mobile" width="220"> |
| Agenda | <img src="docs/screenshots/03-agenda-desktop.png" alt="Agenda mode desktop" width="520"> | <img src="docs/screenshots/03-agenda-mobile.png" alt="Agenda mode mobile" width="220"> |
| Prestasi | <img src="docs/screenshots/04-achievements-desktop.png" alt="Prestasi mode desktop" width="520"> | <img src="docs/screenshots/04-achievements-mobile.png" alt="Prestasi mode mobile" width="220"> |
| Login | <img src="docs/screenshots/05-login-desktop.png" alt="Login mode desktop" width="520"> | <img src="docs/screenshots/05-login-mobile.png" alt="Login mode mobile" width="220"> |

## Kebutuhan sistem

- PHP 8.3+ (`bcmath`, `curl`, `dom`, `fileinfo`, `gd`, `mbstring`, `openssl`, `pdo_mysql`, `zip`)
- Composer 2.7+
- Node.js 20+ dan npm
- MySQL 8+; MariaDB 10.4 XAMPP dapat dipakai untuk development lokal

## Instalasi

```bash
composer install
copy .env.example .env
php artisan key:generate
npm install
npm run build
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Buat database `smart_school` terlebih dahulu, lalu sesuaikan `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada `.env`. Di PowerShell dengan execution policy terbatas, gunakan `npm.cmd` sebagai pengganti `npm`.

## Akun development

- Email: `superadmin@example.test`
- Password: `password`

> Wajib ganti password dan nonaktifkan/ubah kredensial seed sebelum deployment production.

## Development

```bash
composer run dev
php artisan test
vendor/bin/pint
```

Test menggunakan SQLite in-memory sehingga tidak menyentuh database development. File upload publik disimpan di `storage/app/public`; jalankan `php artisan storage:link` sekali.

## Queue dan scheduler

```bash
php artisan queue:work --tries=3
php artisan schedule:work
```

Production sebaiknya menjalankan queue melalui process supervisor dan cron `php artisan schedule:run` setiap menit.

## Deployment production

1. Atur `APP_ENV=production`, `APP_DEBUG=false`, URL HTTPS, database, mail, cache, queue, dan session pada environment server.
2. Jalankan `composer install --no-dev --optimize-autoloader` dan `npm ci && npm run build`.
3. Jalankan `php artisan migrate --force`, `php artisan storage:link`, lalu `php artisan optimize`.
4. Pastikan web root menunjuk ke folder `public`, izin tulis hanya untuk `storage` dan `bootstrap/cache`, serta backup database/uploads aktif.
5. Ganti password akun seed dan gunakan kredensial database dengan privilege minimum.

## Struktur inti

- `app/Models`: User dengan RBAC, SchoolProfile, Setting, Menu
- `app/Http/Requests`: validasi profil dan upload
- `app/Http/Controllers/Admin`: endpoint admin terotorisasi
- `resources/views/layouts`: layout publik dan dashboard
- `database/seeders`: role, permission, profil, settings, menu, super admin
- `tests/Feature/Feature`: test fondasi dan otorisasi profil

## Troubleshooting

- `could not find driver`: aktifkan `pdo_mysql` atau `pdo_sqlite` pada PHP CLI.
- `Vite manifest not found`: jalankan `npm install && npm run build`.
- Gambar tidak tampil: jalankan `php artisan storage:link`.
- Perubahan permission belum terbaca: jalankan `php artisan permission:cache-reset`.
- Error konfigurasi lama: jalankan `php artisan optimize:clear`.

## Roadmap

Phase 2 berikutnya melengkapi CRUD CMS untuk berita, pengumuman, agenda, galeri, fasilitas, prestasi, ekstrakurikuler, download, dan SEO. Modul lanjutan mengikuti phase pada master brief setelah core stabil.
