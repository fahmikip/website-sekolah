<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AchievementCriterion;
use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\Classroom;
use App\Models\Curriculum;
use App\Models\LearningObjective;
use App\Models\LearningOutcome;
use App\Models\Phase;
use App\Models\Semester;
use App\Models\StudentScore;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Diagnostik', 'Formatif', 'Sumatif', 'Tugas', 'Quiz', 'Praktik', 'Proyek', 'Portofolio', 'Observasi', 'UTS', 'UAS/SAS', 'SAT'] as $name) {
            AssessmentType::firstOrCreate(['code' => str($name)->slug('_')->upper()], ['name' => $name, 'is_active' => true, 'is_summative' => in_array($name, ['Sumatif', 'UTS', 'UAS/SAS', 'SAT'])]);
        }
        $criterion = AchievementCriterion::firstOrCreate(['name' => 'KKTP Default'], ['curriculum_id' => Curriculum::firstOrFail()->id, 'passing_score' => 71, 'is_active' => true]);
        foreach ([[0, 60, 'Perlu Bimbingan'], [61, 70, 'Mulai Berkembang'], [71, 85, 'Tercapai'], [86, 100, 'Sangat Baik']] as [$min,$max,$label]) {
            $criterion->ranges()->firstOrCreate(['min_score' => $min, 'max_score' => $max], ['label' => $label]);
        }
        $subject = Subject::first();
        $year = AcademicYear::first();
        $classroom = Classroom::with('students')->first();
        $outcome = LearningOutcome::firstOrCreate(['academic_year_id' => $year->id, 'subject_id' => $subject->id, 'code' => 'CP-01'], ['curriculum_id' => Curriculum::first()->id, 'phase_id' => Phase::first()->id, 'description' => 'Memahami konsep dan menerapkannya dalam pemecahan masalah.', 'is_active' => true]);
        $objectives = collect(range(1, 3))->map(fn ($i) => LearningObjective::firstOrCreate(['learning_outcome_id' => $outcome->id, 'code' => 'TP-'.$i], ['description' => 'Tujuan pembelajaran '.$i, 'sequence' => $i, 'is_active' => true]));
        $assessment = Assessment::firstOrCreate(['title' => 'Asesmen Formatif Awal'], ['academic_year_id' => $year->id, 'semester_id' => Semester::first()->id, 'classroom_id' => $classroom->id, 'subject_id' => $subject->id, 'teacher_id' => Teacher::first()->id, 'assessment_type_id' => AssessmentType::where('name', 'Formatif')->first()->id, 'assessment_date' => now(), 'max_score' => 100, 'status' => 'draft']);
        foreach ($objectives as $i => $objective) {
            $assessment->components()->firstOrCreate(['name' => 'TP '.($i + 1)], ['learning_objective_id' => $objective->id, 'weight' => $i === 2 ? 34 : 33, 'max_score' => 100]);
        }
        foreach ($classroom->students as $student) {
            StudentScore::firstOrCreate(['assessment_id' => $assessment->id, 'student_id' => $student->id], ['status' => 'draft']);
        }
    }
}
