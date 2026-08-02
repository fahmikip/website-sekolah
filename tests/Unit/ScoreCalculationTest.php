<?php

namespace Tests\Unit;

use App\Services\ScoreCalculationService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ScoreCalculationTest extends TestCase
{
    public function test_it_calculates_configurable_weighted_score(): void
    {
        $service = new ScoreCalculationService;
        $this->assertSame(84.0, $service->weightedAverage([['score' => 80, 'max_score' => 100, 'weight' => 30], ['score' => 90, 'max_score' => 100, 'weight' => 50], ['score' => 75, 'max_score' => 100, 'weight' => 20]]));
    }

    public function test_weights_must_total_one_hundred(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new ScoreCalculationService)->weightedAverage([['score' => 80, 'weight' => 50]]);
    }

    public function test_it_resolves_configured_criterion(): void
    {
        $label = (new ScoreCalculationService)->criterion(88, [['min' => 0, 'max' => 60, 'label' => 'Perlu Bimbingan'], ['min' => 86, 'max' => 100, 'label' => 'Sangat Baik']]);
        $this->assertSame('Sangat Baik', $label);
    }
}
