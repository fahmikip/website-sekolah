<?php

namespace App\Models;

use Database\Factories\ParentProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ParentProfile extends Model
{
    /** @use HasFactory<ParentProfileFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'parent_student')->withPivot('is_primary')->withTimestamps();
    }
}
