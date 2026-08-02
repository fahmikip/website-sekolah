<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AchievementCriterion extends Model
{
    protected $guarded = ['id'];

    public function ranges()
    {
        return $this->hasMany(CriterionRange::class);
    }
}
