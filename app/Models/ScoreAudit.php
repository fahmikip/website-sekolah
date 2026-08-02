<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoreAudit extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['changed_at' => 'datetime'];
    }
}
