<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoreDetail extends Model
{
    protected $guarded = ['id'];

    public function component()
    {
        return $this->belongsTo(AssessmentComponent::class, 'assessment_component_id');
    }
}
