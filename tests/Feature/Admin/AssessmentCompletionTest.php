<?php

namespace Tests\Feature\Admin;

use App\Models\Assessment;
use App\Models\LearningObjective;
use App\Models\StudentScore;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class AssessmentCompletionTest extends TestCase
{
    use RefreshDatabase;

    private function context(): array
    {
        $this->seed(DatabaseSeeder::class);

        return [User::where('email', 'superadmin@example.test')->firstOrFail(), Assessment::with('components')->firstOrFail()];
    }

    public function test_learning_scope_crud_works(): void
    {
        [$admin] = $this->context();
        $objective = LearningObjective::firstOrFail();
        $this->actingAs($admin)->post(route('admin.assessment.config.store', 'scopes'), ['learning_objective_id' => $objective->id, 'title' => 'Bilangan dan aljabar', 'description' => 'Lingkup materi', 'sort_order' => 1])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('learning_scopes', ['title' => 'Bilangan dan aljabar']);
    }

    public function test_score_row_autosaves_and_is_audited(): void
    {
        [$admin, $assessment] = $this->context();
        $score = StudentScore::where('assessment_id', $assessment->id)->firstOrFail();
        $details = $assessment->components->map(fn ($component) => ['assessment_component_id' => $component->id, 'score' => 88, 'weight' => $component->weight, 'max_score' => $component->max_score])->all();
        $this->actingAs($admin)->postJson(route('admin.assessment.scores.autosave', $assessment), ['scores' => [['student_score_id' => $score->id, 'reason' => 'Autosave spreadsheet', 'details' => $details]]])->assertOk()->assertJson(['saved' => true, 'final_score' => 88]);
        $this->assertDatabaseHas('score_audits', ['student_score_id' => $score->id, 'reason' => 'Autosave spreadsheet']);
    }

    public function test_scores_import_and_export_real_excel(): void
    {
        [$admin, $assessment] = $this->context();
        $score = StudentScore::with('student')->where('assessment_id', $assessment->id)->firstOrFail();
        $headers = ['nis', 'nama', ...$assessment->components->map(fn ($component) => 'component_'.$component->id)->all()];
        $values = [$score->student->nis, $score->student->name, ...$assessment->components->map(fn () => 91)->all()];
        $sheet = new Spreadsheet;
        $sheet->getActiveSheet()->fromArray([$headers, $values]);
        $path = tempnam(sys_get_temp_dir(), 'scores');
        (new Xlsx($sheet))->save($path);
        $file = new UploadedFile($path, 'scores.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
        $this->actingAs($admin)->post(route('admin.assessment.scores.import', $assessment), ['file' => $file])->assertSessionHas('success');
        $this->assertEquals(91, $score->fresh()->final_score);
        $this->actingAs($admin)->get(route('admin.assessment.scores.export', $assessment))->assertOk()->assertDownload();
    }

    public function test_remedial_and_enrichment_crud_are_authorized(): void
    {
        [$admin, $assessment] = $this->context();
        $score = StudentScore::firstOrFail();
        $this->actingAs($admin)->post(route('admin.assessment.interventions.store', 'remedials'), ['student_id' => $score->student_id, 'assessment_id' => $assessment->id, 'old_score' => 60, 'remedial_score' => 80, 'final_score' => 80, 'remedial_type' => 'Tes ulang'])->assertSessionHasNoErrors();
        $this->actingAs($admin)->post(route('admin.assessment.interventions.store', 'enrichments'), ['student_id' => $score->student_id, 'subject_id' => $assessment->subject_id, 'activity' => 'Proyek lanjutan'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('remedials', ['student_id' => $score->student_id, 'final_score' => 80]);
        $this->assertDatabaseHas('enrichments', ['student_id' => $score->student_id, 'activity' => 'Proyek lanjutan']);
    }

    public function test_assessment_and_components_are_created_transactionally(): void
    {
        [$admin, $existing] = $this->context();
        $payload = $existing->only(['academic_year_id', 'semester_id', 'classroom_id', 'subject_id', 'teacher_id', 'assessment_type_id']) + ['title' => 'Asesmen Baru', 'assessment_date' => now()->toDateString(), 'max_score' => 100, 'status' => 'draft', 'components' => [['learning_objective_id' => null, 'name' => 'Pengetahuan', 'weight' => 60, 'max_score' => 100], ['learning_objective_id' => null, 'name' => 'Keterampilan', 'weight' => 40, 'max_score' => 100]]];
        $this->actingAs($admin)->post(route('admin.assessments.store'), $payload)->assertSessionHasNoErrors();
        $created = Assessment::where('title', 'Asesmen Baru')->firstOrFail();
        $this->assertCount(2, $created->components);
        $this->assertSame($created->classroom->students()->count(), $created->studentScores()->count());
        $payload['components'][1]['weight'] = 30;
        $this->actingAs($admin)->post(route('admin.assessments.store'), $payload)->assertSessionHasErrors('components');
        $this->assertSame(1, Assessment::where('title', 'Asesmen Baru')->count());
    }
}
