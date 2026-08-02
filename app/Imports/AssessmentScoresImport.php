<?php

namespace App\Imports;

use App\Models\Assessment;
use App\Models\StudentScore;
use App\Models\User;
use App\Services\ScorePersistenceService;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssessmentScoresImport implements ToCollection, WithHeadingRow
{
    public int $count = 0;

    public function __construct(private readonly Assessment $assessment, private readonly User $actor) {}

    public function collection(Collection $rows): void
    {
        $this->assessment->load('components');
        foreach ($rows as $row) {
            $score = StudentScore::where('assessment_id', $this->assessment->id)->whereHas('student', fn ($q) => $q->where('nis', (string) $row['nis']))->first();
            if (! $score) {
                continue;
            }
            $details = $this->assessment->components->map(function ($component) use ($row) {
                $value = $row['component_'.$component->id] ?? null;
                if (! is_numeric($value) || $value < 0 || $value > $component->max_score) {
                    throw ValidationException::withMessages(['file' => "Nilai {$component->name} tidak valid."]);
                }

                return ['assessment_component_id' => $component->id, 'score' => (float) $value, 'weight' => (float) $component->weight, 'max_score' => (float) $component->max_score];
            })->all();
            app(ScorePersistenceService::class)->save($score, $details, $this->actor, 'Import Excel');
            $this->count++;
        }
    }
}
