<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Announcement;
use App\Models\Banner;
use App\Models\Download;
use App\Models\Event;
use App\Models\Extracurricular;
use App\Models\Facility;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([['Libur Semester Ganjil', 'Libur'], ['Pelaksanaan Asesmen Sekolah', 'Ujian'], ['Pendaftaran PPDB Dibuka', 'PPDB']] as $i => [$title,$category]) {
            Announcement::firstOrCreate(['slug' => Str::slug($title)], ['title' => $title, 'category' => $category, 'excerpt' => 'Informasi penting untuk seluruh warga sekolah.', 'content' => 'Silakan memperhatikan jadwal dan ketentuan yang telah disampaikan sekolah. Informasi lanjutan dapat diperoleh melalui layanan administrasi.', 'status' => 'published', 'published_at' => now()->subDays($i)]);
        }
        foreach ([['Pameran Karya Siswa', 7], ['Pertemuan Orang Tua', 14], ['Pekan Olahraga Sekolah', 21]] as [$title,$days]) {
            Event::firstOrCreate(['slug' => Str::slug($title)], ['title' => $title, 'description' => 'Agenda kolaboratif sekolah yang melibatkan siswa, guru, dan keluarga.', 'location' => 'Aula Sekolah', 'starts_at' => now()->addDays($days)->setTime(8, 0), 'ends_at' => now()->addDays($days)->setTime(12, 0), 'person_in_charge' => 'Wakil Kepala Sekolah', 'status' => 'published']);
        }
        foreach (['Laboratorium Sains', 'Perpustakaan Digital', 'Ruang Kelas Interaktif', 'Lapangan Serbaguna'] as $name) {
            Facility::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => 'Fasilitas terawat untuk mendukung pengalaman belajar yang aman dan bermakna.', 'quantity' => 1, 'condition' => 'Baik', 'is_published' => true]);
        }
        foreach ([['Juara Olimpiade Sains', 'Akademik', 'Nasional'], ['Juara Festival Seni Pelajar', 'Seni', 'Provinsi'], ['Tim Robotik Terbaik', 'Teknologi', 'Nasional']] as $i => [$title,$category,$level]) {
            Achievement::firstOrCreate(['slug' => Str::slug($title)], ['title' => $title, 'recipient_name' => 'Siswa SMARTECH', 'category' => $category, 'level' => $level, 'achievement_date' => now()->subMonths($i + 1), 'description' => 'Prestasi membanggakan hasil kerja keras siswa dan pendampingan guru.', 'is_published' => true]);
        }
        foreach (['Robotik', 'Pramuka', 'Paduan Suara', 'Futsal'] as $i => $name) {
            Extracurricular::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'advisor' => 'Pembina '.$name, 'schedule' => 'Jumat, 15.00 WIB', 'description' => 'Ruang pengembangan minat, bakat, karakter, dan kolaborasi siswa.', 'member_count' => 20 + $i * 5, 'is_published' => true]);
        }
        foreach (['Dokumentasi Kegiatan Sekolah', 'Pekan Kreativitas Siswa'] as $title) {
            Gallery::firstOrCreate(['slug' => Str::slug($title)], ['title' => $title, 'category' => 'Kegiatan', 'activity_year' => now()->year, 'description' => 'Dokumentasi momen belajar dan kebersamaan warga sekolah.', 'is_published' => true]);
        }
        foreach ([['Apa jam layanan sekolah?', 'Layanan administrasi tersedia Senin sampai Jumat pukul 07.30–15.00 WIB.'], ['Bagaimana memperoleh informasi PPDB?', 'Informasi PPDB tersedia melalui halaman PPDB dan pengumuman resmi sekolah.'], ['Bagaimana menghubungi wali kelas?', 'Orang tua dapat menggunakan kanal komunikasi resmi yang diberikan pada awal tahun ajaran.']] as $i => [$q,$a]) {
            Faq::firstOrCreate(['question' => $q], ['answer' => $a, 'category' => 'Umum', 'sort_order' => $i, 'is_active' => true]);
        }
        foreach ([['Tentang Sekolah', 'tentang-sekolah'], ['Sejarah', 'sejarah'], ['Visi dan Misi', 'visi-misi']] as [$title,$slug]) {
            Page::firstOrCreate(['slug' => $slug], ['title' => $title, 'content' => 'SMARTECH Nusantara berkomitmen menyediakan pendidikan berkualitas, inklusif, dan relevan dengan perkembangan zaman.', 'status' => 'published', 'meta_title' => $title.' SMARTECH Nusantara']);
        }
        Banner::firstOrCreate(['title' => 'Belajar Hari Ini, Memimpin Esok'], ['subtitle' => 'Ekosistem pendidikan modern untuk generasi Indonesia yang berkarakter dan berdaya.', 'cta_label' => 'Lihat Profil', 'cta_url' => '/#profil', 'sort_order' => 1, 'is_active' => true]);
        $path = 'downloads/panduan-sekolah.pdf';
        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, "%PDF-1.4\n% Smart School sample document\n");
        }
        Download::firstOrCreate(['slug' => 'panduan-sekolah'], ['title' => 'Panduan Sekolah', 'category' => 'Dokumen Sekolah', 'description' => 'Panduan informasi dan layanan sekolah.', 'file_path' => $path, 'original_name' => 'panduan-sekolah.pdf', 'mime_type' => 'application/pdf', 'file_size' => Storage::disk('public')->size($path), 'is_published' => true]);
    }
}
