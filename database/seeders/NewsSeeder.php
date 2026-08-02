<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = User::where('email', 'superadmin@example.test')->firstOrFail();
        $categories = collect(['Akademik', 'Kegiatan', 'Prestasi'])->mapWithKeys(function ($name) {
            $category = NewsCategory::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => "Informasi {$name} sekolah", 'is_active' => true]);

            return [$name => $category];
        });
        foreach ([
            ['Semangat Baru Memulai Tahun Ajaran', 'Akademik'], ['Siswa Raih Prestasi Tingkat Nasional', 'Prestasi'],
            ['Pekan Kreativitas dan Teknologi Sekolah', 'Kegiatan'], ['Kolaborasi Orang Tua dalam Pembelajaran', 'Akademik'],
            ['Tim Sekolah Menjuarai Kompetisi Sains', 'Prestasi'], ['Gerakan Sekolah Hijau dan Berkelanjutan', 'Kegiatan'],
        ] as $index => [$title, $category]) {
            News::firstOrCreate(['slug' => Str::slug($title)], [
                'news_category_id' => $categories[$category]->id, 'author_id' => $author->id, 'title' => $title,
                'excerpt' => 'Kabar terbaru dari ekosistem belajar SMARTECH Nusantara yang aktif, kolaboratif, dan menginspirasi.',
                'content' => "Sekolah terus menghadirkan pengalaman belajar yang relevan bagi seluruh siswa.\n\nProgram ini terlaksana melalui kolaborasi guru, siswa, keluarga, dan komunitas. Setiap kegiatan dirancang untuk menumbuhkan kompetensi sekaligus karakter.",
                'status' => 'published', 'is_featured' => $index === 0, 'published_at' => now()->subDays($index + 1),
                'meta_title' => $title, 'meta_description' => 'Berita terbaru SMARTECH Nusantara tentang '.$title.'.',
            ]);
        }
    }
}
