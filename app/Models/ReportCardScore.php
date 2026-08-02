<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportCardScore extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['description_approved' => 'boolean'];
    }

    public function reportCard()
    {
        return $this->belongsTo(ReportCard::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
