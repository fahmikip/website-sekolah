# Smart School Information System

Smart School Information System adalah platform manajemen sekolah berbasis web yang menyatukan website publik, CMS, data akademik, penilaian, e-Rapor, dan portal pengguna dalam satu aplikasi. Sistem dibangun dengan Laravel 12 menggunakan pendekatan mobile-first dan Progressive Web App (PWA), sehingga nyaman digunakan melalui desktop maupun perangkat Android.

Aplikasi ini dirancang untuk membantu sekolah mengelola publikasi informasi dan proses akademik secara terstruktur, aman, dan mudah dikembangkan. Data sensitif tetap berada di area terautentikasi, sementara informasi publik disajikan melalui website sekolah yang modern, responsif, dan ramah mesin pencari.

> Status pengembangan: fondasi sistem, CMS publik, master akademik, penilaian, e-Rapor, dan portal pengguna telah tersedia. Modul pembelajaran, layanan siswa, analitik, dan kesiapan produksi lanjutan dikembangkan mengikuti roadmap proyek.

## Kapabilitas utama

### Website publik dan CMS

- Beranda modern dengan profil singkat, statistik, program, berita, agenda, prestasi, dan informasi kontak.
- Pengelolaan berita beserta kategori, tag, status publikasi, jadwal tayang, gambar unggulan, dan metadata SEO.
- Modul pengumuman, agenda, galeri, fasilitas, prestasi, ekstrakurikuler, unduhan, FAQ, halaman, dan banner.
- Halaman daftar dan detail konten yang responsif serta sitemap XML.
- Pengaturan identitas sekolah, logo, kontak, lokasi, akreditasi, dan tema dari dashboard.
- PWA dengan manifest, service worker, halaman offline, tombol instalasi, dan navigasi bawah pada perangkat mobile.

### Administrasi dan akademik

- Dashboard administrasi responsif dengan navigasi berbasis hak akses.
- Master tahun ajaran, semester, kurikulum, fase, jenjang, rombongan belajar, mata pelajaran, guru, siswa, orang tua, staf, dan alumni.
- Relasi guru–mata pelajaran, siswa–kelas, wali kelas, serta jadwal pelajaran.
- Validasi bentrok jadwal guru dan ruangan.
- Impor data siswa dan ekspor data akademik menggunakan Excel.
- Upload dokumen dengan validasi tipe dan ukuran file.

### Penilaian

- Konfigurasi Capaian Pembelajaran (CP), Tujuan Pembelajaran (TP), lingkup materi, KKTP, jenis asesmen, dan bobot nilai.
- Pembuatan asesmen per tahun ajaran, semester, kelas, mata pelajaran, dan guru.
- Input nilai berbentuk tabel dengan autosave, bulk update, salin-tempel dari Excel, impor, dan ekspor.
- Siklus status nilai: `draft`, `submitted`, `verified`, dan `locked`.
- Perhitungan nilai akhir, rata-rata, median, nilai tertinggi/terendah, ketuntasan, distribusi, dan heatmap kompetensi.
- Pencatatan perubahan nilai melalui score audit serta modul remedial dan pengayaan.

### e-Rapor dan portal pengguna

- Pembuatan rapor semester berdasarkan data akademik dan nilai siswa.
- Deskripsi capaian, catatan wali kelas, kehadiran, prestasi, ekstrakurikuler, dan status kenaikan.
- Preview, tampilan cetak, ekspor PDF, serta token dan QR untuk verifikasi rapor.
- Halaman verifikasi publik hanya menampilkan informasi minimum yang diperlukan.
- Portal khusus guru, siswa, orang tua, dan kepala sekolah.
- Satu akun orang tua dapat mengakses anak yang terhubung dengan akunnya.

### Keamanan dan kualitas

- Autentikasi Laravel Breeze dengan verifikasi email, reset password, konfirmasi password, dan login throttling.
- Role-Based Access Control menggunakan Spatie Laravel Permission.
- Form Request validation, proteksi CSRF, output escaping Blade, mass-assignment protection, dan query melalui Eloquent.
- Pembatasan akses pada route serta validasi upload file.
- Database transaction untuk proses multi-data penting.
- Feature test dan unit test dengan database SQLite in-memory.

## Role pengguna

Sistem menyediakan fondasi role berikut:

