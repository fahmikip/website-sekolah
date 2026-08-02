<?php

namespace App\Models;

use Database\Factories\DownloadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Download extends Model
{
    /** @use HasFactory<DownloadFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'download_count'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }
}
