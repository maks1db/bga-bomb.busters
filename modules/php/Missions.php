<?php

declare(strict_types=1);

namespace Bga\Games\bombbusters;

final class Missions
{
    public const OPTION_ID = 100;
    public const PRACTICE_ID = 0;

    public static function get(int $id): array
    {
        $missions = self::all();
        if (!isset($missions[$id])) {
            throw new \InvalidArgumentException("Unknown mission ID: {$id}");
        }
        return $missions[$id];
    }

    public static function all(): array
    {
        return [
            1 => [
                'id' => 1,
                'name' => 'Mission 1',
                'title' => 'Training, Day 1',
                'lesson' => 'Lesson 1: How do you cut a wire?',
                'blueValues' => range(1, 6),
                'red' => ['count' => 0, 'candidates' => 0],
                'yellow' => ['count' => 0, 'candidates' => 0],
                'equipment' => false,
                'specialRules' => [],
            ],
            self::PRACTICE_ID => [
                'id' => self::PRACTICE_ID,
                'name' => 'Practice Mission',
                'title' => 'Full wire set',
                'lesson' => 'An unofficial mission for testing the base actions.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 2, 'candidates' => 2],
                'yellow' => ['count' => 2, 'candidates' => 2],
                'equipment' => false,
                'specialRules' => [],
            ],
        ];
    }

    public static function getWireCount(array $mission): int
    {
        return count($mission['blueValues']) * 4
            + $mission['red']['count']
            + $mission['yellow']['count'];
    }
}
