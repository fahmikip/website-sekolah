<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alumnus extends Model
{
    use SoftDeletes;

    protected $table = 'alumni';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['publication_consent' => 'boolean'];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
