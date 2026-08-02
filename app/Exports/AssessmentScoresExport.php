<?php

namespace App\Exports;

use App\Models\Assessment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssessmentScoresExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly Assessment $assessment) {}

    public function headings(): array
    {
        return ['nis', 'nama', ...$this->assessment->components->map(fn ($component) => 'component_'.$component->id)->all(), 'nilai_akhir', 'status'];
    }

    public function collection(): Collection
    {
        return $this->assessment->studentScores->map(function ($score) {
            $details = $score->details->keyBy('assessment_component_id');

            return [$score->student->nis, $score->student->name, ...$this->assessment->components->map(fn ($component) => $details->get($component->id)?->score)->all(), $score->final_score, $score->status];
        });
    }
}
