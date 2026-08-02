<?php

namespace App\Services;

use App\Models\News;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class NewsService
{
    public function store(array $data, int $authorId, ?UploadedFile $featuredImage = null, ?UploadedFile $ogImage = null): News
    {
        return DB::transaction(function () use ($data, $authorId, $featuredImage, $ogImage) {
            $data['author_id'] = $authorId;
            $data = $this->prepare($data, null, $featuredImage, $ogImage);

            return News::create($data);
        });
    }

    public function update(News $news, array $data, ?UploadedFile $featuredImage = null, ?UploadedFile $ogImage = null): News
    {
        return DB::transaction(function () use ($news, $data, $featuredImage, $ogImage) {
            $news->update($this->prepare($data, $news, $featuredImage, $ogImage));

            return $news->refresh();
        });
    }

    public function delete(News $news): void
    {
        DB::transaction(fn () => $news->delete());
    }

    private function prepare(array $data, ?News $news, ?UploadedFile $featuredImage, ?UploadedFile $ogImage): array
    {
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? null) ?: $data['title'], $news?->id);
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if ($data['status'] === 'scheduled' && (empty($data['published_at']) || now()->greaterThanOrEqualTo($data['published_at']))) {
            throw ValidationException::withMessages(['published_at' => 'Jadwal publikasi harus berada di masa mendatang.']);
        }

        foreach (['featured_image' => $featuredImage, 'og_image' => $ogImage] as $column => $file) {
            if (! $file) {
                unset($data[$column]);

                continue;
            }
            if ($news?->{$column}) {
                Storage::disk('public')->delete($news->{$column});
            }
            $data[$column] = $file->store('news/'.now()->format('Y/m'), 'public');
        }

        return $data;
    }

    private function uniqueSlug(string $value, ?int $ignoreId): string
    {
        $base = Str::slug($value) ?: Str::random(8);
        $slug = $base;
        $counter = 2;
        while (News::withTrashed()->where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
