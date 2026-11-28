class SetupInfo {
    constructor(game, bga) {
        this.game = game;
        this.bga = bga;
    }

    onEnteringState(args, isCurrentPlayerActive) {
        this.onPlayerActivationChange(args, isCurrentPlayerActive);
    }

    onPlayerActivationChange(args, isCurrentPlayerActive) {
        this.game.setupSelectionEnabled = isCurrentPlayerActive;
        this.bga.statusBar.setTitle(isCurrentPlayerActive
            ? _('${you} must choose a blue wire for the initial Info token')
            : _('Waiting for the initial Info tokens'));
        this.game.renderAll();
    }

    onLeavingState() {
        this.game.setupSelectionEnabled = false;
    }
}

class PlayerTurn {
    constructor(game, bga) {
        this.game = game;
        this.bga = bga;
    }

    onEnteringState(args, isCurrentPlayerActive) {
        this.game.playerTurnActive = isCurrentPlayerActive;
        this.game.selectedDualValue = null;
        this.bga.statusBar.setTitle(isCurrentPlayerActive
            ? _('${you} must perform a wire action')
            : _('${actplayer} must perform a wire action'));

        if (isCurrentPlayerActive) {
            args.soloValues.forEach(value => {
                const label = _('Solo Cut: ${value}').replace('${value}', this.game.valueLabel(value));
                this.bga.statusBar.addActionButton(label, () => {
                    this.bga.actions.performAction('actSoloCut', { value });
                });
            });
            if (args.revealRed) {
                this.bga.statusBar.addActionButton(_('Reveal Your Red Wires'), () => {
                    this.bga.actions.performAction('actRevealRed');
                }, { color: 'secondary' });
            }
        }
        this.game.renderAll();
    }

    onLeavingState() {
        this.game.playerTurnActive = false;
        this.game.selectedDualValue = null;
    }
}

export class Game {
    constructor(bga) {
        this.bga = bga;
        this.setupSelectionEnabled = false;
        this.playerTurnActive = false;
        this.selectedDualValue = null;
        this.bga.states.register('SetupInfo', new SetupInfo(this, bga));
        this.bga.states.register('PlayerTurn', new PlayerTurn(this, bga));
    }

    setup(gamedatas) {
        this.gamedatas = gamedatas;
        this.bga.gameArea.getElement().insertAdjacentHTML('beforeend', `
            <div id="bb-game" class="bombbusters_game">
                <section class="bombbusters_panel">
                    <h2 id="bb-mission-title"></h2>
                    <div id="bb-mission-details"></div>
                    <div id="bb-outcome"></div>
                    <div id="bb-detonator"></div>
                    <div id="bb-special-wires"></div>
                    <div id="bb-validated"></div>
                </section>
                <div id="bb-selection" class="bombbusters_selection"></div>
                <section class="bombbusters_panel">
                    <h2>Cut and Revealed Wires</h2>
                    <div id="bb-cut-wires" class="bombbusters_wire_list"></div>
                </section>
                <div id="bb-players"></div>
            </div>
        `);
        this.bga.gameArea.getElement().addEventListener('click', event => this.onGameAreaClick(event));
        this.renderAll();
        this.bga.notifications.setupPromiseNotifications();
    }

    renderAll() {
        if (!this.gamedatas || !document.getElementById('bb-game')) {
            return;
        }
        this.renderMission();
        this.renderOutcome();
        this.renderDetonator();
        this.renderSpecialWires();
        this.renderValidatedValues();
        this.renderCutWires();
        this.renderPlayers();
        this.renderSelectionHelp();
    }

    renderMission() {
        const mission = this.gamedatas.mission;
        document.getElementById('bb-mission-title').textContent = `${mission.name}: ${mission.title}`;
        const setup = [`Blue values: ${mission.blueValues.join(', ')} (4 copies each)`];
        ['red', 'yellow'].forEach(color => {
            const count = parseInt(mission[color].count, 10);
            const candidates = parseInt(mission[color].candidates, 10);
            if (candidates > 0) {
                setup.push(candidates > count
                    ? `${count} of ${candidates} ${color} wires`
                    : `${count} ${color} wires`);
            }
        });
        setup.push(mission.equipment ? 'Equipment cards not implemented yet' : 'No equipment');
        document.getElementById('bb-mission-details').innerHTML = `
            <div class="bombbusters_mission_lesson">${this.escapeHtml(mission.lesson)}</div>
            <div>${this.escapeHtml(setup.join(' · '))}</div>
        `;
    }

    renderOutcome() {
        const outcome = this.gamedatas.outcome;
        const messages = {
            success: 'Mission accomplished!',
            red: 'A red wire was cut. Mission failed.',
            detonator: 'The detonator reached the skull. Mission failed.',
        };
        document.getElementById('bb-outcome').innerHTML = outcome
            ? `<div class="bombbusters_outcome bombbusters_${this.escapeHtml(outcome)}">${messages[outcome] || ''}</div>`
            : '';
    }

