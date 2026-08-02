<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Remedial extends Model
{
    protected $guarded = ['id'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}