| Role | Fokus akses |
| --- | --- |
| Super Admin | Seluruh konfigurasi dan modul sistem |
| Administrator | Operasional website, akademik, penilaian, dan rapor |
| Kepala Sekolah | Monitoring, verifikasi, publikasi rapor, dan dashboard pimpinan |
| Wakil Kepala Sekolah | Operasional akademik sesuai permission |
| Operator | Pengelolaan data sekolah dan akademik |
| Guru | Jadwal, kelas, asesmen, nilai, dan portal guru |
| Wali Kelas | Rekap kelas, nilai, catatan, dan rapor |
| Guru BK | Fondasi akses layanan konseling sesuai permission |
| Siswa | Informasi akademik dan portal siswa |
| Orang Tua | Pemantauan data akademik anak |
| Petugas Perpustakaan | Fondasi role untuk modul perpustakaan |
| Panitia PPDB | Fondasi role untuk modul penerimaan siswa baru |

Permission tetap menjadi sumber otorisasi utama. Role dapat memperoleh kumpulan permission yang berbeda sesuai kebijakan sekolah.

## Teknologi

| Area | Teknologi |
| --- | --- |
| Backend | PHP 8.3+, Laravel 12 |
| Frontend | Blade, Livewire 4, Alpine.js, Tailwind CSS, Vite |
| Database | MySQL 8+ / MariaDB untuk development |
| Autentikasi | Laravel Breeze |
| Otorisasi | Spatie Laravel Permission |
| Spreadsheet | Laravel Excel |
| Dokumen | DomPDF |
| Verifikasi | Simple QR Code |
| Pengujian | PHPUnit 11 |
| PWA | Web App Manifest dan Service Worker |

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

- PHP 8.3 atau lebih baru dengan ekstensi `bcmath`, `curl`, `dom`, `fileinfo`, `gd`, `mbstring`, `openssl`, `pdo_mysql`, dan `zip`.
- Composer 2.7 atau lebih baru.
- Node.js 20 atau lebih baru dan npm.
- MySQL 8 atau lebih baru. MariaDB 10.4 dari XAMPP dapat digunakan untuk development lokal.

## Instalasi lokal

1. Clone repository dan masuk ke direktori aplikasi.

   ```bash
   git clone https://github.com/fahmikip/website-sekolah.git
   cd website-sekolah
   ```

2. Pasang dependency backend dan frontend.

   ```bash
   composer install
   npm install
   ```

3. Siapkan environment aplikasi.

   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

4. Buat database bernama `smart_school`, kemudian sesuaikan konfigurasi berikut pada `.env`.

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=smart_school
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Siapkan database, storage publik, dan aset frontend.

   ```bash
   php artisan migrate --seed
   php artisan storage:link
   npm run build
   ```

6. Jalankan aplikasi.

   ```bash
   php artisan serve
   ```

Aplikasi tersedia secara default di `http://127.0.0.1:8000`. Pada PowerShell dengan execution policy terbatas, gunakan `npm.cmd` sebagai pengganti `npm`.

## Akun development

| Field | Nilai |
| --- | --- |
| Email | `superadmin@example.test` |
| Password | `password` |

> Akun ini hanya untuk development. Ganti password, hapus kredensial contoh, dan tinjau ulang seluruh role serta permission sebelum deployment production.

## Menjalankan mode development

Untuk menjalankan server Laravel, queue listener, log viewer, dan Vite secara bersamaan:

```bash
composer run dev
```

Atau jalankan setiap layanan secara terpisah:

```bash
php artisan serve
php artisan queue:work --tries=3
php artisan schedule:work
npm run dev
```

## Pengujian dan quality check

```bash
php artisan test
vendor/bin/pint --test
npm run build
php artisan route:list
```

Test menggunakan SQLite in-memory sehingga tidak mengubah database development. Sebelum commit, pastikan test, pemeriksaan format, dan build frontend seluruhnya berhasil.

## Struktur proyek

