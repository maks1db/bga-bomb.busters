# Bomb Busters — Implementation Assumptions

These are working assumptions, not additional rules. Each one is linked to a question in `AUTHOR_QUESTIONS.md` and must be revisited when an answer is available.

- **[H1] No blue wire at setup:** a player with no blue wire skips initial Info-token placement. Ref: Q4.
- **[H2] Equipment selection:** unless specified by the Mission card, Equipment cards are drawn randomly without replacement, one per player. Ref: Q5.
- **[H3] Double Detector and double stands:** the two targets may be anywhere in the teammate’s hand, including different stands owned by that teammate. Ref: Q6.
- **[H4] Double Detector and red wires:** one matching non-red wire makes the action succeed safely. On failure, one red wire does not explode and the non-red wire receives the Info token; two red wires cause an explosion. Ref: Q7.
- **[H5] Red-wire actions:** `RED` cannot be declared for Dual Cut or used for Solo Cut. Red wires are removed safely only by Reveal Your Red Wires when all remaining wires in that hand are red. Ref: Q8.
- **[H6] Info-token supply:** the digital interface does not run out of Info tokens; the physical token count does not restrict information display. Ref: Q9.
- **[H7] Already identified wires:** a wire has at most one Info token. A repeated failed guess still advances the detonator but does not add or replace a token. Ref: Q9.
