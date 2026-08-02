<?php

namespace Tests\Feature\Admin;

use App\Models\Assessment;
use App\Models\Remedial;
use App\Models\ScoreAudit;
use App\Models\StudentScore;
use App\Models\User;
use App\Services\ScorePersistenceService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoreWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_score_save_calculates_audits_and_marks_remedial(): void
    {
        $this->seed(DatabaseSeeder::class);
        $actor = User::where('email', 'superadmin@example.test')->firstOrFail();
        $assessment = Assessment::with('components')->firstOrFail();
        $score = StudentScore::where('assessment_id', $assessment->id)->firstOrFail();
        $details = $assessment->components->map(fn ($component) => ['assessment_component_id' => $component->id, 'score' => 60, 'max_score' => (float) $component->max_score, 'weight' => (float) $component->weight])->all();
        app(ScorePersistenceService::class)->save($score, $details, $actor, 'Test input');
        $this->assertEquals(60, $score->fresh()->final_score);
        $this->assertDatabaseHas(ScoreAudit::class, ['student_score_id' => $score->id, 'reason' => 'Test input']);
        $this->assertDatabaseHas(Remedial::class, ['student_id' => $score->student_id, 'assessment_id' => $assessment->id]);
    }

    public function test_locked_score_rejects_actor_without_unlock_permission(): void
    {
        $this->seed(DatabaseSeeder::class);
        $score = StudentScore::firstOrFail();
        $score->update(['locked_at' => now(), 'status' => 'locked']);
        $actor = User::where('email', 'guru@example.test')->firstOrFail();
        $this->expectException(AuthorizationException::class);
        app(ScorePersistenceService::class)->save($score, [], $actor, 'Attempt');
    }
}