```text
app/
├── Exports/             # Ekspor spreadsheet
├── Http/
│   ├── Controllers/     # Controller publik, portal, dan administrasi
│   └── Requests/        # Validasi serta otorisasi request
├── Imports/             # Impor spreadsheet
├── Models/              # Model dan relasi domain
└── Services/            # Logika bisnis penilaian, rapor, profil, dan berita
database/
├── factories/           # Factory data pengujian
├── migrations/          # Struktur dan constraint database
└── seeders/             # Role, permission, konfigurasi, serta data contoh
resources/
├── css/                 # Tailwind dan style aplikasi
├── js/                  # Alpine, PWA, dan interaksi frontend
└── views/               # Blade publik, admin, portal, dan dokumen
routes/                  # Route web, autentikasi, dan scheduler
tests/                   # Unit test dan feature test
public/                  # Entry point, manifest, service worker, dan ikon PWA
docs/screenshots/        # Dokumentasi visual desktop dan mobile
```

## Data dan penyimpanan

- Upload publik disimpan melalui disk `public` pada `storage/app/public`.
- Jalankan `php artisan storage:link` satu kali pada setiap environment baru.
- Jangan commit `.env`, kredensial, database dump, log, atau file upload pengguna.
- Gunakan object storage/S3-compatible storage bila volume file production cukup besar.

## Queue dan scheduler

Development dapat menggunakan:

```bash
php artisan queue:work --tries=3
php artisan schedule:work
```

Pada production, gunakan process supervisor untuk queue worker dan cron berikut untuk scheduler:

```cron
* * * * * php /path/to/application/artisan schedule:run >> /dev/null 2>&1
```

Scheduler digunakan untuk proses terjadwal, termasuk pembaruan status publikasi CMS.

## Deployment production

1. Gunakan PHP 8.3+, MySQL 8+, HTTPS, dan document root yang menunjuk ke direktori `public`.
2. Atur `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, database, mail, cache, queue, session, dan storage melalui environment server.
3. Pasang dependency dan bangun aset production.

   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   ```

4. Jalankan proses deployment Laravel.

   ```bash
   php artisan migrate --force
   php artisan storage:link
   php artisan optimize
   ```

5. Aktifkan queue worker, scheduler, rotasi log, monitoring, serta backup database dan upload.
6. Lakukan smoke test untuk website publik, login, setiap role, input nilai, pembuatan rapor, PDF, dan verifikasi QR.

## Prinsip keamanan production

- Jangan gunakan password seed atau kredensial database bersama.
- Gunakan HTTPS dan secure cookie pada seluruh akses production.
- Berikan permission minimum pada user database dan akun aplikasi.
- Batasi akses tulis hanya ke `storage` dan `bootstrap/cache`.
- Validasi MIME, ekstensi, ukuran, dan hak akses setiap upload.
- Jalankan audit dependency dan backup sebelum release.
- Jangan menampilkan NIK, nomor KK, data orang tua, nilai lengkap, atau data pribadi lain pada halaman publik.

## Troubleshooting

| Kendala | Solusi |
| --- | --- |
| `could not find driver` | Aktifkan `pdo_mysql` untuk aplikasi atau `pdo_sqlite` untuk test pada PHP CLI. |
| `Vite manifest not found` | Jalankan `npm install` lalu `npm run build`. |
| Gambar upload tidak tampil | Jalankan `php artisan storage:link` dan periksa konfigurasi `APP_URL`. |
| Permission belum diperbarui | Jalankan `php artisan permission:cache-reset`. |
| Konfigurasi lama masih terbaca | Jalankan `php artisan optimize:clear`. |
| Queue tidak memproses job | Pastikan koneksi queue benar dan worker sedang aktif. |
| URL HTTPS/tunnel menghasilkan aset HTTP | Pastikan proxy dipercaya dan `APP_URL` menggunakan HTTPS. |

## Roadmap

- **Phase 1–6:** fondasi, CMS publik, akademik, penilaian, e-Rapor, dan portal pengguna.
- **Phase 7:** materi pembelajaran, tugas, submission, bank soal, dan CBT.
- **Phase 8:** PPDB, bimbingan konseling, perpustakaan, dan alumni.
- **Phase 9:** analitik akademik, analitik kehadiran, dan early warning berbasis aturan.
- **Phase 10:** audit keamanan, backup, observabilitas, optimasi performa, QA, dan kesiapan go-live.

Detail progres internal dikelola terpisah melalui checklist pengembangan dan tidak menjadi bagian dari repository publik.

## Lisensi dan penggunaan

Repository menggunakan struktur dasar Laravel berlisensi MIT. Untuk penggunaan di lingkungan sekolah, pastikan kebijakan privasi, retensi data, hak akses, dan prosedur operasional disesuaikan dengan regulasi serta kebijakan institusi yang berlaku.
