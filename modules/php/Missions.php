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
            9 => [
                'id' => 9,
                'name' => 'Mission 9',
                'title' => 'Sense of Priority',
                'lesson' => 'A mad scientist has tampered with this bomb: some wires must be cut in a specific order. Stay focused and keep control!',
                'setupDescription' => 'Use all 48 blue wires, one red wire, and two yellow wires. Shuffle the number cards, draw three face up in a row, and place Sequence card A above the leftmost card. For two players, use two red wires and four yellow wires instead.',
                'rulesDescription' => 'Number cards A, B, and C define a cutting order. B and C cannot be cut until at least two A wires have been cut; C cannot be cut until at least two B wires have been cut. Other wires can be cut at any time. After completing each step, turn over its number card and advance the Sequence card.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 1, 'candidates' => 1],
                'yellow' => ['count' => 2, 'candidates' => 2],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'red' => ['count' => 2, 'candidates' => 2],
                        'yellow' => ['count' => 4, 'candidates' => 4],
                    ],
                ],
            ],
            10 => [
                'id' => 10,
                'name' => 'Mission 10',
                'title' => 'A Run of Bad Luck',
                'lesson' => 'You must untangle a bundle of wires, but things are tangled between your teammates too. Keep your team spirit: there are enough wires for everyone!',
                'setupDescription' => 'Use all 48 blue wires, one red wire, and four yellow wires. Prepare a 15-minute timer, or 12 minutes for two players. Replace equipment card 11 (Coffee Thermos) if drawn during setup or play.',
                'rulesDescription' => 'Start the timer after setup and defuse the bomb before time expires. Turns need not proceed clockwise: after the active player finishes, any player may say “Snip!” to become active. The same player cannot take consecutive turns except in a two-player game.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 1, 'candidates' => 1],
                'yellow' => ['count' => 4, 'candidates' => 4],
                'equipment' => true,
                'specialRules' => [],
            ],
            11 => [
                'id' => 11,
                'name' => 'Mission 11',
                'title' => 'Blue on Red Is Just Terrible!',
                'lesson' => '“Captain, I am dyslexic and color-blind.”',
                'setupDescription' => 'Use all 48 blue wires and two yellow wires; do not use red wires. Draw a random number card face up onto the back of the mission card. Replace any equipment card showing that value. For two players, use four yellow wires and the captain does not place an Info token during setup.',
                'rulesDescription' => 'The four blue wires with the value on the number card count as red. Cutting any of them immediately ends the mission in failure. To reveal them safely, a player must have only wires of that value left in hand. Info tokens may still be placed before those wires.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 0, 'candidates' => 0],
                'yellow' => ['count' => 2, 'candidates' => 2],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'yellow' => ['count' => 4, 'candidates' => 4],
                    ],
                ],
            ],
            12 => [
                'id' => 12,
                'name' => 'Mission 12',
                'title' => 'Lost Equipment',
                'lesson' => '“Wires, wires... I am caught between a hammer and an anvil!” Speaking of hammers, where is all the equipment?',
                'setupDescription' => 'Use all 48 blue wires, one red wire, and four yellow wires. Place a number card on every equipment card in play without covering the equipment’s own value. For two players, use two red wires and four yellow wires.',
                'rulesDescription' => 'To unlock equipment, cut two wires of its own value and two wires of the value on its number card, in either order. When Double Bottom replaces equipment, place the number card on both new cards. If either required pair is already cut, remove that number card instead of placing a new one.',
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
            13 => [
                'id' => 13,
                'name' => 'Mission 13',
                'title' => 'Code Red!',
                'lesson' => 'The mad scientist is truly furious! He sees red and has installed three red wires in this homemade bomb!',
                'setupDescription' => 'Use all 48 blue wires and three red wires; do not use yellow wires. Do not shuffle the red wires with the others: deal them face down clockwise starting with the captain. With two players, the captain receives two red wires, one on each stand. Players receive random Info tokens instead of choosing them; replace yellow tokens. With two players, the captain places no Info token.',
                'rulesDescription' => 'A special action allows a player to point to three uncut wires and cut all three red wires simultaneously. If any selected wire is not red, the bomb explodes. With four or five players, even a player without red wires may use this action. A player whose hand contains only red wires must use it.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 3, 'candidates' => 3],
                'yellow' => ['count' => 0, 'candidates' => 0],
                'equipment' => true,
                'specialRules' => [],
            ],
            14 => [
                'id' => 14,
                'name' => 'Mission 14',
                'title' => 'Dangerous Bomb Buster',
                'lesson' => 'This rookie needs constant supervision! Any mistake could be fatal!',
                'setupDescription' => 'Use all 48 blue wires, two red wires, and two yellow wires selected from three candidates. Shuffle and deal character cards face down, then reveal them. The player with the Captain card is the Rookie for this mission. For two players, use three red wires and four yellow wires.',
                'rulesDescription' => 'If the Rookie fails a Dual Cut, the bomb immediately explodes. The Rookie cannot use equipment card 9 (Stabilizer).',
                'blueValues' => range(1, 12),
                'red' => ['count' => 2, 'candidates' => 2],
                'yellow' => ['count' => 2, 'candidates' => 3],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'red' => ['count' => 3, 'candidates' => 3],
                        'yellow' => ['count' => 4, 'candidates' => 4],
                    ],
                ],
            ],
            15 => [
                'id' => 15,
                'name' => 'Mission 15',
                'title' => 'Mission in Nagareyama',
                'lesson' => 'We cannot read it either. All your equipment boxes are labeled in Japanese, so every box contains a surprise!',
                'setupDescription' => 'Use all 48 blue wires and one red wire selected from three candidates; do not use yellow wires. Place equipment cards face down without revealing them. Shuffle all 12 number cards into a face-down deck and reveal the top card. For two players, use two red wires selected from three candidates.',
                'rulesDescription' => 'When all four wires matching the current number card have been cut, reveal one equipment card. It becomes available immediately regardless of its own value, then reveal the next number card. If a newly revealed number is already complete, replace it with the next card without revealing equipment.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 1, 'candidates' => 3],
                'yellow' => ['count' => 0, 'candidates' => 0],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'red' => ['count' => 2, 'candidates' => 3],
                    ],
                ],
            ],
            16 => [
                'id' => 16,
                'name' => 'Mission 16',
                'title' => 'Perfectly Logical',
                'lesson' => 'Do not let anyone get ahead of you; act logically!',
                'setupDescription' => 'Use all 48 blue wires, one red wire, and two yellow wires selected from three candidates. Shuffle the number cards, draw three face up in a row, and place Sequence card B above the leftmost card. For two players, use two red wires and four yellow wires.',
                'rulesDescription' => 'Number cards A, B, and C define a cutting order. B and C cannot be cut until all four A wires have been cut; C cannot be cut until all four B wires have been cut. Other wires can be cut at any time. After completing each step, turn over its number card and advance the Sequence card.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 1, 'candidates' => 1],
                'yellow' => ['count' => 2, 'candidates' => 3],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'red' => ['count' => 2, 'candidates' => 2],
                        'yellow' => ['count' => 4, 'candidates' => 4],
                    ],
                ],
            ],
            17 => [
                'id' => 17,
                'name' => 'Mission 17',
                'title' => 'Baron Munchausen',
                'lesson' => 'Can you tell truth from fiction?',
                'setupDescription' => 'Use all 48 blue wires and two red wires selected from three candidates; do not use yellow wires. Deal character cards randomly. The captain becomes Baron Munchausen and returns the Captain card, losing the Double Detector. Instead of one true Info token, the Baron places two false tokens before non-red wires whose values do not match. For two players, use three red wires.',
                'rulesDescription' => 'Every Info token placed by the Baron is false and means “this wire does not have that value.” After a failed Dual Cut with the Baron, place a token showing the value guessed by the teammate. The Baron cannot use equipment but may participate in equipment cards 2 (Walkie-Talkie) and 8 (Shared Scanner).',
                'blueValues' => range(1, 12),
                'red' => ['count' => 2, 'candidates' => 3],
                'yellow' => ['count' => 0, 'candidates' => 0],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'red' => ['count' => 3, 'candidates' => 3],
                    ],
                ],
            ],
            18 => [
                'id' => 18,
                'name' => 'Mission 18',
                'title' => 'Bat-Help',
                'lesson' => 'No equipment was issued for this mission, but fortunately Batman lent you his Bat-Radar.',
                'setupDescription' => 'Use all 48 blue wires and two red wires; do not use yellow wires. Shuffle the 12 number cards into a face-down deck. Place only equipment card 8 (Shared Scanner) face up and no other equipment. Do not place Info tokens during setup. For two players, use three red wires.',
                'rulesDescription' => 'The Shared Scanner is always available. On each turn, reveal a number card, use the Shared Scanner for that value, and choose a player to cut wires of that value. Reshuffle the number cards when the deck runs out and discard a number when all four matching wires are cut. A player with only red wires must reveal them. Do not repeat scanner answers from earlier rounds.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 2, 'candidates' => 2],
                'yellow' => ['count' => 0, 'candidates' => 0],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'red' => ['count' => 3, 'candidates' => 3],
                    ],
                ],
            ],
            19 => [
                'id' => 19,
                'name' => 'Mission 19',
                'title' => 'Into the Beast’s Jaws...',
                'lesson' => 'You have finally found the mad scientist’s secret hideout! He is making his sinister plans in this cave. Be careful: one foot in the grave is not always a good plan!',
                'setupDescription' => 'Use all 48 blue wires, one red wire, and two yellow wires selected from three candidates. Before the first turn, play the audio file identified by the QR code on the mission card.',
                'rulesDescription' => 'Follow the audio instructions. On failure, listen again and try not to repeat the previous mistake. On success, the team finds the scientist’s safe and may open the box containing missions 20–30.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 1, 'candidates' => 1],
                'yellow' => ['count' => 2, 'candidates' => 3],
                'equipment' => true,
                'specialRules' => [],
            ],
            20 => [
                'id' => 20,
                'name' => 'Mission 20',
                'title' => 'Big Bad Wolf',
                'lesson' => 'This is how a serial bomber signs his work. Who is afraid of the Big Bad Wolf? Certainly not you!',
                'setupDescription' => 'Use all 48 blue wires, two red wires, and two yellow wires. Place the last wire dealt to each stand at its right end regardless of value and mark it with an X token. Replace equipment card 2 (Walkie-Talkie) if drawn during setup or play. For two players, use two red wires selected from three candidates and four yellow wires.',
                'rulesDescription' => 'Equipment cannot target wires marked X. Those wires ignore all equipment, including personal equipment, Super Detector (5), and Shared Scanner (8). Marked wires remain out of numerical order at the right end of their stands.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 2, 'candidates' => 2],
                'yellow' => ['count' => 2, 'candidates' => 2],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'red' => ['count' => 2, 'candidates' => 3],
                        'yellow' => ['count' => 4, 'candidates' => 4],
                    ],
                ],
            ],
            21 => [
                'id' => 21,
                'name' => 'Mission 21',
                'title' => 'Deadly Haggis',
                'lesson' => 'An unprecedented case in Scotland! A bomb is hidden inside a haggis!',
                'setupDescription' => 'Use all 48 blue wires and one red wire selected from two candidates; do not use yellow wires. Replace all Info tokens with Even/Odd tokens, including during setup. For two players, use two red wires.',
                'rulesDescription' => 'Use Even/Odd tokens instead of numbered Info tokens throughout the mission, including after mistakes and when using equipment card 4 (Sticker). A red wire has no value and is neither even nor odd.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 1, 'candidates' => 2],
                'yellow' => ['count' => 0, 'candidates' => 0],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'red' => ['count' => 2, 'candidates' => 2],
                    ],
                ],
            ],
            22 => [
                'id' => 22,
                'name' => 'Mission 22',
                'title' => 'Anything but That!',
                'lesson' => '“When you have eliminated the impossible, whatever remains, however improbable, must be the truth.” — Sherlock Holmes',
                'setupDescription' => 'Use all 48 blue wires, one red wire, and four yellow wires. Instead of choosing normal Info tokens, each player takes two tokens showing values absent from their hand and places them beside it; yellow tokens are allowed. A player with two stands places one token beside each. If fewer than two values are absent, place fewer tokens.',
                'rulesDescription' => 'After the first pair of yellow wires is cut, each player in clockwise order starting with the captain chooses an Info token from the supply and gives it to the player on their left. The recipient places it before a matching wire, or beside the hand if that value is absent.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 1, 'candidates' => 1],
                'yellow' => ['count' => 4, 'candidates' => 4],
                'equipment' => true,
                'specialRules' => [],
            ],
            23 => [
                'id' => 23,
                'name' => 'Mission 23',
                'title' => 'Mission in Fordwich',
                'lesson' => 'Population 381, 103 km from London. This is not a movie: dangerous missions do not all happen in New York! Even the post office cannot find this town... Your equipment is delayed.',
                'setupDescription' => 'Use all 48 blue wires and one red wire selected from three candidates; do not use yellow wires. Instead of the normal equipment setup, place seven random equipment cards face down on the board. Place a random number card face up beside the mission card. For two players, use two red wires selected from three candidates.',
                'rulesDescription' => 'All four wires of the shown value must be cut simultaneously using a special action that selects them without equipment or Double Detector. Failure explodes the bomb. At the end of each round before the captain’s turn, discard one of the seven equipment cards until the special action succeeds. All remaining equipment then becomes available regardless of previously cut wires.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 1, 'candidates' => 3],
                'yellow' => ['count' => 0, 'candidates' => 0],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'red' => ['count' => 2, 'candidates' => 3],
                    ],
                ],
            ],
            24 => [
                'id' => 24,
                'name' => 'Mission 24',
                'title' => 'All Personnel Present!',
                'lesson' => 'We are counting on you to count the wires; the countdown has already begun...',
                'setupDescription' => 'Use all 48 blue wires and two red wires; do not use yellow wires. Replace Info tokens with ×1, ×2, and ×3 tokens. They indicate how many wires of one value are on a stand, including wires already cut. Tokens ×2 and ×3 may be placed before any wire in the matching group, but never before red wires. For two players, use three red wires.',
                'rulesDescription' => 'Use ×1, ×2, and ×3 tokens throughout the mission instead of numbered Info tokens, including after mistakes and when using equipment card 4 (Sticker). Sticker may place its token before a wire that has already been cut.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 2, 'candidates' => 2],
                'yellow' => ['count' => 0, 'candidates' => 0],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'red' => ['count' => 3, 'candidates' => 3],
                    ],
                ],
            ],
            25 => [
                'id' => 25,
                'name' => 'Mission 25',
                'title' => 'So I Can Hear You Better...',
                'lesson' => 'Whether its ears are big or not, you know the Big Bad Wolf can hear you! Everything you say in this mission may have terrible consequences!',
                'setupDescription' => 'Use all 48 blue wires and two red wires; do not use yellow wires. For two players, use three red wires.',
                'rulesDescription' => 'Players may not say wire values aloud. They may write, make sounds, use their fingers, or speak indirectly instead. If a player forgets and says a value aloud, advance the detonator by one space.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 2, 'candidates' => 2],
                'yellow' => ['count' => 0, 'candidates' => 0],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'red' => ['count' => 3, 'candidates' => 3],
                    ],
                ],
            ],
            26 => [
                'id' => 26,
                'name' => 'Mission 26',
                'title' => 'Speaking of Wolves...',
                'lesson' => 'You are in the beast’s jaws again. Only well-coordinated teamwork will get you out!',
                'setupDescription' => 'Use all 48 blue wires and two red wires; do not use yellow wires. Place all 12 number cards face up on the table. Replace equipment card 10 (XY Ray) if drawn during setup.',
                'rulesDescription' => 'At the start of a turn, flip one face-up number card face down, then use Dual Cut or Solo Cut on that value. Subsequent turns may use only values still face up. If all cards are face down at the start of a turn, flip them all face up. Remove a number card when all four matching wires are cut. A player with no wire of a visible value passes without advancing the detonator; otherwise they must cut one.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 2, 'candidates' => 2],
                'yellow' => ['count' => 0, 'candidates' => 0],
                'equipment' => true,
                'specialRules' => [],
            ],
            27 => [
                'id' => 27,
                'name' => 'Mission 27',
                'title' => 'Wires Are Not Toys',
                'lesson' => 'This gray substance is plastic dough, called “clay” or “plastique” in some parts of the world. No time for word games: it is explosive!',
                'setupDescription' => 'Use all 48 blue wires, one red wire, and four yellow wires. After appointing the captain, do not deal character cards: no Double Detectors are available. Replace equipment card 7 (Spare Batteries) if drawn during setup or play. With two players, the captain places no Info token during setup.',
                'rulesDescription' => 'After the first pair of yellow wires is cut, reveal random Info tokens equal to the player count. In clockwise order starting with the captain, each player chooses one and places it before a matching wire, or beside the hand if that value is absent.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 1, 'candidates' => 1],
                'yellow' => ['count' => 4, 'candidates' => 4],
                'equipment' => true,
                'specialRules' => [],
            ],
            28 => [
                'id' => 28,
                'name' => 'Mission 28',
                'title' => 'Captain Lazy',
                'lesson' => 'Captain Lazy is not the most brilliant bomb buster. He forgot the equipment. If he makes a mistake, it is all over!',
                'setupDescription' => 'Use all 48 blue wires, two red wires, and four yellow wires. After character cards are dealt, the captain becomes Captain Lazy and returns the character card, losing the Double Detector. For two players, use three red wires and four yellow wires.',
                'rulesDescription' => 'Captain Lazy cannot use equipment or a Double Detector. If Captain Lazy fails a Dual Cut, the bomb immediately explodes. They may participate in equipment cards 2 (Walkie-Talkie) and 8 (Shared Scanner).',
                'blueValues' => range(1, 12),
                'red' => ['count' => 2, 'candidates' => 2],
                'yellow' => ['count' => 4, 'candidates' => 4],
                'equipment' => true,
                'specialRules' => [],
                'playerCountOverrides' => [
                    2 => [
                        'red' => ['count' => 3, 'candidates' => 3],
                    ],
                ],
            ],
            29 => [
                'id' => 29,
                'name' => 'Mission 29',
                'title' => 'Wrong Number',
                'lesson' => '“Beware of dangerous numbers!”',
                'setupDescription' => 'Use all 48 blue wires and three red wires; do not use yellow wires. Deal two random number cards face down to each player and three to the player on the captain’s right. Place the rest in a face-down deck. Players look at their own cards without showing them. With two players, the captain places no Info token during setup.',
                'rulesDescription' => 'At the start of a turn, the player on the active player’s right places one of their number cards face down. After the active player acts, reveal it: if they cut wires of that value, advance the detonator one space. The active player then takes the card. Remove completed number cards. A player with one card left draws a replacement, skipping completed values. When only wires of one value remain, remove the last number card.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 3, 'candidates' => 3],
                'yellow' => ['count' => 0, 'candidates' => 0],
                'equipment' => true,
                'specialRules' => [],
            ],
            30 => [
                'id' => 30,
                'name' => 'Mission 30',
                'title' => 'Speed Mission!',
                'lesson' => 'The Big Bad Wolf is back and shows no mercy. Buckle up: he has prepared a mission that must be completed at full speed!',
                'setupDescription' => 'Use all 48 blue wires, one red wire selected from two candidates, and four yellow wires. Shuffle the 12 number cards into a face-down deck beside the board. Before the first turn, play the audio file identified by the QR code on the mission card.',
                'rulesDescription' => 'Follow the audio instructions. On failure, listen again and try to act faster. On success, the police catch the Big Bad Wolf and the team may open the box containing missions 31–42.',
                'blueValues' => range(1, 12),
                'red' => ['count' => 1, 'candidates' => 2],
                'yellow' => ['count' => 4, 'candidates' => 4],
                'equipment' => true,
                'specialRules' => [],
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
