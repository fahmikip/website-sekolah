<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type', 'is_public'];

    protected function casts(): array
    {
        return ['is_public' => 'boolean'];
    }

    public static function valueFor(string $key, mixed $default = null): mixed
    {
        return cache()->remember("setting.{$key}", 3600, fn () => static::where('key', $key)->value('value') ?? $default);
    }
}
