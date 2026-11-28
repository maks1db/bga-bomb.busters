<?php

declare(strict_types=1);

namespace Bga\Games\bombbusters;

final class Missions
{
    public const OPTION_ID = 100;
    public const PRACTICE_ID = 0;

    public static function get(int $id, ?int $playerCount = null): array
    {
        $missions = self::all();
        if (!isset($missions[$id])) {
            throw new \InvalidArgumentException("Unknown mission ID: {$id}");
        }
        $mission = $missions[$id];
        if ($playerCount !== null) {
            foreach ($mission['playerCountOverrides'][$playerCount] ?? [] as $key => $value) {
                $mission[$key] = $value;
            }
        }
        unset($mission['playerCountOverrides']);
        return $mission;
    }

    public static function all(): array
    {
        return [
            1 => [
                'id' => 1,
                'name' => 'Mission 1',
                'title' => 'Training, Day 1',
                'lesson' => 'Lesson 1: How do you cut a wire?',
                'setupDescription' => 'Use 24 blue wires with values 1–6, four copies of each value. Do not use red or yellow wires or equipment cards.',
                'rulesDescription' => 'Cut wires using Dual Cut or Solo Cut. This training mission introduces the basic cutting rules.',
                'blueValues' => range(1, 6),
                'red' => ['count' => 0, 'candidates' => 0],
                'yellow' => ['count' => 0, 'candidates' => 0],
                'equipment' => false,
                'specialRules' => [],
            ],
            2 => [
                'id' => 2,
                'name' => 'Mission 2',
                'title' => 'Training, Day 2',
                'lesson' => 'Lesson 2: Cutting the yellow wire',
                'setupDescription' => 'Use 32 blue wires with values 1–8 and two yellow wires with sort values 1.1–7.1. Do not use red wires or equipment cards.',
                'rulesDescription' => 'All yellow wires count as the same value when cutting. They can be cut with Dual Cut or Solo Cut. Do not place a yellow Info token during setup.',
                'blueValues' => range(1, 8),
                'red' => ['count' => 0, 'candidates' => 0],
                'yellow' => ['count' => 2, 'candidates' => 2, 'maxValue' => 7],
                'equipment' => false,
                'specialRules' => [],
            ],
            3 => [
                'id' => 3,
                'name' => 'Mission 3',
                'title' => 'Training, Day 3',
                'lesson' => 'Lesson 3: (Do not) cut the red wire',
                'setupDescription' => 'Use 40 blue wires with values 1–10 and one red wire with a sort value from 1.5 to 9.5. Draw one random equipment card per player, replacing cards 11 (Coffee Thermos) and 12 (Equals Tag) if drawn.',
                'rulesDescription' => 'Cutting a red wire immediately ends the mission in failure. When only red wires remain in your hand, use Reveal Red to remove them safely. Equipment becomes available after two wires of its shown value have been cut and can be used once.',
                'blueValues' => range(1, 10),
                'red' => ['count' => 1, 'candidates' => 1, 'maxValue' => 9],
                'yellow' => ['count' => 0, 'candidates' => 0],
                'equipment' => true,
                'specialRules' => [],
            ],
            4 => [
                'id' => 4,
                'name' => 'Mission 4',
                'title' => 'Field Training, Day 1',
                'lesson' => 'From theory to practice!',
                'setupDescription' => 'Use all 48 blue wires, one red wire, and two yellow wires. For two players, use four yellow wires instead.',
                'rulesDescription' => 'This mission combines the rules learned so far: equipment unlocks after its shown pair is cut, yellow wires are cut together, and a red wire is safe only when revealed as the last remaining wire in a hand.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 1, 'candidates' => 1],
                'yellow' => ['count' => 2, 'candidates' => 2],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'yellow' => ['count' => 4, 'candidates' => 4],
                    ],
                ],
            ],
            5 => [
                'id' => 5,
                'name' => 'Mission 5',
                'title' => 'Field Training, Day 2',
                'lesson' => 'This is a minefield, not a yellow brick road...',
                'setupDescription' => 'Use all 48 blue wires, one red wire, and two yellow wires secretly selected from three revealed candidates. Mark all three candidates with question marks. For two players, use two red wires instead.',
                'rulesDescription' => 'The two yellow wires must be cut together. Until then, each yellow wire has one of the possible values shown by the question-mark markers.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 1, 'candidates' => 1],
                'yellow' => ['count' => 2, 'candidates' => 3],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'red' => ['count' => 2, 'candidates' => 2],
                    ],
                ],
            ],
            6 => [
                'id' => 6,
                'name' => 'Mission 6',
                'title' => 'Field Training, Day 3',
                'lesson' => 'What is yellow and waiting for you? Four yellow wires that must be cut in time!',
                'setupDescription' => 'Use all 48 blue wires, one red wire, and four yellow wires. For two players, use two red wires instead.',
                'rulesDescription' => 'All four yellow wires count as the same value. Cut two with Dual Cut, or cut two or four with Solo Cut when you hold all remaining yellow wires.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 1, 'candidates' => 1],
                'yellow' => ['count' => 4, 'candidates' => 4],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'red' => ['count' => 2, 'candidates' => 2],
                    ],
                ],
            ],
            7 => [
                'id' => 7,
                'name' => 'Mission 7',
                'title' => 'Final Training Day',
                'lesson' => 'There is a good red wire and a bad red wire...',
                'setupDescription' => 'Use all 48 blue wires and one red wire secretly selected from two revealed candidates. Mark both candidates with question marks. For two players, select the red wire from three candidates instead. Do not use yellow wires.',
                'rulesDescription' => 'Exactly one red wire is in play. Its sort value is one of two possible values, or one of three possible values in a two-player game.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 1, 'candidates' => 2],
                'yellow' => ['count' => 0, 'candidates' => 0],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'red' => ['count' => 1, 'candidates' => 3],
                    ],
                ],
            ],
            8 => [
                'id' => 8,
                'name' => 'Mission 8',
                'title' => 'Graduation Exam',
                'lesson' => 'Today is the big day! Are you ready to become a Bomb Buster?',
                'setupDescription' => 'Use all 48 blue wires, one red wire secretly selected from two candidates, and two yellow wires secretly selected from three candidates. Mark all candidates with question marks. For two players, select the red wire from three candidates and use four yellow wires instead.',
                'rulesDescription' => 'There are no additional rules: apply everything you have learned. If you fail, retry the exam. If you succeed, your team graduates and can continue to missions 9–19.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 1, 'candidates' => 2],
                'yellow' => ['count' => 2, 'candidates' => 3],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'red' => ['count' => 1, 'candidates' => 3],
                        'yellow' => ['count' => 4, 'candidates' => 4],
                    ],
                ],
            ],
            self::PRACTICE_ID => [
                'id' => self::PRACTICE_ID,
                'name' => 'Practice Mission',
                'title' => 'Full wire set',
                'lesson' => 'An unofficial mission for testing the base actions.',
                'setupDescription' => 'Use all 48 blue wires, two red wires, and two yellow wires.',
                'rulesDescription' => 'An unofficial practice setup for testing the base actions.',
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