    renderDetonator() {
        const current = parseInt(this.gamedatas.detonator, 10);
        const max = parseInt(this.gamedatas.detonatorMax, 10);
        const steps = Array.from({ length: max }, (ignored, index) => {
            const reached = index < current ? ' bombbusters_reached' : '';
            const label = index === max - 1 ? '☠' : `${index + 1}`;
            return `<span class="bombbusters_dial_step${reached}">${label}</span>`;
        }).join('');
        document.getElementById('bb-detonator').innerHTML = `
            <div class="bombbusters_section_title">Detonator: ${current} / ${max}</div>
            <div class="bombbusters_dial">${steps}</div>
        `;
    }

    renderSpecialWires() {
        const markers = this.gamedatas.specialWires.map(wire => `
            <span class="bombbusters_marker bombbusters_${wire.color}">
                ${wire.color === 'red' ? 'Red' : 'Yellow'} ${String(wire.sortValue)}${wire.possible ? ' ?' : ''}
            </span>
        `).join('');
        document.getElementById('bb-special-wires').innerHTML = `
            <div class="bombbusters_section_title">Special wire values</div>
            <div class="bombbusters_markers">${markers || '<span class="bombbusters_empty">None in this mission</span>'}</div>
        `;
    }

    renderValidatedValues() {
        const validated = new Set(this.gamedatas.validatedValues.map(value => parseInt(value, 10)));
        const values = this.gamedatas.mission.blueValues.map(value => {
            const done = validated.has(parseInt(value, 10)) ? ' bombbusters_validated' : '';
            return `<span class="bombbusters_validation${done}">${value}</span>`;
        }).join('');
        document.getElementById('bb-validated').innerHTML = `
            <div class="bombbusters_section_title">Completed values</div>
            <div class="bombbusters_validations">${values}</div>
        `;
    }

    renderCutWires() {
        const container = document.getElementById('bb-cut-wires');
        if (this.gamedatas.cutWires.length === 0) {
            container.innerHTML = '<span class="bombbusters_empty">None yet</span>';
            return;
        }
        container.innerHTML = this.gamedatas.cutWires.map(wire => this.renderPublicWire(wire)).join('');
    }

    renderPlayers() {
        document.getElementById('bb-players').innerHTML = Object.values(this.gamedatas.players).map(player => {
            const playerId = parseInt(player.id, 10);
            const hand = this.gamedatas.hands[playerId] || [];
            const isViewer = playerId === parseInt(this.gamedatas.viewerId, 10);
            const captain = playerId === parseInt(this.gamedatas.captainId, 10);
            const labels = [this.escapeHtml(player.name)];
            if (captain) {
                labels.push('Captain');
            }
            if (isViewer) {
                labels.push('you');
            }
            const stands = Array.from({ length: parseInt(player.standCount, 10) }, (ignored, index) => {
                const stand = index + 1;
                const wires = hand.filter(wire => parseInt(wire.stand, 10) === stand)
                    .sort((a, b) => parseInt(a.position, 10) - parseInt(b.position, 10));
                return `
                    <div class="bombbusters_stand">
                        <div class="bombbusters_stand_title">Stand ${stand}</div>
                        <div class="bombbusters_wire_list">
                            ${wires.length ? wires.map(wire => this.renderWire(wire)).join('') : '<span class="bombbusters_empty">Empty</span>'}
                        </div>
                    </div>
                `;
            }).join('');
            return `
                <section class="bombbusters_player" style="--bombbusters-player-color: #${this.escapeHtml(player.color)}">
                    <h3>${labels.join(' · ')} <small>${hand.length} wires</small></h3>
                    <div class="bombbusters_stands">${stands}</div>
                </section>
            `;
        }).join('');
    }

    renderWire(wire) {
        const revealed = typeof wire.color === 'string';
        const color = revealed ? wire.color : 'hidden';
        const wireId = parseInt(wire.id, 10);
        const ownerId = parseInt(wire.ownerId, 10);
        const value = revealed ? wire.value : '';
        const selected = revealed && value === this.selectedDualValue && ownerId === parseInt(this.gamedatas.viewerId, 10);
        const targetable = !revealed && this.playerTurnActive && this.selectedDualValue !== null;
        const classes = [
            'bombbusters_wire',
            `bombbusters_${color}`,
            selected ? 'bombbusters_selected' : '',
            targetable ? 'bombbusters_targetable' : '',
        ].filter(Boolean).join(' ');
        const mainLabel = revealed ? this.compactValueLabel(value) : '?';
        const info = wire.info
            ? `<span class="bombbusters_info">${this.valueLabel(wire.info.value)}</span>`
            : '';
        return `
            <button type="button" class="${classes}" data-wire-id="${wireId}" data-owner-id="${ownerId}" title="Wire ${wireId}">
                <span class="bombbusters_wire_value">${mainLabel}</span>
                ${info}
            </button>
        `;
    }

