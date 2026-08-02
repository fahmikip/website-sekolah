<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningScope extends Model
{
    protected $guarded = ['id'];

    public function objective()
    {
        return $this->belongsTo(LearningObjective::class, 'learning_objective_id');
    }
}
