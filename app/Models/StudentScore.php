<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentScore extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime', 'verified_at' => 'datetime', 'locked_at' => 'datetime'];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function details()
    {
        return $this->hasMany(ScoreDetail::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}
