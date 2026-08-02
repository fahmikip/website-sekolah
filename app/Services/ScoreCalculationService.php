<?php

namespace App\Services;

use InvalidArgumentException;

class ScoreCalculationService
{
    public function weightedAverage(array $components): float
    {
        if ($components === []) {
            return 0.0;
        }
        $weight = array_sum(array_column($components, 'weight'));
        if (abs($weight - 100) > 0.001) {
            throw new InvalidArgumentException('Total bobot komponen harus 100%.');
        }
        $result = 0.0;
        foreach ($components as $component) {
            $max = (float) ($component['max_score'] ?? 100);
            if ($max <= 0 || $component['score'] < 0 || $component['score'] > $max) {
                throw new InvalidArgumentException('Nilai komponen berada di luar rentang.');
            }
            $result += ((float) $component['score'] / $max * 100) * ((float) $component['weight'] / 100);
        }

        return round($result, 2);
    }

    public function criterion(float $score, array $ranges): ?string
    {
        foreach ($ranges as $range) {
            if ($score >= $range['min'] && $score <= $range['max']) {
                return $range['label'];
            }
        }

        return null;
    }
}
