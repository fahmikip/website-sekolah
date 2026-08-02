<?php

namespace App\Services;

use App\Models\ReportCard;
use App\Models\Student;
use App\Models\StudentScore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportCardService
{
    public function generate(Student $student, int $academicYearId, int $semesterId, int $classroomId): ReportCard
    {
        return DB::transaction(function () use ($student, $academicYearId, $semesterId, $classroomId) {
            $report = ReportCard::firstOrCreate(
                ['student_id' => $student->id, 'academic_year_id' => $academicYearId, 'semester_id' => $semesterId],
                ['classroom_id' => $classroomId, 'verification_token' => Str::random(48)]
            );
            $scores = StudentScore::query()
                ->where('student_id', $student->id)
                ->whereNotNull('final_score')
                ->whereHas('assessment', fn ($q) => $q->where('academic_year_id', $academicYearId)->where('semester_id', $semesterId)->where('classroom_id', $classroomId))
                ->with('assessment.subject')->get()->groupBy('assessment.subject_id');
            foreach ($scores as $subjectScores) {
                $average = round($subjectScores->avg('final_score'), 2);
                $subject = $subjectScores->first()->assessment->subject;
                $predicate = match (true) {
                    $average >= 90 => 'A', $average >= 80 => 'B', $average >= 70 => 'C', default => 'D'
                };
                $report->scores()->updateOrCreate(['subject_id' => $subject->id], [
                    'final_score' => $average, 'predicate' => $predicate,
                    'description' => "Menunjukkan capaian {$predicate} pada {$subject->name}; pertahankan kekuatan dan tingkatkan kompetensi yang belum tercapai.",
                ]);
            }

            return $report->load(['student', 'classroom', 'academicYear', 'semester', 'scores.subject']);
        });
    }
}
