<?php

namespace App\Services;

use App\Models\AchievementCriterion;
use App\Models\Remedial;
use App\Models\ScoreAudit;
use App\Models\ScoreDetail;
use App\Models\StudentScore;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class ScorePersistenceService
{
    public function save(StudentScore $record, array $details, User $actor, string $reason): StudentScore
    {
        return DB::transaction(function () use ($record, $details, $actor, $reason) {
            if ($record->locked_at && ! $actor->can('unlock_scores')) {
                throw new AuthorizationException('Nilai telah dikunci.');
            }$old = $record->final_score;
            $parts = [];
            foreach ($details as $detail) {
                ScoreDetail::updateOrCreate(['student_score_id' => $record->id, 'assessment_component_id' => $detail['assessment_component_id']], ['score' => $detail['score']]);
                $parts[] = $detail;
            }$final = app(ScoreCalculationService::class)->weightedAverage($parts);
            $record->update(['final_score' => $final]);
            $assessment = $record->assessment;
            $passing = AchievementCriterion::where('subject_id', $assessment->subject_id)->where('is_active', true)->value('passing_score') ?? 75;
            if ($final < $passing) {
                Remedial::updateOrCreate(['student_id' => $record->student_id, 'assessment_id' => $record->assessment_id], ['old_score' => $final, 'remedial_type' => 'Pembelajaran ulang']);
            } else {
                Remedial::where('student_id', $record->student_id)->where('assessment_id', $record->assessment_id)->whereNull('remedial_score')->delete();
            }
            ScoreAudit::create(['student_score_id' => $record->id, 'changed_by' => $actor->id, 'old_value' => $old, 'new_value' => $final, 'reason' => $reason, 'changed_at' => now(), 'ip_address' => request()->ip()]);

            return $record->refresh();
        });
    }
}
