# Bomb Busters — Handoff

Last updated: 2026-11-26

## Current Status

The project is at the rules-analysis stage. No game logic has been implemented yet; the downloaded BGA Studio scaffold remains unchanged.

- Downloaded all 26 files from the SFTP directory `/bombbusters` into the project root.
- Verified every downloaded file against the server by size and SHA-256.
- Read the project skill at `.claude/skills/board-game-arena/SKILL.md` and the rules-clarification reference.
- Extracted and analyzed `misc/BombBusters_rules_EN-web.pdf` (English rules v1.0, 8 pages).
- Created `doc/RULES.md`, `doc/AUTHOR_QUESTIONS.md`, and `doc/ASSUMPTIONS.md`.
- Created two commits:
  - `49d4f5d` — `chore: import BGA Studio scaffold`
  - `422a218` — `docs: analyze Bomb Busters rules`

## Rules Summary

Bomb Busters is a cooperative game for 2–5 players. Players cut matching blue or yellow wires through Dual Cut and Solo Cut actions, safely reveal red wires only when a hand contains nothing else, and win when every hand is empty. Cutting a red wire or advancing the detonator to the skull causes a shared loss.

The base rules are in `doc/RULES.md`. Interpretations must not be added to that file; working assumptions belong in `doc/ASSUMPTIONS.md` and must remain linked to `doc/AUTHOR_QUESTIONS.md`.

## Graphics and UI

No game graphics are available yet. This does not block development.

Use a functional HTML/CSS interface with text-based placeholders:

- wires as colored text tiles;
- hands, the detonator, and markers as regular DOM elements;
- Equipment and Character cards as text cards.

Keep rendering separate from game logic so final artwork can replace placeholders without rewriting the rules implementation.

## Open Questions

The following items are still OPEN in `doc/AUTHOR_QUESTIONS.md`:

1. **Q1:** Mission, Equipment, and Character card contents are missing. Scans, photos, or faithful transcriptions are needed.
2. **Q2:** Numeric detonator starting positions and loss thresholds for 2–5 players are missing.
3. **Q3:** The rules do not explain Solo Cut when one or three yellow wires remain in one hand.

Six additional questions have working assumptions. Seven hypotheses, H1–H7, are recorded in `doc/ASSUMPTIONS.md`.

## Known Scaffold Mismatches

`gameinfos.jsonc` still contains template metadata:

- `game_name` is `My Great Game`;
- players are `[2, 3, 4]`, while the rules support 2–5;
- `is_coop` is `0`, while the game is cooperative.

There is no project `Makefile` yet. SFTP connectivity has been confirmed using the Paramiko helper and configuration approach from the neighboring `bga-big.top` project. Do not record credentials or private-key contents in this file.

## Verification Performed

- Recursive server/local comparison: 26 files, no missing files, size mismatches, or SHA-256 mismatches.
- Markdown checks: final newlines, trailing whitespace, and question/assumption cross-references passed.
- `git diff --check` passed for the rules documents. The unchanged BGA-generated scaffold contains pre-existing whitespace warnings and was imported verbatim.
- No PHP/JS checks, deployment, or real BGA table test has been performed because game implementation has not started.

## Next Steps

1. Configure project metadata and add `make check` / `make deploy` support.
2. Obtain Mission 1 setup data and the required Equipment/Character card texts.
3. Define the wire/item model and mission configuration.
4. Implement setup and the basic turn states with placeholder UI.
5. Deploy and test a real hotseat table on BGA Studio.
6. Update this handoff after each completed development stage, including verification results and new blockers.
