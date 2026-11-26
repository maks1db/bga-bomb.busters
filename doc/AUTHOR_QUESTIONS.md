# Bomb Busters — Questions for the Authors

A digital implementation needs an explicit outcome for every situation, including rare ones. The questions below concern rules or components that are not fully specified by the base rulebook. Working assumptions are listed separately so development can continue where a reasonable default exists.

## Open Questions (OPEN)

### Q1. [RULES-MISSING] Mission, Equipment, and Character card content

**Status:** OPEN

**What the rules say:** “The Captain reads the Mission card aloud… Each mission may introduce new rules not found in this rulebook.” The components also include 12 Equipment cards and 5 Character cards, but the rulebook explains only Captain and Double Detector.

**The problem:** The base PDF does not contain the setup or special rules of missions 1–8, the number of blue/red/yellow wires used by each mission, or the texts of most Equipment and Character cards.

**What would help:** Scans, photos, or faithful transcriptions of both sides of the Mission cards and of all Equipment and Character cards. Please also indicate which missions beyond the training campaign should be included in the digital version.

### Q2. [RULES-VISUAL] Detonator dial positions and loss threshold

**Status:** OPEN

**What the rules say:** “Set the detonator dial to the section matching the number of bomb disposal experts” and “the bomb explodes if the dial reaches the skull.”

**The problem:** The text does not provide numeric starting positions, the number of advances required to reach the skull, or whether the track differs by player count beyond its starting section.

**What would help:** A clear board/dial image or a table giving, for each player count from 2 to 5, the starting position and the number of failed cuts that reaches the skull.

### Q3. [RULES-AMBIGUOUS] Solo Cut with an odd number of remaining yellow wires

**Status:** OPEN

**What the rules say:** Solo Cut cuts “those identical wires in pairs (either 2 or 4).” Yellow Solo Cut is available when a player “has all the remaining yellow wires in their hand.” Missions can bring an odd number of yellow wires into play, such as “1 out of 2” or “1 out of 3.”

**The problem:** A player can eventually hold one or three remaining yellow wires while no other player has yellow wires. Cutting a pair can leave one yellow wire that cannot be Solo Cut and has no teammate’s yellow wire for a Dual Cut.

**What would help:** The intended outcome for one or three remaining yellow wires in one hand: whether they can be cut together, whether one can be cut alone, or whether another rule prevents this position.

## Questions with Working Assumptions (ASSUMED)

### Q4. [RULES-MISSING] Initial Info token when a player has no blue wire

**Status:** ASSUMED → [H1] in `ASSUMPTIONS.md`

**What the rules say:** Each player places an Info token pointing to “one of the blue wires in their hand.”

**The problem:** A random deal can give a player only red and/or yellow wires, especially in missions using fewer blue wires.

**Our assumption:** That player skips the initial Info-token placement.

**Impact if wrong:** Setup needs a different legal choice or a reroll rule.

### Q5. [RULES-MISSING] Selecting the Equipment cards for a mission

**Status:** ASSUMED → [H2] in `ASSUMPTIONS.md`

**What the rules say:** “Take as many Equipment cards as there are bomb disposal experts… This is the only equipment available for this game.”

**The problem:** The base rules do not say whether these cards are chosen, drafted, ordered by the mission, or drawn randomly.

**Our assumption:** Unless a Mission card specifies particular equipment, draw the required number randomly without replacement.

**Impact if wrong:** Mission setup and the equipment-selection interface will need adjustment.

### Q6. [RULES-AMBIGUOUS] Double Detector across two tile stands

**Status:** ASSUMED → [H3] in `ASSUMPTIONS.md`

**What the rules say:** Double Detector points to “2 wires in a teammate’s stand,” while another rule says that two stands together form one hand.

**The problem:** It is not explicit whether the two selected wires may be on different stands belonging to the same teammate.

**Our assumption:** They may be selected from either or both stands of that teammate because the two stands form one hand.

**Impact if wrong:** Double Detector targeting must be restricted to one selected stand.

### Q7. [RULES-IMPLICIT] Double Detector when red wires are selected

**Status:** ASSUMED → [H4] in `ASSUMPTIONS.md`

**What the rules say:** A match makes the action succeed. On failure, “If only 1 of the 2 chosen wires is red, the bomb does not explode,” and the Info token identifies the non-red wire.

**The problem:** The outcomes for one red wire plus one matching wire, and for two red wires, are not stated in separate examples.

**Our assumption:** A matching non-red wire makes the action succeed without cutting the red wire. If neither wire matches, one red wire is safe as described, but two red wires cause an explosion.

**Impact if wrong:** Double Detector resolution and loss handling need adjustment.

### Q8. [RULES-IMPLICIT] Naming RED during a Dual Cut

**Status:** ASSUMED → [H5] in `ASSUMPTIONS.md`

**What the rules say:** All red wires have the same value, `RED`, but there is a separate Reveal Your Red Wires action. The quick reference says, “Red wire: the bomb explodes!”

**The problem:** The base action description does not explicitly say whether a player may declare `RED` as the guessed value in a Dual Cut.

**Our assumption:** `RED` is not a legal declared value for Dual or Solo Cut. Red wires are removed safely only through Reveal Your Red Wires; cutting one otherwise causes failure.

**Impact if wrong:** The legal-action validation must allow red Dual Cuts and define their outcome.

### Q9. [RULES-MISSING] Info-token supply and already identified wires

**Status:** ASSUMED → [H6] and [H7] in `ASSUMPTIONS.md`

**What the rules say:** A failed Dual Cut places an Info token showing the target wire’s true value. The game contains 26 Info tokens, including only two yellow ones.

**The problem:** The rules do not cover exhaustion of a particular token type or another failed guess targeting a wire that already has its Info token.

**Our assumption:** Information display is not a limiting resource in the digital version. A wire holds at most one Info token; a repeated failed guess still advances the dial but leaves the existing token unchanged.

**Impact if wrong:** Token inventory and duplicate-token handling must enforce the physical limits or another supplied rule.

## Closed Questions (CLOSED)

None yet.