    renderPublicWire(wire) {
        return `
            <span class="bombbusters_wire bombbusters_${wire.color}" title="Player ${parseInt(wire.ownerId, 10)}">
                <span class="bombbusters_wire_value">${this.compactValueLabel(wire.value)}</span>
            </span>
        `;
    }

    renderSelectionHelp() {
        const element = document.getElementById('bb-selection');
        if (this.setupSelectionEnabled) {
            element.textContent = 'Choose one of your blue wires to place an Info token.';
        } else if (this.playerTurnActive && this.selectedDualValue !== null) {
            element.textContent = `Selected value: ${this.valueLabel(this.selectedDualValue)}. Now choose a teammate’s wire.`;
        } else if (this.playerTurnActive) {
            element.textContent = 'For a Dual Cut, choose one of your blue or yellow wires, then choose a teammate’s wire.';
        } else {
            element.textContent = '';
        }
    }

    onGameAreaClick(event) {
        const button = event.target.closest('button[data-wire-id]');
        if (!button) {
            return;
        }
        const wireId = parseInt(button.dataset.wireId, 10);
        const ownerId = parseInt(button.dataset.ownerId, 10);
        const viewerId = parseInt(this.gamedatas.viewerId, 10);

        if (ownerId === viewerId && this.setupSelectionEnabled) {
            const wire = this.findHandWire(wireId);
            if (wire && wire.color === 'blue') {
                this.bga.actions.performAction('actChooseInitialInfo', { wireId });
            }
            return;
        }

        if (!this.playerTurnActive) {
            return;
        }
        if (ownerId === viewerId) {
            const wire = this.findHandWire(wireId);
            if (wire && (wire.color === 'blue' || wire.color === 'yellow')) {
                this.selectedDualValue = wire.value;
                this.renderAll();
            }
            return;
        }
        if (this.selectedDualValue !== null) {
            const value = this.selectedDualValue;
            this.selectedDualValue = null;
            this.bga.actions.performAction('actDualCut', { targetWireId: wireId, value });
            this.renderAll();
        }
    }

    findHandWire(wireId) {
        for (const hand of Object.values(this.gamedatas.hands)) {
            const wire = hand.find(candidate => parseInt(candidate.id, 10) === wireId);
            if (wire) {
                return wire;
            }
        }
        return null;
    }

    removeWiresFromHands(wires) {
        const ids = new Set(wires.map(wire => parseInt(wire.id, 10)));
        Object.keys(this.gamedatas.hands).forEach(playerId => {
            this.gamedatas.hands[playerId] = this.gamedatas.hands[playerId]
                .filter(wire => !ids.has(parseInt(wire.id, 10)));
        });
        this.gamedatas.cutWires.push(...wires);
    }

    notif_initialInfo(args) {
        const wire = this.findHandWire(parseInt(args.wire.id, 10));
        if (wire) {
            wire.info = args.wire.info;
        }
        this.renderAll();
    }

    notif_dualCutSuccess(args) {
        this.gamedatas.validatedValues = args.validatedValues;
        this.removeWiresFromHands(args.wires);
        this.renderAll();
    }

    notif_dualCutFailure(args) {
        this.gamedatas.detonator = parseInt(args.detonator, 10);
        const wire = this.findHandWire(parseInt(args.wire.id, 10));
        if (wire) {
            wire.info = args.wire.info;
        }
        this.renderAll();
    }

    notif_redWireExploded(args) {
        this.gamedatas.outcome = 'red';
        this.removeWiresFromHands([args.wire]);
        this.renderAll();
    }

    notif_detonatorExploded(args) {
        this.gamedatas.detonator = parseInt(args.detonator, 10);
        this.gamedatas.outcome = 'detonator';
        this.renderAll();
    }

    notif_soloCut(args) {
        this.gamedatas.validatedValues = args.validatedValues;
        this.removeWiresFromHands(args.wires);
        this.renderAll();
    }

    notif_redWiresRevealed(args) {
        this.removeWiresFromHands(args.wires);
        this.renderAll();
    }

    notif_missionEnded(args) {
        this.gamedatas.outcome = args.outcome;
        this.renderAll();
    }

    valueLabel(value) {
        if (value === 'yellow') {
            return 'YELLOW';
        }
        if (value === 'red') {
            return 'RED';
        }
        return String(value);
    }

    compactValueLabel(value) {
        if (value === 'yellow') {
            return 'Y';
        }
        if (value === 'red') {
            return 'R';
        }
        return String(value);
    }

    escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }
}
